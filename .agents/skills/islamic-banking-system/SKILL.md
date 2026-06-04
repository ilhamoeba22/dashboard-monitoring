# Islamic Banking System - Skill Komprehensif

## Identitas Skill
- **Nama**: Islamic Banking System (IBS)
- **Deskripsi**: Skill komprehensif untuk development sistem perbankan syariah Indonesia dengan pemahaman deep-domain dalam rumus, rasio, akad, CKPN, neraca, dan kepatuhan regulasi OJK. Mampu bekerja dengan berbagai naming convention database yang tidak standar.
- **Origin**: Custom - Developed for Indonesian Islamic Banking
- **Bahasa**: Indonesia (ID) dengan istilah teknis bahasa Inggris

---

## Scope & Kapabilitas

### 1. Produk & Akad Syariah
- [x] Murabahah (Jual Beli Margin)
- [x] Mudharabah (Investasi Bagi Hasil)
- [x] Musyarakah (Modal Bersama)
- [x] Ijara (Sewa)
- [x] Istishna (Pesanan)
- [x] Salam (Jual Beli di Muka)
- [x] Qardh (Pinjaman Hikmat)
- [x] Rahn (Gadai)
- [x] Kafalah (Jaminan)
- [x] Wakalah (Delegasi)
- [x] Istishna' (Pesanan)
- [x] Hiwalah (Transfer Utang)
- [x] Muzara'ah & Musaqah (Pertanian)
- [x] Mardhatil Amanah (Titipan)

### 2. Rasio Keuangan & Kesehatan Bank

#### A. Rasio Permodalan (Capital)
| Rasio | Keterangan | Standar OJK | Formula |
|-------|------------|------------|---------|
| CAR | Capital Adequacy Ratio | ≥ 8% | (Modal Inti + Modal Tambahan) / ATMR × 100% |
| Tier 1 Ratio | Modal Inti / ATMR | ≥ 6% | Modal Inti / ATMR × 100% |
| Tier 2 Ratio | Modal Tambahan / ATMR | ≤ 2% | Modal Tambahan / ATMR × 100% |

#### B. Rasio Kualitas Aktiva Produktif (Asset Quality)
| Rasio | Keterangan | Standar | Formula |
|-------|------------|---------|---------|
| NPF Gross | Non-Performing Financing Gross | < 5% | (Pembiayaan Bermasalah / Total Pembiayaan) × 100% |
| NPF Net | Non-Performing Financing Net | < 5% | (Pembiayaan Bermasalah - CKPN) / Total Pembiayaan × 100% |
| PPAP | Penyisihan Penghapusan Aktiva Produktif | ≥ 1% | CKPN / Aset Produktif × 100% |

#### C. Rasio Profitabilitas (Profitability)
| Rasio | Keterangan | Standar | Formula |
|-------|------------|---------|---------|
| ROA | Return on Assets | > 1.5% | (Laba Bersih / Total Aset Rata-rata) × 100% |
| ROE | Return on Equity | > 5% | (Laba Bersih / Ekuitas Rata-rata) × 100% |
| NIM | Net Interest Margin | 3-5% | (Pendapatan Margin / Aset Produktif Rata-rata) × 100% |
| BOPO | Biaya Operasional / Pendapatan Operasional | < 93% | Beban Operasional / Pendapatan Operasional × 100% |
| Revenue Mix | Proporsi pendapatan | - | Pendapatannon-Margin / Total Pendapatan |

#### D. Rasio Likuiditas (Liquidity)
| Rasio | Keterangan | Standar | Formula |
|-------|------------|---------|---------|
| FDR | Financing to Deposit Ratio | 80-100% | (Pembiayaan Beredar / Dana Pihak Ketiga + Asset) × 100% |
| Cash Ratio | Ratio Kas | - | (Kas + Bank / Kewajiban Lancar) × 100% |
| Liquidity Ratio | Ratio Likuiditas | ≥ 100% | Aktiva Lancar / Kewajiban Lancar × 100% |

#### E. Rasio Efisiensi
| Rasio | Keterangan | Formula |
|-------|------------|---------|
| CIR | Cost to Income Ratio | Beban Operasional / Pendapatan Operasional × 100% |
| Efficiency Ratio | Efisiensi Pengeluaran | Total Beban / Total Pendapatan × 100% |
| Fee Income Ratio | Rasio Pendapatan Fee | Pendapatan Fee / Total Pendapatan × 100% |

### 3. CKPN - Cadangan Kerugian Penurunan Nilai (PSAK 71 / IFRS 9)

