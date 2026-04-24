<?php

namespace App\Models\Mci\Lainnya;

use App\Models\Mci\MciBaseModel;

/**
 * Model MCI: TOFTMPRAK
 * --------------------------------------------------------------------------
 * Domain   : Lainnya
 * Tabel    : [dbo].[TOFTMPRAK]
 * Kolom    : 12
 * Sumber   : Mapping_MCI_MAR26_01042026.sql / MCI_MAR26_01042026.xlsx
 *
 * CATATAN:
 *  - Model ini READ-ONLY (lihat trait ReadOnlyModel di MciBaseModel).
 *  - Koneksi default `dashboard_data` (SQL Server), otomatis ke DB bulan terbaru.
 *  - Kolom dengan spasi/karakter khusus diakses via:
 *    $model->getAttribute('NAMA KOLOM')  atau  $model->{'NAMA KOLOM'}.
 *
 * @property string|null $kantorasal  type: varchar(7)
 * @property string|null $kantortujuan  type: varchar(7)
 * @property string|null $nosbb  type: varchar(7)
 * @property string|null $borrow_pusat  type: numeric(9)
 * @property string|null $placement_pusat  type: numeric(9)
 * @property string|null $borrow_cab  type: numeric(9)
 * @property string|null $placement_cab  type: numeric(9)
 * @property string|null $rate  type: numeric(5)
 * @property string|null $biaya_pusat  type: numeric(9)
 * @property string|null $pend_pusat  type: numeric(9)
 * @property string|null $biaya_cab  type: numeric(9)
 * @property string|null $pend_cab  type: numeric(9)
 */
class Toftmprak extends MciBaseModel
{
    /**
     * Nama tabel (case-sensitive di SQL Server).
     */
    protected $table = 'TOFTMPRAK';

    /**
     * Daftar LENGKAP kolom sesuai database (12 kolom).
     * Model ini read-only, $fillable hanya untuk dokumentasi & IDE helper.
     *
     * @var array<int,string>
     */
    protected $fillable = [
        'kantorasal',
        'kantortujuan',
        'nosbb',
        'borrow_pusat',
        'placement_pusat',
        'borrow_cab',
        'placement_cab',
        'rate',
        'biaya_pusat',
        'pend_pusat',
        'biaya_cab',
        'pend_cab',
    ];

    /**
     * Casting tipe kolom (non-string saja).
     *
     * @var array<string,string>
     */
    protected $casts = [
        'borrow_pusat' => 'decimal:2',
        'placement_pusat' => 'decimal:2',
        'borrow_cab' => 'decimal:2',
        'placement_cab' => 'decimal:2',
        'rate' => 'decimal:2',
        'biaya_pusat' => 'decimal:2',
        'pend_pusat' => 'decimal:2',
        'biaya_cab' => 'decimal:2',
        'pend_cab' => 'decimal:2',
    ];
}
