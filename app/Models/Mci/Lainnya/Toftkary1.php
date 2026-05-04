<?php

declare(strict_types=1);

namespace App\Models\Mci\Lainnya;

use App\Models\Mci\MciBaseModel;

/**
 * Model MCI: TOFTKARY1
 * --------------------------------------------------------------------------
 * Domain   : Lainnya
 * Tabel    : [dbo].[TOFTKARY1]
 * Kolom    : 26
 * Sumber   : Mapping_MCI_MAR26_01042026.sql / MCI_MAR26_01042026.xlsx
 *
 * CATATAN:
 *  - Model ini READ-ONLY (lihat trait ReadOnlyModel di MciBaseModel).
 *  - Koneksi default `dashboard_data` (SQL Server), otomatis ke DB bulan terbaru.
 *  - Kolom dengan spasi/karakter khusus diakses via:
 *    $model->getAttribute('NAMA KOLOM')  atau  $model->{'NAMA KOLOM'}.
 *
 * @property string|null $nik type: varchar(16)
 * @property string|null $nama type: varchar(50)
 * @property string|null $alamat type: varchar(150)
 * @property string|null $stsjabatpatuh type: char(1)
 * @property string|null $stsjabatrisk type: char(1)
 * @property string|null $stsjabataudit type: char(1)
 * @property string|null $stsjabatapu type: char(1)
 * @property string|null $stsjabatlain type: char(1)
 * @property string|null $tgleff type: varchar(8)
 * @property string|null $nosrtangkat type: varchar(50)
 * @property string|null $tglsrtangkat type: varchar(8)
 * @property string|null $nosrttegas type: varchar(50)
 * @property string|null $tglsrttegas type: varchar(8)
 * @property string|null $stskomiteaudit type: char(1)
 * @property string|null $stskomiterisk type: char(1)
 * @property string|null $stskomiteremunerasi type: char(1)
 * @property string|null $stsrec type: char(1)
 * @property string|null $inpuser type: varchar(10)
 * @property string|null $inptgljam type: varchar(14)
 * @property string|null $inpterm type: varchar(10)
 * @property string|null $chguser type: varchar(10)
 * @property string|null $chgtgljam type: varchar(14)
 * @property string|null $chgterm type: varchar(10)
 * @property string|null $autuser type: varchar(10)
 * @property string|null $auttgljam type: varchar(14)
 * @property string|null $autterm type: varchar(10)
 */
class Toftkary1 extends MciBaseModel
{
    /**
     * Nama tabel (case-sensitive di SQL Server).
     */
    protected $table = 'TOFTKARY1';

    /**
     * Daftar LENGKAP kolom sesuai database (26 kolom).
     * Model ini read-only, $fillable hanya untuk dokumentasi & IDE helper.
     *
     * @var array<int,string>
     */
    protected $fillable = [
        'nik',
        'nama',
        'alamat',
        'stsjabatpatuh',
        'stsjabatrisk',
        'stsjabataudit',
        'stsjabatapu',
        'stsjabatlain',
        'tgleff',
        'nosrtangkat',
        'tglsrtangkat',
        'nosrttegas',
        'tglsrttegas',
        'stskomiteaudit',
        'stskomiterisk',
        'stskomiteremunerasi',
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
