<?php

namespace App\Models\Mci\Financing;

use App\Models\Mci\MciBaseModel;

/**
 * Model MCI: TMP_UPLOADLOAN
 * --------------------------------------------------------------------------
 * Domain   : Financing / Loan
 * Tabel    : [dbo].[TMP_UPLOADLOAN]
 * Kolom    : 61
 * Sumber   : Mapping_MCI_MAR26_01042026.sql / MCI_MAR26_01042026.xlsx
 *
 * CATATAN:
 *  - Model ini READ-ONLY (lihat trait ReadOnlyModel di MciBaseModel).
 *  - Koneksi default `dashboard_data` (SQL Server), otomatis ke DB bulan terbaru.
 *  - Kolom dengan spasi/karakter khusus diakses via:
 *    $model->getAttribute('NAMA KOLOM')  atau  $model->{'NAMA KOLOM'}.
 *
 * @property string|null $nocif  type: varchar(11)
 * @property string|null $nama  type: varchar(50)
 * @property string|null $nokontrak  type: varchar(11)
 * @property string|null $kdprd  type: varchar(2)
 * @property string|null $tglakad  type: varchar(8)
 * @property string|null $tgleff  type: varchar(8)
 * @property int|null $jw  type: int(4)
 * @property string|null $kdjw  type: varchar(4)
 * @property string|null $tglexp  type: varchar(8)
 * @property string|null $pokok  type: decimal(9)
 * @property string|null $margin  type: decimal(9)
 * @property string|null $kdloc  type: varchar(3)
 * @property string|null $kdsi  type: varchar(3)
 * @property string|null $tmplahir  type: varchar(50)
 * @property string|null $tgllahir  type: varchar(8)
 * @property string|null $sex  type: varchar(1)
 * @property string|null $noid  type: varchar(10)
 * @property string|null $alamat  type: varchar(50)
 * @property string|null $kelurahan  type: varchar(25)
 * @property string|null $kecamatan  type: varchar(25)
 * @property string|null $kota  type: varchar(25)
 * @property string|null $kdpos  type: varchar(5)
 * @property string|null $dati2  type: varchar(20)
 * @property string|null $nmibukandung  type: varchar(25)
 * @property string|null $notab  type: varchar(12)
 * @property string|null $noakad  type: varchar(50)
 * @property string|null $biaya01  type: decimal(9)
 * @property string|null $biaya02  type: decimal(9)
 * @property string|null $biaya03  type: decimal(9)
 * @property string|null $biaya04  type: decimal(9)
 * @property string|null $biaya05  type: decimal(9)
 * @property string|null $sekon_lb  type: varchar(10)
 * @property string|null $gunadeb_lb  type: varchar(10)
 * @property string|null $goldeb_lb  type: varchar(10)
 * @property string|null $sifatpby_lb  type: varchar(10)
 * @property string|null $stspby_lb  type: varchar(10)
 * @property string|null $jnspby_lb  type: varchar(10)
 * @property string|null $goljam_lb  type: varchar(10)
 * @property string|null $porsjam_lb  type: varchar(4)
 * @property string|null $golpiutang_lb  type: varchar(10)
 * @property string|null $tujuanpguna_lb  type: varchar(2)
 * @property string|null $sifatplafond_lb  type: varchar(1)
 * @property string|null $tujuanpembiayaan_lb  type: varchar(100)
 * @property string|null $sektorekonomi_slik  type: varchar(6)
 * @property string|null $jnspenggunaan_slik  type: varchar(3)
 * @property string|null $jnspembiayaan_slik  type: varchar(5)
 * @property string|null $kdatmrlama_slik  type: varchar(5)
 * @property string|null $kdatmrbaru_slik  type: varchar(5)
 * @property string|null $golpenjamin_slik  type: varchar(5)
 * @property string|null $cararestruk_slik  type: varchar(5)
 * @property string|null $kdgroup  type: varchar(5)
 * @property string|null $kdtagih  type: varchar(5)
 * @property int|null $frektagih  type: int(4)
 * @property string|null $ao  type: varchar(10)
 * @property string|null $kdhtgmgn  type: varchar(10)
 * @property string|null $kdjadwal  type: varchar(10)
 * @property int|null $intervalpokok  type: int(4)
 * @property int|null $intervalmargin  type: int(4)
 * @property string|null $bagjamin  type: varchar(5)
 * @property string|null $GLB  type: varchar(2)
 * @property string|null $KDKOLEK  type: varchar(10)
 */
class TmpUploadloan extends MciBaseModel
{
    /**
     * Nama tabel (case-sensitive di SQL Server).
     */
    protected $table = 'TMP_UPLOADLOAN';

    /**
     * Daftar LENGKAP kolom sesuai database (61 kolom).
     * Model ini read-only, $fillable hanya untuk dokumentasi & IDE helper.
     *
     * @var array<int,string>
     */
    protected $fillable = [
        'nocif',
        'nama',
        'nokontrak',
        'kdprd',
        'tglakad',
        'tgleff',
        'jw',
        'kdjw',
        'tglexp',
        'pokok',
        'margin',
        'kdloc',
        'kdsi',
        'tmplahir',
        'tgllahir',
        'sex',
        'noid',
        'alamat',
        'kelurahan',
        'kecamatan',
        'kota',
        'kdpos',
        'dati2',
        'nmibukandung',
        'notab',
        'noakad',
        'biaya01',
        'biaya02',
        'biaya03',
        'biaya04',
        'biaya05',
        'sekon_lb',
        'gunadeb_lb',
        'goldeb_lb',
        'sifatpby_lb',
        'stspby_lb',
        'jnspby_lb',
        'goljam_lb',
        'porsjam_lb',
        'golpiutang_lb',
        'tujuanpguna_lb',
        'sifatplafond_lb',
        'tujuanpembiayaan_lb',
        'sektorekonomi_slik',
        'jnspenggunaan_slik',
        'jnspembiayaan_slik',
        'kdatmrlama_slik',
        'kdatmrbaru_slik',
        'golpenjamin_slik',
        'cararestruk_slik',
        'kdgroup',
        'kdtagih',
        'frektagih',
        'ao',
        'kdhtgmgn',
        'kdjadwal',
        'intervalpokok',
        'intervalmargin',
        'bagjamin',
        'GLB',
        'KDKOLEK',
    ];

    /**
     * Casting tipe kolom (non-string saja).
     *
     * @var array<string,string>
     */
    protected $casts = [
        'jw' => 'integer',
        'pokok' => 'decimal:2',
        'margin' => 'decimal:2',
        'biaya01' => 'decimal:2',
        'biaya02' => 'decimal:2',
        'biaya03' => 'decimal:2',
        'biaya04' => 'decimal:2',
        'biaya05' => 'decimal:2',
        'frektagih' => 'integer',
        'intervalpokok' => 'integer',
        'intervalmargin' => 'integer',
    ];
}
