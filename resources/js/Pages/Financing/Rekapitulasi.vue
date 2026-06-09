<script setup>
import { ref, computed, onMounted, watch } from 'vue'
import { Head } from '@inertiajs/vue3'
import DefaultLayout from '@/layouts/default.vue'
import axios from 'axios'
import VueApexCharts from 'vue3-apexcharts'
import '@/assets/css/financing-shared.css'
import { formatExactNumber, formatExactRupiah, formatTruncatedPercentage } from '@/utils/money'

defineOptions({ layout: DefaultLayout })

// ─── State Management ────────────────────────────────────────
const isLoading = ref(true)
const selectedDimension = ref('cabang')
const selectedMetric = ref('os')
const selectedCabang = ref('')
const selectedTahun = ref(null)
const selectedBulan = ref(null)
const viewMode = ref('grid')
const hideEmptyRows = ref(true)
const isExporting = ref(false)

const rekapData = ref({
  rows: [],
  totals: {
    noa: 0,
    total_os: 0,
    npf_ratio: 0,
    total_ppap: 0,
    npf_os: 0
  },
  meta: {}
})

const cabangs = ref([])
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

const dimensionOptions = [
  { label: 'Per Cabang', value: 'cabang' },
  { label: 'Per Wilayah', value: 'wilayah' },
  { label: 'Per Account Officer', value: 'ao' },
  { label: 'Per Produk/Akad', value: 'produk' },
  { label: 'Per Segmen', value: 'segmen' },
  { label: 'Per Sektor Ekonomi', value: 'sekon' },
]

// ─── Computed Properties ─────────────────────────────────────
const filteredRows = computed(() => {
  if (!hideEmptyRows.value) return rekapData.value.rows
  return rekapData.value.rows.filter(r => r.noa > 0 && r.total_os > 0)
})

const totals = computed(() => rekapData.value.totals)
const dimensionLabel = computed(() => dimensionOptions.find(d => d.value === selectedDimension.value)?.label || 'Dimensi')
const selectedCabangLabel = computed(() => cabangs.value.find(c => c.kdloc === selectedCabang.value)?.nama || 'Konsolidasi Seluruh Cabang')
const periodUnavailable = computed(() => rekapData.value.meta?.period_available === false)
const activePeriodLabel = computed(() => {
  if (!selectedTahun.value || !selectedBulan.value) return 'Periode aktif CBS'
  const month = monthOptions.find(item => item.value === selectedBulan.value)?.title || '-'
  return `${month} ${selectedTahun.value}`
})
const sourceInfoLabel = computed(() => {
  const database = rekapData.value.meta?.source_database || '-'
  const table = rekapData.value.meta?.source_table || '-'
  return `${database} · ${table}`
})
const sortedRowsByOs = computed(() => [...filteredRows.value].sort((a, b) => Number(b.total_os || 0) - Number(a.total_os || 0)))
const sortedRowsByNpf = computed(() => [...filteredRows.value].sort((a, b) => Number(b.npf_os || 0) - Number(a.npf_os || 0)))
const topContributor = computed(() => sortedRowsByOs.value[0] || null)
const topNpfContributor = computed(() => sortedRowsByNpf.value.find(row => Number(row.npf_os || 0) > 0) || null)
const concentrationRatio = computed(() => totals.value.total_os > 0 && topContributor.value ? (Number(topContributor.value.total_os || 0) / Number(totals.value.total_os || 0)) * 100 : 0)
const npfContributionRatio = computed(() => totals.value.npf_os > 0 && topNpfContributor.value ? (Number(topNpfContributor.value.npf_os || 0) / Number(totals.value.npf_os || 0)) * 100 : 0)
const operationalInsight = computed(() => {
  if (periodUnavailable.value) return 'Periode belum tersedia, sehingga dashboard tidak menampilkan tabel agar user tidak membaca data yang salah.'
  if (!filteredRows.value.length) return 'Tidak ada portofolio pada kombinasi filter ini.'
  if ((totals.value.npf_ratio || 0) > 5) return 'NPF konsolidasi melewati ambang pengawasan; gunakan Quality & Risk untuk drill-down penyebab dan action owner.'
  if (concentrationRatio.value >= 35) return 'Portofolio terkonsentrasi pada satu dimensi dominan; pantau limit konsentrasi dan diversifikasi bisnis.'
  return 'Portofolio dapat dipakai sebagai master rekap volume; analisis kualitas detail tetap ditempatkan di Quality & Risk.'
})

// Helper: Format Rupiah
const formatRp = (value) => {
  return formatExactRupiah(value)
}

const formatNumber = (value) => {
  return formatExactNumber(value)
}

// Format metric for table
const formatMetric = (valOS, valNOA) => {
  if (selectedMetric.value === 'os') {
    return formatRp(valOS)
  }
  return formatNumber(valNOA)
}

