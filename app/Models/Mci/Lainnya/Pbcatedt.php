<?php

namespace App\Models\Mci\Lainnya;

use App\Models\Mci\MciBaseModel;

/**
 * Model MCI: pbcatedt
 * --------------------------------------------------------------------------
 * Domain   : Lainnya
 * Tabel    : [dbo].[pbcatedt]
 * Kolom    : 7
 * Sumber   : Mapping_MCI_MAR26_01042026.sql / MCI_MAR26_01042026.xlsx
 *
 * CATATAN:
 *  - Model ini READ-ONLY (lihat trait ReadOnlyModel di MciBaseModel).
 *  - Koneksi default `dashboard_data` (SQL Server), otomatis ke DB bulan terbaru.
 *  - Kolom dengan spasi/karakter khusus diakses via:
 *    $model->getAttribute('NAMA KOLOM')  atau  $model->{'NAMA KOLOM'}.
 *
 * @property string $pbe_name  type: varchar(30)
 * @property string|null $pbe_edit  type: varchar(254)
 * @property int $pbe_type  type: smallint(2)
 * @property int|null $pbe_cntr  type: int(4)
 * @property int $pbe_seqn  type: smallint(2)
 * @property int|null $pbe_flag  type: int(4)
 * @property string|null $pbe_work  type: char(32)
 */
class Pbcatedt extends MciBaseModel
{
    /**
     * Nama tabel (case-sensitive di SQL Server).
     */
    protected $table = 'pbcatedt';

    /**
     * Daftar LENGKAP kolom sesuai database (7 kolom).
     * Model ini read-only, $fillable hanya untuk dokumentasi & IDE helper.
     *
     * @var array<int,string>
     */
    protected $fillable = [
        'pbe_name',
        'pbe_edit',
        'pbe_type',
        'pbe_cntr',
        'pbe_seqn',
        'pbe_flag',
        'pbe_work',
    ];

    /**
     * Casting tipe kolom (non-string saja).
     *
     * @var array<string,string>
     */
    protected $casts = [
        'pbe_type' => 'integer',
        'pbe_cntr' => 'integer',
        'pbe_seqn' => 'integer',
        'pbe_flag' => 'integer',
    ];
}
