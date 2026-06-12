<?php

declare(strict_types=1);

namespace App\Repositories\Mci;

use App\Repositories\Interfaces\FundingBaghasRepositoryInterface;
use App\Services\Mci\MciBaseRepository;
use Illuminate\Support\Facades\DB;

class FundingBaghasRepository extends MciBaseRepository implements FundingBaghasRepositoryInterface
{
    protected function getTableName(): string
    {
        return 'funding_baghas';
    }

    public function getOverview(): array
    {
        $saving = $this->getSavingSummary();
        $deposit = $this->getDepositSummary();

        return [
            'summary' => $this->buildSummary($saving, $deposit),
            'saving_summary' => $saving,
            'deposit_summary' => $deposit,
            'saving_by_product' => $this->getSavingByProduct(),
            'deposit_by_product' => $this->getDepositByProduct(),
            'deposit_by_nisbah' => $this->getDepositByNisbah(),
            'top_baghas_depositors' => $this->getTopBaghasDepositors(),
            'system_period' => $this->getCurrentPeriodInternal(),
            'trace_note' => 'Perhitungan memakai field core CBS yang sudah jelas: TOFTABB.bhhtg/bhbayar/taxbayar dan TOFDEP.bnghtg/bngbayar/tax. Laporan accrual/cadangan/realisasi resmi CBS tetap perlu SQL tracing LAPORAN19 sebelum dijadikan angka final.',
        ];
    }

    private function getSavingSummary(): object
    {
        return DB::connection($this->connection)
            ->table('TOFTABB as a')
            ->where('a.stsrec', '!=', 'C')
            ->where(function ($query) {
                $query->where('a.tgltutup', '=', '')
                    ->orWhereNull('a.tgltutup');
            })
            ->selectRaw('COUNT(a.notab) AS noa')
            ->selectRaw('SUM(CAST(ISNULL(a.sahirrp, 0) AS DECIMAL(38, 2))) AS saldo')
            ->selectRaw('SUM(CAST(ISNULL(a.saldoavg, 0) AS DECIMAL(38, 2))) AS saldo_rata')
            ->selectRaw('SUM(CAST(ISNULL(a.bhhtg, 0) AS DECIMAL(38, 2))) AS baghas_hitung')
            ->selectRaw('SUM(CAST(ISNULL(a.bhbayar, 0) AS DECIMAL(38, 2))) AS baghas_bayar')
            ->selectRaw('SUM(CAST(ISNULL(a.taxbayar, 0) AS DECIMAL(38, 2))) AS pajak')
            ->selectRaw('AVG(CAST(ISNULL(a.nisbah, 0) AS DECIMAL(18, 6))) AS avg_nisbah')
            ->selectRaw('AVG(CAST(ISNULL(a.rate, 0) AS DECIMAL(18, 6))) AS avg_rate')
            ->first() ?? (object) [];
    }

    private function getDepositSummary(): object
    {
        return DB::connection($this->connection)
            ->table('TOFDEP as a')
            ->where('a.stsrec', '!=', 'C')
            ->where(function ($query) {
                $query->where('a.tgltutup', '=', '')
                    ->orWhereNull('a.tgltutup');
            })
            ->selectRaw('COUNT(a.nodep) AS noa')
            ->selectRaw('SUM(CAST(ISNULL(a.nomrp, 0) AS DECIMAL(38, 2))) AS saldo')
            ->selectRaw('SUM(CAST(ISNULL(a.saldrata1, 0) AS DECIMAL(38, 2))) AS saldo_rata')
            ->selectRaw('SUM(CAST(ISNULL(a.bnghtg, 0) AS DECIMAL(38, 2))) AS baghas_hitung')
            ->selectRaw('SUM(CAST(ISNULL(a.bngbayar, 0) AS DECIMAL(38, 2))) AS baghas_bayar')
            ->selectRaw('SUM(CAST(ISNULL(a.tax, 0) AS DECIMAL(38, 2))) AS pajak')
            ->selectRaw('AVG(CAST(ISNULL(a.nisbah, 0) AS DECIMAL(18, 6))) AS avg_nisbah')
            ->selectRaw('AVG(CAST(ISNULL(a.equivrate, 0) AS DECIMAL(18, 6))) AS avg_rate')
            ->first() ?? (object) [];
    }

