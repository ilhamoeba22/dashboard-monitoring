<?php

namespace App\Models\Mci\Investasi;

use App\Models\Mci\MciBaseModel;

/**
 * Model MCI: TOFSAHAM
 * --------------------------------------------------------------------------
 * Domain   : Investasi / Saham
 * Tabel    : [dbo].[TOFSAHAM]
 * Kolom    : 35
 * Sumber   : Mapping_MCI_MAR26_01042026.sql / MCI_MAR26_01042026.xlsx
 *
 * CATATAN:
 *  - Model ini READ-ONLY (lihat trait ReadOnlyModel di MciBaseModel).
 *  - Koneksi default `dashboard_data` (SQL Server), otomatis ke DB bulan terbaru.
 *  - Kolom dengan spasi/karakter khusus diakses via:
 *    $model->getAttribute('NAMA KOLOM')  atau  $model->{'NAMA KOLOM'}.
 *
 * @property string $noid  type: varchar(16)
 * @property string $nama  type: varchar(100)
 * @property string|null $alamat  type: varchar(100)
 * @property string|null $kelurahan  type: varchar(30)
 * @property string|null $kecamatan  type: varchar(30)
 * @property string|null $kota  type: varchar(30)
 * @property string|null $kdpos  type: varchar(5)
 * @property string|null $telprmh  type: varchar(15)
 * @property string|null $hp  type: varchar(16)
 * @property string|null $nomsaham  type: numeric(9)
 * @property string|null $prossaham  type: numeric(5)
 * @property string|null $stsrec  type: char(1)
 * @property string|null $inpuser  type: varchar(10)
 * @property string|null $inptgljam  type: varchar(14)
 * @property string|null $inpterm  type: varchar(10)
 * @property string|null $chguser  type: varchar(10)
 * @property string|null $chgtgljam  type: varchar(14)
 * @property string|null $chgterm  type: varchar(10)
 * @property string|null $autuser  type: varchar(10)
 * @property string|null $auttgljam  type: varchar(14)
 * @property string|null $autterm  type: varchar(10)
 * @property string|null $golcust  type: varchar(5)
 * @property string|null $terkait  type: char(1)
 * @property string|null $jnsinstrumen  type: varchar(2)
 * @property string|null $tglojk  type: varchar(8)
 * @property string|null $nosrtojk  type: varchar(50)
 * @property string|null $npwp  type: varchar(20)
 * @property string|null $jnssaham  type: char(2)
 * @property string|null $golpemilik  type: char(1)
 * @property string|null $stspsp  type: char(1)
 * @property string|null $sts_jabat  type: varchar(2)
 * @property string|null $ket_jabat_direksi  type: varchar(-1)
 * @property string|null $alasan_perubahan  type: varchar(-1)
 * @property string|null $sts_perubahan  type: varchar(1)
 * @property string|null $nocif  type: varchar(9)
 */
class Tofsaham extends MciBaseModel
{
    /**
     * Nama tabel (case-sensitive di SQL Server).
     */
    protected $table = 'TOFSAHAM';

    /**
     * Daftar LENGKAP kolom sesuai database (35 kolom).
     * Model ini read-only, $fillable hanya untuk dokumentasi & IDE helper.
     *
     * @var array<int,string>
     */
    protected $fillable = [
        'noid',
        'nama',
        'alamat',
        'kelurahan',
        'kecamatan',
        'kota',
        'kdpos',
        'telprmh',
        'hp',
        'nomsaham',
        'prossaham',
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
        'golcust',
        'terkait',
        'jnsinstrumen',
        'tglojk',
        'nosrtojk',
        'npwp',
        'jnssaham',
        'golpemilik',
        'stspsp',
        'sts_jabat',
        'ket_jabat_direksi',
        'alasan_perubahan',
        'sts_perubahan',
        'nocif',
    ];

    /**
     * Casting tipe kolom (non-string saja).
     *
     * @var array<string,string>
     */
    protected $casts = [
        'nomsaham' => 'decimal:2',
        'prossaham' => 'decimal:2',
    ];
}
