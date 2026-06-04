# Rasio Keuangan Perbankan Syariah

## 1. Rasio Permodalan

### CAR / KPMM (Capital Adequacy Ratio)
```
CAR = (Modal Inti + Modal Pelengkap) / ATMR × 100%

Komponen Modal Inti (Tier 1):
  - Modal Disetor
  - Tambahan Modal Disetor (Agio Saham)
  - Laba Ditahan
  - Laba Tahun Berjalan (diperhitungkan 50%)
  - Dana Setoran Modal
  - (-) Goodwill & Intangible Assets
  - (-) Shortfall CKPN

Komponen Modal Pelengkap (Tier 2):
  - Instrumen Modal Tier 2
  - CKPN yang dapat diperhitungkan (max 1.25% ATMR Risiko Kredit)
  - Cadangan Umum (max 1.25% ATMR Risiko Kredit)

ATMR Risiko Kredit:
  Setiap aset dikalikan bobot risiko (0%, 20%, 35%, 40%, 50%, 75%, 85%, 100%, 150%)
  Pembiayaan konsumtif KPR: 35% (LTV ≤ 70%) s/d 100%
  Pembiayaan korporasi rated AAA-AA: 20%, A: 50%, BBB-B: 100%, < B: 150%
  Pembiayaan UMKM: 75% (portofolio ritel regulatori)

ATMR Risiko Operasional (Basic Indicator Approach):
  = 15% × Rata-rata Gross Income 3 tahun terakhir (yang positif)

ATMR Risiko Pasar:
  = (Risiko Spesifik + Risiko Umum) × 12.5

Threshold Minimum CAR:
  BUKU 1: 8%
  BUKU 2: 9%
  BUKU 3: 10%
  BUKU 4: 11%
  + Buffer Konservasi Modal: 2.5% (berlaku penuh)
  + Buffer Countercyclical: 0% - 2.5% (ditetapkan OJK)
  + Buffer Sistemik (D-SIB): 1% - 3.5%
```

### Rasio Modal Inti (Tier 1 Ratio)
```
Tier 1 Ratio = Modal Inti / ATMR × 100%
Minimum: 6%
```

---

## 2. Rasio Kualitas Aset

### NPF (Non Performing Financing)
```
NPF Gross = (Kol 3 + Kol 4 + Kol 5) / Total Pembiayaan × 100%
NPF Net   = (Kol 3 + Kol 4 + Kol 5 - CKPN Individual) / Total Pembiayaan × 100%

Kolektibilitas Pembiayaan (POJK No. 40/POJK.03/2019):
  Kol 1 - Lancar       : 0 hari tunggakan
  Kol 2 - DPK          : 1 - 90 hari tunggakan (Dalam Perhatian Khusus)
  Kol 3 - Kurang Lancar: 91 - 120 hari tunggakan
  Kol 4 - Diragukan    : 121 - 180 hari tunggakan
  Kol 5 - Macet        : > 180 hari tunggakan

Untuk Murabahah: dihitung dari tunggakan angsuran pokok/margin
Untuk Mudharabah/Musyarakah: dihitung dari tunggakan bagi hasil & pokok
Untuk Ijarah: dihitung dari tunggakan ujrah

Threshold OJK: NPF Net ≤ 5%
Benchmark industri 2023: NPF Net ≈ 2.0% - 2.5%
```

### Loan at Risk (LAR) / Financing at Risk (FAR)
```
FAR = (Kol 2 + Kol 3 + Kol 4 + Kol 5) / Total Pembiayaan × 100%
(Digunakan dalam konteks restrukturisasi & monitoring portofolio)
```

### CKPN to Total Pembiayaan
```
CKPN Ratio = Total CKPN / Total Pembiayaan × 100%
(Mengukur kecukupan pencadangan secara agregat)

Coverage Ratio = Total CKPN / (Kol 3 + Kol 4 + Kol 5) × 100%
Benchmark: ≥ 100% menunjukkan pencadangan yang prudent
```

---

## 3. Rasio Rentabilitas (Profitabilitas)

### ROA (Return on Assets)
```
ROA = Laba Sebelum Pajak / Rata-rata Total Aset × 100%
Rata-rata Aset = (Aset Awal Periode + Aset Akhir Periode) / 2

Predikat OJK (SE OJK 10/2014):
  Sangat Sehat : ROA > 1.5%
  Sehat        : 1.25% < ROA ≤ 1.5%
  Cukup Sehat  : 0.5% < ROA ≤ 1.25%
  Kurang Sehat : 0% < ROA ≤ 0.5%
  Tidak Sehat  : ROA ≤ 0%
```

### ROE (Return on Equity)
```
ROE = Laba Bersih Setelah Pajak / Rata-rata Modal Inti × 100%
(Mengukur imbal hasil bagi pemegang saham)
```

