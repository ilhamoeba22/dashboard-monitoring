<?php

namespace App\Models\Mci\Agunan;

use App\Models\Mci\MciBaseModel;

/**
 * Model MCI: A01
 * --------------------------------------------------------------------------
 * Domain   : Agunan / Jaminan
 * Tabel    : [dbo].[A01]
 * Kolom    : 28
 * Sumber   : Mapping_MCI_MAR26_01042026.sql / MCI_MAR26_01042026.xlsx
 *
 * CATATAN:
 *  - Model ini READ-ONLY (lihat trait ReadOnlyModel di MciBaseModel).
 *  - Koneksi default `dashboard_data` (SQL Server), otomatis ke DB bulan terbaru.
 *  - Kolom dengan spasi/karakter khusus diakses via:
 *    $model->getAttribute('NAMA KOLOM')  atau  $model->{'NAMA KOLOM'}.
 *
 * COLUMN [FLAG DETAIL] -> string|null (varchar(255))
 * COLUMN [KODE REGISTER / NOMOR AGUNAN] -> string|null (varchar(255))
 * COLUMN [NOMOR REKENING FASILITAS] -> string|null (varchar(255))
 * COLUMN [NOMOR CIF DEBITUR] -> string|null (varchar(255))
 * COLUMN [KODE JENIS SEGMEN FASILITAS] -> string|null (varchar(255))
 * COLUMN [KODE STATUS AGUNAN] -> string|null (varchar(255))
 * COLUMN [KODE JENIS AGUNAN] -> string|null (varchar(255))
 * COLUMN [PERINGKAT AGUNAN] -> string|null (varchar(255))
 * COLUMN [KODE LEMBAGA PEMERINGKAT] -> string|null (varchar(255))
 * COLUMN [KODE JENIS PENGIKATAN] -> string|null (varchar(255))
 * COLUMN [TANGGAL PENGIKATAN] -> string|null (varchar(255))
 * COLUMN [NAMA PEMILIK AGUNAN] -> string|null (varchar(255))
 * COLUMN [BUKTI KEPEMILIKAN] -> string|null (varchar(255))
 * COLUMN [ALAMAT AGUNAN] -> string|null (varchar(255))
 * COLUMN [KODE KAB/KOTA (DATI 2) LOKASI AGUNAN] -> string|null (varchar(255))
 * COLUMN [NILAI AGUNAN SESUAI NJOP] -> string|null (numeric(9))
 * COLUMN [NILAI AGUNAN MENURUT LJK] -> string|null (numeric(9))
 * COLUMN [TANGGAL PENILAIAN LJK] -> string|null (varchar(255))
 * COLUMN [NILAI AGUNAN PENILAI INDEPENDEN] -> string|null (numeric(9))
 * COLUMN [NAMA PENILAI INDEPENDEN] -> string|null (varchar(255))
 * COLUMN [TANGGAL PENILAIAN PENILAI INDEPENDEN] -> string|null (varchar(255))
 * COLUMN [STATUS PARIPASU] -> string|null (varchar(255))
 * COLUMN [PERSENTASE PARIPASU] -> string|null (numeric(9))
 * COLUMN [STATUS KREDIT JOIN] -> string|null (varchar(255))
 * @property string|null $DIASURANSIKAN  type: varchar(255)
 * @property string|null $KETERANGAN  type: varchar(255)
 * COLUMN [KODE KANTOR CABANG] -> string|null (varchar(255))
 * COLUMN [OPERASI DATA] -> string|null (varchar(255))
 */
class A01 extends MciBaseModel
{
    /**
     * Nama tabel (case-sensitive di SQL Server).
     */
    protected $table = 'A01';

    /**
     * Daftar LENGKAP kolom sesuai database (28 kolom).
     * Model ini read-only, $fillable hanya untuk dokumentasi & IDE helper.
     *
     * @var array<int,string>
     */
    protected $fillable = [
        'FLAG DETAIL',
        'KODE REGISTER / NOMOR AGUNAN',
        'NOMOR REKENING FASILITAS',
        'NOMOR CIF DEBITUR',
        'KODE JENIS SEGMEN FASILITAS',
        'KODE STATUS AGUNAN',
        'KODE JENIS AGUNAN',
        'PERINGKAT AGUNAN',
        'KODE LEMBAGA PEMERINGKAT',
        'KODE JENIS PENGIKATAN',
        'TANGGAL PENGIKATAN',
        'NAMA PEMILIK AGUNAN',
        'BUKTI KEPEMILIKAN',
        'ALAMAT AGUNAN',
        'KODE KAB/KOTA (DATI 2) LOKASI AGUNAN',
        'NILAI AGUNAN SESUAI NJOP',
        'NILAI AGUNAN MENURUT LJK',
        'TANGGAL PENILAIAN LJK',
        'NILAI AGUNAN PENILAI INDEPENDEN',
        'NAMA PENILAI INDEPENDEN',
        'TANGGAL PENILAIAN PENILAI INDEPENDEN',
        'STATUS PARIPASU',
        'PERSENTASE PARIPASU',
        'STATUS KREDIT JOIN',
        'DIASURANSIKAN',
        'KETERANGAN',
        'KODE KANTOR CABANG',
        'OPERASI DATA',
    ];

    /**
     * Casting tipe kolom (non-string saja).
     *
     * @var array<string,string>
     */
    protected $casts = [
        'NILAI AGUNAN SESUAI NJOP' => 'decimal:2',
        'NILAI AGUNAN MENURUT LJK' => 'decimal:2',
        'NILAI AGUNAN PENILAI INDEPENDEN' => 'decimal:2',
        'PERSENTASE PARIPASU' => 'decimal:2',
    ];
}
