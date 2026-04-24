<?php

namespace App\Models\Mci\Pesanan;

use App\Models\Mci\MciBaseModel;

/**
 * Model MCI: TOFTPSNKTRBYR
 * --------------------------------------------------------------------------
 * Domain   : Pesanan / Payroll
 * Tabel    : [dbo].[TOFTPSNKTRBYR]
 * Kolom    : 12
 * Sumber   : Mapping_MCI_MAR26_01042026.sql / MCI_MAR26_01042026.xlsx
 *
 * CATATAN:
 *  - Model ini READ-ONLY (lihat trait ReadOnlyModel di MciBaseModel).
 *  - Koneksi default `dashboard_data` (SQL Server), otomatis ke DB bulan terbaru.
 *  - Kolom dengan spasi/karakter khusus diakses via:
 *    $model->getAttribute('NAMA KOLOM')  atau  $model->{'NAMA KOLOM'}.
 *
 * @property string $kdktrbyr  type: nvarchar(20)
 * @property string|null $ket  type: nvarchar(100)
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
class Toftpsnktrbyr extends MciBaseModel
{
    /**
     * Nama tabel (case-sensitive di SQL Server).
     */
    protected $table = 'TOFTPSNKTRBYR';

    /**
     * Daftar LENGKAP kolom sesuai database (12 kolom).
     * Model ini read-only, $fillable hanya untuk dokumentasi & IDE helper.
     *
     * @var array<int,string>
     */
    protected $fillable = [
        'kdktrbyr',
        'ket',
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

    ];
}
