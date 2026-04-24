<?php

namespace App\Models\Mci\Lainnya;

use App\Models\Mci\MciBaseModel;

/**
 * Model MCI: dtproperties
 * --------------------------------------------------------------------------
 * Domain   : Lainnya
 * Tabel    : [dbo].[dtproperties]
 * Kolom    : 7
 * Sumber   : Mapping_MCI_MAR26_01042026.sql / MCI_MAR26_01042026.xlsx
 *
 * CATATAN:
 *  - Model ini READ-ONLY (lihat trait ReadOnlyModel di MciBaseModel).
 *  - Koneksi default `dashboard_data` (SQL Server), otomatis ke DB bulan terbaru.
 *  - Kolom dengan spasi/karakter khusus diakses via:
 *    $model->getAttribute('NAMA KOLOM')  atau  $model->{'NAMA KOLOM'}.
 *
 * @property int $id  type: int(4)
 * @property int|null $objectid  type: int(4)
 * @property string $property  type: varchar(64)
 * @property string|null $value  type: varchar(255)
 * @property string|null $uvalue  type: nvarchar(510)
 * @property string|null $lvalue  type: image(16)
 * @property int $version  type: int(4)
 */
class Dtproperties extends MciBaseModel
{
    /**
     * Nama tabel (case-sensitive di SQL Server).
     */
    protected $table = 'dtproperties';

    /**
     * Daftar LENGKAP kolom sesuai database (7 kolom).
     * Model ini read-only, $fillable hanya untuk dokumentasi & IDE helper.
     *
     * @var array<int,string>
     */
    protected $fillable = [
        'id',
        'objectid',
        'property',
        'value',
        'uvalue',
        'lvalue',
        'version',
    ];

    /**
     * Casting tipe kolom (non-string saja).
     *
     * @var array<string,string>
     */
    protected $casts = [
        'id' => 'integer',
        'objectid' => 'integer',
        'version' => 'integer',
    ];
}
