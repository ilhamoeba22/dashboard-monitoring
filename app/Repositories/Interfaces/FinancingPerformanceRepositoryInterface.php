<?php

declare(strict_types=1);

namespace App\Repositories\Interfaces;

use Illuminate\Support\Collection;

interface FinancingPerformanceRepositoryInterface
{
    /**
     * Dapatkan daftar repayment rate keseluruhan.
     *
     * @param  array<string, mixed>  $filters
     */
    public function getRepaymentRate(array $filters = []): Collection;

    /**
     * Dapatkan summary scorecard untuk halaman Repayment Rate.
     *
     * @param  array<string, mixed>  $filters
     * @return array<string, mixed>
     */
    public function getRepaymentRateSummary(array $filters = []): array;

    /**
     * Dapatkan daftar repayment rate untuk nasabah baru (akuisisi vs retensi).
     *
     * @param  array<string, mixed>  $filters
     */
    public function getRepaymentRateNew(array $filters = []): Collection;

    /**
     * Dapatkan summary scorecard untuk Repayment Rate Nasabah Baru.
     *
     * @param  array<string, mixed>  $filters
     * @return array<string, mixed>
     */
    public function getRepaymentRateNewSummary(array $filters = []): array;
}
