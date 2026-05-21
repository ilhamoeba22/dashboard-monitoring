# PANDUAN PER HALAMAN - BAGIAN 2

## 📈 HALAMAN 4: PERKEMBANGAN

### Tujuan Halaman
Analisis tren dan pola pertumbuhan pembiayaan dari waktu ke waktu dengan visualisasi time-series yang komprehensif.

### Untuk Siapa?
- **Manajemen**: Analisis tren strategis
- **Business Intelligence**: Forecasting dan proyeksi
- **Marketing**: Evaluasi efektivitas kampanye

### Komponen Halaman

#### A. KPI Cards (4 Metrik Pertumbuhan)

**1. Growth Rate (MoM)**
- **Metrik**: Pertumbuhan Month-over-Month
- **Rumus**: ((O/S bulan ini - O/S bulan lalu) / O/S bulan lalu) × 100%
- **Warna**: Hijau (positif) / Merah (negatif)
- **Target**: 1-3% per bulan (sesuai RBB)

**2. Growth Rate (YoY)**
- **Metrik**: Pertumbuhan Year-over-Year
- **Rumus**: ((O/S tahun ini - O/S tahun lalu) / O/S tahun lalu) × 100%
- **Warna**: Hijau (positif) / Merah (negatif)
- **Target**: 15-20% per tahun

**3. Net New Financing**
- **Metrik**: Penyaluran baru dikurangi pelunasan
- **Rumus**: Penyaluran Baru - Pelunasan
- **Interpretasi**:
  - Positif: Ekspansi portofolio ✅
  - Negatif: Kontraksi portofolio ⚠️

**4. Penyaluran Bulan Ini**
- **Metrik**: Total penyaluran pembiayaan baru bulan berjalan
- **Warna**: Biru
- **Gunakan untuk**: Monitor aktivitas penyaluran

#### B. Grafik Tren O/S (24 Bulan)

**Jenis**: Area Chart dengan gradient
**Periode**: 24 bulan terakhir (2 tahun)
**Sumbu X**: Bulan-Tahun
**Sumbu Y**: Total O/S (Miliar Rupiah)

**Fitur**:
- Hover untuk detail per bulan
- Zoom in/out untuk fokus periode tertentu
- Export chart sebagai image

**Cara Membaca**:
- **Tren Naik Konsisten**: Pertumbuhan sehat ✅
- **Tren Datar**: Stagnasi, perlu strategi growth ⚠️
- **Tren Turun**: Kontraksi, investigasi penyebab ❌
- **Spike Naik**: Event penyaluran besar (promo, kampanye)
- **Spike Turun**: Event pelunasan massal

**Analisis Lanjutan**:
- Identifikasi pola musiman (seasonality)
- Korelasi dengan event bisnis (Ramadan, Lebaran, akhir tahun)
- Proyeksi tren 6-12 bulan ke depan

#### C. Grafik Penyaluran vs Pelunasan (12 Bulan)

**Jenis**: Dual Bar Chart
**Bar Hijau**: Penyaluran baru
**Bar Merah**: Pelunasan
**Line Biru**: Net New Financing

**Cara Membaca**:
- Bar hijau > Bar merah → Net positive, portofolio tumbuh ✅
- Bar hijau < Bar merah → Net negative, portofolio menyusut ⚠️
- Line biru di atas 0 → Ekspansi
- Line biru di bawah 0 → Kontraksi

**Insight**:
- Bulan dengan pelunasan tinggi → Cek penyebab (jatuh tempo massal? refinancing?)
- Bulan dengan penyaluran tinggi → Evaluasi efektivitas kampanye
- Rasio penyaluran/pelunasan → Indikator kesehatan bisnis

#### D. Tabel Perkembangan Bulanan

**Kolom**:
1. **Bulan**: Periode
2. **O/S Awal**: Saldo awal bulan
3. **Penyaluran**: Pembiayaan baru
4. **Pelunasan**: Pembayaran lunas
5. **O/S Akhir**: Saldo akhir bulan
6. **Growth (%)**: Pertumbuhan MoM
7. **Net Change**: Penyaluran - Pelunasan

**Fitur**:
- Export ke Excel untuk analisis lanjutan
- Filter by date range
- Summary row (total, average)

**Cara Menggunakan**:
1. Identifikasi bulan dengan growth tertinggi → Pelajari faktor sukses
2. Identifikasi bulan dengan growth terendah → Analisis penyebab
3. Hitung average growth → Bandingkan dengan target
4. Proyeksikan O/S akhir tahun

