<script setup>
import { ref, computed, onMounted, watch } from 'vue'
import { Head } from '@inertiajs/vue3'
import DefaultLayout from '@/layouts/default.vue'
import axios from 'axios'
import VueApexCharts from 'vue3-apexcharts'
import { formatExactRupiah } from '@/utils/money'

defineOptions({ layout: DefaultLayout })

// ─── State Management ────────────────────────────────────────
const isLoading = ref(true)
const activeTab = ref(0)
const selectedCabang = ref(null)

const cabangs = ref([])
const segmens = ref(['Semua Segmen', 'Retail', 'Korporasi', 'Mikro'])
const qualityData = ref({
  kolektibilitas: [],
  akad_risk: [],
  aging: [],
  branch_compare: [],
  alerts: [],
  trend: [],
  trend_meta: { data_count: 0, last_bulan: null, filter_tahun: null, filter_bulan: null },
  top_obligor: [],
  ao_matrix: [],
  sector_data: [],
  product_data: [],
  ecl_staging: {
    ckpn_stage_1: 0,
    ckpn_stage_2: 0,
    ckpn_stage_3: 0
  },
  restru_guard: {
    total_os_restru: 0,
    total_kontrak_restru: 0,
    restru_to_total_ratio: 0,
    gagal_kontrak: 0,
    vintage_failure_rate: 0
  },
  stress_test: {
    top5_os: 0,
    top10_os: 0,
    npf_gross_now: 0,
    npf_if_top5_fail: 0,
    npf_if_top10_fail: 0
  },
  summary: {
    total_os: 0,
    total_npf: 0,
    total_ppap: 0,
    npf_gross: 0,
    npf_net: 0,
    coverage_ratio: 0,
    far_ratio: 0,
    top_akad_risk: 'N/A',
    fdr: 0,
    porsi_bagi_hasil: 0,
    composite_score: 1,
    risk_profile: {
      Kredit: 0, Likuiditas: 0, Operasional: 0, Kepatuhan: 0, Reputasi: 0
    }
  }
})

const filters = ref({
  tahun: new Date().getFullYear(),
  bulan: new Date().getMonth() + 1,
  segmen: 'Semua Segmen',
})

const years = computed(() => {
  const current = new Date().getFullYear()
  return [current, current - 1, current - 2, current - 3]
})

const months = [
  { title: 'Januari', value: 1 }, { title: 'Februari', value: 2 }, { title: 'Maret', value: 3 },
  { title: 'April', value: 4 }, { title: 'Mei', value: 5 }, { title: 'Juni', value: 6 },
  { title: 'Juli', value: 7 }, { title: 'Agustus', value: 8 }, { title: 'September', value: 9 },
  { title: 'Oktober', value: 10 }, { title: 'November', value: 11 }, { title: 'Desember', value: 12 }
]

// ─── Computed Properties ─────────────────────────────────────
const summary = computed(() => qualityData.value.summary || {})
const stressTest = computed(() => qualityData.value.stress_test || {})
const restruGuard = computed(() => qualityData.value.restru_guard || {})

const lastAvailableTrendMonth = computed(() => {
  if (qualityData.value.trend_meta?.last_bulan) {
    return qualityData.value.trend_meta.last_bulan
  }
  const trend = qualityData.value.trend || []
  if (trend.length === 0) return null
  return trend[trend.length - 1].bulan
})
const trendDataCount = computed(() => qualityData.value.trend_meta?.data_count ?? (qualityData.value.trend || []).length)
const selectedMonthLabel = computed(() => months.find(m => m.value === filters.value.bulan)?.title || '')
const hasTrendGap = computed(() => {
  const trend = qualityData.value.trend || []
  if (trend.length === 0) return false
  const monthNamesShort = ['Jan','Feb','Mar','Apr','Mei','Jun','Jul','Ags','Sep','Okt','Nov','Des']
  const selectedShort = monthNamesShort[filters.value.bulan - 1]
  return lastAvailableTrendMonth.value !== selectedShort
})

const formatRp = (v) => formatExactRupiah(v)
const formatRpSingkat = (v) => {
  return formatRp(v)
}

const activeFilterText = computed(() => {
  const m = months.find(x => x.value === filters.value.bulan)?.title || ''
  const c = cabangs.value.find(x => x.kdloc === selectedCabang.value)?.nama || 'Konsolidasi Seluruh Cabang'
  return `Menampilkan data: ${c} | Periode ${m} ${filters.value.tahun} | ${filters.value.segmen}`
})

// Chart TAB 1: Radar Chart (Profil Risiko)
const radarChartSeries = computed(() => {
  const profile = summary.value.risk_profile || {}
  return [{
    name: 'Tingkat Risiko',
    data: [
      parseFloat(profile.Kredit) || 0,
      parseFloat(profile.Likuiditas) || 0,
      parseFloat(profile.Operasional) || 0,
      parseFloat(profile.Kepatuhan) || 0,
      parseFloat(profile.Reputasi) || 0
    ]
  }]
})
const radarChartOpts = computed(() => ({
  chart: { type: 'radar', toolbar: { show: false }, fontFamily: "'Plus Jakarta Sans', sans-serif", background: 'transparent' },
  labels: ['Risiko Pembiayaan', 'Risiko Likuiditas', 'Risiko Operasional', 'Risiko Kepatuhan', 'Risiko Reputasi'],
  colors: ['#0d9488'],
  stroke: { width: 2.5, colors: ['#0d9488'] },
  fill: { opacity: 0.15, colors: ['#0d9488'] },
  markers: { size: 5, colors: ['#fff'], strokeColors: '#0d9488', strokeWidth: 2.5 },
  yaxis: { show: false, min: 0, max: 5, tickAmount: 5 },
  plotOptions: { radar: { polygons: { strokeColors: '#e2e8f0', connectorColors: '#e2e8f0', fill: { colors: ['#f8fafc', '#ffffff'] } } } }
}))

// NPF Trend
const trendChartSeries = computed(() => {
  const trendData = qualityData.value.trend || []
  if (trendData.length === 0) return []
  return [
    { name: 'NPF Gross (%)', data: trendData.map(r => r.total_os > 0 ? parseFloat(((r.npf_os / r.total_os) * 100).toFixed(2)) : 0) },
    { name: 'NPF Net (%)', data: trendData.map(r => {
        const netVal = Math.max(0, parseFloat(r.npf_os) - parseFloat(r.total_ppap))
        return r.total_os > 0 ? parseFloat(((netVal / r.total_os) * 100).toFixed(2)) : 0
      })
    }
  ]
})
const trendChartOpts = computed(() => ({
  chart: { type: 'area', toolbar: { show: false }, fontFamily: "'Plus Jakarta Sans', sans-serif", dropShadow: { enabled: true, top: 4, left: 0, blur: 8, opacity: 0.06 } },
  colors: ['#e11d48', '#059669'],
  stroke: { curve: 'smooth', width: 4 },
  fill: { type: 'gradient', gradient: { shadeIntensity: 1, opacityFrom: 0.35, opacityTo: 0.02, stops: [0, 90, 100] } },
  markers: { size: 4, strokeWidth: 2.5, hover: { size: 7 } },
  xaxis: { categories: (qualityData.value.trend || []).map(r => r.bulan), labels: { style: { colors: '#94a3b8', fontWeight: 500, fontSize: '12px' } }, axisBorder: { show: false }, axisTicks: { show: false } },
  yaxis: { labels: { formatter: (v) => v.toFixed(1) + '%', style: { colors: '#94a3b8', fontWeight: 500 } } },
  grid: { borderColor: '#f1f5f9', strokeDashArray: 5, padding: { left: 10, right: 10 } },
  legend: { position: 'top', horizontalAlign: 'right', fontWeight: 600, fontSize: '13px' },
  dataLabels: { enabled: false },
  tooltip: { theme: 'light', y: { formatter: (v) => v.toFixed(2) + '%' } }
}))

// Chart TAB 2: Kolektibilitas (Donut)
const kolChartSeries = computed(() => {
  const data = qualityData.value.kolektibilitas || []
  if (data.length === 0) return []
  const mapKol = { '1': 0, '2': 0, '3': 0, '4': 0, '5': 0 }
  data.forEach(r => {
    if (mapKol[r.kol] !== undefined) mapKol[r.kol] += (parseFloat(r.total_os) || 0)
  })
  return [mapKol['1'], mapKol['2'], mapKol['3'], mapKol['4'], mapKol['5']]
})
const kolChartOpts = computed(() => ({
  chart: { type: 'donut', fontFamily: "'Plus Jakarta Sans', sans-serif" },
  labels: ['Kol 1 (Lancar)', 'Kol 2 (DPK)', 'Kol 3 (Kurang Lancar)', 'Kol 4 (Diragukan)', 'Kol 5 (Macet)'],
  colors: ['#10b981', '#f59e0b', '#f97316', '#ef4444', '#991b1b'],
  plotOptions: { pie: { donut: { size: '72%', labels: { show: true, name: { show: true, fontSize: '13px' }, value: { formatter: (v) => formatRpSingkat(v), fontSize: '15px', fontWeight: '700' }, total: { show: true, showAlways: true, label: 'Total Portofolio', fontSize: '12px', fontWeight: '600', formatter: function (w) { return formatRpSingkat(w.globals.seriesTotals.reduce((a, b) => a + b, 0)) } } } } } },
  dataLabels: { enabled: false },
  legend: { position: 'bottom', horizontalAlign: 'center', fontSize: '12px', fontWeight: 500, markers: { radius: 4 } },
  stroke: { show: true, colors: ['#ffffff'], width: 3 },
  tooltip: { y: { formatter: (val) => formatRp(val) } }
}))

