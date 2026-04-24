<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\v1\DashboardMetricsController;

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

    // === DASHBOARD ENDPOINTS ===
    Route::prefix('dashboard')->group(function () {
        
        // Metrics
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
                'GET /api/v1/dashboard/metrics',
                'GET /api/v1/dashboard/metrics/financing',
                'GET /api/v1/dashboard/metrics/saving',
                'GET /api/v1/dashboard/metrics/deposito',
                'GET /api/v1/dashboard/chart/{type}',
                'GET /api/v1/dashboard/branches',
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