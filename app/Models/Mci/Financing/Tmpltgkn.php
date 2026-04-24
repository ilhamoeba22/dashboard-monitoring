<?php

namespace App\Models\Mci\Financing;

use App\Models\Mci\MciBaseModel;

/**
 * Model MCI: TMPLTGKN
 * --------------------------------------------------------------------------
 * Domain   : Financing / Loan
 * Tabel    : [dbo].[TMPLTGKN]
 * Kolom    : 37
 * Sumber   : Mapping_MCI_MAR26_01042026.sql / MCI_MAR26_01042026.xlsx
 *
 * CATATAN:
 *  - Model ini READ-ONLY (lihat trait ReadOnlyModel di MciBaseModel).
 *  - Koneksi default `dashboard_data` (SQL Server), otomatis ke DB bulan terbaru.
 *  - Kolom dengan spasi/karakter khusus diakses via:
 *    $model->getAttribute('NAMA KOLOM')  atau  $model->{'NAMA KOLOM'}.
 *
 * @property string $userid  type: varchar(10)
 * @property string $nokontrak  type: varchar(11)
 * @property string $nama  type: varchar(30)
 * @property string $kdprd  type: varchar(2)
 * @property string $kdloc  type: varchar(2)
 * @property string|null $kdwil  type: varchar(2)
 * @property string|null $tgleff  type: varchar(8)
 * @property string|null $jw  type: numeric(5)
 * @property string|null $tglexp  type: varchar(8)
 * @property string|null $mdlawal  type: numeric(9)
 * @property string|null $mgnawal  type: numeric(9)
 * @property string|null $osmdlc  type: numeric(9)
 * @property string|null $osmgnc  type: numeric(9)
 * @property string|null $colbaru  type: varchar(1)
 * @property string|null $kdaoh  type: varchar(10)
 * @property string|null $acpok  type: varchar(11)
 * @property string|null $angsmdl  type: numeric(9)
 * @property string|null $angsmgn  type: numeric(9)
 * @property string|null $alamat  type: varchar(40)
 * @property string|null $telprmh  type: varchar(10)
 * @property string|null $hp  type: varchar(20)
 * @property string|null $fnama  type: varchar(30)
 * @property string|null $sahirrp  type: numeric(9)
 * @property string|null $tgkpok  type: numeric(9)
 * @property string|null $tgkmgn  type: numeric(9)
 * @property string|null $tgkdnd  type: numeric(9)
 * @property string|null $blntgkpok  type: numeric(5)
 * @property string|null $blntgkmgn  type: numeric(5)
 * @property string|null $blntgkdnd  type: numeric(5)
 * @property string|null $kdkolek  type: varchar(10)
 * @property string|null $kdgroupdeb  type: varchar(10)
 * @property string|null $kdgroupdana  type: varchar(10)
 * @property string|null $kelurahan  type: varchar(30)
 * @property string|null $kecamatan  type: varchar(30)
 * @property string|null $kota  type: varchar(30)
 * @property string|null $tgkharilanjut  type: numeric(5)
 * @property string|null $colllanjut  type: varchar(1)
 */
class Tmpltgkn extends MciBaseModel
{
    /**
     * Nama tabel (case-sensitive di SQL Server).
     */
    protected $table = 'TMPLTGKN';

    /**
     * Daftar LENGKAP kolom sesuai database (37 kolom).
     * Model ini read-only, $fillable hanya untuk dokumentasi & IDE helper.
     *
     * @var array<int,string>
     */
    protected $fillable = [
        'userid',
        'nokontrak',
        'nama',
        'kdprd',
        'kdloc',
        'kdwil',
        'tgleff',
        'jw',
        'tglexp',
        'mdlawal',
        'mgnawal',
        'osmdlc',
        'osmgnc',
        'colbaru',
        'kdaoh',
        'acpok',
        'angsmdl',
        'angsmgn',
        'alamat',
        'telprmh',
        'hp',
        'fnama',
        'sahirrp',
        'tgkpok',
        'tgkmgn',
        'tgkdnd',
        'blntgkpok',
        'blntgkmgn',
        'blntgkdnd',
        'kdkolek',
        'kdgroupdeb',
        'kdgroupdana',
        'kelurahan',
        'kecamatan',
        'kota',
        'tgkharilanjut',
        'colllanjut',
    ];

    /**
     * Casting tipe kolom (non-string saja).
     *
     * @var array<string,string>
     */
    protected $casts = [
        'jw' => 'decimal:2',
        'mdlawal' => 'decimal:2',
        'mgnawal' => 'decimal:2',
        'osmdlc' => 'decimal:2',
        'osmgnc' => 'decimal:2',
        'angsmdl' => 'decimal:2',
        'angsmgn' => 'decimal:2',
        'sahirrp' => 'decimal:2',
        'tgkpok' => 'decimal:2',
        'tgkmgn' => 'decimal:2',
        'tgkdnd' => 'decimal:2',
        'blntgkpok' => 'decimal:2',
        'blntgkmgn' => 'decimal:2',
        'blntgkdnd' => 'decimal:2',
        'tgkharilanjut' => 'decimal:2',
    ];
}
