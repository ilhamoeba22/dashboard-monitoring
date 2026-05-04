<?php

declare(strict_types=1);

namespace App\Console\Commands\Mci;

use App\Services\Mci\MciConnectionService;
use Illuminate\Console\Command;

class ListDatabasesCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mci:list-databases';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'List semua database MCI yang tersedia';

    /**
     * Execute the console command.
     */
    public function handle(MciConnectionService $mciService): int
    {
        $this->info('Melihat database MCI...');
        $this->newLine();

        $databases = $mciService->listDatabases();

        if (empty($databases)) {
            $this->warn('Tidak ada database MCI yang ditemukan.');

            return self::SUCCESS;
        }

        $this->info('Database MCI yang tersedia ('.count($databases).'):');
        $this->newLine();

        $current = $mciService->getActiveDatabase();

        foreach ($databases as $index => $db) {
            $isActive = ($db === $current) ? ' ✅' : '';
            $this->line(sprintf('  %d. %s%s', $index + 1, $db, $isActive));
        }

        $this->newLine();
        $this->line("Database aktif: {$current}");

        return self::SUCCESS;
    }
}
