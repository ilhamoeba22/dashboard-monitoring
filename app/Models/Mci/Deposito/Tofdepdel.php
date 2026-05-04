<?php

declare(strict_types=1);

namespace App\Models\Mci\Deposito;

use App\Models\Mci\MciBaseModel;

/**
 * Model MCI: TOFDEPDEL
 * --------------------------------------------------------------------------
 * Domain   : Deposito
 * Tabel    : [dbo].[TOFDEPDEL]
 * Kolom    : 81
 * Sumber   : Mapping_MCI_MAR26_01042026.sql / MCI_MAR26_01042026.xlsx
 *
 * CATATAN:
 *  - Model ini READ-ONLY (lihat trait ReadOnlyModel di MciBaseModel).
 *  - Koneksi default `dashboard_data` (SQL Server), otomatis ke DB bulan terbaru.
 *  - Kolom dengan spasi/karakter khusus diakses via:
 *    $model->getAttribute('NAMA KOLOM')  atau  $model->{'NAMA KOLOM'}.
 *
 * @property string|null $nodep type: varchar(11)
 * @property string|null $nocif type: varchar(9)
 * @property string|null $nobilyet type: varchar(10)
 * @property string|null $nama type: varchar(30)
 * @property string|null $kdprd type: varchar(2)
 * @property string|null $kdcab type: varchar(3)
 * @property string|null $kdloc type: varchar(2)
 * @property string|null $kdkas type: varchar(2)
 * @property string|null $cc type: varchar(2)
 * @property string|null $kurs type: numeric(9)
 * @property string|null $nomawal type: numeric(9)
 * @property string|null $nomrp type: numeric(9)
 * @property string|null $nomva type: numeric(9)
 * @property string|null $tglbuka type: varchar(8)
 * @property string|null $jkwaktu type: numeric(5)
 * @property string|null $jnsjkwaktu type: varchar(1)
 * @property string|null $tgleff type: varchar(8)
 * @property string|null $tgljtempo type: varchar(8)
 * @property string|null $aro type: varchar(1)
 * @property string|null $tambahnom type: varchar(1)
 * @property string|null $nisbahold type: numeric(5)
 * @property string|null $nisbah type: numeric(5)
 * @property string|null $spread type: numeric(5)
 * @property string|null $stsrate type: varchar(1)
 * @property string|null $stsacc type: varchar(1)
 * @property string|null $terkait type: varchar(1)
 * @property string|null $doc type: varchar(1)
 * @property string|null $noacbng type: varchar(11)
 * @property string|null $jnsacbng type: varchar(1)
 * @property string|null $noacpok type: varchar(11)
 * @property string|null $jnsacpok type: varchar(1)
 * @property string|null $noacpokc type: varchar(11)
 * @property string|null $jnsacpokc type: varchar(1)
 * @property string|null $ststrn type: varchar(1)
 * @property string|null $cetakke type: numeric(5)
 * @property string|null $mtdbng type: varchar(1)
 * @property string|null $bnghtg type: numeric(9)
 * @property string|null $bngacru1 type: numeric(9)
 * @property string|null $bngacru2 type: numeric(9)
 * @property string|null $bngbayar type: numeric(9)
 * @property string|null $tax type: numeric(9)
 * @property string|null $tglhtg type: varchar(8)
 * @property string|null $tglhtgnext type: varchar(8)
 * @property string|null $tglacru type: varchar(8)
 * @property string|null $tglbayar type: varchar(8)
 * @property string|null $kodeaoh type: varchar(8)
 * @property string|null $kodeaop type: varchar(8)
 * @property string|null $glb type: varchar(2)
 * @property string|null $segmen type: varchar(3)
 * @property string|null $sbbpok type: varchar(11)
 * @property string|null $subsiditax type: numeric(5)
 * @property string|null $penalty type: varchar(1)
 * @property string|null $special type: varchar(1)
 * @property string|null $tglbng type: varchar(2)
 * @property string|null $kdwil type: varchar(2)
 * @property string|null $ststrnbng type: varchar(1)
 * @property string|null $ststrntax type: varchar(1)
 * @property string|null $saldrata1 type: numeric(9)
 * @property string|null $saldrata2 type: numeric(9)
 * @property string|null $tglcair type: varchar(8)
 * @property string|null $tgltutup type: varchar(8)
 * @property string|null $tgljtblok type: varchar(8)
 * @property string|null $hari1 type: numeric(5)
 * @property string|null $hari2 type: numeric(5)
 * @property string|null $nisbahrp type: numeric(9)
 * @property string|null $nisbahku type: numeric(9)
 * @property string|null $stszakat type: varchar(1)
 * @property string|null $zakat type: numeric(9)
 * @property string|null $infaq type: numeric(9)
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
 * @property string|null $equivrate type: numeric(5)
 * @property string|null $rateawal type: numeric(5)
 */
