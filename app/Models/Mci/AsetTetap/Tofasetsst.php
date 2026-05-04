<?php

declare(strict_types=1);

namespace App\Models\Mci\AsetTetap;

use App\Models\Mci\MciBaseModel;

/**
 * Model MCI: TOFASETSST
 * --------------------------------------------------------------------------
 * Domain   : Aset Tetap
 * Tabel    : [dbo].[TOFASETSST]
 * Kolom    : 9
 * Sumber   : Mapping_MCI_MAR26_01042026.sql / MCI_MAR26_01042026.xlsx
 *
 * CATATAN:
 *  - Model ini READ-ONLY (lihat trait ReadOnlyModel di MciBaseModel).
 *  - Koneksi default `dashboard_data` (SQL Server), otomatis ke DB bulan terbaru.
 *  - Kolom dengan spasi/karakter khusus diakses via:
 *    $model->getAttribute('NAMA KOLOM')  atau  $model->{'NAMA KOLOM'}.
 *
 * @property string|null $kdaset type: varchar(5)
 * @property string|null $tgltagih type: varchar(8)
 * @property string|null $thnbln type: varchar(6)
 * @property string|null $tgl type: varchar(2)
 * @property string|null $pokok type: numeric(9)
 * @property string|null $stssusut type: varchar(1)
 * @property string|null $tglsusut type: varchar(8)
 * @property string|null $inpuser type: varchar(10)
 * @property string|null $sstuser type: varchar(10)
 */
class Tofasetsst extends MciBaseModel
{
    /**
     * Nama tabel (case-sensitive di SQL Server).
     */
    protected $table = 'TOFASETSST';

    /**
     * Daftar LENGKAP kolom sesuai database (9 kolom).
     * Model ini read-only, $fillable hanya untuk dokumentasi & IDE helper.
     *
     * @var array<int,string>
     */
    protected $fillable = [
        'kdaset',
        'tgltagih',
        'thnbln',
        'tgl',
        'pokok',
        'stssusut',
        'tglsusut',
        'inpuser',
        'sstuser',
    ];

    /**
     * Casting tipe kolom (non-string saja).
     *
     * @var array<string,string>
     */
    protected $casts = [
        'pokok' => 'decimal:2',
    ];
}
