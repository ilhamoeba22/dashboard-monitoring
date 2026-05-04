<?php

declare(strict_types=1);

namespace App\Models\Mci\Lainnya;

use App\Models\Mci\MciBaseModel;

/**
 * Model MCI: pbcatfmt
 * --------------------------------------------------------------------------
 * Domain   : Lainnya
 * Tabel    : [dbo].[pbcatfmt]
 * Kolom    : 4
 * Sumber   : Mapping_MCI_MAR26_01042026.sql / MCI_MAR26_01042026.xlsx
 *
 * CATATAN:
 *  - Model ini READ-ONLY (lihat trait ReadOnlyModel di MciBaseModel).
 *  - Koneksi default `dashboard_data` (SQL Server), otomatis ke DB bulan terbaru.
 *  - Kolom dengan spasi/karakter khusus diakses via:
 *    $model->getAttribute('NAMA KOLOM')  atau  $model->{'NAMA KOLOM'}.
 *
 * @property string $pbf_name type: varchar(30)
 * @property string $pbf_frmt type: varchar(254)
 * @property int $pbf_type type: smallint(2)
 * @property int|null $pbf_cntr type: int(4)
 */
class Pbcatfmt extends MciBaseModel
{
    /**
     * Nama tabel (case-sensitive di SQL Server).
     */
    protected $table = 'pbcatfmt';

    /**
     * Daftar LENGKAP kolom sesuai database (4 kolom).
     * Model ini read-only, $fillable hanya untuk dokumentasi & IDE helper.
     *
     * @var array<int,string>
     */
    protected $fillable = [
        'pbf_name',
        'pbf_frmt',
        'pbf_type',
        'pbf_cntr',
    ];

    /**
     * Casting tipe kolom (non-string saja).
     *
     * @var array<string,string>
     */
    protected $casts = [
        'pbf_type' => 'integer',
        'pbf_cntr' => 'integer',
    ];
}
