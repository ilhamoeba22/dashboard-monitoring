<?php

namespace App\Models\Mci\Lainnya;

use App\Models\Mci\MciBaseModel;

/**
 * Model MCI: TOFMSBBETAP
 * --------------------------------------------------------------------------
 * Domain   : Lainnya
 * Tabel    : [dbo].[TOFMSBBETAP]
 * Kolom    : 10
 * Sumber   : Mapping_MCI_MAR26_01042026.sql / MCI_MAR26_01042026.xlsx
 *
 * CATATAN:
 *  - Model ini READ-ONLY (lihat trait ReadOnlyModel di MciBaseModel).
 *  - Koneksi default `dashboard_data` (SQL Server), otomatis ke DB bulan terbaru.
 *  - Kolom dengan spasi/karakter khusus diakses via:
 *    $model->getAttribute('NAMA KOLOM')  atau  $model->{'NAMA KOLOM'}.
 *
 * @property string|null $nokontrak  type: varchar(11)
 * @property string|null $kdbiaya  type: numeric(5)
 * @property string|null $tgl  type: varchar(8)
 * @property string|null $nom_awal  type: numeric(9)
 * @property string|null $jw  type: numeric(5)
 * @property string|null $nom_amort  type: numeric(9)
 * @property string|null $total_amort  type: numeric(9)
 * @property string|null $tgl_amort  type: varchar(8)
 * @property string|null $amort_ke  type: numeric(5)
 * @property string|null $stsrec  type: varchar(1)
 */
class Tofmsbbetap extends MciBaseModel
{
    /**
     * Nama tabel (case-sensitive di SQL Server).
     */
    protected $table = 'TOFMSBBETAP';

    /**
     * Daftar LENGKAP kolom sesuai database (10 kolom).
     * Model ini read-only, $fillable hanya untuk dokumentasi & IDE helper.
     *
     * @var array<int,string>
     */
    protected $fillable = [
        'nokontrak',
        'kdbiaya',
        'tgl',
        'nom_awal',
        'jw',
        'nom_amort',
        'total_amort',
        'tgl_amort',
        'amort_ke',
        'stsrec',
    ];

    /**
     * Casting tipe kolom (non-string saja).
     *
     * @var array<string,string>
     */
    protected $casts = [
        'kdbiaya' => 'decimal:2',
        'nom_awal' => 'decimal:2',
        'jw' => 'decimal:2',
        'nom_amort' => 'decimal:2',
        'total_amort' => 'decimal:2',
        'amort_ke' => 'decimal:2',
    ];
}