class Tofdepdel extends MciBaseModel
{
    /**
     * Nama tabel (case-sensitive di SQL Server).
     */
    protected $table = 'TOFDEPDEL';

    /**
     * Daftar LENGKAP kolom sesuai database (81 kolom).
     * Model ini read-only, $fillable hanya untuk dokumentasi & IDE helper.
     *
     * @var array<int,string>
     */
    protected $fillable = [
        'nodep',
        'nocif',
        'nobilyet',
        'nama',
        'kdprd',
        'kdcab',
        'kdloc',
        'kdkas',
        'cc',
        'kurs',
        'nomawal',
        'nomrp',
        'nomva',
        'tglbuka',
        'jkwaktu',
        'jnsjkwaktu',
        'tgleff',
        'tgljtempo',
        'aro',
        'tambahnom',
        'nisbahold',
        'nisbah',
        'spread',
        'stsrate',
        'stsacc',
        'terkait',
        'doc',
        'noacbng',
        'jnsacbng',
        'noacpok',
        'jnsacpok',
        'noacpokc',
        'jnsacpokc',
        'ststrn',
        'cetakke',
        'mtdbng',
        'bnghtg',
        'bngacru1',
        'bngacru2',
        'bngbayar',
        'tax',
        'tglhtg',
        'tglhtgnext',
        'tglacru',
        'tglbayar',
        'kodeaoh',
        'kodeaop',
        'glb',
        'segmen',
        'sbbpok',
        'subsiditax',
        'penalty',
        'special',
        'tglbng',
        'kdwil',
        'ststrnbng',
        'ststrntax',
        'saldrata1',
        'saldrata2',
        'tglcair',
        'tgltutup',
        'tgljtblok',
        'hari1',
        'hari2',
        'nisbahrp',
        'nisbahku',
        'stszakat',
        'zakat',
        'infaq',
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
        'equivrate',
        'rateawal',
    ];

    /**
     * Casting tipe kolom (non-string saja).
     *
     * @var array<string,string>
     */
    protected $casts = [
        'kurs' => 'decimal:2',
        'nomawal' => 'decimal:2',
        'nomrp' => 'decimal:2',
        'nomva' => 'decimal:2',
        'jkwaktu' => 'decimal:2',
        'nisbahold' => 'decimal:2',
        'nisbah' => 'decimal:2',
        'spread' => 'decimal:2',
        'cetakke' => 'decimal:2',
        'bnghtg' => 'decimal:2',
        'bngacru1' => 'decimal:2',
        'bngacru2' => 'decimal:2',
        'bngbayar' => 'decimal:2',
        'tax' => 'decimal:2',
        'subsiditax' => 'decimal:2',
        'saldrata1' => 'decimal:2',
        'saldrata2' => 'decimal:2',
        'hari1' => 'decimal:2',
        'hari2' => 'decimal:2',
        'nisbahrp' => 'decimal:2',
        'nisbahku' => 'decimal:2',
        'zakat' => 'decimal:2',
        'infaq' => 'decimal:2',
        'equivrate' => 'decimal:2',
        'rateawal' => 'decimal:2',
    ];
}
