<?php

declare(strict_types=1);

namespace App\Models\Mci\Lainnya;

use App\Models\Mci\MciBaseModel;

/**
 * Model MCI: H_EMTRN
 * --------------------------------------------------------------------------
 * Domain   : Lainnya
 * Tabel    : [dbo].[H_EMTRN]
 * Kolom    : 22
 * Sumber   : Mapping_MCI_MAR26_01042026.sql / MCI_MAR26_01042026.xlsx
 *
 * CATATAN:
 *  - Model ini READ-ONLY (lihat trait ReadOnlyModel di MciBaseModel).
 *  - Koneksi default `dashboard_data` (SQL Server), otomatis ke DB bulan terbaru.
 *  - Kolom dengan spasi/karakter khusus diakses via:
 *    $model->getAttribute('NAMA KOLOM')  atau  $model->{'NAMA KOLOM'}.
 *
 * @property int $ID type: bigint(8)
 * @property string $tgltrn type: varchar(8)
 * @property string $kodetrn type: varchar(4)
 * @property string $batch type: numeric(5)
 * @property string $notrn type: numeric(9)
 * @property string $dracc type: varchar(17)
 * @property string $cracc type: varchar(17)
 * @property string $amount type: numeric(9)
 * @property string $dokumen type: varchar(30)
 * @property string $ket type: varchar(100)
 * @property string $kdmchn type: varchar(11)
 * @property string|null $kdbank type: varchar(6)
 * @property string|null $nmdr type: varchar(30)
 * @property string|null $nmcr type: varchar(30)
 * @property string|null $ststrn type: varchar(10)
 * @property string|null $stsemail type: char(1)
 * @property string|null $inpuser type: varchar(10)
 * @property string|null $inptgljam type: varchar(14)
 * @property string|null $inpterm type: varchar(10)
 * @property string|null $autuser type: varchar(10)
 * @property string|null $auttgljam type: varchar(14)
 * @property string|null $autterm type: varchar(10)
 */
class HEmtrn extends MciBaseModel
{
    /**
     * Nama tabel (case-sensitive di SQL Server).
     */
    protected $table = 'H_EMTRN';

    /**
     * Daftar LENGKAP kolom sesuai database (22 kolom).
     * Model ini read-only, $fillable hanya untuk dokumentasi & IDE helper.
     *
     * @var array<int,string>
     */
    protected $fillable = [
        'ID',
        'tgltrn',
        'kodetrn',
        'batch',
        'notrn',
        'dracc',
        'cracc',
        'amount',
        'dokumen',
        'ket',
        'kdmchn',
        'kdbank',
        'nmdr',
        'nmcr',
        'ststrn',
        'stsemail',
        'inpuser',
        'inptgljam',
        'inpterm',
        'autuser',
        'auttgljam',
        'autterm',
    ];

    /**
     * Casting tipe kolom (non-string saja).
     *
     * @var array<string,string>
     */
    protected $casts = [
        'ID' => 'integer',
        'batch' => 'decimal:2',
        'notrn' => 'decimal:2',
        'amount' => 'decimal:2',
    ];
}
