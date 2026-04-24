<?php

namespace App\Models\Mci\Lainnya;

use App\Models\Mci\MciBaseModel;

/**
 * Model MCI: TOFTFEETRF
 * --------------------------------------------------------------------------
 * Domain   : Lainnya
 * Tabel    : [dbo].[TOFTFEETRF]
 * Kolom    : 26
 * Sumber   : Mapping_MCI_MAR26_01042026.sql / MCI_MAR26_01042026.xlsx
 *
 * CATATAN:
 *  - Model ini READ-ONLY (lihat trait ReadOnlyModel di MciBaseModel).
 *  - Koneksi default `dashboard_data` (SQL Server), otomatis ke DB bulan terbaru.
 *  - Kolom dengan spasi/karakter khusus diakses via:
 *    $model->getAttribute('NAMA KOLOM')  atau  $model->{'NAMA KOLOM'}.
 *
 * @property int $ID  type: bigint(8)
 * @property string $kdtrf  type: varchar(10)
 * @property string $ket  type: varchar(50)
 * @property string $kdbank  type: varchar(10)
 * @property string $reknostro  type: varchar(20)
 * @property string|null $mediatrf  type: char(1)
 * @property string|null $kdnsbah  type: char(1)
 * @property string|null $bytrf  type: numeric(9)
 * @property string|null $feetrfbu  type: numeric(9)
 * @property string|null $feetrfbprs  type: numeric(9)
 * @property string|null $feetrfmgp  type: numeric(9)
 * @property string $sbbnostro  type: varchar(11)
 * @property string|null $sbbttptrf  type: varchar(11)
 * @property string|null $sbbpendtrf  type: varchar(11)
 * @property string|null $sbbttpmgp  type: varchar(11)
 * @property string|null $stsrec  type: char(1)
 * @property string $inpuser  type: varchar(10)
 * @property string $inptgljam  type: varchar(14)
 * @property string $inpterm  type: varchar(10)
 * @property string $chguser  type: varchar(10)
 * @property string $chgtgljam  type: varchar(14)
 * @property string $chgterm  type: varchar(10)
 * @property string $autuser  type: varchar(10)
 * @property string $auttgljam  type: varchar(14)
 * @property string $autterm  type: varchar(10)
 * @property string|null $useridtrf  type: varchar(30)
 */
class Toftfeetrf extends MciBaseModel
{
    /**
     * Nama tabel (case-sensitive di SQL Server).
     */
    protected $table = 'TOFTFEETRF';

    /**
     * Daftar LENGKAP kolom sesuai database (26 kolom).
     * Model ini read-only, $fillable hanya untuk dokumentasi & IDE helper.
     *
     * @var array<int,string>
     */
    protected $fillable = [
        'ID',
        'kdtrf',
        'ket',
        'kdbank',
        'reknostro',
        'mediatrf',
        'kdnsbah',
        'bytrf',
        'feetrfbu',
        'feetrfbprs',
        'feetrfmgp',
        'sbbnostro',
        'sbbttptrf',
        'sbbpendtrf',
        'sbbttpmgp',
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
        'useridtrf',
    ];

    /**
     * Casting tipe kolom (non-string saja).
     *
     * @var array<string,string>
     */
    protected $casts = [
        'ID' => 'integer',
        'bytrf' => 'decimal:2',
        'feetrfbu' => 'decimal:2',
        'feetrfbprs' => 'decimal:2',
        'feetrfmgp' => 'decimal:2',
    ];
}
