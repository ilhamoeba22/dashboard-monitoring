<script setup>
import DefaultLayout from '@/layouts/default.vue'
import { useFinancingStore } from '@/stores/financingStore'
import SummaryCards from '@/components/Financing/SummaryCards.vue'
import TrendChart from '@/components/Financing/TrendChart.vue'
import MultiPeriodCompareChart from '@/components/Financing/MultiPeriodCompareChart.vue'
import ComparisonTable from '@/components/Financing/ComparisonTable.vue'
import { ref, computed, onMounted } from 'vue'

defineOptions({ layout: DefaultLayout })

const store = useFinancingStore()
const viewMode = ref('realtime')

// Metric Options for Selector
const metricOptions = [
  { title: 'Total Outstanding', value: 'total_os' },
  { title: 'Nominal NPF', value: 'total_npf' },
  { title: 'Rasio NPF (%)', value: 'npf_persen' }
]

/**
 * Formatter for DB Labels
 * Converts "MCI_MAR26_01042026" -> "Maret 2026"
 */
function formatDbLabel(name) {
  if (!name) return ''
  
  const months = {
    'JAN': 'Januari', 'FEB': 'Februari', 'MAR': 'Maret', 'APR': 'April',
    'MEI': 'Mei', 'JUN': 'Juni', 'JUL': 'Juli', 'AGU': 'Agustus',
    'SEP': 'September', 'OKT': 'Oktober', 'NOV': 'November', 'DES': 'Desember'
  }

  // New format: MCI_MAR26_01042026
  const matchNew = name.match(/MCI_([A-Z]{3})(\d{2})_/)
  if (matchNew) {
    const month = months[matchNew[1]] || matchNew[1]
    const year = '20' + matchNew[2]
    return `${month} ${year}`
  }

  // Old format: MCI_JAN_31012026
  const matchOld = name.match(/MCI_([A-Z]{3})_/)
  if (matchOld) {
    const month = months[matchOld[1]] || matchOld[1]
    const year = name.slice(-4)
    return `${month} ${year}`
  }

  return name
}

// Map raw database list to formatted dropdown items
const periodItems = computed(() => {
  return (store.comparisonPeriods || []).map(p => ({
    value: p.name,
    title: formatDbLabel(p.name) || p.label
  }))
})

const isLoading = computed(() => store.loadingRealtime)
const hasData = computed(() => store.realtimeData !== null)

// Formatting Helpers
function formatCurrency(value) {
  if (!value && value !== 0) return '—'
  const num = parseFloat(value)
  if (isNaN(num)) return '—'
  if (Math.abs(num) >= 1e9) return `${(num / 1e9).toFixed(2)} M`
  if (Math.abs(num) >= 1e6) return `${(num / 1e6).toFixed(1)} Jt`
  return num.toLocaleString('id-ID')
}

function formatNumber(value) {
  if (!value && value !== 0) return '0'
  return parseInt(value).toLocaleString('id-ID')
}

// Kolektibilitas Style & Logic
function getKolColor(kol) {
  const colors = {
    '1': '#10B981', // Emerald/Success
    '2': '#3B82F6', // Blue/Info
    '3': '#F59E0B', // Amber/Warning
    '4': '#F97316', // Orange
    '5': '#EF4444'  // Red/Error
  }
  return colors[String(kol)] || '#64748B'
}

function getKolLabel(kol) {
  const labels = {
    '1': 'Lancar',
    '2': 'DPK',
    '3': 'Kurang Lancar',
    '4': 'Diragukan',
    '5': 'Macet'
  }
  return labels[String(kol)] || 'Unknown'
}

// Calculation Helpers
const totalOS = computed(() => {
  if (!store.kolektibilitas) return 0
  return store.kolektibilitas.reduce((sum, item) => sum + (parseFloat(item.osmdlc) || 0), 0)
})

function calculatePorsi(value) {
  if (!totalOS.value || totalOS.value === 0) return '0.00'
  return ((parseFloat(value) / totalOS.value) * 100).toFixed(2)
}

