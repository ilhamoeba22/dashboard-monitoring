<?php

namespace App\Models\Mci\GeneralLedger;

use App\Models\Mci\MciBaseModel;

/**
 * Model MCI: M_NERACA01
 * --------------------------------------------------------------------------
 * Domain   : GL / Accounting
 * Tabel    : [dbo].[M_NERACA01]
 * Kolom    : 17
 * Sumber   : Mapping_MCI_MAR26_01042026.sql / MCI_MAR26_01042026.xlsx
 *
 * CATATAN:
 *  - Model ini READ-ONLY (lihat trait ReadOnlyModel di MciBaseModel).
 *  - Koneksi default `dashboard_data` (SQL Server), otomatis ke DB bulan terbaru.
 *  - Kolom dengan spasi/karakter khusus diakses via:
 *    $model->getAttribute('NAMA KOLOM')  atau  $model->{'NAMA KOLOM'}.
 *
 * @property string|null $kodebank  type: varchar(3)
 * @property string|null $kodecab  type: varchar(2)
 * @property string|null $kodeloc  type: varchar(2)
 * @property string|null $thn  type: varchar(4)
 * @property string|null $bln  type: varchar(2)
 * @property string|null $nosbb  type: varchar(7)
 * @property string|null $nobb  type: varchar(7)
 * @property string|null $tgl01  type: decimal(9)
 * @property string|null $tgl02  type: decimal(9)
 * @property string|null $tgl03  type: decimal(9)
 * @property string|null $tgl04  type: decimal(9)
 * @property string|null $tgl05  type: decimal(9)
 * @property string|null $tgl06  type: decimal(9)
 * @property string|null $tgl07  type: decimal(9)
 * @property string|null $tgl08  type: decimal(9)
 * @property string|null $tgl09  type: decimal(9)
 * @property string|null $tgl10  type: decimal(9)
 */
class MNeraca01 extends MciBaseModel
{
    /**
     * Nama tabel (case-sensitive di SQL Server).
     */
    protected $table = 'M_NERACA01';

    /**
     * Daftar LENGKAP kolom sesuai database (17 kolom).
     * Model ini read-only, $fillable hanya untuk dokumentasi & IDE helper.
     *
     * @var array<int,string>
     */
    protected $fillable = [
        'kodebank',
        'kodecab',
        'kodeloc',
        'thn',
        'bln',
        'nosbb',
        'nobb',
        'tgl01',
        'tgl02',
        'tgl03',
        'tgl04',
        'tgl05',
        'tgl06',
        'tgl07',
        'tgl08',
        'tgl09',
        'tgl10',
    ];

    /**
     * Casting tipe kolom (non-string saja).
     *
     * @var array<string,string>
     */
    protected $casts = [
        'tgl01' => 'decimal:2',
        'tgl02' => 'decimal:2',
        'tgl03' => 'decimal:2',
        'tgl04' => 'decimal:2',
        'tgl05' => 'decimal:2',
        'tgl06' => 'decimal:2',
        'tgl07' => 'decimal:2',
        'tgl08' => 'decimal:2',
        'tgl09' => 'decimal:2',
        'tgl10' => 'decimal:2',
    ];
}
