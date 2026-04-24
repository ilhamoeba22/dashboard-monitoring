<?php

namespace App\Models\Mci\Lainnya;

use App\Models\Mci\MciBaseModel;

/**
 * Model MCI: H_DEPTRN
 * --------------------------------------------------------------------------
 * Domain   : Lainnya
 * Tabel    : [dbo].[H_DEPTRN]
 * Kolom    : 17
 * Sumber   : Mapping_MCI_MAR26_01042026.sql / MCI_MAR26_01042026.xlsx
 *
 * CATATAN:
 *  - Model ini READ-ONLY (lihat trait ReadOnlyModel di MciBaseModel).
 *  - Koneksi default `dashboard_data` (SQL Server), otomatis ke DB bulan terbaru.
 *  - Kolom dengan spasi/karakter khusus diakses via:
 *    $model->getAttribute('NAMA KOLOM')  atau  $model->{'NAMA KOLOM'}.
 *
 * @property string|null $tgltrn  type: varchar(8)
 * @property string|null $batch  type: numeric(5)
 * @property string|null $notrn  type: numeric(5)
 * @property string|null $nodep  type: varchar(11)
 * @property string|null $nominal  type: numeric(9)
 * @property string|null $baghas  type: numeric(9)
 * @property string|null $bonus  type: numeric(9)
 * @property string|null $tax  type: numeric(9)
 * @property string|null $zakat  type: numeric(9)
 * @property string|null $infaq  type: numeric(9)
 * @property string|null $equivrate  type: numeric(5)
 * @property string|null $nisbah  type: numeric(5)
 * @property string|null $spread  type: numeric(5)
 * @property string|null $komitrate  type: numeric(5)
 * @property string|null $ket  type: varchar(512)
 * @property string|null $nolawan  type: varchar(11)
 * @property int|null $ID  type: bigint(8)
 */
class HDeptrn extends MciBaseModel
{
    /**
     * Nama tabel (case-sensitive di SQL Server).
     */
    protected $table = 'H_DEPTRN';

    /**
     * Daftar LENGKAP kolom sesuai database (17 kolom).
     * Model ini read-only, $fillable hanya untuk dokumentasi & IDE helper.
     *
     * @var array<int,string>
     */
    protected $fillable = [
        'tgltrn',
        'batch',
        'notrn',
        'nodep',
        'nominal',
        'baghas',
        'bonus',
        'tax',
        'zakat',
        'infaq',
        'equivrate',
        'nisbah',
        'spread',
        'komitrate',
        'ket',
        'nolawan',
        'ID',
    ];

    /**
     * Casting tipe kolom (non-string saja).
     *
     * @var array<string,string>
     */
    protected $casts = [
        'batch' => 'decimal:2',
        'notrn' => 'decimal:2',
        'nominal' => 'decimal:2',
        'baghas' => 'decimal:2',
        'bonus' => 'decimal:2',
        'tax' => 'decimal:2',
        'zakat' => 'decimal:2',
        'infaq' => 'decimal:2',
        'equivrate' => 'decimal:2',
        'nisbah' => 'decimal:2',
        'spread' => 'decimal:2',
        'komitrate' => 'decimal:2',
        'ID' => 'integer',
    ];
}
