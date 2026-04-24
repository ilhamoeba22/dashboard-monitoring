<?php

namespace App\Models\Mci\Agunan;

use App\Models\Mci\MciBaseModel;

/**
 * Model MCI: H_AGUNTRN
 * --------------------------------------------------------------------------
 * Domain   : Agunan / Jaminan
 * Tabel    : [dbo].[H_AGUNTRN]
 * Kolom    : 21
 * Sumber   : Mapping_MCI_MAR26_01042026.sql / MCI_MAR26_01042026.xlsx
 *
 * CATATAN:
 *  - Model ini READ-ONLY (lihat trait ReadOnlyModel di MciBaseModel).
 *  - Koneksi default `dashboard_data` (SQL Server), otomatis ke DB bulan terbaru.
 *  - Kolom dengan spasi/karakter khusus diakses via:
 *    $model->getAttribute('NAMA KOLOM')  atau  $model->{'NAMA KOLOM'}.
 *
 * @property string $ID  type: numeric(9)
 * @property string|null $notrn  type: numeric(5)
 * @property string|null $nokontrak  type: varchar(11)
 * @property string|null $noreg  type: varchar(10)
 * @property string|null $urut  type: numeric(5)
 * @property string|null $tgl  type: varchar(8)
 * @property string|null $kdtrx  type: varchar(3)
 * @property string|null $ket  type: varchar(50)
 * @property string|null $ketuser  type: varchar(100)
 * @property string|null $noregbaru  type: varchar(10)
 * @property string|null $urutbaru  type: numeric(5)
 * @property string|null $peminjam  type: varchar(50)
 * @property string|null $tglkembali  type: varchar(8)
 * @property string|null $stsrec  type: varchar(1)
 * @property string|null $inpuser  type: numeric(5)
 * @property string|null $inptgljam  type: varchar(14)
 * @property string|null $inpterm  type: varchar(10)
 * @property string|null $autuser  type: numeric(5)
 * @property string|null $auttgljam  type: varchar(14)
 * @property string|null $autterm  type: varchar(10)
 * @property string|null $kdphk3  type: varchar(10)
 */
class HAguntrn extends MciBaseModel
{
    /**
     * Nama tabel (case-sensitive di SQL Server).
     */
    protected $table = 'H_AGUNTRN';

    /**
     * Daftar LENGKAP kolom sesuai database (21 kolom).
     * Model ini read-only, $fillable hanya untuk dokumentasi & IDE helper.
     *
     * @var array<int,string>
     */
    protected $fillable = [
        'ID',
        'notrn',
        'nokontrak',
        'noreg',
        'urut',
        'tgl',
        'kdtrx',
        'ket',
        'ketuser',
        'noregbaru',
        'urutbaru',
        'peminjam',
        'tglkembali',
        'stsrec',
        'inpuser',
        'inptgljam',
        'inpterm',
        'autuser',
        'auttgljam',
        'autterm',
        'kdphk3',
    ];

    /**
     * Casting tipe kolom (non-string saja).
     *
     * @var array<string,string>
     */
    protected $casts = [
        'ID' => 'decimal:2',
        'notrn' => 'decimal:2',
        'urut' => 'decimal:2',
        'urutbaru' => 'decimal:2',
        'inpuser' => 'decimal:2',
        'autuser' => 'decimal:2',
    ];
}
