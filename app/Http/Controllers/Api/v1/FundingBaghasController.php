<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Api\v1\Concerns\UsesMciSnapshotDatabase;
use App\Http\Controllers\Controller;
use App\Repositories\Interfaces\FundingBaghasRepositoryInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class FundingBaghasController extends Controller
{
    use UsesMciSnapshotDatabase;

    public function __construct(private readonly FundingBaghasRepositoryInterface $repository)
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
                'message' => 'Gagal memuat bagi hasil funding',
            ], 500);
        }
    }
}
