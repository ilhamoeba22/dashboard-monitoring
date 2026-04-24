<?php

namespace App\Models\Mci\Channel;

use App\Models\Mci\MciBaseModel;

/**
 * Model MCI: LOGATM
 * --------------------------------------------------------------------------
 * Domain   : Channel / ATM / Card
 * Tabel    : [dbo].[LOGATM]
 * Kolom    : 11
 * Sumber   : Mapping_MCI_MAR26_01042026.sql / MCI_MAR26_01042026.xlsx
 *
 * CATATAN:
 *  - Model ini READ-ONLY (lihat trait ReadOnlyModel di MciBaseModel).
 *  - Koneksi default `dashboard_data` (SQL Server), otomatis ke DB bulan terbaru.
 *  - Kolom dengan spasi/karakter khusus diakses via:
 *    $model->getAttribute('NAMA KOLOM')  atau  $model->{'NAMA KOLOM'}.
 *
 * @property int $ID  type: bigint(8)
 * @property string|null $modul  type: varchar(30)
 * @property string|null $tgltrn  type: varchar(8)
 * @property string|null $noreff  type: varchar(20)
 * @property string|null $atmid  type: varchar(20)
 * @property string|null $norek  type: varchar(20)
 * @property string|null $otpcode  type: varchar(20)
 * @property string|null $expcode  type: varchar(20)
 * @property string|null $errorcode  type: varchar(10)
 * @property string|null $ket  type: varchar(100)
 * @property string|null $tgljam  type: varchar(14)
 */
class Logatm extends MciBaseModel
{
    /**
     * Nama tabel (case-sensitive di SQL Server).
     */
    protected $table = 'LOGATM';

    /**
     * Daftar LENGKAP kolom sesuai database (11 kolom).
     * Model ini read-only, $fillable hanya untuk dokumentasi & IDE helper.
     *
     * @var array<int,string>
     */
    protected $fillable = [
        'ID',
        'modul',
        'tgltrn',
        'noreff',
        'atmid',
        'norek',
        'otpcode',
        'expcode',
        'errorcode',
        'ket',
        'tgljam',
    ];

    /**
     * Casting tipe kolom (non-string saja).
     *
     * @var array<string,string>
     */
    protected $casts = [
        'ID' => 'integer',
    ];
}
