<?php

declare(strict_types=1);

namespace App\Models\Mci\Lainnya;

use App\Models\Mci\MciBaseModel;

/**
 * Model MCI: NOMASTPRD
 * --------------------------------------------------------------------------
 * Domain   : Lainnya
 * Tabel    : [dbo].[NOMASTPRD]
 * Kolom    : 5
 * Sumber   : Mapping_MCI_MAR26_01042026.sql / MCI_MAR26_01042026.xlsx
 *
 * CATATAN:
 *  - Model ini READ-ONLY (lihat trait ReadOnlyModel di MciBaseModel).
 *  - Koneksi default `dashboard_data` (SQL Server), otomatis ke DB bulan terbaru.
 *  - Kolom dengan spasi/karakter khusus diakses via:
 *    $model->getAttribute('NAMA KOLOM')  atau  $model->{'NAMA KOLOM'}.
 *
 * @property string|null $kdcab type: varchar(4)
 * @property string $kdloc type: varchar(2)
 * @property string|null $kdprd type: varchar(2)
 * @property string|null $kdmodul type: varchar(1)
 * @property string|null $nomor type: numeric(9)
 */
class Nomastprd extends MciBaseModel
{
    /**
     * Nama tabel (case-sensitive di SQL Server).
     */
    protected $table = 'NOMASTPRD';

    /**
     * Daftar LENGKAP kolom sesuai database (5 kolom).
     * Model ini read-only, $fillable hanya untuk dokumentasi & IDE helper.
     *
     * @var array<int,string>
     */
    protected $fillable = [
        'kdcab',
        'kdloc',
        'kdprd',
        'kdmodul',
        'nomor',
    ];

    /**
     * Casting tipe kolom (non-string saja).
     *
     * @var array<string,string>
     */
    protected $casts = [
        'nomor' => 'decimal:2',
    ];
}
