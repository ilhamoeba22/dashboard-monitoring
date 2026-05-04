<?php

declare(strict_types=1);

namespace App\Models\Mci\Pesanan;

use App\Models\Mci\MciBaseModel;

/**
 * Model MCI: TOFPAYROLL
 * --------------------------------------------------------------------------
 * Domain   : Pesanan / Payroll
 * Tabel    : [dbo].[TOFPAYROLL]
 * Kolom    : 10
 * Sumber   : Mapping_MCI_MAR26_01042026.sql / MCI_MAR26_01042026.xlsx
 *
 * CATATAN:
 *  - Model ini READ-ONLY (lihat trait ReadOnlyModel di MciBaseModel).
 *  - Koneksi default `dashboard_data` (SQL Server), otomatis ke DB bulan terbaru.
 *  - Kolom dengan spasi/karakter khusus diakses via:
 *    $model->getAttribute('NAMA KOLOM')  atau  $model->{'NAMA KOLOM'}.
 *
 * @property string|null $tglproc type: varchar(8)
 * @property string|null $kdcomp type: varchar(3)
 * @property string|null $ke type: numeric(9)
 * @property string|null $noacc type: varchar(11)
 * @property string|null $nama type: varchar(30)
 * @property string|null $gaji type: numeric(9)
 * @property string|null $potongan type: numeric(9)
 * @property string|null $ket type: varchar(50)
 * @property string|null $ketpot type: varchar(40)
 * @property string|null $stsproc type: varchar(1)
 */
class Tofpayroll extends MciBaseModel
{
    /**
     * Nama tabel (case-sensitive di SQL Server).
     */
    protected $table = 'TOFPAYROLL';

    /**
     * Daftar LENGKAP kolom sesuai database (10 kolom).
     * Model ini read-only, $fillable hanya untuk dokumentasi & IDE helper.
     *
     * @var array<int,string>
     */
    protected $fillable = [
        'tglproc',
        'kdcomp',
        'ke',
        'noacc',
        'nama',
        'gaji',
        'potongan',
        'ket',
        'ketpot',
        'stsproc',
    ];

    /**
     * Casting tipe kolom (non-string saja).
     *
     * @var array<string,string>
     */
    protected $casts = [
        'ke' => 'decimal:2',
        'gaji' => 'decimal:2',
        'potongan' => 'decimal:2',
    ];
}
