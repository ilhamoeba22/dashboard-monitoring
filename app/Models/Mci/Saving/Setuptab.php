<?php

namespace App\Models\Mci\Saving;

use App\Models\Mci\MciBaseModel;

/**
 * Model MCI: SETUPTAB
 * --------------------------------------------------------------------------
 * Domain   : Saving
 * Tabel    : [dbo].[SETUPTAB]
 * Kolom    : 130
 * Sumber   : Mapping_MCI_MAR26_01042026.sql / MCI_MAR26_01042026.xlsx
 *
 * CATATAN:
 *  - Model ini READ-ONLY (lihat trait ReadOnlyModel di MciBaseModel).
 *  - Koneksi default `dashboard_data` (SQL Server), otomatis ke DB bulan terbaru.
 *  - Kolom dengan spasi/karakter khusus diakses via:
 *    $model->getAttribute('NAMA KOLOM')  atau  $model->{'NAMA KOLOM'}.
 *
 * @property string $kodeprd  type: varchar(2)
 * @property string|null $nmpdkprd  type: varchar(10)
 * @property string|null $nmpjgprd  type: varchar(30)
 * @property string|null $cc  type: varchar(2)
 * @property string $htgbunga  type: varchar(1)
 * @property string $byrbunga  type: varchar(1)
 * @property string $faktor  type: varchar(1)
 * @property string|null $ntkp  type: numeric(9)
 * @property string|null $tax  type: numeric(5)
 * @property string|null $kodebuku  type: varchar(2)
 * @property string $postkerek  type: varchar(1)
 * @property string|null $progresif  type: varchar(1)
 * @property string|null $saldomin1  type: numeric(9)
 * @property string|null $saldomax1  type: numeric(9)
 * @property string|null $saldomin2  type: numeric(9)
 * @property string|null $saldomax2  type: numeric(9)
 * @property string|null $saldomin3  type: numeric(9)
 * @property string|null $saldomax3  type: numeric(9)
 * @property string|null $saldomin4  type: numeric(9)
 * @property string|null $saldomax4  type: numeric(9)
 * @property string|null $saldomin5  type: numeric(9)
 * @property string|null $saldomax5  type: numeric(9)
 * @property string|null $rate1  type: numeric(5)
 * @property string|null $rate2  type: numeric(5)
 * @property string|null $rate3  type: numeric(5)
 * @property string|null $rate4  type: numeric(5)
 * @property string|null $rate5  type: numeric(5)
 * @property string|null $sbbprinc  type: varchar(7)
 * @property string|null $sbbbunga  type: varchar(7)
 * @property string|null $sbbbyadm  type: varchar(7)
 * @property string|null $sbbttptax  type: varchar(7)
 * @property string|null $sbbtutup  type: varchar(7)
 * @property string|null $sbbacrbng  type: varchar(7)
 * @property string|null $sbbpasif  type: varchar(7)
 * @property string|null $sbbttpbh  type: varchar(7)
 * @property string|null $byadmbln  type: numeric(5)
 * @property string|null $bytutup  type: numeric(5)
 * @property string|null $bybuku  type: numeric(5)
 * @property string|null $bykoran  type: numeric(5)
 * @property string|null $bypasif  type: numeric(5)
 * @property string|null $saldopasif  type: numeric(9)
 * @property string|null $minsetor1  type: numeric(9)
 * @property string|null $minsetor2  type: numeric(9)
 * @property string|null $minsaldo  type: numeric(9)
 * @property string|null $rate  type: numeric(5)
 * @property string|null $berlaku  type: varchar(8)
 * @property string|null $ratelama  type: numeric(5)
 * @property string|null $maxspread  type: numeric(5)
 * @property string|null $mintutup  type: varchar(1)
 * @property string|null $lamapasif  type: numeric(5)
 * @property string|null $otorisasi  type: varchar(1)
 * @property string|null $umrpending  type: numeric(5)
 * @property string|null $tgladmbln  type: varchar(2)
 * @property string|null $maxtarik  type: numeric(9)
 * @property string|null $dndmaxtrk  type: numeric(5)
 * @property string|null $sbbdnd  type: varchar(7)
 * @property string|null $setoran  type: numeric(9)
 * @property string|null $frek  type: numeric(5)
 * @property string|null $masa  type: numeric(5)
 * @property string|null $dndmasa  type: numeric(5)
 * @property string|null $premi  type: numeric(9)
 * @property string|null $sbbttpprem  type: varchar(7)
 * @property string|null $sbbbypremi  type: varchar(7)
 * @property string|null $sbbdndtrk  type: varchar(7)
 * @property string|null $sbbdndmasa  type: varchar(7)
 * @property string|null $postakhmsa  type: varchar(10)
 * @property string|null $sbbsubstax  type: varchar(7)
 * @property string|null $ddawal  type: varchar(2)
 * @property string|null $ddakhir  type: varchar(2)
 * @property string|null $tglawal  type: varchar(8)
 * @property string|null $tglakhir  type: varchar(8)
 * @property string|null $tglbuku  type: varchar(8)
 * @property string|null $bagihasil  type: varchar(1)
 * @property string|null $bbtdana  type: numeric(5)
 * @property string|null $sbbzakat  type: varchar(7)
 * @property string|null $zakat  type: numeric(5)
 * @property string|null $sbbinfaq  type: varchar(7)
 * @property string|null $admklg  type: varchar(1)
 * @property string|null $sbbolim  type: varchar(7)
 * @property string|null $sbblain  type: varchar(7)
 * @property string|null $sbbbuku  type: varchar(7)
 * @property string|null $sbbkoran  type: varchar(7)
 * @property string|null $stsrec  type: varchar(1)
 * @property string|null $saldo1  type: numeric(9)
 * @property string|null $saldo2  type: numeric(9)
 * @property string|null $saldo3  type: numeric(9)
 * @property string|null $saldo4  type: numeric(9)
 * @property string|null $saldo5  type: numeric(9)
 * @property string|null $bonus1  type: numeric(5)
 * @property string|null $bonus2  type: numeric(5)
 * @property string|null $bonus3  type: numeric(5)
 * @property string|null $bonus4  type: numeric(5)
 * @property string|null $bonus5  type: numeric(5)
 * @property string|null $stsrate  type: varchar(1)
 * @property string|null $byonline  type: numeric(5)
 * @property string|null $bytariktni  type: numeric(5)
 * @property string|null $mintariktni  type: numeric(9)
 * @property string|null $stsdebet  type: varchar(1)
 * @property string|null $nombebas  type: numeric(9)
 * @property string|null $stspccode  type: varchar(1)
 * @property string|null $stswarkat  type: varchar(1)
 * @property string|null $sbbonline  type: varchar(7)
 * @property string|null $sbbmintarik  type: varchar(7)
 * @property string|null $inpuser  type: varchar(10)
 * @property string|null $inptgljam  type: varchar(14)
 * @property string|null $inpterm  type: varchar(10)
 * @property string|null $chguser  type: varchar(10)
 * @property string|null $chgtgljam  type: varchar(14)
 * @property string|null $chgterm  type: varchar(10)
 * @property string|null $autuser  type: varchar(10)
 * @property string|null $auttgljam  type: varchar(14)
 * @property string|null $autterm  type: varchar(10)
 * @property string|null $admprog  type: varchar(1)
 * @property string|null $saldotier1  type: numeric(9)
 * @property string|null $saldotier2  type: numeric(9)
 * @property string|null $saldotier3  type: numeric(9)
 * @property string|null $saldotier4  type: numeric(9)
 * @property string|null $saldotier5  type: numeric(9)
 * @property string|null $byadm1  type: numeric(9)
 * @property string|null $byadm2  type: numeric(9)
 * @property string|null $byadm3  type: numeric(9)
 * @property string|null $byadm4  type: numeric(9)
 * @property string|null $byadm5  type: numeric(9)
 * @property string|null $stsproduk  type: varchar(1)
 * @property string|null $stscetaktax  type: varchar(1)
 * @property string|null $stsbyadm  type: varchar(1)
 * @property string|null $haritutup  type: varchar(8)
 * @property string|null $frektarik  type: numeric(5)
 * @property string|null $stsbnspasif  type: char(1)
 * @property string|null $prosbyadm  type: numeric(5)
 */
