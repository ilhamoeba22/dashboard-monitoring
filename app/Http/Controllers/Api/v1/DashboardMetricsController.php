<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Services\Mci\DashboardRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

/**
 * DashboardMetricsController
 * --------------------------------------------------------------------------
 * Controller tipis (thin controller) — hanya routing + error handling.
 * Semua business logic ada di DashboardRepository (RULE #11).
 *
 * Endpoints:
 *   GET  /api/v1/dashboard/metrics
 *   GET  /api/v1/dashboard/metrics/financing
 *   GET  /api/v1/dashboard/metrics/saving
 *   GET  /api/v1/dashboard/metrics/deposito
 *   GET  /api/v1/dashboard/chart/{type}
 *   GET  /api/v1/dashboard/branches
 *   POST /api/v1/dashboard/clear-cache
 */
class DashboardMetricsController extends Controller
{
    public function __construct(
        private readonly DashboardRepository $repository,
    ) {}

    /**
     * GET /api/v1/dashboard/metrics
     * Semua key metrics sekaligus (Financing + Saving + Deposito).
     */
    public function index(Request $request): JsonResponse
    {
        try {
            // #region agent log
            $this->debugLog('H8', 'app/Http/Controllers/Api/v1/DashboardMetricsController.php:43', 'Dashboard metrics endpoint hit', [
                'path' => $request->path(),
                'query' => $request->query(),
            ]);
            // #endregion
            $dto = $this->repository->getKeyMetrics();

            return $this->success($dto->jsonSerialize(), [
                'generated_at' => $dto->generatedAt,
                'cache_ttl' => 60,
                'version' => 'v1',
            ]);
        } catch (\Throwable $e) {
            return $this->error('Failed to fetch dashboard metrics', $e);
        }
    }

    /**
     * GET /api/v1/dashboard/metrics/financing
     */
    public function financing(Request $request): JsonResponse
    {
        try {
            $period = $this->repository->getCurrentPeriod();
            $dto = $this->repository->getFinancingMetrics(
                (string) $period['year'],
                (string) $period['previous_year'],
            );

            return $this->success($dto->jsonSerialize(), [
                'periode' => $period['period'],
                'cache_ttl' => 60,
                'version' => 'v1',
            ]);
        } catch (\Throwable $e) {
            return $this->error('Failed to fetch financing metrics', $e);
        }
    }

    /**
     * GET /api/v1/dashboard/metrics/saving
     */
    public function saving(Request $request): JsonResponse
    {
        try {
            $period = $this->repository->getCurrentPeriod();
            $dto = $this->repository->getSavingMetrics(
                (string) $period['year'],
                (string) $period['previous_year'],
            );

            return $this->success($dto->jsonSerialize(), [
                'periode' => $period['period'],
                'cache_ttl' => 60,
                'version' => 'v1',
            ]);
        } catch (\Throwable $e) {
            return $this->error('Failed to fetch saving metrics', $e);
        }
    }

    /**
     * GET /api/v1/dashboard/metrics/deposito
     */
    public function deposito(Request $request): JsonResponse
    {
        try {
            $period = $this->repository->getCurrentPeriod();
            $dto = $this->repository->getDepositoMetrics(
                (string) $period['year'],
                (string) $period['previous_year'],
            );

            return $this->success($dto->jsonSerialize(), [
                'periode' => $period['period'],
                'cache_ttl' => 60,
                'version' => 'v1',
            ]);
        } catch (\Throwable $e) {
            return $this->error('Failed to fetch deposito metrics', $e);
        }
    }

