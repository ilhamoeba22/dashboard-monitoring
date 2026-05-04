<?php

declare(strict_types=1);

namespace App\Console\Commands\Mci;

use App\Models\Mci\DailyMetricsHistory;
use App\Services\Mci\DashboardRepository;
use App\Services\Mci\MciConnectionService;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('mci:daily-snapshot {--date= : Custom date (Y-m-d) to snapshot}')]
#[Description('Take a daily snapshot of MCI CBS metrics and save to local Data Warehouse (MySQL)')]
class DailySnapshotCommand extends Command
{
    /**
     * Execute the console command.
     */
    public function handle(DashboardRepository $repository, MciConnectionService $connectionService): int
    {
        $dateStr = $this->option('date') ?: now()->toDateString();
        $this->info("Starting Daily Snapshot for date: {$dateStr}");

        try {
            // Ambil semua metric (Realtime) dari SQL Server CBS
            $this->info('Fetching data from SQL Server...');
            $metrics = $repository->getKeyMetrics();

            $dbName = $connectionService->getActiveDatabase();

            // Simpan atau Update ke MySQL
            $this->info('Saving to MySQL Data Warehouse...');
            DailyMetricsHistory::updateOrCreate(
                ['tgl_snapshot' => $dateStr],
                [
                    'financing_os' => $metrics->financing->totalOs,
                    'financing_npf' => $metrics->financing->totalNpf,
                    'financing_noa' => $metrics->financing->totalNoa,

                    'saving_saldo' => $metrics->saving->totalSaldo,
                    'saving_noa' => $metrics->saving->totalNoa,

                    'deposito_saldo' => $metrics->deposito->totalSaldo,
                    'deposito_baghas' => $metrics->deposito->totalBaghas,
                    'deposito_noa' => $metrics->deposito->totalNoa,

                    'source_database' => $dbName,
                ],
            );

            $this->info('✅ Daily snapshot completed successfully!');

            return self::SUCCESS;
        } catch (\Throwable $e) {
            $this->error('Failed to take snapshot: '.$e->getMessage());

            return self::FAILURE;
        }
    }
}
