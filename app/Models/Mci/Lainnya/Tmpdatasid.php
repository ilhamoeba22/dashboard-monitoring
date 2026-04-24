<?php

namespace App\Models\Mci\Lainnya;

use App\Models\Mci\MciBaseModel;

/**
 * Model MCI: TMPDATASID
 * --------------------------------------------------------------------------
 * Domain   : Lainnya
 * Tabel    : [dbo].[TMPDATASID]
 * Kolom    : 17
 * Sumber   : Mapping_MCI_MAR26_01042026.sql / MCI_MAR26_01042026.xlsx
 *
 * CATATAN:
 *  - Model ini READ-ONLY (lihat trait ReadOnlyModel di MciBaseModel).
 *  - Koneksi default `dashboard_data` (SQL Server), otomatis ke DB bulan terbaru.
 *  - Kolom dengan spasi/karakter khusus diakses via:
 *    $model->getAttribute('NAMA KOLOM')  atau  $model->{'NAMA KOLOM'}.
 *
 * @property string|null $namasid  type: varchar(100)
 * @property string|null $nmibu  type: varchar(30)
 * @property string|null $alamat  type: varchar(150)
 * @property string|null $tmplhr  type: varchar(30)
 * @property string|null $tgllhr  type: varchar(8)
 * @property string|null $sandidati  type: varchar(5)
 * @property string|null $noid  type: varchar(30)
 * @property string|null $norekening  type: varchar(11)
 * @property string|null $plafond  type: numeric(9)
 * @property string|null $bakidebet  type: numeric(9)
 * @property string|null $kondisi  type: varchar(10)
 * @property string|null $tglkondisi  type: varchar(8)
 * @property string|null $coll  type: varchar(1)
 * @property string|null $tgltgkn  type: varchar(8)
 * @property string|null $tglpk  type: varchar(8)
 * @property string|null $nopk  type: varchar(20)
 * @property string|null $tglexp  type: varchar(8)
 */
class Tmpdatasid extends MciBaseModel
{
    /**
     * Nama tabel (case-sensitive di SQL Server).
     */
    protected $table = 'TMPDATASID';

    /**
     * Daftar LENGKAP kolom sesuai database (17 kolom).
     * Model ini read-only, $fillable hanya untuk dokumentasi & IDE helper.
     *
     * @var array<int,string>
     */
    protected $fillable = [
        'namasid',
        'nmibu',
        'alamat',
        'tmplhr',
        'tgllhr',
        'sandidati',
        'noid',
        'norekening',
        'plafond',
        'bakidebet',
        'kondisi',
        'tglkondisi',
        'coll',
        'tgltgkn',
        'tglpk',
        'nopk',
        'tglexp',
    ];

    /**
     * Casting tipe kolom (non-string saja).
     *
     * @var array<string,string>
     */
    protected $casts = [
        'plafond' => 'decimal:2',
        'bakidebet' => 'decimal:2',
    ];
}
