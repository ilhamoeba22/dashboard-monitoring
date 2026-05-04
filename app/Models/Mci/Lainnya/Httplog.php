<?php

declare(strict_types=1);

namespace App\Models\Mci\Lainnya;

use App\Models\Mci\MciBaseModel;

/**
 * Model MCI: HTTPLOG
 * --------------------------------------------------------------------------
 * Domain   : Lainnya
 * Tabel    : [dbo].[HTTPLOG]
 * Kolom    : 16
 * Sumber   : Mapping_MCI_MAR26_01042026.sql / MCI_MAR26_01042026.xlsx
 *
 * CATATAN:
 *  - Model ini READ-ONLY (lihat trait ReadOnlyModel di MciBaseModel).
 *  - Koneksi default `dashboard_data` (SQL Server), otomatis ke DB bulan terbaru.
 *  - Kolom dengan spasi/karakter khusus diakses via:
 *    $model->getAttribute('NAMA KOLOM')  atau  $model->{'NAMA KOLOM'}.
 *
 * @property string $http_date type: varchar(8)
 * @property string $http_time type: varchar(6)
 * @property string $http_realdatetime type: varchar(14)
 * @property string $http_method type: varchar(20)
 * @property string $http_url type: varchar(200)
 * @property string $http_host type: varchar(50)
 * @property string $application_id type: varchar(100)
 * @property string $inout type: varchar(10)
 * @property string $trace_id type: varchar(100)
 * @property string $noreff type: varchar(200)
 * @property string $identity_id type: varchar(100)
 * @property string|null $amount type: numeric(9)
 * @property string $body_header type: varchar(-1)
 * @property string $body_data type: varchar(-1)
 * @property string $return_code type: varchar(20)
 * @property string $return_desc type: varchar(200)
 */
class Httplog extends MciBaseModel
{
    /**
     * Nama tabel (case-sensitive di SQL Server).
     */
    protected $table = 'HTTPLOG';

    /**
     * Daftar LENGKAP kolom sesuai database (16 kolom).
     * Model ini read-only, $fillable hanya untuk dokumentasi & IDE helper.
     *
     * @var array<int,string>
     */
    protected $fillable = [
        'http_date',
        'http_time',
        'http_realdatetime',
        'http_method',
        'http_url',
        'http_host',
        'application_id',
        'inout',
        'trace_id',
        'noreff',
        'identity_id',
        'amount',
        'body_header',
        'body_data',
        'return_code',
        'return_desc',
    ];

    /**
     * Casting tipe kolom (non-string saja).
     *
     * @var array<string,string>
     */
    protected $casts = [
        'amount' => 'decimal:2',
    ];
}
