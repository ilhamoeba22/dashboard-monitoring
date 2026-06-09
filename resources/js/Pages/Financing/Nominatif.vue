<script setup>
import { computed, ref, watch, onMounted } from 'vue'
import { Head } from '@inertiajs/vue3'
import DefaultLayout from '@/layouts/default.vue'
import * as XLSX from 'xlsx'
import jsPDF from 'jspdf'
import autoTable from 'jspdf-autotable'
import '@/assets/css/financing-shared.css'
import { formatExactRupiah } from '@/utils/money'

defineOptions({ layout: DefaultLayout })

// Debounce helper
function debounce(fn, delay) {
  let timeout
  return (...args) => {
    clearTimeout(timeout)
    timeout = setTimeout(() => fn(...args), delay)
  }
}

// State
const loading = ref(false)
const snackbar = ref(false)
const snackbarMessage = ref('')
const snackbarColor = ref('warning')

function notify(message, color = 'warning') {
  snackbarMessage.value = message
  snackbarColor.value = color
  snackbar.value = true
}
const search = ref('')
const page = ref(1)
const itemsPerPage = ref(15)
const totalItems = ref(0)
const serverItems = ref([])
const periodMeta = ref({
  requested_period: null,
  active_period: null,
  is_historical: false,
  period_available: true,
  source_table: 'TOFLMB',
  message: null,
})

const rowsPerPageOptions = [
  { title: '15 Baris', value: 15 },
  { title: '30 Baris', value: 30 },
  { title: '50 Baris', value: 50 },
  { title: '100 Baris', value: 100 },
]

// Enterprise Filters
const filterCabang = ref('Semua Cabang')
const filterKol = ref('Semua Kol')
const filterTahun = ref(null)
const filterBulan = ref(null)
const cabangOptions = ref(['Semua Cabang']) 
const kolOptions = ['Semua Kol', '1 - Lancar', '2 - DPK', '3 - Kurang Lancar', '4 - Diragukan', '5 - Macet']
const yearOptions = computed(() => {
  const current = new Date().getFullYear()
  return [current, current - 1, current - 2, current - 3, current - 4]
})
const monthOptions = [
  { title: 'Januari', value: 1 }, { title: 'Februari', value: 2 }, { title: 'Maret', value: 3 },
  { title: 'April', value: 4 }, { title: 'Mei', value: 5 }, { title: 'Juni', value: 6 },
  { title: 'Juli', value: 7 }, { title: 'Agustus', value: 8 }, { title: 'September', value: 9 },
  { title: 'Oktober', value: 10 }, { title: 'November', value: 11 }, { title: 'Desember', value: 12 }
]
const activePeriodLabel = computed(() => {
  if (!filterTahun.value || !filterBulan.value) return 'Periode aktif CBS'
  const month = monthOptions.find(item => item.value === filterBulan.value)?.title || '-'
  return `${month} ${filterTahun.value}`
})
const periodUnavailable = computed(() => periodMeta.value?.period_available === false)
const periodUnavailableMessage = computed(() => {
  if (!periodUnavailable.value) return ''
  const active = String(periodMeta.value?.active_period || '')
  const activeYear = active.slice(0, 4)
  const activeMonth = monthOptions.find(item => item.value === Number(active.slice(4, 6)))?.title || active.slice(4, 6)

  return periodMeta.value?.message
    || `Data periode ${activePeriodLabel.value} belum tersedia. Periode database aktif saat ini ${activeMonth} ${activeYear}.`
})

// Fetch Metadata
async function fetchCabangs() {
  try {
    const response = await fetch('/api/v1/financing/cabangs')
    const json = await response.json()
    if (json.success) {
      const names = json.data.map(c => c.nama.trim())
      cabangOptions.value = ['Semua Cabang', ...names]
    }
  } catch (error) {
    console.error('Gagal memuat list cabang:', error)
  }
}

function syncFiltersFromPeriodMeta(meta) {
  const requested = String(meta?.requested_period || '')
  if (requested.length !== 6) return

  const year = Number(requested.slice(0, 4))
  const month = Number(requested.slice(4, 6))
  if (Number.isInteger(year) && Number.isInteger(month) && month >= 1 && month <= 12) {
    filterTahun.value = year
    filterBulan.value = month
  }
}

