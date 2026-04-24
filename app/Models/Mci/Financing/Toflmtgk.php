<?php

namespace App\Models\Mci\Financing;

use App\Models\Mci\MciBaseModel;

/**
 * Model MCI: TOFLMTGK
 * --------------------------------------------------------------------------
 * Domain   : Financing / Loan
 * Tabel    : [dbo].[TOFLMTGK]
 * Kolom    : 12
 * Sumber   : Mapping_MCI_MAR26_01042026.sql / MCI_MAR26_01042026.xlsx
 *
 * CATATAN:
 *  - Model ini READ-ONLY (lihat trait ReadOnlyModel di MciBaseModel).
 *  - Koneksi default `dashboard_data` (SQL Server), otomatis ke DB bulan terbaru.
 *  - Kolom dengan spasi/karakter khusus diakses via:
 *    $model->getAttribute('NAMA KOLOM')  atau  $model->{'NAMA KOLOM'}.
 *
 * @property string $nokontrak  type: varchar(11)
 * @property string $thnbln  type: varchar(6)
 * @property string|null $tgkmdl  type: numeric(9)
 * @property string|null $tgkmgn  type: numeric(9)
 * @property string|null $tgkdnd  type: numeric(9)
 * @property string|null $blnmdl  type: numeric(5)
 * @property string|null $blnmgn  type: numeric(5)
 * @property string|null $blndnd  type: numeric(5)
 * @property string|null $collama  type: varchar(1)
 * @property string|null $colbaru  type: varchar(1)
 * @property string|null $mdleom  type: numeric(9)
 * @property string|null $mgneom  type: numeric(9)
 */
class Toflmtgk extends MciBaseModel
{
    /**
     * Nama tabel (case-sensitive di SQL Server).
     */
    protected $table = 'TOFLMTGK';

    /**
     * Daftar LENGKAP kolom sesuai database (12 kolom).
     * Model ini read-only, $fillable hanya untuk dokumentasi & IDE helper.
     *
     * @var array<int,string>
     */
    protected $fillable = [
        'nokontrak',
        'thnbln',
        'tgkmdl',
        'tgkmgn',
        'tgkdnd',
        'blnmdl',
        'blnmgn',
        'blndnd',
        'collama',
        'colbaru',
        'mdleom',
        'mgneom',
    ];

    /**
     * Casting tipe kolom (non-string saja).
     *
     * @var array<string,string>
     */
    protected $casts = [
        'tgkmdl' => 'decimal:2',
        'tgkmgn' => 'decimal:2',
        'tgkdnd' => 'decimal:2',
        'blnmdl' => 'decimal:2',
        'blnmgn' => 'decimal:2',
        'blndnd' => 'decimal:2',
        'mdleom' => 'decimal:2',
        'mgneom' => 'decimal:2',
    ];
}
