<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Repositories\Interfaces\FinancingRepositoryInterface;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class FinancingController extends Controller
{
    private FinancingRepositoryInterface $repository;

    public function __construct(FinancingRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    /**
     * GET /api/v1/financing/nominative
     */
    public function nominative(Request $request): JsonResponse
    {
        try {
            $filters = [
                'search' => $request->query('search'),
                'cabang' => $request->query('cabang'),
                'ao' => $request->query('ao'),
            ];

            $perPage = (int) $request->query('per_page', '50');
            // #region agent log
            $this->debugLog('H5', 'app/Http/Controllers/Api/v1/FinancingController.php:36', 'Financing controller received per_page', [
                'per_page' => $perPage,
                'cursor' => $request->query('cursor'),
                'search' => $request->query('search'),
            ]);
            // #endregion

            $data = $this->repository->getNominative($filters, $perPage);

            return response()->json([
                'success' => true,
                'data' => $data, // Berisi links & meta bawaan CursorPaginator
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memuat data nominatif pembiayaan',
            ], 500);
        }
    }

    /**
     * GET /api/v1/financing/aos
     */
    public function aos(): JsonResponse
    {
        try {
            $data = $this->repository->getUniqueAos();

            return response()->json([
                'success' => true,
                'data' => $data,
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memuat data AO',
            ], 500);
        }
    }

    /**
     * GET /api/v1/financing/{nokontrak}/angsuran
     */
    public function angsuran(string $nokontrak): JsonResponse
    {
        try {
            $data = $this->repository->getDetailAngsuran($nokontrak);

            return response()->json([
                'success' => true,
                'data' => $data,
            ]);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Data kontrak tidak ditemukan',
            ], 404);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memuat detail angsuran',
            ], 500);
        }
    }

    /**
     * GET /api/v1/financing/rekapitulasi
     * Query Params: ?group_by=cabang (cabang|wilayah|ao|produk|segmen|kolektibilitas)
     */
    public function rekapitulasi(Request $request): JsonResponse
    {
        try {
            $groupBy = $request->query('group_by', 'cabang');

            $validGroups = ['cabang', 'wilayah', 'ao', 'produk', 'segmen', 'kolektibilitas'];
            if (! in_array($groupBy, $validGroups)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Parameter group_by tidak valid. Gunakan: '.implode(', ', $validGroups),
                ], 422);
            }

            $data = $this->repository->getRekapitulasi($groupBy);

            return response()->json([
                'success' => true,
                'data' => $data,
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memuat rekapitulasi pembiayaan',
                'debug' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * GET /api/v1/financing/jatuh-tempo
     */
    public function jatuhTempo(Request $request): JsonResponse
    {
        try {
            $filters = [
                'cabang' => $request->query('cabang'),
                'ao' => $request->query('ao'),
            ];

            $perPage = (int) $request->query('per_page', '50');
            // #region agent log
            $this->debugLog('H5', 'app/Http/Controllers/Api/v1/FinancingController.php:145', 'Financing jatuh-tempo controller received per_page', [
                'per_page' => $perPage,
                'cursor' => $request->query('cursor'),
            ]);
            // #endregion
            $data = $this->repository->getJatuhTempo($filters, $perPage);

            return response()->json([
                'success' => true,
                'data' => $data,
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memuat daftar jatuh tempo pembiayaan',
            ], 500);
        }
    }

    // #region agent log
    private function debugLog(string $hypothesisId, string $location, string $message, array $data = []): void
    {
        $payload = [
            'sessionId' => 'f35f8f',
            'runId' => 'pre-fix',
            'hypothesisId' => $hypothesisId,
            'location' => $location,
            'message' => $message,
            'data' => $data,
            'timestamp' => (int) round(microtime(true) * 1000),
        ];

        @file_put_contents(base_path('debug-f35f8f.log'), json_encode($payload, JSON_UNESCAPED_SLASHES).PHP_EOL, FILE_APPEND);
    }
    // #endregion
}
