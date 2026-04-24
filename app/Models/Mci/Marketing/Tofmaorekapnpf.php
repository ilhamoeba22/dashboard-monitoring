<?php

namespace App\Models\Mci\Marketing;

use App\Models\Mci\MciBaseModel;

/**
 * Model MCI: TOFMAOREKAPNPF
 * --------------------------------------------------------------------------
 * Domain   : AO / Marketing
 * Tabel    : [dbo].[TOFMAOREKAPNPF]
 * Kolom    : 13
 * Sumber   : Mapping_MCI_MAR26_01042026.sql / MCI_MAR26_01042026.xlsx
 *
 * CATATAN:
 *  - Model ini READ-ONLY (lihat trait ReadOnlyModel di MciBaseModel).
 *  - Koneksi default `dashboard_data` (SQL Server), otomatis ke DB bulan terbaru.
 *  - Kolom dengan spasi/karakter khusus diakses via:
 *    $model->getAttribute('NAMA KOLOM')  atau  $model->{'NAMA KOLOM'}.
 *
 * @property string|null $kdao  type: varchar(10)
 * @property string|null $periode  type: varchar(6)
 * @property string|null $npf  type: numeric(5)
 * @property string|null $npf_rata  type: numeric(5)
 * @property string|null $coll_1  type: numeric(9)
 * @property string|null $coll_2  type: numeric(9)
 * @property string|null $coll_3  type: numeric(9)
 * @property string|null $coll_4  type: numeric(9)
 * @property string|null $coll_5  type: numeric(9)
 * @property string|null $tgkpok  type: numeric(9)
 * @property string|null $tgkmgn  type: numeric(9)
 * @property string|null $osbaru  type: numeric(9)
 * @property string|null $bonusnpf  type: numeric(9)
 */
class Tofmaorekapnpf extends MciBaseModel
{
    /**
     * Nama tabel (case-sensitive di SQL Server).
     */
    protected $table = 'TOFMAOREKAPNPF';

    /**
     * Daftar LENGKAP kolom sesuai database (13 kolom).
     * Model ini read-only, $fillable hanya untuk dokumentasi & IDE helper.
     *
     * @var array<int,string>
     */
    protected $fillable = [
        'kdao',
        'periode',
        'npf',
        'npf_rata',
        'coll_1',
        'coll_2',
        'coll_3',
        'coll_4',
        'coll_5',
        'tgkpok',
        'tgkmgn',
        'osbaru',
        'bonusnpf',
    ];

    /**
     * Casting tipe kolom (non-string saja).
     *
     * @var array<string,string>
     */
    protected $casts = [
        'npf' => 'decimal:2',
        'npf_rata' => 'decimal:2',
        'coll_1' => 'decimal:2',
        'coll_2' => 'decimal:2',
        'coll_3' => 'decimal:2',
        'coll_4' => 'decimal:2',
        'coll_5' => 'decimal:2',
        'tgkpok' => 'decimal:2',
        'tgkmgn' => 'decimal:2',
        'osbaru' => 'decimal:2',
        'bonusnpf' => 'decimal:2',
    ];
}
