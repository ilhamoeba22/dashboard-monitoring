<?php

declare(strict_types=1);

namespace App\Models\Mci\Lainnya;

use App\Models\Mci\MciBaseModel;

/**
 * Model MCI: TOFMSPK
 * --------------------------------------------------------------------------
 * Domain   : Lainnya
 * Tabel    : [dbo].[TOFMSPK]
 * Kolom    : 133
 * Sumber   : Mapping_MCI_MAR26_01042026.sql / MCI_MAR26_01042026.xlsx
 *
 * CATATAN:
 *  - Model ini READ-ONLY (lihat trait ReadOnlyModel di MciBaseModel).
 *  - Koneksi default `dashboard_data` (SQL Server), otomatis ke DB bulan terbaru.
 *  - Kolom dengan spasi/karakter khusus diakses via:
 *    $model->getAttribute('NAMA KOLOM')  atau  $model->{'NAMA KOLOM'}.
 *
 * @property string $noreg type: varchar(30)
 * @property string|null $nokontrak type: varchar(11)
 * @property string|null $tglaplikasi type: varchar(8)
 * @property string|null $nospp type: varchar(50)
 * @property string|null $tglspp type: varchar(8)
 * @property string|null $tglexpspp type: varchar(8)
 * @property string|null $nowakalah type: varchar(50)
 * @property string|null $tglwakalah type: varchar(8)
 * @property string|null $hari type: numeric(5)
 * @property string|null $nospk type: varchar(50)
 * @property string|null $tglspk type: varchar(8)
 * @property string|null $stsdebitur type: varchar(1)
 * @property string|null $nocif type: varchar(9)
 * @property string|null $nama type: varchar(50)
 * @property string|null $sex type: varchar(1)
 * @property string|null $tmplhr type: varchar(25)
 * @property string|null $tgllhr type: varchar(8)
 * @property string|null $alamat type: varchar(100)
 * @property string|null $kelurahan type: varchar(30)
 * @property string|null $kecamatan type: varchar(30)
 * @property string|null $kota type: varchar(30)
 * @property string|null $kdpos type: varchar(5)
 * @property string|null $kdarea type: varchar(5)
 * @property string|null $notelp type: varchar(20)
 * @property string|null $nohp type: varchar(20)
 * @property string|null $noid type: varchar(20)
 * @property string|null $tglexpid type: varchar(8)
 * @property string|null $kdkerja type: varchar(10)
 * @property string|null $ketkerja type: varchar(50)
 * @property string|null $nommohon type: numeric(9)
 * @property string|null $jwmohon type: numeric(5)
 * @property string|null $kdjwmohon type: varchar(1)
 * @property string|null $sifat type: varchar(1)
 * @property string|null $stspasangan type: varchar(1)
 * @property string|null $nmpasangan type: varchar(50)
 * @property string|null $noidpasangan type: varchar(20)
 * @property string|null $tglexpidpasangan type: varchar(8)
 * @property string|null $alamatpasangan type: varchar(100)
 * @property string|null $kerjapasangan type: varchar(30)
 * @property string|null $ststtd2 type: varchar(1)
 * @property string|null $ketststtd2 type: varchar(30)
 * @property string|null $nmttd2 type: varchar(30)
 * @property string|null $noidttd2 type: varchar(20)
 * @property string|null $tglexpidttd2 type: varchar(8)
 * @property string|null $alamatttd2 type: varchar(100)
 * @property string|null $kerjattd2 type: varchar(50)
 * @property string|null $tmplhrttd2 type: varchar(30)
 * @property string|null $tgllhrttd2 type: varchar(8)
 * @property string|null $ststtd3 type: varchar(1)
 * @property string|null $ketststtd3 type: varchar(30)
 * @property string|null $nmttd3 type: varchar(30)
 * @property string|null $noidttd3 type: varchar(20)
 * @property string|null $tglexpidttd3 type: varchar(8)
 * @property string|null $alamatttd3 type: varchar(100)
 * @property string|null $kerjattd3 type: varchar(50)
 * @property string|null $tmplhrttd3 type: varchar(30)
 * @property string|null $tgllhrttd3 type: varchar(8)
 * @property string|null $kdprd type: varchar(2)
 * @property string|null $kdao type: varchar(10)
 * @property string|null $ketbarang type: varchar(100)
 * @property string|null $habeli type: numeric(9)
 * @property string|null $margin type: numeric(9)
 * @property string|null $hajual type: numeric(9)
 * @property string|null $dp type: numeric(9)
 * @property string|null $porsimodal type: numeric(5)
 * @property string|null $porsinisbah type: numeric(5)
 * @property string|null $kdakad type: varchar(2)
 * @property string|null $tglakad type: varchar(8)
 * @property string|null $tgljtempo type: varchar(8)
 * @property string|null $jw type: numeric(5)
 * @property string|null $kdjw type: varchar(1)
 * @property string|null $kdangsur type: varchar(1)
 * @property string|null $tiaptgl type: varchar(2)
 * @property string|null $nomangsur type: numeric(9)
 * @property string|null $nomsettab type: numeric(9)
 * @property string|null $nomdenda type: numeric(5)
 * @property string|null $notab type: varchar(11)
 * @property string|null $noskep type: varchar(20)
 * @property string|null $tglskep type: varchar(8)
 * @property string|null $tujuan type: varchar(200)
 * @property string|null $stscetak type: varchar(1)
 * @property string|null $cetakke type: numeric(5)
 * @property string|null $cetakby type: varchar(10)
 * @property string|null $kdloc type: varchar(3)
 * @property string|null $nmpimp type: varchar(50)
 * @property string|null $saksi1 type: varchar(50)
 * @property string|null $saksi2 type: varchar(50)
 * @property string|null $saksi3 type: varchar(50)
 * @property string|null $byadm type: numeric(9)
 * @property string|null $bynotaris type: numeric(9)
 * @property string|null $byasrjiwa type: numeric(9)
 * @property string|null $byasragun type: numeric(9)
 * @property string|null $bytabaru type: numeric(9)
 * @property string|null $bymaterai type: numeric(9)
 * @property string|null $bysurvey type: numeric(9)
 * @property string|null $bylain type: numeric(9)
 * @property string|null $agunan1 type: varchar(250)
 * @property string|null $agunan2 type: varchar(250)
 * @property string|null $agunan3 type: varchar(250)
 * @property string|null $agunan4 type: varchar(250)
 * @property string|null $agunan5 type: varchar(250)
 * @property string|null $jnsagun1 type: varchar(2)
 * @property string|null $jnsagun2 type: varchar(2)
 * @property string|null $jnsagun3 type: varchar(2)
 * @property string|null $jnsagun4 type: varchar(2)
 * @property string|null $jnsagun5 type: varchar(2)
 * @property string|null $stsaplikasi type: varchar(1)
 * @property string|null $catatan type: varchar(150)
 * @property string|null $kdikat type: varchar(2)
 * @property string|null $koordao type: varchar(10)
 * @property string|null $tglsetuju type: varchar(8)
 * @property string|null $komite1 type: varchar(20)
 * @property string|null $komite2 type: varchar(20)
 * @property string|null $komite3 type: varchar(20)
 * @property string|null $komite4 type: varchar(20)
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
 * @property string|null $noregagunan type: varchar(10)
 * @property string|null $tmplhrpasangan type: varchar(20)
 * @property string|null $tgllhrpasangan type: varchar(8)
 * @property string|null $pp_bank type: numeric(9)
 * @property string|null $pp_nasabah type: numeric(9)
 * @property string|null $nisbah_bank type: numeric(5)
 * @property string|null $nisbah_nasabah type: numeric(5)
 * @property string|null $bypeliharanasabah type: numeric(9)
 */
