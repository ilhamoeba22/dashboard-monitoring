# PANDUAN PER HALAMAN - BAGIAN 3

## 📋 HALAMAN 8: KOLEKTIBILITAS

### Tujuan Halaman
Analisis distribusi dan pergerakan kolektibilitas portofolio untuk monitoring kualitas aset.

### Untuk Siapa?
- **Risk Management**: Monitoring kualitas harian
- **Collection Team**: Identifikasi prioritas collection
- **Manajemen**: Evaluasi kesehatan portofolio

### Komponen Halaman

#### A. KPI Cards (5 Metrik Kolektibilitas)

**1. Kol 1 (Lancar)**
- Metrik: Total O/S Kol 1
- Warna: Hijau
- Target: > 90% dari total

**2. Kol 2 (DPK)**
- Metrik: Total O/S Kol 2
- Warna: Biru
- Target: < 5% dari total

**3. Kol 3 (Kurang Lancar)**
- Metrik: Total O/S Kol 3
- Warna: Kuning
- Target: < 3% dari total

**4. Kol 4 (Diragukan)**
- Metrik: Total O/S Kol 4
- Warna: Orange
- Target: < 1% dari total

**5. Kol 5 (Macet)**
- Metrik: Total O/S Kol 5
- Warna: Merah
- Target: < 1% dari total

#### B. Grafik Distribusi Kolektibilitas

**Jenis**: Stacked Area Chart (12 bulan)

**Cara Membaca**:
- Area hijau mengecil → Kualitas memburuk ⚠️
- Area merah/orange membesar → NPF meningkat ❌
- Area biru membesar → Early warning ⚠️

#### C. Migration Matrix (Matriks Perpindahan)

**Tabel**: Perpindahan kolektibilitas bulan ini vs bulan lalu

**Contoh**:
```
Dari Kol 1 → Kol 1: 950 rekening (stabil)
Dari Kol 1 → Kol 2: 30 rekening (downgrade)
Dari Kol 2 → Kol 1: 15 rekening (upgrade)
Dari Kol 2 → Kol 3: 5 rekening (downgrade)
```

**Cara Menggunakan**:
- Identifikasi downgrade terbanyak
- Monitor upgrade rate (efektivitas collection)
- Prediksi pergerakan bulan depan

#### D. Tabel Detail Per Kolektibilitas

**Kolom**:
1. No. Kontrak
2. Nama Nasabah
3. Cabang
4. AO
5. O/S
6. Tunggakan (hari)
7. Kolektibilitas
8. Kol Bulan Lalu
9. Movement (↑↓→)
10. Action

**Filter**:
- Filter by kolektibilitas
- Filter by movement (upgrade/downgrade/stabil)
- Filter by cabang

### Cara Menggunakan

**Workflow Monitoring**:
1. Cek KPI Cards → Distribusi sehat?
2. Lihat Grafik Tren → Ada pergeseran?
3. Analisis Migration Matrix → Downgrade meningkat?
4. Review Detail → Prioritas action

---

## 📊 HALAMAN 9: DATA NOMINATIF

### Tujuan Halaman
Database lengkap seluruh rekening pembiayaan dengan fitur pencarian dan filter advanced.

### Untuk Siapa?
- **Semua User**: Akses data detail nasabah
- **AO**: Monitoring portofolio sendiri
- **Audit**: Verifikasi data

### Komponen Halaman

#### A. Summary Cards (4 Metrik)

**1. Total Rekening**
- Jumlah rekening dalam view saat ini
- Update sesuai filter

**2. Total O/S**
- Total outstanding dalam view
- Update sesuai filter

**3. Average Plafon**
- Rata-rata plafon per rekening
- Indikator segmen (retail/komersial)

**4. NPF Count**
- Jumlah rekening NPF dalam view
- Quick check kualitas

#### B. Filter Advanced

