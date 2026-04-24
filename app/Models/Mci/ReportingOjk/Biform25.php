<?php

namespace App\Models\Mci\ReportingOjk;

use App\Models\Mci\MciBaseModel;

/**
 * Model MCI: BIFORM25
 * --------------------------------------------------------------------------
 * Domain   : Reporting OJK/BI
 * Tabel    : [dbo].[BIFORM25]
 * Kolom    : 18
 * Sumber   : Mapping_MCI_MAR26_01042026.sql / MCI_MAR26_01042026.xlsx
 *
 * CATATAN:
 *  - Model ini READ-ONLY (lihat trait ReadOnlyModel di MciBaseModel).
 *  - Koneksi default `dashboard_data` (SQL Server), otomatis ke DB bulan terbaru.
 *  - Kolom dengan spasi/karakter khusus diakses via:
 *    $model->getAttribute('NAMA KOLOM')  atau  $model->{'NAMA KOLOM'}.
 *
 * @property string $kdloc  type: char(2)
 * @property string $nocif  type: varchar(10)
 * @property string|null $golcust  type: varchar(6)
 * @property string|null $hubbank  type: char(1)
 * @property string|null $jnsinstrumen  type: char(1)
 * @property string|null $norek  type: varchar(20)
 * @property string|null $tgleff  type: varchar(8)
 * @property string|null $tglexp  type: varchar(8)
 * @property string|null $jnsakad  type: char(1)
 * @property string|null $sifatinvestasi  type: char(1)
 * @property string|null $mtdbaghas  type: char(1)
 * @property string|null $nisbah  type: numeric(5)
 * @property string|null $rate_awal  type: numeric(5)
 * @property string|null $rate  type: numeric(5)
 * @property string|null $saldo  type: numeric(9)
 * @property string|null $byadm  type: numeric(9)
 * @property string|null $jumlah  type: numeric(9)
 * @property string|null $kelonggarantarik  type: numeric(9)
 */
class Biform25 extends MciBaseModel
{
    /**
     * Nama tabel (case-sensitive di SQL Server).
     */
    protected $table = 'BIFORM25';

    /**
     * Daftar LENGKAP kolom sesuai database (18 kolom).
     * Model ini read-only, $fillable hanya untuk dokumentasi & IDE helper.
     *
     * @var array<int,string>
     */
    protected $fillable = [
        'kdloc',
        'nocif',
        'golcust',
        'hubbank',
        'jnsinstrumen',
        'norek',
        'tgleff',
        'tglexp',
        'jnsakad',
        'sifatinvestasi',
        'mtdbaghas',
        'nisbah',
        'rate_awal',
        'rate',
        'saldo',
        'byadm',
        'jumlah',
        'kelonggarantarik',
    ];

    /**
     * Casting tipe kolom (non-string saja).
     *
     * @var array<string,string>
     */
    protected $casts = [
        'nisbah' => 'decimal:2',
        'rate_awal' => 'decimal:2',
        'rate' => 'decimal:2',
        'saldo' => 'decimal:2',
        'byadm' => 'decimal:2',
        'jumlah' => 'decimal:2',
        'kelonggarantarik' => 'decimal:2',
    ];
}
