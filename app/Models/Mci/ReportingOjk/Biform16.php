<?php

declare(strict_types=1);

namespace App\Models\Mci\ReportingOjk;

use App\Models\Mci\MciBaseModel;

/**
 * Model MCI: BIFORM16
 * --------------------------------------------------------------------------
 * Domain   : Reporting OJK/BI
 * Tabel    : [dbo].[BIFORM16]
 * Kolom    : 18
 * Sumber   : Mapping_MCI_MAR26_01042026.sql / MCI_MAR26_01042026.xlsx
 *
 * CATATAN:
 *  - Model ini READ-ONLY (lihat trait ReadOnlyModel di MciBaseModel).
 *  - Koneksi default `dashboard_data` (SQL Server), otomatis ke DB bulan terbaru.
 *  - Kolom dengan spasi/karakter khusus diakses via:
 *    $model->getAttribute('NAMA KOLOM')  atau  $model->{'NAMA KOLOM'}.
 *
 * @property string $kdloc type: char(2)
 * @property string $noaset type: varchar(25)
 * @property string|null $jnsaset type: char(3)
 * @property string|null $latitude type: numeric(9)
 * @property string|null $longitude type: numeric(9)
 * @property string|null $nocif type: varchar(9)
 * @property string|null $noid type: varchar(16)
 * @property string|null $norek type: varchar(20)
 * @property string|null $jnsakad type: char(2)
 * @property string|null $tglayda type: varchar(8)
 * @property string|null $coll type: char(1)
 * @property string|null $osmdlc type: numeric(9)
 * @property string|null $osmgnc type: numeric(9)
 * @property string|null $nilaiagunan type: numeric(9)
 * @property string|null $nilaibersih type: numeric(9)
 * @property string|null $ckpn type: numeric(9)
 * @property string|null $nilaitercatat type: numeric(9)
 * @property string|null $jumlah type: numeric(9)
 */
class Biform16 extends MciBaseModel
{
    /**
     * Nama tabel (case-sensitive di SQL Server).
     */
    protected $table = 'BIFORM16';

    /**
     * Daftar LENGKAP kolom sesuai database (18 kolom).
     * Model ini read-only, $fillable hanya untuk dokumentasi & IDE helper.
     *
     * @var array<int,string>
     */
    protected $fillable = [
        'kdloc',
        'noaset',
        'jnsaset',
        'latitude',
        'longitude',
        'nocif',
        'noid',
        'norek',
        'jnsakad',
        'tglayda',
        'coll',
        'osmdlc',
        'osmgnc',
        'nilaiagunan',
        'nilaibersih',
        'ckpn',
        'nilaitercatat',
        'jumlah',
    ];

    /**
     * Casting tipe kolom (non-string saja).
     *
     * @var array<string,string>
     */
    protected $casts = [
        'latitude' => 'decimal:2',
        'longitude' => 'decimal:2',
        'osmdlc' => 'decimal:2',
        'osmgnc' => 'decimal:2',
        'nilaiagunan' => 'decimal:2',
        'nilaibersih' => 'decimal:2',
        'ckpn' => 'decimal:2',
        'nilaitercatat' => 'decimal:2',
        'jumlah' => 'decimal:2',
    ];
}