#### E. Analisis Pertumbuhan Per Cabang

**Visualisasi**: Heatmap atau Bar Chart

**Metrik**:
- Growth rate per cabang
- Ranking pertumbuhan
- Kontribusi terhadap total growth

**Warna**:
- Hijau tua: Growth tinggi (> 5%)
- Hijau muda: Growth sedang (2-5%)
- Kuning: Growth rendah (0-2%)
- Merah: Negatif growth (< 0%)

**Cara Menggunakan**:
- Identifikasi cabang dengan growth tertinggi → Best practice sharing
- Identifikasi cabang dengan negative growth → Root cause analysis
- Benchmark antar cabang
- Alokasi resources ke cabang potensial

### Cara Menggunakan Halaman Ini

**Workflow Analisis Perkembangan**:
1. Cek KPI Cards → Pahami kondisi growth saat ini
2. Lihat Grafik Tren 24 bulan → Identifikasi pola jangka panjang
3. Analisis Penyaluran vs Pelunasan → Pahami dinamika portofolio
4. Review Tabel Bulanan → Detail per periode
5. Cek Pertumbuhan per Cabang → Identifikasi area fokus

**Contoh Kasus**:
- **Kasus 1**: Growth MoM -2%
  - Analisis: Cek grafik penyaluran vs pelunasan
  - Temuan: Pelunasan naik 50% (jatuh tempo massal)
  - Action: Normal, tidak perlu action khusus
  
- **Kasus 2**: Growth YoY 5% (target 15%)
  - Analisis: Cek tren 24 bulan, identifikasi turning point
  - Temuan: Growth melambat sejak 6 bulan lalu
  - Action: Intensifkan marketing, review strategi penyaluran

---

## ⏰ HALAMAN 5: JATUH TEMPO & EARLY WARNING

### Tujuan Halaman
Monitoring antrian pembiayaan yang akan jatuh tempo dan deteksi dini potensi tunggakan melalui analisis kesiapan saldo nasabah.

### Untuk Siapa?
- **Collection Team**: Monitoring harian antrian jatuh tempo
- **Account Officer**: Follow up nasabah
- **Operasional**: Persiapan proses autodebet

### Komponen Halaman

#### A. KPI Cards (3 Metrik Utama)

**1. Total Antrian**
- **Metrik**: Jumlah rekening yang akan jatuh tempo bulan ini
- **Warna**: Abu-abu
- **Ikon**: User Follow
- **Info Tambahan**: Mini donut chart kesiapan saldo

**2. Proyeksi Tagihan**
- **Metrik**: Total tagihan (pokok + margin) yang akan jatuh tempo
- **Warna**: Biru
- **Ikon**: Money Dollar
- **Gunakan untuk**: Estimasi likuiditas masuk

**3. Saldo Cukup**
- **Metrik**: Jumlah rekening dengan saldo tabungan mencukupi
- **Warna**: Hijau
- **Ikon**: Checkbox Circle
- **Interpretasi**:
  - Tinggi: Potensi collection lancar ✅
  - Rendah: Perlu follow up intensif ⚠️

#### B. Tabel Antrian Jatuh Tempo

**Kolom**:
1. **No. Kontrak**: Nomor rekening pembiayaan
2. **Nama Nasabah**: Identitas nasabah
3. **Tgl Jatuh Tempo**: Tanggal jatuh tempo angsuran
4. **Tagihan Pokok**: Angsuran pokok
5. **Tagihan Margin**: Angsuran margin
6. **Total Tagihan**: Pokok + Margin
7. **Saldo Tabungan**: Saldo efektif nasabah
8. **Status Saldo**: Cukup/Kurang (badge warna)
9. **Urgency**: Level urgensi (badge warna)
10. **No. HP**: Nomor kontak
11. **Action**: Tombol WA Blast

**Fitur Tabel**:
- ✅ **Sorting**: Klik header untuk sort
- ✅ **Filter**: Filter by status saldo, urgency
- ✅ **Search**: Cari by nama/no kontrak
- ✅ **Pagination**: 15 data per halaman
- ✅ **Bulk Action**: Select multiple untuk WA blast
- ✅ **Export**: Download ke Excel

