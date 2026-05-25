<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Repositories\Interfaces\CifAuditRepositoryInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CifAuditController extends Controller
{
    public function __construct(
        private readonly CifAuditRepositoryInterface $repository
    ) {}

    public function summary(Request $request): JsonResponse
    {
        try {
            $data = $this->repository->getAuditSummary();
            return response()->json([
                'success' => true,
                'data' => $data,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data summary',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function pembiayaan(Request $request): JsonResponse
    {
        try {
            $filters = $request->only(['cabang', 'status', 'search', 'golcust']);
            $perPage = (int) $request->input('perPage', 50);
            
            $data = $this->repository->getPembiayaanAudit($filters, $perPage);
            
            return response()->json([
                'success' => true,
                'data' => $data->items(),
                'meta' => [
                    'next_cursor' => $data->nextCursor()?->encode(),
                    'prev_cursor' => $data->previousCursor()?->encode(),
                    'has_pages' => $data->hasPages(),
                    'per_page' => $data->perPage(),
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data pengecekan pembiayaan',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function tabungan(Request $request): JsonResponse
    {
        try {
            $filters = $request->only(['cabang', 'status', 'search', 'golcust']);
            $perPage = (int) $request->input('perPage', 50);
            
            $data = $this->repository->getTabunganAudit($filters, $perPage);
            
            return response()->json([
                'success' => true,
                'data' => $data->items(),
                'meta' => [
                    'next_cursor' => $data->nextCursor()?->encode(),
                    'prev_cursor' => $data->previousCursor()?->encode(),
                    'has_pages' => $data->hasPages(),
                    'per_page' => $data->perPage(),
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data pengecekan tabungan',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function deposito(Request $request): JsonResponse
    {
        try {
            $filters = $request->only(['cabang', 'status', 'search', 'golcust']);
            $perPage = (int) $request->input('perPage', 50);
            
            $data = $this->repository->getDepositoAudit($filters, $perPage);
            
            return response()->json([
                'success' => true,
                'data' => $data->items(),
                'meta' => [
                    'next_cursor' => $data->nextCursor()?->encode(),
                    'prev_cursor' => $data->previousCursor()?->encode(),
                    'has_pages' => $data->hasPages(),
                    'per_page' => $data->perPage(),
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data pengecekan deposito',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
