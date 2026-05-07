<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Repositories\Mci\FinancingOverviewRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * FinancingOverviewController
 *
 * API Controller untuk halaman Financing Overview.
 * Menyediakan data realtime, historical, dan comparison.
 *
 * Endpoints:
 * - GET /api/v1/financing/overview - Full overview (all data combined)
 * - GET /api/v1/financing/overview/realtime - Realtime metrics only
 * - GET /api/v1/financing/overview/trend - Historical trend
 * - GET /api/v1/financing/overview/compare - Comparison data
 */
class FinancingOverviewController extends Controller
{
    private FinancingOverviewRepository $repository;

    public function __construct(FinancingOverviewRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * GET /api/v1/financing/overview
     *
     * Full overview - semua data (realtime, trend, comparison)
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $kdloc = $request->get('cabang');
            $data = $this->repository->getCompleteOverview($kdloc);

            return response()->json([
                'success' => true,
                'data' => $data,
            ]);
        } catch (\Throwable $e) {
            return $this->handleError($e);
        }
    }

    /**
     * GET /api/v1/financing/overview/realtime
     *
     * Realtime metrics dari database aktif
     */
    public function realtime(): JsonResponse
    {
        try {
            $data = $this->repository->getRealtimeMetrics();

            return response()->json([
                'success' => true,
                'data' => $data,
            ]);
        } catch (\Throwable $e) {
            return $this->handleError($e);
        }
    }

    /**
     * GET /api/v1/financing/overview/trend
     *
     * Trend data (O/S & NOA) - UNION ALL TOFLMB + TOFLMBEOM
     * Query params: period (3, 6, 12) atau months
     */
    public function trend(Request $request): JsonResponse
    {
        try {
            // Support both 'period' and 'months' params
            $period = (int) $request->get('period', $request->get('months', 12));
            $period = max(3, min(36, $period)); // Min 3 months, Max 36

            $data = $this->repository->getTrendData($period);

            return response()->json([
                'success' => true,
                'data' => $data,
            ]);
        } catch (\Throwable $e) {
            return $this->handleError($e);
        }
    }

    /**
     * GET /api/v1/financing/overview/compare
     *
     * Comparison antara realtime dan historical
     * Query params: start (YYYY-MM), end (YYYY-MM), periods[] (DB names)
     */
    public function compare(Request $request): JsonResponse
    {
        try {
            // MULTI-PERIOD MODE (New)
            if ($request->has('periods')) {
                $periods = (array) $request->get('periods');
                $data = $this->repository->getMultiPeriodComparison($periods);

                return response()->json([
                    'success' => true,
                    'data' => $data,
                ]);
            }

            // CLASSIC MODE (1-to-1)
            $start = $request->get('start');
            $end = $request->get('end');

            // Validate date format if provided
            if ($start && ! preg_match('/^\d{4}-\d{2}$/', $start)) {
                return response()->json([
                    'success' => false,
                    'error' => 'Invalid start date format. Use YYYY-MM',
                ], 400);
            }

            if ($end && ! preg_match('/^\d{4}-\d{2}$/', $end)) {
                return response()->json([
                    'success' => false,
                    'error' => 'Invalid end date format. Use YYYY-MM',
                ], 400);
            }

            $data = $this->repository->getComparison($start, $end);

            return response()->json([
                'success' => true,
                'data' => $data,
            ]);
        } catch (\Throwable $e) {
            return $this->handleError($e);
        }
    }

    /**
     * GET /api/v1/financing/overview/periods
     *
     * List semua database MCI yang tersedia
     */
    public function periods(\App\Services\Mci\MciConnectionService $mciService): JsonResponse
    {
        try {
            $data = $mciService->getDatabasesWithMeta();

            return response()->json([
                'success' => true,
                'data' => $data,
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
            : 'An error occurred while fetching financing overview data.';

        if (config('app.debug')) {
            \Log::channel('metrics')->error('FinancingOverviewController error', [
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
