<?php

declare(strict_types=1);

namespace App\Models\Mci\Financing;

use App\Models\Mci\MciBaseModel;

/**
 * Model MCI: TOFLMBHPX
 * --------------------------------------------------------------------------
 * Domain   : Financing / Loan
 * Tabel    : [dbo].[TOFLMBHPX]
 * Kolom    : 60
 * Sumber   : Mapping_MCI_MAR26_01042026.sql / MCI_MAR26_01042026.xlsx
 *
 * CATATAN:
 *  - Model ini READ-ONLY (lihat trait ReadOnlyModel di MciBaseModel).
 *  - Koneksi default `dashboard_data` (SQL Server), otomatis ke DB bulan terbaru.
 *  - Kolom dengan spasi/karakter khusus diakses via:
 *    $model->getAttribute('NAMA KOLOM')  atau  $model->{'NAMA KOLOM'}.
 *
 * @property string $nokontrak type: varchar(11)
 * @property string|null $ke type: numeric(5)
 * @property string|null $kdrest type: varchar(1)
 * @property string|null $jwo type: numeric(5)
 * @property string|null $jwn type: numeric(5)
 * @property string|null $jto type: varchar(8)
 * @property string|null $jtn type: varchar(8)
 * @property string|null $teo type: varchar(8)
 * @property string|null $ten type: varchar(8)
 * @property string|null $bmso type: numeric(5)
 * @property string|null $bmsn type: numeric(5)
 * @property string|null $osmdl type: numeric(9)
 * @property string|null $osmdln type: numeric(9)
 * @property string|null $osbmso type: numeric(9)
 * @property string|null $osbmsn type: numeric(9)
 * @property string|null $ket type: varchar(150)
 * @property string|null $noakado type: varchar(50)
 * @property string|null $noakadn type: varchar(50)
 * @property string|null $tglakado type: varchar(8)
 * @property string|null $tglakadn type: varchar(8)
 * @property string|null $kdprdo type: varchar(2)
 * @property string|null $kdprdn type: varchar(2)
 * @property string|null $stsrecn type: varchar(1)
 * @property string|null $stsrec type: varchar(1)
 * @property string|null $ststrn type: varchar(1)
 * @property string|null $inpuser type: varchar(10)
 * @property string|null $inptgl type: varchar(14)
 * @property string|null $inpterm type: varchar(10)
 * @property string|null $chguser type: varchar(10)
 * @property string|null $chgtgl type: varchar(14)
 * @property string|null $chgterm type: varchar(10)
 * @property string|null $autuser type: varchar(10)
 * @property string|null $auttgl type: varchar(14)
 * @property string|null $autterm type: varchar(10)
 * @property string|null $kdcararest type: char(2)
 * @property string|null $gppoko type: numeric(5)
 * @property string|null $frekpoko type: numeric(5)
 * @property string|null $intvpoko type: numeric(5)
 * @property string|null $gppokn type: numeric(5)
 * @property string|null $frekpokn type: numeric(5)
 * @property string|null $intvpokn type: numeric(5)
 * @property string|null $gpmgno type: numeric(5)
 * @property string|null $frekmgno type: numeric(5)
 * @property string|null $intvmgno type: numeric(5)
 * @property string|null $gpmgnn type: numeric(5)
 * @property string|null $frekmgnn type: numeric(5)
 * @property string|null $intvmgnn type: numeric(5)
 * @property string|null $pokpbyo type: char(2)
 * @property string|null $pokpbyn type: char(2)
 * @property string|null $colold type: char(1)
 * @property string|null $colnew type: char(1)
 * @property string|null $angsmdlo type: numeric(9)
 * @property string|null $angsmgno type: numeric(9)
 * @property string|null $angsmdln type: numeric(9)
 * @property string|null $angsmgnn type: numeric(9)
 * @property string|null $dpdo type: numeric(5)
 * @property string|null $biaya01 type: numeric(9)
 * @property string|null $biaya02 type: numeric(9)
 * @property string|null $kdbiaya01 type: numeric(9)
 * @property string|null $kdbiaya02 type: numeric(9)
 */
class Toflmbhpx extends MciBaseModel
{
    /**
     * Nama tabel (case-sensitive di SQL Server).
     */
    protected $table = 'TOFLMBHPX';

    /**
     * Daftar LENGKAP kolom sesuai database (60 kolom).
     * Model ini read-only, $fillable hanya untuk dokumentasi & IDE helper.
     *
     * @var array<int,string>
     */
    protected $fillable = [
        'nokontrak',
        'ke',
        'kdrest',
        'jwo',
        'jwn',
        'jto',
        'jtn',
        'teo',
        'ten',
        'bmso',
        'bmsn',
        'osmdl',
        'osmdln',
        'osbmso',
        'osbmsn',
        'ket',
        'noakado',
        'noakadn',
        'tglakado',
        'tglakadn',
        'kdprdo',
        'kdprdn',
        'stsrecn',
        'stsrec',
        'ststrn',
        'inpuser',
        'inptgl',
        'inpterm',
        'chguser',
        'chgtgl',
        'chgterm',
        'autuser',
        'auttgl',
        'autterm',
        'kdcararest',
        'gppoko',
        'frekpoko',
        'intvpoko',
        'gppokn',
        'frekpokn',
        'intvpokn',
        'gpmgno',
        'frekmgno',
        'intvmgno',
        'gpmgnn',
        'frekmgnn',
        'intvmgnn',
        'pokpbyo',
        'pokpbyn',
        'colold',
        'colnew',
        'angsmdlo',
        'angsmgno',
        'angsmdln',
        'angsmgnn',
        'dpdo',
        'biaya01',
        'biaya02',
        'kdbiaya01',
        'kdbiaya02',
    ];

    /**
     * Casting tipe kolom (non-string saja).
     *
     * @var array<string,string>
     */
    protected $casts = [
        'ke' => 'decimal:2',
        'jwo' => 'decimal:2',
        'jwn' => 'decimal:2',
        'bmso' => 'decimal:2',
        'bmsn' => 'decimal:2',
        'osmdl' => 'decimal:2',
        'osmdln' => 'decimal:2',
        'osbmso' => 'decimal:2',
        'osbmsn' => 'decimal:2',
        'gppoko' => 'decimal:2',
        'frekpoko' => 'decimal:2',
        'intvpoko' => 'decimal:2',
        'gppokn' => 'decimal:2',
        'frekpokn' => 'decimal:2',
        'intvpokn' => 'decimal:2',
        'gpmgno' => 'decimal:2',
        'frekmgno' => 'decimal:2',
        'intvmgno' => 'decimal:2',
        'gpmgnn' => 'decimal:2',
        'frekmgnn' => 'decimal:2',
        'intvmgnn' => 'decimal:2',
        'angsmdlo' => 'decimal:2',
        'angsmgno' => 'decimal:2',
        'angsmdln' => 'decimal:2',
        'angsmgnn' => 'decimal:2',
        'dpdo' => 'decimal:2',
        'biaya01' => 'decimal:2',
        'biaya02' => 'decimal:2',
        'kdbiaya01' => 'decimal:2',
        'kdbiaya02' => 'decimal:2',
    ];
}
