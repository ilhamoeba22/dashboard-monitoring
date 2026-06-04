# Database Mapping — Sistem CBS (Core Banking System) Perbankan Syariah

---

## PRINSIP UTAMA

**JANGAN pernah mengasumsikan nama kolom atau tabel.**
Sistem CBS yang berbeda (Temenos, Silverlake, Misys/Finastra, Oracle FLEXCUBE,
iGTB, Cobis, CBS dalam negeri, custom-built) menggunakan penamaan yang sangat berbeda.

**Langkah wajib sebelum membuat query:**
1. Minta DDL (CREATE TABLE) atau describe tabel relevan
2. Minta sample data (5-10 baris per tabel)
3. Tanyakan ERD atau relasi antar tabel
4. Konfirmasi field kode/status (nilai aktual vs deskripsi)

---

## 1. POLA UMUM PENAMAAN (Bukan Standar, Hanya Referensi)

### Tabel Pembiayaan
```sql
-- Nama tabel umum yang sering ditemukan (BUKAN pasti):
-- FINANCING, LOAN, MST_FINANCING, TRX_LOAN, ACCOUNT, ACCT, 
-- FASILITAS, PEMBIAYAAN, FM_LOAN

-- Kolom yang umumnya ada (nama BERBEDA tiap sistem):
-- Nomor Akad/Nomor Rekening:
   ACCOUNT_NO, ACCT_NO, LOAN_NO, FINANCING_NO, NO_REKENING, NO_AKAD,
   CONTRACT_NO, FACILITY_NO, APP_NO

-- Nama Debitur/Nasabah:
   CUSTOMER_NAME, CIF_NAME, NASABAH_NAMA, DEBTOR_NAME, CUST_NM

-- CIF (Customer Information File):
   CIF_NO, CUSTOMER_ID, CUST_ID, ID_NASABAH, PARTY_ID

-- Pokok/Plafon:
   PRINCIPAL, PLAFOND, AMOUNT, LOAN_AMT, CEILING_AMT, POKOK,
   APPROVED_AMOUNT, OUTSTANDING

-- Saldo Outstanding:
   OS_AMOUNT, OUTSTANDING, BALANCE, SALDO, CURRENT_BAL, BOOK_VALUE,
   PRINCIPAL_BAL, OUTSTANDING_AMT

-- Margin/Rate:
   RATE, MARGIN_RATE, PROFIT_RATE, NISBAH, MARK_UP_RATE, 
   YIELD_RATE, EFFECTIVE_RATE

-- Tanggal:
   BOOKING_DATE, START_DATE, MATURITY_DATE, FIRST_DUE_DATE,
   TGL_AKAD, TGL_MULAI, TGL_JATUH_TEMPO, VALUE_DATE

-- Jenis Akad/Produk:
   PRODUCT_CODE, AKAD_CODE, PRODUCT_TYPE, LOAN_TYPE, SCHEMA_CODE,
   PRODUK, JENIS_PEMBIAYAAN, CONTRACT_TYPE

-- Kolektibilitas:
   COLL_STATUS, KOLEKTIBILITAS, KUALITAS, COLLECTABILITY, 
   CREDIT_QUALITY, RISK_GRADE, NPF_FLAG

-- Hari Tunggakan:
   DPD, DAYS_PAST_DUE, OVERDUE_DAYS, TUNGGAKAN_HARI, AGING_DAYS

-- Stage PSAK 71:
   ECL_STAGE, PSAK71_STAGE, IMPAIRMENT_STAGE, STAGE_CODE

-- CKPN:
   CKPN_AMOUNT, ALLOWANCE, IMPAIRMENT, PROVISION_AMT, CKPN_AMT,
   PENCADANGAN

-- Sektor/Segmen:
   SECTOR_CODE, SEGMENT, ECONOMIC_SECTOR, SEKTOR_EKONOMI, 
   CATEGORY, BPK_CODE (kode sektor OJK)
```

