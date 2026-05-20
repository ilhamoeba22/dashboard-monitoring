<script setup>
import { ref, computed, onMounted, watch } from 'vue'
import { Head } from '@inertiajs/vue3'
import DefaultLayout from '@/layouts/default.vue'
import axios from 'axios'
import VueApexCharts from 'vue3-apexcharts'
import '@/assets/css/financing-shared.css'

defineOptions({ layout: DefaultLayout })

// ─── State Management ────────────────────────────────────────
const isLoading    = ref(true)
const selectedYear = ref(new Date().getFullYear())
const selectedMonth = ref(new Date().getMonth() + 1) // Default ke bulan saat ini (Mingguan)
const emptyMsg     = ref('')

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

const pacingColor = computed(() => {
  const p = scorecards.value.pacing_pct
  if (p >= 100) return '#10B981' // Hijau
  if (p >= 80) return '#F59E0B'  // Kuning
  return '#EF4444'               // Merah
})

// ─── Chart Options ───────────────────────────────────────────
const chartOpts = computed(() => ({
  chart: {
    type: 'bar',
    fontFamily: "'Plus Jakarta Sans', sans-serif",
    toolbar: { show: false },
    zoom: { enabled: false },
    animations: { enabled: true, easing: 'easeinout', speed: 600 }
  },
  plotOptions: {
    bar: { columnWidth: '50%', borderRadius: 6, borderRadiusApplication: 'end' }
  },
  colors: ['#06B6D4', '#64748B'], // Cyan/Biru Korporat untuk Bar, Abu Gelap untuk Line
  dataLabels: { enabled: false },
  stroke: {
    curve: 'smooth',
    width: [0, 3], // 0 untuk bar, 3 untuk line
    dashArray: [0, 5] // Putus-putus untuk target line
  },
  fill: {
    type: ['gradient', 'solid'],
    gradient: {
      type: 'vertical',
      shadeIntensity: 1,
      opacityFrom: 0.9,
      opacityTo: 0.6,
      stops: [0, 100]
    }
  },
  xaxis: {
    categories: chartData.value.categories,
    axisBorder: { show: false },
    axisTicks: { show: false },
    labels: { style: { fontSize: '12px', fontWeight: 600 } }
  },
  yaxis: {
    labels: { formatter: v => v !== null ? `Rp ${Number(v).toFixed(1)} M` : '' }
  },
  legend: {
    position: 'top', horizontalAlign: 'right',
    markers: { shape: 'circle', size: 6 },
    fontWeight: 700, fontSize: '13px'
  },
  tooltip: {
    shared: true,
    intersect: false,
    y: { formatter: v => v !== null ? `Rp ${Number(v).toFixed(2)} Miliar` : '-' }
  },
  grid: { borderColor: '#F1F5F9', strokeDashArray: 4 }
}))

const chartSeries = computed(() => [
  { name: 'Pencairan Riil', type: 'bar', data: chartData.value.realisasi },
  { name: 'Target Pencairan', type: 'line', data: chartData.value.target }
])

const aoChartSeries = computed(() => {
  if (!selectedAOData.value) return []
  return [
    { name: 'Realisasi AO', type: 'bar', data: selectedAOData.value.chart_realisasi },
    { name: 'Target AO', type: 'line', data: selectedAOData.value.chart_target }
  ]
})

// ─── Helpers ─────────────────────────────────────────────────
const formatM = (val) => `${Number(val).toFixed(1)} M`
const formatRp = (val) => new Intl.NumberFormat('id-ID').format(val)

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

const getProgressColor = (pct) => {
  if (pct >= 100) return 'success'
  if (pct >= 70) return 'warning'
  return 'error'
}

