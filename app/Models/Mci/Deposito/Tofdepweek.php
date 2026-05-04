<?php

declare(strict_types=1);

namespace App\Models\Mci\Deposito;

use App\Models\Mci\MciBaseModel;

/**
 * Model MCI: TOFDEPWEEK
 * --------------------------------------------------------------------------
 * Domain   : Deposito
 * Tabel    : [dbo].[TOFDEPWEEK]
 * Kolom    : 12
 * Sumber   : Mapping_MCI_MAR26_01042026.sql / MCI_MAR26_01042026.xlsx
 *
 * CATATAN:
 *  - Model ini READ-ONLY (lihat trait ReadOnlyModel di MciBaseModel).
 *  - Koneksi default `dashboard_data` (SQL Server), otomatis ke DB bulan terbaru.
 *  - Kolom dengan spasi/karakter khusus diakses via:
 *    $model->getAttribute('NAMA KOLOM')  atau  $model->{'NAMA KOLOM'}.
 *
 * @property string $tglweek type: char(8)
 * @property string $nodep type: varchar(11)
 * @property string $nocif type: varchar(9)
 * @property string|null $kdprd type: varchar(2)
 * @property string|null $kdloc type: varchar(2)
 * @property string|null $nomawal type: numeric(9)
 * @property string|null $nomrp type: numeric(9)
 * @property string|null $tglbuka type: varchar(8)
 * @property string|null $jkwaktu type: numeric(5)
 * @property string|null $jnsjkwaktu type: varchar(1)
 * @property string|null $tgleff type: varchar(8)
 * @property string|null $tgljtempo type: varchar(8)
 */
class Tofdepweek extends MciBaseModel
{
    /**
     * Nama tabel (case-sensitive di SQL Server).
     */
    protected $table = 'TOFDEPWEEK';

    /**
     * Daftar LENGKAP kolom sesuai database (12 kolom).
     * Model ini read-only, $fillable hanya untuk dokumentasi & IDE helper.
     *
     * @var array<int,string>
     */
    protected $fillable = [
        'tglweek',
        'nodep',
        'nocif',
        'kdprd',
        'kdloc',
        'nomawal',
        'nomrp',
        'tglbuka',
        'jkwaktu',
        'jnsjkwaktu',
        'tgleff',
        'tgljtempo',
    ];

    /**
     * Casting tipe kolom (non-string saja).
     *
     * @var array<string,string>
     */
    protected $casts = [
        'nomawal' => 'decimal:2',
        'nomrp' => 'decimal:2',
        'jkwaktu' => 'decimal:2',
    ];
}
