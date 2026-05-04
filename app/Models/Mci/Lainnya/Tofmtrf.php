<?php

declare(strict_types=1);

namespace App\Models\Mci\Lainnya;

use App\Models\Mci\MciBaseModel;

/**
 * Model MCI: TOFMTRF
 * --------------------------------------------------------------------------
 * Domain   : Lainnya
 * Tabel    : [dbo].[TOFMTRF]
 * Kolom    : 49
 * Sumber   : Mapping_MCI_MAR26_01042026.sql / MCI_MAR26_01042026.xlsx
 *
 * CATATAN:
 *  - Model ini READ-ONLY (lihat trait ReadOnlyModel di MciBaseModel).
 *  - Koneksi default `dashboard_data` (SQL Server), otomatis ke DB bulan terbaru.
 *  - Kolom dengan spasi/karakter khusus diakses via:
 *    $model->getAttribute('NAMA KOLOM')  atau  $model->{'NAMA KOLOM'}.
 *
 * @property int $ID type: bigint(8)
 * @property string $tgltrn type: varchar(8)
 * @property string $batch type: numeric(5)
 * @property string $notrn type: numeric(5)
 * @property string|null $noreff type: varchar(256)
 * @property string $kdtrf type: varchar(10)
 * @property string|null $kdidtx type: varchar(20)
 * @property string|null $nmtx type: varchar(100)
 * @property string|null $nohp type: varchar(20)
 * @property string $kdbank type: varchar(10)
 * @property string $norekrx type: varchar(20)
 * @property string|null $nmbank type: varchar(100)
 * @property string|null $alamatbank type: varchar(50)
 * @property string|null $nmrx type: varchar(100)
 * @property string|null $alamatrx type: varchar(100)
 * @property string|null $hprx type: varchar(20)
 * @property string|null $amount type: numeric(9)
 * @property string|null $amount_biaya type: numeric(9)
 * @property string|null $norek type: varchar(20)
 * @property string|null $kddr type: char(1)
 * @property string|null $ket type: varchar(100)
 * @property string|null $jnsdev type: char(1)
 * @property string|null $kdloc type: char(2)
 * @property string|null $noaba type: varchar(20)
 * @property string|null $sbbtrf type: varchar(11)
 * @property string|null $sbbfeetrf type: varchar(11)
 * @property string|null $stsrec type: char(1)
 * @property string $inpuser type: varchar(10)
 * @property string $inptgljam type: varchar(14)
 * @property string $inpterm type: varchar(10)
 * @property string $chguser type: varchar(10)
 * @property string $chgtgljam type: varchar(14)
 * @property string $chgterm type: varchar(10)
 * @property string $autuser type: varchar(10)
 * @property string $auttgljam type: varchar(14)
 * @property string $autterm type: varchar(10)
 * @property string|null $ststrn type: varchar(1)
 * @property string|null $jnssetor type: varchar(5)
 * @property string $bankidtx type: varchar(20)
 * @property string $nmbanktx type: varchar(100)
 * @property string|null $ketapproval type: varchar(-1)
 * @property string $username type: varchar(100)
 * @property string $mtuang type: varchar(50)
 * @property string $tobankreff type: varchar(255)
 * @property string $senderbankreff type: varchar(255)
 * @property string|null $tglsistem type: varchar(15)
 * @property string|null $email type: varchar(50)
 * @property string $rc type: varchar(6)
 * @property string $rcdesc type: varchar(100)
 */
class Tofmtrf extends MciBaseModel
{
    /**
     * Nama tabel (case-sensitive di SQL Server).
     */
    protected $table = 'TOFMTRF';

    /**
     * Daftar LENGKAP kolom sesuai database (49 kolom).
     * Model ini read-only, $fillable hanya untuk dokumentasi & IDE helper.
     *
     * @var array<int,string>
     */
    protected $fillable = [
        'ID',
        'tgltrn',
        'batch',
        'notrn',
        'noreff',
        'kdtrf',
        'kdidtx',
        'nmtx',
        'nohp',
        'kdbank',
        'norekrx',
        'nmbank',
        'alamatbank',
        'nmrx',
        'alamatrx',
        'hprx',
        'amount',
        'amount_biaya',
        'norek',
        'kddr',
        'ket',
        'jnsdev',
        'kdloc',
        'noaba',
        'sbbtrf',
        'sbbfeetrf',
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
        'ststrn',
        'jnssetor',
        'bankidtx',
        'nmbanktx',
        'ketapproval',
        'username',
        'mtuang',
        'tobankreff',
        'senderbankreff',
        'tglsistem',
        'email',
        'rc',
        'rcdesc',
    ];

    /**
     * Casting tipe kolom (non-string saja).
     *
     * @var array<string,string>
     */
    protected $casts = [
        'ID' => 'integer',
        'batch' => 'decimal:2',
        'notrn' => 'decimal:2',
        'amount' => 'decimal:2',
        'amount_biaya' => 'decimal:2',
    ];
}
