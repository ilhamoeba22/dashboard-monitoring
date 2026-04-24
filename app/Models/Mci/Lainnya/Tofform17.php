<?php

namespace App\Models\Mci\Lainnya;

use App\Models\Mci\MciBaseModel;

/**
 * Model MCI: TOFFORM17
 * --------------------------------------------------------------------------
 * Domain   : Lainnya
 * Tabel    : [dbo].[TOFFORM17]
 * Kolom    : 9
 * Sumber   : Mapping_MCI_MAR26_01042026.sql / MCI_MAR26_01042026.xlsx
 *
 * CATATAN:
 *  - Model ini READ-ONLY (lihat trait ReadOnlyModel di MciBaseModel).
 *  - Koneksi default `dashboard_data` (SQL Server), otomatis ke DB bulan terbaru.
 *  - Kolom dengan spasi/karakter khusus diakses via:
 *    $model->getAttribute('NAMA KOLOM')  atau  $model->{'NAMA KOLOM'}.
 *
 * @property string|null $kode_laporan  type: varchar(10)
 * @property string|null $sandi_form  type: varchar(2)
 * @property string|null $sandi_bank  type: varchar(6)
 * @property string|null $sandi_kantor  type: varchar(3)
 * @property string|null $bulan  type: varchar(2)
 * @property string|null $tahun  type: varchar(4)
 * @property string|null $kdrupa  type: varchar(2)
 * @property string|null $ket  type: varchar(100)
 * @property string|null $jumlah  type: numeric(9)
 */
class Tofform17 extends MciBaseModel
{
    /**
     * Nama tabel (case-sensitive di SQL Server).
     */
    protected $table = 'TOFFORM17';

    /**
     * Daftar LENGKAP kolom sesuai database (9 kolom).
     * Model ini read-only, $fillable hanya untuk dokumentasi & IDE helper.
     *
     * @var array<int,string>
     */
    protected $fillable = [
        'kode_laporan',
        'sandi_form',
        'sandi_bank',
        'sandi_kantor',
        'bulan',
        'tahun',
        'kdrupa',
        'ket',
        'jumlah',
    ];

    /**
     * Casting tipe kolom (non-string saja).
     *
     * @var array<string,string>
     */
    protected $casts = [
        'jumlah' => 'decimal:2',
    ];
}
