# PANDUAN PER HALAMAN - BAGIAN 1

## 4. PANDUAN DETAIL PER HALAMAN

---

## 📊 HALAMAN 1: EXECUTIVE OVERVIEW

### Tujuan Halaman
Dashboard utama yang memberikan gambaran menyeluruh tentang kesehatan dan performa portofolio pembiayaan secara real-time.

### Untuk Siapa?
- **Direksi**: Monitoring strategis harian
- **Manajemen**: Quick check performa keseluruhan
- **Semua User**: Pintu masuk utama sistem

### Komponen Halaman

#### A. Quick Links (4 Tombol Akses Cepat)
**Lokasi**: Bagian atas setelah header

1. **Master Console** 🔵
   - Warna: Biru
   - Fungsi: Akses cepat ke analisis volume multidimensi
   - Kapan digunakan: Saat ingin analisis detail volume pembiayaan

2. **Quality & Risk** 🔴
   - Warna: Merah
   - Fungsi: Monitoring kualitas dan risiko portofolio
   - Kapan digunakan: Saat ingin cek NPF dan risiko

3. **Data Nominatif** 🟢
   - Warna: Hijau
   - Fungsi: Akses data detail per rekening
   - Kapan digunakan: Saat perlu data spesifik nasabah

4. **Target RBB** 🟡
   - Warna: Kuning/Amber
   - Fungsi: Monitoring pencapaian target tahunan
   - Kapan digunakan: Saat evaluasi pencapaian target

#### B. Key Performance Indicators (4 KPI Cards)

**1. Total Portofolio**
- **Metrik**: Total Outstanding Pokok
- **Warna**: Biru
- **Ikon**: Dompet (Wallet)
- **Info Tambahan**: Jumlah rekening aktif
- **Interpretasi**:
  - Angka besar = Skala bisnis besar
  - Tren naik = Pertumbuhan positif
  - Tren turun = Perlu investigasi (pelunasan massal? penurunan penyaluran?)

**2. NPF Ratio**
- **Metrik**: Persentase pembiayaan bermasalah
- **Warna**: Amber/Orange
- **Ikon**: Warning
- **Standar**:
  - < 5%: ✅ Sehat (hijau)
  - 5-8%: ⚠️ Perlu perhatian (kuning)
  - > 8%: ❌ Bermasalah (merah)
- **Interpretasi**:
  - NPF naik = Kualitas memburuk, perlu action plan
  - NPF turun = Perbaikan kualitas, pertahankan

**3. Total Tunggakan**
- **Metrik**: Jumlah rupiah tunggakan (pokok + margin)
- **Warna**: Merah
- **Ikon**: Alert
- **Info Tambahan**: Jumlah rekening menunggak
- **Interpretasi**:
  - Tinggi = Masalah likuiditas dan collection
  - Perlu dipecah per aging untuk action plan

**4. Coverage Ratio**
- **Metrik**: Persentase CKPN terhadap NPF
- **Warna**: Hijau/Merah (dinamis)
- **Ikon**: Shield
- **Standar**: Minimal 100%
- **Interpretasi**:
  - < 100%: ❌ CKPN tidak cukup, perlu tambahan
  - ≥ 100%: ✅ CKPN memadai

#### C. Grafik Pertumbuhan Portofolio (12 Bulan)
**Jenis**: Area Chart
**Sumbu X**: Bulan (12 bulan terakhir)
**Sumbu Y**: Total O/S dalam Miliar Rupiah

**Cara Membaca**:
- **Tren Naik**: Pertumbuhan positif, ekspansi bisnis
- **Tren Datar**: Stagnasi, perlu strategi growth
- **Tren Turun**: Kontraksi, perlu investigasi penyebab
- **Fluktuasi Tajam**: Perlu analisis event khusus (pelunasan besar, penyaluran massal)

**Tips Analisis**:
- Bandingkan dengan target RBB
- Identifikasi pola musiman (seasonality)
- Cek korelasi dengan event bisnis (promo, kampanye)

#### D. Distribusi Kolektibilitas (Donut Chart)
**Jenis**: Donut Chart
**Warna**:
- Hijau: Kol 1 (Lancar)
- Biru: Kol 2 (DPK)
- Kuning: Kol 3 (Kurang Lancar)
- Orange: Kol 4 (Diragukan)
- Merah: Kol 5 (Macet)

**Cara Membaca**:
- **Dominasi Hijau**: ✅ Portofolio sehat
- **Kuning/Orange/Merah Besar**: ⚠️ Perlu action plan collection
- **Biru Membesar**: ⚠️ Early warning, perlu monitoring ketat