function calculateTotal(data, key) {
  if (!data || !Array.isArray(data)) return 0
  return data.reduce((sum, item) => sum + (parseFloat(item[key]) || 0), 0)
}

// Trend Summary Calculations
const trendStats = computed(() => {
  const data = store.trendSeries || []
  if (data.length === 0) return { highest: 0, lowest: 0, average: 0 }
  
  const values = data.map(s => parseFloat(s.total_os) || 0)
  const sum = values.reduce((a, b) => a + b, 0)
  
  return {
    highest: Math.max(...values),
    lowest: Math.min(...values),
    average: sum / values.length
  }
})

async function handleMultiCompare() {
    await store.fetchMultiComparison()
}

onMounted(() => {
  store.loadAll()
})
</script>

<template>
  <div class="financing-overview">
    <!-- Page Header -->
    <div class="page-header mb-6">
      <v-row align="center" justify="space-between" no-gutters>
        <v-col cols="12" md="auto">
          <div class="d-flex align-center gap-4">
            <div class="header-icon">
              <v-icon icon="ri-bank-line" size="28" color="primary" />
            </div>
            <div>
              <h1 class="text-h4 font-weight-bold mb-1">Pembiayaan Overview</h1>
              <p class="text-body-2 text-medium-emphasis mb-0">Monitoring pembiayaan dengan data real-time</p>
            </div>
          </div>
        </v-col>
        <v-col cols="12" md="auto" class="mt-4 mt-md-0">
          <div class="view-toggle-wrapper">
            <v-btn-toggle
              v-model="viewMode"
              mandatory
              density="comfortable"
              color="primary"
              rounded="pill"
              divided
              variant="outlined"
              class="view-toggle"
            >
              <v-btn value="realtime" size="small" class="px-4">
                <v-icon start icon="ri-flashlight-line" size="18" />
                <span class="d-none d-sm-inline">Realtime</span>
              </v-btn>
              <v-btn value="compare" size="small" class="px-4">
                <v-icon start icon="ri-arrow-left-right-line" size="18" />
                <span class="d-none d-sm-inline">Compare</span>
              </v-btn>
              <v-btn value="trend" size="small" class="px-4">
                <v-icon start icon="ri-line-chart-line" size="18" />
                <span class="d-none d-sm-inline">Trend</span>
              </v-btn>
            </v-btn-toggle>
          </div>
        </v-col>
      </v-row>
    </div>

    <!-- Loading -->
    <div v-if="isLoading" class="loading-skeleton">
      <v-row class="mb-2">
        <v-col v-for="i in 4" :key="i" cols="12" sm="6" lg="3">
          <v-skeleton-loader type="card" />
        </v-col>
      </v-row>
    </div>

    <!-- ======================== REALTIME MODE ======================== -->
    <template v-else-if="viewMode === 'realtime' && hasData">
      <SummaryCards :data="store.realtimeData" />
      
      <!-- Two-Pillar Masonry Layout -->
      <v-row class="mt-4">
        
        <!-- Pillar Kiri -->
        <v-col cols="12" lg="6" class="d-flex flex-column" style="gap: 24px;">
          
          <!-- 1. Distribusi Kolektibilitas -->
          <v-card elevation="0" class="data-card">
            <v-card-title class="d-flex align-center pa-4">
              <v-icon icon="ri-pie-chart-line" color="primary" class="me-3" />
              <span class="text-h6 font-weight-bold">Distribusi Kolektibilitas</span>
            </v-card-title>
            <v-card-text class="pa-0">
              <v-table density="comfortable" class="custom-table">
                <thead>
                  <tr>
                    <th class="text-caption font-weight-bold text-uppercase w-33">Keterangan</th>
                    <th class="text-right text-caption font-weight-bold text-uppercase">NOA</th>
                    <th class="text-right text-caption font-weight-bold text-uppercase">Outstanding</th>
                    <th class="text-right text-caption font-weight-bold text-uppercase">Porsi (%)</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="item in store.kolektibilitas" :key="item.kol">
                    <td class="text-caption py-2 font-weight-medium text-high-emphasis">
                      <div class="d-flex align-center">
                        <div 
                          class="kol-indicator me-2" 
                          :style="{ backgroundColor: getKolColor(item.kol) }"
                        >
                          {{ item.kol }}
                        </div>
                        <div class="leading-tight">
                          <div class="font-weight-bold text-uppercase" :style="{ color: getKolColor(item.kol) }">
                            KOL {{ item.kol }}
                          </div>
                          <div class="whitespace-normal break-words">{{ getKolLabel(item.kol) }}</div>
                        </div>
                      </div>
                    </td>
                    <td class="text-right text-caption">{{ formatNumber(item.noa) }}</td>
                    <td class="text-right text-caption font-weight-medium">Rp {{ formatCurrency(item.osmdlc) }}</td>
                    <td class="text-right text-caption" style="width: 100px;">
                      <div class="d-flex flex-column align-end">
                        <span class="text-caption font-weight-bold mb-1">{{ calculatePorsi(item.osmdlc) }}%</span>
                        <v-progress-linear
                          :model-value="calculatePorsi(item.osmdlc)"
                          height="4"
                          rounded
                          :color="getKolColor(item.kol)"
                          class="w-100"
                        />
                      </div>
                    </td>
                  </tr>
                </tbody>
                <tfoot>
                  <tr class="bg-grey-lighten-4 font-weight-bold">
                    <td class="text-caption">TOTAL</td>
                    <td class="text-right text-caption">{{ formatNumber(calculateTotal(store.kolektibilitas, 'noa')) }}</td>
                    <td class="text-right text-caption">Rp {{ formatCurrency(totalOS) }}</td>
                    <td class="text-right text-caption">100.00%</td>
                  </tr>
                </tfoot>
              </v-table>
            </v-card-text>
          </v-card>

          <!-- 3. Distribusi Per Segmen -->
          <v-card elevation="0" class="data-card">
            <v-card-title class="d-flex align-center pa-4">
              <v-icon icon="ri-team-line" color="success" class="me-3" />
              <span class="text-h6 font-weight-bold">Distribusi Per Segmen</span>
            </v-card-title>
            <v-card-text class="pa-0">
              <v-table density="comfortable" class="custom-table">
                <thead>
                  <tr>
                    <th class="text-caption font-weight-bold text-uppercase w-50">Segmen</th>
                    <th class="text-right text-caption font-weight-bold text-uppercase">NOA</th>
                    <th class="text-right text-caption font-weight-bold text-uppercase">Outstanding</th>
                    <th class="text-right text-caption font-weight-bold text-uppercase">NPF (%)</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="item in store.segmenData" :key="item.kdseg">
                    <td class="text-caption py-2 whitespace-normal break-words leading-tight font-weight-medium text-high-emphasis">{{ item.nmseg }}</td>
                    <td class="text-right text-caption">{{ formatNumber(item.noa) }}</td>
                    <td class="text-right text-caption font-weight-medium">Rp {{ formatCurrency(item.osmdlc) }}</td>
                    <td class="text-right text-caption">
                      <v-chip size="x-small" :color="item.npf_persen > 5 ? 'error' : 'success'" variant="tonal" class="font-weight-bold">
                        {{ item.npf_persen }}%
                      </v-chip>
                    </td>
                  </tr>
                </tbody>
                <tfoot>
                  <tr class="bg-grey-lighten-4 font-weight-bold">
                    <td class="text-caption">TOTAL</td>
                    <td class="text-right text-caption">{{ formatNumber(calculateTotal(store.segmenData, 'noa')) }}</td>
                    <td class="text-right text-caption">Rp {{ formatCurrency(calculateTotal(store.segmenData, 'osmdlc')) }}</td>
                    <td class="text-right text-caption">—</td>
                  </tr>
                </tfoot>
              </v-table>
            </v-card-text>
          </v-card>

          <!-- 5. Jatuh Tempo Bulan Ini -->
          <v-card elevation="0" class="data-card">
            <v-card-title class="d-flex align-center pa-4">
              <v-icon icon="ri-calendar-event-line" color="error" class="me-3" />
              <span class="text-h6 font-weight-bold">Jatuh Tempo Bulan Ini</span>
            </v-card-title>
            <v-card-text class="pa-0">
              <v-table density="comfortable" class="custom-table">
                <thead>
                  <tr>
                    <th class="text-caption font-weight-bold text-uppercase w-40">Nama</th>
                    <th class="text-right text-caption font-weight-bold text-uppercase">Outstanding</th>
                    <th class="text-right text-caption font-weight-bold text-uppercase">Segmen</th>
                    <th class="text-right text-caption font-weight-bold text-uppercase">AO</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="(item, idx) in store.jatuhTempoData" :key="idx">
                    <td class="text-caption py-2 whitespace-normal break-words leading-tight font-weight-medium text-high-emphasis">{{ item.nama }}</td>
                    <td class="text-right text-caption font-weight-medium">Rp {{ formatCurrency(item.osmdlc) }}</td>
                    <td class="text-right text-caption whitespace-normal break-words leading-tight">{{ item.nmseg }}</td>
                    <td class="text-right text-caption whitespace-normal break-words leading-tight">{{ item.nmao }}</td>
                  </tr>
                  <tr v-if="!store.jatuhTempoData.length">
                    <td colspan="4" class="text-center pa-8 text-caption text-medium-emphasis">Tidak ada data jatuh tempo</td>
                  </tr>
                </tbody>
                <tfoot v-if="store.jatuhTempoData.length">
                  <tr class="bg-grey-lighten-4 font-weight-bold">
                    <td class="text-caption">TOTAL</td>
                    <td class="text-right text-caption">Rp {{ formatCurrency(calculateTotal(store.jatuhTempoData, 'osmdlc')) }}</td>
                    <td colspan="2"></td>
                  </tr>
                </tfoot>
              </v-table>
            </v-card-text>
          </v-card>
        </v-col>

        <!-- Pillar Kanan -->
        <v-col cols="12" lg="6" class="d-flex flex-column" style="gap: 24px;">
          
          <!-- 2. Distribusi Per Produk -->
          <v-card elevation="0" class="data-card">
            <v-card-title class="d-flex align-center pa-4">
              <v-icon icon="ri-briefcase-line" color="warning" class="me-3" />
              <span class="text-h6 font-weight-bold">Distribusi Per Produk</span>
            </v-card-title>
            <v-card-text class="pa-0">
              <v-table density="comfortable" class="custom-table">
                <thead>
                  <tr>
                    <th class="text-caption font-weight-bold text-uppercase w-50">Produk</th>
                    <th class="text-right text-caption font-weight-bold text-uppercase">NOA</th>
                    <th class="text-right text-caption font-weight-bold text-uppercase">Outstanding</th>
                    <th class="text-right text-caption font-weight-bold text-uppercase">NPF (%)</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="item in store.produkData" :key="item.kdprd">
                    <td class="text-caption py-2 whitespace-normal break-words leading-tight font-weight-medium text-high-emphasis">{{ item.nmproduk }}</td>
                    <td class="text-right text-caption">{{ formatNumber(item.noa) }}</td>
                    <td class="text-right text-caption font-weight-medium">Rp {{ formatCurrency(item.osmdlc) }}</td>
                    <td class="text-right text-caption">
                      <v-chip size="x-small" :color="item.npf_persen > 5 ? 'error' : 'success'" variant="tonal" class="font-weight-bold">
                        {{ item.npf_persen }}%
                      </v-chip>
                    </td>
                  </tr>
                </tbody>
                <tfoot>
                  <tr class="bg-grey-lighten-4 font-weight-bold">
                    <td class="text-caption">TOTAL</td>
                    <td class="text-right text-caption">{{ formatNumber(calculateTotal(store.produkData, 'noa')) }}</td>
                    <td class="text-right text-caption">Rp {{ formatCurrency(calculateTotal(store.produkData, 'osmdlc')) }}</td>
                    <td class="text-right text-caption">—</td>
                  </tr>
                </tfoot>
              </v-table>
            </v-card-text>
          </v-card>

          <!-- 4. Distribusi Per AO -->
          <v-card elevation="0" class="data-card">
            <v-card-title class="d-flex align-center pa-4">
              <v-icon icon="ri-user-star-line" color="info" class="me-3" />
              <span class="text-h6 font-weight-bold">Distribusi Per AO</span>
            </v-card-title>
            <v-card-text class="pa-0">
              <v-table density="comfortable" class="custom-table">
                <thead>
                  <tr>
                    <th class="text-caption font-weight-bold text-uppercase w-50">Nama AO</th>
                    <th class="text-right text-caption font-weight-bold text-uppercase">NOA</th>
                    <th class="text-right text-caption font-weight-bold text-uppercase">Outstanding</th>
                    <th class="text-right text-caption font-weight-bold text-uppercase">NPF (%)</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="item in store.aoData" :key="item.kdao">
                    <td class="text-caption py-2 whitespace-normal break-words leading-tight font-weight-medium text-high-emphasis">{{ item.nmao }}</td>
                    <td class="text-right text-caption">{{ formatNumber(item.noa) }}</td>
                    <td class="text-right text-caption font-weight-medium">Rp {{ formatCurrency(item.osmdlc) }}</td>
                    <td class="text-right text-caption">
                      <v-chip size="x-small" :color="item.npf_persen > 5 ? 'error' : 'success'" variant="tonal" class="font-weight-bold">
                        {{ item.npf_persen }}%
                      </v-chip>
                    </td>
                  </tr>
                </tbody>
                <tfoot>
                  <tr class="bg-grey-lighten-4 font-weight-bold">
                    <td class="text-caption">TOTAL</td>
                    <td class="text-right text-caption">{{ formatNumber(calculateTotal(store.aoData, 'noa')) }}</td>
                    <td class="text-right text-caption">Rp {{ formatCurrency(calculateTotal(store.aoData, 'osmdlc')) }}</td>
                    <td class="text-right text-caption">—</td>
                  </tr>
                </tfoot>
              </v-table>
            </v-card-text>
          </v-card>

          <!-- 6. Realisasi Bulan Kemarin -->
          <v-card elevation="0" class="data-card">
            <v-card-title class="d-flex align-center pa-4">
              <v-icon icon="ri-checkbox-circle-line" color="success" class="me-3" />
              <span class="text-h6 font-weight-bold">Realisasi Bulan Kemarin</span>
            </v-card-title>
            <v-card-text class="pa-0">
              <v-table density="comfortable" class="custom-table">
                <thead>
                  <tr>
                    <th class="text-caption font-weight-bold text-uppercase w-40">Nama</th>
                    <th class="text-right text-caption font-weight-bold text-uppercase">Nominal</th>
                    <th class="text-right text-caption font-weight-bold text-uppercase">Segmen</th>
                    <th class="text-right text-caption font-weight-bold text-uppercase">AO</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="(item, idx) in store.realizeKemarinData" :key="idx">
                    <td class="text-caption py-2 whitespace-normal break-words leading-tight font-weight-medium text-high-emphasis">{{ item.nama }}</td>
                    <td class="text-right text-caption font-weight-medium">Rp {{ formatCurrency(item.nominal) }}</td>
                    <td class="text-right text-caption whitespace-normal break-words leading-tight">{{ item.nmseg }}</td>
                    <td class="text-right text-caption whitespace-normal break-words leading-tight">{{ item.nmao }}</td>
                  </tr>
                  <tr v-if="!store.realizeKemarinData.length">
                    <td colspan="4" class="text-center pa-8 text-caption text-medium-emphasis">Tidak ada data realisasi</td>
                  </tr>
                </tbody>
                <tfoot v-if="store.realizeKemarinData.length">
                  <tr class="bg-grey-lighten-4 font-weight-bold">
                    <td class="text-caption">TOTAL</td>
                    <td class="text-right text-caption">Rp {{ formatCurrency(calculateTotal(store.realizeKemarinData, 'nominal')) }}</td>
                    <td colspan="2"></td>
                  </tr>
                </tfoot>
              </v-table>
            </v-card-text>
          </v-card>
        </v-col>

      </v-row>
    </template>

    <!-- ======================== COMPARE MODE ======================== -->
    <template v-else-if="viewMode === 'compare'">
      <v-row class="mb-4" align="center">
        <v-col cols="12" md="5">
          <v-autocomplete
            v-model="store.multiComparisonPeriods"
            :items="periodItems"
            item-title="title"
            item-value="value"
            label="Periode Komparasi"
            variant="outlined"
            density="comfortable"
            prepend-inner-icon="ri-calendar-line"
            rounded="xl"
            multiple
            chips
            closable-chips
            persistent-placeholder
            @update:model-value="handleMultiCompare"
          >
          </v-autocomplete>
        </v-col>
        
        <v-col cols="12" md="4">
          <v-select
            v-model="store.compareMetric"
            :items="metricOptions"
            item-title="title"
            item-value="value"
            label="Tampilkan Data"
            variant="outlined"
            density="comfortable"
            prepend-inner-icon="ri-line-chart-line"
            rounded="xl"
            persistent-placeholder
          />
        </v-col>

        <v-col cols="12" md="3">
          <v-alert type="info" variant="tonal" density="compact" rounded="xl" class="d-flex align-center py-3">
            <span class="text-caption font-weight-bold">Comparison Mode</span>
          </v-alert>
        </v-col>
      </v-row>

      <div class="mb-6">
          <MultiPeriodCompareChart 
            :data="store.multiComparisonData" 
            :metric="store.compareMetric"
            :loading="store.loadingCompare" 
          />
      </div>

      <div v-if="store.loadingCompare" class="d-flex justify-center py-12">
        <v-progress-circular indeterminate color="primary" size="56" />
      </div>

      <v-alert v-if="!store.loadingCompare && !store.multiComparisonData" type="warning" variant="tonal" class="mb-6" rounded="lg">
        <v-icon icon="ri-database-warning-line" class="me-3" />
        <strong>Belum ada data</strong> - Pilih satu atau lebih periode histori di atas.
      </v-alert>
    </template>

    <!-- ======================== TREND MODE ======================== -->
    <template v-else-if="viewMode === 'trend'">
      <TrendChart 
        :series="store.trendSeries" 
        :loading="store.loadingTrend" 
        @period-change="store.fetchTrend"
      />

      <v-row v-if="store.trendSeries && store.trendSeries.length > 0" class="mt-4">
        <v-col cols="12" md="4">
          <v-card elevation="0" class="stat-card pa-4">
            <div class="d-flex align-center">
              <div class="stat-icon" style="background: rgba(5,150,105,0.12);">
                <v-icon icon="ri-arrow-up-line" color="success" size="24" />
              </div>
              <div class="ms-4">
                <p class="text-body-2 text-medium-emphasis mb-1">Highest Disbursement</p>
                <h3 class="text-h6 font-weight-bold text-success">
                  {{ store.formatRp(trendStats.highest) }}
                </h3>
              </div>
            </div>
          </v-card>
        </v-col>
        <v-col cols="12" md="4">
          <v-card elevation="0" class="stat-card pa-4">
            <div class="d-flex align-center">
              <div class="stat-icon" style="background: rgba(217,119,6,0.12);">
                <v-icon icon="ri-arrow-down-line" color="warning" size="24" />
              </div>
              <div class="ms-4">
                <p class="text-body-2 text-medium-emphasis mb-1">Lowest Disbursement</p>
                <h3 class="text-h6 font-weight-bold" style="color:#D97706;">
                  {{ store.formatRp(trendStats.lowest) }}
                </h3>
              </div>
            </div>
          </v-card>
        </v-col>
        <v-col cols="12" md="4">
          <v-card elevation="0" class="stat-card pa-4">
            <div class="d-flex align-center">
              <div class="stat-icon" style="background: rgba(var(--v-theme-info),0.12);">
                <v-icon icon="ri-bar-chart-2-line" color="info" size="24" />
              </div>
              <div class="ms-4">
                <p class="text-body-2 text-medium-emphasis mb-1">Average Disbursement</p>
                <h3 class="text-h6 font-weight-bold text-info">
                  {{ store.formatRp(trendStats.average) }}
                </h3>
              </div>
            </div>
          </v-card>
        </v-col>
      </v-row>
    </template>

    <!-- Empty State -->
    <v-alert v-if="!isLoading && !hasData && viewMode === 'realtime'" type="warning" variant="tonal" class="mt-6" rounded="lg">
      <v-icon icon="ri-database-warning-line" size="48" class="me-4" />
      <strong>Tidak ada data tersedia</strong>
    </v-alert>
  </div>
