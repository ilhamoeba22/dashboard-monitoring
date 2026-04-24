<?php

namespace App\Models\Mci\Lainnya;

use App\Models\Mci\MciBaseModel;

/**
 * Model MCI: MGL
 * --------------------------------------------------------------------------
 * Domain   : Lainnya
 * Tabel    : [dbo].[MGL]
 * Kolom    : 56
 * Sumber   : Mapping_MCI_MAR26_01042026.sql / MCI_MAR26_01042026.xlsx
 *
 * CATATAN:
 *  - Model ini READ-ONLY (lihat trait ReadOnlyModel di MciBaseModel).
 *  - Koneksi default `dashboard_data` (SQL Server), otomatis ke DB bulan terbaru.
 *  - Kolom dengan spasi/karakter khusus diakses via:
 *    $model->getAttribute('NAMA KOLOM')  atau  $model->{'NAMA KOLOM'}.
 *
 * @property string $golac  type: varchar(1)
 * @property string $jnsgol  type: varchar(1)
 * @property string|null $nobb  type: varchar(11)
 * @property string|null $nosbb  type: varchar(11)
 * @property string|null $cc  type: varchar(2)
 * @property string|null $glb  type: varchar(2)
 * @property string|null $segmen  type: varchar(3)
 * @property string|null $nmsbb  type: varchar(30)
 * @property string|null $posttyp  type: varchar(1)
 * @property string|null $sawalrp  type: numeric(9)
 * @property string|null $mtsdrrp  type: numeric(9)
 * @property string|null $mtscrrp  type: numeric(9)
 * @property string|null $sahirrp  type: numeric(9)
 * @property string|null $sawalva  type: numeric(9)
 * @property string|null $mtsdrva  type: numeric(9)
 * @property string|null $mtscrva  type: numeric(9)
 * @property string|null $sahirva  type: numeric(9)
 * @property string|null $stsac  type: varchar(1)
 * @property string|null $nourut  type: numeric(9)
 * @property string|null $anggar  type: numeric(9)
 * @property string|null $kdcab  type: varchar(3)
 * @property string|null $kdloc  type: varchar(2)
 * @property string|null $kdkas  type: varchar(2)
 * @property string|null $nobbx  type: varchar(7)
 * @property string|null $nosbbx  type: varchar(7)
 * @property string|null $sandibi  type: varchar(5)
 * @property string|null $stsmdl  type: varchar(1)
 * @property string|null $stsatmr  type: varchar(1)
 * @property string|null $bbtatmr  type: numeric(5)
 * @property string|null $stsbyops  type: varchar(1)
 * @property string|null $stspdops  type: varchar(1)
 * @property string|null $stsloan  type: varchar(1)
 * @property string|null $stsdpk  type: varchar(1)
 * @property string|null $stspsh  type: varchar(1)
 * @property string|null $stsreval  type: varchar(1)
 * @property string|null $kdtks  type: varchar(2)
 * @property string|null $stsnisb  type: varchar(1)
 * @property string|null $stsrupa  type: varchar(1)
 * @property string|null $stsaruskas  type: varchar(1)
 * @property string|null $stsrak  type: varchar(1)
 * @property string|null $kdlawan  type: varchar(3)
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
 * @property string|null $saldoeomrp  type: numeric(9)
 * @property string|null $saldoeomva  type: numeric(9)
 * @property string|null $saldoeomold  type: numeric(9)
 * @property string|null $saldokum  type: numeric(9)
 * @property string|null $saldokumeom  type: numeric(9)
 */
class Mgl extends MciBaseModel
{
    /**
     * Nama tabel (case-sensitive di SQL Server).
     */
    protected $table = 'MGL';

    /**
     * Daftar LENGKAP kolom sesuai database (56 kolom).
     * Model ini read-only, $fillable hanya untuk dokumentasi & IDE helper.
     *
     * @var array<int,string>
     */
    protected $fillable = [
        'golac',
        'jnsgol',
        'nobb',
        'nosbb',
        'cc',
        'glb',
        'segmen',
        'nmsbb',
        'posttyp',
        'sawalrp',
        'mtsdrrp',
        'mtscrrp',
        'sahirrp',
        'sawalva',
        'mtsdrva',
        'mtscrva',
        'sahirva',
        'stsac',
        'nourut',
        'anggar',
        'kdcab',
        'kdloc',
        'kdkas',
        'nobbx',
        'nosbbx',
        'sandibi',
        'stsmdl',
        'stsatmr',
        'bbtatmr',
        'stsbyops',
        'stspdops',
        'stsloan',
        'stsdpk',
        'stspsh',
        'stsreval',
        'kdtks',
        'stsnisb',
        'stsrupa',
        'stsaruskas',
        'stsrak',
        'kdlawan',
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
        'saldoeomrp',
        'saldoeomva',
        'saldoeomold',
        'saldokum',
        'saldokumeom',
    ];

    /**
     * Casting tipe kolom (non-string saja).
     *
     * @var array<string,string>
     */
    protected $casts = [
        'sawalrp' => 'decimal:2',
        'mtsdrrp' => 'decimal:2',
        'mtscrrp' => 'decimal:2',
        'sahirrp' => 'decimal:2',
        'sawalva' => 'decimal:2',
        'mtsdrva' => 'decimal:2',
        'mtscrva' => 'decimal:2',
        'sahirva' => 'decimal:2',
        'nourut' => 'decimal:2',
        'anggar' => 'decimal:2',
        'bbtatmr' => 'decimal:2',
        'saldoeomrp' => 'decimal:2',
        'saldoeomva' => 'decimal:2',
        'saldoeomold' => 'decimal:2',
        'saldokum' => 'decimal:2',
        'saldokumeom' => 'decimal:2',
    ];
}
