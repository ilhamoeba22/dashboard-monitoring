<?php

declare(strict_types=1);

namespace App\Models\Mci\Lainnya;

use App\Models\Mci\MciBaseModel;

/**
 * Model MCI: TOFMPIPE
 * --------------------------------------------------------------------------
 * Domain   : Lainnya
 * Tabel    : [dbo].[TOFMPIPE]
 * Kolom    : 24
 * Sumber   : Mapping_MCI_MAR26_01042026.sql / MCI_MAR26_01042026.xlsx
 *
 * CATATAN:
 *  - Model ini READ-ONLY (lihat trait ReadOnlyModel di MciBaseModel).
 *  - Koneksi default `dashboard_data` (SQL Server), otomatis ke DB bulan terbaru.
 *  - Kolom dengan spasi/karakter khusus diakses via:
 *    $model->getAttribute('NAMA KOLOM')  atau  $model->{'NAMA KOLOM'}.
 *
 * @property string|null $nopipe type: varchar(20)
 * @property string|null $nama type: varchar(40)
 * @property string|null $alamat type: varchar(100)
 * @property string|null $kota type: varchar(30)
 * @property string|null $kdpos type: varchar(5)
 * @property string|null $telprmh type: varchar(20)
 * @property string|null $hp type: varchar(20)
 * @property string|null $nominal type: numeric(9)
 * @property string|null $tujuan type: varchar(150)
 * @property string|null $potensi type: numeric(5)
 * @property string|null $kdao type: varchar(10)
 * @property string|null $stspipe type: varchar(1)
 * @property string|null $ket type: varchar(150)
 * @property string|null $tglrealisasi type: varchar(8)
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
 */
class Tofmpipe extends MciBaseModel
{
    /**
     * Nama tabel (case-sensitive di SQL Server).
     */
    protected $table = 'TOFMPIPE';

    /**
     * Daftar LENGKAP kolom sesuai database (24 kolom).
     * Model ini read-only, $fillable hanya untuk dokumentasi & IDE helper.
     *
     * @var array<int,string>
     */
    protected $fillable = [
        'nopipe',
        'nama',
        'alamat',
        'kota',
        'kdpos',
        'telprmh',
        'hp',
        'nominal',
        'tujuan',
        'potensi',
        'kdao',
        'stspipe',
        'ket',
        'tglrealisasi',
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
    ];

    /**
     * Casting tipe kolom (non-string saja).
     *
     * @var array<string,string>
     */
    protected $casts = [
        'nominal' => 'decimal:2',
        'potensi' => 'decimal:2',
    ];
}
