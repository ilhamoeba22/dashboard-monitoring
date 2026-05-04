<?php

declare(strict_types=1);

namespace App\Models\Mci\Lainnya;

use App\Models\Mci\MciBaseModel;

/**
 * Model MCI: TOFTGKRT
 * --------------------------------------------------------------------------
 * Domain   : Lainnya
 * Tabel    : [dbo].[TOFTGKRT]
 * Kolom    : 23
 * Sumber   : Mapping_MCI_MAR26_01042026.sql / MCI_MAR26_01042026.xlsx
 *
 * CATATAN:
 *  - Model ini READ-ONLY (lihat trait ReadOnlyModel di MciBaseModel).
 *  - Koneksi default `dashboard_data` (SQL Server), otomatis ke DB bulan terbaru.
 *  - Kolom dengan spasi/karakter khusus diakses via:
 *    $model->getAttribute('NAMA KOLOM')  atau  $model->{'NAMA KOLOM'}.
 *
 * @property string|null $karat type: numeric(5)
 * @property string|null $ket type: varchar(40)
 * @property string|null $bysewa type: numeric(9)
 * @property string|null $maxtaksiran type: numeric(9)
 * @property string|null $disclunas type: numeric(9)
 * @property string|null $bytenggang type: numeric(9)
 * @property string|null $kdpok type: varchar(3)
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
 * @property string|null $deluser type: varchar(10)
 * @property string|null $deltgljam type: varchar(14)
 * @property string|null $delterm type: varchar(10)
 * @property string|null $kdbysewa type: varchar(1)
 * @property string|null $nomlipat type: numeric(9)
 * @property string|null $nilaipasar type: numeric(9)
 */
class Toftgkrt extends MciBaseModel
{
    /**
     * Nama tabel (case-sensitive di SQL Server).
     */
    protected $table = 'TOFTGKRT';

    /**
     * Daftar LENGKAP kolom sesuai database (23 kolom).
     * Model ini read-only, $fillable hanya untuk dokumentasi & IDE helper.
     *
     * @var array<int,string>
     */
    protected $fillable = [
        'karat',
        'ket',
        'bysewa',
        'maxtaksiran',
        'disclunas',
        'bytenggang',
        'kdpok',
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
        'deluser',
        'deltgljam',
        'delterm',
        'kdbysewa',
        'nomlipat',
        'nilaipasar',
    ];

    /**
     * Casting tipe kolom (non-string saja).
     *
     * @var array<string,string>
     */
    protected $casts = [
        'karat' => 'decimal:2',
        'bysewa' => 'decimal:2',
        'maxtaksiran' => 'decimal:2',
        'disclunas' => 'decimal:2',
        'bytenggang' => 'decimal:2',
        'nomlipat' => 'decimal:2',
        'nilaipasar' => 'decimal:2',
    ];
}
