<?php

namespace App\Models\Mci\Lainnya;

use App\Models\Mci\MciBaseModel;

/**
 * Model MCI: TOFMTRFOUT
 * --------------------------------------------------------------------------
 * Domain   : Lainnya
 * Tabel    : [dbo].[TOFMTRFOUT]
 * Kolom    : 29
 * Sumber   : Mapping_MCI_MAR26_01042026.sql / MCI_MAR26_01042026.xlsx
 *
 * CATATAN:
 *  - Model ini READ-ONLY (lihat trait ReadOnlyModel di MciBaseModel).
 *  - Koneksi default `dashboard_data` (SQL Server), otomatis ke DB bulan terbaru.
 *  - Kolom dengan spasi/karakter khusus diakses via:
 *    $model->getAttribute('NAMA KOLOM')  atau  $model->{'NAMA KOLOM'}.
 *
 * @property int $ID  type: bigint(8)
 * @property string|null $tgltrn  type: varchar(8)
 * @property string|null $batch  type: numeric(5)
 * @property string|null $notrn  type: numeric(5)
 * @property string $kdprog  type: char(2)
 * @property string|null $kdbank  type: varchar(6)
 * @property string|null $nmbank  type: varchar(30)
 * @property string|null $noacc  type: varchar(20)
 * @property string|null $an  type: varchar(50)
 * @property string|null $amount  type: numeric(9)
 * @property string|null $berita  type: varchar(50)
 * @property string|null $kdtrf  type: char(1)
 * @property string|null $nmpengirim  type: varchar(50)
 * @property string|null $alamat  type: varchar(50)
 * @property string|null $nohp  type: varchar(20)
 * @property string|null $bytrf  type: numeric(5)
 * @property string|null $kdbytrf  type: char(1)
 * @property string|null $ststrn  type: char(1)
 * @property string|null $ststrf  type: char(1)
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
 */
class Tofmtrfout extends MciBaseModel
{
    /**
     * Nama tabel (case-sensitive di SQL Server).
     */
    protected $table = 'TOFMTRFOUT';

    /**
     * Daftar LENGKAP kolom sesuai database (29 kolom).
     * Model ini read-only, $fillable hanya untuk dokumentasi & IDE helper.
     *
     * @var array<int,string>
     */
    protected $fillable = [
        'ID',
        'tgltrn',
        'batch',
        'notrn',
        'kdprog',
        'kdbank',
        'nmbank',
        'noacc',
        'an',
        'amount',
        'berita',
        'kdtrf',
        'nmpengirim',
        'alamat',
        'nohp',
        'bytrf',
        'kdbytrf',
        'ststrn',
        'ststrf',
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
        'ID' => 'integer',
        'batch' => 'decimal:2',
        'notrn' => 'decimal:2',
        'amount' => 'decimal:2',
        'bytrf' => 'decimal:2',
    ];
}