#### Staging Classification
```
Stage 1: 12-month ECL (Perform Lancar)
Stage 2: Lifetime ECL - Significant Increase in Credit Risk (Performing but Deteriorated)
Stage 3: Lifetime ECL - Credit Impaired (Non-Performing/Macet)
```

#### Key Components
- **PD (Probability of Default)**: Probabilitas gagal bayar
- **LGD (Loss Given Default)**: Kerugian saat default (%)
- **EAD (Exposure at Default)**: Eksposur saat default
- **ECL (Expected Credit Loss)**: Kerugian ekspektasian

#### Formula CKPN
```
ECL Stage 1 = PD_12month × LGD × EAD × (1 - 0.5 × LGD × PD)
ECL Stage 2/3 = PD_lifetime × LGD × EAD × (1 - 0.5 × LGD × PD)

CKPN = Σ ECL untuk seluruh aset
```

#### Kualitas Aset & Kategori Pembiayaan
| Kategori | Keterangan | CKPN Minimum |
|----------|------------|--------------|
| Lancar | Tidak ada masalah | Stage 1 ECL |
| Dalam Perhatian Khusus | Minor issues | Stage 2 ECL |
| Tidak Lancar | Sedang bermasalah | Stage 3 ECL |
| Macet | Sangat bermasalah | Stage 3 ECL + 100% |

### 4. Struktur Database Pattern

#### A. Naming Convention Patterns yang Diakomodasi
```sql
-- Pattern 1: Indonesian Standard
financing_tabungan, financing_murabahah, ckpn_table

-- Pattern 2: CamelCase
financingMurabahah, customerInfo, transactionHistory

-- Pattern 3: Underscore
financing_murabahah, customer_info, transaction_history

-- Pattern 4: Abbreviation
fin_mur, cust, txn, npf_calc

-- Pattern 5: Mixed/Irregular
pemb_biaya, tbl_mur, financing_murabahah_tab
```

#### B. Table Patterns yang Dipahami

```sql
-- Pembiayaan/Margin Financing
[financing, pemb, pembiayan, portofolio_pemb]
[murabahah, mrb, murb, mur]
[mudharabah, mdh, mudar]
[musyarakah, msr, musy]
[ijara, ijr, ijar]
[istishna, ist, isn]
[qardh, qrd, qard]
[salam, slm, sala]

-- Dana/K模 Dana
[dana, funds, third_party_fund]
[dpk, third_party_fund, dana_piihak_ketiga]
[tabungan, savings, tab]
[deposito, time_deposit, dep]
[giro, current_account, current_fund]

-- Customer/Nasabah
[nasabah, customer, cust, customer_info]
[nasabah_individual, personal_customer]
[nasabah_korporat, corporate_customer]

-- Jurnal/Akuntansi
[jurnal, journal, journal_entry]
[akad, contract, agreement]
[memo_acct, memo_accounting, memorandum]

-- Rasio/Perhitungan
[ratio_calc, rasio, financial_ratio]
[kualitas_aset, asset_quality]
[camels, camel_ratio]
```

### 5. Neraca & Laporan Keuangan Struktur

#### Neraca Bank Umum Syariah (Versi Standar)
```
AKTIVA
├── Aktiva Lancar
│   ├── Kas
│   ├── Bank Indonesia (Giro Wajib Minimum)
│   ├── Penempatan pada Bank Lain
│   └── Tagihan Akseptasi
├── Aktiva Tidak Lancar
│   ├── Pembiayaan (Net - CKPN)
│   ├── Aset Tetap
│   └── Aset Tidak Berwujud
└── Total Aktiva

LIABILITAS
├── Liabilitas Lancar
│   ├── Kewajiban Segera
│   ├── Tabungan
│   ├── Deposito
│   └── Giro
├── Liabilitas Jangka Panjang
│   └── Surat Berharga yang Diterbitkan
└── Total Liabilitas

EKUITAS
├── Modal Disetor
├── Saldo Laba
└── Total Ekuitas
```

#### Laporan Laba Rugi (Versi Perbankan Syariah)
```
PENDAPATAN OPERASIONAL
├── Pendapatan Margin Murabahah
├── Pendapatan Margin Mudharabah
├── Pendapatan Ijara
├── Pendapatan Fee/Commission
└── Total Pendapatan

BEBAN OPERASIONAL
├── Beban Margin Bagi Hasil
├── Beban Bonus Tabungan
├── Beban Penyisihan CKPN
├── Beban Umum & Adm
└── Total Beban

LABA/RUGI OPERASIONAL
LABA/RUGI NON-OPERASIONAL
LABA BERSIH
```

