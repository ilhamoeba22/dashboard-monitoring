<?php

declare(strict_types=1);

namespace App\Models\Mci\Cif;

use App\Models\Mci\MciBaseModel;

/**
 * Model MCI: MCIFJOB
 * --------------------------------------------------------------------------
 * Domain   : CIF / Customer
 * Tabel    : [dbo].[MCIFJOB]
 * Kolom    : 23
 * Sumber   : Mapping_MCI_MAR26_01042026.sql / MCI_MAR26_01042026.xlsx
 *
 * CATATAN:
 *  - Model ini READ-ONLY (lihat trait ReadOnlyModel di MciBaseModel).
 *  - Koneksi default `dashboard_data` (SQL Server), otomatis ke DB bulan terbaru.
 *  - Kolom dengan spasi/karakter khusus diakses via:
 *    $model->getAttribute('NAMA KOLOM')  atau  $model->{'NAMA KOLOM'}.
 *
 * @property string|null $nocif type: varchar(9)
 * @property string|null $kdkerja type: varchar(5)
 * @property string|null $kdbidang type: varchar(5)
 * @property string|null $namapt type: varchar(100)
 * @property string|null $alamat type: varchar(256)
 * @property string|null $kota type: varchar(50)
 * @property string|null $kdpos type: varchar(5)
 * @property string|null $kdhasil type: varchar(5)
 * @property string|null $nip type: varchar(20)
 * @property string|null $stsrec type: varchar(1)
 * @property string|null $inpuser type: varchar(10)
 * @property string|null $inptgljam type: varchar(14)
 * @property string|null $inpterm type: varchar(10)
 * @property string|null $chguser type: varchar(10)
 * @property string|null $chgtgljam type: varchar(14)
 * @property string|null $chgterm type: varchar(10)
 * @property string|null $kdsumbhasil type: varchar(1)
 * @property string|null $kdbidangslik type: varchar(6)
 * @property string|null $stspep type: varchar(1)
 * @property string $jabatan type: varchar(256)
 * @property string $jabatan_lain type: varchar(256)
 * @property string $bagian type: varchar(256)
 * @property string|null $kdrisk type: char(1)
 */
class Mcifjob extends MciBaseModel
{
    /**
     * Nama tabel (case-sensitive di SQL Server).
     */
    protected $table = 'MCIFJOB';

    /**
     * Daftar LENGKAP kolom sesuai database (23 kolom).
     * Model ini read-only, $fillable hanya untuk dokumentasi & IDE helper.
     *
     * @var array<int,string>
     */
    protected $fillable = [
        'nocif',
        'kdkerja',
        'kdbidang',
        'namapt',
        'alamat',
        'kota',
        'kdpos',
        'kdhasil',
        'nip',
        'stsrec',
        'inpuser',
        'inptgljam',
        'inpterm',
        'chguser',
        'chgtgljam',
        'chgterm',
        'kdsumbhasil',
        'kdbidangslik',
        'stspep',
        'jabatan',
        'jabatan_lain',
        'bagian',
        'kdrisk',
    ];

    /**
     * Casting tipe kolom (non-string saja).
     *
     * @var array<string,string>
     */
    protected $casts = [

    ];
}
