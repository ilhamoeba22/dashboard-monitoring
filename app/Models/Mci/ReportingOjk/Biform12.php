<?php

declare(strict_types=1);

namespace App\Models\Mci\ReportingOjk;

use App\Models\Mci\MciBaseModel;

/**
 * Model MCI: BIFORM12
 * --------------------------------------------------------------------------
 * Domain   : Reporting OJK/BI
 * Tabel    : [dbo].[BIFORM12]
 * Kolom    : 5
 * Sumber   : Mapping_MCI_MAR26_01042026.sql / MCI_MAR26_01042026.xlsx
 *
 * CATATAN:
 *  - Model ini READ-ONLY (lihat trait ReadOnlyModel di MciBaseModel).
 *  - Koneksi default `dashboard_data` (SQL Server), otomatis ke DB bulan terbaru.
 *  - Kolom dengan spasi/karakter khusus diakses via:
 *    $model->getAttribute('NAMA KOLOM')  atau  $model->{'NAMA KOLOM'}.
 *
 * @property string $kdloc type: char(2)
 * @property string $jnsinstrumen type: char(2)
 * @property string|null $sifatpemb type: char(1)
 * @property string|null $ppapumum type: numeric(9)
 * @property string|null $ppapkhusus type: numeric(9)
 */
class Biform12 extends MciBaseModel
{
    /**
     * Nama tabel (case-sensitive di SQL Server).
     */
    protected $table = 'BIFORM12';

    /**
     * Daftar LENGKAP kolom sesuai database (5 kolom).
     * Model ini read-only, $fillable hanya untuk dokumentasi & IDE helper.
     *
     * @var array<int,string>
     */
    protected $fillable = [
        'kdloc',
        'jnsinstrumen',
        'sifatpemb',
        'ppapumum',
        'ppapkhusus',
    ];

    /**
     * Casting tipe kolom (non-string saja).
     *
     * @var array<string,string>
     */
    protected $casts = [
        'ppapumum' => 'decimal:2',
        'ppapkhusus' => 'decimal:2',
    ];
}
