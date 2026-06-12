# Funding Query Inventory

Dokumen ini menjadi pegangan implementasi menu Funding agar query tetap presisi terhadap sumber legacy `C:\laragon\www\dashboard\mdb-dashboard` dan CBS/LAPORAN19. UI boleh didesain ulang, tetapi angka harus berasal dari query operasional yang jelas.

## Prinsip Data

- Sumber utama realtime: database CBS aktif melalui tabel operasional `TOFTABB`, `TOFTABC`, `TOFTABEOM`, `TOFDEP`, `TOFDEPEOM`, `TOFDEPDEL`, `H_GLTRN`, `TARGETAO`, dan master `AO`, `CABANG`, `WILAYAH`, `SETUPTAB`, `SETUPDEP`, `mCIF`.
- Sumber historis: database snapshot bulanan dari konfigurasi CBS/LAPORAN19, digunakan untuk laporan history ketika periode tidak berada di database aktif.
- Nominal tidak dibulatkan. Persentase maksimal 2 digit di belakang koma.
- Query dashboard baru harus bisa ditelusuri balik ke legacy MDB atau hasil tracing CBS/LAPORAN19.

## Urutan Pengumpulan Query

1. Core DPK: nominatif tabungan dan deposito sebagai fondasi seluruh laporan.
2. Rekapitulasi: AO, cabang, wilayah, produk, dan pembukaan baru.
3. Perkembangan/target: EOM historis plus live bulan berjalan.
4. Mutasi/transaksi: debet, kredit, netto harian/bulanan.
5. Risiko funding: dormant, jatuh tempo, konsentrasi CIF, deposan terbesar, dan concentration risk.
6. Bagi hasil: nisbah, bagi hasil hitung/bayar, pajak, accrual/realisasi.
7. Gap CBS: laporan yang hanya ada di menu LAPORAN19 perlu SQL tracing.

## Query Inti Tabungan

### Nominatif Tabungan Aktif

Sumber legacy: `C:\laragon\www\dashboard\mdb-dashboard\app\Models\Saving\SavingNominative.php`

```sql
FROM TOFTABB a
LEFT JOIN TOFTABC b ON a.notab = b.notab
LEFT JOIN AO c ON a.kodeaoh = c.kdao
LEFT JOIN CABANG d ON a.kodeloc = d.kdloc
LEFT JOIN WILAYAH e ON a.kdwil = e.kodewil
LEFT JOIN SETUPTAB f ON a.kodeprd = f.kodeprd
LEFT JOIN mCIF g ON a.nocif = g.nocif
WHERE a.stsrec <> 'C'
  AND (a.tgltutup = '' OR a.tgltutup IS NULL)
ORDER BY c.nmao ASC, b.fnama ASC
```

Kolom utama: `notab`, `fnama`, `alamat`, `tglbuka`, `tglbayar`, `bhbayar`, `taxbayar`, `nisbah`, `spcnisbah`, `rate`, `sahirrp`, `saldoavg`, produk, AO, cabang, wilayah, umur.

### Rekapitulasi Tabungan

Sumber legacy: `C:\laragon\www\dashboard\mdb-dashboard\app\Http\Controllers\Saving\SavingRekapitulasiController.php`

Dimensi:

- AO: `TOFTABB.kodeaoh = AO.kdao`
- Wilayah: `TOFTABB.kdwil = WILAYAH.kodewil`
- Produk: `TOFTABB.kodeprd = SETUPTAB.kodeprd`

Agregat:

```sql
COUNT(a.notab) AS jml,
SUM(a.sawalrp) AS saldo_awal,
SUM(a.sahirrp) AS saldo_akhir,
SUM(a.saldoavg) AS saldo_rata,
SUM(a.bhhtg) AS bagi_hasil_hitung,
SUM(a.bhbayar) AS bagi_hasil_bayar,
SUM(a.taxbayar) AS pajak_bayar
```

Filter utama:

```sql
WHERE a.stsrec <> 'C'
```

Untuk rekap produk legacy juga mensyaratkan:

```sql
AND a.tgltutup = ''
```

### Pembukaan Baru Tabungan

Sumber legacy: `C:\laragon\www\dashboard\mdb-dashboard\app\Http\Controllers\Saving\SavingRekapitulasiController.php`

