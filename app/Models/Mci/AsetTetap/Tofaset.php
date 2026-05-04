<?php

declare(strict_types=1);

namespace App\Models\Mci\AsetTetap;

use App\Models\Mci\MciBaseModel;

/**
 * Model MCI: TOFASET
 * --------------------------------------------------------------------------
 * Domain   : Aset Tetap
 * Tabel    : [dbo].[TOFASET]
 * Kolom    : 99
 * Sumber   : Mapping_MCI_MAR26_01042026.sql / MCI_MAR26_01042026.xlsx
 *
 * CATATAN:
 *  - Model ini READ-ONLY (lihat trait ReadOnlyModel di MciBaseModel).
 *  - Koneksi default `dashboard_data` (SQL Server), otomatis ke DB bulan terbaru.
 *  - Kolom dengan spasi/karakter khusus diakses via:
 *    $model->getAttribute('NAMA KOLOM')  atau  $model->{'NAMA KOLOM'}.
 *
 * @property string|null $kdaset type: varchar(10)
 * @property string|null $ket type: varchar(30)
 * @property string|null $golaset type: varchar(2)
 * @property string|null $groupaset type: varchar(5)
 * @property string|null $cc type: varchar(2)
 * @property string|null $ccjual type: varchar(2)
 * @property string|null $kurs type: numeric(9)
 * @property string|null $habeli type: numeric(9)
 * @property string|null $disc type: numeric(9)
 * @property string|null $biaya type: numeric(9)
 * @property string|null $haper type: numeric(9)
 * @property string|null $ppnin type: numeric(9)
 * @property string|null $ppnout type: numeric(9)
 * @property string|null $hajual type: numeric(9)
 * @property string|null $hajar type: numeric(9)
 * @property string|null $tglbeli type: varchar(8)
 * @property string|null $tglkirim type: varchar(8)
 * @property string|null $tglkirimb type: varchar(8)
 * @property string|null $nodok type: varchar(20)
 * @property string|null $margin type: numeric(9)
 * @property string|null $lokasi type: varchar(40)
 * @property string|null $kota type: varchar(20)
 * @property string|null $kdcab type: varchar(3)
 * @property string|null $kdloc type: varchar(2)
 * @property string|null $kdkas type: varchar(2)
 * @property string|null $kdcabhand type: varchar(3)
 * @property string|null $stsaset type: varchar(1)
 * @property string|null $kdsusut type: varchar(1)
 * @property string|null $masasusut type: numeric(5)
 * @property string|null $susutbln type: numeric(9)
 * @property string|null $totsusut type: numeric(9)
 * @property string|null $nilaibuku type: numeric(9)
 * @property string|null $susutke type: numeric(5)
 * @property string|null $tglsusut type: varchar(8)
 * @property string|null $priodesst type: varchar(6)
 * @property string|null $kdkondisi type: varchar(1)
 * @property string|null $kondisi type: varchar(150)
 * @property string|null $btkaset type: varchar(2)
 * @property string|null $stspbeli type: varchar(1)
 * @property string|null $nocif type: varchar(11)
 * @property string|null $nmpbeli type: varchar(30)
 * @property string|null $almpbeli type: varchar(11)
 * @property string|null $kotapbeli type: varchar(30)
 * @property string|null $notelp type: varchar(20)
 * @property string|null $dp type: numeric(9)
 * @property string|null $tgljual type: varchar(8)
 * @property string|null $kdmitra type: varchar(4)
 * @property string|null $acmitra type: varchar(11)
 * @property string|null $sbb01 type: varchar(11)
 * @property string|null $sbb02 type: varchar(11)
 * @property string|null $sbb03 type: varchar(11)
 * @property string|null $sbb04 type: varchar(11)
 * @property string|null $sbb05 type: varchar(11)
 * @property string|null $sbb06 type: varchar(11)
 * @property string|null $sbb07 type: varchar(11)
 * @property string|null $perbaikan type: numeric(9)
 * @property string|null $hargabuku type: numeric(9)
 * @property string|null $hargasewa type: numeric(9)
 * @property string|null $totsewa type: numeric(9)
 * @property string|null $umrp type: numeric(9)
 * @property string|null $umva type: numeric(9)
 * @property string|null $nopolis type: varchar(25)
 * @property string|null $stsasr type: varchar(1)
 * @property string|null $nokontrak type: varchar(11)
 * @property string|null $stskontrak type: varchar(1)
 * @property string|null $stsjam type: varchar(1)
 * @property string|null $ststrnum type: varchar(1)
 * @property string|null $ststrnaset type: varchar(1)
 * @property string|null $ststrndrop type: varchar(1)
 * @property string|null $ststrnppni type: varchar(1)
 * @property string|null $ststrnppno type: varchar(1)
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
 * @property string|null $qty type: numeric(5)
 * @property string|null $tgloffset type: varchar(8)
 * @property string|null $up type: numeric(9)
 * @property string|null $premi type: numeric(9)
 * @property string|null $tglpolis type: varchar(8)
 * @property string|null $tgleff type: varchar(8)
 * @property string|null $tglexp type: varchar(8)
 * @property string|null $tglterima type: varchar(8)
 * @property string|null $savebox type: varchar(15)
 * @property string|null $jnsrisk type: varchar(2)
 * @property string|null $mtdsusut type: char(2)
 * @property string|null $ckpn type: numeric(9)
 * @property string|null $ds_latitude type: numeric(9)
 * @property string|null $ds_longitude type: numeric(9)
 * @property string|null $lb_stsaset type: char(1)
 * @property string|null $sandidati type: char(4)
 * @property string|null $mtdukur type: varchar(2)
 * @property string|null $tglterbengkalai type: varchar(8)
 */