// ─── API Fetch ───────────────────────────────────────────────
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
    <Head title="Monitoring Target vs Realisasi (RBB)" />

    <!-- ── HERO HEADER ─────────────────────────────────────────── -->
    <div class="fin-hero mb-6">
      <div class="fin-hero__deco"></div>
      <div class="fin-hero__inner">
        <div class="d-flex flex-column flex-md-row justify-space-between align-start align-md-center gap-4">
          <div class="d-flex align-center gap-4">
            <div class="fin-hero__icon fin-icon-blue">
              <v-icon icon="ri-focus-3-line" size="26" color="white" />
            </div>
            <div class="fin-hero__meta">
              <h1 class="fin-hero__title">Monitoring Target vs Realisasi (RBB)</h1>
              <p class="fin-hero__subtitle">
                Analisis pergerakan pembiayaan terhadap Rencana Bisnis Bank.
              </p>
              <div class="fin-hero__badges">
                <span class="fin-badge fin-badge--success">
                  <span class="pulse-dot mr-1"></span> Real-time Sync
                </span>
              </div>
            </div>
          </div>

          <div class="fin-filter-bar">
            <v-select
              v-model="selectedMonth"
              :items="monthOptions"
              item-title="title"
              item-value="value"
              variant="plain"
              density="compact"
              hide-details
              prepend-inner-icon="ri-filter-3-line"
              style="width: 140px;"
              placeholder="Pilih Bulan"
            />
            <div style="width: 1px; height: 24px; background: rgba(255,255,255,0.2);" class="mx-1"></div>
            <v-select
              v-model="selectedYear"
              :items="[2024, 2025, 2026, 2027]"
              variant="plain"
              density="compact"
              hide-details
              prepend-inner-icon="ri-calendar-2-line"
              style="width: 100px;"
            />
          </div>
        </div>
      </div>
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
        <v-col cols="12">
          <v-skeleton-loader type="table" height="350" rounded="xl" />
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
      <!-- 2. EXECUTIVE SCORECARDS -->
      <div class="kpi-cards-grid mb-5">
        <!-- Target -->
        <div class="kpi-card">
          <div class="kpi-card__accent" style="background: linear-gradient(90deg, #64748b, #94a3b8)"></div>
          <div class="kpi-card__inner">
            <div class="kpi-card__header">
              <span class="kpi-card__label">Total Target RBB</span>
              <div class="kpi-card__icon bg-slate-100">
                <v-icon icon="ri-flag-2-line" size="18" color="grey-darken-1" />
              </div>
            </div>
            <div class="kpi-card__value">{{ formatM(scorecards.total_target_annual) }}</div>
            <div class="kpi-card__sub">Akumulasi Target {{ selectedYear }}</div>
          </div>
        </div>

        <!-- Pencairan -->
        <div class="kpi-card kpi-card--info">
          <div class="kpi-card__accent" style="background: linear-gradient(90deg, #3b82f6, #0ea5e9)"></div>
          <div class="kpi-card__inner">
            <div class="kpi-card__header">
              <span class="kpi-card__label text-blue-600">Total Pencairan Baru</span>
              <div class="kpi-card__icon fin-icon-blue">
                <v-icon icon="ri-bank-card-line" size="18" />
              </div>
            </div>
            <div class="kpi-card__value">{{ formatM(scorecards.total_realisasi) }}</div>
            <div class="kpi-card__sub">Volume pencairan YTD</div>
          </div>
        </div>

        <!-- Pacing % -->
        <div class="kpi-card">
          <div class="kpi-card__accent" :style="{ background: pacingColor }"></div>
          <div class="kpi-card__inner">
            <div class="kpi-card__header">
              <span class="kpi-card__label" :style="{ color: pacingColor }">Pacing Achievement</span>
              <div class="kpi-card__icon" :style="{ background: pacingColor + '20', color: pacingColor }">
                <v-icon icon="ri-percent-line" size="18" />
              </div>
            </div>
            <div class="kpi-card__value" :style="{ color: pacingColor }">{{ scorecards.pacing_pct }}<span class="text-h6">%</span></div>
            <div class="kpi-card__sub">vs Target s/d Bulan {{ scorecards.current_month }}</div>
          </div>
        </div>

        <!-- Gap -->
        <div class="kpi-card" :class="scorecards.gap_miliar > 0 ? 'kpi-card--success' : 'kpi-card--danger'">
          <div class="kpi-card__accent" :style="scorecards.gap_miliar > 0 ? 'background: linear-gradient(90deg, #10b981, #34d399)' : 'background: linear-gradient(90deg, #e11d48, #fb7185)'"></div>
          <div class="kpi-card__inner">
            <div class="kpi-card__header">
              <span class="kpi-card__label" :class="scorecards.gap_miliar > 0 ? 'text-emerald-600' : 'text-rose-600'">Selisih / Gap RBB</span>
              <div class="kpi-card__icon" :class="scorecards.gap_miliar > 0 ? 'fin-icon-green' : 'fin-icon-red'">
                <v-icon :icon="scorecards.gap_miliar > 0 ? 'ri-arrow-up-circle-line' : 'ri-arrow-down-circle-line'" size="18" />
              </div>
            </div>
            <div class="kpi-card__value">
              {{ scorecards.gap_miliar > 0 ? '+' : (scorecards.gap_miliar < 0 ? '-' : '') }} {{ formatM(Math.abs(scorecards.gap_miliar)) }}
            </div>
            <div class="kpi-card__sub">
              {{ scorecards.gap_miliar > 0 ? 'Surplus pencairan' : 'Kekurangan pencairan' }}
            </div>
          </div>
        </div>
      </div>

      <!-- 3. DUAL-LINE PACING CHART -->
      <v-row class="mb-5">
        <v-col cols="12">
          <v-card elevation="0" border rounded="xl" class="pa-6">
            <div class="d-flex align-center gap-2 mb-6">
              <v-icon icon="ri-bar-chart-box-line" color="primary" />
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
                    <th class="text-center font-weight-bold text-slate-400 text-uppercase" style="width: 80px">Rank</th>
                    <th class="text-left font-weight-bold text-slate-400 text-uppercase">Account Officer</th>
                    <th class="text-right font-weight-bold text-slate-400 text-uppercase">Target YTD</th>
                    <th class="text-right font-weight-bold text-slate-400 text-uppercase">Pencairan</th>
                    <th class="text-left font-weight-bold text-slate-400 text-uppercase" style="min-width: 250px">Pencapaian (%)</th>
                    <th class="text-center font-weight-bold text-slate-400 text-uppercase" style="width: 140px">Status</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="(ao, idx) in leaderboard" :key="ao.kdao" @click="openAODetail(ao)" class="cursor-pointer">
                    <td class="text-center">
                      <span v-if="idx === 0" class="text-h6 font-weight-black text-amber-500">🥇</span>
                      <span v-else-if="idx === 1" class="text-h6 font-weight-black text-slate-400">🥈</span>
                      <span v-else-if="idx === 2" class="text-h6 font-weight-black text-orange-400">🥉</span>
                      <span v-else class="font-weight-bold text-slate-400">#{{ idx + 1 }}</span>
                    </td>
                    <td>
                      <div class="d-flex align-center gap-3 py-2">
                        <v-avatar size="40" :color="idx === 0 ? 'warning' : 'primary'" variant="tonal" class="font-weight-bold text-subtitle-2">
                          {{ ao.name.substring(0, 2).toUpperCase() }}
                        </v-avatar>
                        <div>
                          <div class="font-weight-bold text-slate-800 text-body-2 group-hover:text-primary transition-colors">{{ ao.name }}</div>
                          <div class="text-caption text-slate-500">{{ ao.kdao }}</div>
                        </div>
                      </div>
                    </td>
                    <td class="text-right font-weight-medium text-slate-600">Rp {{ formatRp(Math.round(ao.target_ytd)) }}</td>
                    <td class="text-right font-weight-black text-slate-800">Rp {{ formatRp(Math.round(ao.realisasi)) }}</td>
                    <td>
                      <div class="d-flex flex-column justify-center pr-4">
                        <div class="d-flex justify-space-between mb-1">
                          <span class="text-caption font-weight-bold text-slate-700">{{ ao.pct }}%</span>
                        </div>
                        <v-progress-linear
                          :model-value="Math.min(ao.pct, 100)"
                          :color="getProgressColor(ao.pct)"
                          height="8"
                          rounded
                          bg-color="slate-100"
                        />
                      </div>
                    </td>
                    <td class="text-center">
                      <v-chip size="small" :color="getStatusColor(ao.status)" variant="flat" class="font-weight-bold text-uppercase" style="font-size: 10px; letter-spacing: 0.5px;">
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
      width="500"
      elevation="4"
    >
      <div v-if="selectedAOData" class="d-flex flex-column h-100 bg-slate-50">
        <!-- Drawer Header -->
        <div class="pa-6 bg-white border-b d-flex justify-space-between align-start">
          <div class="d-flex align-center gap-4">
            <v-avatar size="56" color="primary" variant="tonal" class="text-h6 font-weight-black">
              {{ selectedAOData.name.substring(0, 2).toUpperCase() }}
            </v-avatar>
            <div>
              <div class="text-h6 font-weight-black text-slate-800 mb-0" style="line-height: 1.2;">{{ selectedAOData.name }}</div>
              <div class="text-body-2 text-slate-500 font-weight-medium mt-1">Kode AO: <span class="font-weight-bold">{{ selectedAOData.kdao }}</span></div>
              <v-chip size="small" :color="getStatusColor(selectedAOData.status)" variant="flat" class="mt-2 font-weight-bold text-uppercase" style="font-size: 10px;">
                {{ getStatusLabel(selectedAOData.status) }}
              </v-chip>
            </div>
          </div>
          <v-btn icon="ri-close-line" variant="text" size="small" color="grey-darken-1" @click="isDrawerOpen = false"></v-btn>
        </div>

        <div class="pa-6 overflow-y-auto" style="flex: 1;">
          <!-- Mini Scorecards -->
          <v-row class="mb-6">
            <v-col cols="6">
              <v-card elevation="0" border rounded="lg" class="pa-4 bg-white text-center">
                <div class="text-caption text-slate-500 font-weight-bold text-uppercase mb-1">Target YTD</div>
                <div class="text-h6 font-weight-black text-slate-800">{{ formatM(selectedAOData.target_ytd) }}</div>
              </v-card>
            </v-col>
            <v-col cols="6">
              <v-card elevation="0" border rounded="lg" class="pa-4 bg-white text-center">
                <div class="text-caption text-slate-500 font-weight-bold text-uppercase mb-1">Realisasi</div>
                <div class="text-h6 font-weight-black text-primary">{{ formatM(selectedAOData.realisasi) }}</div>
              </v-card>
            </v-col>
            <v-col cols="12">
              <v-card elevation="0" border rounded="lg" class="pa-4 bg-white d-flex align-center justify-space-between">
                <div>
                  <div class="text-caption text-slate-500 font-weight-bold text-uppercase mb-1">Pencapaian</div>
                  <div class="text-h5 font-weight-black" :class="`text-${getProgressColor(selectedAOData.pct)}`">
                    {{ selectedAOData.pct }}%
                  </div>
                </div>
                <div class="text-right">
                  <div class="text-caption text-slate-500 font-weight-bold text-uppercase mb-1">Gap / Selisih</div>
                  <div class="text-subtitle-1 font-weight-bold" :class="selectedAOData.gap > 0 ? 'text-emerald-600' : 'text-rose-600'">
                    {{ selectedAOData.gap > 0 ? '+' : (selectedAOData.gap < 0 ? '-' : '') }} {{ formatM(Math.abs(selectedAOData.gap)) }}
                  </div>
                </div>
              </v-card>
            </v-col>
          </v-row>

          <!-- AO Pacing Chart -->
          <v-card elevation="0" border rounded="xl" class="pa-4 bg-white">
            <div class="d-flex align-center gap-2 mb-4">
              <v-icon icon="ri-line-chart-line" color="primary" />
              <h3 class="text-subtitle-1 font-weight-black text-slate-800 mb-0">Grafik Kinerja AO</h3>
            </div>
            <VueApexCharts type="bar" height="300" :options="chartOpts" :series="aoChartSeries" />
          </v-card>
        </div>
      </div>
    </v-navigation-drawer>
  </div>
