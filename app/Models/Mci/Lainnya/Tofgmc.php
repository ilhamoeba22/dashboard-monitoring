<?php

namespace App\Models\Mci\Lainnya;

use App\Models\Mci\MciBaseModel;

/**
 * Model MCI: TOFGMC
 * --------------------------------------------------------------------------
 * Domain   : Lainnya
 * Tabel    : [dbo].[TOFGMC]
 * Kolom    : 60
 * Sumber   : Mapping_MCI_MAR26_01042026.sql / MCI_MAR26_01042026.xlsx
 *
 * CATATAN:
 *  - Model ini READ-ONLY (lihat trait ReadOnlyModel di MciBaseModel).
 *  - Koneksi default `dashboard_data` (SQL Server), otomatis ke DB bulan terbaru.
 *  - Kolom dengan spasi/karakter khusus diakses via:
 *    $model->getAttribute('NAMA KOLOM')  atau  $model->{'NAMA KOLOM'}.
 *
 * @property string|null $nosbg  type: varchar(20)
 * @property string|null $nocif  type: varchar(9)
 * @property string|null $nokontrak  type: varchar(11)
 * @property string|null $noakad  type: varchar(50)
 * @property string|null $noaplikasi  type: varchar(20)
 * @property string|null $kdsewa  type: varchar(2)
 * @property string|null $kdprd  type: varchar(2)
 * @property string|null $kdcab  type: varchar(3)
 * @property string|null $kdloc  type: varchar(2)
 * @property string|null $nomtaksiran  type: numeric(9)
 * @property string|null $nompinjaman  type: numeric(9)
 * @property string|null $deviasi  type: numeric(5)
 * @property string|null $discbysewa  type: numeric(9)
 * @property string|null $bysewa  type: numeric(9)
 * @property string|null $byasr  type: numeric(9)
 * @property string|null $byadm  type: numeric(9)
 * @property string|null $bymaterai  type: numeric(9)
 * @property string|null $qty  type: numeric(9)
 * @property string|null $jw  type: numeric(5)
 * @property string|null $jnsjw  type: varchar(1)
 * @property string|null $tgleff  type: varchar(8)
 * @property string|null $tgljtempo  type: varchar(8)
 * @property string|null $tgllelang  type: varchar(8)
 * @property string|null $nomacrubln  type: numeric(9)
 * @property string|null $totacru  type: numeric(9)
 * @property string|null $tglacru  type: varchar(8)
 * @property string|null $tglacrunext  type: varchar(8)
 * @property string|null $gadaike  type: numeric(5)
 * @property string|null $nobilyet  type: varchar(10)
 * @property string|null $cetakke  type: numeric(5)
 * @property string|null $noacc  type: varchar(11)
 * @property string|null $ststrn  type: varchar(1)
 * @property string|null $stscair  type: varchar(1)
 * @property string|null $stsrec  type: varchar(1)
 * @property string|null $inpuser  type: varchar(10)
 * @property string|null $inptgljam  type: varchar(14)
 * @property string|null $inpterm  type: varchar(10)
 * @property string|null $chguser  type: varchar(10)
 * @property string|null $chgtgljam  type: varchar(14)
 * @property string|null $chgterm  type: varchar(10)
 * @property string|null $autuser  type: varchar(10)
 * @property string|null $auttgljam  type: varchar(14)
 * @property string|null $autterm  type: varchar(10)
 * @property string|null $AO  type: varchar(8)
 * @property string|null $sekon  type: varchar(10)
 * @property string|null $byfotocopy  type: numeric(9)
 * @property string|null $stslelang  type: varchar(1)
 * @property string|null $ketlelang  type: varchar(150)
 * @property string|null $ststo  type: varchar(2)
 * @property string|null $tglin  type: varchar(8)
 * @property string|null $tgleffto  type: varchar(8)
 * @property string|null $tglexpto  type: varchar(8)
 * @property string|null $ststrnto  type: varchar(2)
 * @property string|null $lb_tujuan  type: varchar(10)
 * @property string|null $jnsguna  type: varchar(1)
 * @property string|null $ujrah  type: numeric(9)
 * @property string|null $stssewa  type: varchar(1)
 * @property string|null $disc  type: numeric(9)
 * @property string|null $segmen  type: varchar(3)
 * @property string|null $glb  type: varchar(2)
 */
class Tofgmc extends MciBaseModel
{
    /**
     * Nama tabel (case-sensitive di SQL Server).
     */
    protected $table = 'TOFGMC';

    /**
     * Daftar LENGKAP kolom sesuai database (60 kolom).
     * Model ini read-only, $fillable hanya untuk dokumentasi & IDE helper.
     *
     * @var array<int,string>
     */
    protected $fillable = [
        'nosbg',
        'nocif',
        'nokontrak',
        'noakad',
        'noaplikasi',
        'kdsewa',
        'kdprd',
        'kdcab',
        'kdloc',
        'nomtaksiran',
        'nompinjaman',
        'deviasi',
        'discbysewa',
        'bysewa',
        'byasr',
        'byadm',
        'bymaterai',
        'qty',
        'jw',
        'jnsjw',
        'tgleff',
        'tgljtempo',
        'tgllelang',
        'nomacrubln',
        'totacru',
        'tglacru',
        'tglacrunext',
        'gadaike',
        'nobilyet',
        'cetakke',
        'noacc',
        'ststrn',
        'stscair',
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
        'AO',
        'sekon',
        'byfotocopy',
        'stslelang',
        'ketlelang',
        'ststo',
        'tglin',
        'tgleffto',
        'tglexpto',
        'ststrnto',
        'lb_tujuan',
        'jnsguna',
        'ujrah',
        'stssewa',
        'disc',
        'segmen',
        'glb',
    ];

    /**
     * Casting tipe kolom (non-string saja).
     *
     * @var array<string,string>
     */
    protected $casts = [
        'nomtaksiran' => 'decimal:2',
        'nompinjaman' => 'decimal:2',
        'deviasi' => 'decimal:2',
        'discbysewa' => 'decimal:2',
        'bysewa' => 'decimal:2',
        'byasr' => 'decimal:2',
        'byadm' => 'decimal:2',
        'bymaterai' => 'decimal:2',
        'qty' => 'decimal:2',
        'jw' => 'decimal:2',
        'nomacrubln' => 'decimal:2',
        'totacru' => 'decimal:2',
        'gadaike' => 'decimal:2',
        'cetakke' => 'decimal:2',
        'byfotocopy' => 'decimal:2',
        'ujrah' => 'decimal:2',
        'disc' => 'decimal:2',
    ];
}
