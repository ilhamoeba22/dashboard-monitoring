<?php

namespace App\Models\Mci\Lainnya;

use App\Models\Mci\MciBaseModel;

/**
 * Model MCI: TOFMSTOCKCC
 * --------------------------------------------------------------------------
 * Domain   : Lainnya
 * Tabel    : [dbo].[TOFMSTOCKCC]
 * Kolom    : 24
 * Sumber   : Mapping_MCI_MAR26_01042026.sql / MCI_MAR26_01042026.xlsx
 *
 * CATATAN:
 *  - Model ini READ-ONLY (lihat trait ReadOnlyModel di MciBaseModel).
 *  - Koneksi default `dashboard_data` (SQL Server), otomatis ke DB bulan terbaru.
 *  - Kolom dengan spasi/karakter khusus diakses via:
 *    $model->getAttribute('NAMA KOLOM')  atau  $model->{'NAMA KOLOM'}.
 *
 * @property int $ID  type: bigint(8)
 * @property string|null $kdcc  type: varchar(2)
 * @property string|null $kdloc  type: varchar(2)
 * @property string|null $kurs  type: numeric(9)
 * @property string|null $kursrata  type: numeric(9)
 * @property string|null $kursreval  type: numeric(9)
 * @property string|null $stockawal  type: numeric(9)
 * @property string|null $mutasibeli  type: numeric(9)
 * @property string|null $mutasijual  type: numeric(9)
 * @property string|null $stockakhir  type: numeric(9)
 * @property string|null $sahirrp  type: numeric(9)
 * @property string|null $sahirva  type: numeric(9)
 * @property string|null $sahirreval  type: numeric(9)
 * @property string|null $tglproses  type: varchar(8)
 * @property string|null $stsrec  type: char(1)
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
class Tofmstockcc extends MciBaseModel
{
    /**
     * Nama tabel (case-sensitive di SQL Server).
     */
    protected $table = 'TOFMSTOCKCC';

    /**
     * Daftar LENGKAP kolom sesuai database (24 kolom).
     * Model ini read-only, $fillable hanya untuk dokumentasi & IDE helper.
     *
     * @var array<int,string>
     */
    protected $fillable = [
        'ID',
        'kdcc',
        'kdloc',
        'kurs',
        'kursrata',
        'kursreval',
        'stockawal',
        'mutasibeli',
        'mutasijual',
        'stockakhir',
        'sahirrp',
        'sahirva',
        'sahirreval',
        'tglproses',
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
        'ID' => 'integer',
        'kurs' => 'decimal:2',
        'kursrata' => 'decimal:2',
        'kursreval' => 'decimal:2',
        'stockawal' => 'decimal:2',
        'mutasibeli' => 'decimal:2',
        'mutasijual' => 'decimal:2',
        'stockakhir' => 'decimal:2',
        'sahirrp' => 'decimal:2',
        'sahirva' => 'decimal:2',
        'sahirreval' => 'decimal:2',
    ];
}
