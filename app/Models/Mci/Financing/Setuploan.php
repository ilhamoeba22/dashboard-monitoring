<?php

declare(strict_types=1);

namespace App\Models\Mci\Financing;

use App\Models\Mci\MciBaseModel;

/**
 * Model MCI: SETUPLOAN
 * --------------------------------------------------------------------------
 * Domain   : Financing / Loan
 * Tabel    : [dbo].[SETUPLOAN]
 * Kolom    : 125
 * Sumber   : Mapping_MCI_MAR26_01042026.sql / MCI_MAR26_01042026.xlsx
 *
 * CATATAN:
 *  - Model ini READ-ONLY (lihat trait ReadOnlyModel di MciBaseModel).
 *  - Koneksi default `dashboard_data` (SQL Server), otomatis ke DB bulan terbaru.
 *  - Kolom dengan spasi/karakter khusus diakses via:
 *    $model->getAttribute('NAMA KOLOM')  atau  $model->{'NAMA KOLOM'}.
 *
 * @property string $kdprd type: varchar(2)
 * @property string|null $singkat type: varchar(15)
 * @property string|null $ket type: varchar(30)
 * @property string|null $cc type: varchar(2)
 * @property string|null $jnspby type: varchar(1)
 * @property string|null $pokpby type: varchar(2)
 * @property string|null $kdcol type: varchar(2)
 * @property string|null $kdsalur type: varchar(1)
 * @property string|null $stsrisk type: varchar(1)
 * @property string|null $stsbank type: varchar(1)
 * @property string|null $program type: varchar(1)
 * @property string|null $htgbng type: varchar(1)
 * @property string|null $polabyr type: varchar(1)
 * @property string|null $bentuktag type: varchar(11)
 * @property string|null $setbyr type: varchar(1)
 * @property string|null $kdadm type: varchar(1)
 * @property string|null $byadm type: numeric(9)
 * @property string|null $maxbyadm type: numeric(9)
 * @property string|null $kdpp type: varchar(1)
 * @property string|null $maxblnpp type: numeric(5)
 * @property string|null $dpd type: numeric(5)
 * @property string|null $btb type: varchar(1)
 * @property string|null $spread type: numeric(5)
 * @property string|null $kddnd type: varchar(1)
 * @property string|null $hrhtgdnd type: numeric(5)
 * @property string|null $dndpsen type: numeric(5)
 * @property string|null $dndrp type: numeric(5)
 * @property string|null $htgdnd type: varchar(1)
 * @property string|null $baghas type: varchar(1)
 * @property string|null $nosbb01 type: varchar(7)
 * @property string|null $nosbb02 type: varchar(7)
 * @property string|null $nosbb03 type: varchar(7)
 * @property string|null $nosbb04 type: varchar(7)
 * @property string|null $nosbb05 type: varchar(7)
 * @property string|null $nosbb06 type: varchar(7)
 * @property string|null $nosbb07 type: varchar(7)
 * @property string|null $nosbb08 type: varchar(7)
 * @property string|null $nosbb09 type: varchar(7)
 * @property string|null $nosbb10 type: varchar(7)
 * @property string|null $nosbb11 type: varchar(7)
 * @property string|null $nosbb12 type: varchar(7)
 * @property string|null $nosbb13 type: varchar(7)
 * @property string|null $nosbb14 type: varchar(7)
 * @property string|null $nosbb15 type: varchar(7)
 * @property string|null $nosbb16 type: varchar(7)
 * @property string|null $nosbb17 type: varchar(7)
 * @property string|null $nosbb18 type: varchar(7)
 * @property string|null $nosbb19 type: varchar(7)
 * @property string|null $nosbb20 type: varchar(7)
 * @property string|null $nosbb21 type: varchar(7)
 * @property string|null $nosbb22 type: varchar(7)
 * @property string|null $nosbb23 type: varchar(7)
 * @property string|null $nosbb24 type: varchar(7)
 * @property string|null $nosbb25 type: varchar(7)
 * @property string|null $sbbsetor type: varchar(7)
 * @property string|null $stspenalty type: varchar(1)
 * @property string|null $htgpenalty type: varchar(1)
 * @property string|null $mintbl1 type: numeric(5)
 * @property string|null $mintblpr1 type: numeric(5)
 * @property string|null $mintbl2 type: numeric(5)
 * @property string|null $mintblpr2 type: numeric(5)
 * @property string|null $nompby1 type: numeric(9)
 * @property string|null $nompby2 type: numeric(9)
 * @property string|null $nompby3 type: numeric(9)
 * @property string|null $nompby4 type: numeric(9)
 * @property string|null $nompby5 type: numeric(9)
 * @property string|null $byadm1 type: numeric(9)
 * @property string|null $byadm2 type: numeric(9)
 * @property string|null $byadm3 type: numeric(9)
 * @property string|null $byadm4 type: numeric(9)
 * @property string|null $byadm5 type: numeric(9)
 * @property string|null $byprov1 type: numeric(9)
 * @property string|null $byprov2 type: numeric(9)
 * @property string|null $byprov3 type: numeric(9)
 * @property string|null $byprov4 type: numeric(9)
 * @property string|null $byprov5 type: numeric(9)
 * @property string|null $thn1 type: numeric(5)
 * @property string|null $thn2 type: numeric(5)
 * @property string|null $thn3 type: numeric(5)
 * @property string|null $thn4 type: numeric(5)
 * @property string|null $thn5 type: numeric(5)
 * @property string|null $stsdisc type: varchar(1)
 * @property string|null $sisabln1 type: numeric(5)
 * @property string|null $sisabln2 type: numeric(5)
 * @property string|null $sisabln3 type: numeric(5)
 * @property string|null $sisabln4 type: numeric(5)
 * @property string|null $sisabln5 type: numeric(5)
 * @property string|null $discpros1 type: numeric(5)
 * @property string|null $discpros2 type: numeric(5)
 * @property string|null $discpros3 type: numeric(5)
 * @property string|null $discpros4 type: numeric(5)
 * @property string|null $discpros5 type: numeric(5)
 * @property string|null $discbln1 type: numeric(5)
 * @property string|null $discbln2 type: numeric(5)
 * @property string|null $discbln3 type: numeric(5)
 * @property string|null $discbln4 type: numeric(5)
 * @property string|null $discbln5 type: numeric(5)
 * @property string|null $mtdlunas type: varchar(1)
 * @property string|null $stsjamin type: varchar(1)
 * @property string|null $media type: varchar(1)
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
 * @property string|null $kdcolint type: varchar(2)
 * @property string|null $maxrateeff type: numeric(5)
 * @property string|null $maxcashback type: numeric(5)
 * @property string|null $sbbcashback type: varchar(7)
 * @property string|null $noaccdana type: varchar(11)
 * @property string|null $stsppapcol1 type: varchar(1)
 * @property string|null $stsppapcol2 type: varchar(1)
 * @property string|null $stsppapcol3 type: varchar(1)
 * @property string|null $stsppapcol4 type: varchar(1)
 * @property string|null $stsangs type: char(1)
 * @property string|null $nosbb26 type: varchar(7)
 * @property string|null $nosbb27 type: varchar(7)
 * @property string|null $sumberdana type: varchar(1)
 * @property string|null $stsmmq type: varchar(1)
 * @property string|null $nosbb28 type: varchar(7)
 */
