<script setup>
import { ref, computed, onMounted } from 'vue'
import DefaultLayout from '@/layouts/default.vue'
import '@/assets/css/cif-shared.css'
import { formatExactNumber, formatTruncatedPercentage } from '@/utils/money'

defineOptions({ layout: DefaultLayout })

const isLoading = ref(true)
const rtData = ref(null)
const hasData = computed(() => rtData.value !== null)

async function fetchSummary() {
  isLoading.value = true
  try {
    const response = await fetch('/api/v1/cif/audit/summary')
    const json = await response.json()
    if (!response.ok || !json.success) {
      throw new Error(json.message || 'Gagal memuat ringkasan CIF.')
    }
    rtData.value = json.data
  } catch {
    rtData.value = null
  } finally {
    isLoading.value = false
  }
}

const quickLinks = [
  { title: 'Audit CIF Pembiayaan', icon: 'ri-bank-line', color: '#10B981', bg: 'rgba(16,185,129,0.12)', route: '/cif/pembiayaan', desc: 'Pengecekan nasabah pembiayaan' },
  { title: 'Audit CIF Tabungan', icon: 'ri-wallet-3-line', color: '#0ea5e9', bg: 'rgba(14,165,233,0.12)', route: '/cif/tabungan', desc: 'Pengecekan nasabah tabungan' },
  { title: 'Audit CIF Deposito', icon: 'ri-safe-2-line', color: '#f59e0b', bg: 'rgba(245,158,11,0.12)', route: '/cif/deposito', desc: 'Pengecekan nasabah deposito' },
]

function formatNumber(value) {
  if (value === null || value === undefined || value === '') return '-'
  return formatExactNumber(value)
}

const cards = computed(() => [
  {
    id: 'total_nasabah',
    title: 'Total Nasabah (CIF)',
    value: rtData.value?.summary?.total_nasabah ?? 0,
    format: 'number',
    subtitle: 'Keseluruhan data terdaftar',
    icon: 'ri-group-line',
    color: 'primary'
  },
  {
    id: 'persen_lengkap',
    title: 'Persentase Lengkap',
    value: rtData.value?.summary?.persen_lengkap ?? 0,
    format: 'percent',
    subtitle: 'Data valid dan lengkap',
    icon: 'ri-checkbox-circle-line',
    color: 'success'
  },
  {
    id: 'persen_belum_lengkap',
    title: 'Persentase Belum Lengkap',
    value: rtData.value?.summary?.persen_belum_lengkap ?? 0,
    format: 'percent',
    subtitle: 'Data perlu pembaruan',
    icon: 'ri-close-circle-line',
    color: 'error'
  },
  {
    id: 'total_anomali',
    title: 'Total Anomali Data',
    value: rtData.value?.summary?.total_anomali ?? 0,
    format: 'number',
    subtitle: 'Indikasi data tidak wajar',
    icon: 'ri-alert-line',
    color: 'warning'
  }
])

function formatValue(value, format) {
  if (value === null || value === undefined) return '-'
  if (format === 'number') return formatExactNumber(value)
  if (format === 'percent') return formatTruncatedPercentage(value)
  return value
}

function getCardColor(color) {
  const colorMap = {
    primary: '#10b981',
    success: '#10B981',
    error: '#EF4444',
    warning: '#F59E0B'
  }
  return colorMap[color] || '#10b981'
}

const donutChartOptions = computed(() => {
  const labels = []
  const colors = []
  if (rtData.value?.status_distribusi) {
    rtData.value.status_distribusi.forEach(item => {
      labels.push(item.status)
      colors.push(item.color)
    })
  }
  return {
    chart: { type: 'donut', fontFamily: 'Inter, sans-serif' },
    labels,
    colors,
    stroke: { width: 0 },
    dataLabels: {
      enabled: true,
      formatter: value => formatTruncatedPercentage(value)
    },
    plotOptions: {
      pie: {
        donut: {
          size: '72%',
          labels: {
            show: true,
            name: { show: true },
            value: { show: true, formatter: value => formatNumber(value) },
            total: {
              show: true,
              showAlways: true,
              label: 'Total CIF',
              formatter: chart => formatNumber(chart.globals.seriesTotals.reduce((total, value) => total + value, 0))
            }
          }
        }
      }
    },
    legend: { position: 'bottom', fontSize: '12px', fontWeight: 600 }
  }
})

const donutChartSeries = computed(() => {
  if (!rtData.value?.status_distribusi) return []
  return rtData.value.status_distribusi.map(item => item.total)
})

