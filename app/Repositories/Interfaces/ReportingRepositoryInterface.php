<?php

declare(strict_types=1);

namespace App\Repositories\Interfaces;

interface ReportingRepositoryInterface
{
    /**
     * Dapatkan data laporan keuangan/operasional secara spesifik.
     *
     * @param string $jenisLaporan (neraca|labarugi|aruskas|jamkrida|ira)
     * @param array<string, mixed> $filters
     * @return array<string, mixed>
     */
    public function getReport(string $jenisLaporan, array $filters = []): array;
}