### NOM (Net Operating Margin) — KHUSUS SYARIAH
```
NOM = Pendapatan Operasional Bersih Setelah Distribusi Bagi Hasil / 
      Rata-rata Aset Produktif × 100%

Pendapatan Operasional Bersih = 
  Pendapatan dari Penyaluran Dana
  - Hak Pihak Ketiga atas Bagi Hasil Dana Syirkah Temporer
  - Beban Operasional Langsung (beban tenaga kerja, dll)

Aset Produktif = Pembiayaan + Penempatan + Surat Berharga + dll

Predikat OJK:
  Sangat Sehat : NOM > 3%
  Sehat        : 2% < NOM ≤ 3%
  Cukup Sehat  : 1.5% < NOM ≤ 2%
  Kurang Sehat : 1% < NOM ≤ 1.5%
  Tidak Sehat  : NOM ≤ 1%

CATATAN: NOM berbeda dari NIM konvensional karena:
  1. Menggunakan "bagi hasil" bukan "bunga"
  2. DST (Dana Syirkah Temporer) diperlakukan khusus — hak pihak ketiga
     dikurangkan dari pendapatan, bukan sebagai beban
```

### BOPO (Beban Operasional terhadap Pendapatan Operasional)
```
BOPO = Beban Operasional / Pendapatan Operasional × 100%

Beban Operasional = 
  Hak Pihak Ketiga atas Bagi Hasil DST
  + Beban Tenaga Kerja
  + Beban Admin & Umum
  + Beban Penyusutan & Amortisasi
  + Beban CKPN / Penurunan Nilai
  + Beban Operasional Lain

Pendapatan Operasional =
  Pendapatan dari Penyaluran Dana
  + Pendapatan Operasional Lain (fee-based income)

Predikat OJK:
  Sangat Sehat : BOPO ≤ 83%
  Sehat        : 83% < BOPO ≤ 85%
  Cukup Sehat  : 85% < BOPO ≤ 87%
  Kurang Sehat : 87% < BOPO ≤ 89%
  Tidak Sehat  : BOPO > 89%

Benchmark industri: BOPO bank syariah umumnya lebih tinggi dari bank konvensional
karena biaya operasional dan sistem yang masih berkembang.
```

---

## 4. Rasio Likuiditas

### FDR (Financing to Deposit Ratio)
```
FDR = Total Pembiayaan / Dana Pihak Ketiga × 100%

Dana Pihak Ketiga (DPK):
  - Giro Wadiah
  - Tabungan Wadiah
  - Tabungan Mudharabah
  - Deposito Mudharabah
  (TIDAK termasuk Dana Syirkah Temporer dari bank lain & modal)

Predikat OJK:
  Sangat Sehat : FDR ≤ 75%
  Sehat        : 75% < FDR ≤ 85%
  Cukup Sehat  : 85% < FDR ≤ 100%
  Kurang Sehat : 100% < FDR ≤ 120%
  Tidak Sehat  : FDR > 120%

CATATAN: Beberapa formula menggunakan penyebut (DPK + Modal),
mengacu SE OJK terbaru untuk konsistensi.
```

### STM (Short Term Mismatch)
```
STM = Aset Jangka Pendek / Kewajiban Jangka Pendek × 100%
Threshold: ≥ 25% (menjamin likuiditas jangka pendek)
```

### GWM (Giro Wajib Minimum)
```
GWM Primer  = 5% dari DPK (dalam Rupiah, di BI)
GWM Sekunder = 5% dari DPK (SBN, SBIS, Sukuk BI, Excess Reserve)
GWM LFR     = jika FDR di bawah 80%, kena disinsentif GWM tambahan
```

### ALMA (Asset Liability Management) — Rasio Pendukung
```
PDN (Posisi Devisa Neto):
  PDN = (Aset Valas - Liabilitas Valas + Posisi Derivatif Off-Balance Sheet) /
        Modal × 100%
  Threshold: PDN ≤ 20%
```

---

## 5. Rasio Efisiensi & Struktur

### REA (Rasio Efisiensi Aktivitas)
```
REA = Beban Operasional / Pendapatan Operasional Bersih × 100%
(Mirip BOPO, namun memakai pendapatan bersih setelah distribusi)
```

### Fee Based Income Ratio
```
Fee Ratio = Pendapatan Fee-Based / Total Pendapatan Operasional × 100%
(Mengukur diversifikasi pendapatan — semakin tinggi semakin baik)
```

### PPAP (Penyisihan Penghapusan Aktiva Produktif) — Pra-PSAK 71
```
PPAP Umum   = 1%  × Aset Produktif Kol 1 (kecuali SBI/SPN)
PPAP Khusus = 5%  × Aset Produktif Kol 2
              15% × Aset Produktif Kol 3 (- nilai agunan)
              50% × Aset Produktif Kol 4 (- nilai agunan)
              100%× Aset Produktif Kol 5 (- nilai agunan)

CATATAN: PPAP digantikan CKPN (PSAK 71) sejak 1 Jan 2020.
Namun beberapa bank masih menyajikan keduanya untuk perbandingan.
```

---

## 6. Cara Membaca Laporan Bulanan (Statistik Perbankan Syariah OJK)

Data utama yang tersedia di SPS OJK:
- Total Aset, Pembiayaan, DPK (bulanan)
- Rasio CAR, NPF, ROA, ROE, NOM, BOPO, FDR (bulanan)
- Komposisi akad pembiayaan
- Komposisi DPK (giro, tabungan, deposito)
- Distribusi bagi hasil (equivalent rate)

Untuk benchmarking, selalu bandingkan dengan:
1. Rata-rata industri BUS (Bank Umum Syariah)
2. Rata-rata UUS (Unit Usaha Syariah)
3. Tren historis bank yang sama (minimal 3 tahun)
