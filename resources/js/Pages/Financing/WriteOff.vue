<template>
  <DefaultLayout>
    <Head title="Write-Off Monitoring" />
    
    <div class="fin-page px-4 pt-0">
      <!-- ── HERO HEADER ─────────────────────────────────────────── -->
      <div class="fin-hero mb-6">
        <div class="fin-hero__deco"></div>
        <div class="fin-hero__inner">
          <div class="d-flex flex-column flex-md-row justify-space-between align-start align-md-center gap-4">
            <div class="d-flex align-center gap-4">
              <div class="fin-hero__icon fin-icon-red">
                <v-icon icon="ri-delete-bin-5-fill" size="26" color="white" />
              </div>
              <div class="fin-hero__meta">
                <h1 class="fin-hero__title">Write-Off Monitoring</h1>
                <p class="fin-hero__subtitle">Monitor pembiayaan yang di-write off dan recovery rate</p>
                <div class="fin-hero__badges">
                  <span class="fin-badge fin-badge--danger">🗑 Hapus Buku</span>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- KPI Cards -->
      <div class="kpi-cards-grid mb-6">
        <div class="kpi-card kpi-card--danger">
          <div class="kpi-card__accent" style="background: linear-gradient(90deg, #e11d48, #fb7185)"></div>
          <div class="kpi-card__inner">
            <div class="kpi-card__header">
              <span class="kpi-card__label text-rose-600">Total Write-Off</span>
              <div class="kpi-card__icon fin-icon-red">
                <v-icon icon="ri-file-damage-line" size="18" />
              </div>
            </div>
            <div class="kpi-card__value mt-2">{{ summary.total_writeoff_count || 0 }}</div>
            <div class="kpi-card__sub text-rose-600 font-weight-bold">Kontrak Terhapus</div>
          </div>
        </div>

        <div class="kpi-card">
          <div class="kpi-card__accent" style="background: linear-gradient(90deg, #d97706, #fbbf24)"></div>
          <div class="kpi-card__inner">
            <div class="kpi-card__header">
              <span class="kpi-card__label">Volume Hapus Buku</span>
              <div class="kpi-card__icon fin-icon-amber">
                <v-icon icon="ri-money-dollar-circle-line" size="18" />
              </div>
            </div>
            <div class="kpi-card__value mt-2">{{ formatCurrency(summary.total_writeoff_volume) }}</div>
            <div class="kpi-card__sub">Total Modal Awal</div>
          </div>
        </div>

        <div class="kpi-card kpi-card--success">
          <div class="kpi-card__accent" style="background: linear-gradient(90deg, #10b981, #34d399)"></div>
          <div class="kpi-card__inner">
            <div class="kpi-card__header">
              <span class="kpi-card__label text-emerald-600">Recovery Rate</span>
              <div class="kpi-card__icon fin-icon-green">
                <v-icon icon="ri-percent-line" size="18" />
              </div>
            </div>
            <div class="kpi-card__value mt-2">{{ parseFloat(summary.avg_recovery_rate || 0).toFixed(2) }}%</div>
            <div class="kpi-card__sub text-emerald-600 font-weight-bold">Rata-rata Koleksi</div>
          </div>
        </div>

        <div class="kpi-card">
          <div class="kpi-card__accent" style="background: linear-gradient(90deg, #1e40af, #3b82f6)"></div>
          <div class="kpi-card__inner">
            <div class="kpi-card__header">
              <span class="kpi-card__label">Top AO</span>
              <div class="kpi-card__icon fin-icon-blue">
                <v-icon icon="ri-medal-line" size="18" />
              </div>
            </div>
            <div class="kpi-card__value mt-2" style="font-size: 20px;">{{ summary.top_ao || 'N/A' }}</div>
            <div class="kpi-card__sub">Performa Terbaik</div>
          </div>
        </div>
      </div>

      <!-- Filter Bar -->
      <v-card class="d-flex flex-wrap align-center ga-3 pa-4 bg-white rounded-xl border shadow-sm mb-6" elevation="0">
        <div style="width: 120px">
          <v-select v-model="filters.tahun" :items="years" label="Tahun" density="compact" variant="outlined" hide-details @update:model-value="fetchData" />
        </div>
        <div style="width: 150px">
          <v-select v-model="filters.bulan" :items="months" label="Bulan" density="compact" variant="outlined" hide-details clearable @update:model-value="fetchData" />
        </div>
        <div class="flex-grow-1">
          <v-text-field v-model="filters.search" label="Cari Nasabah, Kontrak, atau NOCIF..." density="compact" variant="outlined" hide-details prepend-inner-icon="ri-search-line" clearable />
        </div>
        <div style="width: 180px">
          <v-select v-model="filters.ao" :items="aoList" label="Filter AO" density="compact" variant="outlined" hide-details clearable />
        </div>
        <v-btn color="primary" variant="flat" class="rounded-lg" @click="fetchData" :loading="loading">
          <v-icon start icon="ri-refresh-line"></v-icon> Refresh
        </v-btn>
        <v-btn color="success" variant="tonal" class="rounded-lg" @click="exportExcel" :loading="exporting">
          <v-icon start icon="ri-file-excel-2-line"></v-icon> Export
        </v-btn>
      </v-card>

      <!-- Chart Section -->
      <v-row class="mb-6">
        <v-col cols="12">
          <v-card class="rounded-xl border shadow-sm bg-white" elevation="0">
            <v-card-title class="d-flex align-center pa-4">
              <v-icon icon="ri-bar-chart-grouped-line" class="mr-2" color="primary"></v-icon>
              <span class="text-h6 font-weight-bold">Tren Recovery Rate Mingguan</span>
            </v-card-title>
            <v-divider></v-divider>
            <v-card-text class="pa-6">
              <div v-if="loading" class="d-flex align-center justify-center" style="height: 350px">
                <v-progress-circular indeterminate color="primary" size="64"></v-progress-circular>
              </div>
              <div v-else-if="!chartSeries || chartSeries.length === 0" class="pa-12 text-center bg-slate-50 rounded-xl border">
                <v-icon icon="ri-inbox-archive-line" size="64" class="text-slate-300 mb-4"></v-icon>
                <div class="text-h6 text-slate-500">Belum ada data Hapus Buku di periode ini</div>
              </div>
              <div v-else>
                <apexchart type="area" height="350" :options="chartOptions" :series="chartSeries" />
              </div>
            </v-card-text>
          </v-card>
        </v-col>
      </v-row>

      <!-- Data Table -->
      <v-card class="rounded-xl border shadow-sm bg-white" elevation="0">
        <div v-if="!data || data.length === 0" class="pa-12 text-center bg-slate-50 rounded-xl">
          <v-icon icon="ri-inbox-archive-line" size="64" class="text-slate-300 mb-4"></v-icon>
          <div class="text-h6 text-slate-500">Tidak ada data Write-Off ditemukan</div>
          <div class="text-caption text-slate-400 mt-2">Coba ubah filter atau periode lain</div>
        </div>
        <v-data-table v-else :headers="headers" :items="filteredData" :loading="loading" :items-per-page="itemsPerPage" class="bg-transparent" density="comfortable">
          <template #item.nama="{ item }">
            <div class="font-weight-bold text-primary">{{ item.nama }}</div>
            <div class="text-caption text-grey">{{ item.nocif }}</div>
          </template>
          
          <template #item.baki_debet="{ item }">
            <div class="font-weight-bold">{{ formatCurrency(item.baki_debet) }}</div>
            <div class="text-caption text-error">Margin: {{ formatCurrency(item.sisa_margin) }}</div>
          </template>

          <template #item.mdlawal="{ item }">
            <div class="font-weight-medium">{{ formatCurrency(item.mdlawal) }}</div>
          </template>

          <template #item.recovery_rate="{ item }">
            <div class="d-flex align-center">
              <div class="flex-grow-1 mr-2" style="min-width: 100px">
                <v-progress-linear :model-value="item.recovery_rate" :color="getRecoveryColor(item.recovery_rate)" height="8" rounded></v-progress-linear>
              </div>
              <v-chip :color="getRecoveryColor(item.recovery_rate)" size="x-small" variant="tonal" class="font-weight-black">
                {{ parseFloat(item.recovery_rate || 0).toFixed(1) }}%
              </v-chip>
            </div>
          </template>

          <template #item.tglwo="{ item }">
            <div class="d-flex align-center">
              <v-icon icon="ri-calendar-event-line" size="14" class="mr-1 text-grey"></v-icon>
              <span>{{ formatDate(item.tglwo) }}</span>
            </div>
          </template>

          <template #item.nmao="{ item }">
            <v-chip size="small" variant="outlined" color="primary" class="font-weight-medium">
              <v-icon start icon="ri-user-follow-line" size="14"></v-icon>
              {{ item.nmao }}
            </v-chip>
          </template>
        </v-data-table>
      </v-card>
    </div>
  </DefaultLayout>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { Head } from '@inertiajs/vue3'
