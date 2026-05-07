# 📋 DESIGN SPEC: G4 Financing Rekapitulasi & Risk Master Console

> **Status:** `READY TO IMPLEMENT`
> **Versi Spec:** 1.0
> **Tanggal:** 2026-05-07
> **Penulis:** Antigravity AI Agent
> **Berlaku untuk:** `monitoring-dashboard` (Laravel 13 + Vue 3 + Vuetify)

---

## 🎯 TUJUAN & RUANG LINGKUP

Mengubah tiga halaman terpisah (`/rekapitulasi`, `/coll`, `/risk`) menjadi satu **Business Intelligence Console terpusat** yang disebut **"Master Rekap Console"** di `Rekapitulasi.vue`.

> **Masalah yang dipecahkan:** Tab Fatigue di level Direksi, data kolektibilitas tersebar di halaman berbeda, tidak ada NPF Heatmap visual.

---

## 📊 STEP 0: SCHEMA DISCOVERY — HASIL TEMUAN

Schema discovery telah dieksekusi langsung ke database aktif `MCI_APR26_30042026`.

### TOFLMB — Kolom Kunci yang Dikonfirmasi

| Field | Tipe | Kegunaan |
|-------|------|----------|
| `colbaru` | VARCHAR | Kolektibilitas saat ini (1-5) |
| `osmdlc` | DECIMAL | Outstanding Pokok (O/S) |
| `haritgk` | INT | Hari tunggakan (untuk Aging Policy OJK) |
| `ppap` | DECIMAL | Provisi PPAP |
| `segmen` | VARCHAR | Kode segmen (FK ke tabel SEGMEN) |
| `kdloc` | VARCHAR | Kode cabang (FK ke tabel CABANG) |
| `kdwil` | VARCHAR | Kode wilayah (FK ke tabel WILAYAH) |
| `kdaoh` | VARCHAR | Kode AO (FK ke tabel AO) |
| `kdprd` | VARCHAR | Kode produk (FK ke tabel SETUPLOAN) |
| `sekon` | VARCHAR | **Sektor Ekonomi (kode langsung, tidak ada tabel FK)** |
| `sekon_usaha` | VARCHAR | Sektor usaha (detail sekon) |
| `stsrec` | VARCHAR | Status record (filter: 'A' = aktif) |
| `stsacc` | VARCHAR | Status akun (exclude: 'W' = write-off) |

### Tabel SEGMEN — Dikonfirmasi
Kolom `kdseg` (PK) dan `ket` (nama). Contoh: `001=UMKM`, `002=PINJAMAN KARYAWAN`.

### TEMUAN KRITIS: Sektor Ekonomi
**Tidak ada tabel referensi `SEKON*` di SQL Server.** Field `sekon` berisi kode standar BI/OJK langsung.
Implikasi: GROUP BY `sekon` langsung, tidak perlu JOIN tambahan, label tampil sebagai kode.

---

## 🏗️ ARSITEKTUR YANG DIPUTUSKAN

### Koreksi dari Master Plan Original

| Aspek | Original Plan | REVISED (Final) | Alasan |
|-------|--------------|-----------------|--------|
| Service Layer | Buat `FinancingRekapService.php` | **EXTEND `FinancingRepository`** | Sudah ada `getRekapitulasi()`, hindari duplikasi |
| State Management | Pinia State | **Component-local `ref()`** | Ikuti pola `Target.vue` yang sudah ada |
| CSS Framework | Vuetify + Tailwind | **Vuetify + Scoped Vanilla CSS** | Tailwind tidak terinstall konsisten |
| Sektor Ekonomi | JOIN ke tabel SEKON | **GROUP BY `sekon` langsung** | Tidak ada tabel referensi |
| Kolektibilitas | SQL PIVOT | **Conditional Aggregation (CASE WHEN)** | Lebih fleksibel, portabel |

### File yang Terlibat

```
Backend:
├── app/Repositories/Mci/FinancingRepository.php              EXTEND
├── app/Repositories/Interfaces/FinancingRepositoryInterface.php  EXTEND
├── app/Http/Controllers/Api/v1/FinancingController.php       EXTEND
└── routes/api.php                                             TAMBAH route

Frontend:
└── resources/js/Pages/Financing/Rekapitulasi.vue             REWRITE TOTAL
```

