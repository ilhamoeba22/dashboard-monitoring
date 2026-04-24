<?php

namespace App\Models\Mci\Kurs;

use App\Models\Mci\MciBaseModel;

/**
 * Model MCI: TOFTKURS
 * --------------------------------------------------------------------------
 * Domain   : Kurs
 * Tabel    : [dbo].[TOFTKURS]
 * Kolom    : 25
 * Sumber   : Mapping_MCI_MAR26_01042026.sql / MCI_MAR26_01042026.xlsx
 *
 * CATATAN:
 *  - Model ini READ-ONLY (lihat trait ReadOnlyModel di MciBaseModel).
 *  - Koneksi default `dashboard_data` (SQL Server), otomatis ke DB bulan terbaru.
 *  - Kolom dengan spasi/karakter khusus diakses via:
 *    $model->getAttribute('NAMA KOLOM')  atau  $model->{'NAMA KOLOM'}.
 *
 * @property string|null $cc  type: varchar(2)
 * @property string|null $kdcc  type: varchar(4)
 * @property string|null $ket  type: varchar(30)
 * @property string|null $kursbeli  type: numeric(9)
 * @property string|null $maxkursbeli  type: numeric(9)
 * @property string|null $kursjual  type: numeric(9)
 * @property string|null $minkursjual  type: numeric(9)
 * @property string|null $kursreval  type: numeric(5)
 * @property string|null $sbbkasrp  type: varchar(7)
 * @property string|null $sbbkasva  type: varchar(7)
 * @property string|null $sbbpendcc  type: varchar(7)
 * @property string|null $sbbbycc  type: varchar(7)
 * @property string|null $sbbpendreval  type: varchar(7)
 * @property string|null $sbbbyreval  type: varchar(7)
 * @property string|null $stsreval  type: varchar(1)
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
class Toftkurs extends MciBaseModel
{
    /**
     * Nama tabel (case-sensitive di SQL Server).
     */
    protected $table = 'TOFTKURS';

    /**
     * Daftar LENGKAP kolom sesuai database (25 kolom).
     * Model ini read-only, $fillable hanya untuk dokumentasi & IDE helper.
     *
     * @var array<int,string>
     */
    protected $fillable = [
        'cc',
        'kdcc',
        'ket',
        'kursbeli',
        'maxkursbeli',
        'kursjual',
        'minkursjual',
        'kursreval',
        'sbbkasrp',
        'sbbkasva',
        'sbbpendcc',
        'sbbbycc',
        'sbbpendreval',
        'sbbbyreval',
        'stsreval',
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
        'kursbeli' => 'decimal:2',
        'maxkursbeli' => 'decimal:2',
        'kursjual' => 'decimal:2',
        'minkursjual' => 'decimal:2',
        'kursreval' => 'decimal:2',
    ];
}
