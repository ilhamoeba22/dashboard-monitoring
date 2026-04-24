<?php

namespace App\Models\Mci\Agunan;

use App\Models\Mci\MciBaseModel;

/**
 * Model MCI: TMPAGUNBI
 * --------------------------------------------------------------------------
 * Domain   : Agunan / Jaminan
 * Tabel    : [dbo].[TMPAGUNBI]
 * Kolom    : 7
 * Sumber   : Mapping_MCI_MAR26_01042026.sql / MCI_MAR26_01042026.xlsx
 *
 * CATATAN:
 *  - Model ini READ-ONLY (lihat trait ReadOnlyModel di MciBaseModel).
 *  - Koneksi default `dashboard_data` (SQL Server), otomatis ke DB bulan terbaru.
 *  - Kolom dengan spasi/karakter khusus diakses via:
 *    $model->getAttribute('NAMA KOLOM')  atau  $model->{'NAMA KOLOM'}.
 *
 * @property string|null $nokontrak  type: varchar(11)
 * @property string|null $nolama  type: varchar(20)
 * @property string|null $kdagunold  type: varchar(1)
 * @property string|null $kdagun  type: varchar(2)
 * @property string|null $kdikat  type: varchar(2)
 * @property string|null $nilaiagun  type: numeric(9)
 * @property string|null $bagjamin  type: varchar(4)
 */
class Tmpagunbi extends MciBaseModel
{
    /**
     * Nama tabel (case-sensitive di SQL Server).
     */
    protected $table = 'TMPAGUNBI';

    /**
     * Daftar LENGKAP kolom sesuai database (7 kolom).
     * Model ini read-only, $fillable hanya untuk dokumentasi & IDE helper.
     *
     * @var array<int,string>
     */
    protected $fillable = [
        'nokontrak',
        'nolama',
        'kdagunold',
        'kdagun',
        'kdikat',
        'nilaiagun',
        'bagjamin',
    ];

    /**
     * Casting tipe kolom (non-string saja).
     *
     * @var array<string,string>
     */
    protected $casts = [
        'nilaiagun' => 'decimal:2',
    ];
}
