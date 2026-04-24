<?php

namespace App\Models\Mci\Lainnya;

use App\Models\Mci\MciBaseModel;

/**
 * Model MCI: datcapil
 * --------------------------------------------------------------------------
 * Domain   : Lainnya
 * Tabel    : [dbo].[datcapil]
 * Kolom    : 21
 * Sumber   : Mapping_MCI_MAR26_01042026.sql / MCI_MAR26_01042026.xlsx
 *
 * CATATAN:
 *  - Model ini READ-ONLY (lihat trait ReadOnlyModel di MciBaseModel).
 *  - Koneksi default `dashboard_data` (SQL Server), otomatis ke DB bulan terbaru.
 *  - Kolom dengan spasi/karakter khusus diakses via:
 *    $model->getAttribute('NAMA KOLOM')  atau  $model->{'NAMA KOLOM'}.
 *
 * @property string|null $nik  type: varchar(20)
 * @property string|null $kk  type: varchar(20)
 * @property string|null $nm  type: varchar(50)
 * @property string|null $agama  type: varchar(10)
 * @property string|null $jk  type: varchar(10)
 * @property string|null $tmplhr  type: varchar(20)
 * @property string|null $tgllhr  type: varchar(14)
 * @property string|null $status  type: varchar(10)
 * @property string|null $nmibu  type: varchar(30)
 * @property string|null $jnspkrjn  type: varchar(15)
 * @property string|null $alamat  type: varchar(30)
 * @property string|null $rt  type: varchar(3)
 * @property string|null $rw  type: varchar(3)
 * @property string|null $prov  type: numeric(5)
 * @property string|null $kab  type: numeric(5)
 * @property string|null $kec  type: numeric(5)
 * @property string|null $kel  type: numeric(5)
 * @property string|null $nmprov  type: varchar(30)
 * @property string|null $nmkab  type: varchar(30)
 * @property string|null $nmkel  type: varchar(30)
 * @property string|null $nmkec  type: varchar(30)
 */
class Datcapil extends MciBaseModel
{
    /**
     * Nama tabel (case-sensitive di SQL Server).
     */
    protected $table = 'datcapil';

    /**
     * Daftar LENGKAP kolom sesuai database (21 kolom).
     * Model ini read-only, $fillable hanya untuk dokumentasi & IDE helper.
     *
     * @var array<int,string>
     */
    protected $fillable = [
        'nik',
        'kk',
        'nm',
        'agama',
        'jk',
        'tmplhr',
        'tgllhr',
        'status',
        'nmibu',
        'jnspkrjn',
        'alamat',
        'rt',
        'rw',
        'prov',
        'kab',
        'kec',
        'kel',
        'nmprov',
        'nmkab',
        'nmkel',
        'nmkec',
    ];

    /**
     * Casting tipe kolom (non-string saja).
     *
     * @var array<string,string>
     */
    protected $casts = [
        'prov' => 'decimal:2',
        'kab' => 'decimal:2',
        'kec' => 'decimal:2',
        'kel' => 'decimal:2',
    ];
}
