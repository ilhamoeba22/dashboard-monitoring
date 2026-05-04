<?php

declare(strict_types=1);

namespace App\Models\Mci\Lainnya;

use App\Models\Mci\MciBaseModel;

/**
 * Model MCI: TOFMCCBLN
 * --------------------------------------------------------------------------
 * Domain   : Lainnya
 * Tabel    : [dbo].[TOFMCCBLN]
 * Kolom    : 36
 * Sumber   : Mapping_MCI_MAR26_01042026.sql / MCI_MAR26_01042026.xlsx
 *
 * CATATAN:
 *  - Model ini READ-ONLY (lihat trait ReadOnlyModel di MciBaseModel).
 *  - Koneksi default `dashboard_data` (SQL Server), otomatis ke DB bulan terbaru.
 *  - Kolom dengan spasi/karakter khusus diakses via:
 *    $model->getAttribute('NAMA KOLOM')  atau  $model->{'NAMA KOLOM'}.
 *
 * @property string $cc type: varchar(2)
 * @property string $thn type: varchar(4)
 * @property string|null $stock01 type: numeric(9)
 * @property string|null $stock02 type: numeric(9)
 * @property string|null $stock03 type: numeric(9)
 * @property string|null $stock04 type: numeric(9)
 * @property string|null $stock05 type: numeric(9)
 * @property string|null $stock06 type: numeric(9)
 * @property string|null $stock07 type: numeric(9)
 * @property string|null $stock08 type: numeric(9)
 * @property string|null $stock09 type: numeric(9)
 * @property string|null $stock10 type: numeric(9)
 * @property string|null $stock11 type: numeric(9)
 * @property string|null $stock12 type: numeric(9)
 * @property string|null $kursreval01 type: numeric(5)
 * @property string|null $kursreval02 type: numeric(5)
 * @property string|null $kursreval03 type: numeric(5)
 * @property string|null $kursreval04 type: numeric(5)
 * @property string|null $kursreval05 type: numeric(5)
 * @property string|null $kursreval06 type: numeric(5)
 * @property string|null $kursreval07 type: numeric(5)
 * @property string|null $kursreval08 type: numeric(5)
 * @property string|null $kursreval09 type: numeric(5)
 * @property string|null $kursreval10 type: numeric(5)
 * @property string|null $kursreval11 type: numeric(5)
 * @property string|null $kursreval12 type: numeric(5)
 * @property string|null $stsrec type: varchar(1)
 * @property string|null $inpuser type: varchar(10)
 * @property string|null $inptgljam type: varchar(14)
 * @property string|null $inpterm type: varchar(10)
 * @property string|null $chguser type: varchar(10)
 * @property string|null $chgtgljam type: varchar(14)
 * @property string|null $chgterm type: varchar(10)
 * @property string|null $autuser type: varchar(10)
 * @property string|null $auttgljam type: varchar(14)
 * @property string|null $autterm type: varchar(10)
 */
class Tofmccbln extends MciBaseModel
{
    /**
     * Nama tabel (case-sensitive di SQL Server).
     */
    protected $table = 'TOFMCCBLN';

    /**
     * Daftar LENGKAP kolom sesuai database (36 kolom).
     * Model ini read-only, $fillable hanya untuk dokumentasi & IDE helper.
     *
     * @var array<int,string>
     */
    protected $fillable = [
        'cc',
        'thn',
        'stock01',
        'stock02',
        'stock03',
        'stock04',
        'stock05',
        'stock06',
        'stock07',
        'stock08',
        'stock09',
        'stock10',
        'stock11',
        'stock12',
        'kursreval01',
        'kursreval02',
        'kursreval03',
        'kursreval04',
        'kursreval05',
        'kursreval06',
        'kursreval07',
        'kursreval08',
        'kursreval09',
        'kursreval10',
        'kursreval11',
        'kursreval12',
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
    ];

    /**
     * Casting tipe kolom (non-string saja).
     *
     * @var array<string,string>
     */
    protected $casts = [
        'stock01' => 'decimal:2',
        'stock02' => 'decimal:2',
        'stock03' => 'decimal:2',
        'stock04' => 'decimal:2',
        'stock05' => 'decimal:2',
        'stock06' => 'decimal:2',
        'stock07' => 'decimal:2',
        'stock08' => 'decimal:2',
        'stock09' => 'decimal:2',
        'stock10' => 'decimal:2',
        'stock11' => 'decimal:2',
        'stock12' => 'decimal:2',
        'kursreval01' => 'decimal:2',
        'kursreval02' => 'decimal:2',
        'kursreval03' => 'decimal:2',
        'kursreval04' => 'decimal:2',
        'kursreval05' => 'decimal:2',
        'kursreval06' => 'decimal:2',
        'kursreval07' => 'decimal:2',
        'kursreval08' => 'decimal:2',
        'kursreval09' => 'decimal:2',
        'kursreval10' => 'decimal:2',
        'kursreval11' => 'decimal:2',
        'kursreval12' => 'decimal:2',
    ];
}
