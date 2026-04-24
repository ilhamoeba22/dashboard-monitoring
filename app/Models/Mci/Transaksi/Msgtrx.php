<?php

namespace App\Models\Mci\Transaksi;

use App\Models\Mci\MciBaseModel;

/**
 * Model MCI: MSGTRX
 * --------------------------------------------------------------------------
 * Domain   : Transaksi
 * Tabel    : [dbo].[MSGTRX]
 * Kolom    : 9
 * Sumber   : Mapping_MCI_MAR26_01042026.sql / MCI_MAR26_01042026.xlsx
 *
 * CATATAN:
 *  - Model ini READ-ONLY (lihat trait ReadOnlyModel di MciBaseModel).
 *  - Koneksi default `dashboard_data` (SQL Server), otomatis ke DB bulan terbaru.
 *  - Kolom dengan spasi/karakter khusus diakses via:
 *    $model->getAttribute('NAMA KOLOM')  atau  $model->{'NAMA KOLOM'}.
 *
 * @property string|null $nohp  type: varchar(16)
 * @property string|null $imei  type: varchar(200)
 * @property string|null $tgltrn  type: varchar(20)
 * @property string|null $batch  type: varchar(4)
 * @property string|null $notrn  type: varchar(4)
 * @property string|null $ststrn  type: varchar(2)
 * @property string|null $stsrpn  type: varchar(2)
 * @property string|null $ket_resp  type: varchar(30)
 * @property string $stssent  type: varchar(1)
 */
class Msgtrx extends MciBaseModel
{
    /**
     * Nama tabel (case-sensitive di SQL Server).
     */
    protected $table = 'MSGTRX';

    /**
     * Daftar LENGKAP kolom sesuai database (9 kolom).
     * Model ini read-only, $fillable hanya untuk dokumentasi & IDE helper.
     *
     * @var array<int,string>
     */
    protected $fillable = [
        'nohp',
        'imei',
        'tgltrn',
        'batch',
        'notrn',
        'ststrn',
        'stsrpn',
        'ket_resp',
        'stssent',
    ];

    /**
     * Casting tipe kolom (non-string saja).
     *
     * @var array<string,string>
     */
    protected $casts = [

    ];
}
