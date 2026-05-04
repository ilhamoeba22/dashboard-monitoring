<?php

declare(strict_types=1);

namespace App\Models\Mci\Cif;

use App\Models\Mci\MciBaseModel;

/**
 * Model MCI: mCIFKLG
 * --------------------------------------------------------------------------
 * Domain   : CIF / Customer
 * Tabel    : [dbo].[mCIFKLG]
 * Kolom    : 19
 * Sumber   : Mapping_MCI_MAR26_01042026.sql / MCI_MAR26_01042026.xlsx
 *
 * CATATAN:
 *  - Model ini READ-ONLY (lihat trait ReadOnlyModel di MciBaseModel).
 *  - Koneksi default `dashboard_data` (SQL Server), otomatis ke DB bulan terbaru.
 *  - Kolom dengan spasi/karakter khusus diakses via:
 *    $model->getAttribute('NAMA KOLOM')  atau  $model->{'NAMA KOLOM'}.
 *
 * @property string $nocif type: varchar(9)
 * @property string|null $urut type: varchar(2)
 * @property string|null $tgllahir type: varchar(8)
 * @property string|null $nama type: varchar(30)
 * @property string|null $kdhub type: varchar(1)
 * @property string|null $stsubah type: varchar(1)
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
 * @property string|null $noid type: varchar(20)
 * @property string|null $hp type: varchar(20)
 * @property string|null $telprmh type: varchar(16)
 */
class Mcifklg extends MciBaseModel
{
    /**
     * Nama tabel (case-sensitive di SQL Server).
     */
    protected $table = 'mCIFKLG';

    /**
     * Daftar LENGKAP kolom sesuai database (19 kolom).
     * Model ini read-only, $fillable hanya untuk dokumentasi & IDE helper.
     *
     * @var array<int,string>
     */
    protected $fillable = [
        'nocif',
        'urut',
        'tgllahir',
        'nama',
        'kdhub',
        'stsubah',
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
        'noid',
        'hp',
        'telprmh',
    ];

    /**
     * Casting tipe kolom (non-string saja).
     *
     * @var array<string,string>
     */
    protected $casts = [

    ];
}
