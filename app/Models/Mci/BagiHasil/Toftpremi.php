<?php

namespace App\Models\Mci\BagiHasil;

use App\Models\Mci\MciBaseModel;

/**
 * Model MCI: TOFTPREMI
 * --------------------------------------------------------------------------
 * Domain   : Bagi Hasil
 * Tabel    : [dbo].[TOFTPREMI]
 * Kolom    : 19
 * Sumber   : Mapping_MCI_MAR26_01042026.sql / MCI_MAR26_01042026.xlsx
 *
 * CATATAN:
 *  - Model ini READ-ONLY (lihat trait ReadOnlyModel di MciBaseModel).
 *  - Koneksi default `dashboard_data` (SQL Server), otomatis ke DB bulan terbaru.
 *  - Kolom dengan spasi/karakter khusus diakses via:
 *    $model->getAttribute('NAMA KOLOM')  atau  $model->{'NAMA KOLOM'}.
 *
 * @property string|null $kdprd  type: varchar(2)
 * @property string|null $jkwaktu  type: numeric(5)
 * @property string|null $suku_premi  type: numeric(9)
 * @property string|null $max_nom  type: numeric(9)
 * @property string|null $noacc  type: varchar(11)
 * @property string|null $nosbbpremi  type: varchar(7)
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
 * @property string|null $stspremi  type: varchar(1)
 * @property string|null $sbbbdd  type: varchar(7)
 * @property string|null $saldorata  type: numeric(9)
 */
class Toftpremi extends MciBaseModel
{
    /**
     * Nama tabel (case-sensitive di SQL Server).
     */
    protected $table = 'TOFTPREMI';

    /**
     * Daftar LENGKAP kolom sesuai database (19 kolom).
     * Model ini read-only, $fillable hanya untuk dokumentasi & IDE helper.
     *
     * @var array<int,string>
     */
    protected $fillable = [
        'kdprd',
        'jkwaktu',
        'suku_premi',
        'max_nom',
        'noacc',
        'nosbbpremi',
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
        'stspremi',
        'sbbbdd',
        'saldorata',
    ];

    /**
     * Casting tipe kolom (non-string saja).
     *
     * @var array<string,string>
     */
    protected $casts = [
        'jkwaktu' => 'decimal:2',
        'suku_premi' => 'decimal:2',
        'max_nom' => 'decimal:2',
        'saldorata' => 'decimal:2',
    ];
}
