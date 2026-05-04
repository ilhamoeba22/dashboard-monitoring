<?php

declare(strict_types=1);

namespace App\Models\Mci\Lainnya;

use App\Models\Mci\MciBaseModel;

/**
 * Model MCI: PROGACT
 * --------------------------------------------------------------------------
 * Domain   : Lainnya
 * Tabel    : [dbo].[PROGACT]
 * Kolom    : 10
 * Sumber   : Mapping_MCI_MAR26_01042026.sql / MCI_MAR26_01042026.xlsx
 *
 * CATATAN:
 *  - Model ini READ-ONLY (lihat trait ReadOnlyModel di MciBaseModel).
 *  - Koneksi default `dashboard_data` (SQL Server), otomatis ke DB bulan terbaru.
 *  - Kolom dengan spasi/karakter khusus diakses via:
 *    $model->getAttribute('NAMA KOLOM')  atau  $model->{'NAMA KOLOM'}.
 *
 * @property string $realdatetime type: varchar(14)
 * @property string $application_id type: varchar(100)
 * @property string $prog_id type: varchar(200)
 * @property string $trace_id type: varchar(100)
 * @property string $identity_id type: varchar(100)
 * @property string $noreff type: varchar(200)
 * @property string $desc_prog type: varchar(-1)
 * @property string $device_id type: varchar(150)
 * @property string $ip_x type: varchar(150)
 * @property string $additional_info type: varchar(-1)
 */
class Progact extends MciBaseModel
{
    /**
     * Nama tabel (case-sensitive di SQL Server).
     */
    protected $table = 'PROGACT';

    /**
     * Daftar LENGKAP kolom sesuai database (10 kolom).
     * Model ini read-only, $fillable hanya untuk dokumentasi & IDE helper.
     *
     * @var array<int,string>
     */
    protected $fillable = [
        'realdatetime',
        'application_id',
        'prog_id',
        'trace_id',
        'identity_id',
        'noreff',
        'desc_prog',
        'device_id',
        'ip_x',
        'additional_info',
    ];

    /**
     * Casting tipe kolom (non-string saja).
     *
     * @var array<string,string>
     */
    protected $casts = [

    ];
}
