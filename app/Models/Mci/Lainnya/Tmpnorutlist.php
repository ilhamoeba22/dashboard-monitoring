<?php

namespace App\Models\Mci\Lainnya;

use App\Models\Mci\MciBaseModel;

/**
 * Model MCI: TMPNORUTLIST
 * --------------------------------------------------------------------------
 * Domain   : Lainnya
 * Tabel    : [dbo].[TMPNORUTLIST]
 * Kolom    : 17
 * Sumber   : Mapping_MCI_MAR26_01042026.sql / MCI_MAR26_01042026.xlsx
 *
 * CATATAN:
 *  - Model ini READ-ONLY (lihat trait ReadOnlyModel di MciBaseModel).
 *  - Koneksi default `dashboard_data` (SQL Server), otomatis ke DB bulan terbaru.
 *  - Kolom dengan spasi/karakter khusus diakses via:
 *    $model->getAttribute('NAMA KOLOM')  atau  $model->{'NAMA KOLOM'}.
 *
 * @property string $noreff  type: nvarchar(100)
 * @property string $noacc  type: varchar(10)
 * @property string $kdedc  type: varchar(10)
 * @property string $kdkolektor  type: varchar(10)
 * @property string $tgltrn  type: varchar(8)
 * @property string|null $dc  type: varchar(1)
 * @property string|null $tgljam  type: nvarchar(100)
 * @property string|null $amount  type: numeric(9)
 * @property string|null $reffva  type: varchar(50)
 * @property string|null $plat  type: varchar(50)
 * @property string|null $plong  type: varchar(50)
 * @property string|null $norut  type: nvarchar(100)
 * @property string|null $ket  type: nvarchar(100)
 * @property string|null $mitratrx  type: nvarchar(100)
 * @property string|null $norektrx  type: nvarchar(100)
 * @property string|null $notrx  type: nvarchar(100)
 * @property string|null $jmlbill  type: nvarchar(100)
 */
class Tmpnorutlist extends MciBaseModel
{
    /**
     * Nama tabel (case-sensitive di SQL Server).
     */
    protected $table = 'TMPNORUTLIST';

    /**
     * Daftar LENGKAP kolom sesuai database (17 kolom).
     * Model ini read-only, $fillable hanya untuk dokumentasi & IDE helper.
     *
     * @var array<int,string>
     */
    protected $fillable = [
        'noreff',
        'noacc',
        'kdedc',
        'kdkolektor',
        'tgltrn',
        'dc',
        'tgljam',
        'amount',
        'reffva',
        'plat',
        'plong',
        'norut',
        'ket',
        'mitratrx',
        'norektrx',
        'notrx',
        'jmlbill',
    ];

    /**
     * Casting tipe kolom (non-string saja).
     *
     * @var array<string,string>
     */
    protected $casts = [
        'amount' => 'decimal:2',
    ];
}