// NPF Heatmap Logic
const getNpfCellStyle = (npfRatio) => {
  if (npfRatio > 5) return { background: '#fee2e2', color: '#dc2626', fontWeight: '700' }
  if (npfRatio > 2) return { background: '#fef9c3', color: '#ca8a04', fontWeight: '600' }
  if (npfRatio > 0) return { background: '#dcfce7', color: '#16a34a', fontWeight: '600' }
  return { background: '#f8fafc', color: '#94a3b8' }
}

// ─── Chart Options ───────────────────────────────────────────
const treeMapSeries = computed(() => [{
  data: filteredRows.value.map(r => ({
    x: r.label || '(Kosong)',
    y: Number(r.total_os || 0)
  }))
}])

const donutSeries = computed(() => [
  totals.value.total_os - (rekapData.value.totals.npf_os || 0), // Lancar
  rekapData.value.totals.npf_os || 0 // NPF
])

const treeMapOpts = computed(() => ({
  chart: { type: 'treemap', fontFamily: "'Plus Jakarta Sans', sans-serif", toolbar: { show: false } },
  title: { text: 'Sebaran Outstanding', style: { fontWeight: 600 } },
  dataLabels: {
    enabled: true,
    formatter: (text, op) => {
        return text + ": " + formatRp(op.value)
    }
  },
  plotOptions: {
    treemap: {
      enableShades: true,
      shadeIntensity: 0.5,
      reverseNegativeShade: true,
      colorScale: {
        ranges: [
          { from: 0, to: 10, color: '#94a3b8' },
          { from: 10, to: 100, color: '#3b82f6' },
          { from: 100, to: 100000, color: '#1e40af' }
        ]
      }
    }
  },
  dataLabels: {
    enabled: true,
    dropShadow: { enabled: false },
    style: { fontSize: '11px', fontWeight: 600 },
    formatter: function (text, op) {
      if (op.value <= 0) return ''
      return [text, formatRp(op.value)]
    }
  }
}))

const donutOpts = computed(() => ({
  chart: { type: 'donut', fontFamily: "'Plus Jakarta Sans', sans-serif" },
  labels: ['Lancar (Kol 1-2)', 'NPF (Kol 3-5)'],
  colors: ['#10b981', '#ef4444'],
  title: { text: 'Rasio NPF vs Lancar', style: { fontWeight: 600 } },
  plotOptions: {
    pie: { donut: { size: '70%' } }
  },
  dataLabels: {
    enabled: true,
    formatter: function (val) {
      return formatTruncatedPercentage(val)
    }
  },
  legend: {
    position: 'bottom'
  }
}))

// ─── API Calls ───────────────────────────────────────────────
const buildExportRows = () => filteredRows.value.map(row => ({
  Dimensi: row.label || '-',
  Kode: row.id || '-',
  NOA: Number(row.noa || 0),
  'Total OS': Number(row.total_os || 0),
  'Kol 1 OS': Number(row.kol1_os || 0),
  'Kol 1 NOA': Number(row.kol1_noa || 0),
  'Kol 2 OS': Number(row.kol2_os || 0),
  'Kol 2 NOA': Number(row.kol2_noa || 0),
  'Kol 3 OS': Number(row.kol3_os || 0),
  'Kol 3 NOA': Number(row.kol3_noa || 0),
  'Kol 4 OS': Number(row.kol4_os || 0),
  'Kol 4 NOA': Number(row.kol4_noa || 0),
  'Kol 5 OS': Number(row.kol5_os || 0),
  'Kol 5 NOA': Number(row.kol5_noa || 0),
  'NPF OS': Number(row.npf_os || 0),
  'NPF NOA': Number(row.npf_noa || 0),
  'NPF %': Number(row.npf_ratio || 0),
  PPKA: Number(row.total_ppap || 0),
}))

const buildMetadataRows = () => [
  { Field: 'Laporan', Value: 'Master Rekap Console Pembiayaan' },
  { Field: 'Periode', Value: activePeriodLabel.value },
  { Field: 'Dimensi', Value: dimensionLabel.value },
  { Field: 'Cabang', Value: selectedCabangLabel.value },
  { Field: 'Sumber Data', Value: sourceInfoLabel.value },
  { Field: 'Generated At', Value: new Date().toLocaleString('id-ID') },
]