import DefaultLayout from '@/layouts/default.vue'
import axios from 'axios'
import VueApexCharts from 'vue3-apexcharts'
import '@/assets/css/financing-shared.css'

const apexchart = VueApexCharts

const loading = ref(false)
const exporting = ref(false)
const data = ref([])
const summary = ref({})
const itemsPerPage = ref(15)

const filters = ref({
  tahun: new Date().getFullYear(),
  bulan: null,
  ao: null,
  search: '',
  sort_by: 'nama'
})

const years = computed(() => {
  const currentYear = new Date().getFullYear()
  return Array.from({ length: 7 }, (_, i) => currentYear - i)
})

const months = [
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
  { title: 'Desember', value: 12 }
]

const aoList = computed(() => {
  if (!data.value || !data.value.length) return []
  const aos = [...new Set(data.value.map(item => item.nmao))].filter(Boolean)
  return aos.sort()
})

const headers = [
  { title: 'NASABAH / CIF', key: 'nama', sortable: true },
  { title: 'NO KONTRAK', key: 'nokontrak', sortable: true },
  { title: 'BAKI DEBET', key: 'baki_debet', sortable: true, align: 'end' },
  { title: 'MODAL AWAL', key: 'mdlawal', sortable: true, align: 'end' },
  { title: 'RECOVERY RATE', key: 'recovery_rate', sortable: true, width: '200px' },
  { title: 'TGL WRITE-OFF', key: 'tglwo', sortable: true },
  { title: 'AO MENGELOLA', key: 'nmao', sortable: true }
]

