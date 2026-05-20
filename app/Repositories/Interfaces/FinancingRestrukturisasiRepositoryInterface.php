<?php

declare(strict_types=1);

namespace App\Repositories\Interfaces;

use Illuminate\Support\Collection;

interface FinancingRestrukturisasiRepositoryInterface
{
    /**
     * Dapatkan daftar pembiayaan yang telah direstrukturisasi/addendum.
     *
     * @param  array<string, mixed>  $filters  (cabang, ao)
     */
    public function getRestrukturisasi(array $filters = []): Collection;

    /**
     * Dapatkan summary scorecard untuk halaman Restrukturisasi.
     *
     * @param  array<string, mixed>  $filters
     * @return array<string, mixed>
     */
    public function getRestrukturisasiSummary(array $filters = []): array;

    /**
     * Dapatkan daftar kontrak Top-Up bulan berjalan.
     *
     * @param  array<string, mixed>  $filters  (ao_baru)
     */
    public function getTopUp(array $filters = []): Collection;

    /**
     * Dapatkan summary scorecard untuk halaman Top-Up.
     *
     * @return array<string, mixed>
     */
    public function getTopUpSummary(): array;
}
