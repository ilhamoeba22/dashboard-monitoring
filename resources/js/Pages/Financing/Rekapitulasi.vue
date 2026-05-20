<script setup>
import { ref, computed, onMounted, watch } from 'vue'
import { Head } from '@inertiajs/vue3'
import DefaultLayout from '@/layouts/default.vue'
import axios from 'axios'
import VueApexCharts from 'vue3-apexcharts'
import '@/assets/css/financing-shared.css'

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
  <div class="fin-page px-4 pt-0">
    <Head title="Master Rekap Console - Pembiayaan" />

    <!-- ── HERO HEADER ─────────────────────────────────────────── -->
    <div class="fin-hero mb-6">
      <div class="fin-hero__deco"></div>
      <div class="fin-hero__inner">
        <div class="d-flex flex-column flex-md-row justify-space-between align-start align-md-center gap-4">
          <div class="d-flex align-center gap-4">
            <div class="fin-hero__icon">
              <v-icon icon="ri-dashboard-3-line" size="26" color="white" />
            </div>
            <div class="fin-hero__meta">
              <h1 class="fin-hero__title">Master Rekap Console</h1>
              <p class="fin-hero__subtitle">
                Volume & distribusi bisnis pembiayaan per dimensi — Analisis risiko di <a href="/financing/quality" class="text-teal-accent-2 font-weight-bold" style="color: #5eead4; text-decoration: underline;">Quality Console</a>
              </p>
              <div class="fin-hero__badges">
                <span class="fin-badge fin-badge--glass">📊 Analytics Module</span>
              </div>
            </div>
          </div>

          <div class="fin-filter-bar">
            <!-- Dimensi Toggle -->
            <v-select
              v-model="selectedDimension"
              :items="dimensionOptions"
              item-title="label"
              item-value="value"
              prepend-inner-icon="ri-layout-grid-line"
              variant="plain"
              density="compact"
              hide-details
              style="width: 140px;"
            ></v-select>

            <div style="width: 1px; height: 24px; background: rgba(255,255,255,0.2);" class="mx-1"></div>

            <!-- Cabang Filter -->
            <v-select
              v-model="selectedCabang"
              :items="cabangs"
              item-title="nama"
              item-value="kdloc"
              label="Semua Cabang"
              prepend-inner-icon="ri-store-2-line"
              variant="plain"
              density="compact"
              hide-details
              clearable
              style="width: 150px;"
            ></v-select>

            <div style="width: 1px; height: 24px; background: rgba(255,255,255,0.2);" class="mx-1"></div>

            <!-- Metric Switcher -->
            <v-select
              v-model="selectedMetric"
              :items="[
                { title: 'Outstanding (Rp)', value: 'os' },
                { title: 'Nasabah (NOA)', value: 'noa' }
              ]"
              item-title="title"
              item-value="value"
              prepend-inner-icon="ri-funds-line"
              variant="plain"
              density="compact"
              hide-details
              style="width: 140px;"
            ></v-select>
            
            <div style="width: 1px; height: 24px; background: rgba(255,255,255,0.2);" class="mx-1"></div>

            <!-- View Toggle -->
            <v-btn-toggle v-model="viewMode" mandatory rounded density="compact" style="background: rgba(255,255,255,0.1); border: 1px solid rgba(255,255,255,0.2);">
              <v-btn value="grid" size="small" icon="ri-table-line" color="white"></v-btn>
              <v-btn value="chart" size="small" icon="ri-bar-chart-box-line" color="white"></v-btn>
            </v-btn-toggle>
          </div>
        </div>
      </div>
    </div>

    <!-- SCORECARDS -->
    <div class="kpi-cards-grid mb-6">
      <div class="kpi-card kpi-card--info">
        <div class="kpi-card__accent" style="background: linear-gradient(90deg, #3b82f6, #0ea5e9)"></div>
        <div class="kpi-card__inner">
          <div class="kpi-card__header">
            <span class="kpi-card__label text-blue-600">TOTAL NOA</span>
            <div class="kpi-card__icon fin-icon-blue">
              <v-icon icon="ri-group-line" size="18" />
            </div>
          </div>
          <div class="kpi-card__value">{{ formatNumber(totals.noa) }}</div>
          <div class="kpi-card__sub">Rekening aktif pembiayaan</div>
        </div>
      </div>

      <div class="kpi-card kpi-card--success">
        <div class="kpi-card__accent" style="background: linear-gradient(90deg, #10b981, #34d399)"></div>
        <div class="kpi-card__inner">
          <div class="kpi-card__header">
            <span class="kpi-card__label text-emerald-600">TOTAL O/S POKOK</span>
            <div class="kpi-card__icon fin-icon-green">
              <v-icon icon="ri-money-dollar-circle-line" size="18" />
            </div>
          </div>
          <div class="kpi-card__value" style="font-size: 20px;">{{ formatRp(totals.total_os) }}</div>
          <div class="kpi-card__sub">Portofolio pembiayaan aktif</div>
        </div>
      </div>

      <div class="kpi-card">
        <div class="kpi-card__accent" style="background: linear-gradient(90deg, #64748b, #94a3b8)"></div>
        <div class="kpi-card__inner">
          <div class="kpi-card__header">
            <span class="kpi-card__label">O/S KOL 1 (LANCAR)</span>
            <div class="kpi-card__icon bg-slate-100">
              <v-icon icon="ri-checkbox-circle-line" size="18" color="grey-darken-1" />
            </div>
          </div>
          <div class="kpi-card__value" style="font-size: 20px;">{{ formatRp(totals.kol1_os || 0) }}</div>
          <div class="kpi-card__sub">Portofolio kolektibilitas lancar</div>
        </div>
      </div>

      <div class="kpi-card kpi-card--danger">
        <div class="kpi-card__accent" style="background: linear-gradient(90deg, #e11d48, #fb7185)"></div>
        <div class="kpi-card__inner">
          <div class="kpi-card__header">
            <span class="kpi-card__label text-rose-600">NOA NPF (KOL 3-5)</span>
            <div class="kpi-card__icon fin-icon-red">
              <v-icon icon="ri-error-warning-line" size="18" />
            </div>
          </div>
          <div class="kpi-card__value">{{ formatNumber((totals.kol3_noa || 0) + (totals.kol4_noa || 0) + (totals.kol5_noa || 0)) }}</div>
          <div class="kpi-card__sub">Rekening Kol 3-5 &mdash; <a href="/financing/quality" style="color: #0284c7;">detail risiko &rsaquo;</a></div>
        </div>
      </div>
    </div>

    <!-- MAIN CONTENT -->
    <div class="content-card">
      <div class="content-card__header">
        <div>
          <div class="content-card__title">Detail {{ dimensionOptions.find(d => d.value === selectedDimension)?.label || 'Dimensi' }}</div>
          <div class="content-card__subtitle">Rincian agregasi portofolio</div>
        </div>
        <div class="d-flex align-center gap-2">
          <v-checkbox
            v-if="viewMode === 'grid'"
            v-model="hideEmptyRows"
            label="Sembunyikan kosong"
            density="compact"
            hide-details
            class="text-xs"
            color="primary"
          ></v-checkbox>
        </div>
      </div>

      <div class="content-card__body pa-0">
        <!-- LOADER -->
        <div v-if="isLoading" class="fin-loading">
          <v-progress-circular indeterminate color="primary" size="48"></v-progress-circular>
        </div>

        <!-- GRID MODE -->
        <div v-else-if="viewMode === 'grid'" class="overflow-x-auto">
          <table class="fin-table fin-vtable">
            <thead>
              <tr>
                <th>Label</th>
                <th style="text-align:right;">NOA</th>
                <th style="text-align:right;">Total O/S</th>
                <th style="text-align:right;">Kol 1 (Lancar)</th>
                <th style="text-align:right;">Kol 2 (DPK)</th>
                <th style="text-align:right;">Kol 3 (KL)</th>
                <th style="text-align:right;">Kol 4 (D)</th>
                <th style="text-align:right;">Kol 5 (M)</th>
                <th style="text-align:center;">NPF %</th>
              </tr>
            </thead>
            <tbody>
              <tr v-if="filteredRows.length === 0">
                <td colspan="9" class="text-center py-8 text-medium-emphasis">Tidak ada data untuk ditampilkan</td>
              </tr>
              <tr v-for="(row, idx) in filteredRows" :key="idx">
                <td class="font-weight-bold" style="color: #1e293b;">{{ row.label }}</td>
                <td style="text-align:right; font-weight: 500;">{{ formatNumber(row.noa) }}</td>
                <td style="text-align:right; font-weight: 700;">{{ formatRp(row.total_os) }}</td>
                <td style="text-align:right;">{{ formatMetric(row.kol1_os, row.kol1_noa) }}</td>
                <td style="text-align:right;">{{ formatMetric(row.kol2_os, row.kol2_noa) }}</td>
                <td style="text-align:right;">{{ formatMetric(row.kol3_os, row.kol3_noa) }}</td>
                <td style="text-align:right;">{{ formatMetric(row.kol4_os, row.kol4_noa) }}</td>
                <td style="text-align:right;">{{ formatMetric(row.kol5_os, row.kol5_noa) }}</td>
                <td style="text-align:center;">
                  <span class="fin-pill" :style="getNpfCellStyle(row.npf_ratio)">
                    {{ row.npf_ratio }}%
                  </span>
                </td>
              </tr>
            </tbody>
            <tfoot>
              <tr>
                <td>TOTAL KESELURUHAN</td>
                <td style="text-align:right;">{{ formatNumber(totals.noa) }}</td>
                <td style="text-align:right;">{{ formatRp(totals.total_os) }}</td>
                <td colspan="5"></td>
                <td style="text-align:center;">
                  <span class="fin-pill" :style="getNpfCellStyle(totals.npf_ratio)">
                    {{ totals.npf_ratio }}%
                  </span>
                </td>
              </tr>
            </tfoot>
          </table>
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
      </div>
    </div>
  </div>
</template>

<style scoped>
/* No extra scoped styles needed, using shared design system */
</style>
