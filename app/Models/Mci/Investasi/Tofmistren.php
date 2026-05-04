<?php

declare(strict_types=1);

namespace App\Models\Mci\Investasi;

use App\Models\Mci\MciBaseModel;

/**
 * Model MCI: TOFMISTREN
 * --------------------------------------------------------------------------
 * Domain   : Investasi / Saham
 * Tabel    : [dbo].[TOFMISTREN]
 * Kolom    : 14
 * Sumber   : Mapping_MCI_MAR26_01042026.sql / MCI_MAR26_01042026.xlsx
 *
 * CATATAN:
 *  - Model ini READ-ONLY (lihat trait ReadOnlyModel di MciBaseModel).
 *  - Koneksi default `dashboard_data` (SQL Server), otomatis ke DB bulan terbaru.
 *  - Kolom dengan spasi/karakter khusus diakses via:
 *    $model->getAttribute('NAMA KOLOM')  atau  $model->{'NAMA KOLOM'}.
 *
 * @property string $kdtrend type: varchar(2)
 * @property string $thn type: varchar(4)
 * @property string|null $jan type: numeric(9)
 * @property string|null $feb type: numeric(9)
 * @property string|null $mar type: numeric(9)
 * @property string|null $apr type: numeric(9)
 * @property string|null $mei type: numeric(9)
 * @property string|null $jun type: numeric(9)
 * @property string|null $jul type: numeric(9)
 * @property string|null $ags type: numeric(9)
 * @property string|null $sep type: numeric(9)
 * @property string|null $okt type: numeric(9)
 * @property string|null $nov type: numeric(9)
 * @property string|null $des type: numeric(9)
 */
class Tofmistren extends MciBaseModel
{
    /**
     * Nama tabel (case-sensitive di SQL Server).
     */
    protected $table = 'TOFMISTREN';

    /**
     * Daftar LENGKAP kolom sesuai database (14 kolom).
     * Model ini read-only, $fillable hanya untuk dokumentasi & IDE helper.
     *
     * @var array<int,string>
     */
    protected $fillable = [
        'kdtrend',
        'thn',
        'jan',
        'feb',
        'mar',
        'apr',
        'mei',
        'jun',
        'jul',
        'ags',
        'sep',
        'okt',
        'nov',
        'des',
    ];

    /**
     * Casting tipe kolom (non-string saja).
     *
     * @var array<string,string>
     */
    protected $casts = [
        'jan' => 'decimal:2',
        'feb' => 'decimal:2',
        'mar' => 'decimal:2',
        'apr' => 'decimal:2',
        'mei' => 'decimal:2',
        'jun' => 'decimal:2',
        'jul' => 'decimal:2',
        'ags' => 'decimal:2',
        'sep' => 'decimal:2',
        'okt' => 'decimal:2',
        'nov' => 'decimal:2',
        'des' => 'decimal:2',
    ];
}
