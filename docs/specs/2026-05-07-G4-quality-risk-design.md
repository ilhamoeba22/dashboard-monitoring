# 📋 DESIGN SPEC: G4 Asset Quality & Risk Console

> **Status:** `DRAFTING`
> **Versi Spec:** 1.0
> **Tanggal:** 2026-05-07
> **Penulis:** Antigravity AI Agent
> **File:** `Quality.vue`

---

## 🎯 TUJUAN & RUANG LINGKUP

Membangun konsol analisis risiko terpadu yang memadukan data **Kolektibilitas Detail**, **Aging Portfolio**, dan **Risk Concentration**. Halaman ini dirancang untuk menjawab pertanyaan: *"Seberapa aman uang yang sudah kita salurkan?"*.

---

## 🏗️ ARSITEKTUR INFORMASI (TAB-BASED)

Halaman ini akan menggunakan **Vuetify Tabs** untuk memisahkan dimensi risiko:

### Tab 1: 🏥 Portofolio Health (Aging Detail)
*   **Fokus:** Usia tunggakan secara granular.
*   **Metrik:**
    *   `Lancar (0 hari)`
    *   `DPK 1-30 hari`
    *   `DPK 31-60 hari`
    *   `DPK 61-90 hari`
    *   `NPF (> 90 hari)`
*   **Visual:** Stacked Bar Chart (Horizontal) per Cabang/Produk.

### Tab 2: 📉 Risk Concentration (Exposure)
*   **Fokus:** Di mana risiko paling menumpuk?
*   **Metrik:**
    *   **Sektor Ekonomi:** TreeMap sebaran NPF per sektor.
    *   **Top 10 Risk Alert:** Tabel nasabah dengan tunggakan terbesar (> 100jt).
    *   **Produk Berisiko:** Donut chart perbandingan NPF per jenis akad.

### Tab 3: 🛡️ Proteksi & Recovery (Coverage)
*   **Fokus:** Ketahanan modal dan pemulihan aset.
*   **Metrik:**
    *   **Coverage Ratio:** PPAP (Cadangan) vs Total NPF.
    *   **Recovery Tracker:** Dashboard untuk memantau angsuran nasabah yang sudah masuk kategori Kol 2-5.

---

## 📊 BACKEND: `getQualityAnalytics()`

### SQL Logic: Aging Bucket Aggregation
```sql
SELECT
    label,
    SUM(CASE WHEN haritgk = 0 THEN osmdlc ELSE 0 END) AS aging_0,
    SUM(CASE WHEN haritgk BETWEEN 1 AND 30 THEN osmdlc ELSE 0 END) AS aging_1_30,
    SUM(CASE WHEN haritgk BETWEEN 31 AND 60 THEN osmdlc ELSE 0 END) AS aging_31_60,
    SUM(CASE WHEN haritgk BETWEEN 61 AND 90 THEN osmdlc ELSE 0 END) AS aging_61_90,
    SUM(CASE WHEN haritgk > 90 THEN osmdlc ELSE 0 END) AS aging_npf
FROM TOFLMB
...
```

---

## 🎨 UI/UX ENTERPRISE STANDARDS

1.  **Zero Redundancy:** Tidak menampilkan O/S atau NOA secara dominan. Fokus pada % Rasio dan Nominal Tunggakan.
2.  **Safety Indicators:** Penggunaan warna yang sangat spesifik (Hijau=Aman, Amber=Watchlist, Merah=Danger).
3.  **Sticky Summary:** Top Scorecards yang selalu menampilkan NPF Gross/Net secara realtime di bagian atas halaman.

---

## 🛠️ IMPLEMENTATION STEPS

1.  **Step 1:** Menambahkan route `/financing/quality` di `routes/web.php` (Inertia).
2.  **Step 2:** Implementasi `getQualityAnalytics` di `FinancingRepository`.
3.  **Step 3:** Membangun UI `Quality.vue` dengan Tab System.
4.  **Step 4:** Integrasi ApexCharts untuk visualisasi Aging.
