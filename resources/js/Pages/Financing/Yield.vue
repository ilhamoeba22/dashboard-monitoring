<template>
  <DefaultLayout>
    <Head title="Yield Analysis" />

    <div class="fin-page px-4 pt-0">
      <!-- ── HERO HEADER ─────────────────────────────────────────── -->
      <div class="fin-hero mb-6">
        <div class="fin-hero__deco"></div>
        <div class="fin-hero__inner">
          <div class="d-flex flex-column flex-md-row justify-space-between align-start align-md-center gap-4">
            <div class="d-flex align-center gap-4">
              <div class="fin-hero__icon fin-icon-blue">
                <v-icon icon="ri-line-chart-fill" size="26" color="white" />
              </div>
              <div class="fin-hero__meta">
                <h1 class="fin-hero__title">Yield Analysis Dashboard</h1>
                <p class="fin-hero__subtitle">Analisis imbal hasil pembiayaan per dimensi (Margin/Bagi Hasil)</p>
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
        <div class="kpi-card">
          <div class="kpi-card__accent" style="background: linear-gradient(90deg, #d97706, #fbbf24)"></div>
          <div class="kpi-card__inner">
            <div class="kpi-card__header">
              <span class="kpi-card__label">Avg Yield Tagihan</span>
              <div class="kpi-card__icon fin-icon-amber">
                <v-icon icon="ri-funds-box-line" size="18" />
              </div>
            </div>
            <div class="kpi-card__value mt-2">{{ safePct(summary.avg_yield_tag).toFixed(2) }}%</div>
            <div class="kpi-card__sub">Target Yield</div>
          </div>
        </div>

        <div class="kpi-card kpi-card--success">
          <div class="kpi-card__accent" style="background: linear-gradient(90deg, #10b981, #34d399)"></div>
          <div class="kpi-card__inner">
            <div class="kpi-card__header">
              <span class="kpi-card__label text-emerald-600">Avg Yield Bayar</span>
              <div class="kpi-card__icon fin-icon-green">
                <v-icon icon="ri-hand-coin-line" size="18" />
              </div>
            </div>
            <div class="kpi-card__value mt-2">{{ safePct(summary.avg_yield_byr).toFixed(2) }}%</div>
            <div class="kpi-card__sub text-emerald-600 font-weight-bold">Actual Yield</div>
          </div>
        </div>

        <div class="kpi-card">
          <div class="kpi-card__accent" style="background: linear-gradient(90deg, #1e40af, #3b82f6)"></div>
          <div class="kpi-card__inner">
            <div class="kpi-card__header">
              <span class="kpi-card__label">Best Performer</span>
              <div class="kpi-card__icon fin-icon-blue">
                <v-icon icon="ri-trophy-line" size="18" />
              </div>
            </div>
            <div class="kpi-card__value mt-2 text-truncate" style="max-width: 160px; font-size: 20px;">{{ summary.best_performer || 'N/A' }}</div>
            <div class="kpi-card__sub text-blue-600 font-weight-bold">{{ safePct(summary.best_performer_yield).toFixed(2) }}%</div>
          </div>
        </div>

        <div class="kpi-card">
          <div class="kpi-card__accent" style="background: linear-gradient(90deg, #9333ea, #c084fc)"></div>
          <div class="kpi-card__inner">
            <div class="kpi-card__header">
              <span class="kpi-card__label text-purple-600">Performance Rate</span>
              <div class="kpi-card__icon fin-icon-purple">
                <v-icon icon="ri-percent-line" size="18" />
              </div>
            </div>
            <div class="kpi-card__value mt-2">{{ safePct(summary.avg_performance).toFixed(2) }}%</div>
            <div class="kpi-card__sub text-purple-600 font-weight-bold">Bayar/Tagihan</div>
          </div>
        </div>
      </div>

      <!-- Dimension Tabs -->
      <v-tabs
        v-model="selectedDimension"
        bg-color="grey-lighten-4"
        color="primary"
        rounded="lg"
        class="border mb-6"
        @update:model-value="fetchData"
      >
        <v-tab value="ao" class="font-weight-bold">Account Officer</v-tab>
        <v-tab value="cabang" class="font-weight-bold">Cabang</v-tab>
        <v-tab value="wilayah" class="font-weight-bold">Wilayah</v-tab>
        <v-tab value="produk" class="font-weight-bold">Produk</v-tab>
        <v-tab value="segmen" class="font-weight-bold">Segmen</v-tab>
      </v-tabs>

      <!-- Filters -->
      <v-card class="d-flex flex-wrap align-center ga-3 pa-4 bg-white rounded-xl border shadow-sm mb-6" elevation="0">
        <div style="width: 150px">
          <v-select
            v-model="filters.tahun"
            :items="years"
            label="Tahun"
            density="compact"
            variant="outlined"
            hide-details
            rounded="lg"
            @update:model-value="fetchData"
          />
        </div>
        <div class="flex-grow-1" style="min-width: 200px">
          <v-text-field
            v-model="filters.search"
            label="Cari Nama"
            density="compact"
            variant="outlined"
            hide-details
            rounded="lg"
            prepend-inner-icon="ri-search-line"
            clearable
          />
        </div>
        <div class="d-flex align-center">
          <v-switch
            v-model="filters.active_only"
            label="Active Only"
            color="primary"
            density="compact"
            hide-details
            @update:model-value="fetchData"
          />
          <v-tooltip text="Hanya tampilkan data kontrak aktif (stsrec = A)">
            <template #activator="{ props }">
              <v-icon v-bind="props" icon="ri-question-line" size="18" color="grey" class="ml-1"></v-icon>
            </template>
          </v-tooltip>
        </div>
        <v-spacer />
        <v-btn color="success" variant="tonal" class="rounded-lg" prepend-icon="ri-file-excel-2-line" @click="exportExcel" :loading="exporting">
          Export Excel
        </v-btn>
      </v-card>

      <!-- Chart Section -->
      <v-card class="rounded-xl border shadow-sm bg-white mb-6" elevation="0">
        <v-card-title class="d-flex align-center pa-4">
          <v-icon icon="ri-bar-chart-grouped-line" class="mr-2" color="primary"></v-icon>
          <span class="text-h6 font-weight-bold">
            Yield Trend - {{ getDimensionLabel(selectedDimension) }}
            <span v-if="currentMonthLabel" class="text-caption text-grey ml-2">(s.d. {{ currentMonthLabel }} {{ filters.tahun }})</span>
          </span>
        </v-card-title>
        <v-divider></v-divider>
        <v-card-text class="pa-6">
          <!-- Multi-Select Selector -->
          <v-row class="mb-4">
            <v-col cols="12">
              <v-select
                v-model="selectedAOs"
                :items="availableAOs"
                multiple
                chips
                closable-chips
                :label="`Pilih ${getDimensionLabel(selectedDimension)} untuk Dibandingkan`"
                variant="outlined"
                density="compact"
                rounded="lg"
                hide-details
                prepend-inner-icon="ri-filter-3-line"
              />
            </v-col>
          </v-row>

          <div v-if="loading" class="d-flex align-center justify-center" style="height: 400px">
            <v-skeleton-loader type="image" height="400" />
          </div>
          <div v-else-if="!chartSeries || chartSeries.length === 0" class="pa-12 text-center bg-slate-50 rounded-xl border">
            <v-icon icon="ri-inbox-archive-line" size="64" class="text-slate-300 mb-4"></v-icon>
            <div class="text-h6 text-slate-500">Belum ada data Yield di periode ini</div>
          </div>
          <div v-else>
            <apexchart
              :key="`chart-${selectedDimension}-${filters.tahun}-${filters.active_only}`"
              type="line"
              height="400"
              :options="chartOptions"
              :series="chartSeries"
            />
          </div>
        </v-card-text>
      </v-card>

      <!-- Data Table -->
      <div class="content-card">
        <div v-if="!data || data.length === 0" class="pa-12 text-center bg-slate-50 rounded-xl">
          <v-icon icon="ri-inbox-archive-line" size="64" class="text-slate-300 mb-4"></v-icon>
          <div class="text-h6 text-slate-500">Tidak ada data Yield ditemukan</div>
          <div class="text-caption text-slate-400 mt-2">Coba ubah filter atau periode lain</div>
        </div>
        <div v-else class="content-card__body pa-0">
          <v-data-table
            :headers="dynamicHeaders"
            :items="filteredData"
            :loading="loading"
            :items-per-page="50"
            class="fin-table fin-vtable bg-transparent"
            density="comfortable"
          >
            <template #item.Nama="{ item }">
              <div class="font-weight-medium">{{ item.Nama || 'N/A' }}</div>
              <div class="text-caption text-grey">{{ item.Kode || '-' }}</div>
            </template>

            <template #[`item.${m}_Yld_Byr`]="{ item }" v-for="m in activeMonths" :key="m">
              <v-chip
                :color="getYieldColor(safeNum(item[`${m}_Yld_Byr`]))"
                size="small"
                variant="tonal"
                class="font-weight-bold"
              >
                {{ safeNum(item[`${m}_Yld_Byr`]).toFixed(2) }}%
              </v-chip>
            </template>

            <template #item.avg_yield="{ item }">
              <div class="font-weight-bold text-primary">{{ safeNum(item.avg_yield).toFixed(2) }}%</div>
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
const selectedDimension = ref('ao')
const selectedAOs = ref([])
const availableAOs = ref([])

