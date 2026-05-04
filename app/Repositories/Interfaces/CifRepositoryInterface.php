<?php

declare(strict_types=1);

namespace App\Repositories\Interfaces;

use Illuminate\Contracts\Pagination\CursorPaginator;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

interface CifRepositoryInterface
{
    /**
     * Dapatkan daftar nasabah (CIF).
     *
     * @param  array<string, mixed>  $filters
     */
    public function getList(array $filters = [], int $perPage = 50): LengthAwarePaginator|CursorPaginator;

    /**
     * Dapatkan sebaran demografi nasabah.
     *
     * @param  string  $groupBy  (cabang|wilayah|ao|segmen|agama)
     */
    public function getRekapitulasi(string $groupBy): Collection;

    /**
     * Dapatkan detail nasabah berdasarkan CIF.
     *
     * @return array<string, mixed>
     */
    public function getDetail(string $nocif): array;
}
