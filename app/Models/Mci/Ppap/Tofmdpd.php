<?php

namespace App\Models\Mci\Ppap;

use App\Models\Mci\MciBaseModel;

/**
 * Model MCI: TOFMDPD
 * --------------------------------------------------------------------------
 * Domain   : PPAP / DPD / Coll
 * Tabel    : [dbo].[TOFMDPD]
 * Kolom    : 13
 * Sumber   : Mapping_MCI_MAR26_01042026.sql / MCI_MAR26_01042026.xlsx
 *
 * CATATAN:
 *  - Model ini READ-ONLY (lihat trait ReadOnlyModel di MciBaseModel).
 *  - Koneksi default `dashboard_data` (SQL Server), otomatis ke DB bulan terbaru.
 *  - Kolom dengan spasi/karakter khusus diakses via:
 *    $model->getAttribute('NAMA KOLOM')  atau  $model->{'NAMA KOLOM'}.
 *
 * @property string|null $noacc  type: varchar(16)
 * @property string|null $nama  type: varchar(30)
 * @property string|null $kdaoh  type: varchar(10)
 * @property string|null $kdaop  type: varchar(10)
 * @property string|null $kdkolektor  type: varchar(10)
 * @property string|null $kdwil  type: varchar(5)
 * @property string|null $kdbank  type: varchar(3)
 * @property string|null $kdcab  type: varchar(2)
 * @property string|null $kdloc  type: varchar(2)
 * @property string|null $dpd  type: numeric(5)
 * @property string|null $tot_pokok  type: numeric(9)
 * @property string|null $tot_bunga  type: numeric(9)
 * @property string|null $tot_os  type: numeric(9)
 */
class Tofmdpd extends MciBaseModel
{
    /**
     * Nama tabel (case-sensitive di SQL Server).
     */
    protected $table = 'TOFMDPD';

    /**
     * Daftar LENGKAP kolom sesuai database (13 kolom).
     * Model ini read-only, $fillable hanya untuk dokumentasi & IDE helper.
     *
     * @var array<int,string>
     */
    protected $fillable = [
        'noacc',
        'nama',
        'kdaoh',
        'kdaop',
        'kdkolektor',
        'kdwil',
        'kdbank',
        'kdcab',
        'kdloc',
        'dpd',
        'tot_pokok',
        'tot_bunga',
        'tot_os',
    ];

    /**
     * Casting tipe kolom (non-string saja).
     *
     * @var array<string,string>
     */
    protected $casts = [
        'dpd' => 'decimal:2',
        'tot_pokok' => 'decimal:2',
        'tot_bunga' => 'decimal:2',
        'tot_os' => 'decimal:2',
    ];
}