    /**
     * @return array<string, mixed>
     */
    private function buildSummary(object $saving, object $deposit): array
    {
        $baghasHitung = (float) ($saving->baghas_hitung ?? 0) + (float) ($deposit->baghas_hitung ?? 0);
        $baghasBayar = (float) ($saving->baghas_bayar ?? 0) + (float) ($deposit->baghas_bayar ?? 0);
        $pajak = (float) ($saving->pajak ?? 0) + (float) ($deposit->pajak ?? 0);
        $saldo = (float) ($saving->saldo ?? 0) + (float) ($deposit->saldo ?? 0);
        $saldoRata = (float) ($saving->saldo_rata ?? 0) + (float) ($deposit->saldo_rata ?? 0);

        return [
            'total_noa' => (int) ($saving->noa ?? 0) + (int) ($deposit->noa ?? 0),
            'total_saldo' => $saldo,
            'total_saldo_rata' => $saldoRata,
            'total_baghas_hitung' => $baghasHitung,
            'total_baghas_bayar' => $baghasBayar,
            'total_pajak' => $pajak,
            'net_baghas_bayar' => $baghasBayar - $pajak,
            'baghas_to_saldo_ratio' => $saldo > 0 ? ($baghasBayar / $saldo) * 100 : 0,
            'baghas_to_average_ratio' => $saldoRata > 0 ? ($baghasBayar / $saldoRata) * 100 : 0,
            'tax_to_baghas_ratio' => $baghasBayar > 0 ? ($pajak / $baghasBayar) * 100 : 0,
            'saving_share_baghas' => $baghasBayar > 0 ? ((float) ($saving->baghas_bayar ?? 0) / $baghasBayar) * 100 : 0,
            'deposit_share_baghas' => $baghasBayar > 0 ? ((float) ($deposit->baghas_bayar ?? 0) / $baghasBayar) * 100 : 0,
        ];
    }

    /**
     * @return array<int, object>
     */
    private function getSavingByProduct(): array
    {
        return DB::connection($this->connection)
            ->table('TOFTABB as a')
            ->leftJoin('SETUPTAB as b', 'a.kodeprd', '=', 'b.kodeprd')
            ->where('a.stsrec', '!=', 'C')
            ->where(function ($query) {
                $query->where('a.tgltutup', '=', '')
                    ->orWhereNull('a.tgltutup');
            })
            ->selectRaw("'Tabungan' AS domain")
            ->selectRaw('a.kodeprd AS kode_produk')
            ->selectRaw("ISNULL(b.nmpjgprd, 'Produk Tabungan Tidak Terdaftar') AS produk")
            ->selectRaw('COUNT(a.notab) AS noa')
            ->selectRaw('SUM(CAST(ISNULL(a.sahirrp, 0) AS DECIMAL(38, 2))) AS saldo')
            ->selectRaw('SUM(CAST(ISNULL(a.bhhtg, 0) AS DECIMAL(38, 2))) AS baghas_hitung')
            ->selectRaw('SUM(CAST(ISNULL(a.bhbayar, 0) AS DECIMAL(38, 2))) AS baghas_bayar')
            ->selectRaw('SUM(CAST(ISNULL(a.taxbayar, 0) AS DECIMAL(38, 2))) AS pajak')
            ->selectRaw('AVG(CAST(ISNULL(a.nisbah, 0) AS DECIMAL(18, 6))) AS avg_nisbah')
            ->selectRaw('AVG(CAST(ISNULL(a.rate, 0) AS DECIMAL(18, 6))) AS avg_rate')
            ->groupBy('a.kodeprd', 'b.nmpjgprd')
            ->orderByDesc('baghas_bayar')
            ->get()
            ->all();
    }

    /**
     * @return array<int, object>
     */
    private function getDepositByProduct(): array
    {
        return DB::connection($this->connection)
            ->table('TOFDEP as a')
            ->leftJoin('SETUPDEP as b', 'a.kdprd', '=', 'b.kdprd')
            ->where('a.stsrec', '!=', 'C')
            ->where(function ($query) {
                $query->where('a.tgltutup', '=', '')
                    ->orWhereNull('a.tgltutup');
            })
            ->selectRaw("'Deposito' AS domain")
            ->selectRaw('a.kdprd AS kode_produk')
            ->selectRaw("ISNULL(b.ket, 'Produk Deposito Tidak Terdaftar') AS produk")
            ->selectRaw('COUNT(a.nodep) AS noa')
            ->selectRaw('SUM(CAST(ISNULL(a.nomrp, 0) AS DECIMAL(38, 2))) AS saldo')
            ->selectRaw('SUM(CAST(ISNULL(a.bnghtg, 0) AS DECIMAL(38, 2))) AS baghas_hitung')
            ->selectRaw('SUM(CAST(ISNULL(a.bngbayar, 0) AS DECIMAL(38, 2))) AS baghas_bayar')
            ->selectRaw('SUM(CAST(ISNULL(a.tax, 0) AS DECIMAL(38, 2))) AS pajak')
            ->selectRaw('AVG(CAST(ISNULL(a.nisbah, 0) AS DECIMAL(18, 6))) AS avg_nisbah')
            ->selectRaw('AVG(CAST(ISNULL(a.equivrate, 0) AS DECIMAL(18, 6))) AS avg_rate')
            ->groupBy('a.kdprd', 'b.ket')
            ->orderByDesc('baghas_bayar')
            ->get()
            ->all();
    }

