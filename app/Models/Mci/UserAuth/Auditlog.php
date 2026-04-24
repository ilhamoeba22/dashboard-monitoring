<?php

namespace App\Models\Mci\UserAuth;

use App\Models\Mci\MciBaseModel;

/**
 * Model MCI: AUDITLOG
 * --------------------------------------------------------------------------
 * Domain   : User / Auth
 * Tabel    : [dbo].[AUDITLOG]
 * Kolom    : 7
 * Sumber   : Mapping_MCI_MAR26_01042026.sql / MCI_MAR26_01042026.xlsx
 *
 * CATATAN:
 *  - Model ini READ-ONLY (lihat trait ReadOnlyModel di MciBaseModel).
 *  - Koneksi default `dashboard_data` (SQL Server), otomatis ke DB bulan terbaru.
 *  - Kolom dengan spasi/karakter khusus diakses via:
 *    $model->getAttribute('NAMA KOLOM')  atau  $model->{'NAMA KOLOM'}.
 *
 * @property string|null $logid  type: varchar(10)
 * @property string|null $loguid  type: varchar(10)
 * @property string|null $logtgl  type: varchar(8)
 * @property string|null $logjam  type: varchar(8)
 * @property string|null $logterm  type: varchar(8)
 * @property string|null $logacc  type: varchar(11)
 * @property string|null $logket  type: varchar(50)
 */
class Auditlog extends MciBaseModel
{
    /**
     * Nama tabel (case-sensitive di SQL Server).
     */
    protected $table = 'AUDITLOG';

    /**
     * Daftar LENGKAP kolom sesuai database (7 kolom).
     * Model ini read-only, $fillable hanya untuk dokumentasi & IDE helper.
     *
     * @var array<int,string>
     */
    protected $fillable = [
        'logid',
        'loguid',
        'logtgl',
        'logjam',
        'logterm',
        'logacc',
        'logket',
    ];

    /**
     * Casting tipe kolom (non-string saja).
     *
     * @var array<string,string>
     */
    protected $casts = [

    ];
}
