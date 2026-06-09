<?php

declare(strict_types=1);

namespace App\Models\Mci\Ppap;

use App\Models\Mci\MciBaseModel;

/**
 * Model MCI: TOFEXCCOL
 * --------------------------------------------------------------------------
 * Domain   : PPKA / DPD / Coll
 * Tabel    : [dbo].[TOFEXCCOL]
 * Kolom    : 14
 * Sumber   : Mapping_MCI_MAR26_01042026.sql / MCI_MAR26_01042026.xlsx
 *
 * CATATAN:
 *  - Model ini READ-ONLY (lihat trait ReadOnlyModel di MciBaseModel).
 *  - Koneksi default `dashboard_data` (SQL Server), otomatis ke DB bulan terbaru.
 *  - Kolom dengan spasi/karakter khusus diakses via:
 *    $model->getAttribute('NAMA KOLOM')  atau  $model->{'NAMA KOLOM'}.
 *
 * @property string $nocif type: varchar(9)
 * @property string|null $nokontrak type: varchar(11)
 * @property string|null $kdalasan type: varchar(10)
 * @property string|null $ket type: varchar(100)
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
class Tofexccol extends MciBaseModel
{
    /**
     * Nama tabel (case-sensitive di SQL Server).
     */
    protected $table = 'TOFEXCCOL';

    /**
     * Daftar LENGKAP kolom sesuai database (14 kolom).
     * Model ini read-only, $fillable hanya untuk dokumentasi & IDE helper.
     *
     * @var array<int,string>
     */
    protected $fillable = [
        'nocif',
        'nokontrak',
        'kdalasan',
        'ket',
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

    ];
}
