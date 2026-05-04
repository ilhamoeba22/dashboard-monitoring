<?php

declare(strict_types=1);

namespace App\Console\Commands\Financing;

use App\Models\FinancingMonthlySnapshot;
use App\Services\Mci\MciConnectionService;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

#[Signature('financing:aggregate-snapshot {--db= : Specific database name} {--all : Aggregate all} {--dry-run : Preview only}')]
#[Description('Aggregate financing data from SQL Server to MySQL monthly snapshot table')]
class AggregateSnapshotCommand extends Command
{
    private MciConnectionService $mciService;

    private int $snapshotsCreated = 0;

    private float $startTime;

    public function __construct(MciConnectionService $mciService)
    {
        parent::__construct();
        $this->mciService = $mciService;
    }

    public function handle(): int
    {
        $this->startTime = microtime(true);
        $this->info('🚀 Starting Financing Snapshot Aggregation...');
        $this->newLine();

        $isDryRun = $this->option('dry-run');
        $isAll = $this->option('all');
        $specificDb = $this->option('db');

        if ($isDryRun) {
            $this->warn('⚠️  DRY RUN MODE - No data will be saved');
            $this->newLine();
        }

        try {
            if ($specificDb) {
                $this->processDatabase($specificDb, $isDryRun);
            } elseif ($isAll) {
                $this->processAllDatabases($isDryRun);
            } else {
                $activeDb = $this->mciService->getActiveDatabase();
                $this->info("📦 Processing current active database: {$activeDb}");
                $this->processDatabase($activeDb, $isDryRun);
            }

            $this->newLine();
            $this->showSummary($isDryRun);

            return Command::SUCCESS;
        } catch (\Throwable $e) {
            $this->error('❌ Error during aggregation: '.$e->getMessage());
            Log::channel('metrics')->error('Financing snapshot aggregation failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return Command::FAILURE;
        }
    }

    private function processAllDatabases(bool $isDryRun): void
    {
        $databases = collect($this->mciService->getAvailableDatabases());
        $this->info("📚 Found {$databases->count()} available databases:");

        foreach ($databases as $db) {
            $this->line("  📦 {$db['database']} (parsed: {$db['parsed_date']})");
            $this->processDatabase($db['database'], $isDryRun);
        }
    }

    private function processDatabase(string $database, bool $isDryRun): void
    {
        $this->line("    └── Connecting to: {$database}");
        $this->mciService->switchToDatabase($database);
        $tahunBulan = $this->parseTahunBulan($database);
        $this->line("    └── Period: {$tahunBulan}");

        if ($isDryRun) {
            $this->line('    └── ⚠️  DRY RUN - Previewing data...');
            $this->showPreview();
        } else {
            FinancingMonthlySnapshot::where('tahun_bulan', $tahunBulan)
                ->where('source_db', $database)
                ->delete();
            $this->aggregateData($tahunBulan, $database);
        }
    }

    private function parseTahunBulan(string $database): string
    {
        $monthMap = [
            'JAN' => '01', 'FEB' => '02', 'MAR' => '03', 'APR' => '04',
            'MEI' => '05', 'JUN' => '06', 'JUL' => '07', 'AGU' => '08',
            'SEP' => '09', 'OKT' => '10', 'NOV' => '11', 'DES' => '12',
        ];

        foreach ($monthMap as $month => $num) {
            if (stripos($database, $month) !== false) {
                if (preg_match('/(\d{2})_(\d{8})/', $database, $matches)) {
                    return '20'.$matches[1].'-'.$num;
                }
            }
        }

        return now()->format('Y-m');
    }

    private function showPreview(): void
    {
        $conn = DB::connection('dashboard_data');
        $res = $conn->select("
            SELECT COUNT(*) as noa, SUM(CAST(osmdlc AS DECIMAL(18,2))) as os
            FROM TOFLMB WHERE stsrec = 'A' AND stsacc <> 'W'
        ");
        
        if (!empty($res)) {
            $this->line("       └── Overall: NOA=" . $res[0]->noa . ", O/S=" . number_format((float)$res[0]->os, 0));
        }
    }

    /**
     * OPTIMIZED AGGREGATION (Rule #1 & #5)
     * Menggunakan GROUP BY untuk menghindari N+1 query.
     */
    private function aggregateData(string $tahunBulan, string $database): void
    {
        $conn = DB::connection('dashboard_data');
        $this->line('    └── Running optimized aggregation (Group By logic)...');

        // 1. OVERALL AGGREGATION
        $overall = $conn->select("
            SELECT 
                COUNT(*) as total_noa,
                SUM(CAST(osmdlc AS DECIMAL(18,2))) as total_osmdlc,
                SUM(CAST(osmgnc AS DECIMAL(18,2))) as total_osmgnc,
                SUM(CAST(mdlawal AS DECIMAL(18,2))) as total_mdlawal,
                AVG(CAST(colbarU AS FLOAT)) as avg_kolek,
                SUM(CASE WHEN colbarU IN ('3','4','5') THEN 1 ELSE 0 END) as npf_noa,
                SUM(CASE WHEN colbarU IN ('3','4','5') THEN CAST(osmdlc AS DECIMAL(18,2)) ELSE 0 END) as npf_osmdlc
            FROM TOFLMB 
            WHERE stsrec = 'A' AND stsacc <> 'W'
        ")[0];

        $this->saveSnapshot($tahunBulan, $database, 'ALL', 'ALL', 'ALL', 'ALL', 'ALL', 'ALL', $overall);

        // 2. BY SEGMENT
        $bySegmen = $conn->select("
            SELECT 
                ISNULL(segmen, 'UNKNOWN') as id,
                COUNT(*) as total_noa,
                SUM(CAST(osmdlc AS DECIMAL(18,2))) as total_osmdlc,
                SUM(CAST(osmgnc AS DECIMAL(18,2))) as total_osmgnc,
                SUM(CAST(mdlawal AS DECIMAL(18,2))) as total_mdlawal,
                AVG(CAST(colbarU AS FLOAT)) as avg_kolek,
                SUM(CASE WHEN colbarU IN ('3','4','5') THEN 1 ELSE 0 END) as npf_noa,
                SUM(CASE WHEN colbarU IN ('3','4','5') THEN CAST(osmdlc AS DECIMAL(18,2)) ELSE 0 END) as npf_osmdlc
            FROM TOFLMB 
            WHERE stsrec = 'A' AND stsacc <> 'W'
            GROUP BY segmen
        ");
        foreach ($bySegmen as $row) {
            $this->saveSnapshot($tahunBulan, $database, $row->id, 'ALL', 'ALL', 'ALL', 'ALL', 'ALL', $row);
        }

        // 3. BY PRODUCT
        $byProduk = $conn->select("
            SELECT 
                ISNULL(kdprd, 'UNKNOWN') as id,
                COUNT(*) as total_noa,
                SUM(CAST(osmdlc AS DECIMAL(18,2))) as total_osmdlc,
                SUM(CAST(osmgnc AS DECIMAL(18,2))) as total_osmgnc,
                SUM(CAST(mdlawal AS DECIMAL(18,2))) as total_mdlawal,
                AVG(CAST(colbarU AS FLOAT)) as avg_kolek,
                SUM(CASE WHEN colbarU IN ('3','4','5') THEN 1 ELSE 0 END) as npf_noa,
                SUM(CASE WHEN colbarU IN ('3','4','5') THEN CAST(osmdlc AS DECIMAL(18,2)) ELSE 0 END) as npf_osmdlc
            FROM TOFLMB 
            WHERE stsrec = 'A' AND stsacc <> 'W'
            GROUP BY kdprd
        ");
        foreach ($byProduk as $row) {
            $this->saveSnapshot($tahunBulan, $database, 'ALL', 'ALL', $row->id, 'ALL', 'ALL', 'ALL', $row);
        }

        // 4. BY AO
        $byAo = $conn->select("
            SELECT 
                ISNULL(kdaoh, 'UNKNOWN') as id,
                COUNT(*) as total_noa,
                SUM(CAST(osmdlc AS DECIMAL(18,2))) as total_osmdlc,
                SUM(CAST(osmgnc AS DECIMAL(18,2))) as total_osmgnc,
                SUM(CAST(mdlawal AS DECIMAL(18,2))) as total_mdlawal,
                AVG(CAST(colbarU AS FLOAT)) as avg_kolek,
                SUM(CASE WHEN colbarU IN ('3','4','5') THEN 1 ELSE 0 END) as npf_noa,
                SUM(CASE WHEN colbarU IN ('3','4','5') THEN CAST(osmdlc AS DECIMAL(18,2)) ELSE 0 END) as npf_osmdlc
            FROM TOFLMB 
            WHERE stsrec = 'A' AND stsacc <> 'W'
            GROUP BY kdaoh
        ");
        foreach ($byAo as $row) {
            $this->saveSnapshot($tahunBulan, $database, 'ALL', $row->id, 'ALL', 'ALL', 'ALL', 'ALL', $row);
        }

        // 5. BY BRANCH
        $byBranch = $conn->select("
            SELECT 
                ISNULL(kdloc, 'UNKNOWN') as id,
                COUNT(*) as total_noa,
                SUM(CAST(osmdlc AS DECIMAL(18,2))) as total_osmdlc,
                SUM(CAST(osmgnc AS DECIMAL(18,2))) as total_osmgnc,
                SUM(CAST(mdlawal AS DECIMAL(18,2))) as total_mdlawal,
                AVG(CAST(colbarU AS FLOAT)) as avg_kolek,
                SUM(CASE WHEN colbarU IN ('3','4','5') THEN 1 ELSE 0 END) as npf_noa,
                SUM(CASE WHEN colbarU IN ('3','4','5') THEN CAST(osmdlc AS DECIMAL(18,2)) ELSE 0 END) as npf_osmdlc
            FROM TOFLMB 
            WHERE stsrec = 'A' AND stsacc <> 'W'
            GROUP BY kdloc
        ");
        foreach ($byBranch as $row) {
            $this->saveSnapshot($tahunBulan, $database, 'ALL', 'ALL', 'ALL', $row->id, 'ALL', 'ALL', $row);
        }

        // 6. BY KOLEKTIBILITAS
        $byKol = $conn->select("
            SELECT 
                colbarU as id,
                COUNT(*) as total_noa,
                SUM(CAST(osmdlc AS DECIMAL(18,2))) as total_osmdlc,
                SUM(CAST(osmgnc AS DECIMAL(18,2))) as total_osmgnc,
                SUM(CAST(mdlawal AS DECIMAL(18,2))) as total_mdlawal,
                AVG(CAST(colbarU AS FLOAT)) as avg_kolek,
                SUM(CASE WHEN colbarU IN ('3','4','5') THEN 1 ELSE 0 END) as npf_noa,
                SUM(CASE WHEN colbarU IN ('3','4','5') THEN CAST(osmdlc AS DECIMAL(18,2)) ELSE 0 END) as npf_osmdlc
            FROM TOFLMB 
            WHERE stsrec = 'A' AND stsacc <> 'W'
            GROUP BY colbarU
        ");
        foreach ($byKol as $row) {
            $this->saveSnapshot($tahunBulan, $database, 'ALL', 'ALL', 'ALL', 'ALL', 'ALL', (string)$row->id, $row);
        }

        $this->line("    └── ✅ Aggregation completed. Created snapshots.");
    }

    private function saveSnapshot(
        string $tahunBulan,
        string $database,
        string $kdseg,
        string $kdaoh,
        string $kdprd,
        string $kdloc,
        string $kdwil,
        string $colbarU,
        object $data
    ): void {
        $osmdlc = (float)($data->total_osmdlc ?? 0);
        $osmgnc = (float)($data->total_osmgnc ?? 0);
        $npfOsmdlc = (float)($data->npf_osmdlc ?? 0);

        FinancingMonthlySnapshot::updateOrCreate(
            [
                'tahun_bulan' => $tahunBulan,
                'source_db'   => $database,
                'kdseg'       => $kdseg,
                'kdaoh'       => $kdaoh,
                'kdprd'       => $kdprd,
                'kdloc'       => $kdloc,
                'kdwil'       => $kdwil,
                'colbarU'     => $colbarU,
            ],
            [
                'total_noa'      => (int)($data->total_noa ?? 0),
                'total_osmdlc'   => $osmdlc,
                'total_osmgnc'   => $osmgnc,
                'total_os_total' => $osmdlc + $osmgnc,
                'total_mdlawal'  => (float)($data->total_mdlawal ?? 0),
                'npf_noa'        => (int)($data->npf_noa ?? 0),
                'npf_osmdlc'     => $npfOsmdlc,
                'npf_persen'     => $osmdlc > 0 ? round(($npfOsmdlc / $osmdlc) * 100, 2) : 0,
                'avg_kolek'      => (float)($data->avg_kolek ?? 0),
            ]
        );
        $this->snapshotsCreated++;
    }

    private function showSummary(bool $isDryRun): void
    {
        $duration = round(microtime(true) - $this->startTime, 2);
        $this->info('📊 Summary:');
        $this->line("   ├── Duration: {$duration}s");
        $this->line("   ├── Snapshots Processed: {$this->snapshotsCreated}");

        if ($isDryRun) {
            $this->warn('   └── ⚠️  DRY RUN - No data was saved');
        } else {
            $this->line('   └── ✅ Data saved to MySQL');
        }
    }
}
