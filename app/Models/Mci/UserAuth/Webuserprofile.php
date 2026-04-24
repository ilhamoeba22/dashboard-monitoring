<?php

namespace App\Models\Mci\UserAuth;

use App\Models\Mci\MciBaseModel;

/**
 * Model MCI: WEBUSERPROFILE
 * --------------------------------------------------------------------------
 * Domain   : User / Auth
 * Tabel    : [dbo].[WEBUSERPROFILE]
 * Kolom    : 15
 * Sumber   : Mapping_MCI_MAR26_01042026.sql / MCI_MAR26_01042026.xlsx
 *
 * CATATAN:
 *  - Model ini READ-ONLY (lihat trait ReadOnlyModel di MciBaseModel).
 *  - Koneksi default `dashboard_data` (SQL Server), otomatis ke DB bulan terbaru.
 *  - Kolom dengan spasi/karakter khusus diakses via:
 *    $model->getAttribute('NAMA KOLOM')  atau  $model->{'NAMA KOLOM'}.
 *
 * @property string $userid  type: varchar(100)
 * @property string $nama  type: varchar(100)
 * @property string $pass  type: varchar(255)
 * @property string|null $kdgroupfasilitas  type: varchar(50)
 * @property string|null $batch  type: numeric(5)
 * @property string|null $twofatoken  type: varchar(-1)
 * @property string|null $ststwofa  type: varchar(3)
 * @property string $stspass  type: varchar(3)
 * @property string $inptgljam  type: varchar(15)
 * @property string $inpuser  type: varchar(100)
 * @property string|null $chgtgljam  type: varchar(15)
 * @property string|null $chguser  type: varchar(100)
 * @property string $stsrec  type: varchar(3)
 * @property string|null $levelx  type: varchar(5)
 * @property string|null $devterm  type: varchar(10)
 */
class Webuserprofile extends MciBaseModel
{
    /**
     * Nama tabel (case-sensitive di SQL Server).
     */
    protected $table = 'WEBUSERPROFILE';

    /**
     * Daftar LENGKAP kolom sesuai database (15 kolom).
     * Model ini read-only, $fillable hanya untuk dokumentasi & IDE helper.
     *
     * @var array<int,string>
     */
    protected $fillable = [
        'userid',
        'nama',
        'pass',
        'kdgroupfasilitas',
        'batch',
        'twofatoken',
        'ststwofa',
        'stspass',
        'inptgljam',
        'inpuser',
        'chgtgljam',
        'chguser',
        'stsrec',
        'levelx',
        'devterm',
    ];

    /**
     * Casting tipe kolom (non-string saja).
     *
     * @var array<string,string>
     */
    protected $casts = [
        'batch' => 'decimal:2',
    ];
}
