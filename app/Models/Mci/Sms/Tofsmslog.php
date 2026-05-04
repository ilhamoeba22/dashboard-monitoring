<?php

declare(strict_types=1);

namespace App\Models\Mci\Sms;

use App\Models\Mci\MciBaseModel;

/**
 * Model MCI: TOFSMSLOG
 * --------------------------------------------------------------------------
 * Domain   : SMS / Notif
 * Tabel    : [dbo].[TOFSMSLOG]
 * Kolom    : 6
 * Sumber   : Mapping_MCI_MAR26_01042026.sql / MCI_MAR26_01042026.xlsx
 *
 * CATATAN:
 *  - Model ini READ-ONLY (lihat trait ReadOnlyModel di MciBaseModel).
 *  - Koneksi default `dashboard_data` (SQL Server), otomatis ke DB bulan terbaru.
 *  - Kolom dengan spasi/karakter khusus diakses via:
 *    $model->getAttribute('NAMA KOLOM')  atau  $model->{'NAMA KOLOM'}.
 *
 * @property string $ID type: varchar(10)
 * @property string|null $nohp type: varchar(20)
 * @property string|null $pesan type: varchar(250)
 * @property string|null $tglops type: varchar(8)
 * @property string|null $tglrx type: timestamp(8)
 * @property string|null $stspesan type: varchar(2)
 */
class Tofsmslog extends MciBaseModel
{
    /**
     * Nama tabel (case-sensitive di SQL Server).
     */
    protected $table = 'TOFSMSLOG';

    /**
     * Daftar LENGKAP kolom sesuai database (6 kolom).
     * Model ini read-only, $fillable hanya untuk dokumentasi & IDE helper.
     *
     * @var array<int,string>
     */
    protected $fillable = [
        'ID',
        'nohp',
        'pesan',
        'tglops',
        'tglrx',
        'stspesan',
    ];

    /**
     * Casting tipe kolom (non-string saja).
     *
     * @var array<string,string>
     */
    protected $casts = [

    ];
}
