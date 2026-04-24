<?php

namespace App\Models\Mci\Lainnya;

use App\Models\Mci\MciBaseModel;

/**
 * Model MCI: H_VACTRN
 * --------------------------------------------------------------------------
 * Domain   : Lainnya
 * Tabel    : [dbo].[H_VACTRN]
 * Kolom    : 26
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
 * @property string|null $dokumen  type: varchar(20)
 * @property string|null $nominal  type: numeric(9)
 * @property string|null $tglval  type: varchar(8)
 * @property string|null $ket  type: varchar(50)
 * @property string|null $kdcab  type: varchar(3)
 * @property string|null $kdloc  type: varchar(2)
 * @property string|null $sbbper  type: varchar(11)
 * @property string|null $jnstrnlx  type: varchar(2)
 * @property string|null $noreff  type: varchar(30)
 * @property string|null $ststrn  type: varchar(1)
 * @property string|null $inpuser  type: varchar(10)
 * @property string|null $inptgljam  type: varchar(14)
 * @property string|null $inpterm  type: varchar(10)
 * @property string|null $autuser  type: varchar(10)
 * @property string|null $auttgljam  type: varchar(14)
 * @property string|null $autterm  type: varchar(10)
 * @property int|null $ID  type: bigint(8)
 */
class HVactrn extends MciBaseModel
{
    /**
     * Nama tabel (case-sensitive di SQL Server).
     */
    protected $table = 'H_VACTRN';

    /**
     * Daftar LENGKAP kolom sesuai database (26 kolom).
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
        'jnstrnlx',
        'noreff',
        'ststrn',
        'inpuser',
        'inptgljam',
        'inpterm',
        'autuser',
        'auttgljam',
        'autterm',
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
        'ID' => 'integer',
    ];
}
