<?php

namespace App\Models\Mci\AsetTetap;

use App\Models\Mci\MciBaseModel;

/**
 * Model MCI: TOFASETP
 * --------------------------------------------------------------------------
 * Domain   : Aset Tetap
 * Tabel    : [dbo].[TOFASETP]
 * Kolom    : 10
 * Sumber   : Mapping_MCI_MAR26_01042026.sql / MCI_MAR26_01042026.xlsx
 *
 * CATATAN:
 *  - Model ini READ-ONLY (lihat trait ReadOnlyModel di MciBaseModel).
 *  - Koneksi default `dashboard_data` (SQL Server), otomatis ke DB bulan terbaru.
 *  - Kolom dengan spasi/karakter khusus diakses via:
 *    $model->getAttribute('NAMA KOLOM')  atau  $model->{'NAMA KOLOM'}.
 *
 * @property string $kdaset  type: varchar(6)
 * @property string $kdmitra  type: varchar(4)
 * @property string|null $cc  type: varchar(2)
 * @property string|null $tahap  type: numeric(5)
 * @property string|null $nomrp  type: numeric(9)
 * @property string|null $kurs  type: numeric(9)
 * @property string|null $nomva  type: numeric(9)
 * @property string|null $tglbyr  type: varchar(8)
 * @property string|null $tglreal  type: varchar(8)
 * @property string|null $stssupl  type: varchar(1)
 */
class Tofasetp extends MciBaseModel
{
    /**
     * Nama tabel (case-sensitive di SQL Server).
     */
    protected $table = 'TOFASETP';

    /**
     * Daftar LENGKAP kolom sesuai database (10 kolom).
     * Model ini read-only, $fillable hanya untuk dokumentasi & IDE helper.
     *
     * @var array<int,string>
     */
    protected $fillable = [
        'kdaset',
        'kdmitra',
        'cc',
        'tahap',
        'nomrp',
        'kurs',
        'nomva',
        'tglbyr',
        'tglreal',
        'stssupl',
    ];

    /**
     * Casting tipe kolom (non-string saja).
     *
     * @var array<string,string>
     */
    protected $casts = [
        'tahap' => 'decimal:2',
        'nomrp' => 'decimal:2',
        'kurs' => 'decimal:2',
        'nomva' => 'decimal:2',
    ];
}
