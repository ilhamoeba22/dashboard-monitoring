<?php

namespace App\Models\Mci\Marketing;

use App\Models\Mci\MciBaseModel;

/**
 * Model MCI: TOFMAOREKAP
 * --------------------------------------------------------------------------
 * Domain   : AO / Marketing
 * Tabel    : [dbo].[TOFMAOREKAP]
 * Kolom    : 16
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
 * @property string|null $npf_rata  type: numeric(5)
 * @property string|null $bonusnpf  type: numeric(9)
 * @property string|null $qty_1  type: numeric(5)
 * @property string|null $qty_2  type: numeric(5)
 * @property string|null $qty_3  type: numeric(5)
 * @property string|null $qty_4  type: numeric(5)
 * @property string|null $qty_5  type: numeric(9)
 * @property string|null $qty_1_baru  type: numeric(5)
 * @property string|null $qty_2_baru  type: numeric(5)
 * @property string|null $qty_3_baru  type: numeric(5)
 * @property string|null $qty_4_baru  type: numeric(5)
 * @property string|null $qty_5_baru  type: numeric(5)
 * @property string|null $bonustopup  type: numeric(9)
 * @property string|null $bonusbaru  type: numeric(9)
 */
class Tofmaorekap extends MciBaseModel
{
    /**
     * Nama tabel (case-sensitive di SQL Server).
     */
    protected $table = 'TOFMAOREKAP';

    /**
     * Daftar LENGKAP kolom sesuai database (16 kolom).
     * Model ini read-only, $fillable hanya untuk dokumentasi & IDE helper.
     *
     * @var array<int,string>
     */
    protected $fillable = [
        'kdao',
        'periode',
        'npf_rata',
        'bonusnpf',
        'qty_1',
        'qty_2',
        'qty_3',
        'qty_4',
        'qty_5',
        'qty_1_baru',
        'qty_2_baru',
        'qty_3_baru',
        'qty_4_baru',
        'qty_5_baru',
        'bonustopup',
        'bonusbaru',
    ];

    /**
     * Casting tipe kolom (non-string saja).
     *
     * @var array<string,string>
     */
    protected $casts = [
        'npf_rata' => 'decimal:2',
        'bonusnpf' => 'decimal:2',
        'qty_1' => 'decimal:2',
        'qty_2' => 'decimal:2',
        'qty_3' => 'decimal:2',
        'qty_4' => 'decimal:2',
        'qty_5' => 'decimal:2',
        'qty_1_baru' => 'decimal:2',
        'qty_2_baru' => 'decimal:2',
        'qty_3_baru' => 'decimal:2',
        'qty_4_baru' => 'decimal:2',
        'qty_5_baru' => 'decimal:2',
        'bonustopup' => 'decimal:2',
        'bonusbaru' => 'decimal:2',
    ];
}
