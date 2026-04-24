<?php

namespace App\Models\Mci\Saving;

use App\Models\Mci\MciBaseModel;

/**
 * Model MCI: TOFTABSH
 * --------------------------------------------------------------------------
 * Domain   : Saving
 * Tabel    : [dbo].[TOFTABSH]
 * Kolom    : 10
 * Sumber   : Mapping_MCI_MAR26_01042026.sql / MCI_MAR26_01042026.xlsx
 *
 * CATATAN:
 *  - Model ini READ-ONLY (lihat trait ReadOnlyModel di MciBaseModel).
 *  - Koneksi default `dashboard_data` (SQL Server), otomatis ke DB bulan terbaru.
 *  - Kolom dengan spasi/karakter khusus diakses via:
 *    $model->getAttribute('NAMA KOLOM')  atau  $model->{'NAMA KOLOM'}.
 *
 * @property string $noacc  type: varchar(11)
 * @property string $thn  type: varchar(4)
 * @property string $bln  type: varchar(2)
 * @property string|null $saldo1  type: varchar(150)
 * @property string|null $saldo2  type: varchar(150)
 * @property string|null $saldo3  type: varchar(165)
 * @property string|null $rate  type: varchar(124)
 * @property string|null $bunga1  type: varchar(150)
 * @property string|null $bunga2  type: varchar(150)
 * @property string|null $bunga3  type: varchar(165)
 */
class Toftabsh extends MciBaseModel
{
    /**
     * Nama tabel (case-sensitive di SQL Server).
     */
    protected $table = 'TOFTABSH';

    /**
     * Daftar LENGKAP kolom sesuai database (10 kolom).
     * Model ini read-only, $fillable hanya untuk dokumentasi & IDE helper.
     *
     * @var array<int,string>
     */
    protected $fillable = [
        'noacc',
        'thn',
        'bln',
        'saldo1',
        'saldo2',
        'saldo3',
        'rate',
        'bunga1',
        'bunga2',
        'bunga3',
    ];

    /**
     * Casting tipe kolom (non-string saja).
     *
     * @var array<string,string>
     */
    protected $casts = [

    ];
}
