<?php

declare(strict_types=1);

namespace App\Models\Mci\Lainnya;

use App\Models\Mci\MciBaseModel;

/**
 * Model MCI: TOFMPK
 * --------------------------------------------------------------------------
 * Domain   : Lainnya
 * Tabel    : [dbo].[TOFMPK]
 * Kolom    : 38
 * Sumber   : Mapping_MCI_MAR26_01042026.sql / MCI_MAR26_01042026.xlsx
 *
 * CATATAN:
 *  - Model ini READ-ONLY (lihat trait ReadOnlyModel di MciBaseModel).
 *  - Koneksi default `dashboard_data` (SQL Server), otomatis ke DB bulan terbaru.
 *  - Kolom dengan spasi/karakter khusus diakses via:
 *    $model->getAttribute('NAMA KOLOM')  atau  $model->{'NAMA KOLOM'}.
 *
 * @property string $noapl type: varchar(30)
 * @property string $tglapl type: varchar(8)
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
 * @property string|null $ktp1 type: varchar(20)
 * @property string|null $nmpasangan type: varchar(40)
 * @property string|null $ktp2 type: varchar(20)
 * @property string|null $alamat2 type: varchar(100)
 * @property string|null $nosrtnikah type: varchar(30)
 * @property string|null $pekerjaan1 type: varchar(40)
 * @property string|null $pekerjaan2 type: varchar(40)
 * @property string|null $noreg type: varchar(5)
 * @property string|null $urut type: numeric(5)
 * @property string|null $pokok type: numeric(9)
 * @property string|null $margin type: numeric(9)
 * @property string|null $jw type: numeric(5)
 * @property string|null $noakad type: varchar(8)
 * @property string|null $tglakad type: varchar(8)
 * @property string|null $tglexp type: varchar(8)
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
class Tofmpk extends MciBaseModel
{
    /**
     * Nama tabel (case-sensitive di SQL Server).
     */
    protected $table = 'TOFMPK';

    /**
     * Daftar LENGKAP kolom sesuai database (38 kolom).
     * Model ini read-only, $fillable hanya untuk dokumentasi & IDE helper.
     *
     * @var array<int,string>
     */
    protected $fillable = [
        'noapl',
        'tglapl',
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
        'ktp1',
        'nmpasangan',
        'ktp2',
        'alamat2',
        'nosrtnikah',
        'pekerjaan1',
        'pekerjaan2',
        'noreg',
        'urut',
        'pokok',
        'margin',
        'jw',
        'noakad',
        'tglakad',
        'tglexp',
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
        'urut' => 'decimal:2',
        'pokok' => 'decimal:2',
        'margin' => 'decimal:2',
        'jw' => 'decimal:2',
    ];
}