```sql
FROM TOFTABB
JOIN AO ON TOFTABB.kodeaoh = AO.kdao
JOIN MCIF ON TOFTABB.nocif = MCIF.nocif
JOIN WILAYAH ON TOFTABB.kdwil = WILAYAH.kodewil
WHERE TOFTABB.stsrec IN ('A', 'N')
  AND TOFTABB.tglbuka BETWEEN @YearStart AND @YearEnd
```

Segmentasi:

```sql
CASE
  WHEN TOFTABB.tglbuka = MCIF.tglbuka THEN 'New-to-Bank (NTB)'
  ELSE 'Existing Customer (EC)'
END
```

### Dormant Tabungan

Sumber legacy: `C:\laragon\www\dashboard\mdb-dashboard\app\Http\Controllers\Saving\SavingDoormantController.php`

Tanggal transaksi akhir:

```sql
CASE
  WHEN a.tgltrnakh IS NULL OR a.tgltrnakh = '' OR a.tgltrnakh = '0'
  THEN b.tgltrnakh
  ELSE a.tgltrnakh
END
```

Detail:

```sql
FROM TOFTABC a
LEFT JOIN TOFTABB b ON a.notab = b.notab
LEFT JOIN AO c ON b.kodeaoh = c.kdao
LEFT JOIN WILAYAH d ON b.kdwil = d.kodewil
WHERE <tgl_transaksi_akhir> < @CutOffDate
```

Cut-off legacy: 6 bulan dan 12 bulan.

### Mutasi Tabungan

Sumber legacy: `C:\laragon\www\dashboard\mdb-dashboard\app\Http\Controllers\Saving\SavingTransactionController.php`

```sql
FROM H_GLTRN b
JOIN TOFTABB a ON a.notab = b.noacclawan
WHERE b.trnuser <> 'OPREOD'
```

Dimensi: AO, cabang, wilayah, produk. Agregat per periode:

```sql
SUM(CASE WHEN b.dc = 'D' THEN b.nominal ELSE 0 END) AS debet,
SUM(CASE WHEN b.dc = 'C' THEN b.nominal ELSE 0 END) AS kredit,
SUM(CASE WHEN b.dc = 'D' THEN b.nominal ELSE -b.nominal END) AS netto
```

### Target Tabungan

Sumber legacy: `C:\laragon\www\dashboard\mdb-dashboard\app\Http\Controllers\Saving\SavingTargetController.php`

Target:

```sql
SELECT RTRIM(kdao) AS kdao,
       CAST(thn AS VARCHAR) + REPLACE(STR(bln, 2), ' ', '0') AS periode,
       SUM(tab) AS tab
FROM TARGETAO
WHERE thn = @Year
GROUP BY kdao, thn, bln
```

Realisasi:

```sql
SELECT RTRIM(b.kodeaoh) AS kdao, a.periode, SUM(a.sahirrp) AS nilai
FROM TOFTABEOM a
INNER JOIN TOFTABB b ON a.notab = b.notab
WHERE LEFT(a.periode, 4) = @Year
  AND a.periode < @CurrentPeriod
GROUP BY b.kodeaoh, a.periode
UNION ALL
SELECT RTRIM(b.kodeaoh), @CurrentPeriod, SUM(b.sahirrp)
FROM TOFTABB b
GROUP BY b.kodeaoh
```

## Query Inti Deposito

### Nominatif Deposito Aktif

Sumber legacy: `C:\laragon\www\dashboard\mdb-dashboard\app\Models\Deposit\Tofdep.php`

```sql
FROM TOFDEP a
LEFT JOIN mCIF g ON a.nocif = g.nocif
LEFT JOIN AO c ON a.kodeaoh = c.kdao
LEFT JOIN SETUPDEP f ON a.kdprd = f.kdprd
LEFT JOIN CABANG h ON a.kdloc = h.kdloc
LEFT JOIN WILAYAH i ON a.kdwil = i.kodewil
WHERE a.stsrec <> 'C'
  AND a.tgltutup = ''
ORDER BY a.nama ASC
```

Kolom utama: seluruh kolom `TOFDEP`, nama nasabah, AO, produk, cabang, wilayah, umur.

### Rekapitulasi Deposito

Sumber legacy: `C:\laragon\www\dashboard\mdb-dashboard\app\Http\Controllers\Deposit\DepositRekapitulasiController.php`

Agregat:

