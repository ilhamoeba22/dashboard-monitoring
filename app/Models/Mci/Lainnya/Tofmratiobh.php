<?php

namespace App\Models\Mci\Lainnya;

use App\Models\Mci\MciBaseModel;

/**
 * Model MCI: TOFMRATIOBH
 * --------------------------------------------------------------------------
 * Domain   : Lainnya
 * Tabel    : [dbo].[TOFMRATIOBH]
 * Kolom    : 13
 * Sumber   : Mapping_MCI_MAR26_01042026.sql / MCI_MAR26_01042026.xlsx
 *
 * CATATAN:
 *  - Model ini READ-ONLY (lihat trait ReadOnlyModel di MciBaseModel).
 *  - Koneksi default `dashboard_data` (SQL Server), otomatis ke DB bulan terbaru.
 *  - Kolom dengan spasi/karakter khusus diakses via:
 *    $model->getAttribute('NAMA KOLOM')  atau  $model->{'NAMA KOLOM'}.
 *
 * @property string|null $kdkol  type: varchar(1)
 * @property string $nokontrak  type: char(11)
 * @property string|null $akum_tagmgn  type: numeric(9)
 * @property string|null $akum_byrmgn  type: numeric(9)
 * @property string|null $periode  type: numeric(5)
 * @property string|null $rata_tagmgn  type: numeric(9)
 * @property string|null $rata_byrmgn  type: numeric(9)
 * @property string|null $prosbyr  type: numeric(9)
 * @property string|null $tgkmodal  type: numeric(9)
 * @property string|null $blntgkmodal  type: numeric(5)
 * @property string|null $byrmodal  type: numeric(9)
 * @property string|null $tglproses  type: char(8)
 * @property string|null $coll  type: char(1)
 */
class Tofmratiobh extends MciBaseModel
{
    /**
     * Nama tabel (case-sensitive di SQL Server).
     */
    protected $table = 'TOFMRATIOBH';

    /**
     * Daftar LENGKAP kolom sesuai database (13 kolom).
     * Model ini read-only, $fillable hanya untuk dokumentasi & IDE helper.
     *
     * @var array<int,string>
     */
    protected $fillable = [
        'kdkol',
        'nokontrak',
        'akum_tagmgn',
        'akum_byrmgn',
        'periode',
        'rata_tagmgn',
        'rata_byrmgn',
        'prosbyr',
        'tgkmodal',
        'blntgkmodal',
        'byrmodal',
        'tglproses',
        'coll',
    ];

    /**
     * Casting tipe kolom (non-string saja).
     *
     * @var array<string,string>
     */
    protected $casts = [
        'akum_tagmgn' => 'decimal:2',
        'akum_byrmgn' => 'decimal:2',
        'periode' => 'decimal:2',
        'rata_tagmgn' => 'decimal:2',
        'rata_byrmgn' => 'decimal:2',
        'prosbyr' => 'decimal:2',
        'tgkmodal' => 'decimal:2',
        'blntgkmodal' => 'decimal:2',
        'byrmodal' => 'decimal:2',
    ];
}
