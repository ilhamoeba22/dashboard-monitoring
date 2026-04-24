<?php

namespace App\Models\Mci\Lainnya;

use App\Models\Mci\MciBaseModel;

/**
 * Model MCI: TOFFORM03
 * --------------------------------------------------------------------------
 * Domain   : Lainnya
 * Tabel    : [dbo].[TOFFORM03]
 * Kolom    : 16
 * Sumber   : Mapping_MCI_MAR26_01042026.sql / MCI_MAR26_01042026.xlsx
 *
 * CATATAN:
 *  - Model ini READ-ONLY (lihat trait ReadOnlyModel di MciBaseModel).
 *  - Koneksi default `dashboard_data` (SQL Server), otomatis ke DB bulan terbaru.
 *  - Kolom dengan spasi/karakter khusus diakses via:
 *    $model->getAttribute('NAMA KOLOM')  atau  $model->{'NAMA KOLOM'}.
 *
 * @property string|null $kdbank  type: varchar(3)
 * @property string|null $sandibank  type: varchar(9)
 * @property string|null $nmbank  type: varchar(100)
 * @property string|null $hubungan  type: varchar(1)
 * @property string|null $jnspenempatan  type: varchar(2)
 * @property string|null $tglmulai  type: varchar(8)
 * @property string|null $tglakhir  type: varchar(8)
 * @property string|null $coll  type: varchar(1)
 * @property string|null $rate  type: varchar(4)
 * @property string|null $nominal  type: numeric(9)
 * @property string|null $jnsagunan  type: varchar(1)
 * @property string|null $nilaiagunan  type: numeric(9)
 * @property string|null $ppap  type: numeric(9)
 * @property string|null $mtdbghsl  type: varchar(1)
 * @property string|null $goljam  type: varchar(3)
 * @property string|null $bagjam  type: varchar(4)
 */
class Tofform03 extends MciBaseModel
{
    /**
     * Nama tabel (case-sensitive di SQL Server).
     */
    protected $table = 'TOFFORM03';

    /**
     * Daftar LENGKAP kolom sesuai database (16 kolom).
     * Model ini read-only, $fillable hanya untuk dokumentasi & IDE helper.
     *
     * @var array<int,string>
     */
    protected $fillable = [
        'kdbank',
        'sandibank',
        'nmbank',
        'hubungan',
        'jnspenempatan',
        'tglmulai',
        'tglakhir',
        'coll',
        'rate',
        'nominal',
        'jnsagunan',
        'nilaiagunan',
        'ppap',
        'mtdbghsl',
        'goljam',
        'bagjam',
    ];

    /**
     * Casting tipe kolom (non-string saja).
     *
     * @var array<string,string>
     */
    protected $casts = [
        'nominal' => 'decimal:2',
        'nilaiagunan' => 'decimal:2',
        'ppap' => 'decimal:2',
    ];
}
