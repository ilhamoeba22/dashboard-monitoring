<script setup>
import { ref, computed, onMounted, watch } from 'vue'
import { Head } from '@inertiajs/vue3'
import DefaultLayout from '@/layouts/default.vue'
import axios from 'axios'
import VueApexCharts from 'vue3-apexcharts'
import '@/assets/css/financing-shared.css'
import { formatExactRupiah, formatTruncatedPercentage } from '@/utils/money'

defineOptions({ layout: DefaultLayout })

// ─── State Management ────────────────────────────────────────
const isLoading    = ref(true)
const selectedYear = ref(new Date().getFullYear())
const selectedMonth = ref(new Date().getMonth() + 1)
const emptyMsg     = ref('')
const isExporting = ref(false)

const isDrawerOpen = ref(false)
const selectedAOData = ref(null)

const analyticsData = ref({
  has_data: false,
  scorecards: {
    total_target_annual: 0,
    total_realisasi: 0,
    total_target_ytd: 0,
    pacing_pct: 0,
    gap_miliar: 0,
    target_gap: 0,
    target_remaining: 0,
    target_surplus: 0,
    current_month: 1,
    sparkline: []
  },
  pacing_chart: {
    categories: [],
    target: [],
    realisasi: []
  },
  leaderboard: []
})

const monthOptions = [
  { title: 'Semua Bulan', value: null },
  { title: 'Januari', value: 1 },
  { title: 'Februari', value: 2 },
  { title: 'Maret', value: 3 },
  { title: 'April', value: 4 },
  { title: 'Mei', value: 5 },
  { title: 'Juni', value: 6 },
  { title: 'Juli', value: 7 },
  { title: 'Agustus', value: 8 },
  { title: 'September', value: 9 },
  { title: 'Oktober', value: 10 },
  { title: 'November', value: 11 },
  { title: 'Desember', value: 12 },
]

// ─── Computed Properties ─────────────────────────────────────
const scorecards = computed(() => analyticsData.value.scorecards)
const chartData  = computed(() => analyticsData.value.pacing_chart)
const leaderboard = computed(() => analyticsData.value.leaderboard)
const selectedPeriodLabel = computed(() => {
  const month = monthOptions.find(item => item.value === selectedMonth.value)?.title
  return selectedMonth.value ? `${month} ${selectedYear.value}` : `Tahun ${selectedYear.value}`
})
const targetGap = computed(() => Number(scorecards.value.target_gap ?? scorecards.value.gap_miliar ?? 0))
const targetRemaining = computed(() => Number(scorecards.value.target_remaining ?? Math.max(-targetGap.value, 0)))
const targetSurplus = computed(() => Number(scorecards.value.target_surplus ?? Math.max(targetGap.value, 0)))
const targetGapLabel = computed(() => targetSurplus.value > 0 ? 'SURPLUS TARGET' : 'SISA TARGET')
const targetGapValue = computed(() => targetSurplus.value > 0 ? targetSurplus.value : targetRemaining.value)
const topPerformer = computed(() => [...leaderboard.value].sort((a, b) => Number(b.pct || 0) - Number(a.pct || 0))[0] || null)
const lowestPerformer = computed(() => [...leaderboard.value].filter(item => Number(item.target_ytd || 0) > 0).sort((a, b) => Number(a.pct || 0) - Number(b.pct || 0))[0] || null)
const underperformingCount = computed(() => leaderboard.value.filter(item => item.status === 'underperforming').length)
const targetInsight = computed(() => {
  const pacing = Number(scorecards.value.pacing_pct || 0)
  if (pacing >= 110) return 'Pencapaian berada di atas target YTD dengan surplus sehat. Fokus berikutnya adalah menjaga kualitas pencairan dan sebaran AO.'
  if (pacing >= 100) return 'Target YTD sudah tercapai. Pertahankan ritme pencairan sambil monitor kualitas portofolio di Quality & Risk.'
  if (pacing >= 80) return 'Pencapaian masih dalam zona on-track, tetapi gap perlu dipantau mingguan agar tidak melebar di akhir periode.'
  return 'Pencapaian berada di bawah pacing target. Prioritaskan pipeline AO dengan gap terbesar dan validasi hambatan pencairan.'
})

const getProgressColor = (pct) => {
  if (pct >= 100) return '#10B981' // Success (Emerald)
  if (pct >= 80) return '#F59E0B'  // Warning (Amber)
  return '#EF4444'               // Error (Rose)
}

const pacingColor = computed(() => getProgressColor(scorecards.value.pacing_pct))

