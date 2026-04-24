<?php

namespace App\Models\Mci\Lainnya;

use App\Models\Mci\MciBaseModel;

/**
 * Model MCI: TMPGLKONV
 * --------------------------------------------------------------------------
 * Domain   : Lainnya
 * Tabel    : [dbo].[TMPGLKONV]
 * Kolom    : 7
 * Sumber   : Mapping_MCI_MAR26_01042026.sql / MCI_MAR26_01042026.xlsx
 *
 * CATATAN:
 *  - Model ini READ-ONLY (lihat trait ReadOnlyModel di MciBaseModel).
 *  - Koneksi default `dashboard_data` (SQL Server), otomatis ke DB bulan terbaru.
 *  - Kolom dengan spasi/karakter khusus diakses via:
 *    $model->getAttribute('NAMA KOLOM')  atau  $model->{'NAMA KOLOM'}.
 *
 * @property string|null $nosbb_baru  type: varchar(10)
 * @property string|null $nobb_baru  type: varchar(10)
 * @property string|null $nama  type: varchar(40)
 * @property string|null $nosbb_lama  type: varchar(10)
 * @property string|null $nom_01  type: numeric(9)
 * @property string|null $nom_02  type: numeric(9)
 * @property string|null $nom_03  type: numeric(9)
 */
class Tmpglkonv extends MciBaseModel
{
    /**
     * Nama tabel (case-sensitive di SQL Server).
     */
    protected $table = 'TMPGLKONV';

    /**
     * Daftar LENGKAP kolom sesuai database (7 kolom).
     * Model ini read-only, $fillable hanya untuk dokumentasi & IDE helper.
     *
     * @var array<int,string>
     */
    protected $fillable = [
        'nosbb_baru',
        'nobb_baru',
        'nama',
        'nosbb_lama',
        'nom_01',
        'nom_02',
        'nom_03',
    ];

    /**
     * Casting tipe kolom (non-string saja).
     *
     * @var array<string,string>
     */
    protected $casts = [
        'nom_01' => 'decimal:2',
        'nom_02' => 'decimal:2',
        'nom_03' => 'decimal:2',
    ];
}
