<?php

namespace App\Models\Mci\Deposito;

use App\Models\Mci\MciBaseModel;

/**
 * Model MCI: SETUPDEP
 * --------------------------------------------------------------------------
 * Domain   : Deposito
 * Tabel    : [dbo].[SETUPDEP]
 * Kolom    : 62
 * Sumber   : Mapping_MCI_MAR26_01042026.sql / MCI_MAR26_01042026.xlsx
 *
 * CATATAN:
 *  - Model ini READ-ONLY (lihat trait ReadOnlyModel di MciBaseModel).
 *  - Koneksi default `dashboard_data` (SQL Server), otomatis ke DB bulan terbaru.
 *  - Kolom dengan spasi/karakter khusus diakses via:
 *    $model->getAttribute('NAMA KOLOM')  atau  $model->{'NAMA KOLOM'}.
 *
 * @property string $kdprd  type: varchar(2)
 * @property string|null $singkat  type: varchar(10)
 * @property string|null $ket  type: varchar(30)
 * @property string|null $masa  type: numeric(5)
 * @property string|null $jnsmasa  type: varchar(1)
 * @property string|null $maxnotax  type: numeric(9)
 * @property string|null $tax  type: numeric(5)
 * @property string|null $maxnbh  type: numeric(5)
 * @property string|null $mtdcadnb  type: varchar(1)
 * @property string|null $bbtdana  type: numeric(5)
 * @property string|null $cc  type: varchar(3)
 * @property string|null $stsnbh  type: varchar(1)
 * @property string|null $nbheom  type: varchar(1)
 * @property string|null $bbscair  type: varchar(1)
 * @property string|null $stspena  type: varchar(1)
 * @property string|null $htgpena  type: varchar(1)
 * @property string|null $stsbaghas  type: varchar(1)
 * @property string|null $minnom  type: numeric(9)
 * @property string|null $maxnom  type: numeric(9)
 * @property string|null $hari1  type: numeric(5)
 * @property string|null $hari2  type: numeric(5)
 * @property string|null $hari3  type: numeric(5)
 * @property string|null $hari4  type: numeric(5)
 * @property string|null $hari5  type: numeric(5)
 * @property string|null $penal1  type: numeric(9)
 * @property string|null $penal2  type: numeric(9)
 * @property string|null $penal3  type: numeric(9)
 * @property string|null $penal4  type: numeric(9)
 * @property string|null $penal5  type: numeric(9)
 * @property string|null $minpenal  type: numeric(5)
 * @property string|null $gldep  type: varchar(7)
 * @property string|null $glnbh  type: varchar(7)
 * @property string|null $gltax  type: varchar(7)
 * @property string|null $glmatera  type: varchar(7)
 * @property string|null $glcadnbh  type: varchar(7)
 * @property string|null $glzakat  type: varchar(7)
 * @property string|null $glinfaq  type: varchar(7)
 * @property string|null $gldepcai  type: varchar(7)
 * @property string|null $glnbhcai  type: varchar(7)
 * @property string|null $glpenalt  type: varchar(7)
 * @property string|null $gldepjt  type: varchar(7)
 * @property string|null $glnbhjt  type: varchar(7)
 * @property string|null $glttpbh  type: varchar(7)
 * @property string|null $glttptunai  type: varchar(7)
 * @property string|null $glttptransfer  type: varchar(7)
 * @property string|null $stsrec  type: varchar(1)
 * @property string|null $stsrate  type: varchar(1)
 * @property string $inpuser  type: varchar(10)
 * @property string|null $inptgl  type: varchar(14)
 * @property string|null $inpterm  type: varchar(10)
 * @property string|null $chguser  type: varchar(10)
 * @property string|null $chgtgl  type: varchar(14)
 * @property string|null $chgterm  type: varchar(10)
 * @property string|null $autuser  type: varchar(10)
 * @property string|null $auttgl  type: varchar(14)
 * @property string|null $autterm  type: varchar(10)
 * @property string|null $stsmud  type: varchar(1)
 * @property string|null $stsresidep  type: varchar(1)
 * @property string|null $glbhcair  type: varchar(7)
 * @property string|null $stsround  type: char(1)
 * @property string|null $glsdq  type: varchar(7)
 * @property string|null $glwakaf  type: varchar(7)
 */
class Setupdep extends MciBaseModel
{
    /**
     * Nama tabel (case-sensitive di SQL Server).
     */
    protected $table = 'SETUPDEP';

    /**
     * Daftar LENGKAP kolom sesuai database (62 kolom).
     * Model ini read-only, $fillable hanya untuk dokumentasi & IDE helper.
     *
     * @var array<int,string>
     */
    protected $fillable = [
        'kdprd',
        'singkat',
        'ket',
        'masa',
        'jnsmasa',
        'maxnotax',
        'tax',
        'maxnbh',
        'mtdcadnb',
        'bbtdana',
        'cc',
        'stsnbh',
        'nbheom',
        'bbscair',
        'stspena',
        'htgpena',
        'stsbaghas',
        'minnom',
        'maxnom',
        'hari1',
        'hari2',
        'hari3',
        'hari4',
        'hari5',
        'penal1',
        'penal2',
        'penal3',
        'penal4',
        'penal5',
        'minpenal',
        'gldep',
        'glnbh',
        'gltax',
        'glmatera',
        'glcadnbh',
        'glzakat',
        'glinfaq',
        'gldepcai',
        'glnbhcai',
        'glpenalt',
        'gldepjt',
        'glnbhjt',
        'glttpbh',
        'glttptunai',
        'glttptransfer',
        'stsrec',
        'stsrate',
        'inpuser',
        'inptgl',
        'inpterm',
        'chguser',
        'chgtgl',
        'chgterm',
        'autuser',
        'auttgl',
        'autterm',
        'stsmud',
        'stsresidep',
        'glbhcair',
        'stsround',
        'glsdq',
        'glwakaf',
    ];

    /**
     * Casting tipe kolom (non-string saja).
     *
     * @var array<string,string>
     */
    protected $casts = [
        'masa' => 'decimal:2',
        'maxnotax' => 'decimal:2',
        'tax' => 'decimal:2',
        'maxnbh' => 'decimal:2',
        'bbtdana' => 'decimal:2',
        'minnom' => 'decimal:2',
        'maxnom' => 'decimal:2',
        'hari1' => 'decimal:2',
        'hari2' => 'decimal:2',
        'hari3' => 'decimal:2',
        'hari4' => 'decimal:2',
        'hari5' => 'decimal:2',
        'penal1' => 'decimal:2',
        'penal2' => 'decimal:2',
        'penal3' => 'decimal:2',
        'penal4' => 'decimal:2',
        'penal5' => 'decimal:2',
        'minpenal' => 'decimal:2',
    ];
}
