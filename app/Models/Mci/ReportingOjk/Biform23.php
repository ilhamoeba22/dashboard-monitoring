<?php

namespace App\Models\Mci\ReportingOjk;

use App\Models\Mci\MciBaseModel;

/**
 * Model MCI: BIFORM23
 * --------------------------------------------------------------------------
 * Domain   : Reporting OJK/BI
 * Tabel    : [dbo].[BIFORM23]
 * Kolom    : 25
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
 * @property string|null $jnsops  type: char(1)
 * @property string|null $hubbank  type: char(1)
 * @property string|null $jnsinstrumen  type: char(1)
 * @property string|null $norek  type: varchar(20)
 * @property string|null $sifatdana  type: char(1)
 * @property string|null $tgleff  type: varchar(8)
 * @property string|null $tglexp  type: varchar(8)
 * @property string|null $jnsakad  type: char(1)
 * @property string|null $sandidati  type: varchar(4)
 * @property string|null $mtdbaghas  type: char(1)
 * @property string|null $nisbah  type: numeric(5)
 * @property string|null $rate_awal  type: numeric(5)
 * @property string|null $rate  type: numeric(5)
 * @property string|null $saldo  type: numeric(9)
 * @property string|null $saldoblokir  type: numeric(9)
 * @property string|null $stsblokir  type: char(1)
 * @property string|null $noidentitas  type: varchar(18)
 * @property string|null $jns_pep  type: char(1)
 * @property string|null $risk_nasabah  type: char(1)
 * @property string|null $sts_data  type: char(1)
 * @property string|null $noid  type: varchar(18)
 * @property string|null $kdalasan  type: varchar(1)
 */
class Biform23 extends MciBaseModel
{
    /**
     * Nama tabel (case-sensitive di SQL Server).
     */
    protected $table = 'BIFORM23';

    /**
     * Daftar LENGKAP kolom sesuai database (25 kolom).
     * Model ini read-only, $fillable hanya untuk dokumentasi & IDE helper.
     *
     * @var array<int,string>
     */
    protected $fillable = [
        'kdloc',
        'nocif',
        'golcust',
        'jnsops',
        'hubbank',
        'jnsinstrumen',
        'norek',
        'sifatdana',
        'tgleff',
        'tglexp',
        'jnsakad',
        'sandidati',
        'mtdbaghas',
        'nisbah',
        'rate_awal',
        'rate',
        'saldo',
        'saldoblokir',
        'stsblokir',
        'noidentitas',
        'jns_pep',
        'risk_nasabah',
        'sts_data',
        'noid',
        'kdalasan',
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
        'saldoblokir' => 'decimal:2',
    ];
}