---

## STEP 1: BACKEND — Spesifikasi Detail

### 1.1 SQL Query Inti (Template untuk group_by=cabang)

```sql
SELECT
    b.nama                                              AS label,
    b.kdloc                                             AS id,
    COUNT(a.nokontrak)                                  AS noa,
    SUM(a.osmdlc)                                       AS total_os,
    SUM(a.ppap)                                         AS total_ppap,
    SUM(CASE WHEN a.colbaru = '1' THEN 1    ELSE 0 END) AS kol1_noa,
    SUM(CASE WHEN a.colbaru = '2' THEN 1    ELSE 0 END) AS kol2_noa,
    SUM(CASE WHEN a.colbaru = '3' THEN 1    ELSE 0 END) AS kol3_noa,
    SUM(CASE WHEN a.colbaru = '4' THEN 1    ELSE 0 END) AS kol4_noa,
    SUM(CASE WHEN a.colbaru = '5' THEN 1    ELSE 0 END) AS kol5_noa,
    SUM(CASE WHEN a.colbaru = '1' THEN a.osmdlc ELSE 0 END) AS kol1_os,
    SUM(CASE WHEN a.colbaru = '2' THEN a.osmdlc ELSE 0 END) AS kol2_os,
    SUM(CASE WHEN a.colbaru = '3' THEN a.osmdlc ELSE 0 END) AS kol3_os,
    SUM(CASE WHEN a.colbaru = '4' THEN a.osmdlc ELSE 0 END) AS kol4_os,
    SUM(CASE WHEN a.colbaru = '5' THEN a.osmdlc ELSE 0 END) AS kol5_os,
    SUM(CASE WHEN a.colbaru IN ('3','4','5') THEN a.osmdlc ELSE 0 END) AS npf_os,
    SUM(CASE WHEN a.colbaru IN ('3','4','5') THEN 1 ELSE 0 END)        AS npf_noa,
    CASE WHEN SUM(a.osmdlc) > 0
        THEN ROUND(
            SUM(CASE WHEN a.colbaru IN ('3','4','5') THEN a.osmdlc ELSE 0 END)
            / SUM(a.osmdlc) * 100, 2)
        ELSE 0
    END AS npf_ratio
FROM TOFLMB a
JOIN CABANG b ON a.kdloc = b.kdloc
WHERE a.stsrec = 'A' AND a.stsacc <> 'W'
GROUP BY b.nama, b.kdloc
ORDER BY b.nama ASC
```

### 1.2 Dynamic JOIN berdasarkan group_by

| group_by | JOIN | Label | ID |
|----------|------|-------|----|
| `cabang` | `CABANG b ON a.kdloc = b.kdloc` | `b.nama` | `b.kdloc` |
| `wilayah` | `WILAYAH b ON a.kdwil = b.kodewil` | `b.ket` | `b.kodewil` |
| `ao` | `AO b ON a.kdaoh = b.kdao` | `b.nmao` | `b.kdao` |
| `produk` | `SETUPLOAN b ON a.kdprd = b.kdprd` | `b.ket` | `b.kdprd` |
| `segmen` | `SEGMEN b ON a.segmen = b.kdseg` | `b.ket` | `b.kdseg` |
| `sekon` | *(no JOIN)* | `a.sekon` | `a.sekon` |

### 1.3 Method Signature

```php
/**
 * Rekapitulasi master dengan breakdown kolektibilitas Kol1-Kol5 + NPF Ratio.
 * Single-hit SQL. Cache 60s. Dukung 6 dimensi.
 *
 * @param string $groupBy  Dimensi: cabang|wilayah|ao|produk|segmen|sekon
 * @param string $cabang   Filter cabang (optional)
 * @return array{rows: Collection, totals: array, meta: array}
 */
public function getRekapMaster(string $groupBy = 'cabang', string $cabang = ''): array
```

### 1.4 Response JSON Contract

