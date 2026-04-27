<?php

declare(strict_types=1);

namespace App\Repositories\Mci;

use App\Models\Mci\Funding\Tofdep;
use App\Repositories\Interfaces\DepositRepositoryInterface;
use App\Services\Mci\MciBaseRepository;
use Illuminate\Contracts\Pagination\CursorPaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class DepositRepository extends MciBaseRepository implements DepositRepositoryInterface
{
    protected function getTableName(): string
    {
        return 'TOFDEP';
    }

    public function getNominative(array $filters = [], int $perPage = 50): CursorPaginator
    {
        $query = Tofdep::query()
            ->with(['ao:kdao,nmao', 'cabang:kdloc,nama', 'cif:nocif,tgllhr'])
            ->where('stsrec', 'A')
            ->where('stsacc', '<>', 'W');

        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                  ->orWhere('nodep', 'like', "%{$search}%");
            });
        }

        if (!empty($filters['cabang'])) {
            $query->where('kdloc', $filters['cabang']);
        }

        if (!empty($filters['ao'])) {
            $query->where('kdaoh', $filters['ao']);
        }

        $query->orderBy('kdaoh', 'asc')
              ->orderBy('nama', 'asc')
              ->orderBy('nodep', 'asc');

        return $query->cursorPaginate($perPage);
    }

    public function getRekapitulasi(string $groupBy): Collection
    {
        $cacheKey = "deposit_rekapitulasi_{$groupBy}";

        return Cache::remember($cacheKey, 60, function () use ($groupBy) {
            $query = Tofdep::query()
                ->where('TOFDEP.stsrec', 'A')
                ->where('TOFDEP.stsacc', '<>', 'W');

            $aggregates = [
                DB::raw('COUNT(TOFDEP.nodep) AS noa'),
                DB::raw('SUM(TOFDEP.nomnl) AS total_nominal'),
                DB::raw('ROUND(AVG(TOFDEP.rate), 2) AS avg_rate'),
            ];

            match ($groupBy) {
                'cabang' => $query->join('CABANG as b', 'TOFDEP.kdloc', '=', 'b.kdloc')
                                  ->select(array_merge(['b.nama as label', 'b.kdloc as id'], $aggregates))
                                  ->groupBy('b.nama', 'b.kdloc')
                                  ->orderBy('b.nama', 'asc'),
                                  
                'wilayah' => $query->join('WILAYAH as b', 'TOFDEP.kdwil', '=', 'b.kodewil')
                                   ->select(array_merge(['b.ket as label', 'b.kodewil as id'], $aggregates))
                                   ->groupBy('b.ket', 'b.kodewil')
                                   ->orderBy('b.ket', 'asc'),
                                   
                'ao' => $query->join('AO as b', 'TOFDEP.kdaoh', '=', 'b.kdao')
                              ->select(array_merge(['b.nmao as label', 'TOFDEP.kdaoh as id'], $aggregates))
                              ->groupBy('TOFDEP.kdaoh', 'b.nmao')
                              ->orderBy('b.nmao', 'asc'),

                default => throw new \InvalidArgumentException("Invalid group_by parameter")
            };

            return $query->get();
        });
    }

    public function getJatuhTempo(array $filters = [], int $perPage = 50): CursorPaginator
    {
        $query = Tofdep::query()
            ->with(['ao:kdao,nmao', 'cabang:kdloc,nama'])
            ->where('stsrec', 'A')
            ->where('stsacc', '<>', 'W')
            // Jatuh tempo di bulan ini (tgljto <= Akhir bulan)
            ->whereRaw("tgljto <= EOMONTH(GETDATE())")
            ->whereRaw("tgljto > '1900-01-01'");

        if (!empty($filters['cabang'])) {
            $query->where('kdloc', $filters['cabang']);
        }

        $query->orderBy('tgljto', 'asc')
              ->orderBy('nodep', 'asc');

        return $query->cursorPaginate($perPage);
    }
}