class Setuptab extends MciBaseModel
{
    /**
     * Nama tabel (case-sensitive di SQL Server).
     */
    protected $table = 'SETUPTAB';

    /**
     * Daftar LENGKAP kolom sesuai database (130 kolom).
     * Model ini read-only, $fillable hanya untuk dokumentasi & IDE helper.
     *
     * @var array<int,string>
     */
    protected $fillable = [
        'kodeprd',
        'nmpdkprd',
        'nmpjgprd',
        'cc',
        'htgbunga',
        'byrbunga',
        'faktor',
        'ntkp',
        'tax',
        'kodebuku',
        'postkerek',
        'progresif',
        'saldomin1',
        'saldomax1',
        'saldomin2',
        'saldomax2',
        'saldomin3',
        'saldomax3',
        'saldomin4',
        'saldomax4',
        'saldomin5',
        'saldomax5',
        'rate1',
        'rate2',
        'rate3',
        'rate4',
        'rate5',
        'sbbprinc',
        'sbbbunga',
        'sbbbyadm',
        'sbbttptax',
        'sbbtutup',
        'sbbacrbng',
        'sbbpasif',
        'sbbttpbh',
        'byadmbln',
        'bytutup',
        'bybuku',
        'bykoran',
        'bypasif',
        'saldopasif',
        'minsetor1',
        'minsetor2',
        'minsaldo',
        'rate',
        'berlaku',
        'ratelama',
        'maxspread',
        'mintutup',
        'lamapasif',
        'otorisasi',
        'umrpending',
        'tgladmbln',
        'maxtarik',
        'dndmaxtrk',
        'sbbdnd',
        'setoran',
        'frek',
        'masa',
        'dndmasa',
        'premi',
        'sbbttpprem',
        'sbbbypremi',
        'sbbdndtrk',
        'sbbdndmasa',
        'postakhmsa',
        'sbbsubstax',
        'ddawal',
        'ddakhir',
        'tglawal',
        'tglakhir',
        'tglbuku',
        'bagihasil',
        'bbtdana',
        'sbbzakat',
        'zakat',
        'sbbinfaq',
        'admklg',
        'sbbolim',
        'sbblain',
        'sbbbuku',
        'sbbkoran',
        'stsrec',
        'saldo1',
        'saldo2',
        'saldo3',
        'saldo4',
        'saldo5',
        'bonus1',
        'bonus2',
        'bonus3',
        'bonus4',
        'bonus5',
        'stsrate',
        'byonline',
        'bytariktni',
        'mintariktni',
        'stsdebet',
        'nombebas',
        'stspccode',
        'stswarkat',
        'sbbonline',
        'sbbmintarik',
        'inpuser',
        'inptgljam',
        'inpterm',
        'chguser',
        'chgtgljam',
        'chgterm',
        'autuser',
        'auttgljam',
        'autterm',
        'admprog',
        'saldotier1',
        'saldotier2',
        'saldotier3',
        'saldotier4',
        'saldotier5',
        'byadm1',
        'byadm2',
        'byadm3',
        'byadm4',
        'byadm5',
        'stsproduk',
        'stscetaktax',
        'stsbyadm',
        'haritutup',
        'frektarik',
        'stsbnspasif',
        'prosbyadm',
    ];

