<?php

declare(strict_types=1);

namespace App\Models\Mci\Lainnya;

use App\Models\Mci\MciBaseModel;

/**
 * Model MCI: TMPKLKMAS
 * --------------------------------------------------------------------------
 * Domain   : Lainnya
 * Tabel    : [dbo].[TMPKLKMAS]
 * Kolom    : 13
 * Sumber   : Mapping_MCI_MAR26_01042026.sql / MCI_MAR26_01042026.xlsx
 *
 * CATATAN:
 *  - Model ini READ-ONLY (lihat trait ReadOnlyModel di MciBaseModel).
 *  - Koneksi default `dashboard_data` (SQL Server), otomatis ke DB bulan terbaru.
 *  - Kolom dengan spasi/karakter khusus diakses via:
 *    $model->getAttribute('NAMA KOLOM')  atau  $model->{'NAMA KOLOM'}.
 *
 * @property string|null $kdkolektor type: nvarchar(20)
 * @property string|null $deviceid type: nvarchar(100)
 * @property string|null $devicereg type: nvarchar(510)
 * @property string|null $nama type: nvarchar(100)
 * @property string|null $pass type: nvarchar(20)
 * @property string|null $head1 type: nvarchar(60)
 * @property string|null $head2 type: nvarchar(60)
 * @property string|null $foot1 type: nvarchar(60)
 * @property string|null $foot2 type: nvarchar(60)
 * @property string $param1 type: varchar(10)
 * @property string $param2 type: varchar(10)
 * @property string $param3 type: varchar(10)
 * @property string $param4 type: varchar(10)
 */
class Tmpklkmas extends MciBaseModel
{
    /**
     * Nama tabel (case-sensitive di SQL Server).
     */
    protected $table = 'TMPKLKMAS';

    /**
     * Daftar LENGKAP kolom sesuai database (13 kolom).
     * Model ini read-only, $fillable hanya untuk dokumentasi & IDE helper.
     *
     * @var array<int,string>
     */
    protected $fillable = [
        'kdkolektor',
        'deviceid',
        'devicereg',
        'nama',
        'pass',
        'head1',
        'head2',
        'foot1',
        'foot2',
        'param1',
        'param2',
        'param3',
        'param4',
    ];

    /**
     * Casting tipe kolom (non-string saja).
     *
     * @var array<string,string>
     */
    protected $casts = [

    ];
}
