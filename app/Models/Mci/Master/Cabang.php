<?php

declare(strict_types=1);

namespace App\Models\Mci\Master;

use App\Models\Mci\MciBaseModel;

/**
 * Model MCI: CABANG
 * --------------------------------------------------------------------------
 * Domain   : Cabang / Wilayah
 * Tabel    : [dbo].[CABANG]
 * Kolom    : 86
 * Sumber   : Mapping_MCI_MAR26_01042026.sql / MCI_MAR26_01042026.xlsx
 *
 * CATATAN:
 *  - Model ini READ-ONLY (lihat trait ReadOnlyModel di MciBaseModel).
 *  - Koneksi default `dashboard_data` (SQL Server), otomatis ke DB bulan terbaru.
 *  - Kolom dengan spasi/karakter khusus diakses via:
 *    $model->getAttribute('NAMA KOLOM')  atau  $model->{'NAMA KOLOM'}.
 *
 * @property string $kdcab type: varchar(3)
 * @property string|null $kdloc type: varchar(2)
 * @property string|null $kdkas type: varchar(2)
 * @property string|null $kdktr type: varchar(1)
 * @property string|null $nama type: varchar(40)
 * @property string|null $singkat type: varchar(3)
 * @property string|null $alamat type: varchar(150)
 * @property string|null $kota type: varchar(30)
 * @property string|null $kdpos type: varchar(5)
 * @property string|null $telp type: varchar(15)
 * @property string|null $fax type: varchar(15)
 * @property string|null $pimp type: varchar(30)
 * @property string|null $wapim type: varchar(30)
 * @property string|null $kaops type: varchar(30)
 * @property string|null $hp1 type: varchar(15)
 * @property string|null $hp2 type: varchar(15)
 * @property string|null $hp3 type: varchar(15)
 * @property string|null $kdktrbi1 type: varchar(6)
 * @property string|null $kdktrbi2 type: varchar(3)
 * @property string|null $stsrec type: varchar(1)
 * @property string|null $inpuser type: varchar(10)
 * @property string|null $tglinp type: varchar(14)
 * @property string|null $devinp type: varchar(10)
 * @property string|null $chguser type: varchar(10)
 * @property string|null $tglchg type: varchar(14)
 * @property string|null $devchg type: varchar(10)
 * @property string|null $autuser type: varchar(10)
 * @property string|null $tglaut type: varchar(14)
 * @property string|null $devaut type: varchar(10)
 * @property string|null $sandidati2 type: varchar(5)
 * @property string|null $kdwilbi type: varchar(5)
 * @property string|null $latitude type: numeric(9)
 * @property string|null $longitude type: numeric(9)
 * @property string|null $stsktr type: char(1)
 * @property string|null $sdm_s3_tetap type: numeric(5)
 * @property string|null $sdm_s3_tdk_tetap type: numeric(5)
 * @property string|null $sdm_s2_tetap type: numeric(5)
 * @property string|null $sdm_s2_tdk_tetap type: numeric(5)
 * @property string|null $sdm_s1_tetap type: numeric(5)
 * @property string|null $sdm_s1_tdk_tetap type: numeric(5)
 * @property string|null $sdm_d3_tetap type: numeric(5)
 * @property string|null $sdm_d3_tdk_tetap type: numeric(5)
 * @property string|null $sdm_slta_tetap type: numeric(5)
 * @property string|null $sdm_slta_tdk_tetap type: numeric(5)
 * @property string|null $sdm_sltp_tetap type: numeric(5)
 * @property string|null $sdm_sltp_tdk_tetap type: numeric(5)
 * @property string|null $sdm_marketing_tetap type: numeric(5)
 * @property string|null $sdm_marketing_tdk_tetap type: numeric(5)
 * @property string|null $sdm_ops_tetap type: numeric(5)
 * @property string|null $sdm_ops_tdk_tetap type: numeric(5)
 * @property string|null $sdm_lain_tetap type: numeric(5)
 * @property string|null $sdm_lain_tdk_tetap type: numeric(5)
 * @property string|null $website type: varchar(50)
 * @property string|null $e_mail type: varchar(50)
 * @property string|null $npwp type: varchar(15)
 * @property string|null $kdwilojk type: varchar(5)
 * @property string|null $wapimp2 type: varchar(30)
 * @property string|null $logo type: varbinary(-1)
 * @property string|null $sts_kdkas type: varchar(2)
 * @property string|null $jns_kdkas type: varchar(2)
 * @property string|null $tgltutup type: varchar(10)
 * @property string|null $stsebanking type: varchar(1)
 * @property int|null $jmlhpihakATM type: int(4)
 * @property string|null $nmpihakATM type: varchar(-1)
 * @property string|null $stsUbah type: varchar(2)
 * @property string|null $nomorPersetujuan type: varchar(100)
 * @property string|null $tglPersetujuan type: varchar(10)
 * @property string|null $alamat2 type: varchar(100)
 * @property string|null $tglPelaksana type: varchar(10)
 * @property string|null $kel type: varchar(35)
 * @property string|null $kec type: varchar(35)
 * @property string|null $sdm_lk type: numeric(5)
 * @property string|null $sdm_pr type: numeric(5)
 * @property string|null $u25 type: numeric(5)
 * @property string|null $u35 type: numeric(5)
 * @property string|null $u45 type: numeric(5)
 * @property string|null $u55 type: numeric(5)
 * @property string|null $u66 type: numeric(5)
 * @property string|null $tot_skk type: numeric(5)
 * @property string|null $edc_sendiri type: numeric(5)
 * @property string|null $edc_bu type: numeric(5)
 * @property string|null $edc_bprs type: numeric(5)
 * @property string|null $atm_sendiri type: numeric(5)
 * @property string|null $atm_pihaklain type: numeric(5)
 * @property string|null $atm_bprs type: numeric(5)
 * @property string|null $atm_nama_pengelola type: varchar(50)
 */
