<?php

declare(strict_types=1);

namespace App\Services\Mci;

use Carbon\Carbon;
use Illuminate\Database\Connection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * MciConnectionService
 * --------------------------------------------------------------------------
 * Mengelola koneksi dynamic ke database MCI SQL Server.
 *
 * ARSITEKTUR DATABASE BULANAN:
 * ─────────────────────────────────────────────────────────────────────────
 * Core Banking System vendor membuat snapshot database baru setiap akhir
 * bulan dengan naming convention:
 *   MCI_{MMM}{YY}_{DDMMYYYY}
 *   Contoh: MCI_MAR26_01042026 (dibuat 01 April 2026 = snapshot Maret 2026)
 *           MCI_JAN_31012026   (format lama, tapi tetap didukung)
 *
 * STRATEGI AUTO-ROTATION (PROFESSIONAL):
 * ─────────────────────────────────────────────────────────────────────────
 * 1. AUTO-DETECT: Setiap hari, scheduler scan SQL Server untuk database MCI_*
 *    terbaru berdasarkan tanggal pembuatan (dari nama database).
 * 2. AUTO-SWITCH: Jika database baru ditemukan, switch otomatis + clear cache.
 * 3. HISTORY:     Database lama TIDAK dihapus → otomatis jadi arsip/history.
 * 4. MANUAL OVERRIDE: Admin bisa paksa switch via artisan atau API endpoint.
 * 5. FALLBACK:    Jika auto-detect gagal, gunakan database dari .env (MCI_ACTIVE_DB).
 *
 * DATABASE LIFECYCLE:
 *   MCI_JAN_31012026   → history Januari 2026
 *   MCI_FEB26_01032026 → history Februari 2026
 *   MCI_MAR26_01042026 → REALTIME saat ini
 *   MCI_APR26_01052026 → akan otomatis jadi REALTIME saat bulan Mei
 */
class MciConnectionService
{
    protected string $connectionName = 'dashboard_data';

    private const CACHE_KEY_ACTIVE_DB = 'mci:active_database';

    private const CACHE_KEY_DB_LIST = 'mci:database_list';

    private const CACHE_TTL_ACTIVE = 3600;   // 1 jam

    private const CACHE_TTL_LIST = 300;    // 5 menit

    /**
     * Regex patterns yang didukung untuk nama database MCI:
     * Dilonggarkan agar menerima semua bentuk MCI_ (termasuk MCI_JUN_2025)
     */
    private const DB_NAME_PATTERNS = [
        'all' => '/^MCI_.+$/i',
    ];

    public function __construct()
    {
        $this->connectionName = config('mci.connection', 'dashboard_data');
    }

    // =========================================================================
    // ACTIVE DATABASE MANAGEMENT
    // =========================================================================

    /**
     * Ambil nama database yang sedang aktif (realtime).
     * Priority: Cache → Env → Auto-detect
     */
    public function getActiveDatabase(): string
    {
        // 1. Dari cache (paling cepat)
        $cached = Cache::get(self::CACHE_KEY_ACTIVE_DB);
        if (is_string($cached) && $cached !== '') {
            return $cached;
        }

        // 2. Dari environment variable (.env MCI_ACTIVE_DB)
        $fromEnv = config('mci.active_database', '');
        if (is_string($fromEnv) && $fromEnv !== '') {
            Cache::put(self::CACHE_KEY_ACTIVE_DB, $fromEnv, self::CACHE_TTL_ACTIVE);

            return $fromEnv;
        }

        // 3. Auto-detect dari SQL Server (fallback terakhir)
        $latest = $this->detectLatestDatabase();
        if ($latest !== null) {
            $this->persistActiveDatabase($latest);

            return $latest;
        }

        return '';
    }

    /**
     * Set database aktif secara manual (admin override).
     * Validates naming convention sebelum switch.
     */
    public function setActiveDatabase(string $database): bool
    {
        if (! $this->isValidDatabaseName($database)) {
            Log::warning('MCI: Rejected invalid database name', ['database' => $database]);

            return false;
        }

        $previous = $this->getActiveDatabase();
        $this->persistActiveDatabase($database);

        Log::info('MCI: Database switched', [
            'from' => $previous,
            'to' => $database,
            'by' => 'manual',
        ]);

        return true;
    }

    /**
     * Auto-detect dan switch ke database terbaru jika ada yang lebih baru.
     * Dipanggil oleh scheduler setiap hari.
     * Return true jika terjadi switch, false jika tidak ada perubahan.
     */
    public function autoDetectAndSwitch(): bool
    {
        $latest = $this->detectLatestDatabase();
        $current = $this->getActiveDatabase();

        if ($latest === null) {
            Log::warning('MCI: Auto-detect found no databases matching MCI_* pattern');

            return false;
        }

        if ($latest === $current) {
            Log::info('MCI: Auto-detect — database sudah up to date', ['database' => $current]);

            return false;
        }

        // Ada database baru — switch otomatis
        $this->persistActiveDatabase($latest);

        Log::info('MCI: Auto-switched to new database', [
            'from' => $current,
            'to' => $latest,
            'by' => 'auto-detect',
        ]);

        return true;
    }

