<?php

namespace App\Models\Mci\Lainnya;

use App\Models\Mci\MciBaseModel;

/**
 * Model MCI: TOFKUITANSI
 * --------------------------------------------------------------------------
 * Domain   : Lainnya
 * Tabel    : [dbo].[TOFKUITANSI]
 * Kolom    : 9
 * Sumber   : Mapping_MCI_MAR26_01042026.sql / MCI_MAR26_01042026.xlsx
 *
 * CATATAN:
 *  - Model ini READ-ONLY (lihat trait ReadOnlyModel di MciBaseModel).
 *  - Koneksi default `dashboard_data` (SQL Server), otomatis ke DB bulan terbaru.
 *  - Kolom dengan spasi/karakter khusus diakses via:
 *    $model->getAttribute('NAMA KOLOM')  atau  $model->{'NAMA KOLOM'}.
 *
 * @property string|null $nokontrak  type: varchar(11)
 * @property string|null $stscetak  type: varchar(1)
 * @property string|null $usercetak  type: varchar(10)
 * @property string|null $tglcetak  type: varchar(14)
 * @property string|null $tglakad  type: varchar(8)
 * @property string|null $usercetakulang  type: varchar(10)
 * @property string|null $tglcetakulang  type: varchar(14)
 * @property string|null $cetakke  type: numeric(5)
 * @property string|null $noreg  type: varchar(10)
 */
class Tofkuitansi extends MciBaseModel
{
    /**
     * Nama tabel (case-sensitive di SQL Server).
     */
    protected $table = 'TOFKUITANSI';

    /**
     * Daftar LENGKAP kolom sesuai database (9 kolom).
     * Model ini read-only, $fillable hanya untuk dokumentasi & IDE helper.
     *
     * @var array<int,string>
     */
    protected $fillable = [
        'nokontrak',
        'stscetak',
        'usercetak',
        'tglcetak',
        'tglakad',
        'usercetakulang',
        'tglcetakulang',
        'cetakke',
        'noreg',
    ];

    /**
     * Casting tipe kolom (non-string saja).
     *
     * @var array<string,string>
     */
    protected $casts = [
        'cetakke' => 'decimal:2',
    ];
}
