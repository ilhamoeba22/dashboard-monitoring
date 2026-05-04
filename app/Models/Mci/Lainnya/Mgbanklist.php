<?php

declare(strict_types=1);

namespace App\Models\Mci\Lainnya;

use App\Models\Mci\MciBaseModel;

/**
 * Model MCI: MGBANKLIST
 * --------------------------------------------------------------------------
 * Domain   : Lainnya
 * Tabel    : [dbo].[MGBANKLIST]
 * Kolom    : 9
 * Sumber   : Mapping_MCI_MAR26_01042026.sql / MCI_MAR26_01042026.xlsx
 *
 * CATATAN:
 *  - Model ini READ-ONLY (lihat trait ReadOnlyModel di MciBaseModel).
 *  - Koneksi default `dashboard_data` (SQL Server), otomatis ke DB bulan terbaru.
 *  - Kolom dengan spasi/karakter khusus diakses via:
 *    $model->getAttribute('NAMA KOLOM')  atau  $model->{'NAMA KOLOM'}.
 *
 * @property string|null $billerid type: varchar(20)
 * @property string|null $nmbank type: varchar(255)
 * @property string|null $kdbank type: varchar(100)
 * @property string|null $stsbank type: varchar(1)
 * @property string|null $tglup type: varchar(8)
 * @property int|null $fee type: int(4)
 * @property string|null $admin type: numeric(9)
 * @property string|null $pendfee type: numeric(9)
 * @property string|null $feemg type: numeric(9)
 */
class Mgbanklist extends MciBaseModel
{
    /**
     * Nama tabel (case-sensitive di SQL Server).
     */
    protected $table = 'MGBANKLIST';

    /**
     * Daftar LENGKAP kolom sesuai database (9 kolom).
     * Model ini read-only, $fillable hanya untuk dokumentasi & IDE helper.
     *
     * @var array<int,string>
     */
    protected $fillable = [
        'billerid',
        'nmbank',
        'kdbank',
        'stsbank',
        'tglup',
        'fee',
        'admin',
        'pendfee',
        'feemg',
    ];

    /**
     * Casting tipe kolom (non-string saja).
     *
     * @var array<string,string>
     */
    protected $casts = [
        'fee' => 'integer',
        'admin' => 'decimal:2',
        'pendfee' => 'decimal:2',
        'feemg' => 'decimal:2',
    ];
}
