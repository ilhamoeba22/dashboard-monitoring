# PANDUAN PER HALAMAN - BAGIAN 5

## 🤝 HALAMAN 16: SINDIKASI

### Tujuan Halaman
Monitoring pembiayaan sindikasi/konsorsium dengan multiple lender.

### Untuk Siapa?
- **Business Development**: Koordinasi sindikasi
- **Risk Management**: Monitor exposure
- **Manajemen**: Evaluasi partnership

### Komponen Halaman

#### A. KPI Cards (3 Metrik)

**1. Total Pembiayaan Sindikasi**
- Jumlah rekening sindikasi aktif
- Warna: Ungu

**2. Total O/S Sindikasi**
- Outstanding pembiayaan sindikasi
- Porsi BPRS HIK MCI

**3. Partner Banks**
- Jumlah bank partner dalam sindikasi
- List bank partner

#### B. Tabel Data Sindikasi

**Kolom**:
1. No. Kontrak
2. Nama Nasabah
3. Total Plafon Sindikasi
4. Porsi BPRS HIK MCI
5. Bank Partner
6. Porsi Partner
7. Lead Arranger
8. Status
9. Kolektibilitas

**Cara Menggunakan**:
- Monitor exposure per sindikasi
- Koordinasi dengan partner bank
- Evaluasi performa sindikasi

---

## 📊 HALAMAN 17: COLLECTION MONITORING

### Tujuan Halaman
Monitoring aktivitas collection dan efektivitas tim collection.

### Untuk Siapa?
- **Collection Team**: Tracking aktivitas harian
- **Supervisor**: Evaluasi tim
- **Manajemen**: Monitoring efektivitas

### Komponen Halaman

#### A. KPI Cards (4 Metrik)

**1. Total Tunggakan**
- Total tunggakan yang harus dicollect
- Warna: Merah

**2. Collected Today**
- Tunggakan yang berhasil dicollect hari ini
- Warna: Hijau

**3. Collection Rate**
- Persentase berhasil collect
- Target: > 70%

**4. Outstanding Collection**
- Sisa tunggakan yang belum tercollect
- Prioritas follow up

#### B. Activity Log

**Kolom**:
1. Tanggal & Waktu
2. No. Kontrak
3. Nama Nasabah
4. Collector
5. Aktivitas (Telepon/Kunjungan/WA)
6. Result
7. Janji Bayar
8. Next Action

**Status Result**:
- ✅ **Berhasil**: Nasabah bayar
- 🟡 **Janji Bayar**: Nasabah berjanji
- 🔴 **Tidak Angkat**: Tidak bisa dihubungi
- ⚪ **Reschedule**: Perlu follow up lagi

#### C. Grafik Collection Performance

**Jenis**: Bar Chart

**Metrik**:
- Target collection per hari
- Realisasi collection
- Achievement rate

**Cara Membaca**:
- Bar hijau > target → Exceed ✅
- Bar hijau < target → Below ⚠️

### Cara Menggunakan

**Workflow Harian**:
1. Cek Total Tunggakan → Prioritas hari ini
2. Input aktivitas collection ke log
3. Update result setiap aktivitas
4. Monitor achievement rate
5. Follow up janji bayar

---

## 🎯 HALAMAN 18: RISK AGGREGATION

### Tujuan Halaman
Agregasi dan analisis risiko portofolio secara komprehensif.

### Untuk Siapa?
- **Risk Management**: Analisis risiko holistik
- **Direksi**: Risk appetite monitoring
- **Compliance**: Regulatory compliance

### Komponen Halaman

#### A. Risk Dashboard

**Metrik Risiko**:

**1. Credit Risk**
- NPF Ratio
- Concentration risk
- Large exposure

**2. Liquidity Risk**
- FDR
- Maturity mismatch
- Funding gap

**3. Operational Risk**
- Process compliance
- System downtime
- Fraud cases

**4. Market Risk**
- Profit rate risk
- FX exposure (jika ada)

#### B. Risk Heatmap

**Visualisasi**: Matrix 5×5

**Sumbu X**: Likelihood (Kemungkinan)
**Sumbu Y**: Impact (Dampak)

**Warna**:
- 🟢 **Low Risk**: Likelihood rendah + Impact rendah
- 🟡 **Medium Risk**: Salah satu tinggi
- 🔴 **High Risk**: Keduanya tinggi

**Risiko yang Dimonitor**:
- Konsentrasi sektor
- Konsentrasi geografis
- Konsentrasi nasabah besar
- NPF meningkat
- Likuiditas menurun
- dll

#### C. Risk Mitigation Plan

