<?php

declare(strict_types=1);

namespace App\Models\Mci\Pajak;

use App\Models\Mci\MciBaseModel;

/**
 * Model MCI: TOFMPAJAK
 * --------------------------------------------------------------------------
 * Domain   : Pajak
 * Tabel    : [dbo].[TOFMPAJAK]
 * Kolom    : 8
 * Sumber   : Mapping_MCI_MAR26_01042026.sql / MCI_MAR26_01042026.xlsx
 *
 * CATATAN:
 *  - Model ini READ-ONLY (lihat trait ReadOnlyModel di MciBaseModel).
 *  - Koneksi default `dashboard_data` (SQL Server), otomatis ke DB bulan terbaru.
 *  - Kolom dengan spasi/karakter khusus diakses via:
 *    $model->getAttribute('NAMA KOLOM')  atau  $model->{'NAMA KOLOM'}.
 *
 * @property string $periode type: varchar(10)
 * @property string|null $kdtax type: varchar(10)
 * @property string|null $nosbb type: varchar(11)
 * @property string|null $nmsbb type: varchar(50)
 * @property string|null $saldo type: numeric(9)
 * @property string|null $koreksi type: numeric(9)
 * @property string|null $fiskal type: numeric(9)
 * @property string|null $inpuser type: varchar(10)
 */
class Tofmpajak extends MciBaseModel
{
    /**
     * Nama tabel (case-sensitive di SQL Server).
     */
    protected $table = 'TOFMPAJAK';

    /**
     * Daftar LENGKAP kolom sesuai database (8 kolom).
     * Model ini read-only, $fillable hanya untuk dokumentasi & IDE helper.
     *
     * @var array<int,string>
     */
    protected $fillable = [
        'periode',
        'kdtax',
        'nosbb',
        'nmsbb',
        'saldo',
        'koreksi',
        'fiskal',
        'inpuser',
    ];

    /**
     * Casting tipe kolom (non-string saja).
     *
     * @var array<string,string>
     */
    protected $casts = [
        'saldo' => 'decimal:2',
        'koreksi' => 'decimal:2',
        'fiskal' => 'decimal:2',
    ];
}
