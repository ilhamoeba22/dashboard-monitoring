<?php

declare(strict_types=1);

namespace App\Models\Mci\Financing;

use App\Models\Mci\MciBaseModel;

/**
 * Model MCI: TOFLMBEOM
 * --------------------------------------------------------------------------
 * Domain   : Financing / Loan
 * Tabel    : [dbo].[TOFLMBEOM]
 * Kolom    : 79
 * Sumber   : Mapping_MCI_MAR26_01042026.sql / MCI_MAR26_01042026.xlsx
 *
 * CATATAN:
 *  - Model ini READ-ONLY (lihat trait ReadOnlyModel di MciBaseModel).
 *  - Koneksi default `dashboard_data` (SQL Server), otomatis ke DB bulan terbaru.
 *  - Kolom dengan spasi/karakter khusus diakses via:
 *    $model->getAttribute('NAMA KOLOM')  atau  $model->{'NAMA KOLOM'}.
 *
 * @property string $periode type: varchar(6)
 * @property string $nokontrak type: varchar(11)
 * @property string $nocif type: varchar(9)
 * @property string|null $nama type: varchar(30)
 * @property string|null $pokpby type: varchar(2)
 * @property string|null $kdloc type: varchar(2)
 * @property string|null $mdlawal type: numeric(9)
 * @property string|null $mgnawal type: numeric(9)
 * @property string|null $mtsmdl type: numeric(9)
 * @property string|null $mtsmgn type: numeric(9)
 * @property string|null $osmdlc type: numeric(9)
 * @property string|null $noakad type: varchar(25)
 * @property string|null $tglakadn type: varchar(8)
 * @property string|null $tglakad type: varchar(8)
 * @property string|null $jw type: numeric(5)
 * @property string|null $kdjw type: varchar(1)
 * @property string|null $tgleff type: varchar(8)
 * @property string|null $tglexp type: varchar(8)
 * @property string|null $tglbook type: varchar(8)
 * @property string|null $angsmdl type: numeric(9)
 * @property string|null $angsmgn type: numeric(9)
 * @property string|null $mdlcust type: numeric(9)
 * @property string|null $mdlbank type: numeric(9)
 * @property string|null $nbhbank type: numeric(5)
 * @property string|null $kdbaghas type: varchar(1)
 * @property string|null $rateeff type: numeric(5)
 * @property string|null $rateflat type: numeric(5)
 * @property string|null $sbb01 type: varchar(11)
 * @property string|null $kdaoh type: varchar(8)
 * @property string|null $kdaop type: varchar(8)
 * @property string|null $kdwil type: varchar(3)
 * @property string|null $nilaijam type: numeric(9)
 * @property string|null $colbaru type: varchar(1)
 * @property string|null $tglmacet type: varchar(8)
 * @property string|null $sebabmacet type: varchar(2)
 * @property string|null $totdnd type: numeric(9)
 * @property string|null $tgllunas type: varchar(8)
 * @property string|null $ststrn type: varchar(10)
 * @property string|null $stsacc type: varchar(1)
 * @property string|null $ppap type: numeric(9)
 * @property string|null $stsrest type: varchar(1)
 * @property string|null $tgkmdl type: numeric(9)
 * @property string|null $tgkmgn type: numeric(9)
 * @property string|null $dpdtgk type: numeric(5)
 * @property string|null $blntgkmdl type: numeric(5)
 * @property string|null $blntgkmgn type: numeric(5)
 * @property string|null $stsrec type: varchar(1)
 * @property string|null $jnspemb type: varchar(5)
 * @property string|null $sid_flag type: varchar(1)
 * @property string|null $kdrate type: varchar(1)
 * @property string|null $sifatplafond type: varchar(1)
 * @property string|null $tglwo type: varchar(8)
 * @property string|null $jnsagun type: varchar(1)
 * @property string|null $htgagun type: numeric(9)
 * @property string|null $jnsagunbi type: varchar(10)
 * @property string|null $frek_restruk type: numeric(5)
 * @property string|null $tgl_restrukawal type: varchar(8)
 * @property string|null $tgl_restrukakhir type: varchar(8)
 * @property string|null $kdcararest type: varchar(2)
 * @property string|null $kdkondisi type: varchar(2)
 * @property string|null $tglkondisi type: varchar(8)
 * @property string|null $slk_takeover type: varchar(6)
 * @property string|null $slk_sumberdana type: varchar(6)
 * @property string|null $stscif_1 type: char(1)
 * @property string|null $stscif_2 type: char(1)
 * @property string|null $stsloan_1 type: char(1)
 * @property string|null $stsloan_2 type: char(1)
 * @property string|null $kdatmro type: varchar(3)
 * @property string|null $kdatmrn type: varchar(3)
 * @property string|null $osmgnc type: numeric(9)
 * @property string|null $kdprd type: char(2)
 * @property string|null $lb_stspiutang type: char(1)
 * @property string|null $lb_sifatpiutang type: char(1)
 * @property string|null $tgkharimdl type: numeric(5)
 * @property string|null $tgkharimgn type: numeric(5)
 * @property string|null $ratio type: numeric(5)
 * @property string|null $haritgkmdl type: numeric(9)
 * @property string|null $haritgkmgn type: numeric(9)
 * @property string $ckpn type: numeric(9)
 */