// Chart TAB 3: Sektor Ekonomi (Horizontal Bar)
const sectorChartSeries = computed(() => {
  const data = qualityData.value.sector_data || []
  if (data.length === 0) return []
  const top10 = data.slice(0, 10)
  return [
    { name: 'Total Outstanding', data: top10.map(r => parseFloat(r.total_os) || 0) },
    { name: 'Pembiayaan Bermasalah (NPF)', data: top10.map(r => parseFloat(r.npf_os) || 0) }
  ]
})
const sectorChartOpts = computed(() => ({
  chart: { type: 'bar', stacked: false, toolbar: { show: false }, fontFamily: "'Plus Jakarta Sans', sans-serif" },
  plotOptions: { bar: { horizontal: true, borderRadius: 5, barHeight: '55%', dataLabels: { position: 'top' } } },
  colors: ['#0284c7', '#e11d48'],
  xaxis: { categories: (qualityData.value.sector_data || []).slice(0, 10).map(r => r.sektor), labels: { formatter: (v) => formatRpSingkat(v), style: { fontSize: '11px', colors: '#94a3b8' } } },
  dataLabels: { enabled: false },
  grid: { xaxis: { lines: { show: true } }, yaxis: { lines: { show: false } }, borderColor: '#f1f5f9', strokeDashArray: 5 },
  tooltip: { shared: true, intersect: false, y: { formatter: (v) => formatRp(v) } },
  legend: { position: 'top', horizontalAlign: 'right', fontWeight: 600 }
}))

// Chart TAB 3: Product Composition (Donut)
const productChartSeries = computed(() => {
  const data = qualityData.value.product_data || []
  if (data.length === 0) return []
  return data.map(r => parseFloat(r.total_os) || 0)
})
const productChartOpts = computed(() => ({
  chart: { type: 'donut', fontFamily: "'Plus Jakarta Sans', sans-serif" },
  labels: (qualityData.value.product_data || []).map(r => r.produk),
  colors: ['#4f46e5', '#8b5cf6', '#d946ef', '#0ea5e9', '#14b8a6', '#f59e0b', '#64748b'],
  plotOptions: { pie: { donut: { size: '65%' } } },
  dataLabels: { enabled: false },
  legend: { 
    position: 'bottom', 
    offsetY: 0, 
    fontSize: '12px',
    markers: { width: 8, height: 8, radius: 4 },
    itemMargin: { horizontal: 5, vertical: 2 }
  },
  stroke: { show: true, colors: ['#ffffff'], width: 3 },
  tooltip: { y: { formatter: (val) => formatRp(val) } }
}))

// PK Score formatter
const pkScoreData = computed(() => {
  const pk = summary.value.composite_score || 1
  if (pk <= 2) return { text: `PK-${pk} (Sehat)`, gradient: 'from-emerald-500 to-teal-600', icon: 'ri-shield-check-fill', badge: 'Sehat' }
  if (pk === 3) return { text: `PK-${pk} (Cukup Sehat)`, gradient: 'from-amber-400 to-orange-500', icon: 'ri-shield-star-fill', badge: 'Cukup Sehat' }
  return { text: `PK-${pk} (Kurang Sehat)`, gradient: 'from-rose-500 to-red-600', icon: 'ri-shield-cross-fill', badge: 'Kurang Sehat' }
})

const tabs = [
  { name: 'RGEC & Profil Risiko', icon: 'ri-dashboard-3-line' },
  { name: 'Kualitas & CKPN', icon: 'ri-safe-2-line' },
  { name: 'Konsentrasi Portofolio', icon: 'ri-pie-chart-2-line' },
  { name: 'Restructuring Guard', icon: 'ri-shield-cross-line' }
]

// ─── API Calls ───────────────────────────────────────────────
const fetchCabangs = async () => {
  try {
    const res = await axios.get('/api/v1/financing/cabangs')
    if (res.data.success) cabangs.value = res.data.data
  } catch (e) { console.error(e) }
}

const fetchSegmens = async () => {
  try {
    const res = await axios.get('/api/v1/financing/segmens')
    if (res.data.success) {
      segmens.value = ['Semua Segmen', ...res.data.data.map(item => item.ket)]
    }
  } catch (e) { console.error(e) }
}

const fetchQualityData = async () => {
  isLoading.value = true
  try {
    const res = await axios.get('/api/v1/financing/quality-analytics', {
      params: {
        cabang: selectedCabang.value || '',
        tahun: filters.value.tahun,
        bulan: filters.value.bulan,
        segmen: filters.value.segmen === 'Semua Segmen' ? '' : filters.value.segmen
      }
    })
    if (res.data.success) {
      qualityData.value = { ...qualityData.value, ...res.data.data }
    }
  } catch (e) { console.error(e) }
  finally { isLoading.value = false }
}

onMounted(() => { fetchCabangs(); fetchSegmens(); fetchQualityData(); })
watch([selectedCabang, filters], fetchQualityData, { deep: true })
</script>

