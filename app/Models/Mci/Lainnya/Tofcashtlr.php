<?php

namespace App\Models\Mci\Lainnya;

use App\Models\Mci\MciBaseModel;

/**
 * Model MCI: TOFCASHTLR
 * --------------------------------------------------------------------------
 * Domain   : Lainnya
 * Tabel    : [dbo].[TOFCASHTLR]
 * Kolom    : 38
 * Sumber   : Mapping_MCI_MAR26_01042026.sql / MCI_MAR26_01042026.xlsx
 *
 * CATATAN:
 *  - Model ini READ-ONLY (lihat trait ReadOnlyModel di MciBaseModel).
 *  - Koneksi default `dashboard_data` (SQL Server), otomatis ke DB bulan terbaru.
 *  - Kolom dengan spasi/karakter khusus diakses via:
 *    $model->getAttribute('NAMA KOLOM')  atau  $model->{'NAMA KOLOM'}.
 *
 * @property string|null $tgltrn  type: varchar(8)
 * @property string|null $userid  type: varchar(10)
 * @property string|null $sawal  type: numeric(9)
 * @property string|null $sahir  type: numeric(9)
 * @property string|null $tab_setor  type: numeric(9)
 * @property string|null $giro_setor  type: numeric(9)
 * @property string|null $dep_setor  type: numeric(9)
 * @property string|null $loan_setor  type: numeric(9)
 * @property string|null $gl_setor  type: numeric(9)
 * @property string|null $gadai_setor  type: numeric(9)
 * @property string|null $virtual_setor  type: numeric(9)
 * @property string|null $teller_setor  type: numeric(9)
 * @property string|null $teller_tarik  type: numeric(9)
 * @property string|null $lain_setor  type: numeric(9)
 * @property string|null $tab_tarik  type: numeric(9)
 * @property string|null $giro_tarik  type: numeric(9)
 * @property string|null $dep_tarik  type: numeric(9)
 * @property string|null $loan_tarik  type: numeric(9)
 * @property string|null $gl_tarik  type: numeric(9)
 * @property string|null $gadai_tarik  type: numeric(9)
 * @property string|null $virtual_tarik  type: numeric(9)
 * @property string|null $lain_tarik  type: numeric(9)
 * @property string|null $tab_debet  type: numeric(9)
 * @property string|null $tab_kredit  type: numeric(9)
 * @property string|null $giro_debet  type: numeric(9)
 * @property string|null $giro_kredit  type: numeric(9)
 * @property string|null $dep_debet  type: numeric(9)
 * @property string|null $dep_kredit  type: numeric(9)
 * @property string|null $loan_debet  type: numeric(9)
 * @property string|null $loan_kredit  type: numeric(9)
 * @property string|null $gl_debet  type: numeric(9)
 * @property string|null $gl_kredit  type: numeric(9)
 * @property string|null $gadai_debet  type: numeric(9)
 * @property string|null $gadai_kredit  type: numeric(9)
 * @property string|null $virtual_debet  type: numeric(9)
 * @property string|null $virtual_kredit  type: numeric(9)
 * @property string|null $cashbox  type: numeric(9)
 * @property string|null $setortovault  type: numeric(9)
 */
class Tofcashtlr extends MciBaseModel
{
    /**
     * Nama tabel (case-sensitive di SQL Server).
     */
    protected $table = 'TOFCASHTLR';

    /**
     * Daftar LENGKAP kolom sesuai database (38 kolom).
     * Model ini read-only, $fillable hanya untuk dokumentasi & IDE helper.
     *
     * @var array<int,string>
     */
    protected $fillable = [
        'tgltrn',
        'userid',
        'sawal',
        'sahir',
        'tab_setor',
        'giro_setor',
        'dep_setor',
        'loan_setor',
        'gl_setor',
        'gadai_setor',
        'virtual_setor',
        'teller_setor',
        'teller_tarik',
        'lain_setor',
        'tab_tarik',
        'giro_tarik',
        'dep_tarik',
        'loan_tarik',
        'gl_tarik',
        'gadai_tarik',
        'virtual_tarik',
        'lain_tarik',
        'tab_debet',
        'tab_kredit',
        'giro_debet',
        'giro_kredit',
        'dep_debet',
        'dep_kredit',
        'loan_debet',
        'loan_kredit',
        'gl_debet',
        'gl_kredit',
        'gadai_debet',
        'gadai_kredit',
        'virtual_debet',
        'virtual_kredit',
        'cashbox',
        'setortovault',
    ];

    /**
     * Casting tipe kolom (non-string saja).
     *
     * @var array<string,string>
     */
    protected $casts = [
        'sawal' => 'decimal:2',
        'sahir' => 'decimal:2',
        'tab_setor' => 'decimal:2',
        'giro_setor' => 'decimal:2',
        'dep_setor' => 'decimal:2',
        'loan_setor' => 'decimal:2',
        'gl_setor' => 'decimal:2',
        'gadai_setor' => 'decimal:2',
        'virtual_setor' => 'decimal:2',
        'teller_setor' => 'decimal:2',
        'teller_tarik' => 'decimal:2',
        'lain_setor' => 'decimal:2',
        'tab_tarik' => 'decimal:2',
        'giro_tarik' => 'decimal:2',
        'dep_tarik' => 'decimal:2',
        'loan_tarik' => 'decimal:2',
        'gl_tarik' => 'decimal:2',
        'gadai_tarik' => 'decimal:2',
        'virtual_tarik' => 'decimal:2',
        'lain_tarik' => 'decimal:2',
        'tab_debet' => 'decimal:2',
        'tab_kredit' => 'decimal:2',
        'giro_debet' => 'decimal:2',
        'giro_kredit' => 'decimal:2',
        'dep_debet' => 'decimal:2',
        'dep_kredit' => 'decimal:2',
        'loan_debet' => 'decimal:2',
        'loan_kredit' => 'decimal:2',
        'gl_debet' => 'decimal:2',
        'gl_kredit' => 'decimal:2',
        'gadai_debet' => 'decimal:2',
        'gadai_kredit' => 'decimal:2',
        'virtual_debet' => 'decimal:2',
        'virtual_kredit' => 'decimal:2',
        'cashbox' => 'decimal:2',
        'setortovault' => 'decimal:2',
    ];
}
