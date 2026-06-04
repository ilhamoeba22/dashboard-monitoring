# CKPN (Cadangan Kerugian Penurunan Nilai) & PSAK 71

## Overview

PSAK 71 (berlaku 1 Januari 2020) menggantikan PSAK 55 dan membawa perubahan fundamental
dari model **Incurred Loss** ke model **Expected Credit Loss (ECL)**.

---

## 1. Klasifikasi Instrumen Keuangan (PSAK 71)

### 3 Kategori Utama Aset Keuangan
```
1. FVTPL (Fair Value Through Profit or Loss) — Nilai Wajar melalui Laba Rugi
   - Diperdagangkan untuk dijual dalam waktu dekat
   - Tidak memenuhi syarat FVOCI atau amortized cost

2. FVOCI (Fair Value Through Other Comprehensive Income) — Nilai Wajar melalui OCI
   - Model bisnis: Hold to Collect AND Sell
   - Arus kas sesuai SPPI test
   - Surat berharga yang diklasifikasikan AFS sebelumnya

3. Amortized Cost (Biaya Perolehan Diamortisasi)
   - Model bisnis: Hold to Collect
   - Arus kas sesuai SPPI test
   - Mayoritas PEMBIAYAAN masuk kategori ini
```

### SPPI Test (Solely Payments of Principal and Interest)
```
Arus kas kontraktual harus HANYA berupa:
  - Pembayaran pokok (principal)
  - Bunga/margin atas pokok yang belum dibayar

Untuk bank syariah:
  - Murabahah: SPPI terpenuhi (margin tetap/proporsional)
  - Mudharabah: SPPI TIDAK terpenuhi (bagi hasil variabel dari usaha)
    → Diklasifikasi FVTPL atau FVOCI (tergantung kebijakan)
  - Musyarakah: SPPI TIDAK terpenuhi (serupa Mudharabah)
  - Ijarah: SPPI terpenuhi (ujrah tetap)
  - Istishna': SPPI terpenuhi

IMPLIKASI: Pembiayaan Mudharabah & Musyarakah tidak masuk Amortized Cost,
sehingga tidak terkena CKPN ECL model yang sama dengan Murabahah.
```

---

## 2. Model ECL (Expected Credit Loss) — 3-Stage Model

### Stage Classification
```
Stage 1 — Tidak ada peningkatan risiko kredit signifikan (SICR)
  → CKPN = 12-Month ECL
  → Bunga dihitung atas Gross Carrying Amount

Stage 2 — Ada SICR sejak pengakuan awal, belum credit-impaired
  → CKPN = Lifetime ECL
  → Bunga dihitung atas Gross Carrying Amount

Stage 3 — Credit-impaired (NPF / default)
  → CKPN = Lifetime ECL
  → Bunga dihitung atas Net Carrying Amount (setelah CKPN)
  → Di bank syariah: tidak ada pengakuan pendapatan/margin
    karena prinsip kehati-hatian

Simplification (PSAK 71 par 5.5.15) — untuk piutang tanpa komponen pembiayaan
signifikan (< 12 bulan):
  → Langsung Lifetime ECL tanpa staging
  → Umumnya digunakan untuk piutang fee-based
```

### SICR (Significant Increase in Credit Risk) — Kriteria Umum
```
Indikator kuantitatif:
  - Penurunan rating internal lebih dari X notch
  - PD (Probability of Default) meningkat lebih dari X% relatif
  - Tunggakan > 30 hari (rebuttable presumption)

Indikator kualitatif:
  - Perubahan negatif kondisi keuangan debitur
  - Restrukturisasi akibat kesulitan keuangan
  - Watch list / Special Mention
  - Industri/sektor mengalami tekanan sistemik
  - Jaminan memburuk nilainya

Low credit risk exemption (PSAK 71 par 5.5.10):
  Jika risiko kredit masih rendah pada tanggal pelaporan → tetap Stage 1
  (harus didokumentasikan kebijakan bank)
```

