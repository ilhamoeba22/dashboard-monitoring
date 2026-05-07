<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Repositories\Mci\FinancingGrowthRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class FinancingGrowthController extends Controller
{
    private FinancingGrowthRepository $repository;

    public function __construct(FinancingGrowthRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * GET /api/v1/financing/growth-trend
     * 
     * Dimension based growth analysis
     */
    public function getGrowthTrend(Request $request): JsonResponse
    {
        try {
            $dimension = $request->get('dimension', 'ao');
            $data = $this->repository->getGrowthTrend($dimension);

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
        if (config('app.debug')) {
            \Log::channel('metrics')->error('FinancingGrowthController error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
        }

        return response()->json([
            'success' => false,
            'error' => config('app.debug') ? $e->getMessage() : 'An error occurred while fetching growth data.',
        ], 500);
    }
}
