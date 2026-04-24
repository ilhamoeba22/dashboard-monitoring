<?php

namespace App\Models\Mci\Deposito;

use App\Models\Mci\MciBaseModel;

/**
 * Model MCI: TOFDEPTAX
 * --------------------------------------------------------------------------
 * Domain   : Deposito
 * Tabel    : [dbo].[TOFDEPTAX]
 * Kolom    : 19
 * Sumber   : Mapping_MCI_MAR26_01042026.sql / MCI_MAR26_01042026.xlsx
 *
 * CATATAN:
 *  - Model ini READ-ONLY (lihat trait ReadOnlyModel di MciBaseModel).
 *  - Koneksi default `dashboard_data` (SQL Server), otomatis ke DB bulan terbaru.
 *  - Kolom dengan spasi/karakter khusus diakses via:
 *    $model->getAttribute('NAMA KOLOM')  atau  $model->{'NAMA KOLOM'}.
 *
 * @property string $kdprd  type: varchar(2)
 * @property string $jkwaktu  type: numeric(5)
 * @property string $jnsjkwaktu  type: varchar(1)
 * @property string $noacc  type: varchar(11)
 * @property string $tglbayar  type: varchar(8)
 * @property string|null $nobilyet  type: varchar(10)
 * @property string|null $fnama  type: varchar(30)
 * @property string|null $nominal  type: numeric(9)
 * @property string|null $rate  type: numeric(5)
 * @property string|null $spread  type: numeric(5)
 * @property string|null $subsiditax  type: numeric(5)
 * @property string|null $bunga  type: numeric(9)
 * @property string|null $pajak  type: numeric(9)
 * @property string|null $subsidi  type: numeric(9)
 * @property string|null $noaccbng  type: varchar(11)
 * @property string|null $ststax  type: varchar(1)
 * @property string|null $sbbtax  type: varchar(11)
 * @property string|null $tglcair  type: varchar(8)
 * @property string|null $usercair  type: varchar(10)
 */
class Tofdeptax extends MciBaseModel
{
    /**
     * Nama tabel (case-sensitive di SQL Server).
     */
    protected $table = 'TOFDEPTAX';

    /**
     * Daftar LENGKAP kolom sesuai database (19 kolom).
     * Model ini read-only, $fillable hanya untuk dokumentasi & IDE helper.
     *
     * @var array<int,string>
     */
    protected $fillable = [
        'kdprd',
        'jkwaktu',
        'jnsjkwaktu',
        'noacc',
        'tglbayar',
        'nobilyet',
        'fnama',
        'nominal',
        'rate',
        'spread',
        'subsiditax',
        'bunga',
        'pajak',
        'subsidi',
        'noaccbng',
        'ststax',
        'sbbtax',
        'tglcair',
        'usercair',
    ];

    /**
     * Casting tipe kolom (non-string saja).
     *
     * @var array<string,string>
     */
    protected $casts = [
        'jkwaktu' => 'decimal:2',
        'nominal' => 'decimal:2',
        'rate' => 'decimal:2',
        'spread' => 'decimal:2',
        'subsiditax' => 'decimal:2',
        'bunga' => 'decimal:2',
        'pajak' => 'decimal:2',
        'subsidi' => 'decimal:2',
    ];
}
