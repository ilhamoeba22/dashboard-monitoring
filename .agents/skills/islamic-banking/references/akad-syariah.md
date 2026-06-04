# Akad Syariah — Produk, Jurnal, dan Pengakuan

---

## 1. MURABAHAH (PSAK 102)

### Definisi & Karakteristik
```
Akad jual beli di mana harga pokok diketahui nasabah + margin disepakati.
Jenis:
  - Murabahah tunai (jarang)
  - Murabahah tangguh / cicilan (dominan)
  - Murabahah dengan pesanan (murabahah li al-wakilah)
Agunan: Umumnya objek yang dibeli atau agunan tambahan
```

### Pengakuan & Pengukuran (PSAK 102 + ISAK 101)
```
Di Bank (sebagai Penjual/Shahib al-Mal):
  - Saat membeli aset: Dr. Persediaan Murabahah / Cr. Kas
  - Saat akad: Dr. Piutang Murabahah / Cr. Persediaan + Keuntungan Tangguhan

Pengakuan Keuntungan (ISAK 101 — EFEKTIF):
  - Metode Anuitas (Effective Interest Method): seperti amortized cost
    → Pendapatan margin = EIR × Saldo pokok berjalan
    → Wajib jika pembiayaan jangka panjang dan bersifat "dengan komponen pembiayaan"
  - Metode Proporsional: margin dibagi rata selama tenor
    → Umumnya untuk jangka pendek

Metode Anuitas (Sesuai Regulasi OJK untuk bank besar):
  Contoh: Harga Pokok = 100jt, Margin Total = 20jt, Tenor = 12 bulan
  EIR dihitung dari arus kas seluruh cicilan
  Pengakuan tiap bulan = EIR × Saldo pokok awal bulan (bukan margin/12)
```

### Jurnal Murabahah
```
1. Bank membeli aset dari supplier:
   Dr. Persediaan/Aset Murabahah         Rp xxx
     Cr. Kas / Hutang                              Rp xxx

2. Penyerahan ke Nasabah (Akad):
   Dr. Piutang Murabahah                 Rp xxx (harga jual = pokok + margin total)
     Cr. Persediaan Murabahah                      Rp xxx (harga beli)
     Cr. Margin Murabahah Tangguhan                Rp xxx (total margin)

3. Pengakuan pendapatan margin (setiap bulan — metode anuitas):
   Dr. Margin Murabahah Tangguhan        Rp xxx
     Cr. Pendapatan Margin Murabahah               Rp xxx

4. Penerimaan angsuran:
   Dr. Kas / Rekening Nasabah            Rp xxx
     Cr. Piutang Murabahah                         Rp xxx

5. Denda (jika ada — tidak diakui sebagai pendapatan, masuk Dana Kebajikan):
   Dr. Kas                               Rp xxx
     Cr. Dana Kebajikan                            Rp xxx

6. CKPN:
   Dr. Beban CKPN                        Rp xxx
     Cr. CKPN - Piutang Murabahah                  Rp xxx

7. Penghapusan (Write-off):
   Dr. CKPN - Piutang Murabahah         Rp xxx
     Cr. Piutang Murabahah                         Rp xxx
```

---

## 2. MUDHARABAH (PSAK 105)

### Definisi & Karakteristik
```
Akad kerja sama modal + tenaga:
  - Bank sebagai Shahibul Maal (penyedia modal)
  - Nasabah/Mitra sebagai Mudharib (pengelola)
Bagi hasil berdasarkan NISBAH dari keuntungan usaha aktual.
Bank menanggung KERUGIAN jika bukan karena kelalaian mudharib.

Jenis Mudharabah:
  - Mudharabah Muthlaqah: pengelola bebas mengelola usaha
  - Mudharabah Muqayyadah: ada batasan/syarat dari bank
  - Mudharabah Musytarakah: pengelola juga ikut menyertakan modal

Produk Bank:
  - Pembiayaan Proyek Mudharabah
  - Pembiayaan Musim Panen (pertanian)
  - Rekening Tabungan Mudharabah (bank sebagai Mudharib)
  - Deposito Mudharabah (bank sebagai Mudharib)
```

