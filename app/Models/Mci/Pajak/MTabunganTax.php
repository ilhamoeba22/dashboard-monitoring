<?php

namespace App\Models\Mci\Pajak;

use App\Models\Mci\MciBaseModel;

/**
 * Model MCI: m_tabungan_tax
 * --------------------------------------------------------------------------
 * Domain   : Pajak
 * Tabel    : [dbo].[m_tabungan_tax]
 * Kolom    : 12
 * Sumber   : Mapping_MCI_MAR26_01042026.sql / MCI_MAR26_01042026.xlsx
 *
 * CATATAN:
 *  - Model ini READ-ONLY (lihat trait ReadOnlyModel di MciBaseModel).
 *  - Koneksi default `dashboard_data` (SQL Server), otomatis ke DB bulan terbaru.
 *  - Kolom dengan spasi/karakter khusus diakses via:
 *    $model->getAttribute('NAMA KOLOM')  atau  $model->{'NAMA KOLOM'}.
 *
 * @property string $kdprd  type: varchar(2)
 * @property string $noacc  type: varchar(11)
 * @property string $tglbayar  type: varchar(8)
 * @property string|null $fnama  type: varchar(30)
 * @property string|null $nominal  type: numeric(9)
 * @property string|null $rate  type: numeric(5)
 * @property string|null $spread  type: numeric(5)
 * @property string|null $subsiditax  type: numeric(5)
 * @property string|null $bunga  type: numeric(9)
 * @property string|null $pajak  type: numeric(9)
 * @property string|null $subsidi  type: numeric(9)
 * @property string|null $ststax  type: varchar(1)
 */
class MTabunganTax extends MciBaseModel
{
    /**
     * Nama tabel (case-sensitive di SQL Server).
     */
    protected $table = 'm_tabungan_tax';

    /**
     * Daftar LENGKAP kolom sesuai database (12 kolom).
     * Model ini read-only, $fillable hanya untuk dokumentasi & IDE helper.
     *
     * @var array<int,string>
     */
    protected $fillable = [
        'kdprd',
        'noacc',
        'tglbayar',
        'fnama',
        'nominal',
        'rate',
        'spread',
        'subsiditax',
        'bunga',
        'pajak',
        'subsidi',
        'ststax',
    ];

    /**
     * Casting tipe kolom (non-string saja).
     *
     * @var array<string,string>
     */
    protected $casts = [
        'nominal' => 'decimal:2',
        'rate' => 'decimal:2',
        'spread' => 'decimal:2',
        'subsiditax' => 'decimal:2',
        'bunga' => 'decimal:2',
        'pajak' => 'decimal:2',
        'subsidi' => 'decimal:2',
    ];
}
