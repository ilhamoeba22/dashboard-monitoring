<?php

namespace App\Models\Mci\Channel;

use App\Models\Mci\MciBaseModel;

/**
 * Model MCI: TMPEDCMASTLx
 * --------------------------------------------------------------------------
 * Domain   : Channel / ATM / Card
 * Tabel    : [dbo].[TMPEDCMASTLx]
 * Kolom    : 11
 * Sumber   : Mapping_MCI_MAR26_01042026.sql / MCI_MAR26_01042026.xlsx
 *
 * CATATAN:
 *  - Model ini READ-ONLY (lihat trait ReadOnlyModel di MciBaseModel).
 *  - Koneksi default `dashboard_data` (SQL Server), otomatis ke DB bulan terbaru.
 *  - Kolom dengan spasi/karakter khusus diakses via:
 *    $model->getAttribute('NAMA KOLOM')  atau  $model->{'NAMA KOLOM'}.
 *
 * @property string $noacc  type: varchar(10)
 * @property string|null $nocif  type: varchar(9)
 * @property string $nama  type: varchar(30)
 * @property string|null $alamat  type: varchar(100)
 * @property string|null $kdkolektor  type: varchar(10)
 * @property string|null $kdmitra  type: varchar(10)
 * @property string|null $sahirtab  type: numeric(9)
 * @property string|null $sahirloan  type: numeric(9)
 * @property string|null $tid  type: varchar(8)
 * @property string|null $downloaded  type: varchar(1)
 * @property string|null $kdloc  type: varchar(2)
 */
class Tmpedcmastlx extends MciBaseModel
{
    /**
     * Nama tabel (case-sensitive di SQL Server).
     */
    protected $table = 'TMPEDCMASTLx';

    /**
     * Daftar LENGKAP kolom sesuai database (11 kolom).
     * Model ini read-only, $fillable hanya untuk dokumentasi & IDE helper.
     *
     * @var array<int,string>
     */
    protected $fillable = [
        'noacc',
        'nocif',
        'nama',
        'alamat',
        'kdkolektor',
        'kdmitra',
        'sahirtab',
        'sahirloan',
        'tid',
        'downloaded',
        'kdloc',
    ];

    /**
     * Casting tipe kolom (non-string saja).
     *
     * @var array<string,string>
     */
    protected $casts = [
        'sahirtab' => 'decimal:2',
        'sahirloan' => 'decimal:2',
    ];
}
