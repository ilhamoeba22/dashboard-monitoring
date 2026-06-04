<?php

declare(strict_types=1);

namespace App\Repositories\Mci;

use App\Services\Mci\MciConnectionService;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;

/**
 * FinancingGrowthRepository
 * 
 * Logic Reconstruction: Matches MDB Legacy standards for Monthly Growth.
 * - Active period detection via TANGGAL table.
 * - Dynamic month generation (Jan to Current).
 * - Mixed YoY/MoM logic.
 */
class FinancingGrowthRepository
{
    private string $connection = 'dashboard_data';
    private MciConnectionService $mciService;

    public function __construct(MciConnectionService $mciService)
    {
        $this->mciService = $mciService;
    }

    public function getGrowthTrend(string $dimension = 'ao'): array
    {
        // 1. Get Absolute Active Period
        $activeDb = $this->mciService->getActiveDatabase();
        $dbDate = $this->mciService->parseDatabaseDate($activeDb);
        
        // Fallback to TANGGAL table
        if (!$dbDate) {
            $sysDateRow = DB::connection($this->connection)->table('TANGGAL')
                ->select('tgl')->orderBy('tgl', 'desc')->first();
            
            if ($sysDateRow && strlen((string)$sysDateRow->tgl) === 8) {
                $tgl = (string)$sysDateRow->tgl;
                // Standard format is dmY (e.g. 30042026)
                $dbDate = Carbon::createFromFormat('dmY', $tgl);
            }
        }

        // Final fallback to system date
        $dbDate = $dbDate ?: Carbon::now();
        
        $currentPeriod = $dbDate->format('Ym');
        $currentYear = $dbDate->format('Y');
        $activeMonth = (int) $dbDate->format('m');
        $tgl_sistem = $dbDate->format('dmY');
        
        // YoY Base: Jan Previous Year
        $yoyBasePeriod = ((int)$currentYear - 1) . '01';

        // 2. Generate Active Periods list (Jan to Current)
        $validPeriods = [];
        for ($m = 1; $m <= $activeMonth; $m++) {
            $validPeriods[] = $currentYear . str_pad((string)$m, 2, '0', STR_PAD_LEFT);
        }

        // Dimension Mapping
        $dimConfig = $this->getDimensionConfig($dimension);
        
        // 3. Fetch Raw Data
        $rawData = $this->fetchRawCombinedData($dimConfig, $currentPeriod, $currentYear, $yoyBasePeriod);

        // 4. Transform to Legacy Matrix
        return $this->transformToLegacyMatrix($rawData, $validPeriods, $yoyBasePeriod, $tgl_sistem);
    }

    private function getDimensionConfig(string $dimension): array
    {
        $configs = [
            'ao' => ['table' => 'AO', 'key' => 'kdao', 'name' => 'nmao', 'foreign' => 'kdaoh'],
            'produk' => ['table' => 'SETUPLOAN', 'key' => 'kdprd', 'name' => 'ket', 'foreign' => 'kdprd'],
            'cabang' => ['table' => 'CABANG', 'key' => 'kdloc', 'name' => 'nama', 'foreign' => 'kdloc'],
            'wilayah' => ['table' => 'WILAYAH', 'key' => 'kodewil', 'name' => 'ket', 'foreign' => 'kdwil'],
            'segmen' => ['table' => 'SEGMEN', 'key' => 'kdseg', 'name' => 'ket', 'foreign' => 'segmen'],
        ];
        return $configs[$dimension] ?? $configs['ao'];
    }

