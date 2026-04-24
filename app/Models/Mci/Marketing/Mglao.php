<?php

namespace App\Models\Mci\Marketing;

use App\Models\Mci\MciBaseModel;

/**
 * Model MCI: MGLAO
 * --------------------------------------------------------------------------
 * Domain   : AO / Marketing
 * Tabel    : [dbo].[MGLAO]
 * Kolom    : 7
 * Sumber   : Mapping_MCI_MAR26_01042026.sql / MCI_MAR26_01042026.xlsx
 *
 * CATATAN:
 *  - Model ini READ-ONLY (lihat trait ReadOnlyModel di MciBaseModel).
 *  - Koneksi default `dashboard_data` (SQL Server), otomatis ke DB bulan terbaru.
 *  - Kolom dengan spasi/karakter khusus diakses via:
 *    $model->getAttribute('NAMA KOLOM')  atau  $model->{'NAMA KOLOM'}.
 *
 * @property string $kdao  type: varchar(8)
 * @property string|null $gol  type: varchar(1)
 * @property string|null $kategori  type: varchar(1)
 * @property string|null $nobb  type: varchar(11)
 * @property string|null $nosbb  type: varchar(11)
 * @property string|null $sahir  type: numeric(9)
 * @property string|null $sahirva  type: numeric(9)
 */
class Mglao extends MciBaseModel
{
    /**
     * Nama tabel (case-sensitive di SQL Server).
     */
    protected $table = 'MGLAO';

    /**
     * Daftar LENGKAP kolom sesuai database (7 kolom).
     * Model ini read-only, $fillable hanya untuk dokumentasi & IDE helper.
     *
     * @var array<int,string>
     */
    protected $fillable = [
        'kdao',
        'gol',
        'kategori',
        'nobb',
        'nosbb',
        'sahir',
        'sahirva',
    ];

    /**
     * Casting tipe kolom (non-string saja).
     *
     * @var array<string,string>
     */
    protected $casts = [
        'sahir' => 'decimal:2',
        'sahirva' => 'decimal:2',
    ];
}
