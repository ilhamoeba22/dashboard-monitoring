<?php

declare(strict_types=1);

namespace App\Models\Mci\UserAuth;

use App\Models\Mci\MciBaseModel;

/**
 * Model MCI: MGSECAUTHCFG
 * --------------------------------------------------------------------------
 * Domain   : User / Auth
 * Tabel    : [dbo].[MGSECAUTHCFG]
 * Kolom    : 15
 * Sumber   : Mapping_MCI_MAR26_01042026.sql / MCI_MAR26_01042026.xlsx
 *
 * CATATAN:
 *  - Model ini READ-ONLY (lihat trait ReadOnlyModel di MciBaseModel).
 *  - Koneksi default `dashboard_data` (SQL Server), otomatis ke DB bulan terbaru.
 *  - Kolom dengan spasi/karakter khusus diakses via:
 *    $model->getAttribute('NAMA KOLOM')  atau  $model->{'NAMA KOLOM'}.
 *
 * @property string $uuid type: varchar(200)
 * @property string $mitraid type: varchar(200)
 * @property string $appid type: varchar(200)
 * @property string $partnerid type: varchar(200)
 * @property string $clientid type: varchar(200)
 * @property string $clientsecret type: varchar(200)
 * @property string $clienthash type: varchar(200)
 * @property string $apikey type: varchar(200)
 * @property string $exptime type: varchar(14)
 * @property string $granttype type: varchar(200)
 * @property string $stsrec type: char(1)
 * @property string $publickeyfile type: varchar(50)
 * @property string $privatekeyfile type: varchar(50)
 * @property string $servicefilter type: varchar(-1)
 * @property string $regid type: varchar(-1)
 */
class Mgsecauthcfg extends MciBaseModel
{
    /**
     * Nama tabel (case-sensitive di SQL Server).
     */
    protected $table = 'MGSECAUTHCFG';

    /**
     * Daftar LENGKAP kolom sesuai database (15 kolom).
     * Model ini read-only, $fillable hanya untuk dokumentasi & IDE helper.
     *
     * @var array<int,string>
     */
    protected $fillable = [
        'uuid',
        'mitraid',
        'appid',
        'partnerid',
        'clientid',
        'clientsecret',
        'clienthash',
        'apikey',
        'exptime',
        'granttype',
        'stsrec',
        'publickeyfile',
        'privatekeyfile',
        'servicefilter',
        'regid',
    ];

    /**
     * Casting tipe kolom (non-string saja).
     *
     * @var array<string,string>
     */
    protected $casts = [

    ];
}
