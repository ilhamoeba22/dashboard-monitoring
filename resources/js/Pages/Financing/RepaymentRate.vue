<script setup>
import { computed, onMounted, ref, watch } from 'vue'
import { Head, router, usePage } from '@inertiajs/vue3'
import DefaultLayout from '@/layouts/default.vue'
import axios from 'axios'
import VueApexCharts from 'vue3-apexcharts'
import '@/assets/css/financing-shared.css'
import { formatExactNumber, formatExactRupiah, formatTruncatedPercentage, toFiniteNumber } from '@/utils/money'

defineOptions({ layout: DefaultLayout })

const page = usePage()
const activeTab = ref(new URLSearchParams(String(page.url || '').split('?')[1] || '').get('tab') === 'new' ? 'new' : 'existing')
const loading = ref(false)
const isExporting = ref(false)
const errorMessage = ref('')
const existingRows = ref([])
const newRows = ref([])
const existingSummary = ref({})
const newSummary = ref({})
const existingFilters = ref({ search: '', ao: null, cabang: null, segmen: null, collectibility: null })
const newFilters = ref({ search: '', ao: null, onboarding_months: 6, risk_status: null })
const currentPage = ref(1)
const itemsPerPage = ref(15)

const kolOptions = [
  { title: 'Kol 1 - Lancar', value: '1' },
  { title: 'Kol 2 - DPK', value: '2' },
  { title: 'Kol 3 - Kurang Lancar', value: '3' },
  { title: 'Kol 4 - Diragukan', value: '4' },
  { title: 'Kol 5 - Macet', value: '5' },
]
const onboardingOptions = [
  { title: '3 bulan terakhir', value: 3 },
  { title: '6 bulan terakhir', value: 6 },
  { title: '12 bulan terakhir', value: 12 },
]
const riskOptions = ['Good', 'Warning', 'At Risk']

const activeRows = computed(() => activeTab.value === 'existing' ? existingRows.value : newRows.value)
const activeSummary = computed(() => activeTab.value === 'existing' ? existingSummary.value : newSummary.value)
const activeTitle = computed(() => activeTab.value === 'existing' ? 'Existing Portfolio' : 'New / Akuisisi & Retensi')

const existingFilteredRows = computed(() => {
  const query = existingFilters.value.search.toLowerCase()
  return existingRows.value.filter(item => {
    const matchSearch = String(item.nama_nasabah || '').toLowerCase().includes(query)
      || String(item.nocif || '').toLowerCase().includes(query)
      || String(item.nokontrak || '').toLowerCase().includes(query)
    const matchAo = !existingFilters.value.ao || item.nama_ao === existingFilters.value.ao
    const matchCabang = !existingFilters.value.cabang || item.nama_cabang === existingFilters.value.cabang
    const matchSegmen = !existingFilters.value.segmen || item.nama_segmen === existingFilters.value.segmen
    const matchKol = !existingFilters.value.collectibility || String(item.colbaru) === String(existingFilters.value.collectibility)
    return matchSearch && matchAo && matchCabang && matchSegmen && matchKol
  })
})

const newFilteredRows = computed(() => {
  const query = newFilters.value.search.toLowerCase()
  return newRows.value.filter(item => {
    const matchSearch = String(item.nama_nasabah || '').toLowerCase().includes(query)
      || String(item.nokontrak || '').toLowerCase().includes(query)
    const matchAo = !newFilters.value.ao || item.nama_ao === newFilters.value.ao
    const matchRisk = !newFilters.value.risk_status || item.risk_status === newFilters.value.risk_status
    return matchSearch && matchAo && matchRisk
  })
})

const filteredRows = computed(() => activeTab.value === 'existing' ? existingFilteredRows.value : newFilteredRows.value)
const totalPages = computed(() => Math.max(1, Math.ceil(filteredRows.value.length / itemsPerPage.value)))
const paginatedRows = computed(() => {
  const start = (currentPage.value - 1) * itemsPerPage.value
  return filteredRows.value.slice(start, start + itemsPerPage.value)
})

const existingAoOptions = computed(() => unique(existingRows.value, 'nama_ao'))
const existingCabangOptions = computed(() => unique(existingRows.value, 'nama_cabang'))
const existingSegmenOptions = computed(() => unique(existingRows.value, 'nama_segmen'))
const newAoOptions = computed(() => unique(newRows.value, 'nama_ao'))

const totalTagihan = computed(() => activeTab.value === 'existing'
  ? sumBy(filteredRows.value, item => item.totaltag)
  : sumBy(filteredRows.value, item => toFiniteNumber(item.tag_current_mdl) + toFiniteNumber(item.tag_current_mgn)))
