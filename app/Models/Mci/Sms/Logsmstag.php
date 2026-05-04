<?php

declare(strict_types=1);

namespace App\Models\Mci\Sms;

use App\Models\Mci\MciBaseModel;

/**
 * Model MCI: LOGSMSTAG
 * --------------------------------------------------------------------------
 * Domain   : SMS / Notif
 * Tabel    : [dbo].[LOGSMSTAG]
 * Kolom    : 6
 * Sumber   : Mapping_MCI_MAR26_01042026.sql / MCI_MAR26_01042026.xlsx
 *
 * CATATAN:
 *  - Model ini READ-ONLY (lihat trait ReadOnlyModel di MciBaseModel).
 *  - Koneksi default `dashboard_data` (SQL Server), otomatis ke DB bulan terbaru.
 *  - Kolom dengan spasi/karakter khusus diakses via:
 *    $model->getAttribute('NAMA KOLOM')  atau  $model->{'NAMA KOLOM'}.
 *
 * @property int $ID type: bigint(8)
 * @property string $tglproses type: varchar(8)
 * @property string $tgltagih type: varchar(8)
 * @property string|null $nokontrak type: varchar(11)
 * @property string $nohp type: varchar(20)
 * @property string|null $status type: varchar(100)
 */
class Logsmstag extends MciBaseModel
{
    /**
     * Nama tabel (case-sensitive di SQL Server).
     */
    protected $table = 'LOGSMSTAG';

    /**
     * Daftar LENGKAP kolom sesuai database (6 kolom).
     * Model ini read-only, $fillable hanya untuk dokumentasi & IDE helper.
     *
     * @var array<int,string>
     */
    protected $fillable = [
        'ID',
        'tglproses',
        'tgltagih',
        'nokontrak',
        'nohp',
        'status',
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
