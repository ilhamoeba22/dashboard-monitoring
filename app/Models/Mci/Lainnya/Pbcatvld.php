<?php

declare(strict_types=1);

namespace App\Models\Mci\Lainnya;

use App\Models\Mci\MciBaseModel;

/**
 * Model MCI: pbcatvld
 * --------------------------------------------------------------------------
 * Domain   : Lainnya
 * Tabel    : [dbo].[pbcatvld]
 * Kolom    : 5
 * Sumber   : Mapping_MCI_MAR26_01042026.sql / MCI_MAR26_01042026.xlsx
 *
 * CATATAN:
 *  - Model ini READ-ONLY (lihat trait ReadOnlyModel di MciBaseModel).
 *  - Koneksi default `dashboard_data` (SQL Server), otomatis ke DB bulan terbaru.
 *  - Kolom dengan spasi/karakter khusus diakses via:
 *    $model->getAttribute('NAMA KOLOM')  atau  $model->{'NAMA KOLOM'}.
 *
 * @property string $pbv_name type: varchar(30)
 * @property string $pbv_vald type: varchar(254)
 * @property int $pbv_type type: smallint(2)
 * @property int|null $pbv_cntr type: int(4)
 * @property string|null $pbv_msg type: varchar(254)
 */
class Pbcatvld extends MciBaseModel
{
    /**
     * Nama tabel (case-sensitive di SQL Server).
     */
    protected $table = 'pbcatvld';

    /**
     * Daftar LENGKAP kolom sesuai database (5 kolom).
     * Model ini read-only, $fillable hanya untuk dokumentasi & IDE helper.
     *
     * @var array<int,string>
     */
    protected $fillable = [
        'pbv_name',
        'pbv_vald',
        'pbv_type',
        'pbv_cntr',
        'pbv_msg',
    ];

    /**
     * Casting tipe kolom (non-string saja).
     *
     * @var array<string,string>
     */
    protected $casts = [
        'pbv_type' => 'integer',
        'pbv_cntr' => 'integer',
    ];
}
