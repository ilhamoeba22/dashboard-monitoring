<?php

declare(strict_types=1);

namespace App\Models\Mci\Saving;

use App\Models\Mci\MciBaseModel;

/**
 * Model MCI: TOFTABDEL
 * --------------------------------------------------------------------------
 * Domain   : Saving
 * Tabel    : [dbo].[TOFTABDEL]
 * Kolom    : 89
 * Sumber   : Mapping_MCI_MAR26_01042026.sql / MCI_MAR26_01042026.xlsx
 *
 * CATATAN:
 *  - Model ini READ-ONLY (lihat trait ReadOnlyModel di MciBaseModel).
 *  - Koneksi default `dashboard_data` (SQL Server), otomatis ke DB bulan terbaru.
 *  - Kolom dengan spasi/karakter khusus diakses via:
 *    $model->getAttribute('NAMA KOLOM')  atau  $model->{'NAMA KOLOM'}.
 *
 * @property string $notab type: varchar(11)
 * @property string|null $nocif type: varchar(9)
 * @property string|null $snama type: varchar(10)
 * @property string|null $fnama type: varchar(30)
 * @property string|null $mailalm type: varchar(2)
 * @property string|null $kodecab type: varchar(3)
 * @property string|null $kodeloc type: varchar(2)
 * @property string|null $kodekas type: varchar(2)
 * @property string|null $kodeprd type: varchar(2)
 * @property string|null $cc type: varchar(2)
 * @property string|null $kurs type: numeric(9)
 * @property string|null $tglbuka type: varchar(8)
 * @property string|null $tgltutup type: varchar(8)
 * @property string|null $tgltrnakh type: varchar(8)
 * @property string|null $tglproses type: varchar(8)
 * @property string|null $sawalrp type: numeric(9)
 * @property string|null $sawalva type: numeric(9)
 * @property string|null $mutasidr type: numeric(9)
 * @property string|null $mutasicr type: numeric(9)
 * @property string|null $sahirrp type: numeric(9)
 * @property string|null $sahirva type: numeric(9)
 * @property string|null $saldobuku type: numeric(9)
 * @property string|null $saldomin type: numeric(9)
 * @property string|null $saldoavg type: numeric(9)
 * @property string|null $saldomax type: numeric(9)
 * @property string|null $mineom type: numeric(9)
 * @property string|null $avgeom type: numeric(9)
 * @property string|null $maxeom type: numeric(9)
 * @property string|null $sahireoy type: numeric(9)
 * @property string|null $sahireom type: numeric(9)
 * @property string|null $stsrest type: varchar(1)
 * @property string|null $stsvip type: varchar(1)
 * @property string|null $mtdbh type: varchar(1)
 * @property string|null $mtdbeban type: varchar(1)
 * @property string|null $kodebuku type: varchar(2)
 * @property string|null $stsacc type: varchar(1)
 * @property string|null $stsblok type: varchar(1)
 * @property string|null $saldoblok type: numeric(9)
 * @property string|null $sbbtab type: varchar(11)
 * @property string|null $pccode type: numeric(5)
 * @property string|null $rate type: numeric(5)
 * @property string|null $nisbah type: numeric(5)
 * @property string|null $spcnisbah type: numeric(5)
 * @property string|null $noaclama type: varchar(12)
 * @property string|null $tglbh type: varchar(8)
 * @property string|null $bh type: numeric(9)
 * @property string|null $akumbh type: numeric(9)
 * @property string|null $tax type: numeric(9)
 * @property string|null $bheom type: numeric(9)
 * @property string|null $taxeom type: numeric(9)
 * @property string|null $brsbuku type: numeric(5)
 * @property string|null $bukuke type: numeric(5)
 * @property string|null $kodeaoh type: varchar(8)
 * @property string|null $kodeaop type: varchar(8)
 * @property string|null $glb type: varchar(2)
 * @property string|null $kodeseg type: varchar(3)
 * @property string|null $terkait type: varchar(1)
 * @property string|null $special type: varchar(1)
 * @property string|null $setfix type: numeric(9)
 * @property string|null $bhhtg type: numeric(9)
 * @property string|null $totbhhtg type: numeric(9)
 * @property string|null $bhacru type: numeric(9)
 * @property string|null $totbhacru type: numeric(9)
 * @property string|null $bhbayar type: numeric(9)
 * @property string|null $taxbayar type: numeric(9)
 * @property string|null $tglhtg type: varchar(8)
 * @property string|null $tglacru type: varchar(8)
 * @property string|null $tglbayar type: varchar(8)
 * @property string|null $kdwil type: varchar(3)
 * @property string|null $hari type: numeric(5)
 * @property string|null $tglexp type: varchar(8)
 * @property string|null $masa type: numeric(5)
 * @property string|null $sisamasa type: numeric(5)
 * @property string|null $tglasr type: varchar(8)
 * @property string|null $harinext type: numeric(5)
 * @property string|null $taxhtg type: numeric(9)
 * @property string|null $tottaxhtg type: numeric(9)
 * @property string|null $tothari type: numeric(5)
 * @property string|null $saldobh type: numeric(9)
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
 */
