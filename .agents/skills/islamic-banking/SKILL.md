---
name: islamic-banking
description: >
  Skill khusus untuk sistem perbankan syariah Indonesia yang mencakup seluruh aspek teknis,
  regulasi, akuntansi, dan pemrograman. SELALU gunakan skill ini ketika pengguna membahas
  topik perbankan syariah, termasuk: perhitungan rasio keuangan bank syariah (CAR, NPF, ROA,
  ROE, NOM, BOPO, FDR), CKPN (Cadangan Kerugian Penurunan Nilai) sesuai PSAK 71/IFRS 9,
  Tingkat Kesehatan Bank (TKS/RBBR), Profil Risiko dan Komposit, akad-akad syariah
  (Murabahah, Mudharabah, Musyarakah, Ijarah, Salam, Istishna, Qardh, Wakalah),
  neraca/laporan keuangan perbankan syariah, regulasi OJK (POJK, SE OJK, PBI, SEBI),
  PSAK Syariah (102, 104–111), query/mapping database sistem perbankan dengan nama kolom
  tidak beraturan, pengembangan sistem CBS (Core Banking System), serta analisis data
  perbankan syariah secara mendalam. Gunakan skill ini bahkan untuk pertanyaan umum tentang
  perbankan syariah, karena domain ini sangat spesifik dan berbeda dari perbankan konvensional.
---

# Islamic Banking System — Master Skill

## Overview Domain

Perbankan Syariah Indonesia beroperasi di bawah tiga lapisan utama:
1. **Regulasi**: UU No. 21/2008, POJK, SE OJK, SEBI, Fatwa DSN-MUI
2. **Akuntansi**: PSAK Syariah (DSAS-IAI), PSAK 71, SAK ETAP
3. **Pengawasan**: TKS (RBBR), KPMM, NOM, BOPO, FDR, NPF

---

## Cara Menggunakan Skill Ini

Baca SKILL.md ini terlebih dahulu, lalu load file referensi yang relevan berdasarkan konteks pertanyaan:

| Topik | File Referensi |
|-------|---------------|
| Rasio keuangan (CAR, NPF, ROA, dll) | `references/rasio-keuangan.md` |
| CKPN, ECL, PSAK 71, staging | `references/ckpn-psak71.md` |
| TKS, RBBR, Profil Risiko, Komposit | `references/tks-rbbr.md` |
| Akad syariah, jurnal, pengakuan | `references/akad-syariah.md` |
| Neraca, LR, Laporan Keuangan | `references/neraca-lapkeu.md` |
| Regulasi OJK, POJK, SE OJK | `references/regulasi-ojk.md` |
| Database mapping, query SQL, kolom | `references/database-mapping.md` |

**Untuk pertanyaan yang menyentuh banyak topik, load semua file relevan sebelum menjawab.**

---

## Prinsip Dasar yang HARUS Selalu Diterapkan

### 1. Prinsip Syariah vs Konvensional
- Bank Syariah **tidak mengenal bunga** (riba) — gunakan terminologi bagi hasil, margin, ujrah, bonus
- Produk berbasis **akad** yang diatur Fatwa DSN-MUI, bukan kontrak bunga
- **Dana Syirkah Temporer (DST)** bukan liabilitas biasa — perlakuan berbeda di neraca
- **Zakat** wajib dilaporkan sebagai komponen tersendiri

### 2. Terminologi Khusus
| Konvensional | Syariah |
|---|---|
| Kredit | Pembiayaan |
| Bunga/Pinjaman | Margin/Bagi Hasil/Ujrah |
| NIM (Net Interest Margin) | NOM (Net Operating Margin) |
| LDR (Loan to Deposit Ratio) | FDR (Financing to Deposit Ratio) |
| NPL (Non Performing Loan) | NPF (Non Performing Financing) |
| Piutang Kredit | Piutang Murabahah / Piutang lainnya |
| Dana Pihak Ketiga (DPK) | DPK + Dana Syirkah Temporer |
| PPAP | CKPN (sejak PSAK 71 berlaku 1 Jan 2020) |

