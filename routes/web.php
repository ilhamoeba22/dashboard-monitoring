<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Dashboard/Index');
});

// === FINANCING MODULE ROUTES ===
Route::prefix('financing')->group(function () {
    // G1: Overview
    Route::get('/', fn() => Inertia::render('Financing/Overview'));

    // G2: Data Entry
    Route::get('/nominatif', fn() => Inertia::render('Financing/Nominatif'))->name('financing.nominatif');
    Route::get('/sindikasi', fn() => Inertia::render('Financing/Sindikasi'))->name('financing.sindikasi');
    Route::get('/karyawan', fn() => Inertia::render('Financing/Karyawan'))->name('financing.karyawan');

    // G3: Perkembangan
    Route::get('/perkembangan', fn() => Inertia::render('Financing/Perkembangan'))->name('financing.perkembangan');
    Route::get('/target', fn() => Inertia::render('Financing/Target'))->name('financing.target');

    // G4: Rekapitulasi & Quality
    Route::get('/rekapitulasi', fn() => Inertia::render('Financing/Rekapitulasi'))->name('financing.rekapitulasi');
    Route::get('/quality', fn() => Inertia::render('Financing/Quality'))->name('financing.quality');
    // G5: Tunggakan
    Route::get('/jatuh-tempo', fn() => Inertia::render('Financing/JatuhTempo'))->name('financing.jatuh-tempo');
    Route::get('/coll-monitoring', fn() => Inertia::render('Financing/CollMonitoring'))->name('financing.coll-monitoring');

    // G6: Restrukturisasi
    Route::get('/restrukturisasi', fn() => Inertia::render('Financing/Restrukturisasi'))->name('financing.restrukturisasi');
    Route::get('/top-up', fn() => Inertia::render('Financing/TopUp'))->name('financing.top-up');

    // G7: Penyelesaian
    Route::get('/ppka', fn() => Inertia::render('Financing/Ppka'))->name('financing.ppka');
    Route::get('/settlement', fn() => Inertia::render('Financing/Settlement'))->name('financing.settlement');
    Route::get('/write-off', fn() => Inertia::render('Financing/WriteOff'))->name('financing.write-off');
    Route::get('/yield', fn() => Inertia::render('Financing/Yield'))->name('financing.yield');

    // G8: Performance
    Route::get('/repayment-rate', fn() => Inertia::render('Financing/RepaymentRate'))->name('financing.repayment-rate');
    Route::get('/repayment-rate-new', fn() => Inertia::render('Financing/RepaymentRateNew'))->name('financing.repayment-rate-new');
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

// === ADMINISTRATOR MODULE ROUTES ===
Route::prefix('admin')->group(function () {
    Route::get('/management', function () {
        return Inertia::render('Admin/Management');
    })->name('admin.management');
});
