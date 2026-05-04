<?php

declare(strict_types=1);

namespace App\Models\Mci\Lainnya;

use App\Models\Mci\MciBaseModel;

/**
 * Model MCI: TOFMGOLJAM
 * --------------------------------------------------------------------------
 * Domain   : Lainnya
 * Tabel    : [dbo].[TOFMGOLJAM]
 * Kolom    : 29
 * Sumber   : Mapping_MCI_MAR26_01042026.sql / MCI_MAR26_01042026.xlsx
 *
 * CATATAN:
 *  - Model ini READ-ONLY (lihat trait ReadOnlyModel di MciBaseModel).
 *  - Koneksi default `dashboard_data` (SQL Server), otomatis ke DB bulan terbaru.
 *  - Kolom dengan spasi/karakter khusus diakses via:
 *    $model->getAttribute('NAMA KOLOM')  atau  $model->{'NAMA KOLOM'}.
 *
 * @property string $nokontrak type: varchar(11)
 * @property string $nocif type: varchar(9)
 * @property string|null $nama type: varchar(100)
 * @property string|null $namaslik type: varchar(150)
 * @property string $noid type: varchar(20)
 * @property string $jnsid type: char(1)
 * @property string|null $sex type: char(1)
 * @property string $kdsegmen type: char(3)
 * @property string|null $kdgoljam type: varchar(10)
 * @property string|null $alamat type: varchar(100)
 * @property string|null $kelurahan type: varchar(30)
 * @property string|null $kecamatan type: varchar(30)
 * @property string|null $kota type: varchar(30)
 * @property string|null $dati2 type: char(4)
 * @property string|null $bagjamin type: numeric(5)
 * @property string|null $ket type: varchar(150)
 * @property string|null $hp type: varchar(16)
 * @property string|null $telprmh type: varchar(16)
 * @property string|null $stsrec type: char(1)
 * @property string|null $inpuser type: varchar(50)
 * @property string|null $inptgljam type: varchar(14)
 * @property string|null $inpterm type: varchar(50)
 * @property string|null $chguser type: varchar(50)
 * @property string|null $chgtgljam type: varchar(14)
 * @property string|null $chgterm type: varchar(50)
 * @property string|null $autuser type: varchar(50)
 * @property string|null $auttgljam type: varchar(14)
 * @property string|null $autterm type: varchar(50)
 * @property string|null $npwp type: varchar(16)
 */
class Tofmgoljam extends MciBaseModel
{
    /**
     * Nama tabel (case-sensitive di SQL Server).
     */
    protected $table = 'TOFMGOLJAM';

    /**
     * Daftar LENGKAP kolom sesuai database (29 kolom).
     * Model ini read-only, $fillable hanya untuk dokumentasi & IDE helper.
     *
     * @var array<int,string>
     */
    protected $fillable = [
        'nokontrak',
        'nocif',
        'nama',
        'namaslik',
        'noid',
        'jnsid',
        'sex',
        'kdsegmen',
        'kdgoljam',
        'alamat',
        'kelurahan',
        'kecamatan',
        'kota',
        'dati2',
        'bagjamin',
        'ket',
        'hp',
        'telprmh',
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
        'npwp',
    ];

    /**
     * Casting tipe kolom (non-string saja).
     *
     * @var array<string,string>
     */
    protected $casts = [
        'bagjamin' => 'decimal:2',
    ];
}
