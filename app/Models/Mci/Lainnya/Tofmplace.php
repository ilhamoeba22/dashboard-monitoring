<?php

namespace App\Models\Mci\Lainnya;

use App\Models\Mci\MciBaseModel;

/**
 * Model MCI: TOFMPLACE
 * --------------------------------------------------------------------------
 * Domain   : Lainnya
 * Tabel    : [dbo].[TOFMPLACE]
 * Kolom    : 59
 * Sumber   : Mapping_MCI_MAR26_01042026.sql / MCI_MAR26_01042026.xlsx
 *
 * CATATAN:
 *  - Model ini READ-ONLY (lihat trait ReadOnlyModel di MciBaseModel).
 *  - Koneksi default `dashboard_data` (SQL Server), otomatis ke DB bulan terbaru.
 *  - Kolom dengan spasi/karakter khusus diakses via:
 *    $model->getAttribute('NAMA KOLOM')  atau  $model->{'NAMA KOLOM'}.
 *
 * @property string $notreasury  type: varchar(11)
 * @property string $nmtreasury  type: varchar(30)
 * @property string $noakad  type: varchar(50)
 * @property string $batch  type: numeric(5)
 * @property string $noref  type: varchar(30)
 * @property string $kdgroupdana  type: varchar(10)
 * @property string $grouppby  type: varchar(2)
 * @property string|null $pokpby  type: varchar(2)
 * @property string|null $jnstrx  type: char(1)
 * @property string $kdloc  type: char(2)
 * @property string $mdlawal  type: numeric(9)
 * @property string $mgnawal  type: numeric(9)
 * @property string $osmdlc  type: numeric(9)
 * @property string $osmgnc  type: numeric(9)
 * @property string|null $tglbuka  type: varchar(8)
 * @property string|null $tgleff  type: varchar(8)
 * @property string|null $tglexp  type: varchar(8)
 * @property string|null $jw  type: numeric(5)
 * @property string|null $kdjw  type: varchar(1)
 * @property string|null $nisbah  type: numeric(9)
 * @property string|null $equivrate  type: numeric(5)
 * @property string|null $estbhfull  type: numeric(9)
 * @property string|null $estbheom  type: numeric(9)
 * @property string|null $realbhfull  type: numeric(9)
 * @property string|null $ppap  type: numeric(9)
 * @property string|null $kddealer  type: varchar(10)
 * @property string|null $ket  type: varchar(100)
 * @property string|null $goljam  type: varchar(5)
 * @property string|null $bagjam  type: varchar(5)
 * @property string|null $coll  type: char(1)
 * @property string|null $noacdrop  type: varchar(20)
 * @property string|null $kdac  type: char(1)
 * @property string|null $sandibank  type: varchar(6)
 * @property string|null $sbbhtg  type: varchar(11)
 * @property string|null $sbbmydt  type: varchar(11)
 * @property string|null $sbbbh  type: varchar(11)
 * @property string|null $sbbttpangs  type: varchar(11)
 * @property string|null $ovdlimit  type: char(1)
 * @property string|null $stsangs  type: char(1)
 * @property string|null $ststrn  type: char(1)
 * @property string|null $stsrec  type: char(1)
 * @property string|null $inpuser  type: varchar(10)
 * @property string|null $inptgljam  type: varchar(14)
 * @property string|null $inpterm  type: varchar(10)
 * @property string|null $chguser  type: varchar(10)
 * @property string|null $chgtgljam  type: varchar(14)
 * @property string|null $chgterm  type: varchar(10)
 * @property string|null $autuser  type: varchar(10)
 * @property string|null $auttgljam  type: varchar(14)
 * @property string|null $autterm  type: varchar(10)
 * @property string|null $osmdlceom  type: numeric(9)
 * @property string|null $equivrateawal  type: numeric(5)
 * @property string|null $golcust  type: varchar(6)
 * @property string|null $terkait  type: char(1)
 * @property string|null $byadm  type: numeric(9)
 * @property string|null $HUBBANK  type: char(1)
 * @property string|null $jnsjamin  type: varchar(10)
 * @property string|null $nomjamin  type: numeric(9)
 * @property string|null $nocif  type: varchar(10)
 */
class Tofmplace extends MciBaseModel
{
    /**
     * Nama tabel (case-sensitive di SQL Server).
     */
    protected $table = 'TOFMPLACE';

    /**
     * Daftar LENGKAP kolom sesuai database (59 kolom).
     * Model ini read-only, $fillable hanya untuk dokumentasi & IDE helper.
     *
     * @var array<int,string>
     */
    protected $fillable = [
        'notreasury',
        'nmtreasury',
        'noakad',
        'batch',
        'noref',
        'kdgroupdana',
        'grouppby',
        'pokpby',
        'jnstrx',
        'kdloc',
        'mdlawal',
        'mgnawal',
        'osmdlc',
        'osmgnc',
        'tglbuka',
        'tgleff',
        'tglexp',
        'jw',
        'kdjw',
        'nisbah',
        'equivrate',
        'estbhfull',
        'estbheom',
        'realbhfull',
        'ppap',
        'kddealer',
        'ket',
        'goljam',
        'bagjam',
        'coll',
        'noacdrop',
        'kdac',
        'sandibank',
        'sbbhtg',
        'sbbmydt',
        'sbbbh',
        'sbbttpangs',
        'ovdlimit',
        'stsangs',
        'ststrn',
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
        'osmdlceom',
        'equivrateawal',
        'golcust',
        'terkait',
        'byadm',
        'HUBBANK',
        'jnsjamin',
        'nomjamin',
        'nocif',
    ];

    /**
     * Casting tipe kolom (non-string saja).
     *
     * @var array<string,string>
     */
    protected $casts = [
        'batch' => 'decimal:2',
        'mdlawal' => 'decimal:2',
        'mgnawal' => 'decimal:2',
        'osmdlc' => 'decimal:2',
        'osmgnc' => 'decimal:2',
        'jw' => 'decimal:2',
        'nisbah' => 'decimal:2',
        'equivrate' => 'decimal:2',
        'estbhfull' => 'decimal:2',
        'estbheom' => 'decimal:2',
        'realbhfull' => 'decimal:2',
        'ppap' => 'decimal:2',
        'osmdlceom' => 'decimal:2',
        'equivrateawal' => 'decimal:2',
        'byadm' => 'decimal:2',
        'nomjamin' => 'decimal:2',
    ];
}
