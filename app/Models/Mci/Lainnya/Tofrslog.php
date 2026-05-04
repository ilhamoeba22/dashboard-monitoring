<?php

declare(strict_types=1);

namespace App\Models\Mci\Lainnya;

use App\Models\Mci\MciBaseModel;

/**
 * Model MCI: TOFRSLOG
 * --------------------------------------------------------------------------
 * Domain   : Lainnya
 * Tabel    : [dbo].[TOFRSLOG]
 * Kolom    : 23
 * Sumber   : Mapping_MCI_MAR26_01042026.sql / MCI_MAR26_01042026.xlsx
 *
 * CATATAN:
 *  - Model ini READ-ONLY (lihat trait ReadOnlyModel di MciBaseModel).
 *  - Koneksi default `dashboard_data` (SQL Server), otomatis ke DB bulan terbaru.
 *  - Kolom dengan spasi/karakter khusus diakses via:
 *    $model->getAttribute('NAMA KOLOM')  atau  $model->{'NAMA KOLOM'}.
 *
 * @property int $ID type: bigint(8)
 * @property string|null $kdubah type: varchar(1)
 * @property string|null $nokontrak type: varchar(10)
 * @property string|null $tgltagih type: varchar(8)
 * @property string|null $tgljtlama type: varchar(8)
 * @property string|null $tgljtbaru type: varchar(8)
 * @property string|null $mdllama type: numeric(9)
 * @property string|null $msbhlama type: numeric(9)
 * @property string|null $dndlama type: numeric(9)
 * @property string|null $mdlbaru type: numeric(9)
 * @property string|null $msbhbaru type: numeric(9)
 * @property string|null $dndbaru type: numeric(9)
 * @property string|null $stsbyrlama type: varchar(1)
 * @property string|null $stsbyrbaru type: varchar(1)
 * @property string|null $stsrec type: varchar(1)
 * @property string|null $chguser type: varchar(10)
 * @property string|null $chgtgl type: varchar(8)
 * @property string|null $chgtime type: varchar(4)
 * @property string|null $chgterm type: varchar(10)
 * @property string|null $autuser type: varchar(10)
 * @property string|null $auttgl type: varchar(8)
 * @property string|null $auttime type: varchar(4)
 * @property string|null $autterm type: varchar(10)
 */
class Tofrslog extends MciBaseModel
{
    /**
     * Nama tabel (case-sensitive di SQL Server).
     */
    protected $table = 'TOFRSLOG';

    /**
     * Daftar LENGKAP kolom sesuai database (23 kolom).
     * Model ini read-only, $fillable hanya untuk dokumentasi & IDE helper.
     *
     * @var array<int,string>
     */
    protected $fillable = [
        'ID',
        'kdubah',
        'nokontrak',
        'tgltagih',
        'tgljtlama',
        'tgljtbaru',
        'mdllama',
        'msbhlama',
        'dndlama',
        'mdlbaru',
        'msbhbaru',
        'dndbaru',
        'stsbyrlama',
        'stsbyrbaru',
        'stsrec',
        'chguser',
        'chgtgl',
        'chgtime',
        'chgterm',
        'autuser',
        'auttgl',
        'auttime',
        'autterm',
    ];

    /**
     * Casting tipe kolom (non-string saja).
     *
     * @var array<string,string>
     */
    protected $casts = [
        'ID' => 'integer',
        'mdllama' => 'decimal:2',
        'msbhlama' => 'decimal:2',
        'dndlama' => 'decimal:2',
        'mdlbaru' => 'decimal:2',
        'msbhbaru' => 'decimal:2',
        'dndbaru' => 'decimal:2',
    ];
}
