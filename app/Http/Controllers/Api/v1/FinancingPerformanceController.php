<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Repositories\Interfaces\FinancingPerformanceRepositoryInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * FinancingPerformanceController
 *
 * Routes:
 *   GET /api/v1/financing/performance/repayment-rate
 *   GET /api/v1/financing/performance/repayment-rate-new
 */
class FinancingPerformanceController extends Controller
{
    public function __construct(
        private readonly FinancingPerformanceRepositoryInterface $repository
    ) {}

    public function repaymentRate(Request $request): JsonResponse
    {
        try {
            $filters = [
                'ao' => $request->query('ao'),
                'cabang' => $request->query('cabang'),
                'segmen' => $request->query('segmen'),
                'collectibility' => $request->query('collectibility'),
                'rr_min' => $request->query('rr_min'),
                'rr_max' => $request->query('rr_max'),
                'search' => $request->query('search'),
                'sort_by' => $request->query('sort_by', 'nama_ao'),
                'sort_order' => $request->query('sort_order', 'asc'),
                'page' => $request->query('page', 1),
                'per_page' => $request->query('per_page', 50),
            ];

            $data = $this->repository->getRepaymentRate($filters);
            $summary = $this->repository->getRepaymentRateSummary($filters);

            return response()->json([
                'success' => true,
                'data' => $data->values(),
                'summary' => $summary,
                'meta' => [
                    'total' => $data->count(),
                    'filters' => $filters,
                    'generated_at' => now()->toIso8601String(),
                ],
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function repaymentRateNew(Request $request): JsonResponse
    {
        try {
            $filters = [
                'ao' => $request->query('ao'),
                'onboarding_months' => $request->query('onboarding_months', 6),
                'risk_status' => $request->query('risk_status'),
                'search' => $request->query('search'),
                'sort_by' => $request->query('sort_by', 'tgleff'),
                'sort_order' => $request->query('sort_order', 'desc'),
            ];

            $data = $this->repository->getRepaymentRateNew($filters);
            $summary = $this->repository->getRepaymentRateNewSummary($filters);

            return response()->json([
                'success' => true,
                'data' => $data->values(),
                'summary' => $summary,
                'meta' => [
                    'total' => $data->count(),
                    'filters' => $filters,
                    'generated_at' => now()->toIso8601String(),
                ],
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
