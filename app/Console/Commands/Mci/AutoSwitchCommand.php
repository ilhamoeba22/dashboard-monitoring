<?php

declare(strict_types=1);

namespace App\Console\Commands\Mci;

use App\Services\Mci\MciConnectionService;
use Illuminate\Console\Command;

/**
 * Artisan command: mci:auto-switch
 * ─────────────────────────────────
 * Detect database MCI terbaru dan switch otomatis jika ada yang baru.
 * Command ini dijalankan oleh Laravel Scheduler setiap hari pukul 06:00.
 *
 * Usage:
 *   php artisan mci:auto-switch           → detect dan switch jika perlu
 *   php artisan mci:auto-switch --dry-run → hanya tampilkan, tidak switch
 *   php artisan mci:auto-switch --force   → paksa switch ke database terbaru
 */
class AutoSwitchCommand extends Command
{
    protected $signature   = 'mci:auto-switch {--dry-run : Hanya tampilkan tanpa switch} {--force : Paksa switch walau sudah sama}';
    protected $description = 'Auto-detect dan switch ke database MCI terbaru (dijalankan scheduler setiap hari)';

    public function __construct(private readonly MciConnectionService $mci)
    {
        parent::__construct();
    }

    public function handle(): int
    {
        $this->info('🔍 MCI Auto-Switch: Scanning available databases...');

        $databases = $this->mci->listDatabases();

        if (empty($databases)) {
            $this->error('❌ Tidak ada database MCI_* ditemukan di SQL Server.');
            $this->warn('   Pastikan koneksi SQL Server sudah benar di .env');
            return self::FAILURE;
        }

        $this->info("📋 Database ditemukan: " . count($databases));

        $meta = $this->mci->getDatabasesWithMeta();
        $headers = ['Database', 'Tanggal', 'Status'];
        $rows    = array_map(fn ($db) => [
            $db['name'],
            $db['period'] ?? '-',
            $db['status'],
        ], $meta);

        $this->table($headers, $rows);

        $latest  = $this->mci->detectLatestDatabase();
        $current = $this->mci->getActiveDatabase();

        $this->line('');
        $this->info("📌 Database aktif saat ini : <comment>{$current}</comment>");
        $this->info("🆕 Database terbaru        : <comment>{$latest}</comment>");

        if ($this->option('dry-run')) {
            $this->warn('🏃 DRY-RUN mode: Tidak ada perubahan yang dilakukan.');
            return self::SUCCESS;
        }

        if ($latest === null) {
            $this->error('❌ Tidak bisa menentukan database terbaru.');
            return self::FAILURE;
        }

        if ($latest === $current && ! $this->option('force')) {
            $this->info('✅ Database sudah up-to-date. Tidak perlu switch.');
            return self::SUCCESS;
        }

        if ($latest !== $current) {
            $this->warn("⚡ Database baru ditemukan! Switching dari [{$current}] ke [{$latest}]...");
        } else {
            $this->warn("⚡ FORCE mode: Switching ke [{$latest}]...");
        }

        $switched = $this->mci->setActiveDatabase($latest);

        if ($switched) {
            $this->info("✅ Berhasil switch ke: <comment>{$latest}</comment>");
            $this->info('   Cache akan di-clear otomatis oleh scheduler.');
            return self::SUCCESS;
        }

        $this->error("❌ Gagal switch ke database: {$latest}");
        return self::FAILURE;
    }
}
