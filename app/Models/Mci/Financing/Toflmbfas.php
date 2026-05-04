<?php

declare(strict_types=1);

namespace App\Models\Mci\Financing;

use App\Models\Mci\MciBaseModel;

/**
 * Model MCI: TOFLMBFAS
 * --------------------------------------------------------------------------
 * Domain   : Financing / Loan
 * Tabel    : [dbo].[TOFLMBFAS]
 * Kolom    : 118
 * Sumber   : Mapping_MCI_MAR26_01042026.sql / MCI_MAR26_01042026.xlsx
 *
 * CATATAN:
 *  - Model ini READ-ONLY (lihat trait ReadOnlyModel di MciBaseModel).
 *  - Koneksi default `dashboard_data` (SQL Server), otomatis ke DB bulan terbaru.
 *  - Kolom dengan spasi/karakter khusus diakses via:
 *    $model->getAttribute('NAMA KOLOM')  atau  $model->{'NAMA KOLOM'}.
 *
 * @property string $nokontrak type: varchar(11)
 * @property string|null $noakad type: varchar(50)
 * @property string|null $nocif type: varchar(9)
 * @property string|null $terkait type: varchar(1)
 * @property string|null $nofas type: varchar(11)
 * @property string|null $nama type: varchar(50)
 * @property string|null $kdprd type: varchar(2)
 * @property string|null $pokpby type: varchar(2)
 * @property string|null $kdcol type: varchar(2)
 * @property string|null $terikat type: varchar(1)
 * @property string|null $kdcab type: varchar(3)
 * @property string|null $kdloc type: varchar(2)
 * @property string|null $kdkas type: varchar(2)
 * @property string|null $mdlawal type: numeric(9)
 * @property string|null $mgnawal type: numeric(9)
 * @property string|null $osmdlold type: numeric(9)
 * @property string|null $osmgnold type: numeric(9)
 * @property string|null $mtsmdl type: numeric(9)
 * @property string|null $mtsmgn type: numeric(9)
 * @property string|null $osmdlc type: numeric(9)
 * @property string|null $osmgnc type: numeric(9)
 * @property string|null $osmdlref type: numeric(9)
 * @property string|null $tglakad type: varchar(8)
 * @property string|null $jw type: numeric(5)
 * @property string|null $kdjw type: varchar(1)
 * @property string|null $tgleff type: varchar(8)
 * @property string|null $tglexp type: varchar(8)
 * @property string|null $kdtahap type: varchar(1)
 * @property string|null $tahap type: numeric(5)
 * @property string|null $tahapke type: numeric(5)
 * @property string|null $gpmdl type: numeric(5)
 * @property string|null $gpmgn type: numeric(5)
 * @property string|null $frekmdl type: numeric(5)
 * @property string|null $frekmgn type: numeric(5)
 * @property string|null $angsmdl type: numeric(9)
 * @property string|null $mdlcust type: numeric(9)
 * @property string|null $mdlbank type: numeric(9)
 * @property string|null $nbhbank type: numeric(5)
 * @property string|null $kdbaghas type: varchar(1)
 * @property string|null $baghas type: numeric(9)
 * @property string|null $kdtagih type: varchar(1)
 * @property string|null $ddtagih type: varchar(2)
 * @property string|null $tgltagiha type: varchar(8)
 * @property string|null $tgltagih type: varchar(8)
 * @property string|null $tgltagihn type: varchar(8)
 * @property string|null $rateeff type: numeric(5)
 * @property string|null $rateflat type: numeric(5)
 * @property string|null $sbb01 type: varchar(11)
 * @property string|null $sbb02 type: varchar(11)
 * @property string|null $sbb03 type: varchar(11)
 * @property string|null $sbb04 type: varchar(11)
 * @property string|null $sbb05 type: varchar(11)
 * @property string|null $sbb06 type: varchar(11)
 * @property string|null $acdrop type: varchar(11)
 * @property string|null $acpok type: varchar(11)
 * @property string|null $acbaghas type: varchar(25)
 * @property string|null $kdaoh type: varchar(8)
 * @property string|null $kdaop type: varchar(8)
 * @property string|null $kdwil type: varchar(3)
 * @property string|null $glb type: varchar(2)
 * @property string|null $segmen type: varchar(3)
 * @property string|null $kdreg type: varchar(10)
 * @property string|null $nilaijam type: numeric(9)
 * @property string|null $collama type: varchar(1)
 * @property string|null $colbaru type: varchar(1)
 * @property string|null $coleomlalu type: varchar(1)
 * @property string|null $coleom type: varchar(1)
 * @property string|null $coleoy type: varchar(1)
 * @property string|null $tgljtcol type: varchar(8)
 * @property string|null $tglubah type: varchar(8)
 * @property string|null $sekon type: varchar(5)
 * @property string|null $sifat type: varchar(5)
 * @property string|null $gunadeb type: varchar(5)
 * @property string|null $goldeb type: varchar(5)
 * @property string|null $goljamin type: varchar(5)
 * @property string|null $bagjamin type: varchar(5)
 * @property string|null $kddnd type: varchar(1)
 * @property string|null $dnd type: numeric(9)
 * @property string|null $tgllunas type: varchar(8)
 * @property string|null $mdleom type: numeric(9)
 * @property string|null $mgneom type: numeric(9)
 * @property string|null $mdleoy type: numeric(9)
 * @property string|null $mgneoy type: numeric(9)
 * @property string|null $ststrn type: varchar(10)
 * @property string|null $stsacc type: varchar(1)
 * @property string|null $ppap type: numeric(9)
 * @property string|null $stsrest type: varchar(1)
 * @property string|null $tgkmdl type: numeric(9)
 * @property string|null $tgkmgn type: numeric(9)
 * @property string|null $blntgkmdl type: numeric(5)
 * @property string|null $blntgkmgn type: numeric(5)
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
 * @property string|null $kdsi type: varchar(1)
 * @property string|null $kdcolint type: varchar(2)
 * @property string|null $collamaint type: varchar(1)
 * @property string|null $colbaruint type: varchar(1)
 * @property string|null $coleomint type: varchar(1)
 * @property string|null $jnspemb type: varchar(2)
 * @property string|null $sekonsid type: varchar(4)
 * @property string|null $jnsusaha type: varchar(50)
 * @property string|null $gunasid type: varchar(4)
 * @property string|null $sid_flag type: varchar(1)
 * @property string|null $kdrate type: varchar(1)
 * @property string|null $golpiutang type: varchar(1)
 * @property string|null $kdtujuan type: varchar(2)
 * @property string|null $sifatplafond type: varchar(1)
 * @property string|null $tglwo type: varchar(8)
 * @property string|null $sifatsid type: varchar(5)
 * @property string|null $kdkolek type: varchar(10)
 */