**Badge Urgency**:
- 🔴 **OVERDUE**: Sudah lewat jatuh tempo
- 🟠 **CRITICAL**: 0-3 hari lagi
- 🟡 **WARNING**: 4-7 hari lagi
- 🟢 **SAFE**: > 7 hari lagi

**Badge Status Saldo**:
- ✅ **Cukup**: Saldo ≥ Total Tagihan (hijau)
- ❌ **Kurang**: Saldo < Total Tagihan (merah)

**Row Highlighting**:
- **Merah**: CRITICAL/OVERDUE + Saldo Kurang (prioritas tertinggi)
- **Kuning**: WARNING + Saldo Kurang
- **Putih**: Normal

#### C. Fitur WA Blast

**Tombol Individual**:
- Lokasi: Kolom Action setiap row
- Ikon: WhatsApp
- Fungsi: Kirim reminder via WhatsApp ke nasabah

**Template Pesan**:
```
Assalamu'alaikum Bpk/Ibu [Nama],

Kami dari BPRS HIK MCI menginformasikan bahwa angsuran pembiayaan No: [No Kontrak] akan jatuh tempo pada [Tanggal] sebesar [Jumlah Tagihan].

Mohon pastikan saldo tabungan mencukupi untuk proses autodebet.

Terima kasih.
```

**Bulk WA Blast**:
- Checkbox di setiap row
- Tombol "Kirim WA Blast" di atas tabel
- Kirim ke multiple nasabah sekaligus

**Cara Menggunakan**:
1. Filter nasabah dengan saldo kurang
2. Centang nasabah yang ingin dikirimi reminder
3. Klik "Kirim WA Blast"
4. Sistem akan membuka WhatsApp Web untuk setiap nasabah

#### D. Grafik Kesiapan Dana

**Jenis**: Donut Chart
**Segmen**:
- Hijau: Saldo Cukup
- Merah: Saldo Kurang

**Metrik**:
- Jumlah rekening per kategori
- Persentase dari total

**Cara Membaca**:
- Hijau > 80%: ✅ Kondisi baik
- Hijau 60-80%: ⚠️ Perlu monitoring
- Hijau < 60%: ❌ Perlu action intensif

#### E. Filter dan Pencarian

**Filter Cabang**:
- Dropdown di header
- Filter data per cabang

**Filter Urgency**:
- Checkbox: OVERDUE, CRITICAL, WARNING, SAFE
- Filter multiple urgency sekaligus

**Filter Status Saldo**:
- Radio button: Semua / Saldo Cukup / Saldo Kurang

**Search Box**:
- Cari by nama nasabah atau no kontrak
- Real-time search

### Cara Menggunakan Halaman Ini

**Workflow Harian Collection Team**:
1. **Pagi Hari (08:00)**:
   - Buka halaman Jatuh Tempo
   - Cek KPI Cards → Berapa antrian hari ini?
   - Filter: OVERDUE + Saldo Kurang
   - Prioritas follow up nasabah OVERDUE

2. **Siang Hari (12:00)**:
   - Filter: CRITICAL (0-3 hari) + Saldo Kurang
   - Kirim WA Blast reminder
   - Telepon nasabah prioritas tinggi

3. **Sore Hari (16:00)**:
   - Filter: WARNING (4-7 hari) + Saldo Kurang
   - Kirim WA Blast early reminder
   - Update status follow up

4. **Akhir Hari (17:00)**:
   - Export data untuk laporan harian
   - Review kesiapan dana untuk besok

**Contoh Kasus**:
- **Kasus 1**: 50 rekening jatuh tempo besok, 30 saldo kurang
  - Action:
    - Prioritas: 30 rekening saldo kurang
    - Kirim WA Blast ke 30 nasabah
    - Telepon 10 nasabah dengan tagihan terbesar
    - Koordinasi dengan AO untuk kunjungan

- **Kasus 2**: Nasabah A OVERDUE 5 hari, saldo kurang
  - Action:
    - Telepon nasabah segera
    - Jika tidak angkat, kunjungan lapangan
    - Tawarkan solusi (top up saldo, restruktur)
    - Eskalasi ke supervisor jika tidak responsif

**Tips Efektif**:
- Gunakan bulk WA blast untuk efisiensi
- Fokus ke nasabah CRITICAL + saldo kurang
- Follow up pagi hari untuk nasabah jatuh tempo hari ini
- Koordinasi dengan AO untuk nasabah sulit dihubungi

---