async function loadItems(options) {
  loading.value = true
  
  const p = options?.page || page.value
  const ipp = options?.itemsPerPage || itemsPerPage.value
  const s = search.value

  try {
    const params = new URLSearchParams({
      page: p,
      per_page: ipp,
      type: ''
    })
    if (filterTahun.value) params.append('tahun', filterTahun.value)
    if (filterBulan.value) params.append('bulan', filterBulan.value)
    
    if (s) params.append('search', s)
    if (filterCabang.value && filterCabang.value !== 'Semua Cabang') {
      params.append('cabang', filterCabang.value)
    }
    if (filterKol.value && filterKol.value !== 'Semua Kol') {
      params.append('kol', String(filterKol.value).charAt(0)) 
    }

    const response = await fetch(`/api/v1/financing/nominative?${params}`)
    const json = await response.json()

    if (json.success) {
      periodMeta.value = json.period_meta || periodMeta.value
      syncFiltersFromPeriodMeta(periodMeta.value)
      serverItems.value = json.data.data
      totalItems.value = json.data.total
      page.value = p
      itemsPerPage.value = ipp
    }
  } catch (error) {
    console.error('Gagal memuat data nominatif:', error)
  } finally {
    loading.value = false
  }
}

// --- EXPORT ENGINE ---

const getFileTypeTitle = () => {
  if (typeof window !== 'undefined') {
    if (window.location.pathname.includes('karyawan')) return { param: 'karyawan', title: 'KARYAWAN' }
    if (window.location.pathname.includes('sindikasi')) return { param: 'sindikasi', title: 'SINDIKASI' }
  }
  return { param: '', title: 'KESELURUHAN' }
}

async function fetchAllDataForExport() {
  loading.value = true
  try {
    const { param } = getFileTypeTitle()
    const params = new URLSearchParams({
      page: 1,
      per_page: 10000,
    })
    if (filterTahun.value) params.append('tahun', filterTahun.value)
    if (filterBulan.value) params.append('bulan', filterBulan.value)
    if (param) params.append('type', param)
    if (search.value) params.append('search', search.value)
    if (filterCabang.value !== 'Semua Cabang') params.append('cabang', filterCabang.value)
    if (filterKol.value !== 'Semua Kol') params.append('kol', String(filterKol.value).charAt(0))

    const response = await fetch(`/api/v1/financing/nominative?${params}`)
    const json = await response.json()
    if (json.period_meta?.period_available === false) {
      notify(json.period_meta.message || `Data periode ${activePeriodLabel.value} belum tersedia di database.`)
      return []
    }
    return json.success ? json.data.data : []
  } catch (error) {
    console.error('Export Error:', error)
    return []
  } finally {
    loading.value = false
  }
}

const getDynamicFileName = (ext) => {
  const d = new Date()
  const months = ['Jan','Feb','Mar','Apr','Mei','Jun','Jul','Ags','Sep','Okt','Nov','Des']
  const dateStr = `${d.getDate().toString().padStart(2, '0')}-${months[d.getMonth()]}-${d.getFullYear()}`
  const timeStr = `${d.getHours().toString().padStart(2, '0')}${d.getMinutes().toString().padStart(2, '0')}`
  const { title } = getFileTypeTitle()
  
  let kolLabel = ''
  if (filterKol.value && filterKol.value !== 'Semua Kol') {
    kolLabel = `_KOL-${String(filterKol.value).charAt(0)}`
  }
  
  const requestedPeriod = String(periodMeta.value?.requested_period || '')
  const periodLabel = filterTahun.value && filterBulan.value
    ? `${filterTahun.value}${String(filterBulan.value).padStart(2, '0')}`
    : (requestedPeriod || 'periode-aktif')
  return `Data_Nominatif_${title}_${periodLabel}${kolLabel}_${dateStr}_${timeStr}.${ext}`
}

const exportExcel = async () => {
  const data = await fetchAllDataForExport()
  if (!data.length) return notify('Tidak ada data yang sesuai filter untuk diexport.')

  const excelData = data.map((item, i) => ({
    'NO': i + 1,
    'NO REKENING': item.nokontrak,
    'NAMA NASABAH': item.nama,
    'SEGMENTASI': item.segmen || '-',
    'NOMOR AKAD': item.noakad || '-',
    'TGL EFEKTIF': item.tgleff || '-',
    'JW': item.jw || 0,
    'SISA JW': item.sisajw || 0,
    'MODAL AWAL': item.mdlawal || 0,
    'O/S MODAL': item.osmdlc || 0,
    'TUNGGAK POKOK': item.tgkmdl || 0,
    'TUNGGAK MARGIN': item.tgkmgn || 0,
    'KOLEKTIBILITAS': getKolLabel(item.colbaru),
    'SALDO TABUNGAN': item.saldo_netto || 0,
    'NILAI JAMINAN': item.htgagun || 0,
    'MARKETING': item.ao || '-',
    'CABANG': item.cabang || '-'
  }))

  const worksheet = XLSX.utils.json_to_sheet(excelData)
  const workbook = XLSX.utils.book_new()
  XLSX.utils.book_append_sheet(workbook, worksheet, "Data Nominatif")
  XLSX.writeFile(workbook, getDynamicFileName('xlsx'))
}