```json
{
  "success": true,
  "meta": { "group_by": "cabang", "generated_at": "2026-05-07T06:00:00+07:00" },
  "totals": {
    "noa": 1523, "total_os": 87234567890,
    "npf_os": 4123456789, "npf_ratio": 4.73, "total_ppap": 1234567890
  },
  "rows": [
    {
      "label": "Cab. Purwokerto", "id": "0101",
      "noa": 245, "total_os": 15234567890, "total_ppap": 234567890,
      "npf_ratio": 3.21, "npf_os": 489234567, "npf_noa": 12,
      "kol1_noa": 220, "kol1_os": 12345678900,
      "kol2_noa": 13,  "kol2_os": 2399654423,
      "kol3_noa": 5,   "kol3_os": 189234567,
      "kol4_noa": 4,   "kol4_os": 150000000,
      "kol5_noa": 3,   "kol5_os": 150000000
    }
  ]
}
```

### 1.5 API Route

```php
// Di routes/api.php dalam grup prefix('financing'):
Route::get('/rekap-master', [FinancingController::class, 'rekapMaster']);
// GET /api/v1/financing/rekap-master?group_by=cabang&cabang=
```

---

## STEP 2: FRONTEND — Spesifikasi UI/UX

### 2.1 Layout

```
HEADER: "Master Rekap Console" + icon
FILTER BAR: [Dimensi Toggle] [Metric Switcher] [Filter Cabang] [Zero Noise Checkbox]
─────────────────────────────────────────────
SUMMARY SCORECARDS (4 kartu KPI):
  Total NOA | Total O/S | NPF Ratio (Heatmap) | Total PPAP
─────────────────────────────────────────────
VIEW TOGGLE: [Grid Mode] [Chart Mode]
─────────────────────────────────────────────
GRID MODE:
  Tabel kolom: Label | NOA | Total O/S | Kol1 | Kol2 | Kol3 | Kol4 | Kol5 | NPF% | PPAP
  (Kol1-5 toggle antara OS / NOA via Metric Switcher)
  NPF% = HEATMAP (merah >5%, kuning 2-5%, hijau <2%)
─────────────────────────────────────────────
CHART MODE:
  TreeMap: Sebaran O/S per dimensi
  Donut: NPF vs Lancar ratio global
```

### 2.2 Dimensi Toggle Options

```javascript
const dimensionOptions = [
  { label: 'Per Cabang',          value: 'cabang' },
  { label: 'Per Wilayah',         value: 'wilayah' },
  { label: 'Per Account Officer', value: 'ao' },
  { label: 'Per Produk/Akad',     value: 'produk' },
  { label: 'Per Segmen',          value: 'segmen' },
  { label: 'Per Sektor Ekonomi',  value: 'sekon' },
]
```

### 2.3 Heatmap Cell Style

```javascript
const getNpfCellStyle = (npfRatio) => {
  if (npfRatio > 5)  return { background: '#fee2e2', color: '#dc2626', fontWeight: '700' }
  if (npfRatio > 2)  return { background: '#fef9c3', color: '#ca8a04', fontWeight: '600' }
  if (npfRatio > 0)  return { background: '#dcfce7', color: '#16a34a', fontWeight: '600' }
  return { background: '#f8fafc', color: '#94a3b8' }
}
```

### 2.4 Metric Switcher Behavior

Saat `selectedMetric` berubah antara `os` dan `noa`, kolom Kol 1-5 otomatis baca `kol{n}_os` atau `kol{n}_noa`. **TIDAK perlu API call ulang.**

### 2.5 Zero Noise Filter

```javascript
const filteredRows = computed(() => {
  if (!hideEmptyRows.value) return rows.value
  return rows.value.filter(r => r.noa > 0 && r.total_os > 0)
})
```

### 2.6 State Management (Component-Local — TIDAK PAKAI PINIA)

```javascript
const selectedDimension = ref('cabang')
const selectedMetric    = ref('os')
const selectedCabang    = ref('')
const viewMode          = ref('grid')
const hideEmptyRows     = ref(true)
const isLoading         = ref(false)
const rekapData         = ref({ rows: [], totals: {}, meta: {} })
watch([selectedDimension, selectedCabang], fetchRekapMaster)
```

---

## COMPLIANCE & LOGIKA BISNIS