const totalBayar = computed(() => activeTab.value === 'existing'
  ? sumBy(filteredRows.value, item => item.totalbyr)
  : sumBy(filteredRows.value, item => toFiniteNumber(item.cash_in_mdl) + toFiniteNumber(item.cash_in_mgn)))
const totalRecoveryTarget = computed(() => activeTab.value === 'new' ? sumBy(filteredRows.value, item => toFiniteNumber(item.target_recovery_mdl) + toFiniteNumber(item.target_recovery_mgn)) : 0)
const overallRate = computed(() => totalTagihan.value > 0 ? (totalBayar.value / totalTagihan.value) * 100 : 0)
const recoveryRate = computed(() => totalRecoveryTarget.value > 0 ? (totalBayar.value / totalRecoveryTarget.value) * 100 : 0)
const perfectRows = computed(() => filteredRows.value.filter(item => activeTab.value === 'existing' ? toFiniteNumber(item.pcttotal) >= 100 : toFiniteNumber(item.rr_pct) >= 100))
const warningRows = computed(() => filteredRows.value.filter(item => {
  const rate = activeTab.value === 'existing' ? toFiniteNumber(item.pcttotal) : toFiniteNumber(item.rr_pct)
  return rate < 80
}))

const interpretation = computed(() => {
  if (errorMessage.value) return `Data belum dapat dimuat: ${errorMessage.value}`
  if (!filteredRows.value.length) return `Tidak ada data ${activeTitle.value} pada filter ini.`
  if (activeTab.value === 'new' && recoveryRate.value > 0 && recoveryRate.value < 50) return `Recovery nasabah baru masih rendah di ${formatTruncatedPercentage(recoveryRate.value)}. Prioritaskan akun expired/onboarding yang sudah punya target recovery.`
  if (overallRate.value < 80) return `Repayment rate ${activeTitle.value} berada di bawah ambang monitoring (${formatTruncatedPercentage(overallRate.value)}). Fokus ke nasabah dengan tagihan besar dan pembayaran rendah.`
  if (warningRows.value.length > perfectRows.value.length) return `${formatNumber(warningRows.value.length)} rekening masih warning. Perlu follow-up AO dan validasi saldo autodebet.`
  return `${activeTitle.value} relatif terkendali dengan repayment rate ${formatTruncatedPercentage(overallRate.value)}. Tetap monitor anomali bayar lebih rendah dari tagihan.`
})

const aoRows = computed(() => {
  const groups = new Map()
  filteredRows.value.forEach(item => {
    const key = item.nama_ao || 'TANPA AO'
    const current = groups.get(key) || { ao: key, rekening: 0, tagihan: 0, bayar: 0, recovery_target: 0, warning: 0 }
    const rowTagihan = activeTab.value === 'existing' ? toFiniteNumber(item.totaltag) : toFiniteNumber(item.tag_current_mdl) + toFiniteNumber(item.tag_current_mgn)
    const rowBayar = activeTab.value === 'existing' ? toFiniteNumber(item.totalbyr) : toFiniteNumber(item.cash_in_mdl) + toFiniteNumber(item.cash_in_mgn)
    const rowRate = rowTagihan > 0 ? (rowBayar / rowTagihan) * 100 : 0
    current.rekening += 1
    current.tagihan += rowTagihan
    current.bayar += rowBayar
    current.recovery_target += activeTab.value === 'new' ? toFiniteNumber(item.target_recovery_mdl) + toFiniteNumber(item.target_recovery_mgn) : 0
    if (rowRate < 80) current.warning += 1
    groups.set(key, current)
  })
  return [...groups.values()].map(row => ({
    ...row,
    rr: row.tagihan > 0 ? (row.bayar / row.tagihan) * 100 : 0,
    recr: row.recovery_target > 0 ? (row.bayar / row.recovery_target) * 100 : 0,
  })).sort((a, b) => b.tagihan - a.tagihan)
})

const rrBucketRows = computed(() => {
  const buckets = [
    { label: 'Lancar >=100%', color: '#059669', rows: filteredRows.value.filter(item => getRowRate(item) >= 100) },
    { label: 'Watch 80-99%', color: '#2563EB', rows: filteredRows.value.filter(item => getRowRate(item) >= 80 && getRowRate(item) < 100) },
    { label: 'Warning 50-79%', color: '#F97316', rows: filteredRows.value.filter(item => getRowRate(item) >= 50 && getRowRate(item) < 80) },
    { label: 'Critical <50%', color: '#E11D48', rows: filteredRows.value.filter(item => getRowRate(item) < 50) },
  ]
  return buckets.map(bucket => ({ ...bucket, count: bucket.rows.length, tagihan: sumBy(bucket.rows, row => activeTab.value === 'existing' ? row.totaltag : toFiniteNumber(row.tag_current_mdl) + toFiniteNumber(row.tag_current_mgn)) }))
})

