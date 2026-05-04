<?php

declare(strict_types=1);

namespace App\Models\Mci\Lainnya;

use App\Models\Mci\MciBaseModel;

/**
 * Model MCI: TMPRAKOFF
 * --------------------------------------------------------------------------
 * Domain   : Lainnya
 * Tabel    : [dbo].[TMPRAKOFF]
 * Kolom    : 13
 * Sumber   : Mapping_MCI_MAR26_01042026.sql / MCI_MAR26_01042026.xlsx
 *
 * CATATAN:
 *  - Model ini READ-ONLY (lihat trait ReadOnlyModel di MciBaseModel).
 *  - Koneksi default `dashboard_data` (SQL Server), otomatis ke DB bulan terbaru.
 *  - Kolom dengan spasi/karakter khusus diakses via:
 *    $model->getAttribute('NAMA KOLOM')  atau  $model->{'NAMA KOLOM'}.
 *
 * @property string $batch type: numeric(5)
 * @property string|null $kdloc type: char(2)
 * @property string|null $nosbb type: varchar(7)
 * @property string|null $saldo_1 type: numeric(9)
 * @property string|null $kdloc_lawan type: char(2)
 * @property string|null $nosbb_lawan type: varchar(7)
 * @property string|null $saldo_2 type: numeric(9)
 * @property string|null $saldonett type: numeric(9)
 * @property string|null $nobb type: varchar(7)
 * @property string|null $nobb_lawan type: varchar(7)
 * @property string|null $saldo_3 type: numeric(9)
 * @property string|null $saldo_4 type: numeric(9)
 * @property string|null $saldonett2 type: numeric(9)
 */
class Tmprakoff extends MciBaseModel
{
    /**
     * Nama tabel (case-sensitive di SQL Server).
     */
    protected $table = 'TMPRAKOFF';

    /**
     * Daftar LENGKAP kolom sesuai database (13 kolom).
     * Model ini read-only, $fillable hanya untuk dokumentasi & IDE helper.
     *
     * @var array<int,string>
     */
    protected $fillable = [
        'batch',
        'kdloc',
        'nosbb',
        'saldo_1',
        'kdloc_lawan',
        'nosbb_lawan',
        'saldo_2',
        'saldonett',
        'nobb',
        'nobb_lawan',
        'saldo_3',
        'saldo_4',
        'saldonett2',
    ];

    /**
     * Casting tipe kolom (non-string saja).
     *
     * @var array<string,string>
     */
    protected $casts = [
        'batch' => 'decimal:2',
        'saldo_1' => 'decimal:2',
        'saldo_2' => 'decimal:2',
        'saldonett' => 'decimal:2',
        'saldo_3' => 'decimal:2',
        'saldo_4' => 'decimal:2',
        'saldonett2' => 'decimal:2',
    ];
}
