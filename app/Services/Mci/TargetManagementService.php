<?php

declare(strict_types=1);

namespace App\Services\Mci;

use App\Models\TargetAnnual;
use App\Models\TargetMonthly;
use App\Models\TargetAdjustment;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class TargetManagementService
{
    /**
     * Get list of targets for a specific year
     */
    public function getTargetList(int $year): array
    {
        return TargetAnnual::where('target_year', $year)
            ->get()
            ->map(function ($item) {
                // Try to find name from AO table if dimension is AO
                $name = $item->dimension_id;
                if ($item->dimension_type === 'ao') {
                    try {
                        $ao = DB::connection('dashboard_data')->table('AO')
                            ->where('kdao', $item->dimension_id)
                            ->first();
                        $name = $ao ? $ao->nmao : $item->dimension_id;
                    } catch (\Throwable) {
                        $name = "AO [" . $item->dimension_id . "]";
                    }
                }

                return [
                    'id' => $item->id,
                    'dimension_type' => $item->dimension_type,
                    'dimension_id' => $item->dimension_id,
                    'name' => $name,
                    'target_year' => $item->target_year,
                    'total_nominal' => (float) $item->total_nominal,
                ];
            })
            ->toArray();
    }

    /**
     * Process a single row from Excel import
     */
    public function processTargetImport(array $row, int $targetYear): void
    {
        // row contains: TIPE, KODE_DIMENSI, NAMA, TAHUN, JAN, FEB, ..., DES
        $type = strtolower($row['tipe'] ?? 'ao');
        $dimensionId = (string) ($row['kode_dimensi'] ?? '');
        
        if (empty($dimensionId)) {
            return;
        }

        // Calculate total nominal from months
        $monthlyValues = [];
        $totalNominal = 0;
        $months = ['jan', 'feb', 'mar', 'apr', 'mei', 'jun', 'jul', 'ags', 'sep', 'okt', 'nov', 'des'];

        foreach ($months as $index => $monthName) {
            $val = (float) ($row[$monthName] ?? 0);
            $monthlyValues[$index + 1] = $val;
            $totalNominal += $val;
        }

        // 1. Update or Create Annual Target
        $annualTarget = TargetAnnual::updateOrCreate(
            [
                'dimension_type' => $type,
                'dimension_id' => $dimensionId,
                'target_year' => $targetYear,
            ],
            [
                'total_nominal' => $totalNominal,
            ]
        );

        // 2. Update or Create Monthly Targets
        foreach ($monthlyValues as $month => $nominal) {
            TargetMonthly::updateOrCreate(
                [
                    'annual_target_id' => $annualTarget->id,
                    'month' => $month,
                ],
                [
                    'nominal_target' => $nominal,
                ]
            );
        }
    }

    /**
     * Transfer remaining target gap from one AO to another (Resign/Handover scenario)
     */
    public function transferTarget(string $fromAo, string $toAo, int $effectiveMonth, int $year): void
    {
        DB::transaction(function () use ($fromAo, $toAo, $effectiveMonth, $year) {
            // 1. Get Source Annual Target
            $sourceAnnual = TargetAnnual::where('dimension_type', 'ao')
                ->where('dimension_id', $fromAo)
                ->where('target_year', $year)
                ->first();

            if (!$sourceAnnual) {
                throw new \Exception("Target tahunan untuk AO asal tidak ditemukan.");
            }

            // 2. Get Destination Annual Target (Create if not exists)
            $destAnnual = TargetAnnual::firstOrCreate(
                [
                    'dimension_type' => 'ao',
                    'dimension_id' => $toAo,
                    'target_year' => $year,
                ],
                ['total_nominal' => 0]
            );

            $totalTransferred = 0;

            // 3. Move monthly targets from effective month onwards
            for ($m = $effectiveMonth; $m <= 12; $m++) {
                $sourceMonth = TargetMonthly::where('annual_target_id', $sourceAnnual->id)
                    ->where('month', $m)
                    ->first();

                if ($sourceMonth) {
                    $nominal = (float) $sourceMonth->nominal_target;
                    $totalTransferred += $nominal;

                    // Update Destination Month (Add nominal)
                    $destMonth = TargetMonthly::firstOrNew([
                        'annual_target_id' => $destAnnual->id,
                        'month' => $m,
                    ]);
                    $destMonth->nominal_target = (float) ($destMonth->nominal_target ?? 0) + $nominal;
                    $destMonth->save();

                    // Nullify Source Month (Following logic: sisa target masa depan dipindahkan)
                    $sourceMonth->nominal_target = 0;
                    $sourceMonth->save();
                }
            }

            // 4. Update Annual Totals
            $sourceAnnual->total_nominal = $sourceAnnual->monthlyTargets()->sum('nominal_target');
            $sourceAnnual->save();

            $destAnnual->total_nominal = $destAnnual->monthlyTargets()->sum('nominal_target');
            $destAnnual->save();

            // 5. Audit Trail
            TargetAdjustment::create([
                'dimension_type' => 'ao',
                'from_dimension_id' => $fromAo,
                'to_dimension_id' => $toAo,
                'effective_month' => $effectiveMonth,
                'target_year' => $year,
                'nominal_transferred' => $totalTransferred,
                'reason' => 'Handover/Resign (Automated Transfer)',
                'created_by' => Auth::user()?->name ?? 'System',
            ]);
        });
    }

    /**
     * Get Executive Analytics (Scorecards, Chart, Leaderboard)
     * using "Volume Pencairan Baru" as realization.
     */
    public function getExecutiveAnalytics(int $year, ?int $drilldownMonth = null): array
    {
        // 1. Fetch Target Data (Annual + Monthly)
        $annuals = TargetAnnual::where('target_year', $year)
            ->where('dimension_type', 'ao')
            ->with('monthlyTargets')
            ->get();

        if ($annuals->isEmpty()) {
            return [
                'has_data' => false,
                'year' => $year,
                'message' => "Data Target RBB Tahun {$year} belum diinisialisasi oleh Administrator."
            ];
        }

        $isWeeklyMode = $drilldownMonth !== null;
        $realizations = collect();
        $mciService = new \App\Services\Mci\MciConnectionService();

        if (!$isWeeklyMode) {
            // ==========================================
            // BULANAN (12 Bulan)
            // ==========================================
            $monthPrefixes = ['JAN', 'FEB', 'MAR', 'APR', 'MEI', 'JUN', 'JUL', 'AGT', 'SEP', 'OKT', 'NOV', 'DES'];
            $yearSuffix = substr((string)$year, 2, 2);

            for ($m = 1; $m <= 12; $m++) {
                $envKey = 'MCI_DB_' . $monthPrefixes[$m - 1] . $yearSuffix;
                $dbName = env($envKey);

                if ($dbName) {
                    try {
                        $conn = $mciService->switchToDatabase($dbName);
                        $monthlyData = $conn->select("
                            SELECT kdaoh as kdao, ? as time_idx, SUM(CAST(mdlawal AS DECIMAL(18,2))) as total_cair
                            FROM TOFLMB
                            WHERE SUBSTRING(tglakad, 1, 4) = ? 
                              AND CAST(SUBSTRING(tglakad, 5, 2) AS INT) = ?
                              AND stsrec = 'A' AND stsacc <> 'W'
                            GROUP BY kdaoh
                        ", [$m, (string)$year, $m]);
                        foreach ($monthlyData as $row) {
                            $realizations->push($row);
                        }
                    } catch (\Exception $e) { }
                } else {
                    try {
                        $monthlyData = DB::connection('dashboard_data')->select("
                            SELECT kdaoh as kdao, ? as time_idx, SUM(CAST(mdlawal AS DECIMAL(18,2))) as total_cair
                            FROM TOFLMB
                            WHERE SUBSTRING(tglakad, 1, 4) = ? AND CAST(SUBSTRING(tglakad, 5, 2) AS INT) = ?
                              AND stsrec = 'A' AND stsacc <> 'W'
                            GROUP BY kdaoh
                        ", [$m, (string)$year, $m]);
                        foreach ($monthlyData as $row) {
                            $realizations->push($row);
                        }
                    } catch (\Exception $e) { }
                }
            }
        } else {
            // ==========================================
            // MINGGUAN (Drill-down 1 Bulan -> 4/5 Minggu)
            // ==========================================
            $monthPrefixes = ['JAN', 'FEB', 'MAR', 'APR', 'MEI', 'JUN', 'JUL', 'AGT', 'SEP', 'OKT', 'NOV', 'DES'];
            $yearSuffix = substr((string)$year, 2, 2);
            $envKey = 'MCI_DB_' . $monthPrefixes[$drilldownMonth - 1] . $yearSuffix;
            $dbName = env($envKey);
            $connName = $dbName ? $dbName : 'dashboard_data';

            try {
                if ($dbName) {
                    $conn = $mciService->switchToDatabase($dbName);
                } else {
                    $conn = DB::connection('dashboard_data');
                }
                
                // Grouping by week based on Day: (DAY(tglakad) - 1) / 7 + 1
                $weeklyData = $conn->select("
                    SELECT kdaoh as kdao, 
                           (CAST(SUBSTRING(tglakad, 7, 2) AS INT) - 1) / 7 + 1 as time_idx, 
                           SUM(CAST(mdlawal AS DECIMAL(18,2))) as total_cair
                    FROM TOFLMB
                    WHERE SUBSTRING(tglakad, 1, 4) = ? 
                      AND CAST(SUBSTRING(tglakad, 5, 2) AS INT) = ?
                      AND stsrec = 'A' AND stsacc <> 'W'
                    GROUP BY kdaoh, (CAST(SUBSTRING(tglakad, 7, 2) AS INT) - 1) / 7 + 1
                ", [(string)$year, $drilldownMonth]);
                
                foreach ($weeklyData as $row) {
                    $realizations->push($row);
                }
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::warning("ExecutiveAnalytics Weekly Query Failed", [
                    'database' => $connName,
                    'error' => $e->getMessage()
                ]);
            }
        }
        
        $mciService->getConnection();

        // Fetch AO Names
        $aoNames = collect(DB::connection('dashboard_data')->table('AO')->get())->keyBy('kdao');

        // 3. Initialize Variables
        $isWeeklyMode = $drilldownMonth !== null;
        $numPeriods = $isWeeklyMode ? 5 : 12;
        $periodNames = $isWeeklyMode 
            ? ['Week 1', 'Week 2', 'Week 3', 'Week 4', 'Week 5'] 
            : ['Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agt','Sep','Okt','Nov','Des'];
            
        $chartTarget = array_fill(1, $numPeriods, 0.0);
        $chartRealisasi = array_fill(1, $numPeriods, 0.0);
        
        $totalTarget = 0.0;
        $totalRealisasi = 0.0;
        
        $currentYear = (int) date('Y');
        $currentPhysicalMonth = (int) date('n');
        
        if ($isWeeklyMode) {
            if ($year < $currentYear || ($year === $currentYear && $drilldownMonth < $currentPhysicalMonth)) {
                $currentPeriod = 5;
            } elseif ($year === $currentYear && $drilldownMonth === $currentPhysicalMonth) {
                $currentPeriod = (int) ceil(date('j') / 7);
                if ($currentPeriod > 5) $currentPeriod = 5;
            } else {
                $currentPeriod = 0;
            }
        } else {
            $currentPeriod = $currentPhysicalMonth;
            if ($year < $currentYear) {
                $currentPeriod = 12;
            } elseif ($year > $currentYear) {
                $currentPeriod = 1;
            }
        }
        
        $leaderboard = [];

        // 4. Process Data per AO
        foreach ($annuals as $annual) {
            $kdao = $annual->dimension_id;
            
            // Resolve AO Name
            $name = $kdao;
            if ($aoNames->has($kdao)) {
                $name = $aoNames->get($kdao)->nmao;
            }

            // Arrays for this AO
            $aoTargetMonths = array_fill(1, $numPeriods, 0.0);
            $aoRealMonths = array_fill(1, $numPeriods, 0.0);

            // Mapping Target
            $aoTotalTarget = 0.0;
            $aoTargetYtd = 0.0;
            
            foreach ($annual->monthlyTargets as $mt) {
                $month = (int) $mt->month;
                $val = (float) $mt->nominal_target;
                
                if ($isWeeklyMode) {
                    if ($month === $drilldownMonth) {
                        $weeklyVal = $val / 5;
                        for ($w = 1; $w <= 5; $w++) {
                            $aoTargetMonths[$w] += $weeklyVal;
                            $chartTarget[$w] += $weeklyVal;
                            $aoTotalTarget += $weeklyVal;
                            
                            if ($w <= $currentPeriod) {
                                $aoTargetYtd += $weeklyVal;
                            }
                        }
                    }
                } else {
                    $aoTargetMonths[$month] += $val;
                    $chartTarget[$month] += $val;
                    $aoTotalTarget += $val;
                    
                    if ($month <= $currentPeriod) {
                        $aoTargetYtd += $val;
                    }
                }
            }

            // Mapping Realization
            $aoTotalRealisasi = 0.0;
            $aoRealisasiData = $realizations->where('kdao', $kdao);
            foreach ($aoRealisasiData as $row) {
                $idx = (int) $row->time_idx;
                $val = (float) $row->total_cair;
                
                if ($idx >= 1 && $idx <= $numPeriods) {
                    $aoRealMonths[$idx] += $val;
                    $chartRealisasi[$idx] += $val;
                    $aoTotalRealisasi += $val;
                }
            }

            // Accumulate Globals
            $totalTarget += $aoTotalTarget;
            $totalRealisasi += $aoTotalRealisasi;

            // Calculate Metrics
            $pct = $aoTargetYtd > 0 ? round(($aoTotalRealisasi / $aoTargetYtd) * 100, 1) : 0;
            $gap = $aoTotalRealisasi - $aoTargetYtd; // Positive gap means surplus (overachieved)

            $status = 'underperforming';
            if ($pct >= 100) $status = 'overachieved';
            elseif ($pct >= 80) $status = 'on-track';

            // Calculate AO Cumulative Chart
            $aoCumTarget = [];
            $aoCumReal = [];
            $aoRunTarget = 0.0;
            $aoRunReal = 0.0;
            
            for ($i = 1; $i <= $numPeriods; $i++) {
                $aoRunTarget += $aoTargetMonths[$i];
                $aoCumTarget[] = round($aoRunTarget / 1e9, 3);
                
                if ($i <= $currentPeriod) {
                    $aoRunReal += $aoRealMonths[$i];
                    $aoCumReal[] = round($aoRunReal / 1e9, 3);
                } else {
                    $aoCumReal[] = null;
                }
            }

            $leaderboard[] = [
                'kdao'       => $kdao,
                'name'       => $name,
                'target_annual' => $aoTotalTarget,
                'target_ytd'   => $aoTargetYtd,
                'realisasi'    => $aoTotalRealisasi,
                'pct'          => $pct,
                'gap'          => $gap,
                'status'       => $status,
                'chart_target' => $aoCumTarget,
                'chart_realisasi'=> $aoCumReal,
            ];
        }

        // Sort leaderboard by pct descending
        usort($leaderboard, fn($a, $b) => $b['pct'] <=> $a['pct']);

        // 5. Calculate Scorecards
        $targetYtdTotal = 0.0;
        for ($i = 1; $i <= $currentPeriod; $i++) {
            $targetYtdTotal += $chartTarget[$i];
        }

        $pacingPct = $targetYtdTotal > 0 ? round(($totalRealisasi / $targetYtdTotal) * 100, 1) : 0;
        $sisaGap = $totalRealisasi - $targetYtdTotal;

        // Cumulative Arrays for Chart
        $cumTarget = [];
        $cumRealisasi = [];
        $runTarget = 0.0;
        $runReal = 0.0;

        for ($i = 1; $i <= $numPeriods; $i++) {
            $runTarget += $chartTarget[$i];
            $cumTarget[] = round($runTarget / 1e9, 3);
            
            if ($i <= $currentPeriod) {
                $runReal += $chartRealisasi[$i];
                $cumRealisasi[] = round($runReal / 1e9, 3);
            } else {
                $cumRealisasi[] = null;
            }
        }

        return [
            'has_data' => true,
            'year'     => $year,
            'scorecards' => [
                'total_target_annual' => round($totalTarget / 1e9, 3),
                'total_realisasi'     => round($totalRealisasi / 1e9, 3),
                'total_target_ytd'    => round($targetYtdTotal / 1e9, 3),
                'pacing_pct'          => $pacingPct,
                'gap_miliar'          => round($sisaGap / 1e9, 3),
                'current_month'       => $currentPeriod,
                'sparkline'           => array_map(fn($v) => round($v / 1e9, 3), array_slice(array_values($chartRealisasi), max(0, $currentPeriod - 6), 6)),
            ],
            'pacing_chart' => [
                'categories' => $periodNames,
                'target'     => $cumTarget,
                'realisasi'  => $cumRealisasi,
            ],
            'leaderboard' => $leaderboard,
        ];
    }
}
