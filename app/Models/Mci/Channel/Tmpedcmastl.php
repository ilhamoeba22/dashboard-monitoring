<?php

namespace App\Models\Mci\Channel;

use App\Models\Mci\MciBaseModel;

/**
 * Model MCI: TMPEDCMASTL
 * --------------------------------------------------------------------------
 * Domain   : Channel / ATM / Card
 * Tabel    : [dbo].[TMPEDCMASTL]
 * Kolom    : 18
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
 * @property string $kdkolektor  type: varchar(10)
 * @property string|null $kdmitra  type: varchar(10)
 * @property string|null $sahirtab  type: numeric(9)
 * @property string|null $sahirloan  type: numeric(9)
 * @property string|null $tid  type: varchar(8)
 * @property string|null $downloaded  type: varchar(1)
 * @property string|null $kdloc  type: varchar(2)
 * @property string|null $minsetor  type: numeric(9)
 * @property string|null $maxtarik  type: numeric(9)
 * @property string $kdgroupdeb  type: varchar(10)
 * @property string $parm1  type: varchar(100)
 * @property string $parm2  type: varchar(100)
 * @property string $parm3  type: varchar(100)
 * @property string $parm4  type: varchar(100)
 */
class Tmpedcmastl extends MciBaseModel
{
    /**
     * Nama tabel (case-sensitive di SQL Server).
     */
    protected $table = 'TMPEDCMASTL';

    /**
     * Daftar LENGKAP kolom sesuai database (18 kolom).
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
        'minsetor',
        'maxtarik',
        'kdgroupdeb',
        'parm1',
        'parm2',
        'parm3',
        'parm4',
    ];

    /**
     * Casting tipe kolom (non-string saja).
     *
     * @var array<string,string>
     */
    protected $casts = [
        'sahirtab' => 'decimal:2',
        'sahirloan' => 'decimal:2',
        'minsetor' => 'decimal:2',
        'maxtarik' => 'decimal:2',
    ];
}
