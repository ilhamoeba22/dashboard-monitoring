<template>
  <DefaultLayout>
    <Head title="Repayment Rate (Akuisisi & Retensi)" />

    <div class="fin-page px-4 pt-0">
      <!-- ── HERO HEADER ─────────────────────────────────────────── -->
      <div class="fin-hero mb-6">
        <div class="fin-hero__deco"></div>
        <div class="fin-hero__inner">
          <div class="d-flex flex-column flex-md-row justify-space-between align-start align-md-center gap-4">
            <div class="d-flex align-center gap-4">
              <div class="fin-hero__icon fin-icon-blue">
                <v-icon icon="ri-pie-chart-2-fill" size="26" color="white" />
              </div>
              <div class="fin-hero__meta">
                <h1 class="fin-hero__title">Repayment Rate (Akuisisi & Retensi)</h1>
                <p class="fin-hero__subtitle">Monitor kinerja pembayaran nasabah baru dan retensi</p>
                <div class="fin-hero__badges">
                  <span class="fin-badge fin-badge--info">📈 Analytics</span>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- KPI Cards -->
      <div class="kpi-cards-grid mb-6">
        <div class="kpi-card kpi-card--success">
          <div class="kpi-card__accent" style="background: linear-gradient(90deg, #10b981, #34d399)"></div>
          <div class="kpi-card__inner">
            <div class="kpi-card__header">
              <span class="kpi-card__label text-emerald-600">Total Cash In</span>
              <div class="kpi-card__icon fin-icon-green">
                <v-icon icon="ri-hand-coin-line" size="18" />
              </div>
            </div>
            <div class="kpi-card__value mt-2">{{ formatCurrency(parseFloat(summary.total_cash_in || 0)) }}</div>
            <div class="kpi-card__sub text-emerald-600 font-weight-bold">Realisasi Pembayaran</div>
          </div>
        </div>

        <div class="kpi-card">
          <div class="kpi-card__accent" style="background: linear-gradient(90deg, #1e40af, #3b82f6)"></div>
          <div class="kpi-card__inner">
            <div class="kpi-card__header">
              <span class="kpi-card__label text-blue-600">Rate Analisis</span>
              <div class="kpi-card__icon fin-icon-blue">
                <v-icon icon="ri-percent-line" size="18" />
              </div>
            </div>
            <div class="kpi-card__value mt-2">{{ parseFloat(summary.overall_rr_pct || 0).toFixed(2) }}%</div>
            <div class="kpi-card__sub text-blue-600 font-weight-bold">Bayar / Tagihan</div>
          </div>
        </div>

        <div class="kpi-card kpi-card--warning">
          <div class="kpi-card__accent" style="background: linear-gradient(90deg, #d97706, #fbbf24)"></div>
          <div class="kpi-card__inner">
            <div class="kpi-card__header">
              <span class="kpi-card__label text-amber-600">Target Tagihan</span>
              <div class="kpi-card__icon fin-icon-amber">
                <v-icon icon="ri-calendar-event-line" size="18" />
              </div>
            </div>
            <div class="kpi-card__value mt-2">{{ formatCurrency(parseFloat(summary.total_tagihan || 0)) }}</div>
            <div class="kpi-card__sub text-amber-600 font-weight-bold">Total Tagihan Aktif</div>
          </div>
        </div>
      </div>

      <!-- Risk Breakdown Cards -->
      <div class="kpi-cards-grid mb-6">
        <div class="kpi-card">
          <div class="kpi-card__accent" style="background: linear-gradient(90deg, #10b981, #34d399)"></div>
          <div class="kpi-card__inner">
            <div class="kpi-card__header">
              <span class="kpi-card__label">Good (RR ≥ 90%)</span>
              <div class="kpi-card__icon fin-icon-green">
                <v-icon icon="ri-emotion-happy-line" size="18" />
              </div>
            </div>
            <div class="kpi-card__value mt-2 text-success">{{ parseInt(summary.good_count || 0) }} nasabah</div>
          </div>
        </div>

        <div class="kpi-card">
          <div class="kpi-card__accent" style="background: linear-gradient(90deg, #f59e0b, #fbbf24)"></div>
          <div class="kpi-card__inner">
            <div class="kpi-card__header">
              <span class="kpi-card__label">Warning (RR 70–90%)</span>
              <div class="kpi-card__icon fin-icon-amber">
                <v-icon icon="ri-emotion-normal-line" size="18" />
              </div>
            </div>
            <div class="kpi-card__value mt-2 text-warning">{{ parseInt(summary.warning_count || 0) }} nasabah</div>
          </div>
        </div>

        <div class="kpi-card">
          <div class="kpi-card__accent" style="background: linear-gradient(90deg, #ef4444, #f87171)"></div>
          <div class="kpi-card__inner">
            <div class="kpi-card__header">
              <span class="kpi-card__label">At Risk (RR &lt; 70%)</span>
              <div class="kpi-card__icon fin-icon-red">
                <v-icon icon="ri-emotion-unhappy-line" size="18" />
              </div>
            </div>
            <div class="kpi-card__value mt-2 text-error">{{ parseInt(summary.at_risk_count || 0) }} nasabah</div>
          </div>
        </div>
      </div>

      <!-- Filter Bar -->
      <v-card class="d-flex flex-wrap align-center ga-3 pa-4 bg-white rounded-xl border shadow-sm mb-6" elevation="0">
        <div style="max-width: 300px; flex: 1 1 auto;">
          <v-text-field
            v-model="filters.search"
            label="Cari Nama/Kontrak"
            density="compact"
            variant="outlined"
            prepend-inner-icon="ri-search-line"
            clearable
            hide-details
            rounded="lg"
            @update:model-value="debouncedSearch"
          />
        </div>
        <div style="max-width: 200px; flex: 1 1 auto;">
          <v-select
            v-model="filters.ao"
            :items="aoList"
            label="Account Officer"
            density="compact"
            variant="outlined"
            clearable
            hide-details
            rounded="lg"
            @update:model-value="fetchData"
          />
        </div>
        <div style="max-width: 200px; flex: 1 1 auto;">
          <v-select
            v-model="filters.onboarding_months"
            :items="onboardingOptions"
            label="Periode Onboarding"
            density="compact"
            variant="outlined"
            hide-details
            rounded="lg"
            @update:model-value="fetchData"
          />
        </div>
        <div style="max-width: 180px; flex: 1 1 auto;">
          <v-select
            v-model="filters.risk_status"
            :items="riskOptions"
            label="Risk Status"
            density="compact"
            variant="outlined"
            clearable
            hide-details
            rounded="lg"
            @update:model-value="fetchData"
          />
        </div>
        <v-spacer />
        <v-btn color="success" variant="tonal" class="rounded-lg" prepend-icon="ri-file-excel-2-line" @click="exportExcel" :loading="exporting">
          Export
        </v-btn>
      </v-card>

      <!-- Chart Section -->
      <v-row class="mb-6">
        <v-col cols="12" md="6">
          <v-card class="rounded-xl border shadow-sm bg-white" elevation="0">
            <v-card-title class="d-flex align-center pa-4 text-h6 font-weight-bold">
              <v-icon icon="ri-pie-chart-line" class="mr-2" color="primary"></v-icon>
              Distribusi Risiko Nasabah
            </v-card-title>
            <v-divider></v-divider>
            <v-card-text class="pa-6">
              <div v-if="loading" class="d-flex align-center justify-center" style="height: 300px">
                <v-skeleton-loader type="image" height="300" />
              </div>
              <div v-else-if="!donutChartSeries || donutChartSeries.length === 0" class="pa-12 text-center bg-slate-50 rounded-xl border">
                <v-icon icon="ri-inbox-archive-line" size="64" class="text-slate-300 mb-4"></v-icon>
                <div class="text-h6 text-slate-500">Belum ada data distribusi risiko</div>
              </div>
              <div v-else>
                <apexchart key="donut-chart" type="donut" height="300" :options="donutChartOptions" :series="donutChartSeries" />
              </div>
            </v-card-text>
          </v-card>
        </v-col>
        <v-col cols="12" md="6">
          <v-card class="rounded-xl border shadow-sm bg-white" elevation="0">
            <v-card-title class="d-flex align-center pa-4 text-h6 font-weight-bold">
              <v-icon icon="ri-bar-chart-grouped-line" class="mr-2" color="primary"></v-icon>
              Trend RR per AO (Top 5)
            </v-card-title>
            <v-divider></v-divider>
            <v-card-text class="pa-6">
              <div v-if="loading" class="d-flex align-center justify-center" style="height: 300px">
                <v-skeleton-loader type="image" height="300" />
              </div>
              <div v-else-if="!barChartSeries || barChartSeries.length === 0" class="pa-12 text-center bg-slate-50 rounded-xl border">
                <v-icon icon="ri-inbox-archive-line" size="64" class="text-slate-300 mb-4"></v-icon>
                <div class="text-h6 text-slate-500">Belum ada data trend AO</div>
              </div>
              <div v-else>
                <apexchart key="bar-chart" type="bar" height="300" :options="barChartOptions" :series="barChartSeries" />
              </div>
            </v-card-text>
          </v-card>
        </v-col>
      </v-row>

      <!-- Data Table -->
      <div class="content-card">
        <div v-if="!data || data.length === 0" class="pa-12 text-center bg-slate-50 rounded-xl">
          <v-icon icon="ri-inbox-archive-line" size="64" class="text-slate-300 mb-4"></v-icon>
          <div class="text-h6 text-slate-500">Tidak ada data nasabah ditemukan</div>
          <div class="text-caption text-slate-400 mt-2">Coba ubah filter periode onboarding atau pencarian</div>
        </div>
        <div v-else class="content-card__body pa-0">
          <v-data-table
            :headers="headers"
            :items="filteredData"
            :loading="loading"
            :items-per-page="50"
            class="fin-table fin-vtable bg-transparent"
            density="comfortable"
          >
            <template #item.risk_status="{ item }">
              <v-chip
                :color="getRiskColor(item.risk_status)"
                size="small"
                variant="tonal"
                class="font-weight-bold px-3"
                :prepend-icon="getRiskIcon(item.risk_status)"
              >
                {{ item.risk_status || '-' }}
              </v-chip>
            </template>

            <template #item.nama_nasabah="{ item }">
              <div class="font-weight-medium">{{ item.nama_nasabah || 'N/A' }}</div>
              <div class="text-caption text-grey">{{ item.nokontrak || '-' }}</div>
            </template>

            <template #item.tgleff="{ item }">
              <div>{{ formatDate(item.tgleff) }}</div>
              <div class="text-caption text-grey">{{ parseInt(item.days_since_onboarding || 0) }} hari</div>
            </template>

            <template #item.rr_pct="{ item }">
              <div class="d-flex align-center" style="min-width: 140px">
                <div class="flex-grow-1 mr-2">
                  <v-progress-linear
                    :model-value="safePct(item.rr_pct)"
                    :color="getRRColor(safePct(item.rr_pct))"
                    height="8"
                    rounded
                  ></v-progress-linear>
                </div>
                <v-chip
                  :color="getRRColor(safePct(item.rr_pct))"
                  size="small"
                  variant="tonal"
                  class="font-weight-bold flex-shrink-0"
                >
                  {{ safePct(item.rr_pct).toFixed(2) }}%
                </v-chip>
              </div>
            </template>

            <template #item.tag_current="{ item }">
              <div class="font-weight-bold">
                {{ formatCurrency(safeSum(item.tag_current_mdl, item.tag_current_mgn)) }}
              </div>
              <div class="text-caption text-grey">
                Modal: {{ formatCurrency(parseFloat(item.tag_current_mdl || 0)) }}
              </div>
            </template>

            <template #item.cash_in="{ item }">
              <div class="font-weight-bold text-success">
                {{ formatCurrency(safeSum(item.cash_in_mdl, item.cash_in_mgn)) }}
              </div>
              <div class="text-caption text-grey">
                Modal: {{ formatCurrency(parseFloat(item.cash_in_mdl || 0)) }}
              </div>
            </template>

            <template #item.recovery_rate="{ item }">
              <v-chip
                :color="getRRColor(safePct(item.recovery_rate))"
                size="small"
                variant="tonal"
                class="font-weight-bold px-3"
              >
                {{ safePct(item.recovery_rate).toFixed(2) }}%
              </v-chip>
            </template>

            <template #item.nama_ao="{ item }">
              <v-chip size="small" variant="outlined" color="primary">
                <v-icon start icon="ri-user-follow-line" size="14"></v-icon>
                {{ item.nama_ao || 'N/A' }}
              </v-chip>
            </template>
          </v-data-table>
        </div>
      </div>
    </div>
  </DefaultLayout>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { Head } from '@inertiajs/vue3'