**Action Plan**:
- Kol 2 naik → Intensifkan reminder dan monitoring
- Kol 3-5 naik → Aktivasi tim collection, restrukturisasi

#### E. Top 10 Cabang NPF Tertinggi (Tabel)
**Kolom**:
1. Nama Cabang
2. Total O/S
3. NPF O/S
4. NPF Ratio (%)
5. Badge Status (Hijau/Kuning/Merah)

**Cara Menggunakan**:
- Identifikasi cabang bermasalah
- Prioritas intervensi manajemen
- Benchmark antar cabang
- Evaluasi kinerja kepala cabang

**Action Plan**:
- NPF > 8% → Kunjungan manajemen, action plan wajib
- NPF 5-8% → Monitoring intensif, coaching kepala cabang
- NPF < 5% → Best practice sharing

### Cara Menggunakan Halaman Ini

**Workflow Harian (Direksi/Manajemen)**:
1. Buka halaman Executive Overview
2. Cek 4 KPI utama → Apakah ada anomali?
3. Lihat grafik tren → Apakah sesuai target?
4. Cek distribusi kolektibilitas → Apakah ada pergeseran?
5. Review top 10 cabang NPF → Cabang mana yang perlu perhatian?
6. Klik Quick Links untuk deep dive ke area spesifik

**Contoh Kasus**:
- **Kasus 1**: NPF naik dari 4% ke 6%
  - Action: Klik "Quality & Risk" → Analisis aging → Identifikasi penyebab → Buat action plan
  
- **Kasus 2**: Total O/S turun signifikan
  - Action: Klik "Master Console" → Cek per cabang/AO → Identifikasi penyebab (pelunasan/penurunan penyaluran)

---

## 📊 HALAMAN 2: MASTER CONSOLE (REKAPITULASI)

### Tujuan Halaman
Analisis volume pembiayaan secara multidimensi dengan kemampuan drill-down dari berbagai sudut pandang.

### Untuk Siapa?
- **Manajemen**: Analisis strategis volume bisnis
- **Kepala Cabang**: Monitoring performa cabang
- **Business Development**: Identifikasi peluang growth

### Komponen Halaman

#### A. KPI Cards (4 Metrik Utama)

**1. Total Volume Pembiayaan**
- **Metrik**: Total O/S seluruh portofolio
- **Warna**: Biru
- **Periode**: Real-time
- **Gunakan untuk**: Ukuran skala bisnis

**2. Jumlah Rekening Aktif (NOA)**
- **Metrik**: Total rekening pembiayaan aktif
- **Warna**: Hijau
- **Gunakan untuk**: Diversifikasi dan skala operasional

**3. Average Financing Size**
- **Metrik**: Rata-rata plafon per rekening
- **Rumus**: Total O/S ÷ NOA
- **Warna**: Ungu
- **Interpretasi**:
  - Tinggi = Fokus ke corporate/komersial
  - Rendah = Fokus ke retail/mikro

**4. Growth Rate (MoM)**
- **Metrik**: Pertumbuhan Month-over-Month
- **Rumus**: ((O/S bulan ini - O/S bulan lalu) / O/S bulan lalu) × 100%
- **Warna**: Hijau (positif) / Merah (negatif)
- **Target**: Sesuai RBB (biasanya 1-3% per bulan)

#### B. Analisis Per Cabang (Tabel Interaktif)

**Kolom Tabel**:
1. **Nama Cabang**: Identitas cabang
2. **Total O/S**: Outstanding pokok
3. **NOA**: Jumlah rekening
4. **Avg Size**: Rata-rata plafon
5. **Growth (%)**: Pertumbuhan MoM
6. **Market Share (%)**: Kontribusi terhadap total
7. **Action**: Tombol detail

**Fitur Tabel**:
- ✅ **Sorting**: Klik header kolom untuk sort
- ✅ **Search**: Cari cabang spesifik
- ✅ **Pagination**: Navigasi halaman data
- ✅ **Export**: Download ke Excel/PDF

**Cara Menggunakan**:
1. **Identifikasi Top Performer**: Sort by Growth (%) descending
2. **Identifikasi Underperformer**: Sort by Growth (%) ascending
3. **Analisis Market Share**: Sort by Market Share (%) descending
4. **Benchmark**: Bandingkan avg size antar cabang

**Contoh Analisis**:
- Cabang A: Growth 5%, Market Share 15% → ⭐ Star Performer
- Cabang B: Growth -2%, Market Share 3% → ⚠️ Perlu Intervensi
- Cabang C: Growth 1%, Market Share 20% → 🔍 Potensi Lebih Besar

