<?php

declare(strict_types=1);

namespace App\Models\Mci\Cif;

use App\Models\Mci\MciBaseModel;

/**
 * Model MCI: MCIFCFB
 * --------------------------------------------------------------------------
 * Domain   : CIF / Customer
 * Tabel    : [dbo].[MCIFCFB]
 * Kolom    : 20
 * Sumber   : Mapping_MCI_MAR26_01042026.sql / MCI_MAR26_01042026.xlsx
 *
 * CATATAN:
 *  - Model ini READ-ONLY (lihat trait ReadOnlyModel di MciBaseModel).
 *  - Koneksi default `dashboard_data` (SQL Server), otomatis ke DB bulan terbaru.
 *  - Kolom dengan spasi/karakter khusus diakses via:
 *    $model->getAttribute('NAMA KOLOM')  atau  $model->{'NAMA KOLOM'}.
 *
 * @property string $nocif type: varchar(9)
 * @property string $skor_risk type: varchar(256)
 * @property string $jns_alamat type: varchar(256)
 * @property string $kd_kerja_lain type: varchar(256)
 * @property string $kd_bidang_slik_lain type: varchar(256)
 * @property string $kd_sumb_hasil_lain type: varchar(256)
 * @property string $notes type: varchar(256)
 * @property string $username type: varchar(256)
 * @property string $education_client type: varchar(256)
 * @property string $industry_client type: varchar(256)
 * @property string $business_type type: varchar(256)
 * @property string $business_name type: varchar(256)
 * @property string $business_industry type: varchar(256)
 * @property string $other_business_industry type: varchar(256)
 * @property string $business_address type: varchar(256)
 * @property string $websites_client type: varchar(256)
 * @property string $other_business_type type: varchar(256)
 * @property string $annual_income type: varchar(256)
 * @property string $business_npwp type: varchar(256)
 * @property string $nib type: varchar(256)
 */
class Mcifcfb extends MciBaseModel
{
    /**
     * Nama tabel (case-sensitive di SQL Server).
     */
    protected $table = 'MCIFCFB';

    /**
     * Daftar LENGKAP kolom sesuai database (20 kolom).
     * Model ini read-only, $fillable hanya untuk dokumentasi & IDE helper.
     *
     * @var array<int,string>
     */
    protected $fillable = [
        'nocif',
        'skor_risk',
        'jns_alamat',
        'kd_kerja_lain',
        'kd_bidang_slik_lain',
        'kd_sumb_hasil_lain',
        'notes',
        'username',
        'education_client',
        'industry_client',
        'business_type',
        'business_name',
        'business_industry',
        'other_business_industry',
        'business_address',
        'websites_client',
        'other_business_type',
        'annual_income',
        'business_npwp',
        'nib',
    ];

    /**
     * Casting tipe kolom (non-string saja).
     *
     * @var array<string,string>
     */
    protected $casts = [

    ];
}
