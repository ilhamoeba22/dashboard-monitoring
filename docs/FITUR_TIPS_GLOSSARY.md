# FITUR UMUM, TIPS, DAN GLOSSARY

## 5. FITUR UMUM SISTEM

### 5.1 Filter dan Pencarian

#### A. Filter Cabang
**Lokasi**: Header setiap halaman (pojok kanan atas)

**Cara Menggunakan**:
1. Klik dropdown "Filter Cabang"
2. Pilih cabang yang diinginkan
3. Data otomatis ter-filter
4. Pilih "Semua Cabang" untuk reset

**Tips**:
- Kepala cabang: Set default ke cabang sendiri
- Manajemen: Gunakan "Semua Cabang" untuk overview
- Bandingkan antar cabang dengan switch filter

#### B. Date Range Picker
**Fungsi**: Memilih periode data

**Cara Menggunakan**:
1. Klik ikon kalender
2. Pilih tanggal mulai
3. Pilih tanggal akhir
4. Klik "Apply"

**Preset Tersedia**:
- Hari Ini
- 7 Hari Terakhir
- 30 Hari Terakhir
- Bulan Ini
- Bulan Lalu
- Quarter Ini
- Tahun Ini

#### C. Search Box
**Fungsi**: Pencarian cepat data

**Tips Pencarian**:
- Ketik minimal 3 karakter
- Gunakan no kontrak untuk hasil pasti
- Gunakan nama untuk pencarian fuzzy
- Case insensitive (tidak peduli huruf besar/kecil)

### 5.2 Export Data

#### A. Export ke Excel
**Format**: .xlsx (Excel 2007+)

**Cara Menggunakan**:
1. Klik tombol "Export Excel"
2. File otomatis terdownload
3. Buka dengan Microsoft Excel atau Google Sheets

**Isi File**:
- Sheet 1: Data tabel
- Sheet 2: Summary/KPI
- Sheet 3: Chart data (jika ada)

**Tips**:
- Data sudah terformat (currency, percentage)
- Bisa langsung digunakan untuk laporan
- Pivot table ready

#### B. Export ke PDF
**Format**: .pdf

**Cara Menggunakan**:
1. Klik tombol "Export PDF"
2. File otomatis terdownload
3. Buka dengan PDF reader

**Isi File**:
- Header: Logo, judul, tanggal
- KPI Cards
- Chart/grafik
- Tabel data
- Footer: Halaman, timestamp

**Tips**:
- Landscape orientation untuk tabel lebar
- Print ready
- Bisa langsung untuk presentasi

#### C. Copy to Clipboard
**Fungsi**: Copy data untuk paste ke aplikasi lain

**Cara Menggunakan**:
1. Klik tombol "Copy"
2. Paste (Ctrl+V) ke Excel/Word/Email

### 5.3 Refresh Data

#### A. Auto Refresh
**Interval**: Setiap 5 menit (configurable)

**Indikator**:
- Ikon loading saat refresh
- Timestamp "Last updated"

#### B. Manual Refresh
**Tombol**: 🔄 Refresh (pojok kanan atas)

**Cara Menggunakan**:
1. Klik tombol refresh
2. Tunggu loading selesai
3. Data ter-update

**Kapan Menggunakan**:
- Setelah input data baru di CBS
- Setelah proses batch selesai
- Saat data terlihat tidak update

### 5.4 Notifikasi dan Alert

#### A. Alert Otomatis
**Trigger**:
- NPF melewati threshold (5%)
- FDR melewati batas (92%)
- Tunggakan meningkat signifikan
- Target tidak tercapai

**Tampilan**:
- Pop-up notification
- Badge merah di menu
- Email notification (optional)

#### B. Dashboard Alert
**Lokasi**: Banner di atas dashboard

**Jenis Alert**:
- 🔴 **Critical**: Perlu action segera
- 🟡 **Warning**: Perlu perhatian
- 🔵 **Info**: Informasi penting

**Contoh**:
```
⚠️ WARNING: NPF Ratio mencapai 5.2%, melewati threshold 5%
Action: Review NPF detail dan buat action plan
```

### 5.5 User Preferences

#### A. Dashboard Customization
**Fitur**:
- Pilih KPI yang ditampilkan
- Atur urutan widget
- Simpan layout favorit

