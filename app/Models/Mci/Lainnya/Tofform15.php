<?php

namespace App\Models\Mci\Lainnya;

use App\Models\Mci\MciBaseModel;

/**
 * Model MCI: TOFFORM15
 * --------------------------------------------------------------------------
 * Domain   : Lainnya
 * Tabel    : [dbo].[TOFFORM15]
 * Kolom    : 17
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
 * @property string|null $sandi_bank2  type: varchar(3)
 * @property string|null $sandi_bank3  type: varchar(10)
 * @property string|null $nmbank  type: varchar(100)
 * @property string|null $hubungan  type: varchar(1)
 * @property string|null $jns_kewajiban  type: varchar(2)
 * @property string|null $tglmulai  type: varchar(8)
 * @property string|null $tglakhir  type: varchar(8)
 * @property string|null $tingkat_imbalan  type: varchar(5)
 * @property string|null $jumlah  type: numeric(9)
 * @property string|null $mtd_bg_hsl_sd  type: varchar(1)
 * @property string|null $kdloc  type: varchar(2)
 */
class Tofform15 extends MciBaseModel
{
    /**
     * Nama tabel (case-sensitive di SQL Server).
     */
    protected $table = 'TOFFORM15';

    /**
     * Daftar LENGKAP kolom sesuai database (17 kolom).
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
        'sandi_bank2',
        'sandi_bank3',
        'nmbank',
        'hubungan',
        'jns_kewajiban',
        'tglmulai',
        'tglakhir',
        'tingkat_imbalan',
        'jumlah',
        'mtd_bg_hsl_sd',
        'kdloc',
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
