<?php

declare(strict_types=1);

namespace App\Services\Mci;

use App\DTO\Api\v1\DashboardMetricsDTO;
use App\DTO\Api\v1\DepositoMetricsDTO;
use App\DTO\Api\v1\FinancingMetricsDTO;
use App\DTO\Api\v1\GrowthDTO;
use App\DTO\Api\v1\SavingMetricsDTO;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

/**
 * DashboardRepository
 * --------------------------------------------------------------------------
 * Repository untuk mengambil key metrics dashboard (Financing, Saving, Deposito).
 *
 * RULES APPLIED (PROJECT_MEMORY.md):
 *  RULE #5  — CTE + GROUPING SETS untuk aggregasi multi-level
 *  RULE #6  — Multi-layer caching (metrics=60s, chart=300s, branches=3600s)
 *  RULE #10 — logPerformance() dari base class auto-triggered
 *  RULE #11 — Semua logic di Repository, bukan di Controller
 */
class DashboardRepository extends MciBaseRepository
{
    protected string $tableName = 'TANGGAL';

    protected function getTableName(): string
    {
        return $this->tableName;
    }

    // =========================================================================
    // PUBLIC API
    // =========================================================================

    /**
     * Ambil tanggal sistem dari tabel TANGGAL MCI.
     * Delegasi ke base class agar tidak duplikat kode.
     */
    public function getSystemDate(): string
    {
        return $this->getSystemDateInternal();
    }

    /**
     * Pecah tanggal sistem ke array period.
     *
     * @return array{tgl: string, year: int, month: int, period: string, previous_year: int}
     */
    public function getCurrentPeriod(): array
    {
        return $this->getCurrentPeriodInternal();
    }

    /**
     * Ambil semua key metrics (Financing + Saving + Deposito) sekaligus.
     * Di-cache sebagai satu unit agar konsisten antar metric.
     * Cache key menyertakan nama connection agar history tidak pakai cache realtime.
     */
    public function getKeyMetrics(): DashboardMetricsDTO
    {
        $period      = $this->getCurrentPeriodInternal();
        $currentYear = (string) $period['year'];
        $prevYear    = (string) $period['previous_year'];
        // Sertakan connection dalam cache key agar jan/feb/mar tidak bentrok
        $cacheKey    = "mci:dashboard:key_metrics:{$this->connection}:{$currentYear}";

        /** @var DashboardMetricsDTO $result */
        $result = Cache::remember($cacheKey, self::CACHE_SHORT, function () use ($period, $currentYear, $prevYear): DashboardMetricsDTO {
            return new DashboardMetricsDTO(
                tgl:         $period['tgl'],
                year:        $period['year'],
                month:       $period['month'],
                period:      $period['period'],
                financing:   $this->getFinancingMetrics($currentYear, $prevYear),
                saving:      $this->getSavingMetrics($currentYear, $prevYear),
                deposito:    $this->getDepositoMetrics($currentYear, $prevYear),
                generatedAt: now()->toIso8601String(),
            );
        });

        return $result;
    }

    /**
     * Ambil data chart dari MySQL Data Warehouse (Tabel daily_metrics_histories).
     * Mensupport filter rentang tanggal bebas (daily).
     *
     * @return array{labels: list<string>, values: list<float>, noa: list<int>, growth: list<float>}
     */
    public function getChartDataFromWarehouse(string $type, ?string $startDate = null, ?string $endDate = null): array
    {
        // Default: 30 hari ke belakang jika tidak ada input
        $end   = $endDate ? \Carbon\Carbon::parse($endDate)->endOfDay() : now()->endOfDay();
        $start = $startDate ? \Carbon\Carbon::parse($startDate)->startOfDay() : now()->subDays(30)->startOfDay();

        // Validasi agar range tidak kebalik
        if ($start->gt($end)) {
            $temp = $start;
            $start = $end;
            $end = $temp;
        }

        // Ambil data dari MySQL
        $histories = \App\Models\Mci\DailyMetricsHistory::whereBetween('tgl_snapshot', [$start->format('Y-m-d'), $end->format('Y-m-d')])
            ->orderBy('tgl_snapshot', 'asc')
            ->get();

        $labels  = [];
        $values  = [];
        $noa     = [];
        $growth  = [];
        $prevVal = null;

        foreach ($histories as $row) {
            $labels[] = $row->tgl_snapshot->format('d M Y'); // Format misal: 27 Apr 2026

            // Tentukan field berdasarkan tipe chart
            $val = match ($type) {
                'financing' => $row->financing_os,
                'saving'    => $row->saving_saldo,
                'deposito'  => $row->deposito_saldo,
                default     => 0.0,
            };

            $currentNoa = match ($type) {
                'financing' => $row->financing_noa,
                'saving'    => $row->saving_noa,
                'deposito'  => $row->deposito_noa,
                default     => 0,
            };

            $values[] = (float) $val;
            $noa[]    = (int) $currentNoa;

            // Hitung growth harian
            $growth[] = $prevVal !== null && $prevVal > 0
                ? round((( (float)$val - $prevVal) / $prevVal) * 100, 2)
                : 0.0;

            $prevVal = (float) $val;
        }

        return [
            'labels' => $labels,
            'values' => $values,
            'noa'    => $noa,
            'growth' => $growth,
        ];
    }

