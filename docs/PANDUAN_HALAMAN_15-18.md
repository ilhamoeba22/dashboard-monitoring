# PANDUAN PER HALAMAN - BAGIAN 4

## 💼 HALAMAN 12: TOP-UP

### Tujuan Halaman
Monitoring penambahan plafon (top-up) untuk nasabah existing dan analisis performa top-up.

### Untuk Siapa?
- **Business Development**: Strategi cross-selling
- **AO**: Identifikasi peluang top-up
- **Risk Management**: Monitor risiko top-up

### Komponen Halaman

#### A. KPI Cards (3 Metrik)

**1. Total Transaksi**
- Jumlah transaksi top-up bulan berjalan
- Warna: Hijau
- Indikator: Aktivitas bisnis

**2. Total Volume Plafon Baru**
- Total nilai top-up bulan ini
- Warna: Biru
- Info tambahan: 
  - Naik: Jumlah plafon naik
  - Turun: Jumlah plafon turun

**3. Tipe Nasabah**
- Breakdown:
  - Top Up: Nasabah tambah plafon
  - Ulangan: Nasabah pelunasan + akad baru
  - Retention: Nasabah perpanjang
- Visualisasi: Mini donut chart

#### B. Grafik Tren Top-Up (12 Bulan)

**Jenis**: Combo Chart

**Bar**: Volume top-up per bulan
**Line**: Jumlah transaksi per bulan

**Cara Membaca**:
- Bar tinggi → Volume besar
- Line tinggi → Frekuensi tinggi
- Bar tinggi + Line rendah → Top-up besar (corporate)
- Bar rendah + Line tinggi → Top-up kecil (retail)

#### C. Analisis Top-Up by Segmen

**Segmen**:
- Konsumtif
- Modal Kerja
- Investasi

**Metrik per Segmen**:
- Jumlah transaksi
- Total volume
- Average size
- Growth rate

**Insight**:
- Segmen mana yang paling aktif top-up?
- Average size menunjukkan segmen target
- Growth rate menunjukkan potensi

#### D. Tabel Data Top-Up

**Kolom**:
1. No. Kontrak Lama
2. No. Kontrak Baru
3. Nama Nasabah
4. Cabang
5. AO
6. Tanggal Top-Up
7. Plafon Lama
8. Plafon Baru
9. Selisih (Top-Up Amount)
10. Tipe (Naik/Turun)
11. Segmen
12. Kol Sebelum Top-Up
13. Kol Saat Ini
14. Performance

**Badge Performance**:
- 🟢 **Good**: Kol 1, bayar lancar
- 🟡 **Watch**: Kol 2
- 🔴 **Bad**: Kol 3-5

**Filter**:
- Filter by tipe (naik/turun)
- Filter by segmen
- Filter by performance
- Filter by periode

### Cara Menggunakan

**Use Case 1: Evaluasi Strategi Top-Up**
1. Cek KPI Cards → Volume top-up sehat?
2. Analisis per Segmen → Fokus ke segmen mana?
3. Review Performance → Apakah top-up ke nasabah yang tepat?

**Use Case 2: Identifikasi Peluang**
1. Filter: Good performance
2. Identifikasi nasabah potensial untuk top-up lagi
3. Koordinasi dengan AO untuk approach

**Use Case 3: Risk Monitoring**
1. Filter: Bad performance
2. Identifikasi top-up yang bermasalah
3. Evaluasi kriteria approval top-up

**Tips**:
- Top-up hanya untuk nasabah Kol 1
- Monitor performa 3-6 bulan pasca top-up
- Avoid top-up untuk tutup tunggakan

---

## 💹 HALAMAN 13: YIELD

### Tujuan Halaman
Analisis tingkat imbal hasil (yield) portofolio pembiayaan untuk evaluasi profitabilitas.

### Untuk Siapa?
- **Treasury**: Analisis profitabilitas
- **Manajemen**: Evaluasi pricing strategy
- **Business Development**: Strategi produk

### Komponen Halaman

#### A. KPI Cards (4 Metrik)

**1. Portfolio Yield**
- Metrik: Rata-rata yield seluruh portofolio
- Rumus: (Total Pendapatan Margin / Total O/S) × 100%
- Warna: Biru
- Target: Sesuai RKAP (biasanya 15-20%)

**2. Pendapatan Margin Bulan Ini**
- Total pendapatan margin bulan berjalan
- Warna: Hijau
- Indikator: Revenue realisasi

**3. Projected Annual Revenue**
- Proyeksi pendapatan tahunan
- Rumus: Pendapatan bulan ini × 12
- Warna: Ungu

**4. Yield Gap vs Target**
- Selisih yield aktual vs target
- Warna: Hijau (positif) / Merah (negatif)

#### B. Grafik Yield Trend (12 Bulan)

**Jenis**: Line Chart dengan area fill

**Line**: Portfolio yield per bulan (%)
**Benchmark Line**: Target yield (garis putus-putus)

