<?php

declare(strict_types=1);

namespace App\Models\Mci\Ppap;

use App\Models\Mci\MciBaseModel;

/**
 * Model MCI: TOFMPCOLHIST
 * --------------------------------------------------------------------------
 * Domain   : PPAP / DPD / Coll
 * Tabel    : [dbo].[TOFMPCOLHIST]
 * Kolom    : 8
 * Sumber   : Mapping_MCI_MAR26_01042026.sql / MCI_MAR26_01042026.xlsx
 *
 * CATATAN:
 *  - Model ini READ-ONLY (lihat trait ReadOnlyModel di MciBaseModel).
 *  - Koneksi default `dashboard_data` (SQL Server), otomatis ke DB bulan terbaru.
 *  - Kolom dengan spasi/karakter khusus diakses via:
 *    $model->getAttribute('NAMA KOLOM')  atau  $model->{'NAMA KOLOM'}.
 *
 * @property string $nokontrak type: varchar(11)
 * @property string|null $periode type: varchar(6)
 * @property string|null $collama type: varchar(1)
 * @property string|null $colbaru type: varchar(1)
 * @property string|null $ket type: varchar(200)
 * @property string|null $inpuser type: varchar(10)
 * @property string|null $inptgl type: varchar(14)
 * @property string|null $inpterm type: varchar(10)
 */
class Tofmpcolhist extends MciBaseModel
{
    /**
     * Nama tabel (case-sensitive di SQL Server).
     */
    protected $table = 'TOFMPCOLHIST';

    /**
     * Daftar LENGKAP kolom sesuai database (8 kolom).
     * Model ini read-only, $fillable hanya untuk dokumentasi & IDE helper.
     *
     * @var array<int,string>
     */
    protected $fillable = [
        'nokontrak',
        'periode',
        'collama',
        'colbaru',
        'ket',
        'inpuser',
        'inptgl',
        'inpterm',
    ];

    /**
     * Casting tipe kolom (non-string saja).
     *
     * @var array<string,string>
     */
    protected $casts = [

    ];
}
