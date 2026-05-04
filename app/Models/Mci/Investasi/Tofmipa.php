<?php

declare(strict_types=1);

namespace App\Models\Mci\Investasi;

use App\Models\Mci\MciBaseModel;

/**
 * Model MCI: TOFMIPA
 * --------------------------------------------------------------------------
 * Domain   : Investasi / Saham
 * Tabel    : [dbo].[TOFMIPA]
 * Kolom    : 17
 * Sumber   : Mapping_MCI_MAR26_01042026.sql / MCI_MAR26_01042026.xlsx
 *
 * CATATAN:
 *  - Model ini READ-ONLY (lihat trait ReadOnlyModel di MciBaseModel).
 *  - Koneksi default `dashboard_data` (SQL Server), otomatis ke DB bulan terbaru.
 *  - Kolom dengan spasi/karakter khusus diakses via:
 *    $model->getAttribute('NAMA KOLOM')  atau  $model->{'NAMA KOLOM'}.
 *
 * @property string $periode type: varchar(6)
 * @property string|null $kdao type: varchar(10)
 * @property string|null $nmao type: varchar(30)
 * @property string|null $kdprog type: varchar(2)
 * @property string|null $qty_deb type: numeric(5)
 * @property string|null $os type: numeric(9)
 * @property string|null $npf type: numeric(5)
 * @property string|null $bonus_loan type: numeric(9)
 * @property string|null $qty_tab type: numeric(5)
 * @property string|null $saratab_baru type: numeric(9)
 * @property string|null $saratab_lama type: numeric(9)
 * @property string|null $qty_dep type: numeric(5)
 * @property string|null $saradep_baru type: numeric(9)
 * @property string|null $saradep_lama type: numeric(9)
 * @property string|null $bonus_tab type: numeric(9)
 * @property string|null $bonus_dep type: numeric(9)
 * @property string|null $batch type: numeric(5)
 */
class Tofmipa extends MciBaseModel
{
    /**
     * Nama tabel (case-sensitive di SQL Server).
     */
    protected $table = 'TOFMIPA';

    /**
     * Daftar LENGKAP kolom sesuai database (17 kolom).
     * Model ini read-only, $fillable hanya untuk dokumentasi & IDE helper.
     *
     * @var array<int,string>
     */
    protected $fillable = [
        'periode',
        'kdao',
        'nmao',
        'kdprog',
        'qty_deb',
        'os',
        'npf',
        'bonus_loan',
        'qty_tab',
        'saratab_baru',
        'saratab_lama',
        'qty_dep',
        'saradep_baru',
        'saradep_lama',
        'bonus_tab',
        'bonus_dep',
        'batch',
    ];

    /**
     * Casting tipe kolom (non-string saja).
     *
     * @var array<string,string>
     */
    protected $casts = [
        'qty_deb' => 'decimal:2',
        'os' => 'decimal:2',
        'npf' => 'decimal:2',
        'bonus_loan' => 'decimal:2',
        'qty_tab' => 'decimal:2',
        'saratab_baru' => 'decimal:2',
        'saratab_lama' => 'decimal:2',
        'qty_dep' => 'decimal:2',
        'saradep_baru' => 'decimal:2',
        'saradep_lama' => 'decimal:2',
        'bonus_tab' => 'decimal:2',
        'bonus_dep' => 'decimal:2',
        'batch' => 'decimal:2',
    ];
}