#### B. Default Settings
**Setting**:
- Default cabang
- Default date range
- Default items per page
- Notification preferences

**Cara Setting**:
1. Klik ikon user (pojok kanan atas)
2. Pilih "Preferences"
3. Atur sesuai kebutuhan
4. Klik "Save"

---

## 6. TIPS DAN BEST PRACTICES

### 6.1 Tips untuk Direksi/Manajemen

#### A. Morning Routine (15 menit)
1. **Buka Executive Overview**
   - Cek 4 KPI utama
   - Lihat grafik tren
   - Review top 10 cabang NPF

2. **Buka Quality & Risk**
   - Cek NPF ratio
   - Review aging analysis
   - Identifikasi red flags

3. **Buka Target RBB**
   - Cek achievement rate
   - Review progress bar
   - Identifikasi cabang laggard

**Output**: Briefing point untuk meeting pagi

#### B. Weekly Review (1 jam)
1. **Analisis Tren**
   - Buka Perkembangan
   - Analisis growth rate
   - Identifikasi pola

2. **Quality Check**
   - Review kolektibilitas
   - Analisis migration matrix
   - Evaluasi CKPN

3. **Performance Review**
   - Review per cabang
   - Review per AO
   - Identifikasi top/bottom performer

**Output**: Action plan mingguan

#### C. Monthly Meeting (2-3 jam)
1. **Comprehensive Review**
   - Export semua data key
   - Prepare presentation
   - Analisis variance vs target

2. **Deep Dive**
   - Fokus ke area bermasalah
   - Root cause analysis
   - Buat action plan detail

3. **Forward Looking**
   - Proyeksi 3 bulan ke depan
   - Adjust strategi jika perlu
   - Set target bulan depan

### 6.2 Tips untuk Kepala Cabang

#### A. Daily Monitoring
1. **Filter ke cabang sendiri**
2. **Cek Jatuh Tempo & Early Warning**
   - Follow up nasabah prioritas
   - Koordinasi dengan AO
3. **Monitor Collection**
   - Review activity log
   - Cek achievement rate
4. **Review AO Performance**
   - Identifikasi yang perlu support

#### B. Weekly Coaching
1. **Review performa per AO**
2. **Identifikasi gap**
3. **Buat coaching plan**
4. **Follow up action items**

#### C. Monthly Evaluation
1. **Export data cabang**
2. **Prepare untuk meeting dengan manajemen**
3. **Evaluasi pencapaian target**
4. **Set target bulan depan**

### 6.3 Tips untuk Account Officer (AO)

#### A. Daily Tasks
1. **Cek portofolio sendiri**
   - Filter by AO name
   - Review nasabah yang perlu follow up

2. **Jatuh Tempo Hari Ini**
   - Cek antrian jatuh tempo
   - Follow up nasabah saldo kurang

3. **Collection**
   - Follow up tunggakan
   - Input activity log
   - Update status

#### B. Weekly Planning
1. **Review performa**
   - Cek ranking
   - Bandingkan dengan target
   - Identifikasi gap

2. **Pipeline Management**
   - Identifikasi peluang top-up
   - Follow up aplikasi baru
   - Retention nasabah akan jatuh tempo

#### C. Monthly Target
1. **Review achievement**
2. **Analisis gap**
3. **Buat action plan**
4. **Koordinasi dengan kepala cabang**

### 6.4 Tips untuk Collection Team

#### A. Prioritization
**Prioritas 1 (Urgent)**:
- OVERDUE + Saldo Kurang
- Kol 3-5 dengan O/S besar
- Janji bayar yang tidak ditepati

**Prioritas 2 (Important)**:
- CRITICAL (0-3 hari) + Saldo Kurang
- Kol 2 dengan tren memburuk
- Nasabah sulit dihubungi

**Prioritas 3 (Normal)**:
- WARNING (4-7 hari)
- Kol 1-2 dengan keterlambatan minor

#### B. Collection Strategy
**Soft Approach** (Kol 1-2):
- WA reminder
- Telepon friendly
- Email reminder

**Medium Approach** (Kol 3):
- Telepon intensif
- Kunjungan lapangan
- Surat peringatan 1

