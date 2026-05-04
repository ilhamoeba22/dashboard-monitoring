<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Repositories\Interfaces\CifRepositoryInterface;
use Illuminate\Database\Eloquent\ModelNotFoundException;
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
                'ao' => $request->query('ao'),
                'has_tabungan' => filter_var($request->query('has_tabungan', false), FILTER_VALIDATE_BOOLEAN),
                'has_deposito' => filter_var($request->query('has_deposito', false), FILTER_VALIDATE_BOOLEAN),
                'has_pembiayaan' => filter_var($request->query('has_pembiayaan', false), FILTER_VALIDATE_BOOLEAN),
            ];

            // Support per_page=-1 untuk muat semua data (tanpa limit 500)
            $perPage = (int) $request->query('per_page', '-1');
            if ($perPage === -1) {
                $perPage = 100000; // High limit untuk "show all"
            }
            // #region agent log
            $this->debugLog('H5', 'app/Http/Controllers/Api/v1/CifController.php:45', 'CIF controller received per_page', [
                'per_page' => $perPage,
                'cursor' => $request->query('cursor'),
                'search' => $request->query('search'),
            ]);
            // #endregion
            $data = $this->repository->getList($filters, $perPage);

            return response()->json([
                'success' => true,
                'data' => $data,
                'meta' => [
                    'total' => $data->total(),
                    'per_page' => $data->perPage(),
                    'current' => $data->currentPage(),
                    'count' => count($data->items()),
                    'has_more' => $data->hasMorePages(),
                    'next_cursor' => method_exists($data, 'nextCursor') ? ($data->nextCursor()?->encode() ?? null) : null,
                    'prev_cursor' => method_exists($data, 'previousCursor') ? ($data->previousCursor()?->encode() ?? null) : null,
                ],
            ]);
        } catch (\Throwable $e) {
            // #region agent log
            $this->debugLog('H10', 'app/Http/Controllers/Api/v1/CifController.php:70', 'CIF endpoint exception', [
                'exception_class' => $e::class,
                'exception_message' => $e->getMessage(),
            ]);

            // #endregion
            return response()->json([
                'success' => false,
                'message' => 'Gagal memuat daftar nasabah',
                'error' => config('app.debug') ? $e->getMessage() : null,
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
                'message' => 'Gagal memuat rekapitulasi nasabah',
                'debug' => $e->getMessage(),
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
                'data' => $data,
            ]);
        } catch (ModelNotFoundException $e) {
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