const exportExcel = async () => {
  if (isExporting.value) return
  isExporting.value = true
  try {
    const XLSX = await import('xlsx')
    const workbook = XLSX.utils.book_new()
    XLSX.utils.book_append_sheet(workbook, XLSX.utils.json_to_sheet(buildMetadataRows()), '00 Metadata')
    XLSX.utils.book_append_sheet(workbook, XLSX.utils.json_to_sheet(buildExportRows()), '01 Rekap Detail')
    XLSX.utils.book_append_sheet(workbook, XLSX.utils.json_to_sheet([{
      NOA: totals.value.noa,
      'Total OS': totals.value.total_os,
      'NPF OS': totals.value.npf_os,
      'NPF NOA': totals.value.npf_noa,
      'NPF %': totals.value.npf_ratio,
      PPKA: totals.value.total_ppap,
    }]), '02 Total')
    XLSX.writeFile(workbook, `rekap-pembiayaan-${selectedDimension.value}-${activePeriodLabel.value.replace(/\s+/g, '-')}.xlsx`)
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
    doc.text('Master Rekap Console Pembiayaan', 40, 38)
    doc.setFont('helvetica', 'normal')
    doc.setFontSize(9)
    doc.text(`${activePeriodLabel.value} · ${dimensionLabel.value} · ${selectedCabangLabel.value}`, 40, 56)
    doc.autoTable({
      startY: 76,
      head: [['Dimensi', 'NOA', 'Total OS', 'Kol 1', 'Kol 2', 'Kol 3', 'Kol 4', 'Kol 5', 'NPF %', 'PPKA']],
      body: buildExportRows().map(row => [
        row.Dimensi,
        formatNumber(row.NOA),
        formatRp(row['Total OS']),
        formatRp(row['Kol 1 OS']),
        formatRp(row['Kol 2 OS']),
        formatRp(row['Kol 3 OS']),
        formatRp(row['Kol 4 OS']),
        formatRp(row['Kol 5 OS']),
        formatTruncatedPercentage(row['NPF %']),
        formatRp(row.PPKA),
      ]),
      styles: { fontSize: 7, cellPadding: 4, overflow: 'linebreak' },
      headStyles: { fillColor: [15, 23, 42], textColor: 255, fontStyle: 'bold' },
      alternateRowStyles: { fillColor: [248, 250, 252] },
      margin: { left: 32, right: 32 },
    })
    const pageCount = doc.internal.getNumberOfPages()
    for (let page = 1; page <= pageCount; page += 1) {
      doc.setPage(page)
      doc.setFontSize(8)
      doc.setTextColor(100)
      doc.text(`Sumber: ${sourceInfoLabel.value}`, 32, doc.internal.pageSize.height - 18)
      doc.text(`Halaman ${page}/${pageCount}`, doc.internal.pageSize.width - 90, doc.internal.pageSize.height - 18)
    }
    doc.save(`rekap-pembiayaan-${selectedDimension.value}-${activePeriodLabel.value.replace(/\s+/g, '-')}.pdf`)
  } finally {
    isExporting.value = false
  }
}

const fetchCabangs = async () => {
  try {
    const res = await axios.get('/api/v1/financing/cabangs')
    if (res.data.success) {
      cabangs.value = res.data.data
    }
  } catch (error) {
    console.error('Failed to load cabangs:', error)
  }
}

const fetchRekapMaster = async () => {
  isLoading.value = true
  try {
    const params = {
      group_by: selectedDimension.value,
      cabang: selectedCabang.value || ''
    }
    if (selectedTahun.value) params.tahun = selectedTahun.value
    if (selectedBulan.value) params.bulan = selectedBulan.value
    const res = await axios.get('/api/v1/financing/rekap-master', { params })
    if (res.data.success) {
      rekapData.value.rows = res.data.rows
      rekapData.value.totals = res.data.totals
      rekapData.value.meta = res.data.meta
      const requested = String(res.data.meta?.requested_period || '')
      if (requested.length === 6) {
        selectedTahun.value = Number(requested.slice(0, 4))
        selectedBulan.value = Number(requested.slice(4, 6))
      }
    }
  } catch (error) {
    console.error('Failed to load rekap data:', error)
  } finally {
    isLoading.value = false
  }
}

// ─── Lifecycle & Watchers ────────────────────────────────────
onMounted(() => {
  fetchCabangs()
  fetchRekapMaster()
})

watch([selectedDimension, selectedCabang, selectedTahun, selectedBulan], () => {
  fetchRekapMaster()
})

</script>

