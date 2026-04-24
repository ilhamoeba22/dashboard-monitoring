<?php

namespace App\Models\Mci\Lainnya;

use App\Models\Mci\MciBaseModel;

/**
 * Model MCI: NOMASTER
 * --------------------------------------------------------------------------
 * Domain   : Lainnya
 * Tabel    : [dbo].[NOMASTER]
 * Kolom    : 6
 * Sumber   : Mapping_MCI_MAR26_01042026.sql / MCI_MAR26_01042026.xlsx
 *
 * CATATAN:
 *  - Model ini READ-ONLY (lihat trait ReadOnlyModel di MciBaseModel).
 *  - Koneksi default `dashboard_data` (SQL Server), otomatis ke DB bulan terbaru.
 *  - Kolom dengan spasi/karakter khusus diakses via:
 *    $model->getAttribute('NAMA KOLOM')  atau  $model->{'NAMA KOLOM'}.
 *
 * @property string $kdcab  type: varchar(3)
 * @property string|null $kdloc  type: varchar(2)
 * @property string|null $kdprd  type: varchar(2)
 * @property string|null $kdmodul  type: varchar(1)
 * @property string|null $nomor  type: numeric(9)
 * @property string|null $batch  type: numeric(5)
 */
class Nomaster extends MciBaseModel
{
    /**
     * Nama tabel (case-sensitive di SQL Server).
     */
    protected $table = 'NOMASTER';

    /**
     * Daftar LENGKAP kolom sesuai database (6 kolom).
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
        'batch',
    ];

    /**
     * Casting tipe kolom (non-string saja).
     *
     * @var array<string,string>
     */
    protected $casts = [
        'nomor' => 'decimal:2',
        'batch' => 'decimal:2',
    ];
}
