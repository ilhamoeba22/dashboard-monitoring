<?php

namespace App\Models\Mci\Saving;

use App\Models\Mci\MciBaseModel;

/**
 * Model MCI: TOFTABH
 * --------------------------------------------------------------------------
 * Domain   : Saving
 * Tabel    : [dbo].[TOFTABH]
 * Kolom    : 15
 * Sumber   : Mapping_MCI_MAR26_01042026.sql / MCI_MAR26_01042026.xlsx
 *
 * CATATAN:
 *  - Model ini READ-ONLY (lihat trait ReadOnlyModel di MciBaseModel).
 *  - Koneksi default `dashboard_data` (SQL Server), otomatis ke DB bulan terbaru.
 *  - Kolom dengan spasi/karakter khusus diakses via:
 *    $model->getAttribute('NAMA KOLOM')  atau  $model->{'NAMA KOLOM'}.
 *
 * @property string $notab  type: varchar(11)
 * @property string|null $nocif  type: varchar(9)
 * @property string|null $snama  type: varchar(10)
 * @property string|null $fnama  type: varchar(30)
 * @property string|null $kodecab  type: varchar(3)
 * @property string|null $kodeloc  type: varchar(2)
 * @property string|null $kodekas  type: varchar(2)
 * @property string|null $kodeprd  type: varchar(2)
 * @property string|null $cc  type: varchar(2)
 * @property string|null $userid  type: varchar(10)
 * @property string|null $tgl  type: varchar(14)
 * @property string|null $term  type: varchar(10)
 * @property string|null $tglbuka  type: varchar(8)
 * @property string|null $tgltutup  type: varchar(8)
 * @property string|null $kdprd  type: varchar(2)
 */
class Toftabh extends MciBaseModel
{
    /**
     * Nama tabel (case-sensitive di SQL Server).
     */
    protected $table = 'TOFTABH';

    /**
     * Daftar LENGKAP kolom sesuai database (15 kolom).
     * Model ini read-only, $fillable hanya untuk dokumentasi & IDE helper.
     *
     * @var array<int,string>
     */
    protected $fillable = [
        'notab',
        'nocif',
        'snama',
        'fnama',
        'kodecab',
        'kodeloc',
        'kodekas',
        'kodeprd',
        'cc',
        'userid',
        'tgl',
        'term',
        'tglbuka',
        'tgltutup',
        'kdprd',
    ];

    /**
     * Casting tipe kolom (non-string saja).
     *
     * @var array<string,string>
     */
    protected $casts = [

    ];
}
