<?php

declare(strict_types=1);

namespace App\Models\Mci\Aruskas;

use App\Models\Mci\MciBaseModel;

/**
 * Model MCI: TOFTARUSKAS
 * --------------------------------------------------------------------------
 * Domain   : Aruskas / IRA
 * Tabel    : [dbo].[TOFTARUSKAS]
 * Kolom    : 9
 * Sumber   : Mapping_MCI_MAR26_01042026.sql / MCI_MAR26_01042026.xlsx
 *
 * CATATAN:
 *  - Model ini READ-ONLY (lihat trait ReadOnlyModel di MciBaseModel).
 *  - Koneksi default `dashboard_data` (SQL Server), otomatis ke DB bulan terbaru.
 *  - Kolom dengan spasi/karakter khusus diakses via:
 *    $model->getAttribute('NAMA KOLOM')  atau  $model->{'NAMA KOLOM'}.
 *
 * @property int $ID type: bigint(8)
 * @property string $urut type: numeric(5)
 * @property string $ket type: varchar(100)
 * @property string|null $amount type: numeric(9)
 * @property string|null $sandibi type: varchar(50)
 * @property string|null $stsrec type: varchar(1)
 * @property string|null $inpuser type: varchar(10)
 * @property string|null $inptgljam type: varchar(14)
 * @property string|null $inpterm type: varchar(14)
 */
class Toftaruskas extends MciBaseModel
{
    /**
     * Nama tabel (case-sensitive di SQL Server).
     */
    protected $table = 'TOFTARUSKAS';

    /**
     * Daftar LENGKAP kolom sesuai database (9 kolom).
     * Model ini read-only, $fillable hanya untuk dokumentasi & IDE helper.
     *
     * @var array<int,string>
     */
    protected $fillable = [
        'ID',
        'urut',
        'ket',
        'amount',
        'sandibi',
        'stsrec',
        'inpuser',
        'inptgljam',
        'inpterm',
    ];

    /**
     * Casting tipe kolom (non-string saja).
     *
     * @var array<string,string>
     */
    protected $casts = [
        'ID' => 'integer',
        'urut' => 'decimal:2',
        'amount' => 'decimal:2',
    ];
}
