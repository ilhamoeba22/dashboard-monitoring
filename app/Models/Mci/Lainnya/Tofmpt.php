<?php

namespace App\Models\Mci\Lainnya;

use App\Models\Mci\MciBaseModel;

/**
 * Model MCI: TOFMPT
 * --------------------------------------------------------------------------
 * Domain   : Lainnya
 * Tabel    : [dbo].[TOFMPT]
 * Kolom    : 16
 * Sumber   : Mapping_MCI_MAR26_01042026.sql / MCI_MAR26_01042026.xlsx
 *
 * CATATAN:
 *  - Model ini READ-ONLY (lihat trait ReadOnlyModel di MciBaseModel).
 *  - Koneksi default `dashboard_data` (SQL Server), otomatis ke DB bulan terbaru.
 *  - Kolom dengan spasi/karakter khusus diakses via:
 *    $model->getAttribute('NAMA KOLOM')  atau  $model->{'NAMA KOLOM'}.
 *
 * @property string|null $kdpt  type: varchar(10)
 * @property string|null $nim  type: varchar(20)
 * @property string|null $nama  type: varchar(50)
 * @property string|null $progstudy  type: varchar(50)
 * @property string|null $thnakademik  type: varchar(10)
 * @property string|null $kelas  type: varchar(10)
 * @property string|null $sex  type: varchar(1)
 * @property string|null $inpuser  type: varchar(10)
 * @property string|null $inptgljam  type: varchar(14)
 * @property string|null $inpterm  type: varchar(10)
 * @property string|null $chguser  type: varchar(10)
 * @property string|null $chgtgljam  type: varchar(14)
 * @property string|null $chgterm  type: varchar(10)
 * @property string $email  type: varchar(50)
 * @property string $nohp  type: varchar(20)
 * @property string $stssent  type: varchar(1)
 */
class Tofmpt extends MciBaseModel
{
    /**
     * Nama tabel (case-sensitive di SQL Server).
     */
    protected $table = 'TOFMPT';

    /**
     * Daftar LENGKAP kolom sesuai database (16 kolom).
     * Model ini read-only, $fillable hanya untuk dokumentasi & IDE helper.
     *
     * @var array<int,string>
     */
    protected $fillable = [
        'kdpt',
        'nim',
        'nama',
        'progstudy',
        'thnakademik',
        'kelas',
        'sex',
        'inpuser',
        'inptgljam',
        'inpterm',
        'chguser',
        'chgtgljam',
        'chgterm',
        'email',
        'nohp',
        'stssent',
    ];

    /**
     * Casting tipe kolom (non-string saja).
     *
     * @var array<string,string>
     */
    protected $casts = [

    ];
}