### Pengakuan Pembiayaan Mudharabah (Bank sebagai Shahibul Maal)
```
Penyaluran:
  Dr. Pembiayaan Mudharabah             Rp xxx
    Cr. Kas                                       Rp xxx

Pengakuan Bagi Hasil (saat diterima/diakui):
  Dr. Kas / Piutang Bagi Hasil          Rp xxx
    Cr. Pendapatan Bagi Hasil Mudharabah          Rp xxx
  
  CATATAN: PSAK 105 par 22 — pendapatan diakui saat diterima ATAU
  saat hak atas bagi hasil timbul (jika dapat diestimasi andal)

Kerugian (bukan karena kelalaian Mudharib):
  Dr. Kerugian Pembiayaan Mudharabah    Rp xxx
    Cr. Pembiayaan Mudharabah                     Rp xxx

Kerugian karena kelalaian Mudharib:
  Dr. Piutang kepada Mudharib           Rp xxx
    Cr. Pembiayaan Mudharabah                     Rp xxx
    (ditanggung Mudharib, bukan bank)
```

### Tabungan/Deposito Mudharabah (Bank sebagai Mudharib)
```
Penerimaan Dana:
  Dr. Kas                               Rp xxx
    Cr. Tabungan/Deposito Mudharabah              Rp xxx
  
  PENTING: DI NERACA DISAJIKAN SEBAGAI "DANA SYIRKAH TEMPORER" (DST)
  BUKAN SEBAGAI LIABILITAS BIASA

Distribusi Bagi Hasil:
  Dr. Hak Pihak Ketiga atas Bagi Hasil DST  Rp xxx
    Cr. Hutang Bagi Hasil                             Rp xxx

Pembayaran Bagi Hasil:
  Dr. Hutang Bagi Hasil                 Rp xxx
    Cr. Kas / Rekening Nasabah                    Rp xxx
```

---

## 3. MUSYARAKAH (PSAK 106)

### Definisi & Karakteristik
```
Akad kerja sama modal antara dua pihak atau lebih.
Bagi hasil berdasarkan NISBAH dari keuntungan proporsional.
Kerugian ditanggung PROPORSIONAL sesuai kontribusi modal.

Jenis Musyarakah:
  - Musyarakah Permanen: porsi kepemilikan tetap selama akad
  - Musyarakah Menurun (Musyarakah Mutanaqishah / MMQ):
    Bank secara bertahap menjual porsinya ke nasabah
    → Digunakan untuk KPR Syariah, KPRS
    → Kombinasi Musyarakah + Ijarah
```

### Jurnal Musyarakah Menurun (MMQ) — KPR Syariah
```
1. Bank menyertakan modal:
   Dr. Investasi Musyarakah              Rp xxx (porsi bank, misal 80%)
     Cr. Kas                                        Rp xxx

2. Nasabah membayar sewa (ujrah) atas porsi bank:
   Dr. Kas                               Rp xxx
     Cr. Pendapatan Ijarah (Sewa)                   Rp xxx

3. Nasabah membeli porsi bank (cicilan pembelian):
   Dr. Kas                               Rp xxx
     Cr. Investasi Musyarakah                       Rp xxx

4. Akhir akad: Kepemilikan penuh beralih ke nasabah
```

---

## 4. IJARAH & IJARAH MUNTAHIYAH BIT TAMLIK / IMBT (PSAK 107)

### Ijarah Murni (Sewa)
```
Bank memiliki/membeli aset, lalu menyewakan ke nasabah.
Pendapatan: ujrah (sewa) per periode.
Aset disusutkan oleh bank.

Jurnal:
  Pembelian Aset: Dr. Aset Ijarah / Cr. Kas
  Sewa: Dr. Piutang Ijarah / Cr. Pendapatan Ijarah
  Penyusutan: Dr. Beban Penyusutan Aset Ijarah / Cr. Akum. Penyusutan Aset Ijarah
```

