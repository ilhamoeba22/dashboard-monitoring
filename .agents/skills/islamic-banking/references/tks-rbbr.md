# TKS (Tingkat Kesehatan Bank) — RBBR (Risk Based Bank Rating)

Dasar Hukum: POJK No. 8/POJK.03/2014 & SE OJK No. 10/SEOJK.03/2014

---

## Kerangka Penilaian TKS

```
TKS = f(Profil Risiko, GCG, Rentabilitas, Permodalan)
       P           R     E           C
     (disingkat: RGEC)
```

Penilaian dilakukan **per semester** (posisi Juni dan Desember).

---

## 1. PROFIL RISIKO (Risk Profile)

### 8 Jenis Risiko yang Dinilai

```
1. Risiko Kredit
2. Risiko Pasar
3. Risiko Likuiditas
4. Risiko Operasional
5. Risiko Hukum
6. Risiko Reputasi
7. Risiko Stratejik
8. Risiko Kepatuhan
+ Risiko Imbal Hasil (khusus BUS/UUS) — tambahan dari konvensional
+ Risiko Investasi (khusus BUS/UUS) — tambahan dari konvensional
```

### Proses Penilaian Per Risiko

```
Untuk setiap jenis risiko:

Step 1: Tentukan Inheren Risk (1-5)
  1 = Low        (Low Risk)
  2 = Low to Moderate
  3 = Moderate   (Medium Risk)
  4 = Moderate to High
  5 = High       (High Risk)

Step 2: Tentukan Kualitas Penerapan Manajemen Risiko (KPMR) (1-5)
  1 = Strong     (sangat memadai)
  2 = Satisfactory
  3 = Fair       (cukup memadai)
  4 = Marginal   (kurang memadai)
  5 = Unsatisfactory (tidak memadai)

Step 3: Gabungkan → Net Risk (Peringkat Risiko)
  Matriks Net Risk: Inheren 1 + KPMR 1 → Net 1, dst.
  (Lihat matriks di SE OJK 10/2014 Lampiran 1)
```

### Komposit Profil Risiko
```
Komposit = Rata-rata tertimbang dari 8 (atau 10) jenis risiko

Bobot risiko (tidak diungkapkan eksplisit OJK, umumnya equal weight atau
bank menentukan sendiri berdasarkan materialitas)

Peringkat Komposit Profil Risiko:
  1 = Low           → "Sangat Sehat"
  2 = Low to Moderate
  3 = Moderate      → "Cukup Sehat"
  4 = Moderate to High
  5 = High          → "Tidak Sehat"
```

### Indikator Risiko Kredit (Kuantitatif)

```
Parameter Utama:
  NPF Gross        : < 2% (Rendah), 2-5% (Sedang), > 5% (Tinggi)
  NPF Net          : < 1% (Rendah), 1-3% (Sedang), > 3% (Tinggi)
  Coverage Ratio   : > 150% (kuat), 100-150% (memadai), < 100% (lemah)
  FAR              : < 10% (Rendah), 10-20% (Sedang), > 20% (Tinggi)
  Konsentrasi      : HHI < 0.15 (tidak terkonsentrasi)
  BMPK Breach      : Ada/Tidak
  Kualitas Agunan  : Liquid/Semi-liquid/Illiquid

KYC & Credit Underwriting Quality (Kualitatif):
  - Efektivitas scoring/rating system
  - Kelengkapan kebijakan perkreditan
  - Implementasi early warning system
  - Pengelolaan portofolio konsentrasi
```

### Indikator Risiko Likuiditas (Kuantitatif)
```
FDR              : 75-85% (sehat), >92% (concern), <60% (berlebih likuid)
STM (Short-Term Mismatch): > 25%
NSFR (Net Stable Funding Ratio): > 100%
LCR (Liquidity Coverage Ratio) : > 100%
Rasio Aset Likuid / Total Aset : > 20%
Ketergantungan Dana Wholesale  : < 20% DPK
```

### Indikator Risiko Pasar
```
PDN (Posisi Devisa Neto) : ≤ 20% Modal
Sensitivitas NIM/NOM terhadap perubahan suku bunga (Interest Rate Risk in Banking Book)
Mark-to-Market Surat Berharga Trading
```

### Indikator Risiko Operasional
```
- Frekuensi dan dampak operational loss events
- Kecukupan Business Continuity Plan (BCP)
- Tingkat kematangan IT system (termasuk CBS)
- Human error rate dalam transaksi
- Kecukupan asuransi kerugian operasional
- Kejadian fraud internal/eksternal
```

### Risiko Imbal Hasil (Khusus Syariah)
```
Risiko akibat perubahan tingkat imbal hasil yang dibayarkan bank kepada 
nasabah Dana Syirkah Temporer (DST), yang mempengaruhi perilaku nasabah.

Indikator:
  - Equivalent Rate Deposito vs Suku Bunga Pasar
  - Selisih bagi hasil bank syariah vs deposito konvensional
  - Tingkat Displaced Commercial Risk (DCR)
  - Alpha (α) = Porsi bagi hasil nasabah yang disubsidi bank dari keuntungannya
    sendiri demi mempertahankan nasabah

Formula DCR:
  DCR = α × α_nasabah × Porsi DST/Total Dana
```

### Risiko Investasi (Khusus Syariah)
```
Risiko yang timbul karena bank syariah menanggung kerugian atas
pembiayaan berbasis bagi hasil (Mudharabah & Musyarakah) ketika
nasabah/mitra mengalami kerugian usaha.

Indikator:
  - Porsi pembiayaan Mudharabah + Musyarakah / Total Pembiayaan
  - Kualitas monitoring investasi
  - Kelayakan sistem seleksi mitra investasi
  - Konsentrasi sektor pada pembiayaan investasi
```

