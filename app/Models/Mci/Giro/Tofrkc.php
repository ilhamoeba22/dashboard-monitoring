<?php

namespace App\Models\Mci\Giro;

use App\Models\Mci\MciBaseModel;

/**
 * Model MCI: TOFRKC
 * --------------------------------------------------------------------------
 * Domain   : Giro / RK
 * Tabel    : [dbo].[TOFRKC]
 * Kolom    : 48
 * Sumber   : Mapping_MCI_MAR26_01042026.sql / MCI_MAR26_01042026.xlsx
 *
 * CATATAN:
 *  - Model ini READ-ONLY (lihat trait ReadOnlyModel di MciBaseModel).
 *  - Koneksi default `dashboard_data` (SQL Server), otomatis ke DB bulan terbaru.
 *  - Kolom dengan spasi/karakter khusus diakses via:
 *    $model->getAttribute('NAMA KOLOM')  atau  $model->{'NAMA KOLOM'}.
 *
 * @property string $nork  type: varchar(11)
 * @property string|null $nama  type: varchar(30)
 * @property string|null $kdcab  type: varchar(3)
 * @property string|null $kdloc  type: varchar(2)
 * @property string|null $kdkas  type: varchar(2)
 * @property string|null $kdprd  type: varchar(2)
 * @property string|null $cc  type: varchar(2)
 * @property string|null $kurs  type: numeric(9)
 * @property string|null $limitrp  type: numeric(9)
 * @property string|null $limitva  type: numeric(9)
 * @property string|null $limitodrp  type: numeric(9)
 * @property string|null $limitodva  type: numeric(9)
 * @property string|null $blawalrp  type: numeric(9)
 * @property string|null $mtsdrrp  type: numeric(9)
 * @property string|null $mtscrrp  type: numeric(9)
 * @property string|null $blakhrp  type: numeric(9)
 * @property string|null $blawalva  type: numeric(9)
 * @property string|null $mtsdrva  type: numeric(9)
 * @property string|null $mtscrva  type: numeric(9)
 * @property string|null $blakhva  type: numeric(9)
 * @property string|null $glb  type: varchar(2)
 * @property string|null $segmen  type: varchar(3)
 * @property string|null $kdaoh  type: varchar(8)
 * @property string|null $blblokrp  type: numeric(9)
 * @property string|null $blblokva  type: numeric(9)
 * @property string|null $tgljtblok  type: varchar(8)
 * @property string|null $stsspc  type: varchar(1)
 * @property string|null $stskait  type: varchar(1)
 * @property string|null $sbbrk  type: varchar(7)
 * @property string|null $sbbod  type: varchar(7)
 * @property string|null $sbbprk  type: varchar(7)
 * @property string|null $kdwil  type: varchar(3)
 * @property string|null $tgltutup  type: varchar(8)
 * @property string|null $tgltrnakh  type: varchar(8)
 * @property string|null $stsacc  type: varchar(1)
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
 * @property string|null $stsblok  type: varchar(1)
 * @property string|null $saldoblok  type: numeric(9)
 * @property string|null $trnke  type: numeric(5)
 */
class Tofrkc extends MciBaseModel
{
    /**
     * Nama tabel (case-sensitive di SQL Server).
     */
    protected $table = 'TOFRKC';

    /**
     * Daftar LENGKAP kolom sesuai database (48 kolom).
     * Model ini read-only, $fillable hanya untuk dokumentasi & IDE helper.
     *
     * @var array<int,string>
     */
    protected $fillable = [
        'nork',
        'nama',
        'kdcab',
        'kdloc',
        'kdkas',
        'kdprd',
        'cc',
        'kurs',
        'limitrp',
        'limitva',
        'limitodrp',
        'limitodva',
        'blawalrp',
        'mtsdrrp',
        'mtscrrp',
        'blakhrp',
        'blawalva',
        'mtsdrva',
        'mtscrva',
        'blakhva',
        'glb',
        'segmen',
        'kdaoh',
        'blblokrp',
        'blblokva',
        'tgljtblok',
        'stsspc',
        'stskait',
        'sbbrk',
        'sbbod',
        'sbbprk',
        'kdwil',
        'tgltutup',
        'tgltrnakh',
        'stsacc',
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
        'stsblok',
        'saldoblok',
        'trnke',
    ];

    /**
     * Casting tipe kolom (non-string saja).
     *
     * @var array<string,string>
     */
    protected $casts = [
        'kurs' => 'decimal:2',
        'limitrp' => 'decimal:2',
        'limitva' => 'decimal:2',
        'limitodrp' => 'decimal:2',
        'limitodva' => 'decimal:2',
        'blawalrp' => 'decimal:2',
        'mtsdrrp' => 'decimal:2',
        'mtscrrp' => 'decimal:2',
        'blakhrp' => 'decimal:2',
        'blawalva' => 'decimal:2',
        'mtsdrva' => 'decimal:2',
        'mtscrva' => 'decimal:2',
        'blakhva' => 'decimal:2',
        'blblokrp' => 'decimal:2',
        'blblokva' => 'decimal:2',
        'saldoblok' => 'decimal:2',
        'trnke' => 'decimal:2',
    ];
}
