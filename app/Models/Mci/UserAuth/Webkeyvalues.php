<?php

declare(strict_types=1);

namespace App\Models\Mci\UserAuth;

use App\Models\Mci\MciBaseModel;

/**
 * Model MCI: WEBKEYVALUES
 * --------------------------------------------------------------------------
 * Domain   : User / Auth
 * Tabel    : [dbo].[WEBKEYVALUES]
 * Kolom    : 6
 * Sumber   : Mapping_MCI_MAR26_01042026.sql / MCI_MAR26_01042026.xlsx
 *
 * CATATAN:
 *  - Model ini READ-ONLY (lihat trait ReadOnlyModel di MciBaseModel).
 *  - Koneksi default `dashboard_data` (SQL Server), otomatis ke DB bulan terbaru.
 *  - Kolom dengan spasi/karakter khusus diakses via:
 *    $model->getAttribute('NAMA KOLOM')  atau  $model->{'NAMA KOLOM'}.
 *
 * @property int $id type: int(4)
 * @property string $groupid type: varchar(50)
 * @property string $keyid type: varchar(50)
 * @property string $value type: varchar(200)
 * @property string|null $keterangan type: varchar(200)
 * @property string|null $stsrec type: varchar(1)
 */
class Webkeyvalues extends MciBaseModel
{
    /**
     * Nama tabel (case-sensitive di SQL Server).
     */
    protected $table = 'WEBKEYVALUES';

    /**
     * Daftar LENGKAP kolom sesuai database (6 kolom).
     * Model ini read-only, $fillable hanya untuk dokumentasi & IDE helper.
     *
     * @var array<int,string>
     */
    protected $fillable = [
        'id',
        'groupid',
        'keyid',
        'value',
        'keterangan',
        'stsrec',
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