<template>
  <div class="fin-page">
    <Head title="Master Rekap Console - Pembiayaan" />

    <!-- ── HERO HEADER ─────────────────────────────────────────── -->
    <div class="rekap-hero">
      <div class="rekap-hero__bg-deco"></div>
      <div class="rekap-hero__inner max-w-7xl mx-auto px-6 py-8">

        <!-- Top Row: Identity -->
        <div class="d-flex flex-wrap justify-space-between align-start gap-6 mb-6">
          <div class="d-flex align-start gap-5">
            <div class="rekap-hero__icon-box">
              <v-icon icon="ri-dashboard-3-line" size="32" color="white"></v-icon>
            </div>
            <div>
              <div class="d-flex align-center gap-2 mb-2">
                <span class="rekap-badge rekap-badge--glass">
                  <v-icon icon="ri-bar-chart-2-line" size="11" class="mr-1"></v-icon>
                  ANALYTICS MODULE
                </span>
                <span class="rekap-badge rekap-badge--compliant">
                  <v-icon icon="ri-pie-chart-line" size="11" class="mr-1"></v-icon>
                  Multi Dimensi
                </span>
              </div>
              <h1 class="rekap-hero__title">Master Rekap Console</h1>
              <p class="rekap-hero__subtitle">
                Volume &amp; distribusi bisnis pembiayaan per dimensi ? analisis risiko detail di
                <a href="/financing/quality" style="color: #5eead4; font-weight: 700; text-decoration: underline;">Quality Console</a>
              </p>
            </div>
          </div>
          <div class="rekap-export-actions">
            <v-btn
              size="small"
              variant="flat"
              color="success"
              rounded="lg"
              :loading="isExporting"
              prepend-icon="ri-file-excel-2-line"
              @click="exportExcel"
            >
              Excel
            </v-btn>
            <v-btn
              size="small"
              variant="flat"
              color="error"
              rounded="lg"
              :loading="isExporting"
              prepend-icon="ri-file-pdf-2-line"
              @click="exportPdf"
            >
              PDF
            </v-btn>
          </div>
        </div>

        <!-- Filter Bar -->
        <div class="filter-bar">
          <div class="filter-group">
            <div class="filter-icon-wrap">
              <v-icon icon="ri-layout-grid-line" size="16" color="#64748b"></v-icon>
            </div>
            <v-select
              v-model="selectedDimension"
              :items="dimensionOptions"
              item-title="label"
              item-value="value"
              label="Dimensi"
              density="compact"
              variant="solo"
              flat
              hide-details
              class="filter-select"
              style="min-width: 160px"
            ></v-select>
          </div>

          <div class="filter-divider"></div>

          <div class="filter-group flex-grow-1">
            <div class="filter-icon-wrap">
              <v-icon icon="ri-building-4-line" size="16" color="#64748b"></v-icon>
            </div>
            <v-select
              v-model="selectedCabang"
              :items="cabangs"
              item-title="nama"
              item-value="kdloc"
              label="Konsolidasi Seluruh Cabang"
              density="compact"
              variant="solo"
              flat
              hide-details
              clearable
              class="filter-select"
              style="min-width: 240px"
            ></v-select>
          </div>

          <div class="filter-divider"></div>

          <div class="filter-group">
            <div class="filter-icon-wrap">
              <v-icon icon="ri-calendar-line" size="16" color="#64748b"></v-icon>
            </div>
            <v-select
              v-model="selectedTahun"
              :items="yearOptions"
              label="Tahun"
              density="compact"
              variant="solo"
              flat
              hide-details
              class="filter-select"
              style="min-width: 120px"
            ></v-select>
          </div>

          <div class="filter-group">
            <div class="filter-icon-wrap">
              <v-icon icon="ri-calendar-event-line" size="16" color="#64748b"></v-icon>
            </div>
            <v-select
              v-model="selectedBulan"
              :items="monthOptions"
              item-title="title"
              item-value="value"
              label="Bulan"
              density="compact"
              variant="solo"
              flat
              hide-details
              class="filter-select"
              style="min-width: 150px"
            ></v-select>
          </div>

          <div class="filter-divider"></div>

          <div class="filter-group">
            <div class="filter-icon-wrap">
              <v-icon icon="ri-funds-line" size="16" color="#64748b"></v-icon>
            </div>
            <v-select
              v-model="selectedMetric"
              :items="[
                { title: 'Outstanding (Rp)', value: 'os' },
                { title: 'Nasabah (NOA)', value: 'noa' }
              ]"
              item-title="title"
              item-value="value"
              label="Metrik"
              density="compact"
              variant="solo"
              flat
              hide-details
              class="filter-select"
              style="min-width: 180px"
            ></v-select>
          </div>

          <div class="filter-divider"></div>

          <!-- View Toggle -->
          <div class="filter-group">
            <div class="filter-icon-wrap">
              <v-icon icon="ri-layout-2-line" size="16" color="#64748b"></v-icon>
            </div>
            <v-btn-toggle
              v-model="viewMode"
              mandatory
              rounded="lg"
              density="compact"
              class="rekap-view-toggle"
            >
              <v-btn value="grid" size="small" class="rekap-toggle-btn">
                <v-icon icon="ri-table-line" size="16"></v-icon>
              </v-btn>
              <v-btn value="chart" size="small" class="rekap-toggle-btn">
                <v-icon icon="ri-bar-chart-box-line" size="16"></v-icon>
              </v-btn>
            </v-btn-toggle>
          </div>
        </div>

        <!-- Active Filter Info -->
        <div class="filter-info-bar mt-3">
          <v-icon icon="ri-information-line" size="13" color="#94a3b8" class="mr-1"></v-icon>
          <span>Menampilkan data: {{ dimensionLabel }} | {{ selectedCabangLabel }} | Periode: {{ activePeriodLabel }} | Sumber: {{ sourceInfoLabel }} | Metrik: {{ selectedMetric === 'os' ? 'Outstanding (Rp)' : 'Nasabah (NOA)' }}</span>
        </div>

      </div>
    </div>

    <!-- SCORECARDS -->
    <div class="main-content max-w-7xl mx-auto px-6 pb-12">
    <v-alert
      v-if="periodUnavailable && !isLoading"
      type="warning"
      variant="tonal"
      border="start"
      rounded="lg"
      class="mb-6"
    >
      <div class="font-weight-bold mb-1">Periode {{ activePeriodLabel }} belum tersedia</div>
      <div class="text-body-2">{{ rekapData.meta?.message || 'Database snapshot/historis periode ini belum tersedia.' }}</div>
    </v-alert>

    <div class="rekap-insight-panel mb-6">
      <div class="rekap-insight-card rekap-insight-card--primary">
        <span>Insight Operasional</span>
        <strong>{{ operationalInsight }}</strong>
      </div>
      <div class="rekap-insight-card">
        <span>Kontributor OS Terbesar</span>
        <strong>{{ topContributor?.label || '-' }}</strong>
        <small>{{ topContributor ? `${formatRp(topContributor.total_os)} · ${formatTruncatedPercentage(concentrationRatio)}` : 'Tidak ada data' }}</small>
      </div>
      <div class="rekap-insight-card">
        <span>Kontributor NPF Terbesar</span>
        <strong>{{ topNpfContributor?.label || '-' }}</strong>
        <small>{{ topNpfContributor ? `${formatRp(topNpfContributor.npf_os)} · ${formatTruncatedPercentage(npfContributionRatio)}` : 'Tidak ada NPF' }}</small>
      </div>
    </div>

    <v-row class="mb-6">
      <v-col cols="12" sm="6" lg="3">
        <v-card class="rounded-xl border shadow-sm transition-swing" elevation="0" style="position: relative; overflow: hidden;">
          <div style="position: absolute; top: -20px; right: -20px; width: 120px; height: 120px; opacity: 0.08;">
            <v-icon icon="ri-wallet-3-line" size="120" color="#3b82f6" />
          </div>
          <v-card-text class="pa-5" style="position: relative; z-index: 1;">
            <div class="d-flex justify-space-between align-start">
              <div>
                <p class="text-caption font-weight-bold text-uppercase tracking-widest mb-1" style="color: #64748B; font-family: 'Inter', sans-serif;">TOTAL OUTSTANDING</p>
                <h2 class="fin-money-exact mb-2" style="color: #3b82f6;">{{ formatRp(totals.total_os) }}</h2>
                <p class="text-caption text-medium-emphasis mb-0" style="font-family: 'Inter', sans-serif;">Volume portofolio</p>
              </div>
            </div>
          </v-card-text>
        </v-card>
      </v-col>

      <v-col cols="12" sm="6" lg="3">
        <v-card class="rounded-xl border shadow-sm transition-swing" elevation="0" style="position: relative; overflow: hidden;">
          <div style="position: absolute; top: -20px; right: -20px; width: 120px; height: 120px; opacity: 0.08;">
            <v-icon icon="ri-file-list-3-line" size="120" color="#10b981" />
          </div>
          <v-card-text class="pa-5" style="position: relative; z-index: 1;">
            <div class="d-flex justify-space-between align-start">
              <div>
                <p class="text-caption font-weight-bold text-uppercase tracking-widest mb-1" style="color: #64748B; font-family: 'Inter', sans-serif;">TOTAL NOA</p>
                <h2 class="text-h4 font-weight-bold mb-2" style="color: #10b981; font-family: 'Plus Jakarta Sans', sans-serif; line-height: 1.2;">{{ formatNumber(totals.noa) }}</h2>
                <p class="text-caption text-medium-emphasis mb-0" style="font-family: 'Inter', sans-serif;">Akad aktif berjalan</p>
              </div>
            </div>
          </v-card-text>
        </v-card>
      </v-col>

      <v-col cols="12" sm="6" lg="3">
        <v-card class="rounded-xl border shadow-sm transition-swing" elevation="0" style="position: relative; overflow: hidden;">
          <div style="position: absolute; top: -20px; right: -20px; width: 120px; height: 120px; opacity: 0.08;">
            <v-icon icon="ri-error-warning-line" size="120" :color="totals.npf_ratio > 5 ? '#ef4444' : (totals.npf_ratio > 3 ? '#f59e0b' : '#10b981')" />
          </div>
          <v-card-text class="pa-5" style="position: relative; z-index: 1;">
            <div class="d-flex justify-space-between align-start">
              <div>
                <p class="text-caption font-weight-bold text-uppercase tracking-widest mb-1" style="color: #64748B; font-family: 'Inter', sans-serif;">NPF RATIO</p>
                <h2 class="text-h4 font-weight-bold mb-2"
                    :style="{ color: totals.npf_ratio > 5 ? '#ef4444' : (totals.npf_ratio > 3 ? '#f59e0b' : '#10b981'), fontFamily: 'Plus Jakarta Sans, sans-serif', lineHeight: 1.2 }">
                  {{ formatTruncatedPercentage(totals.npf_ratio) }}
                </h2>
                <p class="text-caption text-medium-emphasis mb-0" style="font-family: 'Inter', sans-serif;">
                  <span class="font-weight-bold">{{ formatRp(totals.npf_os) }}</span> Macet
                </p>
              </div>
            </div>
          </v-card-text>
        </v-card>
      </v-col>

      <v-col cols="12" sm="6" lg="3">
        <v-card class="rounded-xl border shadow-sm transition-swing" elevation="0" style="position: relative; overflow: hidden;">
          <div style="position: absolute; top: -20px; right: -20px; width: 120px; height: 120px; opacity: 0.08;">
            <v-icon icon="ri-safe-2-line" size="120" color="#8b5cf6" />
          </div>
          <v-card-text class="pa-5" style="position: relative; z-index: 1;">
            <div class="d-flex justify-space-between align-start">
              <div>
                <p class="text-caption font-weight-bold text-uppercase tracking-widest mb-1" style="color: #64748B; font-family: 'Inter', sans-serif;">TOTAL PPKA</p>
                <h2 class="fin-money-exact mb-2" style="color: #8b5cf6;">{{ formatRp(totals.total_ppap) }}</h2>
                <p class="text-caption text-medium-emphasis mb-0" style="font-family: 'Inter', sans-serif;">Cadangan kerugian</p>
              </div>
            </div>
          </v-card-text>
        </v-card>
      </v-col>
    </v-row>

    <!-- MAIN CONTENT -->
    <div class="content-card">
      <div class="content-card__header">
        <div>
          <div class="content-card__title">Detail {{ dimensionLabel }}</div>
          <div class="content-card__subtitle">Rincian agregasi portofolio</div>
        </div>
        <div class="d-flex align-center gap-2">
          <v-checkbox
            v-if="viewMode === 'grid'"
            v-model="hideEmptyRows"
            label="Sembunyikan kosong"
            density="compact"
            hide-details
            class="text-xs"
            color="primary"
          ></v-checkbox>
        </div>
      </div>

      <div class="content-card__body pa-0">
        <!-- LOADER -->
        <div v-if="isLoading" class="fin-loading">
          <v-progress-circular indeterminate color="primary" size="48"></v-progress-circular>
        </div>

        <!-- GRID MODE -->
        <div v-else-if="viewMode === 'grid'" class="overflow-x-auto">
          <table class="fin-table fin-vtable">
            <thead>
              <tr>
                <th>Label</th>
                <th style="text-align:right;">NOA</th>
                <th style="text-align:right;">Total O/S</th>
                <th style="text-align:right;">PPKA</th>
                <th style="text-align:right;">Kol 1 (Lancar)</th>
                <th style="text-align:right;">Kol 2 (DPK)</th>
                <th style="text-align:right;">Kol 3 (KL)</th>
                <th style="text-align:right;">Kol 4 (D)</th>
                <th style="text-align:right;">Kol 5 Macet</th>
                <th style="text-align:center;">NPF %</th>
              </tr>
            </thead>
            <tbody>
              <tr v-if="filteredRows.length === 0">
                <td colspan="10" class="text-center py-8 text-medium-emphasis">Tidak ada data untuk ditampilkan</td>
              </tr>
              <tr v-for="(row, idx) in filteredRows" :key="idx">
                <td class="font-weight-bold" style="color: #1e293b;">{{ row.label }}</td>
                <td style="text-align:right; font-weight: 500;">{{ formatNumber(row.noa) }}</td>
                <td style="text-align:right; font-weight: 700;">{{ formatRp(row.total_os) }}</td>
                <td style="text-align:right; font-weight: 600; color: #7c3aed;">{{ formatRp(row.total_ppap) }}</td>
                <td style="text-align:right;">{{ formatMetric(row.kol1_os, row.kol1_noa) }}</td>
                <td style="text-align:right;">{{ formatMetric(row.kol2_os, row.kol2_noa) }}</td>
                <td style="text-align:right;">{{ formatMetric(row.kol3_os, row.kol3_noa) }}</td>
                <td style="text-align:right;">{{ formatMetric(row.kol4_os, row.kol4_noa) }}</td>
                <td style="text-align:right;">{{ formatMetric(row.kol5_os, row.kol5_noa) }}</td>
                <td style="text-align:center;">
                  <span class="fin-pill" :style="getNpfCellStyle(row.npf_ratio)">
                    {{ formatTruncatedPercentage(row.npf_ratio) }}
                  </span>
                </td>
              </tr>
            </tbody>
            <tfoot>
              <tr>
                <td>TOTAL KESELURUHAN</td>
                <td style="text-align:right;">{{ formatNumber(totals.noa) }}</td>
                <td style="text-align:right;">{{ formatRp(totals.total_os) }}</td>
                <td style="text-align:right;">{{ formatRp(totals.total_ppap) }}</td>
                <td colspan="5"></td>
                <td style="text-align:center;">
                  <span class="fin-pill" :style="getNpfCellStyle(totals.npf_ratio)">
                    {{ formatTruncatedPercentage(totals.npf_ratio) }}
                  </span>
                </td>
              </tr>
            </tfoot>
          </table>
        </div>

        <!-- CHART MODE -->
        <div v-else-if="viewMode === 'chart'" class="pa-6">
          <v-row>
            <v-col cols="12">
              <v-card variant="outlined" class="rounded-lg border">
                <v-card-title class="text-subtitle-1 font-weight-bold pa-4 pb-0">Distribusi Outstanding per {{ dimensionLabel }}</v-card-title>
                <v-card-text>
                  <VueApexCharts type="treemap" height="500" :options="treeMapOpts" :series="treeMapSeries" />
                </v-card-text>
              </v-card>
            </v-col>
          </v-row>
          <v-row class="mt-0">
            <v-col cols="12">
              <v-alert type="info" variant="tonal" density="compact" rounded="lg" class="text-caption">
                <v-icon start size="small">ri-information-line</v-icon>
                Untuk analisis <strong>rasio NPF, coverage ratio, dan aging bucket</strong>, buka halaman
                <a href="/financing/quality" class="font-weight-bold">Quality & Risk Console</a>.
              </v-alert>
            </v-col>
          </v-row>
        </div>
      </div>
    </div>
    </div>
  </div>
