<?php

declare(strict_types=1);

namespace App\Models\Mci\Lainnya;

use App\Models\Mci\MciBaseModel;

/**
 * Model MCI: TOFFORM23
 * --------------------------------------------------------------------------
 * Domain   : Lainnya
 * Tabel    : [dbo].[TOFFORM23]
 * Kolom    : 10
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
 * @property string|null $nomor type: numeric(5)
 * @property string|null $jenis_aktiva type: varchar(2)
 * @property string|null $jumlah type: numeric(9)
 * @property string|null $ket type: varchar(100)
 */
class Tofform23 extends MciBaseModel
{
    /**
     * Nama tabel (case-sensitive di SQL Server).
     */
    protected $table = 'TOFFORM23';

    /**
     * Daftar LENGKAP kolom sesuai database (10 kolom).
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
        'nomor',
        'jenis_aktiva',
        'jumlah',
        'ket',
    ];

    /**
     * Casting tipe kolom (non-string saja).
     *
     * @var array<string,string>
     */
    protected $casts = [
        'nomor' => 'decimal:2',
        'jumlah' => 'decimal:2',
    ];
}
