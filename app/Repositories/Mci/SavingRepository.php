<?php

declare(strict_types=1);

namespace App\Repositories\Mci;

use App\Repositories\Interfaces\SavingRepositoryInterface;
use App\Services\Mci\MciBaseRepository;
use Illuminate\Contracts\Pagination\CursorPaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class SavingRepository extends MciBaseRepository implements SavingRepositoryInterface
{
    protected function getTableName(): string
    {
        return 'TOFTABB';
    }

    public function getSummary(): array
    {
        $row = DB::connection($this->connection)
            ->table('TOFTABB as a')
            ->where('a.stsrec', '!=', 'C')
            ->where(function ($query) {
                $query->where('a.tgltutup', '=', '')
                    ->orWhereNull('a.tgltutup');
            })
            ->selectRaw('COUNT(a.notab) AS noa')
            ->selectRaw('SUM(a.sahirrp) AS total_saldo')
            ->selectRaw('SUM(a.saldoavg) AS saldo_rata')
            ->selectRaw('SUM(a.bhhtg) AS bagi_hasil_htg')
            ->selectRaw('SUM(a.bhbayar) AS bagi_hasil_bayar')
            ->selectRaw('SUM(a.taxbayar) AS pajak_bayar')
            ->first();

        return [
            'noa' => (int) ($row->noa ?? 0),
            'total_saldo' => (float) ($row->total_saldo ?? 0),
            'saldo_rata' => (float) ($row->saldo_rata ?? 0),
            'bagi_hasil_htg' => (float) ($row->bagi_hasil_htg ?? 0),
            'bagi_hasil_bayar' => (float) ($row->bagi_hasil_bayar ?? 0),
            'pajak_bayar' => (float) ($row->pajak_bayar ?? 0),
        ];
    }

    public function getFilterOptions(): array
    {
        $activeSaving = function ($query): void {
            $query->where('a.stsrec', '!=', 'C')
                ->where(function ($subQuery) {
                    $subQuery->where('a.tgltutup', '=', '')
                        ->orWhereNull('a.tgltutup');
                });
        };

        $cabangs = DB::connection($this->connection)
            ->table('TOFTABB as a')
            ->leftJoin('CABANG as b', 'a.kodeloc', '=', 'b.kdloc')
            ->where($activeSaving)
            ->whereNotNull('a.kodeloc')
            ->where('a.kodeloc', '!=', '')
            ->select([
                'a.kodeloc as value',
                DB::raw("ISNULL(b.nama, a.kodeloc) as title"),
            ])
            ->selectRaw('COUNT(a.notab) AS rekening')
            ->selectRaw('SUM(CAST(ISNULL(a.sahirrp, 0) AS DECIMAL(38, 2))) AS saldo')
            ->groupBy('a.kodeloc', 'b.nama')
            ->orderBy('a.kodeloc')
            ->get();

        $accountOfficers = DB::connection($this->connection)
            ->table('TOFTABB as a')
            ->leftJoin('AO as b', 'a.kodeaoh', '=', 'b.kdao')
            ->where($activeSaving)
            ->whereNotNull('a.kodeaoh')
            ->where('a.kodeaoh', '!=', '')
            ->select([
                'a.kodeaoh as value',
                DB::raw("ISNULL(b.nmao, a.kodeaoh) as title"),
            ])
            ->selectRaw('COUNT(a.notab) AS rekening')
            ->selectRaw('SUM(CAST(ISNULL(a.sahirrp, 0) AS DECIMAL(38, 2))) AS saldo')
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
            ->table('TOFTABB as a')
            ->leftJoin('AO as c', 'a.kodeaoh', '=', 'c.kdao')
            ->leftJoin('CABANG as d', 'a.kodeloc', '=', 'd.kdloc')
            ->leftJoin('WILAYAH as e', 'a.kdwil', '=', 'e.kodewil')
            ->leftJoin('SETUPTAB as f', 'a.kodeprd', '=', 'f.kodeprd')
            ->leftJoin('mCIF as g', 'a.nocif', '=', 'g.nocif')
            ->select([
                'a.notab as norekening',
                'a.nocif',
                'a.fnama as nama',
                'g.alamat',
                'a.kodeprd',
                'f.nmpjgprd as jenis',
                'a.kodeaoh',
                'c.nmao as ao',
                'a.kodeloc as kdloc',
                'd.nama as cabang',
                'a.kdwil',
                'e.ket as wilayah',
                'a.tglbuka',
                'a.tgltrnakh',
                'a.sawalrp',
                'a.sahirrp as saldo',
                'a.saldoavg as saldo_rata',
                'a.bhhtg',
                'a.bhbayar',
                'a.taxbayar',
                'a.nisbah',
                'a.rate',
                'a.stsrec',
                'a.stsacc',
            ])
            ->selectRaw("
                CASE
                    WHEN ISDATE(g.tgllhr) = 1
                    THEN ROUND((CAST(DATEDIFF(day, CAST(g.tgllhr AS DATE), GETDATE()) AS FLOAT) / 365.25), 0)
                    ELSE 0
                END AS age
            ")
            ->where('a.stsrec', '!=', 'C')
            ->where(function ($query) {
                $query->where('a.tgltutup', '=', '')
                    ->orWhereNull('a.tgltutup');
            });

        if (! empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('a.fnama', 'like', "%{$search}%")
                    ->orWhere('a.notab', 'like', "%{$search}%")
                    ->orWhere('a.nocif', 'like', "%{$search}%");
            });
        }

        if (! empty($filters['cabang'])) {
            $query->where('a.kodeloc', $filters['cabang']);
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
            ->table('TOFTABB as a')
            ->where('a.stsrec', '!=', 'C')
            ->where(function ($query) {
                $query->where('a.tgltutup', '=', '')
                    ->orWhereNull('a.tgltutup');
            });

        if (! empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('a.fnama', 'like', "%{$search}%")
                    ->orWhere('a.notab', 'like', "%{$search}%")
                    ->orWhere('a.nocif', 'like', "%{$search}%");
            });
        }

        if (! empty($filters['cabang'])) {
            $query->where('a.kodeloc', $filters['cabang']);
        }

        if (! empty($filters['ao'])) {
            $query->where('a.kodeaoh', $filters['ao']);
        }

        $aggregates = [
            DB::raw('COUNT(a.notab) AS noa'),
            DB::raw('SUM(a.sawalrp) AS saldo_awal'),
            DB::raw('SUM(a.sahirrp) AS total_saldo'),
            DB::raw('SUM(a.saldoavg) AS saldo_rata'),
            DB::raw('SUM(a.bhhtg) AS bagi_hasil_htg'),
            DB::raw('SUM(a.bhbayar) AS bagi_hasil_bayar'),
            DB::raw('SUM(a.taxbayar) AS pajak_bayar'),
        ];

        match ($groupBy) {
            'cabang' => $query->leftJoin('CABANG as b', 'a.kodeloc', '=', 'b.kdloc')
                ->select(array_merge([DB::raw("ISNULL(b.nama, 'Tidak Terdaftar') as label"), 'a.kodeloc as id'], $aggregates))
                ->groupBy('b.nama', 'a.kodeloc')
                ->orderBy('a.kodeloc', 'asc'),

            'wilayah' => $query->leftJoin('WILAYAH as b', 'a.kdwil', '=', 'b.kodewil')
                ->select(array_merge([DB::raw("ISNULL(b.ket, 'Tidak Terdaftar') as label"), 'a.kdwil as id'], $aggregates))
                ->groupBy('b.ket', 'a.kdwil')
                ->orderBy('a.kdwil', 'asc'),

            'ao' => $query->leftJoin('AO as b', 'a.kodeaoh', '=', 'b.kdao')
                ->select(array_merge([DB::raw("ISNULL(b.nmao, 'AO Tidak Terdaftar') as label"), 'a.kodeaoh as id'], $aggregates))
                ->groupBy('b.nmao', 'a.kodeaoh')
                ->orderBy('b.nmao', 'asc'),

            'produk' => $query->leftJoin('SETUPTAB as b', 'a.kodeprd', '=', 'b.kodeprd')
                ->select(array_merge([DB::raw("ISNULL(b.nmpjgprd, 'Produk Tidak Terdaftar') as label"), 'a.kodeprd as id'], $aggregates))
                ->groupBy('b.nmpjgprd', 'a.kodeprd')
                ->orderBy('a.kodeprd', 'asc'),

            default => throw new \InvalidArgumentException('Invalid group_by parameter')
        };

        return $query->get();
    }

    public function getDoormant(array $filters = [], int $perPage = 50): CursorPaginator
    {
        $query = DB::connection($this->connection)
            ->table('TOFTABC')
            ->leftJoin('TOFTABB', 'TOFTABC.notab', '=', 'TOFTABB.notab')
            ->leftJoin('AO', 'TOFTABB.kodeaoh', '=', 'AO.kdao')
            ->leftJoin('WILAYAH', 'TOFTABB.kdwil', '=', 'WILAYAH.kodewil')
            ->select([
                'TOFTABC.notab as norekening',
                'TOFTABB.nocif',
                'TOFTABC.fnama as nama',
                'TOFTABC.tglbuka',
                'TOFTABC.saldobuku',
                'TOFTABC.sahirrp as saldo',
                'TOFTABB.saldoavg as saldo_rata',
                'TOFTABB.kodeloc as kdloc',
                'TOFTABB.kodeaoh',
                'AO.nmao as ao',
                'TOFTABB.kdwil',
                'WILAYAH.ket as wilayah',
            ])
            ->selectRaw("CAST(CASE
                WHEN TOFTABC.tgltrnakh IS NULL OR TOFTABC.tgltrnakh = '' OR TOFTABC.tgltrnakh = '0'
                THEN TOFTABC.tglbuka ELSE TOFTABC.tgltrnakh END AS DATE) as tgltrans")
            ->selectRaw("DATEDIFF(day, CAST(CASE
                WHEN TOFTABC.tgltrnakh IS NULL OR TOFTABC.tgltrnakh = '' OR TOFTABC.tgltrnakh = '0'
                THEN TOFTABC.tglbuka ELSE TOFTABC.tgltrnakh END AS DATE), GETDATE()) as hari_pasif")
            ->where('TOFTABB.stsrec', '!=', 'C');

        if (! empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('TOFTABC.fnama', 'like', "%{$search}%")
                    ->orWhere('TOFTABC.notab', 'like', "%{$search}%")
                    ->orWhere('TOFTABB.nocif', 'like', "%{$search}%");
            });
        }

        if (! empty($filters['cabang'])) {
            $query->where('TOFTABB.kodeloc', $filters['cabang']);
        }

        if (! empty($filters['ao'])) {
            $query->where('TOFTABB.kodeaoh', $filters['ao']);
        }

        $query->orderBy('tgltrans', 'asc')
            ->orderBy('norekening', 'asc');

        return $query->cursorPaginate($perPage);
    }
}
