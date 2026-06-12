<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Api\v1\Concerns\UsesMciSnapshotDatabase;
use App\Http\Controllers\Controller;
use App\Repositories\Interfaces\DepositRepositoryInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DepositController extends Controller
{
    use UsesMciSnapshotDatabase;

    private DepositRepositoryInterface $repository;

    public function __construct(DepositRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function summary(Request $request): JsonResponse
    {
        try {
            return $this->withRequestedMciDatabase($request, fn () => response()->json(['success' => true, 'data' => $this->repository->getSummary()]));
        } catch (\Throwable $e) {
            return response()->json(['success' => false, 'message' => 'Gagal memuat ringkasan deposito'], 500);
        }
    }

    public function filterOptions(Request $request): JsonResponse
    {
        try {
            return $this->withRequestedMciDatabase($request, fn () => response()->json(['success' => true, 'data' => $this->repository->getFilterOptions()]));
        } catch (\Throwable $e) {
            return response()->json(['success' => false, 'message' => 'Gagal memuat filter deposito'], 500);
        }
    }

    public function nominative(Request $request): JsonResponse
    {
        try {
            $filters = [
                'search' => $request->query('search'),
                'cabang' => $request->query('cabang'),
                'ao' => $request->query('ao'),
            ];

            $perPage = (int) $request->query('per_page', '50');
            $data = $this->withRequestedMciDatabase($request, fn () => $this->repository->getNominative($filters, $perPage));

            return response()->json(['success' => true, 'data' => $data]);
        } catch (\Throwable $e) {
            return response()->json(['success' => false, 'message' => 'Gagal memuat data nominatif deposito'], 500);
        }
    }

    public function rekapitulasi(Request $request): JsonResponse
    {
        try {
            $groupBy = $request->query('group_by', 'cabang');
            $validGroups = ['cabang', 'wilayah', 'ao', 'produk'];
            if (! in_array($groupBy, $validGroups)) {
                return response()->json(['success' => false, 'message' => 'Invalid group_by'], 422);
            }

            $filters = [
                'search' => $request->query('search'),
                'cabang' => $request->query('cabang'),
                'ao' => $request->query('ao'),
            ];

            $data = $this->withRequestedMciDatabase($request, fn () => $this->repository->getRekapitulasi($groupBy, $filters));

            return response()->json(['success' => true, 'data' => $data]);
        } catch (\Throwable $e) {
            return response()->json(['success' => false, 'message' => 'Gagal memuat rekapitulasi deposito'], 500);
        }
    }

    public function jatuhTempo(Request $request): JsonResponse
    {
        try {
            $filters = [
                'search' => $request->query('search'),
                'cabang' => $request->query('cabang'),
                'ao' => $request->query('ao'),
            ];
            $perPage = (int) $request->query('per_page', '50');
            $data = $this->withRequestedMciDatabase($request, fn () => $this->repository->getJatuhTempo($filters, $perPage));

            return response()->json(['success' => true, 'data' => $data]);
        } catch (\Throwable $e) {
            return response()->json(['success' => false, 'message' => 'Gagal memuat daftar jatuh tempo'], 500);
        }
    }
}
