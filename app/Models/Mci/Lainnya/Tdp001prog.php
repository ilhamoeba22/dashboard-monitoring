<?php

declare(strict_types=1);

namespace App\Models\Mci\Lainnya;

use App\Models\Mci\MciBaseModel;

/**
 * Model MCI: TDP001PROG
 * --------------------------------------------------------------------------
 * Domain   : Lainnya
 * Tabel    : [dbo].[TDP001PROG]
 * Kolom    : 6
 * Sumber   : Mapping_MCI_MAR26_01042026.sql / MCI_MAR26_01042026.xlsx
 *
 * CATATAN:
 *  - Model ini READ-ONLY (lihat trait ReadOnlyModel di MciBaseModel).
 *  - Koneksi default `dashboard_data` (SQL Server), otomatis ke DB bulan terbaru.
 *  - Kolom dengan spasi/karakter khusus diakses via:
 *    $model->getAttribute('NAMA KOLOM')  atau  $model->{'NAMA KOLOM'}.
 *
 * @property string $kdprd type: char(2)
 * @property string $jw type: numeric(5)
 * @property string $kdjw type: char(1)
 * @property string|null $nominal type: numeric(9)
 * @property string|null $nisbahold type: numeric(5)
 * @property string|null $nisbahnew type: numeric(5)
 */
class Tdp001prog extends MciBaseModel
{
    /**
     * Nama tabel (case-sensitive di SQL Server).
     */
    protected $table = 'TDP001PROG';

    /**
     * Daftar LENGKAP kolom sesuai database (6 kolom).
     * Model ini read-only, $fillable hanya untuk dokumentasi & IDE helper.
     *
     * @var array<int,string>
     */
    protected $fillable = [
        'kdprd',
        'jw',
        'kdjw',
        'nominal',
        'nisbahold',
        'nisbahnew',
    ];

    /**
     * Casting tipe kolom (non-string saja).
     *
     * @var array<string,string>
     */
    protected $casts = [
        'jw' => 'decimal:2',
        'nominal' => 'decimal:2',
        'nisbahold' => 'decimal:2',
        'nisbahnew' => 'decimal:2',
    ];
}