    /**
     * Reset ke database default dari .env (MCI_ACTIVE_DB).
     */
    public function resetToDefault(): void
    {
        $default = config('mci.active_database', '');
        if (is_string($default) && $default !== '') {
            $this->persistActiveDatabase($default);
        }
    }

    // =========================================================================
    // DATABASE DISCOVERY
    // =========================================================================

    /**
     * List semua database MCI_* yang tersedia di SQL Server.
     * Sorted dari terbaru ke terlama (berdasarkan tanggal di nama).
     *
     * @return list<string>
     */
    public function listDatabases(): array
    {
        /** @var list<string> $cached */
        $cached = Cache::get(self::CACHE_KEY_DB_LIST, []);
        if (! empty($cached)) {
            return $cached;
        }

        try {
            $prefix = config('mci.prefix', 'MCI_').'%';
            /** @var list<object{name: string}> $rows */
            $rows = DB::connection($this->connectionName)
                ->select('SELECT name FROM sys.databases WHERE name LIKE ? ORDER BY name', [$prefix]);

            $names = array_column($rows, 'name');
            $historyConfig = config('mci.history', []);

            // Sort berdasarkan urutan: TANGGAL > CONFIG > ALFABETIS
            // HARUS ASCENDING (Terlama di atas, Terbaru di bawah) agar end() mengambil yang terbaru
            usort($names, function (string $a, string $b) use ($historyConfig): int {
                $dateA = $this->parseDatabaseDate($a);
                $dateB = $this->parseDatabaseDate($b);

                // Prioritas 1 & 2: Urutkan berdasarkan TANGGAL (Mutlak)
                if ($dateA !== null && $dateB !== null) {
                    return $dateA->timestamp <=> $dateB->timestamp;
                }
                
                // Jika salah satu punya tanggal, yang punya tanggal dianggap LEBIH BARU (taruh di bawah)
                if ($dateA !== null) return 1;
                if ($dateB !== null) return -1;

                // Prioritas 3: Jika keduanya NULL (tidak punya tanggal), gunakan Config
                $indexA = array_search($a, $historyConfig);
                $indexB = array_search($b, $historyConfig);

                if ($indexA !== false && $indexB !== false) {
                    // Dibalik agar index 0 di config (terbaru) berada di paling bawah array
                    return $indexB <=> $indexA; 
                }
                if ($indexA !== false) return 1;
                if ($indexB !== false) return -1;

                // Fallback: Alfabetis
                return strcmp($a, $b);
            });

            /** @var list<string> $sorted */
            $sorted = array_values($names);
            Cache::put(self::CACHE_KEY_DB_LIST, $sorted, self::CACHE_TTL_LIST);

            return $sorted;
        } catch (\Throwable $e) {
            Log::error('MCI: Failed to list databases', ['error' => $e->getMessage()]);

            return [];
        }
    }

    /**
     * Ambil database terbaru (yang akan jadi realtime).
     */
    public function detectLatestDatabase(): ?string
    {
        $databases = $this->listDatabases();

        return ! empty($databases) ? end($databases) ?: null : null;
    }

    /**
     * Get available databases with parsed metadata.
     * Alias untuk getDatabasesWithMeta() untuk backward compatibility.
     *
     * @return list<array{database: string, parsed_date: string|null, tahun_bulan: string|null}>
     */
    public function getAvailableDatabases(): array
    {
        $databases = $this->listDatabases();
        $result = [];

        foreach ($databases as $name) {
            $date = $this->parseDatabaseDate($name);
            $tahunBulan = null;

            if ($date) {
                $tahunBulan = $date->format('Y-m');
            }

            $result[] = [
                'database' => $name,
                'parsed_date' => $date?->format('Y-m-d'),
                'tahun_bulan' => $tahunBulan,
            ];
        }

        return $result;
    }

    /**
     * Ambil semua database dengan metadata (untuk admin/history view).
     *
     * @return list<array{name: string, date: string|null, is_active: bool, is_latest: bool, label: string}>
     */
    public function getDatabasesWithMeta(): array
    {
        $databases = $this->listDatabases();
        $active = $this->getActiveDatabase();
        $latest = ! empty($databases) ? end($databases) : null;

        $result = [];
        foreach ($databases as $name) {
            $date = $this->parseDatabaseDate($name);
            $isActive = $name === $active;
            $isLatest = $name === $latest;

            $result[] = [
                'name' => $name,
                'date' => $date?->format('Y-m-d'),
                'period' => $date ? $date->format('F Y') : null,
                'is_active' => $isActive,
                'is_latest' => $isLatest,
                'status' => $isActive ? 'realtime' : ($isLatest ? 'pending' : 'history'),
                'label' => $this->buildDatabaseLabel($name, $date, $isActive),
            ];
        }

        // Return terbaru di atas
        return array_reverse($result);
    }

