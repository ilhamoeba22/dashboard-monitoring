<?php

namespace App\Models\Mci\Deposito;

use App\Models\Mci\MciBaseModel;

/**
 * Model MCI: TOFDEPBDD
 * --------------------------------------------------------------------------
 * Domain   : Deposito
 * Tabel    : [dbo].[TOFDEPBDD]
 * Kolom    : 19
 * Sumber   : Mapping_MCI_MAR26_01042026.sql / MCI_MAR26_01042026.xlsx
 *
 * CATATAN:
 *  - Model ini READ-ONLY (lihat trait ReadOnlyModel di MciBaseModel).
 *  - Koneksi default `dashboard_data` (SQL Server), otomatis ke DB bulan terbaru.
 *  - Kolom dengan spasi/karakter khusus diakses via:
 *    $model->getAttribute('NAMA KOLOM')  atau  $model->{'NAMA KOLOM'}.
 *
 * @property string|null $nodep  type: char(11)
 * @property string|null $nombdd  type: numeric(9)
 * @property string|null $realbdd  type: numeric(9)
 * @property string|null $ratebdd  type: numeric(5)
 * @property string|null $raterealbdd  type: numeric(5)
 * @property string|null $sbbbdd  type: char(7)
 * @property string|null $rekpenerima  type: char(11)
 * @property string|null $ketbdd  type: char(150)
 * @property string|null $stsrec  type: char(1)
 * @property string|null $ststrn  type: char(1)
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
class Tofdepbdd extends MciBaseModel
{
    /**
     * Nama tabel (case-sensitive di SQL Server).
     */
    protected $table = 'TOFDEPBDD';

    /**
     * Daftar LENGKAP kolom sesuai database (19 kolom).
     * Model ini read-only, $fillable hanya untuk dokumentasi & IDE helper.
     *
     * @var array<int,string>
     */
    protected $fillable = [
        'nodep',
        'nombdd',
        'realbdd',
        'ratebdd',
        'raterealbdd',
        'sbbbdd',
        'rekpenerima',
        'ketbdd',
        'stsrec',
        'ststrn',
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
        'nombdd' => 'decimal:2',
        'realbdd' => 'decimal:2',
        'ratebdd' => 'decimal:2',
        'raterealbdd' => 'decimal:2',
    ];
}
