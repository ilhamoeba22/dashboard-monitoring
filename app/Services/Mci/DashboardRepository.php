<?php

declare(strict_types=1);

namespace App\Services\Mci;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class DashboardRepository extends MciBaseRepository
{
    protected string $tableName = 'TANGGAL';

    /**
     * Get table name (required by MciBaseRepository)
     */
    protected function getTableName(): string
    {
        return $this->tableName;
    }

    /**
     * Ambil tanggal sistem dari MCI
     */
    public function getSystemDate(): string
    {
        $cacheKey = $this->cacheKey('system_date');

        return Cache::remember($cacheKey, 60, function (): string {
            $data = DB::connection($this->connection)
                ->table('TANGGAL')
                ->orderBy('tgl', 'desc')
                ->value('tgl');

            return $data ?? date('dmY');
        });
    }

    /**
     * Ambil tahun dan periode saat ini
     */
    public function getCurrentPeriod(): array
    {
        $tgl = $this->getSystemDate();
        $year = substr($tgl, -4);
        $month = substr($tgl, 2, 2);

        return [
            'tgl' => $tgl,
            'year' => (int) $year,
            'month' => (int) $month,
            'period' => $year . $month,
            'previous_year' => (int) $year - 1,
        ];
    }

    /**
     * Ambil semua data key metrics untuk dashboard
     */
    public function getKeyMetrics(): array
    {
        $period = $this->getCurrentPeriod();
        $currentYear = (string) $period['year'];
        $previousYear = (string) $period['previous_year'];
        $cacheKey = $this->cacheKey("key_metrics:{$currentYear}");

        return Cache::remember($cacheKey, 60, function () use ($currentYear, $previousYear): array {
            return [
                'financing' => $this->getFinancingMetrics($currentYear, $previousYear),
                'saving' => $this->getSavingMetrics($currentYear, $previousYear),
                'deposito' => $this->getDepositoMetrics($currentYear, $previousYear),
            ];
        });
    }

    /**
     * ==== FINANCING METRICS ====
     */
    public function getFinancingMetrics(string $currentYear, string $previousYear): array
    {
        $query = "
            WITH RiwayatNasabah AS (
                -- DATA BULAN BERJALAN (LIVE)
                SELECT
                    CAST(SUBSTRING(TGL.tgl, 5, 4) AS VARCHAR(4)) + CAST(SUBSTRING(TGL.tgl, 3, 2) AS VARCHAR(2)) AS periode_yyyymm,
                    T1.kdaoh,
                    T1.nokontrak,
                    CAST(T1.osmdlc AS DECIMAL(18,2)) AS osmdlc,
                    T1.colbaru,
                    T1.kdloc,
                    COALESCE(CG.kdloc, '00') AS kdloc_filter,
                    COALESCE(CG.nama, 'Konsolidasi') AS nama_cabang
                FROM TOFLMB T1
                CROSS JOIN TANGGAL TGL
                LEFT JOIN CABANG CG ON T1.kdloc = CG.kdloc
                WHERE CAST(SUBSTRING(TGL.tgl, 5, 4) AS VARCHAR(4)) = ?
                AND T1.stsrec = 'A'
                AND T1.stsacc <> 'W'

                UNION ALL

                -- DATA HISTORIS (EOM)
                SELECT
                    T2.periode AS periode_yyyymm,
                    T3.kdaoh,
                    T2.nokontrak,
                    CAST(T2.osmdlc AS DECIMAL(18,2)) AS osmdlc,
                    T3.colbaru,
                    T3.kdloc,
                    COALESCE(CG.kdloc, '00') AS kdloc_filter,
                    COALESCE(CG.nama, 'Konsolidasi') AS nama_cabang
                FROM TOFLMBEOM T2
                LEFT JOIN TOFLMB T3 ON T2.nokontrak = T3.nokontrak
                LEFT JOIN CABANG CG ON T3.kdloc = CG.kdloc
                WHERE (LEFT(T2.periode, 4) = ? OR LEFT(T2.periode, 4) = ?)
            )
            SELECT
                periode_yyyymm AS periode,
                COALESCE(nama_cabang, 'Konsolidasi') AS nama,
                SUM(osmdlc) AS TotalOS,
                SUM(CASE WHEN colbaru IN ('3','4','5') THEN osmdlc ELSE 0 END) AS TotalNPF,
                COUNT(DISTINCT nokontrak) AS TotalNOA,
                COUNT(DISTINCT kdaoh) AS TotalAO
            FROM RiwayatNasabah
            GROUP BY
                GROUPING SETS ((periode_yyyymm, nama_cabang), (periode_yyyymm))
            ORDER BY periode_yyyymm ASC, nama ASC;
        ";

        $data = $this->select($query, [$currentYear, $currentYear, $previousYear]);

        return $this->processKeyMetrics($data, 'TotalOS');
    }

