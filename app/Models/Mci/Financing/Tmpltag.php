<?php

declare(strict_types=1);

namespace App\Models\Mci\Financing;

use App\Models\Mci\MciBaseModel;

/**
 * Model MCI: TMPLTAG
 * --------------------------------------------------------------------------
 * Domain   : Financing / Loan
 * Tabel    : [dbo].[TMPLTAG]
 * Kolom    : 16
 * Sumber   : Mapping_MCI_MAR26_01042026.sql / MCI_MAR26_01042026.xlsx
 *
 * CATATAN:
 *  - Model ini READ-ONLY (lihat trait ReadOnlyModel di MciBaseModel).
 *  - Koneksi default `dashboard_data` (SQL Server), otomatis ke DB bulan terbaru.
 *  - Kolom dengan spasi/karakter khusus diakses via:
 *    $model->getAttribute('NAMA KOLOM')  atau  $model->{'NAMA KOLOM'}.
 *
 * @property string|null $nokontrak type: varchar(11)
 * @property string|null $nama type: varchar(40)
 * @property string|null $pokpby type: varchar(2)
 * @property string|null $kdprd type: varchar(2)
 * @property string|null $kdaoh type: varchar(10)
 * @property string|null $osmdlc type: numeric(9)
 * @property string|null $osmgnc type: numeric(9)
 * @property string|null $tag_pok_lalu type: numeric(9)
 * @property string|null $tag_mgn_lalu type: numeric(9)
 * @property string|null $bln_tag_pok type: numeric(5)
 * @property string|null $bln_tag_mgn type: numeric(9)
 * @property string|null $tag_pok type: numeric(9)
 * @property string|null $tag_mgn type: numeric(9)
 * @property string|null $tgl type: varchar(2)
 * @property string|null $kdloc type: varchar(2)
 * @property string|null $inpuser type: varchar(10)
 */
class Tmpltag extends MciBaseModel
{
    /**
     * Nama tabel (case-sensitive di SQL Server).
     */
    protected $table = 'TMPLTAG';

    /**
     * Daftar LENGKAP kolom sesuai database (16 kolom).
     * Model ini read-only, $fillable hanya untuk dokumentasi & IDE helper.
     *
     * @var array<int,string>
     */
    protected $fillable = [
        'nokontrak',
        'nama',
        'pokpby',
        'kdprd',
        'kdaoh',
        'osmdlc',
        'osmgnc',
        'tag_pok_lalu',
        'tag_mgn_lalu',
        'bln_tag_pok',
        'bln_tag_mgn',
        'tag_pok',
        'tag_mgn',
        'tgl',
        'kdloc',
        'inpuser',
    ];

    /**
     * Casting tipe kolom (non-string saja).
     *
     * @var array<string,string>
     */
    protected $casts = [
        'osmdlc' => 'decimal:2',
        'osmgnc' => 'decimal:2',
        'tag_pok_lalu' => 'decimal:2',
        'tag_mgn_lalu' => 'decimal:2',
        'bln_tag_pok' => 'decimal:2',
        'bln_tag_mgn' => 'decimal:2',
        'tag_pok' => 'decimal:2',
        'tag_mgn' => 'decimal:2',
    ];
}
