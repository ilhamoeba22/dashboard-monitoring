<?php

namespace App\Models\Mci\SetupConfig;

use App\Models\Mci\MciBaseModel;

/**
 * Model MCI: MGESETUP
 * --------------------------------------------------------------------------
 * Domain   : Setup / Config
 * Tabel    : [dbo].[MGESETUP]
 * Kolom    : 27
 * Sumber   : Mapping_MCI_MAR26_01042026.sql / MCI_MAR26_01042026.xlsx
 *
 * CATATAN:
 *  - Model ini READ-ONLY (lihat trait ReadOnlyModel di MciBaseModel).
 *  - Koneksi default `dashboard_data` (SQL Server), otomatis ke DB bulan terbaru.
 *  - Kolom dengan spasi/karakter khusus diakses via:
 *    $model->getAttribute('NAMA KOLOM')  atau  $model->{'NAMA KOLOM'}.
 *
 * @property string $idsetup  type: varchar(100)
 * @property string $parmgrp  type: varchar(50)
 * @property string $parmid  type: varchar(50)
 * @property string $parmnmgrp  type: varchar(150)
 * @property string $parmnm  type: varchar(255)
 * @property string $parmvalue  type: varchar(50)
 * @property string $parm1  type: varchar(50)
 * @property string $parm1validasi  type: varchar(100)
 * @property string $parm2  type: varchar(50)
 * @property string $parm2validasi  type: varchar(100)
 * @property string $parm3  type: varchar(50)
 * @property string $parm3validasi  type: varchar(100)
 * @property string $parm4  type: varchar(50)
 * @property string $parm4validasi  type: varchar(100)
 * @property string $cekvalidasi  type: char(1)
 * @property string $validasi  type: varchar(100)
 * @property string $stsrec  type: char(1)
 * @property string $keterangan  type: varchar(-1)
 * @property string $inpuser  type: varchar(50)
 * @property string $inpterm  type: varchar(-1)
 * @property string $inptgl  type: varchar(14)
 * @property string $chguser  type: varchar(50)
 * @property string $chgterm  type: varchar(-1)
 * @property string $chgtgl  type: varchar(14)
 * @property string $autuser  type: varchar(50)
 * @property string $autterm  type: varchar(-1)
 * @property string $auttgl  type: varchar(14)
 */
class Mgesetup extends MciBaseModel
{
    /**
     * Nama tabel (case-sensitive di SQL Server).
     */
    protected $table = 'MGESETUP';

    /**
     * Daftar LENGKAP kolom sesuai database (27 kolom).
     * Model ini read-only, $fillable hanya untuk dokumentasi & IDE helper.
     *
     * @var array<int,string>
     */
    protected $fillable = [
        'idsetup',
        'parmgrp',
        'parmid',
        'parmnmgrp',
        'parmnm',
        'parmvalue',
        'parm1',
        'parm1validasi',
        'parm2',
        'parm2validasi',
        'parm3',
        'parm3validasi',
        'parm4',
        'parm4validasi',
        'cekvalidasi',
        'validasi',
        'stsrec',
        'keterangan',
        'inpuser',
        'inpterm',
        'inptgl',
        'chguser',
        'chgterm',
        'chgtgl',
        'autuser',
        'autterm',
        'auttgl',
    ];

    /**
     * Casting tipe kolom (non-string saja).
     *
     * @var array<string,string>
     */
    protected $casts = [

    ];
}