### IMBT (Ijarah Muntahiyah Bit Tamlik) — Sewa-Beli
```
Sewa yang diakhiri dengan pemindahan kepemilikan (hibah atau jual beli).
Mirip finance lease konvensional, tapi ada janji (wa'd) dari bank, bukan kewajiban.

Metode IMBT:
  1. IMBT Hibah: di akhir akad, aset dihibahkan tanpa bayar tambahan
  2. IMBT Jual Beli: di akhir akad, nasabah membeli aset dengan harga yang
     disepakati di awal (umumnya sisa nilai buku atau harga nominal)

Pengakuan Pendapatan:
  → Pendapatan sewa diakui per periode (sama dengan Ijarah)
  → Nilai aset tetap dicatat bank selama akad berjalan
  → Saat penyerahan kepemilikan: Dr. Kas / Cr. Aset Ijarah
```

---

## 5. ISTISHNA' (PSAK 104)

### Karakteristik
```
Akad pesanan pembuatan barang (konstruksi, manufaktur).
Produsen/Kontraktor wajib membuat/mengadakan barang sesuai spesifikasi.
Pembayaran dapat dilakukan: di muka, cicilan, atau di belakang.

Istishna' Paralel: Bank menerima pesanan nasabah, lalu memesan ke kontraktor.
  → Sering digunakan untuk KPR inden, proyek konstruksi

Pengakuan Pendapatan:
  Metode Persentase Penyelesaian (Percentage of Completion):
    Pendapatan = % Penyelesaian × Total Nilai Kontrak Istishna'
  atau Metode Akad Selesai (jika tidak dapat diestimasi andal)
```

---

## 6. SALAM (PSAK 103)

### Karakteristik
```
Akad jual beli barang yang diserahkan di masa depan (forward sale).
Pembayaran dilakukan PENUH di muka oleh pembeli (bank).
Objek: komoditas yang dapat dispesifikasi (pertanian, emas, dll.)

Salam Paralel: Bank membeli komoditas dengan salam, lalu menjual kembali
  dengan salam kepada pihak lain dengan harga lebih tinggi.

Jurnal Salam (Bank sebagai Pembeli):
  Dr. Piutang Salam     Rp xxx (nilai aset yang akan diterima)
    Cr. Kas                       Rp xxx (dibayar di muka)
  
  Saat terima barang:
  Dr. Aset/Persediaan   Rp xxx
    Cr. Piutang Salam             Rp xxx
```

---

## 7. QARDH (Pinjaman Kebajikan)

### Karakteristik
```
Pinjaman murni tanpa imbal hasil (benevolent loan).
Bank syariah wajib memiliki produk Qardh untuk kebutuhan sosial.

Sumber dana: Dana Kebajikan (bukan DPK komersial)

Produk Qardh:
  - Talangan Haji
  - Dana kebajikan anggota koperasi
  - Pinjaman darurat (emergency loan)
  - Talangan hutang (bridging)

Jurnal:
  Penyaluran: Dr. Qardh / Cr. Kas (Dana Kebajikan)
  Pelunasan:  Dr. Kas / Cr. Qardh
```

---

## 8. WAKALAH (Perwakilan)

### Karakteristik
```
Akad perwakilan — satu pihak mewakilkan kepada pihak lain untuk melakukan
suatu tindakan (atas nama dan untuk kepentingannya).

Produk Bank berbasis Wakalah:
  - Transfer & pembayaran
  - L/C Syariah (Wakalah bil Ujrah)
  - Murabahah li al-Wakilah: nasabah menjadi wakil bank untuk membeli aset
  - Reksadana (bank sebagai Manajer Investasi — Wakil nasabah)

Ujrah Wakalah: fee yang diterima bank (diakui sebagai pendapatan fee)
```

