<?php

declare(strict_types=1);

namespace App\Models\Mci\Saving;

use App\Models\Mci\MciBaseModel;

/**
 * Model MCI: SETUPBUKUH
 * --------------------------------------------------------------------------
 * Domain   : Saving
 * Tabel    : [dbo].[SETUPBUKUH]
 * Kolom    : 2
 * Sumber   : Mapping_MCI_MAR26_01042026.sql / MCI_MAR26_01042026.xlsx
 *
 * CATATAN:
 *  - Model ini READ-ONLY (lihat trait ReadOnlyModel di MciBaseModel).
 *  - Koneksi default `dashboard_data` (SQL Server), otomatis ke DB bulan terbaru.
 *  - Kolom dengan spasi/karakter khusus diakses via:
 *    $model->getAttribute('NAMA KOLOM')  atau  $model->{'NAMA KOLOM'}.
 *
 * @property string|null $kdbuku type: varchar(2)
 * @property string|null $kdformat type: varchar(1)
 */
class Setupbukuh extends MciBaseModel
{
    /**
     * Nama tabel (case-sensitive di SQL Server).
     */
    protected $table = 'SETUPBUKUH';

    /**
     * Daftar LENGKAP kolom sesuai database (2 kolom).
     * Model ini read-only, $fillable hanya untuk dokumentasi & IDE helper.
     *
     * @var array<int,string>
     */
    protected $fillable = [
        'kdbuku',
        'kdformat',
    ];

    /**
     * Casting tipe kolom (non-string saja).
     *
     * @var array<string,string>
     */
    protected $casts = [

    ];
}
