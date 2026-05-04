<?php

declare(strict_types=1);

use Monolog\Handler\NullHandler;
use Monolog\Handler\StreamHandler;
use Monolog\Processor\PsrLogMessageProcessor;

return [

    /*
    |--------------------------------------------------------------------------
    | Default Log Channel
    |--------------------------------------------------------------------------
    |
    | Semua log akan tertulis ke single file: logs/laravel.log
    | Rotation: 5MB per file, keep 14 days
    |
    */

    'default' => env('LOG_CHANNEL', 'single'),

    /*
    |--------------------------------------------------------------------------
    | Deprecations Log Channel
    |--------------------------------------------------------------------------
    */

    'deprecations' => [
        'channel' => env('LOG_DEPRECATIONS_CHANNEL', 'null'),
        'trace' => env('LOG_DEPRECATIONS_TRACE', false),
    ],

    /*
    |--------------------------------------------------------------------------
    | Log Channels
    |--------------------------------------------------------------------------
    |
    | CENTRALIZED LOGGING:
    | - single: Semua log ke satu file (laravel.log)
    | - daily:  Rotasi harian (laravel-YYYY-MM-DD.log)
    | - metrics: Performa & metrics saja
    | - api: API request/response
    |
    */

    'channels' => [

        'stack' => [
            'driver' => 'stack',
            'channels' => ['single'],
            'ignore_exceptions' => false,
        ],

        // ==========================================
        // CENTRALIZED: Single log file (PRIMARY)
        // ==========================================
        'single' => [
            'driver' => 'single',
            'path' => storage_path('logs/laravel.log'),
            'level' => env('LOG_LEVEL', 'debug'),
            'replace_placeholders' => true,
        ],

        // ==========================================
        // DAILY ROTATION:一个新文件每天
        // ==========================================
        'daily' => [
            'driver' => 'daily',
            'path' => storage_path('logs/laravel.log'),
            'level' => env('LOG_LEVEL', 'debug'),
            'days' => 14,  // Keep 14 days
            'replace_placeholders' => true,
        ],

        // ==========================================
        // METRICS: Performa query & API response
        // ==========================================
        'metrics' => [
            'driver' => 'single',
            'path' => storage_path('logs/metrics.log'),
            'level' => 'debug',
            'replace_placeholders' => true,
        ],

        // ==========================================
        // API: API request/response tracking
        // ==========================================
        'api' => [
            'driver' => 'single',
            'path' => storage_path('logs/api.log'),
            'level' => 'debug',
            'replace_placeholders' => true,
        ],

        // ==========================================
        // SLOW QUERY: Database query yang >100ms
        // ==========================================
        'slow_query' => [
            'driver' => 'single',
            'path' => storage_path('logs/slow-query.log'),
            'level' => 'debug',
            'replace_placeholders' => true,
        ],

        // ==========================================
        // ERROR ONLY: Hanya error & critical
        // ==========================================
        'error' => [
            'driver' => 'single',
            'path' => storage_path('logs/error.log'),
            'level' => 'error',
            'replace_placeholders' => true,
        ],

        'slack' => [
            'driver' => 'slack',
            'url' => env('LOG_SLACK_WEBHOOK_URL'),
            'username' => env('LOG_SLACK_USERNAME', env('APP_NAME', 'MCI Dashboard')),
            'emoji' => env('LOG_SLACK_EMOJI', ':boom:'),
            'level' => env('LOG_LEVEL', 'critical'),
            'replace_placeholders' => true,
        ],

        'stderr' => [
            'driver' => 'monolog',
            'level' => env('LOG_LEVEL', 'debug'),
            'handler' => StreamHandler::class,
            'handler_with' => [
                'stream' => 'php://stderr',
            ],
            'processor' => [PsrLogMessageProcessor::class],
        ],

        'null' => [
            'driver' => 'monolog',
            'handler' => NullHandler::class,
        ],

        'emergency' => [
            'path' => storage_path('logs/laravel.log'),
        ],

    ],

];