**Hard Approach** (Kol 4-5):
- Kunjungan lapangan wajib
- Surat peringatan 2-3
- Koordinasi legal
- Evaluasi restruktur/write-off

#### C. Documentation
**Wajib Didokumentasikan**:
- Setiap kontak dengan nasabah
- Hasil kunjungan
- Janji bayar
- Alasan keterlambatan
- Next action plan

**Input ke System**:
- Activity log
- Update status
- Upload bukti (foto, surat, dll)

### 6.5 Tips untuk Risk Management

#### A. Daily Monitoring
1. **Quality & Risk Dashboard**
   - Cek NPF ratio
   - Review aging
   - Monitor konsentrasi

2. **Alert Management**
   - Review alert otomatis
   - Investigasi anomali
   - Eskalasi jika perlu

#### B. Weekly Analysis
1. **Trend Analysis**
   - NPF trend
   - Kolektibilitas migration
   - CKPN adequacy

2. **Risk Reporting**
   - Prepare weekly risk report
   - Highlight key risks
   - Recommend mitigation

#### C. Monthly Risk Committee
1. **Comprehensive Risk Report**
   - Credit risk
   - Liquidity risk
   - Operational risk
   - Market risk

2. **Risk Mitigation Update**
   - Progress mitigation plan
   - New risks identified
   - Recommendation

---

## 7. GLOSSARY (KAMUS ISTILAH)

### A. Istilah Pembiayaan

**Account Officer (AO)**
Staff yang bertanggung jawab mengelola portofolio nasabah pembiayaan.

**Aging**
Klasifikasi tunggakan berdasarkan jumlah hari keterlambatan.

**Akad**
Perjanjian/kontrak pembiayaan sesuai prinsip syariah.

**Angsuran**
Pembayaran cicilan pembiayaan (pokok + margin).

**Autodebet**
Pemotongan otomatis dari rekening tabungan untuk pembayaran angsuran.

**BPRS**
Bank Pembiayaan Rakyat Syariah.

**CBS (Core Banking System)**
Sistem perbankan inti yang mencatat semua transaksi.

**CKPN (Cadangan Kerugian Penurunan Nilai)**
Dana cadangan untuk mengantisipasi kerugian dari pembiayaan bermasalah.

**Collection**
Proses penagihan tunggakan pembiayaan.

**Coverage Ratio**
Rasio CKPN terhadap NPF, minimal 100%.

**DPK (Dana Pihak Ketiga)**
Dana masyarakat yang dihimpun bank (tabungan, deposito).

**Early Warning System (EWS)**
Sistem deteksi dini potensi masalah pembiayaan.

**FDR (Financing to Deposit Ratio)**
Rasio pembiayaan terhadap DPK, ideal 80-92%.

**Ijarah**
Akad sewa dalam pembiayaan syariah.

**Jatuh Tempo**
Tanggal pembayaran angsuran yang telah dijadwalkan.

**Kolektibilitas**
Klasifikasi kualitas pembiayaan (1=Lancar, 2=DPK, 3=Kurang Lancar, 4=Diragukan, 5=Macet).

**Margin**
Keuntungan bank dalam pembiayaan (equivalent dengan bunga).

**Mudharabah**
Akad bagi hasil dengan bank sebagai pemilik modal.

**Murabahah**
Akad jual beli dengan margin yang disepakati.

**Musyarakah**
Akad kerjasama modal antara bank dan nasabah.

**NOA (Number of Account)**
Jumlah rekening pembiayaan.

**NPF (Non-Performing Financing)**
Pembiayaan bermasalah (Kol 3, 4, 5).

**O/S (Outstanding)**
Saldo pokok pembiayaan yang masih harus dibayar.

**Plafon**
Jumlah maksimal pembiayaan yang disetujui.

**Qardh**
Pinjaman kebajikan tanpa margin.

**RBB (Rencana Bisnis Bank)**
Rencana strategis dan target tahunan bank.

**Restrukturisasi**
Perubahan syarat pembiayaan untuk membantu nasabah kesulitan bayar.

**RGEC**
Framework penilaian kesehatan bank (Risk profile, Good corporate governance, Earnings, Capital).

**Segmen**
Kategori pembiayaan (Konsumtif, Modal Kerja, Investasi).

