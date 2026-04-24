<?php

namespace App\Models\Mci\Lainnya;

use App\Models\Mci\MciBaseModel;

/**
 * Model MCI: TOFMBDD
 * --------------------------------------------------------------------------
 * Domain   : Lainnya
 * Tabel    : [dbo].[TOFMBDD]
 * Kolom    : 23
 * Sumber   : Mapping_MCI_MAR26_01042026.sql / MCI_MAR26_01042026.xlsx
 *
 * CATATAN:
 *  - Model ini READ-ONLY (lihat trait ReadOnlyModel di MciBaseModel).
 *  - Koneksi default `dashboard_data` (SQL Server), otomatis ke DB bulan terbaru.
 *  - Kolom dengan spasi/karakter khusus diakses via:
 *    $model->getAttribute('NAMA KOLOM')  atau  $model->{'NAMA KOLOM'}.
 *
 * @property int $ID  type: bigint(8)
 * @property string|null $nobdd  type: varchar(10)
 * @property string|null $tgltrn  type: varchar(8)
 * @property string|null $batch  type: numeric(5)
 * @property string|null $notrn  type: numeric(5)
 * @property string|null $kdloc  type: char(2)
 * @property string|null $nmbdd  type: varchar(50)
 * @property string|null $nombdd  type: numeric(9)
 * @property string|null $nomamort  type: numeric(9)
 * @property string|null $totamort  type: numeric(9)
 * @property string|null $tglmulai  type: varchar(8)
 * @property string|null $tglnext  type: varchar(8)
 * @property string|null $tglproc  type: varchar(8)
 * @property string|null $jw  type: numeric(5)
 * @property string|null $qty  type: numeric(5)
 * @property string|null $stsamort  type: char(1)
 * @property string|null $stsbdd  type: char(1)
 * @property string|null $sbbbdd  type: varchar(7)
 * @property string|null $sbbbybdd  type: varchar(7)
 * @property string|null $stsrec  type: char(1)
 * @property string|null $inpuser  type: varchar(10)
 * @property string|null $inptgljam  type: varchar(14)
 * @property string|null $inpterm  type: varchar(10)
 */
class Tofmbdd extends MciBaseModel
{
    /**
     * Nama tabel (case-sensitive di SQL Server).
     */
    protected $table = 'TOFMBDD';

    /**
     * Daftar LENGKAP kolom sesuai database (23 kolom).
     * Model ini read-only, $fillable hanya untuk dokumentasi & IDE helper.
     *
     * @var array<int,string>
     */
    protected $fillable = [
        'ID',
        'nobdd',
        'tgltrn',
        'batch',
        'notrn',
        'kdloc',
        'nmbdd',
        'nombdd',
        'nomamort',
        'totamort',
        'tglmulai',
        'tglnext',
        'tglproc',
        'jw',
        'qty',
        'stsamort',
        'stsbdd',
        'sbbbdd',
        'sbbbybdd',
        'stsrec',
        'inpuser',
        'inptgljam',
        'inpterm',
    ];

    /**
     * Casting tipe kolom (non-string saja).
     *
     * @var array<string,string>
     */
    protected $casts = [
        'ID' => 'integer',
        'batch' => 'decimal:2',
        'notrn' => 'decimal:2',
        'nombdd' => 'decimal:2',
        'nomamort' => 'decimal:2',
        'totamort' => 'decimal:2',
        'jw' => 'decimal:2',
        'qty' => 'decimal:2',
    ];
}
