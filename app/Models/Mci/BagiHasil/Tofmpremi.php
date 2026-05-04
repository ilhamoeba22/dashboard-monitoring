<?php

declare(strict_types=1);

namespace App\Models\Mci\BagiHasil;

use App\Models\Mci\MciBaseModel;

/**
 * Model MCI: TOFMPREMI
 * --------------------------------------------------------------------------
 * Domain   : Bagi Hasil
 * Tabel    : [dbo].[TOFMPREMI]
 * Kolom    : 11
 * Sumber   : Mapping_MCI_MAR26_01042026.sql / MCI_MAR26_01042026.xlsx
 *
 * CATATAN:
 *  - Model ini READ-ONLY (lihat trait ReadOnlyModel di MciBaseModel).
 *  - Koneksi default `dashboard_data` (SQL Server), otomatis ke DB bulan terbaru.
 *  - Kolom dengan spasi/karakter khusus diakses via:
 *    $model->getAttribute('NAMA KOLOM')  atau  $model->{'NAMA KOLOM'}.
 *
 * @property string|null $tgltrans type: varchar(8)
 * @property string|null $kdpremi type: varchar(1)
 * @property string|null $nodep type: varchar(11)
 * @property string|null $kdprd type: varchar(3)
 * @property string|null $jkwaktu type: numeric(5)
 * @property string|null $nama type: varchar(40)
 * @property string|null $nomrp type: numeric(9)
 * @property string|null $suku_premi type: numeric(9)
 * @property string|null $premi type: numeric(9)
 * @property string|null $kdloc type: varchar(2)
 * @property string|null $tglexp type: varchar(8)
 */
class Tofmpremi extends MciBaseModel
{
    /**
     * Nama tabel (case-sensitive di SQL Server).
     */
    protected $table = 'TOFMPREMI';

    /**
     * Daftar LENGKAP kolom sesuai database (11 kolom).
     * Model ini read-only, $fillable hanya untuk dokumentasi & IDE helper.
     *
     * @var array<int,string>
     */
    protected $fillable = [
        'tgltrans',
        'kdpremi',
        'nodep',
        'kdprd',
        'jkwaktu',
        'nama',
        'nomrp',
        'suku_premi',
        'premi',
        'kdloc',
        'tglexp',
    ];

    /**
     * Casting tipe kolom (non-string saja).
     *
     * @var array<string,string>
     */
    protected $casts = [
        'jkwaktu' => 'decimal:2',
        'nomrp' => 'decimal:2',
        'suku_premi' => 'decimal:2',
        'premi' => 'decimal:2',
    ];
}
