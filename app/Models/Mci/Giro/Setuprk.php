<?php

declare(strict_types=1);

namespace App\Models\Mci\Giro;

use App\Models\Mci\MciBaseModel;

/**
 * Model MCI: SETUPRK
 * --------------------------------------------------------------------------
 * Domain   : Giro / RK
 * Tabel    : [dbo].[SETUPRK]
 * Kolom    : 75
 * Sumber   : Mapping_MCI_MAR26_01042026.sql / MCI_MAR26_01042026.xlsx
 *
 * CATATAN:
 *  - Model ini READ-ONLY (lihat trait ReadOnlyModel di MciBaseModel).
 *  - Koneksi default `dashboard_data` (SQL Server), otomatis ke DB bulan terbaru.
 *  - Kolom dengan spasi/karakter khusus diakses via:
 *    $model->getAttribute('NAMA KOLOM')  atau  $model->{'NAMA KOLOM'}.
 *
 * @property string $kdprd type: varchar(2)
 * @property string|null $cc type: varchar(3)
 * @property string|null $singkat type: varchar(10)
 * @property string|null $ket type: varchar(30)
 * @property string $htgbng type: varchar(1)
 * @property string $byrbng type: varchar(1)
 * @property string $faktor type: varchar(1)
 * @property string|null $ntkp type: numeric(9)
 * @property string|null $tax type: numeric(5)
 * @property string $postkerek type: varchar(1)
 * @property string|null $progresif type: varchar(1)
 * @property string|null $saldmin1 type: numeric(9)
 * @property string|null $saldmax1 type: numeric(9)
 * @property string|null $saldmin2 type: numeric(9)
 * @property string|null $saldmax2 type: numeric(9)
 * @property string|null $saldmin3 type: numeric(9)
 * @property string|null $saldmax3 type: numeric(9)
 * @property string|null $saldmin4 type: numeric(9)
 * @property string|null $saldmax4 type: numeric(9)
 * @property string|null $saldmin5 type: numeric(9)
 * @property string|null $saldmax5 type: numeric(9)
 * @property string|null $rate1 type: numeric(5)
 * @property string|null $rate2 type: numeric(5)
 * @property string|null $rate3 type: numeric(5)
 * @property string|null $rate4 type: numeric(5)
 * @property string|null $rate5 type: numeric(5)
 * @property string|null $sbbgiro type: varchar(7)
 * @property string|null $sbbbng type: varchar(7)
 * @property string|null $sbbadm type: varchar(7)
 * @property string|null $sbbtax type: varchar(7)
 * @property string|null $sbbtutup type: varchar(7)
 * @property string|null $sbbsalmin type: varchar(7)
 * @property string|null $sbbpasif type: varchar(7)
 * @property string|null $sbbodgiro type: varchar(7)
 * @property string|null $sbbodprk type: varchar(7)
 * @property string|null $sbbpendod type: varchar(7)
 * @property string|null $byadm type: numeric(5)
 * @property string|null $bytutup type: numeric(5)
 * @property string|null $bykoran type: numeric(5)
 * @property string|null $bysalmin type: numeric(5)
 * @property string|null $bypasif type: numeric(5)
 * @property string|null $saldpasf type: numeric(9)
 * @property string|null $minsetor type: numeric(5)
 * @property string|null $minsaldo type: numeric(5)
 * @property string|null $rate type: numeric(5)
 * @property string|null $berlaku type: varchar(8)
 * @property string|null $ratelama type: numeric(5)
 * @property string|null $lamapasif type: numeric(5)
 * @property string|null $otorisasi type: varchar(1)
 * @property string|null $tgladm type: varchar(2)
 * @property string|null $ddawal type: varchar(2)
 * @property string|null $ddakhir type: varchar(2)
 * @property string|null $tglawal type: varchar(8)
 * @property string|null $tglakhir type: varchar(8)
 * @property string|null $tglbuku type: varchar(8)
 * @property string|null $bagihasil type: varchar(1)
 * @property string|null $bbtdana type: numeric(5)
 * @property string|null $sbbzakat type: varchar(7)
 * @property string|null $zakat type: numeric(5)
 * @property string|null $sbbinfaq type: varchar(7)
 * @property string|null $syaratrk type: varchar(1)
 * @property string|null $minsaldrk type: numeric(9)
 * @property string|null $basesaldo type: varchar(1)
 * @property string|null $mintrn type: numeric(5)
 * @property string|null $stsrec type: varchar(1)
 * @property string|null $stsrate type: varchar(1)
 * @property string|null $inpuser type: varchar(10)
 * @property string|null $inptgl type: varchar(14)
 * @property string|null $inpterm type: varchar(10)
 * @property string|null $chguser type: varchar(10)
 * @property string|null $chgtgl type: varchar(14)
 * @property string|null $chgterm type: varchar(10)
 * @property string|null $autuser type: varchar(10)
 * @property string|null $auttgl type: varchar(14)
 * @property string|null $autterm type: varchar(10)
 */
