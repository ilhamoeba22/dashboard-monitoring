<?php

namespace App\Models\Mci\Lainnya;

use App\Models\Mci\MciBaseModel;

/**
 * Model MCI: MGKONTEN
 * --------------------------------------------------------------------------
 * Domain   : Lainnya
 * Tabel    : [dbo].[MGKONTEN]
 * Kolom    : 21
 * Sumber   : Mapping_MCI_MAR26_01042026.sql / MCI_MAR26_01042026.xlsx
 *
 * CATATAN:
 *  - Model ini READ-ONLY (lihat trait ReadOnlyModel di MciBaseModel).
 *  - Koneksi default `dashboard_data` (SQL Server), otomatis ke DB bulan terbaru.
 *  - Kolom dengan spasi/karakter khusus diakses via:
 *    $model->getAttribute('NAMA KOLOM')  atau  $model->{'NAMA KOLOM'}.
 *
 * @property string $kdkonten  type: varchar(11)
 * @property string|null $tpkonten  type: varchar(50)
 * @property string|null $userid  type: varchar(50)
 * @property string|null $time  type: char(12)
 * @property string|null $konten  type: text(16)
 * @property string|null $flag  type: char(1)
 * @property string|null $path  type: varchar(50)
 * @property string|null $alias  type: varchar(20)
 * @property string|null $title  type: varchar(64)
 * @property string|null $tglawal  type: varchar(12)
 * @property string|null $tglakhir  type: varchar(12)
 * @property string $tgljam_mulai  type: varchar(20)
 * @property string $tgljam_selesai  type: varchar(20)
 * @property bool $use_notification  type: bit(1)
 * @property string|null $tgljam_notif  type: varchar(20)
 * @property string $inptgljam  type: varchar(20)
 * @property string $inpuser  type: varchar(50)
 * @property string $stsrec  type: varchar(2)
 * @property string|null $kdgroupusrmb  type: varchar(50)
 * @property string|null $title_notif  type: varchar(50)
 * @property string|null $konten_notif  type: varchar(200)
 */
class Mgkonten extends MciBaseModel
{
    /**
     * Nama tabel (case-sensitive di SQL Server).
     */
    protected $table = 'MGKONTEN';

    /**
     * Daftar LENGKAP kolom sesuai database (21 kolom).
     * Model ini read-only, $fillable hanya untuk dokumentasi & IDE helper.
     *
     * @var array<int,string>
     */
    protected $fillable = [
        'kdkonten',
        'tpkonten',
        'userid',
        'time',
        'konten',
        'flag',
        'path',
        'alias',
        'title',
        'tglawal',
        'tglakhir',
        'tgljam_mulai',
        'tgljam_selesai',
        'use_notification',
        'tgljam_notif',
        'inptgljam',
        'inpuser',
        'stsrec',
        'kdgroupusrmb',
        'title_notif',
        'konten_notif',
    ];

    /**
     * Casting tipe kolom (non-string saja).
     *
     * @var array<string,string>
     */
    protected $casts = [
        'use_notification' => 'boolean',
    ];
}
