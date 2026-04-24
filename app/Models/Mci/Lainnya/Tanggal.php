<?php

namespace App\Models\Mci\Lainnya;

use App\Models\Mci\MciBaseModel;

/**
 * Model MCI: TANGGAL
 * --------------------------------------------------------------------------
 * Domain   : Lainnya
 * Tabel    : [dbo].[TANGGAL]
 * Kolom    : 8
 * Sumber   : Mapping_MCI_MAR26_01042026.sql / MCI_MAR26_01042026.xlsx
 *
 * CATATAN:
 *  - Model ini READ-ONLY (lihat trait ReadOnlyModel di MciBaseModel).
 *  - Koneksi default `dashboard_data` (SQL Server), otomatis ke DB bulan terbaru.
 *  - Kolom dengan spasi/karakter khusus diakses via:
 *    $model->getAttribute('NAMA KOLOM')  atau  $model->{'NAMA KOLOM'}.
 *
 * @property string $tgl  type: varchar(8)
 * @property string $tgllalu  type: varchar(8)
 * @property string $tglesok  type: varchar(8)
 * @property string|null $eom  type: varchar(1)
 * @property string|null $ststab  type: varchar(1)
 * @property string|null $stsloan  type: varchar(1)
 * @property string|null $stsgiro  type: varchar(1)
 * @property string|null $stsdep  type: varchar(1)
 */
class Tanggal extends MciBaseModel
{
    /**
     * Nama tabel (case-sensitive di SQL Server).
     */
    protected $table = 'TANGGAL';

    /**
     * Daftar LENGKAP kolom sesuai database (8 kolom).
     * Model ini read-only, $fillable hanya untuk dokumentasi & IDE helper.
     *
     * @var array<int,string>
     */
    protected $fillable = [
        'tgl',
        'tgllalu',
        'tglesok',
        'eom',
        'ststab',
        'stsloan',
        'stsgiro',
        'stsdep',
    ];

    /**
     * Casting tipe kolom (non-string saja).
     *
     * @var array<string,string>
     */
    protected $casts = [

    ];
}
