<?php

declare(strict_types=1);

namespace App\Models\Mci\BagiHasil;

use App\Models\Mci\MciBaseModel;

/**
 * Model MCI: NISBAHPROG
 * --------------------------------------------------------------------------
 * Domain   : Bagi Hasil
 * Tabel    : [dbo].[NISBAHPROG]
 * Kolom    : 32
 * Sumber   : Mapping_MCI_MAR26_01042026.sql / MCI_MAR26_01042026.xlsx
 *
 * CATATAN:
 *  - Model ini READ-ONLY (lihat trait ReadOnlyModel di MciBaseModel).
 *  - Koneksi default `dashboard_data` (SQL Server), otomatis ke DB bulan terbaru.
 *  - Kolom dengan spasi/karakter khusus diakses via:
 *    $model->getAttribute('NAMA KOLOM')  atau  $model->{'NAMA KOLOM'}.
 *
 * @property string $kdprd type: varchar(2)
 * @property string|null $saldmin1 type: numeric(9)
 * @property string|null $saldmax1 type: numeric(9)
 * @property string|null $saldmin2 type: numeric(9)
 * @property string|null $saldmax2 type: numeric(9)
 * @property string|null $saldmin3 type: numeric(9)
 * @property string|null $saldmax3 type: numeric(9)
 * @property string|null $saldmin4 type: numeric(9)
 * @property string|null $saldmax4 type: numeric(9)
 * @property string|null $saldmin5 type: numeric(9)
 * @property string|null $saldmax5 type: numeric(9)
 * @property string|null $rate1 type: numeric(5)
 * @property string|null $rate2 type: numeric(5)
 * @property string|null $rate3 type: numeric(5)
 * @property string|null $rate4 type: numeric(5)
 * @property string|null $rate5 type: numeric(5)
 * @property string|null $rate1n type: numeric(5)
 * @property string|null $rate2n type: numeric(5)
 * @property string|null $rate3n type: numeric(5)
 * @property string|null $rate4n type: numeric(5)
 * @property string|null $rate5n type: numeric(5)
 * @property string|null $berlaku type: varchar(8)
 * @property string|null $stsrec type: varchar(1)
 * @property string|null $inpuser type: varchar(10)
 * @property string|null $inptgl type: varchar(14)
 * @property string|null $inpterm type: varchar(10)
 * @property string|null $chguser type: varchar(10)
 * @property string $chgtgl type: varchar(14)
 * @property string|null $chgterm type: varchar(10)
 * @property string|null $autuser type: varchar(10)
 * @property string|null $auttgl type: varchar(14)
 * @property string|null $autterm type: varchar(10)
 */
class Nisbahprog extends MciBaseModel
{
    /**
     * Nama tabel (case-sensitive di SQL Server).
     */
    protected $table = 'NISBAHPROG';

    /**
     * Daftar LENGKAP kolom sesuai database (32 kolom).
     * Model ini read-only, $fillable hanya untuk dokumentasi & IDE helper.
     *
     * @var array<int,string>
     */
    protected $fillable = [
        'kdprd',
        'saldmin1',
        'saldmax1',
        'saldmin2',
        'saldmax2',
        'saldmin3',
        'saldmax3',
        'saldmin4',
        'saldmax4',
        'saldmin5',
        'saldmax5',
        'rate1',
        'rate2',
        'rate3',
        'rate4',
        'rate5',
        'rate1n',
        'rate2n',
        'rate3n',
        'rate4n',
        'rate5n',
        'berlaku',
        'stsrec',
        'inpuser',
        'inptgl',
        'inpterm',
        'chguser',
        'chgtgl',
        'chgterm',
        'autuser',
        'auttgl',
        'autterm',
    ];

    /**
     * Casting tipe kolom (non-string saja).
     *
     * @var array<string,string>
     */
    protected $casts = [
        'saldmin1' => 'decimal:2',
        'saldmax1' => 'decimal:2',
        'saldmin2' => 'decimal:2',
        'saldmax2' => 'decimal:2',
        'saldmin3' => 'decimal:2',
        'saldmax3' => 'decimal:2',
        'saldmin4' => 'decimal:2',
        'saldmax4' => 'decimal:2',
        'saldmin5' => 'decimal:2',
        'saldmax5' => 'decimal:2',
        'rate1' => 'decimal:2',
        'rate2' => 'decimal:2',
        'rate3' => 'decimal:2',
        'rate4' => 'decimal:2',
        'rate5' => 'decimal:2',
        'rate1n' => 'decimal:2',
        'rate2n' => 'decimal:2',
        'rate3n' => 'decimal:2',
        'rate4n' => 'decimal:2',
        'rate5n' => 'decimal:2',
    ];
}
