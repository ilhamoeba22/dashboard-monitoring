<?php

declare(strict_types=1);

namespace App\Models\Mci\Lainnya;

use App\Models\Mci\MciBaseModel;

/**
 * Model MCI: TOFFORM13
 * --------------------------------------------------------------------------
 * Domain   : Lainnya
 * Tabel    : [dbo].[TOFFORM13]
 * Kolom    : 18
 * Sumber   : Mapping_MCI_MAR26_01042026.sql / MCI_MAR26_01042026.xlsx
 *
 * CATATAN:
 *  - Model ini READ-ONLY (lihat trait ReadOnlyModel di MciBaseModel).
 *  - Koneksi default `dashboard_data` (SQL Server), otomatis ke DB bulan terbaru.
 *  - Kolom dengan spasi/karakter khusus diakses via:
 *    $model->getAttribute('NAMA KOLOM')  atau  $model->{'NAMA KOLOM'}.
 *
 * @property string|null $kode_laporan type: varchar(4)
 * @property string|null $sandi_form type: varchar(2)
 * @property string|null $sandi_bank type: varchar(6)
 * @property string|null $sandi_kantor type: varchar(3)
 * @property string|null $bulan type: varchar(2)
 * @property string|null $tahun type: varchar(4)
 * @property string|null $nomor_rekening type: varchar(15)
 * @property string|null $jumlah_rekening type: numeric(5)
 * @property string|null $hubungan type: varchar(1)
 * @property string|null $lokasi_nasabah type: varchar(4)
 * @property string|null $tingkat_imbalan type: varchar(5)
 * @property string|null $jumlah type: numeric(9)
 * @property string|null $mtd_bg_hsl_sd type: varchar(1)
 * @property string|null $gol_nasabah type: varchar(3)
 * @property string|null $tglmulai type: varchar(8)
 * @property string|null $tglakhir type: varchar(8)
 * @property string|null $kdloc type: varchar(2)
 * @property string|null $urut type: numeric(5)
 */
class Tofform13 extends MciBaseModel
{
    /**
     * Nama tabel (case-sensitive di SQL Server).
     */
    protected $table = 'TOFFORM13';

    /**
     * Daftar LENGKAP kolom sesuai database (18 kolom).
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
        'nomor_rekening',
        'jumlah_rekening',
        'hubungan',
        'lokasi_nasabah',
        'tingkat_imbalan',
        'jumlah',
        'mtd_bg_hsl_sd',
        'gol_nasabah',
        'tglmulai',
        'tglakhir',
        'kdloc',
        'urut',
    ];

    /**
     * Casting tipe kolom (non-string saja).
     *
     * @var array<string,string>
     */
    protected $casts = [
        'jumlah_rekening' => 'decimal:2',
        'jumlah' => 'decimal:2',
        'urut' => 'decimal:2',
    ];
}