// Helper: Load Image
const loadImage = (url) => {
  return new Promise((resolve) => {
    const img = new Image()
    img.src = url
    img.onload = () => resolve(img)
    img.onerror = () => resolve(null)
  })
}

const exportPDF = async () => {
  const data = await fetchAllDataForExport()
  if (!data.length) return notify('Tidak ada data yang sesuai filter untuk diexport.')

  // Portrait Legal
  const doc = new jsPDF({ orientation: 'portrait', format: 'legal' })
  const { title } = getFileTypeTitle()
  const d = new Date()

  // --- KOP SURAT PROFESIONAL ---
  const logo = await loadImage('/assets/images/logos/logo_mci.png')
  if (logo) {
    const props = doc.getImageProperties(logo)
    const h = 15 // Tinggi fiks 15mm
    const w = (props.width * h) / props.height // Lebar otomatis sesuai aspek rasio (biar tidak tergencet)
    const x = doc.internal.pageSize.getWidth() - w - 14 // Letakkan di pojok kanan
    doc.addImage(logo, 'PNG', x, 12, w, h)
  }

  doc.setFont('helvetica', 'bold')
  doc.setFontSize(16)
  doc.setTextColor(21, 128, 61) // Emerald Green
  doc.text('PT. BPRS HIK MCI', 14, 20)
  
  doc.setFontSize(12)
  doc.setTextColor(51, 65, 85) // Slate 700
  doc.text(`LAPORAN NOMINATIF PEMBIAYAAN - ${title}`, 14, 28)
  
  doc.setFontSize(9)
  doc.setFont('helvetica', 'normal')
  doc.text(`Dicetak pada : ${d.toLocaleString('id-ID')} WIB`, 14, 38)
  doc.text(`Filter Aktif : Periode [${activePeriodLabel.value}] | Cabang [${filterCabang.value}] | Kolektibilitas [${filterKol.value}]`, 14, 43)
  
  doc.setDrawColor(226, 232, 240)
  doc.setLineWidth(0.5)
  doc.line(14, 46, 200, 46) 

  const tableBody = data.map((item, i) => [
    i + 1,
    item.nokontrak,
    item.nama,
    formatCurrency(item.osmdlc),
    formatCurrency(item.tgkmdl),
    getKolLabel(item.colbaru),
    item.ao || '-',
    item.cabang || '-'
  ])

  autoTable(doc, {
    startY: 52,
    head: [['NO', 'REKENING', 'NAMA NASABAH', 'O/S MODAL', 'TUNGGAKAN', 'KOL', 'MARKETING', 'CABANG']],
    body: tableBody,
    theme: 'grid',
    headStyles: { fillColor: [21, 128, 61], textColor: 255, fontSize: 8, fontStyle: 'bold', halign: 'center' },
    styles: { fontSize: 7, cellPadding: 2, textColor: [51, 65, 85] },
    alternateRowStyles: { fillColor: [248, 250, 252] },
    columnStyles: {
      0: { halign: 'center', cellWidth: 10 },
      3: { halign: 'right' },
      4: { halign: 'right' },
      5: { halign: 'center' }
    }
  })

  doc.save(getDynamicFileName('pdf'))
}

// Handlers
const updateSearch = debounce(() => {
  loadItems({ page: 1, itemsPerPage: itemsPerPage.value })
}, 500)

watch(search, updateSearch)

watch([filterCabang, filterKol, filterTahun, filterBulan], () => {
  page.value = 1
  loadItems({ page: 1, itemsPerPage: itemsPerPage.value })
})

watch(itemsPerPage, (newVal) => {
  page.value = 1
  loadItems({ page: 1, itemsPerPage: newVal })
})