### Tabel DPK / Dana Pihak Ketiga
```sql
-- Nama tabel: DEPOSIT, SAVING, ACCOUNT, DPK, GIRO, TABUNGAN, DEPOSITO,
--             LIABILITIES, FUNDING

-- Jenis Produk:
   PRODUCT_CODE, FUNDING_TYPE, ACCOUNT_TYPE, JENIS_PRODUK,
   -- Kode: GWD=Giro Wadiah, TWD=Tabungan Wadiah, TMD=Tabungan Mudharabah,
   --       DMD=Deposito Mudharabah, dst (BERBEDA per bank)

-- Nisbah Bagi Hasil:
   NISBAH, RATIO, BH_RATIO, PROFIT_SHARING_RATIO, NISBAH_NASABAH,
   NISBAH_BANK

-- Tanggal Jatuh Tempo Deposito:
   MATURITY_DATE, DUE_DATE, TGL_JATUH_TEMPO

-- Tenor:
   TENOR, TERM, PERIOD, BULAN
```

### Tabel Angsuran / Jadwal
```sql
-- Nama tabel: SCHEDULE, AMORTIZATION, INSTALLMENT, CICILAN,
--             ANGSURAN, REPAYMENT_SCHEDULE, DUE_SCHEDULE

-- Kolom penting:
   DUE_DATE, PAYMENT_DATE, SCHEDULED_DATE   -- Tanggal jatuh tempo
   PRINCIPAL_DUE, ANGSURAN_POKOK            -- Pokok jatuh tempo
   PROFIT_DUE, MARGIN_DUE, ANGSURAN_MARGIN  -- Margin/bagi hasil jatuh tempo
   PAID_DATE, ACTUAL_PAYMENT_DATE           -- Tanggal dibayar
   PRINCIPAL_PAID, PROFIT_PAID              -- Realisasi pembayaran
   STATUS, PAYMENT_STATUS, FLAG             -- Status bayar
```

### Tabel Nasabah / CIF
```sql
-- Nama tabel: CUSTOMER, CIF, PARTY, NASABAH, MST_CUSTOMER

-- Kolom:
   CIF_NO, CUSTOMER_ID                      -- ID nasabah
   FULL_NAME, CUSTOMER_NAME, NAMA_LENGKAP   -- Nama
   ID_TYPE, ID_NO, KTP_NO                   -- Identitas
   NPWP                                     -- NPWP
   SEGMENT, CATEGORY, TIPE_NASABAH          -- Segmentasi
   BRANCH_CODE, CABANG_CODE                 -- Cabang
   RELATIONSHIP_MANAGER, RM_ID, AO_ID      -- Account Officer
```

### Tabel Agunan / Collateral
```sql
-- Nama tabel: COLLATERAL, AGUNAN, SECURITY, JAMINAN

-- Kolom:
   COLLATERAL_TYPE, JENIS_AGUNAN           -- Jenis agunan
   COLLATERAL_VALUE, NILAI_AGUNAN          -- Nilai agunan
   APPRAISAL_DATE, TGL_APPRAISAL          -- Tanggal appraisal
   HAIRCUT, HAIRCUT_PCT                   -- Persentase haircut
   NET_VALUE, NILAI_BERSIH                -- Nilai setelah haircut
   CERTIFICATE_NO, NO_SERTIFIKAT         -- Nomor sertifikat
```

---

## 2. QUERY TEMPLATE UMUM (PERLU DISESUAIKAN DENGAN SKEMA AKTUAL)

