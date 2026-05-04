<?php

declare(strict_types=1);

namespace App\Models\Mci\Lainnya;

use App\Models\Mci\MciBaseModel;

/**
 * Model MCI: ACKONV
 * --------------------------------------------------------------------------
 * Domain   : Lainnya
 * Tabel    : [dbo].[ACKONV]
 * Kolom    : 6
 * Sumber   : Mapping_MCI_MAR26_01042026.sql / MCI_MAR26_01042026.xlsx
 *
 * CATATAN:
 *  - Model ini READ-ONLY (lihat trait ReadOnlyModel di MciBaseModel).
 *  - Koneksi default `dashboard_data` (SQL Server), otomatis ke DB bulan terbaru.
 *  - Kolom dengan spasi/karakter khusus diakses via:
 *    $model->getAttribute('NAMA KOLOM')  atau  $model->{'NAMA KOLOM'}.
 *
 * @property string|null $kode type: varchar(1)
 * @property string|null $nolama type: varchar(30)
 * @property string|null $nobaru type: varchar(30)
 * @property string|null $nama type: varchar(100)
 * @property string|null $kdloc type: varchar(2)
 * @property string|null $nolama2 type: varchar(30)
 */
class Ackonv extends MciBaseModel
{
    /**
     * Nama tabel (case-sensitive di SQL Server).
     */
    protected $table = 'ACKONV';

    /**
     * Daftar LENGKAP kolom sesuai database (6 kolom).
     * Model ini read-only, $fillable hanya untuk dokumentasi & IDE helper.
     *
     * @var array<int,string>
     */
    protected $fillable = [
        'kode',
        'nolama',
        'nobaru',
        'nama',
        'kdloc',
        'nolama2',
    ];

    /**
     * Casting tipe kolom (non-string saja).
     *
     * @var array<string,string>
     */
    protected $casts = [

    ];
}
