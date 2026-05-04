<?php

declare(strict_types=1);

namespace App\Models\Mci\Cif;

use App\Models\Mci\MciBaseModel;

/**
 * Model MCI: MCIFDOCFS
 * --------------------------------------------------------------------------
 * Domain   : CIF / Customer
 * Tabel    : [dbo].[MCIFDOCFS]
 * Kolom    : 39
 * Sumber   : Mapping_MCI_MAR26_01042026.sql / MCI_MAR26_01042026.xlsx
 *
 * CATATAN:
 *  - Model ini READ-ONLY (lihat trait ReadOnlyModel di MciBaseModel).
 *  - Koneksi default `dashboard_data` (SQL Server), otomatis ke DB bulan terbaru.
 *  - Kolom dengan spasi/karakter khusus diakses via:
 *    $model->getAttribute('NAMA KOLOM')  atau  $model->{'NAMA KOLOM'}.
 *
 * @property string $periode type: char(6)
 * @property string $nocif type: char(9)
 * @property string|null $aset type: numeric(9)
 * @property string|null $aset_lcr type: numeric(9)
 * @property string|null $kasbank type: numeric(9)
 * @property string|null $piutang type: numeric(9)
 * @property string|null $invest type: numeric(9)
 * @property string|null $aset_lcr_lain type: numeric(9)
 * @property string|null $aset_kl type: numeric(9)
 * @property string|null $piutang_kl type: numeric(9)
 * @property string|null $invest_kl type: numeric(9)
 * @property string|null $aset_kl_lain type: numeric(9)
 * @property string|null $liab type: numeric(9)
 * @property string|null $liab_pdk type: numeric(9)
 * @property string|null $pinj_pdk type: numeric(9)
 * @property string|null $utangush_pdk type: numeric(9)
 * @property string|null $liab_pdk_lain type: numeric(9)
 * @property string|null $liab_pjg type: numeric(9)
 * @property string|null $pinj_pjg type: numeric(9)
 * @property string|null $utangush_pjg type: numeric(9)
 * @property string|null $liab_pjg_lain type: numeric(9)
 * @property string|null $ekuitas type: numeric(9)
 * @property string|null $pend_ush type: numeric(9)
 * @property string|null $beban_pok_ops type: numeric(9)
 * @property string|null $lr_bruto type: numeric(9)
 * @property string|null $pend_lain type: numeric(9)
 * @property string|null $beban_lain type: numeric(9)
 * @property string|null $lr_sblm_tax type: numeric(9)
 * @property string|null $lr_berjln type: numeric(9)
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
class Mcifdocfs extends MciBaseModel
{
    /**
     * Nama tabel (case-sensitive di SQL Server).
     */
    protected $table = 'MCIFDOCFS';

    /**
     * Daftar LENGKAP kolom sesuai database (39 kolom).
     * Model ini read-only, $fillable hanya untuk dokumentasi & IDE helper.
     *
     * @var array<int,string>
     */
    protected $fillable = [
        'periode',
        'nocif',
        'aset',
        'aset_lcr',
        'kasbank',
        'piutang',
        'invest',
        'aset_lcr_lain',
        'aset_kl',
        'piutang_kl',
        'invest_kl',
        'aset_kl_lain',
        'liab',
        'liab_pdk',
        'pinj_pdk',
        'utangush_pdk',
        'liab_pdk_lain',
        'liab_pjg',
        'pinj_pjg',
        'utangush_pjg',
        'liab_pjg_lain',
        'ekuitas',
        'pend_ush',
        'beban_pok_ops',
        'lr_bruto',
        'pend_lain',
        'beban_lain',
        'lr_sblm_tax',
        'lr_berjln',
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
        'aset' => 'decimal:2',
        'aset_lcr' => 'decimal:2',
        'kasbank' => 'decimal:2',
        'piutang' => 'decimal:2',
        'invest' => 'decimal:2',
        'aset_lcr_lain' => 'decimal:2',
        'aset_kl' => 'decimal:2',
        'piutang_kl' => 'decimal:2',
        'invest_kl' => 'decimal:2',
        'aset_kl_lain' => 'decimal:2',
        'liab' => 'decimal:2',
        'liab_pdk' => 'decimal:2',
        'pinj_pdk' => 'decimal:2',
        'utangush_pdk' => 'decimal:2',
        'liab_pdk_lain' => 'decimal:2',
        'liab_pjg' => 'decimal:2',
        'pinj_pjg' => 'decimal:2',
        'utangush_pjg' => 'decimal:2',
        'liab_pjg_lain' => 'decimal:2',
        'ekuitas' => 'decimal:2',
        'pend_ush' => 'decimal:2',
        'beban_pok_ops' => 'decimal:2',
        'lr_bruto' => 'decimal:2',
        'pend_lain' => 'decimal:2',
        'beban_lain' => 'decimal:2',
        'lr_sblm_tax' => 'decimal:2',
        'lr_berjln' => 'decimal:2',
    ];
}
