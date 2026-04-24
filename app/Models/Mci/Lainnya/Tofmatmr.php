<?php

namespace App\Models\Mci\Lainnya;

use App\Models\Mci\MciBaseModel;

/**
 * Model MCI: TOFMATMR
 * --------------------------------------------------------------------------
 * Domain   : Lainnya
 * Tabel    : [dbo].[TOFMATMR]
 * Kolom    : 7
 * Sumber   : Mapping_MCI_MAR26_01042026.sql / MCI_MAR26_01042026.xlsx
 *
 * CATATAN:
 *  - Model ini READ-ONLY (lihat trait ReadOnlyModel di MciBaseModel).
 *  - Koneksi default `dashboard_data` (SQL Server), otomatis ke DB bulan terbaru.
 *  - Kolom dengan spasi/karakter khusus diakses via:
 *    $model->getAttribute('NAMA KOLOM')  atau  $model->{'NAMA KOLOM'}.
 *
 * @property string $periode  type: char(6)
 * @property string $kdatmr  type: char(4)
 * @property string|null $ket  type: varchar(250)
 * @property string|null $saldo_1  type: numeric(9)
 * @property string|null $ppap  type: numeric(9)
 * @property string|null $bobot  type: numeric(9)
 * @property string|null $saldo_2  type: numeric(9)
 */
class Tofmatmr extends MciBaseModel
{
    /**
     * Nama tabel (case-sensitive di SQL Server).
     */
    protected $table = 'TOFMATMR';

    /**
     * Daftar LENGKAP kolom sesuai database (7 kolom).
     * Model ini read-only, $fillable hanya untuk dokumentasi & IDE helper.
     *
     * @var array<int,string>
     */
    protected $fillable = [
        'periode',
        'kdatmr',
        'ket',
        'saldo_1',
        'ppap',
        'bobot',
        'saldo_2',
    ];

    /**
     * Casting tipe kolom (non-string saja).
     *
     * @var array<string,string>
     */
    protected $casts = [
        'saldo_1' => 'decimal:2',
        'ppap' => 'decimal:2',
        'bobot' => 'decimal:2',
        'saldo_2' => 'decimal:2',
    ];
}
