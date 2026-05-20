<?php

declare(strict_types=1);

namespace App\Repositories\Mci;

use App\Repositories\Interfaces\FinancingPerformanceRepositoryInterface;
use App\Services\Mci\MciBaseRepository;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

/**
 * FinancingPerformanceRepository
 *
 * Sumber Legacy: FinancingRepaymentRateController.php & FinancingRepaymentRateNewController.php
 * Models Legacy: FinancingRepaymentRate.php & FinancingRepaymentRateNew.php
 *
 * SQL Server 2008 Compatible — No TRY_CONVERT, No EOMONTH, No FORMAT().
 */
class FinancingPerformanceRepository extends MciBaseRepository implements FinancingPerformanceRepositoryInterface
{
    protected function getTableName(): string
    {
        return 'TOFLMB';
    }

    // =========================================================================
    // G8 — REPAYMENT RATE
    // =========================================================================

    /**
     * Dapatkan daftar repayment rate keseluruhan.
     * Sumber: TOFLMB JOIN TOFRS
     * Legacy ref: FinancingRepaymentRate::getRepaymentQuery()
     *
     * @param  array<string, mixed>  $filters
     */
    public function getRepaymentRate(array $filters = []): Collection
    {
        $start  = microtime(true);
        $memory = memory_get_usage(true);

        $cacheKey = 'financing:performance:rr:' . md5(serialize($filters));

        $result = $this->remember($cacheKey, function () use ($filters): array {
            // Note: This is the exact legacy query from FinancingRepaymentRate model
            $sql = "
                DECLARE @Current_YYYYMM VARCHAR(6);

                -- 1. Mengambil YYYYMM dari TANGGAL
                SELECT TOP 1 @Current_YYYYMM = SUBSTRING(tgl, 5, 4) + SUBSTRING(tgl, 3, 2)
                FROM TANGGAL;

                -- CTE 1: Agregasi Angsuran
                WITH TAGIHAN_AGG AS (
                    SELECT
                        nokontrak,
                        ISNULL(SUM(tagmdl), 0) AS tagmdl,
                        ISNULL(SUM(tagmgn), 0) AS tagmgn,
                        MAX(CASE WHEN ISNULL(byrmdl, 0) > 0 THEN tglbyrmdl ELSE NULL END) AS tglbyrmdl_raw,
                        MAX(CASE WHEN ISNULL(byrmgn, 0) > 0 THEN tglbyrmgn ELSE NULL END) AS tglbyrmgn_raw,

                        ISNULL(SUM(CASE
                            WHEN stsbyr = 'L' THEN tagmdl
                            WHEN LEFT(tglbyrmdl, 6) = @Current_YYYYMM THEN
                                (CASE WHEN byrmdl > tagmdl THEN tagmdl ELSE byrmdl END)
                            ELSE 0
                        END), 0) AS byrmdl_capped,

                        ISNULL(SUM(CASE
                            WHEN stsbyr = 'L' THEN tagmgn
                            WHEN LEFT(tglbyrmdl, 6) = @Current_YYYYMM OR LEFT(tglbyrmgn, 6) = @Current_YYYYMM THEN
                                (CASE WHEN byrmgn > tagmgn THEN tagmgn ELSE byrmgn END)
                            ELSE 0
                        END), 0) AS byrmgn_capped,

                        MAX(tgltagih) AS Max_Tgl_Tagih
                    FROM TOFRS
                    WHERE LEFT(tgltagih, 6) = @Current_YYYYMM
                    GROUP BY nokontrak
                ),

                -- CTE BARU: Menghitung Akumulasi Sisa Tunggakan
                TUNGGAKAN_RIIL_AGG AS (
                    SELECT
                        nokontrak,
                        ISNULL(SUM(CASE WHEN tagmdl > byrmdl THEN tagmdl - byrmdl ELSE 0 END), 0) AS totaltagmdl_akumulasi,
                        ISNULL(SUM(CASE WHEN tagmgn > byrmgn THEN tagmgn - byrmgn ELSE 0 END), 0) AS totaltagmgn_akumulasi
                    FROM TOFRS
                    WHERE stsbyr <> 'L'
                    AND LEFT(tgltagih, 6) <= @Current_YYYYMM
                    GROUP BY nokontrak
                ),

                -- CTE 2: Gabungkan Master Data Pembiayaan
                BASE_DATA AS (
                    SELECT
                        A.nokontrak, A.nocif, G.alamat, A.nama AS nama_nasabah, F.ket AS nama_produk,
                        A.colbaru, A.osmdlc, A.osmgnc, A.tgleff, A.jw, A.tglexp, A.kdaoh,
                        C.nmao AS nama_ao, A.kdloc, D.nama AS nama_cabang, A.kdwil, E.ket AS nama_wilayah,
                        A.acpok, A.kdsi, A.segmen, H.ket AS nama_segmen,

                        ISNULL(B.tagmdl, 0) AS tagmdl,
                        ISNULL(B.tagmgn, 0) AS tagmgn,
                        B.tglbyrmdl_raw,
                        B.tglbyrmgn_raw,
                        ISNULL(B.byrmdl_capped, 0) AS byrmdl_capped,
                        ISNULL(B.byrmgn_capped, 0) AS byrmgn_capped,
                        B.Max_Tgl_Tagih,

                        ISNULL(T.totaltagmdl_akumulasi, 0) AS totaltagmdl_akumulasi,
                        ISNULL(T.totaltagmgn_akumulasi, 0) AS totaltagmgn_akumulasi
                    FROM TOFLMB A
                    LEFT JOIN TAGIHAN_AGG B ON A.nokontrak = B.nokontrak
                    LEFT JOIN TUNGGAKAN_RIIL_AGG T ON A.nokontrak = T.nokontrak
                    LEFT JOIN AO C ON A.kdaoh = C.kdao
                    LEFT JOIN CABANG D ON A.kdloc = D.kdloc
                    LEFT JOIN WILAYAH E ON A.kdwil = E.kodewil
                    LEFT JOIN SETUPLOAN F ON A.kdprd = F.kdprd
                    LEFT JOIN mCIF G ON A.nocif = G.nocif
                    LEFT JOIN SEGMEN H ON A.segmen = H.kdseg
                    WHERE A.stsrec = 'A' AND A.stsacc <> 'W'
                )

                -- Query Final
                SELECT
                    LTRIM(RTRIM(B.nokontrak)) AS nokontrak,
                    B.nocif,
                    B.alamat,
                    B.nama_nasabah,
                    LTRIM(RTRIM(B.colbaru)) AS colbaru,
                    B.nama_produk,
                    CAST(B.osmdlc AS FLOAT) AS osmdlc,
                    CAST(B.osmgnc AS FLOAT) AS osmgnc,
                    ISNULL(CONVERT(VARCHAR(11), CAST(B.tgleff AS DATE), 106), '-') AS tgleff,
                    B.jw,
                    ISNULL(CONVERT(VARCHAR(11), CAST(B.tglexp AS DATE), 106), '-') AS tglexp,
                    @Current_YYYYMM AS Periode_Tagihan,
                    ISNULL(CONVERT(VARCHAR(11), CAST(B.Max_Tgl_Tagih AS DATE), 106), '-') AS Max_Tgl_Tagih,
                    ISNULL(CONVERT(VARCHAR(11), CAST(B.tglbyrmdl_raw AS DATE), 106), '-') AS tglbyrmdl,
                    ISNULL(CONVERT(VARCHAR(11), CAST(B.tglbyrmgn_raw AS DATE), 106), '-') AS tglbyrmgn,

                    CAST(B.tagmdl AS FLOAT) AS tagmdl,
                    CAST(B.tagmgn AS FLOAT) AS tagmgn,
                    CAST((B.tagmdl + B.tagmgn) AS FLOAT) AS totaltag,

                    CAST(B.totaltagmdl_akumulasi AS FLOAT) AS totaltagmdl,
                    CAST(B.totaltagmgn_akumulasi AS FLOAT) AS totaltagmgn,
                    CAST((B.totaltagmdl_akumulasi + B.totaltagmgn_akumulasi) AS FLOAT) AS grand_totaltag,

                    CAST(B.byrmdl_capped AS FLOAT) AS byrmdl,
                    CAST(B.byrmgn_capped AS FLOAT) AS byrmgn,
                    CAST((B.byrmdl_capped + B.byrmgn_capped) AS FLOAT) AS totalbyr,

                    CAST(ISNULL(ROUND((B.byrmdl_capped * 100.0 / NULLIF(B.tagmdl, 0)), 2), 0) AS FLOAT) AS pctmdl,
                    CAST(ISNULL(ROUND((B.byrmgn_capped * 100.0 / NULLIF(B.tagmgn, 0)), 2), 0) AS FLOAT) AS pctmgn,
                    CAST(ISNULL(ROUND(((B.byrmdl_capped + B.byrmgn_capped) * 100.0 / NULLIF(B.tagmdl + B.tagmgn, 0)), 2), 0) AS FLOAT) AS pcttotal,

                    CAST(ISNULL(H.sahirrp - (H.saldoblok + 20000), 0) AS FLOAT) AS saldo_netto,

                    B.kdaoh, ISNULL(LTRIM(RTRIM(B.nama_ao)), 'TANPA AO') AS nama_ao,
                    B.segmen, B.nama_segmen,
                    B.kdloc, B.nama_cabang,
                    B.kdwil, B.nama_wilayah,

                    CASE
                        WHEN RTRIM(ISNULL(B.kdsi, '')) = '' THEN 'Repayment Manual'
                        WHEN B.kdsi = 'F' THEN 'Auto Debet Full Payment'
                        WHEN B.kdsi = 'M' THEN 'Auto Debet (Prioritas Margin, Modal)'
                        WHEN B.kdsi = 'N' THEN 'Repayment Manual (+ Denda)'
                        ELSE 'Repayment Manual'
                    END AS sts_autodebet

                FROM BASE_DATA B
                LEFT JOIN TOFTABC H ON B.acpok = H.notab
                ORDER BY B.nama_ao ASC, B.colbaru DESC;
            ";

            return DB::connection($this->connection)->select($sql);
        }, self::CACHE_MEDIUM);

        $this->logPerformance(__METHOD__, $start, $memory);

        $collection = collect($result)->map(function ($row) {
            // Transform types
            return (array) $row;
        });

        // Apply filters locally (server-side filtering after caching for performance on small dataset)
        if (!empty($filters['ao'])) {
            $collection = $collection->filter(fn($item) => $item['nama_ao'] === $filters['ao']);
        }
        if (!empty($filters['cabang'])) {
            $collection = $collection->filter(fn($item) => $item['nama_cabang'] === $filters['cabang']);
        }
        if (!empty($filters['segmen'])) {
            $collection = $collection->filter(fn($item) => $item['nama_segmen'] === $filters['segmen']);
        }
        if (!empty($filters['collectibility'])) {
            $collection = $collection->filter(fn($item) => $item['colbaru'] === $filters['collectibility']);
        }
        if (isset($filters['rr_min'])) {
            $collection = $collection->filter(fn($item) => ($item['pcttotal'] ?? 0) >= (float)$filters['rr_min']);
        }
        if (isset($filters['rr_max'])) {
            $collection = $collection->filter(fn($item) => ($item['pcttotal'] ?? 0) <= (float)$filters['rr_max']);
        }
        if (!empty($filters['search'])) {
            $search = strtolower($filters['search']);
            $collection = $collection->filter(function($item) use ($search) {
                return stripos($item['nama_nasabah'], $search) !== false ||
                       stripos($item['nokontrak'], $search) !== false ||
                       stripos($item['nocif'], $search) !== false;
            });
        }

        // Sorting
        $sortBy = $filters['sort_by'] ?? 'nama_ao';
        $sortOrder = $filters['sort_order'] ?? 'asc';
        $collection = $sortOrder === 'desc' 
            ? $collection->sortByDesc($sortBy) 
            : $collection->sortBy($sortBy);

        return $collection->values();
    }

