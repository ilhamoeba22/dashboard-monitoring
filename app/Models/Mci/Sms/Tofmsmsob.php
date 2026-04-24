<?php

namespace App\Models\Mci\Sms;

use App\Models\Mci\MciBaseModel;

/**
 * Model MCI: TOFMSMSOB
 * --------------------------------------------------------------------------
 * Domain   : SMS / Notif
 * Tabel    : [dbo].[TOFMSMSOB]
 * Kolom    : 13
 * Sumber   : Mapping_MCI_MAR26_01042026.sql / MCI_MAR26_01042026.xlsx
 *
 * CATATAN:
 *  - Model ini READ-ONLY (lihat trait ReadOnlyModel di MciBaseModel).
 *  - Koneksi default `dashboard_data` (SQL Server), otomatis ke DB bulan terbaru.
 *  - Kolom dengan spasi/karakter khusus diakses via:
 *    $model->getAttribute('NAMA KOLOM')  atau  $model->{'NAMA KOLOM'}.
 *
 * @property string $ID  type: numeric(9)
 * @property string|null $ID_terima  type: numeric(9)
 * @property string $notrn  type: numeric(9)
 * @property string $tgltrn  type: varchar(8)
 * @property string $jamtrn  type: varchar(8)
 * @property string $nohp  type: varchar(20)
 * @property string $dracc  type: varchar(11)
 * @property string $cracc  type: varchar(20)
 * @property string $nominal  type: numeric(9)
 * @property string|null $stsproc  type: varchar(1)
 * @property string $timelimit  type: varchar(10)
 * @property string|null $tglcair  type: varchar(8)
 * @property string|null $jamcair  type: varchar(8)
 */
class Tofmsmsob extends MciBaseModel
{
    /**
     * Nama tabel (case-sensitive di SQL Server).
     */
    protected $table = 'TOFMSMSOB';

    /**
     * Daftar LENGKAP kolom sesuai database (13 kolom).
     * Model ini read-only, $fillable hanya untuk dokumentasi & IDE helper.
     *
     * @var array<int,string>
     */
    protected $fillable = [
        'ID',
        'ID_terima',
        'notrn',
        'tgltrn',
        'jamtrn',
        'nohp',
        'dracc',
        'cracc',
        'nominal',
        'stsproc',
        'timelimit',
        'tglcair',
        'jamcair',
    ];

    /**
     * Casting tipe kolom (non-string saja).
     *
     * @var array<string,string>
     */
    protected $casts = [
        'ID' => 'decimal:2',
        'ID_terima' => 'decimal:2',
        'notrn' => 'decimal:2',
        'nominal' => 'decimal:2',
    ];
}
