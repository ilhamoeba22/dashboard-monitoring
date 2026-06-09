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
  avg_yield_tag: 0,
  avg_yield_byr: 0,
  avg_performance: 0,
  best_performer: 'N/A',
  best_performer_yield: 0,
  worst_performer: 'N/A',
  worst_performer_yield: 0,
  total_dimensions: 0,
  current_month: 12,
  active_only: true,
})
const periodMeta = ref({
  requested_period: null,
  active_period: null,
  period_available: true,
  source_table: 'TOFLMBEOM, TOFRS, TOFTRNH, TOFLMB',
  source_database: null,
})

const selectedDimension = ref('ao')
const selectedTahun = ref(new Date().getFullYear())
const selectedActiveOnly = ref(true)
const selectedPerformer = ref([])
const searchQuery = ref('')
const currentPage = ref(1)
const itemsPerPage = ref(15)

const allMonths = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des']
const dimensionOptions = [
  { title: 'Account Officer', value: 'ao' },
  { title: 'Cabang', value: 'cabang' },
  { title: 'Wilayah', value: 'wilayah' },
  { title: 'Produk', value: 'produk' },
  { title: 'Segmen', value: 'segmen' },
]

const yearOptions = computed(() => {
  const current = new Date().getFullYear()
  return [current, current - 1, current - 2, current - 3, current - 4]
})
const activeMonths = computed(() => allMonths.slice(0, Math.max(1, Math.min(12, Number(summary.value.current_month || 12)))))
const activePeriodLabel = computed(() => `${selectedTahun.value} sampai ${activeMonths.value.at(-1) || '-'}`)
const dimensionLabel = computed(() => dimensionOptions.find(item => item.value === selectedDimension.value)?.title || selectedDimension.value)
const sourceInfoLabel = computed(() => `${periodMeta.value?.source_database || '-'} | ${periodMeta.value?.source_table || '-'}`)

const enrichedRows = computed(() => data.value.map(row => ({
  ...row,
  avg_yield_tag: avgBySuffix(row, 'Yld_Tag'),
  avg_yield_byr: avgBySuffix(row, 'Yld_Byr'),
  avg_performance: avgBySuffix(row, 'Perf'),
  total_os_prev: sumBySuffix(row, 'OS_Prev'),
  total_tagihan: sumBySuffix(row, 'Tag'),
  total_bayar: sumBySuffix(row, 'Byr'),
})))

const filteredRows = computed(() => {
  const query = searchQuery.value.toLowerCase()
  return enrichedRows.value.filter(row => String(row.Nama || '').toLowerCase().includes(query) || String(row.Kode || '').toLowerCase().includes(query))
})

const totalPages = computed(() => Math.max(1, Math.ceil(filteredRows.value.length / itemsPerPage.value)))
const paginatedRows = computed(() => {
  const start = (currentPage.value - 1) * itemsPerPage.value
  return filteredRows.value.slice(start, start + itemsPerPage.value)
})

const totalOsPrev = computed(() => filteredRows.value.reduce((sum, row) => sum + row.total_os_prev, 0))
const totalTagihan = computed(() => filteredRows.value.reduce((sum, row) => sum + row.total_tagihan, 0))
const totalBayar = computed(() => filteredRows.value.reduce((sum, row) => sum + row.total_bayar, 0))
const actualYieldWeighted = computed(() => totalOsPrev.value > 0 ? (totalBayar.value / totalOsPrev.value) * 100 : 0)
const targetYieldWeighted = computed(() => totalOsPrev.value > 0 ? (totalTagihan.value / totalOsPrev.value) * 100 : 0)
const performanceWeighted = computed(() => totalTagihan.value > 0 ? (totalBayar.value / totalTagihan.value) * 100 : 0)
const yieldGap = computed(() => targetYieldWeighted.value - actualYieldWeighted.value)
const topRows = computed(() => [...filteredRows.value].sort((a, b) => b.avg_yield_byr - a.avg_yield_byr).slice(0, 8))
const lowRows = computed(() => [...filteredRows.value].filter(row => row.total_os_prev > 0).sort((a, b) => a.avg_yield_byr - b.avg_yield_byr).slice(0, 8))

