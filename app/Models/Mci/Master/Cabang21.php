<?php

declare(strict_types=1);

namespace App\Models\Mci\Master;

use App\Models\Mci\MciBaseModel;

/**
 * Model MCI: CABANG21
 * --------------------------------------------------------------------------
 * Domain   : Cabang / Wilayah
 * Tabel    : [dbo].[CABANG21]
 * Kolom    : 28
 * Sumber   : Mapping_MCI_MAR26_01042026.sql / MCI_MAR26_01042026.xlsx
 *
 * CATATAN:
 *  - Model ini READ-ONLY (lihat trait ReadOnlyModel di MciBaseModel).
 *  - Koneksi default `dashboard_data` (SQL Server), otomatis ke DB bulan terbaru.
 *  - Kolom dengan spasi/karakter khusus diakses via:
 *    $model->getAttribute('NAMA KOLOM')  atau  $model->{'NAMA KOLOM'}.
 *
 * @property string|null $kdloc type: char(2)
 * @property string|null $nmpelapor type: varchar(50)
 * @property string|null $bagpelapor type: varchar(50)
 * @property string|null $nmkap type: varchar(50)
 * @property string|null $nmap type: varchar(50)
 * @property string|null $frekaudit type: numeric(5)
 * @property string|null $tglrups type: varchar(8)
 * @property string|null $dividen type: numeric(9)
 * @property string|null $bonus type: numeric(9)
 * @property string|null $nilaisaham type: numeric(9)
 * @property string|null $edc_own type: numeric(5)
 * @property string|null $edc_bus type: numeric(5)
 * @property string|null $edc_bprs type: numeric(5)
 * @property string|null $atm_own type: numeric(5)
 * @property string|null $atm_bus type: numeric(5)
 * @property string|null $stspva type: char(1)
 * @property string|null $tglpva type: char(8)
 * @property string|null $ktrpva type: numeric(5)
 * @property string|null $stsrec type: char(1)
 * @property string|null $inpuser type: varchar(10)
 * @property string|null $inptgljam type: varchar(14)
 * @property string|null $inpterm type: varchar(10)
 * @property string|null $chguser type: varchar(10)
 * @property string|null $chgtgljam type: varchar(14)
 * @property string|null $chgterm type: varchar(10)
 * @property string|null $autuser type: varchar(10)
 * @property string|null $auttgljam type: varchar(14)
 * @property string|null $autterm type: varchar(10)
 */
class Cabang21 extends MciBaseModel
{
    /**
     * Nama tabel (case-sensitive di SQL Server).
     */
    protected $table = 'CABANG21';

    /**
     * Daftar LENGKAP kolom sesuai database (28 kolom).
     * Model ini read-only, $fillable hanya untuk dokumentasi & IDE helper.
     *
     * @var array<int,string>
     */
    protected $fillable = [
        'kdloc',
        'nmpelapor',
        'bagpelapor',
        'nmkap',
        'nmap',
        'frekaudit',
        'tglrups',
        'dividen',
        'bonus',
        'nilaisaham',
        'edc_own',
        'edc_bus',
        'edc_bprs',
        'atm_own',
        'atm_bus',
        'stspva',
        'tglpva',
        'ktrpva',
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
        'frekaudit' => 'decimal:2',
        'dividen' => 'decimal:2',
        'bonus' => 'decimal:2',
        'nilaisaham' => 'decimal:2',
        'edc_own' => 'decimal:2',
        'edc_bus' => 'decimal:2',
        'edc_bprs' => 'decimal:2',
        'atm_own' => 'decimal:2',
        'atm_bus' => 'decimal:2',
        'ktrpva' => 'decimal:2',
    ];
}
