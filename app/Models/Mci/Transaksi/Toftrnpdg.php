<?php

declare(strict_types=1);

namespace App\Models\Mci\Transaksi;

use App\Models\Mci\MciBaseModel;

/**
 * Model MCI: TOFTRNPDG
 * --------------------------------------------------------------------------
 * Domain   : Transaksi
 * Tabel    : [dbo].[TOFTRNPDG]
 * Kolom    : 25
 * Sumber   : Mapping_MCI_MAR26_01042026.sql / MCI_MAR26_01042026.xlsx
 *
 * CATATAN:
 *  - Model ini READ-ONLY (lihat trait ReadOnlyModel di MciBaseModel).
 *  - Koneksi default `dashboard_data` (SQL Server), otomatis ke DB bulan terbaru.
 *  - Kolom dengan spasi/karakter khusus diakses via:
 *    $model->getAttribute('NAMA KOLOM')  atau  $model->{'NAMA KOLOM'}.
 *
 * @property string $tgltrn type: varchar(8)
 * @property string|null $trnuser type: varchar(10)
 * @property string $batch type: numeric(5)
 * @property string $notrn type: numeric(5)
 * @property string|null $kodetrn type: varchar(4)
 * @property string|null $dracc type: varchar(11)
 * @property string|null $drmodul type: varchar(1)
 * @property string|null $cracc type: varchar(11)
 * @property string|null $crmodul type: varchar(1)
 * @property string|null $dc type: varchar(1)
 * @property string|null $nominal type: numeric(9)
 * @property string|null $kodebpr type: varchar(3)
 * @property string|null $kodecab type: varchar(2)
 * @property string|null $kodeloc type: varchar(2)
 * @property string|null $ststrn type: varchar(1)
 * @property string|null $inpuser type: varchar(10)
 * @property string|null $inptgljam type: varchar(14)
 * @property string|null $prog type: varchar(10)
 * @property string|null $groupno type: numeric(5)
 * @property string|null $stscetak type: varchar(1)
 * @property string|null $jnstrntx type: varchar(2)
 * @property string|null $ket type: varchar(40)
 * @property string|null $trnkedr type: numeric(5)
 * @property string|null $trnkecr type: numeric(5)
 * @property string|null $kdao type: varchar(8)
 */
class Toftrnpdg extends MciBaseModel
{
    /**
     * Nama tabel (case-sensitive di SQL Server).
     */
    protected $table = 'TOFTRNPDG';

    /**
     * Daftar LENGKAP kolom sesuai database (25 kolom).
     * Model ini read-only, $fillable hanya untuk dokumentasi & IDE helper.
     *
     * @var array<int,string>
     */
    protected $fillable = [
        'tgltrn',
        'trnuser',
        'batch',
        'notrn',
        'kodetrn',
        'dracc',
        'drmodul',
        'cracc',
        'crmodul',
        'dc',
        'nominal',
        'kodebpr',
        'kodecab',
        'kodeloc',
        'ststrn',
        'inpuser',
        'inptgljam',
        'prog',
        'groupno',
        'stscetak',
        'jnstrntx',
        'ket',
        'trnkedr',
        'trnkecr',
        'kdao',
    ];

    /**
     * Casting tipe kolom (non-string saja).
     *
     * @var array<string,string>
     */
    protected $casts = [
        'batch' => 'decimal:2',
        'notrn' => 'decimal:2',
        'nominal' => 'decimal:2',
        'groupno' => 'decimal:2',
        'trnkedr' => 'decimal:2',
        'trnkecr' => 'decimal:2',
    ];
}
