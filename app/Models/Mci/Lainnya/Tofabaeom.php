<?php

declare(strict_types=1);

namespace App\Models\Mci\Lainnya;

use App\Models\Mci\MciBaseModel;

/**
 * Model MCI: TOFABAEOM
 * --------------------------------------------------------------------------
 * Domain   : Lainnya
 * Tabel    : [dbo].[TOFABAEOM]
 * Kolom    : 25
 * Sumber   : Mapping_MCI_MAR26_01042026.sql / MCI_MAR26_01042026.xlsx
 *
 * CATATAN:
 *  - Model ini READ-ONLY (lihat trait ReadOnlyModel di MciBaseModel).
 *  - Koneksi default `dashboard_data` (SQL Server), otomatis ke DB bulan terbaru.
 *  - Kolom dengan spasi/karakter khusus diakses via:
 *    $model->getAttribute('NAMA KOLOM')  atau  $model->{'NAMA KOLOM'}.
 *
 * @property string $periode type: varchar(10)
 * @property string $nosbb type: varchar(10)
 * @property string $norek type: varchar(20)
 * @property string|null $jw type: numeric(5)
 * @property string|null $rate type: numeric(5)
 * @property string|null $coll type: varchar(1)
 * @property string|null $baghas type: numeric(5)
 * @property string|null $jns_penempatan type: varchar(2)
 * @property string|null $ppap type: numeric(9)
 * @property string|null $sahirrp type: numeric(9)
 * @property string|null $mtsdr type: numeric(9)
 * @property string|null $mtscr type: numeric(9)
 * @property string|null $equivrateawal type: numeric(5)
 * @property string|null $equivrateblnlalu type: numeric(5)
 * @property string|null $equivrateblnlap type: numeric(5)
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
 */
class Tofabaeom extends MciBaseModel
{
    /**
     * Nama tabel (case-sensitive di SQL Server).
     */
    protected $table = 'TOFABAEOM';

    /**
     * Daftar LENGKAP kolom sesuai database (25 kolom).
     * Model ini read-only, $fillable hanya untuk dokumentasi & IDE helper.
     *
     * @var array<int,string>
     */
    protected $fillable = [
        'periode',
        'nosbb',
        'norek',
        'jw',
        'rate',
        'coll',
        'baghas',
        'jns_penempatan',
        'ppap',
        'sahirrp',
        'mtsdr',
        'mtscr',
        'equivrateawal',
        'equivrateblnlalu',
        'equivrateblnlap',
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
        'jw' => 'decimal:2',
        'rate' => 'decimal:2',
        'baghas' => 'decimal:2',
        'ppap' => 'decimal:2',
        'sahirrp' => 'decimal:2',
        'mtsdr' => 'decimal:2',
        'mtscr' => 'decimal:2',
        'equivrateawal' => 'decimal:2',
        'equivrateblnlalu' => 'decimal:2',
        'equivrateblnlap' => 'decimal:2',
    ];
}
