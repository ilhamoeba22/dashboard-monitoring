<?php

declare(strict_types=1);

namespace App\Models\Mci\Investasi;

use App\Models\Mci\MciBaseModel;

/**
 * Model MCI: TOFSHM
 * --------------------------------------------------------------------------
 * Domain   : Investasi / Saham
 * Tabel    : [dbo].[TOFSHM]
 * Kolom    : 27
 * Sumber   : Mapping_MCI_MAR26_01042026.sql / MCI_MAR26_01042026.xlsx
 *
 * CATATAN:
 *  - Model ini READ-ONLY (lihat trait ReadOnlyModel di MciBaseModel).
 *  - Koneksi default `dashboard_data` (SQL Server), otomatis ke DB bulan terbaru.
 *  - Kolom dengan spasi/karakter khusus diakses via:
 *    $model->getAttribute('NAMA KOLOM')  atau  $model->{'NAMA KOLOM'}.
 *
 * @property string|null $noreg type: varchar(30)
 * @property string|null $urut type: numeric(9)
 * @property string|null $nokontrak type: varchar(11)
 * @property string|null $kddoc type: varchar(5)
 * @property string|null $nodoc type: varchar(20)
 * @property string|null $tglexp type: varchar(8)
 * @property string|null $luas type: numeric(5)
 * @property string|null $provinsi type: varchar(50)
 * @property string|null $kab type: varchar(50)
 * @property string|null $kecamatan type: varchar(50)
 * @property string|null $kelurahan type: varchar(50)
 * @property string|null $alamat type: varchar(50)
 * @property string|null $penerbit type: varchar(50)
 * @property string|null $an type: varchar(50)
 * @property string|null $ket type: varchar(150)
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
 * @property string|null $nosrtukur type: varchar(15)
 * @property string|null $tglsrtukur type: varchar(8)
 */
class Tofshm extends MciBaseModel
{
    /**
     * Nama tabel (case-sensitive di SQL Server).
     */
    protected $table = 'TOFSHM';

    /**
     * Daftar LENGKAP kolom sesuai database (27 kolom).
     * Model ini read-only, $fillable hanya untuk dokumentasi & IDE helper.
     *
     * @var array<int,string>
     */
    protected $fillable = [
        'noreg',
        'urut',
        'nokontrak',
        'kddoc',
        'nodoc',
        'tglexp',
        'luas',
        'provinsi',
        'kab',
        'kecamatan',
        'kelurahan',
        'alamat',
        'penerbit',
        'an',
        'ket',
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
        'nosrtukur',
        'tglsrtukur',
    ];

    /**
     * Casting tipe kolom (non-string saja).
     *
     * @var array<string,string>
     */
    protected $casts = [
        'urut' => 'decimal:2',
        'luas' => 'decimal:2',
    ];
}