const filters = ref({
  tahun: new Date().getFullYear(),
  search: '',
  active_only: true
})

const allMonths = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des']

const years = computed(() => {
  const currentYear = new Date().getFullYear()
  return Array.from({ length: 7 }, (_, i) => currentYear - i)
})

// =========================================================================
// SAFE HELPERS (ANTI-RpNaN)
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

// =========================================================================
// ACTIVE MONTHS (dari backend / default ke bulan ini)
// =========================================================================

const currentMonthNum = computed(() => {
  return summary.value.current_month ? parseInt(summary.value.current_month) : new Date().getMonth() + 1
})

const activeMonths = computed(() => {
  return allMonths.slice(0, currentMonthNum.value)
})

const currentMonthLabel = computed(() => {
  return allMonths[currentMonthNum.value - 1] || ''
})

// =========================================================================
// DYNAMIC HEADERS (hanya bulan aktif)
// =========================================================================

const dynamicHeaders = computed(() => {
  const monthHeaders = activeMonths.value.map(m => ({
    title: m,
    key: `${m}_Yld_Byr`,
    sortable: true,
    align: 'center',
    width: '90px'
  }))

  return [
    { title: 'Nama', key: 'Nama', sortable: true, width: '200px' },
    ...monthHeaders,
    { title: 'Avg Yield', key: 'avg_yield', sortable: true, align: 'center', width: '100px' }
  ]
})