    /**
     * Dapatkan summary scorecard untuk halaman Repayment Rate.
     *
     * @param  array<string, mixed>  $filters
     * @return array<string, mixed>
     */
    public function getRepaymentRateSummary(array $filters = []): array
    {
        $data = $this->getRepaymentRate($filters);

        $totalTagih = $data->sum('totaltag');
        $totalBayar = $data->sum('totalbyr');

        $overallRate = $totalTagih > 0 ? round(($totalBayar / $totalTagih) * 100, 2) : 0;

        $nasabah100 = $data->filter(fn($item) => ($item['pcttotal'] ?? 0) >= 100)->count();
        $nasabahWarning = $data->filter(fn($item) => ($item['pcttotal'] ?? 0) < 80)->count();

        return [
            'total_nasabah'   => $data->count(),
            'total_tagihan'   => $totalTagih,
            'total_pembayaran'=> $totalBayar,
            'overall_rate'    => $overallRate,
            'nasabah_100_pct' => $nasabah100,
            'nasabah_warning' => $nasabahWarning,
        ];
    }

    // =========================================================================
    // G8 — REPAYMENT RATE NEW (AKUISISI VS RETENSI)
    // =========================================================================

    /**
     * Dapatkan daftar repayment rate untuk nasabah baru.
     * Sumber: FinancingRepaymentRateNew::getRepaymentQuery()
     *
     * @param  array<string, mixed>  $filters
     */
    public function getRepaymentRateNew(array $filters = []): Collection
    {
        $start  = microtime(true);
        $memory = memory_get_usage(true);

        $onboardingMonths = (int)($filters['onboarding_months'] ?? 6);

        $cacheKey = 'financing:performance:rrnew:' . $onboardingMonths . ':' . md5(serialize($filters));

        $result = $this->remember($cacheKey, function () use ($onboardingMonths): array {
            $sql = "
                WITH DATE_PARAM AS (
                    SELECT
                        MAX(CONVERT(DATE, SUBSTRING(tgl, 5, 4) + SUBSTRING(tgl, 3, 2) + SUBSTRING(tgl, 1, 2))) AS AnalyzeDate,
                        SUBSTRING(MAX(tgl), 5, 4) + SUBSTRING(MAX(tgl), 3, 2) AS Current_YYYYMM
                    FROM TANGGAL
                ),
                BASE_AGG AS (
                    SELECT
                        LTRIM(RTRIM(A.nokontrak)) AS nokontrak, A.nama AS nama_nasabah, F.ket AS nama_produk,
                        LTRIM(RTRIM(A.colbaru)) AS colbaru, A.mdlawal, A.mgnawal, A.osmdlc, A.osmgnc, A.tgleff, A.jw, A.tglexp,
                        A.kdaoh, ISNULL(LTRIM(RTRIM(C.nmao)), 'TANPA AO') AS nama_ao, A.kdloc, D.nama AS nama_cabang,
                        A.kdwil, E.ket AS nama_wilayah, DP.Current_YYYYMM AS Periode_Tagihan,

                        -- 1. LOGIKA REPAYMENT (TAGIHAN BULAN BERJALAN SAJA)
                        ISNULL(SUM(CASE WHEN SUBSTRING(B.tgltagih, 1, 6) = DP.Current_YYYYMM THEN B.tagmdl ELSE 0 END), 0) AS tag_current_mdl,
                        ISNULL(SUM(CASE WHEN SUBSTRING(B.tgltagih, 1, 6) = DP.Current_YYYYMM THEN B.tagmgn ELSE 0 END), 0) AS tag_current_mgn,

                        -- 2. LOGIKA RECOVERY (SALDO EXPIRED / TUNGGAKAN LAMA)
                        ISNULL(CASE WHEN A.tglexp < DP.Current_YYYYMM + '01' THEN A.osmdlc ELSE 0 END, 0) AS target_recovery_mdl,
                        ISNULL(CASE WHEN A.tglexp < DP.Current_YYYYMM + '01' THEN A.osmgnc ELSE 0 END, 0) AS target_recovery_mgn,

                        -- 3. LOGIKA PEMBAYARAN (CASH IN BULAN INI)
                        ISNULL(SUM(CASE WHEN SUBSTRING(B.tglbyrmdl, 1, 6) = DP.Current_YYYYMM THEN B.byrmdl ELSE 0 END), 0) AS cash_in_mdl,
                        ISNULL(SUM(CASE WHEN SUBSTRING(B.tglbyrmdl, 1, 6) = DP.Current_YYYYMM THEN B.byrmgn ELSE 0 END), 0) AS cash_in_mgn,

                        MAX(B.tgltagih) AS Max_Tgl_Tagih,
                        
                        -- NEW: Days since onboarding
                        DATEDIFF(DAY, CAST(A.tgleff AS DATE), DP.AnalyzeDate) AS days_since_onboarding
                    FROM TOFLMB A
                    LEFT JOIN TOFRS B ON A.nokontrak = B.nokontrak
                    LEFT JOIN AO C ON A.kdaoh = C.kdao
                    LEFT JOIN CABANG D ON A.kdloc = D.kdloc
                    LEFT JOIN WILAYAH E ON A.kdwil = E.kodewil
                    LEFT JOIN SETUPLOAN F ON A.kdprd = F.kdprd
                    CROSS JOIN DATE_PARAM DP
                    WHERE A.stsrec = 'A' AND A.stsacc <> 'W'
                    -- NEW: Filter for new customers only (last N months)
                    AND CAST(A.tgleff AS DATE) >= DATEADD(MONTH, -?, DP.AnalyzeDate)
                    AND (SUBSTRING(B.tgltagih, 1, 6) = DP.Current_YYYYMM OR (A.tglexp < DP.Current_YYYYMM + '01' AND (A.osmdlc + A.osmgnc) > 0))
                    GROUP BY A.nokontrak, A.nama, F.ket, A.colbaru, A.mdlawal, A.mgnawal, A.osmdlc, A.osmgnc, A.tgleff, A.jw, A.tglexp, A.kdaoh, C.nmao, A.kdloc, D.nama, A.kdwil, E.ket, DP.Current_YYYYMM, DP.AnalyzeDate
                )
                SELECT
                    B.*,
                    -- Kalkulasi Repayment Rate (RR)
                    CAST(ISNULL(ROUND((B.cash_in_mdl + B.cash_in_mgn) * 100.0 / NULLIF(B.tag_current_mdl + B.tag_current_mgn, 0), 2), 0) AS FLOAT) AS rr_pct,

                    -- Kalkulasi Recovery Rate (RecR)
                    CAST(ISNULL(ROUND((B.cash_in_mdl + B.cash_in_mgn) * 100.0 / NULLIF(B.target_recovery_mdl + B.target_recovery_mgn, 0), 2), 0) AS FLOAT) AS recr_pct,
                    
                    -- NEW: Risk Status
                    CASE
                        WHEN (B.cash_in_mdl + B.cash_in_mgn) * 100.0 / NULLIF(B.tag_current_mdl + B.tag_current_mgn, 0) >= 90 THEN 'Good'
                        WHEN (B.cash_in_mdl + B.cash_in_mgn) * 100.0 / NULLIF(B.tag_current_mdl + B.tag_current_mgn, 0) >= 70 THEN 'Warning'
                        ELSE 'At Risk'
                    END AS risk_status
                FROM BASE_AGG B
                ORDER BY B.nama_ao ASC, B.nama_nasabah ASC;
            ";

            return DB::connection($this->connection)->select($sql, [$onboardingMonths]);
        }, self::CACHE_SHORT); // Shorter cache for new customers (15 min)

        $this->logPerformance(__METHOD__, $start, $memory);

        $collection = collect($result)->map(function ($row) {
            return (array) $row;
        });

        if (!empty($filters['ao'])) {
            $collection = $collection->filter(fn($item) => $item['nama_ao'] === $filters['ao']);
        }
        if (!empty($filters['risk_status'])) {
            $collection = $collection->filter(fn($item) => $item['risk_status'] === $filters['risk_status']);
        }
        if (!empty($filters['search'])) {
            $search = strtolower($filters['search']);
            $collection = $collection->filter(function($item) use ($search) {
                return stripos($item['nama_nasabah'], $search) !== false ||
                       stripos($item['nokontrak'], $search) !== false;
            });
        }

        // Sorting
        $sortBy = $filters['sort_by'] ?? 'tgleff';
        $sortOrder = $filters['sort_order'] ?? 'desc';
        $collection = $sortOrder === 'desc' 
            ? $collection->sortByDesc($sortBy) 
            : $collection->sortBy($sortBy);

        return $collection->values();
    }

