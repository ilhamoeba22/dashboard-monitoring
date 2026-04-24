<?php

namespace App\Models\Mci\Cif;

use App\Models\Mci\MciBaseModel;

/**
 * Model MCI: MCIFPSN
 * --------------------------------------------------------------------------
 * Domain   : CIF / Customer
 * Tabel    : [dbo].[MCIFPSN]
 * Kolom    : 11
 * Sumber   : Mapping_MCI_MAR26_01042026.sql / MCI_MAR26_01042026.xlsx
 *
 * CATATAN:
 *  - Model ini READ-ONLY (lihat trait ReadOnlyModel di MciBaseModel).
 *  - Koneksi default `dashboard_data` (SQL Server), otomatis ke DB bulan terbaru.
 *  - Kolom dengan spasi/karakter khusus diakses via:
 *    $model->getAttribute('NAMA KOLOM')  atau  $model->{'NAMA KOLOM'}.
 *
 * @property string $nocif  type: nvarchar(18)
 * @property string|null $nama  type: nvarchar(100)
 * @property string|null $kdpensiun  type: nvarchar(20)
 * @property string|null $nopen  type: nvarchar(40)
 * @property string|null $karip  type: nvarchar(40)
 * @property string|null $kdktrbyr  type: nvarchar(40)
 * @property string|null $kdloket  type: nvarchar(50)
 * @property string|null $noskep  type: nvarchar(400)
 * @property string|null $tglskep  type: nvarchar(16)
 * @property string|null $penerbit  type: nvarchar(80)
 * @property string|null $kdktrbyrlama  type: nvarchar(40)
 */
class Mcifpsn extends MciBaseModel
{
    /**
     * Nama tabel (case-sensitive di SQL Server).
     */
    protected $table = 'MCIFPSN';

    /**
     * Daftar LENGKAP kolom sesuai database (11 kolom).
     * Model ini read-only, $fillable hanya untuk dokumentasi & IDE helper.
     *
     * @var array<int,string>
     */
    protected $fillable = [
        'nocif',
        'nama',
        'kdpensiun',
        'nopen',
        'karip',
        'kdktrbyr',
        'kdloket',
        'noskep',
        'tglskep',
        'penerbit',
        'kdktrbyrlama',
    ];

    /**
     * Casting tipe kolom (non-string saja).
     *
     * @var array<string,string>
     */
    protected $casts = [

    ];
}