// ─── Chart Options ───────────────────────────────────────────
const chartOpts = computed(() => ({
  chart: {
    type: 'bar',
    fontFamily: "'Plus Jakarta Sans', sans-serif",
    toolbar: { show: false },
    zoom: { enabled: false },
    animations: { enabled: true, easing: 'easeinout', speed: 600 },
  },
  plotOptions: {
    bar: { columnWidth: '45%', borderRadius: 6, borderRadiusApplication: 'end' }
  },
  colors: ['#06B6D4', '#94a3b8'],
  dataLabels: { enabled: false },
  stroke: {
    curve: 'smooth',
    width: [0, 4],
    dashArray: [0, 5]
  },
  markers: {
    size: [0, 5],
    strokeColors: '#fff',
    strokeWidth: 2,
    hover: { size: 7 }
  },
  fill: {
    type: ['gradient', 'solid'],
    gradient: {
      type: 'vertical',
      shadeIntensity: 1,
      opacityFrom: 0.85,
      opacityTo: 0.5,
      stops: [0, 100]
    }
  },
  xaxis: {
    categories: chartData.value.categories,
    axisBorder: { show: false },
    axisTicks: { show: false },
    labels: { style: { fontSize: '11px', fontWeight: 600, colors: '#64748B' } }
  },
  yaxis: {
    labels: { 
        formatter: v => formatExactRupiah(v),
        style: { colors: '#94a3b8' }
    },
    title: { text: 'Nominal Rupiah', style: { color: '#94a3b8', fontSize: '11px' } }
  },
  legend: {
    position: 'top', horizontalAlign: 'right',
    markers: { shape: 'circle', size: 6, radius: 12 },
    fontWeight: 700, fontSize: '12px',
    labels: { colors: '#334155' }
  },
  tooltip: {
    shared: true,
    intersect: false,
    theme: 'light',
    y: { formatter: v => v !== null ? formatExactRupiah(v) : '-' }
  },
  grid: { borderColor: '#F1F5F9', strokeDashArray: 4 }
}))

const chartSeries = computed(() => [
  { name: 'Pencairan Riil', type: 'bar', data: chartData.value.realisasi },
  { name: 'Target RBB', type: 'line', data: chartData.value.target }
])

const aoChartSeries = computed(() => {
  if (!selectedAOData.value) return []
  return [
    { name: 'Realisasi AO', type: 'bar', data: selectedAOData.value.chart_realisasi },
    { name: 'Target AO', type: 'line', data: selectedAOData.value.chart_target }
  ]
})

// ─── Helpers ─────────────────────────────────────────────────
const formatFull = (val) => formatExactRupiah(val)
const pct = (val) => formatTruncatedPercentage(val)
const safeRealization = (item) => Number(item?.realisasi ?? item?.realisai ?? 0)

const getStatusColor = (status) => {
  if (status === 'overachieved') return 'success'
  if (status === 'on-track') return 'warning'
  return 'error'
}

const getStatusLabel = (status) => {
  if (status === 'overachieved') return 'Overachieved'
  if (status === 'on-track') return 'On-Track'
  return 'Underperforming'
}

// ─── API Fetch ───────────────────────────────────────────────
const buildExportRows = () => leaderboard.value.map((ao, index) => ({
  Rank: index + 1,
  'Kode AO': ao.kdao,
  'Nama AO': ao.name,
  'Target Tahunan': Number(ao.target_annual || 0),
  'Target YTD': Number(ao.target_ytd || 0),
  Realisasi: safeRealization(ao),
  'Pencapaian %': Number(ao.pct || 0),
  'Gap Target': Number(ao.target_gap ?? ao.gap ?? 0),
  'Sisa Target': Number(ao.target_remaining ?? Math.max(Number(ao.target_ytd || 0) - safeRealization(ao), 0)),
  'Surplus Target': Number(ao.target_surplus ?? Math.max(safeRealization(ao) - Number(ao.target_ytd || 0), 0)),
  Status: getStatusLabel(ao.status),
}))

const buildSummaryRows = () => [
  { Metrik: 'Periode', Nilai: selectedPeriodLabel.value },
  { Metrik: 'Target Tahunan RBB', Nilai: Number(scorecards.value.total_target_annual || 0) },
  { Metrik: 'Target YTD', Nilai: Number(scorecards.value.total_target_ytd || 0) },
  { Metrik: 'Realisasi YTD', Nilai: Number(scorecards.value.total_realisasi || 0) },
  { Metrik: 'Pencapaian %', Nilai: Number(scorecards.value.pacing_pct || 0) },
  { Metrik: 'Sisa Target', Nilai: targetRemaining.value },
  { Metrik: 'Surplus Target', Nilai: targetSurplus.value },
  { Metrik: 'AO Underperforming', Nilai: underperformingCount.value },
]

