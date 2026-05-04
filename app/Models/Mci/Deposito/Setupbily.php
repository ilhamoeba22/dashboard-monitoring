<?php

declare(strict_types=1);

namespace App\Models\Mci\Deposito;

use App\Models\Mci\MciBaseModel;

/**
 * Model MCI: SETUPBILY
 * --------------------------------------------------------------------------
 * Domain   : Deposito
 * Tabel    : [dbo].[SETUPBILY]
 * Kolom    : 19
 * Sumber   : Mapping_MCI_MAR26_01042026.sql / MCI_MAR26_01042026.xlsx
 *
 * CATATAN:
 *  - Model ini READ-ONLY (lihat trait ReadOnlyModel di MciBaseModel).
 *  - Koneksi default `dashboard_data` (SQL Server), otomatis ke DB bulan terbaru.
 *  - Kolom dengan spasi/karakter khusus diakses via:
 *    $model->getAttribute('NAMA KOLOM')  atau  $model->{'NAMA KOLOM'}.
 *
 * @property string $kdprd type: varchar(2)
 * @property string|null $kolom type: varchar(25)
 * @property string|null $x type: numeric(9)
 * @property string|null $y type: numeric(9)
 * @property string|null $w type: numeric(9)
 * @property string|null $h type: numeric(9)
 * @property string|null $stsrec type: varchar(1)
 * @property string|null $inpuser type: varchar(10)
 * @property string|null $inptgl type: varchar(14)
 * @property string|null $inpterm type: varchar(10)
 * @property string|null $chguser type: varchar(10)
 * @property string|null $chgtgl type: varchar(14)
 * @property string|null $chgterm type: varchar(10)
 * @property string|null $autuser type: varchar(10)
 * @property string|null $auttgl type: varchar(14)
 * @property string|null $autterm type: varchar(10)
 * @property string|null $kdcab type: varchar(3)
 * @property string|null $kdloc type: varchar(2)
 * @property string|null $keterangan type: varchar(100)
 */
class Setupbily extends MciBaseModel
{
    /**
     * Nama tabel (case-sensitive di SQL Server).
     */
    protected $table = 'SETUPBILY';

    /**
     * Daftar LENGKAP kolom sesuai database (19 kolom).
     * Model ini read-only, $fillable hanya untuk dokumentasi & IDE helper.
     *
     * @var array<int,string>
     */
    protected $fillable = [
        'kdprd',
        'kolom',
        'x',
        'y',
        'w',
        'h',
        'stsrec',
        'inpuser',
        'inptgl',
        'inpterm',
        'chguser',
        'chgtgl',
        'chgterm',
        'autuser',
        'auttgl',
        'autterm',
        'kdcab',
        'kdloc',
        'keterangan',
    ];

    /**
     * Casting tipe kolom (non-string saja).
     *
     * @var array<string,string>
     */
    protected $casts = [
        'x' => 'decimal:2',
        'y' => 'decimal:2',
        'w' => 'decimal:2',
        'h' => 'decimal:2',
    ];
}
