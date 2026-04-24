<?php

namespace App\Models\Mci\ReportingOjk;

use App\Models\Mci\MciBaseModel;

/**
 * Model MCI: GB0106
 * --------------------------------------------------------------------------
 * Domain   : Reporting OJK/BI
 * Tabel    : [dbo].[GB0106]
 * Kolom    : 14
 * Sumber   : Mapping_MCI_MAR26_01042026.sql / MCI_MAR26_01042026.xlsx
 *
 * CATATAN:
 *  - Model ini READ-ONLY (lihat trait ReadOnlyModel di MciBaseModel).
 *  - Koneksi default `dashboard_data` (SQL Server), otomatis ke DB bulan terbaru.
 *  - Kolom dengan spasi/karakter khusus diakses via:
 *    $model->getAttribute('NAMA KOLOM')  atau  $model->{'NAMA KOLOM'}.
 *
 * @property int $id  type: int(4)
 * @property string|null $nama  type: varchar(100)
 * @property string|null $noid  type: varchar(20)
 * @property string|null $kdjabatan  type: varchar(1)
 * @property string|null $tgl_mulai  type: varchar(10)
 * @property string|null $kom_audit  type: varchar(1)
 * @property string|null $kom_risk  type: varchar(1)
 * @property string|null $kom_remunerasi  type: varchar(1)
 * @property string|null $kepatuhan  type: varchar(1)
 * @property string|null $komisaris_independen  type: varchar(1)
 * @property string|null $tgl_berhenti  type: varchar(10)
 * @property string|null $status_berhenti  type: varchar(1)
 * @property string|null $alasan  type: text(16)
 * @property string|null $kom_manajemen_risk  type: varchar(1)
 */
class Gb0106 extends MciBaseModel
{
    /**
     * Nama tabel (case-sensitive di SQL Server).
     */
    protected $table = 'GB0106';

    /**
     * Daftar LENGKAP kolom sesuai database (14 kolom).
     * Model ini read-only, $fillable hanya untuk dokumentasi & IDE helper.
     *
     * @var array<int,string>
     */
    protected $fillable = [
        'id',
        'nama',
        'noid',
        'kdjabatan',
        'tgl_mulai',
        'kom_audit',
        'kom_risk',
        'kom_remunerasi',
        'kepatuhan',
        'komisaris_independen',
        'tgl_berhenti',
        'status_berhenti',
        'alasan',
        'kom_manajemen_risk',
    ];

    /**
     * Casting tipe kolom (non-string saja).
     *
     * @var array<string,string>
     */
    protected $casts = [
        'id' => 'integer',
    ];
}
