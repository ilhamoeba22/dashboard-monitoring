<?php

namespace App\Models\Mci\Lainnya;

use App\Models\Mci\MciBaseModel;

/**
 * Model MCI: TOFMHTGBNK
 * --------------------------------------------------------------------------
 * Domain   : Lainnya
 * Tabel    : [dbo].[TOFMHTGBNK]
 * Kolom    : 32
 * Sumber   : Mapping_MCI_MAR26_01042026.sql / MCI_MAR26_01042026.xlsx
 *
 * CATATAN:
 *  - Model ini READ-ONLY (lihat trait ReadOnlyModel di MciBaseModel).
 *  - Koneksi default `dashboard_data` (SQL Server), otomatis ke DB bulan terbaru.
 *  - Kolom dengan spasi/karakter khusus diakses via:
 *    $model->getAttribute('NAMA KOLOM')  atau  $model->{'NAMA KOLOM'}.
 *
 * @property int $ID  type: bigint(8)
 * @property string $nohtg  type: varchar(10)
 * @property string $noakad  type: varchar(40)
 * @property string|null $kdhtg  type: char(2)
 * @property string|null $kdphk3  type: varchar(10)
 * @property string|null $kdprd  type: char(2)
 * @property string|null $pokpby  type: char(2)
 * @property string|null $nama  type: varchar(50)
 * @property string|null $tglbuka  type: varchar(8)
 * @property string|null $tgleff  type: varchar(8)
 * @property string|null $tglexp  type: varchar(8)
 * @property string|null $jw  type: numeric(5)
 * @property string|null $rate  type: numeric(5)
 * @property string|null $kdrate  type: char(1)
 * @property string|null $nisbah  type: numeric(5)
 * @property string|null $mdlawal  type: numeric(9)
 * @property string|null $mgnawal  type: numeric(9)
 * @property string|null $mutasidr  type: numeric(9)
 * @property string|null $mutasicr  type: numeric(9)
 * @property string|null $osmdlc  type: numeric(9)
 * @property string|null $osmgnc  type: numeric(9)
 * @property string|null $ststrn  type: char(1)
 * @property string|null $stsrec  type: char(1)
 * @property string|null $inpuser  type: varchar(10)
 * @property string|null $inptgl  type: varchar(10)
 * @property string|null $inpterm  type: varchar(10)
 * @property string|null $chguser  type: varchar(10)
 * @property string|null $chgtgl  type: varchar(10)
 * @property string|null $chgterm  type: varchar(10)
 * @property string|null $autuser  type: varchar(10)
 * @property string|null $auttgl  type: varchar(10)
 * @property string|null $autterm  type: varchar(10)
 */
class Tofmhtgbnk extends MciBaseModel
{
    /**
     * Nama tabel (case-sensitive di SQL Server).
     */
    protected $table = 'TOFMHTGBNK';

    /**
     * Daftar LENGKAP kolom sesuai database (32 kolom).
     * Model ini read-only, $fillable hanya untuk dokumentasi & IDE helper.
     *
     * @var array<int,string>
     */
    protected $fillable = [
        'ID',
        'nohtg',
        'noakad',
        'kdhtg',
        'kdphk3',
        'kdprd',
        'pokpby',
        'nama',
        'tglbuka',
        'tgleff',
        'tglexp',
        'jw',
        'rate',
        'kdrate',
        'nisbah',
        'mdlawal',
        'mgnawal',
        'mutasidr',
        'mutasicr',
        'osmdlc',
        'osmgnc',
        'ststrn',
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
    ];

    /**
     * Casting tipe kolom (non-string saja).
     *
     * @var array<string,string>
     */
    protected $casts = [
        'ID' => 'integer',
        'jw' => 'decimal:2',
        'rate' => 'decimal:2',
        'nisbah' => 'decimal:2',
        'mdlawal' => 'decimal:2',
        'mgnawal' => 'decimal:2',
        'mutasidr' => 'decimal:2',
        'mutasicr' => 'decimal:2',
        'osmdlc' => 'decimal:2',
        'osmgnc' => 'decimal:2',
    ];
}
