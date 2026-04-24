<?php

namespace App\Models\Mci\Giro;

use App\Models\Mci\MciBaseModel;

/**
 * Model MCI: TOFRIC
 * --------------------------------------------------------------------------
 * Domain   : Giro / RK
 * Tabel    : [dbo].[TOFRIC]
 * Kolom    : 11
 * Sumber   : Mapping_MCI_MAR26_01042026.sql / MCI_MAR26_01042026.xlsx
 *
 * CATATAN:
 *  - Model ini READ-ONLY (lihat trait ReadOnlyModel di MciBaseModel).
 *  - Koneksi default `dashboard_data` (SQL Server), otomatis ke DB bulan terbaru.
 *  - Kolom dengan spasi/karakter khusus diakses via:
 *    $model->getAttribute('NAMA KOLOM')  atau  $model->{'NAMA KOLOM'}.
 *
 * @property string $nori  type: varchar(11)
 * @property string|null $kurs  type: numeric(9)
 * @property string|null $plafond  type: numeric(9)
 * @property string|null $sawalrp  type: numeric(9)
 * @property string|null $sawalva  type: numeric(9)
 * @property string|null $mtsdr  type: numeric(9)
 * @property string|null $mtscr  type: numeric(9)
 * @property string|null $sahirrp  type: numeric(9)
 * @property string|null $sahirva  type: numeric(9)
 * @property string|null $trnke  type: numeric(5)
 * @property string|null $stsrec  type: varchar(1)
 */
class Tofric extends MciBaseModel
{
    /**
     * Nama tabel (case-sensitive di SQL Server).
     */
    protected $table = 'TOFRIC';

    /**
     * Daftar LENGKAP kolom sesuai database (11 kolom).
     * Model ini read-only, $fillable hanya untuk dokumentasi & IDE helper.
     *
     * @var array<int,string>
     */
    protected $fillable = [
        'nori',
        'kurs',
        'plafond',
        'sawalrp',
        'sawalva',
        'mtsdr',
        'mtscr',
        'sahirrp',
        'sahirva',
        'trnke',
        'stsrec',
    ];

    /**
     * Casting tipe kolom (non-string saja).
     *
     * @var array<string,string>
     */
    protected $casts = [
        'kurs' => 'decimal:2',
        'plafond' => 'decimal:2',
        'sawalrp' => 'decimal:2',
        'sawalva' => 'decimal:2',
        'mtsdr' => 'decimal:2',
        'mtscr' => 'decimal:2',
        'sahirrp' => 'decimal:2',
        'sahirva' => 'decimal:2',
        'trnke' => 'decimal:2',
    ];
}
