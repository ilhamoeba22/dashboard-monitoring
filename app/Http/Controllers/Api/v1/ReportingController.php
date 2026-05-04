<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Repositories\Interfaces\ReportingRepositoryInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ReportingController extends Controller
{
    private ReportingRepositoryInterface $repository;

    public function __construct(ReportingRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    /**
     * GET /api/v1/reporting/{jenis}
     */
    public function generate(string $jenis, Request $request): JsonResponse
    {
        try {
            $filters = [
                'periode' => $request->query('periode'),
                'cabang' => $request->query('cabang'),
            ];

            $data = $this->repository->getReport($jenis, $filters);

            return response()->json([
                'success' => true,
                'data' => $data,
            ]);
        } catch (\InvalidArgumentException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 404);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal membuat laporan: '.$jenis,
            ], 500);
        }
    }
}
