<?php

namespace App\Models\Mci\ReportingOjk;

use App\Models\Mci\MciBaseModel;

/**
 * Model MCI: GB0107
 * --------------------------------------------------------------------------
 * Domain   : Reporting OJK/BI
 * Tabel    : [dbo].[GB0107]
 * Kolom    : 19
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
 * @property string|null $noid  type: varchar(30)
 * @property string|null $kepatuhan  type: varchar(1)
 * @property string|null $manajemen_risiko  type: varchar(1)
 * @property string|null $audit_intern  type: varchar(1)
 * @property string|null $apu_dan_ppt  type: varchar(1)
 * @property string|null $fungsi_lainnya  type: varchar(1)
 * @property string|null $tgl_mulai_menjabat  type: varchar(10)
 * @property string|null $no_surat_pengangkatan  type: varchar(30)
 * @property string|null $tanggal_pengangkatan  type: varchar(10)
 * @property string|null $komite_audit  type: varchar(1)
 * @property string|null $komite_pemantau_risiko  type: varchar(1)
 * @property string|null $komite_remunerasi_dan_nominasi  type: varchar(1)
 * @property string|null $status_berhenti_menjabat  type: varchar(1)
 * @property string|null $alasan_berhenti  type: text(16)
 * @property string|null $no_surat_berhenti  type: varchar(30)
 * @property string|null $tanggal_berhenti  type: varchar(10)
 * @property string|null $kom_manajemen_risk  type: varchar(1)
 */
class Gb0107 extends MciBaseModel
{
    /**
     * Nama tabel (case-sensitive di SQL Server).
     */
    protected $table = 'GB0107';

    /**
     * Daftar LENGKAP kolom sesuai database (19 kolom).
     * Model ini read-only, $fillable hanya untuk dokumentasi & IDE helper.
     *
     * @var array<int,string>
     */
    protected $fillable = [
        'id',
        'nama',
        'noid',
        'kepatuhan',
        'manajemen_risiko',
        'audit_intern',
        'apu_dan_ppt',
        'fungsi_lainnya',
        'tgl_mulai_menjabat',
        'no_surat_pengangkatan',
        'tanggal_pengangkatan',
        'komite_audit',
        'komite_pemantau_risiko',
        'komite_remunerasi_dan_nominasi',
        'status_berhenti_menjabat',
        'alasan_berhenti',
        'no_surat_berhenti',
        'tanggal_berhenti',
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
