<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\v1\DashboardMetricsResource;
use App\Services\Mci\DashboardRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class DashboardMetricsController extends Controller
{
    public function __construct(
        protected DashboardRepository $repository
    ) {}

    /**
     * GET /api/v1/dashboard/metrics
     * 
     * Ambil semua key metrics dashboard (Financing + Saving + Deposito)
     * Cached 60 detik untuk real-time updates
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $metrics = $this->repository->getKeyMetrics();
            $period = $this->repository->getCurrentPeriod();

            return response()->json([
                'success' => true,
                'data' => [
                    'periode' => [
                        'tgl' => $period['tgl'],
                        'year' => $period['year'],
                        'month' => $period['month'],
                        'period' => $period['period'],
                    ],
                    'financing' => $this->formatFinancingMetrics($metrics['financing'] ?? []),
                    'saving' => $this->formatSavingMetrics($metrics['saving'] ?? []),
                    'deposito' => $this->formatDepositoMetrics($metrics['deposito'] ?? []),
                ],
                'meta' => [
                    'generated_at' => now()->toIso8601String(),
                    'cache_ttl' => 60,
                    'version' => 'v1',
                ],
            ]);
        } catch (\Throwable $e) {
            Log::error('Dashboard Metrics API Error: ' . $e->getMessage(), [
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch dashboard metrics',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * GET /api/v1/dashboard/metrics/financing
     * 
     * Financing key metrics only
     */
    public function financing(Request $request): JsonResponse
    {
        try {
            $period = $this->repository->getCurrentPeriod();
            $metrics = $this->repository->getFinancingMetrics(
                (string) $period['year'],
                (string) $period['previous_year']
            );

            return response()->json([
                'success' => true,
                'data' => $this->formatFinancingMetrics($metrics),
                'meta' => [
                    'generated_at' => now()->toIso8601String(),
                    'cache_ttl' => 60,
                    'version' => 'v1',
                ],
            ]);
        } catch (\Throwable $e) {
            Log::error('Financing Metrics API Error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch financing metrics',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * GET /api/v1/dashboard/metrics/saving
     * 
     * Saving key metrics only
     */
    public function saving(Request $request): JsonResponse
    {
        try {
            $period = $this->repository->getCurrentPeriod();
            $metrics = $this->repository->getSavingMetrics(
                (string) $period['year'],
                (string) $period['previous_year']
            );

            return response()->json([
                'success' => true,
                'data' => $this->formatSavingMetrics($metrics),
                'meta' => [
                    'generated_at' => now()->toIso8601String(),
                    'cache_ttl' => 60,
                    'version' => 'v1',
                ],
            ]);
        } catch (\Throwable $e) {
            Log::error('Saving Metrics API Error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch saving metrics',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * GET /api/v1/dashboard/metrics/deposito
     * 
     * Deposito key metrics only
     */
    public function deposito(Request $request): JsonResponse
    {
        try {
            $period = $this->repository->getCurrentPeriod();
            $metrics = $this->repository->getDepositoMetrics(
                (string) $period['year'],
                (string) $period['previous_year']
            );

            return response()->json([
                'success' => true,
                'data' => $this->formatDepositoMetrics($metrics),
                'meta' => [
                    'generated_at' => now()->toIso8601String(),
                    'cache_ttl' => 60,
                    'version' => 'v1',
                ],
            ]);
        } catch (\Throwable $e) {
            Log::error('Deposito Metrics API Error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch deposito metrics',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * GET /api/v1/dashboard/chart/{type}
     * 
     * Chart data: financing | saving | deposito
     */
    public function chart(Request $request, string $type): JsonResponse
    {
        $validTypes = ['financing', 'saving', 'deposito'];

        if (! in_array($type, $validTypes)) {
            return response()->json([
                'success' => false,
                'message' => "Invalid chart type. Valid: " . implode(', ', $validTypes),
            ], 400);
        }

        try {
            $chartData = $this->repository->getChartData($type);

            return response()->json([
                'success' => true,
                'data' => $chartData,
                'meta' => [
                    'type' => $type,
                    'generated_at' => now()->toIso8601String(),
                    'cache_ttl' => 300,
                    'version' => 'v1',
                ],
            ]);
        } catch (\Throwable $e) {
            Log::error("Chart API Error [{$type}]: " . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch chart data',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * GET /api/v1/dashboard/branches
     * 
     * Daftar cabang untuk filter
     */
    public function branches(Request $request): JsonResponse
    {
        try {
            $branches = $this->repository->getBranchList();

            return response()->json([
                'success' => true,
                'data' => $branches,
                'meta' => [
                    'total' => count($branches),
                    'generated_at' => now()->toIso8601String(),
                    'cache_ttl' => 3600,
                    'version' => 'v1',
                ],
            ]);
        } catch (\Throwable $e) {
            Log::error('Branches API Error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch branches',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * POST /api/v1/dashboard/clear-cache
     * 
     * Clear dashboard cache (admin only)
     */
    public function clearCache(Request $request): JsonResponse
    {
        // TODO: Add admin auth check
        
        try {
            $this->repository->clearCache();

            return response()->json([
                'success' => true,
                'message' => 'Dashboard cache cleared successfully',
                'meta' => [
                    'generated_at' => now()->toIso8601String(),
                    'version' => 'v1',
                ],
            ]);
        } catch (\Throwable $e) {
            Log::error('Clear Cache API Error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to clear cache',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Format Financing Metrics
     */
    protected function formatFinancingMetrics(array $data): array
    {
        return [
            'total_os' => $data['total'] ?? 0,
            'os_formatted' => $this->formatRupiah($data['total'] ?? 0),
            'total_npf' => $data['secondary'] ?? 0,
            'npf_formatted' => $this->formatRupiah($data['secondary'] ?? 0),
            'total_noa' => $data['noa'] ?? 0,
            'total_ao' => $data['ao'] ?? 0,
            'growth' => $data['growth'] ?? ['value' => '0%', 'class' => 'text-muted', 'raw' => 0],
            'noa_growth' => $data['noa_growth'] ?? ['value' => '0%', 'class' => 'text-muted', 'raw' => 0],
            'ao_growth' => $data['ao_growth'] ?? ['value' => '0%', 'class' => 'text-muted', 'raw' => 0],
            'npf_growth' => $data['secondary_growth'] ?? ['value' => '0%', 'class' => 'text-muted', 'raw' => 0],
        ];
    }

    /**
     * Format Saving Metrics
     */
    protected function formatSavingMetrics(array $data): array
    {
        return [
            'total_saldo' => $data['total'] ?? 0,
            'saldo_formatted' => $this->formatRupiah($data['total'] ?? 0),
            'total_noa' => $data['noa'] ?? 0,
            'total_ao' => $data['ao'] ?? 0,
            'growth' => $data['growth'] ?? ['value' => '0%', 'class' => 'text-muted', 'raw' => 0],
            'noa_growth' => $data['noa_growth'] ?? ['value' => '0%', 'class' => 'text-muted', 'raw' => 0],
            'ao_growth' => $data['ao_growth'] ?? ['value' => '0%', 'class' => 'text-muted', 'raw' => 0],
        ];
    }

    /**
     * Format Deposito Metrics
     */
    protected function formatDepositoMetrics(array $data): array
    {
        return [
            'total_saldo' => $data['total'] ?? 0,
            'saldo_formatted' => $this->formatRupiah($data['total'] ?? 0),
            'total_baghas' => $data['secondary'] ?? 0,
            'baghas_formatted' => $this->formatRupiah($data['secondary'] ?? 0),
            'total_noa' => $data['noa'] ?? 0,
            'total_ao' => $data['ao'] ?? 0,
            'growth' => $data['growth'] ?? ['value' => '0%', 'class' => 'text-muted', 'raw' => 0],
            'noa_growth' => $data['noa_growth'] ?? ['value' => '0%', 'class' => 'text-muted', 'raw' => 0],
            'ao_growth' => $data['ao_growth'] ?? ['value' => '0%', 'class' => 'text-muted', 'raw' => 0],
            'baghas_growth' => $data['secondary_growth'] ?? ['value' => '0%', 'class' => 'text-muted', 'raw' => 0],
        ];
    }

    /**
     * Helper format Rupiah
     */
    protected function formatRupiah(float $value): string
    {
        return 'Rp ' . number_format($value, 0, ',', '.');
    }
}