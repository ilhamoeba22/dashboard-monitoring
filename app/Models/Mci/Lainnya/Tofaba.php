<?php

declare(strict_types=1);

namespace App\Models\Mci\Lainnya;

use App\Models\Mci\MciBaseModel;

/**
 * Model MCI: TOFABA
 * --------------------------------------------------------------------------
 * Domain   : Lainnya
 * Tabel    : [dbo].[TOFABA]
 * Kolom    : 70
 * Sumber   : Mapping_MCI_MAR26_01042026.sql / MCI_MAR26_01042026.xlsx
 *
 * CATATAN:
 *  - Model ini READ-ONLY (lihat trait ReadOnlyModel di MciBaseModel).
 *  - Koneksi default `dashboard_data` (SQL Server), otomatis ke DB bulan terbaru.
 *  - Kolom dengan spasi/karakter khusus diakses via:
 *    $model->getAttribute('NAMA KOLOM')  atau  $model->{'NAMA KOLOM'}.
 *
 * @property string $nosbb type: varchar(10)
 * @property string|null $nmsbb type: varchar(30)
 * @property string|null $norek type: varchar(20)
 * @property string|null $kdrek type: numeric(5)
 * @property string|null $nominal type: numeric(9)
 * @property string|null $jw type: numeric(5)
 * @property string|null $rate type: numeric(5)
 * @property string|null $coll type: varchar(1)
 * @property string|null $nmbank type: varchar(30)
 * @property string|null $sandibank type: varchar(5)
 * @property string|null $sbbinduk type: varchar(10)
 * @property string|null $tglbuka type: varchar(8)
 * @property string|null $tgljtempo type: varchar(8)
 * @property string|null $baghas type: numeric(5)
 * @property string|null $terkait type: varchar(1)
 * @property string|null $jns_penempatan type: varchar(2)
 * @property string|null $saldoeom type: numeric(9)
 * @property string|null $jns_agunan type: varchar(1)
 * @property string|null $agunan type: numeric(9)
 * @property string|null $ppap type: numeric(9)
 * @property string|null $mtd_bg_hsl_sd type: varchar(1)
 * @property string|null $gol_penjamin type: varchar(3)
 * @property string|null $bag_dijamin type: varchar(5)
 * @property string|null $sandi_bank_2 type: varchar(3)
 * @property string|null $nm_pejabat_1 type: varchar(50)
 * @property string|null $nm_pejabat_2 type: varchar(50)
 * @property string|null $nm_jabatan_1 type: varchar(50)
 * @property string|null $nm_jabatan_2 type: varchar(50)
 * @property string|null $stsrec type: varchar(1)
 * @property string|null $inpuser type: varchar(10)
 * @property string|null $inptgljam type: varchar(14)
 * @property string|null $inpterm type: varchar(10)
 * @property string|null $chguser type: varchar(10)
 * @property string|null $chgtgljam type: varchar(14)
 * @property string|null $chgterm type: varchar(10)
 * @property string|null $autuser type: varchar(10)
 * @property string|null $auttgljam type: varchar(14)
 * @property string|null $autterm type: varchar(10)
 * @property string|null $kdloc type: varchar(3)
 * @property string|null $kdbankbi type: varchar(10)
 * @property string|null $sahirrp type: numeric(9)
 * @property string|null $mtsdr type: numeric(9)
 * @property string|null $mtscr type: numeric(9)
 * @property string|null $kdbinva type: char(6)
 * @property string|null $stsva type: char(1)
 * @property string|null $feeva type: numeric(9)
 * @property string|null $sbbbyva type: varchar(11)
 * @property string|null $sbbpendva type: varchar(11)
 * @property string|null $sbbttpva type: varchar(11)
 * @property string|null $userIDva type: varchar(10)
 * @property string|null $pendfeeva type: numeric(5)
 * @property string|null $feemg type: numeric(5)
 * @property string|null $ttpfeemg type: varchar(7)
 * @property string|null $nomeom type: numeric(9)
 * @property string|null $equivrateawal type: numeric(5)
 * @property string|null $equivrateblnlalu type: numeric(5)
 * @property string|null $equivrateblnlap type: numeric(5)
 * @property string|null $bhawal type: numeric(9)
 * @property string|null $bhblnlalu type: numeric(9)
 * @property string|null $bhblnlap type: numeric(9)
 * @property string|null $stsacru type: char(1)
 * @property string|null $bhacrueom type: numeric(9)
 * @property string|null $bhacruday type: numeric(9)
 * @property string|null $bhajustment type: numeric(9)
 * @property string|null $useridtrf type: varchar(30)
 * @property string|null $nocif type: varchar(25)
 * @property string|null $dati2 type: varchar(10)
 * @property string|null $tgleff type: varchar(8)
 * @property string|null $saldoblok type: numeric(9)
 * @property string|null $jns_ckpn type: char(1)
 */
