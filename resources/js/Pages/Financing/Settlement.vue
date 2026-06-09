<script setup>
import { computed, onMounted, ref, watch } from 'vue'
import { Head } from '@inertiajs/vue3'
import DefaultLayout from '@/layouts/default.vue'
import axios from 'axios'
import VueApexCharts from 'vue3-apexcharts'
import '@/assets/css/financing-shared.css'
import { formatExactNumber, formatExactRupiah, formatTruncatedPercentage, toFiniteNumber } from '@/utils/money'

defineOptions({ layout: DefaultLayout })

const loading = ref(true)
const isExporting = ref(false)
const errorMessage = ref('')
const rawData = ref([])
const summary = ref({
  total_realisasi_count: 0,
  total_realisasi_volume: 0,
  total_realisasi_margin: 0,
  total_pelunasan_count: 0,
  total_pelunasan_volume: 0,
  total_pelunasan_margin: 0,
  net_cash_flow: 0,
  early_settlement_count: 0,
  early_settlement_volume: 0,
})
const periodMeta = ref({
  requested_period: null,
  active_period: null,
  is_historical: false,
  period_available: true,
  source_table: 'TOFLMB',
  source_database: null,
  message: null,
})

const activeTab = ref('all')
const searchQuery = ref('')
const selectedCabang = ref('Semua Cabang')
const selectedAo = ref('Semua AO')
const selectedProduk = ref('Semua Produk')
const selectedTahun = ref(null)
const selectedBulan = ref(null)
const currentPage = ref(1)
const itemsPerPage = ref(15)
let syncingPeriodFromApi = false

const monthOptions = [
  { title: 'Januari', value: 1 }, { title: 'Februari', value: 2 }, { title: 'Maret', value: 3 },
  { title: 'April', value: 4 }, { title: 'Mei', value: 5 }, { title: 'Juni', value: 6 },
  { title: 'Juli', value: 7 }, { title: 'Agustus', value: 8 }, { title: 'September', value: 9 },
  { title: 'Oktober', value: 10 }, { title: 'November', value: 11 }, { title: 'Desember', value: 12 },
]

const yearOptions = computed(() => {
  const current = new Date().getFullYear()
  return [current, current - 1, current - 2, current - 3, current - 4]
})

const activePeriodLabel = computed(() => {
  if (!selectedTahun.value || !selectedBulan.value) return 'Periode aktif CBS'
  const month = monthOptions.find(item => item.value === selectedBulan.value)?.title || '-'
  return `${month} ${selectedTahun.value}`
})
const periodUnavailable = computed(() => periodMeta.value?.period_available === false)
const sourceInfoLabel = computed(() => `${periodMeta.value?.source_database || '-'} | ${periodMeta.value?.source_table || 'TOFLMB'}`)

const cabangOptions = computed(() => ['Semua Cabang', ...uniqueOptions('nama_cabang')])
const aoOptions = computed(() => ['Semua AO', ...uniqueOptions('nmao')])
const produkOptions = computed(() => ['Semua Produk', ...uniqueOptions('nama_produk')])

const realisasiRows = computed(() => rawData.value.filter(item => item.stsrec === 'A'))
const pelunasanRows = computed(() => rawData.value.filter(item => item.stsrec === 'L'))
const earlySettlementRows = computed(() => pelunasanRows.value.filter(item => isEarlySettlement(item)))
const normalPelunasanRows = computed(() => pelunasanRows.value.filter(item => !isEarlySettlement(item)))

const filteredData = computed(() => {
  const query = searchQuery.value.toLowerCase()
  return rawData.value.filter(item => {
    const matchTab = activeTab.value === 'all'
      || (activeTab.value === 'realisasi' && item.stsrec === 'A')
      || (activeTab.value === 'pelunasan' && item.stsrec === 'L')
      || (activeTab.value === 'early' && item.stsrec === 'L' && isEarlySettlement(item))
    const matchCabang = selectedCabang.value === 'Semua Cabang' || item.nama_cabang === selectedCabang.value
    const matchAo = selectedAo.value === 'Semua AO' || item.nmao === selectedAo.value
    const matchProduk = selectedProduk.value === 'Semua Produk' || item.nama_produk === selectedProduk.value
    const matchSearch = String(item.nama || '').toLowerCase().includes(query)
      || String(item.nokontrak || '').includes(searchQuery.value)
      || String(item.noakad || '').toLowerCase().includes(query)
    return matchTab && matchCabang && matchAo && matchProduk && matchSearch
  })
})

const totalPages = computed(() => Math.max(1, Math.ceil(filteredData.value.length / itemsPerPage.value)))
const paginatedData = computed(() => {
  const start = (currentPage.value - 1) * itemsPerPage.value
  return filteredData.value.slice(start, start + itemsPerPage.value)
})

