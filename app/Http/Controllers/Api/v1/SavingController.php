<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Repositories\Interfaces\SavingRepositoryInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SavingController extends Controller
{
    private SavingRepositoryInterface $repository;

    public function __construct(SavingRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function nominative(Request $request): JsonResponse
    {
        try {
            $filters = [
                'search' => $request->query('search'),
                'cabang' => $request->query('cabang'),
                'ao'     => $request->query('ao'),
            ];

            $perPage = (int) $request->query('per_page', '50');
            $data = $this->repository->getNominative($filters, $perPage);

            return response()->json(['success' => true, 'data' => $data]);
        } catch (\Throwable $e) {
            return response()->json(['success' => false, 'message' => 'Gagal memuat data nominatif tabungan'], 500);
        }
    }

    public function rekapitulasi(Request $request): JsonResponse
    {
        try {
            $groupBy = $request->query('group_by', 'cabang');
            $validGroups = ['cabang', 'wilayah', 'ao'];
            if (!in_array($groupBy, $validGroups)) {
                return response()->json(['success' => false, 'message' => 'Invalid group_by'], 422);
            }

            $data = $this->repository->getRekapitulasi($groupBy);
            return response()->json(['success' => true, 'data' => $data]);
        } catch (\Throwable $e) {
            return response()->json(['success' => false, 'message' => 'Gagal memuat rekapitulasi tabungan'], 500);
        }
    }

    public function doormant(Request $request): JsonResponse
    {
        try {
            $filters = ['cabang' => $request->query('cabang')];
            $perPage = (int) $request->query('per_page', '50');
            $data = $this->repository->getDoormant($filters, $perPage);

            return response()->json(['success' => true, 'data' => $data]);
        } catch (\Throwable $e) {
            return response()->json(['success' => false, 'message' => 'Gagal memuat data doormant'], 500);
        }
    }
}
