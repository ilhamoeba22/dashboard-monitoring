<?php

namespace App\Models\Mci\Deposito;

use App\Models\Mci\MciBaseModel;

/**
 * Model MCI: TOFDEPTRF
 * --------------------------------------------------------------------------
 * Domain   : Deposito
 * Tabel    : [dbo].[TOFDEPTRF]
 * Kolom    : 12
 * Sumber   : Mapping_MCI_MAR26_01042026.sql / MCI_MAR26_01042026.xlsx
 *
 * CATATAN:
 *  - Model ini READ-ONLY (lihat trait ReadOnlyModel di MciBaseModel).
 *  - Koneksi default `dashboard_data` (SQL Server), otomatis ke DB bulan terbaru.
 *  - Kolom dengan spasi/karakter khusus diakses via:
 *    $model->getAttribute('NAMA KOLOM')  atau  $model->{'NAMA KOLOM'}.
 *
 * @property string $nodep  type: varchar(11)
 * @property string|null $norek  type: varchar(30)
 * @property string|null $jnsrek  type: varchar(1)
 * @property string|null $an  type: varchar(60)
 * @property string|null $bank  type: varchar(50)
 * @property string|null $kota  type: varchar(50)
 * @property string|null $biaya  type: numeric(9)
 * @property string|null $kdbank  type: varchar(3)
 * @property string|null $nosbbaba  type: varchar(11)
 * @property string|null $noacbank  type: varchar(20)
 * @property string|null $tgltransf  type: varchar(8)
 * @property string|null $bh_netto  type: numeric(9)
 */
class Tofdeptrf extends MciBaseModel
{
    /**
     * Nama tabel (case-sensitive di SQL Server).
     */
    protected $table = 'TOFDEPTRF';

    /**
     * Daftar LENGKAP kolom sesuai database (12 kolom).
     * Model ini read-only, $fillable hanya untuk dokumentasi & IDE helper.
     *
     * @var array<int,string>
     */
    protected $fillable = [
        'nodep',
        'norek',
        'jnsrek',
        'an',
        'bank',
        'kota',
        'biaya',
        'kdbank',
        'nosbbaba',
        'noacbank',
        'tgltransf',
        'bh_netto',
    ];

    /**
     * Casting tipe kolom (non-string saja).
     *
     * @var array<string,string>
     */
    protected $casts = [
        'biaya' => 'decimal:2',
        'bh_netto' => 'decimal:2',
    ];
}
