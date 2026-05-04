<?php

declare(strict_types=1);

namespace App\Models\Mci\Lainnya;

use App\Models\Mci\MciBaseModel;

/**
 * Model MCI: TOFBONUS
 * --------------------------------------------------------------------------
 * Domain   : Lainnya
 * Tabel    : [dbo].[TOFBONUS]
 * Kolom    : 6
 * Sumber   : Mapping_MCI_MAR26_01042026.sql / MCI_MAR26_01042026.xlsx
 *
 * CATATAN:
 *  - Model ini READ-ONLY (lihat trait ReadOnlyModel di MciBaseModel).
 *  - Koneksi default `dashboard_data` (SQL Server), otomatis ke DB bulan terbaru.
 *  - Kolom dengan spasi/karakter khusus diakses via:
 *    $model->getAttribute('NAMA KOLOM')  atau  $model->{'NAMA KOLOM'}.
 *
 * @property string $kdprddn type: varchar(1)
 * @property string $periode type: varchar(6)
 * @property string|null $kdcab type: varchar(3)
 * @property string|null $cc type: varchar(2)
 * @property string|null $bonus type: numeric(9)
 * @property string|null $stsrec type: varchar(1)
 */
class Tofbonus extends MciBaseModel
{
    /**
     * Nama tabel (case-sensitive di SQL Server).
     */
    protected $table = 'TOFBONUS';

    /**
     * Daftar LENGKAP kolom sesuai database (6 kolom).
     * Model ini read-only, $fillable hanya untuk dokumentasi & IDE helper.
     *
     * @var array<int,string>
     */
    protected $fillable = [
        'kdprddn',
        'periode',
        'kdcab',
        'cc',
        'bonus',
        'stsrec',
    ];

    /**
     * Casting tipe kolom (non-string saja).
     *
     * @var array<string,string>
     */
    protected $casts = [
        'bonus' => 'decimal:2',
    ];
}