// =========================================================================
// FILTERED DATA
// =========================================================================

const filteredData = computed(() => {
  if (!data.value || !Array.isArray(data.value) || data.value.length === 0) return []

  let result = data.value.map(item => {
    let total = 0
    let count = 0
    activeMonths.value.forEach(m => {
      const val = safeNum(item[`${m}_Yld_Byr`])
      if (val > 0) {
        total += val
        count++
      }
    })
    return {
      ...item,
      avg_yield: count > 0 ? total / count : 0
    }
  })

  if (filters.value.search) {
    const search = filters.value.search.toLowerCase()
    result = result.filter(item =>
      (item.Nama || '').toLowerCase().includes(search) ||
      (item.Kode || '').toLowerCase().includes(search)
    )
  }

  return result
})

// =========================================================================
// CHART — PRO MAX (Smooth + Gradient + All AOs + Timeframe Limit)
// =========================================================================

const chartSeries = computed(() => {
  if (!data.value || !Array.isArray(data.value) || data.value.length === 0) return []

  const months = activeMonths.value
  
  // Filter based on selectedAOs
  return data.value
    .filter(item => selectedAOs.value.includes(item.Nama || 'N/A'))
    .map(item => ({
      name: item.Nama || 'N/A',
      data: months.map(m => safeNum(item[`${m}_Yld_Byr`]))
    }))
})