const bucketChart = computed(() => ({
  series: rrBucketRows.value.map(row => row.count),
  options: {
    chart: { type: 'donut', background: 'transparent', fontFamily: "'Plus Jakarta Sans', sans-serif" },
    labels: rrBucketRows.value.map(row => row.label),
    colors: rrBucketRows.value.map(row => row.color),
    dataLabels: { enabled: false },
    legend: { show: false },
    plotOptions: { pie: { donut: { size: '76%' } } },
    stroke: { width: 0 },
    tooltip: { y: { formatter: value => `${formatNumber(value)} rekening` } },
  },
}))

const aoChart = computed(() => {
  const rows = aoRows.value.slice(0, 8)
  return {
    series: [
      { name: 'Tagihan', data: rows.map(row => row.tagihan) },
      { name: 'Bayar', data: rows.map(row => row.bayar) },
    ],
    options: {
      chart: { type: 'bar', toolbar: { show: false }, fontFamily: 'Inter, sans-serif' },
      colors: ['#2563EB', '#10B981'],
      plotOptions: { bar: { borderRadius: 8, horizontal: true } },
      dataLabels: { enabled: false },
      xaxis: { categories: rows.map(row => row.ao), labels: { formatter: value => formatRp(value), style: { colors: '#64748b', fontSize: '10px' } } },
      yaxis: { labels: { style: { colors: '#0f172a', fontWeight: 800 } } },
      grid: { borderColor: '#e2e8f0', strokeDashArray: 4 },
      tooltip: { y: { formatter: value => formatRp(value) } },
    },
  }
})

function unique(rows, key) {
  return [...new Set(rows.map(item => item[key]).filter(Boolean))].sort()
}

function sumBy(rows, selector) {
  return rows.reduce((sum, item) => sum + toFiniteNumber(selector(item)), 0)
}

function getRowRate(item) {
  return activeTab.value === 'existing' ? toFiniteNumber(item.pcttotal) : toFiniteNumber(item.rr_pct)
}

function formatRp(value) {
  return formatExactRupiah(value)
}

function formatNumber(value) {
  return formatExactNumber(value)
}

function getKolColor(kol) {
  return { 1: 'success', 2: 'info', 3: 'warning', 4: 'deep-purple', 5: 'error' }[String(kol)] || 'grey'
}

function getRateColor(rate) {
  const value = toFiniteNumber(rate)
  if (value >= 100) return 'success'
  if (value >= 80) return 'primary'
  if (value >= 50) return 'warning'
  return 'error'
}

function statusDebet(item) {
  if (activeTab.value !== 'existing') return item.risk_status || '-'
  const tagihan = Math.max(0, toFiniteNumber(item.tagmdl) + toFiniteNumber(item.tagmgn))
  const bayar = toFiniteNumber(item.byrmdl) + toFiniteNumber(item.byrmgn)
  const sisa = tagihan - bayar
  if (toFiniteNumber(item.pcttotal) >= 100) return 'LUNAS'
  if (toFiniteNumber(item.saldo_netto) >= sisa && sisa > 0) return 'Cukup'
  return 'Kurang'
}

const fetchExisting = async () => {
  const response = await axios.get('/api/v1/financing/performance/repayment-rate')
  existingRows.value = response.data.data || []
  existingSummary.value = response.data.summary || {}
}

const fetchNew = async () => {
  const response = await axios.get('/api/v1/financing/performance/repayment-rate-new', {
    params: { onboarding_months: newFilters.value.onboarding_months },
  })
  newRows.value = response.data.data || []
  newSummary.value = response.data.summary || {}
}

const fetchData = async () => {
  loading.value = true
  errorMessage.value = ''
  try {
    if (activeTab.value === 'existing' && !existingRows.value.length) await fetchExisting()
    if (activeTab.value === 'new' && !newRows.value.length) await fetchNew()
  } catch (error) {
    errorMessage.value = error?.response?.data?.error || error.message || 'Gagal memuat data Repayment Rate'
  } finally {
    loading.value = false
  }
}

