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
            $data = $this->repository->getList($filters, $perPage);

            return response()->json([
                'success' => true,
                'data' => $data,
                'meta' => [
                    'per_page' => $data->perPage(),
                    'count' => count($data->items()),
                    'has_more' => $data->hasMorePages(),
                    'next_cursor' => method_exists($data, 'nextCursor') ? ($data->nextCursor()?->encode() ?? null) : null,
                    'prev_cursor' => method_exists($data, 'previousCursor') ? ($data->previousCursor()?->encode() ?? null) : null,
                    'generated_at' => now()->toIso8601String(),
                ],
            ]);
        } catch (\Throwable $e) {
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
                'error' => config('app.debug') ? $e->getMessage() : null,
            ], 500);
        }
    }

}
