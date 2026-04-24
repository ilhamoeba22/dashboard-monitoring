<?php

namespace App\Models\Mci\Financing;

use App\Models\Mci\MciBaseModel;

/**
 * Model MCI: TOFLMB
 * --------------------------------------------------------------------------
 * Domain   : Financing / Loan
 * Tabel    : [dbo].[TOFLMB]
 * Kolom    : 220
 * Sumber   : Mapping_MCI_MAR26_01042026.sql / MCI_MAR26_01042026.xlsx
 *
 * CATATAN:
 *  - Model ini READ-ONLY (lihat trait ReadOnlyModel di MciBaseModel).
 *  - Koneksi default `dashboard_data` (SQL Server), otomatis ke DB bulan terbaru.
 *  - Kolom dengan spasi/karakter khusus diakses via:
 *    $model->getAttribute('NAMA KOLOM')  atau  $model->{'NAMA KOLOM'}.
 *
 * @property string $nokontrak  type: varchar(11)
 * @property string|null $nocif  type: varchar(9)
 * @property string|null $nocifgrp  type: varchar(9)
 * @property string|null $terkait  type: varchar(1)
 * @property string|null $nofas  type: varchar(11)
 * @property string|null $nama  type: varchar(50)
 * @property string|null $kdprd  type: varchar(2)
 * @property string|null $pokpby  type: varchar(2)
 * @property string|null $kdcol  type: varchar(2)
 * @property string|null $kdsalur  type: varchar(1)
 * @property string|null $terikat  type: varchar(1)
 * @property string|null $kdcab  type: varchar(3)
 * @property string|null $kdloc  type: varchar(2)
 * @property string|null $kdkas  type: varchar(2)
 * @property string|null $cc  type: varchar(2)
 * @property string|null $kurs  type: numeric(9)
 * @property string|null $mdlawal  type: numeric(9)
 * @property string|null $mgnawal  type: numeric(9)
 * @property string|null $osmdlold  type: numeric(9)
 * @property string|null $osmgnold  type: numeric(9)
 * @property string|null $mtsmdl  type: numeric(9)
 * @property string|null $mtsmgn  type: numeric(9)
 * @property string|null $osmdlc  type: numeric(9)
 * @property string|null $osmgnc  type: numeric(9)
 * @property string|null $osmdlcv  type: numeric(9)
 * @property string|null $osmgncv  type: numeric(9)
 * @property string|null $osmdlref  type: numeric(9)
 * @property string|null $osmdlaset  type: numeric(9)
 * @property string|null $kdaset  type: varchar(5)
 * @property string|null $noakad  type: varchar(50)
 * @property string|null $tglakadn  type: varchar(8)
 * @property string|null $tglakad  type: varchar(8)
 * @property string|null $jw  type: numeric(5)
 * @property string|null $kdjw  type: varchar(1)
 * @property string|null $tgleff  type: varchar(8)
 * @property string|null $tglexp  type: varchar(8)
 * @property string|null $tglbook  type: varchar(8)
 * @property string|null $kdtahap  type: varchar(1)
 * @property string|null $tahap  type: numeric(5)
 * @property string|null $tahapke  type: numeric(5)
 * @property string|null $gpmdl  type: numeric(5)
 * @property string|null $gpmgn  type: numeric(5)
 * @property string|null $frekmdl  type: numeric(5)
 * @property string|null $frekmgn  type: numeric(5)
 * @property string|null $angsmdl  type: numeric(9)
 * @property string|null $angsmgn  type: numeric(9)
 * @property string|null $angsmdlke  type: numeric(5)
 * @property string|null $angsmgnke  type: numeric(5)
 * @property string|null $mdlcust  type: numeric(9)
 * @property string|null $mdlbank  type: numeric(9)
 * @property string|null $nbhbank  type: numeric(5)
 * @property string|null $kdbaghas  type: varchar(1)
 * @property string|null $baghas  type: numeric(9)
 * @property string|null $baghasrg  type: numeric(9)
 * @property string|null $baghaslb  type: numeric(9)
 * @property string|null $totbaghas  type: numeric(9)
 * @property string|null $osptg  type: numeric(9)
 * @property string|null $osptgv  type: numeric(9)
 * @property string|null $kdtagih  type: varchar(1)
 * @property string|null $ddtagih  type: varchar(2)
 * @property string|null $tgltagiha  type: varchar(8)
 * @property string|null $tgltagih  type: varchar(8)
 * @property string|null $tgltagihn  type: varchar(8)
 * @property string|null $kdrs  type: varchar(1)
 * @property string|null $rateeff  type: numeric(5)
 * @property string|null $rateflat  type: numeric(5)
 * @property string|null $dnpool  type: numeric(5)
 * @property string|null $dnrkp  type: numeric(5)
 * @property string|null $dnumum  type: numeric(5)
 * @property string|null $dnrak  type: numeric(5)
 * @property string|null $dnpoolrp  type: numeric(9)
 * @property string|null $dnrkprp  type: numeric(9)
 * @property string|null $dnumumrp  type: numeric(9)
 * @property string|null $dnrakrp  type: numeric(9)
 * @property string|null $sbb01  type: varchar(11)
 * @property string|null $sbb02  type: varchar(11)
 * @property string|null $sbb03  type: varchar(11)
 * @property string|null $sbb04  type: varchar(11)
 * @property string|null $sbb05  type: varchar(11)
 * @property string|null $sbb06  type: varchar(11)
 * @property string|null $acdrop  type: varchar(11)
 * @property string|null $acpok  type: varchar(11)
 * @property string|null $acbaghas  type: varchar(25)
 * @property string|null $kdaoh  type: varchar(8)
 * @property string|null $kdaop  type: varchar(8)
 * @property string|null $kdwil  type: varchar(3)
 * @property string|null $glb  type: varchar(2)
 * @property string|null $segmen  type: varchar(3)
 * @property string|null $kdreg  type: varchar(10)
 * @property string|null $nilaijam  type: numeric(9)
 * @property string|null $collama  type: varchar(1)
 * @property string|null $colbaru  type: varchar(1)
 * @property string|null $coleomlalu  type: varchar(1)
 * @property string|null $coleom  type: varchar(1)
 * @property string|null $coleoy  type: varchar(1)
 * @property string|null $tgljtcol  type: varchar(8)
 * @property string|null $tglubah  type: varchar(8)
 * @property string|null $sekon  type: varchar(5)
 * @property string|null $sifat  type: varchar(5)
 * @property string|null $gunadeb  type: varchar(5)
 * @property string|null $goldeb  type: varchar(5)
 * @property string|null $goljamin  type: varchar(5)
 * @property string|null $bagjamin  type: varchar(5)
 * @property string|null $kddnd  type: varchar(1)
 * @property string|null $dnd  type: numeric(9)
 * @property string|null $tgllunas  type: varchar(8)
 * @property string|null $mdleom  type: numeric(9)
 * @property string|null $mgneom  type: numeric(9)
 * @property string|null $mdleoy  type: numeric(9)
 * @property string|null $mgneoy  type: numeric(9)
 * @property string|null $ststrn  type: varchar(10)
 * @property string|null $ststrnum  type: varchar(1)
 * @property string|null $stsacc  type: varchar(1)
 * @property string|null $ppap  type: numeric(9)
 * @property string|null $totanggota  type: numeric(5)
 * @property string|null $stsrest  type: varchar(1)
 * @property string|null $tgkmdl  type: numeric(9)
 * @property string|null $tgkmgn  type: numeric(9)
 * @property string|null $blntgkmdl  type: numeric(5)
 * @property string|null $blntgkmgn  type: numeric(5)
 * @property string|null $stsrec  type: varchar(1)
 * @property string|null $inpuser  type: varchar(10)
 * @property string|null $inptgl  type: varchar(14)
 * @property string|null $inpterm  type: varchar(10)
 * @property string|null $chguser  type: varchar(10)
 * @property string|null $chgtgl  type: varchar(14)
 * @property string|null $chgterm  type: varchar(10)
 * @property string|null $autuser  type: varchar(10)
 * @property string|null $auttgl  type: varchar(14)
 * @property string|null $autterm  type: varchar(10)
 * @property string|null $kdsi  type: varchar(1)
 * @property string|null $kdcolint  type: varchar(2)
 * @property string|null $collamaint  type: varchar(1)
 * @property string|null $colbaruint  type: varchar(1)
 * @property string|null $coleomint  type: varchar(1)
 * @property string|null $jnspemb  type: varchar(2)
 * @property string|null $sekonsid  type: varchar(4)
 * @property string|null $jnsusaha  type: varchar(50)
 * @property string|null $gunasid  type: varchar(4)
 * @property string|null $sid_flag  type: varchar(1)
 * @property string|null $kdrate  type: varchar(1)
 * @property string|null $golpiutang  type: varchar(1)
 * @property string|null $kdtujuan  type: varchar(2)
 * @property string|null $sifatplafond  type: varchar(1)
 * @property string|null $tglwo  type: varchar(8)
 * @property string|null $sifatsid  type: varchar(5)
 * @property string|null $kdkolek  type: varchar(10)
 * @property string|null $dp  type: numeric(9)
 * @property string|null $stswakalah  type: varchar(1)
 * @property string|null $stsaset  type: varchar(1)
 * @property string|null $acsuplier  type: varchar(11)
 * @property string|null $ststopup  type: varchar(1)
 * @property string|null $noreg  type: varchar(12)
 * @property string|null $collcur  type: varchar(1)
 * @property string|null $osmdlcur  type: numeric(9)
 * @property string|null $autoblok  type: numeric(5)
 * @property string|null $kdgroupdana  type: varchar(10)
 * @property string|null $kdgroupdeb  type: varchar(10)
 * @property string|null $batch  type: numeric(5)
 * @property string|null $setorwajib  type: numeric(9)
 * @property string|null $kdsetor  type: varchar(1)
 * @property string|null $noacsetor  type: varchar(11)
 * @property string|null $jnsagun  type: varchar(1)
 * @property string|null $htgagun  type: numeric(9)
 * @property string|null $jnsagunbi  type: varchar(10)
 * @property string|null $intervpok  type: numeric(5)
 * @property string|null $intervmgn  type: numeric(5)
 * @property string|null $kdsp  type: char(1)
 * @property string|null $tglsp  type: varchar(8)
 * @property string|null $slk_takeover  type: varchar(6)
 * @property string|null $slk_sumberdana  type: varchar(6)
 * @property string|null $slk_kdljk  type: varchar(6)
 * @property string|null $slk_nilaiproyek  type: numeric(9)
 * @property string|null $slk_dati2proyek  type: varchar(4)
 * @property string|null $slk_sifat  type: varchar(1)
 * @property string|null $slk_jnspemb  type: varchar(5)
 * @property string|null $slk_gunadeb  type: varchar(1)
 * @property string|null $slk_orienguna  type: varchar(1)
 * @property string|null $slk_sekon  type: varchar(6)
 * @property string|null $slk_kategorideb  type: varchar(2)
 * @property string|null $slk_progpem  type: varchar(3)
 * @property string|null $slk_kdrest  type: varchar(2)
 * @property string|null $slk_kdlunas  type: varchar(2)
 * @property string|null $stscif_1  type: char(1)
 * @property string|null $stscif_2  type: char(1)
 * @property string|null $stsloan_1  type: char(1)
 * @property string|null $stsloan_2  type: char(1)
 * @property string|null $slk_goljam  type: varchar(10)
 * @property string|null $kdatmro  type: char(3)
 * @property string|null $kdatmrn  type: char(3)
 * @property string|null $stspendmgn  type: char(1)
 * @property string|null $notreasury  type: varchar(11)
 * @property string|null $kdprdold  type: char(2)
 * @property string|null $stscasie  type: char(1)
 * @property string|null $jwcasie  type: numeric(5)
 * @property string|null $kdjwcasie  type: char(1)
 * @property string|null $osmdlccasie  type: numeric(9)
 * @property string|null $osmgnccasie  type: numeric(9)
 * @property string|null $stsreccasie  type: char(1)
 * @property string|null $ststrncasie  type: char(1)
 * @property string|null $stsacmb  type: char(1)
 * @property string|null $lb_jnspiutang  type: char(2)
 * @property string|null $lb_stspiutang  type: char(1)
 * @property string|null $lb_sifatpiutang  type: char(1)
 * @property string|null $lb_sekon  type: char(10)
 * @property string|null $tgkharimdl  type: numeric(5)
 * @property string|null $tgkharimgn  type: numeric(5)
 * @property string|null $tglmacet  type: varchar(8)
 * @property string|null $haritgkmdl  type: numeric(9)
 * @property string|null $haritgkmgn  type: numeric(9)
 * @property string|null $haritgk  type: numeric(9)
 * @property string|null $ppap_ajust  type: numeric(9)
 * @property string|null $kdbank  type: varchar(10)
 * @property string|null $lb_rateflat_awal  type: numeric(5)
 * @property string|null $lb_rateeff_awal  type: numeric(9)
 * @property string|null $jns_ckpn  type: char(1)
 * @property string|null $pby_prog  type: char(3)
 * @property string|null $sekon_usaha  type: char(3)
 * @property string|null $sandi_fintech  type: char(8)
 * @property string $ckpn  type: numeric(9)
 */