## 💰 HALAMAN 6: REPAYMENT RATE

### Tujuan Halaman
Analisis tingkat pembayaran (repayment rate) nasabah untuk mengukur kualitas collection dan prediksi cash flow.

### Untuk Siapa?
- **Collection Team**: Evaluasi efektivitas collection
- **Risk Management**: Analisis kualitas portofolio
- **Treasury**: Proyeksi cash flow

### Komponen Halaman

#### A. KPI Cards (4 Metrik Pembayaran)

**1. Overall Repayment Rate**
- **Metrik**: Persentase pembayaran tepat waktu
- **Rumus**: (Jumlah bayar tepat waktu / Total jatuh tempo) × 100%
- **Warna**: Hijau (≥90%) / Kuning (70-89%) / Merah (<70%)
- **Target**: ≥ 90%

**2. On-Time Payment**
- **Metrik**: Jumlah rekening bayar tepat waktu (0 hari tunggakan)
- **Warna**: Hijau
- **Gunakan untuk**: Ukuran disiplin nasabah

**3. Late Payment**
- **Metrik**: Jumlah rekening bayar terlambat (1-30 hari)
- **Warna**: Kuning
- **Gunakan untuk**: Early warning potential NPF

**4. Non-Payment**
- **Metrik**: Jumlah rekening tidak bayar (>30 hari)
- **Warna**: Merah
- **Gunakan untuk**: Identifikasi problem accounts

#### B. Grafik Tren Repayment Rate (12 Bulan)

**Jenis**: Line Chart dengan area fill
**Sumbu X**: Bulan
**Sumbu Y**: Repayment Rate (%)

**Benchmark Line**: 90% (garis putus-putus merah)

**Cara Membaca**:
- Line di atas 90%: ✅ Collection efektif
- Line di bawah 90%: ⚠️ Perlu perbaikan strategi
- Tren naik: ✅ Perbaikan kualitas
- Tren turun: ❌ Deteriorasi kualitas

**Analisis**:
- Identifikasi bulan dengan repayment rate terendah
- Korelasi dengan event (Lebaran, akhir tahun)
- Evaluasi efektivitas strategi collection

#### C. Breakdown Repayment by Aging

**Visualisasi**: Stacked Bar Chart

**Kategori**:
- 🟢 **On-Time (0 hari)**: Bayar tepat waktu
- 🟡 **1-7 hari**: Terlambat 1 minggu
- 🟠 **8-30 hari**: Terlambat 1 bulan
- 🔴 **31-90 hari**: Terlambat 1-3 bulan
- ⚫ **>90 hari**: Terlambat >3 bulan

**Cara Membaca**:
- Dominasi hijau: ✅ Portofolio sehat
- Kuning/orange meningkat: ⚠️ Early warning
- Merah/hitam besar: ❌ Masalah serius

#### D. Tabel Repayment Per Cabang

**Kolom**:
1. **Cabang**
2. **Total Jatuh Tempo**: Jumlah rekening jatuh tempo
3. **On-Time**: Jumlah bayar tepat waktu
4. **Late**: Jumlah terlambat
5. **Non-Payment**: Jumlah tidak bayar
6. **Repayment Rate (%)**: Persentase on-time
7. **Ranking**: Peringkat antar cabang
8. **Trend**: Ikon naik/turun vs bulan lalu

**Fitur**:
- Sort by repayment rate untuk ranking
- Filter cabang bermasalah (<80%)
- Export untuk evaluasi kepala cabang

**Cara Menggunakan**:
- Identifikasi cabang dengan repayment rate tertinggi → Best practice
- Identifikasi cabang dengan repayment rate terendah → Coaching
- Benchmark antar cabang
- Evaluasi efektivitas collection team per cabang

#### E. Analisis Repayment by Segmen

**Segmen**:
- Konsumtif
- Modal Kerja
- Investasi
- Multiguna

**Metrik per Segmen**:
- Repayment rate
- Average days late
- Non-payment ratio

**Insight**:
- Segmen mana yang paling disiplin?
- Segmen mana yang perlu strategi collection khusus?
- Adjust risk appetite per segmen

### Cara Menggunakan Halaman Ini

**Workflow Evaluasi Repayment**:
1. Cek Overall Repayment Rate → Apakah di atas 90%?
2. Lihat Tren 12 bulan → Apakah membaik atau memburuk?
3. Analisis Breakdown Aging → Kategori mana yang meningkat?
4. Review per Cabang → Cabang mana yang perlu support?
5. Cek per Segmen → Segmen mana yang bermasalah?

