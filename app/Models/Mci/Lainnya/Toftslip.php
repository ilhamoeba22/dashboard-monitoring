<?php

declare(strict_types=1);

namespace App\Models\Mci\Lainnya;

use App\Models\Mci\MciBaseModel;

/**
 * Model MCI: TOFTSLIP
 * --------------------------------------------------------------------------
 * Domain   : Lainnya
 * Tabel    : [dbo].[TOFTSLIP]
 * Kolom    : 10
 * Sumber   : Mapping_MCI_MAR26_01042026.sql / MCI_MAR26_01042026.xlsx
 *
 * CATATAN:
 *  - Model ini READ-ONLY (lihat trait ReadOnlyModel di MciBaseModel).
 *  - Koneksi default `dashboard_data` (SQL Server), otomatis ke DB bulan terbaru.
 *  - Kolom dengan spasi/karakter khusus diakses via:
 *    $model->getAttribute('NAMA KOLOM')  atau  $model->{'NAMA KOLOM'}.
 *
 * @property string|null $kdtrn type: varchar(4)
 * @property string|null $nmfield type: varchar(20)
 * @property string|null $x type: numeric(9)
 * @property string|null $y type: numeric(9)
 * @property string|null $w type: numeric(9)
 * @property string|null $h type: numeric(9)
 * @property string|null $stsrec type: varchar(1)
 * @property string|null $inpuser type: varchar(10)
 * @property string|null $inptgljam type: varchar(14)
 * @property string|null $inpterm type: varchar(10)
 */
class Toftslip extends MciBaseModel
{
    /**
     * Nama tabel (case-sensitive di SQL Server).
     */
    protected $table = 'TOFTSLIP';

    /**
     * Daftar LENGKAP kolom sesuai database (10 kolom).
     * Model ini read-only, $fillable hanya untuk dokumentasi & IDE helper.
     *
     * @var array<int,string>
     */
    protected $fillable = [
        'kdtrn',
        'nmfield',
        'x',
        'y',
        'w',
        'h',
        'stsrec',
        'inpuser',
        'inptgljam',
        'inpterm',
    ];

    /**
     * Casting tipe kolom (non-string saja).
     *
     * @var array<string,string>
     */
    protected $casts = [
        'x' => 'decimal:2',
        'y' => 'decimal:2',
        'w' => 'decimal:2',
        'h' => 'decimal:2',
    ];
}
