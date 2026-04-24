<?php

namespace App\Models\Mci\Lainnya;

use App\Models\Mci\MciBaseModel;

/**
 * Model MCI: H_TOFASETHP
 * --------------------------------------------------------------------------
 * Domain   : Lainnya
 * Tabel    : [dbo].[H_TOFASETHP]
 * Kolom    : 23
 * Sumber   : Mapping_MCI_MAR26_01042026.sql / MCI_MAR26_01042026.xlsx
 *
 * CATATAN:
 *  - Model ini READ-ONLY (lihat trait ReadOnlyModel di MciBaseModel).
 *  - Koneksi default `dashboard_data` (SQL Server), otomatis ke DB bulan terbaru.
 *  - Kolom dengan spasi/karakter khusus diakses via:
 *    $model->getAttribute('NAMA KOLOM')  atau  $model->{'NAMA KOLOM'}.
 *
 * @property string|null $tgltrn  type: varchar(10)
 * @property string|null $trnuser  type: varchar(10)
 * @property string|null $notrn  type: varchar(10)
 * @property string|null $noreg  type: varchar(25)
 * @property string|null $kdloc  type: varchar(3)
 * @property string|null $sbbdr  type: varchar(10)
 * @property string|null $sbbcr  type: varchar(10)
 * @property string|null $dokumen  type: varchar(250)
 * @property int|null $qty  type: int(4)
 * @property string|null $nominal  type: numeric(9)
 * @property string|null $ket  type: varchar(250)
 * @property string|null $ststrn  type: char(1)
 * @property string|null $jnstrx  type: char(1)
 * @property string|null $inpuser  type: varchar(10)
 * @property string|null $inptgl  type: varchar(14)
 * @property string|null $inpterm  type: varchar(10)
 * @property string|null $chguser  type: varchar(10)
 * @property string|null $chgtgl  type: varchar(14)
 * @property string|null $chgterm  type: varchar(10)
 * @property string|null $autuser  type: varchar(10)
 * @property string|null $auttgl  type: varchar(14)
 * @property string|null $autterm  type: varchar(10)
 * @property string|null $w_prog  type: varchar(25)
 */
class HTofasethp extends MciBaseModel
{
    /**
     * Nama tabel (case-sensitive di SQL Server).
     */
    protected $table = 'H_TOFASETHP';

    /**
     * Daftar LENGKAP kolom sesuai database (23 kolom).
     * Model ini read-only, $fillable hanya untuk dokumentasi & IDE helper.
     *
     * @var array<int,string>
     */
    protected $fillable = [
        'tgltrn',
        'trnuser',
        'notrn',
        'noreg',
        'kdloc',
        'sbbdr',
        'sbbcr',
        'dokumen',
        'qty',
        'nominal',
        'ket',
        'ststrn',
        'jnstrx',
        'inpuser',
        'inptgl',
        'inpterm',
        'chguser',
        'chgtgl',
        'chgterm',
        'autuser',
        'auttgl',
        'autterm',
        'w_prog',
    ];

    /**
     * Casting tipe kolom (non-string saja).
     *
     * @var array<string,string>
     */
    protected $casts = [
        'qty' => 'integer',
        'nominal' => 'decimal:2',
    ];
}
