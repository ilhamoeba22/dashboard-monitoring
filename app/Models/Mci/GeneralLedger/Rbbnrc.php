<?php

declare(strict_types=1);

namespace App\Models\Mci\GeneralLedger;

use App\Models\Mci\MciBaseModel;

/**
 * Model MCI: RBBNRC
 * --------------------------------------------------------------------------
 * Domain   : GL / Accounting
 * Tabel    : [dbo].[RBBNRC]
 * Kolom    : 20
 * Sumber   : Mapping_MCI_MAR26_01042026.sql / MCI_MAR26_01042026.xlsx
 *
 * CATATAN:
 *  - Model ini READ-ONLY (lihat trait ReadOnlyModel di MciBaseModel).
 *  - Koneksi default `dashboard_data` (SQL Server), otomatis ke DB bulan terbaru.
 *  - Kolom dengan spasi/karakter khusus diakses via:
 *    $model->getAttribute('NAMA KOLOM')  atau  $model->{'NAMA KOLOM'}.
 *
 * @property string $thn type: varchar(4)
 * @property string $nosbb type: varchar(7)
 * @property string|null $sandibi type: varchar(5)
 * @property string|null $kdrbb type: varchar(14)
 * @property string|null $kdds type: varchar(14)
 * @property string|null $kdds2 type: varchar(14)
 * @property string|null $declalu type: numeric(9)
 * @property string|null $rbb_jan type: numeric(9)
 * @property string|null $rbb_feb type: numeric(9)
 * @property string|null $rbb_mar type: numeric(9)
 * @property string|null $rbb_apr type: numeric(9)
 * @property string|null $rbb_mei type: numeric(9)
 * @property string|null $rbb_jun type: numeric(9)
 * @property string|null $rbb_jul type: numeric(9)
 * @property string|null $rbb_ags type: numeric(9)
 * @property string|null $rbb_sep type: numeric(9)
 * @property string|null $rbb_okt type: numeric(9)
 * @property string|null $rbb_nov type: numeric(9)
 * @property string|null $rbb_dec type: numeric(9)
 * @property string|null $stshide type: char(1)
 */
class Rbbnrc extends MciBaseModel
{
    /**
     * Nama tabel (case-sensitive di SQL Server).
     */
    protected $table = 'RBBNRC';

    /**
     * Daftar LENGKAP kolom sesuai database (20 kolom).
     * Model ini read-only, $fillable hanya untuk dokumentasi & IDE helper.
     *
     * @var array<int,string>
     */
    protected $fillable = [
        'thn',
        'nosbb',
        'sandibi',
        'kdrbb',
        'kdds',
        'kdds2',
        'declalu',
        'rbb_jan',
        'rbb_feb',
        'rbb_mar',
        'rbb_apr',
        'rbb_mei',
        'rbb_jun',
        'rbb_jul',
        'rbb_ags',
        'rbb_sep',
        'rbb_okt',
        'rbb_nov',
        'rbb_dec',
        'stshide',
    ];

    /**
     * Casting tipe kolom (non-string saja).
     *
     * @var array<string,string>
     */
    protected $casts = [
        'declalu' => 'decimal:2',
        'rbb_jan' => 'decimal:2',
        'rbb_feb' => 'decimal:2',
        'rbb_mar' => 'decimal:2',
        'rbb_apr' => 'decimal:2',
        'rbb_mei' => 'decimal:2',
        'rbb_jun' => 'decimal:2',
        'rbb_jul' => 'decimal:2',
        'rbb_ags' => 'decimal:2',
        'rbb_sep' => 'decimal:2',
        'rbb_okt' => 'decimal:2',
        'rbb_nov' => 'decimal:2',
        'rbb_dec' => 'decimal:2',
    ];
}