    /**
     * @return array<int, object>
     */
    private function getDepositByNisbah(): array
    {
        $sql = "
            WITH Bucketed AS (
                SELECT
                    CASE
                        WHEN ISNULL(nisbah, 0) <= 0 THEN 0
                        WHEN nisbah <= 25 THEN 1
                        WHEN nisbah <= 50 THEN 2
                        WHEN nisbah <= 75 THEN 3
                        ELSE 4
                    END AS bucket_order,
                    CASE
                        WHEN ISNULL(nisbah, 0) <= 0 THEN 'Nisbah 0 / kosong'
                        WHEN nisbah <= 25 THEN 'Nisbah <=25%'
                        WHEN nisbah <= 50 THEN 'Nisbah 26-50%'
                        WHEN nisbah <= 75 THEN 'Nisbah 51-75%'
                        ELSE 'Nisbah >75%'
                    END AS bucket,
                    nodep,
                    nomrp,
                    bnghtg,
                    bngbayar,
                    tax,
                    nisbah,
                    equivrate
                FROM TOFDEP
                WHERE stsrec <> 'C'
                  AND (tgltutup = '' OR tgltutup IS NULL)
            )
            SELECT
                bucket_order,
                bucket,
                COUNT(nodep) AS noa,
                SUM(CAST(ISNULL(nomrp, 0) AS DECIMAL(38, 2))) AS saldo,
                SUM(CAST(ISNULL(bnghtg, 0) AS DECIMAL(38, 2))) AS baghas_hitung,
                SUM(CAST(ISNULL(bngbayar, 0) AS DECIMAL(38, 2))) AS baghas_bayar,
                SUM(CAST(ISNULL(tax, 0) AS DECIMAL(38, 2))) AS pajak,
                AVG(CAST(ISNULL(nisbah, 0) AS DECIMAL(18, 6))) AS avg_nisbah,
                AVG(CAST(ISNULL(equivrate, 0) AS DECIMAL(18, 6))) AS avg_rate
            FROM Bucketed
            GROUP BY bucket_order, bucket
            ORDER BY bucket_order ASC
        ";

        return DB::connection($this->connection)->select($sql);
    }

    /**
     * @return array<int, object>
     */
    private function getTopBaghasDepositors(): array
    {
        $sql = "
            SELECT TOP 25
                ROW_NUMBER() OVER (ORDER BY SUM(CAST(ISNULL(a.bngbayar, 0) AS DECIMAL(38, 2))) DESC, a.nocif ASC) AS ranking,
                a.nocif,
                COALESCE(NULLIF(g.nm, ''), NULLIF(a.nama, ''), a.nocif) AS nama,
                COUNT(a.nodep) AS noa_deposito,
                SUM(CAST(ISNULL(a.nomrp, 0) AS DECIMAL(38, 2))) AS saldo_deposito,
                SUM(CAST(ISNULL(a.bnghtg, 0) AS DECIMAL(38, 2))) AS baghas_hitung,
                SUM(CAST(ISNULL(a.bngbayar, 0) AS DECIMAL(38, 2))) AS baghas_bayar,
                SUM(CAST(ISNULL(a.tax, 0) AS DECIMAL(38, 2))) AS pajak,
                AVG(CAST(ISNULL(a.nisbah, 0) AS DECIMAL(18, 6))) AS avg_nisbah,
                AVG(CAST(ISNULL(a.equivrate, 0) AS DECIMAL(18, 6))) AS avg_rate
            FROM TOFDEP a
            LEFT JOIN mCIF g ON a.nocif = g.nocif
            WHERE a.stsrec <> 'C'
              AND (a.tgltutup = '' OR a.tgltutup IS NULL)
            GROUP BY a.nocif, COALESCE(NULLIF(g.nm, ''), NULLIF(a.nama, ''), a.nocif)
            HAVING SUM(CAST(ISNULL(a.bngbayar, 0) AS DECIMAL(38, 2))) > 0
            ORDER BY baghas_bayar DESC, a.nocif ASC
        ";

        return DB::connection($this->connection)->select($sql);
    }
}
