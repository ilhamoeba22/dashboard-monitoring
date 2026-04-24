<?php

namespace App\Models\Mci\Kurs;

use App\Models\Mci\MciBaseModel;

/**
 * Model MCI: MATAUANG
 * --------------------------------------------------------------------------
 * Domain   : Kurs
 * Tabel    : [dbo].[MATAUANG]
 * Kolom    : 13
 * Sumber   : Mapping_MCI_MAR26_01042026.sql / MCI_MAR26_01042026.xlsx
 *
 * CATATAN:
 *  - Model ini READ-ONLY (lihat trait ReadOnlyModel di MciBaseModel).
 *  - Koneksi default `dashboard_data` (SQL Server), otomatis ke DB bulan terbaru.
 *  - Kolom dengan spasi/karakter khusus diakses via:
 *    $model->getAttribute('NAMA KOLOM')  atau  $model->{'NAMA KOLOM'}.
 *
 * @property string $kdcc  type: varchar(2)
 * @property string|null $simbol  type: varchar(3)
 * @property string|null $ket  type: varchar(15)
 * @property string $stsrec  type: varchar(1)
 * @property string $inpuser  type: varchar(10)
 * @property string $inptgl  type: varchar(14)
 * @property string $inpterm  type: varchar(10)
 * @property string $chguser  type: varchar(10)
 * @property string $chgtgl  type: varchar(14)
 * @property string $chgterm  type: varchar(10)
 * @property string $autuser  type: varchar(10)
 * @property string $auttgl  type: varchar(14)
 * @property string $autterm  type: varchar(10)
 */
class Matauang extends MciBaseModel
{
    /**
     * Nama tabel (case-sensitive di SQL Server).
     */
    protected $table = 'MATAUANG';

    /**
     * Daftar LENGKAP kolom sesuai database (13 kolom).
     * Model ini read-only, $fillable hanya untuk dokumentasi & IDE helper.
     *
     * @var array<int,string>
     */
    protected $fillable = [
        'kdcc',
        'simbol',
        'ket',
        'stsrec',
        'inpuser',
        'inptgl',
        'inpterm',
        'chguser',
        'chgtgl',
        'chgterm',
        'autuser',
        'auttgl',
        'autterm',
    ];

    /**
     * Casting tipe kolom (non-string saja).
     *
     * @var array<string,string>
     */
    protected $casts = [

    ];
}
