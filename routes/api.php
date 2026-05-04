<?php

declare(strict_types=1);

use App\Http\Controllers\Api\v1\CifController;
use App\Http\Controllers\Api\v1\DashboardMetricsController;
use App\Http\Controllers\Api\v1\DepositController;
use App\Http\Controllers\Api\v1\FinancingController;
use App\Http\Controllers\Api\v1\FinancingOverviewController;
use App\Http\Controllers\Api\v1\ReportingController;
use App\Http\Controllers\Api\v1\SavingController;
use App\Services\Mci\MciConnectionService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes - v1 (MCI Dashboard REST API)
|--------------------------------------------------------------------------
|
| Full REST API endpoints untuk dashboard metrics
| Rate Limit: 100 requests/minute
|
*/

// === API v1 PREFIX ===
Route::prefix('v1')->middleware(['throttle:100,1'])->group(function () {

    // ==========================================
    // CIF MODULE API
    // ==========================================
    Route::prefix('cif')->group(function () {
        Route::get('/', [CifController::class, 'index']);
        Route::get('/rekapitulasi', [CifController::class, 'rekapitulasi']);
        Route::get('/{nocif}', [CifController::class, 'detail']);
    });

    // ==========================================
    // FUNDING MODULE API (SAVING & DEPOSIT)
    // ==========================================
    Route::prefix('saving')->group(function () {
        Route::get('/nominative', [SavingController::class, 'nominative']);
        Route::get('/rekapitulasi', [SavingController::class, 'rekapitulasi']);
        Route::get('/doormant', [SavingController::class, 'doormant']);
    });

    Route::prefix('deposit')->group(function () {
        Route::get('/nominative', [DepositController::class, 'nominative']);
        Route::get('/rekapitulasi', [DepositController::class, 'rekapitulasi']);
        Route::get('/jatuh-tempo', [DepositController::class, 'jatuhTempo']);
    });

    // ==========================================
    // REPORTING MODULE API
    // ==========================================
    Route::prefix('reporting')->group(function () {
        Route::get('/{jenis}', [ReportingController::class, 'generate']);
    });

    // ==========================================
    // FINANCING MODULE API
    // ==========================================
    Route::prefix('financing')->group(function () {
        // Overview endpoints (G1: Dashboard Ringan)
        Route::get('/overview', [FinancingOverviewController::class, 'index']);
        Route::get('/overview/realtime', [FinancingOverviewController::class, 'realtime']);
        Route::get('/overview/trend', [FinancingOverviewController::class, 'trend']);
        Route::get('/overview/compare', [FinancingOverviewController::class, 'compare']);
        Route::get('/overview/periods', [FinancingOverviewController::class, 'periods']);

        // Existing endpoints
        Route::get('/nominative', [FinancingController::class, 'nominative']);
        Route::get('/rekapitulasi', [FinancingController::class, 'rekapitulasi']);
        Route::get('/jatuh-tempo', [FinancingController::class, 'jatuhTempo']);
        Route::get('/aos', [FinancingController::class, 'aos']);
        Route::get('/{nokontrak}/angsuran', [FinancingController::class, 'angsuran']);
    });

    // === DASHBOARD ENDPOINTS ===
    Route::prefix('dashboard')->group(function () {

        // Metrics — REALTIME (database aktif / bulan berjalan)
        Route::get('metrics', [DashboardMetricsController::class, 'index']);
        Route::get('metrics/financing', [DashboardMetricsController::class, 'financing']);
        Route::get('metrics/saving', [DashboardMetricsController::class, 'saving']);
        Route::get('metrics/deposito', [DashboardMetricsController::class, 'deposito']);

        // Chart data
        Route::get('chart/{type}', [DashboardMetricsController::class, 'chart']);

        // Branch list
        Route::get('branches', [DashboardMetricsController::class, 'branches']);

        // Cache management
        Route::post('clear-cache', [DashboardMetricsController::class, 'clearCache']);

        // Metrics HISTORY per database — query ke database bulan tertentu
        // GET /api/v1/dashboard/history/{db}/metrics
        // db: mar | feb | jan
        Route::get('history/{db}/metrics', [DashboardMetricsController::class, 'historyMetrics'])
            ->where('db', 'mar|feb|jan');

        Route::get('history/{db}/metrics/financing', [DashboardMetricsController::class, 'historyFinancing'])
            ->where('db', 'mar|feb|jan');

        Route::get('history/{db}/metrics/saving', [DashboardMetricsController::class, 'historySaving'])
            ->where('db', 'mar|feb|jan');

        Route::get('history/{db}/metrics/deposito', [DashboardMetricsController::class, 'historyDeposito'])
            ->where('db', 'mar|feb|jan');
    });

    // === DATABASE INFO & CONNECTION TEST ===
    Route::prefix('databases')->group(function () {

        // Info semua database yang dikonfigurasi
        Route::get('/', function () {
            $mci = app(MciConnectionService::class);

            return response()->json([
                'success' => true,
                'data' => [
                    'active' => $mci->getActiveDatabase(),
                    'databases' => [
                        'realtime' => [
                            'connection' => 'dashboard_data',
                            'database' => env('SQL_SERVER_DATABASE'),
                            'label' => 'Maret 2026 (REALTIME)',
                            'env_key' => 'SQL_SERVER_DATABASE',
                        ],
                        'history' => [
                            ['connection' => 'dashboard_mar', 'database' => env('MCI_DB_MAR'), 'label' => 'Maret 2026', 'env_key' => 'MCI_DB_MAR'],
                            ['connection' => 'dashboard_feb', 'database' => env('MCI_DB_FEB'), 'label' => 'Februari 2026', 'env_key' => 'MCI_DB_FEB'],
                            ['connection' => 'dashboard_jan', 'database' => env('MCI_DB_JAN'), 'label' => 'Januari 2026', 'env_key' => 'MCI_DB_JAN'],
                        ],
                    ],
                ],
                'meta' => ['generated_at' => now()->toIso8601String()],
            ]);
        });

        // Test semua koneksi sekaligus
        Route::get('test-all', function () {
            $connections = [
                'dashboard_data (realtime/mar)' => 'dashboard_data',
                'dashboard_feb (februari)' => 'dashboard_feb',
                'dashboard_jan (januari)' => 'dashboard_jan',
            ];

            $results = [];
            foreach ($connections as $label => $conn) {
                try {
                    $db = config("database.connections.{$conn}.database");
                    $pdo = DB::connection($conn)->getPdo();
                    $results[$label] = [
                        'status' => 'connected',
                        'database' => $db,
                        'driver' => $pdo->getAttribute(PDO::ATTR_DRIVER_NAME),
                    ];
                } catch (Throwable $e) {
                    $results[$label] = [
                        'status' => 'failed',
                        'error' => $e->getMessage(),
                    ];
                }
            }

            $allOk = collect($results)->every(fn ($r) => $r['status'] === 'connected');

            return response()->json([
                'success' => $allOk,
                'results' => $results,
                'meta' => ['tested_at' => now()->toIso8601String()],
            ], $allOk ? 200 : 207);
        });
    });

    // === HEALTH CHECK ===
    Route::get('health', function () {
        return response()->json([
            'status' => 'healthy',
            'service' => 'MCI Dashboard API',
            'version' => 'v1',
            'timestamp' => now()->toIso8601String(),
        ]);
    });

    // === API INFO ===
    Route::get('info', function () {
        return response()->json([
            'name' => 'MCI Dashboard REST API',
            'version' => 'v1',
            'description' => 'REST API for MCI Banking Dashboard',
            'endpoints' => [
                'GET  /api/v1/health',
                'GET  /api/v1/databases',
                'GET  /api/v1/databases/test-all',
                'GET  /api/v1/dashboard/metrics',
                'GET  /api/v1/dashboard/metrics/financing',
                'GET  /api/v1/dashboard/metrics/saving',
                'GET  /api/v1/dashboard/metrics/deposito',
                'GET  /api/v1/dashboard/chart/{type}',
                'GET  /api/v1/dashboard/branches',
                'GET  /api/v1/dashboard/history/{mar|feb|jan}/metrics',
                'GET  /api/v1/dashboard/history/{mar|feb|jan}/metrics/financing',
                'GET  /api/v1/dashboard/history/{mar|feb|jan}/metrics/saving',
                'GET  /api/v1/dashboard/history/{mar|feb|jan}/metrics/deposito',
                'POST /api/v1/dashboard/clear-cache',
            ],
            'rate_limit' => '100 requests/minute',
            'cache_ttl' => '60 seconds',
        ]);
    });
});

// === API v2 (FUTURE) ===
Route::prefix('v2')->middleware(['throttle:100,1'])->group(function () {
    Route::get('health', function () {
        return response()->json([
            'status' => 'healthy',
            'service' => 'MCI Dashboard API',
            'version' => 'v2',
            'timestamp' => now()->toIso8601String(),
        ]);
    });
});