import DefaultLayout from '@/Layouts/default.vue'
import axios from 'axios'
import VueApexCharts from 'vue3-apexcharts'
import '@/assets/css/financing-shared.css'

const apexchart = VueApexCharts

const loading = ref(false)
const exporting = ref(false)
const data = ref([])
const summary = ref({})

const filters = ref({
  ao: null,
  onboarding_months: 6,
  risk_status: null,
  search: ''
})

const onboardingOptions = [
  { title: 'Last 1 Month', value: 1 },
  { title: 'Last 3 Months', value: 3 },
  { title: 'Last 6 Months', value: 6 },
  { title: 'Last 12 Months', value: 12 }
]

const riskOptions = [
  { title: 'Good (≥90%)', value: 'Good' },
  { title: 'Warning (70-90%)', value: 'Warning' },
  { title: 'At Risk (<70%)', value: 'At Risk' }
]

// =========================================================================
// SAFE COMPUTED HELPERS
// =========================================================================

const aoList = computed(() => {
  if (!data.value || !Array.isArray(data.value) || data.value.length === 0) return []
  const aos = [...new Set(data.value.map(item => item.nama_ao).filter(Boolean))]
  return aos.sort()
})

// =========================================================================
// TABLE HEADERS
// =========================================================================

const headers = [
  { title: 'Risk', key: 'risk_status', sortable: true, width: '130px' },
  { title: 'Nasabah / Kontrak', key: 'nama_nasabah', sortable: true, width: '220px' },
  { title: 'Tgl Akad', key: 'tgleff', sortable: true, width: '140px' },
  { title: 'Tagihan', key: 'tag_current', sortable: true, align: 'end', width: '160px' },
  { title: 'Cash In', key: 'cash_in', sortable: true, align: 'end', width: '160px' },
  { title: 'RR %', key: 'rr_pct', sortable: true, width: '160px' },
  { title: 'Rec Rate', key: 'recovery_rate', sortable: true, width: '110px' },
  { title: 'AO', key: 'nama_ao', sortable: true, width: '160px' }
]

