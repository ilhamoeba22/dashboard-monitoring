<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Config;

/**
 * Konfigurasi MCI Database Dashboard.
 *
 * File ini mengatur pattern penamaan database bulanan MCI dan koneksi yang tersedia.
 * Database MCI dibuat setiap bulan dengan format: MCI_{BULAN}{TAHUN}_{DDMMYYYY}
 * Contoh: MCI_MAR26_01042026 (database bulan Maret 2026, di-generate 1 April 2026)
 */
return [
    /*
    |--------------------------------------------------------------------------
    | Pattern Penamaan Database
    |--------------------------------------------------------------------------
    |
    | Pattern regex untuk menentukan format nama database MCI.
    | Contoh: MCI_{MMM}{YY}_{DDMMYYYY} → MCI_MAR26_01042026
    |
    */
    // Pattern baru dilonggarkan agar menerima format bulan seperti JULI, Sept, dan DES_312025
    'pattern' => '/^MCI_.+$/i',

    /*
    |--------------------------------------------------------------------------
    | Prefix Database
    |--------------------------------------------------------------------------
    |
    |-awalan nama database MCI.
    |
    */
    'prefix' => 'MCI_',

    /*
    |--------------------------------------------------------------------------
    | Koneksi Default
    |--------------------------------------------------------------------------
    |
    | Nama koneksi database yang digunakan untuk MCI (sesuai config/database.php).
    |
    */
    'connection' => 'dashboard_data',

    /*
    |--------------------------------------------------------------------------
    | Database Aktif Saat Ini
    |--------------------------------------------------------------------------
    |
    | Database MCI yang aktif digunakan. Di-set via .env (MCI_ACTIVE_DATABASE)
    | atau bisa diubah via session/request.
    |
    |--------------------------------------------------------------------------
    |
    | NOTE: Database ini harus ada di SQL Server tujuan.
    | Gunakan command: php artisan mci:list-databases
    | untuk melihat daftar database yang tersedia.
    |
    */
    'active_database' => env('MCI_ACTIVE_DATABASE', 'MCI_MAR26_01042026'),

    /*
    |--------------------------------------------------------------------------
    | Riwayat Database
    |--------------------------------------------------------------------------
    |
    | Daftar database MCI historis yang pernah digunakan.
    | Bisa di-scan dari SQL Server atau ditulis manual.
    |
    */
    'history' => [
        // Database terbaru = realtime aktif
        'MCI_MAR26_01042026',  // Maret 2026 (REALTIME saat ini)
        'MCI_FEB26_01032026',  // Februari 2026
        'MCI_JAN_31012026',    // Januari 2026
        'MCI_DES_312025',      // Desember 2025
        'MCI_NOV_30112025',    // November 2025
        'MCI_OKT_31102025',    // Oktober 2025
        'MCI_Sept_302025',     // September 2025
        'MCI_AGT_31082025',    // Agustus 2025
        'MCI_JULI_31072025',   // Juli 2025
        'MCI_JUN_2025',        // Juni 2025
    ],

    /*
    |--------------------------------------------------------------------------
    | Pengaturan Cache
    |--------------------------------------------------------------------------
    |
    | Konfigurasi caching untuk query MCI.
    |
    */
    'cache' => [
        // Cache selama 60 detik untuk data yang sering diakses
        'ttl' => 60,

        // Prefix key cache
        'prefix' => 'mci:',
    ],

    /*
    |--------------------------------------------------------------------------
    | Pengaturan Query
    |--------------------------------------------------------------------------
    |
    | Konfigurasi tambahan untuk query builder.
    |
    */
    'query' => [
        // Chunk size untuk proses data besar
        'chunk_size' => 1000,

        // Timeout query dalam detik
        'timeout' => 30,
    ],
];