const barChartOptions = computed(() => {
  const categories = []
  if (rtData.value?.top_anomali_cabang) {
    rtData.value.top_anomali_cabang.forEach(item => {
      categories.push(item.cabang)
    })
  }
  return {
    chart: { type: 'bar', fontFamily: 'Inter, sans-serif', toolbar: { show: false } },
    colors: ['#EF4444'],
    plotOptions: { bar: { borderRadius: 4, horizontal: true } },
    dataLabels: { enabled: true, formatter: value => formatNumber(value) },
    xaxis: { categories, labels: { formatter: value => formatNumber(value) } },
    tooltip: { y: { formatter: value => `${formatNumber(value)} CIF` } }
  }
})

const barChartSeries = computed(() => {
  const data = []
  if (rtData.value?.top_anomali_cabang) {
    rtData.value.top_anomali_cabang.forEach(item => {
      data.push(item.anomali)
    })
  }
  return [{ name: 'Total Anomali', data }]
})

onMounted(() => {
  fetchSummary()
})
</script>

<template>
  <div class="cif-page px-4 pt-0">

    <!-- HERO HEADER -->
    <div class="cif-hero mb-6">
      <div class="cif-hero__deco"></div>
      <div class="cif-hero__inner">
        <div class="cif-hero__top">
          <div class="cif-hero__icon">
            <v-icon icon="ri-user-search-line" size="26" color="white" />
          </div>
          <div class="cif-hero__meta">
            <h1 class="cif-hero__title">Dashboard CIF & Pengecekan Nasabah</h1>
            <p class="cif-hero__subtitle">Overview status kelengkapan dan anomali data Customer Information File (Individu & Badan Hukum).</p>
            <div class="cif-hero__badges">
              <span class="cif-badge cif-badge--teal">Islamic Banking</span>
              <span class="cif-badge cif-badge--glass">
                <v-icon size="10" color="white">ri-database-2-line</v-icon>
                {{ rtData?.database || 'Memuat...' }}
              </span>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- LOADING SKELETON -->
    <div v-if="isLoading">
      <v-row class="mb-4">
        <v-col v-for="i in 4" :key="i" cols="12" sm="6" lg="3">
          <v-skeleton-loader type="card" rounded="xl" />
        </v-col>
      </v-row>
      <v-row>
        <v-col cols="12" lg="4"><v-skeleton-loader type="image" height="350" rounded="xl" /></v-col>
        <v-col cols="12" lg="8"><v-skeleton-loader type="image" height="350" rounded="xl" /></v-col>
      </v-row>
    </div>

    <!-- DASHBOARD CONTENT -->
    <template v-else-if="hasData">

      <!-- Quick Links Navigation (3 Columns) -->
      <div class="mb-6">
        <div class="cif-section__label">
          <v-icon size="13" class="me-1">ri-navigation-line</v-icon>
          Navigasi Cepat
        </div>
        <v-row>
          <v-col v-for="(link, i) in quickLinks" :key="i" cols="12" sm="4">
            <a :href="link.route" style="text-decoration: none; display: block;">
              <div class="content-card quick-link-card pa-0">
                <div class="d-flex align-center gap-3 pa-4">
                  <div class="content-card__icon" :style="{ background: link.bg }">
                    <v-icon :icon="link.icon" size="22" :style="{ color: link.color }" />
                  </div>
                  <div>
                    <div class="font-weight-bold text-sm" style="font-size:14px; color:#1e293b;">{{ link.title }}</div>
                    <div class="text-xs mt-1" style="font-size:11.5px; color:#94a3b8; line-height:1.3;">{{ link.desc }}</div>
                  </div>
                </div>
              </div>
            </a>
          </v-col>
        </v-row>
      </div>

      <!-- KPI Cards -->
      <v-row class="mb-2">
        <v-col v-for="card in cards" :key="card.id" cols="12" sm="6" lg="3">
          <v-card
            elevation="0"
            :style="{
              border: '1px solid rgba(var(--v-border-color), 0.08)',
              borderRadius: '12px',
              overflow: 'hidden',
              position: 'relative'
            }"
            class="transition-swing kpi-card-hover"
          >
            <!-- Background accent stripe -->
            <div
              :style="{
                position: 'absolute',
                top: 0,
                left: 0,
                right: 0,
                height: '4px',
                background: `linear-gradient(90deg, ${getCardColor(card.color)} 0%, transparent 100%)`
              }"
            />
            
            <v-card-text class="pa-5" style="padding: 20px !important;">
              <div class="d-flex align-start justify-space-between">
                <!-- Content -->
                <div class="flex-grow-1 pe-4">
                  <p 
                    class="text-body-2 text-medium-emphasis mb-2 font-weight-medium"
                    style="font-size: 12px; letter-spacing: 0.5px; text-transform: uppercase;"
                  >
                    {{ card.title }}
                  </p>
                  
                  <h2 
                    class="text-h4 font-weight-bold mb-2"
                    :style="{ 
                      color: getCardColor(card.color),
                      fontFamily: 'Plus Jakarta Sans, sans-serif',
                      lineHeight: 1.2
                    }"
                  >
                    {{ formatValue(card.value, card.format) }}
                  </h2>
                  
                  <p class="text-caption text-medium-emphasis mb-0">
                    {{ card.subtitle }}
                  </p>
                </div>
                
                <!-- Icon Container -->
                <div
                  :style="{
                    width: '52px',
                    height: '52px',
                    borderRadius: '12px',
                    background: `${getCardColor(card.color)}1E`, // 12% opacity roughly
                    display: 'flex',
                    alignItems: 'center',
                    justifyContent: 'center'
                  }"
                >
                  <v-icon
                    :icon="card.icon"
                    size="22px"
                    :color="getCardColor(card.color)"
                  />
                </div>
              </div>
            </v-card-text>
          </v-card>
        </v-col>
      </v-row>

      <!-- Visual Analytics -->
      <v-row class="mt-4 mb-6">
        <!-- Donut Chart -->
        <v-col cols="12" lg="4">
          <div class="content-card">
            <div class="content-card__accent-top" style="background: linear-gradient(90deg, #10B981, #34D399);"></div>
            <div class="content-card__header">
              <div>
                <div class="content-card__title">Distribusi Status Kelengkapan</div>
                <div class="content-card__subtitle">Berdasarkan validasi NIK dan pasangan</div>
              </div>
              <div class="content-card__icon cif-icon-green">
                <v-icon icon="ri-pie-chart-2-fill" size="20" />
              </div>
            </div>
            <div class="content-card__body">
              <apexchart
                v-if="donutChartSeries.length"
                type="donut" height="300"
                :options="donutChartOptions"
                :series="donutChartSeries"
              />
              <div v-else class="cif-empty py-12">
                <v-icon icon="ri-bar-chart-2-line" size="40" class="cif-empty__icon" />
                <div class="cif-empty__desc">Data distribusi tidak tersedia</div>
              </div>
            </div>
          </div>
        </v-col>

        <!-- Bar Chart -->
        <v-col cols="12" lg="8">
          <div class="content-card">
            <div class="content-card__accent-top" style="background: linear-gradient(90deg, #EF4444, #F87171);"></div>
            <div class="content-card__header">
              <div>
                <div class="content-card__title">Top 5 Cabang (Anomali Terbanyak)</div>
                <div class="content-card__subtitle">Fokus prioritas perbaikan data CIF</div>
              </div>
              <a href="/cif/quality" style="text-decoration: none;">
                <div class="cif-badge cif-badge--glass" style="background: rgba(239,68,68,0.12); color: #EF4444; border-color: rgba(239,68,68,0.2);">
                  Audit Quality ->
                </div>
              </a>
            </div>
            <div class="content-card__body">
              <apexchart
                v-if="barChartSeries[0].data.length"
                type="bar" height="300"
                :options="barChartOptions"
                :series="barChartSeries"
              />
              <div v-else class="cif-empty py-12">
                <div class="cif-empty__desc">Data tren tidak tersedia</div>
              </div>
            </div>
          </div>
        </v-col>
      </v-row>

    </template>

    <!-- Empty State -->
    <div v-if="!isLoading && !hasData" class="cif-empty mt-6">
      <v-icon icon="ri-database-warning-line" size="56" class="cif-empty__icon" />
      <div class="cif-empty__title">Data Tidak Tersedia</div>
      <div class="cif-empty__desc">Tidak ada data yang dapat dimuat. Periksa koneksi API.</div>
    </div>

  </div>
</template>

<style scoped>
.cif-page { background: var(--cif-bg); min-height: 100vh; padding-bottom: 48px; }

.quick-link-card {
  cursor: pointer;
  transition: transform 0.22s cubic-bezier(0.4,0,0.2,1), box-shadow 0.22s ease;
}
.quick-link-card:hover {
  transform: translateY(-4px);
  box-shadow: 0 8px 24px rgba(0,0,0,0.10) !important;
}

.kpi-card-hover {
  transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}
.kpi-card-hover:hover {
  transform: translateY(-2px);
  box-shadow: 0 8px 24px rgba(16, 185, 129, 0.12) !important;
}
</style>
