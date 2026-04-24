<?php

namespace App\Models\Mci\Lainnya;

use App\Models\Mci\MciBaseModel;

/**
 * Model MCI: TOFTRRN
 * --------------------------------------------------------------------------
 * Domain   : Lainnya
 * Tabel    : [dbo].[TOFTRRN]
 * Kolom    : 10
 * Sumber   : Mapping_MCI_MAR26_01042026.sql / MCI_MAR26_01042026.xlsx
 *
 * CATATAN:
 *  - Model ini READ-ONLY (lihat trait ReadOnlyModel di MciBaseModel).
 *  - Koneksi default `dashboard_data` (SQL Server), otomatis ke DB bulan terbaru.
 *  - Kolom dengan spasi/karakter khusus diakses via:
 *    $model->getAttribute('NAMA KOLOM')  atau  $model->{'NAMA KOLOM'}.
 *
 * @property string $tgltrn  type: varchar(8)
 * @property string $batch  type: numeric(5)
 * @property string $notrn  type: numeric(5)
 * @property string $external_id  type: varchar(256)
 * @property string $rrn  type: varchar(256)
 * @property string $channelid  type: varchar(100)
 * @property string $parm1  type: varchar(256)
 * @property string $parm2  type: varchar(256)
 * @property string $parm3  type: varchar(256)
 * @property string $parm4  type: varchar(256)
 */
class Toftrrn extends MciBaseModel
{
    /**
     * Nama tabel (case-sensitive di SQL Server).
     */
    protected $table = 'TOFTRRN';

    /**
     * Daftar LENGKAP kolom sesuai database (10 kolom).
     * Model ini read-only, $fillable hanya untuk dokumentasi & IDE helper.
     *
     * @var array<int,string>
     */
    protected $fillable = [
        'tgltrn',
        'batch',
        'notrn',
        'external_id',
        'rrn',
        'channelid',
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
        'batch' => 'decimal:2',
        'notrn' => 'decimal:2',
    ];
}
