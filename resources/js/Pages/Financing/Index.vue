<script setup>
import { ref, computed, onMounted } from 'vue'
import { Head, Link } from '@inertiajs/vue3'
import DefaultLayout from '@/layouts/default.vue'
import VueApexCharts from 'vue3-apexcharts'
import axios from 'axios'
import '@/assets/css/financing-shared.css'

defineOptions({ layout: DefaultLayout })

// State
const isLoading = ref(true)
const selectedCabang = ref('Semua Cabang')
const cabangs = ref([])

const overviewData = ref({
  summary: { total_os: 0, total_noa: 0, npf_os: 0, npf_ratio: 0, total_tunggakan: 0 },
  trend: [],
  kolektibilitas: [],
  segmen: [],
  cabang: [],
  top_npf: []
})

// Quick Links Configuration
const quickLinks = [
  { title: 'Master Console', icon: 'ri-bar-chart-grouped-line', color: 'primary', route: '/financing/rekapitulasi', desc: 'Analisis volume, NOA, O/S multidimensi' },
  { title: 'Quality & Risk', icon: 'ri-shield-keyhole-line', color: 'error', route: '/financing/quality', desc: 'Monitoring NPF, aging, konsentrasi risiko' },
  { title: 'Data Nominatif', icon: 'ri-list-check-3', color: 'success', route: '/financing/nominatif', desc: 'Data rinci per rekening nasabah' },
  { title: 'Target RBB', icon: 'ri-focus-2-line', color: 'warning', route: '/financing/target', desc: 'Pencapaian vs target RBB tahunan' },
]

// Computed Charts
const trendChartOptions = computed(() => ({
  chart: { type: 'area', fontFamily: "'Plus Jakarta Sans', sans-serif", toolbar: { show: false }, zoom: { enabled: false } },
  dataLabels: { enabled: false },
  stroke: { curve: 'smooth', width: 3 },
  xaxis: { categories: overviewData.value.trend.map(t => t.month) },
  yaxis: { labels: { formatter: (val) => `Rp ${(val / 1e9).toFixed(1)}M` } },
  colors: ['#0ea5e9'],
  fill: { type: 'gradient', gradient: { shadeIntensity: 1, opacityFrom: 0.4, opacityTo: 0.05, stops: [0, 100] } },
  title: { text: 'Pertumbuhan Portofolio (12 Bulan)', style: { fontWeight: 600 } }
}))

const trendChartSeries = computed(() => [{
  name: 'Total O/S Pokok',
  data: overviewData.value.trend.map(t => parseFloat(t.total_os))
}])

const kolDonutOptions = computed(() => ({
  chart: { type: 'donut', fontFamily: "'Plus Jakarta Sans', sans-serif" },
  labels: overviewData.value.kolektibilitas.map(k => k.label),
  colors: ['#22c55e', '#3b82f6', '#f59e0b', '#f97316', '#ef4444'],
  dataLabels: { enabled: false },
  legend: { position: 'bottom' },
  title: { text: 'Distribusi Kolektibilitas', style: { fontWeight: 600 } }
}))

const kolDonutSeries = computed(() => overviewData.value.kolektibilitas.map(k => parseFloat(k.total_os)))

// Helpers
const formatRp = (v) => {
  if (!v) return 'Rp 0'
  if (v >= 1e9) return `Rp ${(v / 1e9).toFixed(2)} Miliar`
  if (v >= 1e6) return `Rp ${(v / 1e6).toFixed(2)} Juta`
  return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', maximumFractionDigits: 0 }).format(v)
}
const formatNum = (v) => new Intl.NumberFormat('id-ID').format(v || 0)

// API Fetch
const fetchCabangs = async () => {
  try {
    const res = await axios.get('/api/v1/financing/cabangs')
    if (res.data.success) {
      cabangs.value = [{ kdloc: 'Semua Cabang', nama: 'Semua Cabang' }, ...res.data.data]
    }
  } catch (e) { console.error(e) }
}