const exportExcel = async () => {
  if (isExporting.value) return
  isExporting.value = true
  try {
    const XLSX = await import('xlsx')
    const workbook = XLSX.utils.book_new()
    XLSX.utils.book_append_sheet(workbook, XLSX.utils.json_to_sheet(buildSummaryRows()), '00 Summary')
    XLSX.utils.book_append_sheet(workbook, XLSX.utils.json_to_sheet(buildExportRows()), '01 Leaderboard AO')
    XLSX.writeFile(workbook, `target-rbb-${selectedPeriodLabel.value.replace(/\s+/g, '-')}.xlsx`)
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
    doc.text('Target Management RBB Pembiayaan', 40, 38)
    doc.setFont('helvetica', 'normal')
    doc.setFontSize(9)
    doc.text(`${selectedPeriodLabel.value} · Pencapaian ${pct(scorecards.value.pacing_pct)} · ${targetGapLabel.value} ${formatFull(targetGapValue.value)}`, 40, 56)
    doc.autoTable({
      startY: 76,
      head: [['Rank', 'AO', 'Target YTD', 'Realisasi', 'Pencapaian', 'Sisa Target', 'Surplus', 'Status']],
      body: buildExportRows().map(row => [
        row.Rank,
        `${row['Nama AO']} (${row['Kode AO']})`,
        formatFull(row['Target YTD']),
        formatFull(row.Realisasi),
        pct(row['Pencapaian %']),
        formatFull(row['Sisa Target']),
        formatFull(row['Surplus Target']),
        row.Status,
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
      doc.text(`Generated: ${new Date().toLocaleString('id-ID')}`, 32, doc.internal.pageSize.height - 18)
      doc.text(`Halaman ${page}/${pageCount}`, doc.internal.pageSize.width - 90, doc.internal.pageSize.height - 18)
    }
    doc.save(`target-rbb-${selectedPeriodLabel.value.replace(/\s+/g, '-')}.pdf`)
  } finally {
    isExporting.value = false
  }
}

const fetchAnalytics = async () => {
  isLoading.value = true
  try {
    const { data } = await axios.get('/api/v1/financing/targets/analytics', {
      params: { 
        year: selectedYear.value,
        month: selectedMonth.value || '' 
      }
    })

    if (!data.success) throw new Error(data.error)
    
    analyticsData.value = data
    if (!data.has_data) {
      emptyMsg.value = data.message || `Tidak ada target RBB di waktu yang dipilih.`
    }
  } catch (error) {
    analyticsData.value.has_data = false
    emptyMsg.value = error.message || 'Gagal terhubung ke server.'
  } finally {
    isLoading.value = false
  }
}

const openAODetail = (ao) => {
  selectedAOData.value = ao
  isDrawerOpen.value = true
}

onMounted(fetchAnalytics)
watch([selectedYear, selectedMonth], fetchAnalytics)
</script>

<template>
  <div class="fin-page px-4 pt-0">
    <Head title="Target Management (RBB)" />

    <!-- ── HERO HEADER ─────────────────────────────────────────── -->
    <div class="fin-hero mb-6">
      <div class="fin-hero__deco"></div>
      <div class="fin-hero__inner">
        <div class="d-flex flex-column flex-lg-row justify-space-between align-start align-lg-center gap-4">
          <div class="d-flex align-center gap-4">
            <div class="fin-hero__icon fin-icon-blue">
              <v-icon icon="ri-focus-3-line" size="26" color="white" />
            </div>
            <div class="fin-hero__meta">
              <h1 class="fin-hero__title">Target Management (RBB)</h1>
              <p class="fin-hero__subtitle">
                Monitoring target, realisasi, pacing, dan gap pencairan pembiayaan berbasis AO.
              </p>
              <div class="fin-hero__badges">
                <span class="fin-badge fin-badge--teal">Islamic Banking</span>
                <span class="fin-badge fin-badge--success">
                  <span class="pulse-dot mr-1"></span> Active Monitor
                </span>
                <span class="fin-badge fin-badge--slate">{{ selectedPeriodLabel }}</span>
              </div>
            </div>
          </div>

          <div class="target-toolbar">
            <div class="fin-filter-bar">
              <v-select
                v-model="selectedMonth"
                :items="monthOptions"
                item-title="title"
                item-value="value"
                variant="solo"
                density="compact"
                flat
                hide-details
                rounded="lg"
                bg-color="white"
                prepend-inner-icon="ri-calendar-todo-line"
                style="min-width: 160px; max-width: 220px;"
                placeholder="Pilih Bulan"
              />
              <div style="width: 1px; height: 24px; background: rgba(255,255,255,0.2);" class="mx-1"></div>
              <v-select
                v-model="selectedYear"
                :items="[2024, 2025, 2026, 2027]"
                variant="solo"
                density="compact"
                flat
                hide-details
                rounded="lg"
                bg-color="white"
                prepend-inner-icon="ri-calendar-line"
                style="min-width: 120px; max-width: 160px;"
              />
            </div>
            <div class="target-export-actions">
              <v-btn size="small" rounded="lg" color="success" variant="flat" :loading="isExporting" prepend-icon="ri-file-excel-2-line" @click="exportExcel">Excel</v-btn>
              <v-btn size="small" rounded="lg" color="error" variant="flat" :loading="isExporting" prepend-icon="ri-file-pdf-2-line" @click="exportPdf">PDF</v-btn>
            </div>
          </div>
        </div>      </div>
    </div>

    <!-- LOADING STATE -->
    <template v-if="isLoading">
      <v-row class="mb-6">
        <v-col v-for="i in 4" :key="i" cols="12" sm="6" lg="3">
          <v-skeleton-loader type="card" height="120" rounded="xl" />
        </v-col>
      </v-row>
      <v-row>
        <v-col cols="12">
          <v-skeleton-loader type="image" height="420" rounded="xl" />
        </v-col>
      </v-row>
    </template>

    <!-- EMPTY STATE -->
    <template v-else-if="!analyticsData.has_data">
      <div class="empty-state d-flex flex-column align-center justify-center pa-10 text-center rounded-xl bg-slate-50 border-dashed border-slate-300 border-opacity-50" style="min-height: 400px; border-width: 2px;">
        <v-icon icon="ri-file-search-line" size="64" color="grey-lighten-1" class="mb-4" />
        <h2 class="text-h5 font-weight-bold text-slate-800 mb-2">Data Belum Tersedia</h2>
        <p class="text-body-1 text-slate-500 mb-6 max-w-md">{{ emptyMsg }}</p>
        <v-btn color="primary" rounded="lg" class="text-none font-weight-bold px-6" prepend-icon="ri-settings-5-line" href="/admin/management">
          Atur Data Target di Control Center
        </v-btn>
      </div>
    </template>

    <!-- MAIN CONTENT -->
    <template v-else>
      <div class="target-insight-panel mb-6">
        <div class="target-insight-card target-insight-card--primary">
          <span>Interpretasi Pacing</span>
          <strong>{{ targetInsight }}</strong>
        </div>
        <div class="target-insight-card">
          <span>Top Performer</span>
          <strong>{{ topPerformer?.name || '-' }}</strong>
          <small>{{ topPerformer ? `${pct(topPerformer.pct)} · ${formatFull(safeRealization(topPerformer))}` : 'Tidak ada data' }}</small>
        </div>
        <div class="target-insight-card">
          <span>Prioritas Coaching</span>
          <strong>{{ lowestPerformer?.name || '-' }}</strong>
          <small>{{ lowestPerformer ? `${pct(lowestPerformer.pct)} · Sisa ${formatFull(lowestPerformer.target_remaining ?? Math.max(Number(lowestPerformer.target_ytd || 0) - safeRealization(lowestPerformer), 0))}` : 'Tidak ada data' }}</small>
        </div>
        <div class="target-insight-card">
          <span>AO Underperforming</span>
          <strong>{{ underperformingCount }}</strong>
          <small>Dari {{ leaderboard.length }} AO dengan target aktif</small>
        </div>
      </div>

      <!-- 2. EXECUTIVE SCORECARDS -->
      <v-row class="mb-6">
        <v-col cols="12" sm="6" lg="3">
          <v-tooltip location="top" offset="10">
            <template #activator="{ props }">
              <v-card v-bind="props" class="rounded-xl border shadow-sm transition-swing" elevation="0" style="position: relative; overflow: hidden;">
                <div style="position: absolute; top: -20px; right: -20px; width: 120px; height: 120px; opacity: 0.08;">
                  <v-icon icon="ri-focus-3-line" size="120" color="#3b82f6" />
                </div>
                <v-card-text class="pa-5" style="position: relative; z-index: 1;">
                  <div>
                    <p class="text-caption font-weight-bold text-uppercase tracking-widest mb-1" style="color: #64748B; font-family: 'Inter', sans-serif;">TARGET ANNUAL (RBB)</p>
                    <h2 class="font-weight-black mb-2 target-money-exact" style="color: #3b82f6; font-family: 'Plus Jakarta Sans', sans-serif;">{{ formatFull(scorecards.total_target_annual) }}</h2>
                    <p class="text-caption text-medium-emphasis mb-0" style="font-family: 'Inter', sans-serif;">Plafon penyaluran tahunan</p>
                  </div>
                </v-card-text>
              </v-card>
            </template>
            <span>Detail: {{ formatFull(scorecards.total_target_annual) }}</span>
          </v-tooltip>
        </v-col>

        <v-col cols="12" sm="6" lg="3">
          <v-tooltip location="top" offset="10">
            <template #activator="{ props }">
              <v-card v-bind="props" class="rounded-xl border shadow-sm transition-swing" elevation="0" style="position: relative; overflow: hidden;">
                <div style="position: absolute; top: -20px; right: -20px; width: 120px; height: 120px; opacity: 0.08;">
                  <v-icon icon="ri-check-double-line" size="120" color="#10b981" />
                </div>
                <v-card-text class="pa-5" style="position: relative; z-index: 1;">
                  <div>
                    <p class="text-caption font-weight-bold text-uppercase tracking-widest mb-1" style="color: #64748B; font-family: 'Inter', sans-serif;">REALISASI (YTD)</p>
                    <h2 class="font-weight-black mb-2 target-money-exact" style="color: #10b981; font-family: 'Plus Jakarta Sans', sans-serif;">{{ formatFull(scorecards.total_realisasi) }}</h2>
                    <p class="text-caption text-medium-emphasis mb-0" style="font-family: 'Inter', sans-serif;">Total pencairan berjalan</p>
                  </div>
                </v-card-text>
              </v-card>
            </template>
            <span>Detail: {{ formatFull(scorecards.total_realisasi) }}</span>
          </v-tooltip>
        </v-col>

        <v-col cols="12" sm="6" lg="3">
          <v-card class="rounded-xl border shadow-sm transition-swing" elevation="0" style="position: relative; overflow: hidden;">
            <div style="position: absolute; top: -20px; right: -20px; width: 120px; height: 120px; opacity: 0.08;">
              <v-icon icon="ri-dashboard-3-line" size="120" :color="pacingColor" />
            </div>
            <v-card-text class="pa-5" style="position: relative; z-index: 1;">
              <div>
                <p class="text-caption font-weight-bold text-uppercase tracking-widest mb-1" style="color: #64748B; font-family: 'Inter', sans-serif;">PACING / PENCAPAIAN</p>
                <h2 class="text-h4 font-weight-black mb-2" :style="{ color: pacingColor, fontFamily: 'Plus Jakarta Sans, sans-serif', lineHeight: 1.2 }">
                  {{ pct(scorecards.pacing_pct) }}
                </h2>
                <p class="text-caption text-medium-emphasis mb-0" style="font-family: 'Inter', sans-serif;">Status performa YTD</p>
              </div>
            </v-card-text>
          </v-card>
        </v-col>

        <v-col cols="12" sm="6" lg="3">
          <v-tooltip location="top" offset="10">
            <template #activator="{ props }">
              <v-card v-bind="props" class="rounded-xl border shadow-sm transition-swing" elevation="0" style="position: relative; overflow: hidden;">
                <div style="position: absolute; top: -20px; right: -20px; width: 120px; height: 120px; opacity: 0.08;">
                  <v-icon icon="ri-flag-2-line" size="120" color="#8b5cf6" />
                </div>
                <v-card-text class="pa-5" style="position: relative; z-index: 1;">
                  <div>
                    <p class="text-caption font-weight-bold text-uppercase tracking-widest mb-1" style="color: #64748B; font-family: 'Inter', sans-serif;">{{ targetGapLabel }}</p>
                    <h2 class="font-weight-black mb-2 target-money-exact" style="color: #8b5cf6; font-family: 'Plus Jakarta Sans', sans-serif;">{{ formatFull(targetGapValue) }}</h2>
                    <p class="text-caption text-medium-emphasis mb-0" style="font-family: 'Inter', sans-serif;">{{ targetSurplus > 0 ? 'Kelebihan atas target YTD' : 'Kekurangan pencairan YTD' }}</p>
                  </div>
                </v-card-text>
              </v-card>
            </template>
            <span>Gap target: {{ formatFull(targetGap) }}</span>
          </v-tooltip>
        </v-col>
      </v-row>

      <!-- 3. DUAL-LINE PACING CHART -->
      <v-row class="mb-6">
        <v-col cols="12">
          <v-card elevation="0" border rounded="xl" class="pa-6 content-card">
            <div class="d-flex align-center gap-2 mb-6">
              <div class="fin-icon-blue pa-2 rounded-lg">
                <v-icon icon="ri-bar-chart-box-line" color="info" size="20" />
              </div>
              <h2 class="text-h6 font-weight-black text-slate-800 mb-0">Trend Pencairan vs RBB Tahunan</h2>
            </div>
            <VueApexCharts type="bar" height="400" :options="chartOpts" :series="chartSeries" />
          </v-card>
        </v-col>
      </v-row>

      <!-- 4. LEADERBOARD KINERJA AO -->
      <v-row>
        <v-col cols="12">
          <div class="content-card">
            <div class="content-card__header">
              <div>
                <div class="content-card__title">Leaderboard Kinerja AO</div>
                <div class="content-card__subtitle">Peringkat pencapaian target pencairan tahunan per Account Officer</div>
              </div>
              <div class="content-card__icon fin-icon-amber">
                <v-icon icon="ri-trophy-line" size="20" />
              </div>
            </div>

            <div class="overflow-x-auto pb-4">
              <table class="fin-table fin-vtable leaderboard-table">
                <thead>
                  <tr>
                    <th class="text-center font-weight-black text-slate-400 text-uppercase" style="width: 80px">Rank</th>
                    <th class="text-left font-weight-black text-slate-400 text-uppercase">Account Officer</th>
                    <th class="text-right font-weight-black text-slate-400 text-uppercase">Target YTD (Rp)</th>
                    <th class="text-right font-weight-black text-slate-400 text-uppercase">Realisasi (Rp)</th>
                    <th class="text-right font-weight-black text-slate-400 text-uppercase">Gap Target</th>
                    <th class="text-left font-weight-black text-slate-400 text-uppercase" style="min-width: 250px">Pencapaian (%)</th>
                    <th class="text-center font-weight-black text-slate-400 text-uppercase" style="width: 140px">Status</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="(ao, idx) in leaderboard" :key="ao.kdao" @click="openAODetail(ao)" class="cursor-pointer transition-swing">
                    <td class="text-center">
                      <v-icon v-if="idx === 0" icon="ri-medal-fill" size="24" color="amber-darken-1" />
                      <v-icon v-else-if="idx === 1" icon="ri-medal-fill" size="24" color="blue-grey-lighten-1" />
                      <v-icon v-else-if="idx === 2" icon="ri-medal-fill" size="24" color="deep-orange-darken-1" />
                      <span v-else class="font-weight-black text-slate-400">#{{ idx + 1 }}</span>
                    </td>
                    <td>
                      <div class="d-flex align-center gap-3 py-2">
                        <v-avatar size="40" :color="idx === 0 ? 'warning' : 'primary'" variant="tonal" class="font-weight-black text-subtitle-2">
                          {{ ao.name.substring(0, 2).toUpperCase() }}
                        </v-avatar>
                        <div>
                          <div class="font-weight-black text-slate-800 text-body-2 group-hover:text-primary">{{ ao.name }}</div>
                          <div class="text-caption text-slate-400 font-weight-bold">{{ ao.kdao }}</div>
                        </div>
                      </div>
                    </td>
                    <td class="text-right font-weight-bold text-slate-600">
                        {{ formatFull(ao.target_ytd).replace('Rp ', '') }}
                    </td>
                    <td class="text-right font-weight-black text-slate-900">
                        {{ formatFull(safeRealization(ao)).replace('Rp ', '') }}
                    </td>
                    <td class="text-right font-weight-black" :class="Number(ao.target_gap ?? ao.gap ?? 0) >= 0 ? 'text-emerald-600' : 'text-rose-600'">
                        {{ Number(ao.target_gap ?? ao.gap ?? 0) >= 0 ? '+' : '' }}{{ formatFull(ao.target_gap ?? ao.gap ?? 0) }}
                    </td>
                    <td>
                      <div class="d-flex flex-column justify-center pr-4">
                        <div class="d-flex justify-space-between mb-1">
                          <span class="text-caption font-weight-black" :style="{ color: getProgressColor(ao.pct) }">{{ pct(ao.pct) }}</span>
                        </div>
                        <div class="position-relative mt-1">
                          <div style="background: #f1f5f9; border-radius: 100px; height: 8px; width: 100%; position: relative; overflow: hidden;">
                            <div 
                              :style="{ width: Math.min(ao.pct, 100) + '%', background: getProgressColor(ao.pct) }"
                              style="height: 100%; border-radius: 100px; transition: width 1s cubic-bezier(0.4, 0, 0.2, 1);"
                            ></div>
                          </div>
                          <!-- Bullet Chart Target Marker (100% YTD) -->
                          <v-tooltip text="Target YTD (100%)">
                            <template #activator="{ props }">
                              <div 
                                v-bind="props"
                                class="position-absolute" 
                                style="top: -4px; bottom: -4px; width: 3px; background-color: #334155; right: 0; z-index: 1; border-radius: 4px;"
                              ></div>
                            </template>
                          </v-tooltip>
                        </div>
                      </div>
                    </td>
                    <td class="text-center">
                      <v-chip size="small" :color="getStatusColor(ao.status)" variant="flat" class="font-weight-black text-uppercase" style="font-size: 10px; letter-spacing: 0.5px;">
                        {{ getStatusLabel(ao.status) }}
                      </v-chip>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </v-col>
      </v-row>
    </template>

    <!-- 5. RIGHT SLIDE-OVER DRAWER (AO DETAIL) -->
    <v-navigation-drawer
      v-model="isDrawerOpen"
      location="right"
      temporary
      width="520"
      elevation="8"
      border="0"
    >
      <div v-if="selectedAOData" class="d-flex flex-column bg-slate-50">
        <!-- Drawer Header -->
        <div class="pa-6 bg-white border-b d-flex justify-space-between align-start">
          <div class="d-flex align-center gap-4">
            <v-avatar size="64" :color="getProgressColor(selectedAOData.pct)" variant="tonal" class="text-h5 font-weight-black">
              {{ selectedAOData.name.substring(0, 2).toUpperCase() }}
            </v-avatar>
            <div>
              <div class="text-h6 font-weight-black text-slate-800 mb-0" style="line-height: 1.2;">{{ selectedAOData.name }}</div>
              <div class="text-body-2 text-slate-500 font-weight-bold mt-1">ID: {{ selectedAOData.kdao }}</div>
              <v-chip size="small" :color="getStatusColor(selectedAOData.status)" variant="flat" class="mt-2 font-weight-black text-uppercase" style="font-size: 10px;">
                {{ getStatusLabel(selectedAOData.status) }}
              </v-chip>
            </div>
          </div>
          <v-btn icon="ri-close-line" variant="tonal" size="small" color="slate-400" @click="isDrawerOpen = false" class="rounded-lg"></v-btn>
        </div>

        <div class="pa-6 overflow-y-auto" style="flex: 1;">
          <!-- Mini Scorecards -->
          <v-row class="mb-6">
            <v-col cols="6">
              <v-card elevation="0" border rounded="xl" class="pa-4 bg-white text-center">
                <div class="text-caption text-slate-500 font-weight-black text-uppercase mb-2">Target YTD</div>
                <div class="text-h6 font-weight-black text-slate-800 target-money-drawer">{{ formatFull(selectedAOData.target_ytd) }}</div>
              </v-card>
            </v-col>
            <v-col cols="6">
              <v-card elevation="0" border rounded="xl" class="pa-4 bg-white text-center">
                <div class="text-caption text-slate-500 font-weight-black text-uppercase mb-2">Realisasi</div>
                <div class="text-h6 font-weight-black text-emerald-600 target-money-drawer">{{ formatFull(safeRealization(selectedAOData)) }}</div>
              </v-card>
            </v-col>
            <v-col cols="12">
              <v-card elevation="0" border rounded="xl" class="pa-5 bg-white d-flex align-center justify-space-between overflow-hidden position-relative">
                <div :style="{ background: getProgressColor(selectedAOData.pct) }" style="position:absolute; left:0; top:0; bottom:0; width:4px; opacity:0.8;"></div>
                <div>
                  <div class="text-caption text-slate-400 font-weight-black text-uppercase mb-1">Efektivitas Pencapaian</div>
                  <div class="text-h4 font-weight-black" :style="{ color: getProgressColor(selectedAOData.pct) }">
                    {{ pct(selectedAOData.pct) }}
                  </div>
                </div>
                <div class="text-right">
                  <div class="text-caption text-slate-400 font-weight-black text-uppercase mb-1">{{ Number(selectedAOData.target_gap ?? selectedAOData.gap ?? 0) >= 0 ? 'Surplus Target' : 'Sisa Target' }}</div>
                  <div class="text-h6 font-weight-black" :class="Number(selectedAOData.target_gap ?? selectedAOData.gap ?? 0) >= 0 ? 'text-emerald-600' : 'text-rose-600'">
                    {{ Number(selectedAOData.target_gap ?? selectedAOData.gap ?? 0) >= 0 ? '+' : '' }} {{ formatFull(selectedAOData.target_gap ?? selectedAOData.gap ?? 0) }}
                  </div>
                </div>
              </v-card>
            </v-col>
          </v-row>

          <!-- AO Pacing Chart -->
          <v-card elevation="0" border rounded="xl" class="pa-4 bg-white">
            <div class="d-flex align-center gap-2 mb-6">
              <v-icon icon="ri-line-chart-line" color="primary" size="20" />
              <h3 class="text-subtitle-1 font-weight-black text-slate-800 mb-0">Matriks Performa Bulanan</h3>
            </div>
            <VueApexCharts type="bar" height="320" :options="chartOpts" :series="aoChartSeries" />
          </v-card>
        </div>
      </div>
    </v-navigation-drawer>
  </div>
</template>

<style scoped>
.fin-page { background: #f8fafc; min-height: 100vh; }
.text-tiny { font-size: 10px; font-weight: 800; opacity: 0.6; }
.target-money-exact {
  font-size: clamp(0.95rem, 1.08vw, 1.26rem);
  line-height: 1.14;
  letter-spacing: -0.035em;
  white-space: nowrap;
}
.target-money-drawer {
  font-size: clamp(0.78rem, 1.05vw, 1rem) !important;
  line-height: 1.2;
  letter-spacing: -0.03em;
  white-space: nowrap;
}

.target-toolbar {
  display: flex;
  align-items: center;
  gap: 12px;
  flex-wrap: wrap;
  justify-content: flex-end;
}

.target-export-actions {
  display: flex;
  align-items: center;
  gap: 8px;
}

.fin-badge--slate {
  background: rgba(255, 255, 255, 0.12);
  color: #e2e8f0;
  border: 1px solid rgba(255, 255, 255, 0.18);
}

.target-insight-panel {
  display: grid;
  grid-template-columns: minmax(0, 1.35fr) repeat(3, minmax(180px, 0.72fr));
  gap: 16px;
}

.target-insight-card {
  min-height: 116px;
  background: linear-gradient(145deg, #ffffff 0%, #f8fafc 100%);
  border: 1px solid #dbe7f3;
  border-radius: 20px;
  padding: 18px 20px;
  box-shadow: 0 10px 28px rgba(15, 23, 42, 0.06);
  display: flex;
  flex-direction: column;
  justify-content: center;
  gap: 7px;
}

.target-insight-card span {
  color: #64748b;
  font-size: 11px;
  font-weight: 900;
  text-transform: uppercase;
  letter-spacing: 0.06em;
}

.target-insight-card strong {
  color: #0f172a;
  font-family: 'Plus Jakarta Sans', sans-serif;
  font-size: 14px;
  line-height: 1.45;
}

.target-insight-card small {
  color: #64748b;
  font-size: 12px;
  font-weight: 700;
}

.target-insight-card--primary {
  background:
    radial-gradient(circle at top right, rgba(59, 130, 246, 0.18), transparent 34%),
    linear-gradient(145deg, #eff6ff 0%, #ffffff 74%);
  border-color: #bfdbfe;
}

.target-insight-card--primary strong {
  color: #1d4ed8;
  font-size: 15px;
}

.content-card {
  background: white;
  border-radius: 20px;
  border: 1px solid #eef2f6;
  box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05) !important;
  overflow: hidden;
}

.kpi-cards-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
  gap: 20px;
}

.leaderboard-table th {
  background-color: #f8fafc;
  border-bottom: 2px solid #edf2f7 !important;
  font-size: 11px;
  letter-spacing: 0.05em;
  padding: 16px 12px !important;
}

.leaderboard-table td {
  padding: 12px !important;
  border-bottom: 1px solid #f1f5f9 !important;
}

.leaderboard-table tr:hover td {
  background-color: #f1f5f9;
}

.pulse-dot {
  width: 8px;
  height: 8px;
  border-radius: 50%;
  background: #10b981;
  animation: pulse 2s infinite;
}

@keyframes pulse {
  0% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.7); }
  70% { transform: scale(1); box-shadow: 0 0 0 6px rgba(16, 185, 129, 0); }
  100% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(16, 185, 129, 0); }
}

.transition-swing {
  transition: all 0.3s cubic-bezier(0.25, 0.8, 0.5, 1);
}

/* Typography fix for long banking numbers */
.fin-money-exact {
    font-size: 1.75rem;
    font-weight: 900;
    letter-spacing: -0.02em;
    word-break: break-all;
}

@media (max-width: 1180px) {
  .target-insight-panel {
    grid-template-columns: 1fr 1fr;
  }
}

@media (max-width: 720px) {
  .target-toolbar,
  .target-export-actions {
    width: 100%;
  }

  .target-export-actions .v-btn {
    flex: 1;
  }

  .target-insight-panel {
    grid-template-columns: 1fr;
  }
}
</style>
