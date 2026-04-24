<?php

namespace App\Models\Mci\Lainnya;

use App\Models\Mci\MciBaseModel;

/**
 * Model MCI: TOFMSBBETAPRS
 * --------------------------------------------------------------------------
 * Domain   : Lainnya
 * Tabel    : [dbo].[TOFMSBBETAPRS]
 * Kolom    : 8
 * Sumber   : Mapping_MCI_MAR26_01042026.sql / MCI_MAR26_01042026.xlsx
 *
 * CATATAN:
 *  - Model ini READ-ONLY (lihat trait ReadOnlyModel di MciBaseModel).
 *  - Koneksi default `dashboard_data` (SQL Server), otomatis ke DB bulan terbaru.
 *  - Kolom dengan spasi/karakter khusus diakses via:
 *    $model->getAttribute('NAMA KOLOM')  atau  $model->{'NAMA KOLOM'}.
 *
 * @property string|null $nokontrak  type: varchar(10)
 * @property string|null $kdbiaya  type: varchar(2)
 * @property string|null $tahap  type: numeric(5)
 * @property string|null $tgl  type: varchar(8)
 * @property string|null $ke  type: numeric(5)
 * @property string|null $amount  type: numeric(9)
 * @property string|null $stsamort  type: varchar(1)
 * @property string|null $tglproses  type: varchar(8)
 */
class Tofmsbbetaprs extends MciBaseModel
{
    /**
     * Nama tabel (case-sensitive di SQL Server).
     */
    protected $table = 'TOFMSBBETAPRS';

    /**
     * Daftar LENGKAP kolom sesuai database (8 kolom).
     * Model ini read-only, $fillable hanya untuk dokumentasi & IDE helper.
     *
     * @var array<int,string>
     */
    protected $fillable = [
        'nokontrak',
        'kdbiaya',
        'tahap',
        'tgl',
        'ke',
        'amount',
        'stsamort',
        'tglproses',
    ];

    /**
     * Casting tipe kolom (non-string saja).
     *
     * @var array<string,string>
     */
    protected $casts = [
        'tahap' => 'decimal:2',
        'ke' => 'decimal:2',
        'amount' => 'decimal:2',
    ];
}
