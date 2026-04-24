<?php

namespace App\Models\Mci\Lainnya;

use App\Models\Mci\MciBaseModel;

/**
 * Model MCI: TMPSLIKSUM
 * --------------------------------------------------------------------------
 * Domain   : Lainnya
 * Tabel    : [dbo].[TMPSLIKSUM]
 * Kolom    : 27
 * Sumber   : Mapping_MCI_MAR26_01042026.sql / MCI_MAR26_01042026.xlsx
 *
 * CATATAN:
 *  - Model ini READ-ONLY (lihat trait ReadOnlyModel di MciBaseModel).
 *  - Koneksi default `dashboard_data` (SQL Server), otomatis ke DB bulan terbaru.
 *  - Kolom dengan spasi/karakter khusus diakses via:
 *    $model->getAttribute('NAMA KOLOM')  atau  $model->{'NAMA KOLOM'}.
 *
 * @property string|null $nokontrak  type: varchar(11)
 * @property string|null $nocif  type: varchar(9)
 * @property string|null $coll_1  type: char(1)
 * @property string|null $dpd_1  type: numeric(9)
 * @property string|null $coll_2  type: char(1)
 * @property string|null $dpd_2  type: numeric(9)
 * @property string|null $coll_3  type: char(1)
 * @property string|null $dpd_3  type: numeric(9)
 * @property string|null $coll_4  type: char(1)
 * @property string|null $dpd_4  type: numeric(9)
 * @property string|null $coll_5  type: char(1)
 * @property string|null $dpd_5  type: numeric(9)
 * @property string|null $coll_6  type: char(1)
 * @property string|null $dpd_6  type: numeric(9)
 * @property string|null $coll_7  type: char(1)
 * @property string|null $dpd_7  type: numeric(9)
 * @property string|null $coll_8  type: char(1)
 * @property string|null $dpd_8  type: numeric(9)
 * @property string|null $coll_9  type: char(1)
 * @property string|null $dpd_9  type: numeric(9)
 * @property string|null $coll_10  type: char(1)
 * @property string|null $dpd_10  type: numeric(9)
 * @property string|null $coll_11  type: char(1)
 * @property string|null $dpd_11  type: numeric(9)
 * @property string|null $coll_12  type: char(1)
 * @property string|null $dpd_12  type: numeric(9)
 * @property string|null $stsrec  type: char(1)
 */
class Tmpsliksum extends MciBaseModel
{
    /**
     * Nama tabel (case-sensitive di SQL Server).
     */
    protected $table = 'TMPSLIKSUM';

    /**
     * Daftar LENGKAP kolom sesuai database (27 kolom).
     * Model ini read-only, $fillable hanya untuk dokumentasi & IDE helper.
     *
     * @var array<int,string>
     */
    protected $fillable = [
        'nokontrak',
        'nocif',
        'coll_1',
        'dpd_1',
        'coll_2',
        'dpd_2',
        'coll_3',
        'dpd_3',
        'coll_4',
        'dpd_4',
        'coll_5',
        'dpd_5',
        'coll_6',
        'dpd_6',
        'coll_7',
        'dpd_7',
        'coll_8',
        'dpd_8',
        'coll_9',
        'dpd_9',
        'coll_10',
        'dpd_10',
        'coll_11',
        'dpd_11',
        'coll_12',
        'dpd_12',
        'stsrec',
    ];

    /**
     * Casting tipe kolom (non-string saja).
     *
     * @var array<string,string>
     */
    protected $casts = [
        'dpd_1' => 'decimal:2',
        'dpd_2' => 'decimal:2',
        'dpd_3' => 'decimal:2',
        'dpd_4' => 'decimal:2',
        'dpd_5' => 'decimal:2',
        'dpd_6' => 'decimal:2',
        'dpd_7' => 'decimal:2',
        'dpd_8' => 'decimal:2',
        'dpd_9' => 'decimal:2',
        'dpd_10' => 'decimal:2',
        'dpd_11' => 'decimal:2',
        'dpd_12' => 'decimal:2',
    ];
}
