<?php

declare(strict_types=1);

namespace App\Models\Mci\Marketing;

use App\Models\Mci\MciBaseModel;

/**
 * Model MCI: TARGETAO
 * --------------------------------------------------------------------------
 * Domain   : AO / Marketing
 * Tabel    : [dbo].[TARGETAO]
 * Kolom    : 24
 * Sumber   : Mapping_MCI_MAR26_01042026.sql / MCI_MAR26_01042026.xlsx
 *
 * CATATAN:
 *  - Model ini READ-ONLY (lihat trait ReadOnlyModel di MciBaseModel).
 *  - Koneksi default `dashboard_data` (SQL Server), otomatis ke DB bulan terbaru.
 *  - Kolom dengan spasi/karakter khusus diakses via:
 *    $model->getAttribute('NAMA KOLOM')  atau  $model->{'NAMA KOLOM'}.
 *
 * @property string $kdao type: varchar(8)
 * @property string $thn type: varchar(4)
 * @property string $bln type: varchar(2)
 * @property string|null $tab type: numeric(9)
 * @property string|null $dep type: numeric(9)
 * @property string|null $loan type: numeric(9)
 * @property string|null $bdr type: numeric(5)
 * @property string|null $rtab type: numeric(9)
 * @property string|null $rdep type: numeric(9)
 * @property string|null $rloan type: numeric(9)
 * @property string|null $rbdr type: numeric(5)
 * @property string|null $tabeom type: numeric(9)
 * @property string|null $depeom type: numeric(9)
 * @property string|null $loaneom type: numeric(9)
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
class Targetao extends MciBaseModel
{
    /**
     * Nama tabel (case-sensitive di SQL Server).
     */
    protected $table = 'TARGETAO';

    /**
     * Daftar LENGKAP kolom sesuai database (24 kolom).
     * Model ini read-only, $fillable hanya untuk dokumentasi & IDE helper.
     *
     * @var array<int,string>
     */
    protected $fillable = [
        'kdao',
        'thn',
        'bln',
        'tab',
        'dep',
        'loan',
        'bdr',
        'rtab',
        'rdep',
        'rloan',
        'rbdr',
        'tabeom',
        'depeom',
        'loaneom',
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
        'tab' => 'decimal:2',
        'dep' => 'decimal:2',
        'loan' => 'decimal:2',
        'bdr' => 'decimal:2',
        'rtab' => 'decimal:2',
        'rdep' => 'decimal:2',
        'rloan' => 'decimal:2',
        'rbdr' => 'decimal:2',
        'tabeom' => 'decimal:2',
        'depeom' => 'decimal:2',
        'loaneom' => 'decimal:2',
    ];
}
