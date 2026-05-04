<?php

declare(strict_types=1);

namespace App\Models\Mci\Lainnya;

use App\Models\Mci\MciBaseModel;

/**
 * Model MCI: TOFTPLAFPOK
 * --------------------------------------------------------------------------
 * Domain   : Lainnya
 * Tabel    : [dbo].[TOFTPLAFPOK]
 * Kolom    : 35
 * Sumber   : Mapping_MCI_MAR26_01042026.sql / MCI_MAR26_01042026.xlsx
 *
 * CATATAN:
 *  - Model ini READ-ONLY (lihat trait ReadOnlyModel di MciBaseModel).
 *  - Koneksi default `dashboard_data` (SQL Server), otomatis ke DB bulan terbaru.
 *  - Kolom dengan spasi/karakter khusus diakses via:
 *    $model->getAttribute('NAMA KOLOM')  atau  $model->{'NAMA KOLOM'}.
 *
 * @property string $kdgroupdeb type: varchar(10)
 * @property string|null $plafon type: numeric(9)
 * @property string|null $jw type: numeric(5)
 * @property string|null $kdjw type: char(1)
 * @property string|null $margin type: numeric(9)
 * @property string|null $angspok type: numeric(9)
 * @property string|null $angsmgn type: numeric(9)
 * @property string|null $byadm type: numeric(5)
 * @property string|null $byasr type: numeric(5)
 * @property string|null $byakad type: numeric(5)
 * @property string|null $kdbiaya_1 type: varchar(2)
 * @property string|null $kdbiaya_2 type: varchar(2)
 * @property string|null $kdbiaya_3 type: varchar(2)
 * @property string|null $kdprd_simp_1 type: varchar(2)
 * @property string|null $kdprd_simp_2 type: varchar(2)
 * @property string|null $kdprd_simp_3 type: varchar(2)
 * @property string|null $kdprd_simp_4 type: varchar(2)
 * @property string|null $kdprd_simp_5 type: varchar(2)
 * @property string|null $setor_simp_1 type: numeric(9)
 * @property string|null $setor_simp_2 type: numeric(9)
 * @property string|null $setor_simp_3 type: numeric(9)
 * @property string|null $setor_simp_4 type: numeric(9)
 * @property string|null $setor_simp_5 type: numeric(9)
 * @property string|null $stsrec type: char(1)
 * @property string|null $inpuser type: varchar(10)
 * @property string|null $inptgljam type: varchar(14)
 * @property string|null $inpterm type: varchar(10)
 * @property string|null $chguser type: varchar(10)
 * @property string|null $chgtgljam type: varchar(14)
 * @property string|null $chgterm type: varchar(10)
 * @property string|null $autuser type: varchar(10)
 * @property string|null $auttgljam type: varchar(14)
 * @property string|null $autterm type: varchar(10)
 * @property string|null $kdtagih type: char(1)
 * @property string|null $frektagih type: numeric(5)
 */
class Toftplafpok extends MciBaseModel
{
    /**
     * Nama tabel (case-sensitive di SQL Server).
     */
    protected $table = 'TOFTPLAFPOK';

    /**
     * Daftar LENGKAP kolom sesuai database (35 kolom).
     * Model ini read-only, $fillable hanya untuk dokumentasi & IDE helper.
     *
     * @var array<int,string>
     */
    protected $fillable = [
        'kdgroupdeb',
        'plafon',
        'jw',
        'kdjw',
        'margin',
        'angspok',
        'angsmgn',
        'byadm',
        'byasr',
        'byakad',
        'kdbiaya_1',
        'kdbiaya_2',
        'kdbiaya_3',
        'kdprd_simp_1',
        'kdprd_simp_2',
        'kdprd_simp_3',
        'kdprd_simp_4',
        'kdprd_simp_5',
        'setor_simp_1',
        'setor_simp_2',
        'setor_simp_3',
        'setor_simp_4',
        'setor_simp_5',
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
        'kdtagih',
        'frektagih',
    ];

    /**
     * Casting tipe kolom (non-string saja).
     *
     * @var array<string,string>
     */
    protected $casts = [
        'plafon' => 'decimal:2',
        'jw' => 'decimal:2',
        'margin' => 'decimal:2',
        'angspok' => 'decimal:2',
        'angsmgn' => 'decimal:2',
        'byadm' => 'decimal:2',
        'byasr' => 'decimal:2',
        'byakad' => 'decimal:2',
        'setor_simp_1' => 'decimal:2',
        'setor_simp_2' => 'decimal:2',
        'setor_simp_3' => 'decimal:2',
        'setor_simp_4' => 'decimal:2',
        'setor_simp_5' => 'decimal:2',
        'frektagih' => 'decimal:2',
    ];
}