</template>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap');

/* ─── Base ───────────────────────────────────────── */
* { box-sizing: border-box; }

/* ─── Rekap Hero Header ───────────────────────────── */
.rekap-hero {
  background: linear-gradient(135deg, #0f172a 0%, #1e293b 50%, #0f3460 100%);
  position: relative;
  overflow: hidden;
  border-bottom: 1px solid rgba(255,255,255,0.06);
  margin-bottom: 28px;
}
.rekap-hero__bg-deco {
  position: absolute;
  inset: 0;
  background: radial-gradient(ellipse at 70% -10%, rgba(99, 102, 241, 0.18) 0%, transparent 60%),
              radial-gradient(ellipse at 10% 110%, rgba(13, 148, 136, 0.15) 0%, transparent 50%);
  pointer-events: none;
}
.rekap-hero__inner { position: relative; z-index: 1; }

.rekap-hero__icon-box {
  width: 60px;
  height: 60px;
  border-radius: 16px;
  background: linear-gradient(135deg, #0d9488, #0f766e);
  display: flex;
  align-items: center;
  justify-content: center;
  box-shadow: 0 8px 24px rgba(13, 148, 136, 0.35);
  flex-shrink: 0;
}

.rekap-hero__title {
  font-size: 26px;
  font-weight: 800;
  color: #ffffff;
  letter-spacing: -0.03em;
  line-height: 1.2;
  margin: 0 0 4px;
}
.rekap-hero__subtitle {
  font-size: 14px;
  color: #94a3b8;
  margin: 0;
  font-weight: 400;
}

.rekap-export-actions {
  display: flex;
  align-items: center;
  gap: 10px;
  flex-wrap: wrap;
}

/* Badges */
.rekap-badge {
  display: inline-flex;
  align-items: center;
  font-size: 10px;
  font-weight: 700;
  padding: 3px 10px;
  border-radius: 99px;
  letter-spacing: 0.05em;
}
.rekap-badge--glass {
  background: linear-gradient(135deg, #0d9488, #0f766e);
  color: white;
  text-transform: uppercase;
}
.rekap-badge--compliant {
  background: rgba(255,255,255,0.08);
  border: 1px solid rgba(255,255,255,0.15);
  color: #cbd5e1;
  letter-spacing: 0.02em;
}

/* ─── Filter Bar ──────────────────────────────────── */
.filter-bar {
  display: flex;
  align-items: center;
  flex-wrap: wrap;
  gap: 0;
  background: rgba(255, 255, 255, 0.06);
  border: 1px solid rgba(255, 255, 255, 0.1);
  border-radius: 16px;
  padding: 6px 6px;
  backdrop-filter: blur(12px);
}
.filter-group {
  display: flex;
  align-items: center;
  gap: 6px;
  padding: 4px 12px;
}
.filter-icon-wrap {
  width: 28px;
  height: 28px;
  display: flex;
  align-items: center;
  justify-content: center;
  border-radius: 8px;
  background: rgba(255,255,255,0.06);
  flex-shrink: 0;
}
.filter-icon-wrap .v-icon { color: rgba(255,255,255,0.6) !important; }
.filter-divider {
  width: 1px;
  height: 32px;
  background: rgba(255,255,255,0.1);
  margin: 0 2px;
}
.filter-select :deep(.v-field) {
  background: transparent !important;
  box-shadow: none !important;
}
.filter-select :deep(.v-field__input) {
  color: #e2e8f0 !important;
  font-size: 13px !important;
  font-weight: 600 !important;
  padding-top: 8px !important;
}
.filter-select :deep(.v-label) {
  color: rgba(255,255,255,0.5) !important;
  font-size: 12px !important;
}
.filter-select :deep(.v-icon) { color: rgba(255,255,255,0.5) !important; }

/* View Toggle inside filter bar */
.rekap-view-toggle {
  background: rgba(255,255,255,0.06) !important;
  border: 1px solid rgba(255,255,255,0.12) !important;
  height: 36px !important;
}
.rekap-toggle-btn {
  color: rgba(255,255,255,0.6) !important;
  background: transparent !important;
  min-width: 38px !important;
  height: 34px !important;
}
.rekap-toggle-btn.v-btn--active {
  background: rgba(255,255,255,0.15) !important;
  color: #ffffff !important;
}

.filter-info-bar {
  display: flex;
  align-items: center;
  font-size: 12px;
  color: #cbd5e1;
  font-weight: 500;
}

.rekap-insight-panel {
  display: grid;
  grid-template-columns: minmax(0, 1.45fr) minmax(240px, 0.8fr) minmax(240px, 0.8fr);
  gap: 16px;
}

.rekap-insight-card {
  min-height: 112px;
  border: 1px solid #dbe7f3;
  border-radius: 20px;
  background: linear-gradient(145deg, #ffffff 0%, #f8fafc 100%);
  padding: 18px 20px;
  box-shadow: 0 10px 28px rgba(15, 23, 42, 0.06);
  display: flex;
  flex-direction: column;
  justify-content: center;
  gap: 7px;
}

.rekap-insight-card span {
  color: #64748b;
  font-size: 11px;
  font-weight: 800;
  letter-spacing: 0.06em;
  text-transform: uppercase;
}

.rekap-insight-card strong {
  color: #0f172a;
  font-family: 'Plus Jakarta Sans', sans-serif;
  font-size: 14px;
  line-height: 1.45;
}

.rekap-insight-card small {
  color: #64748b;
  font-size: 12px;
  font-weight: 600;
}

.rekap-insight-card--primary {
  background:
    radial-gradient(circle at top right, rgba(20, 184, 166, 0.18), transparent 34%),
    linear-gradient(145deg, #ecfeff 0%, #ffffff 72%);
  border-color: #99f6e4;
}

.rekap-insight-card--primary strong {
  color: #0f766e;
  font-size: 15px;
}

/* ─── Layout Helpers ──────────────────────────────── */
.max-w-7xl { max-width: 1280px; }
.mx-auto { margin-left: auto; margin-right: auto; }
.px-6 { padding-left: 24px; padding-right: 24px; }
.py-8 { padding-top: 32px; padding-bottom: 32px; }
.mb-6 { margin-bottom: 24px; }
.mt-3 { margin-top: 12px; }
.mr-1 { margin-right: 4px; }
.d-flex { display: flex; }
.align-start { align-items: flex-start; }
.align-center { align-items: center; }
.justify-space-between { justify-content: space-between; }
.flex-grow-1 { flex-grow: 1; }
.flex-wrap { flex-wrap: wrap; }
.gap-2 { gap: 8px; }
.gap-5 { gap: 20px; }
.gap-6 { gap: 24px; }
.pb-12 { padding-bottom: 48px; }

@media (max-width: 1100px) {
  .rekap-insight-panel {
    grid-template-columns: 1fr;
  }
}

@media (max-width: 768px) {
  .rekap-export-actions {
    width: 100%;
  }

  .rekap-export-actions .v-btn {
    flex: 1;
  }
}
</style>
