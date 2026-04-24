<?php

namespace App\Models\Mci\ReportingOjk;

use App\Models\Mci\MciBaseModel;

/**
 * Model MCI: GB0500
 * --------------------------------------------------------------------------
 * Domain   : Reporting OJK/BI
 * Tabel    : [dbo].[GB0500]
 * Kolom    : 20
 * Sumber   : Mapping_MCI_MAR26_01042026.sql / MCI_MAR26_01042026.xlsx
 *
 * CATATAN:
 *  - Model ini READ-ONLY (lihat trait ReadOnlyModel di MciBaseModel).
 *  - Koneksi default `dashboard_data` (SQL Server), otomatis ke DB bulan terbaru.
 *  - Kolom dengan spasi/karakter khusus diakses via:
 *    $model->getAttribute('NAMA KOLOM')  atau  $model->{'NAMA KOLOM'}.
 *
 * @property string $nama  type: varchar(50)
 * @property string $nocif  type: varchar(10)
 * @property string|null $norek  type: varchar(20)
 * @property string|null $nik  type: varchar(16)
 * @property string|null $kdgroup  type: varchar(10)
 * @property string|null $mtdrest  type: char(2)
 * @property string|null $frek  type: numeric(5)
 * @property string|null $old_jnsakad  type: char(1)
 * @property string|null $old_osmdlc  type: numeric(9)
 * @property string|null $old_osmgnc  type: numeric(9)
 * @property string|null $old_tgleff  type: varchar(8)
 * @property string|null $old_tglexp  type: varchar(8)
 * @property string|null $old_coll  type: varchar(8)
 * @property string|null $new_jnsakad  type: char(1)
 * @property string|null $new_osmdlc  type: numeric(9)
 * @property string|null $new_osmgnc  type: numeric(9)
 * @property string|null $new_tgleff  type: varchar(8)
 * @property string|null $new_tglexp  type: varchar(8)
 * @property string|null $new_coll  type: varchar(8)
 * @property string|null $pokpby  type: char(2)
 */
class Gb0500 extends MciBaseModel
{
    /**
     * Nama tabel (case-sensitive di SQL Server).
     */
    protected $table = 'GB0500';

    /**
     * Daftar LENGKAP kolom sesuai database (20 kolom).
     * Model ini read-only, $fillable hanya untuk dokumentasi & IDE helper.
     *
     * @var array<int,string>
     */
    protected $fillable = [
        'nama',
        'nocif',
        'norek',
        'nik',
        'kdgroup',
        'mtdrest',
        'frek',
        'old_jnsakad',
        'old_osmdlc',
        'old_osmgnc',
        'old_tgleff',
        'old_tglexp',
        'old_coll',
        'new_jnsakad',
        'new_osmdlc',
        'new_osmgnc',
        'new_tgleff',
        'new_tglexp',
        'new_coll',
        'pokpby',
    ];

    /**
     * Casting tipe kolom (non-string saja).
     *
     * @var array<string,string>
     */
    protected $casts = [
        'frek' => 'decimal:2',
        'old_osmdlc' => 'decimal:2',
        'old_osmgnc' => 'decimal:2',
        'new_osmdlc' => 'decimal:2',
        'new_osmgnc' => 'decimal:2',
    ];
}
