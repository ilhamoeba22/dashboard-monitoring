<?php

namespace App\Models\Mci\Lainnya;

use App\Models\Mci\MciBaseModel;

/**
 * Model MCI: AM_MENU
 * --------------------------------------------------------------------------
 * Domain   : Lainnya
 * Tabel    : [dbo].[AM_MENU]
 * Kolom    : 13
 * Sumber   : Mapping_MCI_MAR26_01042026.sql / MCI_MAR26_01042026.xlsx
 *
 * CATATAN:
 *  - Model ini READ-ONLY (lihat trait ReadOnlyModel di MciBaseModel).
 *  - Koneksi default `dashboard_data` (SQL Server), otomatis ke DB bulan terbaru.
 *  - Kolom dengan spasi/karakter khusus diakses via:
 *    $model->getAttribute('NAMA KOLOM')  atau  $model->{'NAMA KOLOM'}.
 *
 * @property string $grpmenu  type: varchar(4)
 * @property string $formid  type: varchar(20)
 * @property string $grpprd  type: varchar(7)
 * @property string $title  type: varchar(50)
 * @property string $subtitle  type: varchar(50)
 * @property string $labelidpel  type: varchar(50)
 * @property string $label1  type: varchar(50)
 * @property string $label2  type: varchar(50)
 * @property string $label3  type: varchar(50)
 * @property string $label4  type: varchar(50)
 * @property string $slug  type: varchar(20)
 * @property string $status  type: varchar(1)
 * @property string $grplist  type: varchar(100)
 */
class AmMenu extends MciBaseModel
{
    /**
     * Nama tabel (case-sensitive di SQL Server).
     */
    protected $table = 'AM_MENU';

    /**
     * Daftar LENGKAP kolom sesuai database (13 kolom).
     * Model ini read-only, $fillable hanya untuk dokumentasi & IDE helper.
     *
     * @var array<int,string>
     */
    protected $fillable = [
        'grpmenu',
        'formid',
        'grpprd',
        'title',
        'subtitle',
        'labelidpel',
        'label1',
        'label2',
        'label3',
        'label4',
        'slug',
        'status',
        'grplist',
    ];

    /**
     * Casting tipe kolom (non-string saja).
     *
     * @var array<string,string>
     */
    protected $casts = [

    ];
}
