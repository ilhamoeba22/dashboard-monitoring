<?php

declare(strict_types=1);

namespace App\Models\Mci\Master;

use App\Models\Mci\MciBaseModel;

/**
 * Model MCI: CABANGTMP
 * --------------------------------------------------------------------------
 * Domain   : Cabang / Wilayah
 * Tabel    : [dbo].[CABANGTMP]
 * Kolom    : 31
 * Sumber   : Mapping_MCI_MAR26_01042026.sql / MCI_MAR26_01042026.xlsx
 *
 * CATATAN:
 *  - Model ini READ-ONLY (lihat trait ReadOnlyModel di MciBaseModel).
 *  - Koneksi default `dashboard_data` (SQL Server), otomatis ke DB bulan terbaru.
 *  - Kolom dengan spasi/karakter khusus diakses via:
 *    $model->getAttribute('NAMA KOLOM')  atau  $model->{'NAMA KOLOM'}.
 *
 * @property string $kdcab type: varchar(3)
 * @property string|null $kdloc type: varchar(2)
 * @property string|null $kdkas type: varchar(2)
 * @property string|null $kdktr type: varchar(1)
 * @property string|null $nama type: varchar(40)
 * @property string|null $singkat type: varchar(3)
 * @property string|null $alamat type: varchar(40)
 * @property string|null $kota type: varchar(30)
 * @property string|null $kdpos type: varchar(5)
 * @property string|null $telp type: varchar(15)
 * @property string|null $fax type: varchar(15)
 * @property string|null $pimp type: varchar(30)
 * @property string|null $wapim type: varchar(30)
 * @property string|null $kaops type: varchar(30)
 * @property string|null $hp1 type: varchar(15)
 * @property string|null $hp2 type: varchar(15)
 * @property string|null $hp3 type: varchar(15)
 * @property string|null $kdktrbi1 type: varchar(3)
 * @property string|null $kdktrbi2 type: varchar(3)
 * @property string|null $stsrec type: varchar(1)
 * @property string|null $inpuser type: varchar(10)
 * @property string|null $tglinp type: varchar(14)
 * @property string|null $devinp type: varchar(10)
 * @property string|null $chguser type: varchar(10)
 * @property string|null $tglchg type: varchar(14)
 * @property string|null $devchg type: varchar(10)
 * @property string|null $autuser type: varchar(10)
 * @property string|null $tglaut type: varchar(14)
 * @property string|null $devaut type: varchar(10)
 * @property string|null $kel type: varchar(35)
 * @property string|null $kec type: varchar(35)
 */
class Cabangtmp extends MciBaseModel
{
    /**
     * Nama tabel (case-sensitive di SQL Server).
     */
    protected $table = 'CABANGTMP';

    /**
     * Daftar LENGKAP kolom sesuai database (31 kolom).
     * Model ini read-only, $fillable hanya untuk dokumentasi & IDE helper.
     *
     * @var array<int,string>
     */
    protected $fillable = [
        'kdcab',
        'kdloc',
        'kdkas',
        'kdktr',
        'nama',
        'singkat',
        'alamat',
        'kota',
        'kdpos',
        'telp',
        'fax',
        'pimp',
        'wapim',
        'kaops',
        'hp1',
        'hp2',
        'hp3',
        'kdktrbi1',
        'kdktrbi2',
        'stsrec',
        'inpuser',
        'tglinp',
        'devinp',
        'chguser',
        'tglchg',
        'devchg',
        'autuser',
        'tglaut',
        'devaut',
        'kel',
        'kec',
    ];

    /**
     * Casting tipe kolom (non-string saja).
     *
     * @var array<string,string>
     */
    protected $casts = [

    ];
}
