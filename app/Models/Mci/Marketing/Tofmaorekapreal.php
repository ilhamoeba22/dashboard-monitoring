<?php

namespace App\Models\Mci\Marketing;

use App\Models\Mci\MciBaseModel;

/**
 * Model MCI: TOFMAOREKAPREAL
 * --------------------------------------------------------------------------
 * Domain   : AO / Marketing
 * Tabel    : [dbo].[TOFMAOREKAPREAL]
 * Kolom    : 11
 * Sumber   : Mapping_MCI_MAR26_01042026.sql / MCI_MAR26_01042026.xlsx
 *
 * CATATAN:
 *  - Model ini READ-ONLY (lihat trait ReadOnlyModel di MciBaseModel).
 *  - Koneksi default `dashboard_data` (SQL Server), otomatis ke DB bulan terbaru.
 *  - Kolom dengan spasi/karakter khusus diakses via:
 *    $model->getAttribute('NAMA KOLOM')  atau  $model->{'NAMA KOLOM'}.
 *
 * @property string|null $kdao  type: varchar(10)
 * @property string|null $periode  type: varchar(6)
 * @property string|null $pokrealisasi  type: numeric(5)
 * @property string|null $qtytopup  type: numeric(5)
 * @property string|null $topup  type: numeric(9)
 * @property string|null $qtybaru  type: numeric(5)
 * @property string|null $baru  type: numeric(9)
 * @property string|null $bonustopup  type: numeric(9)
 * @property string|null $bonusbaru  type: numeric(9)
 * @property string|null $tglproses  type: varchar(8)
 * @property string|null $inproses  type: varchar(10)
 */
class Tofmaorekapreal extends MciBaseModel
{
    /**
     * Nama tabel (case-sensitive di SQL Server).
     */
    protected $table = 'TOFMAOREKAPREAL';

    /**
     * Daftar LENGKAP kolom sesuai database (11 kolom).
     * Model ini read-only, $fillable hanya untuk dokumentasi & IDE helper.
     *
     * @var array<int,string>
     */
    protected $fillable = [
        'kdao',
        'periode',
        'pokrealisasi',
        'qtytopup',
        'topup',
        'qtybaru',
        'baru',
        'bonustopup',
        'bonusbaru',
        'tglproses',
        'inproses',
    ];

    /**
     * Casting tipe kolom (non-string saja).
     *
     * @var array<string,string>
     */
    protected $casts = [
        'pokrealisasi' => 'decimal:2',
        'qtytopup' => 'decimal:2',
        'topup' => 'decimal:2',
        'qtybaru' => 'decimal:2',
        'baru' => 'decimal:2',
        'bonustopup' => 'decimal:2',
        'bonusbaru' => 'decimal:2',
    ];
}
