<script setup>
import { ref, computed, onMounted, watch } from 'vue'
import { Head } from '@inertiajs/vue3'
import DefaultLayout from '@/layouts/default.vue'
import axios from 'axios'
import VueApexCharts from 'vue3-apexcharts'

defineOptions({ layout: DefaultLayout })

// ─── State Management ────────────────────────────────────────
const isLoading = ref(true)
const selectedDimension = ref('cabang')
const selectedMetric = ref('os')
const selectedCabang = ref('')
const viewMode = ref('grid')
const hideEmptyRows = ref(true)

const rekapData = ref({
  rows: [],
  totals: {
    noa: 0,
    total_os: 0,
    npf_ratio: 0,
    total_ppap: 0,
    npf_os: 0
  },
  meta: {}
})

const cabangs = ref([])

const dimensionOptions = [
  { label: 'Per Cabang', value: 'cabang' },
  { label: 'Per Wilayah', value: 'wilayah' },
  { label: 'Per Account Officer', value: 'ao' },
  { label: 'Per Produk/Akad', value: 'produk' },
  { label: 'Per Segmen', value: 'segmen' },
  { label: 'Per Sektor Ekonomi', value: 'sekon' },
]

// ─── Computed Properties ─────────────────────────────────────
const filteredRows = computed(() => {
  if (!hideEmptyRows.value) return rekapData.value.rows
  return rekapData.value.rows.filter(r => r.noa > 0 && r.total_os > 0)
})

const totals = computed(() => rekapData.value.totals)

// Helper: Format Rupiah
const formatRp = (value) => {
  if (!value) return 'Rp 0'
  return new Intl.NumberFormat('id-ID', {
    style: 'currency',
    currency: 'IDR',
    minimumFractionDigits: 0,
    maximumFractionDigits: 0
  }).format(value)
}

const formatNumber = (value) => {
  if (!value) return '0'
  return new Intl.NumberFormat('id-ID').format(value)
}

// Format metric for table
const formatMetric = (valOS, valNOA) => {
  if (selectedMetric.value === 'os') {
    return formatRp(valOS)
  }
  return formatNumber(valNOA)
}

// NPF Heatmap Logic
const getNpfCellStyle = (npfRatio) => {
  if (npfRatio > 5) return { background: '#fee2e2', color: '#dc2626', fontWeight: '700' }
  if (npfRatio > 2) return { background: '#fef9c3', color: '#ca8a04', fontWeight: '600' }
  if (npfRatio > 0) return { background: '#dcfce7', color: '#16a34a', fontWeight: '600' }
  return { background: '#f8fafc', color: '#94a3b8' }
}

// ─── Chart Options ───────────────────────────────────────────
const treeMapSeries = computed(() => [{
  data: filteredRows.value.map(r => ({
    x: r.label || '(Kosong)',
    y: Number((r.total_os / 1e9).toFixed(2)) // Miliar
  }))
}])

const donutSeries = computed(() => [
  totals.value.total_os - (rekapData.value.totals.npf_os || 0), // Lancar
  rekapData.value.totals.npf_os || 0 // NPF
])

const treeMapOpts = computed(() => ({
  chart: { type: 'treemap', fontFamily: "'Plus Jakarta Sans', sans-serif", toolbar: { show: false } },
  title: { text: 'Sebaran Outstanding (Miliar)', style: { fontWeight: 600 } },
  plotOptions: {
    treemap: {
      enableShades: true,
      shadeIntensity: 0.5,
      reverseNegativeShade: true,
      colorScale: {
        ranges: [
          { from: 0, to: 10, color: '#94a3b8' },
          { from: 10, to: 100, color: '#3b82f6' },
          { from: 100, to: 100000, color: '#1e40af' }
        ]
      }
    }
  },
  dataLabels: {
    enabled: true,
    formatter: function (text, op) {
      return [text, op.value + ' M']
    }
  }
}))

const donutOpts = computed(() => ({
  chart: { type: 'donut', fontFamily: "'Plus Jakarta Sans', sans-serif" },
  labels: ['Lancar (Kol 1-2)', 'NPF (Kol 3-5)'],
  colors: ['#10b981', '#ef4444'],
  title: { text: 'Rasio NPF vs Lancar', style: { fontWeight: 600 } },
  plotOptions: {
    pie: { donut: { size: '70%' } }
  },
  dataLabels: {
    enabled: true,
    formatter: function (val) {
      return val.toFixed(2) + "%"
    }
  },
  legend: {
    position: 'bottom'
  }
}))