**Filter Tersedia**:
- **Cabang**: Dropdown multi-select
- **AO**: Dropdown multi-select
- **Kolektibilitas**: Checkbox (1,2,3,4,5)
- **Segmen**: Konsumtif, Modal Kerja, Investasi
- **Akad**: Murabahah, Musyarakah, dll
- **Range Plafon**: Min - Max
- **Range O/S**: Min - Max
- **Status**: Aktif, Lunas, Write-off

**Cara Menggunakan**:
1. Pilih filter yang diinginkan
2. Klik "Apply Filter"
3. Data tabel akan update otomatis
4. Export hasil filter ke Excel

#### C. Tabel Data Nominatif

**Kolom** (20+ kolom):
1. No. Kontrak
2. Nama Nasabah
3. NIK
4. Alamat
5. No. HP
6. Cabang
7. AO
8. Tanggal Akad
9. Jatuh Tempo
10. Akad
11. Segmen
12. Plafon Awal
13. O/S Pokok
14. Tunggakan Pokok
15. Tunggakan Margin
16. Total Tunggakan
17. Kolektibilitas
18. Hari Tunggakan
19. CKPN
20. Status

**Fitur Tabel**:
- ✅ Sorting semua kolom
- ✅ Search global
- ✅ Pagination (50/100/500 per halaman)
- ✅ Column visibility toggle
- ✅ Export Excel/PDF
- ✅ Print view

#### D. Detail View (Modal)

**Klik row** → Muncul modal detail lengkap:

**Tab 1: Info Nasabah**
- Data pribadi lengkap
- Kontak
- Alamat
- Pekerjaan

**Tab 2: Info Pembiayaan**
- Detail akad
- Plafon & O/S
- Jadwal angsuran
- History pembayaran

**Tab 3: Agunan**
- Jenis agunan
- Nilai taksasi
- Status legal

**Tab 4: History**
- Log aktivitas
- History restruktur
- History collection

### Cara Menggunakan

**Use Case 1: Cari Data Nasabah Spesifik**
1. Ketik nama/no kontrak di search box
2. Klik row untuk detail lengkap
3. Lihat info di modal detail

**Use Case 2: Export Data NPF**
1. Filter: Kolektibilitas 3,4,5
2. Klik "Export Excel"
3. Gunakan untuk laporan

**Use Case 3: Monitoring Portofolio AO**
1. Filter: Pilih AO tertentu
2. Review semua rekening AO
3. Identifikasi yang perlu perhatian

---

## 🔄 HALAMAN 10: RESTRUKTURISASI

### Tujuan Halaman
Monitoring pembiayaan yang direstrukturisasi dan evaluasi performa pasca restruktur.

### Untuk Siapa?
- **Risk Management**: Monitoring risiko restruktur
- **Collection Team**: Evaluasi efektivitas
- **Compliance**: Monitoring kepatuhan

### Komponen Halaman

#### A. KPI Cards (4 Metrik)

**1. Total Restrukturisasi**
- Jumlah rekening restruktur aktif
- Warna: Orange

**2. Total O/S Restruktur**
- Outstanding pembiayaan restruktur
- Persentase dari total portofolio

**3. Success Rate**
- Persentase restruktur yang lancar
- Rumus: (Lancar / Total) × 100%
- Target: > 80%

**4. Failure Rate**
- Persentase restruktur yang gagal (kembali NPF)
- Rumus: (Gagal / Total) × 100%
- Target: < 20%

#### B. Grafik Tren Restrukturisasi

**Jenis**: Dual Line Chart

**Line 1**: Jumlah restruktur baru per bulan
**Line 2**: Jumlah restruktur gagal per bulan

**Cara Membaca**:
- Line 1 naik → Volume restruktur meningkat
- Line 2 naik → Efektivitas menurun ⚠️
- Gap melebar → Perlu evaluasi strategi

#### C. Breakdown by Jenis Restruktur

**Jenis**:
1. **Perpanjangan Jangka Waktu**
   - Paling umum
   - Success rate biasanya tinggi