</template>

<style scoped>
.target-page {
  max-width: 1400px;
  margin: 0 auto;
  font-family: 'Plus Jakarta Sans', sans-serif;
}

.kpi-card {
  border: 1px solid #e2e8f0;
  transition: all 0.2s ease;
}
.kpi-card:hover {
  transform: translateY(-4px);
  box-shadow: 0 12px 24px -8px rgba(148, 163, 184, 0.2) !important;
}

.icon-box {
  width: 36px;
  height: 36px;
  border-radius: 10px;
  display: flex;
  align-items: center;
  justify-content: center;
}

.leaderboard-table th {
  background-color: #ffffff;
  border-bottom: 2px solid #f1f5f9 !important;
  font-size: 11px;
}
.leaderboard-table td {
  border-bottom: 1px solid #f8fafc !important;
}
.leaderboard-table tr:hover {
  background-color: #f8fafc !important;
}

.pulse-dot {
  width: 6px;
  height: 6px;
  border-radius: 50%;
  background: #10b981;
  animation: pulse 1.5s infinite;
}

@keyframes pulse {
  0% { box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.4); }
  70% { box-shadow: 0 0 0 4px rgba(16, 185, 129, 0); }
  100% { box-shadow: 0 0 0 0 rgba(16, 185, 129, 0); }
}

/* Tailwind custom colors mapped to standard names */
.text-slate-400 { color: #94a3b8; }
.text-slate-500 { color: #64748b; }
.text-slate-600 { color: #475569; }
.text-slate-700 { color: #334155; }
.text-slate-800 { color: #1e293b; }
.bg-slate-50 { background-color: #f8fafc; }
.bg-slate-100 { background-color: #f1f5f9; }

.text-blue-600 { color: #2563eb; }
.text-blue-900 { color: #1e3a8a; }
.bg-blue-100 { background-color: #dbeafe; }

.text-rose-500 { color: #f43f5e; }
.text-rose-600 { color: #e11d48; }
.text-rose-700 { color: #be123c; }
.bg-rose-100 { background-color: #ffe4e6; }

.text-emerald-600 { color: #059669; }
.bg-emerald-50 { background-color: #ecfdf5; }
</style>
