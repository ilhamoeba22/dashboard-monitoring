<?php

declare(strict_types=1);

namespace App\Models\Mci\AsetTetap;

use App\Models\Mci\MciBaseModel;

/**
 * Model MCI: SETUPASET
 * --------------------------------------------------------------------------
 * Domain   : Aset Tetap
 * Tabel    : [dbo].[SETUPASET]
 * Kolom    : 17
 * Sumber   : Mapping_MCI_MAR26_01042026.sql / MCI_MAR26_01042026.xlsx
 *
 * CATATAN:
 *  - Model ini READ-ONLY (lihat trait ReadOnlyModel di MciBaseModel).
 *  - Koneksi default `dashboard_data` (SQL Server), otomatis ke DB bulan terbaru.
 *  - Kolom dengan spasi/karakter khusus diakses via:
 *    $model->getAttribute('NAMA KOLOM')  atau  $model->{'NAMA KOLOM'}.
 *
 * @property string $pokaset type: varchar(2)
 * @property string|null $urut type: numeric(5)
 * @property string|null $kdbiaya type: numeric(5)
 * @property string|null $ket type: varchar(30)
 * @property string|null $nosbb type: varchar(7)
 * @property string|null $nosubsbb type: varchar(7)
 * @property string|null $stsrec type: varchar(1)
 * @property string|null $inpuser type: varchar(10)
 * @property string|null $inptgl type: varchar(14)
 * @property string|null $inpterm type: varchar(10)
 * @property string|null $chguser type: varchar(10)
 * @property string|null $chgtgl type: varchar(14)
 * @property string|null $chgterm type: varchar(10)
 * @property string|null $autuser type: varchar(10)
 * @property string|null $auttgl type: varchar(14)
 * @property string|null $autterm type: varchar(10)
 * @property string|null $groupaset type: varchar(5)
 */
class Setupaset extends MciBaseModel
{
    /**
     * Nama tabel (case-sensitive di SQL Server).
     */
    protected $table = 'SETUPASET';

    /**
     * Daftar LENGKAP kolom sesuai database (17 kolom).
     * Model ini read-only, $fillable hanya untuk dokumentasi & IDE helper.
     *
     * @var array<int,string>
     */
    protected $fillable = [
        'pokaset',
        'urut',
        'kdbiaya',
        'ket',
        'nosbb',
        'nosubsbb',
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
        'groupaset',
    ];

    /**
     * Casting tipe kolom (non-string saja).
     *
     * @var array<string,string>
     */
    protected $casts = [
        'urut' => 'decimal:2',
        'kdbiaya' => 'decimal:2',
    ];
}
