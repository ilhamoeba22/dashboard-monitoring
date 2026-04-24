<?php

namespace App\Models\Mci\Lainnya;

use App\Models\Mci\MciBaseModel;

/**
 * Model MCI: TMPTRNMULTI
 * --------------------------------------------------------------------------
 * Domain   : Lainnya
 * Tabel    : [dbo].[TMPTRNMULTI]
 * Kolom    : 7
 * Sumber   : Mapping_MCI_MAR26_01042026.sql / MCI_MAR26_01042026.xlsx
 *
 * CATATAN:
 *  - Model ini READ-ONLY (lihat trait ReadOnlyModel di MciBaseModel).
 *  - Koneksi default `dashboard_data` (SQL Server), otomatis ke DB bulan terbaru.
 *  - Kolom dengan spasi/karakter khusus diakses via:
 *    $model->getAttribute('NAMA KOLOM')  atau  $model->{'NAMA KOLOM'}.
 *
 * @property string|null $dok_lawan  type: varchar(20)
 * @property string|null $ket_lawan  type: varchar(40)
 * @property string|null $noacc_lawan  type: varchar(11)
 * @property string|null $nmacc_lawan  type: varchar(30)
 * @property string|null $ststrn  type: varchar(1)
 * @property string|null $nominal_lawan  type: numeric(9)
 * @property string|null $stsaba  type: varchar(1)
 */
class Tmptrnmulti extends MciBaseModel
{
    /**
     * Nama tabel (case-sensitive di SQL Server).
     */
    protected $table = 'TMPTRNMULTI';

    /**
     * Daftar LENGKAP kolom sesuai database (7 kolom).
     * Model ini read-only, $fillable hanya untuk dokumentasi & IDE helper.
     *
     * @var array<int,string>
     */
    protected $fillable = [
        'dok_lawan',
        'ket_lawan',
        'noacc_lawan',
        'nmacc_lawan',
        'ststrn',
        'nominal_lawan',
        'stsaba',
    ];

    /**
     * Casting tipe kolom (non-string saja).
     *
     * @var array<string,string>
     */
    protected $casts = [
        'nominal_lawan' => 'decimal:2',
    ];
}
