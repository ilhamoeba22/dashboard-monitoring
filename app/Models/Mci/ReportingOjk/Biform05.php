<?php

declare(strict_types=1);

namespace App\Models\Mci\ReportingOjk;

use App\Models\Mci\MciBaseModel;

/**
 * Model MCI: BIFORM05
 * --------------------------------------------------------------------------
 * Domain   : Reporting OJK/BI
 * Tabel    : [dbo].[BIFORM05]
 * Kolom    : 22
 * Sumber   : Mapping_MCI_MAR26_01042026.sql / MCI_MAR26_01042026.xlsx
 *
 * CATATAN:
 *  - Model ini READ-ONLY (lihat trait ReadOnlyModel di MciBaseModel).
 *  - Koneksi default `dashboard_data` (SQL Server), otomatis ke DB bulan terbaru.
 *  - Kolom dengan spasi/karakter khusus diakses via:
 *    $model->getAttribute('NAMA KOLOM')  atau  $model->{'NAMA KOLOM'}.
 *
 * @property string $kdloc type: varchar(2)
 * @property string $norek type: varchar(20)
 * @property string|null $sandibank type: varchar(9)
 * @property string|null $nmbank type: varchar(50)
 * @property string|null $hubbank type: char(1)
 * @property string|null $jnsops type: char(1)
 * @property string|null $jnsinstrumen type: char(1)
 * @property string|null $tglmulai type: varchar(8)
 * @property string|null $tglexp type: varchar(8)
 * @property string|null $sumberdana type: char(1)
 * @property string|null $jnsakad type: char(1)
 * @property string|null $mtdbaghas type: char(1)
 * @property string|null $nisbah type: numeric(5)
 * @property string|null $equivrateawal type: numeric(5)
 * @property string|null $equivrate type: numeric(5)
 * @property string|null $coll type: char(1)
 * @property string|null $stsbpmd type: char(1)
 * @property string|null $nom type: numeric(9)
 * @property string|null $nomblokir type: numeric(9)
 * @property string|null $pad type: numeric(9)
 * @property string|null $ppap type: numeric(9)
 * @property string|null $jns_ckpn type: char(1)
 */
class Biform05 extends MciBaseModel
{
    /**
     * Nama tabel (case-sensitive di SQL Server).
     */
    protected $table = 'BIFORM05';

    /**
     * Daftar LENGKAP kolom sesuai database (22 kolom).
     * Model ini read-only, $fillable hanya untuk dokumentasi & IDE helper.
     *
     * @var array<int,string>
     */
    protected $fillable = [
        'kdloc',
        'norek',
        'sandibank',
        'nmbank',
        'hubbank',
        'jnsops',
        'jnsinstrumen',
        'tglmulai',
        'tglexp',
        'sumberdana',
        'jnsakad',
        'mtdbaghas',
        'nisbah',
        'equivrateawal',
        'equivrate',
        'coll',
        'stsbpmd',
        'nom',
        'nomblokir',
        'pad',
        'ppap',
        'jns_ckpn',
    ];

    /**
     * Casting tipe kolom (non-string saja).
     *
     * @var array<string,string>
     */
    protected $casts = [
        'nisbah' => 'decimal:2',
        'equivrateawal' => 'decimal:2',
        'equivrate' => 'decimal:2',
        'nom' => 'decimal:2',
        'nomblokir' => 'decimal:2',
        'pad' => 'decimal:2',
        'ppap' => 'decimal:2',
    ];
}
