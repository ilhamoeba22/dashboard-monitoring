<?php

declare(strict_types=1);

namespace App\Models\Mci\ReportingOjk;

use App\Models\Mci\MciBaseModel;

/**
 * Model MCI: OJK0600
 * --------------------------------------------------------------------------
 * Domain   : Reporting OJK/BI
 * Tabel    : [dbo].[OJK0600]
 * Kolom    : 84
 * Sumber   : Mapping_MCI_MAR26_01042026.sql / MCI_MAR26_01042026.xlsx
 *
 * CATATAN:
 *  - Model ini READ-ONLY (lihat trait ReadOnlyModel di MciBaseModel).
 *  - Koneksi default `dashboard_data` (SQL Server), otomatis ke DB bulan terbaru.
 *  - Kolom dengan spasi/karakter khusus diakses via:
 *    $model->getAttribute('NAMA KOLOM')  atau  $model->{'NAMA KOLOM'}.
 *
 * @property int $ID type: bigint(8)
 * @property string|null $periode type: varchar(10)
 * @property string|null $kdloc type: varchar(2)
 * @property string|null $nocif type: varchar(9)
 * @property string|null $nama type: varchar(50)
 * @property string|null $norek type: varchar(11)
 * @property string|null $noid type: varchar(16)
 * @property string|null $groupdeb type: varchar(10)
 * @property string|null $goldeb type: varchar(10)
 * @property string|null $hubbank type: char(1)
 * @property string|null $golusaha type: char(1)
 * @property string|null $tglmulai type: varchar(8)
 * @property string|null $tglexp type: varchar(8)
 * @property string|null $sumberdana type: char(1)
 * @property string|null $sumberdana_porsi type: numeric(5)
 * @property string|null $sandidati type: varchar(5)
 * @property string|null $jnspiutang type: char(2)
 * @property string|null $sifatpiutang type: char(1)
 * @property string|null $stspiutang type: char(1)
 * @property string|null $jnsguna type: char(1)
 * @property string|null $sekon type: varchar(10)
 * @property string|null $nilaikontrak type: numeric(9)
 * @property string|null $caraangs type: char(1)
 * @property string|null $caraangsbh type: char(1)
 * @property string|null $equivrateawal type: numeric(5)
 * @property string|null $equivrate type: numeric(5)
 * @property string|null $coll type: char(1)
 * @property string|null $stsbpmd type: char(1)
 * @property string|null $nompokok type: numeric(9)
 * @property string|null $byadm type: numeric(9)
 * @property string|null $nommydt type: numeric(9)
 * @property string|null $jumlahnom type: numeric(9)
 * @property string|null $tgkhari type: numeric(5)
 * @property string|null $tgkpokok type: numeric(9)
 * @property string|null $tgkharimgn type: numeric(5)
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
 * @property string|null $sifatinvestasi type: char(1)
 * @property string|null $jnsakad type: char(2)
 * @property string|null $mtdbaghas type: char(1)
 * @property string|null $nisbah type: numeric(5)
 * @property string|null $ratio type: numeric(5)
 * @property string|null $kelonggarantarik type: numeric(9)
 * @property string|null $kodeint type: char(5)
 * @property string|null $form type: varchar(10)
 * @property string|null $Nilai01 type: numeric(9)
 * @property string|null $Nilai02 type: numeric(9)
 * @property string|null $Nilai03 type: numeric(9)
 * @property string|null $Nilai04 type: numeric(9)
 * @property string|null $Nilai05 type: numeric(9)
 * @property string|null $Nilai06 type: numeric(9)
 * @property string|null $Nilai07 type: numeric(9)
 * @property string|null $Nilai08 type: numeric(9)
 * @property string|null $Nilai09 type: numeric(9)
 * @property string|null $Nilai10 type: numeric(9)
 * @property string|null $Nilai11 type: numeric(9)
 * @property string|null $Nilai12 type: numeric(9)
 * @property string|null $Nilai13 type: numeric(9)
 * @property string|null $Nilai14 type: numeric(9)
 * @property string|null $Nilai15 type: numeric(9)
 * @property string|null $Nilai16 type: numeric(9)
 * @property string|null $Nilai17 type: numeric(9)
 * @property string|null $Nilai18 type: numeric(9)
 * @property string|null $Nilai19 type: numeric(9)
 * @property string|null $Nilai20 type: numeric(9)
 * @property string|null $Nilai21 type: numeric(9)
 * @property string|null $Nilai22 type: numeric(9)
 * @property string|null $goljamin type: varchar(5)
 * @property string|null $golpiutang type: varchar(3)
 * @property string|null $golcust type: varchar(15)
 * @property string|null $porsi type: numeric(9)
 */
