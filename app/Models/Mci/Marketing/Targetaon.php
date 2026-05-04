<?php

declare(strict_types=1);

namespace App\Models\Mci\Marketing;

use App\Models\Mci\MciBaseModel;

/**
 * Model MCI: TARGETAON
 * --------------------------------------------------------------------------
 * Domain   : AO / Marketing
 * Tabel    : [dbo].[TARGETAON]
 * Kolom    : 19
 * Sumber   : Mapping_MCI_MAR26_01042026.sql / MCI_MAR26_01042026.xlsx
 *
 * CATATAN:
 *  - Model ini READ-ONLY (lihat trait ReadOnlyModel di MciBaseModel).
 *  - Koneksi default `dashboard_data` (SQL Server), otomatis ke DB bulan terbaru.
 *  - Kolom dengan spasi/karakter khusus diakses via:
 *    $model->getAttribute('NAMA KOLOM')  atau  $model->{'NAMA KOLOM'}.
 *
 * @property string|null $thn type: varchar(4)
 * @property string|null $bln type: varchar(2)
 * @property string|null $kdao type: varchar(10)
 * @property string|null $kdrek type: varchar(1)
 * @property string|null $kdprd type: varchar(2)
 * @property string|null $jw type: numeric(5)
 * @property string|null $target type: numeric(9)
 * @property string|null $realisasi type: numeric(9)
 * @property string|null $deviasi type: numeric(5)
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
 */
class Targetaon extends MciBaseModel
{
    /**
     * Nama tabel (case-sensitive di SQL Server).
     */
    protected $table = 'TARGETAON';

    /**
     * Daftar LENGKAP kolom sesuai database (19 kolom).
     * Model ini read-only, $fillable hanya untuk dokumentasi & IDE helper.
     *
     * @var array<int,string>
     */
    protected $fillable = [
        'thn',
        'bln',
        'kdao',
        'kdrek',
        'kdprd',
        'jw',
        'target',
        'realisasi',
        'deviasi',
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
        'jw' => 'decimal:2',
        'target' => 'decimal:2',
        'realisasi' => 'decimal:2',
        'deviasi' => 'decimal:2',
    ];
}