const filteredRealisasiVolume = computed(() => sumBy(filteredData.value.filter(item => item.stsrec === 'A'), item => item.mdlawal))
const filteredPelunasanVolume = computed(() => sumBy(filteredData.value.filter(item => item.stsrec === 'L'), item => item.mdleom))
const filteredNetCashFlow = computed(() => filteredRealisasiVolume.value - filteredPelunasanVolume.value)
const filteredMargin = computed(() => sumBy(filteredData.value, item => item.stsrec === 'A' ? item.mgnawal : item.mgneom))
const earlySettlementRatio = computed(() => pelunasanRows.value.length ? (earlySettlementRows.value.length / pelunasanRows.value.length) * 100 : 0)
const pelunasanRatio = computed(() => rawData.value.length ? (pelunasanRows.value.length / rawData.value.length) * 100 : 0)

const interpretation = computed(() => {
  if (errorMessage.value) return `Data belum dapat dimuat: ${errorMessage.value}`
  if (periodUnavailable.value) return 'Periode belum tersedia, sehingga tabel dikosongkan agar tidak membaca data dari database yang salah.'
  if (!rawData.value.length) return 'Tidak ada realisasi baru maupun pelunasan pada periode ini.'
  if (filteredNetCashFlow.value < 0) return `Pelunasan lebih besar dari realisasi pada filter ini. Net cash flow ${formatRp(filteredNetCashFlow.value)} perlu dibaca bersama pipeline pencairan baru dan strategi retensi nasabah lunas.`
  if (earlySettlementRows.value.length > 0) return `${formatNumber(earlySettlementRows.value.length)} pelunasan lebih awal terdeteksi. Validasi alasan pelunasan, potensi top-up/retention, dan dampaknya ke margin berjalan.`
  if (realisasiRows.value.length > pelunasanRows.value.length) return 'Aktivitas periode ini didominasi realisasi baru. Pastikan kualitas akad, agunan, dan kolektibilitas awal tetap terkendali.'
  return 'Realisasi dan pelunasan relatif seimbang. Fokus pada kualitas nasabah baru dan follow-up retention untuk nasabah yang lunas.'
})

const cabangRows = computed(() => groupRows(filteredData.value, 'nama_cabang').sort((a, b) => b.realisasi_volume + b.pelunasan_volume - (a.realisasi_volume + a.pelunasan_volume)))
const aoRows = computed(() => groupRows(filteredData.value, 'nmao').sort((a, b) => b.realisasi_volume + b.pelunasan_volume - (a.realisasi_volume + a.pelunasan_volume)))

const statusChart = computed(() => ({
  series: [realisasiRows.value.length, normalPelunasanRows.value.length, earlySettlementRows.value.length],
  options: {
    chart: { type: 'donut', background: 'transparent', fontFamily: "'Plus Jakarta Sans', sans-serif" },
    labels: ['Realisasi Baru', 'Pelunasan', 'Early Settlement'],
    colors: ['#10B981', '#F43F5E', '#7C3AED'],
    dataLabels: { enabled: false },
    legend: { show: false },
    plotOptions: { pie: { donut: { size: '76%' } } },
    stroke: { width: 0 },
    tooltip: { y: { formatter: value => `${formatNumber(value)} kontrak` } },
  },
}))

const cabangChart = computed(() => {
  const rows = cabangRows.value.slice(0, 8)
  return {
    series: [
      { name: 'Realisasi', data: rows.map(row => row.realisasi_volume) },
      { name: 'Pelunasan', data: rows.map(row => row.pelunasan_volume) },
    ],
    options: {
      chart: { type: 'bar', toolbar: { show: false }, fontFamily: 'Inter, sans-serif' },
      colors: ['#10B981', '#F43F5E'],
      plotOptions: { bar: { borderRadius: 8, horizontal: true } },
      dataLabels: { enabled: false },
      xaxis: { categories: rows.map(row => row.label), labels: { formatter: value => formatRp(value), style: { colors: '#64748b', fontSize: '10px' } } },
      yaxis: { labels: { style: { colors: '#0f172a', fontWeight: 800 } } },
      grid: { borderColor: '#e2e8f0', strokeDashArray: 4 },
      tooltip: { y: { formatter: value => formatRp(value) } },
    },
  }
})

function uniqueOptions(key) {
  return [...new Set(rawData.value.map(item => item[key]).filter(Boolean))].sort()
}

function sumBy(rows, selector) {
  return rows.reduce((sum, item) => sum + toFiniteNumber(selector(item)), 0)
}

