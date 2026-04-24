<?php

namespace App\Models\Mci\Lainnya;

use App\Models\Mci\MciBaseModel;

/**
 * Model MCI: TRANSPC
 * --------------------------------------------------------------------------
 * Domain   : Lainnya
 * Tabel    : [dbo].[TRANSPC]
 * Kolom    : 12
 * Sumber   : Mapping_MCI_MAR26_01042026.sql / MCI_MAR26_01042026.xlsx
 *
 * CATATAN:
 *  - Model ini READ-ONLY (lihat trait ReadOnlyModel di MciBaseModel).
 *  - Koneksi default `dashboard_data` (SQL Server), otomatis ke DB bulan terbaru.
 *  - Kolom dengan spasi/karakter khusus diakses via:
 *    $model->getAttribute('NAMA KOLOM')  atau  $model->{'NAMA KOLOM'}.
 *
 * @property string $tgltrn  type: varchar(8)
 * @property string|null $batch  type: numeric(5)
 * @property string|null $notrn  type: numeric(5)
 * @property string|null $noacc  type: varchar(11)
 * @property string|null $dc  type: varchar(1)
 * @property string|null $nominal  type: numeric(9)
 * @property string|null $nominalva  type: numeric(9)
 * @property string|null $stscetak  type: varchar(1)
 * @property string|null $kdtrnbuku  type: varchar(2)
 * @property string|null $trnke  type: numeric(5)
 * @property string|null $ket  type: varchar(40)
 * @property int $ID  type: bigint(8)
 */
class Transpc extends MciBaseModel
{
    /**
     * Nama tabel (case-sensitive di SQL Server).
     */
    protected $table = 'TRANSPC';

    /**
     * Daftar LENGKAP kolom sesuai database (12 kolom).
     * Model ini read-only, $fillable hanya untuk dokumentasi & IDE helper.
     *
     * @var array<int,string>
     */
    protected $fillable = [
        'tgltrn',
        'batch',
        'notrn',
        'noacc',
        'dc',
        'nominal',
        'nominalva',
        'stscetak',
        'kdtrnbuku',
        'trnke',
        'ket',
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
        'nominalva' => 'decimal:2',
        'trnke' => 'decimal:2',
        'ID' => 'integer',
    ];
}
