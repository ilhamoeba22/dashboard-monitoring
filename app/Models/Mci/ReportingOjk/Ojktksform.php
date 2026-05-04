<?php

declare(strict_types=1);

namespace App\Models\Mci\ReportingOjk;

use App\Models\Mci\MciBaseModel;

/**
 * Model MCI: OJKTKSFORM
 * --------------------------------------------------------------------------
 * Domain   : Reporting OJK/BI
 * Tabel    : [dbo].[OJKTKSFORM]
 * Kolom    : 14
 * Sumber   : Mapping_MCI_MAR26_01042026.sql / MCI_MAR26_01042026.xlsx
 *
 * CATATAN:
 *  - Model ini READ-ONLY (lihat trait ReadOnlyModel di MciBaseModel).
 *  - Koneksi default `dashboard_data` (SQL Server), otomatis ke DB bulan terbaru.
 *  - Kolom dengan spasi/karakter khusus diakses via:
 *    $model->getAttribute('NAMA KOLOM')  atau  $model->{'NAMA KOLOM'}.
 *
 * @property string|null $periode type: varchar(12)
 * @property string|null $kdsandi type: varchar(10)
 * @property string|null $sandi type: varchar(15)
 * @property string|null $nilai01 type: numeric(9)
 * @property string|null $nilai02 type: numeric(9)
 * @property string|null $nilai03 type: numeric(9)
 * @property string|null $nilai04 type: numeric(9)
 * @property string|null $nilai05 type: numeric(9)
 * @property string|null $nilai06 type: numeric(9)
 * @property string|null $nilai07 type: numeric(9)
 * @property string|null $nilai08 type: numeric(9)
 * @property string|null $nilai09 type: numeric(9)
 * @property string|null $nilai10 type: numeric(9)
 * @property string|null $ket type: varchar(225)
 */
class Ojktksform extends MciBaseModel
{
    /**
     * Nama tabel (case-sensitive di SQL Server).
     */
    protected $table = 'OJKTKSFORM';

    /**
     * Daftar LENGKAP kolom sesuai database (14 kolom).
     * Model ini read-only, $fillable hanya untuk dokumentasi & IDE helper.
     *
     * @var array<int,string>
     */
    protected $fillable = [
        'periode',
        'kdsandi',
        'sandi',
        'nilai01',
        'nilai02',
        'nilai03',
        'nilai04',
        'nilai05',
        'nilai06',
        'nilai07',
        'nilai08',
        'nilai09',
        'nilai10',
        'ket',
    ];

    /**
     * Casting tipe kolom (non-string saja).
     *
     * @var array<string,string>
     */
    protected $casts = [
        'nilai01' => 'decimal:2',
        'nilai02' => 'decimal:2',
        'nilai03' => 'decimal:2',
        'nilai04' => 'decimal:2',
        'nilai05' => 'decimal:2',
        'nilai06' => 'decimal:2',
        'nilai07' => 'decimal:2',
        'nilai08' => 'decimal:2',
        'nilai09' => 'decimal:2',
        'nilai10' => 'decimal:2',
    ];
}
