<?php

namespace App\Models\Mci\Lainnya;

use App\Models\Mci\MciBaseModel;

/**
 * Model MCI: m_loan_writeoff
 * --------------------------------------------------------------------------
 * Domain   : Lainnya
 * Tabel    : [dbo].[m_loan_writeoff]
 * Kolom    : 22
 * Sumber   : Mapping_MCI_MAR26_01042026.sql / MCI_MAR26_01042026.xlsx
 *
 * CATATAN:
 *  - Model ini READ-ONLY (lihat trait ReadOnlyModel di MciBaseModel).
 *  - Koneksi default `dashboard_data` (SQL Server), otomatis ke DB bulan terbaru.
 *  - Kolom dengan spasi/karakter khusus diakses via:
 *    $model->getAttribute('NAMA KOLOM')  atau  $model->{'NAMA KOLOM'}.
 *
 * @property string $kdacc  type: varchar(1)
 * @property string $kdprd  type: varchar(2)
 * @property string $noacc  type: varchar(11)
 * @property string|null $fnama  type: varchar(30)
 * @property string|null $nocif  type: varchar(10)
 * @property string|null $tglproses  type: varchar(8)
 * @property string|null $os  type: numeric(9)
 * @property string|null $bunga  type: numeric(9)
 * @property string|null $denda  type: numeric(9)
 * @property string|null $os_lalu  type: numeric(9)
 * @property string|null $mutasi_dr  type: numeric(9)
 * @property string|null $mutasi_cr  type: numeric(9)
 * @property string|null $os_akhir  type: numeric(9)
 * @property string|null $bunga_akhir  type: numeric(9)
 * @property string|null $denda_akhir  type: numeric(9)
 * @property string|null $sbbpok  type: varchar(7)
 * @property string|null $dokumen  type: varchar(30)
 * @property string|null $ket  type: varchar(50)
 * @property string|null $stsrec  type: varchar(1)
 * @property string|null $inpuser  type: varchar(10)
 * @property string|null $inptgljam  type: varchar(14)
 * @property string|null $tglkons  type: varchar(8)
 */
class MLoanWriteoff extends MciBaseModel
{
    /**
     * Nama tabel (case-sensitive di SQL Server).
     */
    protected $table = 'm_loan_writeoff';

    /**
     * Daftar LENGKAP kolom sesuai database (22 kolom).
     * Model ini read-only, $fillable hanya untuk dokumentasi & IDE helper.
     *
     * @var array<int,string>
     */
    protected $fillable = [
        'kdacc',
        'kdprd',
        'noacc',
        'fnama',
        'nocif',
        'tglproses',
        'os',
        'bunga',
        'denda',
        'os_lalu',
        'mutasi_dr',
        'mutasi_cr',
        'os_akhir',
        'bunga_akhir',
        'denda_akhir',
        'sbbpok',
        'dokumen',
        'ket',
        'stsrec',
        'inpuser',
        'inptgljam',
        'tglkons',
    ];

    /**
     * Casting tipe kolom (non-string saja).
     *
     * @var array<string,string>
     */
    protected $casts = [
        'os' => 'decimal:2',
        'bunga' => 'decimal:2',
        'denda' => 'decimal:2',
        'os_lalu' => 'decimal:2',
        'mutasi_dr' => 'decimal:2',
        'mutasi_cr' => 'decimal:2',
        'os_akhir' => 'decimal:2',
        'bunga_akhir' => 'decimal:2',
        'denda_akhir' => 'decimal:2',
    ];
}