---

## 2. GCG (Good Corporate Governance)

### 11 Faktor Penilaian GCG (SE OJK 10/2014)
```
1. Pelaksanaan tugas & tanggung jawab Dewan Komisaris
2. Pelaksanaan tugas & tanggung jawab Direksi
3. Kelengkapan & pelaksanaan tugas Komite
4. Penanganan benturan kepentingan
5. Penerapan fungsi kepatuhan bank
6. Penerapan fungsi audit intern
7. Penerapan fungsi audit ekstern
8. Penerapan manajemen risiko termasuk SPI
9. Penyediaan dana kepada pihak terkait (Related Party) & BMPK
10. Transparansi kondisi keuangan & non-keuangan
11. Rencana strategis bank

Khusus BUS: Ditambah
12. Pelaksanaan tugas & tanggung jawab DPS (Dewan Pengawas Syariah)
```

### Peringkat GCG
```
Peringkat 1 = Sangat Baik
Peringkat 2 = Baik
Peringkat 3 = Cukup Baik
Peringkat 4 = Kurang Baik
Peringkat 5 = Tidak Baik
```

---

## 3. RENTABILITAS (Earnings)

### Faktor Penilaian
```
1. Kinerja rentabilitas bank (ROA, ROE, NOM, BOPO) — lihat referensi rasio-keuangan.md
2. Sumber-sumber rentabilitas:
   - Pendapatan bagi hasil / margin
   - Fee-based income
   - Keuntungan trading
3. Stabilitas rentabilitas (tren, volatilitas)
4. Kemampuan memenuhi target laba (budget vs actual)
5. Prospek rentabilitas ke depan (forward-looking)

Peringkat Rentabilitas:
  1 = Sangat Sehat  : ROA > 1.5% AND NOM > 3% AND BOPO < 83%
  2 = Sehat         : kombinasi baik
  3 = Cukup Sehat   : kombinasi moderat
  4 = Kurang Sehat  : ROA mendekati 0 / NOM rendah
  5 = Tidak Sehat   : ROA < 0
```

---

## 4. PERMODALAN (Capital)

### Parameter Utama
```
1. CAR/KPMM vs Profil Risiko bank
2. Komposisi permodalan (kualitas modal, modal inti vs modal pelengkap)
3. Kemampuan bank memenuhi kebutuhan tambahan modal
4. Proyeksi permodalan (Capital Plan) — minimal 3 tahun ke depan
5. Kemampuan mengcover potensi kerugian dari risiko yang ada

Peringkat Permodalan:
  1 = Sangat Sehat  : CAR >> threshold, buffer sangat besar
  2 = Sehat         : CAR di atas threshold dengan buffer memadai
  3 = Cukup Sehat   : CAR memenuhi threshold, buffer terbatas
  4 = Kurang Sehat  : CAR mendekati threshold
  5 = Tidak Sehat   : CAR di bawah threshold
```

---

## 5. KOMPOSIT TKS

### Penetapan Peringkat Komposit
```
Peringkat Komposit (PK) merupakan integrasi dari 4 faktor RGEC:

Penetapan dilakukan secara judgmental, mempertimbangkan:
  - Peringkat masing-masing komponen
  - Materialitas dan signifikansi tiap komponen
  - Interaksi antar komponen

PK-1 = Sangat Sehat
PK-2 = Sehat
PK-3 = Cukup Sehat
PK-4 = Kurang Sehat
PK-5 = Tidak Sehat

Implikasi Peringkat Komposit:
  PK-1 : Memenuhi semua persyaratan minimum dengan kondisi sangat baik
  PK-2 : Memenuhi persyaratan dengan kondisi baik, koreksi minor diperlukan
  PK-3 : Beberapa kelemahan, berpotensi mengganggu kelangsungan usaha jika
          tidak segera diperbaiki (Supervisory Concern)
  PK-4 : Kelemahan signifikan, berisiko terhadap kelangsungan usaha
          (Close Supervisory Attention)
  PK-5 : Kelemahan sangat signifikan, terancam kelangsungan usahanya
          (Cease and Desist Order / Resolusi)
```

---

## 6. Aksi Pengawasan OJK Berdasarkan TKS

```
PK-1 & PK-2: Normal supervision
  → Laporan berkala + pemeriksaan rutin

PK-3: Enhanced supervision
  → Action Plan perbaikan wajib disampaikan dalam 15 hari
  → Monitoring bulanan
  → OJK dapat membatasi ekspansi bisnis

PK-4: Intensif supervision
  → Rapat direksi dengan OJK
  → Action Plan 30 hari
  → Batasan signifikan pada bisnis baru
  → Potensi pergantian manajemen

PK-5: Special supervision / Pengawasan Khusus
  → Penetapan status "Bank Dalam Pengawasan Khusus" (BDPK)
  → Jangka waktu 180 hari untuk perbaikan
  → Jika tidak membaik → LPS (Likuidasi atau Penyelamatan)
```

---

## 7. Laporan Penilaian TKS

### Format Laporan Internal
```
Laporan TKS minimal memuat:
1. Executive Summary dengan Peringkat Komposit
2. Analisis Per Faktor (R, G, E, C) dengan peringkat dan justifikasi
3. Perbandingan dengan periode sebelumnya
4. Rencana Tindak Lanjut (Action Plan) jika ada kelemahan
5. Tanda tangan Direksi & Komisaris

Periode pelaporan kepada OJK:
  - Semester I  : paling lambat 31 Juli
  - Semester II : paling lambat 31 Januari tahun berikutnya
```