class Tofmspk extends MciBaseModel
{
    /**
     * Nama tabel (case-sensitive di SQL Server).
     */
    protected $table = 'TOFMSPK';

    /**
     * Daftar LENGKAP kolom sesuai database (133 kolom).
     * Model ini read-only, $fillable hanya untuk dokumentasi & IDE helper.
     *
     * @var array<int,string>
     */
    protected $fillable = [
        'noreg',
        'nokontrak',
        'tglaplikasi',
        'nospp',
        'tglspp',
        'tglexpspp',
        'nowakalah',
        'tglwakalah',
        'hari',
        'nospk',
        'tglspk',
        'stsdebitur',
        'nocif',
        'nama',
        'sex',
        'tmplhr',
        'tgllhr',
        'alamat',
        'kelurahan',
        'kecamatan',
        'kota',
        'kdpos',
        'kdarea',
        'notelp',
        'nohp',
        'noid',
        'tglexpid',
        'kdkerja',
        'ketkerja',
        'nommohon',
        'jwmohon',
        'kdjwmohon',
        'sifat',
        'stspasangan',
        'nmpasangan',
        'noidpasangan',
        'tglexpidpasangan',
        'alamatpasangan',
        'kerjapasangan',
        'ststtd2',
        'ketststtd2',
        'nmttd2',
        'noidttd2',
        'tglexpidttd2',
        'alamatttd2',
        'kerjattd2',
        'tmplhrttd2',
        'tgllhrttd2',
        'ststtd3',
        'ketststtd3',
        'nmttd3',
        'noidttd3',
        'tglexpidttd3',
        'alamatttd3',
        'kerjattd3',
        'tmplhrttd3',
        'tgllhrttd3',
        'kdprd',
        'kdao',
        'ketbarang',
        'habeli',
        'margin',
        'hajual',
        'dp',
        'porsimodal',
        'porsinisbah',
        'kdakad',
        'tglakad',
        'tgljtempo',
        'jw',
        'kdjw',
        'kdangsur',
        'tiaptgl',
        'nomangsur',
        'nomsettab',
        'nomdenda',
        'notab',
        'noskep',
        'tglskep',
        'tujuan',
        'stscetak',
        'cetakke',
        'cetakby',
        'kdloc',
        'nmpimp',
        'saksi1',
        'saksi2',
        'saksi3',
        'byadm',
        'bynotaris',
        'byasrjiwa',
        'byasragun',
        'bytabaru',
        'bymaterai',
        'bysurvey',
        'bylain',
        'agunan1',
        'agunan2',
        'agunan3',
        'agunan4',
        'agunan5',
        'jnsagun1',
        'jnsagun2',
        'jnsagun3',
        'jnsagun4',
        'jnsagun5',
        'stsaplikasi',
        'catatan',
        'kdikat',
        'koordao',
        'tglsetuju',
        'komite1',
        'komite2',
        'komite3',
        'komite4',
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
        'noregagunan',
        'tmplhrpasangan',
        'tgllhrpasangan',
        'pp_bank',
        'pp_nasabah',
        'nisbah_bank',
        'nisbah_nasabah',
        'bypeliharanasabah',
    ];

