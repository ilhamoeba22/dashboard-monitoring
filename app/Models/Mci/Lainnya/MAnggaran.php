<?php

namespace App\Models\Mci\Lainnya;

use App\Models\Mci\MciBaseModel;

/**
 * Model MCI: m_anggaran
 * --------------------------------------------------------------------------
 * Domain   : Lainnya
 * Tabel    : [dbo].[m_anggaran]
 * Kolom    : 18
 * Sumber   : Mapping_MCI_MAR26_01042026.sql / MCI_MAR26_01042026.xlsx
 *
 * CATATAN:
 *  - Model ini READ-ONLY (lihat trait ReadOnlyModel di MciBaseModel).
 *  - Koneksi default `dashboard_data` (SQL Server), otomatis ke DB bulan terbaru.
 *  - Kolom dengan spasi/karakter khusus diakses via:
 *    $model->getAttribute('NAMA KOLOM')  atau  $model->{'NAMA KOLOM'}.
 *
 * @property string $nosbb  type: varchar(11)
 * @property string $nobb  type: varchar(11)
 * @property string $posisi  type: varchar(1)
 * @property string|null $pindsaldo  type: numeric(9)
 * @property string|null $jan  type: numeric(9)
 * @property string|null $feb  type: numeric(9)
 * @property string|null $mar  type: numeric(9)
 * @property string|null $apr  type: numeric(9)
 * @property string|null $mei  type: numeric(9)
 * @property string|null $jun  type: numeric(9)
 * @property string|null $jul  type: numeric(9)
 * @property string|null $ags  type: numeric(9)
 * @property string|null $sep  type: numeric(9)
 * @property string|null $okt  type: numeric(9)
 * @property string|null $nov  type: numeric(9)
 * @property string|null $des  type: numeric(9)
 * @property string $tahun  type: numeric(5)
 * @property string|null $kdloc  type: varchar(2)
 */
class MAnggaran extends MciBaseModel
{
    /**
     * Nama tabel (case-sensitive di SQL Server).
     */
    protected $table = 'm_anggaran';

    /**
     * Daftar LENGKAP kolom sesuai database (18 kolom).
     * Model ini read-only, $fillable hanya untuk dokumentasi & IDE helper.
     *
     * @var array<int,string>
     */
    protected $fillable = [
        'nosbb',
        'nobb',
        'posisi',
        'pindsaldo',
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
        'tahun',
        'kdloc',
    ];

    /**
     * Casting tipe kolom (non-string saja).
     *
     * @var array<string,string>
     */
    protected $casts = [
        'pindsaldo' => 'decimal:2',
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
        'tahun' => 'decimal:2',
    ];
}