2. **Penurunan Margin**
   - Untuk nasabah cash flow bermasalah
   - Perlu analisis ketat

3. **Pengurangan Tunggakan**
   - Untuk nasabah goodwill
   - Risk tinggi

4. **Konversi Akad**
   - Jarang dilakukan
   - Perlu approval khusus

**Metrik per Jenis**:
- Jumlah kasus
- Success rate
- Average O/S
- Average duration

#### D. Tabel Monitoring Restruktur

**Kolom**:
1. No. Kontrak
2. Nama Nasabah
3. Tanggal Restruktur
4. Jenis Restruktur
5. O/S Saat Restruktur
6. O/S Saat Ini
7. Kol Sebelum Restruktur
8. Kol Saat Ini
9. Bulan Sejak Restruktur
10. Status Performance
11. Action

**Badge Status**:
- 🟢 **Performing**: Bayar lancar pasca restruktur
- 🟡 **Watch**: Ada keterlambatan minor
- 🔴 **Non-Performing**: Kembali bermasalah

**Filter**:
- Filter by jenis restruktur
- Filter by status performance
- Filter by periode restruktur

### Cara Menggunakan

**Workflow Monitoring**:
1. Cek Success/Failure Rate → Efektif?
2. Analisis per Jenis → Jenis mana yang paling efektif?
3. Review Tabel → Identifikasi yang perlu perhatian
4. Follow up restruktur yang watch/non-performing

**Evaluasi Efektivitas**:
- Success rate > 80% → Strategi efektif ✅
- Success rate < 60% → Perlu review kriteria ⚠️
- Failure rate tinggi → Terlalu mudah approve ❌

---

## 📝 HALAMAN 11: WRITE-OFF

### Tujuan Halaman
Monitoring pembiayaan yang di-write-off dan tracking recovery pasca write-off.

### Untuk Siapa?
- **Risk Management**: Monitoring write-off
- **Collection Team**: Recovery tracking
- **Accounting**: Rekonsiliasi

### Komponen Halaman

#### A. KPI Cards (4 Metrik)

**1. Total Write-Off**
- Jumlah rekening yang di-write-off
- Warna: Merah

**2. Total O/S Write-Off**
- Outstanding yang di-write-off
- Impact ke neraca

**3. Recovery Amount**
- Total yang berhasil direcovery
- Warna: Hijau

**4. Recovery Rate**
- Persentase recovery dari total write-off
- Rumus: (Recovery / Total Write-off) × 100%
- Target: > 10%

#### B. Grafik Write-Off Trend

**Jenis**: Bar Chart

**Bar Merah**: Write-off baru per bulan
**Bar Hijau**: Recovery per bulan

**Cara Membaca**:
- Bar merah tinggi → Kualitas memburuk ⚠️
- Bar hijau tinggi → Collection efektif ✅
- Rasio hijau/merah → Efektivitas recovery

#### C. Tabel Data Write-Off

**Kolom**:
1. No. Kontrak
2. Nama Nasabah
3. Tanggal Write-Off
4. O/S Saat Write-Off
5. Recovery s.d. Saat Ini
6. Outstanding Recovery
7. Recovery Rate (%)
8. Status Collection
9. Last Contact Date
10. Action Plan

**Status Collection**:
- 🔵 **Active**: Masih dalam proses collection
- 🟢 **Recovered**: Sudah lunas
- 🟡 **Partial**: Sebagian recovered
- 🔴 **Inactive**: Tidak bisa dihubungi

**Filter**:
- Filter by tahun write-off
- Filter by status collection
- Filter by recovery rate

### Cara Menggunakan

**Workflow Recovery**:
1. Cek Recovery Rate → Apakah efektif?
2. Filter: Active collection
3. Prioritas: O/S terbesar
4. Follow up collection team

**Tips Recovery**:
- Fokus ke partial recovery (sudah ada goodwill)
- Koordinasi dengan legal untuk yang besar
- Tawarkan settlement discount
- Update status collection rutin

---

