<?php

declare(strict_types=1);

namespace App\Models\Mci\Deposito;

use App\Models\Mci\MciBaseModel;

/**
 * Model MCI: TOFDEPTRFOUT
 * --------------------------------------------------------------------------
 * Domain   : Deposito
 * Tabel    : [dbo].[TOFDEPTRFOUT]
 * Kolom    : 22
 * Sumber   : Mapping_MCI_MAR26_01042026.sql / MCI_MAR26_01042026.xlsx
 *
 * CATATAN:
 *  - Model ini READ-ONLY (lihat trait ReadOnlyModel di MciBaseModel).
 *  - Koneksi default `dashboard_data` (SQL Server), otomatis ke DB bulan terbaru.
 *  - Kolom dengan spasi/karakter khusus diakses via:
 *    $model->getAttribute('NAMA KOLOM')  atau  $model->{'NAMA KOLOM'}.
 *
 * @property string $nodep type: varchar(11)
 * @property string|null $nama type: varchar(30)
 * @property string|null $kdloc type: varchar(3)
 * @property string|null $tglbayar type: varchar(9)
 * @property string|null $tax type: numeric(9)
 * @property string|null $baghas type: numeric(9)
 * @property string|null $baghas2 type: numeric(9)
 * @property string|null $jumlah type: numeric(9)
 * @property string|null $noacbng type: varchar(11)
 * @property string|null $norek type: varchar(30)
 * @property string|null $an type: varchar(30)
 * @property string|null $kdbank type: varchar(3)
 * @property string|null $nmbank type: varchar(50)
 * @property string|null $inpuser type: varchar(20)
 * @property string|null $inptgl type: varchar(20)
 * @property string|null $chguser type: varchar(20)
 * @property string|null $chgtgl type: varchar(20)
 * @property string|null $trxuser type: varchar(20)
 * @property string|null $trxtgl type: varchar(20)
 * @property string|null $stsrec type: char(2)
 * @property string|null $viabank type: varchar(20)
 * @property string|null $notrx type: numeric(9)
 */
class Tofdeptrfout extends MciBaseModel
{
    /**
     * Nama tabel (case-sensitive di SQL Server).
     */
    protected $table = 'TOFDEPTRFOUT';

    /**
     * Daftar LENGKAP kolom sesuai database (22 kolom).
     * Model ini read-only, $fillable hanya untuk dokumentasi & IDE helper.
     *
     * @var array<int,string>
     */
    protected $fillable = [
        'nodep',
        'nama',
        'kdloc',
        'tglbayar',
        'tax',
        'baghas',
        'baghas2',
        'jumlah',
        'noacbng',
        'norek',
        'an',
        'kdbank',
        'nmbank',
        'inpuser',
        'inptgl',
        'chguser',
        'chgtgl',
        'trxuser',
        'trxtgl',
        'stsrec',
        'viabank',
        'notrx',
    ];

    /**
     * Casting tipe kolom (non-string saja).
     *
     * @var array<string,string>
     */
    protected $casts = [
        'tax' => 'decimal:2',
        'baghas' => 'decimal:2',
        'baghas2' => 'decimal:2',
        'jumlah' => 'decimal:2',
        'notrx' => 'decimal:2',
    ];
}