**Cara Membaca**:
- Line di atas benchmark → Exceed target ✅
- Line di bawah benchmark → Below target ⚠️
- Tren naik → Pricing membaik
- Tren turun → Perlu review pricing

#### C. Yield by Segmen

**Visualisasi**: Bar Chart Horizontal

**Segmen**:
- Konsumtif: Biasanya yield tertinggi (18-25%)
- Modal Kerja: Yield menengah (15-20%)
- Investasi: Yield rendah (12-18%)

**Metrik per Segmen**:
- Average yield
- Total O/S
- Kontribusi revenue
- Benchmark yield

**Insight**:
- Segmen mana yang paling profitable?
- Apakah pricing kompetitif?
- Perlu adjust pricing?

#### D. Yield by Akad

**Akad**:
- Murabahah: Fixed margin
- Musyarakah: Bagi hasil
- Mudharabah: Bagi hasil
- Ijarah: Sewa

**Metrik**:
- Average yield per akad
- Volume per akad
- Revenue contribution

#### E. Tabel Detail Yield

**Kolom**:
1. Segmen/Akad
2. Total O/S
3. Pendapatan Margin
4. Yield (%)
5. Target Yield (%)
6. Gap (%)
7. Jumlah Rekening
8. Avg Yield per Rekening

**Filter**:
- Filter by segmen
- Filter by akad
- Filter by cabang

### Cara Menggunakan

**Workflow Analisis Yield**:
1. Cek Portfolio Yield → Apakah meet target?
2. Analisis Trend → Apakah membaik/memburuk?
3. Breakdown by Segmen → Segmen mana yang underperform?
4. Review Pricing → Perlu adjustment?

**Contoh Kasus**:
- **Kasus 1**: Yield turun dari 18% ke 16%
  - Analisis: Cek per segmen
  - Temuan: Konsumtif turun karena kompetisi
  - Action: Review pricing, tingkatkan value proposition

- **Kasus 2**: Yield Modal Kerja 12% (target 15%)
  - Analisis: Below target
  - Action: Adjust pricing untuk akad baru, review existing

**Tips**:
- Balance antara yield dan volume
- Yield terlalu tinggi → Risiko kehilangan nasabah
- Yield terlalu rendah → Profitabilitas terancam
- Monitor competitor pricing

---

## 🏦 HALAMAN 14: SETTLEMENT (PELUNASAN)

### Tujuan Halaman
Monitoring pelunasan pembiayaan untuk analisis cash flow dan customer behavior.

### Untuk Siapa?
- **Treasury**: Proyeksi cash flow
- **Business Development**: Analisis churn
- **Manajemen**: Evaluasi retention

### Komponen Halaman

#### A. KPI Cards (4 Metrik)

**1. Total Pelunasan Bulan Ini**
- Jumlah rekening yang lunas bulan berjalan
- Warna: Biru

**2. Volume Pelunasan**
- Total nilai pelunasan (Rupiah)
- Warna: Hijau
- Impact: Cash inflow

**3. Early Settlement**
- Jumlah pelunasan sebelum jatuh tempo
- Warna: Orange
- Indikator: Prepayment risk

**4. Retention Rate**
- Persentase nasabah lunas yang akad baru
- Rumus: (Akad Baru / Total Lunas) × 100%
- Target: > 60%

#### B. Grafik Pelunasan Trend (12 Bulan)

**Jenis**: Stacked Bar Chart

**Segmen**:
- Pelunasan Normal (jatuh tempo)
- Pelunasan Dipercepat (early settlement)
- Pelunasan Restruktur

**Cara Membaca**:
- Bar tinggi → Cash inflow besar
- Early settlement tinggi → Perlu analisis penyebab
- Retention rate rendah → Churn tinggi ⚠️

#### C. Analisis Penyebab Pelunasan

**Kategori**:
1. **Jatuh Tempo Normal** (60-70%)
   - Sesuai jadwal
   - Healthy churn

2. **Refinancing ke Kompetitor** (15-25%)
   - Churn negatif
   - Perlu retention strategy

3. **Tidak Butuh Lagi** (10-15%)
   - Natural churn
   - Acceptable

4. **Tidak Puas** (5-10%)
   - Churn negatif
   - Perlu perbaikan service

**Visualisasi**: Pie Chart

**Action Plan**:
- Refinancing tinggi → Review pricing & service
- Tidak puas tinggi → Improve customer experience

#### D. Tabel Data Pelunasan

**Kolom**:
1. No. Kontrak
2. Nama Nasabah
3. Tanggal Akad
4. Tanggal Jatuh Tempo
5. Tanggal Pelunasan
6. Plafon Awal
7. O/S Saat Lunas
8. Jumlah Pelunasan
9. Tipe (Normal/Early)
10. Alasan Pelunasan
11. Status Retention
12. AO

