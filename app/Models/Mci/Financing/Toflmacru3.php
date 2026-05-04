<?php

declare(strict_types=1);

namespace App\Models\Mci\Financing;

use App\Models\Mci\MciBaseModel;

/**
 * Model MCI: TOFLMACRU3
 * --------------------------------------------------------------------------
 * Domain   : Financing / Loan
 * Tabel    : [dbo].[TOFLMACRU3]
 * Kolom    : 24
 * Sumber   : Mapping_MCI_MAR26_01042026.sql / MCI_MAR26_01042026.xlsx
 *
 * CATATAN:
 *  - Model ini READ-ONLY (lihat trait ReadOnlyModel di MciBaseModel).
 *  - Koneksi default `dashboard_data` (SQL Server), otomatis ke DB bulan terbaru.
 *  - Kolom dengan spasi/karakter khusus diakses via:
 *    $model->getAttribute('NAMA KOLOM')  atau  $model->{'NAMA KOLOM'}.
 *
 * @property string|null $tgltrn type: varchar(8)
 * @property string|null $batch type: numeric(5)
 * @property string|null $notrn type: numeric(5)
 * @property string|null $noacc type: varchar(16)
 * @property string|null $kdprd type: varchar(2)
 * @property string|null $tgltagih type: varchar(8)
 * @property string|null $kodebank type: varchar(3)
 * @property string|null $kodecab type: varchar(2)
 * @property string|null $kodeloc type: varchar(2)
 * @property string|null $nosbb02 type: varchar(7)
 * @property string|null $nosbb16 type: varchar(7)
 * @property string|null $hari type: numeric(5)
 * @property string|null $tagmgn type: numeric(9)
 * @property string|null $tagmgn1 type: numeric(9)
 * @property string|null $tagmgn2 type: numeric(9)
 * @property string|null $byrmgn type: numeric(9)
 * @property string|null $byrmgnc type: numeric(9)
 * @property string|null $ststrn type: varchar(1)
 * @property string|null $inpuser type: varchar(10)
 * @property string|null $inptgljam type: varchar(14)
 * @property string|null $inpterm type: varchar(10)
 * @property string|null $autuser type: varchar(10)
 * @property string|null $auttgljam type: varchar(14)
 * @property string|null $autterm type: varchar(10)
 */
class Toflmacru3 extends MciBaseModel
{
    /**
     * Nama tabel (case-sensitive di SQL Server).
     */
    protected $table = 'TOFLMACRU3';

    /**
     * Daftar LENGKAP kolom sesuai database (24 kolom).
     * Model ini read-only, $fillable hanya untuk dokumentasi & IDE helper.
     *
     * @var array<int,string>
     */
    protected $fillable = [
        'tgltrn',
        'batch',
        'notrn',
        'noacc',
        'kdprd',
        'tgltagih',
        'kodebank',
        'kodecab',
        'kodeloc',
        'nosbb02',
        'nosbb16',
        'hari',
        'tagmgn',
        'tagmgn1',
        'tagmgn2',
        'byrmgn',
        'byrmgnc',
        'ststrn',
        'inpuser',
        'inptgljam',
        'inpterm',
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
        'batch' => 'decimal:2',
        'notrn' => 'decimal:2',
        'hari' => 'decimal:2',
        'tagmgn' => 'decimal:2',
        'tagmgn1' => 'decimal:2',
        'tagmgn2' => 'decimal:2',
        'byrmgn' => 'decimal:2',
        'byrmgnc' => 'decimal:2',
    ];
}
