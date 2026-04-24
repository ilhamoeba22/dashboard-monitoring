<?php

namespace App\Models\Mci\Lainnya;

use App\Models\Mci\MciBaseModel;

/**
 * Model MCI: TOFGMD
 * --------------------------------------------------------------------------
 * Domain   : Lainnya
 * Tabel    : [dbo].[TOFGMD]
 * Kolom    : 14
 * Sumber   : Mapping_MCI_MAR26_01042026.sql / MCI_MAR26_01042026.xlsx
 *
 * CATATAN:
 *  - Model ini READ-ONLY (lihat trait ReadOnlyModel di MciBaseModel).
 *  - Koneksi default `dashboard_data` (SQL Server), otomatis ke DB bulan terbaru.
 *  - Kolom dengan spasi/karakter khusus diakses via:
 *    $model->getAttribute('NAMA KOLOM')  atau  $model->{'NAMA KOLOM'}.
 *
 * @property string|null $nosbg  type: varchar(20)
 * @property string|null $urut  type: numeric(5)
 * @property string|null $qty  type: numeric(5)
 * @property string|null $ket  type: varchar(100)
 * @property string|null $karat  type: numeric(5)
 * @property string|null $beratkotor  type: numeric(5)
 * @property string|null $beratbersih  type: numeric(5)
 * @property string|null $nomtaksiran  type: numeric(9)
 * @property string|null $flag_1  type: varchar(1)
 * @property string|null $flag_2  type: varchar(1)
 * @property string|null $nokontrak  type: char(11)
 * @property string|null $NOREG  type: varchar(10)
 * @property string|null $JNS  type: varchar(3)
 * @property string|null $nompasar  type: numeric(9)
 */
class Tofgmd extends MciBaseModel
{
    /**
     * Nama tabel (case-sensitive di SQL Server).
     */
    protected $table = 'TOFGMD';

    /**
     * Daftar LENGKAP kolom sesuai database (14 kolom).
     * Model ini read-only, $fillable hanya untuk dokumentasi & IDE helper.
     *
     * @var array<int,string>
     */
    protected $fillable = [
        'nosbg',
        'urut',
        'qty',
        'ket',
        'karat',
        'beratkotor',
        'beratbersih',
        'nomtaksiran',
        'flag_1',
        'flag_2',
        'nokontrak',
        'NOREG',
        'JNS',
        'nompasar',
    ];

    /**
     * Casting tipe kolom (non-string saja).
     *
     * @var array<string,string>
     */
    protected $casts = [
        'urut' => 'decimal:2',
        'qty' => 'decimal:2',
        'karat' => 'decimal:2',
        'beratkotor' => 'decimal:2',
        'beratbersih' => 'decimal:2',
        'nomtaksiran' => 'decimal:2',
        'nompasar' => 'decimal:2',
    ];
}
