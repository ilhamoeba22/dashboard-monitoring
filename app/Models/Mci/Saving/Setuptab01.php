<?php

namespace App\Models\Mci\Saving;

use App\Models\Mci\MciBaseModel;

/**
 * Model MCI: SETUPTAB01
 * --------------------------------------------------------------------------
 * Domain   : Saving
 * Tabel    : [dbo].[SETUPTAB01]
 * Kolom    : 22
 * Sumber   : Mapping_MCI_MAR26_01042026.sql / MCI_MAR26_01042026.xlsx
 *
 * CATATAN:
 *  - Model ini READ-ONLY (lihat trait ReadOnlyModel di MciBaseModel).
 *  - Koneksi default `dashboard_data` (SQL Server), otomatis ke DB bulan terbaru.
 *  - Kolom dengan spasi/karakter khusus diakses via:
 *    $model->getAttribute('NAMA KOLOM')  atau  $model->{'NAMA KOLOM'}.
 *
 * @property string $kdprd  type: varchar(2)
 * @property string|null $saldo1  type: numeric(9)
 * @property string|null $saldo2  type: numeric(9)
 * @property string|null $saldo3  type: numeric(9)
 * @property string|null $saldo4  type: numeric(9)
 * @property string|null $saldo5  type: numeric(9)
 * @property string|null $bonus1  type: numeric(5)
 * @property string|null $bonus2  type: numeric(5)
 * @property string|null $bonus3  type: numeric(5)
 * @property string|null $bonus4  type: numeric(5)
 * @property string|null $bonus5  type: numeric(5)
 * @property string|null $tgleff  type: varchar(8)
 * @property string|null $stsrec  type: varchar(1)
 * @property string|null $inpuser  type: varchar(10)
 * @property string|null $inptgl  type: varchar(14)
 * @property string|null $inpterm  type: varchar(10)
 * @property string|null $chguser  type: varchar(10)
 * @property string|null $chgtgl  type: varchar(14)
 * @property string|null $chgterm  type: varchar(10)
 * @property string|null $autuser  type: varchar(10)
 * @property string|null $auttgl  type: varchar(14)
 * @property string|null $autterm  type: varchar(10)
 */
class Setuptab01 extends MciBaseModel
{
    /**
     * Nama tabel (case-sensitive di SQL Server).
     */
    protected $table = 'SETUPTAB01';

    /**
     * Daftar LENGKAP kolom sesuai database (22 kolom).
     * Model ini read-only, $fillable hanya untuk dokumentasi & IDE helper.
     *
     * @var array<int,string>
     */
    protected $fillable = [
        'kdprd',
        'saldo1',
        'saldo2',
        'saldo3',
        'saldo4',
        'saldo5',
        'bonus1',
        'bonus2',
        'bonus3',
        'bonus4',
        'bonus5',
        'tgleff',
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
        'saldo1' => 'decimal:2',
        'saldo2' => 'decimal:2',
        'saldo3' => 'decimal:2',
        'saldo4' => 'decimal:2',
        'saldo5' => 'decimal:2',
        'bonus1' => 'decimal:2',
        'bonus2' => 'decimal:2',
        'bonus3' => 'decimal:2',
        'bonus4' => 'decimal:2',
        'bonus5' => 'decimal:2',
    ];
}
