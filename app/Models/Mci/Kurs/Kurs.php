<?php

namespace App\Models\Mci\Kurs;

use App\Models\Mci\MciBaseModel;

/**
 * Model MCI: KURS
 * --------------------------------------------------------------------------
 * Domain   : Kurs
 * Tabel    : [dbo].[KURS]
 * Kolom    : 28
 * Sumber   : Mapping_MCI_MAR26_01042026.sql / MCI_MAR26_01042026.xlsx
 *
 * CATATAN:
 *  - Model ini READ-ONLY (lihat trait ReadOnlyModel di MciBaseModel).
 *  - Koneksi default `dashboard_data` (SQL Server), otomatis ke DB bulan terbaru.
 *  - Kolom dengan spasi/karakter khusus diakses via:
 *    $model->getAttribute('NAMA KOLOM')  atau  $model->{'NAMA KOLOM'}.
 *
 * @property string $kdcc  type: varchar(2)
 * @property string|null $pajak  type: numeric(9)
 * @property string|null $revaluasi  type: numeric(9)
 * @property string|null $baserate  type: numeric(9)
 * @property string|null $bnbeli  type: numeric(9)
 * @property string|null $bnjual  type: numeric(9)
 * @property string|null $ttbeli  type: numeric(9)
 * @property string|null $ttjual  type: numeric(9)
 * @property string|null $mtbeli  type: numeric(9)
 * @property string|null $mtjual  type: numeric(9)
 * @property string|null $chbeli  type: numeric(9)
 * @property string|null $chjual  type: numeric(9)
 * @property string|null $btsatas  type: numeric(5)
 * @property string|null $btsbawah  type: numeric(5)
 * @property string|null $tgleff  type: varchar(8)
 * @property string|null $jameff  type: varchar(6)
 * @property string|null $sbbbyrev  type: varchar(7)
 * @property string|null $sbbpdrev  type: varchar(7)
 * @property string|null $stsrec  type: varchar(1)
 * @property string|null $inpuser  type: varchar(10)
 * @property string|null $inptgl  type: varchar(14)
 * @property string|null $inpterm  type: varchar(10)
 * @property string|null $chguser  type: varchar(10)
 * @property string|null $chgtgl  type: varchar(14)
 * @property string|null $chgterm  type: varchar(10)
 * @property string|null $autuser  type: varchar(10)
 * @property string|null $auttgl  type: varchar(14)
 * @property string|null $autterm  type: varchar(10)
 */
class Kurs extends MciBaseModel
{
    /**
     * Nama tabel (case-sensitive di SQL Server).
     */
    protected $table = 'KURS';

    /**
     * Daftar LENGKAP kolom sesuai database (28 kolom).
     * Model ini read-only, $fillable hanya untuk dokumentasi & IDE helper.
     *
     * @var array<int,string>
     */
    protected $fillable = [
        'kdcc',
        'pajak',
        'revaluasi',
        'baserate',
        'bnbeli',
        'bnjual',
        'ttbeli',
        'ttjual',
        'mtbeli',
        'mtjual',
        'chbeli',
        'chjual',
        'btsatas',
        'btsbawah',
        'tgleff',
        'jameff',
        'sbbbyrev',
        'sbbpdrev',
        'stsrec',
        'inpuser',
        'inptgl',
        'inpterm',
        'chguser',
        'chgtgl',
        'chgterm',
        'autuser',
        'auttgl',
        'autterm',
    ];

    /**
     * Casting tipe kolom (non-string saja).
     *
     * @var array<string,string>
     */
    protected $casts = [
        'pajak' => 'decimal:2',
        'revaluasi' => 'decimal:2',
        'baserate' => 'decimal:2',
        'bnbeli' => 'decimal:2',
        'bnjual' => 'decimal:2',
        'ttbeli' => 'decimal:2',
        'ttjual' => 'decimal:2',
        'mtbeli' => 'decimal:2',
        'mtjual' => 'decimal:2',
        'chbeli' => 'decimal:2',
        'chjual' => 'decimal:2',
        'btsatas' => 'decimal:2',
        'btsbawah' => 'decimal:2',
    ];
}
