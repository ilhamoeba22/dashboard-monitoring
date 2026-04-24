<?php

namespace App\Models\Mci\Sms;

use App\Models\Mci\MciBaseModel;

/**
 * Model MCI: TOFLOGACT
 * --------------------------------------------------------------------------
 * Domain   : SMS / Notif
 * Tabel    : [dbo].[TOFLOGACT]
 * Kolom    : 10
 * Sumber   : Mapping_MCI_MAR26_01042026.sql / MCI_MAR26_01042026.xlsx
 *
 * CATATAN:
 *  - Model ini READ-ONLY (lihat trait ReadOnlyModel di MciBaseModel).
 *  - Koneksi default `dashboard_data` (SQL Server), otomatis ke DB bulan terbaru.
 *  - Kolom dengan spasi/karakter khusus diakses via:
 *    $model->getAttribute('NAMA KOLOM')  atau  $model->{'NAMA KOLOM'}.
 *
 * @property string $ID  type: numeric(9)
 * @property string|null $tgl  type: varchar(8)
 * @property string|null $modul  type: varchar(10)
 * @property string|null $term  type: varchar(10)
 * @property string|null $ip  type: varchar(20)
 * @property string|null $jamin  type: varchar(10)
 * @property string|null $jamout  type: varchar(10)
 * @property string|null $userid  type: varchar(10)
 * @property string|null $ket  type: varchar(100)
 * @property string|null $fungsi  type: varchar(2)
 */
class Toflogact extends MciBaseModel
{
    /**
     * Nama tabel (case-sensitive di SQL Server).
     */
    protected $table = 'TOFLOGACT';

    /**
     * Daftar LENGKAP kolom sesuai database (10 kolom).
     * Model ini read-only, $fillable hanya untuk dokumentasi & IDE helper.
     *
     * @var array<int,string>
     */
    protected $fillable = [
        'ID',
        'tgl',
        'modul',
        'term',
        'ip',
        'jamin',
        'jamout',
        'userid',
        'ket',
        'fungsi',
    ];

    /**
     * Casting tipe kolom (non-string saja).
     *
     * @var array<string,string>
     */
    protected $casts = [
        'ID' => 'decimal:2',
    ];
}
