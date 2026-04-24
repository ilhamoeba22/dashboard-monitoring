<?php

namespace App\Models\Mci\SetupConfig;

use App\Models\Mci\MciBaseModel;

/**
 * Model MCI: SETUPJAM
 * --------------------------------------------------------------------------
 * Domain   : Setup / Config
 * Tabel    : [dbo].[SETUPJAM]
 * Kolom    : 28
 * Sumber   : Mapping_MCI_MAR26_01042026.sql / MCI_MAR26_01042026.xlsx
 *
 * CATATAN:
 *  - Model ini READ-ONLY (lihat trait ReadOnlyModel di MciBaseModel).
 *  - Koneksi default `dashboard_data` (SQL Server), otomatis ke DB bulan terbaru.
 *  - Kolom dengan spasi/karakter khusus diakses via:
 *    $model->getAttribute('NAMA KOLOM')  atau  $model->{'NAMA KOLOM'}.
 *
 * @property string $kdjam  type: varchar(2)
 * @property string|null $ket  type: varchar(30)
 * @property string|null $bobot  type: numeric(5)
 * @property string|null $bobotjam  type: numeric(5)
 * @property string|null $sandijambi  type: varchar(1)
 * @property string|null $sandijamsid  type: varchar(3)
 * @property string|null $ststaks  type: varchar(1)
 * @property string|null $periode1  type: numeric(5)
 * @property string|null $periode2  type: numeric(5)
 * @property string|null $periode3  type: numeric(5)
 * @property string|null $periode4  type: numeric(5)
 * @property string|null $bobot1  type: numeric(5)
 * @property string|null $bobot2  type: numeric(5)
 * @property string|null $bobot3  type: numeric(5)
 * @property string|null $bobot4  type: numeric(5)
 * @property string|null $stsrec  type: varchar(1)
 * @property string|null $inpuser  type: varchar(10)
 * @property string|null $inptgl  type: varchar(14)
 * @property string|null $inpterm  type: varchar(10)
 * @property string|null $chguser  type: varchar(10)
 * @property string|null $chgtgl  type: varchar(14)
 * @property string|null $chgterm  type: varchar(10)
 * @property string|null $autuser  type: varchar(10)
 * @property string|null $auttgl  type: varchar(14)
 * @property string|null $autterm  type: varchar(10)
 * @property string|null $stsbobotjam  type: varchar(1)
 * @property string|null $sandijamslik  type: varchar(15)
 * @property string|null $jnsikat  type: varchar(5)
 */
class Setupjam extends MciBaseModel
{
    /**
     * Nama tabel (case-sensitive di SQL Server).
     */
    protected $table = 'SETUPJAM';

    /**
     * Daftar LENGKAP kolom sesuai database (28 kolom).
     * Model ini read-only, $fillable hanya untuk dokumentasi & IDE helper.
     *
     * @var array<int,string>
     */
    protected $fillable = [
        'kdjam',
        'ket',
        'bobot',
        'bobotjam',
        'sandijambi',
        'sandijamsid',
        'ststaks',
        'periode1',
        'periode2',
        'periode3',
        'periode4',
        'bobot1',
        'bobot2',
        'bobot3',
        'bobot4',
        'stsrec',
        'inpuser',
        'inptgl',
        'inpterm',
        'chguser',
        'chgtgl',
        'chgterm',
        'autuser',
        'auttgl',
        'autterm',
        'stsbobotjam',
        'sandijamslik',
        'jnsikat',
    ];

    /**
     * Casting tipe kolom (non-string saja).
     *
     * @var array<string,string>
     */
    protected $casts = [
        'bobot' => 'decimal:2',
        'bobotjam' => 'decimal:2',
        'periode1' => 'decimal:2',
        'periode2' => 'decimal:2',
        'periode3' => 'decimal:2',
        'periode4' => 'decimal:2',
        'bobot1' => 'decimal:2',
        'bobot2' => 'decimal:2',
        'bobot3' => 'decimal:2',
        'bobot4' => 'decimal:2',
    ];
}