    private function fetchRawCombinedData(array $config, string $currentPeriod, string $currentYear, string $yoyBasePeriod): Collection
    {
        $table = $config['table'];
        $key = $config['key'];
        $name = $config['name'];
        $foreign = $config['foreign'];

        $selectEom = "a.$foreign";
        $joinEom = "";
        if ($foreign === 'segmen') {
            $selectEom = "ISNULL(b.segmen, 'UNKNOWN')";
            $joinEom = "LEFT JOIN TOFLMB b ON a.nokontrak = b.nokontrak";
        }

        $sql = "
            WITH CombinedData AS (
                SELECT $foreign as DimID, osmdlc, '$currentPeriod' as PeriodeIdx
                FROM TOFLMB
                WHERE stsrec = 'A' AND stsacc <> 'W'
                AND $foreign IS NOT NULL AND $foreign <> ''

                UNION ALL

                SELECT $selectEom as DimID, a.osmdlc, CONVERT(VARCHAR(6), a.periode) as PeriodeIdx
                FROM TOFLMBEOM a
                $joinEom
                WHERE (LEFT(CONVERT(VARCHAR(6), a.periode), 4) = ? OR CONVERT(VARCHAR(6), a.periode) = ?)
                AND CONVERT(VARCHAR(6), a.periode) <> '$currentPeriod'
                AND a.stsrec = 'A'
                AND a.stsacc <> 'W'
            )
            SELECT
                a.DimID,
                MAX(m.$name) as DimName,
                a.PeriodeIdx,
                SUM(CAST(a.osmdlc AS DECIMAL(18,2))) as TotalNominal
            FROM CombinedData a
            LEFT JOIN $table m ON a.DimID = m.$key
            GROUP BY a.DimID, a.PeriodeIdx
        ";

        return collect(DB::connection($this->connection)->select($sql, [$currentYear, $yoyBasePeriod]));
    }

    private function transformToLegacyMatrix(Collection $rawData, array $validPeriods, string $yoyBasePeriod, string $tglSistem): array
    {
        $grouped = $rawData->groupBy('DimID');
        $matrix = [];
        
        foreach ($grouped as $dimId => $rows) {
            $periodMap = $rows->pluck('TotalNominal', 'PeriodeIdx')->toArray();
            $yoyBase = (float)($periodMap[$yoyBasePeriod] ?? 0);
            
            $item = [
                'id' => $dimId,
                'category' => $rows->first()->DimName ?? 'Unknown (' . $dimId . ')',
                'yoy_base' => $yoyBase,
                'monthly_data' => []
            ];

            // Map each valid month
            foreach ($validPeriods as $index => $p) {
                $nominal = (float)($periodMap[$p] ?? 0);
                
                // Growth Logic: Jan uses YoY, others use MoM
                $growth = 0;
                $growthType = 'mom';

                if ($index === 0) { // January
                    $growth = $this->calculatePct($nominal, $yoyBase);
                    $growthType = 'yoy';
                } else {
                    $prevP = $validPeriods[$index - 1];
                    $prevNominal = (float)($periodMap[$prevP] ?? 0);
                    $growth = $this->calculatePct($nominal, $prevNominal);
                }

                $item['monthly_data'][] = [
                    'periode' => $p,
                    'nominal' => $nominal,
                    'growth' => $growth,
                    'growth_type' => $growthType
                ];
                
                // Shortcut for table looping
                $item['m' . ($index + 1) . '_nominal'] = $nominal;
                $item['m' . ($index + 1) . '_growth'] = $growth;
            }

            $matrix[] = $item;
        }

        // Format metadata for header
        $carbon = Carbon::createFromFormat('dmY', $tglSistem);
        $periodLabel = $carbon->translatedFormat('F Y');

        return [
            'matrix' => $matrix,
            'periods' => collect($validPeriods)->map(fn($p, $i) => [
                'key' => $p,
                'label' => Carbon::createFromFormat('Ym', $p)->startOfMonth()->translatedFormat('M'),
                'index' => $i + 1
            ])->toArray(),
            'current_period_label' => $periodLabel
        ];
    }

    private function calculatePct(float $curr, float $prev): float
    {
        if ($prev == 0) return $curr > 0 ? 100.0 : 0.0;
        return (float)round((($curr - $prev) / $prev) * 100, 2);
    }
}
