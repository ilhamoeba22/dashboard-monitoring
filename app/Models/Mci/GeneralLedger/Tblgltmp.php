<?php

declare(strict_types=1);

namespace App\Models\Mci\GeneralLedger;

use App\Models\Mci\MciBaseModel;

/**
 * Model MCI: TBLGLTMP
 * --------------------------------------------------------------------------
 * Domain   : GL / Accounting
 * Tabel    : [dbo].[TBLGLTMP]
 * Kolom    : 31
 * Sumber   : Mapping_MCI_MAR26_01042026.sql / MCI_MAR26_01042026.xlsx
 *
 * CATATAN:
 *  - Model ini READ-ONLY (lihat trait ReadOnlyModel di MciBaseModel).
 *  - Koneksi default `dashboard_data` (SQL Server), otomatis ke DB bulan terbaru.
 *  - Kolom dengan spasi/karakter khusus diakses via:
 *    $model->getAttribute('NAMA KOLOM')  atau  $model->{'NAMA KOLOM'}.
 *
 * @property string $golac type: varchar(1)
 * @property string $jnsgol type: varchar(1)
 * @property string|null $nobb type: varchar(7)
 * @property string|null $nosbb type: varchar(7)
 * @property string|null $cc type: varchar(2)
 * @property string|null $nmsbb type: varchar(40)
 * @property string|null $posttyp type: varchar(1)
 * @property string|null $stsac type: varchar(1)
 * @property string|null $nourut type: numeric(9)
 * @property string|null $sandibi type: varchar(5)
 * @property string|null $stsmdl type: varchar(1)
 * @property string|null $stsatmr type: varchar(1)
 * @property string|null $bbtatmr type: numeric(5)
 * @property string|null $stsbyops type: varchar(1)
 * @property string|null $stspdops type: varchar(1)
 * @property string|null $stsloan type: varchar(1)
 * @property string|null $stsdpk type: varchar(1)
 * @property string|null $stspsh type: varchar(1)
 * @property string|null $stsreval type: varchar(1)
 * @property string|null $kdtks type: varchar(2)
 * @property string|null $stsnisb type: varchar(1)
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
class Tblgltmp extends MciBaseModel
{
    /**
     * Nama tabel (case-sensitive di SQL Server).
     */
    protected $table = 'TBLGLTMP';

    /**
     * Daftar LENGKAP kolom sesuai database (31 kolom).
     * Model ini read-only, $fillable hanya untuk dokumentasi & IDE helper.
     *
     * @var array<int,string>
     */
    protected $fillable = [
        'golac',
        'jnsgol',
        'nobb',
        'nosbb',
        'cc',
        'nmsbb',
        'posttyp',
        'stsac',
        'nourut',
        'sandibi',
        'stsmdl',
        'stsatmr',
        'bbtatmr',
        'stsbyops',
        'stspdops',
        'stsloan',
        'stsdpk',
        'stspsh',
        'stsreval',
        'kdtks',
        'stsnisb',
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
        'nourut' => 'decimal:2',
        'bbtatmr' => 'decimal:2',
    ];
}
