<?php

namespace App\Models\Mci\BagiHasil;

use App\Models\Mci\MciBaseModel;

/**
 * Model MCI: TOFBAGHASR
 * --------------------------------------------------------------------------
 * Domain   : Bagi Hasil
 * Tabel    : [dbo].[TOFBAGHASR]
 * Kolom    : 17
 * Sumber   : Mapping_MCI_MAR26_01042026.sql / MCI_MAR26_01042026.xlsx
 *
 * CATATAN:
 *  - Model ini READ-ONLY (lihat trait ReadOnlyModel di MciBaseModel).
 *  - Koneksi default `dashboard_data` (SQL Server), otomatis ke DB bulan terbaru.
 *  - Kolom dengan spasi/karakter khusus diakses via:
 *    $model->getAttribute('NAMA KOLOM')  atau  $model->{'NAMA KOLOM'}.
 *
 * @property string $tgltrn  type: varchar(8)
 * @property string|null $kdcab  type: varchar(3)
 * @property string|null $nosbb  type: varchar(11)
 * @property string|null $kdac  type: varchar(1)
 * @property string|null $kdprd  type: varchar(2)
 * @property string|null $cc  type: varchar(2)
 * @property string|null $jw  type: numeric(5)
 * @property string|null $jnsjw  type: varchar(1)
 * @property string|null $totdana  type: numeric(9)
 * @property string|null $danarata  type: numeric(9)
 * @property string|null $bobot  type: numeric(5)
 * @property string|null $danatbg  type: numeric(9)
 * @property string|null $dapat  type: numeric(9)
 * @property string|null $biaya  type: numeric(9)
 * @property string|null $nisbah  type: numeric(5)
 * @property string|null $baghas  type: numeric(9)
 * @property string|null $equiv  type: numeric(5)
 */
class Tofbaghasr extends MciBaseModel
{
    /**
     * Nama tabel (case-sensitive di SQL Server).
     */
    protected $table = 'TOFBAGHASR';

    /**
     * Daftar LENGKAP kolom sesuai database (17 kolom).
     * Model ini read-only, $fillable hanya untuk dokumentasi & IDE helper.
     *
     * @var array<int,string>
     */
    protected $fillable = [
        'tgltrn',
        'kdcab',
        'nosbb',
        'kdac',
        'kdprd',
        'cc',
        'jw',
        'jnsjw',
        'totdana',
        'danarata',
        'bobot',
        'danatbg',
        'dapat',
        'biaya',
        'nisbah',
        'baghas',
        'equiv',
    ];

    /**
     * Casting tipe kolom (non-string saja).
     *
     * @var array<string,string>
     */
    protected $casts = [
        'jw' => 'decimal:2',
        'totdana' => 'decimal:2',
        'danarata' => 'decimal:2',
        'bobot' => 'decimal:2',
        'danatbg' => 'decimal:2',
        'dapat' => 'decimal:2',
        'biaya' => 'decimal:2',
        'nisbah' => 'decimal:2',
        'baghas' => 'decimal:2',
        'equiv' => 'decimal:2',
    ];
}
