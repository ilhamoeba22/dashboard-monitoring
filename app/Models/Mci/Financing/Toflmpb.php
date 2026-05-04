<?php

declare(strict_types=1);

namespace App\Models\Mci\Financing;

use App\Models\Mci\MciBaseModel;

/**
 * Model MCI: TOFLMPB
 * --------------------------------------------------------------------------
 * Domain   : Financing / Loan
 * Tabel    : [dbo].[TOFLMPB]
 * Kolom    : 27
 * Sumber   : Mapping_MCI_MAR26_01042026.sql / MCI_MAR26_01042026.xlsx
 *
 * CATATAN:
 *  - Model ini READ-ONLY (lihat trait ReadOnlyModel di MciBaseModel).
 *  - Koneksi default `dashboard_data` (SQL Server), otomatis ke DB bulan terbaru.
 *  - Kolom dengan spasi/karakter khusus diakses via:
 *    $model->getAttribute('NAMA KOLOM')  atau  $model->{'NAMA KOLOM'}.
 *
 * @property string $nokontrak type: varchar(11)
 * @property string $kdtag type: varchar(1)
 * @property string|null $nomawal type: numeric(9)
 * @property string|null $cc type: varchar(2)
 * @property string|null $gp type: numeric(5)
 * @property string|null $frek type: numeric(5)
 * @property string|null $bln type: numeric(5)
 * @property string|null $tiaptgl type: varchar(2)
 * @property string|null $kdtagih type: varchar(1)
 * @property string|null $tglawal type: varchar(8)
 * @property string|null $tgltagih type: varchar(8)
 * @property string|null $tgltagihn type: varchar(8)
 * @property string|null $tgljtempo type: varchar(8)
 * @property string|null $angs type: numeric(9)
 * @property string|null $dpd type: numeric(5)
 * @property string|null $ststag type: varchar(1)
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
 * @property string|null $kdsi type: varchar(10)
 */
class Toflmpb extends MciBaseModel
{
    /**
     * Nama tabel (case-sensitive di SQL Server).
     */
    protected $table = 'TOFLMPB';

    /**
     * Daftar LENGKAP kolom sesuai database (27 kolom).
     * Model ini read-only, $fillable hanya untuk dokumentasi & IDE helper.
     *
     * @var array<int,string>
     */
    protected $fillable = [
        'nokontrak',
        'kdtag',
        'nomawal',
        'cc',
        'gp',
        'frek',
        'bln',
        'tiaptgl',
        'kdtagih',
        'tglawal',
        'tgltagih',
        'tgltagihn',
        'tgljtempo',
        'angs',
        'dpd',
        'ststag',
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
    ];

    /**
     * Casting tipe kolom (non-string saja).
     *
     * @var array<string,string>
     */
    protected $casts = [
        'nomawal' => 'decimal:2',
        'gp' => 'decimal:2',
        'frek' => 'decimal:2',
        'bln' => 'decimal:2',
        'angs' => 'decimal:2',
        'dpd' => 'decimal:2',
    ];
}
