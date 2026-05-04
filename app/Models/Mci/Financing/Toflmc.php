<?php

declare(strict_types=1);

namespace App\Models\Mci\Financing;

use App\Models\Mci\MciBaseModel;

/**
 * Model MCI: TOFLMC
 * --------------------------------------------------------------------------
 * Domain   : Financing / Loan
 * Tabel    : [dbo].[TOFLMC]
 * Kolom    : 18
 * Sumber   : Mapping_MCI_MAR26_01042026.sql / MCI_MAR26_01042026.xlsx
 *
 * CATATAN:
 *  - Model ini READ-ONLY (lihat trait ReadOnlyModel di MciBaseModel).
 *  - Koneksi default `dashboard_data` (SQL Server), otomatis ke DB bulan terbaru.
 *  - Kolom dengan spasi/karakter khusus diakses via:
 *    $model->getAttribute('NAMA KOLOM')  atau  $model->{'NAMA KOLOM'}.
 *
 * @property string $nokontrak type: varchar(11)
 * @property string|null $ttpum type: numeric(9)
 * @property string|null $ttpsewa type: numeric(9)
 * @property string|null $ttpset type: numeric(9)
 * @property string|null $dropmdl type: numeric(9)
 * @property string|null $dropmgn type: numeric(9)
 * @property string|null $angsmdl type: numeric(9)
 * @property string|null $angsmgn type: numeric(9)
 * @property string|null $angsdnd type: numeric(9)
 * @property string|null $angssewa type: numeric(9)
 * @property string|null $angske type: numeric(5)
 * @property string|null $lunasmdl type: numeric(9)
 * @property string|null $lunasmgn type: numeric(9)
 * @property string|null $discount type: numeric(9)
 * @property string|null $ststrn type: varchar(1)
 * @property string|null $stsrec type: varchar(1)
 * @property string|null $tgltagih type: varchar(8)
 * @property string|null $tgltrn type: varchar(10)
 */
class Toflmc extends MciBaseModel
{
    /**
     * Nama tabel (case-sensitive di SQL Server).
     */
    protected $table = 'TOFLMC';

    /**
     * Daftar LENGKAP kolom sesuai database (18 kolom).
     * Model ini read-only, $fillable hanya untuk dokumentasi & IDE helper.
     *
     * @var array<int,string>
     */
    protected $fillable = [
        'nokontrak',
        'ttpum',
        'ttpsewa',
        'ttpset',
        'dropmdl',
        'dropmgn',
        'angsmdl',
        'angsmgn',
        'angsdnd',
        'angssewa',
        'angske',
        'lunasmdl',
        'lunasmgn',
        'discount',
        'ststrn',
        'stsrec',
        'tgltagih',
        'tgltrn',
    ];

    /**
     * Casting tipe kolom (non-string saja).
     *
     * @var array<string,string>
     */
    protected $casts = [
        'ttpum' => 'decimal:2',
        'ttpsewa' => 'decimal:2',
        'ttpset' => 'decimal:2',
        'dropmdl' => 'decimal:2',
        'dropmgn' => 'decimal:2',
        'angsmdl' => 'decimal:2',
        'angsmgn' => 'decimal:2',
        'angsdnd' => 'decimal:2',
        'angssewa' => 'decimal:2',
        'angske' => 'decimal:2',
        'lunasmdl' => 'decimal:2',
        'lunasmgn' => 'decimal:2',
        'discount' => 'decimal:2',
    ];
}