class Toflmb extends MciBaseModel
{
    /**
     * Nama tabel (case-sensitive di SQL Server).
     */
    protected $table = 'TOFLMB';

    /**
     * Daftar LENGKAP kolom sesuai database (220 kolom).
     * Model ini read-only, $fillable hanya untuk dokumentasi & IDE helper.
     *
     * @var array<int,string>
     */
    protected $fillable = [
        'nokontrak',
        'nocif',
        'nocifgrp',
        'terkait',
        'nofas',
        'nama',
        'kdprd',
        'pokpby',
        'kdcol',
        'kdsalur',
        'terikat',
        'kdcab',
        'kdloc',
        'kdkas',
        'cc',
        'kurs',
        'mdlawal',
        'mgnawal',
        'osmdlold',
        'osmgnold',
        'mtsmdl',
        'mtsmgn',
        'osmdlc',
        'osmgnc',
        'osmdlcv',
        'osmgncv',
        'osmdlref',
        'osmdlaset',
        'kdaset',
        'noakad',
        'tglakadn',
        'tglakad',
        'jw',
        'kdjw',
        'tgleff',
        'tglexp',
        'tglbook',
        'kdtahap',
        'tahap',
        'tahapke',
        'gpmdl',
        'gpmgn',
        'frekmdl',
        'frekmgn',
        'angsmdl',
        'angsmgn',
        'angsmdlke',
        'angsmgnke',
        'mdlcust',
        'mdlbank',
        'nbhbank',
        'kdbaghas',
        'baghas',
        'baghasrg',
        'baghaslb',
        'totbaghas',
        'osptg',
        'osptgv',
        'kdtagih',
        'ddtagih',
        'tgltagiha',
        'tgltagih',
        'tgltagihn',
        'kdrs',
        'rateeff',
        'rateflat',
        'dnpool',
        'dnrkp',
        'dnumum',
        'dnrak',
        'dnpoolrp',
        'dnrkprp',
        'dnumumrp',
        'dnrakrp',
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
        'ststrnum',
        'stsacc',
        'ppap',
        'totanggota',
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
        'dp',
        'stswakalah',
        'stsaset',
        'acsuplier',
        'ststopup',
        'noreg',
        'collcur',
        'osmdlcur',
        'autoblok',
        'kdgroupdana',
        'kdgroupdeb',
        'batch',
        'setorwajib',
        'kdsetor',
        'noacsetor',
        'jnsagun',
        'htgagun',
        'jnsagunbi',
        'intervpok',
        'intervmgn',
        'kdsp',
        'tglsp',
        'slk_takeover',
        'slk_sumberdana',
        'slk_kdljk',
        'slk_nilaiproyek',
        'slk_dati2proyek',
        'slk_sifat',
        'slk_jnspemb',
        'slk_gunadeb',
        'slk_orienguna',
        'slk_sekon',
        'slk_kategorideb',
        'slk_progpem',
        'slk_kdrest',
        'slk_kdlunas',
        'stscif_1',
        'stscif_2',
        'stsloan_1',
        'stsloan_2',
        'slk_goljam',
        'kdatmro',
        'kdatmrn',
        'stspendmgn',
        'notreasury',
        'kdprdold',
        'stscasie',
        'jwcasie',
        'kdjwcasie',
        'osmdlccasie',
        'osmgnccasie',
        'stsreccasie',
        'ststrncasie',
        'stsacmb',
        'lb_jnspiutang',
        'lb_stspiutang',
        'lb_sifatpiutang',
        'lb_sekon',
        'tgkharimdl',
        'tgkharimgn',
        'tglmacet',
        'haritgkmdl',
        'haritgkmgn',
        'haritgk',
        'ppap_ajust',
        'kdbank',
        'lb_rateflat_awal',
        'lb_rateeff_awal',
        'jns_ckpn',
        'pby_prog',
        'sekon_usaha',
        'sandi_fintech',
        'ckpn',
    ];

