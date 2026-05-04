<?php

declare(strict_types=1);

namespace App\Models\Mci\Lainnya;

use App\Models\Mci\MciBaseModel;

/**
 * Model MCI: T_DEBITUR
 * --------------------------------------------------------------------------
 * Domain   : Lainnya
 * Tabel    : [dbo].[T_DEBITUR]
 * Kolom    : 49
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
 * @property string $BULAN type: varchar(2)
 * @property string $TAHUN type: varchar(4)
 * @property string $ID_DEBITUR type: varchar(43)
 * @property string|null $DIN type: varchar(20)
 * @property string|null $NAMA_DEBITUR type: varchar(100)
 * @property string|null $NAMA_ALIAS type: varchar(50)
 * @property string|null $STATUS type: varchar(4)
 * @property string|null $KET_STATUS type: varchar(50)
 * @property string|null $GOL_DEBITUR type: varchar(3)
 * @property string|null $JNS_KELAMIN type: varchar(1)
 * @property string|null $TEMPAT_LAHIR type: varchar(50)
 * @property string|null $TGL_AKTE_AWAL type: varchar(8)
 * @property string|null $NO_AKTE_AWAL type: varchar(30)
 * @property string|null $NO_PASPOR type: varchar(30)
 * @property string|null $TGL_AKTE_AKHIR type: varchar(8)
 * @property string|null $NO_AKTE_AKHIR type: varchar(30)
 * @property string|null $NPWP_DEBITUR type: varchar(20)
 * @property string $ALAMAT_DEBITUR type: varchar(100)
 * @property string|null $DATI2_DEBITUR type: varchar(4)
 * @property string|null $KODE_POS type: varchar(5)
 * @property string|null $KELURAHAN type: varchar(50)
 * @property string|null $KECAMATAN type: varchar(50)
 * @property string|null $KODE_AREA type: varchar(4)
 * @property string|null $TELEPON type: varchar(8)
 * @property string|null $SANDI_PEKERJAAN type: varchar(3)
 * @property string|null $TEMPAT_BEKERJA type: varchar(50)
 * @property string|null $BIDANG_USAHA type: varchar(5)
 * @property string|null $IBU_DEBITUR type: varchar(50)
 * @property string|null $NEGARA_DOMISILI type: varchar(3)
 * @property string|null $GIN type: varchar(6)
 * @property string|null $NAMA_GROUP type: varchar(20)
 * @property string|null $HUB_DGN_BANK type: varchar(4)
 * @property string|null $LANGGAR_BMPK type: varchar(1)
 * @property string|null $LAMPAU_BMPK type: varchar(1)
 * @property string|null $RATING_DEBITUR type: varchar(5)
 * @property string|null $LEMBAGA_RATING type: varchar(50)
 * @property string|null $GO_PUBLIC type: varchar(1)
 * @property string|null $STATUS_KIRIM type: varchar(1)
 * @property string|null $STATUS_DATA type: varchar(1)
 * @property string|null $TGL_TERIMA type: varchar(8)
 * @property string|null $VERSI type: varchar(8)
 * @property string|null $OPERATION type: varchar(1)
 * @property string|null $CREATE_USER type: varchar(20)
 * @property string|null $CREATE_DATE type: varchar(8)
 * @property string|null $UPDATE_DATE type: varchar(8)
 * @property string $SANDI_TERMINAL type: varchar(2)
 */
class TDebitur extends MciBaseModel
{
    /**
     * Nama tabel (case-sensitive di SQL Server).
     */
    protected $table = 'T_DEBITUR';

    /**
     * Daftar LENGKAP kolom sesuai database (49 kolom).
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
        'ID_DEBITUR',
        'DIN',
        'NAMA_DEBITUR',
        'NAMA_ALIAS',
        'STATUS',
        'KET_STATUS',
        'GOL_DEBITUR',
        'JNS_KELAMIN',
        'TEMPAT_LAHIR',
        'TGL_AKTE_AWAL',
        'NO_AKTE_AWAL',
        'NO_PASPOR',
        'TGL_AKTE_AKHIR',
        'NO_AKTE_AKHIR',
        'NPWP_DEBITUR',
        'ALAMAT_DEBITUR',
        'DATI2_DEBITUR',
        'KODE_POS',
        'KELURAHAN',
        'KECAMATAN',
        'KODE_AREA',
        'TELEPON',
        'SANDI_PEKERJAAN',
        'TEMPAT_BEKERJA',
        'BIDANG_USAHA',
        'IBU_DEBITUR',
        'NEGARA_DOMISILI',
        'GIN',
        'NAMA_GROUP',
        'HUB_DGN_BANK',
        'LANGGAR_BMPK',
        'LAMPAU_BMPK',
        'RATING_DEBITUR',
        'LEMBAGA_RATING',
        'GO_PUBLIC',
        'STATUS_KIRIM',
        'STATUS_DATA',
        'TGL_TERIMA',
        'VERSI',
        'OPERATION',
        'CREATE_USER',
        'CREATE_DATE',
        'UPDATE_DATE',
        'SANDI_TERMINAL',
    ];

    /**
     * Casting tipe kolom (non-string saja).
     *
     * @var array<string,string>
     */
    protected $casts = [

    ];
}