class Toftabdel extends MciBaseModel
{
    /**
     * Nama tabel (case-sensitive di SQL Server).
     */
    protected $table = 'TOFTABDEL';

    /**
     * Daftar LENGKAP kolom sesuai database (89 kolom).
     * Model ini read-only, $fillable hanya untuk dokumentasi & IDE helper.
     *
     * @var array<int,string>
     */
    protected $fillable = [
        'notab',
        'nocif',
        'snama',
        'fnama',
        'mailalm',
        'kodecab',
        'kodeloc',
        'kodekas',
        'kodeprd',
        'cc',
        'kurs',
        'tglbuka',
        'tgltutup',
        'tgltrnakh',
        'tglproses',
        'sawalrp',
        'sawalva',
        'mutasidr',
        'mutasicr',
        'sahirrp',
        'sahirva',
        'saldobuku',
        'saldomin',
        'saldoavg',
        'saldomax',
        'mineom',
        'avgeom',
        'maxeom',
        'sahireoy',
        'sahireom',
        'stsrest',
        'stsvip',
        'mtdbh',
        'mtdbeban',
        'kodebuku',
        'stsacc',
        'stsblok',
        'saldoblok',
        'sbbtab',
        'pccode',
        'rate',
        'nisbah',
        'spcnisbah',
        'noaclama',
        'tglbh',
        'bh',
        'akumbh',
        'tax',
        'bheom',
        'taxeom',
        'brsbuku',
        'bukuke',
        'kodeaoh',
        'kodeaop',
        'glb',
        'kodeseg',
        'terkait',
        'special',
        'setfix',
        'bhhtg',
        'totbhhtg',
        'bhacru',
        'totbhacru',
        'bhbayar',
        'taxbayar',
        'tglhtg',
        'tglacru',
        'tglbayar',
        'kdwil',
        'hari',
        'tglexp',
        'masa',
        'sisamasa',
        'tglasr',
        'harinext',
        'taxhtg',
        'tottaxhtg',
        'tothari',
        'saldobh',
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
    ];

    /**
     * Casting tipe kolom (non-string saja).
     *
     * @var array<string,string>
     */
    protected $casts = [
        'kurs' => 'decimal:2',
        'sawalrp' => 'decimal:2',
        'sawalva' => 'decimal:2',
        'mutasidr' => 'decimal:2',
        'mutasicr' => 'decimal:2',
        'sahirrp' => 'decimal:2',
        'sahirva' => 'decimal:2',
        'saldobuku' => 'decimal:2',
        'saldomin' => 'decimal:2',
        'saldoavg' => 'decimal:2',
        'saldomax' => 'decimal:2',
        'mineom' => 'decimal:2',
        'avgeom' => 'decimal:2',
        'maxeom' => 'decimal:2',
        'sahireoy' => 'decimal:2',
        'sahireom' => 'decimal:2',
        'saldoblok' => 'decimal:2',
        'pccode' => 'decimal:2',
        'rate' => 'decimal:2',
        'nisbah' => 'decimal:2',
        'spcnisbah' => 'decimal:2',
        'bh' => 'decimal:2',
        'akumbh' => 'decimal:2',
        'tax' => 'decimal:2',
        'bheom' => 'decimal:2',
        'taxeom' => 'decimal:2',
        'brsbuku' => 'decimal:2',
        'bukuke' => 'decimal:2',
        'setfix' => 'decimal:2',
        'bhhtg' => 'decimal:2',
        'totbhhtg' => 'decimal:2',
        'bhacru' => 'decimal:2',
        'totbhacru' => 'decimal:2',
        'bhbayar' => 'decimal:2',
        'taxbayar' => 'decimal:2',
        'hari' => 'decimal:2',
        'masa' => 'decimal:2',
        'sisamasa' => 'decimal:2',
        'harinext' => 'decimal:2',
        'taxhtg' => 'decimal:2',
        'tottaxhtg' => 'decimal:2',
        'tothari' => 'decimal:2',
        'saldobh' => 'decimal:2',
    ];
}
