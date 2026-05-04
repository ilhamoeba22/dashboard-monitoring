<?php

declare(strict_types=1);

namespace App\Models\Mci\Lainnya;

use App\Models\Mci\MciBaseModel;

/**
 * Model MCI: TOFMVACC
 * --------------------------------------------------------------------------
 * Domain   : Lainnya
 * Tabel    : [dbo].[TOFMVACC]
 * Kolom    : 12
 * Sumber   : Mapping_MCI_MAR26_01042026.sql / MCI_MAR26_01042026.xlsx
 *
 * CATATAN:
 *  - Model ini READ-ONLY (lihat trait ReadOnlyModel di MciBaseModel).
 *  - Koneksi default `dashboard_data` (SQL Server), otomatis ke DB bulan terbaru.
 *  - Kolom dengan spasi/karakter khusus diakses via:
 *    $model->getAttribute('NAMA KOLOM')  atau  $model->{'NAMA KOLOM'}.
 *
 * @property string|null $kdlembaga type: varchar(10)
 * @property string|null $noacva type: varchar(20)
 * @property string|null $mutasidr type: numeric(9)
 * @property string|null $mutasicr type: numeric(9)
 * @property string|null $sahirrp type: numeric(9)
 * @property string|null $saldoblok type: numeric(9)
 * @property string|null $saldobuku type: numeric(9)
 * @property string|null $kdbuku type: varchar(2)
 * @property string|null $hal type: numeric(5)
 * @property string|null $brs type: numeric(5)
 * @property string|null $notrn type: numeric(5)
 * @property string|null $stsrec type: varchar(1)
 */
class Tofmvacc extends MciBaseModel
{
    /**
     * Nama tabel (case-sensitive di SQL Server).
     */
    protected $table = 'TOFMVACC';

    /**
     * Daftar LENGKAP kolom sesuai database (12 kolom).
     * Model ini read-only, $fillable hanya untuk dokumentasi & IDE helper.
     *
     * @var array<int,string>
     */
    protected $fillable = [
        'kdlembaga',
        'noacva',
        'mutasidr',
        'mutasicr',
        'sahirrp',
        'saldoblok',
        'saldobuku',
        'kdbuku',
        'hal',
        'brs',
        'notrn',
        'stsrec',
    ];

    /**
     * Casting tipe kolom (non-string saja).
     *
     * @var array<string,string>
     */
    protected $casts = [
        'mutasidr' => 'decimal:2',
        'mutasicr' => 'decimal:2',
        'sahirrp' => 'decimal:2',
        'saldoblok' => 'decimal:2',
        'saldobuku' => 'decimal:2',
        'hal' => 'decimal:2',
        'brs' => 'decimal:2',
        'notrn' => 'decimal:2',
    ];
}
