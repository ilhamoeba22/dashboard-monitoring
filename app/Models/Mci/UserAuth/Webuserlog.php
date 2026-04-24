<?php

namespace App\Models\Mci\UserAuth;

use App\Models\Mci\MciBaseModel;

/**
 * Model MCI: WEBUSERLOG
 * --------------------------------------------------------------------------
 * Domain   : User / Auth
 * Tabel    : [dbo].[WEBUSERLOG]
 * Kolom    : 13
 * Sumber   : Mapping_MCI_MAR26_01042026.sql / MCI_MAR26_01042026.xlsx
 *
 * CATATAN:
 *  - Model ini READ-ONLY (lihat trait ReadOnlyModel di MciBaseModel).
 *  - Koneksi default `dashboard_data` (SQL Server), otomatis ke DB bulan terbaru.
 *  - Kolom dengan spasi/karakter khusus diakses via:
 *    $model->getAttribute('NAMA KOLOM')  atau  $model->{'NAMA KOLOM'}.
 *
 * @property int $id  type: bigint(8)
 * @property string $userid  type: varchar(20)
 * @property string $kdid  type: varchar(20)
 * @property string $traceid  type: varchar(20)
 * @property string $appid  type: varchar(20)
 * @property string $inptgljam  type: varchar(20)
 * @property string|null $web_version  type: varchar(20)
 * @property string|null $server_version  type: varchar(20)
 * @property string|null $ip_address  type: varchar(50)
 * @property string|null $lokasi  type: varchar(255)
 * @property string|null $rc  type: varchar(20)
 * @property string|null $rcdesc  type: varchar(255)
 * @property string|null $description  type: varchar(-1)
 */
class Webuserlog extends MciBaseModel
{
    /**
     * Nama tabel (case-sensitive di SQL Server).
     */
    protected $table = 'WEBUSERLOG';

    /**
     * Daftar LENGKAP kolom sesuai database (13 kolom).
     * Model ini read-only, $fillable hanya untuk dokumentasi & IDE helper.
     *
     * @var array<int,string>
     */
    protected $fillable = [
        'id',
        'userid',
        'kdid',
        'traceid',
        'appid',
        'inptgljam',
        'web_version',
        'server_version',
        'ip_address',
        'lokasi',
        'rc',
        'rcdesc',
        'description',
    ];

    /**
     * Casting tipe kolom (non-string saja).
     *
     * @var array<string,string>
     */
    protected $casts = [
        'id' => 'integer',
    ];
}
