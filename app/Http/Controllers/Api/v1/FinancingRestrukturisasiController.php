<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Repositories\Interfaces\FinancingRestrukturisasiRepositoryInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * FinancingRestrukturisasiController
 * Controller tipis — logika bisnis ada di Repository (Rule #11).
 *
 * Routes:
 *   GET /api/v1/financing/restrukturisasi          → restrukturisasi()
 *   GET /api/v1/financing/top-up                   → topUp()
 */
class FinancingRestrukturisasiController extends Controller
{
    public function __construct(
        private readonly FinancingRestrukturisasiRepositoryInterface $repository
    ) {}

    /**
     * GET /api/v1/financing/restrukturisasi
     * Data addendum & restrukturisasi pembiayaan dengan summary scorecard.
     */
    public function restrukturisasi(Request $request): JsonResponse
    {
        try {
            $filters = [
                'cabang' => $request->query('cabang'),
                'ao'     => $request->query('ao'),
                'tahun'  => $request->query('tahun'),
                'bulan'  => $request->query('bulan'),
            ];

            $data    = $this->repository->getRestrukturisasi($filters);
            $summary = $this->repository->getRestrukturisasiSummary($filters);

            return response()->json([
                'success' => true,
                'data'    => $data->values(),
                'summary' => $summary,
                'period_meta' => $this->repository->getLastPeriodMeta(),
                'meta'    => [
                    'total'        => $data->count(),
                    'generated_at' => now()->toIso8601String(),
                ],
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'error'   => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * GET /api/v1/financing/top-up
     * Data kontrak top-up periode berjalan dengan summary scorecard.
     */
    public function topUp(Request $request): JsonResponse
    {
        try {
            $filters = [
                'ao_baru' => $request->query('ao_baru'),
            ];

            $data    = $this->repository->getTopUp($filters);
            $summary = $this->repository->getTopUpSummary();

            return response()->json([
                'success' => true,
                'data'    => $data->values(),
                'summary' => $summary,
                'meta'    => [
                    'total'        => $data->count(),
                    'generated_at' => now()->toIso8601String(),
                ],
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'error'   => $e->getMessage(),
            ], 500);
        }
    }
}
