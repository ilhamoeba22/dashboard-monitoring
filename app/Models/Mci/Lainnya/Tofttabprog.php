<?php

namespace App\Models\Mci\Lainnya;

use App\Models\Mci\MciBaseModel;

/**
 * Model MCI: TOFTTABPROG
 * --------------------------------------------------------------------------
 * Domain   : Lainnya
 * Tabel    : [dbo].[TOFTTABPROG]
 * Kolom    : 33
 * Sumber   : Mapping_MCI_MAR26_01042026.sql / MCI_MAR26_01042026.xlsx
 *
 * CATATAN:
 *  - Model ini READ-ONLY (lihat trait ReadOnlyModel di MciBaseModel).
 *  - Koneksi default `dashboard_data` (SQL Server), otomatis ke DB bulan terbaru.
 *  - Kolom dengan spasi/karakter khusus diakses via:
 *    $model->getAttribute('NAMA KOLOM')  atau  $model->{'NAMA KOLOM'}.
 *
 * @property int $ID  type: bigint(8)
 * @property string $kdprog  type: char(2)
 * @property string|null $ket  type: varchar(50)
 * @property string|null $tgleff  type: varchar(8)
 * @property string|null $tglexp  type: varchar(8)
 * @property string|null $nilai_hadiah  type: numeric(9)
 * @property string|null $kdhadiah  type: char(1)
 * @property string|null $sbbpershdh  type: varchar(7)
 * @property string|null $sbbbyhdh  type: varchar(7)
 * @property string|null $sbbcadhdh  type: varchar(7)
 * @property string|null $sbbbddhdh  type: varchar(7)
 * @property string|null $sbbbnshdh  type: varchar(7)
 * @property string|null $sbbdenda  type: varchar(7)
 * @property string|null $kdhdh  type: char(1)
 * @property string|null $setawal  type: numeric(9)
 * @property string|null $setnext  type: numeric(9)
 * @property string|null $jw  type: numeric(5)
 * @property string|null $saldorencana  type: numeric(9)
 * @property string|null $maxtgkn  type: numeric(5)
 * @property string|null $kddenda  type: char(1)
 * @property string|null $nisbah  type: numeric(5)
 * @property string|null $kdnisbah  type: char(1)
 * @property string|null $autoblok  type: char(1)
 * @property string|null $stsrec  type: char(1)
 * @property string|null $inpuser  type: varchar(10)
 * @property string|null $inptgljam  type: varchar(14)
 * @property string|null $inpterm  type: varchar(10)
 * @property string|null $chguser  type: varchar(10)
 * @property string|null $chgtgljam  type: varchar(14)
 * @property string|null $chgterm  type: varchar(10)
 * @property string|null $autuser  type: varchar(10)
 * @property string|null $auttgljam  type: varchar(14)
 * @property string|null $autterm  type: varchar(10)
 */
class Tofttabprog extends MciBaseModel
{
    /**
     * Nama tabel (case-sensitive di SQL Server).
     */
    protected $table = 'TOFTTABPROG';

    /**
     * Daftar LENGKAP kolom sesuai database (33 kolom).
     * Model ini read-only, $fillable hanya untuk dokumentasi & IDE helper.
     *
     * @var array<int,string>
     */
    protected $fillable = [
        'ID',
        'kdprog',
        'ket',
        'tgleff',
        'tglexp',
        'nilai_hadiah',
        'kdhadiah',
        'sbbpershdh',
        'sbbbyhdh',
        'sbbcadhdh',
        'sbbbddhdh',
        'sbbbnshdh',
        'sbbdenda',
        'kdhdh',
        'setawal',
        'setnext',
        'jw',
        'saldorencana',
        'maxtgkn',
        'kddenda',
        'nisbah',
        'kdnisbah',
        'autoblok',
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
        'ID' => 'integer',
        'nilai_hadiah' => 'decimal:2',
        'setawal' => 'decimal:2',
        'setnext' => 'decimal:2',
        'jw' => 'decimal:2',
        'saldorencana' => 'decimal:2',
        'maxtgkn' => 'decimal:2',
        'nisbah' => 'decimal:2',
    ];
}
