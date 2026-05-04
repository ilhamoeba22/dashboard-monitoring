<?php

declare(strict_types=1);

namespace App\Models\Mci\BagiHasil;

use App\Models\Mci\MciBaseModel;

/**
 * Model MCI: TOFBHEOM
 * --------------------------------------------------------------------------
 * Domain   : Bagi Hasil
 * Tabel    : [dbo].[TOFBHEOM]
 * Kolom    : 15
 * Sumber   : Mapping_MCI_MAR26_01042026.sql / MCI_MAR26_01042026.xlsx
 *
 * CATATAN:
 *  - Model ini READ-ONLY (lihat trait ReadOnlyModel di MciBaseModel).
 *  - Koneksi default `dashboard_data` (SQL Server), otomatis ke DB bulan terbaru.
 *  - Kolom dengan spasi/karakter khusus diakses via:
 *    $model->getAttribute('NAMA KOLOM')  atau  $model->{'NAMA KOLOM'}.
 *
 * @property string $periode type: varchar(6)
 * @property string|null $nosbb type: varchar(11)
 * @property string|null $kdcab type: varchar(3)
 * @property string|null $kdloc type: varchar(2)
 * @property string|null $dpk type: numeric(9)
 * @property string|null $pyd type: numeric(9)
 * @property string|null $revenue type: numeric(9)
 * @property string|null $pengurang type: numeric(9)
 * @property string|null $ijarah type: numeric(9)
 * @property string|null $baghas type: numeric(9)
 * @property string|null $akumdpk type: numeric(9)
 * @property string|null $akumpyd type: numeric(9)
 * @property string|null $hari type: numeric(5)
 * @property string|null $dpk_tab type: numeric(9)
 * @property string|null $dpk_dep type: numeric(9)
 */
class Tofbheom extends MciBaseModel
{
    /**
     * Nama tabel (case-sensitive di SQL Server).
     */
    protected $table = 'TOFBHEOM';

    /**
     * Daftar LENGKAP kolom sesuai database (15 kolom).
     * Model ini read-only, $fillable hanya untuk dokumentasi & IDE helper.
     *
     * @var array<int,string>
     */
    protected $fillable = [
        'periode',
        'nosbb',
        'kdcab',
        'kdloc',
        'dpk',
        'pyd',
        'revenue',
        'pengurang',
        'ijarah',
        'baghas',
        'akumdpk',
        'akumpyd',
        'hari',
        'dpk_tab',
        'dpk_dep',
    ];

    /**
     * Casting tipe kolom (non-string saja).
     *
     * @var array<string,string>
     */
    protected $casts = [
        'dpk' => 'decimal:2',
        'pyd' => 'decimal:2',
        'revenue' => 'decimal:2',
        'pengurang' => 'decimal:2',
        'ijarah' => 'decimal:2',
        'baghas' => 'decimal:2',
        'akumdpk' => 'decimal:2',
        'akumpyd' => 'decimal:2',
        'hari' => 'decimal:2',
        'dpk_tab' => 'decimal:2',
        'dpk_dep' => 'decimal:2',
    ];
}
