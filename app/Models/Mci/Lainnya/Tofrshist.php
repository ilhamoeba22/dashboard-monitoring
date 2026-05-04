<?php

declare(strict_types=1);

namespace App\Models\Mci\Lainnya;

use App\Models\Mci\MciBaseModel;

/**
 * Model MCI: TOFRSHIST
 * --------------------------------------------------------------------------
 * Domain   : Lainnya
 * Tabel    : [dbo].[TOFRSHIST]
 * Kolom    : 47
 * Sumber   : Mapping_MCI_MAR26_01042026.sql / MCI_MAR26_01042026.xlsx
 *
 * CATATAN:
 *  - Model ini READ-ONLY (lihat trait ReadOnlyModel di MciBaseModel).
 *  - Koneksi default `dashboard_data` (SQL Server), otomatis ke DB bulan terbaru.
 *  - Kolom dengan spasi/karakter khusus diakses via:
 *    $model->getAttribute('NAMA KOLOM')  atau  $model->{'NAMA KOLOM'}.
 *
 * @property string $urut type: numeric(9)
 * @property string|null $tglhist type: varchar(8)
 * @property string|null $nokontrak type: varchar(11)
 * @property string|null $thn type: varchar(4)
 * @property string|null $bln type: varchar(2)
 * @property string|null $tgl type: varchar(2)
 * @property string|null $tgltagih type: varchar(8)
 * @property string|null $tgljt type: varchar(8)
 * @property string|null $os type: numeric(9)
 * @property string|null $tagmdl type: numeric(9)
 * @property string|null $tagmgn type: numeric(9)
 * @property string|null $tagdnd type: numeric(9)
 * @property string|null $byrmdl type: numeric(9)
 * @property string|null $byrmgn type: numeric(9)
 * @property string|null $byrdnd type: numeric(9)
 * @property string|null $stsbyr type: varchar(1)
 * @property string|null $tglbyrmdl type: varchar(8)
 * @property string|null $tglbyrmgn type: varchar(8)
 * @property string|null $tglbyrdnd type: varchar(8)
 * @property string|null $prosbyr type: numeric(5)
 * @property string|null $coll type: numeric(5)
 * @property string|null $stsrec type: varchar(1)
 * @property string|null $inpuser type: varchar(10)
 * @property string|null $inptgljam type: varchar(14)
 * @property string|null $inpterm type: varchar(10)
 * @property string|null $chguser type: varchar(10)
 * @property string|null $chgtgljam type: varchar(14)
 * @property string|null $chgterm type: varchar(10)
 * @property string|null $autuser type: varchar(10)
 * @property string|null $auttgljam type: varchar(14)
 * @property string|null $autterm type: varchar(10)
 * @property string|null $stsacru type: varchar(1)
 * @property string|null $tglacru type: varchar(8)
 * @property string|null $tgldnd type: varchar(8)
 * @property string|null $stsmdl type: numeric(5)
 * @property string|null $stsmgn type: numeric(5)
 * @property string|null $stsdnd type: numeric(5)
 * @property string|null $stsposadm type: varchar(1)
 * @property string|null $tglposadm type: varchar(8)
 * @property string|null $hari type: numeric(5)
 * @property string|null $nisbahbank type: numeric(5)
 * @property string|null $pp_cust type: numeric(9)
 * @property string|null $ratiobh type: numeric(5)
 * @property string|null $porsi_bank type: numeric(9)
 * @property string|null $porsi_cust type: numeric(9)
 * @property string|null $porsi_bank_p type: numeric(5)
 * @property string|null $porsi_cust_p type: numeric(5)
 */
class Tofrshist extends MciBaseModel
{
    /**
     * Nama tabel (case-sensitive di SQL Server).
     */
    protected $table = 'TOFRSHIST';

    /**
     * Daftar LENGKAP kolom sesuai database (47 kolom).
     * Model ini read-only, $fillable hanya untuk dokumentasi & IDE helper.
     *
     * @var array<int,string>
     */
    protected $fillable = [
        'urut',
        'tglhist',
        'nokontrak',
        'thn',
        'bln',
        'tgl',
        'tgltagih',
        'tgljt',
        'os',
        'tagmdl',
        'tagmgn',
        'tagdnd',
        'byrmdl',
        'byrmgn',
        'byrdnd',
        'stsbyr',
        'tglbyrmdl',
        'tglbyrmgn',
        'tglbyrdnd',
        'prosbyr',
        'coll',
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
        'stsacru',
        'tglacru',
        'tgldnd',
        'stsmdl',
        'stsmgn',
        'stsdnd',
        'stsposadm',
        'tglposadm',
        'hari',
        'nisbahbank',
        'pp_cust',
        'ratiobh',
        'porsi_bank',
        'porsi_cust',
        'porsi_bank_p',
        'porsi_cust_p',
    ];

    /**
     * Casting tipe kolom (non-string saja).
     *
     * @var array<string,string>
     */
    protected $casts = [
        'urut' => 'decimal:2',
        'os' => 'decimal:2',
        'tagmdl' => 'decimal:2',
        'tagmgn' => 'decimal:2',
        'tagdnd' => 'decimal:2',
        'byrmdl' => 'decimal:2',
        'byrmgn' => 'decimal:2',
        'byrdnd' => 'decimal:2',
        'prosbyr' => 'decimal:2',
        'coll' => 'decimal:2',
        'stsmdl' => 'decimal:2',
        'stsmgn' => 'decimal:2',
        'stsdnd' => 'decimal:2',
        'hari' => 'decimal:2',
        'nisbahbank' => 'decimal:2',
        'pp_cust' => 'decimal:2',
        'ratiobh' => 'decimal:2',
        'porsi_bank' => 'decimal:2',
        'porsi_cust' => 'decimal:2',
        'porsi_bank_p' => 'decimal:2',
        'porsi_cust_p' => 'decimal:2',
    ];
}
