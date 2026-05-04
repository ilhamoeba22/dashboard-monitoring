<?php

declare(strict_types=1);

namespace App\Models\Mci\Sms;

use App\Models\Mci\MciBaseModel;

/**
 * Model MCI: TOFESMS
 * --------------------------------------------------------------------------
 * Domain   : SMS / Notif
 * Tabel    : [dbo].[TOFESMS]
 * Kolom    : 3
 * Sumber   : Mapping_MCI_MAR26_01042026.sql / MCI_MAR26_01042026.xlsx
 *
 * CATATAN:
 *  - Model ini READ-ONLY (lihat trait ReadOnlyModel di MciBaseModel).
 *  - Koneksi default `dashboard_data` (SQL Server), otomatis ke DB bulan terbaru.
 *  - Kolom dengan spasi/karakter khusus diakses via:
 *    $model->getAttribute('NAMA KOLOM')  atau  $model->{'NAMA KOLOM'}.
 *
 * @property string $kdprd type: varchar(2)
 * @property string $notab type: varchar(10)
 * @property string $nocif type: varchar(10)
 */
class Tofesms extends MciBaseModel
{
    /**
     * Nama tabel (case-sensitive di SQL Server).
     */
    protected $table = 'TOFESMS';

    /**
     * Daftar LENGKAP kolom sesuai database (3 kolom).
     * Model ini read-only, $fillable hanya untuk dokumentasi & IDE helper.
     *
     * @var array<int,string>
     */
    protected $fillable = [
        'kdprd',
        'notab',
        'nocif',
    ];

    /**
     * Casting tipe kolom (non-string saja).
     *
     * @var array<string,string>
     */
    protected $casts = [

    ];
}