### NPF Formula (Wajib sesuai Standar BI/OJK)

```
NPF Ratio = (Kol3_OS + Kol4_OS + Kol5_OS) / Total_OS × 100%
```

Dihitung di SQL Server. JANGAN hitung ulang di PHP/Vue.

### Aging Policy

Field `haritgk` di TOFLMB adalah sumber kebenaran hari tunggakan.
Kolektibilitas dibaca dari field `colbaru` yang sudah ditetapkan CBS MCI.

### Label Kolektibilitas

| Kode | Label | Status |
|------|-------|--------|
| 1 | Lancar | Sehat |
| 2 | DPK (Dalam Perhatian Khusus) | Perhatian |
| 3 | Kurang Lancar | NPF |
| 4 | Diragukan | NPF |
| 5 | Macet | NPF |

---

## ROADMAP IMPLEMENTASI

### STEP 0: Schema Discovery — SELESAI
- [x] Konfirmasi kolom TOFLMB (selesai sesi ini, 2026-05-07)
- [x] Tidak ada tabel SEKON referensi — gunakan GROUP BY sekon langsung
- [x] Konfirmasi tabel SEGMEN

### STEP 1: Backend
- [ ] `FinancingRepository::getRekapMaster()` — SQL conditional aggregation
- [ ] `FinancingRepositoryInterface` — tambah signature method
- [ ] `FinancingController::rekapMaster()` — validasi params, return JSON
- [ ] `routes/api.php` — daftarkan route GET rekap-master
- [ ] **Verifikasi:** Bandingkan total NPF dengan laporan legacy `mdb-dashboard`

### STEP 2: Frontend Grid Mode
- [ ] Hapus placeholder ComingSoon di `Rekapitulasi.vue`
- [ ] 4 Summary Scorecards KPI
- [ ] Dimensi Toggle dropdown
- [ ] Metric Switcher button group
- [ ] Filter Cabang
- [ ] Tabel matriks Kol 1-5 dengan Heatmap NPF
- [ ] Zero Noise Filter

### STEP 3: Chart Mode & Polish
- [ ] View Toggle Grid ↔ Chart
- [ ] ApexCharts TreeMap (distribusi O/S)
- [ ] ApexCharts Donut (NPF vs Lancar)
- [ ] Final QA vs `RiskAggregation.vue` dan sistem legacy
- [ ] `npm run build`
- [ ] Update `UPDATE_LOG.md`

---

## ATURAN TIDAK BOLEH DILANGGAR

1. JANGAN buat `FinancingRekapService.php` baru — extend `FinancingRepository`
2. JANGAN pakai Pinia — pakai `ref()` component-local
3. JANGAN pakai Tailwind — pakai Vuetify classes + scoped CSS
4. JANGAN pakai SQL PIVOT — pakai Conditional Aggregation CASE WHEN
5. JANGAN hitung NPF Ratio di PHP/Vue — hitung di SQL
6. JANGAN API call ulang untuk Metric Switcher — toggle client-side
7. WAJIB cache TTL 60s menggunakan pola `MciBaseRepository`
8. WAJIB log query lambat (>100ms) via `logPerformance()`

---

## REFERENSI FILE TERKAIT

| File | Peran | Status |
|------|-------|--------|
| `app/Repositories/Mci/FinancingRepository.php` | Repository utama | Ada, akan di-extend |
| `app/Repositories/Interfaces/FinancingRepositoryInterface.php` | Interface | Ada, akan di-extend |
| `app/Http/Controllers/Api/v1/FinancingController.php` | Controller | Ada, akan di-extend |
| `resources/js/Pages/Financing/RiskAggregation.vue` | Referensi QA Risk | Ada |
| `resources/js/Pages/Financing/Target.vue` | Referensi pola state | Ada, ikuti polanya |
| `app/Services/Mci/DashboardRepository.php` | Trend data (reusable) | Ada, siap pakai |
| `app/Services/Mci/MciBaseRepository.php` | Base class pattern | Ada, semua pattern ada di sini |

---

*Dokumen ini dibuat untuk AI agent berikutnya yang melanjutkan implementasi. Baca seluruh dokumen sebelum menulis kode.*
