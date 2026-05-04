<?php

declare(strict_types=1);

namespace App\Models\Mci\UserAuth;

use App\Models\Mci\MciBaseModel;

/**
 * Model MCI: USERPROFILE2
 * --------------------------------------------------------------------------
 * Domain   : User / Auth
 * Tabel    : [dbo].[USERPROFILE2]
 * Kolom    : 16
 * Sumber   : Mapping_MCI_MAR26_01042026.sql / MCI_MAR26_01042026.xlsx
 *
 * CATATAN:
 *  - Model ini READ-ONLY (lihat trait ReadOnlyModel di MciBaseModel).
 *  - Koneksi default `dashboard_data` (SQL Server), otomatis ke DB bulan terbaru.
 *  - Kolom dengan spasi/karakter khusus diakses via:
 *    $model->getAttribute('NAMA KOLOM')  atau  $model->{'NAMA KOLOM'}.
 *
 * @property string|null $useriba type: varchar(10)
 * @property string|null $imei type: varchar(20)
 * @property string|null $userid type: varchar(20)
 * @property string|null $password type: varchar(255)
 * @property string|null $regimei type: varchar(255)
 * @property string|null $stssent type: char(1)
 * @property string|null $stsrec type: char(1)
 * @property string|null $inpuser type: varchar(10)
 * @property string|null $inptgljam type: varchar(14)
 * @property string|null $inpterm type: varchar(10)
 * @property string|null $chguser type: varchar(10)
 * @property string|null $chgtgljam type: varchar(14)
 * @property string|null $chgterm type: varchar(10)
 * @property string|null $autuser type: varchar(10)
 * @property string|null $auttgljam type: varchar(14)
 * @property string|null $autterm type: varchar(10)
 */
class Userprofile2 extends MciBaseModel
{
    /**
     * Nama tabel (case-sensitive di SQL Server).
     */
    protected $table = 'USERPROFILE2';

    /**
     * Daftar LENGKAP kolom sesuai database (16 kolom).
     * Model ini read-only, $fillable hanya untuk dokumentasi & IDE helper.
     *
     * @var array<int,string>
     */
    protected $fillable = [
        'useriba',
        'imei',
        'userid',
        'password',
        'regimei',
        'stssent',
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
