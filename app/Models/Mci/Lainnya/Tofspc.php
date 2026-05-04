<?php

declare(strict_types=1);

namespace App\Models\Mci\Lainnya;

use App\Models\Mci\MciBaseModel;

/**
 * Model MCI: TOFSPC
 * --------------------------------------------------------------------------
 * Domain   : Lainnya
 * Tabel    : [dbo].[TOFSPC]
 * Kolom    : 24
 * Sumber   : Mapping_MCI_MAR26_01042026.sql / MCI_MAR26_01042026.xlsx
 *
 * CATATAN:
 *  - Model ini READ-ONLY (lihat trait ReadOnlyModel di MciBaseModel).
 *  - Koneksi default `dashboard_data` (SQL Server), otomatis ke DB bulan terbaru.
 *  - Kolom dengan spasi/karakter khusus diakses via:
 *    $model->getAttribute('NAMA KOLOM')  atau  $model->{'NAMA KOLOM'}.
 *
 * @property string $urutspc type: numeric(5)
 * @property string $noacc type: varchar(11)
 * @property string|null $tgleff type: varchar(8)
 * @property string|null $tglexp type: varchar(8)
 * @property string|null $jnsspc type: varchar(2)
 * @property string|null $nomspc type: numeric(9)
 * @property string|null $rate type: numeric(5)
 * @property string|null $spread type: numeric(5)
 * @property string|null $stsacc type: varchar(1)
 * @property string|null $ket type: varchar(40)
 * @property string|null $kdcab type: varchar(3)
 * @property string|null $kdloc type: varchar(2)
 * @property string|null $jnsacc type: varchar(1)
 * @property string|null $stsrec type: varchar(1)
 * @property string|null $inpuser type: varchar(10)
 * @property string|null $inptgljam type: varchar(14)
 * @property string|null $inpterm type: varchar(10)
 * @property string|null $chguser type: varchar(10)
 * @property string|null $chgtgljam type: varchar(14)
 * @property string|null $chgterm type: varchar(10)
 * @property string|null $autuser type: varchar(10)
 * @property string|null $auttgljam type: varchar(14)
 * @property string|null $autterm type: varchar(10)
 * @property string|null $maxtarik type: numeric(9)
 */
class Tofspc extends MciBaseModel
{
    /**
     * Nama tabel (case-sensitive di SQL Server).
     */
    protected $table = 'TOFSPC';

    /**
     * Daftar LENGKAP kolom sesuai database (24 kolom).
     * Model ini read-only, $fillable hanya untuk dokumentasi & IDE helper.
     *
     * @var array<int,string>
     */
    protected $fillable = [
        'urutspc',
        'noacc',
        'tgleff',
        'tglexp',
        'jnsspc',
        'nomspc',
        'rate',
        'spread',
        'stsacc',
        'ket',
        'kdcab',
        'kdloc',
        'jnsacc',
        'stsrec',
        'inpuser',
        'inptgljam',
        'inpterm',
        'chguser',
        'chgtgljam',
        'chgterm',
        'autuser',
        'auttgljam',
        'autterm',
        'maxtarik',
    ];

    /**
     * Casting tipe kolom (non-string saja).
     *
     * @var array<string,string>
     */
    protected $casts = [
        'urutspc' => 'decimal:2',
        'nomspc' => 'decimal:2',
        'rate' => 'decimal:2',
        'spread' => 'decimal:2',
        'maxtarik' => 'decimal:2',
    ];
}
