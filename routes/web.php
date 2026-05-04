<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Dashboard/Index');
});

// === FINANCING MODULE ROUTES ===
Route::prefix('financing')->group(function () {
    // G1: Overview (Dashboard Ringan)
    Route::get('/', function () {
        return Inertia::render('Financing/Overview');
    });

    // Nominatif (existing)
    Route::get('/nominatif', function () {
        return Inertia::render('Financing/Index');
    });
});

Route::get('/cif', function () {
    return Inertia::render('Cif/Index');
});

Route::get('/funding', function () {
    return Inertia::render('Funding/Index');
});

Route::get('/reporting', function () {
    return Inertia::render('Reporting/Index');
});
