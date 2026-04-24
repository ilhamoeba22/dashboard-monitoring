<?php

namespace App\Models\Mci\Lainnya;

use App\Models\Mci\MciBaseModel;

/**
 * Model MCI: H_GLTRN
 * --------------------------------------------------------------------------
 * Domain   : Lainnya
 * Tabel    : [dbo].[H_GLTRN]
 * Kolom    : 40
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
 * @property string|null $noacc  type: varchar(11)
 * @property string|null $noacclawan  type: varchar(11)
 * @property string|null $modullawan  type: varchar(1)
 * @property string|null $dc  type: varchar(1)
 * @property string|null $dokumen  type: varchar(40)
 * @property string|null $nominal  type: numeric(9)
 * @property string|null $tglval  type: varchar(8)
 * @property string|null $ket  type: varchar(512)
 * @property string|null $kdcab  type: varchar(3)
 * @property string|null $kdloc  type: varchar(2)
 * @property string|null $sbbper  type: varchar(11)
 * @property string|null $ststrn  type: varchar(1)
 * @property string|null $inpuser  type: varchar(10)
 * @property string|null $inptgljam  type: varchar(14)
 * @property string|null $inpterm  type: varchar(10)
 * @property string|null $chguser  type: varchar(10)
 * @property string|null $chgtgljam  type: varchar(14)
 * @property string|null $chgterm  type: varchar(10)
 * @property string|null $autuser  type: varchar(10)
 * @property string|null $auttgljam  type: varchar(14)
 * @property string|null $autterm  type: varchar(10)
 * @property string|null $prog  type: varchar(10)
 * @property string|null $groupno  type: numeric(5)
 * @property string|null $modul  type: varchar(1)
 * @property string|null $stscetak  type: varchar(1)
 * @property string|null $thnbln  type: varchar(6)
 * @property string|null $jnstrnlx  type: varchar(3)
 * @property string|null $jnstrntx  type: varchar(2)
 * @property string|null $proofsheet  type: varchar(1)
 * @property string|null $stscair  type: varchar(1)
 * @property string|null $tglcair  type: varchar(8)
 * @property string|null $trnke  type: numeric(5)
 * @property string|null $kdao  type: varchar(8)
 * @property string|null $kdkol  type: varchar(8)
 * @property int|null $ID  type: bigint(8)
 */
class HGltrn extends MciBaseModel
{
    /**
     * Nama tabel (case-sensitive di SQL Server).
     */
    protected $table = 'H_GLTRN';

    /**
     * Daftar LENGKAP kolom sesuai database (40 kolom).
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
        'noacc',
        'noacclawan',
        'modullawan',
        'dc',
        'dokumen',
        'nominal',
        'tglval',
        'ket',
        'kdcab',
        'kdloc',
        'sbbper',
        'ststrn',
        'inpuser',
        'inptgljam',
        'inpterm',
        'chguser',
        'chgtgljam',
        'chgterm',
        'autuser',
        'auttgljam',
        'autterm',
        'prog',
        'groupno',
        'modul',
        'stscetak',
        'thnbln',
        'jnstrnlx',
        'jnstrntx',
        'proofsheet',
        'stscair',
        'tglcair',
        'trnke',
        'kdao',
        'kdkol',
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
        'nominal' => 'decimal:2',
        'groupno' => 'decimal:2',
        'trnke' => 'decimal:2',
        'ID' => 'integer',
    ];
}