---

## 3. Komponen ECL

### Formula ECL
```
ECL = PD × LGD × EAD × DF

PD  = Probability of Default (kemungkinan gagal bayar)
LGD = Loss Given Default (kerugian jika terjadi default) = 1 - Recovery Rate
EAD = Exposure at Default (exposure saat default, termasuk off-balance sheet)
DF  = Discount Factor = 1 / (1 + Effective Interest Rate)^n

Untuk Lifetime ECL:
  ECL = Σ (PD_t × LGD_t × EAD_t × DF_t) untuk t = 1 s/d maturity

Untuk 12-Month ECL:
  ECL = PD_12bulan × LGD × EAD × DF
```

### Pendekatan Perhitungan yang Umum Digunakan Bank

#### Pendekatan Individual (Significant Exposure)
```
- Digunakan untuk pembiayaan korporasi besar (umumnya > Rp 10-50 miliar)
- Cash Flow Projection: proyeksi arus kas masa depan dari debitur
- Nilai Jaminan: NJOP, nilai likuidasi, haircut berdasarkan jenis agunan
- Formula: CKPN = GCA - PV(Arus Kas yang Diharapkan + Nilai Agunan)

Haircut Agunan yang Lazim (sesuai regulasi & best practice):
  - Tanah: 80-90% nilai wajar
  - Bangunan komersial: 70-80%
  - Mesin/peralatan: 50-60%
  - Kendaraan: 50-70%
  - Piutang/Persediaan: 50-60%
  - Deposito/SBN: 95-100%
```

#### Pendekatan Kolektif (Collective Assessment)
```
- Digunakan untuk portofolio homogen (KPR, KKB, UMKM, kartu kredit)
- Metode:
  a. Migration Analysis (Roll Rate)
  b. Vintage Analysis
  c. Probability Model (Logistic Regression, Machine Learning)
  d. Expert Judgment dengan historical loss rate

Migration Matrix (Roll Rate) — contoh:
  Dari\Ke  Kol 1  Kol 2  Kol 3  Kol 4  Kol 5  Lunas
  Kol 1    89%    8%     1%     0.5%   0.3%   1.2%
  Kol 2    15%    68%    12%    3%     1%     1%
  Kol 3    5%     20%    55%    15%    4%     1%
  ...

PD derivasi dari migration matrix:
  PD 12-bulan = probabilitas berpindah ke Kol 3, 4, atau 5 dalam 12 bulan
  PD Lifetime = probabilitas berpindah ke Kol 3, 4, atau 5 sepanjang sisa tenor
```

---

## 4. Forward-Looking Information (FLI)

```
PSAK 71 mewajibkan penggunaan informasi masa depan yang reasonable & supportable:

Faktor Makroekonomi Umum yang Digunakan:
  - Pertumbuhan GDP (nasional, sektoral)
  - Tingkat Pengangguran
  - Harga Komoditas (untuk sektor pertanian, perkebunan, pertambangan)
  - Suku Bunga / BI Rate (untuk perbandingan biaya modal)
  - Nilai Tukar Rupiah (untuk pembiayaan valuta asing)
  - Harga Properti (untuk pembiayaan KPR & properti komersial)
  - Indeks PMI (Purchasing Manager Index) untuk sektor manufaktur

Skenario Multiple Scenarios (umumnya 3 skenario):
  1. Base Case  : probabilitas ~50-60%, asumsi konsensus pasar
  2. Upside     : probabilitas ~20-25%, kondisi lebih baik
  3. Downside   : probabilitas ~20-25%, kondisi lebih buruk

ECL_final = P(Base) × ECL_base + P(Up) × ECL_up + P(Down) × ECL_down

Sumber FLI yang dapat digunakan:
  - Proyeksi makro Bank Indonesia
  - Proyeksi IMF World Economic Outlook
  - Proyeksi internal tim ekonom bank
  - Laporan OJK/BPS
```

---

## 5. CKPN di Laporan Keuangan

