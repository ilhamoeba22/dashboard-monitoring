<?php

declare(strict_types=1);

namespace App\Models\Mci\Lainnya;

use App\Models\Mci\MciBaseModel;

/**
 * Model MCI: TOFTSETOR
 * --------------------------------------------------------------------------
 * Domain   : Lainnya
 * Tabel    : [dbo].[TOFTSETOR]
 * Kolom    : 23
 * Sumber   : Mapping_MCI_MAR26_01042026.sql / MCI_MAR26_01042026.xlsx
 *
 * CATATAN:
 *  - Model ini READ-ONLY (lihat trait ReadOnlyModel di MciBaseModel).
 *  - Koneksi default `dashboard_data` (SQL Server), otomatis ke DB bulan terbaru.
 *  - Kolom dengan spasi/karakter khusus diakses via:
 *    $model->getAttribute('NAMA KOLOM')  atau  $model->{'NAMA KOLOM'}.
 *
 * @property string|null $kdgroupdeb type: varchar(10)
 * @property string|null $sbbttp type: varchar(10)
 * @property string|null $kdprd_loan_1 type: varchar(2)
 * @property string|null $kdprd_loan_2 type: varchar(2)
 * @property string|null $kdprd_loan_3 type: varchar(2)
 * @property string|null $kdprd_loan_4 type: varchar(2)
 * @property string|null $kdprd_loan_5 type: varchar(2)
 * @property string|null $kdprd_simp_1 type: varchar(2)
 * @property string|null $kdprd_simp_2 type: varchar(2)
 * @property string|null $kdprd_simp_3 type: varchar(2)
 * @property string|null $kdprd_simp_4 type: varchar(2)
 * @property string|null $kdprd_simp_5 type: varchar(2)
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
 * @property string|null $kdprd_loan_6 type: char(2)
 */
class Toftsetor extends MciBaseModel
{
    /**
     * Nama tabel (case-sensitive di SQL Server).
     */
    protected $table = 'TOFTSETOR';

    /**
     * Daftar LENGKAP kolom sesuai database (23 kolom).
     * Model ini read-only, $fillable hanya untuk dokumentasi & IDE helper.
     *
     * @var array<int,string>
     */
    protected $fillable = [
        'kdgroupdeb',
        'sbbttp',
        'kdprd_loan_1',
        'kdprd_loan_2',
        'kdprd_loan_3',
        'kdprd_loan_4',
        'kdprd_loan_5',
        'kdprd_simp_1',
        'kdprd_simp_2',
        'kdprd_simp_3',
        'kdprd_simp_4',
        'kdprd_simp_5',
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
        'kdprd_loan_6',
    ];

    /**
     * Casting tipe kolom (non-string saja).
     *
     * @var array<string,string>
     */
    protected $casts = [

    ];
}
