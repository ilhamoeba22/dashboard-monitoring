<?php

namespace App\Models\Mci\Cif;

use App\Models\Mci\MciBaseModel;

/**
 * Model MCI: MCIFDOC
 * --------------------------------------------------------------------------
 * Domain   : CIF / Customer
 * Tabel    : [dbo].[MCIFDOC]
 * Kolom    : 18
 * Sumber   : Mapping_MCI_MAR26_01042026.sql / MCI_MAR26_01042026.xlsx
 *
 * CATATAN:
 *  - Model ini READ-ONLY (lihat trait ReadOnlyModel di MciBaseModel).
 *  - Koneksi default `dashboard_data` (SQL Server), otomatis ke DB bulan terbaru.
 *  - Kolom dengan spasi/karakter khusus diakses via:
 *    $model->getAttribute('NAMA KOLOM')  atau  $model->{'NAMA KOLOM'}.
 *
 * @property string|null $nocif  type: varchar(9)
 * @property string|null $namadepan  type: varchar(50)
 * @property string|null $nama1  type: varchar(30)
 * @property string|null $nama2  type: varchar(30)
 * @property string|null $namaakhir  type: varchar(30)
 * @property string|null $tempatakte  type: varchar(40)
 * @property string|null $noakteawal  type: varchar(40)
 * @property string|null $tglawal  type: varchar(8)
 * @property string|null $noakteakhir  type: varchar(40)
 * @property string|null $tglakhir  type: varchar(8)
 * @property string|null $langgarbmpk  type: varchar(1)
 * @property string|null $lampauibmpk  type: varchar(1)
 * @property string|null $rating  type: varchar(5)
 * @property string|null $pemeringkat  type: varchar(40)
 * @property string|null $gopublic  type: varchar(1)
 * @property string|null $tglperingkat  type: varchar(8)
 * @property string|null $kdusaha  type: varchar(6)
 * @property string|null $jku  type: varchar(3)
 */
class Mcifdoc extends MciBaseModel
{
    /**
     * Nama tabel (case-sensitive di SQL Server).
     */
    protected $table = 'MCIFDOC';

    /**
     * Daftar LENGKAP kolom sesuai database (18 kolom).
     * Model ini read-only, $fillable hanya untuk dokumentasi & IDE helper.
     *
     * @var array<int,string>
     */
    protected $fillable = [
        'nocif',
        'namadepan',
        'nama1',
        'nama2',
        'namaakhir',
        'tempatakte',
        'noakteawal',
        'tglawal',
        'noakteakhir',
        'tglakhir',
        'langgarbmpk',
        'lampauibmpk',
        'rating',
        'pemeringkat',
        'gopublic',
        'tglperingkat',
        'kdusaha',
        'jku',
    ];

    /**
     * Casting tipe kolom (non-string saja).
     *
     * @var array<string,string>
     */
    protected $casts = [

    ];
}
