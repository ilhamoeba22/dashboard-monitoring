<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Repositories\Interfaces\FinancingTunggakanRepositoryInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class FinancingTunggakanController extends Controller
{
    private FinancingTunggakanRepositoryInterface $repository;

    public function __construct(FinancingTunggakanRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    /**
     * GET /api/v1/financing/tunggakan/jatuh-tempo
     */
    public function jatuhTempo(Request $request): JsonResponse
    {
        try {
            $kdloc = $request->get('cabang');
            $kdaoh = $request->get('ao');
            $tahun = (int) $request->get('tahun', 0);
            $bulan = (int) $request->get('bulan', 0);

            $data = $this->repository->getJatuhTempoList($kdloc, $kdaoh, $tahun, $bulan);

            return response()->json([
                'success' => true,
                'data' => $data,
                'period_meta' => $this->repository->getLastPeriodMeta(),
            ]);
        } catch (\Throwable $e) {
            return $this->handleError($e);
        }
    }

    /**
     * GET /api/v1/financing/tunggakan/coll-monitoring
     */
    public function collMonitoring(Request $request): JsonResponse
    {
        try {
            $kdloc = $request->get('cabang');
            $kdaoh = $request->get('ao');

            $data = $this->repository->getCollMonitoringProyeksi($kdloc, $kdaoh);

            return response()->json([
                'success' => true,
                'data' => $data,
                'meta' => $this->repository->getLastCollMonitoringMeta(),
            ]);
        } catch (\Throwable $e) {
            return $this->handleError($e);
        }
    }

    /**
     * Handle error with proper logging
     */
    private function handleError(\Throwable $e): JsonResponse
    {
        $message = config('app.debug')
            ? $e->getMessage()
            : 'Terjadi kesalahan saat mengambil data tunggakan.';

        if (config('app.debug')) {
            \Log::channel('metrics')->error('FinancingTunggakanController error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
        }

        return response()->json([
            'success' => false,
            'error' => $message,
        ], 500);
    }
}
