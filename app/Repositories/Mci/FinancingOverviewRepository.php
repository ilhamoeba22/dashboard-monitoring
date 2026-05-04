<?php

declare(strict_types=1);

namespace App\Repositories\Mci;

use App\Models\FinancingMonthlySnapshot;
use App\Services\Mci\MciConnectionService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

/**
 * FinancingOverviewRepository
 *
 * Repository untuk data overview pembiayaan:
 * - Realtime metrics (from current SQL Server database)
 * - Historical trend (from MySQL snapshots)
 * - Comparison (realtime vs historical)
 */
class FinancingOverviewRepository
{
    private string $connection = 'dashboard_data';

    private MciConnectionService $mciService;

    // Cache TTL
    private const CACHE_REALTIME = 60;     // 1 min

    private const CACHE_TREND = 3600;      // 1 hour

    private const CACHE_COMPARE = 900;     // 15 min

    public function __construct(MciConnectionService $mciService)
    {
        $this->mciService = $mciService;
    }

    // ============================================================
    // REALTIME METRICS (from current SQL Server database)
    // ============================================================

    /**
     * Get realtime overview metrics from active database
     */
    public function getRealtimeMetrics(): array
    {
        $activeDb = $this->mciService->getActiveDatabase();
        $cacheKey = 'financing.overview.realtime.'.md5($activeDb);

        return Cache::remember($cacheKey, self::CACHE_REALTIME, function () use ($activeDb) {
            // Overall metrics
            $overall = $this->queryOverall();

            // By kolektibilitas
            $byKol = $this->queryByKolektibilitas();

            // By segmen
            $bySegmen = $this->queryBySegmen();

            // By AO
            $byAo = $this->queryByAo();

            // By Produk
            $byProduk = $this->queryByProduk();

            // Jatuh Tempo Bulan Ini
            $jatuhTempo = $this->queryJatuhTempo();

            // Realisasi Bulan Kemarin (atau Berjalan)
            $realisasiKemarin = $this->queryRealisasiKemarin();

            return [
                'summary' => $overall,
                'kolektibilitas' => $byKol,
                'segmen' => $bySegmen,
                'ao' => $byAo,
                'produk' => $byProduk,
                'jatuh_tempo' => $jatuhTempo,
                'realisasi_kemarin' => $realisasiKemarin,
                'source' => 'realtime',
                'database' => $activeDb,
                'generated_at' => now()->toIso8601String(),
            ];
        });
    }

