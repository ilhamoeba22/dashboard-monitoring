<?php

declare(strict_types=1);

namespace App\Models\Mci\BagiHasil;

use App\Models\Mci\MciBaseModel;

/**
 * Model MCI: TOFBHREKAP
 * --------------------------------------------------------------------------
 * Domain   : Bagi Hasil
 * Tabel    : [dbo].[TOFBHREKAP]
 * Kolom    : 13
 * Sumber   : Mapping_MCI_MAR26_01042026.sql / MCI_MAR26_01042026.xlsx
 *
 * CATATAN:
 *  - Model ini READ-ONLY (lihat trait ReadOnlyModel di MciBaseModel).
 *  - Koneksi default `dashboard_data` (SQL Server), otomatis ke DB bulan terbaru.
 *  - Kolom dengan spasi/karakter khusus diakses via:
 *    $model->getAttribute('NAMA KOLOM')  atau  $model->{'NAMA KOLOM'}.
 *
 * @property string|null $thnbln type: varchar(6)
 * @property string|null $prd type: varchar(3)
 * @property string|null $kdprd type: varchar(2)
 * @property string|null $jw type: numeric(5)
 * @property string|null $nisbah type: numeric(5)
 * @property string|null $spread type: numeric(5)
 * @property string|null $saldorata type: numeric(9)
 * @property string|null $bhcust type: numeric(9)
 * @property string|null $equivrate type: numeric(9)
 * @property string|null $nisbahbank type: numeric(5)
 * @property string|null $bhbank type: numeric(9)
 * @property string|null $bhrekap type: numeric(9)
 * @property string|null $ubahrate type: numeric(9)
 */
class Tofbhrekap extends MciBaseModel
{
    /**
     * Nama tabel (case-sensitive di SQL Server).
     */
    protected $table = 'TOFBHREKAP';

    /**
     * Daftar LENGKAP kolom sesuai database (13 kolom).
     * Model ini read-only, $fillable hanya untuk dokumentasi & IDE helper.
     *
     * @var array<int,string>
     */
    protected $fillable = [
        'thnbln',
        'prd',
        'kdprd',
        'jw',
        'nisbah',
        'spread',
        'saldorata',
        'bhcust',
        'equivrate',
        'nisbahbank',
        'bhbank',
        'bhrekap',
        'ubahrate',
    ];

    /**
     * Casting tipe kolom (non-string saja).
     *
     * @var array<string,string>
     */
    protected $casts = [
        'jw' => 'decimal:2',
        'nisbah' => 'decimal:2',
        'spread' => 'decimal:2',
        'saldorata' => 'decimal:2',
        'bhcust' => 'decimal:2',
        'equivrate' => 'decimal:2',
        'nisbahbank' => 'decimal:2',
        'bhbank' => 'decimal:2',
        'bhrekap' => 'decimal:2',
        'ubahrate' => 'decimal:2',
    ];
}
