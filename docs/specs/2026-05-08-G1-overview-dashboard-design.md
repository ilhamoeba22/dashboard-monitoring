# Executive Overview Dashboard (G1) Design & Architecture

## 1. Filosofi & Tujuan (UI/UX Pro Max)
Halaman **Overview** adalah **Landing Page Utama** untuk modul Pembiayaan. Target audiensnya adalah jajaran Direksi dan Manajer (Level Eksekutif).
Karena waktu eksekutif sangat terbatas, halaman ini didesain menggunakan prinsip:
1. **Zero Data Clutter**: Tidak menampilkan tabel mentah (seperti Nominatif) di halaman utama.
2. **Progressive Disclosure**: Menampilkan ringkasan tingkat atas (Macro Level), kemudian memberikan "Pintu Masuk" (navigasi) ke halaman spesifik untuk rincian (Micro Level).
3. **Exception-Based Management**: Fokus pada anomali dan risiko tinggi (seperti Top Nasabah Macet) yang membutuhkan aksi hari itu juga.

## 2. Kenapa "Compare" dan "Trend" Dihapus dari Overview?
Sebelumnya, Overview mencoba menjadi "Swiss Army Knife" dengan memasukkan tab Compare dan Trend. Ini adalah desain yang **buruk dan redundan** karena:
- Tren pertumbuhan MoM/YoY sudah memiliki "rumah" khusus yang jauh lebih canggih di `/financing/perkembangan`.
- Komparasi (cabang, AO, segmen) sudah memiliki analisis multidimensi di `/financing/rekapitulasi`.
- (Jawaban teknis: Nilai Outstanding dan NPF sebelumnya muncul dalam bentuk **persen** di Comparison Mode karena grafik tersebut mencoba menghitung *growth variance* antara dua titik waktu, bukan nilai mutlak. Ini sering membingungkan jika user mengharapkan nilai absolut).

Oleh karena itu, **Overview direkonstruksi murni menjadi Single-Page Realtime Dashboard**.

---

## 3. Struktur Blueprint Halaman Overview Baru

### A. The Global Header & Context
- **Title**: Executive Overview (Pembiayaan)
- **Subtitle**: Ringkasan performa dan kesehatan portofolio secara real-time.
- **Global Filter**: Dropdown `Semua Cabang` vs `Spesifik Cabang`. Berubahnya filter ini akan merefresh *seluruh* angka di dashboard.

### B. The Navigation Strip (Quick Actions)
Alih-alih menyembunyikan link di sidebar panjang, kita meletakkan 4 Kartu Navigasi Visual di bagian atas untuk akses satu klik ke *powerhouse* sistem:
1. 📊 **Master Console** (`/rekapitulasi`): Untuk analisis volume, NOA, O/S multidimensi.
2. 🛡️ **Quality & Risk** (`/quality`): Untuk monitoring NPF, aging, konsentrasi risiko.
3. 📋 **Data Nominatif** (`/nominatif`): Untuk pencarian data rinci per rekening nasabah.
4. 🎯 **Target RBB** (`/target`): Untuk melihat pencapaian pencairan vs target tahunan.

### C. Executive Scorecards (At a Glance)
Empat kotak metrik terpenting bagi Bank:
1. **Total Portofolio (Aset)**: Nilai O/S Pokok (Miliar/Triliun) + Jumlah Rekening (NOA) aktif.
2. **Total Tunggakan**: Nominal uang pokok yang sedang menunggak hari ini.
3. **Exposure NPF**: Total Outstanding yang berada di status macet (Kol 3, 4, 5).
4. **NPF Ratio**: Persentase NPF. *UI/UX logic: Border Hijau jika < 5%, Border Merah jika > 5%*.

### D. The Visual Analytics (Macro Pillars)
Dua visualisasi yang cukup ringan tapi menjawab dua pertanyaan makro:
1. **Donut Chart**: Komposisi Portofolio berdasarkan Kolektibilitas (Lancar vs DPK vs Kurang Lancar vs Diragukan vs Macet). Menjawab: *Sehatkah komposisi kita?*
2. **Area Chart**: Pertumbuhan/Pergerakan Total O/S selama 12 bulan terakhir. Memberikan konteks visual apakah grafik sedang naik atau turun secara agregat.

### E. The Actionable Exception (Top Risk Alerts)
Bagian paling bawah didedikasikan untuk **Early Warning System (EWS)**.
- **Tabel Mini**: Hanya menampilkan maksimal **Top 5 - 10 Nasabah NPF dengan O/S Tertinggi**.
- **Tujuan**: Begitu buka dashboard, Kepala Cabang / Direktur langsung tahu "Siapa pengutang macet terbesar kita hari ini?" dan langsung bisa menelpon AO yang bersangkutan.

## 4. Kesimpulan Logis
Desain ini "Sangat Masuk Akal" karena memecahkan masalah kognitif: *Information Overload*. Kita tidak memaksa eksekutif membaca ratusan baris data di halaman depan. Kita hanya memberikan "Ringkasan" (Scorecards), "Arah" (Quick Actions), dan "Bahaya" (Top Risk Alerts). Sempurna untuk level Enterprise.