    /**
     * ==== SAVING METRICS ====
     */
    public function getSavingMetrics(string $currentYear, string $previousYear): array
    {
        $query = "
            WITH RiwayatNasabah AS (
                SELECT
                    CAST(SUBSTRING(TGL.tgl, 5, 4) AS VARCHAR(4)) + CAST(SUBSTRING(TGL.tgl, 3, 2) AS VARCHAR(2)) AS periode_yyyymm,
                    T1.kodeaoh,
                    T1.notab,
                    CAST(T1.sahirrp AS DECIMAL(18,2)) AS sahirrp,
                    T1.kodeloc,
                    COALESCE(CG.kdloc, '00') AS kdloc_filter,
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
                    CAST(T2.sahirrp AS DECIMAL(18,2)) AS sahirrp,
                    T3.kodeloc,
                    COALESCE(CG.kdloc, '00') AS kdloc_filter,
                    COALESCE(CG.nama, 'Konsolidasi') AS nama_cabang
                FROM TOFTABEOM T2
                LEFT JOIN TOFTABB T3 ON T2.notab = T3.notab
                LEFT JOIN CABANG CG ON T3.kodeloc = CG.kdloc
                WHERE (LEFT(T2.periode, 4) = ? OR LEFT(T2.periode, 4) = ?)
            )
            SELECT
                periode_yyyymm AS periode,
                COALESCE(nama_cabang, 'Konsolidasi') AS nama,
                SUM(sahirrp) AS TotalSaldo,
                COUNT(DISTINCT notab) AS TotalNOA,
                COUNT(DISTINCT kodeaoh) AS TotalAO
            FROM RiwayatNasabah
            GROUP BY
                GROUPING SETS ((periode_yyyymm, nama_cabang), (periode_yyyymm))
            ORDER BY periode_yyyymm ASC, nama ASC;
        ";

        $data = $this->select($query, [$currentYear, $currentYear, $previousYear]);

        return $this->processKeyMetrics($data, 'TotalSaldo');
    }

    /**
     * ==== DEPOSITO METRICS ====
     */
    public function getDepositoMetrics(string $currentYear, string $previousYear): array
    {
        $query = "
            WITH RiwayatNasabah AS (
                SELECT
                    CAST(SUBSTRING(TGL.tgl, 5, 4) AS VARCHAR(4)) + CAST(SUBSTRING(TGL.tgl, 3, 2) AS VARCHAR(2)) AS periode_yyyymm,
                    T1.kodeaoh,
                    T1.nodep,
                    CAST(T1.nomrp AS DECIMAL(18,2)) AS TotalSaldo,
                    CAST(T1.bnghtg AS DECIMAL(18,2)) AS TotalBaghas,
                    T1.kdloc,
                    COALESCE(CG.kdloc, '00') AS kdloc_filter,
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
                    CAST(T2.nomrp AS DECIMAL(18,2)) AS TotalSaldo,
                    CAST(T2.bnghtg AS DECIMAL(18,2)) AS TotalBaghas,
                    T3.kdloc,
                    COALESCE(CG.kdloc, '00') AS kdloc_filter,
                    COALESCE(CG.nama, 'Konsolidasi') AS nama_cabang
                FROM TOFDEPEOM T2
                LEFT JOIN TOFDEP T3 ON T2.nodep = T3.nodep
                LEFT JOIN CABANG CG ON T3.kdloc = CG.kdloc
                WHERE (LEFT(T2.periode, 4) = ? OR LEFT(T2.periode, 4) = ?)
            )
            SELECT
                periode_yyyymm AS periode,
                COALESCE(nama_cabang, 'Konsolidasi') AS nama,
                SUM(TotalSaldo) AS TotalSaldo,
                SUM(TotalBaghas) AS TotalBaghas,
                COUNT(DISTINCT nodep) AS TotalNOA,
                COUNT(DISTINCT kodeaoh) AS TotalAO
            FROM RiwayatNasabah
            GROUP BY
                GROUPING SETS ((periode_yyyymm, nama_cabang), (periode_yyyymm))
            ORDER BY periode_yyyymm ASC, nama ASC;
        ";

        $data = $this->select($query, [$currentYear, $currentYear, $previousYear]);

        return $this->processKeyMetrics($data, 'TotalSaldo', 'TotalBaghas');
    }

