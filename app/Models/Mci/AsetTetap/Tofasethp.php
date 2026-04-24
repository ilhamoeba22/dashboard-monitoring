<?php

namespace App\Models\Mci\AsetTetap;

use App\Models\Mci\MciBaseModel;

/**
 * Model MCI: TOFASETHP
 * --------------------------------------------------------------------------
 * Domain   : Aset Tetap
 * Tabel    : [dbo].[TOFASETHP]
 * Kolom    : 23
 * Sumber   : Mapping_MCI_MAR26_01042026.sql / MCI_MAR26_01042026.xlsx
 *
 * CATATAN:
 *  - Model ini READ-ONLY (lihat trait ReadOnlyModel di MciBaseModel).
 *  - Koneksi default `dashboard_data` (SQL Server), otomatis ke DB bulan terbaru.
 *  - Kolom dengan spasi/karakter khusus diakses via:
 *    $model->getAttribute('NAMA KOLOM')  atau  $model->{'NAMA KOLOM'}.
 *
 * @property string $groupaset  type: varchar(10)
 * @property string $noreg  type: varchar(25)
 * @property string|null $nmaset  type: varchar(250)
 * @property string|null $tglbeli  type: varchar(8)
 * @property string|null $kdloc  type: varchar(3)
 * @property string|null $haper  type: numeric(9)
 * @property string|null $nilaibuku  type: numeric(9)
 * @property string|null $nilaisusut  type: numeric(9)
 * @property int|null $qty  type: int(4)
 * @property string|null $totsusut  type: numeric(9)
 * @property string|null $sbbdr  type: varchar(15)
 * @property string|null $catatan  type: varchar(150)
 * @property string|null $stsrec  type: char(1)
 * @property string|null $ststrn  type: char(1)
 * @property string|null $inpuser  type: varchar(10)
 * @property string|null $inptgl  type: varchar(14)
 * @property string|null $inpterm  type: varchar(10)
 * @property string|null $chguser  type: varchar(10)
 * @property string|null $chgtgl  type: varchar(14)
 * @property string|null $chgterm  type: varchar(10)
 * @property string|null $autuser  type: varchar(10)
 * @property string|null $auttgl  type: varchar(14)
 * @property string|null $autterm  type: varchar(10)
 */
class Tofasethp extends MciBaseModel
{
    /**
     * Nama tabel (case-sensitive di SQL Server).
     */
    protected $table = 'TOFASETHP';

    /**
     * Daftar LENGKAP kolom sesuai database (23 kolom).
     * Model ini read-only, $fillable hanya untuk dokumentasi & IDE helper.
     *
     * @var array<int,string>
     */
    protected $fillable = [
        'groupaset',
        'noreg',
        'nmaset',
        'tglbeli',
        'kdloc',
        'haper',
        'nilaibuku',
        'nilaisusut',
        'qty',
        'totsusut',
        'sbbdr',
        'catatan',
        'stsrec',
        'ststrn',
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
        'haper' => 'decimal:2',
        'nilaibuku' => 'decimal:2',
        'nilaisusut' => 'decimal:2',
        'qty' => 'integer',
        'totsusut' => 'decimal:2',
    ];
}
