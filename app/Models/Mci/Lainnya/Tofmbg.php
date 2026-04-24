<?php

namespace App\Models\Mci\Lainnya;

use App\Models\Mci\MciBaseModel;

/**
 * Model MCI: TOFMBG
 * --------------------------------------------------------------------------
 * Domain   : Lainnya
 * Tabel    : [dbo].[TOFMBG]
 * Kolom    : 27
 * Sumber   : Mapping_MCI_MAR26_01042026.sql / MCI_MAR26_01042026.xlsx
 *
 * CATATAN:
 *  - Model ini READ-ONLY (lihat trait ReadOnlyModel di MciBaseModel).
 *  - Koneksi default `dashboard_data` (SQL Server), otomatis ke DB bulan terbaru.
 *  - Kolom dengan spasi/karakter khusus diakses via:
 *    $model->getAttribute('NAMA KOLOM')  atau  $model->{'NAMA KOLOM'}.
 *
 * @property string $kdbg  type: char(1)
 * @property string|null $nobg  type: varchar(50)
 * @property string|null $nocif  type: varchar(9)
 * @property string|null $tglbg  type: varchar(8)
 * @property string|null $amount_bg  type: numeric(9)
 * @property string|null $amount_setjam  type: numeric(9)
 * @property string|null $amount_byadm  type: numeric(9)
 * @property string|null $tujuan  type: varchar(150)
 * @property string|null $tgleff  type: varchar(8)
 * @property string|null $tglexp  type: varchar(8)
 * @property string|null $masaclaim  type: numeric(5)
 * @property string|null $tglexpclaim  type: varchar(8)
 * @property string|null $ket  type: varchar(100)
 * @property string|null $nodebet  type: varchar(11)
 * @property string|null $nosbbbg  type: varchar(11)
 * @property string|null $ststrn  type: char(1)
 * @property string|null $stscetak  type: char(1)
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
class Tofmbg extends MciBaseModel
{
    /**
     * Nama tabel (case-sensitive di SQL Server).
     */
    protected $table = 'TOFMBG';

    /**
     * Daftar LENGKAP kolom sesuai database (27 kolom).
     * Model ini read-only, $fillable hanya untuk dokumentasi & IDE helper.
     *
     * @var array<int,string>
     */
    protected $fillable = [
        'kdbg',
        'nobg',
        'nocif',
        'tglbg',
        'amount_bg',
        'amount_setjam',
        'amount_byadm',
        'tujuan',
        'tgleff',
        'tglexp',
        'masaclaim',
        'tglexpclaim',
        'ket',
        'nodebet',
        'nosbbbg',
        'ststrn',
        'stscetak',
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
        'amount_bg' => 'decimal:2',
        'amount_setjam' => 'decimal:2',
        'amount_byadm' => 'decimal:2',
        'masaclaim' => 'decimal:2',
    ];
}