function formatCurrency(v) {
  if (v === null || v === undefined || v === '') return '-'
  return formatExactRupiah(v)
}

// Data Formatting Helpers
function formatNA(val) {
  if (val === null || val === undefined) return '-'
  const s = String(val).trim().toUpperCase()
  return (s === 'N/A' || s === 'NA' || s === '' || s === 'NULL') ? '-' : val
}

function getDebetStyle(val) {
  const s = String(val || '').trim().toLowerCase();
  if (s === 'cukup') return { backgroundColor: '#dcfce7', color: '#15803d', borderColor: '#bbf7d0' }; 
  if (s === 'kurang') return { backgroundColor: '#ffe4e6', color: '#be123c', borderColor: '#fecdd3' }; 
  return { backgroundColor: '#f1f5f9', color: '#334155', borderColor: '#e2e8f0' }; 
}

function getKolStyle(kol) {
  const k = String(kol || '').trim().toLowerCase();
  if (k === '1' || k === '01' || k === 'lancar') return { backgroundColor: '#dcfce7', color: '#15803d', borderColor: '#bbf7d0' };
  if (k === '2' || k === '02' || k === 'dpk') return { backgroundColor: '#dbeafe', color: '#1d4ed8', borderColor: '#bfdbfe' }; 
  if (k === '3' || k === '03' || k === 'kurang lancar') return { backgroundColor: '#fef3c7', color: '#b45309', borderColor: '#fde68a' }; 
  if (k === '4' || k === '04' || k === 'diragukan') return { backgroundColor: '#ffedd5', color: '#c2410c', borderColor: '#fed7aa' }; 
  if (k === '5' || k === '05' || k === 'macet') return { backgroundColor: '#ffe4e6', color: '#be123c', borderColor: '#fecdd3' }; 
  return { backgroundColor: '#f1f5f9', color: '#334155', borderColor: '#e2e8f0' };
}

function getKolLabel(kol) {
  const labels = {
    '1': 'Lancar', '01': 'Lancar',
    '2': 'DPK', '02': 'DPK',
    '3': 'Kurang Lancar', '03': 'Kurang Lancar',
    '4': 'Diragukan', '04': 'Diragukan',
    '5': 'Macet', '05': 'Macet'
  }
  return labels[String(kol).trim()] || `Kol ${kol}`
}

const headers = [
  { 
    title: 'No.', 
    key: 'index', 
    sortable: false, 
    align: 'center',
    headerProps: { style: 'position: sticky; left: 0px; z-index: 40; background-color: #f0fdf4; min-width: 50px; max-width: 50px; border-bottom: 1px solid #E2E8F0;' },
    cellProps: { style: 'position: sticky; left: 0px; z-index: 20; background-color: white; min-width: 50px; max-width: 50px; border-bottom: 1px solid #F1F5F9;' }
  },
  { 
    title: 'No. Rekening', 
    key: 'nokontrak', 
    sortable: false, 
    headerProps: { style: 'position: sticky; left: 50px; z-index: 40; background-color: #f0fdf4; min-width: 130px; max-width: 130px; border-bottom: 1px solid #E2E8F0;' },
    cellProps: { style: 'position: sticky; left: 50px; z-index: 20; background-color: white; min-width: 130px; max-width: 130px; border-bottom: 1px solid #F1F5F9;' }
  },
  { 
    title: 'Nama Nasabah', 
    key: 'nama', 
    sortable: false, 
    headerProps: { style: 'position: sticky; left: 180px; z-index: 40; background-color: #f0fdf4; min-width: 250px; max-width: 250px; border-bottom: 1px solid #E2E8F0; border-right: 2px solid #E2E8F0;' },
    cellProps: { style: 'position: sticky; left: 180px; z-index: 20; background-color: white; min-width: 250px; max-width: 250px; border-bottom: 1px solid #F1F5F9; border-right: 2px solid #E2E8F0; box-shadow: 4px 0 8px -2px rgba(0,0,0,0.08);' }
  },
  { title: 'Umur', key: 'umur', sortable: false, align: 'center', width: '70px' },
  { title: 'Kel. Umur', key: 'kelompok_umur', sortable: false, align: 'center', width: '100px' },
  { title: 'Segmentasi', key: 'segmen', sortable: false, width: '150px' },
  { title: 'Nomor Akad', key: 'noakad', sortable: false, width: '150px' },
  { title: 'Tgl Efektif', key: 'tgleff', sortable: false, align: 'center', width: '110px' },
  { title: 'JW', key: 'jw', sortable: false, align: 'center', width: '60px' },
  { title: 'Sisa JW', key: 'sisajw', sortable: false, align: 'center', width: '70px' },
  { title: 'Tgl Expired', key: 'tglexp', sortable: false, align: 'center', width: '110px' },
  { title: 'Modal Awal', key: 'mdlawal', sortable: false, align: 'end', width: '140px' },
  { title: 'O/S Modal', key: 'osmdlc', sortable: false, align: 'end', width: '140px' },
  { title: 'Tunggak Mdl', key: 'tgkmdl', sortable: false, align: 'end', width: '130px' },
  { title: 'Tunggak Mgn', key: 'tgkmgn', sortable: false, align: 'end', width: '130px' },
  { title: 'Coll Baru', key: 'colbaru', sortable: false, align: 'center', width: '90px' },
  { title: 'Tgl Macet', key: 'tglmacet', sortable: false, align: 'center', width: '110px' },
  { title: 'Saldo Tabungan', key: 'saldo_netto', sortable: false, align: 'end', width: '140px' },
  { title: 'Ket. Debet', key: 'keterangan_debet', sortable: false, align: 'center', width: '100px' },
  { title: 'Tgk vs Tab', key: 'tunggakan_vs_tabungan', sortable: false, align: 'end', width: '140px' },
  { title: 'Nilai Jaminan', key: 'htgagun', sortable: false, align: 'end', width: '140px' },
  { title: 'Pencadangan PPKA', key: 'ppap', sortable: false, align: 'end', width: '140px' },
  { title: 'Marketing', key: 'ao', sortable: false, width: '150px' },
  { title: 'Cabang', key: 'cabang', sortable: false, width: '130px' },
  { title: 'Wilayah', key: 'wilayah', sortable: false, width: '130px' },
  { title: 'Alamat', key: 'alamat', sortable: false, width: '300px' },
]