    /**
     * Casting tipe kolom (non-string saja).
     *
     * @var array<string,string>
     */
    protected $casts = [
        'kurs' => 'decimal:2',
        'mdlawal' => 'decimal:2',
        'mgnawal' => 'decimal:2',
        'osmdlold' => 'decimal:2',
        'osmgnold' => 'decimal:2',
        'mtsmdl' => 'decimal:2',
        'mtsmgn' => 'decimal:2',
        'osmdlc' => 'decimal:2',
        'osmgnc' => 'decimal:2',
        'osmdlcv' => 'decimal:2',
        'osmgncv' => 'decimal:2',
        'osmdlref' => 'decimal:2',
        'osmdlaset' => 'decimal:2',
        'jw' => 'decimal:2',
        'tahap' => 'decimal:2',
        'tahapke' => 'decimal:2',
        'gpmdl' => 'decimal:2',
        'gpmgn' => 'decimal:2',
        'frekmdl' => 'decimal:2',
        'frekmgn' => 'decimal:2',
        'angsmdl' => 'decimal:2',
        'angsmgn' => 'decimal:2',
        'angsmdlke' => 'decimal:2',
        'angsmgnke' => 'decimal:2',
        'mdlcust' => 'decimal:2',
        'mdlbank' => 'decimal:2',
        'nbhbank' => 'decimal:2',
        'baghas' => 'decimal:2',
        'baghasrg' => 'decimal:2',
        'baghaslb' => 'decimal:2',
        'totbaghas' => 'decimal:2',
        'osptg' => 'decimal:2',
        'osptgv' => 'decimal:2',
        'rateeff' => 'decimal:2',
        'rateflat' => 'decimal:2',
        'dnpool' => 'decimal:2',
        'dnrkp' => 'decimal:2',
        'dnumum' => 'decimal:2',
        'dnrak' => 'decimal:2',
        'dnpoolrp' => 'decimal:2',
        'dnrkprp' => 'decimal:2',
        'dnumumrp' => 'decimal:2',
        'dnrakrp' => 'decimal:2',
        'nilaijam' => 'decimal:2',
        'dnd' => 'decimal:2',
        'mdleom' => 'decimal:2',
        'mgneom' => 'decimal:2',
        'mdleoy' => 'decimal:2',
        'mgneoy' => 'decimal:2',
        'ppap' => 'decimal:2',
        'totanggota' => 'decimal:2',
        'tgkmdl' => 'decimal:2',
        'tgkmgn' => 'decimal:2',
        'blntgkmdl' => 'decimal:2',
        'blntgkmgn' => 'decimal:2',
        'dp' => 'decimal:2',
        'osmdlcur' => 'decimal:2',
        'autoblok' => 'decimal:2',
        'batch' => 'decimal:2',
        'setorwajib' => 'decimal:2',
        'htgagun' => 'decimal:2',
        'intervpok' => 'decimal:2',
        'intervmgn' => 'decimal:2',
        'slk_nilaiproyek' => 'decimal:2',
        'jwcasie' => 'decimal:2',
        'osmdlccasie' => 'decimal:2',
        'osmgnccasie' => 'decimal:2',
        'tgkharimdl' => 'decimal:2',
        'tgkharimgn' => 'decimal:2',
        'haritgkmdl' => 'decimal:2',
        'haritgkmgn' => 'decimal:2',
        'haritgk' => 'decimal:2',
        'ppap_ajust' => 'decimal:2',
        'lb_rateflat_awal' => 'decimal:2',
        'lb_rateeff_awal' => 'decimal:2',
        'ckpn' => 'decimal:2',
    ];
}
