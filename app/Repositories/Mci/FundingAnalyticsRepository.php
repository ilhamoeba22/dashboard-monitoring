<?php

declare(strict_types=1);

namespace App\Repositories\Mci;

use App\Repositories\Interfaces\FundingAnalyticsRepositoryInterface;
use App\Services\Mci\MciBaseRepository;
use Illuminate\Support\Facades\DB;

class FundingAnalyticsRepository extends MciBaseRepository implements FundingAnalyticsRepositoryInterface
{
    protected function getTableName(): string
    {
        return 'funding_analytics';
    }

    public function getPerkembangan(): array
    {
        $period = $this->getCurrentPeriodInternal();
        $year = (string) $period['year'];
        $currentPeriod = $period['period'];

        return [
            'system_period' => $period,
            'saving' => $this->monthlyBalance('saving', $year, $currentPeriod),
            'deposit' => $this->monthlyBalance('deposit', $year, $currentPeriod),
        ];
    }

    public function getTarget(): array
    {
        $period = $this->getCurrentPeriodInternal();
        $year = (string) $period['year'];
        $currentPeriod = $period['period'];

        return [
            'system_period' => $period,
            'saving' => $this->monthlyTarget('tab', 'saving', $year, $currentPeriod),
            'deposit' => $this->monthlyTarget('dep', 'deposit', $year, $currentPeriod),
        ];
    }

    public function getMutasi(): array
    {
        $period = $this->getCurrentPeriodInternal();
        $year = (string) $period['year'];

        return [
            'system_period' => $period,
            'saving' => $this->savingMutation($year),
            'deposit' => $this->depositMutation($year),
        ];
    }

    private function monthlyBalance(string $domain, string $year, string $currentPeriod): array
    {
        if ($domain === 'saving') {
            $sql = "
                WITH Raw AS (
                    SELECT a.periode, SUM(CAST(ISNULL(a.sahirrp, 0) AS DECIMAL(38, 2))) AS nominal, COUNT(DISTINCT a.notab) AS noa
                    FROM TOFTABEOM a
                    WHERE LEFT(a.periode, 4) = ?
                      AND a.periode < ?
                    GROUP BY a.periode
                    UNION ALL
                    SELECT ?, SUM(CAST(ISNULL(a.sahirrp, 0) AS DECIMAL(38, 2))) AS nominal, COUNT(DISTINCT a.notab) AS noa
                    FROM TOFTABB a
                    WHERE a.stsrec <> 'C'
                      AND (a.tgltutup = '' OR a.tgltutup IS NULL)
                )
                SELECT periode, SUM(nominal) AS nominal, SUM(noa) AS noa
                FROM Raw
                GROUP BY periode
                ORDER BY periode ASC
            ";
        } else {
            $sql = "
                WITH Raw AS (
                    SELECT a.periode, SUM(CAST(ISNULL(a.nomrp, 0) AS DECIMAL(38, 2))) AS nominal, COUNT(DISTINCT a.nodep) AS noa
                    FROM TOFDEPEOM a
                    WHERE LEFT(a.periode, 4) = ?
                      AND a.periode < ?
                    GROUP BY a.periode
                    UNION ALL
                    SELECT ?, SUM(CAST(ISNULL(a.nomrp, 0) AS DECIMAL(38, 2))) AS nominal, COUNT(DISTINCT a.nodep) AS noa
                    FROM TOFDEP a
                    WHERE a.stsrec <> 'C'
                      AND (a.tgltutup = '' OR a.tgltutup IS NULL)
                )
                SELECT periode, SUM(nominal) AS nominal, SUM(noa) AS noa
                FROM Raw
                GROUP BY periode
                ORDER BY periode ASC
            ";
        }

        return $this->withGrowth(DB::connection($this->connection)->select($sql, [$year, $currentPeriod, $currentPeriod]));
    }

