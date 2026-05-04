<?php

declare(strict_types=1);

namespace App\Models\Mci\Lainnya;

use App\Models\Mci\MciBaseModel;

/**
 * Model MCI: TOFTFEEKU
 * --------------------------------------------------------------------------
 * Domain   : Lainnya
 * Tabel    : [dbo].[TOFTFEEKU]
 * Kolom    : 24
 * Sumber   : Mapping_MCI_MAR26_01042026.sql / MCI_MAR26_01042026.xlsx
 *
 * CATATAN:
 *  - Model ini READ-ONLY (lihat trait ReadOnlyModel di MciBaseModel).
 *  - Koneksi default `dashboard_data` (SQL Server), otomatis ke DB bulan terbaru.
 *  - Kolom dengan spasi/karakter khusus diakses via:
 *    $model->getAttribute('NAMA KOLOM')  atau  $model->{'NAMA KOLOM'}.
 *
 * @property string $kdku type: char(1)
 * @property string|null $feecust type: numeric(5)
 * @property string|null $feenoncust type: numeric(5)
 * @property string|null $feebank type: numeric(5)
 * @property string|null $feebu type: numeric(5)
 * @property string|null $feemg type: numeric(5)
 * @property string|null $feeasb type: numeric(5)
 * @property string|null $sbbttptrf type: varchar(7)
 * @property string|null $sbbttpfee type: varchar(7)
 * @property string|null $sbbpendbank type: varchar(7)
 * @property string|null $sbbttpmg type: varchar(7)
 * @property string|null $sbbttpasb type: varchar(7)
 * @property string|null $stsrec type: char(1)
 * @property string|null $inpuser type: varchar(10)
 * @property string|null $inptgljam type: varchar(14)
 * @property string|null $inptgljam_mesin type: varchar(14)
 * @property string|null $inpterm type: varchar(10)
 * @property string|null $chguser type: varchar(10)
 * @property string|null $chgtgljam type: varchar(14)
 * @property string|null $chgterm type: varchar(10)
 * @property string|null $autuser type: varchar(10)
 * @property string|null $auttgljam type: varchar(14)
 * @property string|null $auttgljam_mesin type: varchar(14)
 * @property string|null $autterm type: varchar(10)
 */
class Toftfeeku extends MciBaseModel
{
    /**
     * Nama tabel (case-sensitive di SQL Server).
     */
    protected $table = 'TOFTFEEKU';

    /**
     * Daftar LENGKAP kolom sesuai database (24 kolom).
     * Model ini read-only, $fillable hanya untuk dokumentasi & IDE helper.
     *
     * @var array<int,string>
     */
    protected $fillable = [
        'kdku',
        'feecust',
        'feenoncust',
        'feebank',
        'feebu',
        'feemg',
        'feeasb',
        'sbbttptrf',
        'sbbttpfee',
        'sbbpendbank',
        'sbbttpmg',
        'sbbttpasb',
        'stsrec',
        'inpuser',
        'inptgljam',
        'inptgljam_mesin',
        'inpterm',
        'chguser',
        'chgtgljam',
        'chgterm',
        'autuser',
        'auttgljam',
        'auttgljam_mesin',
        'autterm',
    ];

    /**
     * Casting tipe kolom (non-string saja).
     *
     * @var array<string,string>
     */
    protected $casts = [
        'feecust' => 'decimal:2',
        'feenoncust' => 'decimal:2',
        'feebank' => 'decimal:2',
        'feebu' => 'decimal:2',
        'feemg' => 'decimal:2',
        'feeasb' => 'decimal:2',
    ];
}
