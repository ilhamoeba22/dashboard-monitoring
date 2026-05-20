<template>
  <DefaultLayout>
    <Head title="Repayment Rate Monitoring" />

    <div class="fin-page px-4 pt-0">
      <!-- ── HERO HEADER ─────────────────────────────────────────── -->
      <div class="fin-hero mb-6">
        <div class="fin-hero__deco"></div>
        <div class="fin-hero__inner">
          <div class="d-flex flex-column flex-md-row justify-space-between align-start align-md-center gap-4">
            <div class="d-flex align-center gap-4">
              <div class="fin-hero__icon fin-icon-blue">
                <v-icon icon="ri-bar-chart-grouped-fill" size="26" color="white" />
              </div>
              <div class="fin-hero__meta">
                <h1 class="fin-hero__title">Repayment Rate Monitoring</h1>
                <p class="fin-hero__subtitle">Analisis tingkat pembayaran nasabah aktif (Modal + Margin)</p>
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
        <div class="kpi-card kpi-card--info">
          <div class="kpi-card__accent" style="background: linear-gradient(90deg, #1e40af, #3b82f6)"></div>
          <div class="kpi-card__inner">
            <div class="kpi-card__header">
              <span class="kpi-card__label text-blue-600">Overall RR</span>
              <div class="kpi-card__icon fin-icon-blue">
                <v-icon icon="ri-percent-line" size="18" />
              </div>
            </div>
            <div class="kpi-card__value mt-2">{{ safePct(summary.overall_rate).toFixed(2) }}%</div>
            <div class="kpi-card__sub text-blue-600 font-weight-bold">Rata-rata</div>
          </div>
        </div>

        <div class="kpi-card">
          <div class="kpi-card__accent" style="background: linear-gradient(90deg, #4338ca, #6366f1)"></div>
          <div class="kpi-card__inner">
            <div class="kpi-card__header">
              <span class="kpi-card__label text-indigo-600">Total Nasabah</span>
              <div class="kpi-card__icon fin-icon-indigo">
                <v-icon icon="ri-group-line" size="18" />
              </div>
            </div>
            <div class="kpi-card__value mt-2">{{ parseInt(summary.total_nasabah || 0) }}</div>
            <div class="kpi-card__sub text-indigo-600 font-weight-bold">Aktif</div>
          </div>
        </div>

        <div class="kpi-card kpi-card--success">
          <div class="kpi-card__accent" style="background: linear-gradient(90deg, #10b981, #34d399)"></div>
          <div class="kpi-card__inner">
            <div class="kpi-card__header">
              <span class="kpi-card__label text-emerald-600">Lancar (100%)</span>
              <div class="kpi-card__icon fin-icon-green">
                <v-icon icon="ri-checkbox-circle-line" size="18" />
              </div>
            </div>
            <div class="kpi-card__value mt-2">{{ parseInt(summary.nasabah_100_pct || 0) }}</div>
            <div class="kpi-card__sub text-emerald-600 font-weight-bold">Nasabah</div>
          </div>
        </div>

        <div class="kpi-card kpi-card--warning">
          <div class="kpi-card__accent" style="background: linear-gradient(90deg, #d97706, #fbbf24)"></div>
          <div class="kpi-card__inner">
            <div class="kpi-card__header">
              <span class="kpi-card__label text-amber-600">Warning (&lt;80%)</span>
              <div class="kpi-card__icon fin-icon-amber">
                <v-icon icon="ri-alert-line" size="18" />
              </div>
            </div>
            <div class="kpi-card__value mt-2">{{ parseInt(summary.nasabah_warning || 0) }}</div>
            <div class="kpi-card__sub text-amber-600 font-weight-bold">Nasabah</div>
          </div>
        </div>
      </div>

      <!-- Filter Bar -->
      <v-card class="d-flex flex-wrap align-center ga-3 pa-4 bg-white rounded-xl border shadow-sm mb-6" elevation="0">
        <div style="max-width: 300px; flex: 1 1 auto;">
          <v-text-field
            v-model="filters.search"
            label="Cari (NOCIF, Nama, Kontrak)"
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
        <div style="max-width: 180px; flex: 1 1 auto;">
          <v-select
            v-model="filters.cabang"
            :items="cabangList"
            label="Cabang"
            density="compact"
            variant="outlined"
            clearable
            hide-details
            rounded="lg"
            @update:model-value="fetchData"
          />
        </div>
        <div style="max-width: 150px; flex: 1 1 auto;">
          <v-select
            v-model="filters.segmen"
            :items="segmenList"
            label="Segmen"
            density="compact"
            variant="outlined"
            clearable
            hide-details
            rounded="lg"
            @update:model-value="fetchData"
          />
        </div>
        <div style="max-width: 150px; flex: 1 1 auto;">
          <v-select
            v-model="filters.collectibility"
            :items="kolOptions"
            label="Kolektibilitas"
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

      <!-- Data Table -->
      <div class="content-card">
        <div v-if="!data || data.length === 0" class="pa-12 text-center bg-slate-50 rounded-xl">
          <v-icon icon="ri-inbox-archive-line" size="64" class="text-slate-300 mb-4"></v-icon>
          <div class="text-h6 text-slate-500">Tidak ada data Repayment Rate ditemukan</div>
          <div class="text-caption text-slate-400 mt-2">Coba ubah filter pencarian</div>
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
            <template #item.nama_nasabah="{ item }">
              <div class="font-weight-medium">{{ item.nama_nasabah || 'N/A' }}</div>
              <div class="text-caption text-grey">{{ item.nocif || '-' }}</div>
            </template>

            <template #item.colbaru="{ item }">
              <v-chip :color="getKolColor(item.colbaru)" size="small" variant="tonal" class="font-weight-bold px-3">
                Kol {{ item.colbaru || '-' }}
              </v-chip>
            </template>

            <template #item.totaltag="{ item }">
              <div class="font-weight-bold">{{ formatCurrency(item.totaltag) }}</div>
              <div class="text-caption text-grey">Modal: {{ formatCurrency(item.tagmdl) }}</div>
            </template>

            <template #item.totalbyr="{ item }">
              <div class="font-weight-bold text-success">{{ formatCurrency(item.totalbyr) }}</div>
              <div class="text-caption text-grey">Modal: {{ formatCurrency(item.byrmdl) }}</div>
            </template>

            <template #item.pcttotal="{ item }">
              <div class="d-flex align-center" style="min-width: 140px">
                <div class="flex-grow-1 mr-2">
                  <v-progress-linear
                    :model-value="safePct(item.pcttotal)"
                    :color="getRRColor(safePct(item.pcttotal))"
                    height="8"
                    rounded
                  ></v-progress-linear>
                </div>
                <v-chip
                  :color="getRRColor(safePct(item.pcttotal))"
                  size="small"
                  variant="tonal"
                  class="font-weight-bold flex-shrink-0"
                >
                  {{ safePct(item.pcttotal).toFixed(2) }}%
                </v-chip>
              </div>
            </template>

            <template #item.nama_ao="{ item }">
              <v-chip size="small" variant="outlined" color="primary">
                <v-icon start icon="ri-user-follow-line" size="14"></v-icon>
                {{ item.nama_ao || 'N/A' }}
              </v-chip>
            </template>

            <template #item.actions="{ item }">
              <v-btn icon size="small" variant="text" color="primary" @click="showDetail(item)">
                <v-icon icon="ri-eye-line"></v-icon>
              </v-btn>
            </template>
          </v-data-table>
        </div>
      </div>

      <!-- Detail Modal -->
      <v-dialog v-model="detailDialog" max-width="900px" scrollable>
        <v-card v-if="selectedItem" class="rounded-xl">
          <v-card-title class="d-flex align-center pa-4 bg-primary text-white">
            <v-icon icon="ri-information-line" class="mr-2" color="white" size="24"></v-icon>
            <span class="text-h6 font-weight-bold">Detail Repayment Rate</span>
            <v-spacer />
            <v-btn icon variant="text" color="white" size="small" @click="detailDialog = false">
              <v-icon icon="ri-close-line" size="20"></v-icon>
            </v-btn>
          </v-card-title>
          <v-divider></v-divider>
          <v-card-text class="pa-4">
            <v-row>
              <v-col cols="12" md="6">
                <div class="mb-2"><strong>Nama:</strong> {{ selectedItem.nama_nasabah || 'N/A' }}</div>
                <div class="mb-2"><strong>NOCIF:</strong> {{ selectedItem.nocif || '-' }}</div>
                <div class="mb-2"><strong>No Kontrak:</strong> {{ selectedItem.nokontrak || '-' }}</div>
                <div class="mb-2"><strong>Produk:</strong> {{ selectedItem.nama_produk || '-' }}</div>
              </v-col>
              <v-col cols="12" md="6">
                <div class="mb-2"><strong>AO:</strong> {{ selectedItem.nama_ao || 'N/A' }}</div>
                <div class="mb-2"><strong>Cabang:</strong> {{ selectedItem.nama_cabang || '-' }}</div>
                <div class="mb-2">
                  <strong>Kolektibilitas:</strong>
                  <v-chip :color="getKolColor(selectedItem.colbaru)" size="small" variant="tonal" class="ml-2 font-weight-bold">
                    Kol {{ selectedItem.colbaru || '-' }}
                  </v-chip>
                </div>
              </v-col>
              <v-col cols="12">
                <v-divider class="my-3" />
                <h4 class="mb-3 text-h6 font-weight-bold">Rincian Pembayaran</h4>
              </v-col>
              <v-col cols="12" md="6">
                <v-card variant="outlined" class="pa-4 rounded-lg">
                  <div class="text-caption text-grey mb-1">Tagihan Modal</div>
                  <div class="text-h6 font-weight-bold">{{ formatCurrency(selectedItem.tagmdl) }}</div>
                  <div class="text-caption text-grey mt-3">Bayar Modal</div>
                  <div class="text-h6 font-weight-bold text-success">{{ formatCurrency(selectedItem.byrmdl) }}</div>
                  <div class="text-caption mt-3">
                    RR Modal:
                    <strong :class="getRRTextColor(safePct(selectedItem.pctmdl))">
                      {{ safePct(selectedItem.pctmdl).toFixed(2) }}%
                    </strong>
                  </div>
                </v-card>
              </v-col>
              <v-col cols="12" md="6">
                <v-card variant="outlined" class="pa-4 rounded-lg">
                  <div class="text-caption text-grey mb-1">Tagihan Margin</div>
                  <div class="text-h6 font-weight-bold">{{ formatCurrency(selectedItem.tagmgn) }}</div>
                  <div class="text-caption text-grey mt-3">Bayar Margin</div>
                  <div class="text-h6 font-weight-bold text-success">{{ formatCurrency(selectedItem.byrmgn) }}</div>
                  <div class="text-caption mt-3">
                    RR Margin:
                    <strong :class="getRRTextColor(safePct(selectedItem.pctmgn))">
                      {{ safePct(selectedItem.pctmgn).toFixed(2) }}%
                    </strong>
                  </div>
                </v-card>
              </v-col>
              <v-col cols="12">
                <v-card class="pa-4 rounded-lg bg-blue-lighten-5 border">
                  <div class="d-flex justify-space-between align-center mb-3">
                    <div>
                      <div class="text-caption text-grey">Total Tagihan</div>
                      <div class="text-h5 font-weight-bold">{{ formatCurrency(selectedItem.totaltag) }}</div>
                    </div>
                    <div class="text-right">
                      <div class="text-caption text-grey">Total Bayar</div>
                      <div class="text-h5 font-weight-bold text-success">{{ formatCurrency(selectedItem.totalbyr) }}</div>
                    </div>
                  </div>
                  <v-divider class="mb-3"></v-divider>
                  <div class="d-flex align-center justify-space-between">
                    <span class="text-h6 font-weight-bold">Repayment Rate Total:</span>
                    <v-chip
                      :color="getRRColor(safePct(selectedItem.pcttotal))"
                      size="large"
                      variant="flat"
                      class="font-weight-bold"
                    >
                      {{ safePct(selectedItem.pcttotal).toFixed(2) }}%
                    </v-chip>
                  </div>
                </v-card>
              </v-col>
            </v-row>
          </v-card-text>
        </v-card>
      </v-dialog>
    </div>
  </DefaultLayout>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { Head } from '@inertiajs/vue3'
