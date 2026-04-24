<?php

namespace App\Models\Mci\Cif;

use App\Models\Mci\MciBaseModel;

/**
 * Model MCI: MCIFTMP
 * --------------------------------------------------------------------------
 * Domain   : CIF / Customer
 * Tabel    : [dbo].[MCIFTMP]
 * Kolom    : 44
 * Sumber   : Mapping_MCI_MAR26_01042026.sql / MCI_MAR26_01042026.xlsx
 *
 * CATATAN:
 *  - Model ini READ-ONLY (lihat trait ReadOnlyModel di MciBaseModel).
 *  - Koneksi default `dashboard_data` (SQL Server), otomatis ke DB bulan terbaru.
 *  - Kolom dengan spasi/karakter khusus diakses via:
 *    $model->getAttribute('NAMA KOLOM')  atau  $model->{'NAMA KOLOM'}.
 *
 * @property string $nocif  type: varchar(9)
 * @property string|null $nm  type: varchar(30)
 * @property string|null $nmkecil  type: varchar(10)
 * @property string|null $agama  type: varchar(1)
 * @property string|null $golcust  type: varchar(1)
 * @property string|null $golcustbi  type: varchar(3)
 * @property string|null $jnsbh  type: varchar(1)
 * @property string|null $npwp  type: varchar(25)
 * @property string|null $sandibank  type: varchar(6)
 * @property string|null $stscust  type: varchar(1)
 * @property string|null $jnsid  type: varchar(1)
 * @property string|null $noid  type: varchar(30)
 * @property string|null $tglexpid  type: varchar(8)
 * @property string|null $stskawin  type: varchar(1)
 * @property string|null $sex  type: varchar(1)
 * @property string|null $tmplhr  type: varchar(30)
 * @property string|null $tgllhr  type: varchar(8)
 * @property string|null $email  type: varchar(40)
 * @property string|null $alamat  type: varchar(40)
 * @property string|null $kota  type: varchar(30)
 * @property string|null $kdpos  type: varchar(5)
 * @property string|null $telprmh  type: varchar(15)
 * @property string|null $telpktr  type: varchar(15)
 * @property string|null $fax  type: varchar(15)
 * @property string|null $hp  type: varchar(20)
 * @property string|null $nmibu  type: varchar(30)
 * @property string|null $stskait  type: varchar(1)
 * @property string|null $aoref  type: varchar(8)
 * @property string|null $aohand  type: varchar(8)
 * @property string|null $segmen  type: varchar(5)
 * @property string|null $glb  type: varchar(2)
 * @property string|null $tglbuka  type: varchar(8)
 * @property string|null $tgltutup  type: varchar(8)
 * @property string|null $alasan  type: varchar(1)
 * @property string|null $vip  type: varchar(1)
 * @property string|null $stsrestr  type: varchar(1)
 * @property string|null $kdcab  type: varchar(3)
 * @property string|null $kdloc  type: varchar(2)
 * @property string|null $kdkas  type: varchar(2)
 * @property string|null $kddidik  type: varchar(1)
 * @property string|null $kdkerja  type: varchar(1)
 * @property string|null $nocifgrp  type: varchar(8)
 * @property string|null $stsrec  type: varchar(1)
 * @property string|null $inpjam  type: varchar(8)
 */
class Mciftmp extends MciBaseModel
{
    /**
     * Nama tabel (case-sensitive di SQL Server).
     */
    protected $table = 'MCIFTMP';

    /**
     * Daftar LENGKAP kolom sesuai database (44 kolom).
     * Model ini read-only, $fillable hanya untuk dokumentasi & IDE helper.
     *
     * @var array<int,string>
     */
    protected $fillable = [
        'nocif',
        'nm',
        'nmkecil',
        'agama',
        'golcust',
        'golcustbi',
        'jnsbh',
        'npwp',
        'sandibank',
        'stscust',
        'jnsid',
        'noid',
        'tglexpid',
        'stskawin',
        'sex',
        'tmplhr',
        'tgllhr',
        'email',
        'alamat',
        'kota',
        'kdpos',
        'telprmh',
        'telpktr',
        'fax',
        'hp',
        'nmibu',
        'stskait',
        'aoref',
        'aohand',
        'segmen',
        'glb',
        'tglbuka',
        'tgltutup',
        'alasan',
        'vip',
        'stsrestr',
        'kdcab',
        'kdloc',
        'kdkas',
        'kddidik',
        'kdkerja',
        'nocifgrp',
        'stsrec',
        'inpjam',
    ];

    /**
     * Casting tipe kolom (non-string saja).
     *
     * @var array<string,string>
     */
    protected $casts = [

    ];
}
