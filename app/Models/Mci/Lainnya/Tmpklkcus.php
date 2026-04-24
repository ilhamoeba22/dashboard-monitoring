<?php

namespace App\Models\Mci\Lainnya;

use App\Models\Mci\MciBaseModel;

/**
 * Model MCI: TMPKLKCUS
 * --------------------------------------------------------------------------
 * Domain   : Lainnya
 * Tabel    : [dbo].[TMPKLKCUS]
 * Kolom    : 9
 * Sumber   : Mapping_MCI_MAR26_01042026.sql / MCI_MAR26_01042026.xlsx
 *
 * CATATAN:
 *  - Model ini READ-ONLY (lihat trait ReadOnlyModel di MciBaseModel).
 *  - Koneksi default `dashboard_data` (SQL Server), otomatis ke DB bulan terbaru.
 *  - Kolom dengan spasi/karakter khusus diakses via:
 *    $model->getAttribute('NAMA KOLOM')  atau  $model->{'NAMA KOLOM'}.
 *
 * @property string $idcus  type: varchar(30)
 * @property string $trxid  type: varchar(30)
 * @property string $ket  type: varchar(50)
 * @property string $label  type: varchar(20)
 * @property string $additional_info  type: varchar(30)
 * @property string $parm1  type: varchar(30)
 * @property string $parm2  type: varchar(30)
 * @property string $parm3  type: varchar(30)
 * @property string $parm4  type: varchar(30)
 */
class Tmpklkcus extends MciBaseModel
{
    /**
     * Nama tabel (case-sensitive di SQL Server).
     */
    protected $table = 'TMPKLKCUS';

    /**
     * Daftar LENGKAP kolom sesuai database (9 kolom).
     * Model ini read-only, $fillable hanya untuk dokumentasi & IDE helper.
     *
     * @var array<int,string>
     */
    protected $fillable = [
        'idcus',
        'trxid',
        'ket',
        'label',
        'additional_info',
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

    ];
}