function buildExistingRows(rows = existingFilteredRows.value) {
  return rows.map(item => ({
    'No CIF': item.nocif || '-',
    'No Kontrak': item.nokontrak || '-',
    Nasabah: item.nama_nasabah || '-',
    Kol: item.colbaru || '-',
    Produk: item.nama_produk || '-',
    AO: item.nama_ao || '-',
    Cabang: item.nama_cabang || '-',
    Wilayah: item.nama_wilayah || '-',
    Segmen: item.nama_segmen || '-',
    'Tag Pokok Current': toFiniteNumber(item.tagmdl),
    'Bayar Pokok': toFiniteNumber(item.byrmdl),
    'RR Pokok': formatTruncatedPercentage(item.pctmdl),
    'Tag Margin Current': toFiniteNumber(item.tagmgn),
    'Bayar Margin': toFiniteNumber(item.byrmgn),
    'RR Margin': formatTruncatedPercentage(item.pctmgn),
    'RR Total': formatTruncatedPercentage(item.pcttotal),
    'Saldo Netto': toFiniteNumber(item.saldo_netto),
    'Status Debet': statusDebet(item),
    'Total Tunggakan': toFiniteNumber(item.grand_totaltag),
    'Status Autodebet': item.sts_autodebet || '-',
  }))
}

function buildNewRows(rows = newFilteredRows.value) {
  return rows.map(item => ({
    'No Kontrak': item.nokontrak || '-',
    Nasabah: item.nama_nasabah || '-',
    Kol: item.colbaru || '-',
    Produk: item.nama_produk || '-',
    AO: item.nama_ao || '-',
    Cabang: item.nama_cabang || '-',
    Wilayah: item.nama_wilayah || '-',
    'Tag Pokok Current': toFiniteNumber(item.tag_current_mdl),
    'Bayar Pokok': toFiniteNumber(item.cash_in_mdl),
    'Tag Margin Current': toFiniteNumber(item.tag_current_mgn),
    'Bayar Margin': toFiniteNumber(item.cash_in_mgn),
    'RR Total': formatTruncatedPercentage(item.rr_pct),
    'Target Recovery': toFiniteNumber(item.target_recovery_mdl) + toFiniteNumber(item.target_recovery_mgn),
    'Recovery Rate': formatTruncatedPercentage(item.recr_pct),
    'Risk Status': item.risk_status || '-',
    'Days Since Onboarding': item.days_since_onboarding || 0,
  }))
}

function buildSummaryRows() {
  return [
    { Metrik: 'Tab', Nilai: activeTitle.value },
    { Metrik: 'Sumber Legacy', Nilai: activeTab.value === 'existing' ? 'repayment-rate-pembiayaan.blade.php / FinancingRepaymentRate.php' : 'repayment-rate-new-pembiayaan.blade.php / FinancingRepaymentRateNew.php' },
    { Metrik: 'Total Rekening', Nilai: filteredRows.value.length },
    { Metrik: 'Total Tagihan', Nilai: totalTagihan.value },
    { Metrik: 'Total Bayar', Nilai: totalBayar.value },
    { Metrik: 'Repayment Rate', Nilai: formatTruncatedPercentage(overallRate.value) },
    { Metrik: 'Perfect/Lunas', Nilai: perfectRows.value.length },
    { Metrik: 'Warning', Nilai: warningRows.value.length },
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
    XLSX.utils.book_append_sheet(workbook, XLSX.utils.json_to_sheet(activeTab.value === 'existing' ? buildExistingRows() : buildNewRows()), '01 Detail Aktif')
    XLSX.utils.book_append_sheet(workbook, XLSX.utils.json_to_sheet(aoRows.value), '02 Rekap AO')
    XLSX.utils.book_append_sheet(workbook, XLSX.utils.json_to_sheet(rrBucketRows.value.map(({ rows, ...row }) => row)), '03 Bucket RR')
    XLSX.writeFile(workbook, `repayment-rate-${activeTab.value}.xlsx`)
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
    doc.text(`Repayment Rate - ${activeTitle.value}`, 40, 38)
    doc.setFont('helvetica', 'normal')
    doc.setFontSize(9)
    doc.text(`Tagihan ${formatRp(totalTagihan.value)} | Bayar ${formatRp(totalBayar.value)} | RR ${formatTruncatedPercentage(overallRate.value)}`, 40, 58)
    doc.autoTable({
      startY: 82,
      head: [['Nasabah', 'Kontrak', 'Kol', 'AO', 'Tagihan', 'Bayar', 'RR', activeTab.value === 'existing' ? 'Status Debet' : 'Risk']],
      body: filteredRows.value.map(item => [
        item.nama_nasabah || '-',
        item.nokontrak || '-',
        item.colbaru || '-',
        item.nama_ao || '-',
        formatRp(activeTab.value === 'existing' ? item.totaltag : toFiniteNumber(item.tag_current_mdl) + toFiniteNumber(item.tag_current_mgn)),
        formatRp(activeTab.value === 'existing' ? item.totalbyr : toFiniteNumber(item.cash_in_mdl) + toFiniteNumber(item.cash_in_mgn)),
        formatTruncatedPercentage(getRowRate(item)),
        statusDebet(item),
      ]),
      styles: { fontSize: 7, cellPadding: 4, overflow: 'linebreak' },
      headStyles: { fillColor: [37, 99, 235], textColor: 255, fontStyle: 'bold' },
      columnStyles: { 4: { halign: 'right' }, 5: { halign: 'right' }, 6: { halign: 'right' } },
    })
    doc.save(`repayment-rate-${activeTab.value}.pdf`)
  } finally {
    isExporting.value = false
  }
}

