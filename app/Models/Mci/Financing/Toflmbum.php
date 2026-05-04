<?php

declare(strict_types=1);

namespace App\Models\Mci\Financing;

use App\Models\Mci\MciBaseModel;

/**
 * Model MCI: TOFLMBUM
 * --------------------------------------------------------------------------
 * Domain   : Financing / Loan
 * Tabel    : [dbo].[TOFLMBUM]
 * Kolom    : 19
 * Sumber   : Mapping_MCI_MAR26_01042026.sql / MCI_MAR26_01042026.xlsx
 *
 * CATATAN:
 *  - Model ini READ-ONLY (lihat trait ReadOnlyModel di MciBaseModel).
 *  - Koneksi default `dashboard_data` (SQL Server), otomatis ke DB bulan terbaru.
 *  - Kolom dengan spasi/karakter khusus diakses via:
 *    $model->getAttribute('NAMA KOLOM')  atau  $model->{'NAMA KOLOM'}.
 *
 * @property string $nokontrak type: varchar(11)
 * @property string|null $kdbiaya type: numeric(5)
 * @property string|null $ket type: varchar(30)
 * @property string|null $acdr type: varchar(11)
 * @property string|null $accr type: varchar(11)
 * @property string|null $dokumen type: varchar(30)
 * @property string|null $biaya type: numeric(9)
 * @property string|null $bydrop type: numeric(9)
 * @property string|null $stsbiaya type: varchar(1)
 * @property string|null $kdphk3 type: varchar(10)
 * @property string|null $stscair type: varchar(1)
 * @property string|null $tglcair type: varchar(8)
 * @property string|null $byreal type: numeric(9)
 * @property string|null $kdao type: varchar(15)
 * @property string|null $catatan type: varchar(225)
 * @property string|null $inpuser type: varchar(10)
 * @property string|null $inptgl type: varchar(11)
 * @property string|null $chguser type: varchar(10)
 * @property string|null $chgtgl type: varchar(11)
 */
class Toflmbum extends MciBaseModel
{
    /**
     * Nama tabel (case-sensitive di SQL Server).
     */
    protected $table = 'TOFLMBUM';

    /**
     * Daftar LENGKAP kolom sesuai database (19 kolom).
     * Model ini read-only, $fillable hanya untuk dokumentasi & IDE helper.
     *
     * @var array<int,string>
     */
    protected $fillable = [
        'nokontrak',
        'kdbiaya',
        'ket',
        'acdr',
        'accr',
        'dokumen',
        'biaya',
        'bydrop',
        'stsbiaya',
        'kdphk3',
        'stscair',
        'tglcair',
        'byreal',
        'kdao',
        'catatan',
        'inpuser',
        'inptgl',
        'chguser',
        'chgtgl',
    ];

    /**
     * Casting tipe kolom (non-string saja).
     *
     * @var array<string,string>
     */
    protected $casts = [
        'kdbiaya' => 'decimal:2',
        'biaya' => 'decimal:2',
        'bydrop' => 'decimal:2',
        'byreal' => 'decimal:2',
    ];
}
