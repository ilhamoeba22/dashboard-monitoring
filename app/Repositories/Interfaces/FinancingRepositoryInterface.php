<?php

declare(strict_types=1);

namespace App\Repositories\Interfaces;

use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Support\Collection;

interface FinancingRepositoryInterface
{
    /**
     * Dapatkan daftar data nominatif nasabah pembiayaan.
     * Menggunakan pagination untuk mendukung lompatan halaman di UI.
     *
     * @param  array<string, mixed>  $filters
     */
    public function getNominative(array $filters = [], int $perPage = 50): Paginator;

    /**
     * Dapatkan daftar nama AO yang unik.
     */
    public function getUniqueAos(): Collection;

    /**
     * Dapatkan daftar cabang yang unik.
     */
    public function getUniqueCabangs(): Collection;

    /**
     * Dapatkan detail jadwal angsuran berdasarkan nomor kontrak.
     *
     * @return array<string, mixed>
     */
    public function getDetailAngsuran(string $nokontrak): array;

    /**
     * Dapatkan data rekapitulasi pembiayaan secara dinamis.
     *
     * @param  string  $groupBy  (cabang|wilayah|ao|produk|segmen|sekon|kolektibilitas)
     */
    public function getRekapitulasi(string $groupBy): Collection;

    /**
     * Dapatkan daftar pembiayaan yang sudah atau akan jatuh tempo.
     *
     * @param  array<string, mixed>  $filters
     */
    public function getJatuhTempo(array $filters = [], int $perPage = 50): Paginator;
}