// =========================================================================
// FILTERED DATA (Client-side filter support)
// =========================================================================

const filteredData = computed(() => {
  if (!data.value || !Array.isArray(data.value) || data.value.length === 0) return []

  let result = data.value

  // AO filter
  if (filters.value.ao) {
    result = result.filter(item => item.nama_ao === filters.value.ao)
  }

  // Risk status filter
  if (filters.value.risk_status) {
    result = result.filter(item => item.risk_status === filters.value.risk_status)
  }

  // Search filter
  if (filters.value.search) {
    const search = filters.value.search.toLowerCase()
    result = result.filter(item =>
      (item.nama_nasabah || '').toLowerCase().includes(search) ||
      (item.nokontrak || '').toLowerCase().includes(search)
    )
  }

  return result
})

// =========================================================================
// CHARTS
// =========================================================================

const donutChartSeries = computed(() => {
  const good = parseInt(summary.value.good_count || 0)
  const warning = parseInt(summary.value.warning_count || 0)
  const atRisk = parseInt(summary.value.at_risk_count || 0)
  if (good === 0 && warning === 0 && atRisk === 0) return []
  return [good, warning, atRisk]
})

const donutChartOptions = computed(() => ({
  chart: { type: 'donut', toolbar: { show: false } },
  labels: ['Good', 'Warning', 'At Risk'],
  colors: ['#059669', '#F59E0B', '#DC2626'],
  legend: { position: 'bottom' },
  dataLabels: {
    enabled: true,
    formatter: (val) => (parseFloat(val) || 0).toFixed(1) + '%'
  },
  plotOptions: {
    pie: {
      donut: {
        size: '60%',
        labels: {
          show: true,
          name: { show: true },
          value: { show: true, formatter: (val) => parseInt(val || 0) + ' org' },
          total: {
            show: true,
            label: 'Total',
            formatter: () => parseInt(summary.value.total_nasabah || 0) + ' nasabah'
          }
        }
      }
    }
  }
}))

