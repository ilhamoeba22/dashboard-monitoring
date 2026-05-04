<?php

declare(strict_types=1);

namespace App\Models\Mci\Aruskas;

use App\Models\Mci\MciBaseModel;

/**
 * Model MCI: TOFTRAK1
 * --------------------------------------------------------------------------
 * Domain   : Aruskas / IRA
 * Tabel    : [dbo].[TOFTRAK1]
 * Kolom    : 14
 * Sumber   : Mapping_MCI_MAR26_01042026.sql / MCI_MAR26_01042026.xlsx
 *
 * CATATAN:
 *  - Model ini READ-ONLY (lihat trait ReadOnlyModel di MciBaseModel).
 *  - Koneksi default `dashboard_data` (SQL Server), otomatis ke DB bulan terbaru.
 *  - Kolom dengan spasi/karakter khusus diakses via:
 *    $model->getAttribute('NAMA KOLOM')  atau  $model->{'NAMA KOLOM'}.
 *
 * @property string|null $kdloc type: char(2)
 * @property string|null $akt_rak type: varchar(7)
 * @property string|null $kdloc_lawan type: char(2)
 * @property string|null $pas_rak type: varchar(7)
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
class Toftrak1 extends MciBaseModel
{
    /**
     * Nama tabel (case-sensitive di SQL Server).
     */
    protected $table = 'TOFTRAK1';

    /**
     * Daftar LENGKAP kolom sesuai database (14 kolom).
     * Model ini read-only, $fillable hanya untuk dokumentasi & IDE helper.
     *
     * @var array<int,string>
     */
    protected $fillable = [
        'kdloc',
        'akt_rak',
        'kdloc_lawan',
        'pas_rak',
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
