<?php

namespace App\Models\Mci\ReportingOjk;

use App\Models\Mci\MciBaseModel;

/**
 * Model MCI: BIFORM30
 * --------------------------------------------------------------------------
 * Domain   : Reporting OJK/BI
 * Tabel    : [dbo].[BIFORM30]
 * Kolom    : 23
 * Sumber   : Mapping_MCI_MAR26_01042026.sql / MCI_MAR26_01042026.xlsx
 *
 * CATATAN:
 *  - Model ini READ-ONLY (lihat trait ReadOnlyModel di MciBaseModel).
 *  - Koneksi default `dashboard_data` (SQL Server), otomatis ke DB bulan terbaru.
 *  - Kolom dengan spasi/karakter khusus diakses via:
 *    $model->getAttribute('NAMA KOLOM')  atau  $model->{'NAMA KOLOM'}.
 *
 * @property string $kdloc  type: char(2)
 * @property string $dpk_nocif  type: varchar(10)
 * @property string|null $noid  type: varchar(16)
 * @property string|null $dpk_golcust  type: char(6)
 * @property string|null $dpk_hubbank  type: char(1)
 * @property string|null $dpk_rate  type: numeric(5)
 * @property string|null $dpk_nom  type: numeric(9)
 * @property string|null $pyd_nocif  type: varchar(10)
 * @property string|null $gol_penyalur  type: char(2)
 * @property string|null $pyd_golcust  type: char(6)
 * @property string|null $pyd_hubbank  type: char(1)
 * @property string|null $jnspenyaluran  type: varchar(2)
 * @property string|null $qty_rek  type: numeric(5)
 * @property string|null $tgleff  type: varchar(8)
 * @property string|null $tglexp  type: varchar(8)
 * @property string|null $sandidati  type: varchar(4)
 * @property string|null $jnsguna  type: char(1)
 * @property string|null $sekon  type: varchar(6)
 * @property string|null $nisbah  type: numeric(5)
 * @property string|null $pyd_rate  type: numeric(5)
 * @property string|null $pyd_nom  type: numeric(9)
 * @property string|null $akum_tgk  type: numeric(9)
 * @property string|null $nom_tgk  type: numeric(9)
 */
class Biform30 extends MciBaseModel
{
    /**
     * Nama tabel (case-sensitive di SQL Server).
     */
    protected $table = 'BIFORM30';

    /**
     * Daftar LENGKAP kolom sesuai database (23 kolom).
     * Model ini read-only, $fillable hanya untuk dokumentasi & IDE helper.
     *
     * @var array<int,string>
     */
    protected $fillable = [
        'kdloc',
        'dpk_nocif',
        'noid',
        'dpk_golcust',
        'dpk_hubbank',
        'dpk_rate',
        'dpk_nom',
        'pyd_nocif',
        'gol_penyalur',
        'pyd_golcust',
        'pyd_hubbank',
        'jnspenyaluran',
        'qty_rek',
        'tgleff',
        'tglexp',
        'sandidati',
        'jnsguna',
        'sekon',
        'nisbah',
        'pyd_rate',
        'pyd_nom',
        'akum_tgk',
        'nom_tgk',
    ];

    /**
     * Casting tipe kolom (non-string saja).
     *
     * @var array<string,string>
     */
    protected $casts = [
        'dpk_rate' => 'decimal:2',
        'dpk_nom' => 'decimal:2',
        'qty_rek' => 'decimal:2',
        'nisbah' => 'decimal:2',
        'pyd_rate' => 'decimal:2',
        'pyd_nom' => 'decimal:2',
        'akum_tgk' => 'decimal:2',
        'nom_tgk' => 'decimal:2',
    ];
}
