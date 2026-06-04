<?php

declare(strict_types=1);

namespace App\Repositories\Mci;

use App\Repositories\Interfaces\FinancingTunggakanRepositoryInterface;
use App\Services\Mci\MciConnectionService;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * FinancingTunggakanRepository
 *
 * Repository khusus untuk Modul Tunggakan (G5):
 * - Jatuh Tempo: Nasabah yang tglexp jatuh di bulan berjalan
 * - Coll Monitoring: Proyeksi perubahan kolektibilitas End-of-Month
 *
 * Mengikuti pola FinancingOverviewRepository:
 * - Standalone (tidak extends MciBaseRepository)
 * - Inject MciConnectionService via constructor
 * - Gunakan executeRaw() dengan mciService->getConnection() wajib dipanggil
 */
class FinancingTunggakanRepository implements FinancingTunggakanRepositoryInterface
{
    private string $connection = 'dashboard_data';

    private MciConnectionService $mciService;

    /** @var array<string, mixed> */
    private array $lastPeriodMeta = [];

    private const CACHE_TTL = 60; // 60 seconds

    public function __construct(MciConnectionService $mciService)
    {
        $this->mciService = $mciService;
    }

    public function getLastPeriodMeta(): array
    {
        return $this->lastPeriodMeta;
    }

    /**
     * Execute raw query — wajib panggil getConnection() dulu agar
     * SQL Server switch ke database aktif (snapshot bulan berjalan).
     */
    private function executeRaw(string $sql, array $params = [], bool $forceActive = true): array
    {
        try {
            // WAJIB: Force switch ke database aktif sebelum query
            if ($forceActive) {
                $this->mciService->getConnection();
            }

            return DB::connection($this->connection)->select($sql, $params);
        } catch (\Throwable $e) {
            Log::error('FinancingTunggakanRepository::executeRaw failed', [
                'error'  => $e->getMessage(),
                'params' => $params,
            ]);
            throw $e;
        }
    }