import DefaultLayout from '@/Layouts/default.vue'
import axios from 'axios'
import '@/assets/css/financing-shared.css'

const loading = ref(false)
const exporting = ref(false)
const data = ref([])
const summary = ref({})
const detailDialog = ref(false)
const selectedItem = ref(null)

const filters = ref({
  ao: null,
  cabang: null,
  segmen: null,
  collectibility: null,
  search: ''
})

const kolOptions = [
  { title: 'Lancar (1)', value: '1' },
  { title: 'DPK (2)', value: '2' },
  { title: 'Kurang Lancar (3)', value: '3' },
  { title: 'Diragukan (4)', value: '4' },
  { title: 'Macet (5)', value: '5' }
]

// =========================================================================
// SAFE COMPUTED HELPERS
// =========================================================================

const aoList = computed(() => {
  if (!data.value || !Array.isArray(data.value) || data.value.length === 0) return []
  const aos = [...new Set(data.value.map(item => item.nama_ao).filter(Boolean))]
  return aos.sort()
})

const cabangList = computed(() => {
  if (!data.value || !Array.isArray(data.value) || data.value.length === 0) return []
  const cabangs = [...new Set(data.value.map(item => item.nama_cabang).filter(Boolean))]
  return cabangs.sort()
})

const segmenList = computed(() => {
  if (!data.value || !Array.isArray(data.value) || data.value.length === 0) return []
  const segmens = [...new Set(data.value.map(item => item.nama_segmen).filter(Boolean))]
  return segmens.sort()
})