```sql
COUNT(TOFDEP.nodep) AS jml,
SUM(TOFDEP.nomawal) AS nomawal,
SUM(TOFDEP.nomrp) AS nomrp,
SUM(TOFDEP.saldrata1) AS saldorata,
SUM(TOFDEP.bnghtg) AS bagi_hasil_hitung,
SUM(TOFDEP.bngbayar) AS bagi_hasil_bayar,
SUM(TOFDEP.tax) AS pajak
```

Dimensi:

- AO: `TOFDEP.kodeaoh = AO.kdao`
- Wilayah: `TRIM(TOFDEP.kdwil) = TRIM(WILAYAH.kodewil)`
- Produk: `TOFDEP.kdprd = SETUPDEP.kdprd`

Filter:

```sql
WHERE TOFDEP.stsrec <> 'C'
```

### Deposito Baru Periode Berjalan

Sumber legacy: `C:\laragon\www\dashboard\mdb-dashboard\app\Http\Controllers\Deposit\DepositRekapitulasiController.php`

```sql
WHERE stsrec <> 'C'
  AND tgltutup = ''
  AND tglbuka BETWEEN @StartOfMonth AND @EndOfMonth
```

### Target Deposito

Sumber legacy: `C:\laragon\www\dashboard\mdb-dashboard\app\Http\Controllers\Deposit\DepositTargetController.php`

Target:

```sql
SELECT kdao, bln, SUM(dep) AS total_target
FROM TARGETAO
WHERE thn = @CurrentYear
GROUP BY kdao, bln
```

Realisasi EOM:

```sql
SELECT TOFDEP.kodeaoh, TOFDEPEOM.periode, SUM(TOFDEPEOM.nomrp) AS nilai
FROM TOFDEPEOM
JOIN TOFDEP ON TOFDEPEOM.nodep = TOFDEP.nodep
WHERE SUBSTRING(TOFDEPEOM.periode, 1, 4) = @CurrentYear
  AND TOFDEPEOM.periode < @CurrentPeriod
GROUP BY TOFDEP.kodeaoh, TOFDEPEOM.periode
```

Realisasi live:

```sql
SELECT kodeaoh, SUM(nomrp) AS nilai
FROM TOFDEP
WHERE kodeaoh IS NOT NULL
GROUP BY kodeaoh
```

### Perkembangan Deposito

Sumber legacy: `C:\laragon\www\dashboard\mdb-dashboard\app\Http\Controllers\Deposit\DepositPerkembanganController.php`

Basis histori:

```sql
FROM TOFDEPEOM
JOIN TOFDEP ON TOFDEPEOM.nodep = TOFDEP.nodep
WHERE TOFDEPEOM.periode IN (@PeriodList)
GROUP BY TOFDEP.<dimension>, TOFDEPEOM.periode
```

Live bulan berjalan:

```sql
SELECT <dimension>, @CurrentMonthPeriod AS periode, SUM(nomrp) AS total
FROM TOFDEP
WHERE stsrec = 'A'
GROUP BY <dimension>
```

Dimensi: produk, AO, cabang, wilayah.

### Mutasi Deposito

Sumber legacy: `C:\laragon\www\dashboard\mdb-dashboard\app\Models\Deposit\DepositTransaction.php`

```sql
SELECT kodeaoh, kdloc, kdwil, kdprd, nomrp, LEFT(tglbuka, 6) AS periode, 'M' AS status
FROM TOFDEP
WHERE LEFT(tglbuka, 4) = @Year
UNION ALL
SELECT kodeaoh, kdloc, kdwil, kdprd, nomrp, LEFT(tglcair, 6) AS periode, 'C' AS status
FROM TOFDEPDEL
WHERE LEFT(tglcair, 4) = @Year
```

Agregat per bulan:

```sql
SUM(CASE WHEN status = 'M' THEN nomrp ELSE 0 END) AS masuk,
SUM(CASE WHEN status = 'C' THEN nomrp ELSE 0 END) AS cair,
SUM(CASE WHEN status = 'M' THEN nomrp ELSE -nomrp END) AS netto
```

## Query Overview DPK

Sumber legacy: `C:\laragon\www\dashboard\mdb-dashboard\app\Http\Controllers\Dashboard\DashboardController.php`

### Tabungan

Realtime bulan berjalan menggunakan `TOFTABB` dan historis menggunakan `TOFTABEOM`, digabung sebagai `RiwayatNasabah`, lalu dihitung:

