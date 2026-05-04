<?php

declare(strict_types=1);

namespace App\Models\Mci\Deposito;

use App\Models\Mci\MciBaseModel;

/**
 * Model MCI: TMPDEPSPC
 * --------------------------------------------------------------------------
 * Domain   : Deposito
 * Tabel    : [dbo].[TMPDEPSPC]
 * Kolom    : 12
 * Sumber   : Mapping_MCI_MAR26_01042026.sql / MCI_MAR26_01042026.xlsx
 *
 * CATATAN:
 *  - Model ini READ-ONLY (lihat trait ReadOnlyModel di MciBaseModel).
 *  - Koneksi default `dashboard_data` (SQL Server), otomatis ke DB bulan terbaru.
 *  - Kolom dengan spasi/karakter khusus diakses via:
 *    $model->getAttribute('NAMA KOLOM')  atau  $model->{'NAMA KOLOM'}.
 *
 * @property string $tgltrn type: varchar(8)
 * @property string $nodep type: varchar(11)
 * @property string|null $nisbah type: numeric(5)
 * @property string|null $spread type: numeric(5)
 * @property string|null $spread_tambah type: numeric(5)
 * @property string|null $stsrec type: varchar(1)
 * @property string|null $inpuser type: varchar(10)
 * @property string|null $inptgljam type: varchar(14)
 * @property string|null $inpterm type: varchar(10)
 * @property string|null $autuser type: varchar(10)
 * @property string|null $auttgljam type: varchar(14)
 * @property string|null $autterm type: varchar(10)
 */
class Tmpdepspc extends MciBaseModel
{
    /**
     * Nama tabel (case-sensitive di SQL Server).
     */
    protected $table = 'TMPDEPSPC';

    /**
     * Daftar LENGKAP kolom sesuai database (12 kolom).
     * Model ini read-only, $fillable hanya untuk dokumentasi & IDE helper.
     *
     * @var array<int,string>
     */
    protected $fillable = [
        'tgltrn',
        'nodep',
        'nisbah',
        'spread',
        'spread_tambah',
        'stsrec',
        'inpuser',
        'inptgljam',
        'inpterm',
        'autuser',
        'auttgljam',
        'autterm',
    ];

    /**
     * Casting tipe kolom (non-string saja).
     *
     * @var array<string,string>
     */
    protected $casts = [
        'nisbah' => 'decimal:2',
        'spread' => 'decimal:2',
        'spread_tambah' => 'decimal:2',
    ];
}