class Toflmbeom extends MciBaseModel
{
    /**
     * Nama tabel (case-sensitive di SQL Server).
     */
    protected $table = 'TOFLMBEOM';

    /**
     * Daftar LENGKAP kolom sesuai database (79 kolom).
     * Model ini read-only, $fillable hanya untuk dokumentasi & IDE helper.
     *
     * @var array<int,string>
     */
    protected $fillable = [
        'periode',
        'nokontrak',
        'nocif',
        'nama',
        'pokpby',
        'kdloc',
        'mdlawal',
        'mgnawal',
        'mtsmdl',
        'mtsmgn',
        'osmdlc',
        'noakad',
        'tglakadn',
        'tglakad',
        'jw',
        'kdjw',
        'tgleff',
        'tglexp',
        'tglbook',
        'angsmdl',
        'angsmgn',
        'mdlcust',
        'mdlbank',
        'nbhbank',
        'kdbaghas',
        'rateeff',
        'rateflat',
        'sbb01',
        'kdaoh',
        'kdaop',
        'kdwil',
        'nilaijam',
        'colbaru',
        'tglmacet',
        'sebabmacet',
        'totdnd',
        'tgllunas',
        'ststrn',
        'stsacc',
        'ppap',
        'stsrest',
        'tgkmdl',
        'tgkmgn',
        'dpdtgk',
        'blntgkmdl',
        'blntgkmgn',
        'stsrec',
        'jnspemb',
        'sid_flag',
        'kdrate',
        'sifatplafond',
        'tglwo',
        'jnsagun',
        'htgagun',
        'jnsagunbi',
        'frek_restruk',
        'tgl_restrukawal',
        'tgl_restrukakhir',
        'kdcararest',
        'kdkondisi',
        'tglkondisi',
        'slk_takeover',
        'slk_sumberdana',
        'stscif_1',
        'stscif_2',
        'stsloan_1',
        'stsloan_2',
        'kdatmro',
        'kdatmrn',
        'osmgnc',
        'kdprd',
        'lb_stspiutang',
        'lb_sifatpiutang',
        'tgkharimdl',
        'tgkharimgn',
        'ratio',
        'haritgkmdl',
        'haritgkmgn',
        'ckpn',
    ];

    /**
     * Casting tipe kolom (non-string saja).
     *
     * @var array<string,string>
     */
    protected $casts = [
        'mdlawal' => 'decimal:2',
        'mgnawal' => 'decimal:2',
        'mtsmdl' => 'decimal:2',
        'mtsmgn' => 'decimal:2',
        'osmdlc' => 'decimal:2',
        'jw' => 'decimal:2',
        'angsmdl' => 'decimal:2',
        'angsmgn' => 'decimal:2',
        'mdlcust' => 'decimal:2',
        'mdlbank' => 'decimal:2',
        'nbhbank' => 'decimal:2',
        'rateeff' => 'decimal:2',
        'rateflat' => 'decimal:2',
        'nilaijam' => 'decimal:2',
        'totdnd' => 'decimal:2',
        'ppap' => 'decimal:2',
        'tgkmdl' => 'decimal:2',
        'tgkmgn' => 'decimal:2',
        'dpdtgk' => 'decimal:2',
        'blntgkmdl' => 'decimal:2',
        'blntgkmgn' => 'decimal:2',
        'htgagun' => 'decimal:2',
        'frek_restruk' => 'decimal:2',
        'osmgnc' => 'decimal:2',
        'tgkharimdl' => 'decimal:2',
        'tgkharimgn' => 'decimal:2',
        'ratio' => 'decimal:2',
        'haritgkmdl' => 'decimal:2',
        'haritgkmgn' => 'decimal:2',
        'ckpn' => 'decimal:2',
    ];
}
