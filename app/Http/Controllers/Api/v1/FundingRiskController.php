<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Api\v1\Concerns\UsesMciSnapshotDatabase;
use App\Http\Controllers\Controller;
use App\Repositories\Interfaces\FundingRiskRepositoryInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class FundingRiskController extends Controller
{
    use UsesMciSnapshotDatabase;

    public function __construct(private readonly FundingRiskRepositoryInterface $repository)
    {
    }

    public function overview(Request $request): JsonResponse
    {
        try {
            return $this->withRequestedMciDatabase($request, fn () => response()->json([
                'success' => true,
                'data' => $this->repository->getOverview(),
            ]));
        } catch (\Throwable $e) {
            report($e);

            return response()->json([
                'success' => false,
                'message' => 'Gagal memuat risk funding',
            ], 500);
        }
    }

    public function concentration(Request $request): JsonResponse
    {
        try {
            return $this->withRequestedMciDatabase($request, fn () => response()->json([
                'success' => true,
                'data' => $this->repository->getConcentrationDetails(),
            ]));
        } catch (\Throwable $e) {
            report($e);

            return response()->json([
                'success' => false,
                'message' => 'Gagal memuat concentration funding',
            ], 500);
        }
    }
}
