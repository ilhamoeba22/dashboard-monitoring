<?php

declare(strict_types=1);

namespace App\Models\Mci\Cif;

use App\Models\Mci\MciBaseModel;

/**
 * Model MCI: MKYCP
 * --------------------------------------------------------------------------
 * Domain   : CIF / Customer
 * Tabel    : [dbo].[MKYCP]
 * Kolom    : 27
 * Sumber   : Mapping_MCI_MAR26_01042026.sql / MCI_MAR26_01042026.xlsx
 *
 * CATATAN:
 *  - Model ini READ-ONLY (lihat trait ReadOnlyModel di MciBaseModel).
 *  - Koneksi default `dashboard_data` (SQL Server), otomatis ke DB bulan terbaru.
 *  - Kolom dengan spasi/karakter khusus diakses via:
 *    $model->getAttribute('NAMA KOLOM')  atau  $model->{'NAMA KOLOM'}.
 *
 * @property string $kdac type: varchar(1)
 * @property string $noac type: varchar(11)
 * @property string $nocif type: varchar(9)
 * @property string|null $dana type: varchar(1)
 * @property string|null $tujuan type: varchar(8)
 * @property string|null $fmtsci type: numeric(5)
 * @property string|null $fmtsco type: numeric(5)
 * @property string|null $fmtsnci type: numeric(5)
 * @property string|null $fmtsnco type: numeric(5)
 * @property string|null $nommin type: numeric(9)
 * @property string|null $nommax type: numeric(9)
 * @property string|null $kdcab type: varchar(3)
 * @property string|null $kdloc type: varchar(2)
 * @property string|null $stsrec type: varchar(1)
 * @property string|null $inpuser type: varchar(10)
 * @property string|null $inptgl type: varchar(14)
 * @property string|null $inpterm type: varchar(10)
 * @property string|null $chguser type: varchar(10)
 * @property string|null $chgtgl type: varchar(14)
 * @property string|null $chgterm type: varchar(10)
 * @property string|null $autuser type: varchar(10)
 * @property string|null $auttgl type: varchar(14)
 * @property string|null $autterm type: varchar(10)
 * @property string|null $ket type: varchar(150)
 * @property string|null $trx_nommax_tunai type: numeric(9)
 * @property string|null $trx_nommax_nontunai type: numeric(9)
 * @property string|null $tujuan_lainnya type: varchar(10)
 */
class Mkycp extends MciBaseModel
{
    /**
     * Nama tabel (case-sensitive di SQL Server).
     */
    protected $table = 'MKYCP';

    /**
     * Daftar LENGKAP kolom sesuai database (27 kolom).
     * Model ini read-only, $fillable hanya untuk dokumentasi & IDE helper.
     *
     * @var array<int,string>
     */
    protected $fillable = [
        'kdac',
        'noac',
        'nocif',
        'dana',
        'tujuan',
        'fmtsci',
        'fmtsco',
        'fmtsnci',
        'fmtsnco',
        'nommin',
        'nommax',
        'kdcab',
        'kdloc',
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
        'ket',
        'trx_nommax_tunai',
        'trx_nommax_nontunai',
        'tujuan_lainnya',
    ];

    /**
     * Casting tipe kolom (non-string saja).
     *
     * @var array<string,string>
     */
    protected $casts = [
        'fmtsci' => 'decimal:2',
        'fmtsco' => 'decimal:2',
        'fmtsnci' => 'decimal:2',
        'fmtsnco' => 'decimal:2',
        'nommin' => 'decimal:2',
        'nommax' => 'decimal:2',
        'trx_nommax_tunai' => 'decimal:2',
        'trx_nommax_nontunai' => 'decimal:2',
    ];
}
