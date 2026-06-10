<?php

declare(strict_types=1);

namespace App\Models\Mci\Cif;

use App\Models\Mci\Financing\Toflmb;
use App\Models\Mci\Deposito\Tofdep;
use App\Models\Mci\Saving\Toftabb;
use App\Models\Mci\Marketing\Ao;
use App\Models\Mci\Master\Cabang;
use App\Models\Mci\Master\Segmen;
use App\Models\Mci\Master\Wilayah;
use App\Models\Mci\MciBaseModel;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Model MCI: mCIF
 * --------------------------------------------------------------------------
 * Domain   : CIF / Customer
 * Tabel    : [dbo].[mCIF]
 * Kolom    : 100
 * Sumber   : Mapping_MCI_MAR26_01042026.sql / MCI_MAR26_01042026.xlsx
 *
 * CATATAN:
 *  - Model ini READ-ONLY (lihat trait ReadOnlyModel di MciBaseModel).
 *  - Koneksi default `dashboard_data` (SQL Server), otomatis ke DB bulan terbaru.
 *  - Kolom dengan spasi/karakter khusus diakses via:
 *    $model->getAttribute('NAMA KOLOM')  atau  $model->{'NAMA KOLOM'}.
 *
 * @property string $nocif type: varchar(9)
 * @property string|null $nm type: varchar(100)
 * @property string|null $nmkecil type: varchar(10)
 * @property string|null $agama type: varchar(1)
 * @property string|null $golcust type: varchar(1)
 * @property string|null $golcustbi type: varchar(3)
 * @property string|null $jnsbh type: varchar(4)
 * @property string|null $npwp type: varchar(25)
 * @property string|null $sandibank type: varchar(6)
 * @property string|null $stscust type: varchar(1)
 * @property string|null $jnsid type: varchar(1)
 * @property string|null $noid type: varchar(30)
 * @property string|null $tglid type: varchar(8)
 * @property string|null $tglexpid type: varchar(8)
 * @property string|null $penerbit type: varchar(20)
 * @property string|null $stskawin type: varchar(1)
 * @property string|null $sex type: varchar(1)
 * @property string|null $tmplhr type: varchar(30)
 * @property string|null $tgllhr type: varchar(8)
 * @property string|null $email type: varchar(40)
 * @property string|null $alamat type: varchar(256)
 * @property string|null $kelurahan type: varchar(50)
 * @property string|null $kecamatan type: varchar(50)
 * @property string|null $kota type: varchar(50)
 * @property string|null $kdpos type: varchar(5)
 * @property string|null $telprmh type: varchar(15)
 * @property string|null $telpktr type: varchar(15)
 * @property string|null $fax type: varchar(15)
 * @property string|null $hp type: varchar(20)
 * @property string|null $sandilok type: varchar(1)
 * @property string|null $sandidati type: varchar(5)
 * @property string|null $kdwil type: varchar(3)
 * @property string|null $nmibu type: varchar(100)
 * @property string|null $stskait type: varchar(1)
 * @property string|null $aoref type: varchar(8)
 * @property string|null $aohand type: varchar(8)
 * @property string|null $segmen type: varchar(5)
 * @property string|null $glb type: varchar(2)
 * @property string|null $tglbuka type: varchar(8)
 * @property string|null $tgltutup type: varchar(8)
 * @property string|null $alasan type: varchar(1)
 * @property string|null $vip type: varchar(1)
 * @property string|null $stsrestr type: varchar(1)
 * @property string|null $kdcab type: varchar(3)
 * @property string|null $kdloc type: varchar(2)
 * @property string|null $kdkas type: varchar(2)
 * @property string|null $kddidik type: varchar(2)
 * @property string|null $kdkerja type: varchar(3)
 * @property string|null $nocifgrp type: varchar(8)
 * @property string|null $stsrec type: varchar(1)
 * @property string|null $inpuser type: varchar(10)
 * @property string|null $chguser type: varchar(10)
 * @property string|null $autuser type: varchar(10)
 * @property string|null $tglinp type: varchar(14)
 * @property string|null $tglchg type: varchar(14)
 * @property string|null $tglaut type: varchar(14)
 * @property string|null $devinp type: varchar(10)
 * @property string|null $devchg type: varchar(10)
 * @property string|null $devaut type: varchar(10)
 * @property string|null $wni type: varchar(1)
 * @property string|null $kdhasil type: varchar(1)
 * @property string|null $kdgelar type: varchar(4)
 * @property string|null $namasid type: varchar(100)
 * @property string|null $din type: varchar(20)
 * @property string|null $bmpk1 type: varchar(1)
 * @property string|null $bmpk2 type: varchar(1)
 * @property string|null $golcustsid type: varchar(3)
 * @property string|null $kdbank type: varchar(10)
 * @property string|null $kdrisk type: varchar(1)
 * @property string|null $kdmitra type: varchar(20)
 * @property string|null $kdkolek type: varchar(10)
 * @property string|null $sandidatidom type: varchar(5)
 * @property string|null $stssms type: varchar(1)
 * @property string|null $kdsumbhasil type: varchar(5)
 * @property string|null $qtytanggungan type: numeric(5)
 * @property string|null $stspisahharta type: varchar(1)
 * @property string|null $kdarearmh type: varchar(4)
 * @property string|null $kdareaktr type: varchar(4)
 * @property string|null $kdareafax type: varchar(4)
 * @property string|null $kdhubbank type: varchar(4)
 * @property string|null $golcustslik type: varchar(15)
 * @property string|null $kdgroupusaha type: varchar(6)
 * @property string|null $stskaitsid type: nchar(20)
 * @property string|null $dd_longitude type: numeric(9)
 * @property string|null $dd_latitude type: numeric(9)
 * @property string|null $dms_longitude_1 type: numeric(5)
 * @property string|null $dms_longitude_2 type: numeric(5)
 * @property string|null $dms_longitude_3 type: numeric(5)
 * @property string|null $dms_latitude_1 type: numeric(5)
 * @property string|null $dms_latitude_2 type: numeric(5)
 * @property string|null $dms_latitude_3 type: numeric(5)
 * @property string|null $hubkait type: char(1)
 * @property string|null $stskaryawan type: char(1)
 * @property string|null $lb_golcust type: varchar(5)
 * @property string|null $nokk type: varchar(17)
 * @property string|null $rtrw type: varchar(10)
 * @property string|null $provinsi type: varchar(50)
 * @property string|null $jku type: varchar(3)
 * @property string|null $rating type: varchar(3)
 * @property string|null $pemeringkat type: varchar(3)
 */
