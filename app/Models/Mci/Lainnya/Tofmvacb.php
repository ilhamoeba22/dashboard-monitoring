<?php

namespace App\Models\Mci\Lainnya;

use App\Models\Mci\MciBaseModel;

/**
 * Model MCI: TOFMVACB
 * --------------------------------------------------------------------------
 * Domain   : Lainnya
 * Tabel    : [dbo].[TOFMVACB]
 * Kolom    : 24
 * Sumber   : Mapping_MCI_MAR26_01042026.sql / MCI_MAR26_01042026.xlsx
 *
 * CATATAN:
 *  - Model ini READ-ONLY (lihat trait ReadOnlyModel di MciBaseModel).
 *  - Koneksi default `dashboard_data` (SQL Server), otomatis ke DB bulan terbaru.
 *  - Kolom dengan spasi/karakter khusus diakses via:
 *    $model->getAttribute('NAMA KOLOM')  atau  $model->{'NAMA KOLOM'}.
 *
 * @property string|null $kdlembaga  type: varchar(10)
 * @property string|null $noacva  type: varchar(20)
 * @property string|null $nama  type: varchar(50)
 * @property string|null $alamat  type: varchar(100)
 * @property string|null $kelurahan  type: varchar(40)
 * @property string|null $kecamatan  type: varchar(40)
 * @property string|null $kota  type: varchar(40)
 * @property string|null $kdpos  type: varchar(5)
 * @property string|null $sawalrp  type: numeric(9)
 * @property string|null $mutasidr  type: numeric(9)
 * @property string|null $mutasicr  type: numeric(9)
 * @property string|null $sahirrp  type: numeric(9)
 * @property string|null $stsacc  type: varchar(1)
 * @property string|null $stsrec  type: varchar(1)
 * @property string|null $inpuser  type: varchar(10)
 * @property string|null $inptgljam  type: varchar(14)
 * @property string|null $inpterm  type: varchar(10)
 * @property string|null $chguser  type: varchar(10)
 * @property string|null $chgtgljam  type: varchar(14)
 * @property string|null $chgterm  type: varchar(10)
 * @property string|null $autuser  type: varchar(10)
 * @property string|null $auttgljam  type: varchar(14)
 * @property string|null $autterm  type: varchar(10)
 * @property string|null $tgltutup  type: varchar(8)
 */
class Tofmvacb extends MciBaseModel
{
    /**
     * Nama tabel (case-sensitive di SQL Server).
     */
    protected $table = 'TOFMVACB';

    /**
     * Daftar LENGKAP kolom sesuai database (24 kolom).
     * Model ini read-only, $fillable hanya untuk dokumentasi & IDE helper.
     *
     * @var array<int,string>
     */
    protected $fillable = [
        'kdlembaga',
        'noacva',
        'nama',
        'alamat',
        'kelurahan',
        'kecamatan',
        'kota',
        'kdpos',
        'sawalrp',
        'mutasidr',
        'mutasicr',
        'sahirrp',
        'stsacc',
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
        'tgltutup',
    ];

    /**
     * Casting tipe kolom (non-string saja).
     *
     * @var array<string,string>
     */
    protected $casts = [
        'sawalrp' => 'decimal:2',
        'mutasidr' => 'decimal:2',
        'mutasicr' => 'decimal:2',
        'sahirrp' => 'decimal:2',
    ];
}
