<?php

namespace App\Models\Mci\Lainnya;

use App\Models\Mci\MciBaseModel;

/**
 * Model MCI: TOFTVACC
 * --------------------------------------------------------------------------
 * Domain   : Lainnya
 * Tabel    : [dbo].[TOFTVACC]
 * Kolom    : 19
 * Sumber   : Mapping_MCI_MAR26_01042026.sql / MCI_MAR26_01042026.xlsx
 *
 * CATATAN:
 *  - Model ini READ-ONLY (lihat trait ReadOnlyModel di MciBaseModel).
 *  - Koneksi default `dashboard_data` (SQL Server), otomatis ke DB bulan terbaru.
 *  - Kolom dengan spasi/karakter khusus diakses via:
 *    $model->getAttribute('NAMA KOLOM')  atau  $model->{'NAMA KOLOM'}.
 *
 * @property string $kdlembaga  type: varchar(10)
 * @property string|null $nmlembaga  type: varchar(40)
 * @property string|null $noaccinduk  type: varchar(11)
 * @property string|null $ststransf  type: varchar(1)
 * @property string|null $stsbaghas  type: varchar(1)
 * @property string|null $porsibaghas  type: numeric(5)
 * @property string|null $noacbaghas  type: varchar(11)
 * @property string|null $nosbbescrow  type: varchar(11)
 * @property string|null $kdbuku  type: varchar(2)
 * @property string|null $stsrec  type: varchar(1)
 * @property string|null $inpuser  type: varchar(10)
 * @property string|null $inptgljam  type: varchar(14)
 * @property string|null $inpterm  type: varchar(10)
 * @property string|null $chguser  type: varchar(10)
 * @property string|null $chgtgljam  type: varchar(14)
 * @property string|null $chgterm  type: varchar(10)
 * @property string|null $autuser  type: varchar(10)
 * @property string|null $auttgljam  type: varchar(14)
 * @property string|null $autterm  type: varchar(10)
 */
class Toftvacc extends MciBaseModel
{
    /**
     * Nama tabel (case-sensitive di SQL Server).
     */
    protected $table = 'TOFTVACC';

    /**
     * Daftar LENGKAP kolom sesuai database (19 kolom).
     * Model ini read-only, $fillable hanya untuk dokumentasi & IDE helper.
     *
     * @var array<int,string>
     */
    protected $fillable = [
        'kdlembaga',
        'nmlembaga',
        'noaccinduk',
        'ststransf',
        'stsbaghas',
        'porsibaghas',
        'noacbaghas',
        'nosbbescrow',
        'kdbuku',
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
        'porsibaghas' => 'decimal:2',
    ];
}
