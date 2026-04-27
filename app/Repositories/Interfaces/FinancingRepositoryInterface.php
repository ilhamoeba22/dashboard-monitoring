<?php

declare(strict_types=1);

namespace App\Repositories\Interfaces;

use Illuminate\Contracts\Pagination\CursorPaginator;
use Illuminate\Support\Collection;

interface FinancingRepositoryInterface
{
    /**
     * Dapatkan daftar data nominatif nasabah pembiayaan.
     * Menggunakan cursor pagination untuk performa optimal.
     *
     * @param  array<string, mixed>  $filters
     * @param  int  $perPage
     * @return CursorPaginator
     */
    public function getNominative(array $filters = [], int $perPage = 50): CursorPaginator;

    /**
     * Dapatkan daftar nama AO yang unik.
     *
     * @return Collection
     */
    public function getUniqueAos(): Collection;

    /**
     * Dapatkan detail jadwal angsuran berdasarkan nomor kontrak.
     *
     * @param  string  $nokontrak
     * @return array<string, mixed>
     */
    public function getDetailAngsuran(string $nokontrak): array;

    /**
     * Dapatkan data rekapitulasi pembiayaan secara dinamis.
     *
     * @param string $groupBy (cabang|wilayah|ao|produk|segmen|sekon|kolektibilitas)
     * @return Collection
     */
    public function getRekapitulasi(string $groupBy): Collection;

    /**
     * Dapatkan daftar pembiayaan yang sudah atau akan jatuh tempo.
     *
     * @param array<string, mixed> $filters
     * @param int $perPage
     * @return CursorPaginator
     */
    public function getJatuhTempo(array $filters = [], int $perPage = 50): CursorPaginator;
}