onMounted(() => {
  fetchCabangs()
  loadItems({ page: page.value, itemsPerPage: itemsPerPage.value, search: search.value })
})
</script>

<template>
  <div class="fin-page px-4 pt-0">
    <Head title="Nominatif Keseluruhan" />

    <!-- ── HERO HEADER ─────────────────────────────────────────── -->
    <div class="fin-hero mb-6">
      <div class="fin-hero__deco"></div>
      <div class="fin-hero__inner">
        <div class="fin-hero__top">
          <div class="fin-hero__icon">
            <v-icon icon="ri-file-list-3-line" size="26" color="white" />
          </div>
          <div class="fin-hero__meta">
            <h1 class="fin-hero__title">Nominatif Keseluruhan</h1>
            <p class="fin-hero__subtitle">Daftar rincian seluruh nasabah pembiayaan berdasarkan periode terpilih</p>
            <div class="fin-hero__badges">
              <span class="fin-badge fin-badge--teal">👥 Data Nasabah</span>
              <span class="fin-badge fin-badge--blue" style="background: rgba(59, 130, 246, 0.18); color: #bfdbfe; border-color: rgba(147, 197, 253, 0.28);">📅 {{ activePeriodLabel }}</span>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Main Content -->
    <v-card elevation="0" class="data-card" border rounded="xl">
      <v-card-text class="pa-4">
        <!-- Enterprise Toolbar: Search, Filters & Export -->
        <div class="d-flex flex-column flex-md-row justify-space-between align-start align-md-center gap-4 mb-4">
          <!-- Left: Search & Filters -->
          <div class="d-flex flex-wrap align-center gap-3 w-100 w-md-auto">
            <v-text-field
              v-model="search"
              placeholder="Cari Nasabah..."
              prepend-inner-icon="ri-search-line"
              variant="outlined"
              density="compact"
              hide-details
              rounded="lg"
              clearable
              style="min-width: 250px; max-width: 300px;"
              class="bg-white text-xs"
            />
            <v-select
              v-model="filterTahun"
              :items="yearOptions"
              label="Tahun"
              variant="outlined"
              density="compact"
              hide-details
              rounded="lg"
              class="text-xs bg-white"
              style="width: 120px;"
            />
            <v-select
              v-model="filterBulan"
              :items="monthOptions"
              item-title="title"
              item-value="value"
              label="Bulan"
              variant="outlined"
              density="compact"
              hide-details
              rounded="lg"
              class="text-xs bg-white"
              style="width: 155px;"
            />
            <v-select
              v-model="filterCabang"
              :items="cabangOptions"
              label="Cabang"
              variant="outlined"
              density="compact"
              hide-details
              rounded="lg"
              class="text-xs bg-white"
              style="width: 160px;"
            />
            <v-select
              v-model="filterKol"
              :items="kolOptions"
              label="Kolektibilitas"
              variant="outlined"
              density="compact"
              hide-details
              rounded="lg"
              class="text-xs bg-white"
              style="width: 160px;"
            />
          </div>

          <!-- Right: Export & Rows Info -->
          <div class="d-flex flex-wrap align-center gap-3 w-100 w-md-auto justify-end">
            <v-btn @click="exportExcel" prepend-icon="ri-file-excel-2-line" color="success" variant="tonal" class="text-none font-weight-bold" rounded="lg" height="40">Excel</v-btn>
            <v-btn @click="exportPDF" prepend-icon="ri-file-pdf-line" color="error" variant="tonal" class="text-none font-weight-bold" rounded="lg" height="40">PDF</v-btn>
            <v-divider vertical class="mx-1 h-50" />
            <div class="d-flex align-center gap-2">
              <span class="text-xs font-medium text-slate-500">Baris:</span>
              <v-select v-model="itemsPerPage" :items="rowsPerPageOptions" variant="outlined" density="compact" hide-details rounded="lg" style="width: 110px;" class="text-xs bg-white" />
            </div>
          </div>
        </div>

        <v-alert
          v-if="periodUnavailable && !loading"
          type="warning"
          variant="tonal"
          border="start"
          rounded="lg"
          class="mb-4"
        >
          <div class="font-weight-bold mb-1">Periode belum tersedia</div>
          <div class="text-body-2">
            {{ periodUnavailableMessage }}
            Silakan pilih bulan/tahun lain yang sudah tersedia di database.
          </div>
        </v-alert>

        <!-- Data Table -->
        <v-data-table-server
          v-if="!periodUnavailable"
          v-model:page="page"
          v-model:items-per-page="itemsPerPage"
          :headers="headers"
          :items="serverItems"
          :items-length="totalItems"
          :loading="loading"
          @update:options="loadItems"
          class="custom-table fin-vtable border rounded-lg"
          density="compact"
          hover
          fixed-header
        >
          <!-- Row Number -->
          <template #[`item.index`]="{ index }">
            <span class="text-xs text-slate-500 font-medium">
              {{ (page - 1) * itemsPerPage + index + 1 }}
            </span>
          </template>

          <!-- No. Rekening & Nama -->
          <template #[`item.nokontrak`]="{ item }">
            <span class="font-weight-bold text-primary text-xs">{{ item.nokontrak }}</span>
          </template>
          <template #[`item.nama`]="{ item }">
            <div class="font-weight-semibold text-slate-800 text-xs whitespace-normal break-words leading-tight" style="min-width: 150px;">
              {{ item.nama }}
            </div>
          </template>

          <!-- Nominals -->
          <template #[`item.mdlawal`]="{ item }">
            <span class="text-xs font-weight-medium text-slate-800">{{ formatCurrency(item.mdlawal) }}</span>
          </template>
          <template #[`item.osmdlc`]="{ item }">
            <span class="text-xs font-weight-bold text-slate-900">{{ formatCurrency(item.osmdlc) }}</span>
          </template>
          <template #[`item.tgkmdl`]="{ item }">
            <span class="text-xs text-error font-medium">{{ formatCurrency(item.tgkmdl) }}</span>
          </template>
          <template #[`item.tgkmgn`]="{ item }">
            <span class="text-xs text-warning-darken-2 font-medium">{{ formatCurrency(item.tgkmgn) }}</span>
          </template>
          <template #[`item.saldo_netto`]="{ item }">
            <span class="text-xs text-slate-700">{{ formatCurrency(item.saldo_netto) }}</span>
          </template>
          <template #[`item.tunggakan_vs_tabungan`]="{ item }">
            <span class="text-xs font-medium" :class="item.tunggakan_vs_tabungan < 0 ? 'text-error' : 'text-success'">
              {{ formatCurrency(item.tunggakan_vs_tabungan) }}
            </span>
          </template>
          <template #[`item.htgagun`]="{ item }">
            <span class="text-xs text-slate-700">{{ formatCurrency(item.htgagun) }}</span>
          </template>
          <template #[`item.ppap`]="{ item }">
            <span class="text-xs text-error-darken-1 font-medium">{{ formatCurrency(item.ppap) }}</span>
          </template>

          <!-- Ket Debet -->
          <template #[`item.keterangan_debet`]="{ item }">
            <span 
              class="inline-block px-2 py-0.5 text-[10px] font-bold rounded border uppercase"
              :style="getDebetStyle(item.keterangan_debet)"
            >
              {{ String(item.keterangan_debet || '-').trim() }}
            </span>
          </template>

          <!-- Kolektibilitas -->
          <template #[`item.colbaru`]="{ item }">
            <span 
              class="inline-block px-2 py-0.5 text-[10px] font-bold rounded border uppercase"
              :style="getKolStyle(item.colbaru)"
            >
              {{ getKolLabel(item.colbaru) }}
            </span>
          </template>

          <!-- Text Columns with N/A Handling -->
          <template #[`item.alamat`]="{ item }">
            <div class="text-xs text-slate-600 whitespace-normal break-words leading-tight" style="min-width: 200px;">
              {{ formatNA(item.alamat) }}
            </div>
          </template>
          <template #[`item.segmen`]="{ item }">
            <div class="text-xs text-slate-700 whitespace-normal break-words leading-tight" style="min-width: 120px;">
              {{ formatNA(item.segmen) }}
            </div>
          </template>
          <template #[`item.ao`]="{ item }">
            <div class="text-xs text-slate-700 font-medium">{{ formatNA(item.ao) }}</div>
          </template>
          <template #[`item.cabang`]="{ item }">
            <div class="text-xs text-slate-600">{{ formatNA(item.cabang) }}</div>
          </template>
          <template #[`item.wilayah`]="{ item }">
            <div class="text-xs text-slate-600">{{ formatNA(item.wilayah) }}</div>
          </template>

          <!-- Loading Slot -->
          <template #loading>
            <v-skeleton-loader type="table-row@10" />
          </template>

          <!-- Footer Pagination -->
          <template #bottom>
            <v-divider />
            <div class="d-flex align-center justify-space-between pa-2 bg-slate-50">
              <div class="text-xs font-medium text-slate-500 ms-2">
                Menampilkan {{ serverItems.length }} dari {{ totalItems.toLocaleString('id-ID') }} data
              </div>
              <v-pagination
                v-model:model-value="page"
                :length="Math.ceil(totalItems / itemsPerPage)"
                :total-visible="5"
                density="compact"
                variant="flat"
                active-color="primary"
                rounded="lg"
                class="my-0"
              />
            </div>
          </template>
        </v-data-table-server>
      </v-card-text>
    </v-card>
    <v-snackbar
      v-model="snackbar"
      :color="snackbarColor"
      location="top right"
      timeout="3600"
      rounded="lg"
      elevation="8"
    >
      {{ snackbarMessage }}
    </v-snackbar>

  </div>
