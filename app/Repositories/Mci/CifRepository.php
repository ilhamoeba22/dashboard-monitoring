<?php

declare(strict_types=1);

namespace App\Repositories\Mci;

use App\Models\Mci\Cif\Mcif;
use App\Repositories\Interfaces\CifRepositoryInterface;
use App\Services\Mci\MciBaseRepository;
use Carbon\Carbon;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class CifRepository extends MciBaseRepository implements CifRepositoryInterface
{
    protected function getTableName(): string
    {
        return 'mCIF';
    }

    public function getList(array $filters = [], int $perPage = 50): LengthAwarePaginator
    {
        $query = Mcif::query()
            ->with(['ao:kdao,nmao', 'cabang:kdloc,nama'])
            ->where('stsrec', 'A');

        if (! empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('nm', 'like', "%{$search}%")
                    ->orWhere('nocif', 'like', "%{$search}%")
                    ->orWhere('noid', 'like', "%{$search}%");
            });
        }

        if (! empty($filters['cabang'])) {
            $query->where('kdloc', $filters['cabang']);
        }

        if (! empty($filters['ao'])) {
            $query->where('aohand', $filters['ao']);
        }

        // Fleksibilitas Filter Portofolio
        if (isset($filters['has_tabungan']) && $filters['has_tabungan']) {
            $query->whereHas('tabungan', function ($q) {
                $q->where('stsrec', 'A');
            });
        }

        if (isset($filters['has_deposito']) && $filters['has_deposito']) {
            $query->whereHas('deposito', function ($q) {
                $q->where('stsrec', 'A');
            });
        }

        if (isset($filters['has_pembiayaan']) && $filters['has_pembiayaan']) {
            $query->whereHas('pembiayaan', function ($q) {
                $q->where('stsrec', 'A');
            });
        }

        $query->orderBy('kdloc', 'asc')
            ->orderBy('nm', 'asc')
            ->orderBy('nocif', 'asc');

        // H5 Fix: Gunakan paginate() untuk fetch all (bypass 500 limit cursorPaginate)
        // cursorPaginate() punya hard limit 500 records internal Laravel
        if ($perPage >= 100000) {
            return $query->paginate($perPage);
        }

        return $query->cursorPaginate($perPage);
    }

    public function getRekapitulasi(string $groupBy): Collection
    {
        $cacheKey = "cif_rekapitulasi_{$groupBy}";

        // Terapkan Rule #6: Cache 60 detik
        return Cache::remember($cacheKey, 60, function () use ($groupBy) {
            $query = Mcif::query()->where('mCIF.stsrec', 'A');

            $aggregates = [
                DB::raw('COUNT(mCIF.nocif) AS total_nasabah'),
            ];

            match ($groupBy) {
                'cabang' => $query->join('CABANG as b', 'mCIF.kdloc', '=', 'b.kdloc')
                    ->select(array_merge(['b.nama as label', 'b.kdloc as id'], $aggregates))
                    ->groupBy('b.nama', 'b.kdloc')
                    ->orderBy('b.nama', 'asc'),

                'wilayah' => $query->join('WILAYAH as b', 'mCIF.kdwil', '=', 'b.kodewil')
                    ->select(array_merge(['b.ket as label', 'b.kodewil as id'], $aggregates))
                    ->groupBy('b.ket', 'b.kodewil')
                    ->orderBy('b.ket', 'asc'),

                'ao' => $query->join('AO as b', 'mCIF.aohand', '=', 'b.kdao')
                    ->select(array_merge(['b.nmao as label', 'mCIF.aohand as id'], $aggregates))
                    ->groupBy('mCIF.aohand', 'b.nmao')
                    ->orderBy('b.nmao', 'asc'),

                'segmen' => $query->join('SEGMEN as b', 'mCIF.segmen', '=', 'b.kdseg')
                    ->select(array_merge(['b.ket as label', 'mCIF.segmen as id'], $aggregates))
                    ->groupBy('mCIF.segmen', 'b.ket')
                    ->orderBy('b.ket', 'asc'),

                'agama' => $query->select(array_merge([
                    DB::raw("CASE mCIF.agama WHEN '1' THEN 'ISLAM' WHEN '2' THEN 'KRISTEN PROTESTAN' WHEN '3' THEN 'KATOLIK' WHEN '4' THEN 'HINDU' WHEN '5' THEN 'BUDHA' ELSE 'LAINNYA' END as label"),
                    'mCIF.agama as id',
                ], $aggregates))
                    ->groupBy('mCIF.agama')
                    ->orderBy('mCIF.agama', 'asc'),

                default => throw new \InvalidArgumentException('Invalid group_by parameter')
            };

            return $query->get();
        });
    }

    public function getDetail(string $nocif): array
    {
        $cif = Mcif::query()
            ->with([
                'ao:kdao,nmao',
                'cabang:kdloc,nama',
                'wilayah:kodewil,ket',
                'tabungan:nocif,notab,sahirrp,stsrec',
                'deposito:nocif,nodep,nomnl,stsrec',
                'pembiayaan:nocif,nokontrak,mdlawal,osmdlc,stsrec',
            ])
            ->where('nocif', $nocif)
            ->where('stsrec', 'A')
            ->firstOrFail();

        return [
            'nocif' => $cif->nocif,
            'nama' => ucwords(strtolower((string) $cif->nm)),
            'ktp' => $cif->noid,
            'npwp' => $cif->npwp,
            'tempat_lahir' => $cif->tmplhr,
            'tanggal_lahir' => $this->formatSafeDate((string) $cif->tgllhr),
            'umur' => $this->calculateAge((string) $cif->tgllhr),
            'alamat' => $cif->alamat,
            'kelurahan' => $cif->kelurahan,
            'kecamatan' => $cif->kecamatan,
            'kota' => $cif->kota,
            'telp' => $cif->hp ?? $cif->telprmh,
            'ibu_kandung' => ucwords(strtolower((string) $cif->nmibu)),
            'cabang' => optional($cif->cabang)->nama,
            'ao' => optional($cif->ao)->nmao,
            'tanggal_buka' => $this->formatSafeDate((string) $cif->tglbuka),
            'portofolio' => [
                'tabungan' => $cif->tabungan->where('stsrec', 'A')->values(),
                'deposito' => $cif->deposito->where('stsrec', 'A')->values(),
                'pembiayaan' => $cif->pembiayaan->where('stsrec', 'A')->values(),
            ],
        ];
    }

    private function formatSafeDate(string $date): string
    {
        if (empty($date) || str_starts_with($date, '0000') || str_starts_with($date, '1900')) {
            return '-';
        }
        try {
            return Carbon::parse($date)->format('d M Y');
        } catch (\Exception $e) {
            return '-';
        }
    }

    private function calculateAge(string $date): int
    {
        if (empty($date) || str_starts_with($date, '0000') || str_starts_with($date, '1900')) {
            return 0;
        }
        try {
            return Carbon::parse($date)->diffInYears(Carbon::now());
        } catch (\Exception $e) {
            return 0;
        }
    }
}
