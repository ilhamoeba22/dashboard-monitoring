<?php

declare(strict_types=1);

namespace App\Models\Mci\GeneralLedger;

use App\Models\Mci\MciBaseModel;

/**
 * Model MCI: TOFNRCEOY
 * --------------------------------------------------------------------------
 * Domain   : GL / Accounting
 * Tabel    : [dbo].[TOFNRCEOY]
 * Kolom    : 10
 * Sumber   : Mapping_MCI_MAR26_01042026.sql / MCI_MAR26_01042026.xlsx
 *
 * CATATAN:
 *  - Model ini READ-ONLY (lihat trait ReadOnlyModel di MciBaseModel).
 *  - Koneksi default `dashboard_data` (SQL Server), otomatis ke DB bulan terbaru.
 *  - Kolom dengan spasi/karakter khusus diakses via:
 *    $model->getAttribute('NAMA KOLOM')  atau  $model->{'NAMA KOLOM'}.
 *
 * @property string $nosbb type: varchar(11)
 * @property string|null $nobb type: varchar(11)
 * @property string|null $golongan type: varchar(1)
 * @property string|null $kdcab type: varchar(3)
 * @property string|null $kdloc type: varchar(2)
 * @property string|null $namaaccount type: varchar(30)
 * @property string|null $statusac type: varchar(1)
 * @property string|null $thn type: varchar(4)
 * @property string|null $bln type: varchar(2)
 * @property string|null $saldo type: numeric(9)
 */
class Tofnrceoy extends MciBaseModel
{
    /**
     * Nama tabel (case-sensitive di SQL Server).
     */
    protected $table = 'TOFNRCEOY';

    /**
     * Daftar LENGKAP kolom sesuai database (10 kolom).
     * Model ini read-only, $fillable hanya untuk dokumentasi & IDE helper.
     *
     * @var array<int,string>
     */
    protected $fillable = [
        'nosbb',
        'nobb',
        'golongan',
        'kdcab',
        'kdloc',
        'namaaccount',
        'statusac',
        'thn',
        'bln',
        'saldo',
    ];

    /**
     * Casting tipe kolom (non-string saja).
     *
     * @var array<string,string>
     */
    protected $casts = [
        'saldo' => 'decimal:2',
    ];
}
