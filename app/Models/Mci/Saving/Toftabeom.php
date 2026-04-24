<?php

namespace App\Models\Mci\Saving;

use App\Models\Mci\MciBaseModel;

/**
 * Model MCI: TOFTABEOM
 * --------------------------------------------------------------------------
 * Domain   : Saving
 * Tabel    : [dbo].[TOFTABEOM]
 * Kolom    : 12
 * Sumber   : Mapping_MCI_MAR26_01042026.sql / MCI_MAR26_01042026.xlsx
 *
 * CATATAN:
 *  - Model ini READ-ONLY (lihat trait ReadOnlyModel di MciBaseModel).
 *  - Koneksi default `dashboard_data` (SQL Server), otomatis ke DB bulan terbaru.
 *  - Kolom dengan spasi/karakter khusus diakses via:
 *    $model->getAttribute('NAMA KOLOM')  atau  $model->{'NAMA KOLOM'}.
 *
 * @property string $periode  type: varchar(6)
 * @property string $nocif  type: varchar(9)
 * @property string $notab  type: varchar(11)
 * @property string $kdloc  type: char(2)
 * @property string $kdprd  type: char(2)
 * @property string|null $nisbah  type: numeric(5)
 * @property string|null $equivrate  type: numeric(5)
 * @property string|null $sahirrp  type: numeric(9)
 * @property string|null $stsrec  type: char(1)
 * @property string|null $kdgroupdana  type: varchar(10)
 * @property string|null $baghas  type: numeric(9)
 * @property string|null $tax  type: numeric(9)
 */
class Toftabeom extends MciBaseModel
{
    /**
     * Nama tabel (case-sensitive di SQL Server).
     */
    protected $table = 'TOFTABEOM';

    /**
     * Daftar LENGKAP kolom sesuai database (12 kolom).
     * Model ini read-only, $fillable hanya untuk dokumentasi & IDE helper.
     *
     * @var array<int,string>
     */
    protected $fillable = [
        'periode',
        'nocif',
        'notab',
        'kdloc',
        'kdprd',
        'nisbah',
        'equivrate',
        'sahirrp',
        'stsrec',
        'kdgroupdana',
        'baghas',
        'tax',
    ];

    /**
     * Casting tipe kolom (non-string saja).
     *
     * @var array<string,string>
     */
    protected $casts = [
        'nisbah' => 'decimal:2',
        'equivrate' => 'decimal:2',
        'sahirrp' => 'decimal:2',
        'baghas' => 'decimal:2',
        'tax' => 'decimal:2',
    ];
}
