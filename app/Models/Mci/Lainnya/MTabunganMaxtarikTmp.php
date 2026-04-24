<?php

namespace App\Models\Mci\Lainnya;

use App\Models\Mci\MciBaseModel;

/**
 * Model MCI: m_tabungan_maxtarik_tmp
 * --------------------------------------------------------------------------
 * Domain   : Lainnya
 * Tabel    : [dbo].[m_tabungan_maxtarik_tmp]
 * Kolom    : 2
 * Sumber   : Mapping_MCI_MAR26_01042026.sql / MCI_MAR26_01042026.xlsx
 *
 * CATATAN:
 *  - Model ini READ-ONLY (lihat trait ReadOnlyModel di MciBaseModel).
 *  - Koneksi default `dashboard_data` (SQL Server), otomatis ke DB bulan terbaru.
 *  - Kolom dengan spasi/karakter khusus diakses via:
 *    $model->getAttribute('NAMA KOLOM')  atau  $model->{'NAMA KOLOM'}.
 *
 * @property string $noacc  type: varchar(11)
 * @property string|null $taritunai  type: numeric(9)
 */
class MTabunganMaxtarikTmp extends MciBaseModel
{
    /**
     * Nama tabel (case-sensitive di SQL Server).
     */
    protected $table = 'm_tabungan_maxtarik_tmp';

    /**
     * Daftar LENGKAP kolom sesuai database (2 kolom).
     * Model ini read-only, $fillable hanya untuk dokumentasi & IDE helper.
     *
     * @var array<int,string>
     */
    protected $fillable = [
        'noacc',
        'taritunai',
    ];

    /**
     * Casting tipe kolom (non-string saja).
     *
     * @var array<string,string>
     */
    protected $casts = [
        'taritunai' => 'decimal:2',
    ];
}
