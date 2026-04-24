<?php

namespace App\Models\Mci\ReportingOjk;

use App\Models\Mci\MciBaseModel;

/**
 * Model MCI: BIFORM29
 * --------------------------------------------------------------------------
 * Domain   : Reporting OJK/BI
 * Tabel    : [dbo].[BIFORM29]
 * Kolom    : 11
 * Sumber   : Mapping_MCI_MAR26_01042026.sql / MCI_MAR26_01042026.xlsx
 *
 * CATATAN:
 *  - Model ini READ-ONLY (lihat trait ReadOnlyModel di MciBaseModel).
 *  - Koneksi default `dashboard_data` (SQL Server), otomatis ke DB bulan terbaru.
 *  - Kolom dengan spasi/karakter khusus diakses via:
 *    $model->getAttribute('NAMA KOLOM')  atau  $model->{'NAMA KOLOM'}.
 *
 * @property string $kdloc  type: char(2)
 * @property string $nocif  type: varchar(10)
 * @property string|null $noid  type: varchar(16)
 * @property string|null $golcust  type: char(6)
 * @property string|null $hubbank  type: char(1)
 * @property string|null $jnsinstrumen  type: varchar(2)
 * @property string|null $norek  type: varchar(20)
 * @property string|null $tglwo  type: char(8)
 * @property string|null $jumlah  type: numeric(9)
 * @property string|null $jumlahdipulihkan  type: numeric(9)
 * @property string|null $bakidebet  type: numeric(9)
 */
class Biform29 extends MciBaseModel
{
    /**
     * Nama tabel (case-sensitive di SQL Server).
     */
    protected $table = 'BIFORM29';

    /**
     * Daftar LENGKAP kolom sesuai database (11 kolom).
     * Model ini read-only, $fillable hanya untuk dokumentasi & IDE helper.
     *
     * @var array<int,string>
     */
    protected $fillable = [
        'kdloc',
        'nocif',
        'noid',
        'golcust',
        'hubbank',
        'jnsinstrumen',
        'norek',
        'tglwo',
        'jumlah',
        'jumlahdipulihkan',
        'bakidebet',
    ];

    /**
     * Casting tipe kolom (non-string saja).
     *
     * @var array<string,string>
     */
    protected $casts = [
        'jumlah' => 'decimal:2',
        'jumlahdipulihkan' => 'decimal:2',
        'bakidebet' => 'decimal:2',
    ];
}
