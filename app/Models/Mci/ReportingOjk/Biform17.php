<?php

namespace App\Models\Mci\ReportingOjk;

use App\Models\Mci\MciBaseModel;

/**
 * Model MCI: BIFORM17
 * --------------------------------------------------------------------------
 * Domain   : Reporting OJK/BI
 * Tabel    : [dbo].[BIFORM17]
 * Kolom    : 14
 * Sumber   : Mapping_MCI_MAR26_01042026.sql / MCI_MAR26_01042026.xlsx
 *
 * CATATAN:
 *  - Model ini READ-ONLY (lihat trait ReadOnlyModel di MciBaseModel).
 *  - Koneksi default `dashboard_data` (SQL Server), otomatis ke DB bulan terbaru.
 *  - Kolom dengan spasi/karakter khusus diakses via:
 *    $model->getAttribute('NAMA KOLOM')  atau  $model->{'NAMA KOLOM'}.
 *
 * @property string $kdloc  type: char(2)
 * @property string $noaset  type: varchar(25)
 * @property string|null $jnsaset  type: char(3)
 * @property string|null $tgleff  type: varchar(8)
 * @property string|null $tglexp  type: varchar(8)
 * @property string|null $latitude  type: numeric(9)
 * @property string|null $longitude  type: numeric(9)
 * @property string|null $stsaset  type: char(1)
 * @property string|null $haper  type: numeric(9)
 * @property string|null $mtdsusut  type: char(2)
 * @property string|null $akumsusut  type: numeric(9)
 * @property string|null $ckpn  type: numeric(9)
 * @property string|null $nilaibuku  type: numeric(9)
 * @property string|null $mtdukur  type: varchar(2)
 */
class Biform17 extends MciBaseModel
{
    /**
     * Nama tabel (case-sensitive di SQL Server).
     */
    protected $table = 'BIFORM17';

    /**
     * Daftar LENGKAP kolom sesuai database (14 kolom).
     * Model ini read-only, $fillable hanya untuk dokumentasi & IDE helper.
     *
     * @var array<int,string>
     */
    protected $fillable = [
        'kdloc',
        'noaset',
        'jnsaset',
        'tgleff',
        'tglexp',
        'latitude',
        'longitude',
        'stsaset',
        'haper',
        'mtdsusut',
        'akumsusut',
        'ckpn',
        'nilaibuku',
        'mtdukur',
    ];

    /**
     * Casting tipe kolom (non-string saja).
     *
     * @var array<string,string>
     */
    protected $casts = [
        'latitude' => 'decimal:2',
        'longitude' => 'decimal:2',
        'haper' => 'decimal:2',
        'akumsusut' => 'decimal:2',
        'ckpn' => 'decimal:2',
        'nilaibuku' => 'decimal:2',
    ];
}
