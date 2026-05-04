<?php

declare(strict_types=1);

namespace App\Models\Mci\Lainnya;

use App\Models\Mci\MciBaseModel;

/**
 * Model MCI: tmpekycdata
 * --------------------------------------------------------------------------
 * Domain   : Lainnya
 * Tabel    : [dbo].[tmpekycdata]
 * Kolom    : 51
 * Sumber   : Mapping_MCI_MAR26_01042026.sql / MCI_MAR26_01042026.xlsx
 *
 * CATATAN:
 *  - Model ini READ-ONLY (lihat trait ReadOnlyModel di MciBaseModel).
 *  - Koneksi default `dashboard_data` (SQL Server), otomatis ke DB bulan terbaru.
 *  - Kolom dengan spasi/karakter khusus diakses via:
 *    $model->getAttribute('NAMA KOLOM')  atau  $model->{'NAMA KOLOM'}.
 *
 * @property string $uuid type: varchar(200)
 * @property string|null $tgljam type: varchar(14)
 * @property string|null $kdmitra type: varchar(50)
 * @property string|null $deviceid type: varchar(50)
 * @property string|null $noid type: varchar(30)
 * @property string|null $nokk type: varchar(30)
 * @property string|null $idexp type: varchar(30)
 * @property string|null $nama type: varchar(50)
 * @property string|null $namaibu type: varchar(50)
 * @property string|null $tmplhr type: varchar(40)
 * @property string|null $tgllhr type: varchar(8)
 * @property string|null $email type: varchar(50)
 * @property string|null $nohp type: varchar(20)
 * @property string $jk type: char(1)
 * @property string|null $pekerjaan type: varchar(50)
 * @property string|null $kewarganegaraan type: varchar(50)
 * @property string $agama type: varchar(20)
 * @property string $goldarah type: char(1)
 * @property string|null $stskawin type: varchar(30)
 * @property string|null $provinsi type: varchar(50)
 * @property string|null $alamat type: varchar(250)
 * @property string|null $kota type: varchar(50)
 * @property string|null $kecamatan type: varchar(50)
 * @property string|null $kelurahan type: varchar(50)
 * @property string|null $rt type: varchar(5)
 * @property string|null $rw type: varchar(5)
 * @property string $stsrec type: char(1)
 * @property string $stsrec2 type: char(1)
 * @property string|null $inptgljam type: varchar(14)
 * @property string|null $inpuser type: varchar(200)
 * @property string|null $authtgljam type: varchar(14)
 * @property string|null $authuser type: varchar(200)
 * @property string|null $chgtgljam type: varchar(14)
 * @property string|null $chguser type: varchar(200)
 * @property string|null $ekycid type: varchar(200)
 * @property string $score_noid type: numeric(9)
 * @property string $score_nama type: numeric(9)
 * @property string $score_tmplhr type: numeric(9)
 * @property string $score_tgllhr type: numeric(9)
 * @property string|null $score_alamat type: numeric(9)
 * @property string $score_provinsi type: numeric(9)
 * @property string $score_kota type: numeric(9)
 * @property string $score_kecamatan type: numeric(9)
 * @property string|null $score_kelurahan type: numeric(9)
 * @property string $score_nokk type: numeric(9)
 * @property string $score_nmibu type: numeric(9)
 * @property string $score_selfie type: numeric(9)
 * @property string $score_photoid type: numeric(9)
 * @property string $score_liveness type: numeric(9)
 * @property string|null $note1 type: text(16)
 * @property string|null $note2 type: text(16)
 */
class Tmpekycdata extends MciBaseModel
{
    /**
     * Nama tabel (case-sensitive di SQL Server).
     */
    protected $table = 'tmpekycdata';

    /**
     * Daftar LENGKAP kolom sesuai database (51 kolom).
     * Model ini read-only, $fillable hanya untuk dokumentasi & IDE helper.
     *
     * @var array<int,string>
     */
    protected $fillable = [
        'uuid',
        'tgljam',
        'kdmitra',
        'deviceid',
        'noid',
        'nokk',
        'idexp',
        'nama',
        'namaibu',
        'tmplhr',
        'tgllhr',
        'email',
        'nohp',
        'jk',
        'pekerjaan',
        'kewarganegaraan',
        'agama',
        'goldarah',
        'stskawin',
        'provinsi',
        'alamat',
        'kota',
        'kecamatan',
        'kelurahan',
        'rt',
        'rw',
        'stsrec',
        'stsrec2',
        'inptgljam',
        'inpuser',
        'authtgljam',
        'authuser',
        'chgtgljam',
        'chguser',
        'ekycid',
        'score_noid',
        'score_nama',
        'score_tmplhr',
        'score_tgllhr',
        'score_alamat',
        'score_provinsi',
        'score_kota',
        'score_kecamatan',
        'score_kelurahan',
        'score_nokk',
        'score_nmibu',
        'score_selfie',
        'score_photoid',
        'score_liveness',
        'note1',
        'note2',
    ];

    /**
     * Casting tipe kolom (non-string saja).
     *
     * @var array<string,string>
     */
    protected $casts = [
        'score_noid' => 'decimal:2',
        'score_nama' => 'decimal:2',
        'score_tmplhr' => 'decimal:2',
        'score_tgllhr' => 'decimal:2',
        'score_alamat' => 'decimal:2',
        'score_provinsi' => 'decimal:2',
        'score_kota' => 'decimal:2',
        'score_kecamatan' => 'decimal:2',
        'score_kelurahan' => 'decimal:2',
        'score_nokk' => 'decimal:2',
        'score_nmibu' => 'decimal:2',
        'score_selfie' => 'decimal:2',
        'score_photoid' => 'decimal:2',
        'score_liveness' => 'decimal:2',
    ];
}
