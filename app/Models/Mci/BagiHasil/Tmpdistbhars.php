<?php

declare(strict_types=1);

namespace App\Models\Mci\BagiHasil;

use App\Models\Mci\MciBaseModel;

/**
 * Model MCI: TMPDISTBHARS
 * --------------------------------------------------------------------------
 * Domain   : Bagi Hasil
 * Tabel    : [dbo].[TMPDISTBHARS]
 * Kolom    : 8
 * Sumber   : Mapping_MCI_MAR26_01042026.sql / MCI_MAR26_01042026.xlsx
 *
 * CATATAN:
 *  - Model ini READ-ONLY (lihat trait ReadOnlyModel di MciBaseModel).
 *  - Koneksi default `dashboard_data` (SQL Server), otomatis ke DB bulan terbaru.
 *  - Kolom dengan spasi/karakter khusus diakses via:
 *    $model->getAttribute('NAMA KOLOM')  atau  $model->{'NAMA KOLOM'}.
 *
 * @property string|null $periode type: char(4)
 * @property string|null $notab type: varchar(11)
 * @property string|null $nama type: varchar(50)
 * @property string|null $tot_baghas type: numeric(9)
 * @property string|null $tot_zakat type: numeric(9)
 * @property string|null $tot_tax type: numeric(9)
 * @property string|null $saldorata type: numeric(9)
 * @property string|null $notabcr type: varchar(11)
 */
class Tmpdistbhars extends MciBaseModel
{
    /**
     * Nama tabel (case-sensitive di SQL Server).
     */
    protected $table = 'TMPDISTBHARS';

    /**
     * Daftar LENGKAP kolom sesuai database (8 kolom).
     * Model ini read-only, $fillable hanya untuk dokumentasi & IDE helper.
     *
     * @var array<int,string>
     */
    protected $fillable = [
        'periode',
        'notab',
        'nama',
        'tot_baghas',
        'tot_zakat',
        'tot_tax',
        'saldorata',
        'notabcr',
    ];

    /**
     * Casting tipe kolom (non-string saja).
     *
     * @var array<string,string>
     */
    protected $casts = [
        'tot_baghas' => 'decimal:2',
        'tot_zakat' => 'decimal:2',
        'tot_tax' => 'decimal:2',
        'saldorata' => 'decimal:2',
    ];
}
