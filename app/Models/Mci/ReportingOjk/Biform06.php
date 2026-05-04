<?php

declare(strict_types=1);

namespace App\Models\Mci\ReportingOjk;

use App\Models\Mci\MciBaseModel;

/**
 * Model MCI: BIFORM06
 * --------------------------------------------------------------------------
 * Domain   : Reporting OJK/BI
 * Tabel    : [dbo].[BIFORM06]
 * Kolom    : 65
 * Sumber   : Mapping_MCI_MAR26_01042026.sql / MCI_MAR26_01042026.xlsx
 *
 * CATATAN:
 *  - Model ini READ-ONLY (lihat trait ReadOnlyModel di MciBaseModel).
 *  - Koneksi default `dashboard_data` (SQL Server), otomatis ke DB bulan terbaru.
 *  - Kolom dengan spasi/karakter khusus diakses via:
 *    $model->getAttribute('NAMA KOLOM')  atau  $model->{'NAMA KOLOM'}.
 *
 * @property int $ID type: bigint(8)
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
 * @property string|null $noagunan type: varchar(25)
 * @property string|null $karat type: numeric(5)
 * @property string|null $berat type: numeric(5)
 * @property string|null $latitude type: numeric(5)
 * @property string|null $longitude type: numeric(5)
 * @property string|null $golpenerbit type: char(3)
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
 * @property string|null $urut type: numeric(5)
 * @property string|null $basedon type: varchar(15)
 * @property string|null $bobotjam type: decimal(5)
 * @property string|null $jns_ckpn type: char(1)
 * @property string|null $pby_prog type: char(3)
 * @property string|null $sekon_usaha type: char(3)
 * @property string|null $sandi_fintech type: char(8)
 * @property string|null $tgl_macet type: varchar(10)
 * @property string|null $tglakad_awal type: varchar(10)
 * @property string|null $tglakad_akhir type: varchar(10)
 */
class Biform06 extends MciBaseModel
{
    /**
     * Nama tabel (case-sensitive di SQL Server).
     */
    protected $table = 'BIFORM06';

    /**
     * Daftar LENGKAP kolom sesuai database (65 kolom).
     * Model ini read-only, $fillable hanya untuk dokumentasi & IDE helper.
     *
     * @var array<int,string>
     */
    protected $fillable = [
        'ID',
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
        'urut',
        'basedon',
        'bobotjam',
        'jns_ckpn',
        'pby_prog',
        'sekon_usaha',
        'sandi_fintech',
        'tgl_macet',
        'tglakad_awal',
        'tglakad_akhir',
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
        'urut' => 'decimal:2',
        'bobotjam' => 'decimal:2',
    ];
}
