<?php

namespace App\Models\Mci\GeneralLedger;

use App\Models\Mci\MciBaseModel;

/**
 * Model MCI: NRCAO
 * --------------------------------------------------------------------------
 * Domain   : GL / Accounting
 * Tabel    : [dbo].[NRCAO]
 * Kolom    : 9
 * Sumber   : Mapping_MCI_MAR26_01042026.sql / MCI_MAR26_01042026.xlsx
 *
 * CATATAN:
 *  - Model ini READ-ONLY (lihat trait ReadOnlyModel di MciBaseModel).
 *  - Koneksi default `dashboard_data` (SQL Server), otomatis ke DB bulan terbaru.
 *  - Kolom dengan spasi/karakter khusus diakses via:
 *    $model->getAttribute('NAMA KOLOM')  atau  $model->{'NAMA KOLOM'}.
 *
 * @property string|null $nosbb  type: varchar(11)
 * @property string|null $nobb  type: varchar(11)
 * @property string|null $golongan  type: varchar(1)
 * @property string|null $thn  type: varchar(4)
 * @property string|null $bln  type: varchar(2)
 * @property string|null $kdao  type: varchar(8)
 * @property string|null $saldo1  type: varchar(150)
 * @property string|null $saldo2  type: varchar(150)
 * @property string|null $saldo3  type: varchar(165)
 */
class Nrcao extends MciBaseModel
{
    /**
     * Nama tabel (case-sensitive di SQL Server).
     */
    protected $table = 'NRCAO';

    /**
     * Daftar LENGKAP kolom sesuai database (9 kolom).
     * Model ini read-only, $fillable hanya untuk dokumentasi & IDE helper.
     *
     * @var array<int,string>
     */
    protected $fillable = [
        'nosbb',
        'nobb',
        'golongan',
        'thn',
        'bln',
        'kdao',
        'saldo1',
        'saldo2',
        'saldo3',
    ];

    /**
     * Casting tipe kolom (non-string saja).
     *
     * @var array<string,string>
     */
    protected $casts = [

    ];
}
