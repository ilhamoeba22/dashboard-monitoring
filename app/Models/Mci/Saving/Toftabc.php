<?php

declare(strict_types=1);

namespace App\Models\Mci\Saving;

use App\Models\Mci\MciBaseModel;

/**
 * Model MCI: TOFTABC
 * --------------------------------------------------------------------------
 * Domain   : Saving
 * Tabel    : [dbo].[TOFTABC]
 * Kolom    : 47
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
 * @property string|null $fnama type: varchar(100)
 * @property string|null $kodecab type: varchar(3)
 * @property string|null $kodeloc type: varchar(2)
 * @property string|null $kodekas type: varchar(2)
 * @property string|null $kodeprd type: varchar(2)
 * @property string|null $cc type: varchar(2)
 * @property string|null $tglbuka type: varchar(8)
 * @property string|null $tgltutup type: varchar(8)
 * @property string|null $tgltrnakh type: varchar(8)
 * @property string|null $mutasidr type: numeric(9)
 * @property string|null $mutasicr type: numeric(9)
 * @property string|null $sahirrp type: numeric(9)
 * @property string|null $sahirva type: numeric(9)
 * @property string|null $saldobuku type: numeric(9)
 * @property string|null $stsrest type: varchar(1)
 * @property string|null $kodebuku type: varchar(2)
 * @property string|null $stsacc type: varchar(1)
 * @property string|null $stsblok type: varchar(1)
 * @property string $terkait type: varchar(1)
 * @property string|null $pccode type: numeric(5)
 * @property string|null $noaclama type: varchar(12)
 * @property string|null $tglbh type: varchar(8)
 * @property string|null $bh type: numeric(9)
 * @property string|null $tax type: numeric(9)
 * @property string|null $brsbuku type: numeric(5)
 * @property string|null $bukuke type: numeric(5)
 * @property string|null $hari type: numeric(5)
 * @property string|null $trnke type: numeric(5)
 * @property string|null $tglexp type: varchar(8)
 * @property string|null $masa type: numeric(5)
 * @property string|null $sisamasa type: numeric(5)
 * @property string|null $hal type: numeric(5)
 * @property string|null $saldoblok type: numeric(9)
 * @property string|null $tariktunai type: numeric(9)
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
 * @property string|null $grouptab type: varchar(20)
 */
class Toftabc extends MciBaseModel
{
    /**
     * Nama tabel (case-sensitive di SQL Server).
     */
    protected $table = 'TOFTABC';

    /**
     * Daftar LENGKAP kolom sesuai database (47 kolom).
     * Model ini read-only, $fillable hanya untuk dokumentasi & IDE helper.
     *
     * @var array<int,string>
     */
    protected $fillable = [
        'notab',
        'nocif',
        'fnama',
        'kodecab',
        'kodeloc',
        'kodekas',
        'kodeprd',
        'cc',
        'tglbuka',
        'tgltutup',
        'tgltrnakh',
        'mutasidr',
        'mutasicr',
        'sahirrp',
        'sahirva',
        'saldobuku',
        'stsrest',
        'kodebuku',
        'stsacc',
        'stsblok',
        'terkait',
        'pccode',
        'noaclama',
        'tglbh',
        'bh',
        'tax',
        'brsbuku',
        'bukuke',
        'hari',
        'trnke',
        'tglexp',
        'masa',
        'sisamasa',
        'hal',
        'saldoblok',
        'tariktunai',
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
        'grouptab',
    ];

    /**
     * Casting tipe kolom (non-string saja).
     *
     * @var array<string,string>
     */
    protected $casts = [
        'mutasidr' => 'decimal:2',
        'mutasicr' => 'decimal:2',
        'sahirrp' => 'decimal:2',
        'sahirva' => 'decimal:2',
        'saldobuku' => 'decimal:2',
        'pccode' => 'decimal:2',
        'bh' => 'decimal:2',
        'tax' => 'decimal:2',
        'brsbuku' => 'decimal:2',
        'bukuke' => 'decimal:2',
        'hari' => 'decimal:2',
        'trnke' => 'decimal:2',
        'masa' => 'decimal:2',
        'sisamasa' => 'decimal:2',
        'hal' => 'decimal:2',
        'saldoblok' => 'decimal:2',
        'tariktunai' => 'decimal:2',
    ];
}
