<?php

namespace App\Models\Mci\Sms;

use App\Models\Mci\MciBaseModel;

/**
 * Model MCI: TOFTSMS
 * --------------------------------------------------------------------------
 * Domain   : SMS / Notif
 * Tabel    : [dbo].[TOFTSMS]
 * Kolom    : 25
 * Sumber   : Mapping_MCI_MAR26_01042026.sql / MCI_MAR26_01042026.xlsx
 *
 * CATATAN:
 *  - Model ini READ-ONLY (lihat trait ReadOnlyModel di MciBaseModel).
 *  - Koneksi default `dashboard_data` (SQL Server), otomatis ke DB bulan terbaru.
 *  - Kolom dengan spasi/karakter khusus diakses via:
 *    $model->getAttribute('NAMA KOLOM')  atau  $model->{'NAMA KOLOM'}.
 *
 * @property string $kdsms  type: varchar(2)
 * @property string $groupsms  type: varchar(2)
 * @property string|null $periode  type: numeric(9)
 * @property string|null $isipesan  type: varchar(500)
 * @property string|null $stsrec  type: varchar(1)
 * @property string|null $inpuser  type: varchar(10)
 * @property string|null $inptgljam  type: varchar(14)
 * @property string|null $inpterm  type: varchar(10)
 * @property string|null $chguser  type: varchar(10)
 * @property string|null $chgtgljam  type: varchar(14)
 * @property string|null $chgterm  type: varchar(10)
 * @property string|null $autuser  type: varchar(10)
 * @property string|null $auttgljam  type: varchar(14)
 * @property string|null $autterm  type: varchar(10)
 * @property string|null $stssms1  type: char(1)
 * @property string|null $stssms2  type: char(1)
 * @property string|null $stssms3  type: char(1)
 * @property string|null $stssms4  type: char(1)
 * @property string|null $stssms5  type: char(1)
 * @property string|null $bysms1  type: numeric(5)
 * @property string|null $bysms2  type: numeric(5)
 * @property string|null $bysms3  type: numeric(5)
 * @property string|null $sbbpendsms1  type: char(7)
 * @property string|null $sbbpendsms2  type: char(7)
 * @property string|null $sbbpendsms3  type: char(7)
 */
class Toftsms extends MciBaseModel
{
    /**
     * Nama tabel (case-sensitive di SQL Server).
     */
    protected $table = 'TOFTSMS';

    /**
     * Daftar LENGKAP kolom sesuai database (25 kolom).
     * Model ini read-only, $fillable hanya untuk dokumentasi & IDE helper.
     *
     * @var array<int,string>
     */
    protected $fillable = [
        'kdsms',
        'groupsms',
        'periode',
        'isipesan',
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
        'stssms1',
        'stssms2',
        'stssms3',
        'stssms4',
        'stssms5',
        'bysms1',
        'bysms2',
        'bysms3',
        'sbbpendsms1',
        'sbbpendsms2',
        'sbbpendsms3',
    ];

    /**
     * Casting tipe kolom (non-string saja).
     *
     * @var array<string,string>
     */
    protected $casts = [
        'periode' => 'decimal:2',
        'bysms1' => 'decimal:2',
        'bysms2' => 'decimal:2',
        'bysms3' => 'decimal:2',
    ];
}
