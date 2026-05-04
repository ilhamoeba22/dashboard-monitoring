<?php

declare(strict_types=1);

namespace App\Models\Mci\Lainnya;

use App\Models\Mci\MciBaseModel;

/**
 * Model MCI: TOFFORM07
 * --------------------------------------------------------------------------
 * Domain   : Lainnya
 * Tabel    : [dbo].[TOFFORM07]
 * Kolom    : 31
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
 * @property string|null $jenis_pembiayaan type: varchar(2)
 * @property string|null $jns_penggunaan type: varchar(2)
 * @property string|null $hubungan type: varchar(1)
 * @property string|null $tglawal type: varchar(8)
 * @property string|null $tglakhir type: varchar(8)
 * @property string|null $kolektibilitas type: varchar(1)
 * @property string|null $tingkat_imbalan type: varchar(5)
 * @property string|null $sektor_ekonomi type: varchar(4)
 * @property string|null $plafond type: numeric(9)
 * @property string|null $kelonggaran_tarik type: numeric(9)
 * @property string|null $saldo_pembiayaan type: numeric(9)
 * @property string|null $jns_agunan type: varchar(1)
 * @property string|null $nilai_agunan type: numeric(9)
 * @property string|null $ppap type: numeric(9)
 * @property string|null $sifat type: varchar(1)
 * @property string|null $mtd_bg_hsl_pemb type: varchar(1)
 * @property string|null $mtd_bg_hsl_sd type: varchar(1)
 * @property string|null $gol_penjamin type: varchar(3)
 * @property string|null $bag_dijamin type: varchar(4)
 * @property string|null $gol_nasabah type: varchar(3)
 * @property string|null $lok_usaha_nasabah type: varchar(4)
 * @property string|null $gol_pembiayaan type: varchar(1)
 * @property string|null $kdloc type: varchar(2)
 */
class Tofform07 extends MciBaseModel
{
    /**
     * Nama tabel (case-sensitive di SQL Server).
     */
    protected $table = 'TOFFORM07';

    /**
     * Daftar LENGKAP kolom sesuai database (31 kolom).
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
        'jenis_pembiayaan',
        'jns_penggunaan',
        'hubungan',
        'tglawal',
        'tglakhir',
        'kolektibilitas',
        'tingkat_imbalan',
        'sektor_ekonomi',
        'plafond',
        'kelonggaran_tarik',
        'saldo_pembiayaan',
        'jns_agunan',
        'nilai_agunan',
        'ppap',
        'sifat',
        'mtd_bg_hsl_pemb',
        'mtd_bg_hsl_sd',
        'gol_penjamin',
        'bag_dijamin',
        'gol_nasabah',
        'lok_usaha_nasabah',
        'gol_pembiayaan',
        'kdloc',
    ];

    /**
     * Casting tipe kolom (non-string saja).
     *
     * @var array<string,string>
     */
    protected $casts = [
        'jumlah_rekening' => 'decimal:2',
        'plafond' => 'decimal:2',
        'kelonggaran_tarik' => 'decimal:2',
        'saldo_pembiayaan' => 'decimal:2',
        'nilai_agunan' => 'decimal:2',
        'ppap' => 'decimal:2',
    ];
}
