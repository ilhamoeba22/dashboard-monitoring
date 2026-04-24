<?php

namespace App\Models\Mci\Lainnya;

use App\Models\Mci\MciBaseModel;

/**
 * Model MCI: m_tabungan_undian
 * --------------------------------------------------------------------------
 * Domain   : Lainnya
 * Tabel    : [dbo].[m_tabungan_undian]
 * Kolom    : 10
 * Sumber   : Mapping_MCI_MAR26_01042026.sql / MCI_MAR26_01042026.xlsx
 *
 * CATATAN:
 *  - Model ini READ-ONLY (lihat trait ReadOnlyModel di MciBaseModel).
 *  - Koneksi default `dashboard_data` (SQL Server), otomatis ke DB bulan terbaru.
 *  - Kolom dengan spasi/karakter khusus diakses via:
 *    $model->getAttribute('NAMA KOLOM')  atau  $model->{'NAMA KOLOM'}.
 *
 * @property string $kodeprd  type: varchar(2)
 * @property string $tahap  type: varchar(2)
 * @property string $noacc  type: varchar(11)
 * @property string|null $totalsaldo  type: numeric(9)
 * @property string|null $saldorata  type: numeric(9)
 * @property string|null $kelipatansaldo  type: numeric(9)
 * @property string|null $point  type: numeric(5)
 * @property string|null $noundianawal  type: numeric(9)
 * @property string|null $noundianakhir  type: numeric(9)
 * @property string|null $tglproses  type: varchar(8)
 */
class MTabunganUndian extends MciBaseModel
{
    /**
     * Nama tabel (case-sensitive di SQL Server).
     */
    protected $table = 'm_tabungan_undian';

    /**
     * Daftar LENGKAP kolom sesuai database (10 kolom).
     * Model ini read-only, $fillable hanya untuk dokumentasi & IDE helper.
     *
     * @var array<int,string>
     */
    protected $fillable = [
        'kodeprd',
        'tahap',
        'noacc',
        'totalsaldo',
        'saldorata',
        'kelipatansaldo',
        'point',
        'noundianawal',
        'noundianakhir',
        'tglproses',
    ];

    /**
     * Casting tipe kolom (non-string saja).
     *
     * @var array<string,string>
     */
    protected $casts = [
        'totalsaldo' => 'decimal:2',
        'saldorata' => 'decimal:2',
        'kelipatansaldo' => 'decimal:2',
        'point' => 'decimal:2',
        'noundianawal' => 'decimal:2',
        'noundianakhir' => 'decimal:2',
    ];
}
