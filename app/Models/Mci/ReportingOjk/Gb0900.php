<?php

namespace App\Models\Mci\ReportingOjk;

use App\Models\Mci\MciBaseModel;

/**
 * Model MCI: GB0900
 * --------------------------------------------------------------------------
 * Domain   : Reporting OJK/BI
 * Tabel    : [dbo].[GB0900]
 * Kolom    : 7
 * Sumber   : Mapping_MCI_MAR26_01042026.sql / MCI_MAR26_01042026.xlsx
 *
 * CATATAN:
 *  - Model ini READ-ONLY (lihat trait ReadOnlyModel di MciBaseModel).
 *  - Koneksi default `dashboard_data` (SQL Server), otomatis ke DB bulan terbaru.
 *  - Kolom dengan spasi/karakter khusus diakses via:
 *    $model->getAttribute('NAMA KOLOM')  atau  $model->{'NAMA KOLOM'}.
 *
 * @property string $urut  type: numeric(5)
 * @property string $sandipos  type: char(7)
 * @property string $saldorata  type: numeric(9)
 * @property string|null $pendbagi  type: numeric(9)
 * @property string|null $porsi  type: numeric(5)
 * @property string|null $porsinom  type: numeric(9)
 * @property string|null $equivrate  type: decimal(9)
 */
class Gb0900 extends MciBaseModel
{
    /**
     * Nama tabel (case-sensitive di SQL Server).
     */
    protected $table = 'GB0900';

    /**
     * Daftar LENGKAP kolom sesuai database (7 kolom).
     * Model ini read-only, $fillable hanya untuk dokumentasi & IDE helper.
     *
     * @var array<int,string>
     */
    protected $fillable = [
        'urut',
        'sandipos',
        'saldorata',
        'pendbagi',
        'porsi',
        'porsinom',
        'equivrate',
    ];

    /**
     * Casting tipe kolom (non-string saja).
     *
     * @var array<string,string>
     */
    protected $casts = [
        'urut' => 'decimal:2',
        'saldorata' => 'decimal:2',
        'pendbagi' => 'decimal:2',
        'porsi' => 'decimal:2',
        'porsinom' => 'decimal:2',
        'equivrate' => 'decimal:2',
    ];
}
