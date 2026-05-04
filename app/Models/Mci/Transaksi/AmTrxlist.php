<?php

declare(strict_types=1);

namespace App\Models\Mci\Transaksi;

use App\Models\Mci\MciBaseModel;

/**
 * Model MCI: AM_TRXLIST
 * --------------------------------------------------------------------------
 * Domain   : Transaksi
 * Tabel    : [dbo].[AM_TRXLIST]
 * Kolom    : 39
 * Sumber   : Mapping_MCI_MAR26_01042026.sql / MCI_MAR26_01042026.xlsx
 *
 * CATATAN:
 *  - Model ini READ-ONLY (lihat trait ReadOnlyModel di MciBaseModel).
 *  - Koneksi default `dashboard_data` (SQL Server), otomatis ke DB bulan terbaru.
 *  - Kolom dengan spasi/karakter khusus diakses via:
 *    $model->getAttribute('NAMA KOLOM')  atau  $model->{'NAMA KOLOM'}.
 *
 * @property string $tanggal type: varchar(10)
 * @property string $kdmitra type: varchar(25)
 * @property string $lp_idcustomer type: varchar(25)
 * @property string $notrx type: varchar(25)
 * @property string|null $notab type: varchar(25)
 * @property string|null $jam type: varchar(25)
 * @property string|null $lp_idaplication type: varchar(25)
 * @property string|null $lp_idloan type: varchar(25)
 * @property string|null $trxlog type: varchar(25)
 * @property string|null $prdid type: varchar(50)
 * @property string|null $denom type: varchar(25)
 * @property string|null $idpel type: varchar(50)
 * @property string|null $idbill type: varchar(50)
 * @property string|null $tagihan type: varchar(25)
 * @property string|null $admin type: varchar(25)
 * @property string|null $total type: varchar(25)
 * @property string|null $feeadm type: varchar(25)
 * @property string|null $jmlbill type: varchar(25)
 * @property string|null $debet type: varchar(25)
 * @property string|null $credit type: varchar(25)
 * @property string|null $saldo type: varchar(25)
 * @property string|null $kodesw type: varchar(25)
 * @property string|null $biller type: varchar(25)
 * @property string|null $billerprd type: varchar(25)
 * @property string|null $kontendata type: varchar(999)
 * @property string|null $noreff type: varchar(50)
 * @property string|null $billerreff type: varchar(50)
 * @property string|null $notrxbiller type: varchar(50)
 * @property string|null $reff1 type: varchar(25)
 * @property string|null $status type: varchar(11)
 * @property string|null $struk1 type: varchar(999)
 * @property string|null $struk2 type: varchar(999)
 * @property string|null $userlock type: varchar(1)
 * @property string|null $trxtype type: varchar(4)
 * @property string|null $trytype type: varchar(4)
 * @property string|null $ch_type type: varchar(25)
 * @property string|null $ch_id type: varchar(50)
 * @property string|null $ch_parm type: varchar(50)
 * @property string|null $stsrekon type: varchar(25)
 */
class AmTrxlist extends MciBaseModel
{
    /**
     * Nama tabel (case-sensitive di SQL Server).
     */
    protected $table = 'AM_TRXLIST';

    /**
     * Daftar LENGKAP kolom sesuai database (39 kolom).
     * Model ini read-only, $fillable hanya untuk dokumentasi & IDE helper.
     *
     * @var array<int,string>
     */
    protected $fillable = [
        'tanggal',
        'kdmitra',
        'lp_idcustomer',
        'notrx',
        'notab',
        'jam',
        'lp_idaplication',
        'lp_idloan',
        'trxlog',
        'prdid',
        'denom',
        'idpel',
        'idbill',
        'tagihan',
        'admin',
        'total',
        'feeadm',
        'jmlbill',
        'debet',
        'credit',
        'saldo',
        'kodesw',
        'biller',
        'billerprd',
        'kontendata',
        'noreff',
        'billerreff',
        'notrxbiller',
        'reff1',
        'status',
        'struk1',
        'struk2',
        'userlock',
        'trxtype',
        'trytype',
        'ch_type',
        'ch_id',
        'ch_parm',
        'stsrekon',
    ];

    /**
     * Casting tipe kolom (non-string saja).
     *
     * @var array<string,string>
     */
    protected $casts = [

    ];
}
