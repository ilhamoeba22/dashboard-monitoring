<?php

declare(strict_types=1);

namespace App\Models\Mci\Lainnya;

use App\Models\Mci\MciBaseModel;

/**
 * Model MCI: TOFTFEEAGEN
 * --------------------------------------------------------------------------
 * Domain   : Lainnya
 * Tabel    : [dbo].[TOFTFEEAGEN]
 * Kolom    : 23
 * Sumber   : Mapping_MCI_MAR26_01042026.sql / MCI_MAR26_01042026.xlsx
 *
 * CATATAN:
 *  - Model ini READ-ONLY (lihat trait ReadOnlyModel di MciBaseModel).
 *  - Koneksi default `dashboard_data` (SQL Server), otomatis ke DB bulan terbaru.
 *  - Kolom dengan spasi/karakter khusus diakses via:
 *    $model->getAttribute('NAMA KOLOM')  atau  $model->{'NAMA KOLOM'}.
 *
 * @property int $ID type: bigint(8)
 * @property string $kdprog type: char(2)
 * @property string|null $ket type: varchar(50)
 * @property string $fee type: numeric(5)
 * @property string|null $kdfee type: char(1)
 * @property string|null $tglexp type: varchar(8)
 * @property string|null $coll_1 type: char(1)
 * @property string|null $coll_2 type: char(1)
 * @property string|null $sbbfee type: varchar(7)
 * @property string|null $tax type: numeric(5)
 * @property string|null $sbbtax type: varchar(7)
 * @property string|null $kdpost type: char(1)
 * @property string|null $tglpost type: numeric(5)
 * @property string|null $stsrec type: char(1)
 * @property string|null $inpuser type: varchar(10)
 * @property string|null $inptgljam type: varchar(14)
 * @property string|null $inpterm type: varchar(10)
 * @property string|null $chguser type: varchar(10)
 * @property string|null $chgtgljam type: varchar(14)
 * @property string|null $chgterm type: varchar(10)
 * @property string|null $autuser type: varchar(10)
 * @property string|null $auttgljam type: varchar(14)
 * @property string|null $autterm type: varchar(10)
 */
class Toftfeeagen extends MciBaseModel
{
    /**
     * Nama tabel (case-sensitive di SQL Server).
     */
    protected $table = 'TOFTFEEAGEN';

    /**
     * Daftar LENGKAP kolom sesuai database (23 kolom).
     * Model ini read-only, $fillable hanya untuk dokumentasi & IDE helper.
     *
     * @var array<int,string>
     */
    protected $fillable = [
        'ID',
        'kdprog',
        'ket',
        'fee',
        'kdfee',
        'tglexp',
        'coll_1',
        'coll_2',
        'sbbfee',
        'tax',
        'sbbtax',
        'kdpost',
        'tglpost',
        'stsrec',
        'inpuser',
        'inptgljam',
        'inpterm',
        'chguser',
        'chgtgljam',
        'chgterm',
        'autuser',
        'auttgljam',
        'autterm',
    ];

    /**
     * Casting tipe kolom (non-string saja).
     *
     * @var array<string,string>
     */
    protected $casts = [
        'ID' => 'integer',
        'fee' => 'decimal:2',
        'tax' => 'decimal:2',
        'tglpost' => 'decimal:2',
    ];
}
