<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

$mci = $app->make(\App\Services\Mci\MciConnectionService::class);
$mci->getConnection();

// Cek tgltagih dari TOFRS
echo "=== tgltagih dari TOFRS ===\n";
$rows = DB::connection('dashboard_data')->select("SELECT TOP 5 tgltagih, stsbyr FROM TOFRS");
foreach ($rows as $r) {
    echo "tgltagih: '{$r->tgltagih}' | stsbyr: '{$r->stsbyr}'\n";
}

// Cek tgleff dari TOFMPCOL
echo "\n=== sample TOFMPCOL ===\n";
$rows2 = DB::connection('dashboard_data')->select("SELECT TOP 3 nokontrak, colbaru, tglexp, ket FROM TOFMPCOL");
foreach ($rows2 as $r) {
    echo "nokontrak: '{$r->nokontrak}' | tglexp: '{$r->tglexp}' | ket: '{$r->ket}'\n";
}

// Cek TOFTCOL
echo "\n=== TOFTCOL columns ===\n";
$cols = DB::connection('dashboard_data')->select("SELECT TOP 1 * FROM TOFTCOL");
foreach ($cols as $r) {
    echo implode(' | ', array_keys((array)$r)) . "\n";
    echo implode(' | ', array_values((array)$r)) . "\n";
}

// Quick test the full CTE query minimal version
echo "\n=== Quick CTE test ===\n";
try {
    $test = DB::connection('dashboard_data')->select("
        WITH SystemDate AS (
            SELECT
                tgl AS TglRaw,
                CONVERT(DATE, SUBSTRING(tgl,5,4) + SUBSTRING(tgl,3,2) + SUBSTRING(tgl,1,2)) AS TglHitung,
                EOMONTH(CONVERT(DATE, SUBSTRING(tgl,5,4) + SUBSTRING(tgl,3,2) + SUBSTRING(tgl,1,2))) AS TglEOM
            FROM TANGGAL WHERE tgl = (SELECT MAX(tgl) FROM TANGGAL)
        )
        SELECT TOP 3 
            tflmb.nokontrak, tflmb.colbaru,
            CONVERT(DATE, tflmb.tglexp, 112) AS tgl_exp_date,
            CONVERT(DATE, tflmb.tgleff, 112) AS tgl_eff_date,
            sd.TglHitung, sd.TglEOM
        FROM TOFLMB tflmb CROSS JOIN SystemDate sd
        WHERE tflmb.stsrec = 'A'
    ");
    foreach ($test as $r) {
        echo "nokontrak: {$r->nokontrak} | tglexp: {$r->tgl_exp_date} | tgleff: {$r->tgl_eff_date} | TglEOM: {$r->TglEOM}\n";
    }
    echo "SUCCESS!\n";
} catch (\Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
}
