<?php

declare(strict_types=1);

namespace App\Models\Mci\Lainnya;

use App\Models\Mci\MciBaseModel;

/**
 * Model MCI: TOFPROFILENSB
 * --------------------------------------------------------------------------
 * Domain   : Lainnya
 * Tabel    : [dbo].[TOFPROFILENSB]
 * Kolom    : 7
 * Sumber   : Mapping_MCI_MAR26_01042026.sql / MCI_MAR26_01042026.xlsx
 *
 * CATATAN:
 *  - Model ini READ-ONLY (lihat trait ReadOnlyModel di MciBaseModel).
 *  - Koneksi default `dashboard_data` (SQL Server), otomatis ke DB bulan terbaru.
 *  - Kolom dengan spasi/karakter khusus diakses via:
 *    $model->getAttribute('NAMA KOLOM')  atau  $model->{'NAMA KOLOM'}.
 *
 * @property string $profile_id type: varchar(30)
 * @property string $ket_profile type: varchar(200)
 * @property string $kd_sandi type: varchar(30)
 * @property string|null $sandi type: varchar(50)
 * @property string|null $inpuser type: varchar(25)
 * @property string|null $inpterm type: varchar(25)
 * @property string|null $inptgl type: varchar(14)
 */
class Tofprofilensb extends MciBaseModel
{
    /**
     * Nama tabel (case-sensitive di SQL Server).
     */
    protected $table = 'TOFPROFILENSB';

    /**
     * Daftar LENGKAP kolom sesuai database (7 kolom).
     * Model ini read-only, $fillable hanya untuk dokumentasi & IDE helper.
     *
     * @var array<int,string>
     */
    protected $fillable = [
        'profile_id',
        'ket_profile',
        'kd_sandi',
        'sandi',
        'inpuser',
        'inpterm',
        'inptgl',
    ];

    /**
     * Casting tipe kolom (non-string saja).
     *
     * @var array<string,string>
     */
    protected $casts = [

    ];
}
