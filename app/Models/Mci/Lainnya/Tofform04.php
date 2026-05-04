<?php

declare(strict_types=1);

namespace App\Models\Mci\Lainnya;

use App\Models\Mci\MciBaseModel;

/**
 * Model MCI: TOFFORM04
 * --------------------------------------------------------------------------
 * Domain   : Lainnya
 * Tabel    : [dbo].[TOFFORM04]
 * Kolom    : 27
 * Sumber   : Mapping_MCI_MAR26_01042026.sql / MCI_MAR26_01042026.xlsx
 *
 * CATATAN:
 *  - Model ini READ-ONLY (lihat trait ReadOnlyModel di MciBaseModel).
 *  - Koneksi default `dashboard_data` (SQL Server), otomatis ke DB bulan terbaru.
 *  - Kolom dengan spasi/karakter khusus diakses via:
 *    $model->getAttribute('NAMA KOLOM')  atau  $model->{'NAMA KOLOM'}.
 *
 * @property string|null $periode type: varchar(6)
 * @property string|null $norek type: varchar(11)
 * @property string|null $jumrek type: numeric(5)
 * @property string|null $guna type: varchar(2)
 * @property string|null $hubbank type: varchar(1)
 * @property string|null $tglawal type: varchar(8)
 * @property string|null $tglakhir type: varchar(8)
 * @property string|null $coll type: varchar(1)
 * @property string|null $rate type: varchar(5)
 * @property string|null $sekon type: varchar(5)
 * @property string|null $hargajual type: numeric(9)
 * @property string|null $hargapokok type: numeric(9)
 * @property string|null $margin type: numeric(9)
 * @property string|null $piutang type: numeric(9)
 * @property string|null $jnsagun type: varchar(1)
 * @property string|null $nomagun type: numeric(9)
 * @property string|null $ppap type: numeric(9)
 * @property string|null $mtdbaghas type: varchar(1)
 * @property string|null $goljam type: varchar(5)
 * @property string|null $bagjam type: varchar(5)
 * @property string|null $golcust type: varchar(4)
 * @property string|null $sandidati2 type: varchar(4)
 * @property string|null $golpiutang type: varchar(1)
 * @property string|null $kdtujuan type: varchar(2)
 * @property string|null $padx type: numeric(9)
 * @property string|null $kdloc type: varchar(2)
 * @property string|null $datarest type: varchar(1)
 */
class Tofform04 extends MciBaseModel
{
    /**
     * Nama tabel (case-sensitive di SQL Server).
     */
    protected $table = 'TOFFORM04';

    /**
     * Daftar LENGKAP kolom sesuai database (27 kolom).
     * Model ini read-only, $fillable hanya untuk dokumentasi & IDE helper.
     *
     * @var array<int,string>
     */
    protected $fillable = [
        'periode',
        'norek',
        'jumrek',
        'guna',
        'hubbank',
        'tglawal',
        'tglakhir',
        'coll',
        'rate',
        'sekon',
        'hargajual',
        'hargapokok',
        'margin',
        'piutang',
        'jnsagun',
        'nomagun',
        'ppap',
        'mtdbaghas',
        'goljam',
        'bagjam',
        'golcust',
        'sandidati2',
        'golpiutang',
        'kdtujuan',
        'padx',
        'kdloc',
        'datarest',
    ];

    /**
     * Casting tipe kolom (non-string saja).
     *
     * @var array<string,string>
     */
    protected $casts = [
        'jumrek' => 'decimal:2',
        'hargajual' => 'decimal:2',
        'hargapokok' => 'decimal:2',
        'margin' => 'decimal:2',
        'piutang' => 'decimal:2',
        'nomagun' => 'decimal:2',
        'ppap' => 'decimal:2',
        'padx' => 'decimal:2',
    ];
}
