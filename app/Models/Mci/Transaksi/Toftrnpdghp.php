<?php

namespace App\Models\Mci\Transaksi;

use App\Models\Mci\MciBaseModel;

/**
 * Model MCI: TOFTRNPDGHP
 * --------------------------------------------------------------------------
 * Domain   : Transaksi
 * Tabel    : [dbo].[TOFTRNPDGHP]
 * Kolom    : 9
 * Sumber   : Mapping_MCI_MAR26_01042026.sql / MCI_MAR26_01042026.xlsx
 *
 * CATATAN:
 *  - Model ini READ-ONLY (lihat trait ReadOnlyModel di MciBaseModel).
 *  - Koneksi default `dashboard_data` (SQL Server), otomatis ke DB bulan terbaru.
 *  - Kolom dengan spasi/karakter khusus diakses via:
 *    $model->getAttribute('NAMA KOLOM')  atau  $model->{'NAMA KOLOM'}.
 *
 * @property int $ID  type: bigint(8)
 * @property string $nohp  type: varchar(20)
 * @property string $imei  type: varchar(30)
 * @property string $tgltrn  type: varchar(8)
 * @property string $batch  type: numeric(5)
 * @property string $notrn  type: numeric(5)
 * @property string $kunci  type: varchar(150)
 * @property string $ststrn  type: char(1)
 * @property string $stspost  type: char(1)
 */
class Toftrnpdghp extends MciBaseModel
{
    /**
     * Nama tabel (case-sensitive di SQL Server).
     */
    protected $table = 'TOFTRNPDGHP';

    /**
     * Daftar LENGKAP kolom sesuai database (9 kolom).
     * Model ini read-only, $fillable hanya untuk dokumentasi & IDE helper.
     *
     * @var array<int,string>
     */
    protected $fillable = [
        'ID',
        'nohp',
        'imei',
        'tgltrn',
        'batch',
        'notrn',
        'kunci',
        'ststrn',
        'stspost',
    ];

    /**
     * Casting tipe kolom (non-string saja).
     *
     * @var array<string,string>
     */
    protected $casts = [
        'ID' => 'integer',
        'batch' => 'decimal:2',
        'notrn' => 'decimal:2',
    ];
}
