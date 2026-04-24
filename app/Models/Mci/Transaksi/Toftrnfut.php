<?php

namespace App\Models\Mci\Transaksi;

use App\Models\Mci\MciBaseModel;

/**
 * Model MCI: TOFTRNFUT
 * --------------------------------------------------------------------------
 * Domain   : Transaksi
 * Tabel    : [dbo].[TOFTRNFUT]
 * Kolom    : 28
 * Sumber   : Mapping_MCI_MAR26_01042026.sql / MCI_MAR26_01042026.xlsx
 *
 * CATATAN:
 *  - Model ini READ-ONLY (lihat trait ReadOnlyModel di MciBaseModel).
 *  - Koneksi default `dashboard_data` (SQL Server), otomatis ke DB bulan terbaru.
 *  - Kolom dengan spasi/karakter khusus diakses via:
 *    $model->getAttribute('NAMA KOLOM')  atau  $model->{'NAMA KOLOM'}.
 *
 * @property string|null $tgltrn  type: varchar(8)
 * @property string|null $batch  type: numeric(5)
 * @property string|null $notrn  type: numeric(5)
 * @property string|null $userid  type: varchar(10)
 * @property string|null $kdtrn  type: varchar(4)
 * @property string|null $kettrn  type: varchar(40)
 * @property string|null $dracc  type: varchar(11)
 * @property string|null $drkdcab  type: varchar(3)
 * @property string|null $drkdloc  type: varchar(2)
 * @property string|null $drnmac  type: varchar(40)
 * @property string|null $cracc  type: varchar(11)
 * @property string|null $crkdcab  type: varchar(3)
 * @property string|null $crkdloc  type: varchar(2)
 * @property string|null $crnmac  type: varchar(40)
 * @property string|null $dokumen  type: varchar(20)
 * @property string|null $tgldok  type: varchar(8)
 * @property string|null $nominal  type: numeric(9)
 * @property string|null $ket  type: varchar(40)
 * @property string|null $tgleff  type: varchar(8)
 * @property string|null $ststrn  type: varchar(1)
 * @property string|null $stsaro  type: varchar(1)
 * @property string|null $stsrec  type: varchar(1)
 * @property string|null $inpuser  type: varchar(10)
 * @property string|null $inptgljam  type: varchar(14)
 * @property string|null $inpterm  type: varchar(10)
 * @property string|null $autuser  type: varchar(10)
 * @property string|null $auttgljam  type: varchar(14)
 * @property string|null $autterm  type: varchar(10)
 */
class Toftrnfut extends MciBaseModel
{
    /**
     * Nama tabel (case-sensitive di SQL Server).
     */
    protected $table = 'TOFTRNFUT';

    /**
     * Daftar LENGKAP kolom sesuai database (28 kolom).
     * Model ini read-only, $fillable hanya untuk dokumentasi & IDE helper.
     *
     * @var array<int,string>
     */
    protected $fillable = [
        'tgltrn',
        'batch',
        'notrn',
        'userid',
        'kdtrn',
        'kettrn',
        'dracc',
        'drkdcab',
        'drkdloc',
        'drnmac',
        'cracc',
        'crkdcab',
        'crkdloc',
        'crnmac',
        'dokumen',
        'tgldok',
        'nominal',
        'ket',
        'tgleff',
        'ststrn',
        'stsaro',
        'stsrec',
        'inpuser',
        'inptgljam',
        'inpterm',
        'autuser',
        'auttgljam',
        'autterm',
    ];

    /**
     * Casting tipe kolom (non-string saja).
     *
     * @var array<string,string>
     */
    protected $casts = [
        'batch' => 'decimal:2',
        'notrn' => 'decimal:2',
        'nominal' => 'decimal:2',
    ];
}
