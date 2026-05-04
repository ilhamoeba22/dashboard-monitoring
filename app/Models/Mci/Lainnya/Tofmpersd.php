<?php

declare(strict_types=1);

namespace App\Models\Mci\Lainnya;

use App\Models\Mci\MciBaseModel;

/**
 * Model MCI: TOFMPERSD
 * --------------------------------------------------------------------------
 * Domain   : Lainnya
 * Tabel    : [dbo].[TOFMPERSD]
 * Kolom    : 22
 * Sumber   : Mapping_MCI_MAR26_01042026.sql / MCI_MAR26_01042026.xlsx
 *
 * CATATAN:
 *  - Model ini READ-ONLY (lihat trait ReadOnlyModel di MciBaseModel).
 *  - Koneksi default `dashboard_data` (SQL Server), otomatis ke DB bulan terbaru.
 *  - Kolom dengan spasi/karakter khusus diakses via:
 *    $model->getAttribute('NAMA KOLOM')  atau  $model->{'NAMA KOLOM'}.
 *
 * @property int $ID type: bigint(8)
 * @property string|null $nopersd type: varchar(10)
 * @property string|null $nmpersd type: varchar(50)
 * @property string|null $grouppersd type: varchar(6)
 * @property string|null $kdloc type: char(2)
 * @property string|null $sawalrp type: numeric(9)
 * @property string|null $mutasidr type: numeric(9)
 * @property string|null $mutasicr type: numeric(9)
 * @property string|null $sahirrpb type: numeric(9)
 * @property string|null $sahirrpc type: numeric(9)
 * @property string|null $tglproc type: varchar(8)
 * @property string|null $qty_a type: numeric(5)
 * @property string|null $qty_in type: numeric(5)
 * @property string|null $qty_out type: numeric(5)
 * @property string|null $qty_b type: numeric(5)
 * @property string|null $qty_c type: numeric(5)
 * @property string|null $sbbpersd type: varchar(7)
 * @property string|null $ststrn type: char(1)
 * @property string|null $stsrec type: char(1)
 * @property string|null $inpuser type: varchar(10)
 * @property string|null $inptgljam type: varchar(14)
 * @property string|null $inpterm type: varchar(10)
 */
class Tofmpersd extends MciBaseModel
{
    /**
     * Nama tabel (case-sensitive di SQL Server).
     */
    protected $table = 'TOFMPERSD';

    /**
     * Daftar LENGKAP kolom sesuai database (22 kolom).
     * Model ini read-only, $fillable hanya untuk dokumentasi & IDE helper.
     *
     * @var array<int,string>
     */
    protected $fillable = [
        'ID',
        'nopersd',
        'nmpersd',
        'grouppersd',
        'kdloc',
        'sawalrp',
        'mutasidr',
        'mutasicr',
        'sahirrpb',
        'sahirrpc',
        'tglproc',
        'qty_a',
        'qty_in',
        'qty_out',
        'qty_b',
        'qty_c',
        'sbbpersd',
        'ststrn',
        'stsrec',
        'inpuser',
        'inptgljam',
        'inpterm',
    ];

    /**
     * Casting tipe kolom (non-string saja).
     *
     * @var array<string,string>
     */
    protected $casts = [
        'ID' => 'integer',
        'sawalrp' => 'decimal:2',
        'mutasidr' => 'decimal:2',
        'mutasicr' => 'decimal:2',
        'sahirrpb' => 'decimal:2',
        'sahirrpc' => 'decimal:2',
        'qty_a' => 'decimal:2',
        'qty_in' => 'decimal:2',
        'qty_out' => 'decimal:2',
        'qty_b' => 'decimal:2',
        'qty_c' => 'decimal:2',
    ];
}
