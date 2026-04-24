<?php

namespace App\Models\Mci\Channel;

use App\Models\Mci\MciBaseModel;

/**
 * Model MCI: TMPEDCNOTAB
 * --------------------------------------------------------------------------
 * Domain   : Channel / ATM / Card
 * Tabel    : [dbo].[TMPEDCNOTAB]
 * Kolom    : 1
 * Sumber   : Mapping_MCI_MAR26_01042026.sql / MCI_MAR26_01042026.xlsx
 *
 * CATATAN:
 *  - Model ini READ-ONLY (lihat trait ReadOnlyModel di MciBaseModel).
 *  - Koneksi default `dashboard_data` (SQL Server), otomatis ke DB bulan terbaru.
 *  - Kolom dengan spasi/karakter khusus diakses via:
 *    $model->getAttribute('NAMA KOLOM')  atau  $model->{'NAMA KOLOM'}.
 *
 * @property string|null $noacc  type: varchar(10)
 */
class Tmpedcnotab extends MciBaseModel
{
    /**
     * Nama tabel (case-sensitive di SQL Server).
     */
    protected $table = 'TMPEDCNOTAB';

    /**
     * Daftar LENGKAP kolom sesuai database (1 kolom).
     * Model ini read-only, $fillable hanya untuk dokumentasi & IDE helper.
     *
     * @var array<int,string>
     */
    protected $fillable = [
        'noacc',
    ];

    /**
     * Casting tipe kolom (non-string saja).
     *
     * @var array<string,string>
     */
    protected $casts = [

    ];
}