**Settlement**
Pelunasan pembiayaan.

**Sindikasi**
Pembiayaan bersama oleh beberapa bank.

**Top-Up**
Penambahan plafon pembiayaan untuk nasabah existing.

**Tunggakan**
Angsuran yang belum dibayar sesuai jadwal.

**Vintage**
Cohort pembiayaan berdasarkan periode akad.

**Write-Off**
Penghapusbukuan pembiayaan macet dari neraca.

**Yield**
Tingkat imbal hasil portofolio pembiayaan.

**YoY (Year-over-Year)**
Perbandingan dengan periode yang sama tahun lalu.

**MoM (Month-over-Month)**
Perbandingan dengan bulan sebelumnya.

**YTD (Year-to-Date)**
Akumulasi dari awal tahun sampai saat ini.

### B. Istilah Teknis Sistem

**Dashboard**
Halaman utama yang menampilkan ringkasan informasi penting.

**Drill-Down**
Kemampuan untuk melihat detail dari data summary.

**Export**
Mengunduh data ke format file (Excel, PDF).

**Filter**
Menyaring data berdasarkan kriteria tertentu.

**KPI (Key Performance Indicator)**
Metrik utama untuk mengukur performa.

**Real-time**
Data yang update secara langsung/instant.

**Refresh**
Memuat ulang data terbaru.

**Widget**
Komponen visual di dashboard (chart, card, table).

---

## 8. TROUBLESHOOTING

### Masalah Umum dan Solusi

**1. Data tidak muncul / Loading terus**
- **Penyebab**: Koneksi internet lambat atau server sibuk
- **Solusi**: 
  - Refresh halaman (F5)
  - Clear browser cache
  - Coba browser lain
  - Hubungi IT support jika masih bermasalah

**2. Data tidak sesuai dengan CBS**
- **Penyebab**: Belum sinkronisasi atau proses batch belum selesai
- **Solusi**:
  - Cek timestamp "Last updated"
  - Tunggu proses batch selesai (biasanya jam 23:00)
  - Klik refresh manual
  - Hubungi IT jika selisih signifikan

**3. Export gagal**
- **Penyebab**: Data terlalu besar atau browser block pop-up
- **Solusi**:
  - Kurangi date range
  - Allow pop-up untuk website ini
  - Gunakan filter untuk kurangi data
  - Coba browser lain

**4. Filter tidak bekerja**
- **Penyebab**: Bug atau cache browser
- **Solusi**:
  - Clear filter dan apply ulang
  - Refresh halaman
  - Clear browser cache

**5. Lupa password**
- **Solusi**:
  - Klik "Forgot Password" di login page
  - Ikuti instruksi reset via email
  - Hubungi IT support jika tidak bisa

---

## 9. KONTAK SUPPORT

### IT Support
- **Email**: it.support@bprshikmci.co.id
- **Telepon**: (021) xxx-xxxx ext. 123
- **WhatsApp**: 0812-xxxx-xxxx
- **Jam Kerja**: Senin-Jumat, 08:00-17:00

### User Guide & Training
- **Email**: training@bprshikmci.co.id
- **Request Training**: Hubungi HR Department

### Bug Report & Feature Request
- **Email**: dev.team@bprshikmci.co.id
- **Format Laporan**:
  - Screenshot error
  - Langkah reproduksi
  - Browser & OS yang digunakan
  - User ID

---

## 10. UPDATE LOG

### Version 2.0 (Current)
- ✅ Transparent background icons untuk semua KPI cards
- ✅ Uniform card height (160px minimum)
- ✅ Enhanced Quality & Risk dengan 4 tabs
- ✅ Improved Jatuh Tempo dengan WA Blast
- ✅ New Repayment Rate analysis
- ✅ Collection Monitoring module

### Version 1.5
- ✅ Export to Excel/PDF
- ✅ Advanced filtering
- ✅ Real-time data sync
- ✅ Mobile responsive

### Version 1.0
- ✅ Initial release
- ✅ Basic dashboard
- ✅ Core modules

---

**© 2024 BPRS HIK MCI - Monitoring Dashboard Pembiayaan**
**Dokumen ini bersifat RAHASIA dan hanya untuk internal BPRS HIK MCI**

