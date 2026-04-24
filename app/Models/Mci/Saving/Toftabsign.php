<?php

namespace App\Models\Mci\Saving;

use App\Models\Mci\MciBaseModel;

/**
 * Model MCI: TOFTABSIGN
 * --------------------------------------------------------------------------
 * Domain   : Saving
 * Tabel    : [dbo].[TOFTABSIGN]
 * Kolom    : 18
 * Sumber   : Mapping_MCI_MAR26_01042026.sql / MCI_MAR26_01042026.xlsx
 *
 * CATATAN:
 *  - Model ini READ-ONLY (lihat trait ReadOnlyModel di MciBaseModel).
 *  - Koneksi default `dashboard_data` (SQL Server), otomatis ke DB bulan terbaru.
 *  - Kolom dengan spasi/karakter khusus diakses via:
 *    $model->getAttribute('NAMA KOLOM')  atau  $model->{'NAMA KOLOM'}.
 *
 * @property int $ID  type: bigint(8)
 * @property string $notab  type: varchar(11)
 * @property string|null $qty  type: numeric(5)
 * @property string|null $nama_1  type: varchar(50)
 * @property string|null $nama_2  type: varchar(50)
 * @property string|null $nama_3  type: varchar(50)
 * @property string|null $id_1  type: varchar(20)
 * @property string|null $id_2  type: varchar(20)
 * @property string|null $id_3  type: varchar(20)
 * @property string|null $nocif_1  type: varchar(9)
 * @property string|null $nocif_2  type: varchar(9)
 * @property string|null $nocif_3  type: varchar(9)
 * @property string|null $limit_1  type: numeric(9)
 * @property string|null $limit_2  type: numeric(9)
 * @property string|null $limit_3  type: numeric(9)
 * @property string $andor_1  type: char(1)
 * @property string $andor_2  type: char(1)
 * @property string $andor_3  type: char(1)
 */
class Toftabsign extends MciBaseModel
{
    /**
     * Nama tabel (case-sensitive di SQL Server).
     */
    protected $table = 'TOFTABSIGN';

    /**
     * Daftar LENGKAP kolom sesuai database (18 kolom).
     * Model ini read-only, $fillable hanya untuk dokumentasi & IDE helper.
     *
     * @var array<int,string>
     */
    protected $fillable = [
        'ID',
        'notab',
        'qty',
        'nama_1',
        'nama_2',
        'nama_3',
        'id_1',
        'id_2',
        'id_3',
        'nocif_1',
        'nocif_2',
        'nocif_3',
        'limit_1',
        'limit_2',
        'limit_3',
        'andor_1',
        'andor_2',
        'andor_3',
    ];

    /**
     * Casting tipe kolom (non-string saja).
     *
     * @var array<string,string>
     */
    protected $casts = [
        'ID' => 'integer',
        'qty' => 'decimal:2',
        'limit_1' => 'decimal:2',
        'limit_2' => 'decimal:2',
        'limit_3' => 'decimal:2',
    ];
}
