<?php

declare(strict_types=1);

namespace App\Models\Mci\UserAuth;

use App\Models\Mci\MciBaseModel;

/**
 * Model MCI: USERPROFILE
 * --------------------------------------------------------------------------
 * Domain   : User / Auth
 * Tabel    : [dbo].[USERPROFILE]
 * Kolom    : 52
 * Sumber   : Mapping_MCI_MAR26_01042026.sql / MCI_MAR26_01042026.xlsx
 *
 * CATATAN:
 *  - Model ini READ-ONLY (lihat trait ReadOnlyModel di MciBaseModel).
 *  - Koneksi default `dashboard_data` (SQL Server), otomatis ke DB bulan terbaru.
 *  - Kolom dengan spasi/karakter khusus diakses via:
 *    $model->getAttribute('NAMA KOLOM')  atau  $model->{'NAMA KOLOM'}.
 *
 * @property string $userid type: varchar(10)
 * @property string $batch type: numeric(5)
 * @property string|null $nmuser type: varchar(30)
 * @property string $pass type: varchar(50)
 * @property string|null $levelx type: varchar(1)
 * @property string|null $tglexp type: varchar(8)
 * @property string|null $akses type: varchar(15)
 * @property string|null $stsaktiv type: varchar(1)
 * @property string|null $tglmulai type: varchar(14)
 * @property string|null $tglakhir type: varchar(14)
 * @property string|null $setupok type: varchar(1)
 * @property string|null $aplok type: varchar(1)
 * @property string|null $kdcab type: varchar(3)
 * @property string|null $kdloc type: varchar(2)
 * @property string|null $kdkas type: varchar(2)
 * @property string|null $limitldr type: numeric(9)
 * @property string|null $limitlcr type: numeric(9)
 * @property string|null $limitcdr type: numeric(9)
 * @property string|null $limitccr type: numeric(9)
 * @property string|null $dept type: varchar(3)
 * @property string|null $sbbkas type: varchar(7)
 * @property string|null $devterm type: varchar(10)
 * @property string|null $stsrec type: varchar(1)
 * @property string|null $inpuser type: varchar(10)
 * @property string|null $inptgl type: varchar(14)
 * @property string|null $inpterm type: varchar(10)
 * @property string|null $chguser type: varchar(10)
 * @property string|null $chgtgl type: varchar(14)
 * @property string|null $chgterm type: varchar(10)
 * @property string|null $autuser type: varchar(10)
 * @property string|null $auttgl type: varchar(14)
 * @property string|null $autterm type: varchar(10)
 * @property string|null $kdao type: varchar(8)
 * @property string|null $limitsetor type: numeric(9)
 * @property string|null $limittarik type: numeric(9)
 * @property string|null $limitbiaya type: numeric(9)
 * @property string|null $stsbatch type: varchar(1)
 * @property string|null $limitsetoral type: numeric(9)
 * @property string|null $limittarikal type: numeric(9)
 * @property string|null $nohp type: varchar(20)
 * @property string|null $stshp type: varchar(1)
 * @property string|null $norek type: varchar(11)
 * @property string|null $stsversion type: varchar(5)
 * @property string|null $passhp type: varchar(255)
 * @property string|null $imei type: varchar(200)
 * @property string|null $regimei type: varchar(255)
 * @property string|null $stssent type: char(1)
 * @property string|null $passweb type: varchar(255)
 * @property string|null $fasilitasid type: varchar(25)
 * @property string $email type: varchar(100)
 * @property string $tokenfcm type: varchar(300)
 * @property string $twofactorkey type: varchar(-1)
 */
class Userprofile extends MciBaseModel
{
    /**
     * Nama tabel (case-sensitive di SQL Server).
     */
    protected $table = 'USERPROFILE';

    /**
     * Daftar LENGKAP kolom sesuai database (52 kolom).
     * Model ini read-only, $fillable hanya untuk dokumentasi & IDE helper.
     *
     * @var array<int,string>
     */
    protected $fillable = [
        'userid',
        'batch',
        'nmuser',
        'pass',
        'levelx',
        'tglexp',
        'akses',
        'stsaktiv',
        'tglmulai',
        'tglakhir',
        'setupok',
        'aplok',
        'kdcab',
        'kdloc',
        'kdkas',
        'limitldr',
        'limitlcr',
        'limitcdr',
        'limitccr',
        'dept',
        'sbbkas',
        'devterm',
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
        'kdao',
        'limitsetor',
        'limittarik',
        'limitbiaya',
        'stsbatch',
        'limitsetoral',
        'limittarikal',
        'nohp',
        'stshp',
        'norek',
        'stsversion',
        'passhp',
        'imei',
        'regimei',
        'stssent',
        'passweb',
        'fasilitasid',
        'email',
        'tokenfcm',
        'twofactorkey',
    ];

    /**
     * Casting tipe kolom (non-string saja).
     *
     * @var array<string,string>
     */
    protected $casts = [
        'batch' => 'decimal:2',
        'limitldr' => 'decimal:2',
        'limitlcr' => 'decimal:2',
        'limitcdr' => 'decimal:2',
        'limitccr' => 'decimal:2',
        'limitsetor' => 'decimal:2',
        'limittarik' => 'decimal:2',
        'limitbiaya' => 'decimal:2',
        'limitsetoral' => 'decimal:2',
        'limittarikal' => 'decimal:2',
    ];
}