const barChartSeries = computed(() => {
  if (!data.value || !Array.isArray(data.value) || data.value.length === 0) return []

  const aoGroups = data.value.reduce((acc, item) => {
    const ao = item.nama_ao || 'Tanpa AO'
    if (!acc[ao]) acc[ao] = { total: 0, count: 0 }
    acc[ao].total += safePct(item.rr_pct)
    acc[ao].count += 1
    return acc
  }, {})

  const top5 = Object.entries(aoGroups)
    .map(([ao, stats]) => ({ ao, avg: stats.count > 0 ? stats.total / stats.count : 0 }))
    .sort((a, b) => b.avg - a.avg)
    .slice(0, 5)

  if (top5.length === 0) return []

  return [{
    name: 'Avg RR',
    data: top5.map(item => parseFloat(item.avg.toFixed(2)))
  }]
})

const barChartOptions = computed(() => {
  const cats = barChartSeries.value.length > 0 && barChartSeries.value[0]?.data
    ? (() => {
        if (!data.value || !Array.isArray(data.value) || data.value.length === 0) return []
        const aoGroups = data.value.reduce((acc, item) => {
          const ao = item.nama_ao || 'Tanpa AO'
          if (!acc[ao]) acc[ao] = { total: 0, count: 0 }
          acc[ao].total += safePct(item.rr_pct)
          acc[ao].count += 1
          return acc
        }, {})
        return Object.entries(aoGroups)
          .map(([ao, stats]) => ({ ao, avg: stats.count > 0 ? stats.total / stats.count : 0 }))
          .sort((a, b) => b.avg - a.avg)
          .slice(0, 5)
          .map(item => item.ao)
      })()
    : []

  return {
    chart: { type: 'bar', toolbar: { show: false } },
    xaxis: { categories: cats },
    yaxis: {
      title: { text: 'Repayment Rate (%)' },
      labels: { formatter: (val) => (parseFloat(val) || 0).toFixed(0) + '%' }
    },
    colors: ['#1E40AF'],
    dataLabels: {
      enabled: true,
      formatter: (val) => (parseFloat(val) || 0).toFixed(1) + '%'
    },
    grid: { borderColor: '#f1f5f9', strokeDashArray: 4 }
  }
})