const selectedPerformerOptions = computed(() => enrichedRows.value.map(row => row.Nama || 'N/A').sort())
const chartRows = computed(() => {
  const selected = selectedPerformer.value.length ? selectedPerformer.value : topRows.value.slice(0, 5).map(row => row.Nama)
  return enrichedRows.value.filter(row => selected.includes(row.Nama))
})

const interpretation = computed(() => {
  if (errorMessage.value) return `Data belum dapat dimuat: ${errorMessage.value}`
  if (!filteredRows.value.length) return 'Tidak ada data yield pada filter ini.'
  if (performanceWeighted.value < 75) return `Performance bayar terhadap tagihan masih rendah di ${formatTruncatedPercentage(performanceWeighted.value)}. Prioritaskan penagihan margin/bagi hasil pada ${dimensionLabel.value} dengan gap terbesar.`
  if (yieldGap.value > 0.5) return `Actual yield masih di bawah yield tagihan dengan gap ${formatTruncatedPercentage(yieldGap.value)}. Cek tunggakan margin dan efektivitas collection bulanan.`
  if (actualYieldWeighted.value > targetYieldWeighted.value) return 'Actual yield berada di atas yield tagihan. Pastikan komponen pembayaran berasal dari transaksi sah dan tidak terjadi salah klasifikasi pendapatan.'
  return 'Yield relatif terkendali. Tetap monitor dimensi dengan rata-rata yield terendah dan volume OS besar.'
})

const yieldChart = computed(() => ({
  series: chartRows.value.map(row => ({
    name: row.Nama || 'N/A',
    data: activeMonths.value.map(month => toFiniteNumber(row[`${month}_Yld_Byr`])),
  })),
  options: {
    chart: { type: 'line', toolbar: { show: true }, zoom: { enabled: true }, fontFamily: 'Plus Jakarta Sans, sans-serif' },
    colors: ['#2563EB', '#10B981', '#F97316', '#7C3AED', '#E11D48', '#0891B2', '#84CC16', '#F59E0B'],
    stroke: { curve: 'smooth', width: 4 },
    markers: { size: 4, strokeWidth: 2 },
    dataLabels: { enabled: false },
    xaxis: { categories: activeMonths.value, labels: { style: { colors: '#64748b', fontWeight: 700 } } },
    yaxis: { labels: { formatter: value => formatTruncatedPercentage(value), style: { colors: '#64748b', fontWeight: 700 } } },
    tooltip: { y: { formatter: value => formatTruncatedPercentage(value) } },
    grid: { borderColor: '#e2e8f0', strokeDashArray: 4 },
    legend: { position: 'top', horizontalAlign: 'left', fontSize: '12px' },
  },
}))

const performanceChart = computed(() => {
  const rows = topRows.value.slice(0, 8)
  return {
    series: [
      { name: 'Yield Bayar', data: rows.map(row => row.avg_yield_byr) },
      { name: 'Performance', data: rows.map(row => row.avg_performance) },
    ],
    options: {
      chart: { type: 'bar', toolbar: { show: false }, fontFamily: 'Inter, sans-serif' },
      colors: ['#10B981', '#2563EB'],
      plotOptions: { bar: { borderRadius: 8, horizontal: true } },
      dataLabels: { enabled: false },
      xaxis: { categories: rows.map(row => row.Nama), labels: { formatter: value => formatTruncatedPercentage(value), style: { colors: '#64748b', fontSize: '10px' } } },
      yaxis: { labels: { style: { colors: '#0f172a', fontWeight: 800 } } },
      tooltip: { y: { formatter: value => formatTruncatedPercentage(value) } },
      grid: { borderColor: '#e2e8f0', strokeDashArray: 4 },
    },
  }
})

function sumBySuffix(row, suffix) {
  return activeMonths.value.reduce((sum, month) => sum + toFiniteNumber(row[`${month}_${suffix}`]), 0)
}

