<?php

declare(strict_types=1);

namespace App\Models\Mci\Lainnya;

use App\Models\Mci\MciBaseModel;

/**
 * Model MCI: TDP001
 * --------------------------------------------------------------------------
 * Domain   : Lainnya
 * Tabel    : [dbo].[TDP001]
 * Kolom    : 18
 * Sumber   : Mapping_MCI_MAR26_01042026.sql / MCI_MAR26_01042026.xlsx
 *
 * CATATAN:
 *  - Model ini READ-ONLY (lihat trait ReadOnlyModel di MciBaseModel).
 *  - Koneksi default `dashboard_data` (SQL Server), otomatis ke DB bulan terbaru.
 *  - Kolom dengan spasi/karakter khusus diakses via:
 *    $model->getAttribute('NAMA KOLOM')  atau  $model->{'NAMA KOLOM'}.
 *
 * @property string $td01kdcab type: varchar(3)
 * @property string $td01kdprd type: varchar(2)
 * @property string|null $td01masa type: numeric(5)
 * @property string|null $td01jmasa type: varchar(1)
 * @property string|null $td01ket type: varchar(30)
 * @property string|null $td01rateo type: numeric(5)
 * @property string|null $td01raten type: numeric(5)
 * @property string|null $td01tgrat type: varchar(8)
 * @property string|null $td01strec type: varchar(1)
 * @property string|null $td01inusr type: varchar(10)
 * @property string|null $td01intgl type: varchar(14)
 * @property string|null $td01indev type: varchar(10)
 * @property string|null $td01chusr type: varchar(10)
 * @property string|null $td01chtgl type: varchar(14)
 * @property string|null $td01chdev type: varchar(10)
 * @property string|null $td01auusr type: varchar(10)
 * @property string|null $td01autgl type: varchar(14)
 * @property string|null $td01audev type: varchar(10)
 */
class Tdp001 extends MciBaseModel
{
    /**
     * Nama tabel (case-sensitive di SQL Server).
     */
    protected $table = 'TDP001';

    /**
     * Daftar LENGKAP kolom sesuai database (18 kolom).
     * Model ini read-only, $fillable hanya untuk dokumentasi & IDE helper.
     *
     * @var array<int,string>
     */
    protected $fillable = [
        'td01kdcab',
        'td01kdprd',
        'td01masa',
        'td01jmasa',
        'td01ket',
        'td01rateo',
        'td01raten',
        'td01tgrat',
        'td01strec',
        'td01inusr',
        'td01intgl',
        'td01indev',
        'td01chusr',
        'td01chtgl',
        'td01chdev',
        'td01auusr',
        'td01autgl',
        'td01audev',
    ];

    /**
     * Casting tipe kolom (non-string saja).
     *
     * @var array<string,string>
     */
    protected $casts = [
        'td01masa' => 'decimal:2',
        'td01rateo' => 'decimal:2',
        'td01raten' => 'decimal:2',
    ];
}
