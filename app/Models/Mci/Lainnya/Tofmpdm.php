<?php

namespace App\Models\Mci\Lainnya;

use App\Models\Mci\MciBaseModel;

/**
 * Model MCI: TOFMPDM
 * --------------------------------------------------------------------------
 * Domain   : Lainnya
 * Tabel    : [dbo].[TOFMPDM]
 * Kolom    : 8
 * Sumber   : Mapping_MCI_MAR26_01042026.sql / MCI_MAR26_01042026.xlsx
 *
 * CATATAN:
 *  - Model ini READ-ONLY (lihat trait ReadOnlyModel di MciBaseModel).
 *  - Koneksi default `dashboard_data` (SQL Server), otomatis ke DB bulan terbaru.
 *  - Kolom dengan spasi/karakter khusus diakses via:
 *    $model->getAttribute('NAMA KOLOM')  atau  $model->{'NAMA KOLOM'}.
 *
 * @property string|null $nokontrak  type: varchar(11)
 * @property string|null $tgltagih  type: varchar(8)
 * @property string|null $modal  type: numeric(9)
 * @property string|null $margin  type: numeric(9)
 * @property string|null $nosbbttp  type: varchar(11)
 * @property string|null $nosbbpend  type: varchar(11)
 * @property string|null $tglcair  type: varchar(8)
 * @property string|null $stscair  type: varchar(1)
 */
class Tofmpdm extends MciBaseModel
{
    /**
     * Nama tabel (case-sensitive di SQL Server).
     */
    protected $table = 'TOFMPDM';

    /**
     * Daftar LENGKAP kolom sesuai database (8 kolom).
     * Model ini read-only, $fillable hanya untuk dokumentasi & IDE helper.
     *
     * @var array<int,string>
     */
    protected $fillable = [
        'nokontrak',
        'tgltagih',
        'modal',
        'margin',
        'nosbbttp',
        'nosbbpend',
        'tglcair',
        'stscair',
    ];

    /**
     * Casting tipe kolom (non-string saja).
     *
     * @var array<string,string>
     */
    protected $casts = [
        'modal' => 'decimal:2',
        'margin' => 'decimal:2',
    ];
}