function avgBySuffix(row, suffix) {
  const values = activeMonths.value.map(month => toFiniteNumber(row[`${month}_${suffix}`])).filter(value => value > 0)
  if (!values.length) return 0
  return values.reduce((sum, value) => sum + value, 0) / values.length
}

function formatRp(value) {
  return formatExactRupiah(value)
}

function formatNumber(value) {
  return formatExactNumber(value)
}

function getYieldColor(value) {
  const number = toFiniteNumber(value)
  if (number <= 0) return 'grey'
  if (number >= 2.5) return 'success'
  if (number >= 1.5) return 'warning'
  return 'error'
}

const fetchData = async () => {
  loading.value = true
  errorMessage.value = ''
  try {
    const response = await axios.get('/api/v1/financing/penyelesaian/yield', {
      params: {
        tahun: selectedTahun.value,
        dimensi: selectedDimension.value,
        active_only: selectedActiveOnly.value,
      },
    })
    if (response.data.success) {
      data.value = response.data.data || []
      summary.value = { ...summary.value, ...(response.data.summary || {}) }
      periodMeta.value = response.data.period_meta || periodMeta.value
      selectedPerformer.value = []
    } else {
      throw new Error(response.data.error || 'Gagal memuat data Yield')
    }
  } catch (error) {
    errorMessage.value = error?.response?.data?.error || error.message || 'Gagal memuat data Yield'
    data.value = []
  } finally {
    loading.value = false
  }
}

function buildSummaryRows() {
  return [
    { Metrik: 'Periode', Nilai: activePeriodLabel.value },
    { Metrik: 'Dimensi', Nilai: dimensionLabel.value },
    { Metrik: 'Sumber Data', Nilai: sourceInfoLabel.value },
    { Metrik: 'Total Dimensi', Nilai: filteredRows.value.length },
    { Metrik: 'Total OS Pembanding', Nilai: totalOsPrev.value },
    { Metrik: 'Total Tagihan Margin', Nilai: totalTagihan.value },
    { Metrik: 'Total Bayar Margin', Nilai: totalBayar.value },
    { Metrik: 'Yield Tagihan Tertimbang', Nilai: formatTruncatedPercentage(targetYieldWeighted.value) },
    { Metrik: 'Yield Bayar Tertimbang', Nilai: formatTruncatedPercentage(actualYieldWeighted.value) },
    { Metrik: 'Performance Tertimbang', Nilai: formatTruncatedPercentage(performanceWeighted.value) },
    { Metrik: 'Interpretasi', Nilai: interpretation.value },
  ]
}

function buildDetailRows(rows = filteredRows.value) {
  return rows.map(row => {
    const base = {
      Kode: row.Kode || '-',
      Nama: row.Nama || '-',
      'Avg Yield Tagihan': formatTruncatedPercentage(row.avg_yield_tag),
      'Avg Yield Bayar': formatTruncatedPercentage(row.avg_yield_byr),
      'Avg Performance': formatTruncatedPercentage(row.avg_performance),
      'Total OS Pembanding': row.total_os_prev,
      'Total Tagihan': row.total_tagihan,
      'Total Bayar': row.total_bayar,
    }
    activeMonths.value.forEach(month => {
      base[`${month} OS`] = toFiniteNumber(row[`${month}_OS_Prev`])
      base[`${month} Tagihan`] = toFiniteNumber(row[`${month}_Tag`])
      base[`${month} Bayar`] = toFiniteNumber(row[`${month}_Byr`])
      base[`${month} Yield Tagihan`] = formatTruncatedPercentage(row[`${month}_Yld_Tag`])
      base[`${month} Yield Bayar`] = formatTruncatedPercentage(row[`${month}_Yld_Byr`])
      base[`${month} Performance`] = formatTruncatedPercentage(row[`${month}_Perf`])
    })
    return base
  })
}

