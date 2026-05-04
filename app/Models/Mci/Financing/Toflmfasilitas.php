<?php

declare(strict_types=1);

namespace App\Models\Mci\Financing;

use App\Models\Mci\MciBaseModel;

/**
 * Model MCI: TOFLMFASILITAS
 * --------------------------------------------------------------------------
 * Domain   : Financing / Loan
 * Tabel    : [dbo].[TOFLMFASILITAS]
 * Kolom    : 19
 * Sumber   : Mapping_MCI_MAR26_01042026.sql / MCI_MAR26_01042026.xlsx
 *
 * CATATAN:
 *  - Model ini READ-ONLY (lihat trait ReadOnlyModel di MciBaseModel).
 *  - Koneksi default `dashboard_data` (SQL Server), otomatis ke DB bulan terbaru.
 *  - Kolom dengan spasi/karakter khusus diakses via:
 *    $model->getAttribute('NAMA KOLOM')  atau  $model->{'NAMA KOLOM'}.
 *
 * @property string $kode type: varchar(8)
 * @property string|null $nokontrak type: varchar(11)
 * @property string|null $nocif type: varchar(9)
 * @property string|null $nofas type: varchar(11)
 * @property string|null $ciffas type: varchar(9)
 * @property string|null $stsrec type: varchar(2)
 * @property string|null $mdlawal type: numeric(9)
 * @property string|null $mgnawal type: numeric(9)
 * @property string|null $osmdlc type: numeric(9)
 * @property string|null $osmgnc type: numeric(9)
 * @property string|null $inpuser type: varchar(10)
 * @property string|null $inptgl type: varchar(14)
 * @property string|null $inpterm type: varchar(10)
 * @property string|null $chguser type: varchar(10)
 * @property string|null $chgtgl type: varchar(14)
 * @property string|null $chgterm type: varchar(10)
 * @property string|null $autuser type: varchar(10)
 * @property string|null $auttgl type: varchar(14)
 * @property string|null $autterm type: varchar(10)
 */
class Toflmfasilitas extends MciBaseModel
{
    /**
     * Nama tabel (case-sensitive di SQL Server).
     */
    protected $table = 'TOFLMFASILITAS';

    /**
     * Daftar LENGKAP kolom sesuai database (19 kolom).
     * Model ini read-only, $fillable hanya untuk dokumentasi & IDE helper.
     *
     * @var array<int,string>
     */
    protected $fillable = [
        'kode',
        'nokontrak',
        'nocif',
        'nofas',
        'ciffas',
        'stsrec',
        'mdlawal',
        'mgnawal',
        'osmdlc',
        'osmgnc',
        'inpuser',
        'inptgl',
        'inpterm',
        'chguser',
        'chgtgl',
        'chgterm',
        'autuser',
        'auttgl',
        'autterm',
    ];

    /**
     * Casting tipe kolom (non-string saja).
     *
     * @var array<string,string>
     */
    protected $casts = [
        'mdlawal' => 'decimal:2',
        'mgnawal' => 'decimal:2',
        'osmdlc' => 'decimal:2',
        'osmgnc' => 'decimal:2',
    ];
}
