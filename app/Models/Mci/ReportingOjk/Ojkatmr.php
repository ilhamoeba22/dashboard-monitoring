<?php

declare(strict_types=1);

namespace App\Models\Mci\ReportingOjk;

use App\Models\Mci\MciBaseModel;

/**
 * Model MCI: OJKATMR
 * --------------------------------------------------------------------------
 * Domain   : Reporting OJK/BI
 * Tabel    : [dbo].[OJKATMR]
 * Kolom    : 5
 * Sumber   : Mapping_MCI_MAR26_01042026.sql / MCI_MAR26_01042026.xlsx
 *
 * CATATAN:
 *  - Model ini READ-ONLY (lihat trait ReadOnlyModel di MciBaseModel).
 *  - Koneksi default `dashboard_data` (SQL Server), otomatis ke DB bulan terbaru.
 *  - Kolom dengan spasi/karakter khusus diakses via:
 *    $model->getAttribute('NAMA KOLOM')  atau  $model->{'NAMA KOLOM'}.
 *
 * @property string $periode type: varchar(8)
 * @property string|null $kode type: varchar(10)
 * @property string|null $ket type: varchar(50)
 * @property string|null $bobot type: numeric(9)
 * @property string|null $nilai type: numeric(13)
 */
class Ojkatmr extends MciBaseModel
{
    /**
     * Nama tabel (case-sensitive di SQL Server).
     */
    protected $table = 'OJKATMR';

    /**
     * Daftar LENGKAP kolom sesuai database (5 kolom).
     * Model ini read-only, $fillable hanya untuk dokumentasi & IDE helper.
     *
     * @var array<int,string>
     */
    protected $fillable = [
        'periode',
        'kode',
        'ket',
        'bobot',
        'nilai',
    ];

    /**
     * Casting tipe kolom (non-string saja).
     *
     * @var array<string,string>
     */
    protected $casts = [
        'bobot' => 'decimal:2',
        'nilai' => 'decimal:2',
    ];
}