```sql
SUM(sahirrp) AS TotalSaldo,
COUNT(DISTINCT notab) AS TotalNOA,
COUNT(DISTINCT kodeaoh) AS TotalAO
```

Grouping legacy memakai:

```sql
GROUPING SETS (
  (periode_yyyymm, nama_cabang),
  (periode_yyyymm)
)
```

### Deposito

Realtime bulan berjalan menggunakan `TOFDEP` dan historis menggunakan `TOFDEPEOM`, lalu dihitung:

```sql
SUM(nomrp) AS TotalSaldo,
SUM(bnghtg) AS TotalBaghas,
COUNT(DISTINCT nodep) AS TotalNOA,
COUNT(DISTINCT kodeaoh) AS TotalAO
```

Grouping legacy sama: cabang dan konsolidasi.

## Gap Yang Perlu SQL Tracing CBS

Menu berikut terlihat di CBS/LAPORAN19 tetapi SQL-nya tidak terbaca langsung dari file `.dwo/.win` PowerBuilder karena file tersimpan binary/compiled:

- Tabungan: sejarah transaksi rekening per group debitur, list saldo tabungan karyawan, saldo harian, range saldo, tabungan pasif, tabungan belum tutup sempurna, tabungan blokir, tabungan khusus, kondisi khusus, premi asuransi, laporan saldo minimum kumulatif rata-rata, point tabungan, setoran via kolektor, rekening virtual account, pembukuan bagi hasil, laporan pajak tabungan, standing instruction, perubahan nisbah, biaya administrasi.
- Deposito: sejarah bagi hasil deposan, maturity profile, total deposito per CIF, rekening transfer bank umum, monitoring bilyet, daftar komitmen rate, deposito bebas PPH 20%, proof sheet titipan bagi hasil, list deposito ber-RESI, hasil dimuka, perhitungan/cadangan/realisasi bagi hasil, pajak bagi hasil, perubahan nisbah.

Tracing disarankan dimulai dari laporan yang paling berdampak:

1. Total Deposito per CIF / Nasabah Terbesar DPK.
2. Maturity Profile dan Deposito Jatuh Tempo.
3. Perhitungan, Cadangan, dan Realisasi Bagi Hasil Deposito.
4. Tabungan Blokir dan Tabungan Pasif/Dormant.
5. Pajak Tabungan/Deposito.

## Rekomendasi Modul Funding

### Struktur Menu Funding Final

Funding sebaiknya tidak dibuat terlalu banyak halaman kecil seperti CBS, karena dashboard ini harus menjadi command center. Menu CBS tetap menjadi referensi logika, sementara web dashboard mengelompokkan laporan berdasarkan kebutuhan keputusan.

| Menu | Tujuan | Isi Utama | Sumber Query |
| --- | --- | --- | --- |
| `/funding` | Executive view DPK | total DPK, total tabungan, total deposito, deposito jatuh tempo, mix DPK, interpretasi likuiditas | `TOFTABB`, `TOFDEP`, `TANGGAL`, `CABANG`, `TOFTABEOM`, `TOFDEPEOM` |
| `/funding/tabungan/nominatif` | Operasional tabungan | nominatif aktif | `TOFTABB`, `TOFTABC`, `AO`, `CABANG`, `WILAYAH`, `SETUPTAB`, `mCIF` |
| `/funding/tabungan/rekapitulasi` | Rekap tabungan | rekap AO/cabang/wilayah/produk | `TOFTABB`, `AO`, `CABANG`, `WILAYAH`, `SETUPTAB` |
| `/funding/tabungan/dormant` | Risiko tabungan pasif | daftar dormant/pasif | `TOFTABC`, `TOFTABB`, `AO`, `WILAYAH` |
| `/funding/deposito/nominatif` | Operasional deposito | nominatif aktif | `TOFDEP`, `mCIF`, `AO`, `SETUPDEP`, `CABANG`, `WILAYAH` |
| `/funding/deposito/rekapitulasi` | Rekap deposito | rekap AO/cabang/wilayah/produk | `TOFDEP`, `AO`, `CABANG`, `WILAYAH`, `SETUPDEP` |
| `/funding/deposito/jatuh-tempo` | Monitoring maturity | deposito jatuh tempo bulan berjalan | `TOFDEP`, `mCIF`, `AO`, `CABANG` |
| `/funding/perkembangan` | Trend Funding | perkembangan bulanan tabungan dan deposito | `TOFTABEOM`, `TOFTABB`, `TOFDEPEOM`, `TOFDEP` |
| `/funding/target` | Target Funding | target vs realisasi tabungan/deposito | `TARGETAO`, `TOFTABEOM`, `TOFTABB`, `TOFDEPEOM`, `TOFDEP` |
| `/funding/mutasi` | Mutasi Funding | mutasi tabungan dan deposito | `H_GLTRN`, `TOFTABB`, `TOFDEP`, `TOFDEPDEL` |
| `/funding/risk` | Risiko dan concentration funding | deposan terbesar, concentration risk per CIF, maturity bucket deposito, dormant/pasif, product mix risk | core tabungan/deposito; sudah tersedia endpoint `/api/v1/funding/risk/overview` |
| `/funding/concentration` | Detail nasabah terbesar | Top 100 deposan, band nominal, share DPK per CIF | `TOFTABB`, `TOFDEP`, `mCIF` |
| `/funding/baghas` | Bagi hasil dan pajak | nisbah, bagi hasil hitung, bagi hasil bayar, pajak tabungan/deposito, top deposan baghas | core `TOFTABB` dan `TOFDEP`; endpoint tahap awal tersedia di `/api/v1/funding/baghas/overview`, accrual/cadangan/realisasi resmi tetap perlu tracing |

