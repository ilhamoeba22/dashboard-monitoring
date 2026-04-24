<?php

namespace App\Models\Mci\ReportingOjk;

use App\Models\Mci\MciBaseModel;

/**
 * Model MCI: OJK0200
 * --------------------------------------------------------------------------
 * Domain   : Reporting OJK/BI
 * Tabel    : [dbo].[OJK0200]
 * Kolom    : 12
 * Sumber   : Mapping_MCI_MAR26_01042026.sql / MCI_MAR26_01042026.xlsx
 *
 * CATATAN:
 *  - Model ini READ-ONLY (lihat trait ReadOnlyModel di MciBaseModel).
 *  - Koneksi default `dashboard_data` (SQL Server), otomatis ke DB bulan terbaru.
 *  - Kolom dengan spasi/karakter khusus diakses via:
 *    $model->getAttribute('NAMA KOLOM')  atau  $model->{'NAMA KOLOM'}.
 *
 * @property string|null $periode  type: char(10)
 * @property string|null $kdloc  type: char(2)
 * @property string|null $kdlap  type: char(3)
 * @property string $urut  type: numeric(5)
 * @property string|null $jnsgol  type: char(10)
 * @property string|null $sandi  type: char(5)
 * @property string|null $ket  type: varchar(225)
 * @property string|null $sandi_lawan  type: char(5)
 * @property string|null $saldo  type: numeric(9)
 * @property string|null $saldo2  type: numeric(9)
 * @property string|null $kdktrbi2  type: char(6)
 * @property string|null $kode  type: char(6)
 */
class Ojk0200 extends MciBaseModel
{
    /**
     * Nama tabel (case-sensitive di SQL Server).
     */
    protected $table = 'OJK0200';

    /**
     * Daftar LENGKAP kolom sesuai database (12 kolom).
     * Model ini read-only, $fillable hanya untuk dokumentasi & IDE helper.
     *
     * @var array<int,string>
     */
    protected $fillable = [
        'periode',
        'kdloc',
        'kdlap',
        'urut',
        'jnsgol',
        'sandi',
        'ket',
        'sandi_lawan',
        'saldo',
        'saldo2',
        'kdktrbi2',
        'kode',
    ];

    /**
     * Casting tipe kolom (non-string saja).
     *
     * @var array<string,string>
     */
    protected $casts = [
        'urut' => 'decimal:2',
        'saldo' => 'decimal:2',
        'saldo2' => 'decimal:2',
    ];
}