---

## 9. KAFALAH (Penjaminan)

### Karakteristik
```
Akad pemberian jaminan (garansi) dari bank kepada pihak ketiga atas kewajiban
nasabah.

Produk: Bank Garansi Syariah, SBLC Syariah

Pengakuan:
  - Ujrah kafalah: pendapatan fee per periode jaminan
  - Contingent liability: kewajiban bersyarat, dicatat off-balance sheet
  - Saat klaim: Dr. Hutang Kafalah / Cr. Kas
```

---

## 10. SHARF (Jual Beli Valuta Asing)

### Karakteristik
```
Akad pertukaran mata uang — harus spot (tunai, tidak boleh forward tanpa lindung nilai)
Kurs: nilai tukar yang berlaku (tidak ada markup yang tidak adil)
Fatwa DSN: MUI No. 28/DSN-MUI/III/2002

Produk: Money Changer Syariah, Transaksi Valas Spot Syariah

LARANGAN: Forward, Option, dan Swap spekulatif dilarang.
Hedging syariah diperbolehkan dengan akad Forward khusus (POJK hedging syariah).
```

---

## 11. DANA SYIRKAH TEMPORER (DST) — Penyajian Neraca

### Definisi & Karakteristik
```
DST adalah dana yang diterima bank sebagai investasi dengan jangka waktu tertentu,
di mana bank berhak mengelola dan menginvestasikan dana tersebut, dan imbalan diberikan
dalam bentuk bagi hasil (nisbah).

Jenis DST:
  - Tabungan Mudharabah
  - Deposito Mudharabah
  - Dana Investasi Terikat (Mudharabah Muqayyadah off-balance sheet)

PENTING — Perbedaan dengan Liabilitas:
  DST BUKAN liabilitas — bank tidak wajib mengembalikan jika terjadi kerugian
  yang bukan karena kelalaian bank (force majeure, risiko bisnis normal).
  Namun dalam praktiknya, bank syariah Indonesia SELALU mengembalikan pokok
  DST demi menjaga kepercayaan nasabah (Displaced Commercial Risk).

Penyajian di Neraca (PSAK 101):
  ASET
  LIABILITAS
  DANA SYIRKAH TEMPORER  ← posisi di antara Liabilitas dan Ekuitas
    - Bukan Liabilitas
    - Bukan Ekuitas
    → Komponen tersendiri dalam laporan keuangan
  EKUITAS
```

---

## 12. ZAKAT & DANA KEBAJIKAN (PSAK 109)

### Zakat Bank Syariah
```
Kewajiban zakat: berdasarkan laba bersih atau nilai aset bersih bank
Nishab: setara 85 gram emas
Kadar: 2.5%

Sumber dana zakat:
  1. Zakat dari pemegang saham (diwakilkan ke bank) — 2.5% dari laba
  2. Zakat dari pendapatan bank sendiri (jika bank membayar zakat atas namanya)

Jurnal Zakat:
  Dr. Beban Zakat                       Rp xxx
    Cr. Hutang Zakat                              Rp xxx
  
  Penyaluran:
  Dr. Hutang Zakat                      Rp xxx
    Cr. Kas                                       Rp xxx

Laporan Sumber & Penggunaan Dana Zakat (wajib dalam LK BUS):
  Saldo Awal + Penerimaan - Penyaluran = Saldo Akhir
```

### Dana Kebajikan
```
Sumber:
  - Denda nasabah (ta'widh yang melebihi kerugian riil bank)
  - Pendapatan non-halal (bunga dari bank koresponden konvensional)
  - Infak & Sedekah nasabah

Penggunaan:
  - Kegiatan sosial keagamaan
  - Qardh (pinjaman kebajikan)
  - Tidak boleh diakui sebagai pendapatan operasional bank

Jurnal & Laporan:
  Laporan Sumber & Penggunaan Dana Kebajikan (tersendiri dalam LK BUS)
```