function groupRows(rows, key) {
  const groups = new Map()
  rows.forEach(item => {
    const label = item[key] || 'Tidak Terdefinisi'
    const current = groups.get(label) || {
      label,
      transaksi: 0,
      realisasi_count: 0,
      pelunasan_count: 0,
      early_count: 0,
      realisasi_volume: 0,
      pelunasan_volume: 0,
      margin: 0,
    }
    current.transaksi += 1
    if (item.stsrec === 'A') {
      current.realisasi_count += 1
      current.realisasi_volume += toFiniteNumber(item.mdlawal)
      current.margin += toFiniteNumber(item.mgnawal)
    }
    if (item.stsrec === 'L') {
      current.pelunasan_count += 1
      current.pelunasan_volume += toFiniteNumber(item.mdleom)
      current.margin += toFiniteNumber(item.mgneom)
      if (isEarlySettlement(item)) current.early_count += 1
    }
    groups.set(label, current)
  })
  return [...groups.values()]
}

function parseDate(value) {
  if (!value || value === '-') return null
  const normalized = String(value).trim()
  if (/^\d{4}-\d{2}-\d{2}$/.test(normalized)) return new Date(`${normalized}T00:00:00`)
  if (/^\d{2}-\d{2}-\d{4}$/.test(normalized)) {
    const [day, month, year] = normalized.split('-')
    return new Date(`${year}-${month}-${day}T00:00:00`)
  }
  return null
}

function daysBetween(start, end) {
  const startDate = parseDate(start)
  const endDate = parseDate(end)
  if (!startDate || !endDate) return 0
  return Math.trunc((endDate - startDate) / (1000 * 60 * 60 * 24))
}

function isEarlySettlement(item) {
  return daysBetween(item.tgllunas, item.tglexp) > 30
}

function formatRp(value) {
  return formatExactRupiah(value)
}

function formatNumber(value) {
  return formatExactNumber(value)
}

function statusLabel(item) {
  if (item.stsrec === 'A') return 'Realisasi Baru'
  return isEarlySettlement(item) ? 'Early Settlement' : 'Pelunasan Normal'
}

function statusColor(item) {
  if (item.stsrec === 'A') return 'success'
  return isEarlySettlement(item) ? 'deep-purple' : 'error'
}

const fetchData = async () => {
  loading.value = true
  errorMessage.value = ''
  try {
    const params = {}
    if (selectedTahun.value) params.tahun = selectedTahun.value
    if (selectedBulan.value) params.bulan = selectedBulan.value
    const response = await axios.get('/api/v1/financing/penyelesaian/settlement', { params })
    if (response.data.success) {
      periodMeta.value = response.data.period_meta || periodMeta.value
      const requested = String(periodMeta.value?.requested_period || '')
      if (requested.length === 6) {
        syncingPeriodFromApi = true
        selectedTahun.value = Number(requested.slice(0, 4))
        selectedBulan.value = Number(requested.slice(4, 6))
        setTimeout(() => { syncingPeriodFromApi = false }, 0)
      }
      rawData.value = response.data.data || []
      summary.value = { ...summary.value, ...(response.data.summary || {}) }
    } else {
      throw new Error(response.data.error || 'Gagal memuat data Settlement')
    }
  } catch (error) {
    errorMessage.value = error?.response?.data?.error || error.message || 'Gagal memuat data Settlement'
    rawData.value = []
  } finally {
    loading.value = false
  }
}

function buildDetailRows(rows = filteredData.value) {
  return rows.map(item => ({
    Status: statusLabel(item),
    'No Kontrak': item.nokontrak || '-',
    'No Akad': item.noakad || '-',
    Nasabah: item.nama || '-',
    Produk: item.nama_produk || '-',
    AO: item.nmao || '-',
    Cabang: item.nama_cabang || '-',
    Wilayah: item.nama_wilayah || '-',
    Segmen: item.nama_segmen || '-',
    'Tanggal Book': item.tglbook || '-',
    'Tanggal Efektif': item.tgleff || '-',
    'Tanggal Jatuh Tempo': item.tglexp || '-',
    'Tanggal Lunas': item.tgllunas || '-',
    'Sisa Hari ke Jatuh Tempo': item.stsrec === 'L' ? daysBetween(item.tgllunas, item.tglexp) : 0,
    'Pokok Realisasi': toFiniteNumber(item.mdlawal),
    'Margin Realisasi': toFiniteNumber(item.mgnawal),
    'Pokok Pelunasan': toFiniteNumber(item.mdleom),
    'Margin Pelunasan': toFiniteNumber(item.mgneom),
  }))
}

