<?php

namespace App\Models\Mci\Financing;

use App\Models\Mci\MciBaseModel;

/**
 * Model MCI: TMPLOANSD
 * --------------------------------------------------------------------------
 * Domain   : Financing / Loan
 * Tabel    : [dbo].[TMPLOANSD]
 * Kolom    : 22
 * Sumber   : Mapping_MCI_MAR26_01042026.sql / MCI_MAR26_01042026.xlsx
 *
 * CATATAN:
 *  - Model ini READ-ONLY (lihat trait ReadOnlyModel di MciBaseModel).
 *  - Koneksi default `dashboard_data` (SQL Server), otomatis ke DB bulan terbaru.
 *  - Kolom dengan spasi/karakter khusus diakses via:
 *    $model->getAttribute('NAMA KOLOM')  atau  $model->{'NAMA KOLOM'}.
 *
 * @property string $noloan  type: varchar(11)
 * @property string|null $nocif  type: varchar(9)
 * @property string|null $nocifgrp  type: varchar(9)
 * @property string|null $nofasili  type: varchar(11)
 * @property string|null $noacc  type: varchar(11)
 * @property string|null $nama  type: varchar(30)
 * @property string|null $kdprd  type: varchar(2)
 * @property string|null $kdcab  type: varchar(3)
 * @property string|null $kdloc  type: varchar(2)
 * @property string|null $kdkas  type: varchar(2)
 * @property string|null $porsipsen  type: numeric(5)
 * @property string|null $porsirp  type: numeric(9)
 * @property string|null $stsrec  type: varchar(1)
 * @property string|null $inpuser  type: varchar(10)
 * @property string|null $inptgl  type: varchar(14)
 * @property string|null $inpterm  type: varchar(10)
 * @property string|null $chguser  type: varchar(10)
 * @property string|null $chgtgl  type: varchar(14)
 * @property string|null $chgterm  type: varchar(10)
 * @property string|null $autuser  type: varchar(10)
 * @property string|null $auttgl  type: varchar(14)
 * @property string|null $autterm  type: varchar(10)
 */
class Tmploansd extends MciBaseModel
{
    /**
     * Nama tabel (case-sensitive di SQL Server).
     */
    protected $table = 'TMPLOANSD';

    /**
     * Daftar LENGKAP kolom sesuai database (22 kolom).
     * Model ini read-only, $fillable hanya untuk dokumentasi & IDE helper.
     *
     * @var array<int,string>
     */
    protected $fillable = [
        'noloan',
        'nocif',
        'nocifgrp',
        'nofasili',
        'noacc',
        'nama',
        'kdprd',
        'kdcab',
        'kdloc',
        'kdkas',
        'porsipsen',
        'porsirp',
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
        'porsipsen' => 'decimal:2',
        'porsirp' => 'decimal:2',
    ];
}