    /**
     * Dapatkan summary scorecard untuk Repayment Rate Nasabah Baru.
     *
     * @param  array<string, mixed>  $filters
     * @return array<string, mixed>
     */
    public function getRepaymentRateNewSummary(array $filters = []): array
    {
        $data = $this->getRepaymentRateNew($filters);

        $tagihanCurrent = $data->sum(fn($item) => ($item['tag_current_mdl'] ?? 0) + ($item['tag_current_mgn'] ?? 0));
        $cashInCurrent = $data->sum(fn($item) => ($item['cash_in_mdl'] ?? 0) + ($item['cash_in_mgn'] ?? 0));
        
        $overallRr = $tagihanCurrent > 0 ? round(($cashInCurrent / $tagihanCurrent) * 100, 2) : 0;

        $targetRecovery = $data->sum(fn($item) => ($item['target_recovery_mdl'] ?? 0) + ($item['target_recovery_mgn'] ?? 0));
        $overallRecr = $targetRecovery > 0 ? round(($cashInCurrent / $targetRecovery) * 100, 2) : 0;

        // Risk breakdown
        $goodCount = $data->where('risk_status', 'Good')->count();
        $warningCount = $data->where('risk_status', 'Warning')->count();
        $atRiskCount = $data->where('risk_status', 'At Risk')->count();

        // First payment success rate (RR >= 100% on first month)
        $firstPaymentSuccess = $data->filter(fn($item) => ($item['rr_pct'] ?? 0) >= 100)->count();
        $firstPaymentRate = $data->count() > 0 ? round(($firstPaymentSuccess / $data->count()) * 100, 2) : 0;

        return [
            'total_nasabah' => $data->count(),
            'total_tagihan' => $tagihanCurrent,
            'total_cash_in' => $cashInCurrent,
            'overall_rr_pct' => $overallRr,
            'target_recovery' => $targetRecovery,
            'overall_recr_pct' => $overallRecr,
            'good_count' => $goodCount,
            'warning_count' => $warningCount,
            'at_risk_count' => $atRiskCount,
            'first_payment_success_rate' => $firstPaymentRate,
            'avg_days_since_onboarding' => $data->count() > 0 ? round($data->avg('days_since_onboarding'), 0) : 0,
        ];
    }
}
