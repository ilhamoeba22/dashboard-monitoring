<?php

namespace App\Models\Mci\Lainnya;

use App\Models\Mci\MciBaseModel;

/**
 * Model MCI: TOFCLOSELOC
 * --------------------------------------------------------------------------
 * Domain   : Lainnya
 * Tabel    : [dbo].[TOFCLOSELOC]
 * Kolom    : 8
 * Sumber   : Mapping_MCI_MAR26_01042026.sql / MCI_MAR26_01042026.xlsx
 *
 * CATATAN:
 *  - Model ini READ-ONLY (lihat trait ReadOnlyModel di MciBaseModel).
 *  - Koneksi default `dashboard_data` (SQL Server), otomatis ke DB bulan terbaru.
 *  - Kolom dengan spasi/karakter khusus diakses via:
 *    $model->getAttribute('NAMA KOLOM')  atau  $model->{'NAMA KOLOM'}.
 *
 * @property string $kdloc  type: char(2)
 * @property string|null $stsclose  type: varchar(1)
 * @property string|null $openuser  type: varchar(10)
 * @property string|null $opentgljam  type: varchar(20)
 * @property string|null $openterm  type: varchar(10)
 * @property string|null $closeuser  type: varchar(10)
 * @property string|null $closetgljam  type: varchar(20)
 * @property string|null $closeterm  type: varchar(10)
 */
class Tofcloseloc extends MciBaseModel
{
    /**
     * Nama tabel (case-sensitive di SQL Server).
     */
    protected $table = 'TOFCLOSELOC';

    /**
     * Daftar LENGKAP kolom sesuai database (8 kolom).
     * Model ini read-only, $fillable hanya untuk dokumentasi & IDE helper.
     *
     * @var array<int,string>
     */
    protected $fillable = [
        'kdloc',
        'stsclose',
        'openuser',
        'opentgljam',
        'openterm',
        'closeuser',
        'closetgljam',
        'closeterm',
    ];

    /**
     * Casting tipe kolom (non-string saja).
     *
     * @var array<string,string>
     */
    protected $casts = [

    ];
}
