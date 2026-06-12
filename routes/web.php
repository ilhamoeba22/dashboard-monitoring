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
    Route::redirect('/ppka', '/financing/quality')->name('financing.ppka');
    Route::get('/settlement', fn() => Inertia::render('Financing/Settlement'))->name('financing.settlement');
    Route::get('/write-off', fn() => Inertia::render('Financing/WriteOff'))->name('financing.write-off');
    Route::get('/yield', fn() => Inertia::render('Financing/Yield'))->name('financing.yield');

    // G8: Performance
    Route::get('/repayment-rate', fn() => Inertia::render('Financing/RepaymentRate'))->name('financing.repayment-rate');
    Route::redirect('/repayment-rate-new', '/financing/repayment-rate?tab=new')->name('financing.repayment-rate-new');
});

Route::prefix('cif')->group(function () {
    Route::get('/', fn() => Inertia::render('Cif/Index'))->name('cif.index');
    Route::get('/pembiayaan', fn() => Inertia::render('Cif/Pembiayaan'))->name('cif.pembiayaan');
    Route::get('/tabungan', fn() => Inertia::render('Cif/Tabungan'))->name('cif.tabungan');
    Route::get('/deposito', fn() => Inertia::render('Cif/Deposito'))->name('cif.deposito');
    Route::get('/rekapitulasi', fn() => Inertia::render('Cif/Rekapitulasi'))->name('cif.rekapitulasi');
    Route::get('/quality', fn() => Inertia::render('Cif/Quality'))->name('cif.quality');
    Route::get('/{nocif}', fn($nocif) => Inertia::render('Cif/Detail', ['nocif' => $nocif]))->name('cif.detail');
});

Route::get('/funding', function () {
    return Inertia::render('Funding/Index', ['initialDomain' => 'overview']);
})->name('funding.index');

Route::get('/funding/tabungan', function () {
    return Inertia::render('Funding/Index', ['initialDomain' => 'tabungan', 'initialFeature' => 'nominatif']);
})->name('funding.tabungan');

Route::get('/funding/tabungan/nominatif', function () {
    return Inertia::render('Funding/Index', ['initialDomain' => 'tabungan', 'initialFeature' => 'nominatif']);
})->name('funding.tabungan.nominatif');

Route::get('/funding/tabungan/rekapitulasi', function () {
    return Inertia::render('Funding/Index', ['initialDomain' => 'tabungan', 'initialFeature' => 'rekapitulasi']);
})->name('funding.tabungan.rekapitulasi');

Route::get('/funding/tabungan/dormant', function () {
    return Inertia::render('Funding/Index', ['initialDomain' => 'tabungan', 'initialFeature' => 'dormant']);
})->name('funding.tabungan.dormant');

Route::get('/funding/deposito', function () {
    return Inertia::render('Funding/Index', ['initialDomain' => 'deposito', 'initialFeature' => 'nominatif']);
})->name('funding.deposito');

Route::get('/funding/deposito/nominatif', function () {
    return Inertia::render('Funding/Index', ['initialDomain' => 'deposito', 'initialFeature' => 'nominatif']);
})->name('funding.deposito.nominatif');

Route::get('/funding/deposito/rekapitulasi', function () {
    return Inertia::render('Funding/Index', ['initialDomain' => 'deposito', 'initialFeature' => 'rekapitulasi']);
})->name('funding.deposito.rekapitulasi');

Route::get('/funding/deposito/jatuh-tempo', function () {
    return Inertia::render('Funding/Index', ['initialDomain' => 'deposito', 'initialFeature' => 'jatuh-tempo']);
})->name('funding.deposito.jatuh-tempo');

Route::get('/funding/perkembangan', function () {
    return Inertia::render('Funding/Index', ['initialDomain' => 'perkembangan']);
})->name('funding.perkembangan');

Route::get('/funding/target', function () {
    return Inertia::render('Funding/Index', ['initialDomain' => 'target']);
})->name('funding.target');

Route::get('/funding/mutasi', function () {
    return Inertia::render('Funding/Index', ['initialDomain' => 'mutasi']);
})->name('funding.mutasi');

Route::get('/funding/risk', function () {
    return Inertia::render('Funding/Index', ['initialDomain' => 'risk']);
})->name('funding.risk');

Route::get('/funding/concentration', function () {
    return Inertia::render('Funding/Index', ['initialDomain' => 'concentration']);
})->name('funding.concentration');

Route::get('/funding/baghas', function () {
    return Inertia::render('Funding/Index', ['initialDomain' => 'baghas']);
})->name('funding.baghas');

Route::get('/reporting', function () {
    return Inertia::render('Reporting/Index');
});

// === ADMINISTRATOR MODULE ROUTES ===
Route::prefix('admin')->group(function () {
    Route::get('/management', function () {
        return Inertia::render('Admin/Management');
    })->name('admin.management');
});
