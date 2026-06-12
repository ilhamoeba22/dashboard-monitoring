<?php

declare(strict_types=1);

namespace App\Repositories\Mci;

use App\Repositories\Interfaces\DepositRepositoryInterface;
use App\Services\Mci\MciBaseRepository;
use Illuminate\Contracts\Pagination\CursorPaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class DepositRepository extends MciBaseRepository implements DepositRepositoryInterface
{
    protected function getTableName(): string
    {
        return 'TOFDEP';
    }

    public function getSummary(): array
    {
        $row = DB::connection($this->connection)
            ->table('TOFDEP as a')
            ->where('a.stsrec', '!=', 'C')
            ->where(function ($query) {
                $query->where('a.tgltutup', '=', '')
                    ->orWhereNull('a.tgltutup');
            })
            ->selectRaw('COUNT(a.nodep) AS noa')
            ->selectRaw('SUM(a.nomawal) AS nominal_awal')
            ->selectRaw('SUM(a.nomrp) AS total_nominal')
            ->selectRaw('SUM(a.saldrata1) AS saldo_rata')
            ->selectRaw('SUM(a.bnghtg) AS bagi_hasil_htg')
            ->selectRaw('SUM(a.bngbayar) AS bagi_hasil_bayar')
            ->selectRaw('SUM(a.tax) AS pajak')
            ->first();

        return [
            'noa' => (int) ($row->noa ?? 0),
            'nominal_awal' => (float) ($row->nominal_awal ?? 0),
            'total_nominal' => (float) ($row->total_nominal ?? 0),
            'saldo_rata' => (float) ($row->saldo_rata ?? 0),
            'bagi_hasil_htg' => (float) ($row->bagi_hasil_htg ?? 0),
            'bagi_hasil_bayar' => (float) ($row->bagi_hasil_bayar ?? 0),
            'pajak' => (float) ($row->pajak ?? 0),
        ];
    }

    public function getFilterOptions(): array
    {
        $activeDeposit = function ($query): void {
            $query->where('a.stsrec', '!=', 'C')
                ->where(function ($subQuery) {
                    $subQuery->where('a.tgltutup', '=', '')
                        ->orWhereNull('a.tgltutup');
                });
        };

        $cabangs = DB::connection($this->connection)
            ->table('TOFDEP as a')
            ->leftJoin('CABANG as b', 'a.kdloc', '=', 'b.kdloc')
            ->where($activeDeposit)
            ->whereNotNull('a.kdloc')
            ->where('a.kdloc', '!=', '')
            ->select([
                'a.kdloc as value',
                DB::raw("ISNULL(b.nama, a.kdloc) as title"),
            ])
            ->selectRaw('COUNT(a.nodep) AS bilyet')
            ->selectRaw('SUM(CAST(ISNULL(a.nomrp, 0) AS DECIMAL(38, 2))) AS nominal')
            ->groupBy('a.kdloc', 'b.nama')
            ->orderBy('a.kdloc')
            ->get();

        $accountOfficers = DB::connection($this->connection)
            ->table('TOFDEP as a')
            ->leftJoin('AO as b', 'a.kodeaoh', '=', 'b.kdao')
            ->where($activeDeposit)
            ->whereNotNull('a.kodeaoh')
            ->where('a.kodeaoh', '!=', '')
            ->select([
                'a.kodeaoh as value',
                DB::raw("ISNULL(b.nmao, a.kodeaoh) as title"),
            ])
            ->selectRaw('COUNT(a.nodep) AS bilyet')
            ->selectRaw('SUM(CAST(ISNULL(a.nomrp, 0) AS DECIMAL(38, 2))) AS nominal')
            ->groupBy('a.kodeaoh', 'b.nmao')
            ->orderBy('b.nmao')
            ->get();

        return [
            'cabangs' => $cabangs,
            'account_officers' => $accountOfficers,
        ];
    }

    public function getNominative(array $filters = [], int $perPage = 50): CursorPaginator
    {
        $query = DB::connection($this->connection)
            ->table('TOFDEP as a')
            ->leftJoin('mCIF as g', 'a.nocif', '=', 'g.nocif')
            ->leftJoin('AO as c', 'a.kodeaoh', '=', 'c.kdao')
            ->leftJoin('SETUPDEP as f', 'a.kdprd', '=', 'f.kdprd')
            ->leftJoin('CABANG as h', 'a.kdloc', '=', 'h.kdloc')
            ->leftJoin('WILAYAH as i', 'a.kdwil', '=', 'i.kodewil')
            ->select([
                'a.nodep as norekening',
                'a.nobilyet',
                'a.nocif',
                DB::raw('COALESCE(g.nm, a.nama) as nama'),
                'a.kdprd',
                'f.ket as jenis',
                'a.kodeaoh',
                'c.nmao as ao',
                'a.kdloc',
                'h.nama as cabang',
                'a.kdwil',
                'i.ket as wilayah',
                'a.nomawal',
                'a.nomrp as nominal',
                'a.saldrata1 as saldo_rata',
                'a.bnghtg',
                'a.bngbayar',
                'a.tax',
                'a.nisbah',
                'a.equivrate',
                'a.tglbuka',
                'a.tgljtempo as jatuh_tempo',
                'a.aro',
                'a.stsrec',
                'a.stsacc',
            ])
            ->selectRaw("DATEDIFF(day, CAST(GETDATE() AS date), CONVERT(date, CASE
                WHEN a.tgljtempo IS NOT NULL AND a.tgljtempo <> '' AND ISDATE(a.tgljtempo) = 1
                THEN a.tgljtempo ELSE '19000101'
            END, 112)) as hari_jatuh_tempo")
            ->where('a.stsrec', '!=', 'C')
            ->where(function ($query) {
                $query->where('a.tgltutup', '=', '')
                    ->orWhereNull('a.tgltutup');
            });

        if (! empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('a.nama', 'like', "%{$search}%")
                    ->orWhere('g.nm', 'like', "%{$search}%")
                    ->orWhere('a.nodep', 'like', "%{$search}%")
                    ->orWhere('a.nobilyet', 'like', "%{$search}%")
                    ->orWhere('a.nocif', 'like', "%{$search}%");
            });
        }

        if (! empty($filters['cabang'])) {
            $query->where('a.kdloc', $filters['cabang']);
        }

        if (! empty($filters['ao'])) {
            $query->where('a.kodeaoh', $filters['ao']);
        }

        $query->orderBy('kodeaoh', 'asc')
            ->orderBy('nama', 'asc')
            ->orderBy('norekening', 'asc');

        return $query->cursorPaginate($perPage);
    }

    public function getRekapitulasi(string $groupBy, array $filters = []): Collection
    {
        $query = DB::connection($this->connection)
            ->table('TOFDEP as a')
            ->where('a.stsrec', '!=', 'C')
            ->where(function ($query) {
                $query->where('a.tgltutup', '=', '')
                    ->orWhereNull('a.tgltutup');
            });

        if (! empty($filters['search'])) {
            $search = $filters['search'];
            $query->leftJoin('mCIF as search_cif', 'a.nocif', '=', 'search_cif.nocif')
                ->where(function ($q) use ($search) {
                    $q->where('a.nama', 'like', "%{$search}%")
                        ->orWhere('search_cif.nm', 'like', "%{$search}%")
                        ->orWhere('a.nodep', 'like', "%{$search}%")
                        ->orWhere('a.nobilyet', 'like', "%{$search}%")
                        ->orWhere('a.nocif', 'like', "%{$search}%");
                });
        }

        if (! empty($filters['cabang'])) {
            $query->where('a.kdloc', $filters['cabang']);
        }

        if (! empty($filters['ao'])) {
            $query->where('a.kodeaoh', $filters['ao']);
        }

        $aggregates = [
            DB::raw('COUNT(a.nodep) AS noa'),
            DB::raw('SUM(a.nomawal) AS nominal_awal'),
            DB::raw('SUM(a.nomrp) AS total_nominal'),
            DB::raw('SUM(a.saldrata1) AS saldo_rata'),
            DB::raw('SUM(a.bnghtg) AS bagi_hasil_htg'),
            DB::raw('SUM(a.bngbayar) AS bagi_hasil_bayar'),
            DB::raw('SUM(a.tax) AS pajak'),
            DB::raw('AVG(a.nisbah) AS avg_nisbah'),
            DB::raw('AVG(a.equivrate) AS avg_equivrate'),
        ];

        match ($groupBy) {
            'cabang' => $query->leftJoin('CABANG as b', 'a.kdloc', '=', 'b.kdloc')
                ->select(array_merge([DB::raw("ISNULL(b.nama, 'Tidak Terdaftar') as label"), 'a.kdloc as id'], $aggregates))
                ->groupBy('b.nama', 'a.kdloc')
                ->orderBy('a.kdloc', 'asc'),

            'wilayah' => $query->leftJoin('WILAYAH as b', 'a.kdwil', '=', 'b.kodewil')
                ->select(array_merge([DB::raw("ISNULL(b.ket, 'Tidak Terdaftar') as label"), 'a.kdwil as id'], $aggregates))
                ->groupBy('b.ket', 'a.kdwil')
                ->orderBy('a.kdwil', 'asc'),

            'ao' => $query->leftJoin('AO as b', 'a.kodeaoh', '=', 'b.kdao')
                ->select(array_merge([DB::raw("ISNULL(b.nmao, 'AO Tidak Terdaftar') as label"), 'a.kodeaoh as id'], $aggregates))
                ->groupBy('b.nmao', 'a.kodeaoh')
                ->orderBy('b.nmao', 'asc'),

            'produk' => $query->leftJoin('SETUPDEP as b', 'a.kdprd', '=', 'b.kdprd')
                ->select(array_merge([DB::raw("ISNULL(b.ket, 'Produk Tidak Terdaftar') as label"), 'a.kdprd as id'], $aggregates))
                ->groupBy('b.ket', 'a.kdprd')
                ->orderBy('a.kdprd', 'asc'),

            default => throw new \InvalidArgumentException('Invalid group_by parameter')
        };

        return $query->get();
    }

    public function getJatuhTempo(array $filters = [], int $perPage = 50): CursorPaginator
    {
        $query = DB::connection($this->connection)
            ->table('TOFDEP as a')
            ->leftJoin('mCIF as g', 'a.nocif', '=', 'g.nocif')
            ->leftJoin('AO as c', 'a.kodeaoh', '=', 'c.kdao')
            ->leftJoin('CABANG as h', 'a.kdloc', '=', 'h.kdloc')
            ->select([
                'a.nodep as norekening',
                'a.nobilyet',
                'a.nocif',
                DB::raw('COALESCE(g.nm, a.nama) as nama'),
                'a.kodeaoh',
                'c.nmao as ao',
                'a.kdloc',
                'h.nama as cabang',
                'a.nomrp as nominal',
                'a.nisbah',
                'a.equivrate',
                'a.tglbuka',
                'a.tgljtempo as jatuh_tempo',
                'a.aro',
                'a.stsrec',
                'a.stsacc',
            ])
            ->selectRaw("DATEDIFF(day, CAST(GETDATE() AS date), CONVERT(date, CASE
                WHEN a.tgljtempo IS NOT NULL AND a.tgljtempo <> '' AND ISDATE(a.tgljtempo) = 1
                THEN a.tgljtempo ELSE '19000101'
            END, 112)) as hari_jatuh_tempo")
            ->where('a.stsrec', '!=', 'C')
            ->where(function ($query) {
                $query->where('a.tgltutup', '=', '')
                    ->orWhereNull('a.tgltutup');
            })
            ->whereRaw("
                CONVERT(date, CASE
                    WHEN a.tgljtempo IS NOT NULL AND a.tgljtempo <> '' AND ISDATE(a.tgljtempo) = 1
                    THEN a.tgljtempo ELSE '19000101'
                END, 112)
                BETWEEN CAST(GETDATE() AS date)
                AND DATEADD(day, -DAY(DATEADD(month, 1, GETDATE())), DATEADD(month, 1, CAST(GETDATE() AS date)))
            ");

        if (! empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('a.nama', 'like', "%{$search}%")
                    ->orWhere('g.nm', 'like', "%{$search}%")
                    ->orWhere('a.nodep', 'like', "%{$search}%")
                    ->orWhere('a.nobilyet', 'like', "%{$search}%")
                    ->orWhere('a.nocif', 'like', "%{$search}%");
            });
        }

        if (! empty($filters['cabang'])) {
            $query->where('a.kdloc', $filters['cabang']);
        }

        if (! empty($filters['ao'])) {
            $query->where('a.kodeaoh', $filters['ao']);
        }

        $query->orderBy('jatuh_tempo', 'asc')
            ->orderBy('norekening', 'asc');

        return $query->cursorPaginate($perPage);
    }
}
