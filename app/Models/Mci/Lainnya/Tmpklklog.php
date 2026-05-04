<?php

declare(strict_types=1);

namespace App\Models\Mci\Lainnya;

use App\Models\Mci\MciBaseModel;

/**
 * Model MCI: TMPKLKLOG
 * --------------------------------------------------------------------------
 * Domain   : Lainnya
 * Tabel    : [dbo].[TMPKLKLOG]
 * Kolom    : 8
 * Sumber   : Mapping_MCI_MAR26_01042026.sql / MCI_MAR26_01042026.xlsx
 *
 * CATATAN:
 *  - Model ini READ-ONLY (lihat trait ReadOnlyModel di MciBaseModel).
 *  - Koneksi default `dashboard_data` (SQL Server), otomatis ke DB bulan terbaru.
 *  - Kolom dengan spasi/karakter khusus diakses via:
 *    $model->getAttribute('NAMA KOLOM')  atau  $model->{'NAMA KOLOM'}.
 *
 * @property int $id type: bigint(8)
 * @property string $idlog type: varchar(100)
 * @property string $tgljamsync type: varchar(14)
 * @property string $kdao type: varchar(50)
 * @property string $deviceid type: varchar(100)
 * @property string $tgl type: varchar(14)
 * @property string $tgljam type: varchar(14)
 * @property string $ket type: varchar(255)
 */
class Tmpklklog extends MciBaseModel
{
    /**
     * Nama tabel (case-sensitive di SQL Server).
     */
    protected $table = 'TMPKLKLOG';

    /**
     * Daftar LENGKAP kolom sesuai database (8 kolom).
     * Model ini read-only, $fillable hanya untuk dokumentasi & IDE helper.
     *
     * @var array<int,string>
     */
    protected $fillable = [
        'id',
        'idlog',
        'tgljamsync',
        'kdao',
        'deviceid',
        'tgl',
        'tgljam',
        'ket',
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