const filteredData = computed(() => {
  if (!data.value || !data.value.length) return []
  
  let result = data.value

  if (filters.value.ao) {
    result = result.filter(item => item.nmao === filters.value.ao)
  }

  if (filters.value.search) {
    const search = filters.value.search.toLowerCase()
    result = result.filter(item =>
      item.nama?.toLowerCase().includes(search) ||
      item.nocif?.toLowerCase().includes(search) ||
      item.nokontrak?.toLowerCase().includes(search)
    )
  }

  return result
})

const allMonths = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des']

const currentMonthNum = computed(() => {
  return summary.value.current_month ? parseInt(summary.value.current_month) : new Date().getMonth() + 1
})

const activeMonths = computed(() => {
  return allMonths.slice(0, currentMonthNum.value)
})

// Chart Configuration
const chartSeries = computed(() => {
  if (!data.value || !data.value.length) return []
  
  const numMonths = currentMonthNum.value
  const monthlyData = Array(numMonths).fill(0)
  const monthlyCount = Array(numMonths).fill(0)
  
  data.value.forEach(item => {
    const date = new Date(item.tglwo)
    const month = date.getMonth()
    if (month < numMonths && item.recovery_rate) {
      monthlyData[month] += item.recovery_rate
      monthlyCount[month]++
    }
  })
  
  const avgMonthlyData = monthlyData.map((total, i) => 
    monthlyCount[i] > 0 ? parseFloat((total / monthlyCount[i]).toFixed(2)) : 0
  )

  return [{
    name: 'Recovery Rate',
    data: avgMonthlyData
  }]
})

const chartOptions = computed(() => ({
  chart: {
    type: 'area',
    toolbar: { show: false },
    sparkline: { enabled: false },
  },
  dataLabels: { enabled: false },
  stroke: { curve: 'smooth', width: 3 },
  fill: {
    type: 'gradient',
    gradient: {
      shadeIntensity: 1,
      opacityFrom: 0.45,
      opacityTo: 0.05,
      stops: [20, 100, 100, 100]
    }
  },
  xaxis: {
    categories: activeMonths.value,
    axisBorder: { show: false },
    axisTicks: { show: false }
  },
  yaxis: {
    labels: {
      formatter: (val) => val.toFixed(0) + '%'
    }
  },
  tooltip: {
    y: {
      formatter: (val) => val.toFixed(2) + '%'
    }
  },
  colors: ['#059669'], // Success Green
  grid: {
    borderColor: '#f1f5f9',
    strokeDashArray: 4,
  }
}))

const fetchData = async () => {
  loading.value = true
  try {
    const params = {
      tahun: filters.value.tahun,
      bulan: filters.value.bulan,
      ao: filters.value.ao,
      sort_by: filters.value.sort_by
    }
    const response = await axios.get('/api/v1/financing/penyelesaian/write-off', { params })
    data.value = response.data.data || []
    summary.value = response.data.summary || {}
  } catch (error) {
    console.error('Error fetching data:', error)
    data.value = []
    summary.value = {}
  } finally {
    loading.value = false
  }
}

const formatCurrency = (value) => {
  if (!value) return 'Rp 0'
  return new Intl.NumberFormat('id-ID', {
    style: 'currency',
    currency: 'IDR',
    minimumFractionDigits: 0
  }).format(value)
}

const formatDate = (date) => {
  if (!date) return '-'
  return new Date(date).toLocaleDateString('id-ID', {
    year: 'numeric',
    month: 'short',
    day: 'numeric'
  })
}

const getRecoveryColor = (rate) => {
  if (rate >= 75) return 'success'
  if (rate >= 50) return 'info'
  if (rate >= 25) return 'warning'
  return 'error'
}

const exportExcel = async () => {
  exporting.value = true
  try {
    console.log('Exporting to Excel...')
    window.location.href = `/api/v1/financing/penyelesaian/write-off/export?tahun=${filters.value.tahun}`
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

.uppercase {
  text-transform: uppercase;
  letter-spacing: 0.05em;
}

/* Data Table Styling */
:deep(.v-data-table) {
  background-color: transparent !important;
}

:deep(.v-data-table .v-data-table__th) {
  background-color: #f8fafc !important;
  color: #1e293b !important;
  font-weight: 900 !important;
  text-transform: uppercase !important;
  letter-spacing: 0.05em !important;
  font-size: 11px !important;
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

/* Lighten Colors */
.error-lighten-4 {
  background-color: #FEE2E2 !important;
}
.warning-lighten-4 {
  background-color: #FEF3C7 !important;
}
.success-lighten-4 {
  background-color: #D1FAE5 !important;
}
.primary-lighten-4 {
  background-color: #DBEAFE !important;
}
</style>