### Pengakuan Awal
```
Saat pengakuan awal aset keuangan (hari 1):
  → Langsung dibentuk CKPN 12-Month ECL (Stage 1)
  → Diakui sebagai beban di laba rugi
  → Jurnal: Dr. Beban CKPN / Cr. CKPN (kontra aset)
```

### Penyajian di Neraca
```
Pembiayaan Murabahah                   Rp xxx
  (-) CKPN Murabahah                  (Rp xxx)
Pembiayaan Murabahah - Neto            Rp xxx

Piutang Murabahah                      Rp xxx
  (-) CKPN Piutang Murabahah          (Rp xxx)
Piutang Murabahah - Neto               Rp xxx
```

### Rekonsiliasi CKPN (Wajib Diungkapkan di Catatan)
```
Saldo CKPN Awal                        Rp xxx
+ Pembentukan CKPN Periode Berjalan    Rp xxx
+ Unwinding Discount                   Rp xxx
- Pemulihan CKPN                      (Rp xxx)
- Penghapusan (Write-off)             (Rp xxx)
+/- Selisih Kurs                      +/- Rp xxx
= Saldo CKPN Akhir                     Rp xxx
```

### Rekonsiliasi per Stage
```
Juga wajib diungkapkan:
  Stage 1    : 12-Month ECL
  Stage 2    : Lifetime ECL (Non-Credit Impaired)
  Stage 3    : Lifetime ECL (Credit Impaired)
  Total CKPN
```

---

## 6. Restrukturisasi & Implikasinya pada CKPN

```
Restrukturisasi Karena Kesulitan Keuangan:
  → Termasuk SICR → pindah ke Stage 2 minimal
  → Debitur yang di-write-off lalu pulih kembali → Stage 3 tetap

Restrukturisasi COVID-19 (POJK 11/2020, diperpanjang beberapa kali):
  → Pembiayaan restrukturisasi COVID dikecualikan dari penurunan kolektibilitas
  → CKPN tetap dihitung secara PSAK 71 (tidak ada pengecualian CKPN)
  → Bank wajib memantau "perbaikan kondisi" dan meng-upgrade stage jika layak

Cure Period (Probation Period):
  → Setelah restrukturisasi, umumnya perlu 12 bulan observasi sebelum
    di-upgrade dari Stage 2 ke Stage 1
```

---

## 7. CKPN vs PPAP — Perbandingan

| Aspek | PPAP (Pra-2020) | CKPN PSAK 71 (Post-2020) |
|---|---|---|
| Model | Incurred Loss | Expected Credit Loss (ECL) |
| Basis | Kolektibilitas (sudah terjadi) | Probabilitas masa depan |
| Staging | Tidak ada | 3 Stage |
| Informasi Forward-Looking | Tidak | Ya (wajib) |
| Nilai Agunan | Dipotong dari dasar PPAP | Bagian dari LGD |
| Volatilitas Beban | Relatif stabil | Lebih fluktuatif (FLI) |
| Pengungkapan | Lebih sederhana | Jauh lebih ekstensif |
| Regulasi | SE BI / SE OJK | PSAK 71 (DSAS-IAI) |

---

## 8. Contoh Perhitungan CKPN Sederhana

```
Kasus: Pembiayaan Murabahah KPR, Stage 1

GCA (Gross Carrying Amount) = Rp 500.000.000
PD 12-bulan                  = 1.5%
LGD                          = 40% (LTV 70%, haircut 30%)
EAD                          = Rp 500.000.000 (fully drawn)
DF                           = 1/(1+0.08)^0.5 ≈ 0.962 (tenor sisa 6 bulan)

ECL_12month = 1.5% × 40% × 500.000.000 × 0.962
            = 0.015 × 0.4 × 500.000.000 × 0.962
            = Rp 2.886.000

Jurnal pembentukan:
  Dr. Beban CKPN         Rp 2.886.000
    Cr. CKPN - Murabahah              Rp 2.886.000
```