// ─── API Calls ───────────────────────────────────────────────
const fetchCabangs = async () => {
  try {
    const res = await axios.get('/api/v1/financing/cabangs')
    if (res.data.success) {
      cabangs.value = res.data.data
    }
  } catch (error) {
    console.error('Failed to load cabangs:', error)
  }
}

const fetchRekapMaster = async () => {
  isLoading.value = true
  try {
    const params = {
      group_by: selectedDimension.value,
      cabang: selectedCabang.value || ''
    }
    const res = await axios.get('/api/v1/financing/rekap-master', { params })
    if (res.data.success) {
      rekapData.value.rows = res.data.rows
      rekapData.value.totals = res.data.totals
      rekapData.value.meta = res.data.meta
    }
  } catch (error) {
    console.error('Failed to load rekap data:', error)
  } finally {
    isLoading.value = false
  }
}

// ─── Lifecycle & Watchers ────────────────────────────────────
onMounted(() => {
  fetchCabangs()
  fetchRekapMaster()
})

watch([selectedDimension, selectedCabang], () => {
  fetchRekapMaster()
})

</script>

<template>
  <div class="rekap-master-console">
    <Head title="Master Rekap Console - Pembiayaan" />

    <!-- HEADER & FILTERS -->
    <v-card class="mb-6 elevation-1 rounded-xl">
      <v-card-text>
        <div class="d-flex flex-column flex-md-row justify-space-between align-center gap-4">
          <div class="d-flex align-center">
            <v-avatar color="primary-lighten-4" class="mr-3" rounded="lg">
              <v-icon color="primary" icon="ri-dashboard-3-line"></v-icon>
            </v-avatar>
            <div>
              <h2 class="text-h5 font-weight-bold mb-0">Master Rekap Console</h2>
              <div class="text-caption text-medium-emphasis">Volume & distribusi bisnis pembiayaan per dimensi — Analisis risiko di <a href="/financing/quality" class="text-primary font-weight-medium">Quality Console</a></div>
            </div>
          </div>

          <div class="d-flex flex-wrap align-center ga-3">
            <!-- Dimensi Toggle -->
            <v-select
              v-model="selectedDimension"
              :items="dimensionOptions"
              item-title="label"
              item-value="value"
              prepend-inner-icon="ri-layout-grid-line"
              variant="outlined"
              density="compact"
              hide-details
              style="min-width: 200px"
              rounded="lg"
              class="flex-shrink-0"
            ></v-select>

            <!-- Cabang Filter -->
            <v-select
              v-model="selectedCabang"
              :items="cabangs"
              item-title="nama"
              item-value="kdloc"
              label="Semua Cabang"
              prepend-inner-icon="ri-store-2-line"
              variant="outlined"
              density="compact"
              hide-details
              clearable
              style="min-width: 200px"
              rounded="lg"
              class="flex-shrink-0"
            ></v-select>

            <!-- Metric Switcher (Dropdown) -->
            <v-select
              v-model="selectedMetric"
              :items="[
                { title: 'Outstanding (Rp)', value: 'os' },
                { title: 'Jumlah Nasabah (NOA)', value: 'noa' }
              ]"
              item-title="title"
              item-value="value"
              prepend-inner-icon="ri-funds-line"
              variant="outlined"
              density="compact"
              hide-details
              style="min-width: 200px"
              rounded="lg"
              class="flex-shrink-0"
            ></v-select>
            
            <!-- View Toggle -->
            <v-btn-toggle v-model="viewMode" mandatory rounded="lg" density="compact" class="border flex-shrink-0">
              <v-btn value="grid" size="small" icon="ri-table-line"></v-btn>
              <v-btn value="chart" size="small" icon="ri-bar-chart-box-line"></v-btn>
            </v-btn-toggle>
          </div>
        </div>
      </v-card-text>
    </v-card>

    <!-- SCORECARDS -->
    <v-row class="mb-6">
      <v-col cols="12" sm="6" md="3">
        <v-card class="elevation-1 rounded-xl overflow-hidden h-100">
          <v-card-text class="d-flex flex-column h-100">
            <div class="d-flex justify-space-between align-center mb-4">
              <div class="text-overline text-medium-emphasis">TOTAL NOA</div>
              <v-avatar size="32" color="blue-lighten-4" rounded>
                <v-icon color="blue-darken-2" icon="ri-group-line" size="small"></v-icon>
              </v-avatar>
            </div>
            <div class="text-h4 font-weight-bold mb-1">{{ formatNumber(totals.noa) }}</div>
            <div class="text-caption text-medium-emphasis mt-auto">Rekening aktif pembiayaan</div>
          </v-card-text>
        </v-card>
      </v-col>

      <v-col cols="12" sm="6" md="3">
        <v-card class="elevation-1 rounded-xl overflow-hidden h-100">
          <v-card-text class="d-flex flex-column h-100">
            <div class="d-flex justify-space-between align-center mb-4">
              <div class="text-overline text-medium-emphasis">TOTAL O/S POKOK</div>
              <v-avatar size="32" color="emerald-lighten-4" rounded>
                <v-icon color="emerald-darken-2" icon="ri-money-dollar-circle-line" size="small"></v-icon>
              </v-avatar>
            </div>
            <div class="text-h5 font-weight-bold mb-1">{{ formatRp(totals.total_os) }}</div>
            <div class="text-caption text-medium-emphasis mt-auto">Portofolio pembiayaan aktif</div>
          </v-card-text>
        </v-card>
      </v-col>

      <v-col cols="12" sm="6" md="3">
        <v-card class="elevation-1 rounded-xl overflow-hidden h-100">
          <v-card-text class="d-flex flex-column h-100">
            <div class="d-flex justify-space-between align-center mb-4">
              <div class="text-overline text-medium-emphasis">O/S KOL 1 (LANCAR)</div>
              <v-avatar size="32" color="green-lighten-4" rounded>
                <v-icon color="green-darken-2" icon="ri-checkbox-circle-line" size="small"></v-icon>
              </v-avatar>
            </div>
            <div class="text-h5 font-weight-bold mb-1">{{ formatRp(totals.kol1_os || 0) }}</div>
            <div class="text-caption text-medium-emphasis mt-auto">Portofolio kolektibilitas lancar</div>
          </v-card-text>
        </v-card>
      </v-col>

      <v-col cols="12" sm="6" md="3">
        <v-card class="elevation-1 rounded-xl overflow-hidden h-100" style="border-left: 4px solid #ef4444">
          <v-card-text class="d-flex flex-column h-100">
            <div class="d-flex justify-space-between align-center mb-4">
              <div class="text-overline text-medium-emphasis">NOA NPF (KOL 3-5)</div>
              <v-avatar size="32" color="red-lighten-4" rounded>
                <v-icon color="red-darken-2" icon="ri-error-warning-line" size="small"></v-icon>
              </v-avatar>
            </div>
            <div class="text-h4 font-weight-bold text-error mb-1">{{ formatNumber((totals.kol3_noa || 0) + (totals.kol4_noa || 0) + (totals.kol5_noa || 0)) }}</div>
            <div class="text-caption text-medium-emphasis mt-auto">Rekening Kol 3+4+5 &mdash; <a href="/financing/quality" class="text-primary">detail risiko &rsaquo;</a></div>
          </v-card-text>
        </v-card>
      </v-col>
    </v-row>

    <!-- MAIN CONTENT -->
    <v-card class="elevation-1 rounded-xl">
      <v-card-text class="pa-0">
        <!-- TOOLBAR -->
        <v-toolbar color="transparent" density="compact" class="px-4 pt-2">
          <v-toolbar-title class="text-subtitle-1 font-weight-bold">
            Detail {{ dimensionOptions.find(d => d.value === selectedDimension)?.label || 'Dimensi' }}
          </v-toolbar-title>
          <v-spacer></v-spacer>
          <v-checkbox
            v-if="viewMode === 'grid'"
            v-model="hideEmptyRows"
            label="Sembunyikan baris kosong"
            density="compact"
            hide-details
            class="mr-4 text-caption"
            color="primary"
          ></v-checkbox>
        </v-toolbar>

        <v-divider></v-divider>

        <!-- LOADER -->
        <div v-if="isLoading" class="d-flex justify-center align-center py-12">
          <v-progress-circular indeterminate color="primary" size="48"></v-progress-circular>
        </div>

        <!-- GRID MODE -->
        <div v-else-if="viewMode === 'grid'" class="overflow-x-auto">
          <v-table class="rekap-table" density="comfortable" hover>
            <thead>
              <tr>
                <th class="text-left font-weight-bold text-uppercase bg-grey-lighten-4">Label</th>
                <th class="text-right font-weight-bold text-uppercase bg-grey-lighten-4">NOA</th>
                <th class="text-right font-weight-bold text-uppercase bg-grey-lighten-4">Total O/S</th>
                <th class="text-right font-weight-bold text-uppercase bg-grey-lighten-4">Kol 1 (Lancar)</th>
                <th class="text-right font-weight-bold text-uppercase bg-grey-lighten-4">Kol 2 (DPK)</th>
                <th class="text-right font-weight-bold text-uppercase bg-grey-lighten-4">Kol 3 (KL)</th>
                <th class="text-right font-weight-bold text-uppercase bg-grey-lighten-4">Kol 4 (D)</th>
                <th class="text-right font-weight-bold text-uppercase bg-grey-lighten-4">Kol 5 (M)</th>
                <th class="text-center font-weight-bold text-uppercase bg-grey-lighten-4">NPF %</th>
              </tr>
            </thead>
            <tbody>
              <tr v-if="filteredRows.length === 0">
                <td colspan="9" class="text-center py-8 text-medium-emphasis">Tidak ada data untuk ditampilkan</td>
              </tr>
              <tr v-for="(row, idx) in filteredRows" :key="idx">
                <td class="font-weight-medium">{{ row.label }}</td>
                <td class="text-right">{{ formatNumber(row.noa) }}</td>
                <td class="text-right">{{ formatRp(row.total_os) }}</td>
                <td class="text-right">{{ formatMetric(row.kol1_os, row.kol1_noa) }}</td>
                <td class="text-right">{{ formatMetric(row.kol2_os, row.kol2_noa) }}</td>
                <td class="text-right">{{ formatMetric(row.kol3_os, row.kol3_noa) }}</td>
                <td class="text-right">{{ formatMetric(row.kol4_os, row.kol4_noa) }}</td>
                <td class="text-right">{{ formatMetric(row.kol5_os, row.kol5_noa) }}</td>
                <td class="text-center" :style="getNpfCellStyle(row.npf_ratio)">
                  {{ row.npf_ratio }}%
                </td>
              </tr>
            </tbody>
            <tfoot class="bg-grey-lighten-4">
              <tr>
                <td class="font-weight-bold">TOTAL KESELURUHAN</td>
                <td class="text-right font-weight-bold">{{ formatNumber(totals.noa) }}</td>
                <td class="text-right font-weight-bold">{{ formatRp(totals.total_os) }}</td>
                <td colspan="5"></td>
                <td class="text-center font-weight-bold" :style="getNpfCellStyle(totals.npf_ratio)">
                  {{ totals.npf_ratio }}%
                </td>
              </tr>
            </tfoot>
          </v-table>
        </div>

        <!-- CHART MODE -->
        <div v-else-if="viewMode === 'chart'" class="pa-6">
          <v-row>
            <v-col cols="12">
              <v-card variant="outlined" class="rounded-lg border">
                <v-card-title class="text-subtitle-1 font-weight-bold pa-4 pb-0">Distribusi Outstanding (Miliar) per {{ dimensionOptions.find(d => d.value === selectedDimension)?.label }}</v-card-title>
                <v-card-text>
                  <VueApexCharts type="treemap" height="500" :options="treeMapOpts" :series="treeMapSeries" />
                </v-card-text>
              </v-card>
            </v-col>
          </v-row>
          <v-row class="mt-0">
            <v-col cols="12">
              <v-alert type="info" variant="tonal" density="compact" rounded="lg" class="text-caption">
                <v-icon start size="small">ri-information-line</v-icon>
                Untuk analisis <strong>rasio NPF, coverage ratio, dan aging bucket</strong>, buka halaman
                <a href="/financing/quality" class="font-weight-bold">Quality & Risk Console</a>.
              </v-alert>
            </v-col>
          </v-row>
        </div>
      </v-card-text>
    </v-card>
  </div>
</template>

<style scoped>
.gap-3 { gap: 12px; }
.gap-4 { gap: 16px; }

.rekap-table {
  border-bottom: 1px solid rgba(0, 0, 0, 0.12);
}

.rekap-table th {
  font-size: 0.75rem !important;
  letter-spacing: 0.05em;
  white-space: nowrap;
}

.rekap-table td {
  font-size: 0.875rem !important;
  white-space: nowrap;
}
</style>
