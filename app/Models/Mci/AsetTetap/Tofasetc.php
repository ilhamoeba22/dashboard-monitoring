<?php

declare(strict_types=1);

namespace App\Models\Mci\AsetTetap;

use App\Models\Mci\MciBaseModel;

/**
 * Model MCI: TOFASETC
 * --------------------------------------------------------------------------
 * Domain   : Aset Tetap
 * Tabel    : [dbo].[TOFASETC]
 * Kolom    : 13
 * Sumber   : Mapping_MCI_MAR26_01042026.sql / MCI_MAR26_01042026.xlsx
 *
 * CATATAN:
 *  - Model ini READ-ONLY (lihat trait ReadOnlyModel di MciBaseModel).
 *  - Koneksi default `dashboard_data` (SQL Server), otomatis ke DB bulan terbaru.
 *  - Kolom dengan spasi/karakter khusus diakses via:
 *    $model->getAttribute('NAMA KOLOM')  atau  $model->{'NAMA KOLOM'}.
 *
 * @property string $kdaset type: varchar(5)
 * @property string|null $haper type: numeric(9)
 * @property string|null $hajual type: numeric(9)
 * @property string|null $margin type: numeric(9)
 * @property string|null $sewabln type: numeric(9)
 * @property string|null $susut type: numeric(9)
 * @property string|null $perbaiki type: numeric(9)
 * @property string|null $nilaibuku type: numeric(9)
 * @property string|null $hajar type: numeric(9)
 * @property string|null $hibah type: numeric(9)
 * @property string|null $jual type: numeric(9)
 * @property string|null $susutke type: numeric(5)
 * @property string|null $stsrec type: varchar(1)
 */
class Tofasetc extends MciBaseModel
{
    /**
     * Nama tabel (case-sensitive di SQL Server).
     */
    protected $table = 'TOFASETC';

    /**
     * Daftar LENGKAP kolom sesuai database (13 kolom).
     * Model ini read-only, $fillable hanya untuk dokumentasi & IDE helper.
     *
     * @var array<int,string>
     */
    protected $fillable = [
        'kdaset',
        'haper',
        'hajual',
        'margin',
        'sewabln',
        'susut',
        'perbaiki',
        'nilaibuku',
        'hajar',
        'hibah',
        'jual',
        'susutke',
        'stsrec',
    ];

    /**
     * Casting tipe kolom (non-string saja).
     *
     * @var array<string,string>
     */
    protected $casts = [
        'haper' => 'decimal:2',
        'hajual' => 'decimal:2',
        'margin' => 'decimal:2',
        'sewabln' => 'decimal:2',
        'susut' => 'decimal:2',
        'perbaiki' => 'decimal:2',
        'nilaibuku' => 'decimal:2',
        'hajar' => 'decimal:2',
        'hibah' => 'decimal:2',
        'jual' => 'decimal:2',
        'susutke' => 'decimal:2',
    ];
}