**Contoh Kasus**:
- **Kasus 1**: Repayment rate turun dari 92% ke 85%
  - Analisis: Cek breakdown aging → Late payment (1-30 hari) naik
  - Root cause: Reminder kurang efektif
  - Action: Intensifkan WA blast, telepon follow up

- **Kasus 2**: Cabang A repayment rate 75% (terendah)
  - Analisis: Cek detail per AO di cabang A
  - Root cause: Collection team kurang aktif
  - Action: Coaching kepala cabang, training collection team

---

## 🛡️ HALAMAN 7: QUALITY & RISK

### Tujuan Halaman
Monitoring komprehensif kualitas portofolio dan profil risiko pembiayaan dengan 4 tab analisis mendalam.

### Untuk Siapa?
- **Risk Management**: Monitoring risiko harian
- **Direksi**: Evaluasi kesehatan portofolio
- **Compliance**: Monitoring kepatuhan rasio

### Tab 1: RGEC & Risk Profile

#### A. KPI Cards (4 Metrik Kualitas)

**1. Total Pembiayaan**
- **Metrik**: Total O/S portofolio
- **Warna**: Hijau
- **Ikon**: Wallet (transparan)

**2. NPF Gross**
- **Metrik**: Total NPF (Kol 3+4+5)
- **Rumus**: NPF O/S / Total O/S × 100%
- **Warna**: Hijau (<5%) / Merah (≥5%)
- **Ikon**: Warning (dinamis)
- **Standar**:
  - < 5%: ✅ Sehat
  - 5-8%: ⚠️ Perlu perhatian
  - > 8%: ❌ Bermasalah

**3. CKPN Coverage**
- **Metrik**: Rasio CKPN terhadap NPF
- **Rumus**: Total CKPN / Total NPF × 100%
- **Warna**: Biru (≥100%) / Orange (<100%)
- **Ikon**: Shield (dinamis)
- **Standar**: Minimal 100%

**4. FDR Likuiditas**
- **Metrik**: Financing to Deposit Ratio
- **Rumus**: Total Pembiayaan / Total DPK × 100%
- **Warna**: Indigo
- **Ikon**: Water Flash
- **Standar**: 80-92% (ideal)

#### B. Grafik NPF Trend (12 Bulan)

**Jenis**: Line Chart dengan dual axis
**Line 1 (Biru)**: NPF O/S (Miliar Rupiah)
**Line 2 (Merah)**: NPF Ratio (%)

**Benchmark Line**: 5% (garis putus-putus)

**Cara Membaca**:
- Line merah di bawah 5%: ✅ Sehat
- Line merah di atas 5%: ⚠️ Bermasalah
- Tren naik: ❌ Deteriorasi kualitas
- Tren turun: ✅ Perbaikan kualitas

**Analisis**:
- Identifikasi turning point (kapan NPF mulai naik/turun)
- Korelasi dengan event bisnis atau ekonomi
- Proyeksi NPF 3-6 bulan ke depan

#### C. Aging Analysis (Tabel)

**Kategori Aging**:
1. **Current (0-30 hari)**: Lancar
2. **31-60 hari**: Early delinquency
3. **61-90 hari**: DPK
4. **91-120 hari**: Kurang Lancar
5. **121-180 hari**: Diragukan
6. **>180 hari**: Macet

**Kolom per Aging**:
- Jumlah rekening
- Total O/S
- Persentase dari total
- Trend vs bulan lalu

**Cara Membaca**:
- **Current tinggi**: ✅ Portofolio sehat
- **31-90 hari naik**: ⚠️ Early warning, perlu action
- **>90 hari naik**: ❌ NPF meningkat, action urgent

**Action Plan**:
- 31-60 hari: Intensifkan reminder
- 61-90 hari: Telepon follow up, kunjungan
- 91-120 hari: Evaluasi restrukturisasi
- >120 hari: Proses collection legal

#### D. Konsentrasi Risiko

**Top 10 Largest Exposure**:
- Nama nasabah (disamarkan)
- Total O/S
- Persentase dari total portofolio
- Kolektibilitas
- Risk rating

**Standar**:
- Single exposure < 10% dari modal
- Top 10 exposure < 50% dari modal

