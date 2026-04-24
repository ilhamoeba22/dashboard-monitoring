<?php

namespace App\Models\Mci\Pesanan;

use App\Models\Mci\MciBaseModel;

/**
 * Model MCI: TOFTPSNPLAF
 * --------------------------------------------------------------------------
 * Domain   : Pesanan / Payroll
 * Tabel    : [dbo].[TOFTPSNPLAF]
 * Kolom    : 17
 * Sumber   : Mapping_MCI_MAR26_01042026.sql / MCI_MAR26_01042026.xlsx
 *
 * CATATAN:
 *  - Model ini READ-ONLY (lihat trait ReadOnlyModel di MciBaseModel).
 *  - Koneksi default `dashboard_data` (SQL Server), otomatis ke DB bulan terbaru.
 *  - Kolom dengan spasi/karakter khusus diakses via:
 *    $model->getAttribute('NAMA KOLOM')  atau  $model->{'NAMA KOLOM'}.
 *
 * @property string $kdprd  type: nvarchar(4)
 * @property string|null $usia  type: decimal(5)
 * @property string|null $plafond  type: decimal(9)
 * @property string|null $jw_1  type: decimal(5)
 * @property string|null $jw_2  type: decimal(5)
 * @property string|null $rate  type: decimal(5)
 * @property string|null $byadm  type: decimal(9)
 * @property string|null $stsrec  type: nvarchar(2)
 * @property string|null $inpuser  type: nvarchar(20)
 * @property string|null $inptgljam  type: nvarchar(28)
 * @property string|null $inpterm  type: nvarchar(20)
 * @property string|null $chguser  type: nvarchar(20)
 * @property string|null $chgtgljam  type: nvarchar(28)
 * @property string|null $chgterm  type: nvarchar(20)
 * @property string|null $autuser  type: nvarchar(20)
 * @property string|null $auttgljam  type: nvarchar(28)
 * @property string|null $autterm  type: nvarchar(20)
 */
class Toftpsnplaf extends MciBaseModel
{
    /**
     * Nama tabel (case-sensitive di SQL Server).
     */
    protected $table = 'TOFTPSNPLAF';

    /**
     * Daftar LENGKAP kolom sesuai database (17 kolom).
     * Model ini read-only, $fillable hanya untuk dokumentasi & IDE helper.
     *
     * @var array<int,string>
     */
    protected $fillable = [
        'kdprd',
        'usia',
        'plafond',
        'jw_1',
        'jw_2',
        'rate',
        'byadm',
        'stsrec',
        'inpuser',
        'inptgljam',
        'inpterm',
        'chguser',
        'chgtgljam',
        'chgterm',
        'autuser',
        'auttgljam',
        'autterm',
    ];

    /**
     * Casting tipe kolom (non-string saja).
     *
     * @var array<string,string>
     */
    protected $casts = [
        'usia' => 'decimal:2',
        'plafond' => 'decimal:2',
        'jw_1' => 'decimal:2',
        'jw_2' => 'decimal:2',
        'rate' => 'decimal:2',
        'byadm' => 'decimal:2',
    ];
}