<template>
  <div class="quality-console">
    <Head title="RGEC Quality & Risk Console" />

    <!-- ══════════════════════════════════════════
         HERO HEADER
    ══════════════════════════════════════════ -->
    <div class="hero-header">
      <div class="hero-bg-decoration"></div>
      <div class="hero-content max-w-7xl mx-auto px-6 py-8">

        <!-- Top Row: Identity + PK Badge -->
        <div class="d-flex flex-wrap justify-space-between align-start gap-6 mb-8">
          <div class="d-flex align-start gap-5">
            <div class="hero-icon-box">
              <v-icon icon="ri-scales-3-line" size="32" color="white"></v-icon>
            </div>
            <div>
              <div class="d-flex align-center gap-2 mb-2">
                <span class="badge-islamic">
                  <v-icon icon="ri-bank-line" size="11" class="mr-1"></v-icon>
                  ISLAMIC BANKING
                </span>
                <span class="badge-compliant">
                  <v-icon icon="ri-verified-badge-line" size="11" class="mr-1"></v-icon>
                  OJK / PSAK 71 Compliant
                </span>
              </div>
              <h1 class="hero-title">Kualitas Aktiva &amp; Mitigasi Risiko</h1>
              <p class="hero-subtitle">Konsol Konsentrasi Risiko, Profil RGEC, dan Pencadangan Kerugian BPRS.</p>
            </div>
          </div>

          <!-- PK Rating Badge -->
          <div class="pk-badge-wrapper">
            <div class="pk-label">Peringkat Komposit OJK</div>
            <div :class="['pk-badge', `pk-gradient-${summary.composite_score <= 2 ? 'sehat' : summary.composite_score === 3 ? 'cukup' : 'kurang'}`]">
              <v-icon :icon="pkScoreData.icon" size="22" color="white"></v-icon>
              <span class="pk-text">{{ pkScoreData.text }}</span>
            </div>
          </div>
        </div>

        <!-- Filter Bar -->
        <div class="filter-bar">
          <div class="filter-group">
            <div class="filter-icon-wrap">
              <v-icon icon="ri-calendar-2-line" size="16" color="#64748b"></v-icon>
            </div>
            <v-select
              v-model="filters.tahun"
              :items="years"
              label="Tahun"
              density="compact"
              variant="solo"
              flat
              hide-details
              class="filter-select"
              style="min-width: 110px"
            ></v-select>
          </div>

          <div class="filter-divider"></div>

          <div class="filter-group">
            <div class="filter-icon-wrap">
              <v-icon icon="ri-time-line" size="16" color="#64748b"></v-icon>
            </div>
            <v-select
              v-model="filters.bulan"
              :items="months"
              item-title="title"
              item-value="value"
              label="Bulan Tutup Buku"
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
              label="Konsolidasi / Cabang"
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
              <v-icon icon="ri-user-star-line" size="16" color="#64748b"></v-icon>
            </div>
            <v-select
              v-model="filters.segmen"
              :items="segmens"
              label="Segmen"
              density="compact"
              variant="solo"
              flat
              hide-details
              class="filter-select"
              style="min-width: 150px"
            ></v-select>
          </div>
        </div>

        <!-- Active Filter Info -->
        <div class="filter-info-bar mt-3">
          <v-icon icon="ri-information-line" size="13" color="#94a3b8" class="mr-1"></v-icon>
          <span>{{ activeFilterText }}</span>
        </div>

      </div>
    </div>

    <!-- ══════════════════════════════════════════
         MAIN CONTENT
    ══════════════════════════════════════════ -->
    <div class="main-content max-w-7xl mx-auto px-6 pt-7 pb-16">

      <!-- Tab Navigation -->
      <div class="tab-nav mb-8">
        <button
          v-for="(tab, idx) in tabs"
          :key="idx"
          :class="['tab-btn', { 'tab-btn--active': activeTab === idx }]"
          @click="activeTab = idx"
        >
          <v-icon :icon="tab.icon" size="17" class="mr-2"></v-icon>
          {{ tab.name }}
        </button>
      </div>

      <v-window v-model="activeTab" class="overflow-visible" :touch="false">

        <!-- ══════════════════════════════════
             TAB 1: RGEC & RISK PROFILE
        ══════════════════════════════════ -->
        <v-window-item :value="0">

          <!-- KPI Cards -->
          <v-row class="mb-6">
            <v-col cols="12" sm="6" lg="3">
              <v-card class="rounded-xl border shadow-sm transition-swing h-100" elevation="0" style="position: relative; overflow: hidden;">
                <div style="position: absolute; top: -20px; right: -20px; width: 120px; height: 120px; opacity: 0.08;">
                  <v-icon icon="ri-wallet-3-line" size="120" color="#059669" />
                </div>
                <v-card-text class="pa-5" style="position: relative; z-index: 1;">
                  <div class="d-flex justify-space-between align-start">
                    <div>
                      <p class="text-caption font-weight-bold text-uppercase tracking-widest mb-1" style="color: #64748B; font-family: 'Inter', sans-serif;">TOTAL PEMBIAYAAN</p>
                      <h2 class="text-h4 font-weight-bold mb-2" style="color: #059669; font-family: 'Plus Jakarta Sans', sans-serif; line-height: 1.2;">
                        <span class="fin-money-exact">{{ formatRpSingkat(summary.total_os) }}</span>
                      </h2>
                      <p class="text-caption text-medium-emphasis mb-0" style="font-family: 'Inter', sans-serif;">Porsi Bagi Hasil: <strong>{{ summary.porsi_bagi_hasil || 0 }}%</strong></p>
                    </div>
                  </div>
                </v-card-text>
              </v-card>
            </v-col>

            <v-col cols="12" sm="6" lg="3">
              <v-card class="rounded-xl border shadow-sm transition-swing h-100" elevation="0" style="position: relative; overflow: hidden;">
                <div style="position: absolute; top: -20px; right: -20px; width: 120px; height: 120px; opacity: 0.08;">
                  <v-icon icon="ri-error-warning-line" size="120" :color="(summary.npf_gross || 0) > 5 ? '#e11d48' : '#059669'" />
                </div>
                <v-card-text class="pa-5" style="position: relative; z-index: 1;">
                  <div class="d-flex justify-space-between align-start">
                    <div>
                      <p class="text-caption font-weight-bold text-uppercase tracking-widest mb-1" style="color: #64748B; font-family: 'Inter', sans-serif;">NPF GROSS</p>
                      <h2 class="text-h4 font-weight-bold mb-2" :style="{ color: (summary.npf_gross || 0) > 5 ? '#e11d48' : '#059669', fontFamily: 'Plus Jakarta Sans, sans-serif', lineHeight: 1.2 }">
                        {{ summary.npf_gross || 0 }}%
                      </h2>
                      <p class="text-caption text-medium-emphasis mb-0" style="font-family: 'Inter', sans-serif;">NPF Net: <strong>{{ summary.npf_net || 0 }}%</strong></p>
                    </div>
                  </div>
                </v-card-text>
              </v-card>
            </v-col>

            <v-col cols="12" sm="6" lg="3">
              <v-card class="rounded-xl border shadow-sm transition-swing h-100" elevation="0" style="position: relative; overflow: hidden;">
                <div style="position: absolute; top: -20px; right: -20px; width: 120px; height: 120px; opacity: 0.08;">
                  <v-icon icon="ri-shield-keyhole-line" size="120" :color="(summary.coverage_ratio || 0) < 100 ? '#d97706' : '#0284c7'" />
                </div>
                <v-card-text class="pa-5" style="position: relative; z-index: 1;">
                  <div class="d-flex justify-space-between align-start">
                    <div>
                      <p class="text-caption font-weight-bold text-uppercase tracking-widest mb-1" style="color: #64748B; font-family: 'Inter', sans-serif;">CKPN COVERAGE</p>
                      <h2 class="text-h4 font-weight-bold mb-2" :style="{ color: (summary.coverage_ratio || 0) < 100 ? '#d97706' : '#0284c7', fontFamily: 'Plus Jakarta Sans, sans-serif', lineHeight: 1.2 }">
                        {{ summary.coverage_ratio || 0 }}%
                      </h2>
                      <p class="text-caption text-medium-emphasis mb-0" style="font-family: 'Inter', sans-serif;">Coverage Ratio</p>
                    </div>
                  </div>
                </v-card-text>
              </v-card>
            </v-col>

            <v-col cols="12" sm="6" lg="3">
              <v-card class="rounded-xl border shadow-sm transition-swing h-100" elevation="0" style="position: relative; overflow: hidden;">
                <div style="position: absolute; top: -20px; right: -20px; width: 120px; height: 120px; opacity: 0.08;">
                  <v-icon icon="ri-water-flash-line" size="120" color="#4f46e5" />
                </div>
                <v-card-text class="pa-5" style="position: relative; z-index: 1;">
                  <div class="d-flex justify-space-between align-start">
                    <div>
                      <p class="text-caption font-weight-bold text-uppercase tracking-widest mb-1" style="color: #64748B; font-family: 'Inter', sans-serif;">FDR (LIKUIDITAS)</p>
                      <h2 class="text-h4 font-weight-bold mb-2" style="color: #4f46e5; font-family: 'Plus Jakarta Sans', sans-serif; line-height: 1.2;">{{ summary.fdr || 0 }}%</h2>
                      <p class="text-caption text-medium-emphasis mb-0" style="font-family: 'Inter', sans-serif;">Target: 75% – 85%</p>
                    </div>
                  </div>
                </v-card-text>
              </v-card>
            </v-col>
          </v-row>

          <!-- Charts Row -->
          <v-row>
            <!-- NPF Trend Chart -->
            <v-col cols="12" lg="8" class="d-flex flex-column">
              <div class="content-card flex-grow-1">
                <div class="content-card__header">
                  <div>
                    <div class="content-card__title">Tren Pembiayaan Bermasalah (NPF)</div>
                    <div class="content-card__subtitle" v-if="lastAvailableTrendMonth">
                      Pergerakan Gross vs Net — Jan s/d
                      <strong :style="hasTrendGap ? 'color: #d97706;' : 'color: #059669;'">
                        {{ lastAvailableTrendMonth }} {{ filters.tahun }}
                      </strong>
                      <span v-if="hasTrendGap" class="ml-2">
                        <em style="color: #d97706; font-size: 11px;">(data EOM s/d {{ lastAvailableTrendMonth }})</em>
                      </span>
                    </div>
                    <div class="content-card__subtitle" v-else>Memuat data tren...</div>
                  </div>
                  <div class="d-flex align-center gap-2">
                    <span v-if="hasTrendGap" class="status-chip status-chip--warning">
                      <v-icon icon="ri-error-warning-line" size="11" class="mr-1"></v-icon>
                      Data s/d {{ lastAvailableTrendMonth }}
                    </span>
                    <span class="status-chip status-chip--neutral">
                      <v-icon icon="ri-calendar-check-line" size="12" class="mr-1"></v-icon>
                      {{ filters.tahun }}
                    </span>
                  </div>
                </div>
                <div class="content-card__body pa-2">
                  <div v-if="isLoading" class="chart-loading"><v-progress-circular indeterminate color="#0d9488" size="36"></v-progress-circular></div>
                  <VueApexCharts v-else-if="qualityData.trend && qualityData.trend.length" type="area" height="320" :options="trendChartOpts" :series="trendChartSeries" />
                  <div v-else class="empty-state">
                    <v-icon icon="ri-bar-chart-grouped-line" size="48" color="#cbd5e1" class="mb-3"></v-icon>
                    <p>Data tren historis belum tersedia</p>
                  </div>
                </div>
              </div>
            </v-col>

            <!-- Risk Radar -->
            <v-col cols="12" lg="4" class="d-flex flex-column">
              <div class="content-card flex-grow-1">
                <div class="content-card__accent-top" style="background: linear-gradient(90deg, #6366f1, #8b5cf6);"></div>
                <div class="content-card__header">
                  <div>
                    <div class="content-card__title">Pemetaan Risiko Inheren</div>
                    <div class="content-card__subtitle">Analisis 5 Pilar Risiko Perbankan</div>
                  </div>
                  <div class="icon-badge" style="background: #eef2ff; color: #4f46e5;">
                    <v-icon icon="ri-radar-line" size="18"></v-icon>
                  </div>
                </div>
                <div class="content-card__body d-flex justify-center align-center" style="min-height: 320px;">
                  <div v-if="isLoading" class="chart-loading"><v-progress-circular indeterminate color="#6366f1" size="36"></v-progress-circular></div>
                  <div v-else class="w-100">
                    <VueApexCharts type="radar" height="320" width="100%" :options="radarChartOpts" :series="radarChartSeries" />
                  </div>
                </div>
              </div>
            </v-col>
          </v-row>

          <!-- Stress Test Panel -->
          <div class="stress-test-panel mt-6">
            <div class="stress-test-panel__bg-icon">
              <v-icon icon="ri-alarm-warning-fill" size="160" color="white"></v-icon>
            </div>
            <div class="stress-test-panel__content">
              <div class="d-flex align-center gap-4 mb-6">
                <div class="stress-test-avatar">
                  <v-icon icon="ri-flask-line" size="26" color="white"></v-icon>
                </div>
                <div>
                  <h3 class="stress-test-title">Simulasi Gagal Bayar Top Obligor (BMPK Stress Test)</h3>
                  <p class="stress-test-desc">Pengujian ketahanan kualitas aset jika debitur raksasa mengalami default seketika.</p>
                </div>
              </div>

              <v-row>
                <v-col cols="12" md="6">
                  <div class="stress-scenario-card">
                    <div class="stress-scenario-card__label">
                      <span class="scenario-badge">Skenario 1</span>
                      Top 5 Debitur Macet
                    </div>
                    <div class="d-flex justify-space-between align-end mt-4 mb-4">
                      <div>
                        <div class="stress-sub-label">Exposure (O/S)</div>
                        <div class="stress-value-primary">{{ formatRpSingkat(stressTest.top5_os) }}</div>
                      </div>
                      <div class="text-right">
                        <div class="stress-sub-label">Lonjakan NPF Gross</div>
                        <div class="d-flex align-center gap-2 mt-1">
                          <span class="npf-before">{{ stressTest.npf_gross_now }}%</span>
                          <v-icon icon="ri-arrow-right-line" size="14" color="#fca5a5"></v-icon>
                          <span class="npf-after npf-after--warn">{{ stressTest.npf_if_top5_fail }}%</span>
                        </div>
                      </div>
                    </div>
                    <div class="stress-progress-track">
                      <div class="stress-progress-fill" :style="`width: ${Math.min(stressTest.npf_if_top5_fail * 5, 100)}%; background: linear-gradient(90deg, #f87171, #ef4444);`"></div>
                    </div>
                  </div>
                </v-col>
                <v-col cols="12" md="6">
                  <div class="stress-scenario-card stress-scenario-card--critical">
                    <div class="stress-scenario-card__label">
                      <span class="scenario-badge scenario-badge--critical">Skenario 2</span>
                      Top 10 Debitur Macet
                    </div>
                    <div class="d-flex justify-space-between align-end mt-4 mb-4">
                      <div>
                        <div class="stress-sub-label">Exposure (O/S)</div>
                        <div class="stress-value-primary">{{ formatRpSingkat(stressTest.top10_os) }}</div>
                      </div>
                      <div class="text-right">
                        <div class="stress-sub-label">Lonjakan NPF Gross</div>
                        <div class="d-flex align-center gap-2 mt-1">
                          <span class="npf-before">{{ stressTest.npf_gross_now }}%</span>
                          <v-icon icon="ri-arrow-right-line" size="14" color="#fca5a5"></v-icon>
                          <span class="npf-after npf-after--critical">{{ stressTest.npf_if_top10_fail }}%</span>
                        </div>
                      </div>
                    </div>
                    <div class="stress-progress-track">
                      <div class="stress-progress-fill" :style="`width: ${Math.min(stressTest.npf_if_top10_fail * 5, 100)}%; background: linear-gradient(90deg, #dc2626, #991b1b);`"></div>
                    </div>
                  </div>
                </v-col>
              </v-row>
            </div>
          </div>

        </v-window-item>

        <!-- ══════════════════════════════════
             TAB 2: KUALITAS ASET & CKPN
        ══════════════════════════════════ -->
        <v-window-item :value="1">
          <v-row class="mb-6">
            <!-- Kolektibilitas Donut -->
            <v-col cols="12" lg="4">
              <div class="content-card h-100">
                <div class="content-card__accent-top" style="background: linear-gradient(90deg, #10b981, #059669);"></div>
                <div class="content-card__header">
                  <div>
                    <div class="content-card__title">Distribusi Kolektibilitas</div>
                    <div class="content-card__subtitle">Berdasarkan Ketentuan OJK</div>
                  </div>
                  <div class="icon-badge" style="background: #ecfdf5; color: #059669;">
                    <v-icon icon="ri-donut-chart-line" size="18"></v-icon>
                  </div>
                </div>
                <div class="content-card__body d-flex justify-center align-center" style="min-height: 300px;">
                  <div v-if="isLoading" class="chart-loading"><v-progress-circular indeterminate color="#10b981" size="36"></v-progress-circular></div>
                  <VueApexCharts v-else-if="qualityData.kolektibilitas && qualityData.kolektibilitas.length" type="donut" height="320" :options="kolChartOpts" :series="kolChartSeries" class="w-100" />
                  <div v-else class="empty-state"><p>Data tidak tersedia</p></div>
                </div>
              </div>
            </v-col>

            <!-- Aging & ECL Table -->
            <v-col cols="12" lg="8">
              <div class="content-card h-100">
                <div class="content-card__accent-top" style="background: linear-gradient(90deg, #0ea5e9, #0284c7);"></div>
                <div class="content-card__header">
                  <div>
                    <div class="content-card__title">Aging Bucket &amp; Pencadangan (ECL PSAK 71)</div>
                    <div class="content-card__subtitle">Matriks penuaan tunggakan dan alokasi CKPN per Stage</div>
                  </div>
                  <div class="icon-badge" style="background: #f0f9ff; color: #0284c7;">
                    <v-icon icon="ri-stack-line" size="18"></v-icon>
                  </div>
                </div>

                <div v-if="isLoading" class="pa-16 text-center"><v-progress-circular indeterminate color="#0ea5e9"></v-progress-circular></div>
                <div v-else-if="qualityData.aging && qualityData.aging.length" class="pa-0">
                  <!-- Stage 1 -->
                  <div class="aging-row aging-row--stage1">
                    <div class="aging-row__indicator" style="background: #10b981;"></div>
                    <div class="aging-row__body">
                      <div class="d-flex align-center gap-3">
                        <div class="aging-avatar" style="background: #ecfdf5; color: #10b981;">
                          <v-icon icon="ri-check-double-line" size="16"></v-icon>
                        </div>
                        <div>
                          <div class="aging-cat-name">Lancar</div>
                          <div class="aging-cat-sub">0 Hari — Belum Menunggak</div>
                        </div>
                      </div>
                      <div class="aging-row__financials">
                        <div class="aging-os">{{ formatRp(qualityData.aging.reduce((a,b) => a + (parseFloat(b.aging_0) || 0), 0)) }}</div>
                        <div class="aging-ecl-wrap">
                          <div class="aging-ecl-label">Stage 1 — 12-Month ECL</div>
                          <div class="aging-ecl-chip aging-ecl-chip--stage1">{{ formatRpSingkat(qualityData.ecl_staging?.ckpn_stage_1) }}</div>
                        </div>
                      </div>
                    </div>
                  </div>

                  <!-- Stage 2 -->
                  <div class="aging-row aging-row--stage2">
                    <div class="aging-row__indicator" style="background: #f59e0b;"></div>
                    <div class="aging-row__body">
                      <div class="d-flex align-center gap-3">
                        <div class="aging-avatar" style="background: #fffbeb; color: #d97706;">
                          <v-icon icon="ri-error-warning-line" size="16"></v-icon>
                        </div>
                        <div>
                          <div class="aging-cat-name">Dalam Perhatian Khusus</div>
                          <div class="aging-cat-sub">1 – 90 Hari (SICR)</div>
                        </div>
                      </div>
                      <div class="aging-row__financials">
                        <div class="aging-os" style="color: #d97706;">{{ formatRp(qualityData.aging.reduce((a,b) => a + (parseFloat(b.aging_1_30)||0) + (parseFloat(b.aging_31_60)||0) + (parseFloat(b.aging_61_90)||0), 0)) }}</div>
                        <div class="aging-ecl-wrap">
                          <div class="aging-ecl-label">Stage 2 — Lifetime ECL</div>
                          <div class="aging-ecl-chip aging-ecl-chip--stage2">{{ formatRpSingkat(qualityData.ecl_staging?.ckpn_stage_2) }}</div>
                        </div>
                      </div>
                    </div>
                  </div>

                  <!-- Stage 3 -->
                  <div class="aging-row aging-row--stage3">
                    <div class="aging-row__indicator" style="background: #e11d48;"></div>
                    <div class="aging-row__body">
                      <div class="d-flex align-center gap-3">
                        <div class="aging-avatar" style="background: #fff1f2; color: #e11d48;">
                          <v-icon icon="ri-close-circle-line" size="16"></v-icon>
                        </div>
                        <div>
                          <div class="aging-cat-name" style="color: #be123c;">Non-Performing (NPF)</div>
                          <div class="aging-cat-sub" style="color: #f43f5e;">> 90 Hari — Credit Impaired</div>
                        </div>
                      </div>
                      <div class="aging-row__financials">
                        <div class="aging-os" style="color: #e11d48; font-weight: 800;">{{ formatRp(qualityData.aging.reduce((a,b) => a + (parseFloat(b.aging_npf) || 0), 0)) }}</div>
                        <div class="aging-ecl-wrap">
                          <div class="aging-ecl-label" style="color: #f43f5e;">Stage 3 — Lifetime ECL</div>
                          <div class="aging-ecl-chip aging-ecl-chip--stage3">{{ formatRpSingkat(qualityData.ecl_staging?.ckpn_stage_3) }}</div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </v-col>
          </v-row>

          <!-- AO Matrix Table -->
          <div class="content-card">
            <div class="content-card__header">
              <div>
                <div class="content-card__title">Matriks Performa Kualitas Aset per Account Officer</div>
                <div class="content-card__subtitle">Monitoring tingkat NPF berdasarkan portfolio kelolaan masing-masing tenaga pemasar.</div>
              </div>
              <button class="export-btn">
                <v-icon icon="ri-download-2-line" size="14" class="mr-1"></v-icon>
                Export CSV
              </button>
            </div>

            <div class="pa-0 overflow-x-auto">
              <table v-if="!isLoading && qualityData.ao_matrix && qualityData.ao_matrix.length" class="data-table">
                <thead>
                  <tr>
                    <th class="text-left">Nama Account Officer</th>
                    <th class="text-right">Kelolaan (O/S)</th>
                    <th class="text-right" style="color: #10b981;">Kol 1</th>
                    <th class="text-right" style="color: #d97706;">Kol 2</th>
                    <th class="text-right" style="color: #ea580c;">Kol 3</th>
                    <th class="text-right" style="color: #f43f5e;">Kol 4</th>
                    <th class="text-right" style="color: #be123c;">Kol 5</th>
                    <th class="text-center">Rasio NPF</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="item in qualityData.ao_matrix" :key="item.nama_ao" :class="{ 'row--danger': item.npf_ratio > 5 }">
                    <td>
                      <div class="d-flex align-center gap-3">
                        <div class="ao-avatar" :class="item.npf_ratio > 5 ? 'ao-avatar--danger' : ''">
                          {{ item.nama_ao.substring(0,2).toUpperCase() }}
                        </div>
                        <span class="font-weight-semibold" :style="item.npf_ratio > 5 ? 'color: #be123c;' : 'color: #334155;'">{{ item.nama_ao }}</span>
                      </div>
                    </td>
                    <td class="text-right font-weight-medium" style="color: #475569;">{{ formatRpSingkat(item.total_os) }}</td>
                    <td class="text-right" style="color: #059669; font-weight: 600;">{{ item.total_os > 0 ? ((item.kol1_os / item.total_os) * 100).toFixed(1) : 0 }}%</td>
                    <td class="text-right" style="color: #d97706; font-weight: 600;">{{ item.total_os > 0 ? ((item.kol2_os / item.total_os) * 100).toFixed(1) : 0 }}%</td>
                    <td class="text-right" style="color: #ea580c; font-weight: 600;">{{ item.total_os > 0 ? ((item.kol3_os / item.total_os) * 100).toFixed(1) : 0 }}%</td>
                    <td class="text-right" style="color: #f43f5e; font-weight: 600;">{{ item.total_os > 0 ? ((item.kol4_os / item.total_os) * 100).toFixed(1) : 0 }}%</td>
                    <td class="text-right" style="color: #be123c; font-weight: 700;">{{ item.total_os > 0 ? ((item.kol5_os / item.total_os) * 100).toFixed(1) : 0 }}%</td>
                    <td class="text-center">
                      <span class="npf-pill" :class="item.npf_ratio > 5 ? 'npf-pill--danger' : 'npf-pill--safe'">
                        {{ item.npf_ratio }}%
                      </span>
                    </td>
                  </tr>
                </tbody>
              </table>
              <div v-else-if="isLoading" class="pa-16 text-center"><v-progress-circular indeterminate color="#0d9488"></v-progress-circular></div>
              <div v-else class="empty-state pa-16">
                <v-icon icon="ri-group-line" size="48" color="#cbd5e1" class="mb-3"></v-icon>
                <p>Data Account Officer tidak tersedia.</p>
              </div>
            </div>
          </div>
        </v-window-item>

        <!-- ══════════════════════════════════
             TAB 3: KONSENTRASI PORTOFOLIO
        ══════════════════════════════════ -->
        <v-window-item :value="2">
          <v-row>
            <!-- Sector Chart -->
            <v-col cols="12" md="7" lg="8">
              <div class="content-card h-100">
                <div class="content-card__header">
                  <div>
                    <div class="content-card__title">Distribusi Sektor Ekonomi</div>
                    <div class="content-card__subtitle">Top 10 Sektor dengan exposure terbesar vs NPF</div>
                  </div>
                  <div class="icon-badge" style="background: #f0f9ff; color: #0284c7;">
                    <v-icon icon="ri-building-2-line" size="18"></v-icon>
                  </div>
                </div>
                <div class="content-card__body pa-4">
                  <div v-if="isLoading" class="chart-loading"><v-progress-circular indeterminate color="#0284c7" size="36"></v-progress-circular></div>
                  <VueApexCharts v-else-if="qualityData.sector_data && qualityData.sector_data.length" type="bar" height="450" :options="sectorChartOpts" :series="sectorChartSeries" />
                  <div v-else class="empty-state py-16">
                    <v-icon icon="ri-building-2-line" size="48" color="#cbd5e1" class="mb-3"></v-icon>
                    <p>Data Sektor tidak tersedia</p>
                  </div>
                </div>
              </div>
            </v-col>

            <!-- Right Column -->
            <v-col cols="12" md="5" lg="4">
              <!-- Product Donut -->
              <div class="content-card mb-6">
                <div class="content-card__header">
                  <div class="content-card__title">Komposisi Akad / Produk</div>
                </div>
                <div class="content-card__body d-flex justify-center align-center" style="min-height: 280px;">
                  <div v-if="isLoading" class="chart-loading"><v-progress-circular indeterminate color="#6366f1" size="36"></v-progress-circular></div>
                  <VueApexCharts v-else-if="qualityData.product_data && qualityData.product_data.length" type="donut" height="300" :options="productChartOpts" :series="productChartSeries" class="w-100" />
                  <div v-else class="empty-state"><p>Data Produk tidak tersedia</p></div>
                </div>
              </div>

              <!-- Top Akad Berisiko -->
              <div class="akad-risk-card">
                <div class="akad-risk-card__icon">
                  <v-icon icon="ri-pie-chart-box-line" size="22" color="white"></v-icon>
                </div>
                <div class="akad-risk-card__content">
                  <div class="akad-risk-card__label">Akad Paling Berisiko</div>
                  <div class="akad-risk-card__value">{{ summary.top_akad_risk || 'N/A' }}</div>
                  <div class="akad-risk-card__desc">Berdasarkan nominal NPF terbesar secara absolut pada portofolio saat ini.</div>
                </div>
              </div>
            </v-col>
          </v-row>
        </v-window-item>

        <!-- ══════════════════════════════════
             TAB 4: RESTRUCTURING GUARD
        ══════════════════════════════════ -->
        <v-window-item :value="3">

          <!-- Restru Metrics -->
          <v-row class="mb-6">
            <v-col cols="12" md="4">
              <v-card class="rounded-xl border shadow-sm transition-swing h-100" elevation="0" style="position: relative; overflow: hidden;">
                <div style="position: absolute; top: -20px; right: -20px; width: 120px; height: 120px; opacity: 0.08;">
                  <v-icon icon="ri-file-damage-line" size="120" color="#d97706" />
                </div>
                <v-card-text class="pa-5" style="position: relative; z-index: 1;">
                  <div class="d-flex justify-space-between align-start">
                    <div>
                      <p class="text-caption font-weight-bold text-uppercase tracking-widest mb-1" style="color: #64748B; font-family: 'Inter', sans-serif;">TOTAL O/S RESTRUKTURISASI</p>
                      <h2 class="text-h4 font-weight-bold mb-2" style="color: #d97706; font-family: 'Plus Jakarta Sans', sans-serif; line-height: 1.2;">{{ formatRpSingkat(restruGuard.total_os_restru) }}</h2>
                      <p class="text-caption text-medium-emphasis mb-0" style="font-family: 'Inter', sans-serif;"><strong style="color: #334155;">{{ restruGuard.total_kontrak_restru }} kontrak</strong> direstrukturisasi</p>
                    </div>
                  </div>
                </v-card-text>
              </v-card>
            </v-col>

            <v-col cols="12" md="4">
              <v-card class="rounded-xl border shadow-sm transition-swing h-100" elevation="0" style="position: relative; overflow: hidden;">
                <div style="position: absolute; top: -20px; right: -20px; width: 120px; height: 120px; opacity: 0.08;">
                  <v-icon icon="ri-pie-chart-line" size="120" color="#0284c7" />
                </div>
                <v-card-text class="pa-5" style="position: relative; z-index: 1;">
                  <div class="d-flex justify-space-between align-start">
                    <div>
                      <p class="text-caption font-weight-bold text-uppercase tracking-widest mb-1" style="color: #64748B; font-family: 'Inter', sans-serif;">RESTRU-TO-TOTAL RATIO</p>
                      <h2 class="text-h4 font-weight-bold mb-2" style="color: #0284c7; font-family: 'Plus Jakarta Sans', sans-serif; line-height: 1.2;">{{ restruGuard.restru_to_total_ratio || 0 }}%</h2>
                      <p class="text-caption text-medium-emphasis mb-0" style="font-family: 'Inter', sans-serif;">Porsi fasilitas restrukturisasi</p>
                    </div>
                  </div>
                </v-card-text>
              </v-card>
            </v-col>

            <v-col cols="12" md="4">
              <v-card class="rounded-xl border shadow-sm transition-swing h-100" elevation="0" style="position: relative; overflow: hidden;">
                <div style="position: absolute; top: -20px; right: -20px; width: 120px; height: 120px; opacity: 0.08;">
                  <v-icon icon="ri-pulse-line" size="120" :color="(restruGuard.vintage_failure_rate || 0) > 10 ? '#e11d48' : '#059669'" />
                </div>
                <v-card-text class="pa-5" style="position: relative; z-index: 1;">
                  <div class="d-flex justify-space-between align-start">
                    <div>
                      <p class="text-caption font-weight-bold text-uppercase tracking-widest mb-1" style="color: #64748B; font-family: 'Inter', sans-serif;">VINTAGE FAILURE RATE</p>
                      <h2 class="text-h4 font-weight-bold mb-2"
                          :style="{ color: (restruGuard.vintage_failure_rate || 0) > 10 ? '#e11d48' : '#059669', fontFamily: 'Plus Jakarta Sans, sans-serif', lineHeight: 1.2 }">
                        {{ restruGuard.vintage_failure_rate || 0 }}%
                      </h2>
                      <p class="text-caption text-medium-emphasis mb-0" style="font-family: 'Inter', sans-serif;">{{ (restruGuard.vintage_failure_rate || 0) > 10 ? 'Indikasi Evergreening' : 'Kualitas Sehat' }}</p>
                    </div>
                  </div>
                </v-card-text>
              </v-card>
            </v-col>
          </v-row>

          <!-- Watchlist Table -->
          <div class="watchlist-card">
            <div class="watchlist-card__header">
              <div class="watchlist-card__header-inner">
                <div class="watchlist-icon">
                  <v-icon icon="ri-spy-line" size="22" color="white"></v-icon>
                </div>
                <div>
                  <div class="watchlist-title">Watchlist: Top High-Risk Obligors (Kol 3–5)</div>
                  <div class="watchlist-subtitle">Daftar debitur dengan eksposur bermasalah tertinggi yang memerlukan penanganan khusus.</div>
                </div>
              </div>
            </div>

            <div class="pa-0 overflow-x-auto">
              <table v-if="!isLoading && qualityData.alerts && qualityData.alerts.length" class="data-table watchlist-table">
                <thead>
                  <tr>
                    <th class="text-left">Nasabah / Fasilitas</th>
                    <th class="text-left">Akad</th>
                    <th class="text-right">Baki Debet (O/S)</th>
                    <th class="text-center">Kolektibilitas</th>
                    <th class="text-center">Menunggak</th>
                    <th class="text-right">Cover Agunan / PPAP</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="item in qualityData.alerts" :key="item.nokontrak" class="watchlist-row">
                    <td>
                      <div class="font-weight-bold text-slate-800" style="font-size: 14px;">{{ item.nama }}</div>
                      <div class="d-flex align-center gap-1 mt-1" style="color: #94a3b8; font-size: 11px; font-family: monospace;">
                        <v-icon icon="ri-file-list-3-line" size="11"></v-icon>
                        {{ item.nokontrak }}
                      </div>
                    </td>
                    <td style="color: #475569; font-size: 13px; font-weight: 500;">{{ item.jenis_akad }}</td>
                    <td class="text-right font-weight-bold" style="color: #334155; font-size: 14px;">{{ formatRp(item.osmdlc) }}</td>
                    <td class="text-center">
                      <span class="kol-badge" :class="item.colbaru === '5' ? 'kol-badge--5' : item.colbaru === '4' ? 'kol-badge--4' : 'kol-badge--3'">
                        KOL {{ item.colbaru }}
                      </span>
                    </td>
                    <td class="text-center">
                      <span class="tunggak-badge">
                        <v-icon icon="ri-time-line" size="12" class="mr-1"></v-icon>
                        {{ item.haritgk }} Hari
                      </span>
                    </td>
                    <td class="text-right">
                      <div style="font-size: 12px; color: #64748b; margin-bottom: 2px;">
                        Agunan: <strong style="color: #334155;">{{ formatRpSingkat(item.htgagun) }}</strong>
                      </div>
                      <div style="font-size: 12px; color: #64748b;">
                        PPAP: <strong style="color: #334155;">{{ formatRpSingkat(item.ppap) }}</strong>
                      </div>
                    </td>
                  </tr>
                </tbody>
              </table>
              <div v-else-if="isLoading" class="pa-16 text-center"><v-progress-circular indeterminate color="#0d9488"></v-progress-circular></div>
              <div v-else class="empty-state pa-16">
                <v-icon icon="ri-shield-check-line" size="56" color="#10b981" class="mb-3" style="opacity: 0.4;"></v-icon>
                <p style="color: #94a3b8; font-size: 15px; font-weight: 500;">Portofolio bersih. Tidak ada obligor berisiko tinggi saat ini.</p>
              </div>
            </div>
          </div>

        </v-window-item>

      </v-window>
    </div>
  </div>