    /**
     * Casting tipe kolom (non-string saja).
     *
     * @var array<string,string>
     */
    protected $casts = [
        'ntkp' => 'decimal:2',
        'tax' => 'decimal:2',
        'saldomin1' => 'decimal:2',
        'saldomax1' => 'decimal:2',
        'saldomin2' => 'decimal:2',
        'saldomax2' => 'decimal:2',
        'saldomin3' => 'decimal:2',
        'saldomax3' => 'decimal:2',
        'saldomin4' => 'decimal:2',
        'saldomax4' => 'decimal:2',
        'saldomin5' => 'decimal:2',
        'saldomax5' => 'decimal:2',
        'rate1' => 'decimal:2',
        'rate2' => 'decimal:2',
        'rate3' => 'decimal:2',
        'rate4' => 'decimal:2',
        'rate5' => 'decimal:2',
        'byadmbln' => 'decimal:2',
        'bytutup' => 'decimal:2',
        'bybuku' => 'decimal:2',
        'bykoran' => 'decimal:2',
        'bypasif' => 'decimal:2',
        'saldopasif' => 'decimal:2',
        'minsetor1' => 'decimal:2',
        'minsetor2' => 'decimal:2',
        'minsaldo' => 'decimal:2',
        'rate' => 'decimal:2',
        'ratelama' => 'decimal:2',
        'maxspread' => 'decimal:2',
        'lamapasif' => 'decimal:2',
        'umrpending' => 'decimal:2',
        'maxtarik' => 'decimal:2',
        'dndmaxtrk' => 'decimal:2',
        'setoran' => 'decimal:2',
        'frek' => 'decimal:2',
        'masa' => 'decimal:2',
        'dndmasa' => 'decimal:2',
        'premi' => 'decimal:2',
        'bbtdana' => 'decimal:2',
        'zakat' => 'decimal:2',
        'saldo1' => 'decimal:2',
        'saldo2' => 'decimal:2',
        'saldo3' => 'decimal:2',
        'saldo4' => 'decimal:2',
        'saldo5' => 'decimal:2',
        'bonus1' => 'decimal:2',
        'bonus2' => 'decimal:2',
        'bonus3' => 'decimal:2',
        'bonus4' => 'decimal:2',
        'bonus5' => 'decimal:2',
        'byonline' => 'decimal:2',
        'bytariktni' => 'decimal:2',
        'mintariktni' => 'decimal:2',
        'nombebas' => 'decimal:2',
        'saldotier1' => 'decimal:2',
        'saldotier2' => 'decimal:2',
        'saldotier3' => 'decimal:2',
        'saldotier4' => 'decimal:2',
        'saldotier5' => 'decimal:2',
        'byadm1' => 'decimal:2',
        'byadm2' => 'decimal:2',
        'byadm3' => 'decimal:2',
        'byadm4' => 'decimal:2',
        'byadm5' => 'decimal:2',
        'frektarik' => 'decimal:2',
        'prosbyadm' => 'decimal:2',
    ];
}
