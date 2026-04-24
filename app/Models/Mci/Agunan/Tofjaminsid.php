<?php

namespace App\Models\Mci\Agunan;

use App\Models\Mci\MciBaseModel;

/**
 * Model MCI: TOFJAMINSID
 * --------------------------------------------------------------------------
 * Domain   : Agunan / Jaminan
 * Tabel    : [dbo].[TOFJAMINSID]
 * Kolom    : 3
 * Sumber   : Mapping_MCI_MAR26_01042026.sql / MCI_MAR26_01042026.xlsx
 *
 * CATATAN:
 *  - Model ini READ-ONLY (lihat trait ReadOnlyModel di MciBaseModel).
 *  - Koneksi default `dashboard_data` (SQL Server), otomatis ke DB bulan terbaru.
 *  - Kolom dengan spasi/karakter khusus diakses via:
 *    $model->getAttribute('NAMA KOLOM')  atau  $model->{'NAMA KOLOM'}.
 *
 * @property string|null $noreg  type: varchar(5)
 * @property string|null $urut  type: numeric(5)
 * @property string|null $id_agunan  type: varchar(40)
 */
class Tofjaminsid extends MciBaseModel
{
    /**
     * Nama tabel (case-sensitive di SQL Server).
     */
    protected $table = 'TOFJAMINSID';

    /**
     * Daftar LENGKAP kolom sesuai database (3 kolom).
     * Model ini read-only, $fillable hanya untuk dokumentasi & IDE helper.
     *
     * @var array<int,string>
     */
    protected $fillable = [
        'noreg',
        'urut',
        'id_agunan',
    ];

    /**
     * Casting tipe kolom (non-string saja).
     *
     * @var array<string,string>
     */
    protected $casts = [
        'urut' => 'decimal:2',
    ];
}