const resetPage = () => { currentPage.value = 1 }

onMounted(fetchData)
watch(activeTab, tab => {
  resetPage()
  router.replace(`/financing/repayment-rate${tab === 'new' ? '?tab=new' : ''}`, { preserveState: true, preserveScroll: true })
  fetchData()
})
watch([existingFilters, newFilters], resetPage, { deep: true })
watch(() => newFilters.value.onboarding_months, async () => {
  newRows.value = []
  if (activeTab.value === 'new') await fetchData()
})
</script>

<template>
  <div class="fin-page px-4 pt-0">
    <Head title="Repayment Rate Monitoring" />

    <div class="fin-hero mb-6">
      <div class="fin-hero__deco"></div>
      <div class="fin-hero__inner">
        <div class="d-flex flex-column flex-xl-row justify-space-between align-start align-xl-center gap-4">
          <div class="d-flex align-center gap-4">
            <div class="fin-hero__icon fin-icon-blue">
              <v-icon icon="ri-speed-up-line" size="26" color="white" />
            </div>
            <div class="fin-hero__meta">
              <h1 class="fin-hero__title">Repayment Rate Monitoring</h1>
              <p class="fin-hero__subtitle">Satu halaman untuk repayment existing portfolio dan new acquisition, dengan dua query legacy yang tetap terpisah.</p>
              <div class="fin-hero__badges">
                <span class="fin-badge fin-badge--info">Performance</span>
                <span class="fin-badge fin-badge--slate">Legacy Source Preserved</span>
              </div>
            </div>
          </div>

          <div class="rr-toolbar">
            <div class="rr-export-actions">
              <v-btn size="small" rounded="lg" color="success" variant="flat" :loading="isExporting" prepend-icon="ri-file-excel-2-line" @click="exportExcel">Excel</v-btn>
              <v-btn size="small" rounded="lg" color="error" variant="flat" :loading="isExporting" prepend-icon="ri-file-pdf-2-line" @click="exportPdf">PDF</v-btn>
              <v-btn size="small" rounded="lg" color="primary" variant="tonal" :loading="loading" prepend-icon="ri-refresh-line" @click="fetchData">Refresh</v-btn>
            </div>
          </div>
        </div>
      </div>
    </div>

    <v-card class="rr-tab-card mb-6" elevation="0">
      <v-tabs v-model="activeTab" color="primary" slider-color="primary" class="rr-tabs">
        <v-tab value="existing">Existing Portfolio</v-tab>
        <v-tab value="new">New / Akuisisi & Retensi</v-tab>
      </v-tabs>
    </v-card>

    <div class="rr-insight-panel mb-6">
      <div class="rr-insight-card rr-insight-card--primary">
        <span>Interpretasi Operasional</span>
        <strong>{{ interpretation }}</strong>
        <small>{{ activeTab === 'existing' ? 'Legacy: repayment-rate-pembiayaan' : 'Legacy: repayment-rate-new-pembiayaan' }}</small>
      </div>
      <div class="rr-insight-card">
        <span>Total Tagihan</span>
        <strong>{{ formatRp(totalTagihan) }}</strong>
        <small>{{ formatNumber(filteredRows.length) }} rekening dalam filter</small>
      </div>
      <div class="rr-insight-card">
        <span>Total Bayar</span>
        <strong>{{ formatRp(totalBayar) }}</strong>
        <small>RR {{ formatTruncatedPercentage(overallRate) }}</small>
      </div>
      <div class="rr-insight-card">
        <span>{{ activeTab === 'new' ? 'Recovery Rate' : 'Warning' }}</span>
        <strong>{{ activeTab === 'new' ? formatTruncatedPercentage(recoveryRate) : `${formatNumber(warningRows.length)} rekening` }}</strong>
        <small>{{ activeTab === 'new' ? `Target ${formatRp(totalRecoveryTarget)}` : 'RR di bawah 80%' }}</small>
      </div>
    </div>

    <v-row class="mb-6">
      <v-col cols="12" sm="6" lg="3"><v-card class="rr-score-card" elevation="0"><v-icon icon="ri-percent-line" size="34" color="#2563eb" /><div><p>Overall RR</p><h2>{{ formatTruncatedPercentage(overallRate) }}</h2><small>Bayar / Tagihan</small></div></v-card></v-col>
      <v-col cols="12" sm="6" lg="3"><v-card class="rr-score-card" elevation="0"><v-icon icon="ri-group-line" size="34" color="#6366f1" /><div><p>Total Rekening</p><h2>{{ formatNumber(filteredRows.length) }}</h2><small>{{ activeTitle }}</small></div></v-card></v-col>
      <v-col cols="12" sm="6" lg="3"><v-card class="rr-score-card" elevation="0"><v-icon icon="ri-checkbox-circle-line" size="34" color="#059669" /><div><p>Lancar</p><h2>{{ formatNumber(perfectRows.length) }}</h2><small>RR minimal 100%</small></div></v-card></v-col>
      <v-col cols="12" sm="6" lg="3"><v-card class="rr-score-card" elevation="0"><v-icon icon="ri-alert-line" size="34" color="#d97706" /><div><p>Warning</p><h2>{{ formatNumber(warningRows.length) }}</h2><small>RR di bawah 80%</small></div></v-card></v-col>
    </v-row>

    <v-card v-if="activeTab === 'existing'" class="rr-filter-card mb-6" elevation="0">
      <v-text-field v-model="existingFilters.search" prepend-inner-icon="ri-search-2-line" placeholder="Cari NOCIF / nama / kontrak..." variant="outlined" density="compact" hide-details rounded="lg" />
      <v-select v-model="existingFilters.ao" :items="existingAoOptions" label="AO" clearable variant="outlined" density="compact" hide-details rounded="lg" />
      <v-select v-model="existingFilters.cabang" :items="existingCabangOptions" label="Cabang" clearable variant="outlined" density="compact" hide-details rounded="lg" />
      <v-select v-model="existingFilters.segmen" :items="existingSegmenOptions" label="Segmen" clearable variant="outlined" density="compact" hide-details rounded="lg" />
      <v-select v-model="existingFilters.collectibility" :items="kolOptions" item-title="title" item-value="value" label="Kol" clearable variant="outlined" density="compact" hide-details rounded="lg" />
    </v-card>

    <v-card v-else class="rr-filter-card rr-filter-card--new mb-6" elevation="0">
      <v-text-field v-model="newFilters.search" prepend-inner-icon="ri-search-2-line" placeholder="Cari nama / kontrak..." variant="outlined" density="compact" hide-details rounded="lg" />
      <v-select v-model="newFilters.ao" :items="newAoOptions" label="AO" clearable variant="outlined" density="compact" hide-details rounded="lg" />
      <v-select v-model="newFilters.onboarding_months" :items="onboardingOptions" item-title="title" item-value="value" label="Onboarding" variant="outlined" density="compact" hide-details rounded="lg" />
      <v-select v-model="newFilters.risk_status" :items="riskOptions" label="Risk Status" clearable variant="outlined" density="compact" hide-details rounded="lg" />
    </v-card>

    <v-row class="mb-6">
      <v-col cols="12" lg="4">
        <div class="content-card">
          <div class="content-card__header"><div><div class="content-card__title">Bucket Repayment</div><div class="content-card__subtitle">Distribusi rekening berdasarkan RR.</div></div></div>
          <div class="content-card__body d-flex align-center justify-center gap-6">
            <VueApexCharts v-if="!loading" type="donut" width="190" height="190" :options="bucketChart.options" :series="bucketChart.series" />
            <div class="rr-chart-legend">
              <span v-for="row in rrBucketRows" :key="row.label"><i :style="{ background: row.color }"></i>{{ row.label }} {{ formatNumber(row.count) }}</span>
            </div>
          </div>
        </div>
      </v-col>
      <v-col cols="12" lg="8">
        <div class="content-card">
          <div class="content-card__header"><div><div class="content-card__title">Top AO by Tagihan</div><div class="content-card__subtitle">Perbandingan tagihan dan bayar per AO.</div></div></div>
          <div class="content-card__body"><VueApexCharts v-if="!loading" type="bar" height="280" :options="aoChart.options" :series="aoChart.series" /></div>
        </div>
      </v-col>
    </v-row>

    <div class="content-card mb-6">
      <div class="content-card__header"><div><div class="content-card__title">Prioritas Account Officer</div><div class="content-card__subtitle">AO dengan tagihan terbesar, warning terbanyak, dan repayment yang perlu dipantau.</div></div></div>
      <div class="content-card__body pa-0">
        <div v-for="row in aoRows.slice(0, 8)" :key="row.ao" class="rr-priority-row">
          <div><strong>{{ row.ao }}</strong><small>{{ formatNumber(row.rekening) }} rekening | warning {{ formatNumber(row.warning) }} | RR {{ formatTruncatedPercentage(row.rr) }}</small></div>
          <span>{{ formatRp(row.tagihan) }}</span>
        </div>
        <div v-if="!aoRows.length" class="pa-8 text-center text-disabled">Tidak ada prioritas AO pada filter ini.</div>
      </div>
    </div>

    <div class="content-card">
      <div class="content-card__header">
        <div><div class="content-card__title">Detail {{ activeTitle }}</div><div class="content-card__subtitle">Detail kontrak, tagihan, pembayaran, repayment rate, status saldo/autodebet, dan recovery untuk tab new.</div></div>
        <v-chip size="x-small" color="primary" variant="flat" class="font-weight-black">{{ formatNumber(filteredRows.length) }} data</v-chip>
      </div>
      <div class="content-card__body pa-0">
        <div class="overflow-x-auto">
          <table class="fin-table fin-vtable rr-table">
            <thead>
              <tr>
                <th>Nasabah / Kontrak</th>
                <th class="text-center">Kol</th>
                <th>AO / Cabang</th>
                <th class="text-right">Tagihan</th>
                <th class="text-right">Bayar</th>
                <th class="text-right">RR</th>
                <th>Status</th>
              </tr>
            </thead>
            <tbody>
              <tr v-if="loading"><td colspan="7" class="pa-12 text-center"><v-progress-circular indeterminate color="primary" size="46" /><div class="text-h6 text-medium-emphasis font-weight-bold mt-4">Memuat Repayment Rate...</div></td></tr>
              <tr v-else-if="paginatedRows.length === 0"><td colspan="7" class="pa-12 text-center"><v-icon icon="ri-inbox-line" size="64" class="text-disabled mb-4" /><div class="text-h6 text-medium-emphasis font-weight-bold">Data Tidak Ditemukan</div></td></tr>
              <tr v-for="item in paginatedRows" :key="`${activeTab}-${item.nokontrak}`">
                <td><div class="font-weight-black text-uppercase">{{ item.nama_nasabah }}</div><div class="rr-contract-flow"><span>{{ item.nocif || '-' }}</span><span>{{ item.nokontrak }}</span></div><div class="text-caption text-medium-emphasis">{{ item.nama_produk || '-' }}</div></td>
                <td class="text-center"><v-chip size="small" :color="getKolColor(item.colbaru)" variant="tonal" class="font-weight-black">Kol {{ item.colbaru }}</v-chip></td>
                <td><div class="font-weight-black text-caption text-uppercase">{{ item.nama_ao || 'TANPA AO' }}</div><div class="text-caption text-medium-emphasis">{{ item.nama_cabang || '-' }} | {{ item.nama_wilayah || '-' }}</div></td>
                <td class="text-right"><div class="rr-money">{{ formatRp(activeTab === 'existing' ? item.totaltag : toFiniteNumber(item.tag_current_mdl) + toFiniteNumber(item.tag_current_mgn)) }}</div><div class="rr-small">Pokok {{ formatRp(activeTab === 'existing' ? item.tagmdl : item.tag_current_mdl) }}</div><div class="rr-small">Margin {{ formatRp(activeTab === 'existing' ? item.tagmgn : item.tag_current_mgn) }}</div></td>
                <td class="text-right"><div class="rr-money rr-money--success">{{ formatRp(activeTab === 'existing' ? item.totalbyr : toFiniteNumber(item.cash_in_mdl) + toFiniteNumber(item.cash_in_mgn)) }}</div><div class="rr-small">Pokok {{ formatRp(activeTab === 'existing' ? item.byrmdl : item.cash_in_mdl) }}</div><div class="rr-small">Margin {{ formatRp(activeTab === 'existing' ? item.byrmgn : item.cash_in_mgn) }}</div></td>
                <td class="text-right"><v-chip size="small" :color="getRateColor(getRowRate(item))" variant="flat" class="font-weight-black text-white">{{ formatTruncatedPercentage(getRowRate(item)) }}</v-chip><div v-if="activeTab === 'new'" class="rr-small">Rec {{ formatTruncatedPercentage(item.recr_pct) }}</div></td>
                <td><v-chip size="small" :color="statusDebet(item) === 'Kurang' || statusDebet(item) === 'At Risk' ? 'warning' : 'success'" variant="tonal" class="font-weight-black">{{ statusDebet(item) }}</v-chip><div v-if="activeTab === 'existing'" class="rr-small">{{ item.sts_autodebet }}</div><div v-else class="rr-small">Target rec {{ formatRp(toFiniteNumber(item.target_recovery_mdl) + toFiniteNumber(item.target_recovery_mgn)) }}</div></td>
              </tr>
            </tbody>
          </table>
        </div>
        <v-divider v-if="filteredRows.length > 0" />
        <div v-if="filteredRows.length > 0" class="pa-4 d-flex align-center justify-space-between bg-slate-50">
          <div class="text-caption text-medium-emphasis font-weight-bold">Menampilkan {{ (currentPage - 1) * itemsPerPage + 1 }} - {{ Math.min(currentPage * itemsPerPage, filteredRows.length) }} dari {{ filteredRows.length }} data</div>
          <v-pagination v-model="currentPage" :length="totalPages" :total-visible="5" density="compact" variant="flat" active-color="primary" />
        </div>
      </div>
    </div>
  </div>
