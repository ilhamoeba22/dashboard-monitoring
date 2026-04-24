<?php

namespace App\Models\Mci\Giro;

use App\Models\Mci\MciBaseModel;

/**
 * Model MCI: TOFRIB
 * --------------------------------------------------------------------------
 * Domain   : Giro / RK
 * Tabel    : [dbo].[TOFRIB]
 * Kolom    : 30
 * Sumber   : Mapping_MCI_MAR26_01042026.sql / MCI_MAR26_01042026.xlsx
 *
 * CATATAN:
 *  - Model ini READ-ONLY (lihat trait ReadOnlyModel di MciBaseModel).
 *  - Koneksi default `dashboard_data` (SQL Server), otomatis ke DB bulan terbaru.
 *  - Kolom dengan spasi/karakter khusus diakses via:
 *    $model->getAttribute('NAMA KOLOM')  atau  $model->{'NAMA KOLOM'}.
 *
 * @property string $nori  type: varchar(11)
 * @property string|null $nmri  type: varchar(30)
 * @property string|null $kdcab  type: varchar(3)
 * @property string|null $kdloc  type: varchar(2)
 * @property string|null $golnori  type: varchar(1)
 * @property string|null $cc  type: varchar(2)
 * @property string|null $kurs  type: numeric(9)
 * @property string|null $nosbb1  type: varchar(11)
 * @property string|null $plafond  type: numeric(9)
 * @property string|null $proofsheet  type: varchar(1)
 * @property string|null $sawalrp  type: numeric(9)
 * @property string|null $sawalva  type: numeric(9)
 * @property string|null $mtsdr  type: numeric(9)
 * @property string|null $mtscr  type: numeric(9)
 * @property string|null $sahirrp  type: numeric(9)
 * @property string|null $sahirva  type: numeric(9)
 * @property string|null $retensi  type: numeric(5)
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
 * @property string|null $deluser  type: varchar(10)
 * @property string|null $deltgl  type: varchar(14)
 * @property string|null $delterm  type: varchar(10)
 */
class Tofrib extends MciBaseModel
{
    /**
     * Nama tabel (case-sensitive di SQL Server).
     */
    protected $table = 'TOFRIB';

    /**
     * Daftar LENGKAP kolom sesuai database (30 kolom).
     * Model ini read-only, $fillable hanya untuk dokumentasi & IDE helper.
     *
     * @var array<int,string>
     */
    protected $fillable = [
        'nori',
        'nmri',
        'kdcab',
        'kdloc',
        'golnori',
        'cc',
        'kurs',
        'nosbb1',
        'plafond',
        'proofsheet',
        'sawalrp',
        'sawalva',
        'mtsdr',
        'mtscr',
        'sahirrp',
        'sahirva',
        'retensi',
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
        'deluser',
        'deltgl',
        'delterm',
    ];

    /**
     * Casting tipe kolom (non-string saja).
     *
     * @var array<string,string>
     */
    protected $casts = [
        'kurs' => 'decimal:2',
        'plafond' => 'decimal:2',
        'sawalrp' => 'decimal:2',
        'sawalva' => 'decimal:2',
        'mtsdr' => 'decimal:2',
        'mtscr' => 'decimal:2',
        'sahirrp' => 'decimal:2',
        'sahirva' => 'decimal:2',
        'retensi' => 'decimal:2',
    ];
}
