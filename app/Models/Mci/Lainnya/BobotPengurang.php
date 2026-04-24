<?php

namespace App\Models\Mci\Lainnya;

use App\Models\Mci\MciBaseModel;

/**
 * Model MCI: bobot_pengurang
 * --------------------------------------------------------------------------
 * Domain   : Lainnya
 * Tabel    : [dbo].[bobot_pengurang]
 * Kolom    : 19
 * Sumber   : Mapping_MCI_MAR26_01042026.sql / MCI_MAR26_01042026.xlsx
 *
 * CATATAN:
 *  - Model ini READ-ONLY (lihat trait ReadOnlyModel di MciBaseModel).
 *  - Koneksi default `dashboard_data` (SQL Server), otomatis ke DB bulan terbaru.
 *  - Kolom dengan spasi/karakter khusus diakses via:
 *    $model->getAttribute('NAMA KOLOM')  atau  $model->{'NAMA KOLOM'}.
 *
 * @property string $jnsjaminan  type: varchar(2)
 * @property string|null $bobot  type: numeric(5)
 * @property string|null $taksasi  type: varchar(1)
 * @property string|null $bln_taksasi_1  type: numeric(5)
 * @property string|null $bln_taksasi_2  type: numeric(5)
 * @property string|null $bln_taksasi_3  type: numeric(5)
 * @property string|null $bobot_1  type: numeric(5)
 * @property string|null $bobot_2  type: numeric(5)
 * @property string|null $bobot_3  type: numeric(5)
 * @property string|null $stsrec  type: varchar(1)
 * @property string|null $inpuser  type: varchar(10)
 * @property string|null $inptgljam  type: varchar(14)
 * @property string|null $inpterm  type: varchar(10)
 * @property string|null $chguser  type: varchar(10)
 * @property string|null $chgtgljam  type: varchar(14)
 * @property string|null $chgterm  type: varchar(10)
 * @property string|null $autuser  type: varchar(10)
 * @property string|null $auttgljam  type: varchar(14)
 * @property string|null $autterm  type: varchar(10)
 */
class BobotPengurang extends MciBaseModel
{
    /**
     * Nama tabel (case-sensitive di SQL Server).
     */
    protected $table = 'bobot_pengurang';

    /**
     * Daftar LENGKAP kolom sesuai database (19 kolom).
     * Model ini read-only, $fillable hanya untuk dokumentasi & IDE helper.
     *
     * @var array<int,string>
     */
    protected $fillable = [
        'jnsjaminan',
        'bobot',
        'taksasi',
        'bln_taksasi_1',
        'bln_taksasi_2',
        'bln_taksasi_3',
        'bobot_1',
        'bobot_2',
        'bobot_3',
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
    ];

    /**
     * Casting tipe kolom (non-string saja).
     *
     * @var array<string,string>
     */
    protected $casts = [
        'bobot' => 'decimal:2',
        'bln_taksasi_1' => 'decimal:2',
        'bln_taksasi_2' => 'decimal:2',
        'bln_taksasi_3' => 'decimal:2',
        'bobot_1' => 'decimal:2',
        'bobot_2' => 'decimal:2',
        'bobot_3' => 'decimal:2',
    ];
}
