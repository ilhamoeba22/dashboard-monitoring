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
const data = ref([])
const summary = ref({
  total_writeoff_count: 0,
  total_writeoff_volume: 0,
  total_baki_debet: 0,
  total_sisa_margin: 0,
  total_recovery: 0,
  avg_recovery_rate: 0,
  top_ao: 'N/A',
  top_ao_count: 0,
  recovery_bucket: { nol: 0, rendah: 0, sedang: 0, tinggi: 0 },
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

const selectedTahun = ref(null)
const selectedBulan = ref(null)
const selectedAo = ref('Semua AO')
const selectedRecoveryBucket = ref('Semua Recovery')
const searchQuery = ref('')
const currentPage = ref(1)
const itemsPerPage = ref(15)
let syncingPeriodFromApi = false

const monthOptions = [
  { title: 'Januari', value: 1 }, { title: 'Februari', value: 2 }, { title: 'Maret', value: 3 },
  { title: 'April', value: 4 }, { title: 'Mei', value: 5 }, { title: 'Juni', value: 6 },
  { title: 'Juli', value: 7 }, { title: 'Agustus', value: 8 }, { title: 'September', value: 9 },
  { title: 'Oktober', value: 10 }, { title: 'November', value: 11 }, { title: 'Desember', value: 12 },
]
const recoveryBucketOptions = ['Semua Recovery', 'Belum Recovery', 'Recovery Rendah', 'Recovery Sedang', 'Recovery Tinggi']

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

const aoOptions = computed(() => ['Semua AO', ...[...new Set(data.value.map(item => item.nmao).filter(Boolean))].sort()])

const filteredData = computed(() => {
  const query = searchQuery.value.toLowerCase()
  return data.value.filter(item => {
    const matchAo = selectedAo.value === 'Semua AO' || item.nmao === selectedAo.value
    const matchRecovery = selectedRecoveryBucket.value === 'Semua Recovery' || getRecoveryBucketLabel(item.recovery_rate) === selectedRecoveryBucket.value
    const matchSearch = String(item.nama || '').toLowerCase().includes(query)
      || String(item.nocif || '').toLowerCase().includes(query)
      || String(item.nokontrak || '').toLowerCase().includes(query)
    return matchAo && matchRecovery && matchSearch
  })
})

const totalPages = computed(() => Math.max(1, Math.ceil(filteredData.value.length / itemsPerPage.value)))
const paginatedData = computed(() => {
  const start = (currentPage.value - 1) * itemsPerPage.value
  return filteredData.value.slice(start, start + itemsPerPage.value)
})

const filteredWriteOffVolume = computed(() => sumBy(filteredData.value, item => item.mdlawal))
const filteredBakiDebet = computed(() => sumBy(filteredData.value, item => item.baki_debet))
const filteredSisaMargin = computed(() => sumBy(filteredData.value, item => item.sisa_margin))
const filteredRecovery = computed(() => sumBy(filteredData.value, item => item.total_bayar_tahun_ini))
const filteredAvgRecovery = computed(() => {
  if (!filteredData.value.length) return 0
  return filteredData.value.reduce((sum, item) => sum + toFiniteNumber(item.recovery_rate), 0) / filteredData.value.length
})
const unrecoveredRows = computed(() => filteredData.value.filter(item => toFiniteNumber(item.recovery_rate) <= 0))
const highRecoveryRows = computed(() => filteredData.value.filter(item => toFiniteNumber(item.recovery_rate) >= 75))
const recoveryCoverage = computed(() => filteredWriteOffVolume.value > 0 ? (filteredRecovery.value / filteredWriteOffVolume.value) * 100 : 0)

const interpretation = computed(() => {
  if (errorMessage.value) return `Data belum dapat dimuat: ${errorMessage.value}`
  if (periodUnavailable.value) return 'Periode belum tersedia, sehingga data write-off dikosongkan agar tidak tertukar dengan database lain.'
  if (!filteredData.value.length) return 'Tidak ada kontrak write-off pada filter ini.'
  if (unrecoveredRows.value.length === filteredData.value.length) return 'Seluruh akun pada filter ini belum memiliki recovery tercatat. Prioritaskan strategi penagihan pasca hapus buku dan pemutakhiran status recovery.'
  if (filteredAvgRecovery.value < 25) return `Recovery rata-rata masih rendah di ${formatTruncatedPercentage(filteredAvgRecovery.value)}. Fokuskan action ke akun nominal besar dengan recovery nol atau rendah.`
  if (highRecoveryRows.value.length > 0) return `${formatNumber(highRecoveryRows.value.length)} akun memiliki recovery tinggi. Jadikan pola penagihan dan kanal pembayaran akun tersebut sebagai referensi recovery berikutnya.`
  return 'Recovery berada pada area monitoring. Lanjutkan pemetaan akun prioritas berdasarkan baki debet, sisa margin, dan AO pengelola.'
})

const aoRows = computed(() => {
  const groups = new Map()
  filteredData.value.forEach(item => {
    const key = item.nmao || 'TANPA AO'
    const current = groups.get(key) || {
      ao: key,
      rekening: 0,
      writeoff_volume: 0,
      baki_debet: 0,
      sisa_margin: 0,
      recovery: 0,
      recovery_rate_total: 0,
      unrecovered: 0,
    }
    current.rekening += 1
    current.writeoff_volume += toFiniteNumber(item.mdlawal)
    current.baki_debet += toFiniteNumber(item.baki_debet)
    current.sisa_margin += toFiniteNumber(item.sisa_margin)
    current.recovery += toFiniteNumber(item.total_bayar_tahun_ini)
    current.recovery_rate_total += toFiniteNumber(item.recovery_rate)
    if (toFiniteNumber(item.recovery_rate) <= 0) current.unrecovered += 1
    groups.set(key, current)
  })
  return [...groups.values()].map(row => ({
    ...row,
    avg_recovery_rate: row.rekening > 0 ? row.recovery_rate_total / row.rekening : 0,
  })).sort((a, b) => b.baki_debet - a.baki_debet)
})

const recoveryBucketRows = computed(() => {
  const buckets = [
    { label: 'Belum Recovery', color: '#E11D48', rows: filteredData.value.filter(item => toFiniteNumber(item.recovery_rate) <= 0) },
    { label: 'Recovery Rendah', color: '#F97316', rows: filteredData.value.filter(item => toFiniteNumber(item.recovery_rate) > 0 && toFiniteNumber(item.recovery_rate) < 25) },
    { label: 'Recovery Sedang', color: '#2563EB', rows: filteredData.value.filter(item => toFiniteNumber(item.recovery_rate) >= 25 && toFiniteNumber(item.recovery_rate) < 75) },
    { label: 'Recovery Tinggi', color: '#059669', rows: filteredData.value.filter(item => toFiniteNumber(item.recovery_rate) >= 75) },
  ]
  return buckets.map(bucket => ({
    label: bucket.label,
    color: bucket.color,
    rekening: bucket.rows.length,
    baki_debet: sumBy(bucket.rows, item => item.baki_debet),
    recovery: sumBy(bucket.rows, item => item.total_bayar_tahun_ini),
  }))
})

const recoveryChart = computed(() => ({
  series: recoveryBucketRows.value.map(row => row.rekening),
  options: {
    chart: { type: 'donut', background: 'transparent', fontFamily: "'Plus Jakarta Sans', sans-serif" },
    labels: recoveryBucketRows.value.map(row => row.label),
    colors: recoveryBucketRows.value.map(row => row.color),
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
      { name: 'Baki Debet', data: rows.map(row => row.baki_debet) },
      { name: 'Recovery', data: rows.map(row => row.recovery) },
    ],
    options: {
      chart: { type: 'bar', toolbar: { show: false }, fontFamily: 'Inter, sans-serif' },
      colors: ['#E11D48', '#059669'],
      plotOptions: { bar: { borderRadius: 8, horizontal: true } },
      dataLabels: { enabled: false },
      xaxis: { categories: rows.map(row => row.ao), labels: { formatter: value => formatRp(value), style: { colors: '#64748b', fontSize: '10px' } } },
      yaxis: { labels: { style: { colors: '#0f172a', fontWeight: 800 } } },
      grid: { borderColor: '#e2e8f0', strokeDashArray: 4 },
      tooltip: { y: { formatter: value => formatRp(value) } },
    },
  }
})

function sumBy(rows, selector) {
  return rows.reduce((sum, item) => sum + toFiniteNumber(selector(item)), 0)
}

function formatRp(value) {
  return formatExactRupiah(value)
}

function formatNumber(value) {
  return formatExactNumber(value)
}

function getRecoveryBucketLabel(rate) {
  const value = toFiniteNumber(rate)
  if (value <= 0) return 'Belum Recovery'
  if (value < 25) return 'Recovery Rendah'
  if (value < 75) return 'Recovery Sedang'
  return 'Recovery Tinggi'
}

function getRecoveryColor(rate) {
  const bucket = getRecoveryBucketLabel(rate)
  return {
    'Belum Recovery': 'error',
    'Recovery Rendah': 'warning',
    'Recovery Sedang': 'primary',
    'Recovery Tinggi': 'success',
  }[bucket] || 'grey'
}

function formatDate(value) {
  if (!value) return '-'
  const date = new Date(`${value}T00:00:00`)
  if (Number.isNaN(date.getTime())) return value
  return date.toLocaleDateString('id-ID', { day: '2-digit', month: 'short', year: 'numeric' })
}

const fetchData = async () => {
  loading.value = true
  errorMessage.value = ''
  try {
    const params = {}
    if (selectedTahun.value) params.tahun = selectedTahun.value
    if (selectedBulan.value) params.bulan = selectedBulan.value
    const response = await axios.get('/api/v1/financing/penyelesaian/write-off', { params })
    if (response.data.success) {
      periodMeta.value = response.data.period_meta || periodMeta.value
      const requested = String(periodMeta.value?.requested_period || '')
      if (requested.length === 6) {
        syncingPeriodFromApi = true
        selectedTahun.value = Number(requested.slice(0, 4))
        selectedBulan.value = Number(requested.slice(4, 6))
        setTimeout(() => { syncingPeriodFromApi = false }, 0)
      }
      data.value = response.data.data || []
      summary.value = { ...summary.value, ...(response.data.summary || {}) }
    } else {
      throw new Error(response.data.error || 'Gagal memuat data Write-Off')
    }
  } catch (error) {
    errorMessage.value = error?.response?.data?.error || error.message || 'Gagal memuat data Write-Off'
    data.value = []
  } finally {
    loading.value = false
  }
}

function buildDetailRows(rows = filteredData.value) {
  return rows.map(item => ({
    'No CIF': item.nocif || '-',
    'No Kontrak': item.nokontrak || '-',
    Nasabah: item.nama || '-',
    AO: item.nmao || '-',
    'Tanggal Efektif': item.tgleff || '-',
    'Tanggal Macet': item.tglmacet || '-',
    'Tanggal Write-Off': item.tglwo || '-',
    'Modal Awal': toFiniteNumber(item.mdlawal),
    'Baki Debet': toFiniteNumber(item.baki_debet),
    'Sisa Margin': toFiniteNumber(item.sisa_margin),
    'Total Recovery Tahun Ini': toFiniteNumber(item.total_bayar_tahun_ini),
    'Recovery Rate': formatTruncatedPercentage(item.recovery_rate),
    'Bucket Recovery': getRecoveryBucketLabel(item.recovery_rate),
  }))
}

function buildSummaryRows() {
  return [
    { Metrik: 'Periode', Nilai: activePeriodLabel.value },
    { Metrik: 'Sumber Data', Nilai: sourceInfoLabel.value },
    { Metrik: 'Total Rekening Filter', Nilai: filteredData.value.length },
    { Metrik: 'Total Modal Awal Write-Off', Nilai: filteredWriteOffVolume.value },
    { Metrik: 'Total Baki Debet', Nilai: filteredBakiDebet.value },
    { Metrik: 'Total Sisa Margin', Nilai: filteredSisaMargin.value },
    { Metrik: 'Total Recovery', Nilai: filteredRecovery.value },
    { Metrik: 'Recovery Coverage', Nilai: formatTruncatedPercentage(recoveryCoverage.value) },
    { Metrik: 'Rata-rata Recovery Rate', Nilai: formatTruncatedPercentage(filteredAvgRecovery.value) },
    { Metrik: 'Belum Recovery', Nilai: unrecoveredRows.value.length },
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
    XLSX.utils.book_append_sheet(workbook, XLSX.utils.json_to_sheet(buildDetailRows()), '01 Detail Write Off')
    XLSX.utils.book_append_sheet(workbook, XLSX.utils.json_to_sheet(aoRows.value), '02 Rekap AO')
    XLSX.utils.book_append_sheet(workbook, XLSX.utils.json_to_sheet(recoveryBucketRows.value), '03 Bucket Recovery')
    XLSX.writeFile(workbook, `write-off-monitoring-${activePeriodLabel.value.replace(/\s+/g, '-')}.xlsx`)
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
    doc.text('Write-Off Monitoring Pembiayaan', 40, 38)
    doc.setFont('helvetica', 'normal')
    doc.setFontSize(9)
    doc.text(`${activePeriodLabel.value} | ${sourceInfoLabel.value}`, 40, 56)
    doc.text(`Baki Debet ${formatRp(filteredBakiDebet.value)} | Recovery ${formatRp(filteredRecovery.value)} | Coverage ${formatTruncatedPercentage(recoveryCoverage.value)}`, 40, 70)
    doc.autoTable({
      startY: 94,
      head: [['Nasabah', 'Kontrak', 'AO', 'Tgl WO', 'Baki Debet', 'Sisa Margin', 'Recovery', 'Rate']],
      body: buildDetailRows().map(row => [row.Nasabah, row['No Kontrak'], row.AO, row['Tanggal Write-Off'], formatRp(row['Baki Debet']), formatRp(row['Sisa Margin']), formatRp(row['Total Recovery Tahun Ini']), row['Recovery Rate']]),
      styles: { fontSize: 7, cellPadding: 4, overflow: 'linebreak' },
      headStyles: { fillColor: [225, 29, 72], textColor: 255, fontStyle: 'bold' },
      columnStyles: { 4: { halign: 'right' }, 5: { halign: 'right' }, 6: { halign: 'right' }, 7: { halign: 'right' } },
    })
    doc.save(`write-off-monitoring-${activePeriodLabel.value.replace(/\s+/g, '-')}.pdf`)
  } finally {
    isExporting.value = false
  }
}

const resetPage = () => { currentPage.value = 1 }

onMounted(fetchData)
watch([selectedAo, selectedRecoveryBucket, searchQuery], resetPage)
watch([selectedTahun, selectedBulan], () => {
  if (syncingPeriodFromApi) return
  resetPage()
  fetchData()
})
</script>

<template>
  <div class="fin-page px-4 pt-0">
    <Head title="Write-Off Monitoring" />

    <div class="fin-hero mb-6">
      <div class="fin-hero__deco"></div>
      <div class="fin-hero__inner">
        <div class="d-flex flex-column flex-xl-row justify-space-between align-start align-xl-center gap-4">
          <div class="d-flex align-center gap-4">
            <div class="fin-hero__icon fin-icon-red">
              <v-icon icon="ri-delete-bin-5-fill" size="26" color="white" />
            </div>
            <div class="fin-hero__meta">
              <h1 class="fin-hero__title">Write-Off Monitoring</h1>
              <p class="fin-hero__subtitle">Monitoring hapus buku, baki debet, sisa margin, recovery, dan prioritas penagihan pasca write-off.</p>
              <div class="fin-hero__badges">
                <span class="fin-badge fin-badge--danger">Hapus Buku</span>
                <span class="fin-badge fin-badge--slate">{{ activePeriodLabel }}</span>
              </div>
            </div>
          </div>

          <div class="wo-toolbar">
            <div class="fin-filter-bar">
              <v-select v-model="selectedTahun" :items="yearOptions" label="Tahun" variant="solo" density="compact" flat hide-details rounded="lg" bg-color="white" prepend-inner-icon="ri-calendar-line" style="min-width: 120px; max-width: 140px;" />
              <v-select v-model="selectedBulan" :items="monthOptions" item-title="title" item-value="value" label="Bulan" variant="solo" density="compact" flat hide-details rounded="lg" bg-color="white" prepend-inner-icon="ri-calendar-event-line" style="min-width: 150px; max-width: 180px;" />
              <v-btn variant="text" density="comfortable" @click="fetchData" :loading="loading" icon="ri-refresh-line" color="white" />
            </div>
            <div class="wo-export-actions">
              <v-btn size="small" rounded="lg" color="success" variant="flat" :loading="isExporting" prepend-icon="ri-file-excel-2-line" @click="exportExcel">Excel</v-btn>
              <v-btn size="small" rounded="lg" color="error" variant="flat" :loading="isExporting" prepend-icon="ri-file-pdf-2-line" @click="exportPdf">PDF</v-btn>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="wo-insight-panel mb-6">
      <div class="wo-insight-card wo-insight-card--primary">
        <span>Interpretasi Operasional</span>
        <strong>{{ interpretation }}</strong>
        <small>{{ sourceInfoLabel }}</small>
      </div>
      <div class="wo-insight-card">
        <span>Baki Debet</span>
        <strong>{{ formatRp(filteredBakiDebet) }}</strong>
        <small>{{ formatNumber(filteredData.length) }} rekening dalam filter</small>
      </div>
      <div class="wo-insight-card">
        <span>Total Recovery</span>
        <strong>{{ formatRp(filteredRecovery) }}</strong>
        <small>Coverage {{ formatTruncatedPercentage(recoveryCoverage) }}</small>
      </div>
      <div class="wo-insight-card">
        <span>Belum Recovery</span>
        <strong>{{ formatNumber(unrecoveredRows.length) }} rekening</strong>
        <small>Perlu prioritas penagihan pasca hapus buku</small>
      </div>
    </div>

    <v-alert v-if="periodUnavailable && !loading" type="warning" variant="tonal" border="start" rounded="lg" class="mb-6">
      <div class="font-weight-bold mb-1">Periode {{ activePeriodLabel }} belum tersedia</div>
      <div class="text-body-2">{{ periodMeta.message || 'Database snapshot periode ini belum tersedia.' }}</div>
    </v-alert>

    <v-row class="mb-6">
      <v-col cols="12" sm="6" lg="3"><v-card class="wo-score-card" elevation="0"><v-icon icon="ri-file-damage-line" size="34" color="#e11d48" /><div><p>Total Write-Off</p><h2>{{ formatNumber(filteredData.length) }}</h2><small>Modal awal {{ formatRp(filteredWriteOffVolume) }}</small></div></v-card></v-col>
      <v-col cols="12" sm="6" lg="3"><v-card class="wo-score-card" elevation="0"><v-icon icon="ri-money-dollar-circle-line" size="34" color="#f97316" /><div><p>Sisa Margin</p><h2>{{ formatRp(filteredSisaMargin) }}</h2><small>Eksposur margin tersisa</small></div></v-card></v-col>
      <v-col cols="12" sm="6" lg="3"><v-card class="wo-score-card" elevation="0"><v-icon icon="ri-percent-line" size="34" color="#059669" /><div><p>Avg Recovery</p><h2>{{ formatTruncatedPercentage(filteredAvgRecovery) }}</h2><small>Coverage volume {{ formatTruncatedPercentage(recoveryCoverage) }}</small></div></v-card></v-col>
      <v-col cols="12" sm="6" lg="3"><v-card class="wo-score-card" elevation="0"><v-icon icon="ri-user-star-line" size="34" color="#2563eb" /><div><p>Top AO</p><h2>{{ aoRows[0]?.ao || 'N/A' }}</h2><small>{{ formatNumber(aoRows[0]?.rekening || 0) }} rekening prioritas</small></div></v-card></v-col>
    </v-row>

    <v-card class="wo-filter-card mb-6" elevation="0">
      <v-text-field v-model="searchQuery" prepend-inner-icon="ri-search-2-line" placeholder="Cari nasabah / NOCIF / kontrak..." variant="outlined" density="compact" hide-details rounded="lg" />
      <v-select v-model="selectedAo" :items="aoOptions" label="AO" prepend-inner-icon="ri-user-star-line" variant="outlined" density="compact" hide-details rounded="lg" />
      <v-select v-model="selectedRecoveryBucket" :items="recoveryBucketOptions" label="Bucket Recovery" prepend-inner-icon="ri-filter-3-line" variant="outlined" density="compact" hide-details rounded="lg" />
    </v-card>

    <v-row class="mb-6">
      <v-col cols="12" lg="4">
        <div class="content-card">
          <div class="content-card__header"><div><div class="content-card__title">Bucket Recovery</div><div class="content-card__subtitle">Segmentasi akun berdasarkan pencapaian recovery.</div></div></div>
          <div class="content-card__body d-flex align-center justify-center gap-6">
            <VueApexCharts v-if="!loading" type="donut" width="190" height="190" :options="recoveryChart.options" :series="recoveryChart.series" />
            <div class="wo-chart-legend">
              <span v-for="row in recoveryBucketRows" :key="row.label"><i :style="{ background: row.color }"></i>{{ row.label }} {{ formatNumber(row.rekening) }}</span>
            </div>
          </div>
        </div>
      </v-col>
      <v-col cols="12" lg="8">
        <div class="content-card">
          <div class="content-card__header"><div><div class="content-card__title">Top AO by Baki Debet</div><div class="content-card__subtitle">Prioritas AO berdasarkan eksposur write-off dan recovery.</div></div></div>
          <div class="content-card__body"><VueApexCharts v-if="!loading" type="bar" height="280" :options="aoChart.options" :series="aoChart.series" /></div>
        </div>
      </v-col>
    </v-row>

    <div class="content-card mb-6">
      <div class="content-card__header"><div><div class="content-card__title">Prioritas Account Officer</div><div class="content-card__subtitle">AO dengan eksposur baki debet terbesar dan akun belum recovery.</div></div></div>
      <div class="content-card__body pa-0">
        <div v-for="row in aoRows.slice(0, 8)" :key="row.ao" class="wo-priority-row">
          <div><strong>{{ row.ao }}</strong><small>{{ formatNumber(row.rekening) }} rekening | belum recovery {{ formatNumber(row.unrecovered) }} | avg {{ formatTruncatedPercentage(row.avg_recovery_rate) }}</small></div>
          <span>{{ formatRp(row.baki_debet) }}</span>
        </div>
        <div v-if="!aoRows.length" class="pa-8 text-center text-disabled">Tidak ada prioritas AO pada filter ini.</div>
      </div>
    </div>

    <div class="content-card">
      <div class="content-card__header">
        <div><div class="content-card__title">Detail Write-Off</div><div class="content-card__subtitle">Daftar akun hapus buku beserta baki debet, margin, recovery, dan AO pengelola.</div></div>
        <v-chip size="x-small" color="error" variant="flat" class="font-weight-black">{{ formatNumber(filteredData.length) }} data</v-chip>
      </div>
      <div class="content-card__body pa-0">
        <div class="overflow-x-auto">
          <table class="fin-table fin-vtable wo-table">
            <thead><tr><th>Nasabah / Kontrak</th><th>Tanggal</th><th>AO</th><th class="text-right">Baki Debet</th><th class="text-right">Recovery</th><th>Bucket</th></tr></thead>
            <tbody>
              <tr v-if="loading"><td colspan="6" class="pa-12 text-center"><v-progress-circular indeterminate color="error" size="46" /><div class="text-h6 text-medium-emphasis font-weight-bold mt-4">Memuat Data Write-Off...</div></td></tr>
              <tr v-else-if="paginatedData.length === 0"><td colspan="6" class="pa-12 text-center"><v-icon icon="ri-inbox-line" size="64" class="text-disabled mb-4" /><div class="text-h6 text-medium-emphasis font-weight-bold">Data Tidak Ditemukan</div><div class="text-caption text-disabled mt-1">Coba sesuaikan periode atau filter pencarian.</div></td></tr>
              <tr v-for="item in paginatedData" :key="item.nokontrak">
                <td><div class="font-weight-black text-uppercase">{{ item.nama }}</div><div class="wo-contract-flow"><span>{{ item.nocif }}</span><span>{{ item.nokontrak }}</span></div></td>
                <td><div class="wo-date-grid"><span>Eff <strong>{{ formatDate(item.tgleff) }}</strong></span><span>Macet <strong>{{ formatDate(item.tglmacet) }}</strong></span><span>WO <strong>{{ formatDate(item.tglwo) }}</strong></span></div></td>
                <td><div class="font-weight-black text-caption text-uppercase">{{ item.nmao || 'TANPA AO' }}</div></td>
                <td class="text-right"><div class="wo-money">{{ formatRp(item.baki_debet) }}</div><div class="text-caption text-medium-emphasis">Margin {{ formatRp(item.sisa_margin) }}</div><div class="text-caption text-medium-emphasis">Modal awal {{ formatRp(item.mdlawal) }}</div></td>
                <td class="text-right"><div class="wo-money wo-money--success">{{ formatRp(item.total_bayar_tahun_ini) }}</div><div class="mt-2"><v-progress-linear :model-value="Math.min(toFiniteNumber(item.recovery_rate), 100)" :color="getRecoveryColor(item.recovery_rate)" height="8" rounded /></div><div class="text-caption font-weight-black mt-1">{{ formatTruncatedPercentage(item.recovery_rate) }}</div></td>
                <td><v-chip size="small" :color="getRecoveryColor(item.recovery_rate)" variant="tonal" class="font-weight-black">{{ getRecoveryBucketLabel(item.recovery_rate) }}</v-chip></td>
              </tr>
            </tbody>
          </table>
        </div>
        <v-divider v-if="filteredData.length > 0" />
        <div v-if="filteredData.length > 0" class="pa-4 d-flex align-center justify-space-between bg-slate-50">
          <div class="text-caption text-medium-emphasis font-weight-bold">Menampilkan {{ (currentPage - 1) * itemsPerPage + 1 }} - {{ Math.min(currentPage * itemsPerPage, filteredData.length) }} dari {{ filteredData.length }} data</div>
          <v-pagination v-model="currentPage" :length="totalPages" :total-visible="5" density="compact" variant="flat" active-color="error" />
        </div>
      </div>
    </div>
  </div>
</template>

<style scoped>
.wo-toolbar,.wo-export-actions{display:flex;align-items:center;gap:12px;flex-wrap:wrap;justify-content:flex-end}.wo-export-actions{gap:8px}.fin-badge--slate{background:rgba(255,255,255,.12);color:#e2e8f0;border:1px solid rgba(255,255,255,.18)}
.wo-insight-panel{display:grid;grid-template-columns:minmax(0,1.45fr) repeat(3,minmax(210px,.75fr));gap:16px}.wo-insight-card,.wo-score-card,.wo-filter-card{background:linear-gradient(145deg,#fff 0%,#f8fafc 100%);border:1px solid #dbe7f3;border-radius:20px;box-shadow:0 10px 28px rgba(15,23,42,.06)}.wo-insight-card{min-height:124px;padding:18px 20px;display:flex;flex-direction:column;justify-content:center;gap:7px}.wo-insight-card span,.wo-score-card p{color:#64748b;font-size:11px;font-weight:900;text-transform:uppercase;letter-spacing:.06em;margin:0}.wo-insight-card strong{color:#0f172a;font-family:'Plus Jakarta Sans',sans-serif;font-size:clamp(.98rem,1vw,1.16rem);line-height:1.45;letter-spacing:-.02em}.wo-insight-card small,.wo-score-card small{color:#64748b;font-size:12px;font-weight:700}.wo-insight-card--primary{background:radial-gradient(circle at top right,rgba(225,29,72,.14),transparent 34%),linear-gradient(145deg,#fff1f2 0%,#fff 74%);border-color:#fecdd3}
.wo-score-card{min-height:132px;padding:20px;display:flex;align-items:center;gap:16px;height:100%}.wo-score-card h2{color:#0f172a;font-family:'Plus Jakarta Sans',sans-serif;font-size:clamp(1rem,1.55vw,1.75rem);font-weight:900;line-height:1.1;margin:4px 0;max-width:280px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis}.wo-filter-card{padding:16px;display:grid;grid-template-columns:minmax(260px,1.4fr) minmax(170px,.8fr) minmax(190px,.8fr);gap:12px}.wo-chart-legend{display:flex;flex-direction:column;gap:10px}.wo-chart-legend span{font-size:12px;font-weight:800;color:#334155}.wo-chart-legend i{display:inline-block;width:10px;height:10px;border-radius:999px;margin-right:8px}.wo-priority-row{display:flex;justify-content:space-between;gap:14px;padding:14px 18px;border-bottom:1px solid #e2e8f0}.wo-priority-row strong{color:#0f172a;font-size:13px;font-weight:900}.wo-priority-row small{display:block;color:#64748b;font-size:11px;font-weight:700;margin-top:3px}.wo-priority-row span,.wo-money{color:#0f172a;font-family:'Plus Jakarta Sans',sans-serif;font-size:12px;font-weight:900;white-space:nowrap}.wo-money--success{color:#059669}.wo-contract-flow{display:flex;align-items:center;gap:8px;color:#64748b;font-family:monospace;font-size:11px;font-weight:800;margin:4px 0}.wo-contract-flow span{background:#f1f5f9;border:1px solid #e2e8f0;border-radius:8px;padding:2px 6px}.wo-date-grid{display:grid;grid-template-columns:repeat(3,minmax(82px,1fr));gap:6px 10px}.wo-date-grid span{color:#64748b;font-size:10px;font-weight:900;text-transform:uppercase}.wo-date-grid strong{display:block;color:#0f172a;font-size:11px;text-transform:none}.wo-table :deep(th){height:52px!important;letter-spacing:.5px!important}.wo-table :deep(td){height:82px!important;vertical-align:middle}
@media(max-width:1180px){.wo-insight-panel{grid-template-columns:1fr 1fr}.wo-filter-card{grid-template-columns:1fr 1fr}}@media(max-width:720px){.wo-toolbar,.wo-export-actions{width:100%}.wo-export-actions .v-btn{flex:1}.wo-insight-panel,.wo-filter-card{grid-template-columns:1fr}.wo-date-grid{grid-template-columns:1fr}}
</style>
