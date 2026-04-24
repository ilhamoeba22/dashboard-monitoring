<?php

namespace App\Models\Mci\Lainnya;

use App\Models\Mci\MciBaseModel;

/**
 * Model MCI: TOFMCCC
 * --------------------------------------------------------------------------
 * Domain   : Lainnya
 * Tabel    : [dbo].[TOFMCCC]
 * Kolom    : 18
 * Sumber   : Mapping_MCI_MAR26_01042026.sql / MCI_MAR26_01042026.xlsx
 *
 * CATATAN:
 *  - Model ini READ-ONLY (lihat trait ReadOnlyModel di MciBaseModel).
 *  - Koneksi default `dashboard_data` (SQL Server), otomatis ke DB bulan terbaru.
 *  - Kolom dengan spasi/karakter khusus diakses via:
 *    $model->getAttribute('NAMA KOLOM')  atau  $model->{'NAMA KOLOM'}.
 *
 * @property string $cc  type: varchar(2)
 * @property string $batch  type: numeric(5)
 * @property string|null $stockawal  type: numeric(9)
 * @property string|null $mtsbeli  type: numeric(9)
 * @property string|null $mtsjual  type: numeric(9)
 * @property string|null $stockakhir  type: numeric(9)
 * @property string|null $sahirva  type: numeric(9)
 * @property string|null $sahirrp  type: numeric(9)
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
class Tofmccc extends MciBaseModel
{
    /**
     * Nama tabel (case-sensitive di SQL Server).
     */
    protected $table = 'TOFMCCC';

    /**
     * Daftar LENGKAP kolom sesuai database (18 kolom).
     * Model ini read-only, $fillable hanya untuk dokumentasi & IDE helper.
     *
     * @var array<int,string>
     */
    protected $fillable = [
        'cc',
        'batch',
        'stockawal',
        'mtsbeli',
        'mtsjual',
        'stockakhir',
        'sahirva',
        'sahirrp',
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
        'batch' => 'decimal:2',
        'stockawal' => 'decimal:2',
        'mtsbeli' => 'decimal:2',
        'mtsjual' => 'decimal:2',
        'stockakhir' => 'decimal:2',
        'sahirva' => 'decimal:2',
        'sahirrp' => 'decimal:2',
    ];
}
