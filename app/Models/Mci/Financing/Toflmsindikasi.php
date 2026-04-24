<?php

namespace App\Models\Mci\Financing;

use App\Models\Mci\MciBaseModel;

/**
 * Model MCI: TOFLMSINDIKASI
 * --------------------------------------------------------------------------
 * Domain   : Financing / Loan
 * Tabel    : [dbo].[TOFLMSINDIKASI]
 * Kolom    : 30
 * Sumber   : Mapping_MCI_MAR26_01042026.sql / MCI_MAR26_01042026.xlsx
 *
 * CATATAN:
 *  - Model ini READ-ONLY (lihat trait ReadOnlyModel di MciBaseModel).
 *  - Koneksi default `dashboard_data` (SQL Server), otomatis ke DB bulan terbaru.
 *  - Kolom dengan spasi/karakter khusus diakses via:
 *    $model->getAttribute('NAMA KOLOM')  atau  $model->{'NAMA KOLOM'}.
 *
 * @property string|null $nokontrak  type: varchar(10)
 * @property string|null $nama  type: varchar(100)
 * @property string|null $stssind  type: varchar(3)
 * @property string|null $nocifpeserta  type: varchar(20)
 * @property string|null $nopeserta  type: varchar(20)
 * @property string|null $nmpeserta  type: varchar(100)
 * @property string|null $kdbank  type: varchar(10)
 * @property string|null $akad  type: varchar(5)
 * @property string|null $stspeserta  type: varchar(3)
 * @property string|null $pendanaan  type: varchar(3)
 * @property string|null $mdlawal  type: numeric(9)
 * @property string|null $mgnawal  type: numeric(9)
 * @property string|null $osmdlc  type: numeric(9)
 * @property string|null $osmgnc  type: numeric(9)
 * @property string|null $coll  type: varchar(1)
 * @property string|null $stsrec  type: varchar(3)
 * @property string|null $inpuser  type: varchar(10)
 * @property string|null $inptgl  type: varchar(14)
 * @property string|null $inpterm  type: varchar(10)
 * @property string|null $chguser  type: varchar(10)
 * @property string|null $chgtgl  type: varchar(14)
 * @property string|null $chgterm  type: varchar(10)
 * @property string|null $autuser  type: varchar(10)
 * @property string|null $auttgl  type: varchar(14)
 * @property string|null $autterm  type: varchar(10)
 * @property string|null $tglakad  type: varchar(10)
 * @property string|null $tgleff  type: varchar(10)
 * @property string|null $tglexp  type: varchar(10)
 * @property string|null $jw  type: numeric(5)
 * @property string|null $kdjw  type: varchar(3)
 */
class Toflmsindikasi extends MciBaseModel
{
    /**
     * Nama tabel (case-sensitive di SQL Server).
     */
    protected $table = 'TOFLMSINDIKASI';

    /**
     * Daftar LENGKAP kolom sesuai database (30 kolom).
     * Model ini read-only, $fillable hanya untuk dokumentasi & IDE helper.
     *
     * @var array<int,string>
     */
    protected $fillable = [
        'nokontrak',
        'nama',
        'stssind',
        'nocifpeserta',
        'nopeserta',
        'nmpeserta',
        'kdbank',
        'akad',
        'stspeserta',
        'pendanaan',
        'mdlawal',
        'mgnawal',
        'osmdlc',
        'osmgnc',
        'coll',
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
        'tglakad',
        'tgleff',
        'tglexp',
        'jw',
        'kdjw',
    ];

    /**
     * Casting tipe kolom (non-string saja).
     *
     * @var array<string,string>
     */
    protected $casts = [
        'mdlawal' => 'decimal:2',
        'mgnawal' => 'decimal:2',
        'osmdlc' => 'decimal:2',
        'osmgnc' => 'decimal:2',
        'jw' => 'decimal:2',
    ];
}
