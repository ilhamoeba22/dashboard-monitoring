<?php

declare(strict_types=1);

namespace App\Models\Mci\GeneralLedger;

use App\Models\Mci\MciBaseModel;

/**
 * Model MCI: TBLNRCLAPBUL
 * --------------------------------------------------------------------------
 * Domain   : GL / Accounting
 * Tabel    : [dbo].[TBLNRCLAPBUL]
 * Kolom    : 6
 * Sumber   : Mapping_MCI_MAR26_01042026.sql / MCI_MAR26_01042026.xlsx
 *
 * CATATAN:
 *  - Model ini READ-ONLY (lihat trait ReadOnlyModel di MciBaseModel).
 *  - Koneksi default `dashboard_data` (SQL Server), otomatis ke DB bulan terbaru.
 *  - Kolom dengan spasi/karakter khusus diakses via:
 *    $model->getAttribute('NAMA KOLOM')  atau  $model->{'NAMA KOLOM'}.
 *
 * @property string $urut type: numeric(5)
 * @property string $golac type: char(1)
 * @property string|null $kdisi type: char(3)
 * @property string $sandibi type: varchar(5)
 * @property string|null $ket type: varchar(100)
 * @property string|null $saldo type: numeric(9)
 */
class Tblnrclapbul extends MciBaseModel
{
    /**
     * Nama tabel (case-sensitive di SQL Server).
     */
    protected $table = 'TBLNRCLAPBUL';

    /**
     * Daftar LENGKAP kolom sesuai database (6 kolom).
     * Model ini read-only, $fillable hanya untuk dokumentasi & IDE helper.
     *
     * @var array<int,string>
     */
    protected $fillable = [
        'urut',
        'golac',
        'kdisi',
        'sandibi',
        'ket',
        'saldo',
    ];

    /**
     * Casting tipe kolom (non-string saja).
     *
     * @var array<string,string>
     */
    protected $casts = [
        'urut' => 'decimal:2',
        'saldo' => 'decimal:2',
    ];
}