class Tofaset extends MciBaseModel
{
    /**
     * Nama tabel (case-sensitive di SQL Server).
     */
    protected $table = 'TOFASET';

    /**
     * Daftar LENGKAP kolom sesuai database (99 kolom).
     * Model ini read-only, $fillable hanya untuk dokumentasi & IDE helper.
     *
     * @var array<int,string>
     */
    protected $fillable = [
        'kdaset',
        'ket',
        'golaset',
        'groupaset',
        'cc',
        'ccjual',
        'kurs',
        'habeli',
        'disc',
        'biaya',
        'haper',
        'ppnin',
        'ppnout',
        'hajual',
        'hajar',
        'tglbeli',
        'tglkirim',
        'tglkirimb',
        'nodok',
        'margin',
        'lokasi',
        'kota',
        'kdcab',
        'kdloc',
        'kdkas',
        'kdcabhand',
        'stsaset',
        'kdsusut',
        'masasusut',
        'susutbln',
        'totsusut',
        'nilaibuku',
        'susutke',
        'tglsusut',
        'priodesst',
        'kdkondisi',
        'kondisi',
        'btkaset',
        'stspbeli',
        'nocif',
        'nmpbeli',
        'almpbeli',
        'kotapbeli',
        'notelp',
        'dp',
        'tgljual',
        'kdmitra',
        'acmitra',
        'sbb01',
        'sbb02',
        'sbb03',
        'sbb04',
        'sbb05',
        'sbb06',
        'sbb07',
        'perbaikan',
        'hargabuku',
        'hargasewa',
        'totsewa',
        'umrp',
        'umva',
        'nopolis',
        'stsasr',
        'nokontrak',
        'stskontrak',
        'stsjam',
        'ststrnum',
        'ststrnaset',
        'ststrndrop',
        'ststrnppni',
        'ststrnppno',
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
        'qty',
        'tgloffset',
        'up',
        'premi',
        'tglpolis',
        'tgleff',
        'tglexp',
        'tglterima',
        'savebox',
        'jnsrisk',
        'mtdsusut',
        'ckpn',
        'ds_latitude',
        'ds_longitude',
        'lb_stsaset',
        'sandidati',
        'mtdukur',
        'tglterbengkalai',
    ];

    /**
     * Casting tipe kolom (non-string saja).
     *
     * @var array<string,string>
     */
    protected $casts = [
        'kurs' => 'decimal:2',
        'habeli' => 'decimal:2',
        'disc' => 'decimal:2',
        'biaya' => 'decimal:2',
        'haper' => 'decimal:2',
        'ppnin' => 'decimal:2',
        'ppnout' => 'decimal:2',
        'hajual' => 'decimal:2',
        'hajar' => 'decimal:2',
        'margin' => 'decimal:2',
        'masasusut' => 'decimal:2',
        'susutbln' => 'decimal:2',
        'totsusut' => 'decimal:2',
        'nilaibuku' => 'decimal:2',
        'susutke' => 'decimal:2',
        'dp' => 'decimal:2',
        'perbaikan' => 'decimal:2',
        'hargabuku' => 'decimal:2',
        'hargasewa' => 'decimal:2',
        'totsewa' => 'decimal:2',
        'umrp' => 'decimal:2',
        'umva' => 'decimal:2',
        'qty' => 'decimal:2',
        'up' => 'decimal:2',
        'premi' => 'decimal:2',
        'ckpn' => 'decimal:2',
        'ds_latitude' => 'decimal:2',
        'ds_longitude' => 'decimal:2',
    ];
}
