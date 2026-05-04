<?php

declare(strict_types=1);

namespace App\Models\Mci\Lainnya;

use App\Models\Mci\MciBaseModel;

/**
 * Model MCI: TMPPERFAON
 * --------------------------------------------------------------------------
 * Domain   : Lainnya
 * Tabel    : [dbo].[TMPPERFAON]
 * Kolom    : 16
 * Sumber   : Mapping_MCI_MAR26_01042026.sql / MCI_MAR26_01042026.xlsx
 *
 * CATATAN:
 *  - Model ini READ-ONLY (lihat trait ReadOnlyModel di MciBaseModel).
 *  - Koneksi default `dashboard_data` (SQL Server), otomatis ke DB bulan terbaru.
 *  - Kolom dengan spasi/karakter khusus diakses via:
 *    $model->getAttribute('NAMA KOLOM')  atau  $model->{'NAMA KOLOM'}.
 *
 * @property string|null $kdao type: varchar(10)
 * @property string|null $periode type: varchar(8)
 * @property string|null $noa_loan type: numeric(9)
 * @property string|null $noa_sirela type: numeric(9)
 * @property string|null $noa_simka type: numeric(9)
 * @property string|null $qty_droping type: numeric(9)
 * @property string|null $nom_droping type: numeric(9)
 * @property string|null $nom_angsur type: numeric(9)
 * @property string|null $nom_mbh type: numeric(9)
 * @property string|null $nom_koreksi_angsur type: numeric(9)
 * @property string|null $nom_koreksi_mbh type: numeric(9)
 * @property string|null $nom_rata_sirela type: numeric(9)
 * @property string|null $nom_rata_simka type: numeric(9)
 * @property string|null $userid type: varchar(10)
 * @property string|null $nom_rata_simka_periode type: numeric(9)
 * @property string|null $nom_rata_sirela_periode type: numeric(9)
 */
class Tmpperfaon extends MciBaseModel
{
    /**
     * Nama tabel (case-sensitive di SQL Server).
     */
    protected $table = 'TMPPERFAON';

    /**
     * Daftar LENGKAP kolom sesuai database (16 kolom).
     * Model ini read-only, $fillable hanya untuk dokumentasi & IDE helper.
     *
     * @var array<int,string>
     */
    protected $fillable = [
        'kdao',
        'periode',
        'noa_loan',
        'noa_sirela',
        'noa_simka',
        'qty_droping',
        'nom_droping',
        'nom_angsur',
        'nom_mbh',
        'nom_koreksi_angsur',
        'nom_koreksi_mbh',
        'nom_rata_sirela',
        'nom_rata_simka',
        'userid',
        'nom_rata_simka_periode',
        'nom_rata_sirela_periode',
    ];

    /**
     * Casting tipe kolom (non-string saja).
     *
     * @var array<string,string>
     */
    protected $casts = [
        'noa_loan' => 'decimal:2',
        'noa_sirela' => 'decimal:2',
        'noa_simka' => 'decimal:2',
        'qty_droping' => 'decimal:2',
        'nom_droping' => 'decimal:2',
        'nom_angsur' => 'decimal:2',
        'nom_mbh' => 'decimal:2',
        'nom_koreksi_angsur' => 'decimal:2',
        'nom_koreksi_mbh' => 'decimal:2',
        'nom_rata_sirela' => 'decimal:2',
        'nom_rata_simka' => 'decimal:2',
        'nom_rata_simka_periode' => 'decimal:2',
        'nom_rata_sirela_periode' => 'decimal:2',
    ];
}
