<?php

declare(strict_types=1);

namespace App\Console\Commands\Mci;

use App\Services\Mci\MciConnectionService;
use Illuminate\Console\Command;

class CurrentCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mci:current {--set=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Tampilkan atau ubah database MCI aktif';

    /**
     * Execute the console command.
     */
    public function handle(MciConnectionService $mciService): int
    {
        // Jika ada opsi --set, ubah database aktif
        $newDb = $this->option('set');
        if ($newDb) {
            if (! $mciService->setActiveDatabase($newDb)) {
                $this->error("Nama database tidak valid: {$newDb}");

                return self::FAILURE;
            }
            $this->info("Database diubah ke: {$newDb}");

            return self::SUCCESS;
        }

        // Tampilkan database aktif
        $current = $mciService->getActiveDatabase();
        $this->info("Database MCI aktif: {$current}");
        $this->newLine();

        $this->line('Info lengkap:');
        $info = $mciService->getConnectionInfo();
        foreach ($info as $key => $value) {
            $this->line("  {$key}: {$value}");
        }

        return self::SUCCESS;
    }
}
