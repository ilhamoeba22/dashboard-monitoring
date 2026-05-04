<?php

declare(strict_types=1);

namespace App\Models\Mci\ReportingOjk;

use App\Models\Mci\MciBaseModel;

/**
 * Model MCI: BIFORM14
 * --------------------------------------------------------------------------
 * Domain   : Reporting OJK/BI
 * Tabel    : [dbo].[BIFORM14]
 * Kolom    : 19
 * Sumber   : Mapping_MCI_MAR26_01042026.sql / MCI_MAR26_01042026.xlsx
 *
 * CATATAN:
 *  - Model ini READ-ONLY (lihat trait ReadOnlyModel di MciBaseModel).
 *  - Koneksi default `dashboard_data` (SQL Server), otomatis ke DB bulan terbaru.
 *  - Kolom dengan spasi/karakter khusus diakses via:
 *    $model->getAttribute('NAMA KOLOM')  atau  $model->{'NAMA KOLOM'}.
 *
 * @property string $kdloc type: char(2)
 * @property string $nocif type: varchar(9)
 * @property string|null $nama type: varchar(50)
 * @property string|null $noid type: varchar(20)
 * @property string|null $groupdeb type: varchar(15)
 * @property string|null $goldeb type: varchar(6)
 * @property string|null $hubbank type: varchar(1)
 * @property string|null $jnsaset type: varchar(3)
 * @property string|null $norek type: varchar(11)
 * @property string|null $tgleff type: varchar(8)
 * @property string|null $tglexp type: varchar(8)
 * @property string|null $tujuan type: char(1)
 * @property string|null $mtdangs type: char(1)
 * @property string|null $hargabeli type: numeric(9)
 * @property string|null $progress type: numeric(5)
 * @property string|null $nocifpemesan type: varchar(9)
 * @property string|null $norekpemesan type: varchar(9)
 * @property string|null $termin type: numeric(9)
 * @property string|null $jumlah type: numeric(9)
 */
class Biform14 extends MciBaseModel
{
    /**
     * Nama tabel (case-sensitive di SQL Server).
     */
    protected $table = 'BIFORM14';

    /**
     * Daftar LENGKAP kolom sesuai database (19 kolom).
     * Model ini read-only, $fillable hanya untuk dokumentasi & IDE helper.
     *
     * @var array<int,string>
     */
    protected $fillable = [
        'kdloc',
        'nocif',
        'nama',
        'noid',
        'groupdeb',
        'goldeb',
        'hubbank',
        'jnsaset',
        'norek',
        'tgleff',
        'tglexp',
        'tujuan',
        'mtdangs',
        'hargabeli',
        'progress',
        'nocifpemesan',
        'norekpemesan',
        'termin',
        'jumlah',
    ];

    /**
     * Casting tipe kolom (non-string saja).
     *
     * @var array<string,string>
     */
    protected $casts = [
        'hargabeli' => 'decimal:2',
        'progress' => 'decimal:2',
        'termin' => 'decimal:2',
        'jumlah' => 'decimal:2',
    ];
}
