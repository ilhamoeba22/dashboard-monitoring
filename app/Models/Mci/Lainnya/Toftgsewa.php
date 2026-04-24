<?php

namespace App\Models\Mci\Lainnya;

use App\Models\Mci\MciBaseModel;

/**
 * Model MCI: TOFTGSEWA
 * --------------------------------------------------------------------------
 * Domain   : Lainnya
 * Tabel    : [dbo].[TOFTGSEWA]
 * Kolom    : 24
 * Sumber   : Mapping_MCI_MAR26_01042026.sql / MCI_MAR26_01042026.xlsx
 *
 * CATATAN:
 *  - Model ini READ-ONLY (lihat trait ReadOnlyModel di MciBaseModel).
 *  - Koneksi default `dashboard_data` (SQL Server), otomatis ke DB bulan terbaru.
 *  - Kolom dengan spasi/karakter khusus diakses via:
 *    $model->getAttribute('NAMA KOLOM')  atau  $model->{'NAMA KOLOM'}.
 *
 * @property string|null $kdprd  type: varchar(2)
 * @property string|null $kdsewa  type: varchar(2)
 * @property string|null $jwsewa  type: numeric(5)
 * @property string|null $jnsjw  type: varchar(1)
 * @property string|null $minjw  type: numeric(5)
 * @property string|null $maxjw  type: numeric(5)
 * @property string|null $kolomjw  type: numeric(5)
 * @property string|null $disclunas  type: numeric(5)
 * @property string|null $jwlunas  type: numeric(5)
 * @property string|null $gp  type: numeric(5)
 * @property string|null $bygp  type: numeric(9)
 * @property string|null $stsrec  type: varchar(1)
 * @property string|null $inpuser  type: varchar(10)
 * @property string|null $inptgljam  type: varchar(14)
 * @property string|null $inpterm  type: varchar(10)
 * @property string|null $chguser  type: varchar(10)
 * @property string|null $chgtgljam  type: varchar(14)
 * @property string|null $chgterm  type: varchar(10)
 * @property string|null $autuser  type: varchar(10)
 * @property string|null $auttgljam  type: varchar(14)
 * @property string|null $autterm  type: varchar(10)
 * @property string|null $kdbysewa  type: varchar(1)
 * @property string|null $bysewa  type: numeric(9)
 * @property string|null $nomlipat  type: numeric(9)
 */
class Toftgsewa extends MciBaseModel
{
    /**
     * Nama tabel (case-sensitive di SQL Server).
     */
    protected $table = 'TOFTGSEWA';

    /**
     * Daftar LENGKAP kolom sesuai database (24 kolom).
     * Model ini read-only, $fillable hanya untuk dokumentasi & IDE helper.
     *
     * @var array<int,string>
     */
    protected $fillable = [
        'kdprd',
        'kdsewa',
        'jwsewa',
        'jnsjw',
        'minjw',
        'maxjw',
        'kolomjw',
        'disclunas',
        'jwlunas',
        'gp',
        'bygp',
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
        'kdbysewa',
        'bysewa',
        'nomlipat',
    ];

    /**
     * Casting tipe kolom (non-string saja).
     *
     * @var array<string,string>
     */
    protected $casts = [
        'jwsewa' => 'decimal:2',
        'minjw' => 'decimal:2',
        'maxjw' => 'decimal:2',
        'kolomjw' => 'decimal:2',
        'disclunas' => 'decimal:2',
        'jwlunas' => 'decimal:2',
        'gp' => 'decimal:2',
        'bygp' => 'decimal:2',
        'bysewa' => 'decimal:2',
        'nomlipat' => 'decimal:2',
    ];
}
