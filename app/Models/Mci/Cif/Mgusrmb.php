<?php

namespace App\Models\Mci\Cif;

use App\Models\Mci\MciBaseModel;

/**
 * Model MCI: MGUSRMB
 * --------------------------------------------------------------------------
 * Domain   : CIF / Customer
 * Tabel    : [dbo].[MGUSRMB]
 * Kolom    : 34
 * Sumber   : Mapping_MCI_MAR26_01042026.sql / MCI_MAR26_01042026.xlsx
 *
 * CATATAN:
 *  - Model ini READ-ONLY (lihat trait ReadOnlyModel di MciBaseModel).
 *  - Koneksi default `dashboard_data` (SQL Server), otomatis ke DB bulan terbaru.
 *  - Kolom dengan spasi/karakter khusus diakses via:
 *    $model->getAttribute('NAMA KOLOM')  atau  $model->{'NAMA KOLOM'}.
 *
 * @property string $UserID  type: varchar(50)
 * @property string|null $nm  type: varchar(50)
 * @property string $Pasw  type: varchar(255)
 * @property string|null $noid  type: varchar(30)
 * @property string|null $noreg  type: varchar(20)
 * @property string|null $noakt  type: varchar(20)
 * @property string|null $imei  type: varchar(50)
 * @property string|null $nocif  type: varchar(9)
 * @property string|null $PIN  type: varchar(100)
 * @property string|null $KdBank  type: varchar(6)
 * @property string|null $TermCon  type: varchar(1)
 * @property string|null $Noacc  type: varchar(11)
 * @property string|null $tglbuka  type: varchar(8)
 * @property string|null $nohp  type: varchar(16)
 * @property string|null $email  type: varchar(40)
 * @property string|null $tgllhr  type: char(8)
 * @property string|null $jk  type: char(1)
 * @property string|null $stssent  type: char(1)
 * @property string|null $stsrec  type: char(1)
 * @property string|null $inpuser  type: varchar(20)
 * @property string|null $inptgljam  type: varchar(14)
 * @property string|null $inpterm  type: varchar(20)
 * @property string|null $chguser  type: varchar(20)
 * @property string|null $chgtgljam  type: varchar(14)
 * @property string|null $chgterm  type: varchar(20)
 * @property string|null $autuser  type: varchar(20)
 * @property string|null $auttgljam  type: varchar(20)
 * @property string|null $autterm  type: varchar(20)
 * @property string|null $sessionId  type: varchar(50)
 * @property string|null $limit  type: numeric(9)
 * @property string|null $tokenfcm  type: varchar(300)
 * @property string|null $fingerId  type: varchar(150)
 * @property string $stsacc  type: varchar(10)
 * @property string $tglvip  type: varchar(8)
 */
class Mgusrmb extends MciBaseModel
{
    /**
     * Nama tabel (case-sensitive di SQL Server).
     */
    protected $table = 'MGUSRMB';

    /**
     * Daftar LENGKAP kolom sesuai database (34 kolom).
     * Model ini read-only, $fillable hanya untuk dokumentasi & IDE helper.
     *
     * @var array<int,string>
     */
    protected $fillable = [
        'UserID',
        'nm',
        'Pasw',
        'noid',
        'noreg',
        'noakt',
        'imei',
        'nocif',
        'PIN',
        'KdBank',
        'TermCon',
        'Noacc',
        'tglbuka',
        'nohp',
        'email',
        'tgllhr',
        'jk',
        'stssent',
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
        'sessionId',
        'limit',
        'tokenfcm',
        'fingerId',
        'stsacc',
        'tglvip',
    ];

    /**
     * Casting tipe kolom (non-string saja).
     *
     * @var array<string,string>
     */
    protected $casts = [
        'limit' => 'decimal:2',
    ];
}
