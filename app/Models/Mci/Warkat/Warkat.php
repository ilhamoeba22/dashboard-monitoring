<?php

declare(strict_types=1);

namespace App\Models\Mci\Warkat;

use App\Models\Mci\MciBaseModel;

/**
 * Model MCI: WARKAT
 * --------------------------------------------------------------------------
 * Domain   : Warkat / Cek
 * Tabel    : [dbo].[WARKAT]
 * Kolom    : 18
 * Sumber   : Mapping_MCI_MAR26_01042026.sql / MCI_MAR26_01042026.xlsx
 *
 * CATATAN:
 *  - Model ini READ-ONLY (lihat trait ReadOnlyModel di MciBaseModel).
 *  - Koneksi default `dashboard_data` (SQL Server), otomatis ke DB bulan terbaru.
 *  - Kolom dengan spasi/karakter khusus diakses via:
 *    $model->getAttribute('NAMA KOLOM')  atau  $model->{'NAMA KOLOM'}.
 *
 * @property string $nork type: varchar(11)
 * @property string|null $jnswarkat type: varchar(1)
 * @property string|null $nowarkat type: numeric(9)
 * @property string|null $lembar type: numeric(5)
 * @property string|null $tglpakai type: varchar(8)
 * @property string|null $status type: varchar(1)
 * @property string|null $kdcab type: varchar(3)
 * @property string|null $kdloc type: varchar(2)
 * @property string|null $stsrec type: varchar(1)
 * @property string|null $inpuser type: varchar(10)
 * @property string|null $inptgl type: varchar(14)
 * @property string|null $inpterm type: varchar(10)
 * @property string|null $chguser type: varchar(10)
 * @property string|null $chgtgl type: varchar(14)
 * @property string|null $chgterm type: varchar(10)
 * @property string|null $autuser type: varchar(10)
 * @property string|null $auttgl type: varchar(14)
 * @property string|null $autterm type: varchar(10)
 */
class Warkat extends MciBaseModel
{
    /**
     * Nama tabel (case-sensitive di SQL Server).
     */
    protected $table = 'WARKAT';

    /**
     * Daftar LENGKAP kolom sesuai database (18 kolom).
     * Model ini read-only, $fillable hanya untuk dokumentasi & IDE helper.
     *
     * @var array<int,string>
     */
    protected $fillable = [
        'nork',
        'jnswarkat',
        'nowarkat',
        'lembar',
        'tglpakai',
        'status',
        'kdcab',
        'kdloc',
        'stsrec',
        'inpuser',
        'inptgl',
        'inpterm',
        'chguser',
        'chgtgl',
        'chgterm',
        'autuser',
        'auttgl',
        'autterm',
    ];

    /**
     * Casting tipe kolom (non-string saja).
     *
     * @var array<string,string>
     */
    protected $casts = [
        'nowarkat' => 'decimal:2',
        'lembar' => 'decimal:2',
    ];
}
