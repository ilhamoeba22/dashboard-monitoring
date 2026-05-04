<?php

declare(strict_types=1);

namespace App\Models\Mci\Financing;

use App\Models\Mci\MciBaseModel;

/**
 * Model MCI: TOFLMCOL
 * --------------------------------------------------------------------------
 * Domain   : Financing / Loan
 * Tabel    : [dbo].[TOFLMCOL]
 * Kolom    : 6
 * Sumber   : Mapping_MCI_MAR26_01042026.sql / MCI_MAR26_01042026.xlsx
 *
 * CATATAN:
 *  - Model ini READ-ONLY (lihat trait ReadOnlyModel di MciBaseModel).
 *  - Koneksi default `dashboard_data` (SQL Server), otomatis ke DB bulan terbaru.
 *  - Kolom dengan spasi/karakter khusus diakses via:
 *    $model->getAttribute('NAMA KOLOM')  atau  $model->{'NAMA KOLOM'}.
 *
 * @property string|null $nokontrak type: varchar(11)
 * @property string|null $tglubah type: varchar(8)
 * @property string|null $jnscol type: varchar(3)
 * @property string|null $col_lama type: varchar(1)
 * @property string|null $col_baru type: varchar(1)
 * @property string|null $kdcol type: varchar(2)
 */
class Toflmcol extends MciBaseModel
{
    /**
     * Nama tabel (case-sensitive di SQL Server).
     */
    protected $table = 'TOFLMCOL';

    /**
     * Daftar LENGKAP kolom sesuai database (6 kolom).
     * Model ini read-only, $fillable hanya untuk dokumentasi & IDE helper.
     *
     * @var array<int,string>
     */
    protected $fillable = [
        'nokontrak',
        'tglubah',
        'jnscol',
        'col_lama',
        'col_baru',
        'kdcol',
    ];

    /**
     * Casting tipe kolom (non-string saja).
     *
     * @var array<string,string>
     */
    protected $casts = [

    ];
}