    /**
     * Process key metrics untuk hitung growth
     */
    protected function processKeyMetrics(array $data, string $primaryField, string $secondaryField = null): array
    {
        if (empty($data)) {
            return $this->getDefaultMetrics();
        }

        // Filter data Konsolidasi (semua cabang)
        $consolidated = array_filter($data, fn($item) => $item->nama === 'Konsolidasi');
        $consolidated = array_values($consolidated);

        $count = count($consolidated);
        $current = $count > 0 ? $consolidated[$count - 1] : null;
        $previous = $count >= 2 ? $consolidated[$count - 2] : null;

        $result = [];

        if ($current) {
            $result['total'] = (float) $current->$primaryField;
            $result['noa'] = (int) ($current->TotalNOA ?? 0);
            $result['ao'] = (int) ($current->TotalAO ?? 0);

            if ($secondaryField) {
                $result['secondary'] = (float) $current->$secondaryField;
            }

            // Hitung growth
            if ($previous) {
                $prevTotal = (float) $previous->$primaryField;
                $prevNoa = (int) ($previous->TotalNOA ?? 0);
                $prevAo = (int) ($previous->TotalAO ?? 0);

                $result['growth'] = $this->calculateGrowth($result['total'], $prevTotal);
                $result['noa_growth'] = $this->calculateGrowth($result['noa'], $prevNoa);
                $result['ao_growth'] = $this->calculateGrowth($result['ao'], $prevAo);

                if ($secondaryField) {
                    $prevSecondary = (float) $previous->$secondaryField;
                    $result['secondary_growth'] = $this->calculateGrowth($result['secondary'], $prevSecondary);
                }
            }
        }

        return $result ?: $this->getDefaultMetrics();
    }

    /**
     * Default metrics saat data kosong
     */
    protected function getDefaultMetrics(): array
    {
        return [
            'total' => 0,
            'noa' => 0,
            'ao' => 0,
            'secondary' => 0,
            'growth' => ['value' => '0%', 'class' => 'text-muted', 'raw' => 0],
            'noa_growth' => ['value' => '0%', 'class' => 'text-muted', 'raw' => 0],
            'ao_growth' => ['value' => '0%', 'class' => 'text-muted', 'raw' => 0],
            'secondary_growth' => ['value' => '0%', 'class' => 'text-muted', 'raw' => 0],
        ];
    }

    /**
     * Ambil data chart historical
     */
    public function getChartData(string $type = 'financing'): array
    {
        $period = $this->getCurrentPeriod();
        $currentYear = (string) $period['year'];
        $previousYear = (string) $period['previous_year'];
        $cacheKey = $this->cacheKey("chart:{$type}:{$currentYear}");

        return Cache::remember($cacheKey, 300, function () use ($type, $currentYear, $previousYear): array {
            $data = match ($type) {
                'financing' => $this->getFinancingMetrics($currentYear, $previousYear),
                'saving' => $this->getSavingMetrics($currentYear, $previousYear),
                'deposito' => $this->getDepositoMetrics($currentYear, $previousYear),
                default => [],
            };

            return $this->formatChartData($data);
        });
    }

    /**
     * Format data untuk chart
     */
    protected function formatChartData(array $data): array
    {
        return [
            'labels' => [],
            'values' => [],
            'noa' => [],
            'growth' => [],
        ];
    }

    /**
     * Ambil daftar cabang
     */
    public function getBranchList(): array
    {
        $cacheKey = $this->cacheKey('branches');

        return Cache::remember($cacheKey, 3600, function (): array {
            $branches = DB::connection($this->connection)
                ->table('CABANG')
                ->select('kdloc', 'nama')
                ->orderBy('kdloc')
                ->get();

            return $branches->map(fn($b) => [
                'kdloc' => $b->kdloc,
                'nama' => $b->nama,
            ])->toArray();
        });
    }

    /**
     * Clear semua cache dashboard
     */
    public function clearCache(): void
    {
        $keys = [
            $this->cacheKey('system_date'),
            $this->cacheKey('key_metrics:*'),
            $this->cacheKey('chart:*'),
            $this->cacheKey('branches'),
        ];

        foreach ($keys as $key) {
            if (str_contains($key, '*')) {
                // Clear pattern matches
                Cache::flush();
            } else {
                Cache::forget($key);
            }
        }
    }
}