    /**
     * Daftar cabang untuk filter UI.
     * Cache 1 jam karena data cabang jarang berubah.
     *
     * @return list<array{kdloc: string, nama: string}>
     */
    public function getBranchList(): array
    {
        $cacheKey = 'mci:dashboard:branches';

        /** @var list<array{kdloc: string, nama: string}> $result */
        $result = Cache::remember($cacheKey, self::CACHE_LONG, function (): array {
            return DB::connection($this->connection)
                ->table('CABANG')
                ->select('kdloc', 'nama')
                ->where('stsrec', 'A')
                ->orderBy('kdloc')
                ->get()
                ->map(fn ($b) => ['kdloc' => $b->kdloc, 'nama' => $b->nama])
                ->values()
                ->all();
        });

        return $result;
    }

    /**
     * Clear semua cache dashboard.
     * Hanya forget key yang diketahui — tidak flush seluruh cache store.
     */
    public function clearCache(): void
    {
        $period      = $this->getCurrentPeriodInternal();
        $currentYear = (string) $period['year'];

        $this->forgetMany([
            "mci:dashboard:key_metrics:{$currentYear}",
            "mci:dashboard:chart:financing:{$currentYear}",
            "mci:dashboard:chart:saving:{$currentYear}",
            "mci:dashboard:chart:deposito:{$currentYear}",
            "mci:dashboard:branches",
            $this->cacheKey('system_date'),
        ]);
    }

    // =========================================================================
    // PER-MODULE METRICS (typed DTOs)
    // =========================================================================

    /**
     * Financing metrics → FinancingMetricsDTO.
     */
    public function getFinancingMetrics(string $currentYear, string $previousYear): FinancingMetricsDTO
    {
        $rawRows = $this->queryFinancing($currentYear, $previousYear);
        $data    = $this->extractConsolidatedMetrics($rawRows, 'TotalOS', 'TotalNPF');

        return new FinancingMetricsDTO(
            totalOs:      $data['current'],
            osFormatted:  $this->formatRupiah($data['current']),
            totalNpf:     $data['secondary'],
            npfFormatted: $this->formatRupiah($data['secondary']),
            totalNoa:     $data['noa'],
            totalAo:      $data['ao'],
            growth:       GrowthDTO::fromArray($this->calculateGrowth($data['current'],   $data['prev'])),
            noaGrowth:    GrowthDTO::fromArray($this->calculateGrowth($data['noa'],        $data['prev_noa'])),
            aoGrowth:     GrowthDTO::fromArray($this->calculateGrowth($data['ao'],         $data['prev_ao'])),
            npfGrowth:    GrowthDTO::fromArray($this->calculateGrowth($data['secondary'],  $data['prev_secondary'])),
        );
    }

    /**
     * Saving metrics → SavingMetricsDTO.
     */
    public function getSavingMetrics(string $currentYear, string $previousYear): SavingMetricsDTO
    {
        $rawRows = $this->querySaving($currentYear, $previousYear);
        $data    = $this->extractConsolidatedMetrics($rawRows, 'TotalSaldo');

        return new SavingMetricsDTO(
            totalSaldo:     $data['current'],
            saldoFormatted: $this->formatRupiah($data['current']),
            totalNoa:       $data['noa'],
            totalAo:        $data['ao'],
            growth:         GrowthDTO::fromArray($this->calculateGrowth($data['current'], $data['prev'])),
            noaGrowth:      GrowthDTO::fromArray($this->calculateGrowth($data['noa'],     $data['prev_noa'])),
            aoGrowth:       GrowthDTO::fromArray($this->calculateGrowth($data['ao'],      $data['prev_ao'])),
        );
    }