class Setuprk extends MciBaseModel
{
    /**
     * Nama tabel (case-sensitive di SQL Server).
     */
    protected $table = 'SETUPRK';

    /**
     * Daftar LENGKAP kolom sesuai database (75 kolom).
     * Model ini read-only, $fillable hanya untuk dokumentasi & IDE helper.
     *
     * @var array<int,string>
     */
    protected $fillable = [
        'kdprd',
        'cc',
        'singkat',
        'ket',
        'htgbng',
        'byrbng',
        'faktor',
        'ntkp',
        'tax',
        'postkerek',
        'progresif',
        'saldmin1',
        'saldmax1',
        'saldmin2',
        'saldmax2',
        'saldmin3',
        'saldmax3',
        'saldmin4',
        'saldmax4',
        'saldmin5',
        'saldmax5',
        'rate1',
        'rate2',
        'rate3',
        'rate4',
        'rate5',
        'sbbgiro',
        'sbbbng',
        'sbbadm',
        'sbbtax',
        'sbbtutup',
        'sbbsalmin',
        'sbbpasif',
        'sbbodgiro',
        'sbbodprk',
        'sbbpendod',
        'byadm',
        'bytutup',
        'bykoran',
        'bysalmin',
        'bypasif',
        'saldpasf',
        'minsetor',
        'minsaldo',
        'rate',
        'berlaku',
        'ratelama',
        'lamapasif',
        'otorisasi',
        'tgladm',
        'ddawal',
        'ddakhir',
        'tglawal',
        'tglakhir',
        'tglbuku',
        'bagihasil',
        'bbtdana',
        'sbbzakat',
        'zakat',
        'sbbinfaq',
        'syaratrk',
        'minsaldrk',
        'basesaldo',
        'mintrn',
        'stsrec',
        'stsrate',
        'inpuser',
        'inptgl',
        'inpterm',
        'chguser',
        'chgtgl',
        'chgterm',
        'autuser',
        'auttgl',
        'autterm',
    ];

    /**
     * Casting tipe kolom (non-string saja).
     *
     * @var array<string,string>
     */
    protected $casts = [
        'ntkp' => 'decimal:2',
        'tax' => 'decimal:2',
        'saldmin1' => 'decimal:2',
        'saldmax1' => 'decimal:2',
        'saldmin2' => 'decimal:2',
        'saldmax2' => 'decimal:2',
        'saldmin3' => 'decimal:2',
        'saldmax3' => 'decimal:2',
        'saldmin4' => 'decimal:2',
        'saldmax4' => 'decimal:2',
        'saldmin5' => 'decimal:2',
        'saldmax5' => 'decimal:2',
        'rate1' => 'decimal:2',
        'rate2' => 'decimal:2',
        'rate3' => 'decimal:2',
        'rate4' => 'decimal:2',
        'rate5' => 'decimal:2',
        'byadm' => 'decimal:2',
        'bytutup' => 'decimal:2',
        'bykoran' => 'decimal:2',
        'bysalmin' => 'decimal:2',
        'bypasif' => 'decimal:2',
        'saldpasf' => 'decimal:2',
        'minsetor' => 'decimal:2',
        'minsaldo' => 'decimal:2',
        'rate' => 'decimal:2',
        'ratelama' => 'decimal:2',
        'lamapasif' => 'decimal:2',
        'bbtdana' => 'decimal:2',
        'zakat' => 'decimal:2',
        'minsaldrk' => 'decimal:2',
        'mintrn' => 'decimal:2',
    ];
}
