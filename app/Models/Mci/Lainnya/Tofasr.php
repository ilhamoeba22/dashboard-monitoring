<?php

declare(strict_types=1);

namespace App\Models\Mci\Lainnya;

use App\Models\Mci\MciBaseModel;

/**
 * Model MCI: TOFASR
 * --------------------------------------------------------------------------
 * Domain   : Lainnya
 * Tabel    : [dbo].[TOFASR]
 * Kolom    : 32
 * Sumber   : Mapping_MCI_MAR26_01042026.sql / MCI_MAR26_01042026.xlsx
 *
 * CATATAN:
 *  - Model ini READ-ONLY (lihat trait ReadOnlyModel di MciBaseModel).
 *  - Koneksi default `dashboard_data` (SQL Server), otomatis ke DB bulan terbaru.
 *  - Kolom dengan spasi/karakter khusus diakses via:
 *    $model->getAttribute('NAMA KOLOM')  atau  $model->{'NAMA KOLOM'}.
 *
 * @property string|null $nopolis type: varchar(50)
 * @property string|null $tglpolis type: varchar(8)
 * @property string|null $anpolis type: varchar(50)
 * @property string|null $tglmulai type: varchar(8)
 * @property string|null $tglakhir type: varchar(8)
 * @property string|null $kdmitra type: varchar(20)
 * @property string|null $cc type: varchar(2)
 * @property string|null $up type: numeric(9)
 * @property string|null $rate type: numeric(5)
 * @property string|null $premi type: numeric(9)
 * @property string|null $jnsrisk type: varchar(2)
 * @property string|null $tglbayar type: varchar(8)
 * @property string|null $tgltutup type: varchar(8)
 * @property string|null $kdcab type: varchar(3)
 * @property string|null $kdloc type: varchar(2)
 * @property string|null $stspolis type: varchar(1)
 * @property string|null $catatan type: varchar(150)
 * @property string|null $stsrec type: varchar(1)
 * @property string|null $inpuser type: varchar(10)
 * @property string|null $inptgl type: varchar(14)
 * @property string|null $inpterm type: varchar(10)
 * @property string|null $chguser type: varchar(10)
 * @property string|null $chgtgl type: varchar(14)
 * @property string|null $chgterm type: varchar(10)
 * @property string|null $autuser type: varchar(10)
 * @property string|null $auttgl type: varchar(14)
 * @property string|null $autterm type: varchar(10)
 * @property string|null $nokontrak type: varchar(11)
 * @property string|null $freecover type: char(1)
 * @property string|null $savebox type: char(15)
 * @property string|null $tglterima type: varchar(8)
 * @property string|null $proses type: varchar(100)
 */
class Tofasr extends MciBaseModel
{
    /**
     * Nama tabel (case-sensitive di SQL Server).
     */
    protected $table = 'TOFASR';

    /**
     * Daftar LENGKAP kolom sesuai database (32 kolom).
     * Model ini read-only, $fillable hanya untuk dokumentasi & IDE helper.
     *
     * @var array<int,string>
     */
    protected $fillable = [
        'nopolis',
        'tglpolis',
        'anpolis',
        'tglmulai',
        'tglakhir',
        'kdmitra',
        'cc',
        'up',
        'rate',
        'premi',
        'jnsrisk',
        'tglbayar',
        'tgltutup',
        'kdcab',
        'kdloc',
        'stspolis',
        'catatan',
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
        'nokontrak',
        'freecover',
        'savebox',
        'tglterima',
        'proses',
    ];

    /**
     * Casting tipe kolom (non-string saja).
     *
     * @var array<string,string>
     */
    protected $casts = [
        'up' => 'decimal:2',
        'rate' => 'decimal:2',
        'premi' => 'decimal:2',
    ];
}
