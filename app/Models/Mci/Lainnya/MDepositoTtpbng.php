<?php

declare(strict_types=1);

namespace App\Models\Mci\Lainnya;

use App\Models\Mci\MciBaseModel;

/**
 * Model MCI: m_deposito_ttpbng
 * --------------------------------------------------------------------------
 * Domain   : Lainnya
 * Tabel    : [dbo].[m_deposito_ttpbng]
 * Kolom    : 11
 * Sumber   : Mapping_MCI_MAR26_01042026.sql / MCI_MAR26_01042026.xlsx
 *
 * CATATAN:
 *  - Model ini READ-ONLY (lihat trait ReadOnlyModel di MciBaseModel).
 *  - Koneksi default `dashboard_data` (SQL Server), otomatis ke DB bulan terbaru.
 *  - Kolom dengan spasi/karakter khusus diakses via:
 *    $model->getAttribute('NAMA KOLOM')  atau  $model->{'NAMA KOLOM'}.
 *
 * @property string $noacc type: varchar(11)
 * @property string $tglbayar type: varchar(8)
 * @property string|null $sbbttpbng type: varchar(11)
 * @property string|null $rate type: numeric(5)
 * @property string|null $bunga type: numeric(9)
 * @property string|null $tax type: numeric(9)
 * @property string|null $subsiditax type: numeric(9)
 * @property string|null $tglcair type: varchar(8)
 * @property string|null $usercair type: varchar(10)
 * @property string|null $zakat type: numeric(9)
 * @property string|null $infaq type: numeric(9)
 */
class MDepositoTtpbng extends MciBaseModel
{
    /**
     * Nama tabel (case-sensitive di SQL Server).
     */
    protected $table = 'm_deposito_ttpbng';

    /**
     * Daftar LENGKAP kolom sesuai database (11 kolom).
     * Model ini read-only, $fillable hanya untuk dokumentasi & IDE helper.
     *
     * @var array<int,string>
     */
    protected $fillable = [
        'noacc',
        'tglbayar',
        'sbbttpbng',
        'rate',
        'bunga',
        'tax',
        'subsiditax',
        'tglcair',
        'usercair',
        'zakat',
        'infaq',
    ];

    /**
     * Casting tipe kolom (non-string saja).
     *
     * @var array<string,string>
     */
    protected $casts = [
        'rate' => 'decimal:2',
        'bunga' => 'decimal:2',
        'tax' => 'decimal:2',
        'subsiditax' => 'decimal:2',
        'zakat' => 'decimal:2',
        'infaq' => 'decimal:2',
    ];
}
