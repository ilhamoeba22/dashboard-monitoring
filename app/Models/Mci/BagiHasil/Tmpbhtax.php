<?php

declare(strict_types=1);

namespace App\Models\Mci\BagiHasil;

use App\Models\Mci\MciBaseModel;

/**
 * Model MCI: TMPBHTAX
 * --------------------------------------------------------------------------
 * Domain   : Bagi Hasil
 * Tabel    : [dbo].[TMPBHTAX]
 * Kolom    : 11
 * Sumber   : Mapping_MCI_MAR26_01042026.sql / MCI_MAR26_01042026.xlsx
 *
 * CATATAN:
 *  - Model ini READ-ONLY (lihat trait ReadOnlyModel di MciBaseModel).
 *  - Koneksi default `dashboard_data` (SQL Server), otomatis ke DB bulan terbaru.
 *  - Kolom dengan spasi/karakter khusus diakses via:
 *    $model->getAttribute('NAMA KOLOM')  atau  $model->{'NAMA KOLOM'}.
 *
 * @property string|null $peridoe type: varchar(6)
 * @property string|null $kdrek type: varchar(50)
 * @property string|null $noacc type: varchar(11)
 * @property string|null $nama type: varchar(50)
 * @property string|null $kdprd type: varchar(2)
 * @property string|null $kdloc type: varchar(2)
 * @property string|null $sahirawal type: numeric(9)
 * @property string|null $baghas type: numeric(9)
 * @property string|null $tax type: numeric(9)
 * @property string|null $zakat type: numeric(9)
 * @property string|null $sahir type: numeric(9)
 */
class Tmpbhtax extends MciBaseModel
{
    /**
     * Nama tabel (case-sensitive di SQL Server).
     */
    protected $table = 'TMPBHTAX';

    /**
     * Daftar LENGKAP kolom sesuai database (11 kolom).
     * Model ini read-only, $fillable hanya untuk dokumentasi & IDE helper.
     *
     * @var array<int,string>
     */
    protected $fillable = [
        'peridoe',
        'kdrek',
        'noacc',
        'nama',
        'kdprd',
        'kdloc',
        'sahirawal',
        'baghas',
        'tax',
        'zakat',
        'sahir',
    ];

    /**
     * Casting tipe kolom (non-string saja).
     *
     * @var array<string,string>
     */
    protected $casts = [
        'sahirawal' => 'decimal:2',
        'baghas' => 'decimal:2',
        'tax' => 'decimal:2',
        'zakat' => 'decimal:2',
        'sahir' => 'decimal:2',
    ];
}
