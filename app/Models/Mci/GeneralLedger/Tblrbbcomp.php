<?php

declare(strict_types=1);

namespace App\Models\Mci\GeneralLedger;

use App\Models\Mci\MciBaseModel;

/**
 * Model MCI: TBLRBBCOMP
 * --------------------------------------------------------------------------
 * Domain   : GL / Accounting
 * Tabel    : [dbo].[TBLRBBCOMP]
 * Kolom    : 20
 * Sumber   : Mapping_MCI_MAR26_01042026.sql / MCI_MAR26_01042026.xlsx
 *
 * CATATAN:
 *  - Model ini READ-ONLY (lihat trait ReadOnlyModel di MciBaseModel).
 *  - Koneksi default `dashboard_data` (SQL Server), otomatis ke DB bulan terbaru.
 *  - Kolom dengan spasi/karakter khusus diakses via:
 *    $model->getAttribute('NAMA KOLOM')  atau  $model->{'NAMA KOLOM'}.
 *
 * @property string $thrkat type: varchar(4)
 * @property string|null $semester1 type: varchar(8)
 * @property string|null $semester2 type: varchar(8)
 * @property string|null $blnmulai type: char(2)
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
 * @property string|null $periode_1 type: char(6)
 * @property string|null $periode_2 type: char(6)
 * @property string|null $periode_3 type: char(6)
 * @property string|null $periode_4 type: char(6)
 * @property string|null $periode_5 type: char(6)
 * @property string|null $periode_6 type: char(6)
 */
class Tblrbbcomp extends MciBaseModel
{
    /**
     * Nama tabel (case-sensitive di SQL Server).
     */
    protected $table = 'TBLRBBCOMP';

    /**
     * Daftar LENGKAP kolom sesuai database (20 kolom).
     * Model ini read-only, $fillable hanya untuk dokumentasi & IDE helper.
     *
     * @var array<int,string>
     */
    protected $fillable = [
        'thrkat',
        'semester1',
        'semester2',
        'blnmulai',
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
        'periode_1',
        'periode_2',
        'periode_3',
        'periode_4',
        'periode_5',
        'periode_6',
    ];

    /**
     * Casting tipe kolom (non-string saja).
     *
     * @var array<string,string>
     */
    protected $casts = [

    ];
}