### Isi Detail Per Halaman

#### 1. Overview DPK

KPI:

- Total DPK = total tabungan aktif + total deposito aktif.
- Total Tabungan = `SUM(TOFTABB.sahirrp)`.
- Total Deposito = `SUM(TOFDEP.nomrp)`.
- Jumlah rekening = `COUNT(notab)` + `COUNT(nodep)`.
- Deposito jatuh tempo bulan berjalan = deposito aktif dengan `tgljtempo` dalam bulan sistem.

Panel:

- DPK mix: persentase tabungan vs deposito.
- Trend DPK bulanan: EOM history + live bulan berjalan.
- Top AO DPK: gabungan tabungan dan deposito per AO.
- Produk mix: SETUPTAB + SETUPDEP.
- Interpretasi otomatis: dominasi deposito/tabungan, risiko konsentrasi, tekanan jatuh tempo.

#### 2. Tabungan

Tab yang disarankan:

- Nominatif: list rekening aktif.
- Rekapitulasi: AO, cabang, wilayah, produk.
- Perkembangan: trend saldo/NOA dan growth.
- Target: target vs realisasi AO.
- Dormant/Pasif: 6 bulan, 12 bulan, bucket saldo.
- Mutasi: debet, kredit, netto harian/bulanan.

Query inti yang sudah ada dari MDB:

- Nominatif: `SavingNominative::activeNominative()`.
- Rekapitulasi: `SavingRekapitulasiController`.
- Perkembangan: `SavingPerkembangan::getBaseQuery()`.
- Target: `SavingTargetController::getRawQuery()`.
- Dormant: `SavingDoormantController`.
- Mutasi: `SavingTransactionController`.

#### 3. Deposito

Tab yang disarankan:

- Nominatif: bilyet aktif.
- Rekapitulasi: AO, cabang, wilayah, produk.
- Perkembangan: trend nominal/baghas/growth.
- Target: target vs realisasi AO.
- Jatuh Tempo/Maturity: jatuh tempo bulan berjalan dan bucket berikutnya.
- Mutasi: deposito masuk, cair, netto.

Query inti yang sudah ada dari MDB:

- Nominatif: `Tofdep::nominatifWithAge()`.
- Rekapitulasi: `DepositRekapitulasiController`.
- Perkembangan: `DepositPerkembanganController`.
- Target: `DepositTargetController`.
- Mutasi: `DepositTransaction::getUnionQuery()`.

#### 4. Risk Funding

Panel yang relevan:

- Top Depositor / Nasabah Terbesar DPK.
- Total deposito per CIF.
- Total DPK per CIF = tabungan + deposito.
- Concentration ratio top 5/top 10/top 25 terhadap total DPK.
- Maturity bucket deposito: jatuh tempo 0-7, 8-14, 15-30, 31-60, 61-90, >90 hari.
- Dormant/pasif tabungan dan nominal idle.
- Tabungan blokir jika definisi tabel/field sudah tervalidasi dari CBS.

Catatan implementasi:

- Risk Funding tidak boleh mengulang tabel nominatif panjang dari Tabungan/Deposito.
- Fokusnya harus agregasi risiko, ranking, bucket, dan interpretasi otomatis.

