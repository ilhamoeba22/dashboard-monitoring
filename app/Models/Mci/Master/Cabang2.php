<?php

declare(strict_types=1);

namespace App\Models\Mci\Master;

use App\Models\Mci\MciBaseModel;

/**
 * Model MCI: CABANG2
 * --------------------------------------------------------------------------
 * Domain   : Cabang / Wilayah
 * Tabel    : [dbo].[CABANG2]
 * Kolom    : 52
 * Sumber   : Mapping_MCI_MAR26_01042026.sql / MCI_MAR26_01042026.xlsx
 *
 * CATATAN:
 *  - Model ini READ-ONLY (lihat trait ReadOnlyModel di MciBaseModel).
 *  - Koneksi default `dashboard_data` (SQL Server), otomatis ke DB bulan terbaru.
 *  - Kolom dengan spasi/karakter khusus diakses via:
 *    $model->getAttribute('NAMA KOLOM')  atau  $model->{'NAMA KOLOM'}.
 *
 * @property string|null $kdloc type: char(2)
 * @property string|null $nmpelapor type: varchar(50)
 * @property string|null $bagpelapor type: varchar(50)
 * @property string|null $nmkap type: varchar(50)
 * @property string|null $nmap type: varchar(50)
 * @property string|null $frekaudit type: numeric(5)
 * @property string|null $tglrups type: varchar(8)
 * @property string|null $dividen type: numeric(9)
 * @property string|null $bonus type: numeric(9)
 * @property string|null $nilaisaham type: numeric(9)
 * @property string|null $edc_own type: numeric(5)
 * @property string|null $edc_bus type: numeric(5)
 * @property string|null $edc_bprs type: numeric(5)
 * @property string|null $atm_own type: numeric(5)
 * @property string|null $atm_bus type: numeric(5)
 * @property string|null $stspva type: char(1)
 * @property string|null $tglpva type: char(8)
 * @property string|null $ktrpva type: numeric(5)
 * @property string|null $stsrec type: char(1)
 * @property string|null $inpuser type: varchar(10)
 * @property string|null $inptgljam type: varchar(14)
 * @property string|null $inpterm type: varchar(10)
 * @property string|null $chguser type: varchar(10)
 * @property string|null $chgtgljam type: varchar(14)
 * @property string|null $chgterm type: varchar(10)
 * @property string|null $autuser type: varchar(10)
 * @property string|null $auttgljam type: varchar(14)
 * @property string|null $autterm type: varchar(10)
 * @property string|null $nmultshare10 type: varchar(50)
 * @property string|null $nmultshare01 type: varchar(50)
 * @property string|null $nmultshare02 type: varchar(50)
 * @property string|null $nmultshare03 type: varchar(50)
 * @property string|null $nmultshare04 type: varchar(50)
 * @property string|null $nmultshare05 type: varchar(50)
 * @property string|null $nmultshare06 type: varchar(50)
 * @property string|null $nmultshare07 type: varchar(50)
 * @property string|null $nmultshare08 type: varchar(50)
 * @property string|null $nmultshare09 type: varchar(50)
 * @property string|null $sts_audit type: char(1)
 * @property string|null $no_sttd_ap type: varchar(50)
 * @property string|null $no_sttd_kap type: varchar(50)
 * @property string|null $sts_efek type: char(1)
 * @property string|null $sts_penyelenggara type: char(1)
 * @property string|null $npwp_penyelenggara type: varchar(25)
 * @property string|null $nama_pjti type: varchar(150)
 * @property string|null $laku_pandai type: char(1)
 * @property int|null $jumlah_laku_pandai type: int(4)
 * @property string|null $nomor_akta_rups type: varchar(50)
 * @property string|null $tgl_rups_pjti type: char(8)
 * @property string|null $notelp type: varchar(15)
 * @property string|null $nofax type: varchar(25)
 * @property string|null $email type: varchar(50)
 */
class Cabang2 extends MciBaseModel
{
    /**
     * Nama tabel (case-sensitive di SQL Server).
     */
    protected $table = 'CABANG2';

    /**
     * Daftar LENGKAP kolom sesuai database (52 kolom).
     * Model ini read-only, $fillable hanya untuk dokumentasi & IDE helper.
     *
     * @var array<int,string>
     */
    protected $fillable = [
        'kdloc',
        'nmpelapor',
        'bagpelapor',
        'nmkap',
        'nmap',
        'frekaudit',
        'tglrups',
        'dividen',
        'bonus',
        'nilaisaham',
        'edc_own',
        'edc_bus',
        'edc_bprs',
        'atm_own',
        'atm_bus',
        'stspva',
        'tglpva',
        'ktrpva',
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
        'nmultshare10',
        'nmultshare01',
        'nmultshare02',
        'nmultshare03',
        'nmultshare04',
        'nmultshare05',
        'nmultshare06',
        'nmultshare07',
        'nmultshare08',
        'nmultshare09',
        'sts_audit',
        'no_sttd_ap',
        'no_sttd_kap',
        'sts_efek',
        'sts_penyelenggara',
        'npwp_penyelenggara',
        'nama_pjti',
        'laku_pandai',
        'jumlah_laku_pandai',
        'nomor_akta_rups',
        'tgl_rups_pjti',
        'notelp',
        'nofax',
        'email',
    ];

    /**
     * Casting tipe kolom (non-string saja).
     *
     * @var array<string,string>
     */
    protected $casts = [
        'frekaudit' => 'decimal:2',
        'dividen' => 'decimal:2',
        'bonus' => 'decimal:2',
        'nilaisaham' => 'decimal:2',
        'edc_own' => 'decimal:2',
        'edc_bus' => 'decimal:2',
        'edc_bprs' => 'decimal:2',
        'atm_own' => 'decimal:2',
        'atm_bus' => 'decimal:2',
        'ktrpva' => 'decimal:2',
        'jumlah_laku_pandai' => 'integer',
    ];
}
