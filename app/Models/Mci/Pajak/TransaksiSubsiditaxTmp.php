<?php

namespace App\Models\Mci\Pajak;

use App\Models\Mci\MciBaseModel;

/**
 * Model MCI: transaksi_subsiditax_tmp
 * --------------------------------------------------------------------------
 * Domain   : Pajak
 * Tabel    : [dbo].[transaksi_subsiditax_tmp]
 * Kolom    : 5
 * Sumber   : Mapping_MCI_MAR26_01042026.sql / MCI_MAR26_01042026.xlsx
 *
 * CATATAN:
 *  - Model ini READ-ONLY (lihat trait ReadOnlyModel di MciBaseModel).
 *  - Koneksi default `dashboard_data` (SQL Server), otomatis ke DB bulan terbaru.
 *  - Kolom dengan spasi/karakter khusus diakses via:
 *    $model->getAttribute('NAMA KOLOM')  atau  $model->{'NAMA KOLOM'}.
 *
 * @property string $jnstrn  type: varchar(10)
 * @property string|null $noacc  type: varchar(11)
 * @property string|null $noacc_dr  type: varchar(11)
 * @property string|null $noacc_cr  type: varchar(11)
 * @property string|null $nominal  type: numeric(9)
 */
class TransaksiSubsiditaxTmp extends MciBaseModel
{
    /**
     * Nama tabel (case-sensitive di SQL Server).
     */
    protected $table = 'transaksi_subsiditax_tmp';

    /**
     * Daftar LENGKAP kolom sesuai database (5 kolom).
     * Model ini read-only, $fillable hanya untuk dokumentasi & IDE helper.
     *
     * @var array<int,string>
     */
    protected $fillable = [
        'jnstrn',
        'noacc',
        'noacc_dr',
        'noacc_cr',
        'nominal',
    ];

    /**
     * Casting tipe kolom (non-string saja).
     *
     * @var array<string,string>
     */
    protected $casts = [
        'nominal' => 'decimal:2',
    ];
}
