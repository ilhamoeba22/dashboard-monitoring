<?php

declare(strict_types=1);

namespace App\Repositories\Mci;

use App\Repositories\Interfaces\FundingRiskRepositoryInterface;
use App\Services\Mci\MciBaseRepository;
use Illuminate\Support\Facades\DB;

class FundingRiskRepository extends MciBaseRepository implements FundingRiskRepositoryInterface
{
    protected function getTableName(): string
    {
        return 'funding_risk';
    }

    public function getOverview(): array
    {
        $topDepositors = $this->getTopDepositors();
        $summary = $this->buildConcentrationSummary($topDepositors);

        return [
            'summary' => $summary,
            'top_depositors' => $topDepositors,
            'maturity_buckets' => $this->getMaturityBuckets(),
            'dormant_buckets' => $this->getDormantBuckets(),
            'product_mix' => $this->getProductMix(),
            'system_period' => $this->getCurrentPeriodInternal(),
        ];
    }

    public function getConcentrationDetails(): array
    {
        $topDepositors = $this->getTopDepositors(100);

        return [
            'summary' => $this->buildConcentrationSummary($topDepositors),
            'top_depositors' => $topDepositors,
            'concentration_bands' => $this->getConcentrationBands(),
            'system_period' => $this->getCurrentPeriodInternal(),
        ];
    }

    /**
     * @return array<int, object>
     */
    private function getTopDepositors(int $limit = 25): array
    {
        $safeLimit = max(1, min($limit, 500));
        $sql = "
            WITH DpkBase AS (
                SELECT
                    a.nocif,
                    COALESCE(NULLIF(g.nm, ''), NULLIF(a.fnama, ''), a.nocif) AS nama,
                    SUM(CAST(ISNULL(a.sahirrp, 0) AS DECIMAL(38, 2))) AS tabungan,
                    CAST(0 AS DECIMAL(38, 2)) AS deposito,
                    COUNT(a.notab) AS noa_tabungan,
                    0 AS noa_deposito
                FROM TOFTABB a
                LEFT JOIN mCIF g ON a.nocif = g.nocif
                WHERE a.stsrec <> 'C'
                  AND (a.tgltutup = '' OR a.tgltutup IS NULL)
                GROUP BY a.nocif, COALESCE(NULLIF(g.nm, ''), NULLIF(a.fnama, ''), a.nocif)

                UNION ALL

                SELECT
                    a.nocif,
                    COALESCE(NULLIF(g.nm, ''), NULLIF(a.nama, ''), a.nocif) AS nama,
                    CAST(0 AS DECIMAL(38, 2)) AS tabungan,
                    SUM(CAST(ISNULL(a.nomrp, 0) AS DECIMAL(38, 2))) AS deposito,
                    0 AS noa_tabungan,
                    COUNT(a.nodep) AS noa_deposito
                FROM TOFDEP a
                LEFT JOIN mCIF g ON a.nocif = g.nocif
                WHERE a.stsrec <> 'C'
                  AND (a.tgltutup = '' OR a.tgltutup IS NULL)
                GROUP BY a.nocif, COALESCE(NULLIF(g.nm, ''), NULLIF(a.nama, ''), a.nocif)
            ),
            DpkCif AS (
                SELECT
                    nocif,
                    MAX(nama) AS nama,
                    SUM(tabungan) AS tabungan,
                    SUM(deposito) AS deposito,
                    SUM(noa_tabungan) AS noa_tabungan,
                    SUM(noa_deposito) AS noa_deposito,
                    SUM(tabungan + deposito) AS total_dpk
                FROM DpkBase
                WHERE nocif IS NOT NULL AND nocif <> ''
                GROUP BY nocif
            ),
            TotalDpk AS (
                SELECT SUM(total_dpk) AS total_bank FROM DpkCif
            )
            SELECT TOP {$safeLimit}
                ROW_NUMBER() OVER (ORDER BY d.total_dpk DESC, d.nocif ASC) AS ranking,
                d.nocif,
                d.nama,
                d.tabungan,
                d.deposito,
                d.noa_tabungan,
                d.noa_deposito,
                d.total_dpk,
                CASE WHEN t.total_bank > 0 THEN (d.total_dpk * 100.0 / t.total_bank) ELSE 0 END AS share_percent
            FROM DpkCif d
            CROSS JOIN TotalDpk t
            WHERE d.total_dpk > 0
            ORDER BY d.total_dpk DESC, d.nocif ASC
        ";

        return DB::connection($this->connection)->select($sql);
    }