**Cara Menggunakan**:
- Identifikasi konsentrasi berlebihan
- Monitor nasabah besar secara khusus
- Mitigasi: Diversifikasi, sindikasi

### Tab 2: NPF Deep Dive

#### A. NPF Breakdown by Kolektibilitas

**Visualisasi**: Stacked Bar Chart

**Segmen**:
- Kol 3 (Kurang Lancar): Kuning
- Kol 4 (Diragukan): Orange
- Kol 5 (Macet): Merah

**Metrik**:
- Jumlah rekening per kol
- Total O/S per kol
- Persentase dari total NPF

**Cara Membaca**:
- Kol 5 dominan: ❌ Masalah kronis
- Kol 3 dominan: ⚠️ Masih ada harapan recovery

#### B. Tabel NPF Detail

**Kolom**:
1. No. Kontrak
2. Nama Nasabah
3. Cabang
4. AO
5. Total O/S
6. Tunggakan (hari)
7. Kolektibilitas
8. CKPN
9. Action Plan
10. Status Follow Up

**Fitur**:
- Filter by kolektibilitas
- Filter by cabang
- Sort by tunggakan (prioritas)
- Export untuk collection team

**Cara Menggunakan**:
1. Sort by tunggakan descending → Prioritas tertinggi
2. Filter Kol 5 → Evaluasi write-off
3. Filter Kol 3-4 → Evaluasi restrukturisasi
4. Export → Distribusi ke collection team

### Tab 3: CKPN Management

#### A. CKPN Summary

**Metrik**:
- Total CKPN yang harus dibentuk
- CKPN yang sudah dibentuk
- Gap (kekurangan/kelebihan)
- Coverage ratio

#### B. CKPN Breakdown by Kolektibilitas

**Tabel**:
| Kol | O/S | Rate | CKPN Required | CKPN Actual | Gap |
|-----|-----|------|---------------|-------------|-----|
| 1 | xxx | 1% | xxx | xxx | xxx |
| 2 | xxx | 5% | xxx | xxx | xxx |
| 3 | xxx | 15% | xxx | xxx | xxx |
| 4 | xxx | 50% | xxx | xxx | xxx |
| 5 | xxx | 100% | xxx | xxx | xxx |

**Cara Menggunakan**:
- Identifikasi gap CKPN
- Hitung kebutuhan tambahan CKPN
- Koordinasi dengan accounting untuk pembentukan

### Tab 4: Restructuring Guard

#### A. KPI Cards (3 Metrik Restrukturisasi)

**1. Total O/S Restrukturisasi**
- **Metrik**: Total O/S pembiayaan restruktur
- **Warna**: Orange
- **Ikon**: File Damage

**2. Restru-to-Total Ratio**
- **Metrik**: Persentase restruktur dari total portofolio
- **Rumus**: O/S Restru / Total O/S × 100%
- **Warna**: Biru
- **Ikon**: Pie Chart
- **Standar**: < 10%

**3. Vintage Failure Rate**
- **Metrik**: Persentase restruktur yang gagal (kembali NPF)
- **Rumus**: Restru Gagal / Total Restru × 100%
- **Warna**: Hijau (<10%) / Merah (≥10%)
- **Ikon**: Pulse (dinamis)
- **Standar**: < 10%

#### B. Tabel Monitoring Restrukturisasi

**Kolom**:
1. No. Kontrak
2. Nama Nasabah
3. Tanggal Restruktur
4. Jenis Restruktur
5. O/S Sebelum
6. O/S Sesudah
7. Status Pembayaran
8. Kolektibilitas Saat Ini
9. Performance (Good/Bad)

**Cara Menggunakan**:
- Monitor performa pasca restruktur
- Identifikasi restruktur yang gagal
- Evaluasi efektivitas jenis restruktur
- Decision making untuk restruktur baru

### Cara Menggunakan Halaman Ini

**Workflow Harian Risk Management**:
1. **Tab 1 (RGEC)**: Cek 4 KPI utama → Apakah ada anomali?
2. **Tab 2 (NPF Deep Dive)**: Review NPF detail → Prioritas collection
3. **Tab 3 (CKPN)**: Cek coverage ratio → Apakah cukup?
4. **Tab 4 (Restructuring)**: Monitor performa restruktur

**Meeting Mingguan**:
1. Export NPF detail dari Tab 2
2. Presentasikan aging analysis
3. Diskusikan action plan per kategori aging
4. Review progress collection week-to-date

---

