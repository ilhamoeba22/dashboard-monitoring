<?php

declare(strict_types=1);

namespace App\Models\Mci\Investasi;

use App\Models\Mci\MciBaseModel;

/**
 * Model MCI: TOFSILOG
 * --------------------------------------------------------------------------
 * Domain   : Investasi / Saham
 * Tabel    : [dbo].[TOFSILOG]
 * Kolom    : 9
 * Sumber   : Mapping_MCI_MAR26_01042026.sql / MCI_MAR26_01042026.xlsx
 *
 * CATATAN:
 *  - Model ini READ-ONLY (lihat trait ReadOnlyModel di MciBaseModel).
 *  - Koneksi default `dashboard_data` (SQL Server), otomatis ke DB bulan terbaru.
 *  - Kolom dengan spasi/karakter khusus diakses via:
 *    $model->getAttribute('NAMA KOLOM')  atau  $model->{'NAMA KOLOM'}.
 *
 * @property int $ID type: bigint(8)
 * @property string|null $tgltrn type: varchar(8)
 * @property string|null $noreff type: varchar(20)
 * @property string|null $noacc type: varchar(11)
 * @property string|null $noacclawan type: varchar(11)
 * @property string|null $amount type: numeric(9)
 * @property string|null $ststrn type: char(1)
 * @property string|null $ket type: varchar(40)
 * @property string|null $prog type: varchar(40)
 */
class Tofsilog extends MciBaseModel
{
    /**
     * Nama tabel (case-sensitive di SQL Server).
     */
    protected $table = 'TOFSILOG';

    /**
     * Daftar LENGKAP kolom sesuai database (9 kolom).
     * Model ini read-only, $fillable hanya untuk dokumentasi & IDE helper.
     *
     * @var array<int,string>
     */
    protected $fillable = [
        'ID',
        'tgltrn',
        'noreff',
        'noacc',
        'noacclawan',
        'amount',
        'ststrn',
        'ket',
        'prog',
    ];

    /**
     * Casting tipe kolom (non-string saja).
     *
     * @var array<string,string>
     */
    protected $casts = [
        'ID' => 'integer',
        'amount' => 'decimal:2',
    ];
}