    /**
     * @return array<int, object>
     */
    private function getConcentrationBands(): array
    {
        $sql = "
            WITH DpkBase AS (
                SELECT
                    a.nocif,
                    SUM(CAST(ISNULL(a.sahirrp, 0) AS DECIMAL(38, 2))) AS tabungan,
                    CAST(0 AS DECIMAL(38, 2)) AS deposito,
                    COUNT(a.notab) AS noa_tabungan,
                    0 AS noa_deposito
                FROM TOFTABB a
                WHERE a.stsrec <> 'C'
                  AND (a.tgltutup = '' OR a.tgltutup IS NULL)
                GROUP BY a.nocif

                UNION ALL

                SELECT
                    a.nocif,
                    CAST(0 AS DECIMAL(38, 2)) AS tabungan,
                    SUM(CAST(ISNULL(a.nomrp, 0) AS DECIMAL(38, 2))) AS deposito,
                    0 AS noa_tabungan,
                    COUNT(a.nodep) AS noa_deposito
                FROM TOFDEP a
                WHERE a.stsrec <> 'C'
                  AND (a.tgltutup = '' OR a.tgltutup IS NULL)
                GROUP BY a.nocif
            ),
            DpkCif AS (
                SELECT
                    nocif,
                    SUM(tabungan) AS tabungan,
                    SUM(deposito) AS deposito,
                    SUM(noa_tabungan) AS noa_tabungan,
                    SUM(noa_deposito) AS noa_deposito,
                    SUM(tabungan + deposito) AS total_dpk
                FROM DpkBase
                WHERE nocif IS NOT NULL AND nocif <> ''
                GROUP BY nocif
            ),
            TotalDpk AS (
                SELECT SUM(total_dpk) AS total_bank FROM DpkCif
            ),
            Bucketed AS (
                SELECT
                    CASE
                        WHEN d.total_dpk >= 10000000000 THEN 1
                        WHEN d.total_dpk >= 5000000000 THEN 2
                        WHEN d.total_dpk >= 1000000000 THEN 3
                        WHEN d.total_dpk >= 500000000 THEN 4
                        ELSE 5
                    END AS band_order,
                    CASE
                        WHEN d.total_dpk >= 10000000000 THEN '>= Rp 10 M'
                        WHEN d.total_dpk >= 5000000000 THEN 'Rp 5 M - < Rp 10 M'
                        WHEN d.total_dpk >= 1000000000 THEN 'Rp 1 M - < Rp 5 M'
                        WHEN d.total_dpk >= 500000000 THEN 'Rp 500 Jt - < Rp 1 M'
                        ELSE '< Rp 500 Jt'
                    END AS band,
                    d.*,
                    t.total_bank
                FROM DpkCif d
                CROSS JOIN TotalDpk t
                WHERE d.total_dpk > 0
            )
            SELECT
                band_order,
                band,
                COUNT(nocif) AS depositor_count,
                SUM(noa_tabungan) AS noa_tabungan,
                SUM(noa_deposito) AS noa_deposito,
                SUM(tabungan) AS tabungan,
                SUM(deposito) AS deposito,
                SUM(total_dpk) AS total_dpk,
                CASE WHEN MAX(total_bank) > 0 THEN SUM(total_dpk) * 100.0 / MAX(total_bank) ELSE 0 END AS share_percent
            FROM Bucketed
            GROUP BY band_order, band
            ORDER BY band_order ASC
        ";

        return DB::connection($this->connection)->select($sql);
    }

    /**
     * @param  array<int, object>  $topDepositors
     * @return array<string, mixed>
     */
    private function buildConcentrationSummary(array $topDepositors): array
    {
        $totalDpk = $this->getTotalDpk();
        $top1 = array_slice($topDepositors, 0, 1);
        $top5 = array_slice($topDepositors, 0, 5);
        $top10 = array_slice($topDepositors, 0, 10);
        $top25 = array_slice($topDepositors, 0, 25);

        $sum = static fn (array $rows): float => array_reduce(
            $rows,
            static fn (float $carry, object $row): float => $carry + (float) ($row->total_dpk ?? 0),
            0.0
        );

        $ratio = static fn (float $value): float => $totalDpk > 0 ? ($value / $totalDpk) * 100 : 0.0;

        return [
            'total_dpk' => $totalDpk,
            'top1_nominal' => $sum($top1),
            'top5_nominal' => $sum($top5),
            'top10_nominal' => $sum($top10),
            'top25_nominal' => $sum($top25),
            'top1_ratio' => $ratio($sum($top1)),
            'top5_ratio' => $ratio($sum($top5)),
            'top10_ratio' => $ratio($sum($top10)),
            'top25_ratio' => $ratio($sum($top25)),
            'depositor_count_sample' => count($topDepositors),
        ];
    }

