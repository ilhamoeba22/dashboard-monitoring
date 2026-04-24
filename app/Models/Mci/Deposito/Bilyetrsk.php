<?php

namespace App\Models\Mci\Deposito;

use App\Models\Mci\MciBaseModel;

/**
 * Model MCI: BILYETRSK
 * --------------------------------------------------------------------------
 * Domain   : Deposito
 * Tabel    : [dbo].[BILYETRSK]
 * Kolom    : 17
 * Sumber   : Mapping_MCI_MAR26_01042026.sql / MCI_MAR26_01042026.xlsx
 *
 * CATATAN:
 *  - Model ini READ-ONLY (lihat trait ReadOnlyModel di MciBaseModel).
 *  - Koneksi default `dashboard_data` (SQL Server), otomatis ke DB bulan terbaru.
 *  - Kolom dengan spasi/karakter khusus diakses via:
 *    $model->getAttribute('NAMA KOLOM')  atau  $model->{'NAMA KOLOM'}.
 *
 * @property string|null $nodep  type: varchar(10)
 * @property string|null $nobilyetlama  type: varchar(10)
 * @property string|null $nobilyetbaru  type: varchar(10)
 * @property string|null $bilyetke  type: numeric(5)
 * @property string|null $stscause  type: varchar(1)
 * @property string|null $ket  type: varchar(100)
 * @property string|null $stsupdate  type: varchar(1)
 * @property string|null $stsrec  type: varchar(1)
 * @property string|null $inpuser  type: varchar(10)
 * @property string|null $inptgljam  type: varchar(14)
 * @property string|null $inpterm  type: varchar(10)
 * @property string|null $chguser  type: varchar(10)
 * @property string|null $chgtgljam  type: varchar(14)
 * @property string|null $chgterm  type: varchar(10)
 * @property string|null $autuser  type: varchar(10)
 * @property string|null $auttgljam  type: varchar(14)
 * @property string|null $autterm  type: varchar(10)
 */
class Bilyetrsk extends MciBaseModel
{
    /**
     * Nama tabel (case-sensitive di SQL Server).
     */
    protected $table = 'BILYETRSK';

    /**
     * Daftar LENGKAP kolom sesuai database (17 kolom).
     * Model ini read-only, $fillable hanya untuk dokumentasi & IDE helper.
     *
     * @var array<int,string>
     */
    protected $fillable = [
        'nodep',
        'nobilyetlama',
        'nobilyetbaru',
        'bilyetke',
        'stscause',
        'ket',
        'stsupdate',
        'stsrec',
        'inpuser',
        'inptgljam',
        'inpterm',
        'chguser',
        'chgtgljam',
        'chgterm',
        'autuser',
        'auttgljam',
        'autterm',
    ];

    /**
     * Casting tipe kolom (non-string saja).
     *
     * @var array<string,string>
     */
    protected $casts = [
        'bilyetke' => 'decimal:2',
    ];
}