### Template: NPF Gross & Net
```sql
-- ⚠️ TEMPLATE SAJA — sesuaikan nama tabel & kolom dengan skema aktual

-- Asumsi: tabel FINANCING, kolom: OUTSTANDING, KUALITAS, CKPN_AMT

SELECT 
    SUM(OUTSTANDING) AS total_pembiayaan,
    SUM(CASE WHEN KUALITAS IN (3,4,5) THEN OUTSTANDING ELSE 0 END) AS npf_gross_amt,
    SUM(CASE WHEN KUALITAS IN (3,4,5) THEN CKPN_AMT ELSE 0 END) AS ckpn_npf,
    
    -- NPF Gross %
    ROUND(
        SUM(CASE WHEN KUALITAS IN (3,4,5) THEN OUTSTANDING ELSE 0 END) 
        / NULLIF(SUM(OUTSTANDING), 0) * 100, 2
    ) AS npf_gross_pct,
    
    -- NPF Net %
    ROUND(
        (SUM(CASE WHEN KUALITAS IN (3,4,5) THEN OUTSTANDING ELSE 0 END) 
         - SUM(CASE WHEN KUALITAS IN (3,4,5) THEN CKPN_AMT ELSE 0 END))
        / NULLIF(SUM(OUTSTANDING), 0) * 100, 2
    ) AS npf_net_pct

FROM FINANCING
WHERE STATUS = 'A'  -- Active, sesuaikan dengan kode status di sistem
  AND PERIOD_DATE = '2024-12-31';  -- tanggal cut-off
```

### Template: CKPN Summary per Stage
```sql
-- Template CKPN per Stage PSAK 71

SELECT 
    ECL_STAGE,
    COUNT(*) AS jumlah_rekening,
    SUM(OUTSTANDING) AS gca_amount,        -- Gross Carrying Amount
    SUM(CKPN_AMT) AS ckpn_amount,
    ROUND(SUM(CKPN_AMT) / NULLIF(SUM(OUTSTANDING),0) * 100, 2) AS ckpn_pct
FROM FINANCING
WHERE STATUS = 'A'
GROUP BY ECL_STAGE
ORDER BY ECL_STAGE;

-- Expected output:
-- STAGE | JUMLAH | GCA         | CKPN        | CKPN%
-- 1     | 5000   | 1.5T        | 15M         | 1.0%
-- 2     | 300    | 200M        | 10M         | 5.0%
-- 3     | 50     | 50M         | 30M         | 60.0%
```

### Template: Distribusi Kolektibilitas
```sql
SELECT 
    KUALITAS AS kolektibilitas,
    CASE KUALITAS 
        WHEN 1 THEN 'Lancar'
        WHEN 2 THEN 'Dalam Perhatian Khusus'
        WHEN 3 THEN 'Kurang Lancar'
        WHEN 4 THEN 'Diragukan'
        WHEN 5 THEN 'Macet'
        ELSE 'Unknown'
    END AS keterangan,
    COUNT(*) AS jumlah_rekening,
    SUM(OUTSTANDING) AS outstanding,
    ROUND(SUM(OUTSTANDING) / SUM(SUM(OUTSTANDING)) OVER() * 100, 2) AS pct_dari_total
FROM FINANCING
WHERE STATUS = 'A'
  AND PERIOD_DATE = :tanggal_laporan
GROUP BY KUALITAS
ORDER BY KUALITAS;
```

### Template: FDR Calculation
```sql
-- Asumsi: tabel FINANCING (pembiayaan) dan FUNDING (DPK/DST)

WITH pembiayaan AS (
    SELECT SUM(OUTSTANDING) AS total_pembiayaan
    FROM FINANCING
    WHERE STATUS = 'A' AND PERIOD_DATE = :tgl
),
dpk AS (
    SELECT SUM(BALANCE) AS total_dpk
    FROM FUNDING
    WHERE STATUS = 'A' 
      AND PRODUCT_TYPE IN ('GIRO_WADIAH','TAB_WADIAH','TAB_MUDHARABAH','DEP_MUDHARABAH')
      AND PERIOD_DATE = :tgl
)
SELECT 
    p.total_pembiayaan,
    d.total_dpk,
    ROUND(p.total_pembiayaan / NULLIF(d.total_dpk, 0) * 100, 2) AS fdr_pct
FROM pembiayaan p, dpk d;
```

