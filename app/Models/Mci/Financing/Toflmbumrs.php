<?php

namespace App\Models\Mci\Financing;

use App\Models\Mci\MciBaseModel;

/**
 * Model MCI: TOFLMBUMRS
 * --------------------------------------------------------------------------
 * Domain   : Financing / Loan
 * Tabel    : [dbo].[TOFLMBUMRS]
 * Kolom    : 22
 * Sumber   : Mapping_MCI_MAR26_01042026.sql / MCI_MAR26_01042026.xlsx
 *
 * CATATAN:
 *  - Model ini READ-ONLY (lihat trait ReadOnlyModel di MciBaseModel).
 *  - Koneksi default `dashboard_data` (SQL Server), otomatis ke DB bulan terbaru.
 *  - Kolom dengan spasi/karakter khusus diakses via:
 *    $model->getAttribute('NAMA KOLOM')  atau  $model->{'NAMA KOLOM'}.
 *
 * @property string $tgltrn  type: varchar(12)
 * @property string|null $urut  type: numeric(9)
 * @property string $nokontrak  type: varchar(11)
 * @property string|null $kdbiaya  type: numeric(5)
 * @property string|null $ket  type: varchar(30)
 * @property string|null $acdr  type: varchar(11)
 * @property string|null $accr  type: varchar(11)
 * @property string|null $dokumen  type: varchar(30)
 * @property string|null $biaya  type: numeric(9)
 * @property string|null $bydrop  type: numeric(9)
 * @property string|null $stsbiaya  type: varchar(1)
 * @property string|null $kdphk3  type: varchar(10)
 * @property string|null $stscair  type: varchar(1)
 * @property string|null $tglcair  type: varchar(8)
 * @property string|null $debet  type: numeric(9)
 * @property string|null $kredit  type: numeric(9)
 * @property string|null $kdao  type: varchar(15)
 * @property string|null $catatan  type: varchar(225)
 * @property string|null $inpuser  type: varchar(10)
 * @property string|null $inptgljam  type: varchar(25)
 * @property string|null $chguser  type: varchar(10)
 * @property string|null $chgtgljam  type: varchar(25)
 */
class Toflmbumrs extends MciBaseModel
{
    /**
     * Nama tabel (case-sensitive di SQL Server).
     */
    protected $table = 'TOFLMBUMRS';

    /**
     * Daftar LENGKAP kolom sesuai database (22 kolom).
     * Model ini read-only, $fillable hanya untuk dokumentasi & IDE helper.
     *
     * @var array<int,string>
     */
    protected $fillable = [
        'tgltrn',
        'urut',
        'nokontrak',
        'kdbiaya',
        'ket',
        'acdr',
        'accr',
        'dokumen',
        'biaya',
        'bydrop',
        'stsbiaya',
        'kdphk3',
        'stscair',
        'tglcair',
        'debet',
        'kredit',
        'kdao',
        'catatan',
        'inpuser',
        'inptgljam',
        'chguser',
        'chgtgljam',
    ];

    /**
     * Casting tipe kolom (non-string saja).
     *
     * @var array<string,string>
     */
    protected $casts = [
        'urut' => 'decimal:2',
        'kdbiaya' => 'decimal:2',
        'biaya' => 'decimal:2',
        'bydrop' => 'decimal:2',
        'debet' => 'decimal:2',
        'kredit' => 'decimal:2',
    ];
}
