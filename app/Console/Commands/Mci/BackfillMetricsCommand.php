<?php

namespace App\Console\Commands\Mci;

use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

class BackfillMetricsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mci:backfill';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Backfill histori saldo EOM (Akhir Bulan) ke dalam MySQL Data Warehouse';

    /**
     * Execute the console command.
     */
    public function handle(\App\Services\Mci\DashboardRepository $repository): int
    {
        $this->info('Starting Historical Backfill (EOM Snapshots) into MySQL...');

        // Kita scan koneksi dashboard dari database.php (mar, feb, jan)
        $historyConnections = ['dashboard_mar', 'dashboard_feb', 'dashboard_jan'];

        foreach ($historyConnections as $conn) {
            $dbName = config("database.connections.{$conn}.database");
            if (!$dbName) continue;

            $this->info("\nProcessing Connection: {$conn} ({$dbName})");

            try {
                // Switch Repository Connection
                $repository->setConnection($conn);
                
                // Ambil metrics & tanggal asli dari database tersebut
                $metrics = $repository->getKeyMetrics();
                
                // Parsing tanggal: $metrics->tgl = "01042026" (DDMMYYYY)
                $dd = substr($metrics->tgl, 0, 2);
                $mm = substr($metrics->tgl, 2, 2);
                $yy = substr($metrics->tgl, 4, 4);
                $dateStr = "{$yy}-{$mm}-{$dd}"; // Format MySQL: YYYY-MM-DD

                $this->line("   - Snapshot Date: {$dateStr}");

                // Insert to MySQL
                \App\Models\Mci\DailyMetricsHistory::updateOrCreate(
                    ['tgl_snapshot' => $dateStr],
                    [
                        'financing_os'    => $metrics->financing->totalOs,
                        'financing_npf'   => $metrics->financing->totalNpf,
                        'financing_noa'   => $metrics->financing->totalNoa,
                        
                        'saving_saldo'    => $metrics->saving->totalSaldo,
                        'saving_noa'      => $metrics->saving->totalNoa,
                        
                        'deposito_saldo'  => $metrics->deposito->totalSaldo,
                        'deposito_baghas' => $metrics->deposito->totalBaghas,
                        'deposito_noa'    => $metrics->deposito->totalNoa,
                        
                        'source_database' => $dbName,
                    ]
                );
                
                $this->info("   [OK] Saved to MySQL.");
            } catch (\Throwable $e) {
                $this->error("   [ERROR] Failed to backfill {$conn}: " . $e->getMessage());
            } finally {
                // Selalu kembalikan koneksi ke default
                $repository->resetConnection();
            }
        }

        $this->info("\n✅ Backfill Process Completed!");
        return self::SUCCESS;
    }
}