    /**
     * Jatuh Tempo List — Nasabah yang tglexp di bulan & tahun sistem berjalan.
     * Rumus saldo tabungan: sahirrp - (saldoblok + minsaldo)
     * Rumus tunggakan real-time: SUM(tagmdl - byrmdl) WHERE stsbyr != 'L'
     */
    public function getJatuhTempoList(?string $kdloc = null, ?string $kdaoh = null, int $tahun = 0, int $bulan = 0): array
    {
        $periodContext = $this->resolvePeriodContext($tahun, $bulan);
        if (! $periodContext['period_available']) {
            return [];
        }

        $activeDb  = (string) ($periodContext['source_database'] ?? $this->mciService->getActiveDatabase());
        $cacheKey  = 'financing.tunggakan.jatuhtempo.' . md5($activeDb . ($periodContext['requested_period'] ?? '') . ($kdloc ?? 'ALL') . ($kdaoh ?? 'ALL'));

        return Cache::remember($cacheKey, self::CACHE_TTL, function () use ($kdloc, $kdaoh, $periodContext) {
            $year = substr((string) $periodContext['requested_period'], 0, 4);
            $month = substr((string) $periodContext['requested_period'], 4, 2);

            // 2. Build WHERE clause + params
            // tglexp format: YYYYMMDD (e.g. '20280701') → tahun=substr(1,4), bulan=substr(5,2)
            $whereParts = [
                "a.stsrec = 'A'",
                "a.stsacc <> 'W'",
                "SUBSTRING(a.tglexp, 1, 4) = ?",
                "SUBSTRING(a.tglexp, 5, 2) = ?",
            ];
            $params = [$year, $month];

            if ($kdloc && $kdloc !== 'Semua Cabang') {
                $whereParts[] = "f.nama = ?";
                $params[]     = $kdloc;
            }
            if ($kdaoh && $kdaoh !== 'Semua AO') {
                $whereParts[] = "c.nmao = ?";
                $params[]     = $kdaoh;
            }

            $where = implode(' AND ', $whereParts);

            $sql = "
                SELECT
                    a.nokontrak,
                    a.nama,
                    a.noakad,
                    a.tgleff,
                    a.jw,
                    a.tglexp,
                    CAST(a.mdlawal  AS DECIMAL(18,2)) AS mdlawal,
                    CAST(a.osmdlc   AS DECIMAL(18,2)) AS osmdlc,
                    CAST(a.osmgnc   AS DECIMAL(18,2)) AS osmgnc,
                    CAST(a.angsmdl  AS DECIMAL(18,2)) AS angsmdl,
                    CAST(a.angsmgn  AS DECIMAL(18,2)) AS angsmgn,
                    a.haritgk,
                    CAST(a.colbaru AS INT) AS colbaru,
                    ISNULL(c.nmao, 'TANPA AO')  AS nmao,
                    ISNULL(f.nama,  '-')         AS cabang,
                    ISNULL(g.ket,   '-')         AS wilayah,
                    ISNULL(d.alamat, '-')        AS alamat,
                    ISNULL(d.hp, '-')            AS hp,
                    CAST(ISNULL(e.sahirrp,   0) AS DECIMAL(18,2)) AS sahirrp,
                    CAST(ISNULL(e.saldoblok, 0) AS DECIMAL(18,2)) AS saldoblok,
                    -- Saldo Efektif = sahirrp - (saldoblok + minsaldo), minimal 0
                    CASE
                        WHEN (ISNULL(e.sahirrp, 0) - (ISNULL(e.saldoblok, 0) + ISNULL(st.minsaldo, 0))) < 0
                            THEN 0
                        ELSE (ISNULL(e.sahirrp, 0) - (ISNULL(e.saldoblok, 0) + ISNULL(st.minsaldo, 0)))
                    END AS saving_balance,
                    -- Tunggakan real-time dari jadwal angsuran
                    ISNULL((
                        SELECT SUM(CAST(tagmdl AS DECIMAL(18,2)) - CAST(byrmdl AS DECIMAL(18,2)))
                        FROM TOFRS
                        WHERE nokontrak = a.nokontrak AND stsbyr != 'L'
                    ), 0) AS tgkmdl,
                    ISNULL((
                        SELECT SUM(CAST(tagmgn AS DECIMAL(18,2)) - CAST(byrmgn AS DECIMAL(18,2)))
                        FROM TOFRS
                        WHERE nokontrak = a.nokontrak AND stsbyr != 'L'
                    ), 0) AS tgkmgn
                FROM TOFLMB a
                LEFT JOIN AO        c  ON a.kdaoh   = c.kdao
                LEFT JOIN mCIF      d  ON a.nocif    = d.nocif
                LEFT JOIN TOFTABC   e  ON a.acpok    = e.notab
                LEFT JOIN SETUPTAB  st ON e.kodeprd  = st.kodeprd
                LEFT JOIN CABANG    f  ON a.kdloc    = f.kdloc
                LEFT JOIN WILAYAH   g  ON a.kdwil    = g.kodewil
                WHERE $where
                ORDER BY a.tglexp ASC, c.nmao ASC, CAST(a.colbaru AS INT) DESC
            ";

            return $this->executeRaw($sql, $params, false);
        });
    }

    /**
     * @return array<string, mixed>
     */
    private function resolvePeriodContext(int $tahun = 0, int $bulan = 0): array
    {
        $this->mciService->getConnection();
        $tglRow = DB::connection($this->connection)->selectOne('SELECT TOP 1 tgl FROM TANGGAL');
        $tglRaw = is_object($tglRow) ? (string) ($tglRow->tgl ?? '') : '';
        $activeYear = strlen($tglRaw) === 8 ? (int) substr($tglRaw, 4, 4) : (int) date('Y');
        $activeMonth = strlen($tglRaw) === 8 ? (int) substr($tglRaw, 2, 2) : (int) date('m');
        $activePeriod = sprintf('%04d%02d', $activeYear, $activeMonth);

        $reqTahun = $tahun > 0 ? $tahun : $activeYear;
        $reqBulan = $bulan > 0 ? max(1, min(12, $bulan)) : $activeMonth;
        $periode = sprintf('%04d%02d', $reqTahun, $reqBulan);
        $isHistorical = ($reqTahun !== $activeYear || $reqBulan !== $activeMonth);
        $snapshotDb = $isHistorical ? $this->resolveMonthlySnapshotDatabase($reqTahun, $reqBulan) : null;
        $periodAvailable = true;

        if ($snapshotDb !== null) {
            $this->mciService->switchToDatabase($snapshotDb);
        } elseif ($isHistorical) {
            $periodAvailable = false;
        }

        $sourceDb = $snapshotDb
            ?: DB::connection($this->connection)->selectOne('SELECT DB_NAME() as database_name')->database_name ?? null;

        $this->lastPeriodMeta = [
            'requested_period' => $periode,
            'active_period' => $activePeriod,
            'is_historical' => $isHistorical,
            'period_available' => $periodAvailable,
            'source_table' => 'TOFLMB',
            'source_database' => $sourceDb,
            'message' => $periodAvailable
                ? null
                : "Database snapshot untuk periode {$periode} belum dikonfigurasi.",
        ];

        return $this->lastPeriodMeta;
    }