function buildSummaryRows() {
  return [
    { Metrik: 'Periode', Nilai: activePeriodLabel.value },
    { Metrik: 'Sumber Data', Nilai: sourceInfoLabel.value },
    { Metrik: 'Total Data Filter', Nilai: filteredData.value.length },
    { Metrik: 'Jumlah Realisasi', Nilai: filteredData.value.filter(item => item.stsrec === 'A').length },
    { Metrik: 'Volume Realisasi', Nilai: filteredRealisasiVolume.value },
    { Metrik: 'Jumlah Pelunasan', Nilai: filteredData.value.filter(item => item.stsrec === 'L').length },
    { Metrik: 'Volume Pelunasan', Nilai: filteredPelunasanVolume.value },
    { Metrik: 'Net Cash Flow', Nilai: filteredNetCashFlow.value },
    { Metrik: 'Early Settlement', Nilai: earlySettlementRows.value.length },
    { Metrik: 'Rasio Pelunasan', Nilai: formatTruncatedPercentage(pelunasanRatio.value) },
    { Metrik: 'Rasio Early Settlement', Nilai: formatTruncatedPercentage(earlySettlementRatio.value) },
    { Metrik: 'Interpretasi', Nilai: interpretation.value },
  ]
}

const exportExcel = async () => {
  if (isExporting.value) return
  isExporting.value = true
  try {
    const XLSX = await import('xlsx')
    const workbook = XLSX.utils.book_new()
    XLSX.utils.book_append_sheet(workbook, XLSX.utils.json_to_sheet(buildSummaryRows()), '00 Summary')
    XLSX.utils.book_append_sheet(workbook, XLSX.utils.json_to_sheet(buildDetailRows()), '01 Detail Settlement')
    XLSX.utils.book_append_sheet(workbook, XLSX.utils.json_to_sheet(buildDetailRows(filteredData.value.filter(item => item.stsrec === 'A'))), '02 Realisasi')
    XLSX.utils.book_append_sheet(workbook, XLSX.utils.json_to_sheet(buildDetailRows(filteredData.value.filter(item => item.stsrec === 'L'))), '03 Pelunasan')
    XLSX.utils.book_append_sheet(workbook, XLSX.utils.json_to_sheet(cabangRows.value), '04 Rekap Cabang')
    XLSX.utils.book_append_sheet(workbook, XLSX.utils.json_to_sheet(aoRows.value), '05 Rekap AO')
    XLSX.writeFile(workbook, `settlement-pembiayaan-${activePeriodLabel.value.replace(/\s+/g, '-')}.xlsx`)
  } finally {
    isExporting.value = false
  }
}

const exportPdf = async () => {
  if (isExporting.value) return
  isExporting.value = true
  try {
    const { default: jsPDF } = await import('jspdf')
    await import('jspdf-autotable')
    const doc = new jsPDF({ orientation: 'landscape', unit: 'pt', format: 'a4' })
    doc.setFont('helvetica', 'bold')
    doc.setFontSize(15)
    doc.text('Settlement Pembiayaan', 40, 38)
    doc.setFont('helvetica', 'normal')
    doc.setFontSize(9)
    doc.text(`${activePeriodLabel.value} | ${sourceInfoLabel.value}`, 40, 56)
    doc.text(`Realisasi ${formatRp(filteredRealisasiVolume.value)} | Pelunasan ${formatRp(filteredPelunasanVolume.value)} | Net ${formatRp(filteredNetCashFlow.value)}`, 40, 70)
    doc.autoTable({
      startY: 94,
      head: [['Status', 'Nasabah', 'Kontrak', 'Produk', 'AO', 'Cabang', 'Tanggal', 'Pokok', 'Margin']],
      body: buildDetailRows().map(row => [
        row.Status,
        row.Nasabah,
        row['No Kontrak'],
        row.Produk,
        row.AO,
        row.Cabang,
        row.Status === 'Realisasi Baru' ? row['Tanggal Efektif'] : row['Tanggal Lunas'],
        formatRp(row.Status === 'Realisasi Baru' ? row['Pokok Realisasi'] : row['Pokok Pelunasan']),
        formatRp(row.Status === 'Realisasi Baru' ? row['Margin Realisasi'] : row['Margin Pelunasan']),
      ]),
      styles: { fontSize: 7, cellPadding: 4, overflow: 'linebreak' },
      headStyles: { fillColor: [30, 64, 175], textColor: 255, fontStyle: 'bold' },
      columnStyles: { 7: { halign: 'right' }, 8: { halign: 'right' } },
    })
    doc.save(`settlement-pembiayaan-${activePeriodLabel.value.replace(/\s+/g, '-')}.pdf`)
  } finally {
    isExporting.value = false
  }
}

