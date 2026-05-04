<?php

declare(strict_types=1);

namespace App\Models\Mci\Saving;

use App\Models\Mci\MciBaseModel;

/**
 * Model MCI: TMPTABSH
 * --------------------------------------------------------------------------
 * Domain   : Saving
 * Tabel    : [dbo].[TMPTABSH]
 * Kolom    : 4
 * Sumber   : Mapping_MCI_MAR26_01042026.sql / MCI_MAR26_01042026.xlsx
 *
 * CATATAN:
 *  - Model ini READ-ONLY (lihat trait ReadOnlyModel di MciBaseModel).
 *  - Koneksi default `dashboard_data` (SQL Server), otomatis ke DB bulan terbaru.
 *  - Kolom dengan spasi/karakter khusus diakses via:
 *    $model->getAttribute('NAMA KOLOM')  atau  $model->{'NAMA KOLOM'}.
 *
 * @property string|null $tgl type: varchar(8)
 * @property string|null $notab type: varchar(11)
 * @property string|null $saldo type: numeric(9)
 * @property string|null $kdprd type: varchar(2)
 */
class Tmptabsh extends MciBaseModel
{
    /**
     * Nama tabel (case-sensitive di SQL Server).
     */
    protected $table = 'TMPTABSH';

    /**
     * Daftar LENGKAP kolom sesuai database (4 kolom).
     * Model ini read-only, $fillable hanya untuk dokumentasi & IDE helper.
     *
     * @var array<int,string>
     */
    protected $fillable = [
        'tgl',
        'notab',
        'saldo',
        'kdprd',
    ];

    /**
     * Casting tipe kolom (non-string saja).
     *
     * @var array<string,string>
     */
    protected $casts = [
        'saldo' => 'decimal:2',
    ];
}
