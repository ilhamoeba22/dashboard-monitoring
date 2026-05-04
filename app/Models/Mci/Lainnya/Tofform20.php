<?php

declare(strict_types=1);

namespace App\Models\Mci\Lainnya;

use App\Models\Mci\MciBaseModel;

/**
 * Model MCI: TOFFORM20
 * --------------------------------------------------------------------------
 * Domain   : Lainnya
 * Tabel    : [dbo].[TOFFORM20]
 * Kolom    : 28
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
 * @property string|null $jumlah_rekening type: numeric(9)
 * @property string|null $jns_penggunaan type: varchar(2)
 * @property string|null $hubungan type: varchar(1)
 * @property string|null $tglmulai type: varchar(8)
 * @property string|null $tglakhir type: varchar(8)
 * @property string|null $kolektibilitas type: varchar(1)
 * @property string|null $sktr_ekn type: varchar(4)
 * @property string|null $mtd_bg_hsl_sd type: varchar(1)
 * @property string|null $gol_penjamin type: varchar(3)
 * @property string|null $bag_dijamin type: varchar(5)
 * @property string|null $gol_nasabah type: varchar(3)
 * @property string|null $lok_keg_multijasa type: varchar(4)
 * @property string|null $gol_piutang type: varchar(1)
 * @property string|null $nilai_akad type: numeric(9)
 * @property string|null $saldo_harga_pokok type: numeric(9)
 * @property string|null $pend_trans_mj_tg type: numeric(9)
 * @property string|null $saldo_piutang type: numeric(9)
 * @property string|null $jns_agunan type: varchar(1)
 * @property string|null $nilai_agunan type: numeric(9)
 * @property string|null $ppap type: numeric(9)
 * @property string|null $kdloc type: varchar(2)
 */
class Tofform20 extends MciBaseModel
{
    /**
     * Nama tabel (case-sensitive di SQL Server).
     */
    protected $table = 'TOFFORM20';

    /**
     * Daftar LENGKAP kolom sesuai database (28 kolom).
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
        'jns_penggunaan',
        'hubungan',
        'tglmulai',
        'tglakhir',
        'kolektibilitas',
        'sktr_ekn',
        'mtd_bg_hsl_sd',
        'gol_penjamin',
        'bag_dijamin',
        'gol_nasabah',
        'lok_keg_multijasa',
        'gol_piutang',
        'nilai_akad',
        'saldo_harga_pokok',
        'pend_trans_mj_tg',
        'saldo_piutang',
        'jns_agunan',
        'nilai_agunan',
        'ppap',
        'kdloc',
    ];

    /**
     * Casting tipe kolom (non-string saja).
     *
     * @var array<string,string>
     */
    protected $casts = [
        'jumlah_rekening' => 'decimal:2',
        'nilai_akad' => 'decimal:2',
        'saldo_harga_pokok' => 'decimal:2',
        'pend_trans_mj_tg' => 'decimal:2',
        'saldo_piutang' => 'decimal:2',
        'nilai_agunan' => 'decimal:2',
        'ppap' => 'decimal:2',
    ];
}