</template>

<style scoped>
.financing-overview { max-width: 100%; }
.page-header { padding-bottom: 16px; border-bottom: 1px solid rgba(var(--v-border-color), 0.06); }
.header-icon {
  width: 56px; height: 56px; border-radius: 16px;
  background: linear-gradient(135deg, rgba(5,150,105,0.15) 0%, rgba(5,150,105,0.05) 100%);
  display: flex; align-items: center; justify-content: center;
  border: 1px solid rgba(5,150,105,0.2);
}
:deep(.text-h4) { font-family: 'Plus Jakarta Sans', sans-serif; }
:deep(.text-h6) { font-family: 'Plus Jakarta Sans', sans-serif; }

.data-card {
  border: 1px solid rgba(var(--v-border-color), 0.12);
  border-radius: 16px;
  background: white;
  transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}
.data-card:hover { transform: translateY(-4px); box-shadow: 0 12px 24px rgba(0,0,0,0.06); }

.custom-table :deep(th) {
  background-color: #F8FAFC !important;
  color: #64748B !important;
  height: 44px !important;
  border-bottom: 1px solid #E2E8F0 !important;
}

.custom-table :deep(td) {
  min-height: 56px !important;
  border-bottom: 1px solid #F1F5F9 !important;
}

.w-33 { width: 33% !important; }
.w-40 { width: 40% !important; }
.w-50 { width: 50% !important; }

