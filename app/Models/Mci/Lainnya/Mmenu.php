<?php

namespace App\Models\Mci\Lainnya;

use App\Models\Mci\MciBaseModel;

/**
 * Model MCI: MMENU
 * --------------------------------------------------------------------------
 * Domain   : Lainnya
 * Tabel    : [dbo].[MMENU]
 * Kolom    : 7
 * Sumber   : Mapping_MCI_MAR26_01042026.sql / MCI_MAR26_01042026.xlsx
 *
 * CATATAN:
 *  - Model ini READ-ONLY (lihat trait ReadOnlyModel di MciBaseModel).
 *  - Koneksi default `dashboard_data` (SQL Server), otomatis ke DB bulan terbaru.
 *  - Kolom dengan spasi/karakter khusus diakses via:
 *    $model->getAttribute('NAMA KOLOM')  atau  $model->{'NAMA KOLOM'}.
 *
 * @property string $Modul  type: varchar(15)
 * @property string $menu  type: varchar(25)
 * @property string $submenu  type: varchar(50)
 * @property string|null $sts  type: varchar(1)
 * @property string|null $PATHICON  type: varchar(200)
 * @property int|null $ISPARENT  type: int(4)
 * @property string|null $WINDOW  type: varchar(150)
 */
class Mmenu extends MciBaseModel
{
    /**
     * Nama tabel (case-sensitive di SQL Server).
     */
    protected $table = 'MMENU';

    /**
     * Daftar LENGKAP kolom sesuai database (7 kolom).
     * Model ini read-only, $fillable hanya untuk dokumentasi & IDE helper.
     *
     * @var array<int,string>
     */
    protected $fillable = [
        'Modul',
        'menu',
        'submenu',
        'sts',
        'PATHICON',
        'ISPARENT',
        'WINDOW',
    ];

    /**
     * Casting tipe kolom (non-string saja).
     *
     * @var array<string,string>
     */
    protected $casts = [
        'ISPARENT' => 'integer',
    ];
}
