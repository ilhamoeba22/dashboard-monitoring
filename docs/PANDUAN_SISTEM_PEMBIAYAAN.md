# 📘 PANDUAN LENGKAP SISTEM MONITORING PEMBIAYAAN
## BPRS HIK MCI - Dashboard Pembiayaan

---

## 📋 DAFTAR ISI

1. [Pengenalan Sistem](#pengenalan-sistem)
2. [Konsep Dasar Pembiayaan](#konsep-dasar-pembiayaan)
3. [Navigasi dan Struktur Menu](#navigasi-dan-struktur-menu)
4. [Panduan Per Halaman](#panduan-per-halaman)
5. [Fitur Umum](#fitur-umum)
6. [Tips dan Best Practices](#tips-dan-best-practices)
7. [Glossary](#glossary)

---

## 1. PENGENALAN SISTEM

### 1.1 Apa itu Sistem Monitoring Pembiayaan?

Sistem Monitoring Pembiayaan adalah platform dashboard berbasis web yang dirancang khusus untuk membantu manajemen dan staff BPRS HIK MCI dalam:

- **Memantau kesehatan portofolio pembiayaan** secara real-time
- **Menganalisis performa** pembiayaan dari berbagai dimensi
- **Mendeteksi risiko** sejak dini (Early Warning System)
- **Membuat keputusan** berbasis data yang akurat
- **Melacak pencapaian target** RBB (Rencana Bisnis Bank)

### 1.2 Siapa yang Menggunakan Sistem Ini?

- **Direksi & Manajemen**: Monitoring strategis dan pengambilan keputusan
- **Kepala Cabang**: Monitoring performa cabang dan tim
- **Account Officer (AO)**: Monitoring portofolio nasabah
- **Risk Management**: Analisis risiko dan mitigasi
- **Compliance & Audit**: Monitoring kepatuhan dan kualitas aset

### 1.3 Keunggulan Sistem

✅ **Real-time Data**: Data langsung dari Core Banking System (CBS)  
✅ **Multi-dimensional Analysis**: Analisis dari berbagai sudut pandang  
✅ **Visual Dashboard**: Grafik dan chart yang mudah dipahami  
✅ **Early Warning System**: Deteksi dini masalah pembiayaan  
✅ **Export & Report**: Ekspor data ke Excel/PDF untuk laporan  
✅ **Filter Dinamis**: Filter berdasarkan cabang, periode, segmen, dll  

---

## 2. KONSEP DASAR PEMBIAYAAN

### 2.1 Istilah Penting yang Harus Dipahami

#### A. Outstanding (O/S)
**Definisi**: Saldo pokok pembiayaan yang masih harus dibayar nasabah.

**Contoh**: 
- Nasabah A mengajukan pembiayaan Rp 100 juta
- Sudah diangsur Rp 30 juta
- Outstanding = Rp 70 juta (sisa yang harus dibayar)

**Mengapa Penting**: O/S menunjukkan total eksposur risiko bank terhadap nasabah.

#### B. Number of Account (NOA)
**Definisi**: Jumlah rekening pembiayaan aktif.

**Contoh**:
- Cabang Jakarta memiliki 500 rekening pembiayaan aktif
- NOA = 500

**Mengapa Penting**: NOA menunjukkan skala operasional dan diversifikasi portofolio.

#### C. Non-Performing Financing (NPF)
**Definisi**: Pembiayaan bermasalah (kolektibilitas 3, 4, 5).

**Kategori**:
- **Kolektibilitas 3 (Kurang Lancar)**: Tunggakan 91-120 hari
- **Kolektibilitas 4 (Diragukan)**: Tunggakan 121-180 hari
- **Kolektibilitas 5 (Macet)**: Tunggakan > 180 hari

**NPF Ratio**: Persentase NPF terhadap total pembiayaan
```
NPF Ratio = (Total NPF / Total O/S) × 100%
```

**Standar Kesehatan**:
- ✅ NPF < 5%: Sehat
- ⚠️ NPF 5-8%: Perlu Perhatian
- ❌ NPF > 8%: Bermasalah

#### D. Kolektibilitas
**Definisi**: Klasifikasi kualitas pembiayaan berdasarkan ketepatan pembayaran.

| Kol | Kategori | Tunggakan | Status |
|-----|----------|-----------|--------|
| 1 | Lancar | 0-30 hari | ✅ Sehat |
| 2 | Dalam Perhatian Khusus (DPK) | 31-90 hari | ⚠️ Waspada |
| 3 | Kurang Lancar | 91-120 hari | ⚠️ Bermasalah |
| 4 | Diragukan | 121-180 hari | ❌ Bermasalah |
| 5 | Macet | > 180 hari | ❌ Bermasalah |

#### E. Cadangan Kerugian Penurunan Nilai (CKPN)
**Definisi**: Dana yang disisihkan bank untuk mengantisipasi kerugian dari pembiayaan bermasalah.

**Perhitungan**:
- Kol 1: 1% × O/S
- Kol 2: 5% × O/S
- Kol 3: 15% × O/S
- Kol 4: 50% × O/S
- Kol 5: 100% × O/S

**Coverage Ratio**: Persentase CKPN terhadap NPF
```
Coverage Ratio = (Total CKPN / Total NPF) × 100%
```

**Standar**: Coverage Ratio minimal 100%

#### F. Financing to Deposit Ratio (FDR)
**Definisi**: Rasio pembiayaan terhadap dana pihak ketiga (DPK).

```
FDR = (Total Pembiayaan / Total DPK) × 100%
```

**Standar Kesehatan**:
- ❌ FDR < 80%: Kurang optimal (dana menganggur)
- ✅ FDR 80-92%: Ideal
- ⚠️ FDR 92-100%: Perlu monitoring
- ❌ FDR > 100%: Berisiko likuiditas

#### G. Restrukturisasi
**Definisi**: Perubahan syarat pembiayaan untuk membantu nasabah yang mengalami kesulitan pembayaran.

**Jenis**:
- Perpanjangan jangka waktu
- Penurunan margin/bagi hasil
- Pengurangan tunggakan
- Konversi akad

#### H. Write-Off
**Definisi**: Penghapusbukuan pembiayaan macet dari neraca bank.

**Catatan**: Write-off ≠ penghapusan tagihan. Bank tetap berhak menagih.

#### I. Top-Up
**Definisi**: Penambahan plafon pembiayaan untuk nasabah existing.

**Contoh**:
- Pembiayaan awal: Rp 50 juta
- Top-up: Rp 30 juta
- Total plafon baru: Rp 80 juta

#### J. Yield
**Definisi**: Tingkat imbal hasil (return) dari portofolio pembiayaan.

```
Yield = (Total Pendapatan Margin / Total O/S) × 100%
```

---

## 3. NAVIGASI DAN STRUKTUR MENU

### 3.1 Struktur Menu Utama

Sistem Pembiayaan terdiri dari **21 halaman** yang dikelompokkan menjadi beberapa kategori:

#### 📊 **DASHBOARD & OVERVIEW**
1. **Executive Overview** - Dashboard utama ringkasan performa
2. **Master Console (Rekapitulasi)** - Analisis multidimensi volume pembiayaan

#### 🎯 **MONITORING OPERASIONAL**
3. **Target RBB** - Monitoring pencapaian target tahunan
4. **Perkembangan** - Tren pertumbuhan pembiayaan
5. **Jatuh Tempo & Early Warning** - Monitoring antrian jatuh tempo
6. **Repayment Rate** - Analisis tingkat pembayaran
7. **Repayment Rate New** - Analisis pembayaran versi baru

#### 🛡️ **QUALITY & RISK MANAGEMENT**
8. **Quality & Risk** - Monitoring NPF, aging, konsentrasi
9. **Kolektibilitas** - Distribusi kualitas pembiayaan
10. **Risk Aggregation** - Agregasi risiko portofolio
11. **Collection Monitoring** - Monitoring penagihan

#### 📋 **DATA & REPORTING**
12. **Data Nominatif** - Data detail per rekening
13. **Restrukturisasi** - Monitoring pembiayaan restruktur
14. **Write-Off** - Data pembiayaan yang di-write-off
15. **Settlement** - Data pelunasan pembiayaan

#### 💰 **BUSINESS DEVELOPMENT**
16. **Top-Up** - Monitoring penambahan plafon
17. **Yield** - Analisis imbal hasil pembiayaan
18. **PPKA (Pola Pembiayaan Karyawan Akhir)** - Pembiayaan karyawan

#### 👥 **PERFORMANCE & SEGMENTATION**
19. **Karyawan (AO Performance)** - Performa Account Officer
20. **Sindikasi** - Pembiayaan sindikasi/konsorsium
21. **Overview** - Ringkasan eksekutif

### 3.2 Cara Mengakses Menu

1. **Dari Sidebar Kiri**: Klik menu "Financing" → Pilih submenu
2. **Dari Dashboard**: Klik Quick Links di halaman Executive Overview
3. **Dari Breadcrumb**: Navigasi cepat antar halaman terkait

### 3.3 Fitur Umum di Semua Halaman

#### A. Filter Cabang
- Lokasi: Pojok kanan atas setiap halaman
- Fungsi: Memfilter data berdasarkan cabang tertentu
- Default: "Semua Cabang"

#### B. Tombol Refresh
- Ikon: 🔄 (Refresh)
- Fungsi: Memuat ulang data terbaru dari CBS
- Lokasi: Sebelah filter cabang

#### C. Export Data
- Format: Excel (.xlsx) dan PDF
- Lokasi: Biasanya di bagian tabel data
- Fungsi: Mengunduh data untuk laporan

#### D. Date Range Picker
- Fungsi: Memilih periode data yang ditampilkan
- Format: Tanggal awal - Tanggal akhir
- Default: Bulan berjalan

---

