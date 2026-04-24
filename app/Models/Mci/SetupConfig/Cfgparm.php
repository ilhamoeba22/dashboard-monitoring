<?php

namespace App\Models\Mci\SetupConfig;

use App\Models\Mci\MciBaseModel;

/**
 * Model MCI: CFGPARM
 * --------------------------------------------------------------------------
 * Domain   : Setup / Config
 * Tabel    : [dbo].[CFGPARM]
 * Kolom    : 18
 * Sumber   : Mapping_MCI_MAR26_01042026.sql / MCI_MAR26_01042026.xlsx
 *
 * CATATAN:
 *  - Model ini READ-ONLY (lihat trait ReadOnlyModel di MciBaseModel).
 *  - Koneksi default `dashboard_data` (SQL Server), otomatis ke DB bulan terbaru.
 *  - Kolom dengan spasi/karakter khusus diakses via:
 *    $model->getAttribute('NAMA KOLOM')  atau  $model->{'NAMA KOLOM'}.
 *
 * @property string $PARMGRP  type: varchar(5)
 * @property string $PARMID  type: varchar(10)
 * @property string|null $PARMNMGRP  type: varchar(50)
 * @property string $PARMNM  type: varchar(150)
 * @property string|null $PARM  type: varchar(50)
 * @property string|null $CEKVALIDASI  type: char(1)
 * @property string|null $VALIDASI  type: varchar(-1)
 * @property string|null $STSREC  type: char(1)
 * @property string|null $KETERANGAN  type: varchar(160)
 * @property string|null $INPUSER  type: varchar(10)
 * @property string|null $INPTERM  type: varchar(10)
 * @property string|null $INPTGL  type: varchar(14)
 * @property string|null $CHGUSER  type: varchar(10)
 * @property string|null $CHGTERM  type: varchar(10)
 * @property string|null $CHGTGL  type: varchar(14)
 * @property string|null $AUTHUSER  type: varchar(10)
 * @property string|null $AUTHTERM  type: varchar(10)
 * @property string|null $AUTHTGL  type: varchar(14)
 */
class Cfgparm extends MciBaseModel
{
    /**
     * Nama tabel (case-sensitive di SQL Server).
     */
    protected $table = 'CFGPARM';

    /**
     * Daftar LENGKAP kolom sesuai database (18 kolom).
     * Model ini read-only, $fillable hanya untuk dokumentasi & IDE helper.
     *
     * @var array<int,string>
     */
    protected $fillable = [
        'PARMGRP',
        'PARMID',
        'PARMNMGRP',
        'PARMNM',
        'PARM',
        'CEKVALIDASI',
        'VALIDASI',
        'STSREC',
        'KETERANGAN',
        'INPUSER',
        'INPTERM',
        'INPTGL',
        'CHGUSER',
        'CHGTERM',
        'CHGTGL',
        'AUTHUSER',
        'AUTHTERM',
        'AUTHTGL',
    ];

    /**
     * Casting tipe kolom (non-string saja).
     *
     * @var array<string,string>
     */
    protected $casts = [

    ];
}
