<?php

declare(strict_types=1);

namespace App\Models\Mci\Investasi;

use App\Models\Mci\MciBaseModel;

/**
 * Model MCI: TOFSLIPANGS
 * --------------------------------------------------------------------------
 * Domain   : Investasi / Saham
 * Tabel    : [dbo].[TOFSLIPANGS]
 * Kolom    : 28
 * Sumber   : Mapping_MCI_MAR26_01042026.sql / MCI_MAR26_01042026.xlsx
 *
 * CATATAN:
 *  - Model ini READ-ONLY (lihat trait ReadOnlyModel di MciBaseModel).
 *  - Koneksi default `dashboard_data` (SQL Server), otomatis ke DB bulan terbaru.
 *  - Kolom dengan spasi/karakter khusus diakses via:
 *    $model->getAttribute('NAMA KOLOM')  atau  $model->{'NAMA KOLOM'}.
 *
 * @property string|null $kdmitra type: varchar(10)
 * @property string|null $batch type: numeric(5)
 * @property string|null $bbtt type: varchar(6)
 * @property string|null $nokontrak type: varchar(11)
 * @property string|null $nama type: varchar(30)
 * @property string|null $alamat type: varchar(50)
 * @property string|null $kota type: varchar(30)
 * @property string|null $notelp type: varchar(50)
 * @property string|null $noacc type: varchar(11)
 * @property string|null $periode type: varchar(8)
 * @property string|null $ke type: varchar(10)
 * @property string|null $pokok type: numeric(9)
 * @property string|null $margin type: numeric(9)
 * @property string|null $tglcetak type: varchar(14)
 * @property string|null $tglbayar type: varchar(8)
 * @property string|null $stsbayar type: varchar(1)
 * @property string|null $kdao type: varchar(10)
 * @property string|null $kdkolektor type: varchar(10)
 * @property string|null $noreg type: varchar(10)
 * @property string|null $cetakulang type: numeric(5)
 * @property string|null $usercetak type: varchar(10)
 * @property string|null $usercetakulang type: varchar(10)
 * @property string|null $kdwil type: varchar(3)
 * @property string|null $nip type: varchar(20)
 * @property string|null $os_pokok type: numeric(9)
 * @property string|null $os_margin type: numeric(9)
 * @property string|null $denda type: numeric(9)
 * @property string|null $kdprd type: char(4)
 */
class Tofslipangs extends MciBaseModel
{
    /**
     * Nama tabel (case-sensitive di SQL Server).
     */
    protected $table = 'TOFSLIPANGS';

    /**
     * Daftar LENGKAP kolom sesuai database (28 kolom).
     * Model ini read-only, $fillable hanya untuk dokumentasi & IDE helper.
     *
     * @var array<int,string>
     */
    protected $fillable = [
        'kdmitra',
        'batch',
        'bbtt',
        'nokontrak',
        'nama',
        'alamat',
        'kota',
        'notelp',
        'noacc',
        'periode',
        'ke',
        'pokok',
        'margin',
        'tglcetak',
        'tglbayar',
        'stsbayar',
        'kdao',
        'kdkolektor',
        'noreg',
        'cetakulang',
        'usercetak',
        'usercetakulang',
        'kdwil',
        'nip',
        'os_pokok',
        'os_margin',
        'denda',
        'kdprd',
    ];

    /**
     * Casting tipe kolom (non-string saja).
     *
     * @var array<string,string>
     */
    protected $casts = [
        'batch' => 'decimal:2',
        'pokok' => 'decimal:2',
        'margin' => 'decimal:2',
        'cetakulang' => 'decimal:2',
        'os_pokok' => 'decimal:2',
        'os_margin' => 'decimal:2',
        'denda' => 'decimal:2',
    ];
}
