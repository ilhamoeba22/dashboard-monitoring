<?php

namespace App\Models\Mci\Transaksi;

use App\Models\Mci\MciBaseModel;

/**
 * Model MCI: TOFTRNC
 * --------------------------------------------------------------------------
 * Domain   : Transaksi
 * Tabel    : [dbo].[TOFTRNC]
 * Kolom    : 68
 * Sumber   : Mapping_MCI_MAR26_01042026.sql / MCI_MAR26_01042026.xlsx
 *
 * CATATAN:
 *  - Model ini READ-ONLY (lihat trait ReadOnlyModel di MciBaseModel).
 *  - Koneksi default `dashboard_data` (SQL Server), otomatis ke DB bulan terbaru.
 *  - Kolom dengan spasi/karakter khusus diakses via:
 *    $model->getAttribute('NAMA KOLOM')  atau  $model->{'NAMA KOLOM'}.
 *
 * @property string|null $tgltrn  type: varchar(8)
 * @property string|null $trnuser  type: varchar(10)
 * @property string|null $batch  type: numeric(5)
 * @property string|null $notrn  type: numeric(5)
 * @property string|null $kodetrn  type: varchar(4)
 * @property string|null $dracc  type: varchar(11)
 * @property string|null $drmodul  type: varchar(1)
 * @property string|null $drcc  type: varchar(2)
 * @property string|null $drkdcab  type: varchar(3)
 * @property string|null $drkdloc  type: varchar(2)
 * @property string|null $cracc  type: varchar(11)
 * @property string|null $crmodul  type: varchar(1)
 * @property string|null $crcc  type: varchar(2)
 * @property string|null $crkdcab  type: varchar(3)
 * @property string|null $crkdloc  type: varchar(2)
 * @property string|null $dc  type: varchar(1)
 * @property string|null $dokumen  type: varchar(40)
 * @property string|null $tgldok  type: varchar(8)
 * @property string|null $nominalrp  type: numeric(9)
 * @property string|null $nominalva  type: numeric(9)
 * @property string|null $kurs  type: numeric(9)
 * @property string|null $nominalod  type: numeric(9)
 * @property string|null $tglval  type: varchar(8)
 * @property string|null $ket  type: varchar(512)
 * @property string|null $kdcab  type: varchar(3)
 * @property string|null $kdloc  type: varchar(2)
 * @property string|null $kdkas  type: varchar(2)
 * @property string|null $prog  type: varchar(10)
 * @property string|null $groupno  type: numeric(5)
 * @property string|null $modul  type: varchar(1)
 * @property string|null $sbbperdr  type: varchar(11)
 * @property string|null $sbbpercr  type: varchar(11)
 * @property string|null $stscetak  type: varchar(1)
 * @property string|null $thnbln  type: varchar(6)
 * @property string|null $jnstrnlx  type: varchar(3)
 * @property string|null $jnstrntx  type: varchar(2)
 * @property string|null $trnkedr  type: numeric(5)
 * @property string|null $trnkecr  type: numeric(5)
 * @property string|null $tgltagihan  type: varchar(8)
 * @property string|null $ststrn  type: varchar(1)
 * @property string|null $inpuser  type: varchar(10)
 * @property string|null $inptgl  type: varchar(14)
 * @property string|null $inpterm  type: varchar(10)
 * @property string|null $autuser  type: varchar(10)
 * @property string|null $auttgl  type: varchar(14)
 * @property string|null $autterm  type: varchar(10)
 * @property string|null $realuser  type: varchar(10)
 * @property string|null $realtgl  type: varchar(14)
 * @property string|null $realterm  type: varchar(10)
 * @property string|null $depfrom  type: varchar(3)
 * @property string|null $depto  type: varchar(3)
 * @property string|null $kdtrnbuku  type: varchar(2)
 * @property string|null $kdaodr  type: varchar(8)
 * @property string|null $kdaocr  type: varchar(8)
 * @property string|null $noreff  type: varchar(11)
 * @property string|null $modreff  type: varchar(1)
 * @property string|null $dcreff  type: varchar(1)
 * @property string|null $segmendr  type: varchar(5)
 * @property string|null $segmencr  type: varchar(5)
 * @property string|null $nocifdr  type: varchar(9)
 * @property string|null $nocifcr  type: varchar(9)
 * @property string|null $stskas  type: varchar(1)
 * @property string|null $pokok  type: numeric(9)
 * @property string|null $margin  type: numeric(9)
 * @property string|null $kdtrncal  type: char(4)
 * @property string|null $namadr  type: char(20)
 * @property string|null $namacr  type: char(20)
 * @property int $ID  type: bigint(8)
 */
class Toftrnc extends MciBaseModel
{
    /**
     * Nama tabel (case-sensitive di SQL Server).
     */
    protected $table = 'TOFTRNC';

    /**
     * Daftar LENGKAP kolom sesuai database (68 kolom).
     * Model ini read-only, $fillable hanya untuk dokumentasi & IDE helper.
     *
     * @var array<int,string>
     */
    protected $fillable = [
        'tgltrn',
        'trnuser',
        'batch',
        'notrn',
        'kodetrn',
        'dracc',
        'drmodul',
        'drcc',
        'drkdcab',
        'drkdloc',
        'cracc',
        'crmodul',
        'crcc',
        'crkdcab',
        'crkdloc',
        'dc',
        'dokumen',
        'tgldok',
        'nominalrp',
        'nominalva',
        'kurs',
        'nominalod',
        'tglval',
        'ket',
        'kdcab',
        'kdloc',
        'kdkas',
        'prog',
        'groupno',
        'modul',
        'sbbperdr',
        'sbbpercr',
        'stscetak',
        'thnbln',
        'jnstrnlx',
        'jnstrntx',
        'trnkedr',
        'trnkecr',
        'tgltagihan',
        'ststrn',
        'inpuser',
        'inptgl',
        'inpterm',
        'autuser',
        'auttgl',
        'autterm',
        'realuser',
        'realtgl',
        'realterm',
        'depfrom',
        'depto',
        'kdtrnbuku',
        'kdaodr',
        'kdaocr',
        'noreff',
        'modreff',
        'dcreff',
        'segmendr',
        'segmencr',
        'nocifdr',
        'nocifcr',
        'stskas',
        'pokok',
        'margin',
        'kdtrncal',
        'namadr',
        'namacr',
        'ID',
    ];

    /**
     * Casting tipe kolom (non-string saja).
     *
     * @var array<string,string>
     */
    protected $casts = [
        'batch' => 'decimal:2',
        'notrn' => 'decimal:2',
        'nominalrp' => 'decimal:2',
        'nominalva' => 'decimal:2',
        'kurs' => 'decimal:2',
        'nominalod' => 'decimal:2',
        'groupno' => 'decimal:2',
        'trnkedr' => 'decimal:2',
        'trnkecr' => 'decimal:2',
        'pokok' => 'decimal:2',
        'margin' => 'decimal:2',
        'ID' => 'integer',
    ];
}
