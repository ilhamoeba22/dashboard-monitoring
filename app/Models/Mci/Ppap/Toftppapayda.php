<?php

declare(strict_types=1);

namespace App\Models\Mci\Ppap;

use App\Models\Mci\MciBaseModel;

/**
 * Model MCI: TOFTPPAPAYDA
 * --------------------------------------------------------------------------
 * Domain   : PPKA / DPD / Coll
 * Tabel    : [dbo].[TOFTPPAPAYDA]
 * Kolom    : 19
 * Sumber   : Mapping_MCI_MAR26_01042026.sql / MCI_MAR26_01042026.xlsx
 *
 * CATATAN:
 *  - Model ini READ-ONLY (lihat trait ReadOnlyModel di MciBaseModel).
 *  - Koneksi default `dashboard_data` (SQL Server), otomatis ke DB bulan terbaru.
 *  - Kolom dengan spasi/karakter khusus diakses via:
 *    $model->getAttribute('NAMA KOLOM')  atau  $model->{'NAMA KOLOM'}.
 *
 * @property string|null $ppap_01 type: numeric(5)
 * @property string|null $ppap_02 type: numeric(5)
 * @property string|null $ppap_03 type: numeric(5)
 * @property string|null $sbbbyppap type: varchar(7)
 * @property string|null $sbbppap type: varchar(7)
 * @property string|null $autopost type: varchar(1)
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
 * @property string|null $sbblaba type: varchar(7)
 * @property string|null $sbbrugi type: varchar(7)
 * @property string|null $sbbttp type: varchar(7)
 */
class Toftppapayda extends MciBaseModel
{
    /**
     * Nama tabel (case-sensitive di SQL Server).
     */
    protected $table = 'TOFTPPAPAYDA';

    /**
     * Daftar LENGKAP kolom sesuai database (19 kolom).
     * Model ini read-only, $fillable hanya untuk dokumentasi & IDE helper.
     *
     * @var array<int,string>
     */
    protected $fillable = [
        'ppap_01',
        'ppap_02',
        'ppap_03',
        'sbbbyppap',
        'sbbppap',
        'autopost',
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
        'sbblaba',
        'sbbrugi',
        'sbbttp',
    ];

    /**
     * Casting tipe kolom (non-string saja).
     *
     * @var array<string,string>
     */
    protected $casts = [
        'ppap_01' => 'decimal:2',
        'ppap_02' => 'decimal:2',
        'ppap_03' => 'decimal:2',
    ];
}