class Tofaba extends MciBaseModel
{
    /**
     * Nama tabel (case-sensitive di SQL Server).
     */
    protected $table = 'TOFABA';

    /**
     * Daftar LENGKAP kolom sesuai database (70 kolom).
     * Model ini read-only, $fillable hanya untuk dokumentasi & IDE helper.
     *
     * @var array<int,string>
     */
    protected $fillable = [
        'nosbb',
        'nmsbb',
        'norek',
        'kdrek',
        'nominal',
        'jw',
        'rate',
        'coll',
        'nmbank',
        'sandibank',
        'sbbinduk',
        'tglbuka',
        'tgljtempo',
        'baghas',
        'terkait',
        'jns_penempatan',
        'saldoeom',
        'jns_agunan',
        'agunan',
        'ppap',
        'mtd_bg_hsl_sd',
        'gol_penjamin',
        'bag_dijamin',
        'sandi_bank_2',
        'nm_pejabat_1',
        'nm_pejabat_2',
        'nm_jabatan_1',
        'nm_jabatan_2',
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
        'kdloc',
        'kdbankbi',
        'sahirrp',
        'mtsdr',
        'mtscr',
        'kdbinva',
        'stsva',
        'feeva',
        'sbbbyva',
        'sbbpendva',
        'sbbttpva',
        'userIDva',
        'pendfeeva',
        'feemg',
        'ttpfeemg',
        'nomeom',
        'equivrateawal',
        'equivrateblnlalu',
        'equivrateblnlap',
        'bhawal',
        'bhblnlalu',
        'bhblnlap',
        'stsacru',
        'bhacrueom',
        'bhacruday',
        'bhajustment',
        'useridtrf',
        'nocif',
        'dati2',
        'tgleff',
        'saldoblok',
        'jns_ckpn',
    ];

    /**
     * Casting tipe kolom (non-string saja).
     *
     * @var array<string,string>
     */
    protected $casts = [
        'kdrek' => 'decimal:2',
        'nominal' => 'decimal:2',
        'jw' => 'decimal:2',
        'rate' => 'decimal:2',
        'baghas' => 'decimal:2',
        'saldoeom' => 'decimal:2',
        'agunan' => 'decimal:2',
        'ppap' => 'decimal:2',
        'sahirrp' => 'decimal:2',
        'mtsdr' => 'decimal:2',
        'mtscr' => 'decimal:2',
        'feeva' => 'decimal:2',
        'pendfeeva' => 'decimal:2',
        'feemg' => 'decimal:2',
        'nomeom' => 'decimal:2',
        'equivrateawal' => 'decimal:2',
        'equivrateblnlalu' => 'decimal:2',
        'equivrateblnlap' => 'decimal:2',
        'bhawal' => 'decimal:2',
        'bhblnlalu' => 'decimal:2',
        'bhblnlap' => 'decimal:2',
        'bhacrueom' => 'decimal:2',
        'bhacruday' => 'decimal:2',
        'bhajustment' => 'decimal:2',
        'saldoblok' => 'decimal:2',
    ];
}
