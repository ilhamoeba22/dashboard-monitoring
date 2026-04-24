<?php

namespace App\Models\Mci\Lainnya;

use App\Models\Mci\MciBaseModel;

/**
 * Model MCI: TOFMSP
 * --------------------------------------------------------------------------
 * Domain   : Lainnya
 * Tabel    : [dbo].[TOFMSP]
 * Kolom    : 11
 * Sumber   : Mapping_MCI_MAR26_01042026.sql / MCI_MAR26_01042026.xlsx
 *
 * CATATAN:
 *  - Model ini READ-ONLY (lihat trait ReadOnlyModel di MciBaseModel).
 *  - Koneksi default `dashboard_data` (SQL Server), otomatis ke DB bulan terbaru.
 *  - Kolom dengan spasi/karakter khusus diakses via:
 *    $model->getAttribute('NAMA KOLOM')  atau  $model->{'NAMA KOLOM'}.
 *
 * @property string|null $kdsp  type: char(1)
 * @property string|null $tglsp  type: char(8)
 * @property string|null $nosp  type: varchar(30)
 * @property string|null $nokontrak  type: varchar(11)
 * @property string|null $tot_tagihan  type: numeric(9)
 * @property string|null $tot_bln  type: numeric(5)
 * @property string|null $tot_pokok  type: numeric(9)
 * @property string|null $tot_margin  type: numeric(9)
 * @property string|null $dndperhari  type: numeric(5)
 * @property string|null $tglbatasakhir  type: char(8)
 * @property string|null $tglspnext  type: char(8)
 */
class Tofmsp extends MciBaseModel
{
    /**
     * Nama tabel (case-sensitive di SQL Server).
     */
    protected $table = 'TOFMSP';

    /**
     * Daftar LENGKAP kolom sesuai database (11 kolom).
     * Model ini read-only, $fillable hanya untuk dokumentasi & IDE helper.
     *
     * @var array<int,string>
     */
    protected $fillable = [
        'kdsp',
        'tglsp',
        'nosp',
        'nokontrak',
        'tot_tagihan',
        'tot_bln',
        'tot_pokok',
        'tot_margin',
        'dndperhari',
        'tglbatasakhir',
        'tglspnext',
    ];

    /**
     * Casting tipe kolom (non-string saja).
     *
     * @var array<string,string>
     */
    protected $casts = [
        'tot_tagihan' => 'decimal:2',
        'tot_bln' => 'decimal:2',
        'tot_pokok' => 'decimal:2',
        'tot_margin' => 'decimal:2',
        'dndperhari' => 'decimal:2',
    ];
}
