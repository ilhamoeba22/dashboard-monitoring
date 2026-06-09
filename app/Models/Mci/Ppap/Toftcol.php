<?php

declare(strict_types=1);

namespace App\Models\Mci\Ppap;

use App\Models\Mci\MciBaseModel;

/**
 * Model MCI: TOFTCOL
 * --------------------------------------------------------------------------
 * Domain   : PPKA / DPD / Coll
 * Tabel    : [dbo].[TOFTCOL]
 * Kolom    : 39
 * Sumber   : Mapping_MCI_MAR26_01042026.sql / MCI_MAR26_01042026.xlsx
 *
 * CATATAN:
 *  - Model ini READ-ONLY (lihat trait ReadOnlyModel di MciBaseModel).
 *  - Koneksi default `dashboard_data` (SQL Server), otomatis ke DB bulan terbaru.
 *  - Kolom dengan spasi/karakter khusus diakses via:
 *    $model->getAttribute('NAMA KOLOM')  atau  $model->{'NAMA KOLOM'}.
 *
 * @property string $kdcol type: varchar(2)
 * @property string|null $ket type: varchar(30)
 * @property string|null $kdincome type: varchar(1)
 * @property string|null $col1mdl type: numeric(5)
 * @property string|null $col1bh type: numeric(5)
 * @property string|null $col1bln type: numeric(5)
 * @property string|null $col2mdl type: numeric(5)
 * @property string|null $col2bh type: numeric(5)
 * @property string|null $col2bln type: numeric(5)
 * @property string|null $col3mdl type: numeric(5)
 * @property string|null $col3bh type: numeric(5)
 * @property string|null $col3bln type: numeric(5)
 * @property string|null $col4mdl type: numeric(5)
 * @property string|null $col4bh type: numeric(5)
 * @property string|null $col4bln type: numeric(5)
 * @property string|null $col5mdl type: numeric(5)
 * @property string|null $col5bh type: numeric(5)
 * @property string|null $col5bln type: numeric(5)
 * @property string|null $onsbb type: varchar(1)
 * @property string|null $sbb1 type: varchar(7)
 * @property string|null $sbb2 type: varchar(7)
 * @property string|null $sbb3 type: varchar(7)
 * @property string|null $sbb4 type: varchar(7)
 * @property string|null $sbb5 type: varchar(7)
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
 * @property string|null $col1jt type: numeric(5)
 * @property string|null $col2jt type: numeric(5)
 * @property string|null $col3jt type: numeric(5)
 * @property string|null $col4jt type: numeric(5)
 * @property string|null $col5jt type: numeric(5)
 */
class Toftcol extends MciBaseModel
{
    /**
     * Nama tabel (case-sensitive di SQL Server).
     */
    protected $table = 'TOFTCOL';

    /**
     * Daftar LENGKAP kolom sesuai database (39 kolom).
     * Model ini read-only, $fillable hanya untuk dokumentasi & IDE helper.
     *
     * @var array<int,string>
     */
    protected $fillable = [
        'kdcol',
        'ket',
        'kdincome',
        'col1mdl',
        'col1bh',
        'col1bln',
        'col2mdl',
        'col2bh',
        'col2bln',
        'col3mdl',
        'col3bh',
        'col3bln',
        'col4mdl',
        'col4bh',
        'col4bln',
        'col5mdl',
        'col5bh',
        'col5bln',
        'onsbb',
        'sbb1',
        'sbb2',
        'sbb3',
        'sbb4',
        'sbb5',
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
        'col1jt',
        'col2jt',
        'col3jt',
        'col4jt',
        'col5jt',
    ];

    /**
     * Casting tipe kolom (non-string saja).
     *
     * @var array<string,string>
     */
    protected $casts = [
        'col1mdl' => 'decimal:2',
        'col1bh' => 'decimal:2',
        'col1bln' => 'decimal:2',
        'col2mdl' => 'decimal:2',
        'col2bh' => 'decimal:2',
        'col2bln' => 'decimal:2',
        'col3mdl' => 'decimal:2',
        'col3bh' => 'decimal:2',
        'col3bln' => 'decimal:2',
        'col4mdl' => 'decimal:2',
        'col4bh' => 'decimal:2',
        'col4bln' => 'decimal:2',
        'col5mdl' => 'decimal:2',
        'col5bh' => 'decimal:2',
        'col5bln' => 'decimal:2',
        'col1jt' => 'decimal:2',
        'col2jt' => 'decimal:2',
        'col3jt' => 'decimal:2',
        'col4jt' => 'decimal:2',
        'col5jt' => 'decimal:2',
    ];
}