const exportExcel = async () => {
  if (isExporting.value) return
  isExporting.value = true
  try {
    const XLSX = await import('xlsx')
    const workbook = XLSX.utils.book_new()
    XLSX.utils.book_append_sheet(workbook, XLSX.utils.json_to_sheet(buildSummaryRows()), '00 Summary')
    XLSX.utils.book_append_sheet(workbook, XLSX.utils.json_to_sheet(buildDetailRows()), '01 Detail Yield')
    XLSX.utils.book_append_sheet(workbook, XLSX.utils.json_to_sheet(buildDetailRows(topRows.value)), '02 Top Performer')
    XLSX.utils.book_append_sheet(workbook, XLSX.utils.json_to_sheet(buildDetailRows(lowRows.value)), '03 Low Performer')
    XLSX.writeFile(workbook, `yield-analysis-${selectedDimension.value}-${selectedTahun.value}.xlsx`)
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
    doc.text('Yield Analysis Pembiayaan', 40, 38)
    doc.setFont('helvetica', 'normal')
    doc.setFontSize(9)
    doc.text(`${dimensionLabel.value} | ${activePeriodLabel.value} | ${sourceInfoLabel.value}`, 40, 56)
    doc.text(`Yield Bayar ${formatTruncatedPercentage(actualYieldWeighted.value)} | Performance ${formatTruncatedPercentage(performanceWeighted.value)} | Bayar ${formatRp(totalBayar.value)}`, 40, 70)
    doc.autoTable({
      startY: 94,
      head: [['Kode', 'Nama', 'Yield Tagihan', 'Yield Bayar', 'Performance', 'OS', 'Tagihan', 'Bayar']],
      body: buildDetailRows(topRows.value).map(row => [row.Kode, row.Nama, row['Avg Yield Tagihan'], row['Avg Yield Bayar'], row['Avg Performance'], formatRp(row['Total OS Pembanding']), formatRp(row['Total Tagihan']), formatRp(row['Total Bayar'])]),
      styles: { fontSize: 7, cellPadding: 4, overflow: 'linebreak' },
      headStyles: { fillColor: [37, 99, 235], textColor: 255, fontStyle: 'bold' },
      columnStyles: { 5: { halign: 'right' }, 6: { halign: 'right' }, 7: { halign: 'right' } },
    })
    doc.save(`yield-analysis-${selectedDimension.value}-${selectedTahun.value}.pdf`)
  } finally {
    isExporting.value = false
  }
}

const resetPage = () => { currentPage.value = 1 }

onMounted(fetchData)
watch([selectedDimension, selectedTahun, selectedActiveOnly], () => {
  resetPage()
  fetchData()
})
watch(searchQuery, resetPage)
</script>

