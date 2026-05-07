<?php

declare(strict_types=1);

use App\Http\Middleware\HandleInertiaRequests;
use App\Http\Middleware\SelectMciDatabase;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->web(append: [
            HandleInertiaRequests::class,
            \App\Http\Middleware\MciDatabaseMiddleware::class,
        ]);

        $middleware->api(append: [
            \App\Http\Middleware\MciDatabaseMiddleware::class,
        ]);

        // Register MCI Database Middleware
        $middleware->alias([
            'mci.database' => SelectMciDatabase::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->reportable(function (\Throwable $e) {
            \Log::error('Global Exception: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
        });

        // Force JSON responses for all API routes — prevents 302 redirect
        // that causes MethodNotAllowedHttpException on POST-only endpoints
        $exceptions->shouldRenderJsonWhen(function (\Illuminate\Http\Request $request) {
            return $request->is('api/*') || $request->expectsJson();
        });
    })->create();
