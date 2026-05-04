<?php

declare(strict_types=1);

namespace App\Models\Mci\Financing;

use App\Models\Mci\MciBaseModel;

/**
 * Model MCI: TOFLMTSLOC
 * --------------------------------------------------------------------------
 * Domain   : Financing / Loan
 * Tabel    : [dbo].[TOFLMTSLOC]
 * Kolom    : 17
 * Sumber   : Mapping_MCI_MAR26_01042026.sql / MCI_MAR26_01042026.xlsx
 *
 * CATATAN:
 *  - Model ini READ-ONLY (lihat trait ReadOnlyModel di MciBaseModel).
 *  - Koneksi default `dashboard_data` (SQL Server), otomatis ke DB bulan terbaru.
 *  - Kolom dengan spasi/karakter khusus diakses via:
 *    $model->getAttribute('NAMA KOLOM')  atau  $model->{'NAMA KOLOM'}.
 *
 * @property string|null $nokontrak type: varchar(11)
 * @property string|null $kdlocasal type: varchar(2)
 * @property string|null $nama type: varchar(20)
 * @property string|null $kdloctujuan type: varchar(2)
 * @property string|null $osmdlc type: numeric(9)
 * @property string|null $osmgnc type: numeric(9)
 * @property string|null $sbbpok type: varchar(7)
 * @property string|null $sbbmydt type: varchar(7)
 * @property string|null $pokpby type: varchar(2)
 * @property string|null $tglproses type: varchar(8)
 * @property string|null $stsrec type: varchar(1)
 * @property string|null $inpuser type: varchar(10)
 * @property string|null $inptgljam type: varchar(14)
 * @property string|null $inpterm type: varchar(10)
 * @property string|null $autuser type: varchar(10)
 * @property string|null $auttgljam type: varchar(14)
 * @property string|null $autterm type: varchar(10)
 */
class Toflmtsloc extends MciBaseModel
{
    /**
     * Nama tabel (case-sensitive di SQL Server).
     */
    protected $table = 'TOFLMTSLOC';

    /**
     * Daftar LENGKAP kolom sesuai database (17 kolom).
     * Model ini read-only, $fillable hanya untuk dokumentasi & IDE helper.
     *
     * @var array<int,string>
     */
    protected $fillable = [
        'nokontrak',
        'kdlocasal',
        'nama',
        'kdloctujuan',
        'osmdlc',
        'osmgnc',
        'sbbpok',
        'sbbmydt',
        'pokpby',
        'tglproses',
        'stsrec',
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
        'osmdlc' => 'decimal:2',
        'osmgnc' => 'decimal:2',
    ];
}
