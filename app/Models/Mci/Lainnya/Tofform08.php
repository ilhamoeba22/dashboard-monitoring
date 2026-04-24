<?php

namespace App\Models\Mci\Lainnya;

use App\Models\Mci\MciBaseModel;

/**
 * Model MCI: TOFFORM08
 * --------------------------------------------------------------------------
 * Domain   : Lainnya
 * Tabel    : [dbo].[TOFFORM08]
 * Kolom    : 25
 * Sumber   : Mapping_MCI_MAR26_01042026.sql / MCI_MAR26_01042026.xlsx
 *
 * CATATAN:
 *  - Model ini READ-ONLY (lihat trait ReadOnlyModel di MciBaseModel).
 *  - Koneksi default `dashboard_data` (SQL Server), otomatis ke DB bulan terbaru.
 *  - Kolom dengan spasi/karakter khusus diakses via:
 *    $model->getAttribute('NAMA KOLOM')  atau  $model->{'NAMA KOLOM'}.
 *
 * @property string|null $nomor_rekening  type: varchar(10)
 * @property string|null $tglperolehan  type: varchar(8)
 * @property string|null $jns_akad  type: varchar(1)
 * @property string|null $jns_pengunaan  type: varchar(2)
 * @property string|null $nilai_perolehan  type: numeric(9)
 * @property string|null $sekon  type: varchar(4)
 * @property string|null $akum_penyusutan  type: numeric(9)
 * @property string|null $nilai_kontrak_sewa  type: numeric(9)
 * @property string|null $tglmulai  type: varchar(8)
 * @property string|null $tglakhir  type: varchar(8)
 * @property string|null $priode_pembayaran  type: varchar(1)
 * @property string|null $angsuran_sewa  type: numeric(9)
 * @property string|null $akum_angsuran_sewa  type: numeric(9)
 * @property string|null $hubungan  type: varchar(1)
 * @property string|null $kolektibilitas  type: varchar(1)
 * @property string|null $jns_agunan  type: varchar(1)
 * @property string|null $nilai_agunan  type: numeric(9)
 * @property string|null $mtd_bg_hsl_sd  type: varchar(1)
 * @property string|null $gol_penjamin  type: varchar(3)
 * @property string|null $bag_dijamin  type: varchar(5)
 * @property string|null $gol_nasabah  type: varchar(3)
 * @property string|null $lok_usaha_nasabah  type: varchar(4)
 * @property string|null $gol_pembiayaan  type: varchar(1)
 * @property string|null $tujuan  type: varchar(2)
 * @property string|null $kdloc  type: varchar(2)
 */
class Tofform08 extends MciBaseModel
{
    /**
     * Nama tabel (case-sensitive di SQL Server).
     */
    protected $table = 'TOFFORM08';

    /**
     * Daftar LENGKAP kolom sesuai database (25 kolom).
     * Model ini read-only, $fillable hanya untuk dokumentasi & IDE helper.
     *
     * @var array<int,string>
     */
    protected $fillable = [
        'nomor_rekening',
        'tglperolehan',
        'jns_akad',
        'jns_pengunaan',
        'nilai_perolehan',
        'sekon',
        'akum_penyusutan',
        'nilai_kontrak_sewa',
        'tglmulai',
        'tglakhir',
        'priode_pembayaran',
        'angsuran_sewa',
        'akum_angsuran_sewa',
        'hubungan',
        'kolektibilitas',
        'jns_agunan',
        'nilai_agunan',
        'mtd_bg_hsl_sd',
        'gol_penjamin',
        'bag_dijamin',
        'gol_nasabah',
        'lok_usaha_nasabah',
        'gol_pembiayaan',
        'tujuan',
        'kdloc',
    ];

    /**
     * Casting tipe kolom (non-string saja).
     *
     * @var array<string,string>
     */
    protected $casts = [
        'nilai_perolehan' => 'decimal:2',
        'akum_penyusutan' => 'decimal:2',
        'nilai_kontrak_sewa' => 'decimal:2',
        'angsuran_sewa' => 'decimal:2',
        'akum_angsuran_sewa' => 'decimal:2',
        'nilai_agunan' => 'decimal:2',
    ];
}
