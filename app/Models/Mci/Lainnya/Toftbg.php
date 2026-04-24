<?php

namespace App\Models\Mci\Lainnya;

use App\Models\Mci\MciBaseModel;

/**
 * Model MCI: TOFTBG
 * --------------------------------------------------------------------------
 * Domain   : Lainnya
 * Tabel    : [dbo].[TOFTBG]
 * Kolom    : 19
 * Sumber   : Mapping_MCI_MAR26_01042026.sql / MCI_MAR26_01042026.xlsx
 *
 * CATATAN:
 *  - Model ini READ-ONLY (lihat trait ReadOnlyModel di MciBaseModel).
 *  - Koneksi default `dashboard_data` (SQL Server), otomatis ke DB bulan terbaru.
 *  - Kolom dengan spasi/karakter khusus diakses via:
 *    $model->getAttribute('NAMA KOLOM')  atau  $model->{'NAMA KOLOM'}.
 *
 * @property string $kdbg  type: char(1)
 * @property string|null $ketbg  type: varchar(50)
 * @property string|null $sbbbg  type: varchar(7)
 * @property string|null $sbbsetjam  type: varchar(7)
 * @property string|null $sbbbyadm  type: varchar(7)
 * @property string|null $sbbbyppap  type: varchar(7)
 * @property string|null $sbbppap  type: varchar(7)
 * @property string|null $sbbaba  type: varchar(7)
 * @property string|null $sbbabp  type: varchar(7)
 * @property string|null $stsrec  type: char(1)
 * @property string|null $inpuser  type: varchar(10)
 * @property string|null $inptgljam  type: varchar(14)
 * @property string|null $inpterm  type: varchar(10)
 * @property string|null $chguser  type: varchar(10)
 * @property string|null $chgtgljam  type: varchar(14)
 * @property string|null $chgterm  type: char(10)
 * @property string|null $autuser  type: char(10)
 * @property string|null $auttgljam  type: char(14)
 * @property string|null $autterm  type: varchar(10)
 */
class Toftbg extends MciBaseModel
{
    /**
     * Nama tabel (case-sensitive di SQL Server).
     */
    protected $table = 'TOFTBG';

    /**
     * Daftar LENGKAP kolom sesuai database (19 kolom).
     * Model ini read-only, $fillable hanya untuk dokumentasi & IDE helper.
     *
     * @var array<int,string>
     */
    protected $fillable = [
        'kdbg',
        'ketbg',
        'sbbbg',
        'sbbsetjam',
        'sbbbyadm',
        'sbbbyppap',
        'sbbppap',
        'sbbaba',
        'sbbabp',
        'stsrec',
        'inpuser',
        'inptgljam',
        'inpterm',
        'chguser',
        'chgtgljam',
        'chgterm',
        'autuser',
        'auttgljam',
        'autterm',
    ];

    /**
     * Casting tipe kolom (non-string saja).
     *
     * @var array<string,string>
     */
    protected $casts = [

    ];
}
