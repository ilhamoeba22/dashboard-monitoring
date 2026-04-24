<?php

namespace App\Models\Mci\Ppap;

use App\Models\Mci\MciBaseModel;

/**
 * Model MCI: M_PPAP
 * --------------------------------------------------------------------------
 * Domain   : PPAP / DPD / Coll
 * Tabel    : [dbo].[M_PPAP]
 * Kolom    : 43
 * Sumber   : Mapping_MCI_MAR26_01042026.sql / MCI_MAR26_01042026.xlsx
 *
 * CATATAN:
 *  - Model ini READ-ONLY (lihat trait ReadOnlyModel di MciBaseModel).
 *  - Koneksi default `dashboard_data` (SQL Server), otomatis ke DB bulan terbaru.
 *  - Kolom dengan spasi/karakter khusus diakses via:
 *    $model->getAttribute('NAMA KOLOM')  atau  $model->{'NAMA KOLOM'}.
 *
 * @property string $thn  type: char(4)
 * @property string $bln  type: char(2)
 * @property string $noacc  type: char(11)
 * @property string|null $nama  type: char(30)
 * @property string|null $kdbank  type: char(3)
 * @property string|null $kdcab  type: char(2)
 * @property string|null $kdloc  type: char(2)
 * @property string|null $kdprd  type: char(2)
 * @property string|null $sifat  type: char(2)
 * @property string|null $guna  type: char(1)
 * @property string|null $tglmulai  type: char(8)
 * @property string|null $tgljt  type: char(8)
 * @property string|null $masa  type: numeric(5)
 * @property string|null $periode  type: char(1)
 * @property string|null $coll  type: char(1)
 * @property string|null $goldeb  type: char(3)
 * @property string|null $sekon  type: char(4)
 * @property string|null $rate  type: numeric(5)
 * @property string|null $carahtg  type: char(1)
 * @property string|null $goljam  type: char(3)
 * @property string|null $bagjam  type: char(4)
 * @property string|null $plafond  type: numeric(9)
 * @property string|null $os  type: numeric(9)
 * @property string|null $tgkpok  type: numeric(9)
 * @property string|null $tgkbng  type: numeric(9)
 * @property string|null $jnsagun  type: char(20)
 * @property string|null $taksasi  type: numeric(9)
 * @property string|null $bobot  type: numeric(5)
 * @property string|null $dptguna  type: numeric(9)
 * @property string|null $htgagun  type: numeric(9)
 * @property string|null $ppapwd  type: numeric(9)
 * @property string|null $noreg  type: varchar(5)
 * @property string|null $pokpby  type: varchar(2)
 * @property string|null $taksasibank  type: numeric(9)
 * @property string|null $inpuser  type: char(10)
 * @property string|null $inptgljam  type: char(14)
 * @property string|null $inpterm  type: char(10)
 * @property string|null $chguser  type: char(10)
 * @property string|null $chgtgljam  type: char(14)
 * @property string|null $chgterm  type: char(10)
 * @property string|null $autuser  type: char(10)
 * @property string|null $auttgljam  type: char(14)
 * @property string|null $autterm  type: char(10)
 */
class MPpap extends MciBaseModel
{
    /**
     * Nama tabel (case-sensitive di SQL Server).
     */
    protected $table = 'M_PPAP';

    /**
     * Daftar LENGKAP kolom sesuai database (43 kolom).
     * Model ini read-only, $fillable hanya untuk dokumentasi & IDE helper.
     *
     * @var array<int,string>
     */
    protected $fillable = [
        'thn',
        'bln',
        'noacc',
        'nama',
        'kdbank',
        'kdcab',
        'kdloc',
        'kdprd',
        'sifat',
        'guna',
        'tglmulai',
        'tgljt',
        'masa',
        'periode',
        'coll',
        'goldeb',
        'sekon',
        'rate',
        'carahtg',
        'goljam',
        'bagjam',
        'plafond',
        'os',
        'tgkpok',
        'tgkbng',
        'jnsagun',
        'taksasi',
        'bobot',
        'dptguna',
        'htgagun',
        'ppapwd',
        'noreg',
        'pokpby',
        'taksasibank',
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
        'masa' => 'decimal:2',
        'rate' => 'decimal:2',
        'plafond' => 'decimal:2',
        'os' => 'decimal:2',
        'tgkpok' => 'decimal:2',
        'tgkbng' => 'decimal:2',
        'taksasi' => 'decimal:2',
        'bobot' => 'decimal:2',
        'dptguna' => 'decimal:2',
        'htgagun' => 'decimal:2',
        'ppapwd' => 'decimal:2',
        'taksasibank' => 'decimal:2',
    ];
}
