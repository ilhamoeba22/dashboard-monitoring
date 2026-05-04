<?php

declare(strict_types=1);

namespace App\Models\Mci\UserAuth;

use App\Models\Mci\MciBaseModel;

/**
 * Model MCI: USERSESSION
 * --------------------------------------------------------------------------
 * Domain   : User / Auth
 * Tabel    : [dbo].[USERSESSION]
 * Kolom    : 3
 * Sumber   : Mapping_MCI_MAR26_01042026.sql / MCI_MAR26_01042026.xlsx
 *
 * CATATAN:
 *  - Model ini READ-ONLY (lihat trait ReadOnlyModel di MciBaseModel).
 *  - Koneksi default `dashboard_data` (SQL Server), otomatis ke DB bulan terbaru.
 *  - Kolom dengan spasi/karakter khusus diakses via:
 *    $model->getAttribute('NAMA KOLOM')  atau  $model->{'NAMA KOLOM'}.
 *
 * @property string|null $userid type: varchar(255)
 * @property string|null $sessionid type: text(16)
 * @property string|null $appid type: varchar(10)
 */
class Usersession extends MciBaseModel
{
    /**
     * Nama tabel (case-sensitive di SQL Server).
     */
    protected $table = 'USERSESSION';

    /**
     * Daftar LENGKAP kolom sesuai database (3 kolom).
     * Model ini read-only, $fillable hanya untuk dokumentasi & IDE helper.
     *
     * @var array<int,string>
     */
    protected $fillable = [
        'userid',
        'sessionid',
        'appid',
    ];

    /**
     * Casting tipe kolom (non-string saja).
     *
     * @var array<string,string>
     */
    protected $casts = [

    ];
}
