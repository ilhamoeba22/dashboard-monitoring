<?php

namespace App\Models\Mci\Transaksi;

use App\Models\Mci\MciBaseModel;

/**
 * Model MCI: AM_TRXLOG
 * --------------------------------------------------------------------------
 * Domain   : Transaksi
 * Tabel    : [dbo].[AM_TRXLOG]
 * Kolom    : 31
 * Sumber   : Mapping_MCI_MAR26_01042026.sql / MCI_MAR26_01042026.xlsx
 *
 * CATATAN:
 *  - Model ini READ-ONLY (lihat trait ReadOnlyModel di MciBaseModel).
 *  - Koneksi default `dashboard_data` (SQL Server), otomatis ke DB bulan terbaru.
 *  - Kolom dengan spasi/karakter khusus diakses via:
 *    $model->getAttribute('NAMA KOLOM')  atau  $model->{'NAMA KOLOM'}.
 *
 * @property string|null $logtrx  type: varchar(50)
 * @property string|null $tanggal  type: varchar(10)
 * @property string|null $kdmitra  type: varchar(25)
 * @property string|null $idcustomer  type: varchar(25)
 * @property string|null $notab  type: varchar(25)
 * @property string|null $notrx  type: varchar(25)
 * @property string|null $produk  type: varchar(25)
 * @property string|null $denom  type: varchar(25)
 * @property string|null $tagihan  type: varchar(20)
 * @property string|null $admin  type: varchar(20)
 * @property string|null $total  type: varchar(20)
 * @property string|null $feeadm  type: varchar(20)
 * @property string|null $jmlbill  type: varchar(20)
 * @property string|null $biller  type: varchar(25)
 * @property string|null $billerprd  type: varchar(25)
 * @property string|null $kontendata  type: text(16)
 * @property string|null $lp_regid  type: varchar(50)
 * @property string|null $lp_idaplication  type: varchar(25)
 * @property string|null $lp_idloan  type: varchar(25)
 * @property string|null $logid  type: varchar(50)
 * @property string|null $kodetrx  type: varchar(10)
 * @property string|null $request  type: text(16)
 * @property string|null $rc  type: varchar(10)
 * @property string|null $rcdesc  type: varchar(30)
 * @property string|null $respon  type: text(16)
 * @property string|null $noreff  type: varchar(50)
 * @property string|null $trxtype  type: varchar(4)
 * @property string|null $trytype  type: varchar(4)
 * @property string|null $ch_type  type: varchar(25)
 * @property string|null $ch_id  type: varchar(50)
 * @property string|null $ch_parm  type: varchar(50)
 */
class AmTrxlog extends MciBaseModel
{
    /**
     * Nama tabel (case-sensitive di SQL Server).
     */
    protected $table = 'AM_TRXLOG';

    /**
     * Daftar LENGKAP kolom sesuai database (31 kolom).
     * Model ini read-only, $fillable hanya untuk dokumentasi & IDE helper.
     *
     * @var array<int,string>
     */
    protected $fillable = [
        'logtrx',
        'tanggal',
        'kdmitra',
        'idcustomer',
        'notab',
        'notrx',
        'produk',
        'denom',
        'tagihan',
        'admin',
        'total',
        'feeadm',
        'jmlbill',
        'biller',
        'billerprd',
        'kontendata',
        'lp_regid',
        'lp_idaplication',
        'lp_idloan',
        'logid',
        'kodetrx',
        'request',
        'rc',
        'rcdesc',
        'respon',
        'noreff',
        'trxtype',
        'trytype',
        'ch_type',
        'ch_id',
        'ch_parm',
    ];

    /**
     * Casting tipe kolom (non-string saja).
     *
     * @var array<string,string>
     */
    protected $casts = [

    ];
}