    /**
     * Deposito metrics → DepositoMetricsDTO.
     */
    public function getDepositoMetrics(string $currentYear, string $previousYear): DepositoMetricsDTO
    {
        $rawRows = $this->queryDeposito($currentYear, $previousYear);
        $data    = $this->extractConsolidatedMetrics($rawRows, 'TotalSaldo', 'TotalBaghas');

        return new DepositoMetricsDTO(
            totalSaldo:     $data['current'],
            saldoFormatted: $this->formatRupiah($data['current']),
            totalBaghas:    $data['secondary'],
            baghasFormatted:$this->formatRupiah($data['secondary']),
            totalNoa:       $data['noa'],
            totalAo:        $data['ao'],
            growth:         GrowthDTO::fromArray($this->calculateGrowth($data['current'],  $data['prev'])),
            noaGrowth:      GrowthDTO::fromArray($this->calculateGrowth($data['noa'],      $data['prev_noa'])),
            aoGrowth:       GrowthDTO::fromArray($this->calculateGrowth($data['ao'],       $data['prev_ao'])),
            baghasGrowth:   GrowthDTO::fromArray($this->calculateGrowth($data['secondary'], $data['prev_secondary'])),
        );
    }

    // =========================================================================
    // RAW SQL QUERIES (RULE #5: CTE + GROUPING SETS)
    // =========================================================================

    /**
     * Query financing data: live (TOFLMB) + historis EOM (TOFLMBEOM).
     * Pakai CTE + GROUPING SETS untuk aggregasi per-cabang & konsolidasi.
     *
     * @return array<int, object>
     */
    private function queryFinancing(string $currentYear, string $previousYear): array
    {
        $sql = <<<SQL
            WITH RiwayatNasabah AS (
                -- DATA BULAN BERJALAN (LIVE dari TOFLMB)
                SELECT
                    CAST(SUBSTRING(TGL.tgl, 5, 4) AS VARCHAR(4))
                        + CAST(SUBSTRING(TGL.tgl, 3, 2) AS VARCHAR(2)) AS periode_yyyymm,
                    T1.kdaoh,
                    T1.nokontrak,
                    CAST(T1.osmdlc  AS DECIMAL(18,2)) AS TotalOS,
                    CAST(CASE WHEN T1.colbaru IN ('3','4','5') THEN T1.osmdlc ELSE 0 END AS DECIMAL(18,2)) AS TotalNPF,
                    T1.kdloc,
                    COALESCE(CG.nama, 'Konsolidasi') AS nama_cabang
                FROM TOFLMB T1
                CROSS JOIN TANGGAL TGL
                LEFT JOIN CABANG CG ON T1.kdloc = CG.kdloc
                WHERE CAST(SUBSTRING(TGL.tgl, 5, 4) AS VARCHAR(4)) = ?
                  AND T1.stsrec = 'A'
                  AND T1.stsacc <> 'W'

                UNION ALL

                -- DATA HISTORIS EOM (TOFLMBEOM)
                SELECT
                    T2.periode AS periode_yyyymm,
                    T3.kdaoh,
                    T2.nokontrak,
                    CAST(T2.osmdlc  AS DECIMAL(18,2)) AS TotalOS,
                    CAST(CASE WHEN T3.colbaru IN ('3','4','5') THEN T2.osmdlc ELSE 0 END AS DECIMAL(18,2)) AS TotalNPF,
                    T3.kdloc,
                    COALESCE(CG.nama, 'Konsolidasi') AS nama_cabang
                FROM TOFLMBEOM T2
                LEFT JOIN TOFLMB  T3 ON T2.nokontrak = T3.nokontrak
                LEFT JOIN CABANG  CG ON T3.kdloc = CG.kdloc
                WHERE LEFT(T2.periode, 4) = ?
                   OR LEFT(T2.periode, 4) = ?
            )
            SELECT
                periode_yyyymm                    AS periode,
                COALESCE(nama_cabang, 'Konsolidasi') AS nama,
                SUM(TotalOS)                      AS TotalOS,
                SUM(TotalNPF)                     AS TotalNPF,
                COUNT(DISTINCT nokontrak)         AS TotalNOA,
                COUNT(DISTINCT kdaoh)             AS TotalAO
            FROM RiwayatNasabah
            GROUP BY GROUPING SETS (
                (periode_yyyymm, nama_cabang),
                (periode_yyyymm)
            )
            ORDER BY periode_yyyymm ASC, nama ASC
        SQL;

        return $this->select($sql, [$currentYear, $currentYear, $previousYear]);
    }

