<?php

declare(strict_types=1);

namespace App\Models\Mci\Aruskas;

use App\Models\Mci\MciBaseModel;

/**
 * Model MCI: TOFTRAKRATE
 * --------------------------------------------------------------------------
 * Domain   : Aruskas / IRA
 * Tabel    : [dbo].[TOFTRAKRATE]
 * Kolom    : 4
 * Sumber   : Mapping_MCI_MAR26_01042026.sql / MCI_MAR26_01042026.xlsx
 *
 * CATATAN:
 *  - Model ini READ-ONLY (lihat trait ReadOnlyModel di MciBaseModel).
 *  - Koneksi default `dashboard_data` (SQL Server), otomatis ke DB bulan terbaru.
 *  - Kolom dengan spasi/karakter khusus diakses via:
 *    $model->getAttribute('NAMA KOLOM')  atau  $model->{'NAMA KOLOM'}.
 *
 * @property string|null $rate type: numeric(5)
 * @property string|null $sbb_biaya type: varchar(7)
 * @property string|null $sbb_pend type: varchar(7)
 * @property string|null $stsproses type: varchar(1)
 */
class Toftrakrate extends MciBaseModel
{
    /**
     * Nama tabel (case-sensitive di SQL Server).
     */
    protected $table = 'TOFTRAKRATE';

    /**
     * Daftar LENGKAP kolom sesuai database (4 kolom).
     * Model ini read-only, $fillable hanya untuk dokumentasi & IDE helper.
     *
     * @var array<int,string>
     */
    protected $fillable = [
        'rate',
        'sbb_biaya',
        'sbb_pend',
        'stsproses',
    ];

    /**
     * Casting tipe kolom (non-string saja).
     *
     * @var array<string,string>
     */
    protected $casts = [
        'rate' => 'decimal:2',
    ];
}