### 6. Regulasi & Compliance

#### Regulasi OJK Utama
| Regulasi | Materi |
|----------|--------|
| POJK 8/2023 | Manajemen Risiko Bank Umum |
| SEOJK 21/2023 | Penilaian Kualitas Aktiva |
| POJK 11/2022 | Batas Maksimum Pembiayaan |
| POJK 24/2023 | GCG Bank Umum |
| SEOJK 16/2023 | Audit Internal |
| POJK 35/2023 | Restrukturisasi Pembiayaan |

#### Batas Regulatory
| Parameter | Batas |
|-----------|-------|
| NPF Gross | < 5% |
| FDR | 80% - 100% |
| CAR | ≥ 8% |
| Maks Pembiayaan | 10% dari total financing (single debtor) |
| PPAP | ≥ 1% dari Aset Produktif |
| Liquid Buffer | ≥ 20% dari total DPK |

---

## Activation Triggers

Skill ini DIACTIVASI secara otomatis ketika:
- User meminta membuat sistem banking/keuangan
- Topic涉及到: perbankan, financing, pembiayaan, Islamic banking, BPRS, BUS
- Discussion tentang: NPF, CAR, FDR, ROA, rasio bank, CKPN
- Query terkait: akad syariah, jurnal akuntansi, neraca bank
- Task melibatkan: database keuangan, laporan bank, audit bank
- Pattern yang mendeteksi: column/kolom dengan nama tidak beraturan

---

## Usage Guidelines

### Untuk Query Rasio
```
Jika user bertanya rasio NPF, langsung berikan:
- Formula lengkap
- Standar OJK terkini
- Contoh query SQL dengan berbagai pattern nama kolom
- Logika handling untuk kolom non-standar
```

### Untuk Query CKPN
```
Jika user bertanya CKPN/PSAK 71:
- Penjelasan staging
- Formula ECL
- Query calculation dengan multiple column patterns
- Handling untuk data dengan quality berbeda
```

### Untuk Database Schema
```
Jika user memberikan schema atau meminta membuat:
- Langsung paham berbagai naming convention
- Mampu mapping kolom tidak standar ke domain logic
- Generate query dengan kemampuan pattern matching
```

---

## Output Format Standards

### Untuk Perhitungan Rasio
```json
{
  "ratio": "NPF_Gross",
  "formula": "(PEMB_BERMASALAH / TOTAL_PEMB) × 100",
  "ojk_standard": "< 5%",
  "sql_patterns": [
    "SELECT ... FROM financing WHERE npf_flag = 1",
    "SELECT ... FROM pemb WHERE kategori = 'bermasalah'"
  ],
  "handling_tips": ["handle NULL values", "exclude written-off"]
}
```

### Untuk Database Query
```sql
-- Pattern Recognition untuk berbagai nama kolom:
-- NPF Calculation dengan flexible column mapping

SELECT 
  COALESCE(
    SUM(CASE WHEN col_nama IN ('pemb_bermasalah','fin_bermasalah','npf_amount')
             THEN col_nilai ELSE 0 END),
    SUM(CASE WHEN col_nama LIKE '%bermasalah%' 
             OR col_nama LIKE '%npf%' THEN col_nilai ELSE 0 END)
  ) AS total_npf,
  
  COALESCE(
    SUM(CASE WHEN col_nama IN ('total_pemb','fin_total','pemb_jumlah')
             THEN col_nilai ELSE 0 END),
    SUM(CASE WHEN col_nama LIKE '%total%' 
             AND col_nama LIKE '%pemb%' THEN col_nilai ELSE 0 END)
  ) AS total_pembiayaan
```

---

## Error Handling & Edge Cases

1. **Kolom Tidak Ditemukan**: Use fuzzy matching dan multiple pattern
2. **Value NULL**: Apply COALESCE dengan default yang aman
3. **Division by Zero**: Handle dengan NULLIF atau CASE WHEN
4. **Currency/Unit Mismatch**: Apply conversion factor
5. **Date Range Issues**: Handle fiscal year vs calendar year
6. **Multiple Currency**: Apply spot rate conversion

---

## Skill Dependencies & Relationships

- database-migrations: Untuk schema design
- security-review: Untuk authentication & authorization
- api-design: Untuk API endpoint design
- laravel-patterns / python-patterns: Untuk implementasi

---

## Version & Update Log

- **v1.0** - Initial release dengan comprehensive Islamic banking knowledge
- **Coverage**: All major ratios, products, CKPN, regulatory compliance