const resetPage = () => { currentPage.value = 1 }

onMounted(fetchData)
watch([activeTab, selectedCabang, selectedAo, selectedProduk, searchQuery], resetPage)
watch([selectedTahun, selectedBulan], () => {
  if (syncingPeriodFromApi) return
  resetPage()
  fetchData()
})
</script>

<template>
  <div class="fin-page px-4 pt-0">
    <Head title="Settlement Pembiayaan" />

    <div class="fin-hero mb-6">
      <div class="fin-hero__deco"></div>
      <div class="fin-hero__inner">
        <div class="d-flex flex-column flex-xl-row justify-space-between align-start align-xl-center gap-4">
          <div class="d-flex align-center gap-4">
            <div class="fin-hero__icon fin-icon-blue">
              <v-icon icon="ri-exchange-funds-line" size="26" color="white" />
            </div>
            <div class="fin-hero__meta">
              <h1 class="fin-hero__title">Settlement Pembiayaan</h1>
              <p class="fin-hero__subtitle">Monitoring realisasi baru, pelunasan, early settlement, net cash flow, dan peluang retensi nasabah lunas.</p>
              <div class="fin-hero__badges">
                <span class="fin-badge fin-badge--warning">Cash Flow</span>
                <span class="fin-badge fin-badge--slate">{{ activePeriodLabel }}</span>
              </div>
            </div>
          </div>

          <div class="settlement-toolbar">
            <div class="fin-filter-bar">
              <v-select v-model="selectedTahun" :items="yearOptions" label="Tahun" variant="solo" density="compact" flat hide-details rounded="lg" bg-color="white" prepend-inner-icon="ri-calendar-line" style="min-width: 120px; max-width: 140px;" />
              <v-select v-model="selectedBulan" :items="monthOptions" item-title="title" item-value="value" label="Bulan" variant="solo" density="compact" flat hide-details rounded="lg" bg-color="white" prepend-inner-icon="ri-calendar-event-line" style="min-width: 150px; max-width: 180px;" />
              <v-btn variant="text" density="comfortable" @click="fetchData" :loading="loading" icon="ri-refresh-line" color="white" />
            </div>
            <div class="settlement-export-actions">
              <v-btn size="small" rounded="lg" color="success" variant="flat" :loading="isExporting" prepend-icon="ri-file-excel-2-line" @click="exportExcel">Excel</v-btn>
              <v-btn size="small" rounded="lg" color="error" variant="flat" :loading="isExporting" prepend-icon="ri-file-pdf-2-line" @click="exportPdf">PDF</v-btn>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="settlement-insight-panel mb-6">
      <div class="settlement-insight-card settlement-insight-card--primary">
        <span>Interpretasi Operasional</span>
        <strong>{{ interpretation }}</strong>
        <small>{{ sourceInfoLabel }}</small>
      </div>
      <div class="settlement-insight-card">
        <span>Realisasi Baru</span>
        <strong>{{ formatRp(filteredRealisasiVolume) }}</strong>
        <small>{{ formatNumber(filteredData.filter(item => item.stsrec === 'A').length) }} kontrak dalam filter</small>
      </div>
      <div class="settlement-insight-card">
        <span>Pelunasan</span>
        <strong>{{ formatRp(filteredPelunasanVolume) }}</strong>
        <small>{{ formatNumber(filteredData.filter(item => item.stsrec === 'L').length) }} kontrak | Early {{ formatNumber(earlySettlementRows.length) }}</small>
      </div>
      <div class="settlement-insight-card">
        <span>Net Cash Flow</span>
        <strong :class="filteredNetCashFlow < 0 ? 'text-error' : 'text-success'">{{ formatRp(filteredNetCashFlow) }}</strong>
        <small>Realisasi dikurangi pelunasan pokok</small>
      </div>
    </div>

    <v-alert v-if="periodUnavailable && !loading" type="warning" variant="tonal" border="start" rounded="lg" class="mb-6">
      <div class="font-weight-bold mb-1">Periode {{ activePeriodLabel }} belum tersedia</div>
      <div class="text-body-2">{{ periodMeta.message || 'Database snapshot periode ini belum tersedia.' }}</div>
    </v-alert>

    <v-row class="mb-6">
      <v-col cols="12" sm="6" lg="3"><v-card class="settlement-score-card" elevation="0"><v-icon icon="ri-download-cloud-2-line" size="34" color="#10b981" /><div><p>Total Realisasi</p><h2>{{ formatNumber(realisasiRows.length) }}</h2><small>Margin {{ formatRp(summary.total_realisasi_margin) }}</small></div></v-card></v-col>
      <v-col cols="12" sm="6" lg="3"><v-card class="settlement-score-card" elevation="0"><v-icon icon="ri-upload-cloud-2-line" size="34" color="#f43f5e" /><div><p>Total Pelunasan</p><h2>{{ formatNumber(pelunasanRows.length) }}</h2><small>{{ formatTruncatedPercentage(pelunasanRatio) }} dari transaksi</small></div></v-card></v-col>
      <v-col cols="12" sm="6" lg="3"><v-card class="settlement-score-card" elevation="0"><v-icon icon="ri-time-line" size="34" color="#7c3aed" /><div><p>Early Settlement</p><h2>{{ formatNumber(earlySettlementRows.length) }}</h2><small>{{ formatTruncatedPercentage(earlySettlementRatio) }} dari pelunasan</small></div></v-card></v-col>
      <v-col cols="12" sm="6" lg="3"><v-card class="settlement-score-card" elevation="0"><v-icon icon="ri-hand-coin-line" size="34" color="#2563eb" /><div><p>Total Margin</p><h2>{{ formatRp(filteredMargin) }}</h2><small>Margin realisasi + pelunasan</small></div></v-card></v-col>
    </v-row>

    <v-card class="settlement-filter-card mb-6" elevation="0">
      <v-text-field v-model="searchQuery" prepend-inner-icon="ri-search-2-line" placeholder="Cari nasabah / kontrak / akad..." variant="outlined" density="compact" hide-details rounded="lg" />
      <v-select v-model="selectedCabang" :items="cabangOptions" label="Cabang" prepend-inner-icon="ri-store-2-line" variant="outlined" density="compact" hide-details rounded="lg" />
      <v-select v-model="selectedAo" :items="aoOptions" label="AO" prepend-inner-icon="ri-user-star-line" variant="outlined" density="compact" hide-details rounded="lg" />
      <v-select v-model="selectedProduk" :items="produkOptions" label="Produk" prepend-inner-icon="ri-bank-card-line" variant="outlined" density="compact" hide-details rounded="lg" />
    </v-card>

    <v-card class="settlement-tab-card mb-6" elevation="0">
      <v-tabs v-model="activeTab" color="primary" slider-color="primary" class="settlement-tabs">
        <v-tab value="all">Semua ({{ formatNumber(rawData.length) }})</v-tab>
        <v-tab value="realisasi">Realisasi ({{ formatNumber(realisasiRows.length) }})</v-tab>
        <v-tab value="pelunasan">Pelunasan ({{ formatNumber(pelunasanRows.length) }})</v-tab>
        <v-tab value="early">Early Settlement ({{ formatNumber(earlySettlementRows.length) }})</v-tab>
      </v-tabs>
    </v-card>

    <v-row class="mb-6">
      <v-col cols="12" lg="4">
        <div class="content-card">
          <div class="content-card__header">
            <div><div class="content-card__title">Komposisi Settlement</div><div class="content-card__subtitle">Realisasi, pelunasan, dan early settlement.</div></div>
          </div>
          <div class="content-card__body d-flex align-center justify-center gap-6">
            <VueApexCharts v-if="!loading" type="donut" width="190" height="190" :options="statusChart.options" :series="statusChart.series" />
            <div class="settlement-chart-legend">
              <span><i style="background:#10B981"></i>Realisasi {{ formatNumber(realisasiRows.length) }}</span>
              <span><i style="background:#F43F5E"></i>Pelunasan {{ formatNumber(normalPelunasanRows.length) }}</span>
              <span><i style="background:#7C3AED"></i>Early {{ formatNumber(earlySettlementRows.length) }}</span>
            </div>
          </div>
        </div>
      </v-col>
      <v-col cols="12" lg="8">
        <div class="content-card">
          <div class="content-card__header">
            <div><div class="content-card__title">Top Cabang by Volume</div><div class="content-card__subtitle">Perbandingan volume realisasi dan pelunasan per cabang.</div></div>
          </div>
          <div class="content-card__body">
            <VueApexCharts v-if="!loading" type="bar" height="280" :options="cabangChart.options" :series="cabangChart.series" />
          </div>
        </div>
      </v-col>
    </v-row>

    <div class="content-card mb-6">
      <div class="content-card__header">
        <div><div class="content-card__title">Prioritas Cabang dan AO</div><div class="content-card__subtitle">Area yang paling besar memengaruhi cash flow pembiayaan periode ini.</div></div>
      </div>
      <div class="content-card__body pa-0">
        <div v-for="row in cabangRows.slice(0, 8)" :key="row.label" class="settlement-priority-row">
          <div><strong>{{ row.label }}</strong><small>{{ row.realisasi_count }} realisasi | {{ row.pelunasan_count }} pelunasan | {{ row.early_count }} early settlement</small></div>
          <span>{{ formatRp(row.realisasi_volume - row.pelunasan_volume) }}</span>
        </div>
        <div v-if="!cabangRows.length" class="pa-8 text-center text-disabled">Tidak ada prioritas cabang pada filter ini.</div>
      </div>
    </div>

    <div class="content-card">
      <div class="content-card__header">
        <div><div class="content-card__title">Detail Settlement Pembiayaan</div><div class="content-card__subtitle">Detail nasabah, tanggal, produk, AO, cabang, pokok, dan margin sesuai filter periode.</div></div>
        <v-chip size="x-small" color="primary" variant="flat" class="font-weight-black">{{ formatNumber(filteredData.length) }} data</v-chip>
      </div>
      <div class="content-card__body pa-0">
        <div class="overflow-x-auto">
          <table class="fin-table fin-vtable settlement-table">
            <thead>
              <tr>
                <th>Nasabah / Kontrak</th>
                <th>Status</th>
                <th>Timeline</th>
                <th>AO / Cabang</th>
                <th class="text-right">Pokok</th>
                <th class="text-right">Margin</th>
              </tr>
            </thead>
            <tbody>
              <tr v-if="loading">
                <td colspan="6" class="pa-12 text-center">
                  <v-progress-circular indeterminate color="primary" size="46" />
                  <div class="text-h6 text-medium-emphasis font-weight-bold mt-4">Memuat Data Settlement...</div>
                </td>
              </tr>
              <tr v-else-if="paginatedData.length === 0">
                <td colspan="6" class="pa-12 text-center">
                  <v-icon icon="ri-inbox-line" size="64" class="text-disabled mb-4" />
                  <div class="text-h6 text-medium-emphasis font-weight-bold">Data Tidak Ditemukan</div>
                  <div class="text-caption text-disabled mt-1">Coba sesuaikan periode atau filter pencarian.</div>
                </td>
              </tr>
              <tr v-for="item in paginatedData" :key="`${item.nokontrak}-${item.stsrec}`">
                <td>
                  <div class="font-weight-black text-uppercase">{{ item.nama }}</div>
                  <div class="settlement-contract-flow"><span>{{ item.nokontrak }}</span><span>{{ item.noakad || '-' }}</span></div>
                  <div class="text-caption text-medium-emphasis">{{ item.nama_produk }} | {{ item.nama_segmen }}</div>
                </td>
                <td>
                  <v-chip size="small" :color="statusColor(item)" variant="flat" class="font-weight-black text-white">{{ statusLabel(item) }}</v-chip>
                  <div v-if="item.stsrec === 'L'" class="text-caption text-medium-emphasis mt-1">Sisa {{ formatNumber(daysBetween(item.tgllunas, item.tglexp)) }} hari</div>
                </td>
                <td>
                  <div class="settlement-date-grid">
                    <span>Book <strong>{{ item.tglbook }}</strong></span>
                    <span>Eff <strong>{{ item.tgleff }}</strong></span>
                    <span>Exp <strong>{{ item.tglexp }}</strong></span>
                    <span v-if="item.stsrec === 'L'">Lunas <strong>{{ item.tgllunas }}</strong></span>
                  </div>
                </td>
                <td>
                  <div class="font-weight-black text-caption text-uppercase">{{ item.nmao || 'TANPA AO' }}</div>
                  <div class="text-caption text-medium-emphasis">{{ item.nama_cabang }} | {{ item.nama_wilayah }}</div>
                </td>
                <td class="text-right">
                  <div class="settlement-money">{{ formatRp(item.stsrec === 'A' ? item.mdlawal : item.mdleom) }}</div>
                  <div class="text-caption text-medium-emphasis">{{ item.stsrec === 'A' ? 'Pokok Realisasi' : 'Pokok Pelunasan' }}</div>
                </td>
                <td class="text-right">
                  <div class="settlement-money settlement-money--muted">{{ formatRp(item.stsrec === 'A' ? item.mgnawal : item.mgneom) }}</div>
                  <div class="text-caption text-medium-emphasis">{{ item.stsrec === 'A' ? 'Margin Awal' : 'Margin EOM' }}</div>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
        <v-divider v-if="filteredData.length > 0" />
        <div v-if="filteredData.length > 0" class="pa-4 d-flex align-center justify-space-between bg-slate-50">
          <div class="text-caption text-medium-emphasis font-weight-bold">Menampilkan {{ (currentPage - 1) * itemsPerPage + 1 }} - {{ Math.min(currentPage * itemsPerPage, filteredData.length) }} dari {{ filteredData.length }} data</div>
          <v-pagination v-model="currentPage" :length="totalPages" :total-visible="5" density="compact" variant="flat" active-color="primary" />
        </div>
      </div>
    </div>
  </div>
