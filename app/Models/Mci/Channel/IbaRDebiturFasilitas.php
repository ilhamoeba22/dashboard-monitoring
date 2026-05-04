<?php

declare(strict_types=1);

namespace App\Models\Mci\Channel;

use App\Models\Mci\MciBaseModel;
use Carbon\Carbon;

/**
 * Model MCI: IBA_R_DEBITUR_FASILITAS
 * --------------------------------------------------------------------------
 * Domain   : Channel / ATM / Card
 * Tabel    : [dbo].[IBA_R_DEBITUR_FASILITAS]
 * Kolom    : 22
 * Sumber   : Mapping_MCI_MAR26_01042026.sql / MCI_MAR26_01042026.xlsx
 *
 * CATATAN:
 *  - Model ini READ-ONLY (lihat trait ReadOnlyModel di MciBaseModel).
 *  - Koneksi default `dashboard_data` (SQL Server), otomatis ke DB bulan terbaru.
 *  - Kolom dengan spasi/karakter khusus diakses via:
 *    $model->getAttribute('NAMA KOLOM')  atau  $model->{'NAMA KOLOM'}.
 *
 * @property string $ID_LEMBAGA type: varchar(3)
 * @property string $ID_BANK type: varchar(6)
 * @property string $ID_KTR_CABANG type: varchar(3)
 * @property int $BULAN type: smallint(2)
 * @property int $TAHUN type: smallint(2)
 * @property string $ID_DEB_FAS type: varchar(30)
 * @property Carbon|null $TGL_TERIMA type: datetime(8)
 * @property string|null $ID_JOINT_ACCOUNT type: varchar(1)
 * @property string|null $JENIS_FASILITAS type: varchar(4)
 * @property string $ID_DEBITUR type: varchar(43)
 * @property string $ID_FASILITAS type: varchar(52)
 * @property string|null $STATUS_KIRIM type: varchar(1)
 * @property string|null $STATUS_DATA type: varchar(1)
 * @property string|null $OPERATION type: varchar(1)
 * @property string|null $CREATE_USER type: varchar(20)
 * @property Carbon|null $CREATE_DATE type: datetime(8)
 * @property Carbon|null $UPDATE_DATE type: datetime(8)
 * @property int|null $VERSI type: int(4)
 * @property string|null $TIPE_ACCOUNT type: varchar(2)
 * @property string|null $SANDI_TERMINAL type: varchar(2)
 * @property string|null $DIN type: varchar(20)
 * @property string|null $NOREK type: varchar(25)
 */
class IbaRDebiturFasilitas extends MciBaseModel
{
    /**
     * Nama tabel (case-sensitive di SQL Server).
     */
    protected $table = 'IBA_R_DEBITUR_FASILITAS';

    /**
     * Daftar LENGKAP kolom sesuai database (22 kolom).
     * Model ini read-only, $fillable hanya untuk dokumentasi & IDE helper.
     *
     * @var array<int,string>
     */
    protected $fillable = [
        'ID_LEMBAGA',
        'ID_BANK',
        'ID_KTR_CABANG',
        'BULAN',
        'TAHUN',
        'ID_DEB_FAS',
        'TGL_TERIMA',
        'ID_JOINT_ACCOUNT',
        'JENIS_FASILITAS',
        'ID_DEBITUR',
        'ID_FASILITAS',
        'STATUS_KIRIM',
        'STATUS_DATA',
        'OPERATION',
        'CREATE_USER',
        'CREATE_DATE',
        'UPDATE_DATE',
        'VERSI',
        'TIPE_ACCOUNT',
        'SANDI_TERMINAL',
        'DIN',
        'NOREK',
    ];

    /**
     * Casting tipe kolom (non-string saja).
     *
     * @var array<string,string>
     */
    protected $casts = [
        'BULAN' => 'integer',
        'TAHUN' => 'integer',
        'TGL_TERIMA' => 'datetime',
        'CREATE_DATE' => 'datetime',
        'UPDATE_DATE' => 'datetime',
        'VERSI' => 'integer',
    ];
}
