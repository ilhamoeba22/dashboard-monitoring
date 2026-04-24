<?php

namespace App\Models\Mci\SetupConfig;

use App\Models\Mci\MciBaseModel;

/**
 * Model MCI: SETUPTRANS
 * --------------------------------------------------------------------------
 * Domain   : Setup / Config
 * Tabel    : [dbo].[SETUPTRANS]
 * Kolom    : 46
 * Sumber   : Mapping_MCI_MAR26_01042026.sql / MCI_MAR26_01042026.xlsx
 *
 * CATATAN:
 *  - Model ini READ-ONLY (lihat trait ReadOnlyModel di MciBaseModel).
 *  - Koneksi default `dashboard_data` (SQL Server), otomatis ke DB bulan terbaru.
 *  - Kolom dengan spasi/karakter khusus diakses via:
 *    $model->getAttribute('NAMA KOLOM')  atau  $model->{'NAMA KOLOM'}.
 *
 * @property string $kdtrans  type: varchar(4)
 * @property string|null $ket  type: varchar(40)
 * @property string|null $mtdpost  type: varchar(1)
 * @property string|null $mainmodul  type: varchar(1)
 * @property string|null $offmodul  type: varchar(1)
 * @property string|null $dc  type: varchar(1)
 * @property string|null $indkurs  type: varchar(2)
 * @property string|null $warkat  type: varchar(1)
 * @property string|null $usiawark  type: numeric(5)
 * @property string|null $limitldr  type: varchar(1)
 * @property string|null $limitlcr  type: varchar(1)
 * @property string|null $limitcdr  type: varchar(1)
 * @property string|null $limitccr  type: varchar(1)
 * @property string|null $jnsterm  type: varchar(1)
 * @property string|null $verterm  type: varchar(1)
 * @property string|null $verkurs  type: varchar(1)
 * @property string|null $ststrnldr  type: varchar(1)
 * @property string|null $ststrnlcr  type: varchar(1)
 * @property string|null $ststrncdr  type: varchar(1)
 * @property string|null $ststrnccr  type: varchar(1)
 * @property string|null $ststerm  type: varchar(1)
 * @property string|null $stswarkat  type: varchar(1)
 * @property string|null $stsodgiro  type: varchar(1)
 * @property string|null $viamodul  type: varchar(1)
 * @property string|null $mainkas  type: varchar(7)
 * @property string|null $offkas  type: varchar(7)
 * @property string|null $kdbuku  type: varchar(1)
 * @property string|null $stsvaltrn  type: varchar(1)
 * @property string|null $hiden  type: varchar(1)
 * @property string|null $stsrec  type: varchar(1)
 * @property string|null $inpuser  type: varchar(10)
 * @property string|null $inptgl  type: varchar(14)
 * @property string|null $inpterm  type: varchar(10)
 * @property string|null $chguser  type: varchar(10)
 * @property string|null $chgtgl  type: varchar(14)
 * @property string|null $chgterm  type: varchar(10)
 * @property string|null $autuser  type: varchar(10)
 * @property string|null $auttgl  type: varchar(14)
 * @property string|null $autterm  type: varchar(10)
 * @property string|null $kdtrnbuku  type: varchar(2)
 * @property string|null $maincc  type: varchar(1)
 * @property string|null $offcc  type: varchar(1)
 * @property string|null $drcc  type: varchar(2)
 * @property string|null $crcc  type: varchar(2)
 * @property string|null $kettrn  type: varchar(30)
 * @property string|null $stsforce_pending  type: varchar(1)
 */
class Setuptrans extends MciBaseModel
{
    /**
     * Nama tabel (case-sensitive di SQL Server).
     */
    protected $table = 'SETUPTRANS';

    /**
     * Daftar LENGKAP kolom sesuai database (46 kolom).
     * Model ini read-only, $fillable hanya untuk dokumentasi & IDE helper.
     *
     * @var array<int,string>
     */
    protected $fillable = [
        'kdtrans',
        'ket',
        'mtdpost',
        'mainmodul',
        'offmodul',
        'dc',
        'indkurs',
        'warkat',
        'usiawark',
        'limitldr',
        'limitlcr',
        'limitcdr',
        'limitccr',
        'jnsterm',
        'verterm',
        'verkurs',
        'ststrnldr',
        'ststrnlcr',
        'ststrncdr',
        'ststrnccr',
        'ststerm',
        'stswarkat',
        'stsodgiro',
        'viamodul',
        'mainkas',
        'offkas',
        'kdbuku',
        'stsvaltrn',
        'hiden',
        'stsrec',
        'inpuser',
        'inptgl',
        'inpterm',
        'chguser',
        'chgtgl',
        'chgterm',
        'autuser',
        'auttgl',
        'autterm',
        'kdtrnbuku',
        'maincc',
        'offcc',
        'drcc',
        'crcc',
        'kettrn',
        'stsforce_pending',
    ];

    /**
     * Casting tipe kolom (non-string saja).
     *
     * @var array<string,string>
     */
    protected $casts = [
        'usiawark' => 'decimal:2',
    ];
}