**Tabel**:
1. Risk ID
2. Risk Description
3. Risk Level
4. Mitigation Plan
5. PIC
6. Target Date
7. Status

**Status**:
- 🔵 **Planned**: Belum dimulai
- 🟡 **In Progress**: Sedang berjalan
- 🟢 **Completed**: Selesai
- 🔴 **Overdue**: Terlambat

### Cara Menggunakan

**Workflow Risk Management**:
1. Review Risk Dashboard → Identifikasi risiko tinggi
2. Analisis Heatmap → Prioritas mitigasi
3. Update Mitigation Plan → Track progress
4. Eskalasi ke manajemen jika perlu

---

## 📈 HALAMAN 19: PERKEMBANGAN (DETAIL)

### Tujuan Halaman
Analisis mendalam perkembangan pembiayaan dengan multiple perspectives.

### Untuk Siapa?
- **Business Intelligence**: Deep analysis
- **Manajemen**: Strategic planning
- **Board**: Board reporting

### Komponen Halaman

#### A. Multi-Dimensional Analysis

**Dimensi**:
1. **Time Series**: Tren waktu
2. **Geographic**: Per wilayah/cabang
3. **Product**: Per segmen/akad
4. **Customer**: Per tipe nasabah
5. **Channel**: Per channel penyaluran

#### B. Comparative Analysis

**Perbandingan**:
- YoY (Year-over-Year)
- MoM (Month-over-Month)
- QoQ (Quarter-over-Quarter)
- vs Budget
- vs Industry

#### C. Forecasting

**Model**:
- Linear regression
- Moving average
- Seasonal adjustment

**Output**:
- Proyeksi 3 bulan
- Proyeksi 6 bulan
- Proyeksi 12 bulan
- Confidence interval

---

## 💼 HALAMAN 20: PPKA (POLA PEMBIAYAAN KARYAWAN AKHIR)

### Tujuan Halaman
Monitoring pembiayaan khusus untuk karyawan dengan pola pembayaran di akhir masa kerja.

### Untuk Siapa?
- **HR**: Koordinasi dengan karyawan
- **Risk Management**: Monitor risiko khusus
- **Payroll**: Koordinasi pemotongan gaji

### Komponen Halaman

#### A. KPI Cards

**1. Total PPKA Aktif**
- Jumlah pembiayaan PPKA aktif

**2. Total O/S PPKA**
- Outstanding PPKA

**3. Maturity This Year**
- Jumlah yang jatuh tempo tahun ini

**4. Risk Level**
- Level risiko (berdasarkan usia pensiun)

#### B. Tabel PPKA

**Kolom**:
1. No. Kontrak
2. Nama Karyawan
3. Instansi
4. Jabatan
5. Usia
6. Tahun Pensiun
7. O/S
8. Angsuran Bulanan
9. Sisa Tenor
10. Risk Level

**Risk Level**:
- 🟢 **Low**: > 5 tahun ke pensiun
- 🟡 **Medium**: 3-5 tahun ke pensiun
- 🔴 **High**: < 3 tahun ke pensiun

### Cara Menggunakan

**Workflow Monitoring**:
1. Identifikasi yang akan pensiun < 3 tahun
2. Koordinasi dengan HR instansi
3. Pastikan mekanisme pelunasan clear
4. Monitor pembayaran rutin

---

## 🔄 HALAMAN 21: REPAYMENT RATE NEW

### Tujuan Halaman
Versi baru analisis repayment rate dengan fitur tambahan dan visualisasi lebih baik.

### Untuk Siapa?
- **Collection Team**: Monitoring harian
- **Risk Management**: Analisis kualitas
- **Manajemen**: Evaluasi efektivitas

### Komponen Halaman

#### A. Enhanced KPI Cards

**Tambahan Metrik**:
- Repayment rate by vintage (cohort analysis)
- Repayment rate by channel
- Repayment rate by product

#### B. Cohort Analysis

**Analisis**:
- Performa pembayaran per cohort (bulan akad)
- Identifikasi cohort bermasalah
- Trend performa per cohort

**Contoh**:
- Cohort Jan 2024: Repayment rate 95%
- Cohort Feb 2024: Repayment rate 88%
- Cohort Mar 2024: Repayment rate 92%

**Insight**:
- Cohort Feb bermasalah → Investigasi penyebab
- Apakah ada perubahan proses approval?
- Apakah ada event khusus?

#### C. Predictive Analytics

**Model**:
- Prediksi repayment rate bulan depan
- Early warning nasabah berisiko
- Recommended action

### Cara Menggunakan

**Workflow Advanced**:
1. Analisis cohort → Identifikasi pola
2. Review predictive model → Antisipasi
3. Action plan preventif
4. Monitor efektivitas

---

