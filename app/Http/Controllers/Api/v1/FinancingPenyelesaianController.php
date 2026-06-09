<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Repositories\Interfaces\FinancingPenyelesaianRepositoryInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class FinancingPenyelesaianController extends Controller
{
    public function __construct(
        private readonly FinancingPenyelesaianRepositoryInterface $repository
    ) {}

    public function ppka(Request $request): JsonResponse
    {
        try {
            $filters = ['ao' => $request->query('ao')];
            
            $data = $this->repository->getPpka($filters);
            $summary = $this->repository->getPpkaSummary($filters);

            return response()->json([
                'success' => true,
                'data' => $data->values(),
                'summary' => $summary,
                'meta' => [
                    'total' => $data->count(),
                    'generated_at' => now()->toIso8601String(),
                ],
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function settlement(Request $request): JsonResponse
    {
        try {
            $filters = [
                'tahun' => $request->query('tahun'),
                'bulan' => $request->query('bulan'),
            ];

            $data = $this->repository->getSettlement($filters);
            $summary = $this->repository->getSettlementSummary($filters);

            return response()->json([
                'success' => true,
                'data' => $data->values(),
                'summary' => $summary,
                'period_meta' => $this->repository->getLastPeriodMeta(),
                'meta' => [
                    'total' => $data->count(),
                    'filters' => $filters,
                    'generated_at' => now()->toIso8601String(),
                ],
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function writeOff(Request $request): JsonResponse
    {
        try {
            $filters = [
                'tahun' => $request->query('tahun', date('Y')),
                'bulan' => $request->query('bulan'),
                'ao' => $request->query('ao'),
                'cabang' => $request->query('cabang'),
                'recovery_min' => $request->query('recovery_min'),
                'recovery_max' => $request->query('recovery_max'),
                'search' => $request->query('search'),
                'sort_by' => $request->query('sort_by', 'nama'),
                'sort_order' => $request->query('sort_order', 'asc'),
            ];
            
            $data = $this->repository->getWriteOff($filters);
            $summary = $this->repository->getWriteOffSummary($filters);

            return response()->json([
                'success' => true,
                'data' => $data->values(),
                'summary' => $summary,
                'period_meta' => $this->repository->getLastPeriodMeta(),
                'meta' => [
                    'total' => $data->count(),
                    'filters' => $filters,
                    'generated_at' => now()->toIso8601String(),
                ],
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function yield(Request $request): JsonResponse
    {
        try {
            $filters = [
                'tahun' => $request->query('tahun', date('Y')),
                'dimensi' => $request->query('dimensi', 'ao'),
                'bulan_start' => $request->query('bulan_start'),
                'bulan_end' => $request->query('bulan_end'),
                'min_yield' => $request->query('min_yield'),
                'max_yield' => $request->query('max_yield'),
                'active_only' => $request->query('active_only', true),
            ];
            
            $data = $this->repository->getYield($filters);
            $summary = $this->repository->getYieldSummary($filters);

            return response()->json([
                'success' => true,
                'data' => $data->values(),
                'summary' => $summary,
                'period_meta' => $this->repository->getLastPeriodMeta(),
                'meta' => [
                    'total' => $data->count(),
                    'filters' => $filters,
                    'generated_at' => now()->toIso8601String(),
                ],
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function savePpkaAdjustment(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'nokontrak' => 'required|string',
                'nominal_ppap' => 'required|numeric',
                'alasan' => 'required|string',
            ]);

            \App\Models\ManualPpapAdjustment::updateOrCreate(
                ['nokontrak' => $validated['nokontrak']],
                [
                    'nominal_ppap' => $validated['nominal_ppap'],
                    'alasan' => $validated['alasan'],
                    'created_by' => 'User',
                ]
            );

            \Illuminate\Support\Facades\Cache::flush();

            return response()->json([
                'success' => true,
                'message' => 'Penyesuaian PPKA manual berhasil disimpan.',
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