class Setuploan extends MciBaseModel
{
    /**
     * Nama tabel (case-sensitive di SQL Server).
     */
    protected $table = 'SETUPLOAN';

    /**
     * Daftar LENGKAP kolom sesuai database (125 kolom).
     * Model ini read-only, $fillable hanya untuk dokumentasi & IDE helper.
     *
     * @var array<int,string>
     */
    protected $fillable = [
        'kdprd',
        'singkat',
        'ket',
        'cc',
        'jnspby',
        'pokpby',
        'kdcol',
        'kdsalur',
        'stsrisk',
        'stsbank',
        'program',
        'htgbng',
        'polabyr',
        'bentuktag',
        'setbyr',
        'kdadm',
        'byadm',
        'maxbyadm',
        'kdpp',
        'maxblnpp',
        'dpd',
        'btb',
        'spread',
        'kddnd',
        'hrhtgdnd',
        'dndpsen',
        'dndrp',
        'htgdnd',
        'baghas',
        'nosbb01',
        'nosbb02',
        'nosbb03',
        'nosbb04',
        'nosbb05',
        'nosbb06',
        'nosbb07',
        'nosbb08',
        'nosbb09',
        'nosbb10',
        'nosbb11',
        'nosbb12',
        'nosbb13',
        'nosbb14',
        'nosbb15',
        'nosbb16',
        'nosbb17',
        'nosbb18',
        'nosbb19',
        'nosbb20',
        'nosbb21',
        'nosbb22',
        'nosbb23',
        'nosbb24',
        'nosbb25',
        'sbbsetor',
        'stspenalty',
        'htgpenalty',
        'mintbl1',
        'mintblpr1',
        'mintbl2',
        'mintblpr2',
        'nompby1',
        'nompby2',
        'nompby3',
        'nompby4',
        'nompby5',
        'byadm1',
        'byadm2',
        'byadm3',
        'byadm4',
        'byadm5',
        'byprov1',
        'byprov2',
        'byprov3',
        'byprov4',
        'byprov5',
        'thn1',
        'thn2',
        'thn3',
        'thn4',
        'thn5',
        'stsdisc',
        'sisabln1',
        'sisabln2',
        'sisabln3',
        'sisabln4',
        'sisabln5',
        'discpros1',
        'discpros2',
        'discpros3',
        'discpros4',
        'discpros5',
        'discbln1',
        'discbln2',
        'discbln3',
        'discbln4',
        'discbln5',
        'mtdlunas',
        'stsjamin',
        'media',
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
        'kdcolint',
        'maxrateeff',
        'maxcashback',
        'sbbcashback',
        'noaccdana',
        'stsppapcol1',
        'stsppapcol2',
        'stsppapcol3',
        'stsppapcol4',
        'stsangs',
        'nosbb26',
        'nosbb27',
        'sumberdana',
        'stsmmq',
        'nosbb28',
    ];