// =========================================================================
// API
// =========================================================================

const fetchData = async () => {
  loading.value = true
  try {
    const params = {
      ao: filters.value.ao,
      onboarding_months: filters.value.onboarding_months,
      risk_status: filters.value.risk_status,
      search: filters.value.search
    }
    const response = await axios.get('/api/v1/financing/performance/repayment-rate-new', { params })
    data.value = Array.isArray(response.data?.data) ? response.data.data : []
    summary.value = response.data?.summary || {}
  } catch (error) {
    console.error('Error fetching data:', error)
    data.value = []
    summary.value = {}
  } finally {
    loading.value = false
  }
}

const debouncedSearch = (() => {
  let timeout
  return () => {
    clearTimeout(timeout)
    timeout = setTimeout(fetchData, 500)
  }
})()

// =========================================================================
// SAFE FORMATTERS (ANTI-RpNaN)
// =========================================================================

const safeNum = (val) => {
  const n = parseFloat(val)
  return Number.isFinite(n) ? n : 0
}

const safePct = (val) => {
  const n = parseFloat(val)
  if (!Number.isFinite(n) || n < 0) return 0
  if (n > 100) return 100
  return n
}

const safeSum = (a, b) => {
  return safeNum(a) + safeNum(b)
}

const formatCurrency = (value) => {
  const num = safeNum(value)
  if (num === 0) return 'Rp 0'
  return new Intl.NumberFormat('id-ID', {
    style: 'currency',
    currency: 'IDR',
    minimumFractionDigits: 0
  }).format(num)
}

