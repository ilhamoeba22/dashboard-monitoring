<?php

declare(strict_types=1);

namespace App\Models\Mci\Financing;

use App\Models\Mci\MciBaseModel;

/**
 * Model MCI: TOFLMDROP
 * --------------------------------------------------------------------------
 * Domain   : Financing / Loan
 * Tabel    : [dbo].[TOFLMDROP]
 * Kolom    : 7
 * Sumber   : Mapping_MCI_MAR26_01042026.sql / MCI_MAR26_01042026.xlsx
 *
 * CATATAN:
 *  - Model ini READ-ONLY (lihat trait ReadOnlyModel di MciBaseModel).
 *  - Koneksi default `dashboard_data` (SQL Server), otomatis ke DB bulan terbaru.
 *  - Kolom dengan spasi/karakter khusus diakses via:
 *    $model->getAttribute('NAMA KOLOM')  atau  $model->{'NAMA KOLOM'}.
 *
 * @property string $nokontrak type: varchar(11)
 * @property string|null $tahapke type: numeric(5)
 * @property string|null $tgldrop type: varchar(8)
 * @property string|null $modal type: numeric(9)
 * @property string|null $laba type: numeric(9)
 * @property string|null $stsdrop type: varchar(1)
 * @property string|null $stslaba type: varchar(1)
 */
class Toflmdrop extends MciBaseModel
{
    /**
     * Nama tabel (case-sensitive di SQL Server).
     */
    protected $table = 'TOFLMDROP';

    /**
     * Daftar LENGKAP kolom sesuai database (7 kolom).
     * Model ini read-only, $fillable hanya untuk dokumentasi & IDE helper.
     *
     * @var array<int,string>
     */
    protected $fillable = [
        'nokontrak',
        'tahapke',
        'tgldrop',
        'modal',
        'laba',
        'stsdrop',
        'stslaba',
    ];

    /**
     * Casting tipe kolom (non-string saja).
     *
     * @var array<string,string>
     */
    protected $casts = [
        'tahapke' => 'decimal:2',
        'modal' => 'decimal:2',
        'laba' => 'decimal:2',
    ];
}
