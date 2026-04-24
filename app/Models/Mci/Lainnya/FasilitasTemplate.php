<?php

namespace App\Models\Mci\Lainnya;

use App\Models\Mci\MciBaseModel;

/**
 * Model MCI: FASILITAS_TEMPLATE
 * --------------------------------------------------------------------------
 * Domain   : Lainnya
 * Tabel    : [dbo].[FASILITAS_TEMPLATE]
 * Kolom    : 11
 * Sumber   : Mapping_MCI_MAR26_01042026.sql / MCI_MAR26_01042026.xlsx
 *
 * CATATAN:
 *  - Model ini READ-ONLY (lihat trait ReadOnlyModel di MciBaseModel).
 *  - Koneksi default `dashboard_data` (SQL Server), otomatis ke DB bulan terbaru.
 *  - Kolom dengan spasi/karakter khusus diakses via:
 *    $model->getAttribute('NAMA KOLOM')  atau  $model->{'NAMA KOLOM'}.
 *
 * @property string $templateid  type: varchar(25)
 * @property string $modulx  type: varchar(50)
 * @property string $menuutama  type: varchar(30)
 * @property string|null $ok  type: varchar(1)
 * @property string|null $groupmenu  type: varchar(50)
 * @property string $subgroup  type: varchar(50)
 * @property string|null $inp  type: varchar(1)
 * @property string|null $ubah  type: varchar(1)
 * @property string|null $auth  type: varchar(1)
 * @property string|null $hapus  type: varchar(1)
 * @property string|null $cetak  type: varchar(1)
 */
class FasilitasTemplate extends MciBaseModel
{
    /**
     * Nama tabel (case-sensitive di SQL Server).
     */
    protected $table = 'FASILITAS_TEMPLATE';

    /**
     * Daftar LENGKAP kolom sesuai database (11 kolom).
     * Model ini read-only, $fillable hanya untuk dokumentasi & IDE helper.
     *
     * @var array<int,string>
     */
    protected $fillable = [
        'templateid',
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
    ];

    /**
     * Casting tipe kolom (non-string saja).
     *
     * @var array<string,string>
     */
    protected $casts = [

    ];
}