#### 5. Bagi Hasil & Pajak

Panel yang relevan:

- Bagi hasil hitung vs bayar tabungan.
- Bagi hasil hitung vs bayar deposito.
- Pajak tabungan dan deposito.
- Nisbah efektif dan special nisbah.
- Deposito bebas PPH 20% jika query CBS sudah terkonfirmasi.
- Accrual/cadangan/realisasi bagi hasil deposito jika SQL tracing sudah tersedia.

Catatan implementasi:

- Untuk tahap awal, gunakan field yang sudah jelas: `TOFTABB.bhhtg`, `TOFTABB.bhbayar`, `TOFTABB.taxbayar`, `TOFDEP.bnghtg`, `TOFDEP.bngbayar`, `TOFDEP.tax`.
- Untuk laporan resmi CBS seperti proof sheet titipan/cadangan/realisasi, jangan dibuat asumsi; harus tracing.

### Standar Navigasi Funding

- Setiap fitur utama harus menjadi route/submenu sendiri, bukan tab horizontal dalam satu halaman.
- Route induk `/funding/tabungan` dan `/funding/deposito` hanya menjadi fallback ke nominatif.
- Halaman berikutnya juga mengikuti standar ini: satu fitur, satu route, satu konteks loading data.

### Implementasi Awal Paling Aman

1. Finalisasi core query Tabungan/Deposito aktif.
2. Tambahkan rekap dimensi AO, cabang, wilayah, produk.
3. Tambahkan target dan perkembangan historis.
4. Tambahkan risk funding yang query-nya sudah bisa diturunkan dari core.
5. Baru tracing CBS untuk laporan bagi hasil dan menu yang SQL-nya belum ada di MDB.

## Roadmap Implementasi

### Fase 1: Presisi Core DPK

- Validasi total tabungan aktif memakai `TOFTABB.sahirrp`.
- Validasi total deposito aktif memakai `TOFDEP.nomrp`.
- Validasi filter aktif/tutup: `stsrec <> 'C'`, `tgltutup = '' OR NULL`, dan field status lain jika terbukti dari CBS.
- Samakan endpoint existing dengan query legacy MDB.

### Fase 2: Tabungan dan Deposito Operasional

- Lengkapi tab nominatif, rekap, perkembangan, target, mutasi.
- Tambahkan filter periode yang konsisten: live database berjalan atau snapshot EOM berdasarkan bulan/tahun.
- Pastikan jika database bulan tidak tersedia, UI menampilkan pesan kosong profesional, bukan error.

### Fase 3: Risk Funding

- Endpoint DPK per CIF dan deposan terbesar tersedia di `/api/v1/funding/risk/overview`.
- Concentration ratio Top 1, Top 5, Top 10, dan Top 25 sudah dihitung dari total DPK realtime.
- Maturity bucket deposito sudah dihitung dari `TOFDEP.tgljtempo`.
- Dormant/pasif tabungan sudah dihitung dari transaksi terakhir `TOFTABC/TOFTABB`.
- Product mix risk sudah dihitung dari `SETUPTAB` dan `SETUPDEP`.

### Fase 4: Bagi Hasil & Pajak

- Endpoint awal dari field core tersedia di `/api/v1/funding/baghas/overview`.
- Angka tabungan memakai `TOFTABB.bhhtg`, `TOFTABB.bhbayar`, `TOFTABB.taxbayar`, `TOFTABB.nisbah`, dan `TOFTABB.rate`.
- Angka deposito memakai `TOFDEP.bnghtg`, `TOFDEP.bngbayar`, `TOFDEP.tax`, `TOFDEP.nisbah`, dan `TOFDEP.equivrate`.
- Dashboard sudah menampilkan ringkasan, produk tabungan, produk deposito, bucket nisbah deposito, dan top deposan berdasarkan bagi hasil bayar.
- Lakukan SQL tracing CBS untuk laporan resmi: perhitungan baghas deposito, cadangan/accrual, realisasi, pajak, bebas PPH.
- Baru aktifkan dashboard baghas final setelah angka hasil tracing cocok.

### Fase 5: Export dan Auditability

- Setiap tab punya Excel multi-sheet sesuai card/tabel aktif.
- PDF ringkas untuk management summary.
- Sertakan metadata periode, database sumber, tanggal sistem CBS, dan catatan filter pada export.