#### C. Analisis Per Segmen Pembiayaan

**Segmen**:
1. **Konsumtif**: Pembiayaan untuk konsumsi (kendaraan, rumah, dll)
2. **Modal Kerja**: Pembiayaan untuk operasional usaha
3. **Investasi**: Pembiayaan untuk aset produktif
4. **Multiguna**: Pembiayaan serbaguna

**Visualisasi**: Bar Chart Horizontal

**Metrik per Segmen**:
- Total O/S
- NOA
- Persentase dari total
- Growth rate

**Cara Menggunakan**:
- Identifikasi segmen dominan
- Monitor pergeseran komposisi segmen
- Sesuaikan strategi marketing per segmen

**Contoh Insight**:
- Modal Kerja 60% → Fokus ke sektor produktif ✅
- Konsumtif 70% → Risiko konsentrasi, perlu diversifikasi ⚠️

#### D. Analisis Per Akad Pembiayaan

**Jenis Akad**:
1. **Murabahah**: Jual beli dengan margin
2. **Musyarakah**: Kerjasama modal
3. **Mudharabah**: Bagi hasil
4. **Ijarah**: Sewa
5. **Qardh**: Pinjaman kebajikan

**Visualisasi**: Pie Chart

**Cara Menggunakan**:
- Monitor kepatuhan syariah
- Analisis preferensi produk
- Evaluasi profitabilitas per akad

#### E. Analisis Per Account Officer (AO)

**Kolom**:
1. Nama AO
2. Cabang
3. Total O/S
4. NOA
5. NPF Ratio
6. Ranking

**Fitur**:
- Ranking otomatis berdasarkan O/S
- Filter per cabang
- Export untuk evaluasi kinerja

**Cara Menggunakan**:
- Evaluasi kinerja individu AO
- Identifikasi top performer untuk reward
- Identifikasi underperformer untuk coaching
- Benchmark antar AO

**Contoh Evaluasi**:
- AO A: O/S 5M, NOA 50, NPF 2% → ⭐ Excellent
- AO B: O/S 3M, NOA 80, NPF 8% → ⚠️ Perlu Coaching (fokus quality)
- AO C: O/S 1M, NOA 20, NPF 1% → 🔍 Potensi Growth

### Cara Menggunakan Halaman Ini

**Workflow Analisis Volume**:
1. Cek KPI Cards → Pahami kondisi overall
2. Analisis per Cabang → Identifikasi cabang prioritas
3. Analisis per Segmen → Pahami komposisi bisnis
4. Analisis per Akad → Evaluasi product mix
5. Analisis per AO → Evaluasi kinerja individu
6. Export data untuk presentasi/laporan

**Contoh Kasus Penggunaan**:
- **Meeting Bulanan**: Export tabel cabang → Presentasi performa
- **Evaluasi AO**: Filter cabang → Sort by NPF → Identifikasi coaching needs
- **Strategi Bisnis**: Analisis segmen → Tentukan fokus marketing

---

## 🎯 HALAMAN 3: TARGET RBB

### Tujuan Halaman
Monitoring pencapaian target pembiayaan sesuai Rencana Bisnis Bank (RBB) tahunan dengan tracking progress real-time.

### Untuk Siapa?
- **Direksi**: Monitoring pencapaian strategis
- **Kepala Cabang**: Tracking target cabang
- **Business Development**: Evaluasi strategi penyaluran

### Komponen Halaman

#### A. KPI Cards (4 Metrik Target)

**1. Target Tahunan**
- **Metrik**: Total target O/S akhir tahun (dari RBB)
- **Warna**: Biru
- **Sumber**: Input manual dari RBB yang disetujui

**2. Realisasi YTD (Year-to-Date)**
- **Metrik**: Total O/S saat ini
- **Warna**: Hijau
- **Update**: Real-time dari CBS

**3. Achievement Rate**
- **Metrik**: Persentase pencapaian target
- **Rumus**: (Realisasi / Target) × 100%
- **Warna**: 
  - Hijau: ≥ 90%
  - Kuning: 70-89%
  - Merah: < 70%
- **Interpretasi**:
  - > 100%: ✅ Exceed target
  - 90-100%: ✅ On track
  - 70-89%: ⚠️ Below target, perlu akselerasi
  - < 70%: ❌ Significantly below, perlu action plan

**4. Gap to Target**
- **Metrik**: Selisih realisasi dengan target
- **Rumus**: Target - Realisasi
- **Warna**: Merah (jika gap besar)
- **Gunakan untuk**: Hitung kebutuhan penyaluran remaining period

