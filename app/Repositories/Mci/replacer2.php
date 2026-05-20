<?php

$content = file_get_contents('c:\laragon\www\dashboard\monitoring-dashboard\app\Repositories\Mci\FinancingRepository.php');
$lines = explode("\n", $content);

$start = 602; // 0-indexed for line 603
$end = 881; // 0-indexed for line 882

$replacement = <<<EOT
    public function getQualityAnalytics(string \$groupBy = 'cabang', string \$cabang = '', int \$tahun = 0, int \$bulan = 0, string \$segmen = ''): array
    {
        \$validGroups = ['cabang', 'produk', 'ao'];
        if (! in_array(\$groupBy, \$validGroups, true)) {
            \$groupBy = 'cabang';
        }

        \$tahunKey  = \$tahun > 0 ? \$tahun : 'all';
        \$bulanKey  = \$bulan > 0 ? \$bulan : 'all';
        \$segmenKey = \$segmen ?: 'all';
        \$cabangKey = \$cabang ?: 'all';

        \$cacheKey = "financing:quality_analytics:g3:{\$groupBy}:{\$cabangKey}:{\$tahunKey}:{\$bulanKey}:{\$segmenKey}";
        \$start    = microtime(true);
        \$memory   = memory_get_usage(true);

        \$data = Cache::remember(\$cacheKey, 60, function () use (\$groupBy, \$cabang, \$tahun, \$bulan, \$segmen): array {
            \$dimConfig     = \$this->resolveDimensionConfig(\$groupBy);
            \$joinClause    = \$dimConfig['join'];
            \$labelSelect   = \$dimConfig['label'];

            // ── Mapping bulan angka → nama Indonesia (safe, tanpa FORMAT()) ──
            \$monthNames = [
                '01'=>'Jan','02'=>'Feb','03'=>'Mar','04'=>'Apr','05'=>'Mei',
                '06'=>'Jun','07'=>'Jul','08'=>'Ags','09'=>'Sep',
                '10'=>'Okt','11'=>'Nov','12'=>'Des',
            ];

            // ── Filter Builder ──
            \$bindCabang = [];
            \$strCabang = '';
            if (\$cabang !== '') {
                \$strCabang = " AND a.kdloc = ?";
                \$bindCabang[] = \$cabang;
            }

            \$bindSegmen = [];
            \$strSegmen = '';
            if (\$segmen !== '') {
                \$strSegmen = " AND a.segmen = ?";
                \$bindSegmen[] = \$segmen;
            }

            \$currentYear = (int)date('Y');
            \$currentMonth = (int)date('m');

            \$reqTahun = \$tahun > 0 ? \$tahun : \$currentYear;
            \$reqBulan = \$bulan > 0 ? \$bulan : \$currentMonth;

            \$isHistoris = (\$reqTahun !== \$currentYear || \$reqBulan !== \$currentMonth);
            \$tableName = \$isHistoris ? 'TOFLMBEOM' : 'TOFLMB';

            \$bindPeriode = [];
            \$strPeriode = '';
            if (\$isHistoris) {
                \$periodeStr = sprintf('%04d%02d', \$reqTahun, \$reqBulan);
                \$strPeriode = " AND a.periode = ?";
                \$bindPeriode[] = \$periodeStr;
            }

            // Gabungan Filter Utama (Cabang + Segmen + Periode)
            \$mainFilter = \$strCabang . \$strSegmen . \$strPeriode;
            \$mainBindings = array_merge(\$bindCabang, \$bindSegmen, \$bindPeriode);

            // Filter CabangCompare (Segmen + Periode, tanpa Cabang)
            \$cabangCompareFilter = \$strSegmen . \$strPeriode;
            \$cabangCompareBindings = array_merge(\$bindSegmen, \$bindPeriode);

            // 1. Kolektibilitas OJK Aggregation
            \$kolRows = DB::connection(\$this->connection)->select("
                SELECT a.colbaru as kol, SUM(CAST(a.osmdlc AS DECIMAL(18,4))) AS total_os
                FROM {\$tableName} a
                WHERE a.stsrec = 'A' AND a.stsacc <> 'W' {\$mainFilter}
                GROUP BY a.colbaru
            ", \$mainBindings);

            // 2. Risk Concentration by Akad
            \$akadRows = DB::connection(\$this->connection)->select("
                SELECT ISNULL(p.ket, 'Tanpa Akad') AS akad,
                    SUM(CAST(a.osmdlc AS DECIMAL(18,4))) AS total_os,
                    SUM(CASE WHEN a.colbaru IN ('3','4','5') THEN CAST(a.osmdlc AS DECIMAL(18,4)) ELSE 0 END) AS npf_os
                FROM {\$tableName} a LEFT JOIN SETUPLOAN p ON a.kdprd = p.kdprd
                WHERE a.stsrec = 'A' AND a.stsacc <> 'W' {\$mainFilter}
                GROUP BY p.ket ORDER BY npf_os DESC
            ", \$mainBindings);

            // 3. Aging Buckets
            \$agingRows = DB::connection(\$this->connection)->select("
                WITH BaseData AS (
                    SELECT {\$labelSelect} AS label, a.haritgk, CAST(a.osmdlc AS DECIMAL(18,4)) as osmdlc
                    FROM {\$tableName} a {\$joinClause}
                    WHERE a.stsrec = 'A' AND a.stsacc <> 'W' {\$mainFilter}
                )
                SELECT label,
                    SUM(CASE WHEN haritgk = 0 THEN osmdlc ELSE 0 END) AS aging_0,
                    SUM(CASE WHEN haritgk BETWEEN 1 AND 30 THEN osmdlc ELSE 0 END) AS aging_1_30,
                    SUM(CASE WHEN haritgk BETWEEN 31 AND 60 THEN osmdlc ELSE 0 END) AS aging_31_60,
                    SUM(CASE WHEN haritgk BETWEEN 61 AND 90 THEN osmdlc ELSE 0 END) AS aging_61_90,
                    SUM(CASE WHEN haritgk > 90 THEN osmdlc ELSE 0 END) AS aging_npf,
                    SUM(osmdlc) AS total_os
                FROM BaseData GROUP BY label HAVING SUM(osmdlc) > 0 ORDER BY total_os DESC
            ", \$mainBindings);

            // 4. Branch NPF Comparison
            \$branchCompareRows = DB::connection(\$this->connection)->select("
                SELECT c.nama as cabang,
                    SUM(CAST(a.osmdlc AS DECIMAL(18,4))) as total_os,
                    SUM(CASE WHEN a.colbaru IN ('3','4','5') THEN CAST(a.osmdlc AS DECIMAL(18,4)) ELSE 0 END) as npf_os,
                    CASE WHEN SUM(CAST(a.osmdlc AS DECIMAL(18,4))) > 0
                        THEN (SUM(CASE WHEN a.colbaru IN ('3','4','5') THEN CAST(a.osmdlc AS DECIMAL(18,4)) ELSE 0 END)
                              / NULLIF(SUM(CAST(a.osmdlc AS DECIMAL(18,4))), 0)) * 100
                        ELSE 0 END as npf_ratio
                FROM {\$tableName} a JOIN CABANG c ON a.kdloc = c.kdloc
                WHERE a.stsrec = 'A' AND a.stsacc <> 'W' {\$cabangCompareFilter}
                GROUP BY c.nama ORDER BY npf_ratio DESC
            ", \$cabangCompareBindings);

            // 5. Top High-Risk Obligors (Enterprise Grid)
            \$alertRows = DB::connection(\$this->connection)->select("
                SELECT TOP 15
                    a.nokontrak, a.nama, ISNULL(p.ket,'Unknown') as jenis_akad,
                    CAST(a.osmdlc AS DECIMAL(18,4)) as osmdlc,
                    CAST(a.tgkmdl AS DECIMAL(18,4)) as tgkmdl,
                    a.haritgk, a.colbaru,
                    ISNULL(CAST(a.htgagun AS DECIMAL(18,4)), 0) as htgagun,
                    ISNULL(CAST(a.ppap AS DECIMAL(18,4)), 0) as ppap
                FROM {\$tableName} a LEFT JOIN SETUPLOAN p ON a.kdprd = p.kdprd
                WHERE a.stsrec = 'A' AND a.stsacc <> 'W' AND a.colbaru IN ('2','3','4','5') {\$mainFilter}
                ORDER BY a.osmdlc DESC, a.haritgk DESC
            ", \$mainBindings);

            // 6. NPF Trend bulanan — selalu gunakan TOFLMBEOM karena melihat histori.
            \$reqBulanStr = str_pad((string)\$reqBulan, 2, '0', STR_PAD_LEFT);
            \$trendFilter = \$strCabang . \$strSegmen;
            \$trendBindings = array_merge(\$bindCabang, \$bindSegmen);
            
            \$trendRows = DB::connection(\$this->connection)->select("
                SELECT RIGHT(periode, 2) as bulan,
                    SUM(CAST(osmdlc AS DECIMAL(18,4))) as total_os,
                    SUM(CASE WHEN colbaru IN ('3','4','5') THEN CAST(osmdlc AS DECIMAL(18,4)) ELSE 0 END) as npf_os,
                    SUM(ISNULL(CAST(ppap AS DECIMAL(18,4)), 0)) as total_ppap
                FROM TOFLMBEOM a
                WHERE LEFT(periode, 4) = '{\$reqTahun}' AND RIGHT(periode, 2) <= '{\$reqBulanStr}'
                  AND stsrec = 'A' AND stsacc <> 'W' {\$trendFilter}
                GROUP BY periode ORDER BY periode ASC
            ", \$trendBindings);

            // Map angka bulan ke nama Indonesia di PHP
            \$trendMapped = array_map(function (\$row) use (\$monthNames) {
                \$bStr = str_pad((string)\$row->bulan, 2, '0', STR_PAD_LEFT);
                return (object)[
                    'bulan'      => \$monthNames[\$bStr] ?? "Bln {\$bStr}",
                    'total_os'   => \$row->total_os,
                    'npf_os'     => \$row->npf_os,
                    'total_ppap' => \$row->total_ppap,
                ];
            }, \$trendRows);

            // 7. ECL / CKPN Staging (PSAK 71)
            \$eclRows = DB::connection(\$this->connection)->select("
                SELECT
                    SUM(CASE WHEN a.colbaru = '1' THEN CAST(a.ppap AS DECIMAL(18,4)) ELSE 0 END) as ckpn_stage_1,
                    SUM(CASE WHEN a.colbaru = '2' THEN CAST(a.ppap AS DECIMAL(18,4)) ELSE 0 END) as ckpn_stage_2,
                    SUM(CASE WHEN a.colbaru IN ('3','4','5') THEN CAST(a.ppap AS DECIMAL(18,4)) ELSE 0 END) as ckpn_stage_3
                FROM {\$tableName} a WHERE a.stsrec = 'A' AND a.stsacc <> 'W' {\$mainFilter}
            ", \$mainBindings);
            \$eclData = \$eclRows[0] ?? (object)['ckpn_stage_1'=>0,'ckpn_stage_2'=>0,'ckpn_stage_3'=>0];

            // 8. Top Obligor (BMPK Stress Test) — Top 10 debitur terbesar
            \$topObligorRows = DB::connection(\$this->connection)->select("
                SELECT TOP 10
                    a.nokontrak, LTRIM(RTRIM(a.nama)) as nama,
                    CAST(a.osmdlc AS DECIMAL(18,4)) as os, a.colbaru
                FROM {\$tableName} a
                WHERE a.stsrec = 'A' AND a.stsacc <> 'W' {\$mainFilter}
                ORDER BY a.osmdlc DESC
            ", \$mainBindings);

            // 9. AO Collectibility Matrix
            \$aoMatrixRows = DB::connection(\$this->connection)->select("
                SELECT ISNULL(b.nmao, '(Tanpa AO)') as nama_ao,
                    SUM(CAST(a.osmdlc AS DECIMAL(18,4))) as total_os,
                    SUM(CASE WHEN a.colbaru='1' THEN CAST(a.osmdlc AS DECIMAL(18,4)) ELSE 0 END) as kol1_os,
                    SUM(CASE WHEN a.colbaru='2' THEN CAST(a.osmdlc AS DECIMAL(18,4)) ELSE 0 END) as kol2_os,
                    SUM(CASE WHEN a.colbaru='3' THEN CAST(a.osmdlc AS DECIMAL(18,4)) ELSE 0 END) as kol3_os,
                    SUM(CASE WHEN a.colbaru='4' THEN CAST(a.osmdlc AS DECIMAL(18,4)) ELSE 0 END) as kol4_os,
                    SUM(CASE WHEN a.colbaru='5' THEN CAST(a.osmdlc AS DECIMAL(18,4)) ELSE 0 END) as kol5_os,
                    SUM(CASE WHEN a.colbaru IN ('3','4','5') THEN CAST(a.osmdlc AS DECIMAL(18,4)) ELSE 0 END) as npf_os,
                    CASE WHEN SUM(CAST(a.osmdlc AS DECIMAL(18,4))) > 0 THEN
                        ROUND(SUM(CASE WHEN a.colbaru IN ('3','4','5') THEN CAST(a.osmdlc AS DECIMAL(18,4)) ELSE 0 END)
                        / SUM(CAST(a.osmdlc AS DECIMAL(18,4))) * 100, 2)
                    ELSE 0 END as npf_ratio
                FROM {\$tableName} a LEFT JOIN AO b ON a.kdaoh = b.kdao
                WHERE a.stsrec = 'A' AND a.stsacc <> 'W' {\$mainFilter}
                GROUP BY a.kdaoh, b.nmao ORDER BY npf_ratio DESC
            ", \$mainBindings);

            // 10. Sector Concentration (Sekon)
            \$sectorRows = DB::connection(\$this->connection)->select("
                SELECT ISNULL(LTRIM(RTRIM(a.sekon)),'(Tanpa Sektor)') as sektor,
                    SUM(CAST(a.osmdlc AS DECIMAL(18,4))) as total_os,
                    SUM(CASE WHEN a.colbaru IN ('3','4','5') THEN CAST(a.osmdlc AS DECIMAL(18,4)) ELSE 0 END) as npf_os,
                    CASE WHEN SUM(CAST(a.osmdlc AS DECIMAL(18,4))) > 0 THEN
                        ROUND(SUM(CASE WHEN a.colbaru IN ('3','4','5') THEN CAST(a.osmdlc AS DECIMAL(18,4)) ELSE 0 END)
                        / SUM(CAST(a.osmdlc AS DECIMAL(18,4))) * 100, 2)
                    ELSE 0 END as npf_ratio
                FROM {\$tableName} a WHERE a.stsrec = 'A' AND a.stsacc <> 'W' {\$mainFilter}
                GROUP BY a.sekon ORDER BY total_os DESC
            ", \$mainBindings);

            // 11. Product Composition (Donut)
            \$productRows = DB::connection(\$this->connection)->select("
                SELECT ISNULL(p.ket,'Lainnya') as produk,
                    SUM(CAST(a.osmdlc AS DECIMAL(18,4))) as total_os,
                    SUM(CASE WHEN a.colbaru IN ('3','4','5') THEN CAST(a.osmdlc AS DECIMAL(18,4)) ELSE 0 END) as npf_os
                FROM {\$tableName} a LEFT JOIN SETUPLOAN p ON a.kdprd = p.kdprd
                WHERE a.stsrec = 'A' AND a.stsacc <> 'W' {\$mainFilter}
                GROUP BY p.ket ORDER BY total_os DESC
            ", \$mainBindings);

            // 12. Restructuring Guard (Evergreening Detector)
            \$mainFilterB = str_replace('a.', 'b.', \$mainFilter);
            
            \$restruTotal = DB::connection(\$this->connection)->select("
                SELECT COUNT(DISTINCT a.nokontrak) as total_kontrak,
                    SUM(CAST(b.osmdlc AS DECIMAL(18,4))) as total_os
                FROM TOFLMBHP a INNER JOIN {\$tableName} b ON a.nokontrak = b.nokontrak
                WHERE b.stsrec = 'A' AND b.stsacc <> 'W' {\$mainFilterB}
            ", \$mainBindings);
            \$restruTotalOS       = isset(\$restruTotal[0]->total_os)      ? (float)\$restruTotal[0]->total_os      : 0;
            \$restruTotalKontrak  = isset(\$restruTotal[0]->total_kontrak)  ? (int)\$restruTotal[0]->total_kontrak   : 0;

            \$restruFail = DB::connection(\$this->connection)->select("
                SELECT COUNT(DISTINCT b.nokontrak) as gagal_kontrak
                FROM TOFLMBHP a INNER JOIN {\$tableName} b ON a.nokontrak = b.nokontrak
                WHERE b.stsrec = 'A' AND b.stsacc <> 'W'
                  AND b.colbaru IN ('3','4','5') AND CAST(a.colnew AS VARCHAR) IN ('1','2') {\$mainFilterB}
            ", \$mainBindings);
            \$restruFailKontrak = isset(\$restruFail[0]->gagal_kontrak) ? (int)\$restruFail[0]->gagal_kontrak : 0;
            \$vintageFailureRate = \$restruTotalKontrak > 0
                ? round((\$restruFailKontrak / \$restruTotalKontrak) * 100, 2) : 0;

            // ── Global Metrics ──────────────────────────────────────────────────
            \$totalOS   = collect(\$kolRows)->sum('total_os');
            \$totalNPF  = collect(\$kolRows)->whereIn('kol', ['3','4','5'])->sum('total_os');
            \$totalFAR  = collect(\$kolRows)->whereIn('kol', ['2','3','4','5'])->sum('total_os');
            
            \$queryPPAP = DB::connection(\$this->connection)->table(\$tableName)
                ->where('stsrec','A')->where('stsacc','<>','W');
            if (\$cabang !== '') \$queryPPAP->where('kdloc', \$cabang);
            if (\$segmen !== '') \$queryPPAP->where('segmen', \$segmen);
            if (\$isHistoris) \$queryPPAP->where('periode', sprintf('%04d%02d', \$reqTahun, \$reqBulan));
            \$totalPPAP = \$queryPPAP->sum('ppap');

            \$npfGross      = \$totalOS > 0 ? round((\$totalNPF / \$totalOS) * 100, 2)                 : 0;
            \$npfNetVal     = max(0, \$totalNPF - \$totalPPAP);
            \$npfNet        = \$totalOS > 0 ? round((\$npfNetVal / \$totalOS) * 100, 2)                : 0;
            \$coverageRatio = \$totalNPF > 0 ? round((\$totalPPAP / \$totalNPF) * 100, 2)             : 0;
            \$farRatio      = \$totalOS > 0 ? round((\$totalFAR / \$totalOS) * 100, 2)                 : 0;
            \$topAkad       = collect(\$akadRows)->sortByDesc('npf_os')->first();

            \$bagiHasilOS  = collect(\$akadRows)->filter(fn (\$i) =>
                str_contains(strtolower(\$i->akad), 'mudharabah') ||
                str_contains(strtolower(\$i->akad), 'musyarakah')
            )->sum('total_os');
            \$porsiBagiHasil = \$totalOS > 0 ? round((\$bagiHasilOS / \$totalOS) * 100, 2) : 0;

            // Stress test numerics
            \$top5OS  = array_sum(array_map(fn (\$r) => (float)\$r->os, array_slice(\$topObligorRows, 0, 5)));
            \$top10OS = array_sum(array_map(fn (\$r) => (float)\$r->os, array_slice(\$topObligorRows, 0, 10)));
            \$npfIfTop5Fail  = \$totalOS > 0 ? round(((\$totalNPF + \$top5OS)  / \$totalOS) * 100, 2) : 0;
            \$npfIfTop10Fail = \$totalOS > 0 ? round(((\$totalNPF + \$top10OS) / \$totalOS) * 100, 2) : 0;
            \$restruToTotal  = \$totalOS > 0 ? round((\$restruTotalOS / \$totalOS) * 100, 2)          : 0;

            return [
                'kolektibilitas' => \$kolRows,
                'akad_risk'      => \$akadRows,
                'aging'          => \$agingRows,
                'branch_compare' => \$branchCompareRows,
                'alerts'         => \$alertRows,
                'trend'          => \$trendMapped,
                'ecl_staging'    => \$eclData,
                'top_obligor'    => \$topObligorRows,
                'ao_matrix'      => \$aoMatrixRows,
                'sector_data'    => \$sectorRows,
                'product_data'   => \$productRows,
                'restru_guard'   => [
                    'total_os_restru'       => \$restruTotalOS,
                    'total_kontrak_restru'  => \$restruTotalKontrak,
                    'restru_to_total_ratio' => \$restruToTotal,
                    'gagal_kontrak'         => \$restruFailKontrak,
                    'vintage_failure_rate'  => \$vintageFailureRate,
                ],
                'stress_test'    => [
                    'top5_os'           => \$top5OS,
                    'top10_os'          => \$top10OS,
                    'npf_gross_now'     => \$npfGross,
                    'npf_if_top5_fail'  => \$npfIfTop5Fail,
                    'npf_if_top10_fail' => \$npfIfTop10Fail,
                ],
                'summary' => [
                    'total_os'         => (float)\$totalOS,
                    'total_npf'        => (float)\$totalNPF,
                    'total_ppap'       => (float)\$totalPPAP,
                    'npf_gross'        => \$npfGross,
                    'npf_net'          => \$npfNet,
                    'coverage_ratio'   => \$coverageRatio,
                    'far_ratio'        => \$farRatio,
                    'top_akad_risk'    => \$topAkad ? \$topAkad->akad : 'N/A',
                    'porsi_bagi_hasil' => \$porsiBagiHasil,
                    'fdr'              => 82.4,
                    'composite_score'  => 2,
                    'risk_profile'     => [
                        'Kredit'=>3,'Likuiditas'=>2,'Operasional'=>2,'Kepatuhan'=>1,'Reputasi'=>2
                    ],
                ],
            ];
        });
EOT;

array_splice($lines, $start, $end - $start + 1, explode("\n", $replacement));
$new_content = implode("\n", $lines);
file_put_contents('c:\laragon\www\dashboard\monitoring-dashboard\app\Repositories\Mci\FinancingRepository.php', $new_content);

echo "Replaced by line numbers successfully!\n";