const chartOptions = computed(() => {
  const months = activeMonths.value

  // Generate palette warna dinamis
  const baseColors = [
    '#3B82F6', // Blue 500
    '#F59E0B', // Amber 500
    '#10B981', // Emerald 500
    '#EF4444', // Red 500
    '#8B5CF6', // Violet 500
    '#06B6D4', // Cyan 500
    '#F43F5E', // Rose 500
    '#84CC16', // Lime 500
    '#EC4899', // Pink 500
    '#14B8A6', // Teal 500
    '#6366F1', // Indigo 500
    '#F97316', // Orange 500
  ]

  return {
    chart: {
      type: 'line',
      toolbar: { show: true },
      zoom: { enabled: true },
      fontFamily: 'Plus Jakarta Sans, sans-serif',
      dropShadow: {
        enabled: true,
        color: '#000',
        top: 5,
        left: 0,
        blur: 6,
        opacity: 0.1
      }
    },
    stroke: {
      curve: 'smooth',
      width: 5
    },
    markers: {
      size: 6,
      strokeWidth: 2,
      hover: {
        size: 9
      }
    },
    fill: {
      type: 'solid'
    },
    xaxis: {
      categories: months,
      labels: {
        style: {
          colors: '#64748b',
          fontSize: '12px',
          fontWeight: 600
        }
      }
    },
    yaxis: {
      title: {
        text: 'Yield (%)',
        style: { fontWeight: 600, color: '#64748b' }
      },
      labels: {
        style: {
          colors: '#64748b',
          fontSize: '12px',
          fontWeight: 600
        },
        formatter: (val) => {
          const n = safeNum(val)
          return n.toFixed(2) + '%'
        }
      }
    },
    tooltip: {
      shared: true,
      intersect: false,
      theme: 'light',
      y: {
        formatter: (val) => {
          const n = safeNum(val)
          return n.toFixed(2) + '%'
        }
      }
    },
    colors: chartSeries.value.length > baseColors.length
      ? [...baseColors, ...Array(chartSeries.value.length - baseColors.length).fill('#6B7280')]
      : baseColors,
    legend: {
      position: 'top',
      horizontalAlign: 'left',
      fontSize: '12px',
      markers: { radius: 12 }
    },
    grid: {
      borderColor: '#f1f5f9',
      strokeDashArray: 4
    },
    dataLabels: {
      enabled: false
    }
  }
})

// =========================================================================
// API
// =========================================================================

const fetchData = async () => {
  loading.value = true
  try {
    const params = {
      tahun: filters.value.tahun,
      dimensi: selectedDimension.value,
      active_only: filters.value.active_only
    }
    const response = await axios.get('/api/v1/financing/penyelesaian/yield', { params })
    const rawData = Array.isArray(response.data?.data) ? response.data.data : []
    data.value = rawData
    summary.value = response.data?.summary || {}

    // Extract available AOs
    availableAOs.value = rawData.map(item => item.Nama || 'N/A')

    // Top 3 Logic: Sort by Avg Yield and pick top 3
    const sortedData = [...rawData].map(item => {
      let total = 0
      let count = 0
      activeMonths.value.forEach(m => {
        const val = safeNum(item[`${m}_Yld_Byr`])
        if (val > 0) {
          total += val
          count++
        }
      })
      return {
        nama: item.Nama || 'N/A',
        avg_yield: count > 0 ? total / count : 0
      }
    }).sort((a, b) => b.avg_yield - a.avg_yield)

    selectedAOs.value = sortedData.slice(0, 3).map(i => i.nama)

  } catch (error) {
    console.error('Error fetching data:', error)
    data.value = []
    summary.value = {}
    availableAOs.value = []
    selectedAOs.value = []
  } finally {
    loading.value = false
  }
}

const getDimensionLabel = (dim) => {
  const labels = {
    ao: 'Account Officer',
    cabang: 'Cabang',
    wilayah: 'Wilayah',
    produk: 'Produk',
    segmen: 'Segmen'
  }
  return labels[dim] || dim
}

const getYieldColor = (value) => {
  const n = safeNum(value)
  if (n === 0) return 'grey'
  if (n >= 2.5) return 'success'
  if (n >= 1.5) return 'warning'
  return 'error'
}

const exportExcel = async () => {
  exporting.value = true
  try {
    const params = new URLSearchParams({
      tahun: String(filters.value.tahun),
      dimensi: selectedDimension.value,
      active_only: filters.value.active_only ? '1' : '0'
    })
    window.location.href = `/api/v1/financing/penyelesaian/yield/export?${params.toString()}`
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

.border {
  border: 1px solid #e2e8f0 !important;
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

/* Tabs styling */
:deep(.v-tabs) {
  border-radius: 12px;
}

:deep(.v-tab) {
  text-transform: none !important;
  letter-spacing: normal !important;
}

:deep(.v-tab:not(.v-tab--selected)) {
  color: rgba(0, 0, 0, 0.6) !important;
}

:deep(.v-tab.v-tab--selected) {
  font-weight: 700 !important;
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
</style>
