<?php

namespace App\Models\Mci\Lainnya;

use App\Models\Mci\MciBaseModel;

/**
 * Model MCI: TOFTMGM1
 * --------------------------------------------------------------------------
 * Domain   : Lainnya
 * Tabel    : [dbo].[TOFTMGM1]
 * Kolom    : 31
 * Sumber   : Mapping_MCI_MAR26_01042026.sql / MCI_MAR26_01042026.xlsx
 *
 * CATATAN:
 *  - Model ini READ-ONLY (lihat trait ReadOnlyModel di MciBaseModel).
 *  - Koneksi default `dashboard_data` (SQL Server), otomatis ke DB bulan terbaru.
 *  - Kolom dengan spasi/karakter khusus diakses via:
 *    $model->getAttribute('NAMA KOLOM')  atau  $model->{'NAMA KOLOM'}.
 *
 * @property string|null $nik  type: varchar(16)
 * @property string|null $nama  type: varchar(50)
 * @property string|null $alamat  type: varchar(150)
 * @property string|null $kdjabat  type: char(1)
 * @property string|null $tgleff  type: varchar(8)
 * @property string|null $tglexp  type: varchar(8)
 * @property string|null $nosrtojk  type: varchar(50)
 * @property string|null $tglsrtojk  type: varchar(8)
 * @property string|null $kdsertifikat  type: char(1)
 * @property string|null $tglexpsertifikat  type: varchar(8)
 * @property string|null $formal_kddidik  type: char(1)
 * @property string|null $formal_tgllulus  type: varchar(8)
 * @property string|null $formal_nmlembaga  type: varchar(50)
 * @property string|null $nonformal_jenispelatihan  type: varchar(50)
 * @property string|null $nonformal_tgl  type: varchar(8)
 * @property string|null $nonformal_nmlembaga  type: varchar(50)
 * @property string|null $stskomiteaudit  type: char(1)
 * @property string|null $stskomiterisk  type: char(1)
 * @property string|null $stskomiteremunerasi  type: char(1)
 * @property string|null $stskepatuhan  type: char(1)
 * @property string|null $stskomindefenden  type: char(1)
 * @property string|null $stsrec  type: char(1)
 * @property string|null $inpuser  type: varchar(10)
 * @property string|null $inptgljam  type: varchar(14)
 * @property string|null $inpterm  type: varchar(10)
 * @property string|null $chguser  type: varchar(10)
 * @property string|null $chgtgljam  type: varchar(14)
 * @property string|null $chgterm  type: varchar(10)
 * @property string|null $autuser  type: varchar(10)
 * @property string|null $auttgljam  type: varchar(14)
 * @property string|null $autterm  type: varchar(10)
 */
class Toftmgm1 extends MciBaseModel
{
    /**
     * Nama tabel (case-sensitive di SQL Server).
     */
    protected $table = 'TOFTMGM1';

    /**
     * Daftar LENGKAP kolom sesuai database (31 kolom).
     * Model ini read-only, $fillable hanya untuk dokumentasi & IDE helper.
     *
     * @var array<int,string>
     */
    protected $fillable = [
        'nik',
        'nama',
        'alamat',
        'kdjabat',
        'tgleff',
        'tglexp',
        'nosrtojk',
        'tglsrtojk',
        'kdsertifikat',
        'tglexpsertifikat',
        'formal_kddidik',
        'formal_tgllulus',
        'formal_nmlembaga',
        'nonformal_jenispelatihan',
        'nonformal_tgl',
        'nonformal_nmlembaga',
        'stskomiteaudit',
        'stskomiterisk',
        'stskomiteremunerasi',
        'stskepatuhan',
        'stskomindefenden',
        'stsrec',
        'inpuser',
        'inptgljam',
        'inpterm',
        'chguser',
        'chgtgljam',
        'chgterm',
        'autuser',
        'auttgljam',
        'autterm',
    ];

    /**
     * Casting tipe kolom (non-string saja).
     *
     * @var array<string,string>
     */
    protected $casts = [

    ];
}