### Template: Aging Analysis Tunggakan
```sql
-- Aging berdasarkan DPD (Days Past Due)

SELECT 
    CASE 
        WHEN DPD = 0 THEN '1. Lancar (0 hari)'
        WHEN DPD BETWEEN 1 AND 30 THEN '2. DPK Ringan (1-30 hari)'
        WHEN DPD BETWEEN 31 AND 60 THEN '3. DPK Sedang (31-60 hari)'
        WHEN DPD BETWEEN 61 AND 90 THEN '4. DPK Berat (61-90 hari)'
        WHEN DPD BETWEEN 91 AND 120 THEN '5. Kurang Lancar (91-120 hari)'
        WHEN DPD BETWEEN 121 AND 180 THEN '6. Diragukan (121-180 hari)'
        ELSE '7. Macet (>180 hari)'
    END AS aging_bucket,
    COUNT(*) AS jumlah_rekening,
    SUM(OUTSTANDING) AS outstanding_amount,
    SUM(TUNGGAKAN_POKOK) AS tunggakan_pokok,
    SUM(TUNGGAKAN_MARGIN) AS tunggakan_margin
FROM FINANCING
WHERE STATUS = 'A'
GROUP BY 
    CASE 
        WHEN DPD = 0 THEN '1. Lancar (0 hari)'
        WHEN DPD BETWEEN 1 AND 30 THEN '2. DPK Ringan (1-30 hari)'
        WHEN DPD BETWEEN 31 AND 60 THEN '3. DPK Sedang (31-60 hari)'
        WHEN DPD BETWEEN 61 AND 90 THEN '4. DPK Berat (61-90 hari)'
        WHEN DPD BETWEEN 91 AND 120 THEN '5. Kurang Lancar (91-120 hari)'
        WHEN DPD BETWEEN 121 AND 180 THEN '6. Diragukan (121-180 hari)'
        ELSE '7. Macet (>180 hari)'
    END
ORDER BY aging_bucket;
```

### Template: Bagi Hasil DST (Equivalent Rate)
```sql
-- Equivalent Rate = perbandingan bagi hasil yang dibayar vs konvensional

SELECT 
    PRODUCT_CODE,
    TENOR,
    SUM(BALANCE) AS total_dana,
    SUM(BAGI_HASIL_BULAN) AS total_bh_dibayar,
    -- Equivalent Rate annualized
    ROUND(
        SUM(BAGI_HASIL_BULAN) / NULLIF(SUM(BALANCE), 0) * 12 * 100, 2
    ) AS equivalent_rate_pa
FROM DEPOSIT_DST
WHERE STATUS = 'A'
  AND PERIOD_DATE = :tgl
GROUP BY PRODUCT_CODE, TENOR
ORDER BY PRODUCT_CODE, TENOR;
```

---

## 3. MAPPING KODE UMUM PER JENIS SISTEM

### Kode Produk Pembiayaan (Sangat Berbeda Tiap Bank)
```
Yang harus dikonfirmasi:
  - Apakah ada tabel master produk?
  - Kode apa yang mewakili Murabahah, Mudharabah, Musyarakah, Ijarah, dll?
  
Contoh variasi kode di beberapa bank:
  Bank A: MRB=Murabahah, MDH=Mudharabah, MSY=Musyarakah
  Bank B: 01=Murabahah, 02=Mudharabah, 03=Musyarakah
  Bank C: MURA, MUDH, MUSY (4 char)
  Bank D: kode 3 digit berurutan dari kode CBS vendor

→ SELALU verifikasi dengan: SELECT DISTINCT PRODUCT_CODE, PRODUCT_NAME FROM MST_PRODUCT
```

### Kode Status Rekening
```
Aktif: A, ACT, 1, Y, ACTIVE, OPEN
Lunas: L, LNS, 0, CLS, CLOSED, LUNAS
Hapus Buku: HB, WO, WRITEOFF, WRITTEN_OFF
NIF (Non Interest/Non Income): NIF, SUSPEND
```

### Kode Kolektibilitas
```
Paling umum: 1, 2, 3, 4, 5 (angka)
Variasi: L=Lancar, DPK, KL, D, M (huruf)
OJK format: COL1, COL2, COL3, COL4, COL5
```

---

## 4. TIPS DEBUGGING DATABASE CBS