    /**
     * GET /api/v1/dashboard/chart/{type}
     * type: financing | saving | deposito
     * Query Params: ?start_date=2026-02-01&end_date=2026-03-15
     */
    public function chart(Request $request, string $type): JsonResponse
    {
        $validTypes = ['financing', 'saving', 'deposito'];

        if (! in_array($type, $validTypes, true)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid chart type. Valid values: '.implode(', ', $validTypes),
            ], 422);
        }

        try {
            $startDate = $request->query('start_date');
            $endDate = $request->query('end_date');

            // Data chart kini ditarik secara kilat dari MySQL Data Warehouse
            $chartData = $this->repository->getChartDataFromWarehouse($type, $startDate, $endDate);

            return $this->success($chartData, [
                'type' => $type,
                'filter' => [
                    'start_date' => $startDate ?? 'last_30_days',
                    'end_date' => $endDate ?? 'today',
                ],
                'cache_ttl' => 0, // Disable cache karena filter dinamis
                'version' => 'v1',
                'generated_at' => now()->toIso8601String(),
            ]);
        } catch (\Throwable $e) {
            return $this->error('Failed to fetch chart data', $e);
        }
    }

    /**
     * GET /api/v1/dashboard/branches
     */
    public function branches(Request $request): JsonResponse
    {
        try {
            $branches = $this->repository->getBranchList();

            return $this->success($branches, [
                'total' => count($branches),
                'cache_ttl' => 3600,
                'version' => 'v1',
                'generated_at' => now()->toIso8601String(),
            ]);
        } catch (\Throwable $e) {
            return $this->error('Failed to fetch branch list', $e);
        }
    }

    /**
     * POST /api/v1/dashboard/clear-cache
     * TODO: Tambahkan middleware admin auth setelah auth system selesai.
     */
    public function clearCache(Request $request): JsonResponse
    {
        try {
            $this->repository->clearCache();

            return response()->json([
                'success' => true,
                'message' => 'Dashboard cache cleared successfully.',
                'meta' => [
                    'cleared_at' => now()->toIso8601String(),
                    'version' => 'v1',
                ],
            ]);
        } catch (\Throwable $e) {
            return $this->error('Failed to clear cache', $e);
        }
    }

    // =========================================================================
    // HISTORY ENDPOINTS — query ke database bulan tertentu
    // =========================================================================

    /**
     * GET /api/v1/dashboard/history/{db}/metrics
     * db: mar | feb | jan
     */
    public function historyMetrics(Request $request, string $db): JsonResponse
    {
        try {
            $connection = $this->resolveHistoryConnection($db);
            $this->repository->setConnection($connection);
            $dto = $this->repository->getKeyMetrics();

            return $this->success($dto->jsonSerialize(), [
                'database' => config("database.connections.{$connection}.database"),
                'connection' => $connection,
                'generated_at' => $dto->generatedAt,
                'cache_ttl' => 60,
                'version' => 'v1',
            ]);
        } catch (\Throwable $e) {
            return $this->error("Failed to fetch history metrics [{$db}]", $e);
        } finally {
            $this->repository->resetConnection(); // Kembalikan ke realtime
        }
    }

    /**
     * GET /api/v1/dashboard/history/{db}/metrics/financing
     */
    public function historyFinancing(Request $request, string $db): JsonResponse
    {
        try {
            $connection = $this->resolveHistoryConnection($db);
            $this->repository->setConnection($connection);
            $period = $this->repository->getCurrentPeriod();
            $dto = $this->repository->getFinancingMetrics(
                (string) $period['year'],
                (string) $period['previous_year'],
            );

            return $this->success($dto->jsonSerialize(), [
                'database' => config("database.connections.{$connection}.database"),
                'connection' => $connection,
                'periode' => $period['period'],
                'cache_ttl' => 60,
                'version' => 'v1',
            ]);
        } catch (\Throwable $e) {
            return $this->error("Failed to fetch history financing [{$db}]", $e);
        } finally {
            $this->repository->resetConnection();
        }
    }

    /**
     * GET /api/v1/dashboard/history/{db}/metrics/saving
     */
    public function historySaving(Request $request, string $db): JsonResponse
    {
        try {
            $connection = $this->resolveHistoryConnection($db);
            $this->repository->setConnection($connection);
            $period = $this->repository->getCurrentPeriod();
            $dto = $this->repository->getSavingMetrics(
                (string) $period['year'],
                (string) $period['previous_year'],
            );

            return $this->success($dto->jsonSerialize(), [
                'database' => config("database.connections.{$connection}.database"),
                'connection' => $connection,
                'periode' => $period['period'],
                'cache_ttl' => 60,
                'version' => 'v1',
            ]);
        } catch (\Throwable $e) {
            return $this->error("Failed to fetch history saving [{$db}]", $e);
        } finally {
            $this->repository->resetConnection();
        }
    }

    /**
     * GET /api/v1/dashboard/history/{db}/metrics/deposito
     */
    public function historyDeposito(Request $request, string $db): JsonResponse
    {
        try {
            $connection = $this->resolveHistoryConnection($db);
            $this->repository->setConnection($connection);
            $period = $this->repository->getCurrentPeriod();
            $dto = $this->repository->getDepositoMetrics(
                (string) $period['year'],
                (string) $period['previous_year'],
            );

            return $this->success($dto->jsonSerialize(), [
                'database' => config("database.connections.{$connection}.database"),
                'connection' => $connection,
                'periode' => $period['period'],
                'cache_ttl' => 60,
                'version' => 'v1',
            ]);
        } catch (\Throwable $e) {
            return $this->error("Failed to fetch history deposito [{$db}]", $e);
        } finally {
            $this->repository->resetConnection();
        }
    }

    // =========================================================================
    // PRIVATE HELPERS
    // =========================================================================

    /**
     * Resolve connection name dari slug db parameter.
     * mar → dashboard_mar, feb → dashboard_feb, jan → dashboard_jan
     */
    private function resolveHistoryConnection(string $db): string
    {
        return match ($db) {
            'mar' => 'dashboard_mar',
            'feb' => 'dashboard_feb',
            'jan' => 'dashboard_jan',
            default => 'dashboard_data',
        };
    }

    /**
     * Buat response sukses yang konsisten.
     *
     * @param  array<string, mixed>  $meta
     */
    private function success(mixed $data, array $meta = []): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => $data,
            'meta' => $meta,
        ]);
    }

    /**
     * Buat response error yang konsisten + log otomatis.
     */
    private function error(string $message, \Throwable $e): JsonResponse
    {
        Log::error($message, [
            'exception' => $e->getMessage(),
            'file' => $e->getFile(),
            'line' => $e->getLine(),
        ]);

        return response()->json([
            'success' => false,
            'message' => $message,
            'error' => config('app.debug') ? $e->getMessage() : 'Internal server error.',
        ], 500);
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
