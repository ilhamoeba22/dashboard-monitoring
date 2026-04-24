<?php

namespace App\Models\Mci\Lainnya;

use App\Models\Mci\MciBaseModel;

/**
 * Model MCI: WEBFASILITAS
 * --------------------------------------------------------------------------
 * Domain   : Lainnya
 * Tabel    : [dbo].[WEBFASILITAS]
 * Kolom    : 11
 * Sumber   : Mapping_MCI_MAR26_01042026.sql / MCI_MAR26_01042026.xlsx
 *
 * CATATAN:
 *  - Model ini READ-ONLY (lihat trait ReadOnlyModel di MciBaseModel).
 *  - Koneksi default `dashboard_data` (SQL Server), otomatis ke DB bulan terbaru.
 *  - Kolom dengan spasi/karakter khusus diakses via:
 *    $model->getAttribute('NAMA KOLOM')  atau  $model->{'NAMA KOLOM'}.
 *
 * @property string $kdfasilitas  type: varchar(100)
 * @property string|null $nmfasilitas  type: varchar(255)
 * @property string|null $kdparent  type: varchar(100)
 * @property string|null $icon  type: varchar(50)
 * @property int|null $urut  type: int(4)
 * @property string|null $hide_sidemenu  type: varchar(1)
 * @property string|null $tipe  type: varchar(20)
 * @property string|null $path_services  type: varchar(-1)
 * @property string|null $is_public  type: varchar(1)
 * @property string|null $custom_url  type: varchar(100)
 * @property string|null $appid  type: varchar(6)
 */
class Webfasilitas extends MciBaseModel
{
    /**
     * Nama tabel (case-sensitive di SQL Server).
     */
    protected $table = 'WEBFASILITAS';

    /**
     * Daftar LENGKAP kolom sesuai database (11 kolom).
     * Model ini read-only, $fillable hanya untuk dokumentasi & IDE helper.
     *
     * @var array<int,string>
     */
    protected $fillable = [
        'kdfasilitas',
        'nmfasilitas',
        'kdparent',
        'icon',
        'urut',
        'hide_sidemenu',
        'tipe',
        'path_services',
        'is_public',
        'custom_url',
        'appid',
    ];

    /**
     * Casting tipe kolom (non-string saja).
     *
     * @var array<string,string>
     */
    protected $casts = [
        'urut' => 'integer',
    ];
}
