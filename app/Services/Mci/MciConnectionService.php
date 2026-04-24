<?php

namespace App\Services\Mci;

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * MciConnectionService - Service untuk mengelola koneksi database MCI.
 * 
 * Service ini menangani switching database MCI di runtime.
 * Setiap bulan, database MCI baru dibuat (snapshot), dan aplikasi
 * harus bisa switch ke database yang benar secara otomatis.
 */
class MciConnectionService
{
    /**
     * Nama koneksi database yang digunakan.
     */
    protected string $connectionName = 'dashboard_data';

    /**
     * Database aktif saat ini.
     */
    protected ?string $activeDatabase = null;

    /**
     * Buat instance service.
     */
    public function __construct()
    {
        $this->connectionName = config('mci.connection', 'dashboard_data');
        $this->activeDatabase = config('mci.active_database');
    }

    /**
     * Ambil nama database aktif.
     */
    public function getActiveDatabase(): ?string
    {
        return $this->activeDatabase ?? config('mci.active_database');
    }

    /**
     * Ambil nama koneksi.
     */
    public function getConnectionName(): string
    {
        return $this->connectionName;
    }

    /**
     * Set database aktif.
     * 
     * Mengubah koneksi ke database MCI tertentu.
     * Ini akan mempengaruhi semua query menggunakan connection 'dashboard_data'.
     */
    public function setActiveDatabase(string $database): bool
    {
        // Validasi format nama database
        if (!$this->isValidDatabaseName($database)) {
            Log::warning("MCI: Invalid database name format: {$database}");
            return false;
        }

        $this->activeDatabase = $database;

        // Update konfigurasi koneksi secara dinamis
        Config::set("database.connections.{$this->connectionName}.database", $database);

        Log::info("MCI: Switched to database: {$database}");

        return true;
    }

    /**
     * Reset ke database default dari config.
     */
    public function resetToDefault(): void
    {
        $defaultDb = config('mci.active_database');
        $this->setActiveDatabase($defaultDb);
    }

    /**
     * Ambil koneksi terkini dengan setingan database aktif.
     * 
     * Mengembalikan instance koneksi yang sudah dikonfigurasi
     * dengan database yang benar.
     */
    public function getConnection()
    {
        // Pastikan database sudah diset sebelum mengambil koneksi
        if ($this->activeDatabase) {
            Config::set("database.connections.{$this->connectionName}.database", $this->activeDatabase);
        }

        return DB::connection($this->connectionName);
    }

    /**
     * Eksekusi query dengan database aktif.
     * 
     * shorthand untuk getConnection()->table(), dll.
     */
    public function table(string $table)
    {
        return $this->getConnection()->table($table);
    }

    /**
     * Eksekusi query RAW.
     */
    public function select(string $query, array $bindings = [])
    {
        return $this->getConnection()->select($query, $bindings);
    }

    /**
     * Test koneksi ke database MCI.
     * 
     * Mengembalikan true jika berhasil konek.
     */
    public function testConnection(): bool
    {
        try {
            $this->getConnection()->getPdo();
            return true;
        } catch (\Exception $e) {
            Log::error("MCI: Connection test failed - " . $e->getMessage());
            return false;
        }
    }

    /**
     * Ambil informasi koneksi terkini.
     */
    public function getConnectionInfo(): array
    {
        $config = config("database.connections.{$this->connectionName}");

        return [
            'connection' => $this->connectionName,
            'database' => $this->getActiveDatabase(),
            'host' => $config['host'] ?? null,
            'port' => $config['port'] ?? null,
            'username' => $config['username'] ?? null,
        ];
    }

    /**
     * Validasi format nama database MCI.
     * 
     * Format: MCI_{MMM}{YY}_{DDMMYYYY}
     * Contoh: MCI_MAR26_01042026
     */
    public function isValidDatabaseName(string $name): bool
    {
        $pattern = config('mci.pattern', '/^MCI_[A-Z]{3}[0-9]{2}_[0-9]{8}$/');
        return (bool) preg_match($pattern, $name);
    }

    /**
     * Ambil daftar database MCI yang tersedia di server.
     * 
     * Scan semua database di SQL Server yang matching pattern MCI_*.
     */
    public function listDatabases(): array
    {
        $pattern = config('mci.prefix', 'MCI_') . '%';

        try {
            // Query untuk list semua database
            $databases = $this->getConnection()
                ->select("SELECT name FROM sys.databases WHERE name LIKE ? ORDER BY name DESC", [$pattern]);

            return array_column($databases, 'name');
        } catch (\Exception $e) {
            Log::error("MCI: Failed to list databases - " . $e->getMessage());
            return [];
        }
    }

    /**
     * Ambil database MCI terjadwal (yang terbaru).
     * 
     * Dari daftar database yang tersedia, ambil yang terbaru
     * berdasarkan nama (format tanggal).
     */
    public function getLatestDatabase(): ?string
    {
        $databases = $this->listDatabases();

        if (empty($databases)) {
            return null;
        }

        // Urutkan descending (paling baru pertama)
        rsort($databases);

        return $databases[0] ?? null;
    }

    /**
     * Auto-detect dan set database MCI terkini.
     * 
     * Mencoba detect database terbaru dari server,
     * atau fallback ke config.
     */
    public function autoDetect(): bool
    {
        $latest = $this->getLatestDatabase();

        if ($latest) {
            return $this->setActiveDatabase($latest);
        }

        // Fallback ke default dari config
        Log::warning("MCI: Could not auto-detect latest database, using default");
        return false;
    }
}