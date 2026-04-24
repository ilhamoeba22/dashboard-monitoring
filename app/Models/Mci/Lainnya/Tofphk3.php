<?php

namespace App\Models\Mci\Lainnya;

use App\Models\Mci\MciBaseModel;

/**
 * Model MCI: TOFPHK3
 * --------------------------------------------------------------------------
 * Domain   : Lainnya
 * Tabel    : [dbo].[TOFPHK3]
 * Kolom    : 30
 * Sumber   : Mapping_MCI_MAR26_01042026.sql / MCI_MAR26_01042026.xlsx
 *
 * CATATAN:
 *  - Model ini READ-ONLY (lihat trait ReadOnlyModel di MciBaseModel).
 *  - Koneksi default `dashboard_data` (SQL Server), otomatis ke DB bulan terbaru.
 *  - Kolom dengan spasi/karakter khusus diakses via:
 *    $model->getAttribute('NAMA KOLOM')  atau  $model->{'NAMA KOLOM'}.
 *
 * @property string|null $kdmitra  type: varchar(20)
 * @property string|null $kdpok  type: varchar(2)
 * @property string|null $nama  type: varchar(30)
 * @property string|null $snama  type: varchar(10)
 * @property string|null $alamat  type: varchar(40)
 * @property string|null $kota  type: varchar(20)
 * @property string|null $kdpos  type: varchar(5)
 * @property string|null $npwp  type: varchar(25)
 * @property string|null $pic  type: varchar(30)
 * @property string|null $nohp  type: varchar(20)
 * @property string|null $notelp  type: varchar(15)
 * @property string|null $email  type: varchar(20)
 * @property string|null $noac  type: varchar(11)
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
 * @property string|null $bendahara1  type: varchar(11)
 * @property string|null $bendahara2  type: varchar(11)
 * @property string|null $kdloc  type: varchar(2)
 * @property string|null $kelurahan  type: varchar(30)
 * @property string|null $kecamatan  type: varchar(30)
 * @property string|null $dati2  type: varchar(4)
 * @property string|null $ID  type: varchar(16)
 */
class Tofphk3 extends MciBaseModel
{
    /**
     * Nama tabel (case-sensitive di SQL Server).
     */
    protected $table = 'TOFPHK3';

    /**
     * Daftar LENGKAP kolom sesuai database (30 kolom).
     * Model ini read-only, $fillable hanya untuk dokumentasi & IDE helper.
     *
     * @var array<int,string>
     */
    protected $fillable = [
        'kdmitra',
        'kdpok',
        'nama',
        'snama',
        'alamat',
        'kota',
        'kdpos',
        'npwp',
        'pic',
        'nohp',
        'notelp',
        'email',
        'noac',
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
        'bendahara1',
        'bendahara2',
        'kdloc',
        'kelurahan',
        'kecamatan',
        'dati2',
        'ID',
    ];

    /**
     * Casting tipe kolom (non-string saja).
     *
     * @var array<string,string>
     */
    protected $casts = [

    ];
}
