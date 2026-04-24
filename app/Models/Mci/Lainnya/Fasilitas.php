<?php

namespace App\Models\Mci\Lainnya;

use App\Models\Mci\MciBaseModel;

/**
 * Model MCI: FASILITAS
 * --------------------------------------------------------------------------
 * Domain   : Lainnya
 * Tabel    : [dbo].[FASILITAS]
 * Kolom    : 13
 * Sumber   : Mapping_MCI_MAR26_01042026.sql / MCI_MAR26_01042026.xlsx
 *
 * CATATAN:
 *  - Model ini READ-ONLY (lihat trait ReadOnlyModel di MciBaseModel).
 *  - Koneksi default `dashboard_data` (SQL Server), otomatis ke DB bulan terbaru.
 *  - Kolom dengan spasi/karakter khusus diakses via:
 *    $model->getAttribute('NAMA KOLOM')  atau  $model->{'NAMA KOLOM'}.
 *
 * @property string $userid  type: varchar(10)
 * @property string|null $modulx  type: varchar(50)
 * @property string|null $menuutama  type: varchar(30)
 * @property string|null $ok  type: varchar(1)
 * @property string|null $groupmenu  type: varchar(50)
 * @property string|null $subgroup  type: varchar(50)
 * @property string|null $inp  type: varchar(1)
 * @property string|null $ubah  type: varchar(1)
 * @property string|null $auth  type: varchar(1)
 * @property string|null $hapus  type: varchar(1)
 * @property string|null $cetak  type: varchar(1)
 * @property string|null $ISDISPLAY  type: varchar(1)
 * @property string|null $ISFAVORITE  type: varchar(1)
 */
class Fasilitas extends MciBaseModel
{
    /**
     * Nama tabel (case-sensitive di SQL Server).
     */
    protected $table = 'FASILITAS';

    /**
     * Daftar LENGKAP kolom sesuai database (13 kolom).
     * Model ini read-only, $fillable hanya untuk dokumentasi & IDE helper.
     *
     * @var array<int,string>
     */
    protected $fillable = [
        'userid',
        'modulx',
        'menuutama',
        'ok',
        'groupmenu',
        'subgroup',
        'inp',
        'ubah',
        'auth',
        'hapus',
        'cetak',
        'ISDISPLAY',
        'ISFAVORITE',
    ];

    /**
     * Casting tipe kolom (non-string saja).
     *
     * @var array<string,string>
     */
    protected $casts = [

    ];
}