**Badge Status Retention**:
- 🟢 **Retained**: Akad baru
- 🟡 **Prospect**: Dalam proses approach
- 🔴 **Lost**: Pindah ke kompetitor
- ⚪ **Natural**: Tidak butuh lagi

**Filter**:
- Filter by tipe pelunasan
- Filter by status retention
- Filter by alasan
- Filter by periode

### Cara Menggunakan

**Workflow Retention**:
1. Cek Retention Rate → Apakah meet target?
2. Analisis Penyebab → Mengapa nasabah lunas?
3. Filter: Lost to competitor
4. Follow up untuk win-back

**Use Case 1: Improve Retention**
1. Identifikasi nasabah yang akan jatuh tempo 3 bulan lagi
2. Approach untuk renewal/top-up
3. Tawarkan benefit (rate discount, proses cepat)
4. Track conversion rate

**Use Case 2: Analisis Churn**
1. Filter: Lost to competitor
2. Analisis pola (segmen, pricing, service)
3. Identifikasi root cause
4. Buat improvement plan

**Tips**:
- Approach nasabah 2-3 bulan sebelum jatuh tempo
- Tawarkan benefit untuk retention
- Exit interview untuk feedback
- Monitor competitor offering

---

## 👥 HALAMAN 15: KARYAWAN (AO PERFORMANCE)

### Tujuan Halaman
Evaluasi performa individu Account Officer untuk performance management.

### Untuk Siapa?
- **Kepala Cabang**: Evaluasi tim
- **HR**: Performance appraisal
- **Manajemen**: Identifikasi top performer

### Komponen Halaman

#### A. KPI Cards (4 Metrik)

**1. Total AO Aktif**
- Jumlah AO yang aktif
- Warna: Biru

**2. Average O/S per AO**
- Rata-rata portofolio per AO
- Benchmark: 3-5 Miliar

**3. Average NOA per AO**
- Rata-rata jumlah rekening per AO
- Benchmark: 50-80 rekening

**4. Top Performer**
- AO dengan performa terbaik bulan ini
- Kriteria: O/S tertinggi + NPF terendah

#### B. Ranking Table AO

**Kolom**:
1. **Rank**: Peringkat
2. **Nama AO**
3. **Cabang**
4. **Total O/S**: Portofolio
5. **NOA**: Jumlah rekening
6. **Avg Size**: Rata-rata plafon
7. **NPF Ratio**: Kualitas portofolio
8. **Penyaluran Bulan Ini**: New business
9. **Growth (%)**: Pertumbuhan MoM
10. **Score**: Composite score
11. **Badge**: Gold/Silver/Bronze

**Scoring System**:
- O/S: 30%
- NPF Ratio: 30%
- Growth: 20%
- Penyaluran: 20%

**Badge**:
- 🥇 **Gold**: Score > 85
- 🥈 **Silver**: Score 70-85
- 🥉 **Bronze**: Score 50-70
- ⚪ **Need Improvement**: Score < 50

#### C. Grafik Distribusi Performa

**Jenis**: Scatter Plot

**Sumbu X**: Total O/S
**Sumbu Y**: NPF Ratio

**Kuadran**:
1. **Top Right**: High O/S + High NPF → ⚠️ High Risk
2. **Top Left**: Low O/S + High NPF → ❌ Poor Performer
3. **Bottom Right**: High O/S + Low NPF → ⭐ Star Performer
4. **Bottom Left**: Low O/S + Low NPF → 🔍 Potential

**Cara Membaca**:
- Star Performer → Reward & retain
- High Risk → Coaching on quality
- Poor Performer → PIP (Performance Improvement Plan)
- Potential → Coaching on growth

#### D. Detail Performa per AO

**Klik AO** → Modal detail:

**Tab 1: Portfolio Summary**
- Total O/S
- NOA
- Breakdown by segmen
- Breakdown by kolektibilitas

**Tab 2: Performance Metrics**
- Penyaluran YTD
- Pelunasan YTD
- Net growth
- NPF ratio trend

**Tab 3: Customer List**
- Daftar nasabah AO
- Status per nasabah
- Action items

**Tab 4: Achievement**
- Target vs realisasi
- Achievement rate
- Ranking history

### Cara Menggunakan

**Workflow Evaluasi Bulanan**:
1. Review Ranking Table
2. Identifikasi top 10% → Reward
3. Identifikasi bottom 10% → Coaching
4. Analisis scatter plot → Kategorisasi
5. Set target bulan depan

**Use Case 1: Performance Appraisal**
1. Export ranking table
2. Review individual detail
3. Prepare feedback session
4. Set improvement target

**Use Case 2: Coaching Session**
1. Identifikasi AO yang perlu coaching
2. Analisis detail performa
3. Identifikasi gap
4. Buat action plan

**Tips**:
- Balance antara quantity (O/S) dan quality (NPF)
- Reward top performer untuk motivasi
- Coaching untuk bottom performer
- Peer learning dari star performer

---

