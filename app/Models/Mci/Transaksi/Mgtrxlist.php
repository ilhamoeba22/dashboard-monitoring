<?php

namespace App\Models\Mci\Transaksi;

use App\Models\Mci\MciBaseModel;

/**
 * Model MCI: MGTRXLIST
 * --------------------------------------------------------------------------
 * Domain   : Transaksi
 * Tabel    : [dbo].[MGTRXLIST]
 * Kolom    : 22
 * Sumber   : Mapping_MCI_MAR26_01042026.sql / MCI_MAR26_01042026.xlsx
 *
 * CATATAN:
 *  - Model ini READ-ONLY (lihat trait ReadOnlyModel di MciBaseModel).
 *  - Koneksi default `dashboard_data` (SQL Server), otomatis ke DB bulan terbaru.
 *  - Kolom dengan spasi/karakter khusus diakses via:
 *    $model->getAttribute('NAMA KOLOM')  atau  $model->{'NAMA KOLOM'}.
 *
 * @property string|null $tgltrn  type: varchar(8)
 * @property string|null $jam  type: varchar(6)
 * @property string|null $iddevice  type: varchar(80)
 * @property string|null $notrx  type: varchar(25)
 * @property string|null $noacc  type: varchar(30)
 * @property string|null $noacclawan  type: varchar(30)
 * @property string|null $nominal  type: numeric(9)
 * @property string|null $ket  type: varchar(50)
 * @property string|null $prdid  type: varchar(10)
 * @property string|null $prdname  type: varchar(50)
 * @property string|null $bankid  type: varchar(25)
 * @property string|null $banknama  type: varchar(50)
 * @property string|null $admin  type: numeric(9)
 * @property string|null $total  type: numeric(9)
 * @property string|null $billerreff  type: varchar(100)
 * @property string|null $konten  type: text(16)
 * @property string|null $status  type: varchar(1)
 * @property string|null $kdtrf  type: varchar(20)
 * @property string|null $parm1  type: varchar(255)
 * @property string|null $parm2  type: varchar(255)
 * @property string|null $parm3  type: varchar(255)
 * @property string|null $parm4  type: varchar(255)
 */
class Mgtrxlist extends MciBaseModel
{
    /**
     * Nama tabel (case-sensitive di SQL Server).
     */
    protected $table = 'MGTRXLIST';

    /**
     * Daftar LENGKAP kolom sesuai database (22 kolom).
     * Model ini read-only, $fillable hanya untuk dokumentasi & IDE helper.
     *
     * @var array<int,string>
     */
    protected $fillable = [
        'tgltrn',
        'jam',
        'iddevice',
        'notrx',
        'noacc',
        'noacclawan',
        'nominal',
        'ket',
        'prdid',
        'prdname',
        'bankid',
        'banknama',
        'admin',
        'total',
        'billerreff',
        'konten',
        'status',
        'kdtrf',
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
        'nominal' => 'decimal:2',
        'admin' => 'decimal:2',
        'total' => 'decimal:2',
    ];
}