</template>

<style scoped>
.rr-toolbar,.rr-export-actions{display:flex;align-items:center;gap:12px;flex-wrap:wrap;justify-content:flex-end}.rr-export-actions{gap:8px}.fin-badge--slate{background:rgba(255,255,255,.12);color:#e2e8f0;border:1px solid rgba(255,255,255,.18)}
.rr-tab-card{background:#fff;border:1px solid #dbe7f3;border-radius:20px;box-shadow:0 10px 28px rgba(15,23,42,.06);overflow:hidden}.rr-tabs :deep(.v-tab){font-size:12px;font-weight:900;text-transform:uppercase;letter-spacing:.04em}
.rr-insight-panel{display:grid;grid-template-columns:minmax(0,1.45fr) repeat(3,minmax(210px,.75fr));gap:16px}.rr-insight-card,.rr-score-card,.rr-filter-card{background:linear-gradient(145deg,#fff 0%,#f8fafc 100%);border:1px solid #dbe7f3;border-radius:20px;box-shadow:0 10px 28px rgba(15,23,42,.06)}.rr-insight-card{min-height:124px;padding:18px 20px;display:flex;flex-direction:column;justify-content:center;gap:7px}.rr-insight-card span,.rr-score-card p{color:#64748b;font-size:11px;font-weight:900;text-transform:uppercase;letter-spacing:.06em;margin:0}.rr-insight-card strong{color:#0f172a;font-family:'Plus Jakarta Sans',sans-serif;font-size:clamp(.98rem,1vw,1.16rem);line-height:1.45;letter-spacing:-.02em}.rr-insight-card small,.rr-score-card small{color:#64748b;font-size:12px;font-weight:700}.rr-insight-card--primary{background:radial-gradient(circle at top right,rgba(37,99,235,.16),transparent 34%),linear-gradient(145deg,#eff6ff 0%,#fff 74%);border-color:#bfdbfe}
.rr-score-card{min-height:132px;padding:20px;display:flex;align-items:center;gap:16px;height:100%}.rr-score-card h2{color:#0f172a;font-family:'Plus Jakarta Sans',sans-serif;font-size:clamp(1rem,1.55vw,1.75rem);font-weight:900;line-height:1.1;margin:4px 0;max-width:280px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis}.rr-filter-card{padding:16px;display:grid;grid-template-columns:minmax(240px,1.35fr) repeat(4,minmax(145px,.7fr));gap:12px}.rr-filter-card--new{grid-template-columns:minmax(260px,1.4fr) repeat(3,minmax(160px,.8fr))}
.rr-chart-legend{display:flex;flex-direction:column;gap:10px}.rr-chart-legend span{font-size:12px;font-weight:800;color:#334155}.rr-chart-legend i{display:inline-block;width:10px;height:10px;border-radius:999px;margin-right:8px}.rr-priority-row{display:flex;justify-content:space-between;gap:14px;padding:14px 18px;border-bottom:1px solid #e2e8f0}.rr-priority-row strong{color:#0f172a;font-size:13px;font-weight:900}.rr-priority-row small{display:block;color:#64748b;font-size:11px;font-weight:700;margin-top:3px}.rr-priority-row span,.rr-money{color:#0f172a;font-family:'Plus Jakarta Sans',sans-serif;font-size:12px;font-weight:900;white-space:nowrap}.rr-money--success{color:#059669}.rr-contract-flow{display:flex;align-items:center;gap:8px;color:#64748b;font-family:monospace;font-size:11px;font-weight:800;margin:4px 0}.rr-contract-flow span{background:#f1f5f9;border:1px solid #e2e8f0;border-radius:8px;padding:2px 6px}.rr-small{color:#64748b;font-size:10px;font-weight:800;margin-top:4px;white-space:nowrap}.rr-table :deep(th){height:52px!important;letter-spacing:.5px!important}.rr-table :deep(td){height:82px!important;vertical-align:middle}
@media(max-width:1180px){.rr-insight-panel{grid-template-columns:1fr 1fr}.rr-filter-card,.rr-filter-card--new{grid-template-columns:1fr 1fr}}@media(max-width:720px){.rr-toolbar,.rr-export-actions{width:100%}.rr-export-actions .v-btn{flex:1}.rr-insight-panel,.rr-filter-card,.rr-filter-card--new{grid-template-columns:1fr}}
</style>