const fetchOverview = async () => {
  isLoading.value = true
  try {
    const params = selectedCabang.value !== 'Semua Cabang' ? { cabang: selectedCabang.value } : {}
    const res = await axios.get('/api/v1/financing/overview', { params })
    if (res.data.success) {
      overviewData.value = {
        summary: res.data.data.summary || overviewData.value.summary,
        trend: res.data.data.trend || [],
        kolektibilitas: res.data.data.kolektibilitas || [],
        segmen: res.data.data.segmen || [],
        cabang: res.data.data.cabang || [],
        top_npf: res.data.data.top_npf || []
      }
    }
  } catch (e) {
    console.error(e)
  } finally {
    isLoading.value = false
  }
}

onMounted(() => {
  fetchCabangs()
  fetchOverview()
})

const badgeColor = (kol) => {
  if (kol <= 2) return 'success'
  if (kol == 3) return 'warning'
  return 'error'
}
</script>

<template>
  <div class="fin-page px-4 pt-0">
    <Head title="Financing Overview" />

    <!-- ── HERO HEADER ─────────────────────────────────────────── -->
    <div class="fin-hero mb-6">
      <div class="fin-hero__deco"></div>
      <div class="fin-hero__inner">
        <div class="d-flex flex-column flex-md-row justify-space-between align-start align-md-center gap-4">
          <div class="d-flex align-center gap-4">
            <div class="fin-hero__icon fin-icon-blue">
              <v-icon icon="ri-dashboard-2-line" size="26" color="white" />
            </div>
            <div class="fin-hero__meta">
              <h1 class="fin-hero__title">Executive Overview</h1>
              <p class="fin-hero__subtitle">Ringkasan performa dan kesehatan portofolio pembiayaan secara realtime.</p>
              <div class="fin-hero__badges">
                <span class="fin-badge fin-badge--primary">🏢 Home</span>
              </div>
            </div>
          </div>
          
          <div class="fin-filter-bar d-flex align-center gap-3">
            <v-select
              v-model="selectedCabang"
              :items="cabangs"
              item-title="nama"
              item-value="kdloc"
              prepend-inner-icon="ri-store-2-line"
              variant="solo"
              density="compact"
              flat hide-details
              rounded="lg"
              bg-color="white"
              style="min-width: 250px;"
              @update:model-value="fetchOverview"
            ></v-select>
          </div>
        </div>
      </div>
    </div>

    <!-- QUICK ACTIONS -->
    <div class="kpi-cards-grid mb-6">
      <Link v-for="(link, i) in quickLinks" :key="i" :href="link.route" style="text-decoration: none;">
        <div class="kpi-card hover:shadow-md transition-swing">
          <div class="kpi-card__accent" :style="`background: var(--fin-color-${link.color}); opacity: 0.8;`"></div>
          <div class="kpi-card__inner d-flex align-start gap-3 pa-4">
            <div :class="`fin-hero__icon fin-icon-${link.color === 'error' ? 'red' : link.color === 'success' ? 'green' : link.color === 'warning' ? 'amber' : 'blue'}`">
              <v-icon :icon="link.icon" size="24" />
            </div>
            <div>
              <div class="font-weight-bold text-slate-800">{{ link.title }}</div>
              <div class="text-caption text-medium-emphasis mt-1 lh-sm">{{ link.desc }}</div>
            </div>
          </div>
        </div>
      </Link>
    </div>

    <v-divider class="mb-6"></v-divider>

    <!-- KEY PERFORMANCE INDICATORS -->
    <v-row class="mb-6">
      <v-col cols="12" sm="6" lg="3">
        <v-card class="rounded-xl border shadow-sm transition-swing h-100" elevation="0" style="position: relative; overflow: hidden;">
          <div style="position: absolute; top: -20px; right: -20px; width: 120px; height: 120px; opacity: 0.08;">
            <v-icon icon="ri-wallet-3-line" size="120" color="#3b82f6" />
          </div>
          <v-card-text class="pa-5" style="position: relative; z-index: 1;">
            <div class="d-flex justify-space-between align-start">
              <div>
                <p class="text-caption font-weight-bold text-uppercase tracking-widest mb-1" style="color: #64748B; font-family: 'Inter', sans-serif;">TOTAL PORTOFOLIO</p>
                <h2 class="text-h4 font-weight-bold mb-2" style="color: #3b82f6; font-family: 'Plus Jakarta Sans', sans-serif; line-height: 1.2;">{{ formatRp(overviewData.summary.total_os) }}</h2>
                <p class="text-caption text-medium-emphasis mb-0" style="font-family: 'Inter', sans-serif;"><v-icon icon="ri-group-line" size="14"></v-icon> {{ formatNum(overviewData.summary.total_noa) }} Rekening Aktif</p>
              </div>
            </div>
          </v-card-text>
        </v-card>
      </v-col>

      <v-col cols="12" sm="6" lg="3">
        <v-card class="rounded-xl border shadow-sm transition-swing h-100" elevation="0" style="position: relative; overflow: hidden;">
          <div style="position: absolute; top: -20px; right: -20px; width: 120px; height: 120px; opacity: 0.08;">
            <v-icon icon="ri-error-warning-line" size="120" color="#d97706" />
          </div>
          <v-card-text class="pa-5" style="position: relative; z-index: 1;">
            <div class="d-flex justify-space-between align-start">
              <div>
                <p class="text-caption font-weight-bold text-uppercase tracking-widest mb-1" style="color: #64748B; font-family: 'Inter', sans-serif;">TOTAL TUNGGAKAN</p>
                <h2 class="text-h4 font-weight-bold mb-2" style="color: #d97706; font-family: 'Plus Jakarta Sans', sans-serif; line-height: 1.2;">{{ formatRp(overviewData.summary.total_tunggakan || 0) }}</h2>
                <p class="text-caption text-medium-emphasis mb-0" style="font-family: 'Inter', sans-serif;">Tunggakan pokok berjalan</p>
              </div>
            </div>
          </v-card-text>
        </v-card>
      </v-col>

      <v-col cols="12" sm="6" lg="3">
        <v-card class="rounded-xl border shadow-sm transition-swing h-100" elevation="0" style="position: relative; overflow: hidden;">
          <div style="position: absolute; top: -20px; right: -20px; width: 120px; height: 120px; opacity: 0.08;">
            <v-icon icon="ri-alarm-warning-fill" size="120" color="#e11d48" />
          </div>
          <v-card-text class="pa-5" style="position: relative; z-index: 1;">
            <div class="d-flex justify-space-between align-start">
              <div>
                <p class="text-caption font-weight-bold text-uppercase tracking-widest mb-1" style="color: #64748B; font-family: 'Inter', sans-serif;">EXPOSURE NPF</p>
                <h2 class="text-h4 font-weight-bold mb-2" style="color: #e11d48; font-family: 'Plus Jakarta Sans', sans-serif; line-height: 1.2;">{{ formatRp(overviewData.summary.npf_os) }}</h2>
                <p class="text-caption text-medium-emphasis mb-0" style="font-family: 'Inter', sans-serif;"><v-icon icon="ri-close-circle-line" size="14"></v-icon> {{ formatNum(overviewData.summary.npf_noa) }} Rekening Macet</p>
              </div>
            </div>
          </v-card-text>
        </v-card>
      </v-col>

      <v-col cols="12" sm="6" lg="3">
        <v-card class="rounded-xl border shadow-sm transition-swing h-100" elevation="0" style="position: relative; overflow: hidden;">
          <div style="position: absolute; top: -20px; right: -20px; width: 120px; height: 120px; opacity: 0.08;">
            <v-icon icon="ri-percent-line" size="120" :color="overviewData.summary.npf_ratio > 5 ? '#e11d48' : '#10b981'" />
          </div>
          <v-card-text class="pa-5" style="position: relative; z-index: 1;">
            <div class="d-flex justify-space-between align-start">
              <div>
                <p class="text-caption font-weight-bold text-uppercase tracking-widest mb-1" style="color: #64748B; font-family: 'Inter', sans-serif;">NPF RATIO</p>
                <h2 class="text-h4 font-weight-bold mb-2" :style="`color: ${overviewData.summary.npf_ratio > 5 ? '#e11d48' : '#10b981'}; font-family: 'Plus Jakarta Sans', sans-serif; line-height: 1.2;`">{{ Number(overviewData.summary.npf_ratio || 0).toFixed(2) }}%</h2>
                <p class="text-caption text-medium-emphasis mb-0" style="font-family: 'Inter', sans-serif;">{{ overviewData.summary.npf_ratio > 5 ? 'Perlu Perhatian' : 'Sehat' }}</p>
              </div>
            </div>
          </v-card-text>
        </v-card>
      </v-col>
    </v-row>

    <!-- CHARTS & TABLES -->
    <v-row>
      <!-- Trend Area Chart -->
      <v-col cols="12" lg="8">
        <v-card class="rounded-xl border shadow-sm h-100">
          <v-card-text>
            <div v-if="!isLoading && overviewData.trend.length">
              <VueApexCharts type="area" height="350" :options="trendChartOptions" :series="trendChartSeries" />
            </div>
            <div v-else class="d-flex justify-center align-center" style="height: 350px">
              <v-progress-circular indeterminate color="primary"></v-progress-circular>
            </div>
          </v-card-text>
        </v-card>
      </v-col>

      <!-- Donut Chart Kolektibilitas -->
      <v-col cols="12" lg="4">
        <v-card class="rounded-xl border shadow-sm h-100">
          <v-card-text class="d-flex align-center justify-center h-100">
            <div v-if="!isLoading && overviewData.kolektibilitas.length" class="w-100">
              <VueApexCharts type="donut" height="300" :options="kolDonutOptions" :series="kolDonutSeries" />
            </div>
          </v-card-text>
        </v-card>
      </v-col>
    </v-row>

    <!-- TOP RISKS ALERT -->
    <div class="content-card mt-4">
      <div class="pa-4 border-b border-slate-100 d-flex align-center gap-2 bg-error-lighten-5">
        <v-icon icon="ri-alarm-warning-fill" color="error"></v-icon>
        <span class="text-subtitle-1 font-weight-bold text-error">Top High-Risk Alerts (NPF)</span>
      </div>
      
      <div class="content-card__body pa-0">
        <v-table density="comfortable" hover class="fin-table fin-vtable">
          <thead>
            <tr>
              <th class="text-caption font-weight-bold">NASABAH</th>
              <th class="text-right text-caption font-weight-bold">O/S POKOK</th>
              <th class="text-right text-caption font-weight-bold">TUNGGAKAN</th>
              <th class="text-center text-caption font-weight-bold">KOL</th>
            </tr>
          </thead>
          <tbody>
            <tr v-if="isLoading">
              <td colspan="4" class="text-center py-4"><v-progress-circular indeterminate size="24"></v-progress-circular></td>
            </tr>
            <tr v-else-if="overviewData.top_npf.length === 0">
              <td colspan="4" class="text-center py-4 text-medium-emphasis">Tidak ada peringatan NPF tinggi</td>
            </tr>
            <tr v-for="item in overviewData.top_npf" :key="item.nokontrak" v-else>
              <td>
                <div class="font-weight-bold text-body-2">{{ item.nama }}</div>
                <div class="text-caption text-medium-emphasis font-mono">{{ item.nokontrak }}</div>
              </td>
              <td class="text-right font-weight-medium">{{ formatRp(item.osmdlc) }}</td>
              <td class="text-right text-error font-weight-bold">{{ formatRp(item.tgkmdl) }}</td>
              <td class="text-center">
                <v-chip size="x-small" :color="badgeColor(item.colbaru)" variant="flat" class="font-weight-bold">
                  {{ item.colbaru }}
                </v-chip>
              </td>
            </tr>
          </tbody>
        </v-table>
      </div>
    </div>

  </div>
</template>

<style scoped>
.gap-2 { gap: 8px; }
.gap-3 { gap: 12px; }
.gap-4 { gap: 16px; }
.bg-gradient-primary {
  background: linear-gradient(135deg, #1e3a8a 0%, #3b82f6 100%) !important;
}
.lh-sm { line-height: 1.25 !important; }
.transition-swing { transition: all 0.3s cubic-bezier(0.25, 0.8, 0.5, 1); }
</style>
