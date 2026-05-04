<?php

declare(strict_types=1);

namespace App\Models\Mci\Financing;

use App\Models\Mci\MciBaseModel;

/**
 * Model MCI: TOFLMDPD
 * --------------------------------------------------------------------------
 * Domain   : Financing / Loan
 * Tabel    : [dbo].[TOFLMDPD]
 * Kolom    : 19
 * Sumber   : Mapping_MCI_MAR26_01042026.sql / MCI_MAR26_01042026.xlsx
 *
 * CATATAN:
 *  - Model ini READ-ONLY (lihat trait ReadOnlyModel di MciBaseModel).
 *  - Koneksi default `dashboard_data` (SQL Server), otomatis ke DB bulan terbaru.
 *  - Kolom dengan spasi/karakter khusus diakses via:
 *    $model->getAttribute('NAMA KOLOM')  atau  $model->{'NAMA KOLOM'}.
 *
 * @property string $periode type: varchar(6)
 * @property string|null $P_01_30 type: numeric(9)
 * @property string|null $P_30_60 type: numeric(9)
 * @property string|null $P_60_90 type: numeric(9)
 * @property string|null $P_90_120 type: numeric(9)
 * @property string|null $P_120_150 type: numeric(9)
 * @property string|null $P_150_180 type: numeric(9)
 * @property string|null $P_180_210 type: numeric(9)
 * @property string|null $P_210_240 type: numeric(9)
 * @property string|null $P_270 type: numeric(9)
 * @property string|null $B_01_30 type: numeric(9)
 * @property string|null $B_30_60 type: numeric(9)
 * @property string|null $B_60_90 type: numeric(9)
 * @property string|null $B_90_120 type: numeric(9)
 * @property string|null $B_120_150 type: numeric(9)
 * @property string|null $B_150_180 type: numeric(9)
 * @property string|null $B_180_210 type: numeric(9)
 * @property string|null $B_210_240 type: numeric(9)
 * @property string|null $B_270 type: numeric(9)
 */
class Toflmdpd extends MciBaseModel
{
    /**
     * Nama tabel (case-sensitive di SQL Server).
     */
    protected $table = 'TOFLMDPD';

    /**
     * Daftar LENGKAP kolom sesuai database (19 kolom).
     * Model ini read-only, $fillable hanya untuk dokumentasi & IDE helper.
     *
     * @var array<int,string>
     */
    protected $fillable = [
        'periode',
        'P_01_30',
        'P_30_60',
        'P_60_90',
        'P_90_120',
        'P_120_150',
        'P_150_180',
        'P_180_210',
        'P_210_240',
        'P_270',
        'B_01_30',
        'B_30_60',
        'B_60_90',
        'B_90_120',
        'B_120_150',
        'B_150_180',
        'B_180_210',
        'B_210_240',
        'B_270',
    ];

    /**
     * Casting tipe kolom (non-string saja).
     *
     * @var array<string,string>
     */
    protected $casts = [
        'P_01_30' => 'decimal:2',
        'P_30_60' => 'decimal:2',
        'P_60_90' => 'decimal:2',
        'P_90_120' => 'decimal:2',
        'P_120_150' => 'decimal:2',
        'P_150_180' => 'decimal:2',
        'P_180_210' => 'decimal:2',
        'P_210_240' => 'decimal:2',
        'P_270' => 'decimal:2',
        'B_01_30' => 'decimal:2',
        'B_30_60' => 'decimal:2',
        'B_60_90' => 'decimal:2',
        'B_90_120' => 'decimal:2',
        'B_120_150' => 'decimal:2',
        'B_150_180' => 'decimal:2',
        'B_180_210' => 'decimal:2',
        'B_210_240' => 'decimal:2',
        'B_270' => 'decimal:2',
    ];
}