    /**
     * Casting tipe kolom (non-string saja).
     *
     * @var array<string,string>
     */
    protected $casts = [
        'byadm' => 'decimal:2',
        'maxbyadm' => 'decimal:2',
        'maxblnpp' => 'decimal:2',
        'dpd' => 'decimal:2',
        'spread' => 'decimal:2',
        'hrhtgdnd' => 'decimal:2',
        'dndpsen' => 'decimal:2',
        'dndrp' => 'decimal:2',
        'mintbl1' => 'decimal:2',
        'mintblpr1' => 'decimal:2',
        'mintbl2' => 'decimal:2',
        'mintblpr2' => 'decimal:2',
        'nompby1' => 'decimal:2',
        'nompby2' => 'decimal:2',
        'nompby3' => 'decimal:2',
        'nompby4' => 'decimal:2',
        'nompby5' => 'decimal:2',
        'byadm1' => 'decimal:2',
        'byadm2' => 'decimal:2',
        'byadm3' => 'decimal:2',
        'byadm4' => 'decimal:2',
        'byadm5' => 'decimal:2',
        'byprov1' => 'decimal:2',
        'byprov2' => 'decimal:2',
        'byprov3' => 'decimal:2',
        'byprov4' => 'decimal:2',
        'byprov5' => 'decimal:2',
        'thn1' => 'decimal:2',
        'thn2' => 'decimal:2',
        'thn3' => 'decimal:2',
        'thn4' => 'decimal:2',
        'thn5' => 'decimal:2',
        'sisabln1' => 'decimal:2',
        'sisabln2' => 'decimal:2',
        'sisabln3' => 'decimal:2',
        'sisabln4' => 'decimal:2',
        'sisabln5' => 'decimal:2',
        'discpros1' => 'decimal:2',
        'discpros2' => 'decimal:2',
        'discpros3' => 'decimal:2',
        'discpros4' => 'decimal:2',
        'discpros5' => 'decimal:2',
        'discbln1' => 'decimal:2',
        'discbln2' => 'decimal:2',
        'discbln3' => 'decimal:2',
        'discbln4' => 'decimal:2',
        'discbln5' => 'decimal:2',
        'maxrateeff' => 'decimal:2',
        'maxcashback' => 'decimal:2',
    ];
}