    // =========================================================================
    // CONNECTION MANAGEMENT
    // =========================================================================

    /**
     * Ambil koneksi database dengan database aktif sudah di-set.
     */
    public function getConnection(): Connection
    {
        $activeDb = $this->getActiveDatabase();

        if ($activeDb !== '') {
            Config::set("database.connections.{$this->connectionName}.database", $activeDb);
            DB::purge($this->connectionName);
        }

        return DB::connection($this->connectionName);
    }

    /**
     * Switch ke database tertentu untuk operasi selanjutnya.
     * Tidak mengubah active database (hanya untuk query history).
     */
    public function switchToDatabase(string $database): Connection
    {
        if (! $this->isValidDatabaseName($database)) {
            throw new \InvalidArgumentException("Invalid database name: {$database}");
        }

        Config::set("database.connections.{$this->connectionName}.database", $database);
        DB::purge($this->connectionName);

        return DB::connection($this->connectionName);
    }

    /**
     * Test koneksi ke SQL Server.
     */
    public function testConnection(): bool
    {
        try {
            $this->getConnection()->getPdo();

            return true;
        } catch (\Throwable $e) {
            Log::error('MCI: Connection test failed', ['error' => $e->getMessage()]);

            return false;
        }
    }

    /**
     * Ambil info koneksi saat ini.
     *
     * @return array{connection: string, active_database: string, host: string|null, status: string}
     */
    public function getConnectionInfo(): array
    {
        /** @var array{host?: string, port?: string, username?: string} $config */
        $config = config("database.connections.{$this->connectionName}", []);

        return [
            'connection' => $this->connectionName,
            'active_database' => $this->getActiveDatabase(),
            'host' => $config['host'] ?? null,
            'port' => $config['port'] ?? null,
            'status' => $this->testConnection() ? 'connected' : 'disconnected',
        ];
    }

    // =========================================================================
    // VALIDATION & PARSING
    // =========================================================================

    /**
     * Validasi format nama database MCI.
     * Mendukung format baru (MCI_MAR26_01042026) dan lama (MCI_JAN_31012026).
     */
    public function isValidDatabaseName(string $name): bool
    {
        foreach (self::DB_NAME_PATTERNS as $pattern) {
            if (preg_match($pattern, $name)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Parse tanggal dari nama database MCI.
     * Mengambil tanggal pembuatan (bagian akhir nama) sebagai Carbon instance.
     *
     * MCI_MAR26_01042026 → Carbon('2026-04-01') (01 April 2026)
     * MCI_JAN_31012026   → Carbon('2026-01-31') (31 Januari 2026)
     */
    public function parseDatabaseDate(string $name): ?Carbon
    {
        // Format baru: MCI_MAR26_01042026
        if (preg_match('/^MCI_[A-Z]{3}\d{2}_(\d{2})(\d{2})(\d{4})$/', $name, $m)) {
            try {
                return Carbon::createFromFormat('d-m-Y', "{$m[1]}-{$m[2]}-{$m[3]}");
            } catch (\Throwable) {
                return null;
            }
        }

        // Format lama: MCI_JAN_31012026
        if (preg_match('/^MCI_[A-Z]{3}_(\d{2})(\d{2})(\d{4})$/', $name, $m)) {
            try {
                return Carbon::createFromFormat('d-m-Y', "{$m[1]}-{$m[2]}-{$m[3]}");
            } catch (\Throwable) {
                return null;
            }
        }

        return null;
    }

    // =========================================================================
    // PRIVATE HELPERS
    // =========================================================================

    /**
     * Simpan database aktif ke cache + update runtime config.
     */
    private function persistActiveDatabase(string $database): void
    {
        Cache::put(self::CACHE_KEY_ACTIVE_DB, $database, self::CACHE_TTL_ACTIVE);
        Cache::forget(self::CACHE_KEY_DB_LIST); // Refresh list
        Config::set("database.connections.{$this->connectionName}.database", $database);
        DB::purge($this->connectionName); // Reset connection pool
    }

    /**
     * Buat label human-readable untuk database.
     */
    private function buildDatabaseLabel(string $name, ?Carbon $date, bool $isActive): string
    {
        $period = $date ? $date->format('F Y') : $name;
        $tag = $isActive ? ' [REALTIME]' : '';

        return $period.$tag;
    }
}