    private function resolveMonthlySnapshotDatabase(int $year, int $month): ?string
    {
        $monthPrefixes = ['JAN', 'FEB', 'MAR', 'APR', 'MEI', 'JUN', 'JUL', 'AGT', 'SEP', 'OKT', 'NOV', 'DES'];
        $yearSuffix = substr((string) $year, -2);
        $database = env('MCI_DB_'.$monthPrefixes[$month - 1].$yearSuffix);

        return is_string($database) && $database !== '' ? $database : null;
    }

    /**
     * Coll Monitoring EOM Projection.
     *
     * Query ini menggunakan T-SQL multi-statement (DECLARE + CTE bertingkat).
     * TIDAK bisa menggunakan Query Builder Laravel biasa.
     * Dijalankan via DB::unprepared() yang mendukung multiple statements.
     *
     * One Obligor: kolektibilitas terburuk per nocif otomatis menular ke semua kontrak.
     * Manual Override: dari tabel TOFMPCOL.
     */
    public function getCollMonitoringProyeksi(?string $kdloc = null, ?string $kdaoh = null): array
    {
        $activeDb = $this->mciService->getActiveDatabase();
        $cacheKey = 'financing.tunggakan.collmonitoring.' . md5($activeDb . ($kdloc ?? 'ALL') . ($kdaoh ?? 'ALL'));

        return Cache::remember($cacheKey, self::CACHE_TTL, function () use ($kdloc, $kdaoh) {
            // Force switch ke database aktif
            $this->mciService->getConnection();

            // Build filter WHERE tambahan (opsional)
            $extraWhere = '';
            if ($kdloc && $kdloc !== 'Semua Cabang') {
                $extraWhere .= " AND cab.nama = " . DB::connection($this->connection)->getPdo()->quote($kdloc);
            }
            if ($kdaoh && $kdaoh !== 'Semua AO') {
                $extraWhere .= " AND aotable.nmao = " . DB::connection($this->connection)->getPdo()->quote($kdaoh);
            }

            // Query T-SQL raksasa — dijalankan sebagai satu string via select()
            // PDO SQLSRV mendukung multiple statements dalam satu execute jika
            // ATTR_EMULATE_PREPARES = true (default di sqlsrv driver).
            // Namun untuk keamanan, kita pisahkan DECLARE ke dalam subquery inline.
            $sql = "
                WITH
                SystemDate AS (
                    SELECT
                        tgl AS TglRaw,
                        -- tgl format = ddmmYYYY, konversi ke DATE
                        CONVERT(DATE,
                            SUBSTRING(tgl,5,4) + SUBSTRING(tgl,3,2) + SUBSTRING(tgl,1,2)
                        ) AS TglHitung,
                        -- Legacy EOM replacement (SQL 2008 compatible)
                        DATEADD(month, DATEDIFF(month, 0, 
                            CONVERT(DATE, SUBSTRING(tgl,5,4) + SUBSTRING(tgl,3,2) + SUBSTRING(tgl,1,2))
                        ) + 1, 0) - 1 AS TglEOM,
                        DATEDIFF(dd,
                            CONVERT(DATE, SUBSTRING(tgl,5,4) + SUBSTRING(tgl,3,2) + SUBSTRING(tgl,1,2)),
                            DATEADD(month, DATEDIFF(month, 0, 
                                CONVERT(DATE, SUBSTRING(tgl,5,4) + SUBSTRING(tgl,3,2) + SUBSTRING(tgl,1,2))
                            ) + 1, 0) - 1
                        ) AS SisaHari
                    FROM TANGGAL
                    WHERE tgl = (SELECT MAX(tgl) FROM TANGGAL)
                ),
                TunggakanSummary AS (
                    SELECT
                        t1.nokontrak,
                        ISNULL(
                            (SUM(CAST(t1.byrmgn AS DECIMAL(18,2))) * 1.0)
                            / NULLIF(SUM(CAST(t1.tagmgn AS DECIMAL(18,2))), 0)
                        , 0) AS RatioBH_Calc
                    FROM TOFRS t1
                    CROSS JOIN SystemDate sd
                    -- tgltagih format YYYYMMDD — pakai CASE + ISDATE (Legacy 2008 compatibility)
                    WHERE (CASE WHEN ISDATE(NULLIF(REPLACE(t1.tgltagih,' ',''), '')) = 1 
                                THEN CONVERT(DATE, NULLIF(REPLACE(t1.tgltagih,' ',''), ''), 112) 
                                ELSE NULL END) <= sd.TglEOM
                    GROUP BY t1.nokontrak
                ),
                RestrukSummary AS (
                    SELECT nokontrak, MAX([ke]) AS Restruk_Ke
                    FROM TOFLMBHP
                    GROUP BY nokontrak
                ),
                OverridePolicy AS (
                    SELECT * FROM (
                        SELECT
                            LTRIM(RTRIM(nokontrak)) AS nokontrak,
                            colbaru AS Col_Manual,
                            (CASE WHEN ISDATE(NULLIF(REPLACE(tglexp,' ',''), '')) = 1 
                                  THEN CONVERT(DATE, NULLIF(REPLACE(tglexp,' ',''), ''), 112) 
                                  ELSE NULL END) AS Tgl_Exp_Manual,
                            LTRIM(RTRIM(ket)) AS Ket_Manual,
                            ROW_NUMBER() OVER(
                                PARTITION BY LTRIM(RTRIM(nokontrak))
                                ORDER BY tglexp DESC
                            ) AS RN
                        FROM TOFMPCOL
                    ) op WHERE op.RN = 1
                ),
                FaktaDanAturan AS (
                    SELECT
                        tflmb.nocif, tflmb.nokontrak, tflmb.nama, tflmb.kdcol,
                        tflmb.tgleff, tflmb.jw, tflmb.tglexp,
                        CAST(tflmb.mdlawal AS DECIMAL(18,2)) AS mdlawal,
                        CAST(tflmb.osmdlc  AS DECIMAL(18,2)) AS osmdlc,
                        CAST(tflmb.osmgnc  AS DECIMAL(18,2)) AS osmgnc,
                        tflmb.haritgkmdl, tflmb.haritgkmgn, tflmb.haritgk,
                        tflmb.coleom, tflmb.colbaru, tflmb.kdaoh,
                        ISNULL(aotable.nmao,       'TANPA AO')   AS nmao,
                        ISNULL(setuploan.ket, 'PRODUK LAIN') AS nama_produk,
                        ISNULL(cab.nama, '-')               AS Nama_Kantor_Cabang,
                        ISNULL(wil.ket,  '-')               AS Nama_Kantor_Pelayanan_Wilayah,
                        ISNULL(seg.ket,  '-')               AS Segmentasi_Pembiayaan,
                        ISNULL(guna.ket, '-')               AS Jenis_Penggunaan,
                        ISNULL(ts.RatioBH_Calc, 0)          AS RatioBH_Calc,
                        -- tglexp format YYYYMMDD, tgleff format YYYYMMDD → CASE + ISDATE (Legacy 2008 compatibility)
                        (CASE WHEN ISDATE(NULLIF(REPLACE(tflmb.tglexp,' ',''), '')) = 1 
                              THEN CONVERT(DATE, NULLIF(REPLACE(tflmb.tglexp,' ',''), ''), 112) 
                              ELSE NULL END) AS Tgl_Exp_Date,
                        (CASE WHEN ISDATE(NULLIF(REPLACE(tflmb.tgleff,' ',''), '')) = 1 
                              THEN CONVERT(DATE, NULLIF(REPLACE(tflmb.tgleff,' ',''), ''), 112) 
                              ELSE NULL END) AS Tgl_Eff_Date,
                        op.Col_Manual    AS fcl_col_manual,
                        op.Tgl_Exp_Manual AS fcl_tgl_manual,
                        op.Ket_Manual    AS fcl_ket_manual,
                        ISNULL(CAST(rs.Restruk_Ke AS VARCHAR(10)), '-') AS Restrukturisasi_Ke,

                        -- Hitung sisa hari sampai EOM
                        CASE
                            WHEN sd.TglEOM > (CASE WHEN ISDATE(NULLIF(REPLACE(tflmb.tglexp,' ',''), '')) = 1 
                                                   THEN CONVERT(DATE, NULLIF(REPLACE(tflmb.tglexp,' ',''), ''), 112) 
                                                   ELSE NULL END)
                                THEN DATEDIFF(dd, (CASE WHEN ISDATE(NULLIF(REPLACE(tflmb.tglexp,' ',''), '')) = 1 
                                                        THEN CONVERT(DATE, NULLIF(REPLACE(tflmb.tglexp,' ',''), ''), 112) 
                                                        ELSE NULL END), sd.TglEOM)
                            ELSE tflmb.haritgk + sd.SisaHari
                        END AS haritgk_eom_real,
                        CASE WHEN tflmb.haritgkmdl > 0 THEN tflmb.haritgkmdl + sd.SisaHari ELSE 0 END AS haritgkmdl_eom,
                        CASE
                            WHEN tflmb.haritgkmgn > 0 THEN tflmb.haritgkmgn + sd.SisaHari
                            WHEN (tflmb.haritgkmgn = 0 AND ts.RatioBH_Calc < 1) THEN 1 + sd.SisaHari
                            ELSE 0
                        END AS haritgkmgn_eom,

                        -- Threshold kolektibilitas per produk (dari TOFTCOL)
                        CASE WHEN tflmb.kdcol <> 20 AND ISNULL(tcol.col1mdl,0)=0 THEN 30  WHEN tflmb.kdcol=20 THEN ISNULL(tcol.col1mdl,30)  ELSE ISNULL(tcol.col1mdl,1)*30  END AS c1m,
                        CASE WHEN tflmb.kdcol <> 20 AND ISNULL(tcol.col2mdl,0)=1 THEN 90  WHEN tflmb.kdcol=20 THEN ISNULL(tcol.col2mdl,90)  ELSE ISNULL(tcol.col2mdl,3)*30  END AS c2m,
                        CASE WHEN tflmb.kdcol <> 20 AND ISNULL(tcol.col3mdl,0)=3 THEN 180 WHEN tflmb.kdcol=20 THEN ISNULL(tcol.col3mdl,180) ELSE ISNULL(tcol.col3mdl,6)*30  END AS c3m,
                        CASE WHEN tflmb.kdcol <> 20 AND ISNULL(tcol.col4mdl,0)=6 THEN 360 WHEN tflmb.kdcol=20 THEN ISNULL(tcol.col4mdl,360) ELSE ISNULL(tcol.col4mdl,12)*30 END AS c4m,
                        ISNULL(tcol.col1bh,80) AS c1b, ISNULL(tcol.col2bh,50) AS c2b,
                        ISNULL(tcol.col3bh,30) AS c3b, ISNULL(tcol.col4bh,3)  AS c4b,
                        ISNULL(tcol.col1jt,30) AS c1j, ISNULL(tcol.col2jt,90) AS c2j,
                        ISNULL(tcol.col3jt,180) AS c3j, ISNULL(tcol.col4jt,360) AS c4j,
                        sd.TglHitung, sd.TglEOM
                    FROM TOFLMB tflmb
                    CROSS JOIN SystemDate sd
                    LEFT JOIN TOFTCOL    tcol     ON tflmb.kdcol    = tcol.kdcol
                    LEFT JOIN TunggakanSummary ts ON tflmb.nokontrak = ts.nokontrak
                    LEFT JOIN OverridePolicy   op ON LTRIM(RTRIM(tflmb.nokontrak)) = op.nokontrak
                    LEFT JOIN RestrukSummary   rs ON tflmb.nokontrak = rs.nokontrak
                    LEFT JOIN AO       aotable       ON tflmb.kdaoh   = aotable.kdao
                    LEFT JOIN SETUPLOAN setuploan ON tflmb.kdprd  = setuploan.kdprd
                    LEFT JOIN SEGMEN   seg       ON tflmb.segmen  = seg.kdseg
                    LEFT JOIN CABANG   cab       ON tflmb.kdloc   = cab.kdloc
                    LEFT JOIN WILAYAH  wil       ON tflmb.kdwil   = wil.kodewil
                    LEFT JOIN TOFTLBBPRS guna    ON tflmb.gunadeb = guna.sandi AND guna.kdsandi = '015'
                    WHERE tflmb.stsrec = 'A' AND tflmb.stsacc <> 'W' $extraWhere
                ),
                ColCalculation AS (
                    SELECT
                        fa.*,
                        CASE WHEN fa.TglEOM > fa.Tgl_Exp_Date
                            THEN CASE WHEN fa.haritgk_eom_real > fa.c4j THEN 5 WHEN fa.haritgk_eom_real > fa.c3j THEN 4 WHEN fa.haritgk_eom_real > fa.c2j THEN 3 WHEN fa.haritgk_eom_real > fa.c1j THEN 2 ELSE 1 END
                            ELSE 1 END AS Calc_Eom_Jt,
                        CASE WHEN fa.TglEOM <= fa.Tgl_Exp_Date
                            THEN CASE WHEN fa.haritgkmdl_eom > fa.c4m THEN 5 WHEN fa.haritgkmdl_eom > fa.c3m THEN 4 WHEN fa.haritgkmdl_eom > fa.c2m THEN 3 WHEN fa.haritgkmdl_eom > fa.c1m THEN 2 ELSE 1 END
                            ELSE 1 END AS Calc_Eom_Mdl,
                        CASE WHEN RTRIM(fa.kdcol)='20' AND fa.TglEOM <= fa.Tgl_Exp_Date
                            THEN CASE
                                WHEN CAST(fa.RatioBH_Calc*100 AS DECIMAL(10,2)) <= CAST(fa.c4b AS DECIMAL(10,2)) THEN 5
                                WHEN CAST(fa.RatioBH_Calc*100 AS DECIMAL(10,2)) <  CAST(fa.c3b AS DECIMAL(10,2)) THEN 4
                                WHEN CAST(fa.RatioBH_Calc*100 AS DECIMAL(10,2)) <= CAST(fa.c2b AS DECIMAL(10,2)) THEN 3
                                WHEN CAST(fa.RatioBH_Calc*100 AS DECIMAL(10,2)) <  CAST(fa.c1b AS DECIMAL(10,2)) THEN 2
                                ELSE 1 END
                            ELSE 1 END AS Calc_Eom_Bh
                    FROM FaktaDanAturan fa
                ),
                OneObligorBase AS (
                    SELECT
                        *,
                        CAST(colbaru AS INT) AS Contract_Col_Curr,
                        CASE
                            WHEN fcl_ket_manual IS NOT NULL
                                AND fcl_tgl_manual >= TglEOM
                                AND DATEDIFF(MONTH, (CASE WHEN ISDATE(NULLIF(REPLACE(tgleff,' ',''), '')) = 1 
                                                          THEN CONVERT(DATE, NULLIF(REPLACE(tgleff,' ',''), ''), 112) 
                                                          ELSE NULL END), TglEOM) <= 3
                                THEN CAST(ISNULL(fcl_col_manual, colbaru) AS INT)
                            ELSE (
                                SELECT MAX(v) FROM (VALUES
                                    (CAST(colbaru AS INT)),
                                    (Calc_Eom_Jt),
                                    (Calc_Eom_Mdl),
                                    (Calc_Eom_Bh)
                                ) AS value(v)
                            )
                        END AS Contract_Col_Individu
                    FROM ColCalculation
                ),
                FinalAnalysis AS (
                    SELECT
                        *,
                        MAX(Contract_Col_Individu) OVER(PARTITION BY nocif) AS Col_One_Obligor,
                        FIRST_VALUE(nokontrak) OVER(
                            PARTITION BY nocif
                            ORDER BY Contract_Col_Individu DESC, haritgk_eom_real DESC
                        ) AS Kontrak_Penyebab
                    FROM OneObligorBase
                )
                SELECT
                    fa.TglHitung        AS Tanggal_Hitung,
                    fa.TglEOM           AS Tanggal_Proyeksi_EOM,
                    fa.nocif, fa.nokontrak, fa.nama, fa.kdcol,
                    fa.Restrukturisasi_Ke,
                    fa.tgleff           AS Tgl_Efektif,
                    fa.jw,
                    fa.tglexp           AS Tgl_Jatuh_Tempo,
                    fa.mdlawal, fa.osmdlc, fa.osmgnc,
                    fa.haritgkmdl, fa.haritgkmgn, fa.haritgk,
                    fa.haritgk_eom_real AS Hari_TGK_EOM_Real,
                    fa.coleom           AS col_awal_kontrak,
                    MAX(fa.Contract_Col_Curr) OVER(PARTITION BY fa.nocif) AS colbaru_final_curr,
                    fa.Col_One_Obligor  AS colbaru_final_eom,
                    -- Keterangan aksi — persis sama dengan legacy MDB
                    CASE
                        WHEN fa.Col_One_Obligor > fa.Contract_Col_Individu THEN
                            CASE
                                WHEN fa.Contract_Col_Individu = 1 THEN 'Pertahankan pada Kolektibilitas 1'
                                WHEN fa.Contract_Col_Individu = 2 THEN 'Kembalikan ke Kolektibilitas 1'
                                ELSE 'Kondisi Fasilitas Normal'
                            END
                            + ' | [ONE OBLIGOR] Terbawa Kol ' + CAST(fa.Col_One_Obligor AS VARCHAR)
                            + ' oleh No Kontrak: ' + fa.Kontrak_Penyebab
                        WHEN fa.fcl_ket_manual IS NOT NULL
                            AND fa.fcl_tgl_manual >= fa.TglEOM
                            AND DATEDIFF(MONTH, (CASE WHEN ISDATE(NULLIF(REPLACE(fa.tgleff,' ',''), '')) = 1 
                                                      THEN CONVERT(DATE, NULLIF(REPLACE(fa.tgleff,' ',''), ''), 112) 
                                                      ELSE NULL END), fa.TglEOM) <= 3
                            THEN 'MANUAL: ' + fa.fcl_ket_manual
                        WHEN RTRIM(fa.kdcol) = '20' THEN
                            'Bagi Hasil: Ratio ' + CAST(CAST(fa.RatioBH_Calc*100 AS DECIMAL(10,2)) AS VARCHAR) + '%'
                            + CASE
                                WHEN fa.TglEOM > fa.Tgl_Exp_Date THEN ' | STATUS: JATUH TEMPO (' + CAST(fa.haritgk_eom_real AS VARCHAR) + ' HARI)'
                                WHEN fa.haritgk_eom_real > fa.c1j THEN ' | PERINGATAN: Tunggakan ' + CAST(fa.haritgk_eom_real AS VARCHAR) + ' Hari'
                                ELSE ''
                              END
                            + CASE WHEN fa.Col_One_Obligor >= 3 THEN ' [Segera Tangani: PPKA]' ELSE '' END
                        WHEN fa.Col_One_Obligor < fa.Contract_Col_Curr AND fa.Col_One_Obligor = 1
                            THEN 'Sudah kembali ke Kolektibilitas 1 (Pertahankan untuk menjaga Portofolio)'
                        WHEN fa.Col_One_Obligor > fa.Contract_Col_Curr THEN
                            CASE
                                WHEN fa.TglEOM > fa.Tgl_Exp_Date THEN 'Maintain: Jatuh Tempo ' + CAST(fa.haritgk_eom_real AS VARCHAR) + ' Hari'
                                WHEN fa.Col_One_Obligor = 2 THEN 'Maintain ke Coll 1 & Estimasi Tunggakan: ' + CAST(fa.haritgk_eom_real AS VARCHAR) + ' Hari'
                                ELSE 'Segera Tangani: Jangan Sampai Membentuk PPKA ('
                                    + CASE WHEN fa.Col_One_Obligor=3 THEN '10%)' WHEN fa.Col_One_Obligor=4 THEN '50%)' ELSE '100%)' END
                            END
                        WHEN fa.Col_One_Obligor = fa.Contract_Col_Curr AND fa.Col_One_Obligor >= 3
                            THEN 'Segera Tangani: Sudah Membentuk PPKA ('
                                + CASE WHEN fa.Col_One_Obligor=3 THEN '10%)' WHEN fa.Col_One_Obligor=4 THEN '50%)' ELSE '100%)' END
                        WHEN fa.Col_One_Obligor = 1 THEN 'Pertahankan pada Kolektibilitas 1'
                        WHEN fa.Col_One_Obligor = 2 THEN 'Kembalikan ke Kolektibilitas 1'
                        ELSE 'Posisi Aman/Tetap'
                    END AS Keterangan_EOM_Detail,
                    fa.nmao AS Nama_AO, fa.nama_produk AS Nama_Produk, fa.kdaoh AS Kode_AO,
                    fa.Segmentasi_Pembiayaan, fa.Nama_Kantor_Cabang,
                    fa.Nama_Kantor_Pelayanan_Wilayah, fa.Jenis_Penggunaan
                FROM FinalAnalysis fa
                ORDER BY fa.nmao ASC, fa.Col_One_Obligor DESC
            ";

            return $this->executeRaw($sql);
        });
    }
}