class Cabang extends MciBaseModel
{
    /**
     * Nama tabel (case-sensitive di SQL Server).
     */
    protected $table = 'CABANG';

    /**
     * Daftar LENGKAP kolom sesuai database (86 kolom).
     * Model ini read-only, $fillable hanya untuk dokumentasi & IDE helper.
     *
     * @var array<int,string>
     */
    protected $fillable = [
        'kdcab',
        'kdloc',
        'kdkas',
        'kdktr',
        'nama',
        'singkat',
        'alamat',
        'kota',
        'kdpos',
        'telp',
        'fax',
        'pimp',
        'wapim',
        'kaops',
        'hp1',
        'hp2',
        'hp3',
        'kdktrbi1',
        'kdktrbi2',
        'stsrec',
        'inpuser',
        'tglinp',
        'devinp',
        'chguser',
        'tglchg',
        'devchg',
        'autuser',
        'tglaut',
        'devaut',
        'sandidati2',
        'kdwilbi',
        'latitude',
        'longitude',
        'stsktr',
        'sdm_s3_tetap',
        'sdm_s3_tdk_tetap',
        'sdm_s2_tetap',
        'sdm_s2_tdk_tetap',
        'sdm_s1_tetap',
        'sdm_s1_tdk_tetap',
        'sdm_d3_tetap',
        'sdm_d3_tdk_tetap',
        'sdm_slta_tetap',
        'sdm_slta_tdk_tetap',
        'sdm_sltp_tetap',
        'sdm_sltp_tdk_tetap',
        'sdm_marketing_tetap',
        'sdm_marketing_tdk_tetap',
        'sdm_ops_tetap',
        'sdm_ops_tdk_tetap',
        'sdm_lain_tetap',
        'sdm_lain_tdk_tetap',
        'website',
        'e_mail',
        'npwp',
        'kdwilojk',
        'wapimp2',
        'logo',
        'sts_kdkas',
        'jns_kdkas',
        'tgltutup',
        'stsebanking',
        'jmlhpihakATM',
        'nmpihakATM',
        'stsUbah',
        'nomorPersetujuan',
        'tglPersetujuan',
        'alamat2',
        'tglPelaksana',
        'kel',
        'kec',
        'sdm_lk',
        'sdm_pr',
        'u25',
        'u35',
        'u45',
        'u55',
        'u66',
        'tot_skk',
        'edc_sendiri',
        'edc_bu',
        'edc_bprs',
        'atm_sendiri',
        'atm_pihaklain',
        'atm_bprs',
        'atm_nama_pengelola',
    ];

    /**
     * Casting tipe kolom (non-string saja).
     *
     * @var array<string,string>
     */
    protected $casts = [
        'latitude' => 'decimal:2',
        'longitude' => 'decimal:2',
        'sdm_s3_tetap' => 'decimal:2',
        'sdm_s3_tdk_tetap' => 'decimal:2',
        'sdm_s2_tetap' => 'decimal:2',
        'sdm_s2_tdk_tetap' => 'decimal:2',
        'sdm_s1_tetap' => 'decimal:2',
        'sdm_s1_tdk_tetap' => 'decimal:2',
        'sdm_d3_tetap' => 'decimal:2',
        'sdm_d3_tdk_tetap' => 'decimal:2',
        'sdm_slta_tetap' => 'decimal:2',
        'sdm_slta_tdk_tetap' => 'decimal:2',
        'sdm_sltp_tetap' => 'decimal:2',
        'sdm_sltp_tdk_tetap' => 'decimal:2',
        'sdm_marketing_tetap' => 'decimal:2',
        'sdm_marketing_tdk_tetap' => 'decimal:2',
        'sdm_ops_tetap' => 'decimal:2',
        'sdm_ops_tdk_tetap' => 'decimal:2',
        'sdm_lain_tetap' => 'decimal:2',
        'sdm_lain_tdk_tetap' => 'decimal:2',
        'jmlhpihakATM' => 'integer',
        'sdm_lk' => 'decimal:2',
        'sdm_pr' => 'decimal:2',
        'u25' => 'decimal:2',
        'u35' => 'decimal:2',
        'u45' => 'decimal:2',
        'u55' => 'decimal:2',
        'u66' => 'decimal:2',
        'tot_skk' => 'decimal:2',
        'edc_sendiri' => 'decimal:2',
        'edc_bu' => 'decimal:2',
        'edc_bprs' => 'decimal:2',
        'atm_sendiri' => 'decimal:2',
        'atm_pihaklain' => 'decimal:2',
        'atm_bprs' => 'decimal:2',
    ];
}
