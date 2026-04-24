<?php

namespace App\Models\Mci\Saving;

use App\Models\Mci\MciBaseModel;

/**
 * Model MCI: TMPTABBH
 * --------------------------------------------------------------------------
 * Domain   : Saving
 * Tabel    : [dbo].[TMPTABBH]
 * Kolom    : 10
 * Sumber   : Mapping_MCI_MAR26_01042026.sql / MCI_MAR26_01042026.xlsx
 *
 * CATATAN:
 *  - Model ini READ-ONLY (lihat trait ReadOnlyModel di MciBaseModel).
 *  - Koneksi default `dashboard_data` (SQL Server), otomatis ke DB bulan terbaru.
 *  - Kolom dengan spasi/karakter khusus diakses via:
 *    $model->getAttribute('NAMA KOLOM')  atau  $model->{'NAMA KOLOM'}.
 *
 * @property string|null $notab  type: varchar(11)
 * @property string|null $saldorata  type: numeric(9)
 * @property string|null $saldoakhir  type: numeric(9)
 * @property string|null $saldokons  type: numeric(9)
 * @property string|null $rate  type: numeric(5)
 * @property string|null $bonus  type: numeric(9)
 * @property string|null $tax  type: numeric(9)
 * @property string|null $zakat  type: numeric(9)
 * @property string|null $infaq  type: numeric(9)
 * @property string|null $tglhtg  type: varchar(8)
 */
class Tmptabbh extends MciBaseModel
{
    /**
     * Nama tabel (case-sensitive di SQL Server).
     */
    protected $table = 'TMPTABBH';

    /**
     * Daftar LENGKAP kolom sesuai database (10 kolom).
     * Model ini read-only, $fillable hanya untuk dokumentasi & IDE helper.
     *
     * @var array<int,string>
     */
    protected $fillable = [
        'notab',
        'saldorata',
        'saldoakhir',
        'saldokons',
        'rate',
        'bonus',
        'tax',
        'zakat',
        'infaq',
        'tglhtg',
    ];

    /**
     * Casting tipe kolom (non-string saja).
     *
     * @var array<string,string>
     */
    protected $casts = [
        'saldorata' => 'decimal:2',
        'saldoakhir' => 'decimal:2',
        'saldokons' => 'decimal:2',
        'rate' => 'decimal:2',
        'bonus' => 'decimal:2',
        'tax' => 'decimal:2',
        'zakat' => 'decimal:2',
        'infaq' => 'decimal:2',
    ];
}
