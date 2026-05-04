<?php

declare(strict_types=1);

namespace App\Models\Mci\ReportingOjk;

use App\Models\Mci\MciBaseModel;

/**
 * Model MCI: GB0108
 * --------------------------------------------------------------------------
 * Domain   : Reporting OJK/BI
 * Tabel    : [dbo].[GB0108]
 * Kolom    : 8
 * Sumber   : Mapping_MCI_MAR26_01042026.sql / MCI_MAR26_01042026.xlsx
 *
 * CATATAN:
 *  - Model ini READ-ONLY (lihat trait ReadOnlyModel di MciBaseModel).
 *  - Koneksi default `dashboard_data` (SQL Server), otomatis ke DB bulan terbaru.
 *  - Kolom dengan spasi/karakter khusus diakses via:
 *    $model->getAttribute('NAMA KOLOM')  atau  $model->{'NAMA KOLOM'}.
 *
 * @property int $id type: int(4)
 * @property string $sandi_kantor_cab type: varchar(3)
 * @property string $nama_kantor_cab type: varchar(100)
 * @property string|null $alamat type: varchar(100)
 * @property string|null $dati type: varchar(4)
 * @property string|null $latitude type: numeric(9)
 * @property string|null $longtitude type: numeric(9)
 * @property string|null $tgl_tutup type: varchar(10)
 */
class Gb0108 extends MciBaseModel
{
    /**
     * Nama tabel (case-sensitive di SQL Server).
     */
    protected $table = 'GB0108';

    /**
     * Daftar LENGKAP kolom sesuai database (8 kolom).
     * Model ini read-only, $fillable hanya untuk dokumentasi & IDE helper.
     *
     * @var array<int,string>
     */
    protected $fillable = [
        'id',
        'sandi_kantor_cab',
        'nama_kantor_cab',
        'alamat',
        'dati',
        'latitude',
        'longtitude',
        'tgl_tutup',
    ];

    /**
     * Casting tipe kolom (non-string saja).
     *
     * @var array<string,string>
     */
    protected $casts = [
        'id' => 'integer',
        'latitude' => 'decimal:2',
        'longtitude' => 'decimal:2',
    ];
}