#### B. Progress Bar Tahunan

**Visualisasi**: Progress bar dengan milestone

**Elemen**:
- Bar hijau: Realisasi saat ini
- Garis putus-putus: Target proporsional (sesuai bulan berjalan)
- Angka persentase: Achievement rate

**Cara Membaca**:
- Bar melewati garis putus-putus → Ahead of schedule ✅
- Bar di bawah garis putus-putus → Behind schedule ⚠️

**Contoh**:
- Bulan Juni (50% tahun berjalan)
- Target proporsional: 50% × Target tahunan
- Jika realisasi 55% → Ahead 5% ✅
- Jika realisasi 45% → Behind 5% ⚠️

#### C. Grafik Tren Bulanan vs Target

**Jenis**: Line Chart dengan 2 garis

**Garis 1 (Biru)**: Realisasi bulanan
**Garis 2 (Merah putus-putus)**: Target proporsional bulanan

**Sumbu X**: Bulan (Jan - Des)
**Sumbu Y**: Total O/S

**Cara Membaca**:
- Garis biru di atas merah → On track ✅
- Garis biru di bawah merah → Behind target ⚠️
- Gap melebar → Perlu action plan segera
- Gap menyempit → Strategi akselerasi berhasil

**Analisis Lanjutan**:
- Identifikasi bulan-bulan underperform
- Cari pola musiman (seasonality)
- Proyeksikan pencapaian akhir tahun

#### D. Tabel Target vs Realisasi Per Cabang

**Kolom**:
1. **Nama Cabang**
2. **Target Tahunan**: Target dari RBB
3. **Realisasi YTD**: O/S saat ini
4. **Achievement (%)**: Persentase pencapaian
5. **Gap**: Selisih target vs realisasi
6. **Status**: Badge warna (Hijau/Kuning/Merah)
7. **Trend**: Ikon naik/turun

**Fitur**:
- Sort by achievement untuk ranking
- Filter cabang bermasalah (achievement < 70%)
- Export untuk laporan manajemen

**Cara Menggunakan**:
1. **Identifikasi Top Achiever**: Sort by Achievement (%) descending
2. **Identifikasi Laggard**: Sort by Achievement (%) ascending
3. **Hitung Total Gap**: Sum kolom Gap
4. **Distribusi Target**: Realokasi target jika perlu

**Contoh Analisis**:
- Cabang Jakarta: Achievement 105% → ⭐ Exceed, bisa bantu cabang lain
- Cabang Bandung: Achievement 65% → ❌ Perlu action plan urgent
- Cabang Surabaya: Achievement 85% → ⚠️ Perlu akselerasi

#### E. Breakdown Target Per Segmen

**Visualisasi**: Stacked Bar Chart

**Segmen**:
- Konsumtif
- Modal Kerja
- Investasi
- Multiguna

**Metrik per Segmen**:
- Target
- Realisasi
- Achievement %

**Cara Menggunakan**:
- Identifikasi segmen underperform
- Fokuskan strategi marketing ke segmen gap besar
- Realokasi resources (AO, marketing budget)

**Contoh Strategi**:
- Modal Kerja: Achievement 60% → Intensifkan kunjungan ke UMKM
- Konsumtif: Achievement 95% → Maintain, fokus ke segmen lain

### Cara Menggunakan Halaman Ini

**Workflow Monitoring Target**:
1. **Cek Achievement Rate** → Apakah on track?
2. **Lihat Progress Bar** → Apakah ahead/behind schedule?
3. **Analisis Grafik Tren** → Identifikasi pola dan proyeksi
4. **Review Tabel Cabang** → Cabang mana yang perlu support?
5. **Cek Breakdown Segmen** → Segmen mana yang perlu push?
6. **Buat Action Plan** → Strategi akselerasi jika behind target

**Contoh Kasus**:
- **Kasus 1**: Achievement 75% di bulan Juni (seharusnya 50%)
  - Analisis: Ahead of schedule ✅
  - Action: Maintain momentum, pastikan quality tetap terjaga
  
- **Kasus 2**: Achievement 40% di bulan Juni (seharusnya 50%)
  - Analisis: Behind 10% ⚠️
  - Action: 
    - Identifikasi cabang laggard
    - Intensifkan marketing campaign
    - Realokasi AO ke area potensial
    - Weekly monitoring progress

**Meeting Bulanan**:
1. Export tabel cabang
2. Presentasikan achievement per cabang
3. Diskusikan action plan untuk cabang underperform
4. Set target bulanan untuk catch up

---