    /**
     * Query saving data: live (TOFTABB) + historis EOM (TOFTABEOM).
     *
     * @return array<int, object>
     */
    private function querySaving(string $currentYear, string $previousYear): array
    {
        $sql = <<<SQL
            WITH RiwayatNasabah AS (
                SELECT
                    CAST(SUBSTRING(TGL.tgl, 5, 4) AS VARCHAR(4))
                        + CAST(SUBSTRING(TGL.tgl, 3, 2) AS VARCHAR(2)) AS periode_yyyymm,
                    T1.kodeaoh,
                    T1.notab,
                    CAST(T1.sahirrp AS DECIMAL(18,2)) AS TotalSaldo,
                    T1.kodeloc,
                    COALESCE(CG.nama, 'Konsolidasi') AS nama_cabang
                FROM TOFTABB T1
                CROSS JOIN TANGGAL TGL
                LEFT JOIN CABANG CG ON T1.kodeloc = CG.kdloc
                WHERE CAST(SUBSTRING(TGL.tgl, 5, 4) AS VARCHAR(4)) = ?
                  AND T1.stsrec = 'A'
                  AND T1.stsacc <> 'W'

                UNION ALL

                SELECT
                    T2.periode AS periode_yyyymm,
                    T3.kodeaoh,
                    T2.notab,
                    CAST(T2.sahirrp AS DECIMAL(18,2)) AS TotalSaldo,
                    T3.kodeloc,
                    COALESCE(CG.nama, 'Konsolidasi') AS nama_cabang
                FROM TOFTABEOM T2
                LEFT JOIN TOFTABB T3 ON T2.notab = T3.notab
                LEFT JOIN CABANG  CG ON T3.kodeloc = CG.kdloc
                WHERE LEFT(T2.periode, 4) = ?
                   OR LEFT(T2.periode, 4) = ?
            )
            SELECT
                periode_yyyymm                       AS periode,
                COALESCE(nama_cabang, 'Konsolidasi') AS nama,
                SUM(TotalSaldo)                      AS TotalSaldo,
                COUNT(DISTINCT notab)                AS TotalNOA,
                COUNT(DISTINCT kodeaoh)              AS TotalAO
            FROM RiwayatNasabah
            GROUP BY GROUPING SETS (
                (periode_yyyymm, nama_cabang),
                (periode_yyyymm)
            )
            ORDER BY periode_yyyymm ASC, nama ASC
        SQL;

        return $this->select($sql, [$currentYear, $currentYear, $previousYear]);
    }

