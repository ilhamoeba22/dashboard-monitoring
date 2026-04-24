<?php

namespace App\Models\Mci\Sms;

use App\Models\Mci\MciBaseModel;

/**
 * Model MCI: MGNTFCTR
 * --------------------------------------------------------------------------
 * Domain   : SMS / Notif
 * Tabel    : [dbo].[MGNTFCTR]
 * Kolom    : 11
 * Sumber   : Mapping_MCI_MAR26_01042026.sql / MCI_MAR26_01042026.xlsx
 *
 * CATATAN:
 *  - Model ini READ-ONLY (lihat trait ReadOnlyModel di MciBaseModel).
 *  - Koneksi default `dashboard_data` (SQL Server), otomatis ke DB bulan terbaru.
 *  - Kolom dengan spasi/karakter khusus diakses via:
 *    $model->getAttribute('NAMA KOLOM')  atau  $model->{'NAMA KOLOM'}.
 *
 * @property int $id  type: bigint(8)
 * @property string $kdntf  type: varchar(20)
 * @property string $nocif  type: varchar(50)
 * @property string $sentconfig  type: varchar(20)
 * @property string $title  type: varchar(30)
 * @property string $msg  type: varchar(-1)
 * @property string $appid  type: varchar(10)
 * @property string $sentdate  type: varchar(8)
 * @property string $reqtime  type: varchar(14)
 * @property string $stssent  type: char(1)
 * @property string $stsmsg  type: varchar(-1)
 */
class Mgntfctr extends MciBaseModel
{
    /**
     * Nama tabel (case-sensitive di SQL Server).
     */
    protected $table = 'MGNTFCTR';

    /**
     * Daftar LENGKAP kolom sesuai database (11 kolom).
     * Model ini read-only, $fillable hanya untuk dokumentasi & IDE helper.
     *
     * @var array<int,string>
     */
    protected $fillable = [
        'id',
        'kdntf',
        'nocif',
        'sentconfig',
        'title',
        'msg',
        'appid',
        'sentdate',
        'reqtime',
        'stssent',
        'stsmsg',
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
