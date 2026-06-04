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
        <div class="d-flex flex-column flex-md-row justify-space-between align-start align-md-center gap-4">
          <div class="d-flex align-center gap-4">
            <div class="fin-hero__icon fin-icon-blue">
              <v-icon icon="ri-focus-3-line" size="26" color="white" />
            </div>
            <div class="fin-hero__meta">
              <h1 class="fin-hero__title">Target Management (RBB)</h1>
              <p class="fin-hero__subtitle">
                Sinkronisasi real-time performa penyaluran terhadap Rencana Bisnis Bank.
              </p>
              <div class="fin-hero__badges">
                <span class="fin-badge fin-badge--teal">🏦 Islamic Banking</span>
                <span class="fin-badge fin-badge--success">
                  <span class="pulse-dot mr-1"></span> Active Monitor
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
      <v-row class="mb-6">
        <v-col cols="12" sm="6" lg="3">
          <v-tooltip location="top" offset="10">
            <template #activator="{ props }">
              <v-card v-bind="props" class="rounded-xl border shadow-sm transition-swing h-100" elevation="0" style="position: relative; overflow: hidden;">
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
              <v-card v-bind="props" class="rounded-xl border shadow-sm transition-swing h-100" elevation="0" style="position: relative; overflow: hidden;">
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
          <v-card class="rounded-xl border shadow-sm transition-swing h-100" elevation="0" style="position: relative; overflow: hidden;">
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
              <v-card v-bind="props" class="rounded-xl border shadow-sm transition-swing h-100" elevation="0" style="position: relative; overflow: hidden;">
                <div style="position: absolute; top: -20px; right: -20px; width: 120px; height: 120px; opacity: 0.08;">
                  <v-icon icon="ri-flag-2-line" size="120" color="#8b5cf6" />
                </div>
                <v-card-text class="pa-5" style="position: relative; z-index: 1;">
                  <div>
                    <p class="text-caption font-weight-bold text-uppercase tracking-widest mb-1" style="color: #64748B; font-family: 'Inter', sans-serif;">SISA TARGET</p>
                    <h2 class="font-weight-black mb-2 target-money-exact" style="color: #8b5cf6; font-family: 'Plus Jakarta Sans', sans-serif;">{{ formatFull(Math.abs(scorecards.gap_miliar)) }}</h2>
                    <p class="text-caption text-medium-emphasis mb-0" style="font-family: 'Inter', sans-serif;">Kekurangan pencairan</p>
                  </div>
                </v-card-text>
              </v-card>
            </template>
            <span>Detail: {{ formatFull(scorecards.gap_miliar) }}</span>
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
                        {{ formatFull(ao.realisai || ao.realisasi).replace('Rp ', '') }}
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
      <div v-if="selectedAOData" class="d-flex flex-column h-100 bg-slate-50">
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
              <v-card elevation="0" border rounded="xl" class="pa-4 bg-white text-center h-100">
                <div class="text-caption text-slate-500 font-weight-black text-uppercase mb-2">Target YTD</div>
                <div class="text-h6 font-weight-black text-slate-800 target-money-drawer">{{ formatFull(selectedAOData.target_ytd) }}</div>
              </v-card>
            </v-col>
            <v-col cols="6">
              <v-card elevation="0" border rounded="xl" class="pa-4 bg-white text-center h-100">
                <div class="text-caption text-slate-500 font-weight-black text-uppercase mb-2">Realisasi</div>
                <div class="text-h6 font-weight-black text-emerald-600 target-money-drawer">{{ formatFull(selectedAOData.realisai || selectedAOData.realisasi) }}</div>
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
                  <div class="text-caption text-slate-400 font-weight-black text-uppercase mb-1">Gap Nominal</div>
                  <div class="text-h6 font-weight-black" :class="selectedAOData.gap >= 0 ? 'text-emerald-600' : 'text-rose-600'">
                    {{ selectedAOData.gap >= 0 ? '+' : '' }} {{ formatFull(selectedAOData.gap) }}
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
</style>
