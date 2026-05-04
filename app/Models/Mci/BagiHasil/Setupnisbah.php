<?php

declare(strict_types=1);

namespace App\Models\Mci\BagiHasil;

use App\Models\Mci\MciBaseModel;

/**
 * Model MCI: SETUPNISBAH
 * --------------------------------------------------------------------------
 * Domain   : Bagi Hasil
 * Tabel    : [dbo].[SETUPNISBAH]
 * Kolom    : 15
 * Sumber   : Mapping_MCI_MAR26_01042026.sql / MCI_MAR26_01042026.xlsx
 *
 * CATATAN:
 *  - Model ini READ-ONLY (lihat trait ReadOnlyModel di MciBaseModel).
 *  - Koneksi default `dashboard_data` (SQL Server), otomatis ke DB bulan terbaru.
 *  - Kolom dengan spasi/karakter khusus diakses via:
 *    $model->getAttribute('NAMA KOLOM')  atau  $model->{'NAMA KOLOM'}.
 *
 * @property string $profit type: varchar(1)
 * @property string|null $revenue type: varchar(1)
 * @property string|null $htgbh type: varchar(1)
 * @property string|null $cadbh type: varchar(1)
 * @property string|null $postbh type: varchar(1)
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
 */
class Setupnisbah extends MciBaseModel
{
    /**
     * Nama tabel (case-sensitive di SQL Server).
     */
    protected $table = 'SETUPNISBAH';

    /**
     * Daftar LENGKAP kolom sesuai database (15 kolom).
     * Model ini read-only, $fillable hanya untuk dokumentasi & IDE helper.
     *
     * @var array<int,string>
     */
    protected $fillable = [
        'profit',
        'revenue',
        'htgbh',
        'cadbh',
        'postbh',
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
    ];

    /**
     * Casting tipe kolom (non-string saja).
     *
     * @var array<string,string>
     */
    protected $casts = [

    ];
}
