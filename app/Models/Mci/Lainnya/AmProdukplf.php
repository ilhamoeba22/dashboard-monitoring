<?php

declare(strict_types=1);

namespace App\Models\Mci\Lainnya;

use App\Models\Mci\MciBaseModel;

/**
 * Model MCI: AM_PRODUKPLF
 * --------------------------------------------------------------------------
 * Domain   : Lainnya
 * Tabel    : [dbo].[AM_PRODUKPLF]
 * Kolom    : 10
 * Sumber   : Mapping_MCI_MAR26_01042026.sql / MCI_MAR26_01042026.xlsx
 *
 * CATATAN:
 *  - Model ini READ-ONLY (lihat trait ReadOnlyModel di MciBaseModel).
 *  - Koneksi default `dashboard_data` (SQL Server), otomatis ke DB bulan terbaru.
 *  - Kolom dengan spasi/karakter khusus diakses via:
 *    $model->getAttribute('NAMA KOLOM')  atau  $model->{'NAMA KOLOM'}.
 *
 * @property string|null $grpprd type: varchar(20)
 * @property string|null $prdid type: varchar(100)
 * @property string|null $denom type: varchar(25)
 * @property string|null $namaprd type: varchar(50)
 * @property string|null $hargajual type: varchar(25)
 * @property string|null $adminfee type: varchar(25)
 * @property string|null $biller type: varchar(25)
 * @property string|null $billerprd type: varchar(15)
 * @property string|null $aktif type: varchar(1)
 * @property string|null $tglup type: varchar(14)
 */
class AmProdukplf extends MciBaseModel
{
    /**
     * Nama tabel (case-sensitive di SQL Server).
     */
    protected $table = 'AM_PRODUKPLF';

    /**
     * Daftar LENGKAP kolom sesuai database (10 kolom).
     * Model ini read-only, $fillable hanya untuk dokumentasi & IDE helper.
     *
     * @var array<int,string>
     */
    protected $fillable = [
        'grpprd',
        'prdid',
        'denom',
        'namaprd',
        'hargajual',
        'adminfee',
        'biller',
        'billerprd',
        'aktif',
        'tglup',
    ];

    /**
     * Casting tipe kolom (non-string saja).
     *
     * @var array<string,string>
     */
    protected $casts = [

    ];
}