const formatDate = (date) => {
  if (!date || date === '0000-00-00') return '-'
  try {
    const d = new Date(date)
    if (isNaN(d.getTime())) return '-'
    return d.toLocaleDateString('id-ID', { year: 'numeric', month: 'short', day: 'numeric' })
  } catch {
    return '-'
  }
}

// =========================================================================
// COLOR HELPERS
// =========================================================================

const getRiskColor = (status) => {
  const colors = { Good: 'success', Warning: 'warning', 'At Risk': 'error' }
  return colors[status] || 'grey'
}

const getRiskIcon = (status) => {
  const icons = {
    Good: 'ri-check-double-line',
    Warning: 'ri-alert-line',
    'At Risk': 'ri-alarm-warning-line'
  }
  return icons[status] || 'ri-question-line'
}

const getRRColor = (rate) => {
  if (rate >= 90) return 'success'
  if (rate >= 70) return 'warning'
  return 'error'
}

const exportExcel = async () => {
  exporting.value = true
  try {
    const params = new URLSearchParams({
      ao: filters.value.ao || '',
      onboarding_months: String(filters.value.onboarding_months || 6),
      risk_status: filters.value.risk_status || '',
      search: filters.value.search || ''
    })
    window.location.href = `/api/v1/financing/performance/repayment-rate-new/export?${params.toString()}`
  } catch (error) {
    console.error('Export error:', error)
  } finally {
    setTimeout(() => { exporting.value = false }, 2000)
  }
}

onMounted(() => {
  fetchData()
})
</script>

<style scoped>
.rounded-xl {
  border-radius: 16px !important;
}

.shadow-sm {
  box-shadow: 0 1px 3px 0 rgb(0 0 0 / 0.1), 0 1px 2px -1px rgb(0 0 0 / 0.1) !important;
}

.bg-slate-50 {
  background-color: #f8fafc !important;
}

.text-slate-300 {
  color: #cbd5e1 !important;
}

.text-slate-500 {
  color: #64748b !important;
}

.text-slate-600 {
  color: #475569 !important;
}

.border {
  border: 1px solid #e2e8f0 !important;
}

/* Data Table Enterprise Styling */
:deep(.v-data-table) {
  background-color: transparent !important;
}

:deep(.v-data-table .v-data-table__th) {
  background-color: #f8fafc !important;
  color: #475569 !important;
  font-weight: 900 !important;
  font-size: 11px !important;
  text-transform: uppercase !important;
  letter-spacing: 0.05em !important;
  border-bottom: 1px solid #e2e8f0 !important;
}

:deep(.v-data-table .v-data-table__td) {
  color: #334155 !important;
  font-size: 13px !important;
  padding-top: 12px !important;
  padding-bottom: 12px !important;
  border-bottom: 1px solid #f1f5f9 !important;
}

:deep(.v-chip) {
  font-weight: 700 !important;
}

/* Avatar lighten colors */
:deep(.v-avatar.green-lighten-5) {
  background-color: #ecfdf5 !important;
}
:deep(.v-avatar.blue-lighten-5) {
  background-color: #eff6ff !important;
}
:deep(.v-avatar.amber-lighten-5) {
  background-color: #fffbeb !important;
}
:deep(.v-avatar.red-lighten-5) {
  background-color: #fef2f2 !important;
}
</style>