    /**
     * Query deposito data: live (TOFDEP) + historis EOM (TOFDEPEOM).
     *
     * @return array<int, object>
     */
    private function queryDeposito(string $currentYear, string $previousYear): array
    {
        $sql = <<<SQL
            WITH RiwayatNasabah AS (
                SELECT
                    CAST(SUBSTRING(TGL.tgl, 5, 4) AS VARCHAR(4))
                        + CAST(SUBSTRING(TGL.tgl, 3, 2) AS VARCHAR(2)) AS periode_yyyymm,
                    T1.kodeaoh,
                    T1.nodep,
                    CAST(T1.nomrp  AS DECIMAL(18,2)) AS TotalSaldo,
                    CAST(T1.bnghtg AS DECIMAL(18,2)) AS TotalBaghas,
                    T1.kdloc,
                    COALESCE(CG.nama, 'Konsolidasi') AS nama_cabang
                FROM TOFDEP T1
                CROSS JOIN TANGGAL TGL
                LEFT JOIN CABANG CG ON T1.kdloc = CG.kdloc
                WHERE CAST(SUBSTRING(TGL.tgl, 5, 4) AS VARCHAR(4)) = ?
                  AND T1.stsrec = 'A'
                  AND T1.stsacc <> 'W'

                UNION ALL

                SELECT
                    T2.periode AS periode_yyyymm,
                    T3.kodeaoh,
                    T2.nodep,
                    CAST(T2.nomrp  AS DECIMAL(18,2)) AS TotalSaldo,
                    CAST(T2.bnghtg AS DECIMAL(18,2)) AS TotalBaghas,
                    T3.kdloc,
                    COALESCE(CG.nama, 'Konsolidasi') AS nama_cabang
                FROM TOFDEPEOM T2
                LEFT JOIN TOFDEP T3 ON T2.nodep = T3.nodep
                LEFT JOIN CABANG CG ON T3.kdloc = CG.kdloc
                WHERE LEFT(T2.periode, 4) = ?
                   OR LEFT(T2.periode, 4) = ?
            )
            SELECT
                periode_yyyymm                       AS periode,
                COALESCE(nama_cabang, 'Konsolidasi') AS nama,
                SUM(TotalSaldo)                      AS TotalSaldo,
                SUM(TotalBaghas)                     AS TotalBaghas,
                COUNT(DISTINCT nodep)                AS TotalNOA,
                COUNT(DISTINCT kodeaoh)              AS TotalAO
            FROM RiwayatNasabah
            GROUP BY GROUPING SETS (
                (periode_yyyymm, nama_cabang),
                (periode_yyyymm)
            )
            ORDER BY periode_yyyymm ASC, nama ASC
        SQL;

        return $this->select($sql, [$currentYear, $currentYear, $previousYear]);
    }

    // =========================================================================
    // DATA PROCESSING HELPERS
    // =========================================================================

    /**
     * Ekstrak metrics dari baris konsolidasi (nama = 'Konsolidasi'):
     * current, previous, noa, ao, dan secondary field opsional.
     *
     * @param  array<int, object>  $rows
     * @return array{
     *   current: float, prev: float,
     *   noa: int,       prev_noa: int,
     *   ao: int,        prev_ao: int,
     *   secondary: float, prev_secondary: float
     * }
     */
    private function extractConsolidatedMetrics(
        array $rows,
        string $primaryField,
        string $secondaryField = ''
    ): array {
        // Ambil hanya baris konsolidasi (semua cabang), sorted ASC by periode
        $consolidated = array_values(
            array_filter($rows, fn ($r) => ($r->nama ?? '') === 'Konsolidasi')
        );

        $count   = count($consolidated);
        $current = $count > 0 ? $consolidated[$count - 1] : null;
        $prev    = $count >= 2 ? $consolidated[$count - 2] : null;

        return [
            'current'        => (float) ($current?->$primaryField ?? 0),
            'prev'           => (float) ($prev?->$primaryField ?? 0),
            'noa'            => (int)   ($current?->TotalNOA ?? 0),
            'prev_noa'       => (int)   ($prev?->TotalNOA ?? 0),
            'ao'             => (int)   ($current?->TotalAO ?? 0),
            'prev_ao'        => (int)   ($prev?->TotalAO ?? 0),
            'secondary'      => $secondaryField ? (float) ($current?->$secondaryField ?? 0) : 0.0,
            'prev_secondary' => $secondaryField ? (float) ($prev?->$secondaryField ?? 0) : 0.0,
        ];
    }

    /**
     * Bangun series data untuk chart (labels + values + noa + growth per periode).
     *
     * @param  array<int, object>  $rows
     * @return array{labels: list<string>, values: list<float>, noa: list<int>, growth: list<float>}
     */
    private function buildChartSeries(array $rows, string $primaryField): array
    {
        // Ambil hanya baris konsolidasi, sorted by periode ASC
        $consolidated = array_values(
            array_filter($rows, fn ($r) => ($r->nama ?? '') === 'Konsolidasi')
        );

        $labels  = [];
        $values  = [];
        $noa     = [];
        $growth  = [];
        $prevVal = null;

        foreach ($consolidated as $row) {
            $labels[] = (string) ($row->periode ?? '');
            $val      = (float)  ($row->$primaryField ?? 0);
            $values[] = $val;
            $noa[]    = (int)   ($row->TotalNOA ?? 0);
            $growth[] = $prevVal !== null && $prevVal > 0
                ? round((($val - $prevVal) / $prevVal) * 100, 2)
                : 0.0;
            $prevVal  = $val;
        }

        return compact('labels', 'values', 'noa', 'growth');
    }
}