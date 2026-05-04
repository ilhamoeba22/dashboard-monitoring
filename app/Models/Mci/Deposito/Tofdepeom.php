<?php

declare(strict_types=1);

namespace App\Models\Mci\Deposito;

use App\Models\Mci\MciBaseModel;

/**
 * Model MCI: TOFDEPEOM
 * --------------------------------------------------------------------------
 * Domain   : Deposito
 * Tabel    : [dbo].[TOFDEPEOM]
 * Kolom    : 36
 * Sumber   : Mapping_MCI_MAR26_01042026.sql / MCI_MAR26_01042026.xlsx
 *
 * CATATAN:
 *  - Model ini READ-ONLY (lihat trait ReadOnlyModel di MciBaseModel).
 *  - Koneksi default `dashboard_data` (SQL Server), otomatis ke DB bulan terbaru.
 *  - Kolom dengan spasi/karakter khusus diakses via:
 *    $model->getAttribute('NAMA KOLOM')  atau  $model->{'NAMA KOLOM'}.
 *
 * @property string $periode type: char(6)
 * @property string|null $nodep type: varchar(11)
 * @property string|null $nocif type: varchar(9)
 * @property string|null $nobilyet type: varchar(10)
 * @property string|null $nama type: varchar(30)
 * @property string|null $kdprd type: varchar(2)
 * @property string|null $kdloc type: varchar(2)
 * @property string|null $nomawal type: numeric(9)
 * @property string|null $nomrp type: numeric(9)
 * @property string|null $tglbuka type: varchar(8)
 * @property string|null $jkwaktu type: numeric(5)
 * @property string|null $jnsjkwaktu type: varchar(1)
 * @property string|null $tgleff type: varchar(8)
 * @property string|null $tgljtempo type: varchar(8)
 * @property string|null $nisbah type: numeric(5)
 * @property string|null $spread type: numeric(5)
 * @property string|null $terkait type: varchar(1)
 * @property string|null $noacbng type: varchar(11)
 * @property string|null $ststrn type: varchar(1)
 * @property string|null $bnghtg type: numeric(9)
 * @property string|null $bhhtgkum type: numeric(9)
 * @property string|null $bngacru1 type: numeric(9)
 * @property string|null $bngacru2 type: numeric(9)
 * @property string|null $bngbayar type: numeric(9)
 * @property string|null $tax type: numeric(9)
 * @property string|null $tglbayar type: varchar(8)
 * @property string|null $kodeaoh type: varchar(8)
 * @property string|null $kodeaop type: varchar(8)
 * @property string|null $sbbpok type: varchar(11)
 * @property string|null $nisbahrp type: numeric(9)
 * @property string|null $nisbahku type: numeric(9)
 * @property string|null $stszakat type: varchar(1)
 * @property string|null $zakat type: numeric(9)
 * @property string|null $komitrate type: numeric(5)
 * @property string|null $equivrate type: numeric(5)
 * @property string|null $kdgroupdana type: varchar(10)
 */
class Tofdepeom extends MciBaseModel
{
    /**
     * Nama tabel (case-sensitive di SQL Server).
     */
    protected $table = 'TOFDEPEOM';

    /**
     * Daftar LENGKAP kolom sesuai database (36 kolom).
     * Model ini read-only, $fillable hanya untuk dokumentasi & IDE helper.
     *
     * @var array<int,string>
     */
    protected $fillable = [
        'periode',
        'nodep',
        'nocif',
        'nobilyet',
        'nama',
        'kdprd',
        'kdloc',
        'nomawal',
        'nomrp',
        'tglbuka',
        'jkwaktu',
        'jnsjkwaktu',
        'tgleff',
        'tgljtempo',
        'nisbah',
        'spread',
        'terkait',
        'noacbng',
        'ststrn',
        'bnghtg',
        'bhhtgkum',
        'bngacru1',
        'bngacru2',
        'bngbayar',
        'tax',
        'tglbayar',
        'kodeaoh',
        'kodeaop',
        'sbbpok',
        'nisbahrp',
        'nisbahku',
        'stszakat',
        'zakat',
        'komitrate',
        'equivrate',
        'kdgroupdana',
    ];

    /**
     * Casting tipe kolom (non-string saja).
     *
     * @var array<string,string>
     */
    protected $casts = [
        'nomawal' => 'decimal:2',
        'nomrp' => 'decimal:2',
        'jkwaktu' => 'decimal:2',
        'nisbah' => 'decimal:2',
        'spread' => 'decimal:2',
        'bnghtg' => 'decimal:2',
        'bhhtgkum' => 'decimal:2',
        'bngacru1' => 'decimal:2',
        'bngacru2' => 'decimal:2',
        'bngbayar' => 'decimal:2',
        'tax' => 'decimal:2',
        'nisbahrp' => 'decimal:2',
        'nisbahku' => 'decimal:2',
        'zakat' => 'decimal:2',
        'komitrate' => 'decimal:2',
        'equivrate' => 'decimal:2',
    ];
}
