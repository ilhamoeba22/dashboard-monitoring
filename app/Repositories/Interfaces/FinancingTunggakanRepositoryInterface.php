<?php

declare(strict_types=1);

namespace App\Repositories\Interfaces;

interface FinancingTunggakanRepositoryInterface
{
    /**
     * Get list of financing accounts approaching or past maturity date (Jatuh Tempo).
     *
     * @param string|null $kdloc Filter by cabang (optional)
     * @param string|null $kdaoh Filter by AO (optional)
     * @return array
     */
    public function getJatuhTempoList(?string $kdloc = null, ?string $kdaoh = null, int $tahun = 0, int $bulan = 0): array;

    /**
     * Metadata periode dari query jatuh tempo terakhir.
     *
     * @return array<string, mixed>
     */
    public function getLastPeriodMeta(): array;

    /**
     * Get end-of-month projection for collectibility changes (Coll Monitoring).
     *
     * @param string|null $kdloc Filter by cabang (optional)
     * @param string|null $kdaoh Filter by AO (optional)
     * @return array
     */
    public function getCollMonitoringProyeksi(?string $kdloc = null, ?string $kdaoh = null): array;
}
