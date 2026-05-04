<?php

declare(strict_types=1);

namespace App\Models\Mci\ReportingOjk;

use App\Models\Mci\MciBaseModel;

/**
 * Model MCI: BIFORM11
 * --------------------------------------------------------------------------
 * Domain   : Reporting OJK/BI
 * Tabel    : [dbo].[BIFORM11]
 * Kolom    : 53
 * Sumber   : Mapping_MCI_MAR26_01042026.sql / MCI_MAR26_01042026.xlsx
 *
 * CATATAN:
 *  - Model ini READ-ONLY (lihat trait ReadOnlyModel di MciBaseModel).
 *  - Koneksi default `dashboard_data` (SQL Server), otomatis ke DB bulan terbaru.
 *  - Kolom dengan spasi/karakter khusus diakses via:
 *    $model->getAttribute('NAMA KOLOM')  atau  $model->{'NAMA KOLOM'}.
 *
 * @property string $kdloc type: varchar(2)
 * @property string|null $nocif type: varchar(9)
 * @property string|null $nama type: varchar(50)
 * @property string $norek type: varchar(11)
 * @property string|null $noid type: varchar(16)
 * @property string|null $groupdeb type: varchar(10)
 * @property string|null $goldeb type: varchar(10)
 * @property string|null $hubbank type: char(1)
 * @property string|null $golusaha type: char(1)
 * @property string|null $tglmulai type: varchar(8)
 * @property string|null $tglexp type: varchar(8)
 * @property string|null $sumberdana type: char(1)
 * @property string|null $sandidati type: varchar(5)
 * @property string|null $jnspiutang type: char(2)
 * @property string|null $sifatpiutang type: char(1)
 * @property string|null $stspiutang type: char(1)
 * @property string|null $jnsguna type: char(1)
 * @property string|null $sekon type: varchar(10)
 * @property string|null $jnsakad type: char(2)
 * @property string|null $sandiaset type: char(3)
 * @property string|null $tglbeli type: varchar(8)
 * @property string|null $haper type: numeric(9)
 * @property string|null $jw type: numeric(5)
 * @property string|null $mtdsusut type: char(2)
 * @property string|null $caraangs type: char(1)
 * @property string|null $nilaikontrak type: numeric(9)
 * @property string|null $nilaisewa type: numeric(9)
 * @property string|null $equivrateawal type: numeric(5)
 * @property string|null $equivrate type: numeric(5)
 * @property string|null $coll type: char(1)
 * @property string|null $stsbpmd type: char(1)
 * @property string|null $nompokok type: numeric(9)
 * @property string|null $byadm type: numeric(9)
 * @property string|null $akumsusut type: numeric(9)
 * @property string|null $ckpn type: numeric(9)
 * @property string|null $tgkhari type: numeric(5)
 * @property string|null $tgkpokok type: numeric(9)
 * @property string|null $tgkmargin type: numeric(9)
 * @property string|null $pad type: numeric(9)
 * @property string|null $jnsagunan type: char(3)
 * @property string|null $jnsikat type: char(3)
 * @property string|null $noagunan type: varchar(10)
 * @property string|null $karat type: numeric(5)
 * @property string|null $berat type: numeric(5)
 * @property string|null $latitude type: numeric(5)
 * @property string|null $longitude type: numeric(5)
 * @property string|null $golpenerbit type: char(2)
 * @property string|null $tgltaksasi type: varchar(8)
 * @property string|null $nilaitaksasi type: numeric(9)
 * @property string|null $agunanygdihtg type: numeric(9)
 * @property string|null $bagjamin type: varchar(8)
 * @property string|null $ppap type: numeric(9)
 * @property string|null $sumberdana_porsi type: numeric(5)
 */
class Biform11 extends MciBaseModel
{
    /**
     * Nama tabel (case-sensitive di SQL Server).
     */
    protected $table = 'BIFORM11';

    /**
     * Daftar LENGKAP kolom sesuai database (53 kolom).
     * Model ini read-only, $fillable hanya untuk dokumentasi & IDE helper.
     *
     * @var array<int,string>
     */
    protected $fillable = [
        'kdloc',
        'nocif',
        'nama',
        'norek',
        'noid',
        'groupdeb',
        'goldeb',
        'hubbank',
        'golusaha',
        'tglmulai',
        'tglexp',
        'sumberdana',
        'sandidati',
        'jnspiutang',
        'sifatpiutang',
        'stspiutang',
        'jnsguna',
        'sekon',
        'jnsakad',
        'sandiaset',
        'tglbeli',
        'haper',
        'jw',
        'mtdsusut',
        'caraangs',
        'nilaikontrak',
        'nilaisewa',
        'equivrateawal',
        'equivrate',
        'coll',
        'stsbpmd',
        'nompokok',
        'byadm',
        'akumsusut',
        'ckpn',
        'tgkhari',
        'tgkpokok',
        'tgkmargin',
        'pad',
        'jnsagunan',
        'jnsikat',
        'noagunan',
        'karat',
        'berat',
        'latitude',
        'longitude',
        'golpenerbit',
        'tgltaksasi',
        'nilaitaksasi',
        'agunanygdihtg',
        'bagjamin',
        'ppap',
        'sumberdana_porsi',
    ];

    /**
     * Casting tipe kolom (non-string saja).
     *
     * @var array<string,string>
     */
    protected $casts = [
        'haper' => 'decimal:2',
        'jw' => 'decimal:2',
        'nilaikontrak' => 'decimal:2',
        'nilaisewa' => 'decimal:2',
        'equivrateawal' => 'decimal:2',
        'equivrate' => 'decimal:2',
        'nompokok' => 'decimal:2',
        'byadm' => 'decimal:2',
        'akumsusut' => 'decimal:2',
        'ckpn' => 'decimal:2',
        'tgkhari' => 'decimal:2',
        'tgkpokok' => 'decimal:2',
        'tgkmargin' => 'decimal:2',
        'pad' => 'decimal:2',
        'karat' => 'decimal:2',
        'berat' => 'decimal:2',
        'latitude' => 'decimal:2',
        'longitude' => 'decimal:2',
        'nilaitaksasi' => 'decimal:2',
        'agunanygdihtg' => 'decimal:2',
        'ppap' => 'decimal:2',
        'sumberdana_porsi' => 'decimal:2',
    ];
}