</template>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap');

/* ─── Base ───────────────────────────────────────── */
* { box-sizing: border-box; }
.quality-console {
  font-family: 'Inter', sans-serif;
  background: #f1f5f9;
  min-height: 100vh;
}

/* ─── Hero Header ─────────────────────────────────── */
.hero-header {
  background: linear-gradient(135deg, #0f172a 0%, #1e293b 50%, #0f3460 100%);
  position: relative;
  overflow: hidden;
  border-bottom: 1px solid rgba(255,255,255,0.06);
}
.hero-bg-decoration {
  position: absolute;
  inset: 0;
  background: radial-gradient(ellipse at 70% -10%, rgba(99, 102, 241, 0.18) 0%, transparent 60%),
              radial-gradient(ellipse at 10% 110%, rgba(13, 148, 136, 0.15) 0%, transparent 50%);
  pointer-events: none;
}
.hero-content { position: relative; z-index: 1; }

.hero-icon-box {
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

.hero-title {
  font-size: 26px;
  font-weight: 800;
  color: #ffffff;
  letter-spacing: -0.03em;
  line-height: 1.2;
  margin: 0 0 4px;
}
.hero-subtitle {
  font-size: 14px;
  color: #94a3b8;
  margin: 0;
  font-weight: 400;
}

/* Badges */
.badge-islamic {
  display: inline-flex;
  align-items: center;
  background: linear-gradient(135deg, #0d9488, #0f766e);
  color: white;
  font-size: 10px;
  font-weight: 700;
  padding: 3px 10px;
  border-radius: 99px;
  letter-spacing: 0.05em;
  text-transform: uppercase;
}
.badge-compliant {
  display: inline-flex;
  align-items: center;
  background: rgba(255,255,255,0.08);
  border: 1px solid rgba(255,255,255,0.15);
  color: #cbd5e1;
  font-size: 10px;
  font-weight: 600;
  padding: 3px 10px;
  border-radius: 99px;
  letter-spacing: 0.02em;
}

/* PK Badge */
.pk-badge-wrapper { text-align: right; }
.pk-label {
  font-size: 11px;
  color: #64748b;
  font-weight: 600;
  letter-spacing: 0.05em;
  text-transform: uppercase;
  margin-bottom: 8px;
}
.pk-badge {
  display: inline-flex;
  align-items: center;
  gap: 8px;
  padding: 10px 20px;
  border-radius: 14px;
  color: white;
  font-size: 16px;
  font-weight: 800;
  box-shadow: 0 8px 24px rgba(0,0,0,0.25);
  transition: transform 0.2s ease;
}
.pk-badge:hover { transform: translateY(-2px); }
.pk-gradient-sehat { background: linear-gradient(135deg, #10b981, #059669); }
.pk-gradient-cukup { background: linear-gradient(135deg, #f59e0b, #d97706); }
.pk-gradient-kurang { background: linear-gradient(135deg, #f43f5e, #e11d48); }

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
.filter-icon-wrap .v-icon { color: rgba(255,255,255,0.6) !important; }
.filter-apply-btn {
  display: flex;
  align-items: center;
  background: linear-gradient(135deg, #0d9488, #0f766e);
  color: white;
  font-size: 13px;
  font-weight: 700;
  padding: 10px 20px;
  border-radius: 12px;
  cursor: pointer;
  transition: all 0.2s ease;
  margin-left: auto;
  white-space: nowrap;
  user-select: none;
  box-shadow: 0 4px 12px rgba(13, 148, 136, 0.4);
}
.filter-apply-btn:hover { transform: translateY(-1px); box-shadow: 0 6px 16px rgba(13, 148, 136, 0.5); }
.filter-apply-btn:active { transform: translateY(0); }

.filter-info-bar {
  display: flex;
  align-items: center;
  font-size: 12px;
  color: #475569;
  font-weight: 500;
}

/* ─── Tab Navigation ──────────────────────────────── */
.tab-nav {
  display: flex;
  flex-wrap: wrap;
  gap: 6px;
  background: rgba(255,255,255,0.7);
  border: 1px solid #e2e8f0;
  padding: 6px;
  border-radius: 14px;
  backdrop-filter: blur(8px);
  width: fit-content;
  box-shadow: 0 1px 3px rgba(0,0,0,0.06);
}
.tab-btn {
  display: inline-flex;
  align-items: center;
  background: transparent;
  border: none;
  color: #64748b;
  font-size: 13px;
  font-weight: 600;
  font-family: 'Inter', sans-serif;
  padding: 9px 18px;
  border-radius: 10px;
  cursor: pointer;
  transition: all 0.2s ease;
  white-space: nowrap;
}
.tab-btn:hover { background: rgba(0,0,0,0.04); color: #334155; }
.tab-btn--active {
  background: #0f172a !important;
  color: #ffffff !important;
  box-shadow: 0 4px 12px rgba(15, 23, 42, 0.2);
}

/* ─── Content Card ────────────────────────────────── */
.content-card {
  background: #ffffff;
  border: 1px solid #e2e8f0;
  border-radius: 18px;
  overflow: hidden;
  box-shadow: 0 1px 3px rgba(0,0,0,0.05), 0 4px 12px rgba(0,0,0,0.04);
  transition: box-shadow 0.25s ease, transform 0.25s ease;
}
.content-card:hover {
  box-shadow: 0 4px 16px rgba(0,0,0,0.09), 0 12px 24px rgba(0,0,0,0.05);
  transform: translateY(-2px);
}
.content-card__accent-top {
  height: 4px;
  width: 100%;
}
.content-card__header {
  display: flex;
  align-items: flex-start;
  justify-content: space-between;
  padding: 20px 24px 16px;
  border-bottom: 1px solid #f1f5f9;
}
.content-card__title {
  font-size: 15px;
  font-weight: 700;
  color: #1e293b;
  letter-spacing: -0.01em;
  line-height: 1.3;
}
.content-card__subtitle {
  font-size: 12px;
  color: #94a3b8;
  margin-top: 3px;
  font-weight: 500;
}
.content-card__body { padding: 8px; }

.icon-badge {
  width: 38px;
  height: 38px;
  border-radius: 10px;
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
}

/* ─── KPI Cards ───────────────────────────────────── */
.kpi-card {
  background: #ffffff;
  border: 1px solid #e2e8f0;
  border-radius: 18px;
  overflow: hidden;
  box-shadow: 0 1px 3px rgba(0,0,0,0.05), 0 2px 8px rgba(0,0,0,0.04);
  transition: all 0.25s ease;
  position: relative;
}
.kpi-card:hover {
  transform: translateY(-4px);
  box-shadow: 0 8px 24px rgba(0,0,0,0.10);
}
.kpi-card--danger {
  border-color: #fecdd3;
  background: linear-gradient(160deg, #fff1f2 0%, #ffffff 60%);
}
.kpi-card__accent {
  height: 4px;
  width: 100%;
}
.kpi-card__inner { padding: 20px 20px 16px; }
.kpi-card__header {
  display: flex;
  align-items: flex-start;
  justify-content: space-between;
  margin-bottom: 10px;
}
.kpi-card__label {
  font-size: 11px;
  font-weight: 700;
  color: #94a3b8;
  text-transform: uppercase;
  letter-spacing: 0.06em;
}
.kpi-card__icon {
  width: 36px;
  height: 36px;
  border-radius: 10px;
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
}
.kpi-card__value {
  font-size: 26px;
  font-weight: 900;
  color: #1e293b;
  letter-spacing: -0.03em;
  line-height: 1.1;
  margin-bottom: 8px;
}
.kpi-card__sub {
  font-size: 12px;
  color: #94a3b8;
  font-weight: 500;
}
.kpi-card__badge {
  display: inline-flex;
  align-items: center;
  font-size: 11px;
  font-weight: 700;
  padding: 4px 10px;
  border-radius: 99px;
  margin-top: 2px;
}
.kpi-badge--success { background: #ecfdf5; color: #059669; }
.kpi-badge--danger { background: #fff1f2; color: #e11d48; }

/* ─── Status Chips ────────────────────────────────── */
.status-chip {
  display: inline-flex;
  align-items: center;
  font-size: 11px;
  font-weight: 700;
  padding: 4px 10px;
  border-radius: 99px;
}
.status-chip--warning { background: #fffbeb; color: #d97706; border: 1px solid #fde68a; }
.status-chip--neutral { background: #ecfdf5; color: #059669; border: 1px solid #a7f3d0; }

/* ─── Stress Test Panel ───────────────────────────── */
.stress-test-panel {
  border-radius: 20px;
  background: linear-gradient(135deg, #1a0a14 0%, #2d0a1e 40%, #3b0726 100%);
  position: relative;
  overflow: hidden;
  border: 1px solid rgba(244, 63, 94, 0.2);
  box-shadow: 0 8px 32px rgba(225, 29, 72, 0.15);
}
.stress-test-panel__bg-icon {
  position: absolute;
  right: -20px;
  top: -20px;
  opacity: 0.04;
  transform: rotate(-10deg);
}
.stress-test-panel__content { padding: 28px; position: relative; z-index: 1; }
.stress-test-avatar {
  width: 52px;
  height: 52px;
  border-radius: 14px;
  background: linear-gradient(135deg, #e11d48, #f43f5e);
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
  box-shadow: 0 6px 18px rgba(225, 29, 72, 0.4);
}
.stress-test-title {
  font-size: 18px;
  font-weight: 800;
  color: #ffffff;
  letter-spacing: -0.02em;
  margin: 0 0 4px;
}
.stress-test-desc {
  font-size: 13px;
  color: #fca5a5;
  margin: 0;
  font-weight: 400;
}

.stress-scenario-card {
  background: rgba(255,255,255,0.06);
  border: 1px solid rgba(255,255,255,0.1);
  border-radius: 14px;
  padding: 20px;
  transition: background 0.2s;
}
.stress-scenario-card:hover { background: rgba(255,255,255,0.09); }
.stress-scenario-card--critical {
  border-color: rgba(220, 38, 38, 0.3);
  background: rgba(153, 27, 27, 0.15);
}
.stress-scenario-card__label {
  font-size: 12px;
  font-weight: 700;
  color: #fca5a5;
  text-transform: uppercase;
  letter-spacing: 0.06em;
  display: flex;
  align-items: center;
  gap: 8px;
}
.scenario-badge {
  background: rgba(244, 63, 94, 0.25);
  color: #fda4af;
  font-size: 10px;
  padding: 2px 8px;
  border-radius: 99px;
  font-weight: 800;
  letter-spacing: 0.05em;
}
.scenario-badge--critical {
  background: rgba(153, 27, 27, 0.4);
  color: #fca5a5;
}
.stress-sub-label { font-size: 11px; color: #94a3b8; margin-bottom: 4px; }
.stress-value-primary { font-size: 18px; font-weight: 800; color: #ffffff; letter-spacing: -0.02em; }
.npf-before {
  font-size: 15px;
  font-weight: 600;
  color: #64748b;
  text-decoration: line-through;
}
.npf-after {
  font-size: 24px;
  font-weight: 900;
  letter-spacing: -0.03em;
}
.npf-after--warn { color: #fbbf24; }
.npf-after--critical { color: #f87171; }
.stress-progress-track {
  height: 8px;
  background: rgba(255,255,255,0.08);
  border-radius: 99px;
  overflow: hidden;
}
.stress-progress-fill {
  height: 100%;
  border-radius: 99px;
  transition: width 0.8s cubic-bezier(0.4, 0, 0.2, 1);
}

/* ─── Aging Rows ──────────────────────────────────── */
.aging-row {
  display: flex;
  position: relative;
  border-bottom: 1px solid #f1f5f9;
  transition: background 0.15s;
}
.aging-row:hover { background: #f8fafc; }
.aging-row--stage3 { background: rgba(255, 241, 242, 0.4); }
.aging-row--stage3:hover { background: rgba(255, 241, 242, 0.7); }
.aging-row__indicator {
  width: 4px;
  flex-shrink: 0;
  align-self: stretch;
}
.aging-row__body {
  flex: 1;
  display: flex;
  align-items: center;
  justify-content: space-between;
  flex-wrap: wrap;
  gap: 12px;
  padding: 18px 24px;
}
.aging-avatar {
  width: 36px;
  height: 36px;
  border-radius: 10px;
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
}
.aging-cat-name { font-size: 14px; font-weight: 700; color: #1e293b; }
.aging-cat-sub { font-size: 11px; color: #94a3b8; font-weight: 500; margin-top: 1px; }
.aging-row__financials { display: flex; align-items: center; gap: 24px; text-align: right; }
.aging-os { font-size: 15px; font-weight: 700; color: #334155; }
.aging-ecl-wrap { text-align: right; }
.aging-ecl-label { font-size: 10px; color: #94a3b8; font-weight: 600; margin-bottom: 4px; text-transform: uppercase; letter-spacing: 0.04em; }
.aging-ecl-chip {
  display: inline-flex;
  align-items: center;
  font-size: 12px;
  font-weight: 800;
  padding: 4px 12px;
  border-radius: 99px;
}
.aging-ecl-chip--stage1 { background: #ecfdf5; color: #059669; }
.aging-ecl-chip--stage2 { background: #fffbeb; color: #d97706; }
.aging-ecl-chip--stage3 { background: #fff1f2; color: #e11d48; }

/* ─── Data Table ──────────────────────────────────── */
.data-table {
  width: 100%;
  border-collapse: separate;
  border-spacing: 0;
}
.data-table thead tr {
  background: #f8fafc;
  border-bottom: 1px solid #e2e8f0;
}
.data-table th {
  font-size: 11px;
  font-weight: 700;
  color: #94a3b8;
  text-transform: uppercase;
  letter-spacing: 0.06em;
  padding: 12px 20px;
  border-bottom: 1px solid #e2e8f0;
  white-space: nowrap;
}
.data-table td {
  padding: 14px 20px;
  border-bottom: 1px solid #f1f5f9;
  font-size: 13px;
  vertical-align: middle;
  white-space: nowrap;
}
.data-table tbody tr { transition: background 0.15s; }
.data-table tbody tr:hover { background: #f8fafc; }
.data-table tbody tr.row--danger { background: rgba(255, 241, 242, 0.5); }
.data-table tbody tr.row--danger:hover { background: rgba(255, 241, 242, 0.8); }

.ao-avatar {
  width: 32px;
  height: 32px;
  border-radius: 8px;
  background: #f1f5f9;
  color: #475569;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 11px;
  font-weight: 800;
  flex-shrink: 0;
}
.ao-avatar--danger { background: #fff1f2; color: #e11d48; }

.npf-pill {
  display: inline-flex;
  align-items: center;
  font-size: 12px;
  font-weight: 800;
  padding: 4px 12px;
  border-radius: 99px;
}
.npf-pill--safe { background: #ecfdf5; color: #059669; }
.npf-pill--danger { background: #e11d48; color: white; }

.export-btn {
  display: inline-flex;
  align-items: center;
  background: #f8fafc;
  border: 1px solid #e2e8f0;
  color: #475569;
  font-size: 12px;
  font-weight: 700;
  font-family: 'Inter', sans-serif;
  padding: 7px 14px;
  border-radius: 10px;
  cursor: pointer;
  transition: all 0.15s;
  white-space: nowrap;
}
.export-btn:hover { background: #f1f5f9; border-color: #cbd5e1; }

/* ─── Akad Risk Card ──────────────────────────────── */
.akad-risk-card {
  background: linear-gradient(135deg, #312e81 0%, #4c1d95 100%);
  border-radius: 18px;
  padding: 20px;
  display: flex;
  align-items: flex-start;
  gap: 16px;
  box-shadow: 0 8px 24px rgba(79, 70, 229, 0.25);
}
.akad-risk-card__icon {
  width: 44px;
  height: 44px;
  border-radius: 12px;
  background: rgba(255,255,255,0.15);
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
}
.akad-risk-card__label {
  font-size: 11px;
  font-weight: 700;
  color: #a5b4fc;
  text-transform: uppercase;
  letter-spacing: 0.06em;
  margin-bottom: 6px;
}
.akad-risk-card__value {
  font-size: 17px;
  font-weight: 900;
  color: #ffffff;
  letter-spacing: -0.01em;
  margin-bottom: 6px;
  line-height: 1.2;
}
.akad-risk-card__desc {
  font-size: 12px;
  color: #c4b5fd;
  font-weight: 400;
  line-height: 1.5;
}

/* ─── Watchlist Card ──────────────────────────────── */
.watchlist-card {
  background: #ffffff;
  border: 1px solid #e2e8f0;
  border-radius: 18px;
  overflow: hidden;
  box-shadow: 0 2px 8px rgba(0,0,0,0.06);
}
.watchlist-card__header {
  background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
  padding: 20px 24px;
  border-bottom: 1px solid rgba(255,255,255,0.05);
}
.watchlist-card__header-inner {
  display: flex;
  align-items: center;
  gap: 16px;
}
.watchlist-icon {
  width: 46px;
  height: 46px;
  border-radius: 12px;
  background: linear-gradient(135deg, #e11d48, #9f1239);
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
  box-shadow: 0 4px 12px rgba(225, 29, 72, 0.35);
}
.watchlist-title {
  font-size: 15px;
  font-weight: 700;
  color: #ffffff;
  letter-spacing: -0.01em;
}
.watchlist-subtitle {
  font-size: 12px;
  color: #94a3b8;
  margin-top: 2px;
}
.watchlist-table { width: 100%; }
.watchlist-row { transition: background 0.15s; }
.watchlist-row:hover { background: #f8fafc !important; }

/* Kolektibilitas Badges */
.kol-badge {
  display: inline-flex;
  align-items: center;
  font-size: 11px;
  font-weight: 800;
  padding: 4px 10px;
  border-radius: 8px;
  text-transform: uppercase;
  letter-spacing: 0.04em;
}
.kol-badge--3 { background: #fff7ed; color: #ea580c; border: 1px solid #fed7aa; }
.kol-badge--4 { background: #fff1f2; color: #f43f5e; border: 1px solid #fecdd3; }
.kol-badge--5 { background: #e11d48; color: white; }

.tunggak-badge {
  display: inline-flex;
  align-items: center;
  background: #fff1f2;
  color: #e11d48;
  font-size: 11px;
  font-weight: 700;
  padding: 4px 10px;
  border-radius: 8px;
}

/* ─── Empty State ─────────────────────────────────── */
.empty-state {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  color: #94a3b8;
  text-align: center;
  padding: 32px;
}
.empty-state p { font-size: 14px; font-weight: 500; margin: 0; }

/* ─── Chart Loading ───────────────────────────────── */
.chart-loading {
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 48px;
  width: 100%;
}

/* ─── Max Width ───────────────────────────────────── */
.max-w-7xl { max-width: 1280px; }
.mx-auto { margin-left: auto; margin-right: auto; }
.px-6 { padding-left: 24px; padding-right: 24px; }
.py-8 { padding-top: 32px; padding-bottom: 32px; }
.pt-7 { padding-top: 28px; }
.pb-16 { padding-bottom: 64px; }
.mb-6 { margin-bottom: 24px; }
.mb-8 { margin-bottom: 32px; }
.mt-6 { margin-top: 24px; }
.mt-3 { margin-top: 12px; }
.h-100 { height: 100%; }
.w-100 { width: 100%; }
.gap-2 { gap: 8px; }
.gap-3 { gap: 12px; }
.gap-4 { gap: 16px; }
.gap-5 { gap: 20px; }
.gap-6 { gap: 24px; }
.pa-4 { padding: 16px; }
.pa-16 { padding: 64px; }
.mr-1 { margin-right: 4px; }
.mr-2 { margin-right: 8px; }
.ml-2 { margin-left: 8px; }
.font-weight-bold { font-weight: 700; }
.font-weight-semibold { font-weight: 600; }
.font-weight-medium { font-weight: 500; }
.text-center { text-align: center; }
.text-right { text-align: right; }
.text-left { text-align: left; }
.d-flex { display: flex; }
.align-center { align-items: center; }
.align-start { align-items: flex-start; }
.justify-center { justify-content: center; }
.justify-space-between { justify-content: space-between; }
.flex-grow-1 { flex-grow: 1; }
.flex-shrink-0 { flex-shrink: 0; }
.flex-column { flex-direction: column; }
.overflow-x-auto { overflow-x: auto; }
.overflow-hidden { overflow: hidden; }
.flex-wrap { flex-wrap: wrap; }
</style>
