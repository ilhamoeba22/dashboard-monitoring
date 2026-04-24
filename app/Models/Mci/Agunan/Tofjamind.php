<?php

namespace App\Models\Mci\Agunan;

use App\Models\Mci\MciBaseModel;

/**
 * Model MCI: TOFJAMIND
 * --------------------------------------------------------------------------
 * Domain   : Agunan / Jaminan
 * Tabel    : [dbo].[TOFJAMIND]
 * Kolom    : 56
 * Sumber   : Mapping_MCI_MAR26_01042026.sql / MCI_MAR26_01042026.xlsx
 *
 * CATATAN:
 *  - Model ini READ-ONLY (lihat trait ReadOnlyModel di MciBaseModel).
 *  - Koneksi default `dashboard_data` (SQL Server), otomatis ke DB bulan terbaru.
 *  - Kolom dengan spasi/karakter khusus diakses via:
 *    $model->getAttribute('NAMA KOLOM')  atau  $model->{'NAMA KOLOM'}.
 *
 * @property string|null $noreg  type: varchar(30)
 * @property string|null $nocif  type: varchar(9)
 * @property int|null $urut  type: int(4)
 * @property string|null $jnsjamin  type: varchar(2)
 * @property string|null $nomtaksasi  type: numeric(9)
 * @property string|null $nompasar  type: numeric(9)
 * @property string|null $nomlikuid  type: numeric(9)
 * @property string|null $plafond  type: numeric(9)
 * @property string|null $digunakan  type: numeric(9)
 * @property string|null $akandiguna  type: numeric(9)
 * @property string|null $stsguna  type: varchar(1)
 * @property string|null $jnsdokumen  type: varchar(1)
 * @property string|null $dokumen  type: varchar(30)
 * @property string|null $jttempo  type: varchar(8)
 * @property string|null $an  type: varchar(30)
 * @property string|null $namaci  type: varchar(30)
 * @property string|null $tgltaks1  type: varchar(8)
 * @property string|null $tgltaks2  type: varchar(8)
 * @property string|null $lokasi  type: varchar(40)
 * @property string|null $status  type: varchar(2)
 * @property string|null $catatan  type: varchar(300)
 * @property string|null $kdcab  type: varchar(3)
 * @property string|null $kdloc  type: varchar(2)
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
 * @property string|null $nokontrak  type: varchar(11)
 * @property string|null $ppap  type: numeric(9)
 * @property string|null $sandidati2  type: varchar(5)
 * @property string|null $paripasu  type: numeric(5)
 * @property string|null $sandijamsid  type: varchar(2)
 * @property string|null $nilaiagunbi  type: numeric(9)
 * @property string|null $tglmasuk  type: varchar(8)
 * @property string|null $tglkeluar  type: varchar(8)
 * @property string|null $noaplikasi  type: varchar(30)
 * @property string|null $kdpenilai  type: varchar(10)
 * @property string|null $dd_longitude  type: numeric(9)
 * @property string|null $dd_latitude  type: numeric(9)
 * @property string|null $dms_longitude_1  type: numeric(5)
 * @property string|null $dms_longitude_2  type: numeric(5)
 * @property string|null $dms_longitude_3  type: numeric(5)
 * @property string|null $dms_latitude_1  type: numeric(5)
 * @property string|null $dms_latitude_2  type: numeric(5)
 * @property string|null $dms_latitude_3  type: numeric(5)
 * @property string|null $karat  type: numeric(5)
 * @property string|null $berat  type: numeric(5)
 * @property string|null $stslapor  type: char(1)
 * @property string|null $peringkat  type: varchar(10)
 * @property string|null $kdperingkat  type: varchar(10)
 */
class Tofjamind extends MciBaseModel
{
    /**
     * Nama tabel (case-sensitive di SQL Server).
     */
    protected $table = 'TOFJAMIND';

    /**
     * Daftar LENGKAP kolom sesuai database (56 kolom).
     * Model ini read-only, $fillable hanya untuk dokumentasi & IDE helper.
     *
     * @var array<int,string>
     */
    protected $fillable = [
        'noreg',
        'nocif',
        'urut',
        'jnsjamin',
        'nomtaksasi',
        'nompasar',
        'nomlikuid',
        'plafond',
        'digunakan',
        'akandiguna',
        'stsguna',
        'jnsdokumen',
        'dokumen',
        'jttempo',
        'an',
        'namaci',
        'tgltaks1',
        'tgltaks2',
        'lokasi',
        'status',
        'catatan',
        'kdcab',
        'kdloc',
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
        'nokontrak',
        'ppap',
        'sandidati2',
        'paripasu',
        'sandijamsid',
        'nilaiagunbi',
        'tglmasuk',
        'tglkeluar',
        'noaplikasi',
        'kdpenilai',
        'dd_longitude',
        'dd_latitude',
        'dms_longitude_1',
        'dms_longitude_2',
        'dms_longitude_3',
        'dms_latitude_1',
        'dms_latitude_2',
        'dms_latitude_3',
        'karat',
        'berat',
        'stslapor',
        'peringkat',
        'kdperingkat',
    ];

    /**
     * Casting tipe kolom (non-string saja).
     *
     * @var array<string,string>
     */
    protected $casts = [
        'urut' => 'integer',
        'nomtaksasi' => 'decimal:2',
        'nompasar' => 'decimal:2',
        'nomlikuid' => 'decimal:2',
        'plafond' => 'decimal:2',
        'digunakan' => 'decimal:2',
        'akandiguna' => 'decimal:2',
        'ppap' => 'decimal:2',
        'paripasu' => 'decimal:2',
        'nilaiagunbi' => 'decimal:2',
        'dd_longitude' => 'decimal:2',
        'dd_latitude' => 'decimal:2',
        'dms_longitude_1' => 'decimal:2',
        'dms_longitude_2' => 'decimal:2',
        'dms_longitude_3' => 'decimal:2',
        'dms_latitude_1' => 'decimal:2',
        'dms_latitude_2' => 'decimal:2',
        'dms_latitude_3' => 'decimal:2',
        'karat' => 'decimal:2',
        'berat' => 'decimal:2',
    ];
}
