<?php

namespace App\Models\Mci\Lainnya;

use App\Models\Mci\MciBaseModel;

/**
 * Model MCI: TOFMCHN
 * --------------------------------------------------------------------------
 * Domain   : Lainnya
 * Tabel    : [dbo].[TOFMCHN]
 * Kolom    : 34
 * Sumber   : Mapping_MCI_MAR26_01042026.sql / MCI_MAR26_01042026.xlsx
 *
 * CATATAN:
 *  - Model ini READ-ONLY (lihat trait ReadOnlyModel di MciBaseModel).
 *  - Koneksi default `dashboard_data` (SQL Server), otomatis ke DB bulan terbaru.
 *  - Kolom dengan spasi/karakter khusus diakses via:
 *    $model->getAttribute('NAMA KOLOM')  atau  $model->{'NAMA KOLOM'}.
 *
 * @property string $kdmchn  type: varchar(15)
 * @property string $nmmchn  type: varchar(50)
 * @property string|null $kdbank  type: varchar(6)
 * @property string|null $nocif  type: varchar(10)
 * @property string|null $nmpemilik  type: varchar(100)
 * @property string|null $alamat  type: varchar(100)
 * @property string|null $kelurahan  type: varchar(50)
 * @property string|null $kecamatan  type: varchar(50)
 * @property string|null $kota  type: varchar(50)
 * @property string|null $kdpos  type: varchar(5)
 * @property string|null $pic  type: varchar(50)
 * @property string|null $nohp  type: varchar(20)
 * @property string|null $notelp  type: varchar(15)
 * @property string|null $email  type: varchar(50)
 * @property string|null $cetakke  type: char(1)
 * @property string|null $IDem  type: varchar(11)
 * @property string|null $norek  type: varchar(20)
 * @property string|null $nmnorek  type: varchar(100)
 * @property string|null $kdaba  type: varchar(6)
 * @property string|null $nmaba  type: varchar(50)
 * @property string|null $nmcab  type: varchar(100)
 * @property string|null $ststrnf  type: char(1)
 * @property string|null $kdloc  type: char(2)
 * @property string|null $kdaoh  type: char(10)
 * @property string|null $stsrec  type: char(1)
 * @property string|null $inpuser  type: varchar(10)
 * @property string|null $inptgljam  type: varchar(14)
 * @property string|null $inpterm  type: varchar(10)
 * @property string|null $chguser  type: varchar(10)
 * @property string|null $chgtgljam  type: varchar(14)
 * @property string|null $chgterm  type: varchar(10)
 * @property string|null $autuser  type: varchar(10)
 * @property string|null $auttgljam  type: varchar(14)
 * @property string|null $autterm  type: varchar(10)
 */
class Tofmchn extends MciBaseModel
{
    /**
     * Nama tabel (case-sensitive di SQL Server).
     */
    protected $table = 'TOFMCHN';

    /**
     * Daftar LENGKAP kolom sesuai database (34 kolom).
     * Model ini read-only, $fillable hanya untuk dokumentasi & IDE helper.
     *
     * @var array<int,string>
     */
    protected $fillable = [
        'kdmchn',
        'nmmchn',
        'kdbank',
        'nocif',
        'nmpemilik',
        'alamat',
        'kelurahan',
        'kecamatan',
        'kota',
        'kdpos',
        'pic',
        'nohp',
        'notelp',
        'email',
        'cetakke',
        'IDem',
        'norek',
        'nmnorek',
        'kdaba',
        'nmaba',
        'nmcab',
        'ststrnf',
        'kdloc',
        'kdaoh',
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
    ];

    /**
     * Casting tipe kolom (non-string saja).
     *
     * @var array<string,string>
     */
    protected $casts = [

    ];
}
