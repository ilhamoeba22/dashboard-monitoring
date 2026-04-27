<?php

declare(strict_types=1);

namespace App\Repositories\Mci;

use App\Models\Mci\Funding\Toftabb;
use App\Repositories\Interfaces\SavingRepositoryInterface;
use App\Services\Mci\MciBaseRepository;
use Illuminate\Contracts\Pagination\CursorPaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class SavingRepository extends MciBaseRepository implements SavingRepositoryInterface
{
    protected function getTableName(): string
    {
        return 'TOFTABB';
    }

    public function getNominative(array $filters = [], int $perPage = 50): CursorPaginator
    {
        $query = Toftabb::query()
            // Asumsi relasi ini akan ditambahkan nanti ke Toftabb
            ->with(['ao:kdao,nmao', 'cabang:kdloc,nama', 'cif:nocif,tgllhr'])
            ->where('stsrec', 'A');

        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                  ->orWhere('notab', 'like', "%{$search}%");
            });
        }

        if (!empty($filters['cabang'])) {
            $query->where('kdloc', $filters['cabang']);
        }

        if (!empty($filters['ao'])) {
            $query->where('kodeaoh', $filters['ao']); // Kode AO Tabungan
        }

        $query->orderBy('kodeaoh', 'asc')
              ->orderBy('nama', 'asc')
              ->orderBy('notab', 'asc');

        return $query->cursorPaginate($perPage);
    }

    public function getRekapitulasi(string $groupBy): Collection
    {
        $cacheKey = "saving_rekapitulasi_{$groupBy}";

        return Cache::remember($cacheKey, 60, function () use ($groupBy) {
            $query = Toftabb::query()
                ->where('TOFTABB.stsrec', 'A');

            $aggregates = [
                DB::raw('COUNT(TOFTABB.notab) AS noa'),
                DB::raw('SUM(TOFTABB.sahirrp) AS total_saldo'),
            ];

            match ($groupBy) {
                'cabang' => $query->join('CABANG as b', 'TOFTABB.kdloc', '=', 'b.kdloc')
                                  ->select(array_merge(['b.nama as label', 'b.kdloc as id'], $aggregates))
                                  ->groupBy('b.nama', 'b.kdloc')
                                  ->orderBy('b.nama', 'asc'),
                                  
                'wilayah' => $query->join('WILAYAH as b', 'TOFTABB.kdwil', '=', 'b.kodewil')
                                   ->select(array_merge(['b.ket as label', 'b.kodewil as id'], $aggregates))
                                   ->groupBy('b.ket', 'b.kodewil')
                                   ->orderBy('b.ket', 'asc'),
                                   
                'ao' => $query->join('AO as b', 'TOFTABB.kodeaoh', '=', 'b.kdao')
                              ->select(array_merge(['b.nmao as label', 'TOFTABB.kodeaoh as id'], $aggregates))
                              ->groupBy('TOFTABB.kodeaoh', 'b.nmao')
                              ->orderBy('b.nmao', 'asc'),

                default => throw new \InvalidArgumentException("Invalid group_by parameter")
            };

            return $query->get();
        });
    }

    public function getDoormant(array $filters = [], int $perPage = 50): CursorPaginator
    {
        $query = Toftabb::query()
            ->with(['ao:kdao,nmao', 'cabang:kdloc,nama'])
            ->where('stsrec', 'A')
            ->where('stsacc', 'D'); // D = Doormant / Pasif

        if (!empty($filters['cabang'])) {
            $query->where('kdloc', $filters['cabang']);
        }

        $query->orderBy('tgltrans', 'asc') // Urutkan dari transaksi paling lama
              ->orderBy('notab', 'asc');

        return $query->cursorPaginate($perPage);
    }
}