    /**
     * Casting tipe kolom (non-string saja).
     *
     * @var array<string,string>
     */
    protected $casts = [
        'hari' => 'decimal:2',
        'nommohon' => 'decimal:2',
        'jwmohon' => 'decimal:2',
        'habeli' => 'decimal:2',
        'margin' => 'decimal:2',
        'hajual' => 'decimal:2',
        'dp' => 'decimal:2',
        'porsimodal' => 'decimal:2',
        'porsinisbah' => 'decimal:2',
        'jw' => 'decimal:2',
        'nomangsur' => 'decimal:2',
        'nomsettab' => 'decimal:2',
        'nomdenda' => 'decimal:2',
        'cetakke' => 'decimal:2',
        'byadm' => 'decimal:2',
        'bynotaris' => 'decimal:2',
        'byasrjiwa' => 'decimal:2',
        'byasragun' => 'decimal:2',
        'bytabaru' => 'decimal:2',
        'bymaterai' => 'decimal:2',
        'bysurvey' => 'decimal:2',
        'bylain' => 'decimal:2',
        'pp_bank' => 'decimal:2',
        'pp_nasabah' => 'decimal:2',
        'nisbah_bank' => 'decimal:2',
        'nisbah_nasabah' => 'decimal:2',
        'bypeliharanasabah' => 'decimal:2',
    ];
}