class Ojk0600 extends MciBaseModel
{
    /**
     * Nama tabel (case-sensitive di SQL Server).
     */
    protected $table = 'OJK0600';

    /**
     * Daftar LENGKAP kolom sesuai database (84 kolom).
     * Model ini read-only, $fillable hanya untuk dokumentasi & IDE helper.
     *
     * @var array<int,string>
     */
    protected $fillable = [
        'ID',
        'periode',
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
        'sumberdana_porsi',
        'sandidati',
        'jnspiutang',
        'sifatpiutang',
        'stspiutang',
        'jnsguna',
        'sekon',
        'nilaikontrak',
        'caraangs',
        'caraangsbh',
        'equivrateawal',
        'equivrate',
        'coll',
        'stsbpmd',
        'nompokok',
        'byadm',
        'nommydt',
        'jumlahnom',
        'tgkhari',
        'tgkpokok',
        'tgkharimgn',
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
        'sifatinvestasi',
        'jnsakad',
        'mtdbaghas',
        'nisbah',
        'ratio',
        'kelonggarantarik',
        'kodeint',
        'form',
        'Nilai01',
        'Nilai02',
        'Nilai03',
        'Nilai04',
        'Nilai05',
        'Nilai06',
        'Nilai07',
        'Nilai08',
        'Nilai09',
        'Nilai10',
        'Nilai11',
        'Nilai12',
        'Nilai13',
        'Nilai14',
        'Nilai15',
        'Nilai16',
        'Nilai17',
        'Nilai18',
        'Nilai19',
        'Nilai20',
        'Nilai21',
        'Nilai22',
        'goljamin',
        'golpiutang',
        'golcust',
        'porsi',
    ];

    /**
     * Casting tipe kolom (non-string saja).
     *
     * @var array<string,string>
     */
    protected $casts = [
        'ID' => 'integer',
        'sumberdana_porsi' => 'decimal:2',
        'nilaikontrak' => 'decimal:2',
        'equivrateawal' => 'decimal:2',
        'equivrate' => 'decimal:2',
        'nompokok' => 'decimal:2',
        'byadm' => 'decimal:2',
        'nommydt' => 'decimal:2',
        'jumlahnom' => 'decimal:2',
        'tgkhari' => 'decimal:2',
        'tgkpokok' => 'decimal:2',
        'tgkharimgn' => 'decimal:2',
        'tgkmargin' => 'decimal:2',
        'pad' => 'decimal:2',
        'karat' => 'decimal:2',
        'berat' => 'decimal:2',
        'latitude' => 'decimal:2',
        'longitude' => 'decimal:2',
        'nilaitaksasi' => 'decimal:2',
        'agunanygdihtg' => 'decimal:2',
        'ppap' => 'decimal:2',
        'nisbah' => 'decimal:2',
        'ratio' => 'decimal:2',
        'kelonggarantarik' => 'decimal:2',
        'Nilai01' => 'decimal:2',
        'Nilai02' => 'decimal:2',
        'Nilai03' => 'decimal:2',
        'Nilai04' => 'decimal:2',
        'Nilai05' => 'decimal:2',
        'Nilai06' => 'decimal:2',
        'Nilai07' => 'decimal:2',
        'Nilai08' => 'decimal:2',
        'Nilai09' => 'decimal:2',
        'Nilai10' => 'decimal:2',
        'Nilai11' => 'decimal:2',
        'Nilai12' => 'decimal:2',
        'Nilai13' => 'decimal:2',
        'Nilai14' => 'decimal:2',
        'Nilai15' => 'decimal:2',
        'Nilai16' => 'decimal:2',
        'Nilai17' => 'decimal:2',
        'Nilai18' => 'decimal:2',
        'Nilai19' => 'decimal:2',
        'Nilai20' => 'decimal:2',
        'Nilai21' => 'decimal:2',
        'Nilai22' => 'decimal:2',
        'porsi' => 'decimal:2',
    ];
}
