<?php

declare(strict_types=1);

namespace App\Models\Mci\Lainnya;

use App\Models\Mci\MciBaseModel;

/**
 * Model MCI: TOFTID
 * --------------------------------------------------------------------------
 * Domain   : Lainnya
 * Tabel    : [dbo].[TOFTID]
 * Kolom    : 42
 * Sumber   : Mapping_MCI_MAR26_01042026.sql / MCI_MAR26_01042026.xlsx
 *
 * CATATAN:
 *  - Model ini READ-ONLY (lihat trait ReadOnlyModel di MciBaseModel).
 *  - Koneksi default `dashboard_data` (SQL Server), otomatis ke DB bulan terbaru.
 *  - Kolom dengan spasi/karakter khusus diakses via:
 *    $model->getAttribute('NAMA KOLOM')  atau  $model->{'NAMA KOLOM'}.
 *
 * @property string|null $kdbuku type: varchar(2)
 * @property string|null $brsawal type: numeric(5)
 * @property string|null $stsnmktr type: varchar(1)
 * @property string|null $stsnotab type: varchar(1)
 * @property string|null $stsnama type: varchar(1)
 * @property string|null $stsalamat type: varchar(1)
 * @property string|null $STSLURAH type: varchar(1)
 * @property string|null $stskota type: varchar(1)
 * @property string|null $ststelp type: varchar(1)
 * @property string|null $ststgl type: varchar(1)
 * @property string|null $kolnmktr type: numeric(5)
 * @property string|null $brsnmktr type: numeric(5)
 * @property string|null $kolnotab type: numeric(5)
 * @property string|null $brsnotab type: numeric(5)
 * @property string|null $kolnama type: numeric(5)
 * @property string|null $brsnama type: numeric(5)
 * @property string|null $kolalamat type: numeric(5)
 * @property string|null $brsalamat type: numeric(5)
 * @property string|null $KOLLURAH type: numeric(5)
 * @property string|null $BRSLURAH type: numeric(5)
 * @property string|null $kolkota type: numeric(5)
 * @property string|null $brskota type: numeric(5)
 * @property string|null $koltelp type: numeric(5)
 * @property string|null $brstelp type: numeric(5)
 * @property string|null $brstgl type: numeric(5)
 * @property string|null $koltgl type: numeric(5)
 * @property string|null $stsrec type: varchar(1)
 * @property string|null $inpuser type: varchar(10)
 * @property string|null $inptgljam type: varchar(14)
 * @property string|null $inpterm type: varchar(10)
 * @property string|null $chguser type: varchar(10)
 * @property string|null $chgtgljam type: varchar(14)
 * @property string|null $chgterm type: varchar(10)
 * @property string|null $autuser type: varchar(10)
 * @property string|null $auttgljam type: varchar(14)
 * @property string|null $autterm type: varchar(10)
 * @property string|null $stsnoid type: varchar(1)
 * @property string|null $brsnoid type: numeric(5)
 * @property string|null $kolnoid type: numeric(5)
 * @property string|null $stsalamat_kantor type: varchar(1)
 * @property string|null $brsalamat_kantor type: numeric(5)
 * @property string|null $kolalamat_kantor type: numeric(5)
 */
class Toftid extends MciBaseModel
{
    /**
     * Nama tabel (case-sensitive di SQL Server).
     */
    protected $table = 'TOFTID';

    /**
     * Daftar LENGKAP kolom sesuai database (42 kolom).
     * Model ini read-only, $fillable hanya untuk dokumentasi & IDE helper.
     *
     * @var array<int,string>
     */
    protected $fillable = [
        'kdbuku',
        'brsawal',
        'stsnmktr',
        'stsnotab',
        'stsnama',
        'stsalamat',
        'STSLURAH',
        'stskota',
        'ststelp',
        'ststgl',
        'kolnmktr',
        'brsnmktr',
        'kolnotab',
        'brsnotab',
        'kolnama',
        'brsnama',
        'kolalamat',
        'brsalamat',
        'KOLLURAH',
        'BRSLURAH',
        'kolkota',
        'brskota',
        'koltelp',
        'brstelp',
        'brstgl',
        'koltgl',
        'stsrec',
        'inpuser',
        'inptgljam',
        'inpterm',
        'chguser',
        'chgtgljam',
        'chgterm',
        'autuser',
        'auttgljam',
        'autterm',
        'stsnoid',
        'brsnoid',
        'kolnoid',
        'stsalamat_kantor',
        'brsalamat_kantor',
        'kolalamat_kantor',
    ];

    /**
     * Casting tipe kolom (non-string saja).
     *
     * @var array<string,string>
     */
    protected $casts = [
        'brsawal' => 'decimal:2',
        'kolnmktr' => 'decimal:2',
        'brsnmktr' => 'decimal:2',
        'kolnotab' => 'decimal:2',
        'brsnotab' => 'decimal:2',
        'kolnama' => 'decimal:2',
        'brsnama' => 'decimal:2',
        'kolalamat' => 'decimal:2',
        'brsalamat' => 'decimal:2',
        'KOLLURAH' => 'decimal:2',
        'BRSLURAH' => 'decimal:2',
        'kolkota' => 'decimal:2',
        'brskota' => 'decimal:2',
        'koltelp' => 'decimal:2',
        'brstelp' => 'decimal:2',
        'brstgl' => 'decimal:2',
        'koltgl' => 'decimal:2',
        'brsnoid' => 'decimal:2',
        'kolnoid' => 'decimal:2',
        'brsalamat_kantor' => 'decimal:2',
        'kolalamat_kantor' => 'decimal:2',
    ];
}
