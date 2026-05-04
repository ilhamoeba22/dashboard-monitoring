<?php

declare(strict_types=1);

namespace App\Models\Mci\Financing;

use App\Models\Mci\MciBaseModel;

/**
 * Model MCI: TMPLMCOL
 * --------------------------------------------------------------------------
 * Domain   : Financing / Loan
 * Tabel    : [dbo].[TMPLMCOL]
 * Kolom    : 7
 * Sumber   : Mapping_MCI_MAR26_01042026.sql / MCI_MAR26_01042026.xlsx
 *
 * CATATAN:
 *  - Model ini READ-ONLY (lihat trait ReadOnlyModel di MciBaseModel).
 *  - Koneksi default `dashboard_data` (SQL Server), otomatis ke DB bulan terbaru.
 *  - Kolom dengan spasi/karakter khusus diakses via:
 *    $model->getAttribute('NAMA KOLOM')  atau  $model->{'NAMA KOLOM'}.
 *
 * @property string $nokontrak type: varchar(11)
 * @property string $colbaru type: varchar(1)
 * @property string|null $blnmdl type: numeric(5)
 * @property string|null $blnmgn type: numeric(5)
 * @property string|null $blntgk type: numeric(5)
 * @property string|null $kdcol type: varchar(2)
 * @property string|null $pokpby type: varchar(2)
 */
class Tmplmcol extends MciBaseModel
{
    /**
     * Nama tabel (case-sensitive di SQL Server).
     */
    protected $table = 'TMPLMCOL';

    /**
     * Daftar LENGKAP kolom sesuai database (7 kolom).
     * Model ini read-only, $fillable hanya untuk dokumentasi & IDE helper.
     *
     * @var array<int,string>
     */
    protected $fillable = [
        'nokontrak',
        'colbaru',
        'blnmdl',
        'blnmgn',
        'blntgk',
        'kdcol',
        'pokpby',
    ];

    /**
     * Casting tipe kolom (non-string saja).
     *
     * @var array<string,string>
     */
    protected $casts = [
        'blnmdl' => 'decimal:2',
        'blnmgn' => 'decimal:2',
        'blntgk' => 'decimal:2',
    ];
}
