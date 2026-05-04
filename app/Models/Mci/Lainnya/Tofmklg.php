<?php

declare(strict_types=1);

namespace App\Models\Mci\Lainnya;

use App\Models\Mci\MciBaseModel;

/**
 * Model MCI: TOFMKLG
 * --------------------------------------------------------------------------
 * Domain   : Lainnya
 * Tabel    : [dbo].[TOFMKLG]
 * Kolom    : 29
 * Sumber   : Mapping_MCI_MAR26_01042026.sql / MCI_MAR26_01042026.xlsx
 *
 * CATATAN:
 *  - Model ini READ-ONLY (lihat trait ReadOnlyModel di MciBaseModel).
 *  - Koneksi default `dashboard_data` (SQL Server), otomatis ke DB bulan terbaru.
 *  - Kolom dengan spasi/karakter khusus diakses via:
 *    $model->getAttribute('NAMA KOLOM')  atau  $model->{'NAMA KOLOM'}.
 *
 * @property string|null $tgltrn type: varchar(8)
 * @property string|null $batch type: numeric(5)
 * @property string|null $notrn type: numeric(5)
 * @property string|null $kodetrn type: varchar(4)
 * @property string|null $kdcab type: varchar(3)
 * @property string|null $kdloc type: varchar(2)
 * @property string|null $nowarkat type: varchar(15)
 * @property string|null $jnswarkat type: varchar(1)
 * @property string|null $nominal type: numeric(9)
 * @property string|null $noackliring type: varchar(10)
 * @property string|null $tglwarkat type: varchar(8)
 * @property string|null $tglkliring type: varchar(8)
 * @property string|null $kdbank type: varchar(3)
 * @property string|null $nmpemilik type: varchar(40)
 * @property string|null $nmpenyetor type: varchar(40)
 * @property string|null $bykliring type: numeric(9)
 * @property string|null $carabayar type: varchar(1)
 * @property string|null $ket type: varchar(100)
 * @property string|null $ststrn type: varchar(1)
 * @property string|null $stskliring type: varchar(1)
 * @property string|null $inpuser type: varchar(10)
 * @property string|null $inptgljam type: varchar(14)
 * @property string|null $inpterm type: varchar(10)
 * @property string|null $usercair type: varchar(10)
 * @property string|null $tglcair type: varchar(8)
 * @property string|null $batchcair type: numeric(5)
 * @property string|null $notrncair type: numeric(5)
 * @property string|null $stsclosing type: varchar(1)
 * @property string|null $stsclosing_cair type: varchar(1)
 */
class Tofmklg extends MciBaseModel
{
    /**
     * Nama tabel (case-sensitive di SQL Server).
     */
    protected $table = 'TOFMKLG';

    /**
     * Daftar LENGKAP kolom sesuai database (29 kolom).
     * Model ini read-only, $fillable hanya untuk dokumentasi & IDE helper.
     *
     * @var array<int,string>
     */
    protected $fillable = [
        'tgltrn',
        'batch',
        'notrn',
        'kodetrn',
        'kdcab',
        'kdloc',
        'nowarkat',
        'jnswarkat',
        'nominal',
        'noackliring',
        'tglwarkat',
        'tglkliring',
        'kdbank',
        'nmpemilik',
        'nmpenyetor',
        'bykliring',
        'carabayar',
        'ket',
        'ststrn',
        'stskliring',
        'inpuser',
        'inptgljam',
        'inpterm',
        'usercair',
        'tglcair',
        'batchcair',
        'notrncair',
        'stsclosing',
        'stsclosing_cair',
    ];

    /**
     * Casting tipe kolom (non-string saja).
     *
     * @var array<string,string>
     */
    protected $casts = [
        'batch' => 'decimal:2',
        'notrn' => 'decimal:2',
        'nominal' => 'decimal:2',
        'bykliring' => 'decimal:2',
        'batchcair' => 'decimal:2',
        'notrncair' => 'decimal:2',
    ];
}
