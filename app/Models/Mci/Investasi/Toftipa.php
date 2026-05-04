<?php

declare(strict_types=1);

namespace App\Models\Mci\Investasi;

use App\Models\Mci\MciBaseModel;

/**
 * Model MCI: TOFTIPA
 * --------------------------------------------------------------------------
 * Domain   : Investasi / Saham
 * Tabel    : [dbo].[TOFTIPA]
 * Kolom    : 39
 * Sumber   : Mapping_MCI_MAR26_01042026.sql / MCI_MAR26_01042026.xlsx
 *
 * CATATAN:
 *  - Model ini READ-ONLY (lihat trait ReadOnlyModel di MciBaseModel).
 *  - Koneksi default `dashboard_data` (SQL Server), otomatis ke DB bulan terbaru.
 *  - Kolom dengan spasi/karakter khusus diakses via:
 *    $model->getAttribute('NAMA KOLOM')  atau  $model->{'NAMA KOLOM'}.
 *
 * @property string $kdprog type: varchar(2)
 * @property string|null $ket type: varchar(50)
 * @property string|null $mindeb type: numeric(5)
 * @property string|null $minos type: numeric(9)
 * @property string|null $npf_1 type: numeric(5)
 * @property string|null $npf_2 type: numeric(5)
 * @property string|null $npf_3 type: numeric(5)
 * @property string|null $npf_4 type: numeric(5)
 * @property string|null $npf_5 type: numeric(5)
 * @property string|null $npf_6 type: numeric(5)
 * @property string|null $bonus_1 type: numeric(9)
 * @property string|null $bonus_2 type: numeric(9)
 * @property string|null $bonus_3 type: numeric(9)
 * @property string|null $bonus_4 type: numeric(9)
 * @property string|null $bonus_5 type: numeric(9)
 * @property string|null $bonus_6 type: numeric(9)
 * @property string|null $pengurang_1 type: numeric(5)
 * @property string|null $mintabbaru type: numeric(5)
 * @property string|null $mindepbaru type: numeric(5)
 * @property string|null $saldoratatabbaru type: numeric(9)
 * @property string|null $saldoratatablama type: numeric(9)
 * @property string|null $saldoratadepbaru type: numeric(9)
 * @property string|null $saldoratadeplama type: numeric(9)
 * @property string|null $bonus_tabbaru type: numeric(9)
 * @property string|null $bonus_saratablama type: numeric(9)
 * @property string|null $bonus_saratabbaru type: numeric(9)
 * @property string|null $bonus_depbaru type: numeric(9)
 * @property string|null $bonus_saradeplama type: numeric(9)
 * @property string|null $bonus_saradepbaru type: numeric(9)
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
 */
class Toftipa extends MciBaseModel
{
    /**
     * Nama tabel (case-sensitive di SQL Server).
     */
    protected $table = 'TOFTIPA';

    /**
     * Daftar LENGKAP kolom sesuai database (39 kolom).
     * Model ini read-only, $fillable hanya untuk dokumentasi & IDE helper.
     *
     * @var array<int,string>
     */
    protected $fillable = [
        'kdprog',
        'ket',
        'mindeb',
        'minos',
        'npf_1',
        'npf_2',
        'npf_3',
        'npf_4',
        'npf_5',
        'npf_6',
        'bonus_1',
        'bonus_2',
        'bonus_3',
        'bonus_4',
        'bonus_5',
        'bonus_6',
        'pengurang_1',
        'mintabbaru',
        'mindepbaru',
        'saldoratatabbaru',
        'saldoratatablama',
        'saldoratadepbaru',
        'saldoratadeplama',
        'bonus_tabbaru',
        'bonus_saratablama',
        'bonus_saratabbaru',
        'bonus_depbaru',
        'bonus_saradeplama',
        'bonus_saradepbaru',
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
    ];

    /**
     * Casting tipe kolom (non-string saja).
     *
     * @var array<string,string>
     */
    protected $casts = [
        'mindeb' => 'decimal:2',
        'minos' => 'decimal:2',
        'npf_1' => 'decimal:2',
        'npf_2' => 'decimal:2',
        'npf_3' => 'decimal:2',
        'npf_4' => 'decimal:2',
        'npf_5' => 'decimal:2',
        'npf_6' => 'decimal:2',
        'bonus_1' => 'decimal:2',
        'bonus_2' => 'decimal:2',
        'bonus_3' => 'decimal:2',
        'bonus_4' => 'decimal:2',
        'bonus_5' => 'decimal:2',
        'bonus_6' => 'decimal:2',
        'pengurang_1' => 'decimal:2',
        'mintabbaru' => 'decimal:2',
        'mindepbaru' => 'decimal:2',
        'saldoratatabbaru' => 'decimal:2',
        'saldoratatablama' => 'decimal:2',
        'saldoratadepbaru' => 'decimal:2',
        'saldoratadeplama' => 'decimal:2',
        'bonus_tabbaru' => 'decimal:2',
        'bonus_saratablama' => 'decimal:2',
        'bonus_saratabbaru' => 'decimal:2',
        'bonus_depbaru' => 'decimal:2',
        'bonus_saradeplama' => 'decimal:2',
        'bonus_saradepbaru' => 'decimal:2',
    ];
}