</template>

<style scoped>
.settlement-toolbar,.settlement-export-actions{display:flex;align-items:center;gap:12px;flex-wrap:wrap;justify-content:flex-end}.settlement-export-actions{gap:8px}.fin-badge--slate{background:rgba(255,255,255,.12);color:#e2e8f0;border:1px solid rgba(255,255,255,.18)}
.settlement-insight-panel{display:grid;grid-template-columns:minmax(0,1.45fr) repeat(3,minmax(210px,.75fr));gap:16px}.settlement-insight-card,.settlement-score-card,.settlement-filter-card,.settlement-tab-card{background:linear-gradient(145deg,#fff 0%,#f8fafc 100%);border:1px solid #dbe7f3;border-radius:20px;box-shadow:0 10px 28px rgba(15,23,42,.06)}.settlement-insight-card{min-height:124px;padding:18px 20px;display:flex;flex-direction:column;justify-content:center;gap:7px}.settlement-insight-card span,.settlement-score-card p{color:#64748b;font-size:11px;font-weight:900;text-transform:uppercase;letter-spacing:.06em;margin:0}.settlement-insight-card strong{color:#0f172a;font-family:'Plus Jakarta Sans',sans-serif;font-size:clamp(.98rem,1vw,1.16rem);line-height:1.45;letter-spacing:-.02em}.settlement-insight-card small,.settlement-score-card small{color:#64748b;font-size:12px;font-weight:700}.settlement-insight-card--primary{background:radial-gradient(circle at top right,rgba(37,99,235,.16),transparent 34%),linear-gradient(145deg,#eff6ff 0%,#fff 74%);border-color:#bfdbfe}
.settlement-score-card{min-height:132px;padding:20px;display:flex;align-items:center;gap:16px;height:100%}.settlement-score-card h2{color:#0f172a;font-family:'Plus Jakarta Sans',sans-serif;font-size:clamp(1rem,1.55vw,1.75rem);font-weight:900;line-height:1.1;margin:4px 0;max-width:280px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis}.settlement-filter-card{padding:16px;display:grid;grid-template-columns:minmax(240px,1.2fr) repeat(3,minmax(160px,.75fr));gap:12px}.settlement-tab-card{overflow:hidden}.settlement-tabs :deep(.v-tab){font-size:12px;font-weight:900;text-transform:uppercase;letter-spacing:.04em}.settlement-chart-legend{display:flex;flex-direction:column;gap:10px}.settlement-chart-legend span{font-size:12px;font-weight:800;color:#334155}.settlement-chart-legend i{display:inline-block;width:10px;height:10px;border-radius:999px;margin-right:8px}.settlement-priority-row{display:flex;justify-content:space-between;gap:14px;padding:14px 18px;border-bottom:1px solid #e2e8f0}.settlement-priority-row strong{color:#0f172a;font-size:13px;font-weight:900}.settlement-priority-row small{display:block;color:#64748b;font-size:11px;font-weight:700;margin-top:3px}.settlement-priority-row span,.settlement-money{color:#0f172a;font-family:'Plus Jakarta Sans',sans-serif;font-size:12px;font-weight:900;white-space:nowrap}.settlement-money--muted{color:#475569}.settlement-contract-flow{display:flex;align-items:center;gap:8px;color:#64748b;font-family:monospace;font-size:11px;font-weight:800;margin:4px 0}.settlement-contract-flow span{background:#f1f5f9;border:1px solid #e2e8f0;border-radius:8px;padding:2px 6px}.settlement-date-grid{display:grid;grid-template-columns:repeat(2,minmax(92px,1fr));gap:6px 10px}.settlement-date-grid span{color:#64748b;font-size:10px;font-weight:900;text-transform:uppercase}.settlement-date-grid strong{display:block;color:#0f172a;font-size:11px;text-transform:none}.settlement-table :deep(th){height:52px!important;letter-spacing:.5px!important}.settlement-table :deep(td){height:78px!important;vertical-align:middle}
@media(max-width:1180px){.settlement-insight-panel{grid-template-columns:1fr 1fr}.settlement-filter-card{grid-template-columns:1fr 1fr}}@media(max-width:720px){.settlement-toolbar,.settlement-export-actions{width:100%}.settlement-export-actions .v-btn{flex:1}.settlement-insight-panel,.settlement-filter-card{grid-template-columns:1fr}.settlement-date-grid{grid-template-columns:1fr}}
</style>
