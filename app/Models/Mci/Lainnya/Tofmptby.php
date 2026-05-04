<?php

declare(strict_types=1);

namespace App\Models\Mci\Lainnya;

use App\Models\Mci\MciBaseModel;

/**
 * Model MCI: TOFMPTBY
 * --------------------------------------------------------------------------
 * Domain   : Lainnya
 * Tabel    : [dbo].[TOFMPTBY]
 * Kolom    : 10
 * Sumber   : Mapping_MCI_MAR26_01042026.sql / MCI_MAR26_01042026.xlsx
 *
 * CATATAN:
 *  - Model ini READ-ONLY (lihat trait ReadOnlyModel di MciBaseModel).
 *  - Koneksi default `dashboard_data` (SQL Server), otomatis ke DB bulan terbaru.
 *  - Kolom dengan spasi/karakter khusus diakses via:
 *    $model->getAttribute('NAMA KOLOM')  atau  $model->{'NAMA KOLOM'}.
 *
 * @property string $kdpt type: varchar(10)
 * @property string $nim type: varchar(20)
 * @property string $kdbiaya type: varchar(20)
 * @property string $periode type: varchar(6)
 * @property string $amount type: numeric(9)
 * @property string|null $amount_bayar type: numeric(9)
 * @property string|null $disc type: numeric(9)
 * @property string|null $cicilke type: numeric(5)
 * @property string $stsbayar type: varchar(1)
 * @property string $tglbayar type: varchar(8)
 */
class Tofmptby extends MciBaseModel
{
    /**
     * Nama tabel (case-sensitive di SQL Server).
     */
    protected $table = 'TOFMPTBY';

    /**
     * Daftar LENGKAP kolom sesuai database (10 kolom).
     * Model ini read-only, $fillable hanya untuk dokumentasi & IDE helper.
     *
     * @var array<int,string>
     */
    protected $fillable = [
        'kdpt',
        'nim',
        'kdbiaya',
        'periode',
        'amount',
        'amount_bayar',
        'disc',
        'cicilke',
        'stsbayar',
        'tglbayar',
    ];

    /**
     * Casting tipe kolom (non-string saja).
     *
     * @var array<string,string>
     */
    protected $casts = [
        'amount' => 'decimal:2',
        'amount_bayar' => 'decimal:2',
        'disc' => 'decimal:2',
        'cicilke' => 'decimal:2',
    ];
}
