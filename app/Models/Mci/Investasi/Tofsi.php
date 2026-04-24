<?php

namespace App\Models\Mci\Investasi;

use App\Models\Mci\MciBaseModel;

/**
 * Model MCI: TOFSI
 * --------------------------------------------------------------------------
 * Domain   : Investasi / Saham
 * Tabel    : [dbo].[TOFSI]
 * Kolom    : 31
 * Sumber   : Mapping_MCI_MAR26_01042026.sql / MCI_MAR26_01042026.xlsx
 *
 * CATATAN:
 *  - Model ini READ-ONLY (lihat trait ReadOnlyModel di MciBaseModel).
 *  - Koneksi default `dashboard_data` (SQL Server), otomatis ke DB bulan terbaru.
 *  - Kolom dengan spasi/karakter khusus diakses via:
 *    $model->getAttribute('NAMA KOLOM')  atau  $model->{'NAMA KOLOM'}.
 *
 * @property string $kdcab  type: varchar(3)
 * @property string $nourut  type: numeric(9)
 * @property string|null $noac  type: varchar(11)
 * @property string|null $tglbeban  type: numeric(5)
 * @property string|null $tgleff  type: varchar(8)
 * @property string|null $tglhapus  type: varchar(8)
 * @property string|null $libur  type: varchar(1)
 * @property string|null $kdbayar  type: varchar(2)
 * @property string|null $nominal  type: numeric(9)
 * @property string|null $nombayar  type: numeric(9)
 * @property string|null $kdnom  type: varchar(1)
 * @property string|null $saldomax  type: numeric(9)
 * @property string|null $nolawan  type: varchar(20)
 * @property string|null $kdlawan  type: varchar(1)
 * @property string|null $nmbank  type: varchar(20)
 * @property string|null $almbank  type: varchar(30)
 * @property string|null $kettrn  type: varchar(30)
 * @property string|null $bytrn  type: numeric(9)
 * @property string|null $bytrf  type: numeric(9)
 * @property string|null $stsrec  type: varchar(1)
 * @property string|null $inpuser  type: varchar(10)
 * @property string|null $inptgl  type: varchar(14)
 * @property string|null $inpterm  type: varchar(10)
 * @property string|null $chguser  type: varchar(10)
 * @property string|null $chgtgl  type: varchar(14)
 * @property string|null $chgterm  type: varchar(10)
 * @property string|null $autuser  type: varchar(10)
 * @property string|null $auttgl  type: varchar(14)
 * @property string|null $autterm  type: varchar(10)
 * @property string|null $kdlembaga  type: varchar(10)
 * @property string|null $kdbiaya  type: varchar(20)
 */
class Tofsi extends MciBaseModel
{
    /**
     * Nama tabel (case-sensitive di SQL Server).
     */
    protected $table = 'TOFSI';

    /**
     * Daftar LENGKAP kolom sesuai database (31 kolom).
     * Model ini read-only, $fillable hanya untuk dokumentasi & IDE helper.
     *
     * @var array<int,string>
     */
    protected $fillable = [
        'kdcab',
        'nourut',
        'noac',
        'tglbeban',
        'tgleff',
        'tglhapus',
        'libur',
        'kdbayar',
        'nominal',
        'nombayar',
        'kdnom',
        'saldomax',
        'nolawan',
        'kdlawan',
        'nmbank',
        'almbank',
        'kettrn',
        'bytrn',
        'bytrf',
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
        'kdlembaga',
        'kdbiaya',
    ];

    /**
     * Casting tipe kolom (non-string saja).
     *
     * @var array<string,string>
     */
    protected $casts = [
        'nourut' => 'decimal:2',
        'tglbeban' => 'decimal:2',
        'nominal' => 'decimal:2',
        'nombayar' => 'decimal:2',
        'saldomax' => 'decimal:2',
        'bytrn' => 'decimal:2',
        'bytrf' => 'decimal:2',
    ];
}
