<?php

namespace App\Models\Mci\Lainnya;

use App\Models\Mci\MciBaseModel;

/**
 * Model MCI: TOFFORM22
 * --------------------------------------------------------------------------
 * Domain   : Lainnya
 * Tabel    : [dbo].[TOFFORM22]
 * Kolom    : 15
 * Sumber   : Mapping_MCI_MAR26_01042026.sql / MCI_MAR26_01042026.xlsx
 *
 * CATATAN:
 *  - Model ini READ-ONLY (lihat trait ReadOnlyModel di MciBaseModel).
 *  - Koneksi default `dashboard_data` (SQL Server), otomatis ke DB bulan terbaru.
 *  - Kolom dengan spasi/karakter khusus diakses via:
 *    $model->getAttribute('NAMA KOLOM')  atau  $model->{'NAMA KOLOM'}.
 *
 * @property string|null $kode_laporan  type: varchar(10)
 * @property string|null $sandi_form  type: varchar(5)
 * @property string|null $sandi_bank  type: varchar(6)
 * @property string|null $sandi_kantor  type: varchar(3)
 * @property string|null $bulan  type: varchar(2)
 * @property string|null $tahun  type: varchar(4)
 * @property string|null $kdagunan  type: varchar(2)
 * @property string|null $hubungan  type: varchar(1)
 * @property string|null $tglambilalih  type: varchar(8)
 * @property string|null $coll  type: varchar(1)
 * @property string|null $nom_pokok  type: numeric(9)
 * @property string|null $nom_margin  type: numeric(9)
 * @property string|null $nom_agunan  type: numeric(9)
 * @property string|null $nom_biaya_jual  type: numeric(9)
 * @property string|null $ppap  type: numeric(9)
 */
class Tofform22 extends MciBaseModel
{
    /**
     * Nama tabel (case-sensitive di SQL Server).
     */
    protected $table = 'TOFFORM22';

    /**
     * Daftar LENGKAP kolom sesuai database (15 kolom).
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
        'kdagunan',
        'hubungan',
        'tglambilalih',
        'coll',
        'nom_pokok',
        'nom_margin',
        'nom_agunan',
        'nom_biaya_jual',
        'ppap',
    ];

    /**
     * Casting tipe kolom (non-string saja).
     *
     * @var array<string,string>
     */
    protected $casts = [
        'nom_pokok' => 'decimal:2',
        'nom_margin' => 'decimal:2',
        'nom_agunan' => 'decimal:2',
        'nom_biaya_jual' => 'decimal:2',
        'ppap' => 'decimal:2',
    ];
}
