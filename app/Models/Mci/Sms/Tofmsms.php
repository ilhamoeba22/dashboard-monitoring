<?php

namespace App\Models\Mci\Sms;

use App\Models\Mci\MciBaseModel;

/**
 * Model MCI: TOFMSMS
 * --------------------------------------------------------------------------
 * Domain   : SMS / Notif
 * Tabel    : [dbo].[TOFMSMS]
 * Kolom    : 31
 * Sumber   : Mapping_MCI_MAR26_01042026.sql / MCI_MAR26_01042026.xlsx
 *
 * CATATAN:
 *  - Model ini READ-ONLY (lihat trait ReadOnlyModel di MciBaseModel).
 *  - Koneksi default `dashboard_data` (SQL Server), otomatis ke DB bulan terbaru.
 *  - Kolom dengan spasi/karakter khusus diakses via:
 *    $model->getAttribute('NAMA KOLOM')  atau  $model->{'NAMA KOLOM'}.
 *
 * @property string $notab  type: varchar(11)
 * @property string $nohp  type: varchar(20)
 * @property string|null $minnom  type: numeric(9)
 * @property string|null $stssms01  type: varchar(1)
 * @property string|null $stssms02  type: varchar(1)
 * @property string|null $stssms03  type: varchar(1)
 * @property string|null $stssms04  type: varchar(1)
 * @property string|null $stssms05  type: varchar(1)
 * @property string|null $stssms06  type: varchar(1)
 * @property string|null $stssms07  type: varchar(1)
 * @property string|null $stssms08  type: varchar(1)
 * @property string|null $stssms09  type: varchar(1)
 * @property string|null $maxtrxtab  type: numeric(9)
 * @property string|null $maxtrxhp  type: numeric(9)
 * @property string|null $maxtrxib  type: numeric(9)
 * @property string|null $maxtrxmb  type: numeric(9)
 * @property string|null $maxtrxtabday  type: numeric(9)
 * @property string|null $maxtrxhpday  type: numeric(9)
 * @property string|null $maxtrxibday  type: numeric(9)
 * @property string|null $maxtrxmbday  type: numeric(9)
 * @property string|null $stsrec  type: varchar(1)
 * @property string|null $inpuser  type: varchar(10)
 * @property string|null $inptgljam  type: varchar(14)
 * @property string|null $inpterm  type: varchar(10)
 * @property string|null $chguser  type: varchar(10)
 * @property string|null $chgtgl  type: varchar(14)
 * @property string|null $chgtgljam  type: varchar(10)
 * @property string|null $chgterm  type: varchar(14)
 * @property string|null $autuser  type: varchar(10)
 * @property string|null $auttgljam  type: varchar(14)
 * @property string|null $autterm  type: varchar(14)
 */
class Tofmsms extends MciBaseModel
{
    /**
     * Nama tabel (case-sensitive di SQL Server).
     */
    protected $table = 'TOFMSMS';

    /**
     * Daftar LENGKAP kolom sesuai database (31 kolom).
     * Model ini read-only, $fillable hanya untuk dokumentasi & IDE helper.
     *
     * @var array<int,string>
     */
    protected $fillable = [
        'notab',
        'nohp',
        'minnom',
        'stssms01',
        'stssms02',
        'stssms03',
        'stssms04',
        'stssms05',
        'stssms06',
        'stssms07',
        'stssms08',
        'stssms09',
        'maxtrxtab',
        'maxtrxhp',
        'maxtrxib',
        'maxtrxmb',
        'maxtrxtabday',
        'maxtrxhpday',
        'maxtrxibday',
        'maxtrxmbday',
        'stsrec',
        'inpuser',
        'inptgljam',
        'inpterm',
        'chguser',
        'chgtgl',
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
        'minnom' => 'decimal:2',
        'maxtrxtab' => 'decimal:2',
        'maxtrxhp' => 'decimal:2',
        'maxtrxib' => 'decimal:2',
        'maxtrxmb' => 'decimal:2',
        'maxtrxtabday' => 'decimal:2',
        'maxtrxhpday' => 'decimal:2',
        'maxtrxibday' => 'decimal:2',
        'maxtrxmbday' => 'decimal:2',
    ];
}