</template>

<style scoped>
.data-card {
  border: 1px solid #E2E8F0;
  background: white;
}

.custom-table :deep(table) {
  border-separate: separate !important;
  border-spacing: 0 !important;
}

.custom-table :deep(th) {
  background-color: #f0fdf4 !important;
  color: #334155 !important;
  height: 36px !important;
  font-weight: 600 !important;
  text-transform: uppercase !important;
  font-size: 10px !important;
  letter-spacing: 0.5px !important;
  border-bottom: 1px solid #E2E8F0 !important;
  white-space: nowrap;
}

/* Sticky Column Core Classes with explicit styles from user instruction */
:deep(.sticky-column),
:deep(.sticky-column-header) {
  position: sticky !important;
  background-color: white !important;
}

.custom-table :deep(td) {
  height: 40px !important;
  padding: 6px 12px !important;
  border-bottom: 1px solid #F1F5F9 !important;
  white-space: nowrap;
}

.custom-table :deep(td:nth-child(3)) {
  white-space: normal !important;
  word-wrap: break-word !important;
}

.custom-table :deep(tr:hover) {
  background-color: #F8FAFC !important;
}

:deep(.v-pagination__list) {
  margin-bottom: 0;
}

:deep(.v-pagination__item) {
  font-size: 11px !important;
  min-width: 28px !important;
  height: 28px !important;
}

:deep(.v-pagination__item--is-active .v-btn) {
  color: white !important;
}
:deep(.v-pagination__item--is-active .v-btn__content) {
  color: white !important;
}
</style>
