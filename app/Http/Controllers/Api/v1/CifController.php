<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Repositories\Interfaces\CifRepositoryInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CifController extends Controller
{
    private CifRepositoryInterface $repository;

    public function __construct(CifRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    /**
     * GET /api/v1/cif
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $filters = [
                'search' => $request->query('search'),
                'cabang' => $request->query('cabang'),
                'ao'     => $request->query('ao'),
                'has_tabungan' => filter_var($request->query('has_tabungan', false), FILTER_VALIDATE_BOOLEAN),
                'has_deposito' => filter_var($request->query('has_deposito', false), FILTER_VALIDATE_BOOLEAN),
                'has_pembiayaan' => filter_var($request->query('has_pembiayaan', false), FILTER_VALIDATE_BOOLEAN),
            ];

            $perPage = (int) $request->query('per_page', '50');
            $data = $this->repository->getList($filters, $perPage);

            return response()->json([
                'success' => true,
                'data'    => $data,
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memuat daftar nasabah',
            ], 500);
        }
    }

    /**
     * GET /api/v1/cif/rekapitulasi
     */
    public function rekapitulasi(Request $request): JsonResponse
    {
        try {
            $groupBy = $request->query('group_by', 'cabang');
            
            $validGroups = ['cabang', 'wilayah', 'ao', 'segmen', 'agama'];
            if (!in_array($groupBy, $validGroups)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Parameter group_by tidak valid. Gunakan: ' . implode(', ', $validGroups)
                ], 422);
            }

            $data = $this->repository->getRekapitulasi($groupBy);

            return response()->json([
                'success' => true,
                'data'    => $data,
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memuat rekapitulasi nasabah',
                'debug'   => $e->getMessage()
            ], 500);
        }
    }

    /**
     * GET /api/v1/cif/{nocif}
     */
    public function detail(string $nocif): JsonResponse
    {
        try {
            $data = $this->repository->getDetail($nocif);

            return response()->json([
                'success' => true,
                'data'    => $data,
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Data CIF tidak ditemukan',
            ], 404);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memuat detail nasabah',
            ], 500);
        }
    }
}