### 3. Standar Akuntansi Berlaku
- **PSAK 71** (Instrumen Keuangan / IFRS 9) — berlaku penuh per 1 Januari 2020
- **PSAK 102** — Akuntansi Murabahah
- **PSAK 104** — Akuntansi Istishna'
- **PSAK 105** — Akuntansi Mudharabah
- **PSAK 106** — Akuntansi Musyarakah
- **PSAK 107** — Akuntansi Ijarah
- **PSAK 108** — Akuntansi Penyelesaian Utang Piutang melalui Penggantian Aset
- **PSAK 109** — Akuntansi Zakat, Infak/Sedekah
- **PSAK 110** — Akuntansi Sukuk
- **PSAK 111** — Akuntansi Wa'd
- **ISAK 101** — Pengakuan Pendapatan Murabahah Tangguh

### 4. Regulasi OJK Utama
- **POJK No. 8/POJK.03/2014** — Penilaian Tingkat Kesehatan Bank Umum Syariah
- **POJK No. 65/POJK.03/2016** — Penerapan Manajemen Risiko BUS dan UUS
- **POJK No. 11/POJK.03/2020** — Stimulus COVID (relevan historis)
- **SE OJK No. 10/SEOJK.03/2014** — Penilaian TKS BUS dan UUS
- **SE OJK No. 13/SEOJK.03/2015** — Penerapan Manajemen Risiko BUS
- **PBI No. 13/23/PBI/2011** — Manajemen Risiko bagi BUS dan UUS

---

## Quick Reference: Formula Utama

### Rasio Kewajiban Pemenuhan Modal Minimum (KPMM/CAR)
```
CAR = (Modal Inti + Modal Pelengkap) / ATMR × 100%
ATMR = ATMR Risiko Kredit + ATMR Risiko Pasar + ATMR Risiko Operasional
Minimum CAR BUS: 8% (BUKU 1), 9% (BUKU 2), 10% (BUKU 3), 11% (BUKU 4)
```

### NPF (Non Performing Financing)
```
NPF Gross = (Pembiayaan Kol 3 + Kol 4 + Kol 5) / Total Pembiayaan × 100%
NPF Net   = (NPF Gross - CKPN untuk NPF) / Total Pembiayaan × 100%
Threshold OJK: NPF Net ≤ 5%
```

### FDR (Financing to Deposit Ratio)
```
FDR = Total Pembiayaan / (DPK + Modal) × 100%
Optimal: 80% - 92%
```

### NOM (Net Operating Margin)
```
NOM = (Pendapatan Operasional Bersih - BOPO) / Rata-rata Aset Produktif × 100%
Pendapatan Operasional Bersih = Pendapatan Penyaluran Dana - Hak Pihak Ketiga atas Bagi Hasil DST
```

---

## Instruksi Umum untuk Claude

1. **Selalu verifikasi akad** sebelum menghitung — Murabahah menggunakan margin, Mudharabah menggunakan nisbah bagi hasil, Ijarah menggunakan ujrah
2. **Cek tanggal berlaku regulasi** — PSAK 71 berlaku 1 Jan 2020 (mengganti PSAK 55), ada perbedaan material dalam CKPN
3. **Perhatikan konsolidasi vs solo** — rasio bisa berbeda antara laporan bank saja vs konsolidasi dengan anak perusahaan
4. **Database dengan kolom tidak beraturan**: load `references/database-mapping.md` untuk panduan mapping kolom
5. **Jangan asumsikan nama tabel/kolom** — selalu tanyakan skema aktual atau minta user menunjukkan DDL/sample data
6. **Untuk CKPN**: load `references/ckpn-psak71.md` — ini sangat kompleks dan kritis
