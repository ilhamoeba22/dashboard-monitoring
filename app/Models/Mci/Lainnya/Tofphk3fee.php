<?php

declare(strict_types=1);

namespace App\Models\Mci\Lainnya;

use App\Models\Mci\MciBaseModel;

/**
 * Model MCI: TOFPHK3FEE
 * --------------------------------------------------------------------------
 * Domain   : Lainnya
 * Tabel    : [dbo].[TOFPHK3FEE]
 * Kolom    : 15
 * Sumber   : Mapping_MCI_MAR26_01042026.sql / MCI_MAR26_01042026.xlsx
 *
 * CATATAN:
 *  - Model ini READ-ONLY (lihat trait ReadOnlyModel di MciBaseModel).
 *  - Koneksi default `dashboard_data` (SQL Server), otomatis ke DB bulan terbaru.
 *  - Kolom dengan spasi/karakter khusus diakses via:
 *    $model->getAttribute('NAMA KOLOM')  atau  $model->{'NAMA KOLOM'}.
 *
 * @property string|null $kdmitra type: varchar(10)
 * @property string|null $ddawal type: varchar(2)
 * @property string|null $ddakhir type: varchar(2)
 * @property string|null $fee_1 type: numeric(5)
 * @property string|null $fee_2 type: numeric(5)
 * @property string|null $kdfee type: varchar(2)
 * @property string|null $stsrec type: varchar(1)
 * @property string|null $inpuser type: varchar(10)
 * @property string|null $inptgljam type: varchar(14)
 * @property string|null $inpterm type: varchar(10)
 * @property string|null $autuser type: varchar(10)
 * @property string|null $auttgljam type: varchar(14)
 * @property string|null $autterm type: varchar(10)
 * @property string|null $golao type: varchar(2)
 * @property string|null $kdbiaya type: numeric(5)
 */
class Tofphk3fee extends MciBaseModel
{
    /**
     * Nama tabel (case-sensitive di SQL Server).
     */
    protected $table = 'TOFPHK3FEE';

    /**
     * Daftar LENGKAP kolom sesuai database (15 kolom).
     * Model ini read-only, $fillable hanya untuk dokumentasi & IDE helper.
     *
     * @var array<int,string>
     */
    protected $fillable = [
        'kdmitra',
        'ddawal',
        'ddakhir',
        'fee_1',
        'fee_2',
        'kdfee',
        'stsrec',
        'inpuser',
        'inptgljam',
        'inpterm',
        'autuser',
        'auttgljam',
        'autterm',
        'golao',
        'kdbiaya',
    ];

    /**
     * Casting tipe kolom (non-string saja).
     *
     * @var array<string,string>
     */
    protected $casts = [
        'fee_1' => 'decimal:2',
        'fee_2' => 'decimal:2',
        'kdbiaya' => 'decimal:2',
    ];
}
