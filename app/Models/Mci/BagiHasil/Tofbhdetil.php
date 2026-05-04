<?php

declare(strict_types=1);

namespace App\Models\Mci\BagiHasil;

use App\Models\Mci\MciBaseModel;

/**
 * Model MCI: TOFBHDETIL
 * --------------------------------------------------------------------------
 * Domain   : Bagi Hasil
 * Tabel    : [dbo].[TOFBHDETIL]
 * Kolom    : 13
 * Sumber   : Mapping_MCI_MAR26_01042026.sql / MCI_MAR26_01042026.xlsx
 *
 * CATATAN:
 *  - Model ini READ-ONLY (lihat trait ReadOnlyModel di MciBaseModel).
 *  - Koneksi default `dashboard_data` (SQL Server), otomatis ke DB bulan terbaru.
 *  - Kolom dengan spasi/karakter khusus diakses via:
 *    $model->getAttribute('NAMA KOLOM')  atau  $model->{'NAMA KOLOM'}.
 *
 * @property string|null $kdac type: varchar(1)
 * @property string|null $kdprd type: varchar(2)
 * @property string|null $jw type: numeric(5)
 * @property string|null $kdloc type: varchar(2)
 * @property string|null $noacc type: varchar(11)
 * @property string|null $nama type: varchar(30)
 * @property string|null $saldorata type: numeric(9)
 * @property string|null $nisbah type: numeric(5)
 * @property string|null $spread type: numeric(5)
 * @property string|null $baghas type: numeric(9)
 * @property string|null $tax type: numeric(9)
 * @property string|null $zakat type: numeric(9)
 * @property string|null $rateequiv type: numeric(5)
 */
class Tofbhdetil extends MciBaseModel
{
    /**
     * Nama tabel (case-sensitive di SQL Server).
     */
    protected $table = 'TOFBHDETIL';

    /**
     * Daftar LENGKAP kolom sesuai database (13 kolom).
     * Model ini read-only, $fillable hanya untuk dokumentasi & IDE helper.
     *
     * @var array<int,string>
     */
    protected $fillable = [
        'kdac',
        'kdprd',
        'jw',
        'kdloc',
        'noacc',
        'nama',
        'saldorata',
        'nisbah',
        'spread',
        'baghas',
        'tax',
        'zakat',
        'rateequiv',
    ];

    /**
     * Casting tipe kolom (non-string saja).
     *
     * @var array<string,string>
     */
    protected $casts = [
        'jw' => 'decimal:2',
        'saldorata' => 'decimal:2',
        'nisbah' => 'decimal:2',
        'spread' => 'decimal:2',
        'baghas' => 'decimal:2',
        'tax' => 'decimal:2',
        'zakat' => 'decimal:2',
        'rateequiv' => 'decimal:2',
    ];
}
