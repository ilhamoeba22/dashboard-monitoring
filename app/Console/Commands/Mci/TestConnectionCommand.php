<?php

namespace App\Console\Commands\Mci;

use App\Services\Mci\MciConnectionService;
use Illuminate\Console\Command;

class TestConnectionCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mci:test-connection {--db=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test koneksi ke database MCI';

    /**
     * Execute the console command.
     */
    public function handle(MciConnectionService $mciService): int
    {
        // Jika ada opsi --db, switch dulu
        $dbName = $this->option('db');
        if ($dbName) {
            if (!$mciService->setActiveDatabase($dbName)) {
                $this->error("Nama database tidak valid: {$dbName}");
                return self::FAILURE;
            }
            $this->info("Switch ke database: {$dbName}");
        }

        // Test koneksi
        $this->info("Menguji koneksi ke MCI...");

        if (!$mciService->testConnection()) {
            $this->error("❌ Gagal terhubung ke database!");
            $this->newLine();
            $this->line("Info koneksi:");
            $info = $mciService->getConnectionInfo();
            foreach ($info as $key => $value) {
                $this->line("  {$key}: {$value}");
            }
            return self::FAILURE;
        }

        $this->info("✅ Berhasil terhubung ke MCI!");
        $this->newLine();
        $this->line("Info koneksi:");
        $info = $mciService->getConnectionInfo();
        foreach ($info as $key => $value) {
            $this->line("  {$key}: {$value}");
        }

        return self::SUCCESS;
    }
}