<template>
  <div class="fin-page px-4 pt-0">
    <Head title="Yield Analysis" />

    <div class="fin-hero mb-6">
      <div class="fin-hero__deco"></div>
      <div class="fin-hero__inner">
        <div class="d-flex flex-column flex-xl-row justify-space-between align-start align-xl-center gap-4">
          <div class="d-flex align-center gap-4">
            <div class="fin-hero__icon fin-icon-blue">
              <v-icon icon="ri-line-chart-fill" size="26" color="white" />
            </div>
            <div class="fin-hero__meta">
              <h1 class="fin-hero__title">Yield Analysis</h1>
              <p class="fin-hero__subtitle">Analisis yield tagihan, yield bayar, performance margin, dan kontribusi nominal per dimensi pembiayaan.</p>
              <div class="fin-hero__badges">
                <span class="fin-badge fin-badge--info">Yield Monitoring</span>
                <span class="fin-badge fin-badge--slate">{{ activePeriodLabel }}</span>
              </div>
            </div>
          </div>

          <div class="yield-toolbar">
            <div class="fin-filter-bar">
              <v-select v-model="selectedTahun" :items="yearOptions" label="Tahun" variant="solo" density="compact" flat hide-details rounded="lg" bg-color="white" prepend-inner-icon="ri-calendar-line" style="min-width: 120px; max-width: 140px;" />
              <v-btn variant="text" density="comfortable" @click="fetchData" :loading="loading" icon="ri-refresh-line" color="white" />
            </div>
            <div class="yield-export-actions">
              <v-btn size="small" rounded="lg" color="success" variant="flat" :loading="isExporting" prepend-icon="ri-file-excel-2-line" @click="exportExcel">Excel</v-btn>
              <v-btn size="small" rounded="lg" color="error" variant="flat" :loading="isExporting" prepend-icon="ri-file-pdf-2-line" @click="exportPdf">PDF</v-btn>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="yield-insight-panel mb-6">
      <div class="yield-insight-card yield-insight-card--primary">
        <span>Interpretasi Operasional</span>
        <strong>{{ interpretation }}</strong>
        <small>{{ sourceInfoLabel }}</small>
      </div>
      <div class="yield-insight-card">
        <span>Yield Bayar Tertimbang</span>
        <strong>{{ formatTruncatedPercentage(actualYieldWeighted) }}</strong>
        <small>Total bayar {{ formatRp(totalBayar) }}</small>
      </div>
      <div class="yield-insight-card">
        <span>Performance</span>
        <strong>{{ formatTruncatedPercentage(performanceWeighted) }}</strong>
        <small>Bayar dibanding tagihan margin</small>
      </div>
      <div class="yield-insight-card">
        <span>Yield Gap</span>
        <strong :class="yieldGap > 0 ? 'text-error' : 'text-success'">{{ formatTruncatedPercentage(yieldGap) }}</strong>
        <small>Yield tagihan dikurangi yield bayar</small>
      </div>
    </div>

    <v-row class="mb-6">
      <v-col cols="12" sm="6" lg="3"><v-card class="yield-score-card" elevation="0"><v-icon icon="ri-funds-box-line" size="34" color="#d97706" /><div><p>Yield Tagihan</p><h2>{{ formatTruncatedPercentage(targetYieldWeighted) }}</h2><small>{{ formatRp(totalTagihan) }}</small></div></v-card></v-col>
      <v-col cols="12" sm="6" lg="3"><v-card class="yield-score-card" elevation="0"><v-icon icon="ri-hand-coin-line" size="34" color="#059669" /><div><p>Yield Bayar</p><h2>{{ formatTruncatedPercentage(actualYieldWeighted) }}</h2><small>{{ formatRp(totalBayar) }}</small></div></v-card></v-col>
      <v-col cols="12" sm="6" lg="3"><v-card class="yield-score-card" elevation="0"><v-icon icon="ri-trophy-line" size="34" color="#2563eb" /><div><p>Best Performer</p><h2>{{ topRows[0]?.Nama || 'N/A' }}</h2><small>{{ formatTruncatedPercentage(topRows[0]?.avg_yield_byr || 0) }}</small></div></v-card></v-col>
      <v-col cols="12" sm="6" lg="3"><v-card class="yield-score-card" elevation="0"><v-icon icon="ri-database-2-line" size="34" color="#7c3aed" /><div><p>Total OS</p><h2>{{ formatRp(totalOsPrev) }}</h2><small>{{ formatNumber(filteredRows.length) }} {{ dimensionLabel }}</small></div></v-card></v-col>
    </v-row>

    <v-card class="yield-filter-card mb-6" elevation="0">
      <v-select v-model="selectedDimension" :items="dimensionOptions" item-title="title" item-value="value" label="Dimensi" prepend-inner-icon="ri-layout-grid-line" variant="outlined" density="compact" hide-details rounded="lg" />
      <v-text-field v-model="searchQuery" prepend-inner-icon="ri-search-2-line" placeholder="Cari nama / kode..." variant="outlined" density="compact" hide-details rounded="lg" />
      <v-switch v-model="selectedActiveOnly" color="primary" hide-details inset label="Kontrak aktif saja" />
    </v-card>

    <v-row class="mb-6">
      <v-col cols="12" lg="7">
        <div class="content-card">
          <div class="content-card__header">
            <div><div class="content-card__title">Trend Yield Bayar</div><div class="content-card__subtitle">Pergerakan yield bayar bulanan untuk dimensi terpilih.</div></div>
          </div>
          <div class="content-card__body">
            <v-select v-model="selectedPerformer" :items="selectedPerformerOptions" multiple chips closable-chips label="Pilih performer untuk dibandingkan" variant="outlined" density="compact" hide-details rounded="lg" class="mb-4" />
            <VueApexCharts v-if="!loading && chartRows.length" type="line" height="320" :options="yieldChart.options" :series="yieldChart.series" />
            <div v-else class="pa-10 text-center text-disabled">Tidak ada data chart pada filter ini.</div>
          </div>
        </div>
      </v-col>
      <v-col cols="12" lg="5">
        <div class="content-card">
          <div class="content-card__header">
            <div><div class="content-card__title">Top Performer</div><div class="content-card__subtitle">Yield bayar dan performance rata-rata tertinggi.</div></div>
          </div>
          <div class="content-card__body">
            <VueApexCharts v-if="!loading && topRows.length" type="bar" height="350" :options="performanceChart.options" :series="performanceChart.series" />
            <div v-else class="pa-10 text-center text-disabled">Tidak ada performer pada filter ini.</div>
          </div>
        </div>
      </v-col>
    </v-row>

    <div class="content-card mb-6">
      <div class="content-card__header"><div><div class="content-card__title">Low Performer Watchlist</div><div class="content-card__subtitle">Dimensi dengan yield bayar terendah dan OS pembanding tetap ada.</div></div></div>
      <div class="content-card__body pa-0">
        <div v-for="row in lowRows" :key="row.Kode" class="yield-priority-row">
          <div><strong>{{ row.Nama }}</strong><small>{{ row.Kode }} | OS {{ formatRp(row.total_os_prev) }} | Tagihan {{ formatRp(row.total_tagihan) }}</small></div>
          <span>{{ formatTruncatedPercentage(row.avg_yield_byr) }}</span>
        </div>
        <div v-if="!lowRows.length" class="pa-8 text-center text-disabled">Tidak ada low performer pada filter ini.</div>
      </div>
    </div>

    <div class="content-card">
      <div class="content-card__header">
        <div><div class="content-card__title">Detail Yield {{ dimensionLabel }}</div><div class="content-card__subtitle">Yield bayar bulanan, rata-rata, performance, serta nominal OS/tagihan/bayar.</div></div>
        <v-chip size="x-small" color="primary" variant="flat" class="font-weight-black">{{ formatNumber(filteredRows.length) }} data</v-chip>
      </div>
      <div class="content-card__body pa-0">
        <div class="overflow-x-auto">
          <table class="fin-table fin-vtable yield-table">
            <thead>
              <tr>
                <th>Dimensi</th>
                <th v-for="month in activeMonths" :key="month" class="text-center">{{ month }}</th>
                <th class="text-right">Avg Yield</th>
                <th class="text-right">Performance</th>
                <th class="text-right">Nominal</th>
              </tr>
            </thead>
            <tbody>
              <tr v-if="loading"><td :colspan="activeMonths.length + 4" class="pa-12 text-center"><v-progress-circular indeterminate color="primary" size="46" /><div class="text-h6 text-medium-emphasis font-weight-bold mt-4">Memuat Data Yield...</div></td></tr>
              <tr v-else-if="paginatedRows.length === 0"><td :colspan="activeMonths.length + 4" class="pa-12 text-center"><v-icon icon="ri-inbox-line" size="64" class="text-disabled mb-4" /><div class="text-h6 text-medium-emphasis font-weight-bold">Data Tidak Ditemukan</div></td></tr>
              <tr v-for="row in paginatedRows" :key="row.Kode">
                <td><div class="font-weight-black text-uppercase">{{ row.Nama }}</div><div class="yield-code">{{ row.Kode }}</div></td>
                <td v-for="month in activeMonths" :key="`${row.Kode}-${month}`" class="text-center">
                  <v-chip size="x-small" :color="getYieldColor(row[`${month}_Yld_Byr`])" variant="tonal" class="font-weight-black">{{ formatTruncatedPercentage(row[`${month}_Yld_Byr`]) }}</v-chip>
                  <div class="yield-small">Perf {{ formatTruncatedPercentage(row[`${month}_Perf`]) }}</div>
                </td>
                <td class="text-right"><div class="yield-money">{{ formatTruncatedPercentage(row.avg_yield_byr) }}</div><div class="yield-small">Tag {{ formatTruncatedPercentage(row.avg_yield_tag) }}</div></td>
                <td class="text-right"><div class="yield-money">{{ formatTruncatedPercentage(row.avg_performance) }}</div></td>
                <td class="text-right"><div class="yield-money">{{ formatRp(row.total_bayar) }}</div><div class="yield-small">OS {{ formatRp(row.total_os_prev) }}</div><div class="yield-small">Tag {{ formatRp(row.total_tagihan) }}</div></td>
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
.yield-toolbar,.yield-export-actions{display:flex;align-items:center;gap:12px;flex-wrap:wrap;justify-content:flex-end}.yield-export-actions{gap:8px}.fin-badge--slate{background:rgba(255,255,255,.12);color:#e2e8f0;border:1px solid rgba(255,255,255,.18)}
.yield-insight-panel{display:grid;grid-template-columns:minmax(0,1.45fr) repeat(3,minmax(210px,.75fr));gap:16px}.yield-insight-card,.yield-score-card,.yield-filter-card{background:linear-gradient(145deg,#fff 0%,#f8fafc 100%);border:1px solid #dbe7f3;border-radius:20px;box-shadow:0 10px 28px rgba(15,23,42,.06)}.yield-insight-card{min-height:124px;padding:18px 20px;display:flex;flex-direction:column;justify-content:center;gap:7px}.yield-insight-card span,.yield-score-card p{color:#64748b;font-size:11px;font-weight:900;text-transform:uppercase;letter-spacing:.06em;margin:0}.yield-insight-card strong{color:#0f172a;font-family:'Plus Jakarta Sans',sans-serif;font-size:clamp(.98rem,1vw,1.16rem);line-height:1.45;letter-spacing:-.02em}.yield-insight-card small,.yield-score-card small{color:#64748b;font-size:12px;font-weight:700}.yield-insight-card--primary{background:radial-gradient(circle at top right,rgba(37,99,235,.16),transparent 34%),linear-gradient(145deg,#eff6ff 0%,#fff 74%);border-color:#bfdbfe}
.yield-score-card{min-height:132px;padding:20px;display:flex;align-items:center;gap:16px;height:100%}.yield-score-card h2{color:#0f172a;font-family:'Plus Jakarta Sans',sans-serif;font-size:clamp(1rem,1.55vw,1.75rem);font-weight:900;line-height:1.1;margin:4px 0;max-width:280px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis}.yield-filter-card{padding:16px;display:grid;grid-template-columns:minmax(180px,.7fr) minmax(260px,1.4fr) minmax(180px,.7fr);gap:12px;align-items:center}.yield-priority-row{display:flex;justify-content:space-between;gap:14px;padding:14px 18px;border-bottom:1px solid #e2e8f0}.yield-priority-row strong{color:#0f172a;font-size:13px;font-weight:900}.yield-priority-row small{display:block;color:#64748b;font-size:11px;font-weight:700;margin-top:3px}.yield-priority-row span,.yield-money{color:#0f172a;font-family:'Plus Jakarta Sans',sans-serif;font-size:12px;font-weight:900;white-space:nowrap}.yield-code{display:inline-flex;background:#f1f5f9;border:1px solid #e2e8f0;border-radius:8px;padding:2px 6px;color:#64748b;font-family:monospace;font-size:11px;font-weight:800;margin-top:4px}.yield-small{color:#64748b;font-size:10px;font-weight:800;margin-top:4px;white-space:nowrap}.yield-table :deep(th){height:52px!important;letter-spacing:.5px!important}.yield-table :deep(td){height:78px!important;vertical-align:middle}
@media(max-width:1180px){.yield-insight-panel{grid-template-columns:1fr 1fr}.yield-filter-card{grid-template-columns:1fr 1fr}}@media(max-width:720px){.yield-toolbar,.yield-export-actions{width:100%}.yield-export-actions .v-btn{flex:1}.yield-insight-panel,.yield-filter-card{grid-template-columns:1fr}}
</style>
