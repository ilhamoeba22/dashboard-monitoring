<?php

namespace App\Models\Mci\Lainnya;

use App\Models\Mci\MciBaseModel;

/**
 * Model MCI: FORMATBUKU
 * --------------------------------------------------------------------------
 * Domain   : Lainnya
 * Tabel    : [dbo].[FORMATBUKU]
 * Kolom    : 27
 * Sumber   : Mapping_MCI_MAR26_01042026.sql / MCI_MAR26_01042026.xlsx
 *
 * CATATAN:
 *  - Model ini READ-ONLY (lihat trait ReadOnlyModel di MciBaseModel).
 *  - Koneksi default `dashboard_data` (SQL Server), otomatis ke DB bulan terbaru.
 *  - Kolom dengan spasi/karakter khusus diakses via:
 *    $model->getAttribute('NAMA KOLOM')  atau  $model->{'NAMA KOLOM'}.
 *
 * @property string $kdbuku  type: varchar(2)
 * @property string|null $ket  type: varchar(30)
 * @property string|null $brspag1  type: numeric(5)
 * @property string|null $brspag2  type: numeric(5)
 * @property string|null $brsanpag  type: numeric(5)
 * @property string|null $satumuka  type: varchar(1)
 * @property string|null $kolom1  type: numeric(5)
 * @property string|null $kolom2  type: numeric(5)
 * @property string|null $kolom3  type: numeric(5)
 * @property string|null $kolom4  type: numeric(5)
 * @property string|null $kolom5  type: numeric(5)
 * @property string|null $kolom6  type: numeric(5)
 * @property string|null $kolom7  type: numeric(5)
 * @property string|null $totalpag  type: numeric(5)
 * @property string|null $totalbrs  type: numeric(5)
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
 * @property string|null $brstrans1  type: numeric(5)
 * @property string|null $brstrans2  type: numeric(5)
 */
class Formatbuku extends MciBaseModel
{
    /**
     * Nama tabel (case-sensitive di SQL Server).
     */
    protected $table = 'FORMATBUKU';

    /**
     * Daftar LENGKAP kolom sesuai database (27 kolom).
     * Model ini read-only, $fillable hanya untuk dokumentasi & IDE helper.
     *
     * @var array<int,string>
     */
    protected $fillable = [
        'kdbuku',
        'ket',
        'brspag1',
        'brspag2',
        'brsanpag',
        'satumuka',
        'kolom1',
        'kolom2',
        'kolom3',
        'kolom4',
        'kolom5',
        'kolom6',
        'kolom7',
        'totalpag',
        'totalbrs',
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
        'brstrans1',
        'brstrans2',
    ];

    /**
     * Casting tipe kolom (non-string saja).
     *
     * @var array<string,string>
     */
    protected $casts = [
        'brspag1' => 'decimal:2',
        'brspag2' => 'decimal:2',
        'brsanpag' => 'decimal:2',
        'kolom1' => 'decimal:2',
        'kolom2' => 'decimal:2',
        'kolom3' => 'decimal:2',
        'kolom4' => 'decimal:2',
        'kolom5' => 'decimal:2',
        'kolom6' => 'decimal:2',
        'kolom7' => 'decimal:2',
        'totalpag' => 'decimal:2',
        'totalbrs' => 'decimal:2',
        'brstrans1' => 'decimal:2',
        'brstrans2' => 'decimal:2',
    ];
}
