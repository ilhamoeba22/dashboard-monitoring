<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Api\v1\Concerns\UsesMciSnapshotDatabase;
use App\Http\Controllers\Controller;
use App\Repositories\Interfaces\FundingAnalyticsRepositoryInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class FundingAnalyticsController extends Controller
{
    use UsesMciSnapshotDatabase;

    public function __construct(private readonly FundingAnalyticsRepositoryInterface $repository)
    {
    }

    public function perkembangan(Request $request): JsonResponse
    {
        try {
            return $this->withRequestedMciDatabase($request, fn () => response()->json(['success' => true, 'data' => $this->repository->getPerkembangan()]));
        } catch (\Throwable $e) {
            report($e);

            return response()->json(['success' => false, 'message' => 'Gagal memuat perkembangan funding'], 500);
        }
    }

    public function target(Request $request): JsonResponse
    {
        try {
            return $this->withRequestedMciDatabase($request, fn () => response()->json(['success' => true, 'data' => $this->repository->getTarget()]));
        } catch (\Throwable $e) {
            report($e);

            return response()->json(['success' => false, 'message' => 'Gagal memuat target funding'], 500);
        }
    }

    public function mutasi(Request $request): JsonResponse
    {
        try {
            return $this->withRequestedMciDatabase($request, fn () => response()->json(['success' => true, 'data' => $this->repository->getMutasi()]));
        } catch (\Throwable $e) {
            report($e);

            return response()->json(['success' => false, 'message' => 'Gagal memuat mutasi funding'], 500);
        }
    }
}
