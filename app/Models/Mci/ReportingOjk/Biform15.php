<?php

declare(strict_types=1);

namespace App\Models\Mci\ReportingOjk;

use App\Models\Mci\MciBaseModel;

/**
 * Model MCI: BIFORM15
 * --------------------------------------------------------------------------
 * Domain   : Reporting OJK/BI
 * Tabel    : [dbo].[BIFORM15]
 * Kolom    : 8
 * Sumber   : Mapping_MCI_MAR26_01042026.sql / MCI_MAR26_01042026.xlsx
 *
 * CATATAN:
 *  - Model ini READ-ONLY (lihat trait ReadOnlyModel di MciBaseModel).
 *  - Koneksi default `dashboard_data` (SQL Server), otomatis ke DB bulan terbaru.
 *  - Kolom dengan spasi/karakter khusus diakses via:
 *    $model->getAttribute('NAMA KOLOM')  atau  $model->{'NAMA KOLOM'}.
 *
 * @property string $kdloc type: char(2)
 * @property string|null $noaset type: varchar(6)
 * @property string|null $jnsaset type: char(2)
 * @property string|null $tgleff type: varchar(8)
 * @property string|null $sandidati type: char(4)
 * @property string|null $haper type: numeric(9)
 * @property string|null $nilaipasar type: numeric(9)
 * @property string|null $nilaitercatat type: numeric(9)
 */
class Biform15 extends MciBaseModel
{
    /**
     * Nama tabel (case-sensitive di SQL Server).
     */
    protected $table = 'BIFORM15';

    /**
     * Daftar LENGKAP kolom sesuai database (8 kolom).
     * Model ini read-only, $fillable hanya untuk dokumentasi & IDE helper.
     *
     * @var array<int,string>
     */
    protected $fillable = [
        'kdloc',
        'noaset',
        'jnsaset',
        'tgleff',
        'sandidati',
        'haper',
        'nilaipasar',
        'nilaitercatat',
    ];

    /**
     * Casting tipe kolom (non-string saja).
     *
     * @var array<string,string>
     */
    protected $casts = [
        'haper' => 'decimal:2',
        'nilaipasar' => 'decimal:2',
        'nilaitercatat' => 'decimal:2',
    ];
}
