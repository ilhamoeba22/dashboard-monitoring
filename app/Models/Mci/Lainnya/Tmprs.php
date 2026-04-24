<?php

namespace App\Models\Mci\Lainnya;

use App\Models\Mci\MciBaseModel;

/**
 * Model MCI: TMPRS
 * --------------------------------------------------------------------------
 * Domain   : Lainnya
 * Tabel    : [dbo].[TMPRS]
 * Kolom    : 18
 * Sumber   : Mapping_MCI_MAR26_01042026.sql / MCI_MAR26_01042026.xlsx
 *
 * CATATAN:
 *  - Model ini READ-ONLY (lihat trait ReadOnlyModel di MciBaseModel).
 *  - Koneksi default `dashboard_data` (SQL Server), otomatis ke DB bulan terbaru.
 *  - Kolom dengan spasi/karakter khusus diakses via:
 *    $model->getAttribute('NAMA KOLOM')  atau  $model->{'NAMA KOLOM'}.
 *
 * @property string|null $nokontrak  type: varchar(11)
 * @property string|null $thn  type: varchar(4)
 * @property string|null $bln  type: varchar(2)
 * @property string|null $tgl  type: varchar(2)
 * @property string|null $tgltagih  type: varchar(8)
 * @property string|null $tgljt  type: varchar(8)
 * @property string|null $os  type: numeric(9)
 * @property string|null $tagmdl  type: numeric(9)
 * @property string|null $tagmgn  type: numeric(9)
 * @property string|null $tagdnd  type: numeric(9)
 * @property string|null $stsrec  type: varchar(1)
 * @property string|null $inpuser  type: varchar(10)
 * @property string|null $inptgljam  type: varchar(14)
 * @property string|null $inpterm  type: varchar(10)
 * @property string|null $porsi_bank  type: numeric(9)
 * @property string|null $porsi_cust  type: numeric(9)
 * @property string|null $porsi_bank_p  type: numeric(5)
 * @property string|null $porsi_cust_p  type: numeric(5)
 */
class Tmprs extends MciBaseModel
{
    /**
     * Nama tabel (case-sensitive di SQL Server).
     */
    protected $table = 'TMPRS';

    /**
     * Daftar LENGKAP kolom sesuai database (18 kolom).
     * Model ini read-only, $fillable hanya untuk dokumentasi & IDE helper.
     *
     * @var array<int,string>
     */
    protected $fillable = [
        'nokontrak',
        'thn',
        'bln',
        'tgl',
        'tgltagih',
        'tgljt',
        'os',
        'tagmdl',
        'tagmgn',
        'tagdnd',
        'stsrec',
        'inpuser',
        'inptgljam',
        'inpterm',
        'porsi_bank',
        'porsi_cust',
        'porsi_bank_p',
        'porsi_cust_p',
    ];

    /**
     * Casting tipe kolom (non-string saja).
     *
     * @var array<string,string>
     */
    protected $casts = [
        'os' => 'decimal:2',
        'tagmdl' => 'decimal:2',
        'tagmgn' => 'decimal:2',
        'tagdnd' => 'decimal:2',
        'porsi_bank' => 'decimal:2',
        'porsi_cust' => 'decimal:2',
        'porsi_bank_p' => 'decimal:2',
        'porsi_cust_p' => 'decimal:2',
    ];
}
