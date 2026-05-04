<?php

declare(strict_types=1);

namespace App\Models\Mci\Channel;

use App\Models\Mci\MciBaseModel;

/**
 * Model MCI: TMPEDC3TRN
 * --------------------------------------------------------------------------
 * Domain   : Channel / ATM / Card
 * Tabel    : [dbo].[TMPEDC3TRN]
 * Kolom    : 7
 * Sumber   : Mapping_MCI_MAR26_01042026.sql / MCI_MAR26_01042026.xlsx
 *
 * CATATAN:
 *  - Model ini READ-ONLY (lihat trait ReadOnlyModel di MciBaseModel).
 *  - Koneksi default `dashboard_data` (SQL Server), otomatis ke DB bulan terbaru.
 *  - Kolom dengan spasi/karakter khusus diakses via:
 *    $model->getAttribute('NAMA KOLOM')  atau  $model->{'NAMA KOLOM'}.
 *
 * @property string $tgltrn type: varchar(8)
 * @property string $batch type: numeric(5)
 * @property string $notrn type: numeric(5)
 * @property string $noacc type: varchar(10)
 * @property string|null $nodoc type: varchar(20)
 * @property string $dc type: varchar(1)
 * @property string $amount type: numeric(9)
 */
class Tmpedc3trn extends MciBaseModel
{
    /**
     * Nama tabel (case-sensitive di SQL Server).
     */
    protected $table = 'TMPEDC3TRN';

    /**
     * Daftar LENGKAP kolom sesuai database (7 kolom).
     * Model ini read-only, $fillable hanya untuk dokumentasi & IDE helper.
     *
     * @var array<int,string>
     */
    protected $fillable = [
        'tgltrn',
        'batch',
        'notrn',
        'noacc',
        'nodoc',
        'dc',
        'amount',
    ];

    /**
     * Casting tipe kolom (non-string saja).
     *
     * @var array<string,string>
     */
    protected $casts = [
        'batch' => 'decimal:2',
        'notrn' => 'decimal:2',
        'amount' => 'decimal:2',
    ];
}