class Toflmbfas extends MciBaseModel
{
    /**
     * Nama tabel (case-sensitive di SQL Server).
     */
    protected $table = 'TOFLMBFAS';

    /**
     * Daftar LENGKAP kolom sesuai database (118 kolom).
     * Model ini read-only, $fillable hanya untuk dokumentasi & IDE helper.
     *
     * @var array<int,string>
     */
    protected $fillable = [
        'nokontrak',
        'noakad',
        'nocif',
        'terkait',
        'nofas',
        'nama',
        'kdprd',
        'pokpby',
        'kdcol',
        'terikat',
        'kdcab',
        'kdloc',
        'kdkas',
        'mdlawal',
        'mgnawal',
        'osmdlold',
        'osmgnold',
        'mtsmdl',
        'mtsmgn',
        'osmdlc',
        'osmgnc',
        'osmdlref',
        'tglakad',
        'jw',
        'kdjw',
        'tgleff',
        'tglexp',
        'kdtahap',
        'tahap',
        'tahapke',
        'gpmdl',
        'gpmgn',
        'frekmdl',
        'frekmgn',
        'angsmdl',
        'mdlcust',
        'mdlbank',
        'nbhbank',
        'kdbaghas',
        'baghas',
        'kdtagih',
        'ddtagih',
        'tgltagiha',
        'tgltagih',
        'tgltagihn',
        'rateeff',
        'rateflat',
        'sbb01',
        'sbb02',
        'sbb03',
        'sbb04',
        'sbb05',
        'sbb06',
        'acdrop',
        'acpok',
        'acbaghas',
        'kdaoh',
        'kdaop',
        'kdwil',
        'glb',
        'segmen',
        'kdreg',
        'nilaijam',
        'collama',
        'colbaru',
        'coleomlalu',
        'coleom',
        'coleoy',
        'tgljtcol',
        'tglubah',
        'sekon',
        'sifat',
        'gunadeb',
        'goldeb',
        'goljamin',
        'bagjamin',
        'kddnd',
        'dnd',
        'tgllunas',
        'mdleom',
        'mgneom',
        'mdleoy',
        'mgneoy',
        'ststrn',
        'stsacc',
        'ppap',
        'stsrest',
        'tgkmdl',
        'tgkmgn',
        'blntgkmdl',
        'blntgkmgn',
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
        'kdsi',
        'kdcolint',
        'collamaint',
        'colbaruint',
        'coleomint',
        'jnspemb',
        'sekonsid',
        'jnsusaha',
        'gunasid',
        'sid_flag',
        'kdrate',
        'golpiutang',
        'kdtujuan',
        'sifatplafond',
        'tglwo',
        'sifatsid',
        'kdkolek',
    ];

    /**
     * Casting tipe kolom (non-string saja).
     *
     * @var array<string,string>
     */
    protected $casts = [
        'mdlawal' => 'decimal:2',
        'mgnawal' => 'decimal:2',
        'osmdlold' => 'decimal:2',
        'osmgnold' => 'decimal:2',
        'mtsmdl' => 'decimal:2',
        'mtsmgn' => 'decimal:2',
        'osmdlc' => 'decimal:2',
        'osmgnc' => 'decimal:2',
        'osmdlref' => 'decimal:2',
        'jw' => 'decimal:2',
        'tahap' => 'decimal:2',
        'tahapke' => 'decimal:2',
        'gpmdl' => 'decimal:2',
        'gpmgn' => 'decimal:2',
        'frekmdl' => 'decimal:2',
        'frekmgn' => 'decimal:2',
        'angsmdl' => 'decimal:2',
        'mdlcust' => 'decimal:2',
        'mdlbank' => 'decimal:2',
        'nbhbank' => 'decimal:2',
        'baghas' => 'decimal:2',
        'rateeff' => 'decimal:2',
        'rateflat' => 'decimal:2',
        'nilaijam' => 'decimal:2',
        'dnd' => 'decimal:2',
        'mdleom' => 'decimal:2',
        'mgneom' => 'decimal:2',
        'mdleoy' => 'decimal:2',
        'mgneoy' => 'decimal:2',
        'ppap' => 'decimal:2',
        'tgkmdl' => 'decimal:2',
        'tgkmgn' => 'decimal:2',
        'blntgkmdl' => 'decimal:2',
        'blntgkmgn' => 'decimal:2',
    ];
}
