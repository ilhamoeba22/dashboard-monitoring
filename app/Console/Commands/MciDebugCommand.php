<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Services\Mci\MciConnectionService;
use Illuminate\Console\Command;

class MciDebugCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mci:debug';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Debug MCI Database Auto-Switch state and detection';

    /**
     * Execute the console command.
     */
    public function handle(MciConnectionService $mciService): int
    {
        $this->info('🔍 MCI Database Debugging Tool');
        $this->info('-------------------------------');

        $activeDb = $mciService->getActiveDatabase();
        $databases = $mciService->getDatabasesWithMeta();

        if (empty($databases)) {
            $this->error('❌ Tidak ada database MCI_* yang terdeteksi di SQL Server.');
            return 1;
        }

        $tableData = collect($databases)->map(function ($db) {
            return [
                'Database Name' => $db['name'],
                'Parsed Date'   => $db['date'] ?? 'N/A',
                'Status'        => strtoupper($db['status']),
                'Is Latest'     => $db['is_latest'] ? '✅ YES' : 'NO',
            ];
        });

        $this->table(
            ['Database Name', 'Parsed Date', 'Status', 'Is Latest'],
            $tableData->toArray()
        );

        $this->newLine();
        $this->info("📌 Active DB saat ini di sistem adalah: [{$activeDb}]");
        
        if ($activeDb === '') {
            $this->warn('⚠️  Peringatan: Active Database KOSONG. Pastikan .env SQL_SERVER_DATABASE sudah terisi.');
        }

        return 0;
    }
}
