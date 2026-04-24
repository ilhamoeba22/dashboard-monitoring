<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('mci:test-models', function () {
    $this->info('=== TESTING MCI MODELS ===');

    try {
        // Test 1: Koneksi Database
        $this->info('1. Testing Database Connection...');
        DB::connection('dashboard_data')->getPdo();
        $this->info('   [OK] Connected to SQL Server successfully.');

        // Test 2: Query Model mCIF (Tabel CIF)
        $this->info("\n2. Testing Model: App\\Models\\Mci\\Cif\\Mcif");
        $cif = \App\Models\Mci\Cif\Mcif::first();
        if ($cif) {
            $this->info('   [OK] Query successful. Found record:');
            $this->line('        nocif: ' . $cif->nocif);
            $this->line('        nm: ' . $cif->nm);
        } else {
            $this->warn('   [WARN] Query successful but table is empty.');
        }

        // Test 3: Query Model TOFLMB (Tabel Financing - 220 kolom)
        $this->info("\n3. Testing Model: App\\Models\\Mci\\Financing\\Toflmb");
        $toflmb = \App\Models\Mci\Financing\Toflmb::first();
        if ($toflmb) {
            $this->info('   [OK] Query successful. Found record:');
            $this->line('        nomor rekening: ' . $toflmb->nomor_rekening ?? 'N/A');
        } else {
            $this->warn('   [WARN] Query successful but table is empty.');
        }

        // Test 4: Query Model A01 (Tabel Agunan - Kolom dengan spasi)
        $this->info("\n4. Testing Model: App\\Models\\Mci\\Agunan\\A01");
        $a01 = \App\Models\Mci\Agunan\A01::first();
        if ($a01) {
            $this->info('   [OK] Query successful. Found record:');
            $this->line('        KODE REGISTER / NOMOR AGUNAN: ' . $a01->{'KODE REGISTER / NOMOR AGUNAN'});
        } else {
            $this->warn('   [WARN] Query successful but table is empty.');
        }

        // Test 5: Read-Only Protection
        $this->info("\n5. Testing Read-Only Protection...");
        try {
            $dummy = new \App\Models\Mci\Cif\Mcif();
            $dummy->nocif = 'TEST12345';
            $dummy->save();
            $this->error('   [FAIL] Model allowed saving! Read-only protection failed.');
        } catch (\RuntimeException $e) {
            $this->info('   [OK] Read-only protection works: ' . $e->getMessage());
        }

    } catch (\Exception $e) {
        $this->error('   [ERROR] ' . $e->getMessage());
    }

    $this->info("\n=== TEST COMPLETED ===");
})->purpose('Test MCI models connection and query');
