<?php

namespace App\Models\Mci\Lainnya;

use App\Models\Mci\MciBaseModel;

/**
 * Model MCI: TOFPROV
 * --------------------------------------------------------------------------
 * Domain   : Lainnya
 * Tabel    : [dbo].[TOFPROV]
 * Kolom    : 11
 * Sumber   : Mapping_MCI_MAR26_01042026.sql / MCI_MAR26_01042026.xlsx
 *
 * CATATAN:
 *  - Model ini READ-ONLY (lihat trait ReadOnlyModel di MciBaseModel).
 *  - Koneksi default `dashboard_data` (SQL Server), otomatis ke DB bulan terbaru.
 *  - Kolom dengan spasi/karakter khusus diakses via:
 *    $model->getAttribute('NAMA KOLOM')  atau  $model->{'NAMA KOLOM'}.
 *
 * @property string|null $kdprd  type: varchar(2)
 * @property string|null $noacc  type: varchar(16)
 * @property string|null $nama  type: varchar(30)
 * @property string|null $provawal  type: numeric(9)
 * @property string|null $provbln  type: numeric(9)
 * @property string|null $masa  type: numeric(5)
 * @property string|null $provtot  type: numeric(9)
 * @property string|null $tglprov  type: varchar(8)
 * @property string|null $tgltrnprov  type: varchar(8)
 * @property string|null $provke  type: numeric(5)
 * @property string|null $ststrn  type: varchar(1)
 */
class Tofprov extends MciBaseModel
{
    /**
     * Nama tabel (case-sensitive di SQL Server).
     */
    protected $table = 'TOFPROV';

    /**
     * Daftar LENGKAP kolom sesuai database (11 kolom).
     * Model ini read-only, $fillable hanya untuk dokumentasi & IDE helper.
     *
     * @var array<int,string>
     */
    protected $fillable = [
        'kdprd',
        'noacc',
        'nama',
        'provawal',
        'provbln',
        'masa',
        'provtot',
        'tglprov',
        'tgltrnprov',
        'provke',
        'ststrn',
    ];

    /**
     * Casting tipe kolom (non-string saja).
     *
     * @var array<string,string>
     */
    protected $casts = [
        'provawal' => 'decimal:2',
        'provbln' => 'decimal:2',
        'masa' => 'decimal:2',
        'provtot' => 'decimal:2',
        'provke' => 'decimal:2',
    ];
}
