<?php

declare(strict_types=1);

namespace App\Models\Mci\BagiHasil;

use App\Models\Mci\MciBaseModel;

/**
 * Model MCI: TOFBAGHAS
 * --------------------------------------------------------------------------
 * Domain   : Bagi Hasil
 * Tabel    : [dbo].[TOFBAGHAS]
 * Kolom    : 44
 * Sumber   : Mapping_MCI_MAR26_01042026.sql / MCI_MAR26_01042026.xlsx
 *
 * CATATAN:
 *  - Model ini READ-ONLY (lihat trait ReadOnlyModel di MciBaseModel).
 *  - Koneksi default `dashboard_data` (SQL Server), otomatis ke DB bulan terbaru.
 *  - Kolom dengan spasi/karakter khusus diakses via:
 *    $model->getAttribute('NAMA KOLOM')  atau  $model->{'NAMA KOLOM'}.
 *
 * @property string|null $kdcab type: varchar(3)
 * @property string $kdac type: varchar(1)
 * @property string|null $kdprd type: varchar(2)
 * @property string|null $cc type: varchar(2)
 * @property string|null $jw type: numeric(5)
 * @property string|null $jnsjw type: varchar(1)
 * @property string $nosbb type: varchar(11)
 * @property string|null $golac type: varchar(1)
 * @property string|null $jnsgol type: varchar(1)
 * @property string|null $stsnisb type: varchar(1)
 * @property string|null $nisbah type: numeric(5)
 * @property string|null $thnbln type: varchar(6)
 * @property string|null $hari type: numeric(5)
 * @property string|null $tgl01 type: numeric(9)
 * @property string|null $tgl02 type: numeric(9)
 * @property string|null $tgl03 type: numeric(9)
 * @property string|null $tgl04 type: numeric(9)
 * @property string|null $tgl05 type: numeric(9)
 * @property string|null $tgl06 type: numeric(9)
 * @property string|null $tgl07 type: numeric(9)
 * @property string|null $tgl08 type: numeric(9)
 * @property string|null $tgl09 type: numeric(9)
 * @property string|null $tgl10 type: numeric(9)
 * @property string|null $tgl11 type: numeric(9)
 * @property string|null $tgl12 type: numeric(9)
 * @property string|null $tgl13 type: numeric(9)
 * @property string|null $tgl14 type: numeric(9)
 * @property string|null $tgl15 type: numeric(9)
 * @property string|null $tgl16 type: numeric(9)
 * @property string|null $tgl17 type: numeric(9)
 * @property string|null $tgl18 type: numeric(9)
 * @property string|null $tgl19 type: numeric(9)
 * @property string|null $tgl20 type: numeric(9)
 * @property string|null $tgl21 type: numeric(9)
 * @property string|null $tgl22 type: numeric(9)
 * @property string|null $tgl23 type: numeric(9)
 * @property string|null $tgl24 type: numeric(9)
 * @property string|null $tgl25 type: numeric(9)
 * @property string|null $tgl26 type: numeric(9)
 * @property string|null $tgl27 type: numeric(9)
 * @property string|null $tgl28 type: numeric(9)
 * @property string|null $tgl29 type: numeric(9)
 * @property string|null $tgl30 type: numeric(9)
 * @property string|null $tgl31 type: numeric(9)
 */
class Tofbaghas extends MciBaseModel
{
    /**
     * Nama tabel (case-sensitive di SQL Server).
     */
    protected $table = 'TOFBAGHAS';

    /**
     * Daftar LENGKAP kolom sesuai database (44 kolom).
     * Model ini read-only, $fillable hanya untuk dokumentasi & IDE helper.
     *
     * @var array<int,string>
     */
    protected $fillable = [
        'kdcab',
        'kdac',
        'kdprd',
        'cc',
        'jw',
        'jnsjw',
        'nosbb',
        'golac',
        'jnsgol',
        'stsnisb',
        'nisbah',
        'thnbln',
        'hari',
        'tgl01',
        'tgl02',
        'tgl03',
        'tgl04',
        'tgl05',
        'tgl06',
        'tgl07',
        'tgl08',
        'tgl09',
        'tgl10',
        'tgl11',
        'tgl12',
        'tgl13',
        'tgl14',
        'tgl15',
        'tgl16',
        'tgl17',
        'tgl18',
        'tgl19',
        'tgl20',
        'tgl21',
        'tgl22',
        'tgl23',
        'tgl24',
        'tgl25',
        'tgl26',
        'tgl27',
        'tgl28',
        'tgl29',
        'tgl30',
        'tgl31',
    ];

    /**
     * Casting tipe kolom (non-string saja).
     *
     * @var array<string,string>
     */
    protected $casts = [
        'jw' => 'decimal:2',
        'nisbah' => 'decimal:2',
        'hari' => 'decimal:2',
        'tgl01' => 'decimal:2',
        'tgl02' => 'decimal:2',
        'tgl03' => 'decimal:2',
        'tgl04' => 'decimal:2',
        'tgl05' => 'decimal:2',
        'tgl06' => 'decimal:2',
        'tgl07' => 'decimal:2',
        'tgl08' => 'decimal:2',
        'tgl09' => 'decimal:2',
        'tgl10' => 'decimal:2',
        'tgl11' => 'decimal:2',
        'tgl12' => 'decimal:2',
        'tgl13' => 'decimal:2',
        'tgl14' => 'decimal:2',
        'tgl15' => 'decimal:2',
        'tgl16' => 'decimal:2',
        'tgl17' => 'decimal:2',
        'tgl18' => 'decimal:2',
        'tgl19' => 'decimal:2',
        'tgl20' => 'decimal:2',
        'tgl21' => 'decimal:2',
        'tgl22' => 'decimal:2',
        'tgl23' => 'decimal:2',
        'tgl24' => 'decimal:2',
        'tgl25' => 'decimal:2',
        'tgl26' => 'decimal:2',
        'tgl27' => 'decimal:2',
        'tgl28' => 'decimal:2',
        'tgl29' => 'decimal:2',
        'tgl30' => 'decimal:2',
        'tgl31' => 'decimal:2',
    ];
}