class Mcif extends MciBaseModel
{
    /**
     * Nama tabel (case-sensitive di SQL Server).
     */
    protected $table = 'mCIF';

    /**
     * Daftar LENGKAP kolom sesuai database (100 kolom).
     * Model ini read-only, $fillable hanya untuk dokumentasi & IDE helper.
     *
     * @var array<int,string>
     */
    protected $fillable = [
        'nocif',
        'nm',
        'nmkecil',
        'agama',
        'golcust',
        'golcustbi',
        'jnsbh',
        'npwp',
        'sandibank',
        'stscust',
        'jnsid',
        'noid',
        'tglid',
        'tglexpid',
        'penerbit',
        'stskawin',
        'sex',
        'tmplhr',
        'tgllhr',
        'email',
        'alamat',
        'kelurahan',
        'kecamatan',
        'kota',
        'kdpos',
        'telprmh',
        'telpktr',
        'fax',
        'hp',
        'sandilok',
        'sandidati',
        'kdwil',
        'nmibu',
        'stskait',
        'aoref',
        'aohand',
        'segmen',
        'glb',
        'tglbuka',
        'tgltutup',
        'alasan',
        'vip',
        'stsrestr',
        'kdcab',
        'kdloc',
        'kdkas',
        'kddidik',
        'kdkerja',
        'nocifgrp',
        'stsrec',
        'inpuser',
        'chguser',
        'autuser',
        'tglinp',
        'tglchg',
        'tglaut',
        'devinp',
        'devchg',
        'devaut',
        'wni',
        'kdhasil',
        'kdgelar',
        'namasid',
        'din',
        'bmpk1',
        'bmpk2',
        'golcustsid',
        'kdbank',
        'kdrisk',
        'kdmitra',
        'kdkolek',
        'sandidatidom',
        'stssms',
        'kdsumbhasil',
        'qtytanggungan',
        'stspisahharta',
        'kdarearmh',
        'kdareaktr',
        'kdareafax',
        'kdhubbank',
        'golcustslik',
        'kdgroupusaha',
        'stskaitsid',
        'dd_longitude',
        'dd_latitude',
        'dms_longitude_1',
        'dms_longitude_2',
        'dms_longitude_3',
        'dms_latitude_1',
        'dms_latitude_2',
        'dms_latitude_3',
        'hubkait',
        'stskaryawan',
        'lb_golcust',
        'nokk',
        'rtrw',
        'provinsi',
        'jku',
        'rating',
        'pemeringkat',
    ];

    /**
     * Casting tipe kolom (non-string saja).
     *
     * @var array<string,string>
     */
    protected $casts = [
        'qtytanggungan' => 'decimal:2',
        'dd_longitude' => 'decimal:2',
        'dd_latitude' => 'decimal:2',
        'dms_longitude_1' => 'decimal:2',
        'dms_longitude_2' => 'decimal:2',
        'dms_longitude_3' => 'decimal:2',
        'dms_latitude_1' => 'decimal:2',
        'dms_latitude_2' => 'decimal:2',
        'dms_latitude_3' => 'decimal:2',
    ];

    /**
     * RELASI: Account Officer (Marketing)
     */
    public function ao(): BelongsTo
    {
        return $this->belongsTo(Ao::class, 'aohand', 'kdao');
    }

    /**
     * RELASI: Cabang
     */
    public function cabang(): BelongsTo
    {
        return $this->belongsTo(Cabang::class, 'kdloc', 'kdloc');
    }

    /**
     * RELASI: Wilayah
     */
    public function wilayah(): BelongsTo
    {
        return $this->belongsTo(Wilayah::class, 'kdwil', 'kodewil');
    }

    /**
     * RELASI: Segmen Pasar
     */
    public function segmenPasar(): BelongsTo
    {
        return $this->belongsTo(Segmen::class, 'segmen', 'kdseg');
    }

    /**
     * RELASI PORTOFOLIO: Tabungan
     */
    public function tabungan(): HasMany
    {
        return $this->hasMany(Toftabb::class, 'nocif', 'nocif');
    }

    /**
     * RELASI PORTOFOLIO: Deposito
     */
    public function deposito(): HasMany
    {
        return $this->hasMany(Tofdep::class, 'nocif', 'nocif');
    }

    /**
     * RELASI PORTOFOLIO: Pembiayaan
     */
    public function pembiayaan(): HasMany
    {
        return $this->hasMany(Toflmb::class, 'nocif', 'nocif');
    }
}