// =========================================================================
// TABLE HEADERS
// =========================================================================

const headers = [
  { title: 'Nasabah / CIF', key: 'nama_nasabah', sortable: true, width: '200px' },
  { title: 'No Kontrak', key: 'nokontrak', sortable: true, width: '140px' },
  { title: 'Kol', key: 'colbaru', sortable: true, width: '100px' },
  { title: 'Tagihan', key: 'totaltag', sortable: true, align: 'end', width: '160px' },
  { title: 'Bayar', key: 'totalbyr', sortable: true, align: 'end', width: '160px' },
  { title: 'RR %', key: 'pcttotal', sortable: true, width: '160px' },
  { title: 'AO', key: 'nama_ao', sortable: true, width: '160px' },
  { title: '', key: 'actions', sortable: false, width: '60px' }
]

// =========================================================================
// FILTERED DATA (Client-side filtering)
// =========================================================================

const filteredData = computed(() => {
  if (!data.value || !Array.isArray(data.value) || data.value.length === 0) return []

  let result = [...data.value]

  // AO filter
  if (filters.value.ao) {
    result = result.filter(item => item.nama_ao === filters.value.ao)
  }

  // Cabang filter
  if (filters.value.cabang) {
    result = result.filter(item => item.nama_cabang === filters.value.cabang)
  }

  // Segmen filter
  if (filters.value.segmen) {
    result = result.filter(item => item.nama_segmen === filters.value.segmen)
  }

  // Collectibility filter
  if (filters.value.collectibility) {
    result = result.filter(item => String(item.colbaru) === String(filters.value.collectibility))
  }

  // Search filter
  if (filters.value.search) {
    const search = filters.value.search.toLowerCase()
    result = result.filter(item =>
      (item.nama_nasabah || '').toLowerCase().includes(search) ||
      (item.nocif || '').toLowerCase().includes(search) ||
      (item.nokontrak || '').toLowerCase().includes(search)
    )
  }

  return result
})

