<?php

namespace App\Models\Mci\Lainnya;

use App\Models\Mci\MciBaseModel;

/**
 * Model MCI: pbcattbl
 * --------------------------------------------------------------------------
 * Domain   : Lainnya
 * Tabel    : [dbo].[pbcattbl]
 * Kolom    : 25
 * Sumber   : Mapping_MCI_MAR26_01042026.sql / MCI_MAR26_01042026.xlsx
 *
 * CATATAN:
 *  - Model ini READ-ONLY (lihat trait ReadOnlyModel di MciBaseModel).
 *  - Koneksi default `dashboard_data` (SQL Server), otomatis ke DB bulan terbaru.
 *  - Kolom dengan spasi/karakter khusus diakses via:
 *    $model->getAttribute('NAMA KOLOM')  atau  $model->{'NAMA KOLOM'}.
 *
 * @property string|null $pbt_tnam  type: char(30)
 * @property int|null $pbt_tid  type: int(4)
 * @property string|null $pbt_ownr  type: char(30)
 * @property int|null $pbd_fhgt  type: smallint(2)
 * @property int|null $pbd_fwgt  type: smallint(2)
 * @property string|null $pbd_fitl  type: char(1)
 * @property string|null $pbd_funl  type: char(1)
 * @property int|null $pbd_fchr  type: smallint(2)
 * @property int|null $pbd_fptc  type: smallint(2)
 * @property string|null $pbd_ffce  type: char(32)
 * @property int|null $pbh_fhgt  type: smallint(2)
 * @property int|null $pbh_fwgt  type: smallint(2)
 * @property string|null $pbh_fitl  type: char(1)
 * @property string|null $pbh_funl  type: char(1)
 * @property int|null $pbh_fchr  type: smallint(2)
 * @property int|null $pbh_fptc  type: smallint(2)
 * @property string|null $pbh_ffce  type: char(32)
 * @property int|null $pbl_fhgt  type: smallint(2)
 * @property int|null $pbl_fwgt  type: smallint(2)
 * @property string|null $pbl_fitl  type: char(1)
 * @property string|null $pbl_funl  type: char(1)
 * @property int|null $pbl_fchr  type: smallint(2)
 * @property int|null $pbl_fptc  type: smallint(2)
 * @property string|null $pbl_ffce  type: char(32)
 * @property string|null $pbt_cmnt  type: varchar(254)
 */
class Pbcattbl extends MciBaseModel
{
    /**
     * Nama tabel (case-sensitive di SQL Server).
     */
    protected $table = 'pbcattbl';

    /**
     * Daftar LENGKAP kolom sesuai database (25 kolom).
     * Model ini read-only, $fillable hanya untuk dokumentasi & IDE helper.
     *
     * @var array<int,string>
     */
    protected $fillable = [
        'pbt_tnam',
        'pbt_tid',
        'pbt_ownr',
        'pbd_fhgt',
        'pbd_fwgt',
        'pbd_fitl',
        'pbd_funl',
        'pbd_fchr',
        'pbd_fptc',
        'pbd_ffce',
        'pbh_fhgt',
        'pbh_fwgt',
        'pbh_fitl',
        'pbh_funl',
        'pbh_fchr',
        'pbh_fptc',
        'pbh_ffce',
        'pbl_fhgt',
        'pbl_fwgt',
        'pbl_fitl',
        'pbl_funl',
        'pbl_fchr',
        'pbl_fptc',
        'pbl_ffce',
        'pbt_cmnt',
    ];

    /**
     * Casting tipe kolom (non-string saja).
     *
     * @var array<string,string>
     */
    protected $casts = [
        'pbt_tid' => 'integer',
        'pbd_fhgt' => 'integer',
        'pbd_fwgt' => 'integer',
        'pbd_fchr' => 'integer',
        'pbd_fptc' => 'integer',
        'pbh_fhgt' => 'integer',
        'pbh_fwgt' => 'integer',
        'pbh_fchr' => 'integer',
        'pbh_fptc' => 'integer',
        'pbl_fhgt' => 'integer',
        'pbl_fwgt' => 'integer',
        'pbl_fchr' => 'integer',
        'pbl_fptc' => 'integer',
    ];
}