    /**
     * Query overall metrics
     */
    private function queryOverall(): array
    {
        $result = $this->executeRaw("
            SELECT 
                COUNT(*) as total_noa,
                SUM(CAST(osmdlc AS DECIMAL(18,2))) as total_osmdlc,
                SUM(CAST(osmgnc AS DECIMAL(18,2))) as total_osmgnc,
                SUM(CAST(mdlawal AS DECIMAL(18,2))) as total_mdlawal,
                AVG(CAST(colbarU AS FLOAT)) as avg_kolek,
                SUM(ISNULL(CAST(ppap AS DECIMAL(18,2)), 0)) as total_ppap
            FROM TOFLMB 
            WHERE stsrec = 'A' AND stsacc <> 'W'
        ");

        $row = $result[0] ?? null;
        $totalOsmdlc = (float) ($row->total_osmdlc ?? 0);
        $totalOsmgnc = (float) ($row->total_osmgnc ?? 0);
        
        // Total O/S HANYA dari osmdlc (Pokok)
        $totalOs = $totalOsmdlc;

        // NPF calculation (Kol 3, 4, 5)
        $npfResult = $this->executeRaw("
            SELECT 
                COUNT(*) as npf_noa,
                SUM(CAST(osmdlc AS DECIMAL(18,2))) as npf_osmdlc
            FROM TOFLMB 
            WHERE stsrec = 'A' AND stsacc <> 'W' AND colbarU IN ('3','4','5')
        ");
        $npfRow = $npfResult[0] ?? null;
        $npfOsmdlc = (float) ($npfRow->npf_osmdlc ?? 0);
        $npfPersen = $totalOsmdlc > 0 ? round(($npfOsmdlc / $totalOsmdlc) * 100, 2) : 0;

        return [
            'total_noa' => (int) ($row->total_noa ?? 0),
            'total_osmdlc' => $totalOsmdlc,
            'total_osmgnc' => $totalOsmgnc,
            'total_os' => $totalOs,
            'total_mdlawal' => (float) ($row->total_mdlawal ?? 0),
            'total_ppap' => (float) ($row->total_ppap ?? 0),
            'npf_noa' => (int) ($npfRow->npf_noa ?? 0),
            'npf_osmdlc' => $npfOsmdlc,
            'npf_persen' => $npfPersen,
            'avg_kolek' => round((float) ($row->avg_kolek ?? 0), 2),
        ];
    }

    /**
     * Query metrics by kolektibilitas
     */
    private function queryByKolektibilitas(): array
    {
        $result = $this->executeRaw("
            SELECT 
                colbarU,
                COUNT(*) as noa,
                SUM(CAST(osmdlc AS DECIMAL(18,2))) as osmdlc,
                SUM(CAST(osmgnc AS DECIMAL(18,2))) as osmgnc,
                AVG(CAST(colbarU AS FLOAT)) as avg_kolek
            FROM TOFLMB 
            WHERE stsrec = 'A' AND stsacc <> 'W'
            GROUP BY colbarU
            ORDER BY colbarU
        ");

        $kolLabels = [
            '1' => 'Lancar',
            '2' => 'Dalam Pengawasan Khusus',
            '3' => 'Kurang Lancar',
            '4' => 'Diragukan',
            '5' => 'Macet',
        ];

        return collect($result)->map(function ($row) use ($kolLabels) {
            $kol = (string) $row->colbarU;
            $osmdlc = (float) $row->osmdlc;
            $osmgnc = (float) $row->osmgnc;

            return [
                'kol' => $kol,
                'label' => $kolLabels[$kol] ?? 'Unknown',
                'noa' => (int) $row->noa,
                'osmdlc' => $osmdlc,
                'osmgnc' => $osmgnc,
                'total_os' => $osmdlc + $osmgnc,
                'avg_kolek' => round((float) $row->avg_kolek, 2),
            ];
        })->toArray();
    }

    /**
     * Query metrics by segmen
     */
    private function queryBySegmen(): array
    {
        $result = $this->executeRaw("
            SELECT 
                ISNULL(s.kdseg, 'UNKNOWN') as kdseg,
                ISNULL(s.ket, 'Tidak Diketahui') as nmseg,
                COUNT(*) as noa,
                SUM(CAST(f.osmdlc AS DECIMAL(18,2))) as osmdlc,
                SUM(CASE WHEN f.colbarU IN ('3','4','5') THEN CAST(f.osmdlc AS DECIMAL(18,2)) ELSE 0 END) as npf_os
            FROM TOFLMB f
            LEFT JOIN SEGMEN s ON f.segmen = s.kdseg
            WHERE f.stsrec = 'A' AND f.stsacc <> 'W'
            GROUP BY s.kdseg, s.ket
            ORDER BY osmdlc DESC
        ");

        return collect($result)->map(function ($row) {
            $osmdlc = (float) $row->osmdlc;
            $npfOs = (float) $row->npf_os;
            return [
                'kdseg' => $row->kdseg,
                'nmseg' => $row->nmseg,
                'noa' => (int) $row->noa,
                'osmdlc' => $osmdlc,
                'npf_os' => $npfOs,
                'npf_persen' => $osmdlc > 0 ? round(($npfOs / $osmdlc) * 100, 2) : 0,
            ];
        })->toArray();
    }

    /**
     * Query metrics by AO (Account Officer)
     */
    private function queryByAo(): array
    {
        $result = $this->executeRaw("
            SELECT TOP 8
                ISNULL(a.kdao, 'UNKNOWN') as kdao,
                ISNULL(a.nmao, 'Tidak Diketahui') as nmao,
                COUNT(*) as noa,
                SUM(CAST(f.osmdlc AS DECIMAL(18,2))) as osmdlc,
                SUM(CASE WHEN f.colbarU IN ('3','4','5') THEN CAST(f.osmdlc AS DECIMAL(18,2)) ELSE 0 END) as npf_os
            FROM TOFLMB f
            LEFT JOIN AO a ON f.kdaoh = a.kdao
            WHERE f.stsrec = 'A' AND f.stsacc <> 'W'
            GROUP BY a.kdao, a.nmao
            ORDER BY osmdlc DESC
        ");

        return collect($result)->map(function ($row) {
            $osmdlc = (float) $row->osmdlc;
            $npfOs = (float) $row->npf_os;
            return [
                'kdao' => $row->kdao,
                'nmao' => $row->nmao,
                'noa' => (int) $row->noa,
                'osmdlc' => $osmdlc,
                'npf_os' => $npfOs,
                'npf_persen' => $osmdlc > 0 ? round(($npfOs / $osmdlc) * 100, 2) : 0,
            ];
        })->toArray();
    }

    /**
     * Query distribusi per Produk
     */
    private function queryByProduk(): array
    {
        $result = $this->executeRaw("
            SELECT TOP 8
                ISNULL(f.kdprd, 'UNKNOWN') as kdprd,
                ISNULL(s.ket, 'Produk Tidak Dikenal') as nmproduk,
                COUNT(*) as noa,
                SUM(CAST(f.osmdlc AS DECIMAL(18,2))) as osmdlc,
                SUM(CASE WHEN f.stsrec = 'A' AND f.colbarU IN ('3','4','5') THEN CAST(f.osmdlc AS DECIMAL(18,2)) ELSE 0 END) as npf_os
            FROM TOFLMB f
            LEFT JOIN SETUPLOAN s ON f.kdprd = s.kdprd
            WHERE f.stsrec = 'A' AND f.stsacc <> 'W'
            GROUP BY f.kdprd, s.ket
            ORDER BY osmdlc DESC
        ");

        return collect($result)->map(function ($row) {
            $osmdlc = (float) $row->osmdlc;
            $npfOs = (float) $row->npf_os;
            return [
                'kdprd' => $row->kdprd,
                'nmproduk' => $row->nmproduk,
                'noa' => (int) $row->noa,
                'osmdlc' => $osmdlc,
                'npf_os' => $npfOs,
                'npf_persen' => $osmdlc > 0 ? round(($npfOs / $osmdlc) * 100, 2) : 0,
            ];
        })->toArray();
    }

    /**
     * Query jatuh tempo bulan ini
     */
    private function queryJatuhTempo(?string $kdloc = null): array
    {
        $now = now();
        $start = $now->copy()->startOfMonth()->format('Y-m-d');
        $end = $now->copy()->endOfMonth()->format('Y-m-d');

        $query = DB::connection($this->connection)
            ->table('TOFLMB as f')
            ->leftJoin('SEGMEN as s', 'f.segmen', '=', 's.kdseg')
            ->leftJoin('AO as a', 'f.kdaoh', '=', 'a.kdao')
            ->select([
                'f.nokontrak',
                'f.nama',
                'f.tglexp',
                DB::raw('CAST(f.osmdlc AS DECIMAL(18,2)) as osmdlc'),
                DB::raw("ISNULL(s.ket, 'N/A') as nmseg"),
                DB::raw("ISNULL(a.nmao, 'N/A') as nmao")
            ])
            ->where('f.stsrec', 'A')
            ->where('f.stsacc', '<>', 'W')
            ->whereBetween('f.tglexp', [$start, $end]);

        if ($kdloc) {
            $query->where('f.kdloc', $kdloc);
        }

        $result = $query->orderBy('f.tglexp', 'asc')
            ->limit(20)
            ->get();

        return collect($result)->map(function ($row) {
            return [
                'nokontrak' => $row->nokontrak,
                'nama' => $row->nama,
                'tglexp' => $row->tglexp,
                'osmdlc' => (float) $row->osmdlc,
                'nmseg' => $row->nmseg,
                'nmao' => $row->nmao,
            ];
        })->toArray();
    }

    /**
     * Query realisasi (Pencairan) bulan berjalan
     */
    private function queryRealisasiKemarin(?string $kdloc = null): array
    {
        // 1. Deteksi Tanggal Dinamis dari Database Aktif
        $activeDb = $this->mciService->getActiveDatabase();
        $dbDate = $this->mciService->parseDatabaseDate($activeDb);
        
        // Gunakan tanggal database jika ada, fallback ke Carbon::now()
        $baseDate = $dbDate ? $dbDate->copy() : Carbon::now();
        
        // Mundur 1 bulan untuk mendapatkan rentang "Bulan Kemarin" yang valid di DB ini
        $targetDate = $baseDate->copy()->subMonth();
        $startDate = $targetDate->copy()->startOfMonth()->format('Ymd');
        $endDate = $targetDate->copy()->endOfMonth()->format('Ymd');

        $query = DB::connection($this->connection)
            ->table('TOFTRNH as th')
            ->join('TOFLMB as l', 'th.dracc', '=', 'l.nokontrak')
            ->leftJoin('SEGMEN as s', 'l.segmen', '=', 's.kdseg')
            ->leftJoin('AO as a', 'l.kdaoh', '=', 'a.kdao')
            ->select([
                'l.nama',
                DB::raw('CAST(th.nominalrp AS DECIMAL(18,2)) as nominal'),
                DB::raw("ISNULL(s.ket, 'N/A') as nmseg"),
                DB::raw("ISNULL(a.nmao, 'N/A') as nmao")
            ])
            ->whereIn('th.ststrn', ['5', '6'])
            ->where('th.jnstrnlx', '01')
            ->whereBetween('th.tgltrn', [$startDate, $endDate])
            ->whereNotExists(function ($q) {
                $q->select(DB::raw(1))
                    ->from('TOFLMBHP as hp')
                    ->whereColumn('hp.nokontrak', 'l.nokontrak');
            });

        if ($kdloc) {
            $query->where('l.kdloc', $kdloc);
        }

        $result = $query->orderByDesc('th.nominalrp')
            ->limit(15)
            ->get();

        return collect($result)->map(function ($row) {
            return [
                'nama' => $row->nama,
                'nominal' => (float) $row->nominal,
                'nmseg' => $row->nmseg,
                'nmao' => $row->nmao,
            ];
        })->toArray();
    }

    // ============================================================
    // HISTORICAL TREND
    // ============================================================

    /**
     * Get Trend Data (Pencairan & NOA) dari SQL Server
     * Murni trend performa pencairan bulanan (Apple to Apple)
     */
    public function getTrendData(int $months = 12): array
    {
        $cacheKey = "financing.overview.trend.disbursement.{$months}";

        return Cache::remember($cacheKey, self::CACHE_TREND, function () use ($months) {
            // Deteksi periode maksimal dari database aktif
            $activeDb = $this->mciService->getActiveDatabase();
            $dbDate = $this->mciService->parseDatabaseDate($activeDb);
            $maxPeriod = $dbDate ? $dbDate->format('Ym') : now()->format('Ym');

            $sql = <<<'SQL'
                SELECT 
                    LEFT(tglakad, 6) as periode,
                    SUM(CAST(mdlawal AS DECIMAL(18,2))) as total_nominal,
                    COUNT(nokontrak) as total_noa
                FROM TOFLMB
                WHERE LEFT(tglakad, 6) <= ?
                  AND stsrec = 'A'
                GROUP BY LEFT(tglakad, 6)
                ORDER BY periode DESC
            SQL;

            $results = $this->executeRaw($sql, [$maxPeriod]);
            
            // Transformasi data agar sesuai dengan frontend (Values & NOA)
            // Ambil $months terakhir dan urutkan kronologis (Lama -> Baru)
            $trend = collect($results)
                ->take($months)
                ->reverse()
                ->map(function($row) {
                    return [
                        'periode' => $row->periode,
                        'total_os' => (float) $row->total_nominal, // Mapping ke total_os agar frontend tidak banyak berubah
                        'total_noa' => (int) $row->total_noa
                    ];
                })
                ->values()
                ->toArray();

            return [
                'series' => $trend,
                'months_count' => count($trend),
                'generated_at' => now()->toIso8601String(),
            ];
        });
    }

    public function getHistoricalTrend(int $months = 12): array
    {
        $cacheKey = "financing.overview.trend.{$months}";

        return Cache::remember($cacheKey, self::CACHE_TREND, function () use ($months) {
            $start = now()->subMonths($months - 1)->startOfMonth()->format('Y-m');
            $end = now()->format('Y-m');

            $snapshots = FinancingMonthlySnapshot::query()
                ->overall()
                ->whereBetween('tahun_bulan', [$start, $end])
                ->orderBy('tahun_bulan')
                ->get();

            $labels = [];
            $series = [
                'noa' => [], 'osmdlc' => [], 'osmgnc' => [],
                'npf_persen' => [], 'avg_kolek' => [],
            ];

            foreach ($snapshots as $snapshot) {
                $labels[] = $this->formatBulanLabel($snapshot->tahun_bulan);
                $series['noa'][] = $snapshot->total_noa;
                $series['osmdlc'][] = round($snapshot->total_osmdlc / 1000000, 2);
                $series['osmgnc'][] = round($snapshot->total_osmgnc / 1000000, 2);
                $series['npf_persen'][] = round((float) $snapshot->npf_persen, 2);
                $series['avg_kolek'][] = round((float) $snapshot->avg_kolek, 2);
            }

            return [
                'labels' => $labels, 'series' => $series,
                'months_count' => count($labels),
                'generated_at' => now()->toIso8601String(),
            ];
        });
    }

    private function formatBulanLabel(string $tahunBulan): string
    {
        $monthMap = [
            '01' => 'Jan', '02' => 'Feb', '03' => 'Mar', '04' => 'Apr',
            '05' => 'Mei', '06' => 'Jun', '07' => 'Jul', '08' => 'Agt',
            '09' => 'Sep', '10' => 'Okt', '11' => 'Nov', '12' => 'Des',
        ];

        if (preg_match('/^(\d{4})-(\d{2})$/', $tahunBulan, $matches)) {
            return $monthMap[$matches[2]].' '.substr($matches[1], 2);
        }

        return $tahunBulan;
    }

    // ============================================================
    // COMPARISON
    // ============================================================

    public function getComparison(?string $start = null, ?string $end = null): array
    {
        $cacheKey = 'financing.overview.compare.'.($start ?? 'default');

        return Cache::remember($cacheKey, self::CACHE_COMPARE, function () use ($start) {
            $realtime = $this->queryOverall();
            $comparisonPeriod = $start ?? now()->subMonth()->format('Y-m');

            $historical = FinancingMonthlySnapshot::query()
                ->overall()
                ->where('tahun_bulan', $comparisonPeriod)
                ->first();

            if (! $historical) {
                return [
                    'realtime' => $realtime, 'historical' => null,
                    'comparison' => null,
                ];
            }

            $historicalData = [
                'total_noa' => $historical->total_noa,
                'total_osmdlc' => (float) $historical->total_osmdlc,
                'total_osmgnc' => (float) $historical->total_osmgnc,
                'total_os' => (float) $historical->total_os_total,
                'npf_noa' => $historical->npf_noa,
                'npf_osmdlc' => (float) $historical->npf_osmdlc,
                'npf_persen' => (float) $historical->npf_persen,
                'avg_kolek' => (float) $historical->avg_kolek,
            ];

            return [
                'realtime' => $realtime, 'historical' => $historicalData,
                'comparison' => $this->calculateComparison($realtime, $historicalData),
                'comparison_period' => $comparisonPeriod,
                'generated_at' => now()->toIso8601String(),
            ];
        });
    }

    private function calculateComparison(array $current, array $previous): array
    {
        $calculateChange = function ($curr, $prev) {
            $change = $curr - $prev;
            $pct = $prev != 0 ? round(($change / $prev) * 100, 2) : 0;

            return [
                'value' => $curr,
                'change' => $change,
                'change_pct' => $pct,
                'direction' => $change > 0 ? 'up' : ($change < 0 ? 'down' : 'same'),
            ];
        };

        return [
            'noa' => $calculateChange($current['total_noa'], $previous['total_noa']),
            'osmdlc' => $calculateChange($current['total_osmdlc'], $previous['total_osmdlc']),
            'osmgnc' => $calculateChange($current['total_osmgnc'], $previous['total_osmgnc']),
            'total_os' => $calculateChange($current['total_os'], $previous['total_os']),
            'npf_noa' => $calculateChange($current['npf_noa'], $previous['npf_noa']),
            'npf_osmdlc' => $calculateChange($current['npf_osmdlc'], $previous['npf_osmdlc']),
            'npf_persen' => $calculateChange($current['npf_persen'], $previous['npf_persen']),
            'avg_kolek' => $calculateChange($current['avg_kolek'], $previous['avg_kolek']),
        ];
    }

    /**
     * Get comparison between multiple periods
     */
    public function getMultiPeriodComparison(array $periods): array
    {
        $activeDb = $this->mciService->getActiveDatabase();
        $results = [];

        // 1. Get Realtime data first
        $realtime = $this->queryOverall();
        $realtimePeriod = $this->mciService->parseDatabaseDate($activeDb);
        $results[] = [
            'period' => $activeDb,
            'label' => $realtimePeriod ? $realtimePeriod->format('M Y') : 'Realtime',
            'total_os' => $realtime['total_os'],
            'total_npf' => $realtime['npf_osmdlc'],
            'npf_persen' => $realtime['npf_persen'],
        ];

        // 2. Loop through historical periods
        foreach ($periods as $period) {
            if ($period === $activeDb) continue;

            try {
                $this->mciService->switchToDatabase($period);
                $data = $this->queryOverall();
                $date = $this->mciService->parseDatabaseDate($period);

                $results[] = [
                    'period' => $period,
                    'label' => $date ? $date->format('M Y') : $period,
                    'total_os' => $data['total_os'],
                    'total_npf' => $data['npf_osmdlc'],
                    'npf_persen' => $data['npf_persen'],
                ];
            } catch (\Throwable $e) {
                continue;
            }
        }

        $this->mciService->resetToDefault();

        // Sort results by date
        usort($results, function ($a, $b) {
            $dateA = $this->mciService->parseDatabaseDate($a['period']);
            $dateB = $this->mciService->parseDatabaseDate($b['period']);
            if (!$dateA || !$dateB) return 0;
            return $dateA->timestamp <=> $dateB->timestamp;
        });

        return [
            'series' => [], // For ApexCharts format if needed directly
            'raw_data' => $results,
            'generated_at' => now()->toIso8601String(),
        ];
    }

    private function executeRaw(string $sql, array $params = []): array
    {
        try {
            return DB::connection($this->connection)->select($sql, $params);
        } catch (\Throwable $e) {
            \Log::error('FinancingOverviewRepository::executeRaw failed', [
                'sql' => $sql,
                'params' => $params,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }
}
