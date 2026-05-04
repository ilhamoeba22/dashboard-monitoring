<?php

declare(strict_types=1);

namespace App\Models\Mci\Ppap;

use App\Models\Mci\MciBaseModel;

/**
 * Model MCI: TMPTSBBPPAP
 * --------------------------------------------------------------------------
 * Domain   : PPAP / DPD / Coll
 * Tabel    : [dbo].[TMPTSBBPPAP]
 * Kolom    : 5
 * Sumber   : Mapping_MCI_MAR26_01042026.sql / MCI_MAR26_01042026.xlsx
 *
 * CATATAN:
 *  - Model ini READ-ONLY (lihat trait ReadOnlyModel di MciBaseModel).
 *  - Koneksi default `dashboard_data` (SQL Server), otomatis ke DB bulan terbaru.
 *  - Kolom dengan spasi/karakter khusus diakses via:
 *    $model->getAttribute('NAMA KOLOM')  atau  $model->{'NAMA KOLOM'}.
 *
 * @property int $ID type: int(4)
 * @property string $kdprd type: char(2)
 * @property string|null $kdppap type: char(1)
 * @property string|null $sbbppap type: char(7)
 * @property string|null $sbbbyppap type: char(7)
 */
class Tmptsbbppap extends MciBaseModel
{
    /**
     * Nama tabel (case-sensitive di SQL Server).
     */
    protected $table = 'TMPTSBBPPAP';

    /**
     * Daftar LENGKAP kolom sesuai database (5 kolom).
     * Model ini read-only, $fillable hanya untuk dokumentasi & IDE helper.
     *
     * @var array<int,string>
     */
    protected $fillable = [
        'ID',
        'kdprd',
        'kdppap',
        'sbbppap',
        'sbbbyppap',
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
