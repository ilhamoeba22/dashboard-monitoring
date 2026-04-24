<?php

namespace App\Models\Mci\BagiHasil;

use App\Models\Mci\MciBaseModel;

/**
 * Model MCI: TOFTBHEQUIV
 * --------------------------------------------------------------------------
 * Domain   : Bagi Hasil
 * Tabel    : [dbo].[TOFTBHEQUIV]
 * Kolom    : 16
 * Sumber   : Mapping_MCI_MAR26_01042026.sql / MCI_MAR26_01042026.xlsx
 *
 * CATATAN:
 *  - Model ini READ-ONLY (lihat trait ReadOnlyModel di MciBaseModel).
 *  - Koneksi default `dashboard_data` (SQL Server), otomatis ke DB bulan terbaru.
 *  - Kolom dengan spasi/karakter khusus diakses via:
 *    $model->getAttribute('NAMA KOLOM')  atau  $model->{'NAMA KOLOM'}.
 *
 * @property string $periode  type: varchar(6)
 * @property string|null $kdgol  type: varchar(1)
 * @property string|null $kdrek  type: varchar(1)
 * @property string|null $kdprd  type: varchar(2)
 * @property string $kdloc  type: varchar(2)
 * @property string $nosbb  type: varchar(7)
 * @property string|null $nmsbb  type: varchar(30)
 * @property string|null $saldoeom  type: numeric(9)
 * @property string|null $saldoposisi  type: numeric(9)
 * @property string|null $saldohi  type: numeric(9)
 * @property string|null $saldorata  type: numeric(9)
 * @property string|null $nisbah  type: numeric(9)
 * @property string|null $spread  type: numeric(5)
 * @property string|null $equivrate  type: numeric(5)
 * @property string|null $baghas  type: numeric(9)
 * @property string|null $hari  type: numeric(5)
 */
class Toftbhequiv extends MciBaseModel
{
    /**
     * Nama tabel (case-sensitive di SQL Server).
     */
    protected $table = 'TOFTBHEQUIV';

    /**
     * Daftar LENGKAP kolom sesuai database (16 kolom).
     * Model ini read-only, $fillable hanya untuk dokumentasi & IDE helper.
     *
     * @var array<int,string>
     */
    protected $fillable = [
        'periode',
        'kdgol',
        'kdrek',
        'kdprd',
        'kdloc',
        'nosbb',
        'nmsbb',
        'saldoeom',
        'saldoposisi',
        'saldohi',
        'saldorata',
        'nisbah',
        'spread',
        'equivrate',
        'baghas',
        'hari',
    ];

    /**
     * Casting tipe kolom (non-string saja).
     *
     * @var array<string,string>
     */
    protected $casts = [
        'saldoeom' => 'decimal:2',
        'saldoposisi' => 'decimal:2',
        'saldohi' => 'decimal:2',
        'saldorata' => 'decimal:2',
        'nisbah' => 'decimal:2',
        'spread' => 'decimal:2',
        'equivrate' => 'decimal:2',
        'baghas' => 'decimal:2',
        'hari' => 'decimal:2',
    ];
}