// =========================================================================
// API
// =========================================================================

const fetchData = async () => {
  loading.value = true
  try {
    const params = {
      ao: filters.value.ao,
      cabang: filters.value.cabang,
      segmen: filters.value.segmen,
      collectibility: filters.value.collectibility,
      search: filters.value.search
    }
    const response = await axios.get('/api/v1/financing/performance/repayment-rate', { params })
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

const showDetail = (item) => {
  selectedItem.value = item
  detailDialog.value = true
}

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

const formatCurrency = (value) => {
  const num = safeNum(value)
  if (num === 0) return 'Rp 0'
  return new Intl.NumberFormat('id-ID', {
    style: 'currency',
    currency: 'IDR',
    minimumFractionDigits: 0
  }).format(num)
}

// =========================================================================
// COLOR HELPERS
// =========================================================================

const getKolColor = (kol) => {
  const colors = { '1': 'success', '2': 'info', '3': 'warning', '4': 'orange-darken-2', '5': 'error' }
  return colors[String(kol)] || 'grey'
}

const getRRColor = (rate) => {
  if (rate >= 100) return 'success'
  if (rate >= 80) return 'info'
  if (rate >= 50) return 'warning'
  return 'error'
}

const getRRTextColor = (rate) => {
  if (rate >= 100) return 'text-success'
  if (rate >= 80) return 'text-info'
  if (rate >= 50) return 'text-warning'
  return 'text-error'
}

const exportExcel = async () => {
  exporting.value = true
  try {
    const params = new URLSearchParams({
      ao: filters.value.ao || '',
      cabang: filters.value.cabang || '',
      segmen: filters.value.segmen || '',
      collectibility: filters.value.collectibility || '',
      search: filters.value.search || ''
    })
    window.location.href = `/api/v1/financing/performance/repayment-rate/export?${params.toString()}`
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

:deep(.v-avatar.blue-lighten-5) {
  background-color: #eff6ff !important;
}
:deep(.v-avatar.indigo-lighten-5) {
  background-color: #eef2ff !important;
}
:deep(.v-avatar.green-lighten-5) {
  background-color: #ecfdf5 !important;
}
:deep(.v-avatar.amber-lighten-5) {
  background-color: #fffbeb !important;
}

/* Modal styling */
:deep(.v-dialog .v-card) {
  border-radius: 16px !important;
}
</style>
