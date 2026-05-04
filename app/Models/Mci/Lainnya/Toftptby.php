<?php

declare(strict_types=1);

namespace App\Models\Mci\Lainnya;

use App\Models\Mci\MciBaseModel;

/**
 * Model MCI: TOFTPTBY
 * --------------------------------------------------------------------------
 * Domain   : Lainnya
 * Tabel    : [dbo].[TOFTPTBY]
 * Kolom    : 25
 * Sumber   : Mapping_MCI_MAR26_01042026.sql / MCI_MAR26_01042026.xlsx
 *
 * CATATAN:
 *  - Model ini READ-ONLY (lihat trait ReadOnlyModel di MciBaseModel).
 *  - Koneksi default `dashboard_data` (SQL Server), otomatis ke DB bulan terbaru.
 *  - Kolom dengan spasi/karakter khusus diakses via:
 *    $model->getAttribute('NAMA KOLOM')  atau  $model->{'NAMA KOLOM'}.
 *
 * @property string|null $kdpt type: varchar(10)
 * @property string|null $kdbiaya type: varchar(20)
 * @property string|null $ket type: varchar(50)
 * @property string|null $norek type: varchar(11)
 * @property string|null $kdtiket type: varchar(2)
 * @property string|null $sbbttp type: varchar(11)
 * @property string|null $norekutama type: varchar(11)
 * @property string|null $disc1 type: numeric(5)
 * @property string|null $disc2 type: numeric(5)
 * @property string|null $disc3 type: numeric(5)
 * @property string|null $stscicil type: varchar(1)
 * @property string|null $maxcicil type: numeric(5)
 * @property string|null $tglmaxbayar type: varchar(8)
 * @property string|null $stsrec type: varchar(1)
 * @property string|null $inpuser type: varchar(10)
 * @property string|null $inptgljam type: varchar(14)
 * @property string|null $inpterm type: varchar(10)
 * @property string|null $chguser type: varchar(10)
 * @property string|null $chgtgljam type: varchar(14)
 * @property string|null $chgterm type: varchar(10)
 * @property string|null $autuser type: varchar(10)
 * @property string|null $auttgljam type: varchar(14)
 * @property string|null $autterm type: varchar(10)
 * @property string|null $bytrx type: varchar(9)
 * @property string|null $sbbpendtrx type: varchar(7)
 */
class Toftptby extends MciBaseModel
{
    /**
     * Nama tabel (case-sensitive di SQL Server).
     */
    protected $table = 'TOFTPTBY';

    /**
     * Daftar LENGKAP kolom sesuai database (25 kolom).
     * Model ini read-only, $fillable hanya untuk dokumentasi & IDE helper.
     *
     * @var array<int,string>
     */
    protected $fillable = [
        'kdpt',
        'kdbiaya',
        'ket',
        'norek',
        'kdtiket',
        'sbbttp',
        'norekutama',
        'disc1',
        'disc2',
        'disc3',
        'stscicil',
        'maxcicil',
        'tglmaxbayar',
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
        'bytrx',
        'sbbpendtrx',
    ];

    /**
     * Casting tipe kolom (non-string saja).
     *
     * @var array<string,string>
     */
    protected $casts = [
        'disc1' => 'decimal:2',
        'disc2' => 'decimal:2',
        'disc3' => 'decimal:2',
        'maxcicil' => 'decimal:2',
    ];
}
