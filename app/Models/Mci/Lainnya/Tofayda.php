<?php

namespace App\Models\Mci\Lainnya;

use App\Models\Mci\MciBaseModel;

/**
 * Model MCI: TOFAYDA
 * --------------------------------------------------------------------------
 * Domain   : Lainnya
 * Tabel    : [dbo].[TOFAYDA]
 * Kolom    : 53
 * Sumber   : Mapping_MCI_MAR26_01042026.sql / MCI_MAR26_01042026.xlsx
 *
 * CATATAN:
 *  - Model ini READ-ONLY (lihat trait ReadOnlyModel di MciBaseModel).
 *  - Koneksi default `dashboard_data` (SQL Server), otomatis ke DB bulan terbaru.
 *  - Kolom dengan spasi/karakter khusus diakses via:
 *    $model->getAttribute('NAMA KOLOM')  atau  $model->{'NAMA KOLOM'}.
 *
 * @property string $noreg  type: varchar(5)
 * @property string|null $nokontrak  type: varchar(11)
 * @property string|null $nama  type: varchar(30)
 * @property string|null $nom_pokok  type: numeric(9)
 * @property string|null $nom_margin  type: numeric(9)
 * @property string|null $ratio_pokok  type: numeric(5)
 * @property string|null $noreg_agunan  type: varchar(10)
 * @property string|null $urut  type: numeric(5)
 * @property string|null $jnsjamin  type: varchar(2)
 * @property string|null $sandijambi  type: varchar(2)
 * @property string|null $nomtaksasi  type: numeric(9)
 * @property string|null $nompasar  type: numeric(9)
 * @property string|null $nomlikuid  type: numeric(9)
 * @property string|null $jnsdokumen  type: varchar(2)
 * @property string|null $dokumen  type: varchar(30)
 * @property string|null $jttempo  type: varchar(8)
 * @property string|null $an  type: varchar(30)
 * @property string|null $namaci  type: varchar(30)
 * @property string|null $lokasi  type: varchar(40)
 * @property string|null $status  type: varchar(1)
 * @property string|null $catatan  type: varchar(150)
 * @property string|null $kdcab  type: varchar(3)
 * @property string|null $kdloc  type: varchar(2)
 * @property string|null $loksimpan  type: varchar(2)
 * @property string|null $ketsimpan  type: varchar(30)
 * @property string|null $stsjamin  type: varchar(1)
 * @property string|null $ppap  type: numeric(9)
 * @property string|null $jns_aktiva  type: varchar(2)
 * @property string|null $hubungan  type: varchar(1)
 * @property string|null $tglayda  type: varchar(8)
 * @property string|null $tgltrnbeli  type: varchar(8)
 * @property string|null $nom_agunan  type: numeric(9)
 * @property string|null $perk_harga_jual  type: numeric(9)
 * @property string|null $ststrn  type: varchar(1)
 * @property string|null $hargabeli  type: numeric(9)
 * @property string|null $hargajual  type: numeric(9)
 * @property string|null $nmpembeli  type: varchar(30)
 * @property string|null $tgljual  type: varchar(8)
 * @property string|null $kdjual  type: varchar(1)
 * @property string|null $sbbayda  type: varchar(10)
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
 * @property string|null $perk_biaya_jual  type: numeric(9)
 * @property string|null $coll  type: varchar(1)
 * @property string|null $ckpn  type: numeric(9)
 */
class Tofayda extends MciBaseModel
{
    /**
     * Nama tabel (case-sensitive di SQL Server).
     */
    protected $table = 'TOFAYDA';

    /**
     * Daftar LENGKAP kolom sesuai database (53 kolom).
     * Model ini read-only, $fillable hanya untuk dokumentasi & IDE helper.
     *
     * @var array<int,string>
     */
    protected $fillable = [
        'noreg',
        'nokontrak',
        'nama',
        'nom_pokok',
        'nom_margin',
        'ratio_pokok',
        'noreg_agunan',
        'urut',
        'jnsjamin',
        'sandijambi',
        'nomtaksasi',
        'nompasar',
        'nomlikuid',
        'jnsdokumen',
        'dokumen',
        'jttempo',
        'an',
        'namaci',
        'lokasi',
        'status',
        'catatan',
        'kdcab',
        'kdloc',
        'loksimpan',
        'ketsimpan',
        'stsjamin',
        'ppap',
        'jns_aktiva',
        'hubungan',
        'tglayda',
        'tgltrnbeli',
        'nom_agunan',
        'perk_harga_jual',
        'ststrn',
        'hargabeli',
        'hargajual',
        'nmpembeli',
        'tgljual',
        'kdjual',
        'sbbayda',
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
        'perk_biaya_jual',
        'coll',
        'ckpn',
    ];

    /**
     * Casting tipe kolom (non-string saja).
     *
     * @var array<string,string>
     */
    protected $casts = [
        'nom_pokok' => 'decimal:2',
        'nom_margin' => 'decimal:2',
        'ratio_pokok' => 'decimal:2',
        'urut' => 'decimal:2',
        'nomtaksasi' => 'decimal:2',
        'nompasar' => 'decimal:2',
        'nomlikuid' => 'decimal:2',
        'ppap' => 'decimal:2',
        'nom_agunan' => 'decimal:2',
        'perk_harga_jual' => 'decimal:2',
        'hargabeli' => 'decimal:2',
        'hargajual' => 'decimal:2',
        'perk_biaya_jual' => 'decimal:2',
        'ckpn' => 'decimal:2',
    ];
}
