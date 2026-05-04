<?php

declare(strict_types=1);

namespace App\Models\Mci\Ppap;

use App\Models\Mci\MciBaseModel;

/**
 * Model MCI: pbcatcol
 * --------------------------------------------------------------------------
 * Domain   : PPAP / DPD / Coll
 * Tabel    : [dbo].[pbcatcol]
 * Kolom    : 20
 * Sumber   : Mapping_MCI_MAR26_01042026.sql / MCI_MAR26_01042026.xlsx
 *
 * CATATAN:
 *  - Model ini READ-ONLY (lihat trait ReadOnlyModel di MciBaseModel).
 *  - Koneksi default `dashboard_data` (SQL Server), otomatis ke DB bulan terbaru.
 *  - Kolom dengan spasi/karakter khusus diakses via:
 *    $model->getAttribute('NAMA KOLOM')  atau  $model->{'NAMA KOLOM'}.
 *
 * @property string|null $pbc_tnam type: char(30)
 * @property int|null $pbc_tid type: int(4)
 * @property string|null $pbc_ownr type: char(30)
 * @property string|null $pbc_cnam type: char(30)
 * @property int|null $pbc_cid type: smallint(2)
 * @property string|null $pbc_labl type: varchar(254)
 * @property int|null $pbc_lpos type: smallint(2)
 * @property string|null $pbc_hdr type: varchar(254)
 * @property int|null $pbc_hpos type: smallint(2)
 * @property int|null $pbc_jtfy type: smallint(2)
 * @property string|null $pbc_mask type: varchar(31)
 * @property int|null $pbc_case type: smallint(2)
 * @property int|null $pbc_hght type: smallint(2)
 * @property int|null $pbc_wdth type: smallint(2)
 * @property string|null $pbc_ptrn type: varchar(31)
 * @property string|null $pbc_bmap type: char(1)
 * @property string|null $pbc_init type: varchar(254)
 * @property string|null $pbc_cmnt type: varchar(254)
 * @property string|null $pbc_edit type: varchar(31)
 * @property string|null $pbc_tag type: varchar(254)
 */
class Pbcatcol extends MciBaseModel
{
    /**
     * Nama tabel (case-sensitive di SQL Server).
     */
    protected $table = 'pbcatcol';

    /**
     * Daftar LENGKAP kolom sesuai database (20 kolom).
     * Model ini read-only, $fillable hanya untuk dokumentasi & IDE helper.
     *
     * @var array<int,string>
     */
    protected $fillable = [
        'pbc_tnam',
        'pbc_tid',
        'pbc_ownr',
        'pbc_cnam',
        'pbc_cid',
        'pbc_labl',
        'pbc_lpos',
        'pbc_hdr',
        'pbc_hpos',
        'pbc_jtfy',
        'pbc_mask',
        'pbc_case',
        'pbc_hght',
        'pbc_wdth',
        'pbc_ptrn',
        'pbc_bmap',
        'pbc_init',
        'pbc_cmnt',
        'pbc_edit',
        'pbc_tag',
    ];

    /**
     * Casting tipe kolom (non-string saja).
     *
     * @var array<string,string>
     */
    protected $casts = [
        'pbc_tid' => 'integer',
        'pbc_cid' => 'integer',
        'pbc_lpos' => 'integer',
        'pbc_hpos' => 'integer',
        'pbc_jtfy' => 'integer',
        'pbc_case' => 'integer',
        'pbc_hght' => 'integer',
        'pbc_wdth' => 'integer',
    ];
}