    private function getTotalDpk(): float
    {
        $row = DB::connection($this->connection)->selectOne("
            SELECT
                (
                    SELECT SUM(CAST(ISNULL(sahirrp, 0) AS DECIMAL(38, 2)))
                    FROM TOFTABB
                    WHERE stsrec <> 'C'
                      AND (tgltutup = '' OR tgltutup IS NULL)
                ) +
                (
                    SELECT SUM(CAST(ISNULL(nomrp, 0) AS DECIMAL(38, 2)))
                    FROM TOFDEP
                    WHERE stsrec <> 'C'
                      AND (tgltutup = '' OR tgltutup IS NULL)
                ) AS total_dpk
        ");

        return (float) ($row->total_dpk ?? 0);
    }

    /**
     * @return array<int, object>
     */
    private function getMaturityBuckets(): array
    {
        $sql = "
            WITH DepositoAktif AS (
                SELECT
                    a.nodep,
                    a.nomrp,
                    a.aro,
                    CONVERT(date, CASE
                        WHEN a.tgljtempo IS NOT NULL AND a.tgljtempo <> '' AND ISDATE(a.tgljtempo) = 1
                        THEN a.tgljtempo ELSE '19000101'
                    END, 112) AS jatuh_tempo
                FROM TOFDEP a
                WHERE a.stsrec <> 'C'
                  AND (a.tgltutup = '' OR a.tgltutup IS NULL)
            ),
            Bucketed AS (
                SELECT
                    nodep,
                    nomrp,
                    aro,
                    DATEDIFF(day, CAST(GETDATE() AS date), jatuh_tempo) AS days_to_maturity,
                    CASE
                        WHEN jatuh_tempo = '1900-01-01' THEN 8
                        WHEN DATEDIFF(day, CAST(GETDATE() AS date), jatuh_tempo) < 0 THEN 0
                        WHEN DATEDIFF(day, CAST(GETDATE() AS date), jatuh_tempo) BETWEEN 0 AND 7 THEN 1
                        WHEN DATEDIFF(day, CAST(GETDATE() AS date), jatuh_tempo) BETWEEN 8 AND 14 THEN 2
                        WHEN DATEDIFF(day, CAST(GETDATE() AS date), jatuh_tempo) BETWEEN 15 AND 30 THEN 3
                        WHEN DATEDIFF(day, CAST(GETDATE() AS date), jatuh_tempo) BETWEEN 31 AND 60 THEN 4
                        WHEN DATEDIFF(day, CAST(GETDATE() AS date), jatuh_tempo) BETWEEN 61 AND 90 THEN 5
                        ELSE 6
                    END AS bucket_order,
                    CASE
                        WHEN jatuh_tempo = '1900-01-01' THEN 'Tanggal tidak valid'
                        WHEN DATEDIFF(day, CAST(GETDATE() AS date), jatuh_tempo) < 0 THEN 'Lewat jatuh tempo'
                        WHEN DATEDIFF(day, CAST(GETDATE() AS date), jatuh_tempo) BETWEEN 0 AND 7 THEN '0-7 hari'
                        WHEN DATEDIFF(day, CAST(GETDATE() AS date), jatuh_tempo) BETWEEN 8 AND 14 THEN '8-14 hari'
                        WHEN DATEDIFF(day, CAST(GETDATE() AS date), jatuh_tempo) BETWEEN 15 AND 30 THEN '15-30 hari'
                        WHEN DATEDIFF(day, CAST(GETDATE() AS date), jatuh_tempo) BETWEEN 31 AND 60 THEN '31-60 hari'
                        WHEN DATEDIFF(day, CAST(GETDATE() AS date), jatuh_tempo) BETWEEN 61 AND 90 THEN '61-90 hari'
                        ELSE '>90 hari'
                    END AS bucket
                FROM DepositoAktif
            )
            SELECT
                bucket_order,
                bucket,
                COUNT(nodep) AS noa,
                SUM(CAST(ISNULL(nomrp, 0) AS DECIMAL(38, 2))) AS nominal,
                SUM(CASE WHEN ISNULL(aro, '') = 'Y' THEN 1 ELSE 0 END) AS aro_count,
                SUM(CASE WHEN ISNULL(aro, '') <> 'Y' THEN 1 ELSE 0 END) AS non_aro_count
            FROM Bucketed
            GROUP BY bucket_order, bucket
            ORDER BY bucket_order ASC
        ";

        return DB::connection($this->connection)->select($sql);
    }

    /**
     * @return array<int, object>
     */
    private function getDormantBuckets(): array
    {
        $sql = "
            WITH Rekening AS (
                SELECT
                    c.notab,
                    c.sahirrp,
                    b.saldoavg,
                    CASE
                        WHEN c.tgltrnakh IS NULL OR c.tgltrnakh = '' OR c.tgltrnakh = '0'
                        THEN c.tglbuka ELSE c.tgltrnakh
                    END AS tgltrans
                FROM TOFTABC c
                LEFT JOIN TOFTABB b ON c.notab = b.notab
                WHERE b.stsrec <> 'C'
                  AND (b.tgltutup = '' OR b.tgltutup IS NULL)
            ),
            Bucketed AS (
                SELECT
                    notab,
                    sahirrp,
                    saldoavg,
                    CASE
                        WHEN ISDATE(tgltrans) <> 1 THEN 4
                        WHEN DATEDIFF(month, CONVERT(date, tgltrans, 112), GETDATE()) >= 12 THEN 1
                        WHEN DATEDIFF(month, CONVERT(date, tgltrans, 112), GETDATE()) >= 6 THEN 2
                        ELSE 3
                    END AS bucket_order,
                    CASE
                        WHEN ISDATE(tgltrans) <> 1 THEN 'Tanggal tidak valid'
                        WHEN DATEDIFF(month, CONVERT(date, tgltrans, 112), GETDATE()) >= 12 THEN 'Dormant >=12 bulan'
                        WHEN DATEDIFF(month, CONVERT(date, tgltrans, 112), GETDATE()) >= 6 THEN 'Dormant 6-12 bulan'
                        ELSE 'Aktif <6 bulan'
                    END AS bucket
                FROM Rekening
            )
            SELECT
                bucket_order,
                bucket,
                COUNT(notab) AS noa,
                SUM(CAST(ISNULL(sahirrp, 0) AS DECIMAL(38, 2))) AS saldo,
                SUM(CAST(ISNULL(saldoavg, 0) AS DECIMAL(38, 2))) AS saldo_rata
            FROM Bucketed
            GROUP BY bucket_order, bucket
            ORDER BY bucket_order ASC
        ";

        return DB::connection($this->connection)->select($sql);
    }

    /**
     * @return array<int, object>
     */
    private function getProductMix(): array
    {
        $sql = "
            WITH ProductBase AS (
                SELECT
                    'Tabungan' AS domain,
                    a.kodeprd AS kode_produk,
                    ISNULL(b.nmpjgprd, 'Produk Tabungan Tidak Terdaftar') AS produk,
                    COUNT(a.notab) AS noa,
                    SUM(CAST(ISNULL(a.sahirrp, 0) AS DECIMAL(38, 2))) AS nominal
                FROM TOFTABB a
                LEFT JOIN SETUPTAB b ON a.kodeprd = b.kodeprd
                WHERE a.stsrec <> 'C'
                  AND (a.tgltutup = '' OR a.tgltutup IS NULL)
                GROUP BY a.kodeprd, b.nmpjgprd

                UNION ALL

                SELECT
                    'Deposito' AS domain,
                    a.kdprd AS kode_produk,
                    ISNULL(b.ket, 'Produk Deposito Tidak Terdaftar') AS produk,
                    COUNT(a.nodep) AS noa,
                    SUM(CAST(ISNULL(a.nomrp, 0) AS DECIMAL(38, 2))) AS nominal
                FROM TOFDEP a
                LEFT JOIN SETUPDEP b ON a.kdprd = b.kdprd
                WHERE a.stsrec <> 'C'
                  AND (a.tgltutup = '' OR a.tgltutup IS NULL)
                GROUP BY a.kdprd, b.ket
            ),
            TotalProduct AS (
                SELECT SUM(nominal) AS total_dpk FROM ProductBase
            )
            SELECT TOP 20
                p.domain,
                p.kode_produk,
                p.produk,
                p.noa,
                p.nominal,
                CASE WHEN t.total_dpk > 0 THEN (p.nominal * 100.0 / t.total_dpk) ELSE 0 END AS share_percent
            FROM ProductBase p
            CROSS JOIN TotalProduct t
            WHERE p.nominal > 0
            ORDER BY p.nominal DESC, p.domain ASC, p.kode_produk ASC
        ";

        return DB::connection($this->connection)->select($sql);
    }
}