### Saat Data Tidak Cocok dengan Laporan Manual
```
1. Cek filter tanggal: apakah menggunakan BOOKING_DATE, VALUE_DATE, 
   atau PERIOD_DATE? (bisa berbeda!)

2. Cek status aktif: rekening dengan DPD tinggi bisa memiliki status
   yang berbeda dari 'A' (misal sudah di-write-off tapi masih ada)

3. Cek off-balance sheet: fasilitas yang belum ditarik (uncommitted)
   mungkin tidak ada di tabel utama pembiayaan

4. Cek konsolidasi: apakah data include cabang lain / seluruh bank?
   Cari kolom BRANCH_CODE atau ENTITY_CODE

5. Cek currency: pembiayaan valas mungkin dalam USD, perlu konversi
   dengan kurs tanggal laporan

6. Double counting: hati-hati dengan join yang menghasilkan baris ganda
   (terutama jika satu pembiayaan punya banyak agunan)
```

### Query Diagnostik Umum
```sql
-- Cek distribusi nilai kolom status/kualitas
SELECT STATUS, COUNT(*), SUM(OUTSTANDING)
FROM FINANCING GROUP BY STATUS;

-- Cek rentang tanggal
SELECT MIN(BOOKING_DATE), MAX(BOOKING_DATE) FROM FINANCING;

-- Cek null values di kolom kritis
SELECT 
    COUNT(*) AS total,
    SUM(CASE WHEN KUALITAS IS NULL THEN 1 ELSE 0 END) AS null_kualitas,
    SUM(CASE WHEN OUTSTANDING IS NULL THEN 1 ELSE 0 END) AS null_outstanding
FROM FINANCING WHERE STATUS = 'A';
```

---

## 5. ARSITEKTUR UMUM CBS SYARIAH

```
Modul Utama CBS:
  ┌─────────────────────────────────────────────────┐
  │  CORE BANKING SYSTEM                            │
  │                                                 │
  │  ┌──────────┐  ┌──────────┐  ┌──────────────┐  │
  │  │ Financing│  │ Funding  │  │  General     │  │
  │  │ (Pembiay)│  │ (DPK/DST)│  │  Ledger (GL) │  │
  │  └──────────┘  └──────────┘  └──────────────┘  │
  │                                                 │
  │  ┌──────────┐  ┌──────────┐  ┌──────────────┐  │
  │  │  CIF     │  │ Treasury │  │  Trade       │  │
  │  │(Nasabah) │  │          │  │  Finance     │  │
  │  └──────────┘  └──────────┘  └──────────────┘  │
  │                                                 │
  │  ┌──────────────────────────────────────────┐   │
  │  │  Reporting / Data Warehouse / MIS        │   │
  │  └──────────────────────────────────────────┘   │
  └─────────────────────────────────────────────────┘

Integrasi Eksternal:
  - BI-FAST / RTGS / SKN (payment system)
  - SLIK OJK (Sistem Layanan Informasi Keuangan — pengganti BI Checking)
  - SiPEBI (pelaporan ke OJK)
  - PPATK (pelaporan anti pencucian uang)
  - DJP (e-faktur, SPT)
  - BPJS Ketenagakerjaan
```

---

## 6. CHECKLIST SEBELUM MEMBANGUN QUERY KOMPLEKS

```
□ Sudah dapat DDL/skema tabel yang relevan?
□ Sudah tahu kode status aktif rekening?
□ Sudah tahu kode kolektibilitas (angka/huruf/string)?
□ Sudah tahu kode produk untuk akad yang relevan?
□ Sudah konfirmasi field tanggal yang digunakan (booking vs value vs period)?
□ Sudah tahu apakah data sudah di-aggregate per bulan atau masih level transaksi?
□ Sudah tahu apakah ada tabel periode/snapshot bulanan atau harus dihitung dari tabel live?
□ Sudah tahu kurs valas yang digunakan untuk pembiayaan non-IDR?
□ Sudah ada sample output yang diharapkan untuk validasi?
□ Sudah konfirmasi unit amount (rupiah penuh, ribuan, jutaan)?
```
