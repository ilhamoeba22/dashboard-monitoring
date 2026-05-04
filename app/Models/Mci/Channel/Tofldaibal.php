<?php

declare(strict_types=1);

namespace App\Models\Mci\Channel;

use App\Models\Mci\MciBaseModel;

/**
 * Model MCI: TOFLDAIBAL
 * --------------------------------------------------------------------------
 * Domain   : Channel / ATM / Card
 * Tabel    : [dbo].[TOFLDAIBAL]
 * Kolom    : 8
 * Sumber   : Mapping_MCI_MAR26_01042026.sql / MCI_MAR26_01042026.xlsx
 *
 * CATATAN:
 *  - Model ini READ-ONLY (lihat trait ReadOnlyModel di MciBaseModel).
 *  - Koneksi default `dashboard_data` (SQL Server), otomatis ke DB bulan terbaru.
 *  - Kolom dengan spasi/karakter khusus diakses via:
 *    $model->getAttribute('NAMA KOLOM')  atau  $model->{'NAMA KOLOM'}.
 *
 * @property string|null $nokontrak type: varchar(11)
 * @property string|null $tgl type: varchar(8)
 * @property string|null $kdprd type: varchar(2)
 * @property string|null $kdcab type: varchar(3)
 * @property string|null $kdloc type: varchar(2)
 * @property string|null $nosbb type: varchar(7)
 * @property string|null $pokok type: numeric(9)
 * @property string|null $margin type: numeric(9)
 */
class Tofldaibal extends MciBaseModel
{
    /**
     * Nama tabel (case-sensitive di SQL Server).
     */
    protected $table = 'TOFLDAIBAL';

    /**
     * Daftar LENGKAP kolom sesuai database (8 kolom).
     * Model ini read-only, $fillable hanya untuk dokumentasi & IDE helper.
     *
     * @var array<int,string>
     */
    protected $fillable = [
        'nokontrak',
        'tgl',
        'kdprd',
        'kdcab',
        'kdloc',
        'nosbb',
        'pokok',
        'margin',
    ];

    /**
     * Casting tipe kolom (non-string saja).
     *
     * @var array<string,string>
     */
    protected $casts = [
        'pokok' => 'decimal:2',
        'margin' => 'decimal:2',
    ];
}
