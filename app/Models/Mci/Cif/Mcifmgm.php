<?php

namespace App\Models\Mci\Cif;

use App\Models\Mci\MciBaseModel;

/**
 * Model MCI: mCIFMGM
 * --------------------------------------------------------------------------
 * Domain   : CIF / Customer
 * Tabel    : [dbo].[mCIFMGM]
 * Kolom    : 28
 * Sumber   : Mapping_MCI_MAR26_01042026.sql / MCI_MAR26_01042026.xlsx
 *
 * CATATAN:
 *  - Model ini READ-ONLY (lihat trait ReadOnlyModel di MciBaseModel).
 *  - Koneksi default `dashboard_data` (SQL Server), otomatis ke DB bulan terbaru.
 *  - Kolom dengan spasi/karakter khusus diakses via:
 *    $model->getAttribute('NAMA KOLOM')  atau  $model->{'NAMA KOLOM'}.
 *
 * @property string $nocif  type: varchar(9)
 * @property string $nama  type: varchar(30)
 * @property string|null $sex  type: varchar(1)
 * @property string|null $id  type: varchar(30)
 * @property string|null $npwp  type: varchar(30)
 * @property string|null $jabat  type: varchar(2)
 * @property string|null $pangsa  type: numeric(5)
 * @property string|null $alamat  type: varchar(100)
 * @property string|null $kelurahan  type: varchar(40)
 * @property string|null $kecamatan  type: varchar(40)
 * @property string|null $dati2  type: varchar(4)
 * @property string|null $hp  type: varchar(15)
 * @property string|null $telp  type: varchar(15)
 * @property string|null $stsubah  type: varchar(1)
 * @property string|null $stsrec  type: varchar(1)
 * @property string|null $inpuser  type: varchar(10)
 * @property string|null $inptgl  type: varchar(14)
 * @property string|null $inpterm  type: varchar(10)
 * @property string|null $chguser  type: varchar(10)
 * @property string|null $chgtgl  type: varchar(14)
 * @property string|null $chgterm  type: varchar(10)
 * @property string|null $autuser  type: varchar(10)
 * @property string|null $auttgl  type: varchar(14)
 * @property string|null $autterm  type: varchar(10)
 * @property string|null $kdid  type: varchar(1)
 * @property string|null $stspengurus  type: varchar(8)
 * @property string|null $tmplhr  type: varchar(30)
 * @property string|null $tgllhr  type: varchar(8)
 */
class Mcifmgm extends MciBaseModel
{
    /**
     * Nama tabel (case-sensitive di SQL Server).
     */
    protected $table = 'mCIFMGM';

    /**
     * Daftar LENGKAP kolom sesuai database (28 kolom).
     * Model ini read-only, $fillable hanya untuk dokumentasi & IDE helper.
     *
     * @var array<int,string>
     */
    protected $fillable = [
        'nocif',
        'nama',
        'sex',
        'id',
        'npwp',
        'jabat',
        'pangsa',
        'alamat',
        'kelurahan',
        'kecamatan',
        'dati2',
        'hp',
        'telp',
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
        'kdid',
        'stspengurus',
        'tmplhr',
        'tgllhr',
    ];

    /**
     * Casting tipe kolom (non-string saja).
     *
     * @var array<string,string>
     */
    protected $casts = [
        'pangsa' => 'decimal:2',
    ];
}
