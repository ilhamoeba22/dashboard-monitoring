<?php

namespace App\Models\Mci\Financing;

use App\Models\Mci\MciBaseModel;

/**
 * Model MCI: TOFLMBHST
 * --------------------------------------------------------------------------
 * Domain   : Financing / Loan
 * Tabel    : [dbo].[TOFLMBHST]
 * Kolom    : 33
 * Sumber   : Mapping_MCI_MAR26_01042026.sql / MCI_MAR26_01042026.xlsx
 *
 * CATATAN:
 *  - Model ini READ-ONLY (lihat trait ReadOnlyModel di MciBaseModel).
 *  - Koneksi default `dashboard_data` (SQL Server), otomatis ke DB bulan terbaru.
 *  - Kolom dengan spasi/karakter khusus diakses via:
 *    $model->getAttribute('NAMA KOLOM')  atau  $model->{'NAMA KOLOM'}.
 *
 * @property int $ID  type: bigint(8)
 * @property string $nokontrak  type: varchar(11)
 * @property string $ke  type: numeric(5)
 * @property string $tgltrn  type: char(8)
 * @property string $collo  type: char(1)
 * @property string $colln  type: char(1)
 * @property string|null $jwo  type: numeric(5)
 * @property string|null $jwn  type: numeric(5)
 * @property string|null $kdjwo  type: char(1)
 * @property string|null $kdjwn  type: char(1)
 * @property string|null $tgleffo  type: varchar(8)
 * @property string|null $tglexpo  type: varchar(8)
 * @property string|null $tgleff  type: varchar(8)
 * @property string|null $tglexp  type: varchar(8)
 * @property string|null $noakado  type: varchar(50)
 * @property string|null $noakadn  type: varchar(50)
 * @property string|null $osmdlo  type: numeric(9)
 * @property string|null $osmdln  type: numeric(9)
 * @property string|null $ppo  type: numeric(9)
 * @property string|null $ppn  type: numeric(9)
 * @property string|null $ket  type: varchar(100)
 * @property string $stsrec  type: char(1)
 * @property string $ststrn  type: char(1)
 * @property string|null $inpuser  type: varchar(10)
 * @property string|null $inptgljam  type: varchar(14)
 * @property string|null $inpterm  type: varchar(10)
 * @property string|null $chguser  type: varchar(10)
 * @property string|null $chgtgljam  type: varchar(14)
 * @property string|null $chgterm  type: varchar(10)
 * @property string|null $autuser  type: varchar(10)
 * @property string|null $auttgljam  type: varchar(14)
 * @property string|null $autterm  type: varchar(10)
 * @property string|null $notrn  type: numeric(5)
 */
class Toflmbhst extends MciBaseModel
{
    /**
     * Nama tabel (case-sensitive di SQL Server).
     */
    protected $table = 'TOFLMBHST';

    /**
     * Daftar LENGKAP kolom sesuai database (33 kolom).
     * Model ini read-only, $fillable hanya untuk dokumentasi & IDE helper.
     *
     * @var array<int,string>
     */
    protected $fillable = [
        'ID',
        'nokontrak',
        'ke',
        'tgltrn',
        'collo',
        'colln',
        'jwo',
        'jwn',
        'kdjwo',
        'kdjwn',
        'tgleffo',
        'tglexpo',
        'tgleff',
        'tglexp',
        'noakado',
        'noakadn',
        'osmdlo',
        'osmdln',
        'ppo',
        'ppn',
        'ket',
        'stsrec',
        'ststrn',
        'inpuser',
        'inptgljam',
        'inpterm',
        'chguser',
        'chgtgljam',
        'chgterm',
        'autuser',
        'auttgljam',
        'autterm',
        'notrn',
    ];

    /**
     * Casting tipe kolom (non-string saja).
     *
     * @var array<string,string>
     */
    protected $casts = [
        'ID' => 'integer',
        'ke' => 'decimal:2',
        'jwo' => 'decimal:2',
        'jwn' => 'decimal:2',
        'osmdlo' => 'decimal:2',
        'osmdln' => 'decimal:2',
        'ppo' => 'decimal:2',
        'ppn' => 'decimal:2',
        'notrn' => 'decimal:2',
    ];
}
