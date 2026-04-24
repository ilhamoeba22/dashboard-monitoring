<?php

namespace App\Models\Mci\Transaksi;

use App\Models\Mci\MciBaseModel;

/**
 * Model MCI: TMPEDCTRNC
 * --------------------------------------------------------------------------
 * Domain   : Transaksi
 * Tabel    : [dbo].[TMPEDCTRNC]
 * Kolom    : 16
 * Sumber   : Mapping_MCI_MAR26_01042026.sql / MCI_MAR26_01042026.xlsx
 *
 * CATATAN:
 *  - Model ini READ-ONLY (lihat trait ReadOnlyModel di MciBaseModel).
 *  - Koneksi default `dashboard_data` (SQL Server), otomatis ke DB bulan terbaru.
 *  - Kolom dengan spasi/karakter khusus diakses via:
 *    $model->getAttribute('NAMA KOLOM')  atau  $model->{'NAMA KOLOM'}.
 *
 * @property string $noreff  type: numeric(9)
 * @property string $noacc  type: varchar(10)
 * @property string|null $kdedc  type: varchar(30)
 * @property string|null $kdkolektor  type: varchar(10)
 * @property string $tgltrn  type: varchar(8)
 * @property string $amount  type: numeric(9)
 * @property string $ststrn  type: varchar(1)
 * @property string|null $dc  type: nvarchar(2)
 * @property string|null $inpuser  type: nvarchar(20)
 * @property string|null $noreffva  type: varchar(100)
 * @property string|null $pokok  type: numeric(9)
 * @property string|null $margin  type: numeric(9)
 * @property string $parm1  type: varchar(100)
 * @property string $parm2  type: varchar(100)
 * @property string $parm3  type: varchar(100)
 * @property string $parm4  type: varchar(100)
 */
class Tmpedctrnc extends MciBaseModel
{
    /**
     * Nama tabel (case-sensitive di SQL Server).
     */
    protected $table = 'TMPEDCTRNC';

    /**
     * Daftar LENGKAP kolom sesuai database (16 kolom).
     * Model ini read-only, $fillable hanya untuk dokumentasi & IDE helper.
     *
     * @var array<int,string>
     */
    protected $fillable = [
        'noreff',
        'noacc',
        'kdedc',
        'kdkolektor',
        'tgltrn',
        'amount',
        'ststrn',
        'dc',
        'inpuser',
        'noreffva',
        'pokok',
        'margin',
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
        'noreff' => 'decimal:2',
        'amount' => 'decimal:2',
        'pokok' => 'decimal:2',
        'margin' => 'decimal:2',
    ];
}
