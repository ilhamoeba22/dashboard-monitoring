<?php

namespace App\Models\Mci\Lainnya;

use App\Models\Mci\MciBaseModel;

/**
 * Model MCI: TMPTRNBD
 * --------------------------------------------------------------------------
 * Domain   : Lainnya
 * Tabel    : [dbo].[TMPTRNBD]
 * Kolom    : 12
 * Sumber   : Mapping_MCI_MAR26_01042026.sql / MCI_MAR26_01042026.xlsx
 *
 * CATATAN:
 *  - Model ini READ-ONLY (lihat trait ReadOnlyModel di MciBaseModel).
 *  - Koneksi default `dashboard_data` (SQL Server), otomatis ke DB bulan terbaru.
 *  - Kolom dengan spasi/karakter khusus diakses via:
 *    $model->getAttribute('NAMA KOLOM')  atau  $model->{'NAMA KOLOM'}.
 *
 * @property string $ID  type: numeric(9)
 * @property string|null $batch  type: numeric(5)
 * @property string|null $tgl  type: char(8)
 * @property string|null $nosbb_dr  type: char(11)
 * @property string|null $nosbb_cr  type: char(11)
 * @property string|null $golac_dr  type: char(1)
 * @property string|null $golac_cr  type: char(1)
 * @property string|null $nama_dr  type: varchar(20)
 * @property string|null $nama_cr  type: varchar(20)
 * @property string|null $dc  type: char(1)
 * @property string|null $nominal  type: numeric(9)
 * @property string|null $ket  type: char(50)
 */
class Tmptrnbd extends MciBaseModel
{
    /**
     * Nama tabel (case-sensitive di SQL Server).
     */
    protected $table = 'TMPTRNBD';

    /**
     * Daftar LENGKAP kolom sesuai database (12 kolom).
     * Model ini read-only, $fillable hanya untuk dokumentasi & IDE helper.
     *
     * @var array<int,string>
     */
    protected $fillable = [
        'ID',
        'batch',
        'tgl',
        'nosbb_dr',
        'nosbb_cr',
        'golac_dr',
        'golac_cr',
        'nama_dr',
        'nama_cr',
        'dc',
        'nominal',
        'ket',
    ];

    /**
     * Casting tipe kolom (non-string saja).
     *
     * @var array<string,string>
     */
    protected $casts = [
        'ID' => 'decimal:2',
        'batch' => 'decimal:2',
        'nominal' => 'decimal:2',
    ];
}