.kol-indicator {
  width: 32px;
  height: 32px;
  border-radius: 10px;
  display: flex;
  align-items: center;
  justify-content: center;
  color: white;
  font-weight: 800;
  font-size: 14px;
  box-shadow: 0 4px 10px rgba(0,0,0,0.1);
}

:deep(.bg-grey-lighten-4) {
  background-color: #F1F5F9 !important;
}

.stat-card {
  border: 1px solid rgba(var(--v-border-color), 0.08);
  border-radius: 12px;
}
.stat-icon {
  width: 48px; height: 48px; border-radius: 12px;
  display: flex; align-items: center; justify-content: center;
}
.view-toggle-wrapper { display: flex; align-items: center; justify-content: flex-end; }
.view-toggle {
  width: auto !important;
  overflow: visible !important;
  border: 2px solid #059669 !important;
  border-radius: 28px !important;
  background: rgb(var(--v-theme-surface)) !important;
  padding: 6px 8px !important;
  gap: 8px !important;
}
:deep(.v-btn-toggle .v-btn) {
  border-radius: 100px !important;
  text-transform: none;
  font-weight: 600;
  letter-spacing: 0;
  width: auto !important;
  min-width: max-content !important;
  padding: 0 20px !important;
  height: 38px !important;
  border: none !important;
  background: transparent !important;
  color: rgb(var(--v-theme-on-surface)) !important;
  transition: all 0.2s ease;
}
:deep(.v-btn-toggle .v-btn--active) {
  background: linear-gradient(135deg, #059669 0%, #10B981 100%) !important;
  color: white !important;
  box-shadow: 0 2px 8px rgba(5, 150, 105, 0.3);
}
:deep(.v-btn-toggle .v-btn:not(.v-btn--active):hover) {
  background: rgba(5, 150, 105, 0.08) !important;
}
</style>