    private function monthlyTarget(string $targetColumn, string $domain, string $year, string $currentPeriod): array
    {
        $realization = $this->monthlyBalance($domain, $year, $currentPeriod);
        $targets = DB::connection($this->connection)
            ->table('TARGETAO')
            ->selectRaw("CAST(thn AS VARCHAR) + RIGHT('0' + CAST(bln AS VARCHAR), 2) AS periode")
            ->selectRaw("SUM(CAST(ISNULL({$targetColumn}, 0) AS DECIMAL(38, 2))) AS target")
            ->where('thn', (int) $year)
            ->groupBy('thn', 'bln')
            ->get()
            ->keyBy('periode');

        return array_map(function (array $row) use ($targets): array {
            $target = (float) ($targets->get($row['periode'])->target ?? 0);
            $actual = (float) $row['nominal'];

            return $row + [
                'target' => $target,
                'actual' => $actual,
                'achievement_percent' => $target > 0 ? ($actual / $target) * 100 : 0,
                'gap' => $actual - $target,
            ];
        }, $realization);
    }

    private function savingMutation(string $year): array
    {
        $sql = "
            SELECT
                LEFT(b.tgltrn, 6) AS periode,
                SUM(CASE WHEN b.dc = 'D' THEN CAST(ISNULL(b.nominal, 0) AS DECIMAL(38, 2)) ELSE 0 END) AS debet,
                SUM(CASE WHEN b.dc = 'C' THEN CAST(ISNULL(b.nominal, 0) AS DECIMAL(38, 2)) ELSE 0 END) AS kredit,
                SUM(CASE WHEN b.dc = 'D' THEN CAST(ISNULL(b.nominal, 0) AS DECIMAL(38, 2)) ELSE -CAST(ISNULL(b.nominal, 0) AS DECIMAL(38, 2)) END) AS netto,
                COUNT(*) AS transaksi
            FROM H_GLTRN b
            INNER JOIN TOFTABB a ON a.notab = b.noacclawan
            WHERE b.trnuser <> 'OPREOD'
              AND LEFT(b.tgltrn, 4) = ?
            GROUP BY LEFT(b.tgltrn, 6)
            ORDER BY periode ASC
        ";

        return array_map(fn (object $row): array => [
            'periode' => (string) $row->periode,
            'debet' => (float) $row->debet,
            'kredit' => (float) $row->kredit,
            'netto' => (float) $row->netto,
            'transaksi' => (int) $row->transaksi,
        ], DB::connection($this->connection)->select($sql, [$year]));
    }

    private function depositMutation(string $year): array
    {
        $sql = "
            WITH Combined AS (
                SELECT LEFT(tglbuka, 6) AS periode, CAST(ISNULL(nomrp, 0) AS DECIMAL(38, 2)) AS nominal, 'M' AS status
                FROM TOFDEP
                WHERE LEFT(tglbuka, 4) = ?
                UNION ALL
                SELECT LEFT(tglcair, 6) AS periode, CAST(ISNULL(nomrp, 0) AS DECIMAL(38, 2)) AS nominal, 'C' AS status
                FROM TOFDEPDEL
                WHERE LEFT(tglcair, 4) = ?
            )
            SELECT
                periode,
                SUM(CASE WHEN status = 'M' THEN nominal ELSE 0 END) AS masuk,
                SUM(CASE WHEN status = 'C' THEN nominal ELSE 0 END) AS cair,
                SUM(CASE WHEN status = 'M' THEN nominal ELSE -nominal END) AS netto,
                COUNT(*) AS transaksi
            FROM Combined
            GROUP BY periode
            ORDER BY periode ASC
        ";

        return array_map(fn (object $row): array => [
            'periode' => (string) $row->periode,
            'masuk' => (float) $row->masuk,
            'cair' => (float) $row->cair,
            'netto' => (float) $row->netto,
            'transaksi' => (int) $row->transaksi,
        ], DB::connection($this->connection)->select($sql, [$year, $year]));
    }

    private function withGrowth(array $rows): array
    {
        $previous = null;

        return array_map(function (object $row) use (&$previous): array {
            $nominal = (float) ($row->nominal ?? 0);
            $growth = $previous !== null && $previous > 0 ? (($nominal - $previous) / $previous) * 100 : 0;
            $previous = $nominal;

            return [
                'periode' => (string) $row->periode,
                'nominal' => $nominal,
                'noa' => (int) ($row->noa ?? 0),
                'mom_growth_percent' => $growth,
            ];
        }, $rows);
    }
}
