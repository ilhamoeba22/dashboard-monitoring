<?php

namespace App\Models\Mci\Lainnya;

use App\Models\Mci\MciBaseModel;

/**
 * Model MCI: TOFFORM25
 * --------------------------------------------------------------------------
 * Domain   : Lainnya
 * Tabel    : [dbo].[TOFFORM25]
 * Kolom    : 11
 * Sumber   : Mapping_MCI_MAR26_01042026.sql / MCI_MAR26_01042026.xlsx
 *
 * CATATAN:
 *  - Model ini READ-ONLY (lihat trait ReadOnlyModel di MciBaseModel).
 *  - Koneksi default `dashboard_data` (SQL Server), otomatis ke DB bulan terbaru.
 *  - Kolom dengan spasi/karakter khusus diakses via:
 *    $model->getAttribute('NAMA KOLOM')  atau  $model->{'NAMA KOLOM'}.
 *
 * @property string|null $kode_laporan  type: varchar(4)
 * @property string|null $sandi_form  type: varchar(2)
 * @property string|null $sandi_bank  type: varchar(6)
 * @property string|null $sandi_kantor  type: varchar(3)
 * @property string|null $bulan  type: varchar(2)
 * @property string|null $tahun  type: varchar(4)
 * @property string|null $sandi_rekening  type: varchar(3)
 * @property string|null $minggu_1  type: numeric(9)
 * @property string|null $minggu_2  type: numeric(9)
 * @property string|null $minggu_3  type: numeric(9)
 * @property string|null $minggu_4  type: numeric(9)
 */
class Tofform25 extends MciBaseModel
{
    /**
     * Nama tabel (case-sensitive di SQL Server).
     */
    protected $table = 'TOFFORM25';

    /**
     * Daftar LENGKAP kolom sesuai database (11 kolom).
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
        'sandi_rekening',
        'minggu_1',
        'minggu_2',
        'minggu_3',
        'minggu_4',
    ];

    /**
     * Casting tipe kolom (non-string saja).
     *
     * @var array<string,string>
     */
    protected $casts = [
        'minggu_1' => 'decimal:2',
        'minggu_2' => 'decimal:2',
        'minggu_3' => 'decimal:2',
        'minggu_4' => 'decimal:2',
    ];
}
