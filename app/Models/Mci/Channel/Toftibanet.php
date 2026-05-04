<?php

declare(strict_types=1);

namespace App\Models\Mci\Channel;

use App\Models\Mci\MciBaseModel;

/**
 * Model MCI: TOFTIBANET
 * --------------------------------------------------------------------------
 * Domain   : Channel / ATM / Card
 * Tabel    : [dbo].[TOFTIBANET]
 * Kolom    : 19
 * Sumber   : Mapping_MCI_MAR26_01042026.sql / MCI_MAR26_01042026.xlsx
 *
 * CATATAN:
 *  - Model ini READ-ONLY (lihat trait ReadOnlyModel di MciBaseModel).
 *  - Koneksi default `dashboard_data` (SQL Server), otomatis ke DB bulan terbaru.
 *  - Kolom dengan spasi/karakter khusus diakses via:
 *    $model->getAttribute('NAMA KOLOM')  atau  $model->{'NAMA KOLOM'}.
 *
 * @property string $kdtrx type: char(2)
 * @property string|null $feebanksource type: numeric(5)
 * @property string|null $feebankdest type: numeric(5)
 * @property string|null $feemg type: numeric(5)
 * @property string|null $sbbdeposit type: varchar(10)
 * @property string|null $sbbtrx type: varchar(10)
 * @property string|null $sbbfeesource type: varchar(10)
 * @property string|null $sbbfeedest type: varchar(10)
 * @property string|null $sbbttpmg type: varchar(10)
 * @property string|null $stsrec type: char(1)
 * @property string|null $inpuser type: varchar(10)
 * @property string|null $inptgljam type: varchar(14)
 * @property string|null $inpterm type: varchar(10)
 * @property string|null $chguser type: varchar(10)
 * @property string|null $chgtgljam type: varchar(14)
 * @property string|null $chgterm type: varchar(10)
 * @property string|null $autuser type: varchar(10)
 * @property string|null $auttgljam type: varchar(14)
 * @property string|null $autterm type: varchar(10)
 */
class Toftibanet extends MciBaseModel
{
    /**
     * Nama tabel (case-sensitive di SQL Server).
     */
    protected $table = 'TOFTIBANET';

    /**
     * Daftar LENGKAP kolom sesuai database (19 kolom).
     * Model ini read-only, $fillable hanya untuk dokumentasi & IDE helper.
     *
     * @var array<int,string>
     */
    protected $fillable = [
        'kdtrx',
        'feebanksource',
        'feebankdest',
        'feemg',
        'sbbdeposit',
        'sbbtrx',
        'sbbfeesource',
        'sbbfeedest',
        'sbbttpmg',
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
    ];

    /**
     * Casting tipe kolom (non-string saja).
     *
     * @var array<string,string>
     */
    protected $casts = [
        'feebanksource' => 'decimal:2',
        'feebankdest' => 'decimal:2',
        'feemg' => 'decimal:2',
    ];
}
