<script setup>
import { ref, computed, onMounted, watch } from 'vue'
import { Head } from '@inertiajs/vue3'
import DefaultLayout from '@/layouts/default.vue'
import axios from 'axios'
import VueApexCharts from 'vue3-apexcharts'
import '@/assets/css/financing-shared.css'
import { formatExactRupiah } from '@/utils/money'

defineOptions({ layout: DefaultLayout })

// State Management
const loading = ref(true)
const rawData = ref([])
const summary = ref({
  total_kontrak: 0,
  total_nasabah: 0,
  avg_ke: 0,
  kol_membaik: 0,
  kol_memburuk: 0,
  kol_tetap: 0,
})
const periodMeta = ref({
  requested_period: null,
  active_period: null,
  is_historical: false,
  period_available: true,
  source_table: 'TOFLMBHP',
  source_database: null,
  message: null,
})

// Filters
const selectedAo = ref('Semua AO')
const selectedCabang = ref('Semua Cabang')
const searchQuery = ref('')
const selectedTahun = ref(null)
const selectedBulan = ref(null)
const monthOptions = [
  { title: 'Januari', value: 1 }, { title: 'Februari', value: 2 }, { title: 'Maret', value: 3 },
  { title: 'April', value: 4 }, { title: 'Mei', value: 5 }, { title: 'Juni', value: 6 },
  { title: 'Juli', value: 7 }, { title: 'Agustus', value: 8 }, { title: 'September', value: 9 },
  { title: 'Oktober', value: 10 }, { title: 'November', value: 11 }, { title: 'Desember', value: 12 },
]
const yearOptions = computed(() => {
  const current = new Date().getFullYear()
  return [current, current - 1, current - 2, current - 3, current - 4]
})
const activePeriodLabel = computed(() => {
  if (!selectedTahun.value || !selectedBulan.value) return 'Periode aktif CBS'
  const month = monthOptions.find(item => item.value === selectedBulan.value)?.title || '-'
  return `${month} ${selectedTahun.value}`
})
const periodUnavailable = computed(() => periodMeta.value?.period_available === false)

// Pagination
const currentPage = ref(1)
const itemsPerPage = ref(15)

// Options for dropdowns
const aoOptions = computed(() => {
  const aos = [...new Set(rawData.value.map(item => item.nama_ao))]
  return ['Semua AO', ...aos.sort()]
})

const cabangOptions = computed(() => {
  const cabangs = [...new Set(rawData.value.map(item => item.kantor_pelayanan))]
  return ['Semua Cabang', ...cabangs.sort()]
})

// Fetch Data
const fetchData = async () => {
  loading.value = true
  try {
    const params = {}
    if (selectedTahun.value) params.tahun = selectedTahun.value
    if (selectedBulan.value) params.bulan = selectedBulan.value
    const response = await axios.get('/api/v1/financing/restrukturisasi', { params })
    if (response.data.success) {
      periodMeta.value = response.data.period_meta || periodMeta.value
      const requested = String(periodMeta.value?.requested_period || '')
      if (requested.length === 6) {
        selectedTahun.value = Number(requested.slice(0, 4))
        selectedBulan.value = Number(requested.slice(4, 6))
      }
      rawData.value = response.data.data
      summary.value = response.data.summary
    }
  } catch (error) {
    console.error('Error fetching restrukturisasi data:', error)
  } finally {
    loading.value = false
  }
}

// Client-side Filtering
const filteredData = computed(() => {
  return rawData.value.filter(item => {
    const matchAo = selectedAo.value === 'Semua AO' || item.nama_ao === selectedAo.value
    const matchCabang = selectedCabang.value === 'Semua Cabang' || item.kantor_pelayanan === selectedCabang.value
    const matchSearch = item.nama.toLowerCase().includes(searchQuery.value.toLowerCase()) || 
                        item.nokontrak.includes(searchQuery.value)
    
    return matchAo && matchCabang && matchSearch
  })
})

// Client-side Pagination
const totalPages = computed(() => Math.ceil(filteredData.value.length / itemsPerPage.value))
const paginatedData = computed(() => {
  const start = (currentPage.value - 1) * itemsPerPage.value
  return filteredData.value.slice(start, start + itemsPerPage.value)
})

// Chart Configuration
const chartOptions = computed(() => ({
  chart: {
    type: 'donut',
    fontFamily: "'Plus Jakarta Sans', sans-serif",
    background: 'transparent',
    animations: { enabled: true, easing: 'easeinout', speed: 800 }
  },
  labels: ['Membaik', 'Memburuk', 'Tetap'],
  colors: ['#10B981', '#EF4444', '#F59E0B'],
  dataLabels: { enabled: false },
  legend: { show: false },
  plotOptions: {
    pie: {
      donut: {
        size: '80%',
        labels: {
          show: true,
          name: { show: false },
          value: {
            show: true,
            fontSize: '28px',
            fontWeight: 800,
            color: '#1E293B',
            formatter: (val) => val
          },
          total: {
            show: true,
            showAlways: true,
            label: 'Total',
            fontSize: '12px',
            color: '#64748B',
            formatter: (w) => summary.value.total_kontrak
          }
        }
      }
    }
  },
  stroke: { width: 0 },
  tooltip: {
    theme: 'light',
    y: { formatter: (val) => `${val} Kontrak` }
  }
}))

const chartSeries = computed(() => [
  summary.value.kol_membaik,
  summary.value.kol_memburuk,
  summary.value.kol_tetap
])

// Utility Formatters
const formatRp = (value) => {
  return formatExactRupiah(value)
}

const getKolStyle = (kol) => {
  const colors = {
    '1': { bg: '#dcfce7', text: '#15803d', border: '#bbf7d0' },
    '2': { bg: '#dbeafe', text: '#1d4ed8', border: '#bfdbfe' },
    '3': { bg: '#fef3c7', text: '#b45309', border: '#fde68a' },
    '4': { bg: '#ffedd5', text: '#c2410c', border: '#fed7aa' },
    '5': { bg: '#ffe4e6', text: '#be123c', border: '#fecdd3' }
  }
  return colors[kol] || { bg: '#f1f5f9', text: '#475569', border: '#e2e8f0' }
}

onMounted(() => {
  fetchData()
})

const resetPage = () => { currentPage.value = 1 }

watch([selectedAo, selectedCabang, searchQuery], resetPage)
watch([selectedTahun, selectedBulan], () => {
  resetPage()
  fetchData()
})
</script>

<template>
  <div class="fin-page px-4 pt-0">
    <Head title="Restrukturisasi Pembiayaan" />

    <!-- ── HERO HEADER ─────────────────────────────────────────── -->
    <div class="fin-hero mb-6">
      <div class="fin-hero__deco"></div>
      <div class="fin-hero__inner">
        <div class="d-flex flex-column flex-md-row justify-space-between align-start align-md-center gap-4">
          <div class="d-flex align-center gap-4">
            <div class="fin-hero__icon fin-icon-blue">
              <v-icon icon="ri-refresh-line" size="26" color="white" />
            </div>
            <div class="fin-hero__meta">
              <h1 class="fin-hero__title">Restrukturisasi Pembiayaan</h1>
              <p class="fin-hero__subtitle">Intelligence Control Center untuk pemantauan data addendum dan perubahan syarat kontrak.</p>
              <div class="fin-hero__badges">
                <span class="fin-badge fin-badge--warning">📄 Addendum</span>
              </div>
            </div>
          </div>
          
          <div class="fin-filter-bar">
            <v-select
              v-model="selectedTahun"
              :items="yearOptions"
              label="Tahun"
              variant="solo"
              density="compact"
              flat
              hide-details
              rounded="lg"
              bg-color="white"
              prepend-inner-icon="ri-calendar-line"
              style="min-width: 120px; max-width: 140px;"
            />
            <v-select
              v-model="selectedBulan"
              :items="monthOptions"
              item-title="title"
              item-value="value"
              label="Bulan"
              variant="solo"
              density="compact"
              flat
              hide-details
              rounded="lg"
              bg-color="white"
              prepend-inner-icon="ri-calendar-event-line"
              style="min-width: 150px; max-width: 180px;"
            />
            <v-btn 
              variant="text" 
              density="comfortable"
              @click="fetchData" 
              :loading="loading"
              icon="ri-refresh-line"
              color="white"
            ></v-btn>
          </div>
        </div>
      </div>
    </div>

    <!-- Executive Scorecards (Pro Max) -->
    <v-row class="mb-6">
      <v-col cols="12" md="4">
        <v-card class="rounded-xl border shadow-sm transition-swing h-100" elevation="0" style="position: relative; overflow: hidden;">
          <div style="position: absolute; top: -20px; right: -20px; width: 120px; height: 120px; opacity: 0.08;">
            <v-icon icon="ri-file-list-3-line" size="120" color="#3b82f6" />
          </div>
          <v-card-text class="pa-5" style="position: relative; z-index: 1;">
            <div class="d-flex justify-space-between align-start">
              <div>
                <p class="text-caption font-weight-bold text-uppercase tracking-widest mb-1" style="color: #64748B; font-family: 'Inter', sans-serif;">TOTAL RESTRUKTURISASI</p>
                <h2 class="text-h4 font-weight-bold mb-2" style="color: #3b82f6; font-family: 'Plus Jakarta Sans', sans-serif; line-height: 1.2;">
                  <v-progress-circular v-if="loading" indeterminate size="24" width="3" color="blue"></v-progress-circular>
                  <template v-else>{{ summary.total_kontrak }}</template>
                </h2>
                <p class="text-caption text-medium-emphasis mb-0" style="font-family: 'Inter', sans-serif;">Dari {{ summary.total_nasabah }} nasabah unik</p>
              </div>
            </div>
          </v-card-text>
        </v-card>
      </v-col>

      <v-col cols="12" md="4">
        <v-card class="rounded-xl border shadow-sm transition-swing h-100" elevation="0" style="position: relative; overflow: hidden;">
          <div style="position: absolute; top: -20px; right: -20px; width: 120px; height: 120px; opacity: 0.08;">
            <v-icon icon="ri-repeat-2-line" size="120" color="#d97706" />
          </div>
          <v-card-text class="pa-5" style="position: relative; z-index: 1;">
            <div class="d-flex justify-space-between align-start">
              <div>
                <p class="text-caption font-weight-bold text-uppercase tracking-widest mb-1" style="color: #64748B; font-family: 'Inter', sans-serif;">AVG. RESTRUKTURISASI KE-</p>
                <h2 class="text-h4 font-weight-bold mb-2" style="color: #d97706; font-family: 'Plus Jakarta Sans', sans-serif; line-height: 1.2;">
                  <v-progress-circular v-if="loading" indeterminate size="24" width="3" color="amber"></v-progress-circular>
                  <template v-else>{{ summary.avg_ke }}x</template>
                </h2>
                <p class="text-caption text-medium-emphasis mb-0" style="font-family: 'Inter', sans-serif;">Rata-rata frekuensi perpanjangan</p>
              </div>
            </div>
          </v-card-text>
        </v-card>
      </v-col>

      <v-col cols="12" md="4">
        <v-card class="rounded-xl border shadow-sm transition-swing h-100" elevation="0" style="position: relative; overflow: hidden;">
          <div style="position: absolute; top: -20px; right: -20px; width: 120px; height: 120px; opacity: 0.08;">
            <v-icon icon="ri-pie-chart-2-line" size="120" color="#059669" />
          </div>
          <v-card-text class="pa-5" style="position: relative; z-index: 1;">
            <div class="d-flex flex-column">
              <p class="text-caption font-weight-bold text-uppercase tracking-widest mb-3" style="color: #64748B; font-family: 'Inter', sans-serif;">PERUBAHAN KOLEKTIBILITAS</p>
              <div class="d-flex align-center justify-center gap-4 flex-grow-1">
                <div style="width: 80px; height: 80px;">
                  <VueApexCharts v-if="!loading" type="donut" width="100%" height="100%" :options="chartOptions" :series="chartSeries"></VueApexCharts>
                  <div v-else class="d-flex align-center justify-center h-100">
                    <v-progress-circular indeterminate color="indigo" size="24"></v-progress-circular>
                  </div>
                </div>
                <div class="d-flex flex-column gap-2">
                  <div class="d-flex align-center gap-2">
                    <div class="w-3 h-3 rounded-full" style="background: #10B981; flex-shrink: 0;"></div>
                    <div>
                      <div class="text-xs font-weight-bold" style="color: #1e293b;">Membaik</div>
                      <div class="text-sm font-weight-bold" style="color: #10B981;">{{ summary.kol_membaik }}</div>
                    </div>
                  </div>
                  <div class="d-flex align-center gap-2">
                    <div class="w-3 h-3 rounded-full" style="background: #EF4444; flex-shrink: 0;"></div>
                    <div>
                      <div class="text-xs font-weight-bold" style="color: #1e293b;">Memburuk</div>
                      <div class="text-sm font-weight-bold" style="color: #EF4444;">{{ summary.kol_memburuk }}</div>
                    </div>
                  </div>
                  <div class="d-flex align-center gap-2">
                    <div class="w-3 h-3 rounded-full" style="background: #F59E0B; flex-shrink: 0;"></div>
                    <div>
                      <div class="text-xs font-weight-bold" style="color: #1e293b;">Tetap</div>
                      <div class="text-sm font-weight-bold" style="color: #F59E0B;">{{ summary.kol_tetap }}</div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </v-card-text>
        </v-card>
      </v-col>
    </v-row>

    <v-alert
      v-if="periodUnavailable && !loading"
      type="warning"
      variant="tonal"
      border="start"
      rounded="lg"
      class="mb-6"
    >
      <div class="font-weight-bold mb-1">Periode {{ activePeriodLabel }} belum tersedia</div>
      <div class="text-body-2">{{ periodMeta.message || 'Database snapshot periode ini belum tersedia.' }}</div>
    </v-alert>

    <!-- Independent Filter Bar (Pro Max) -->
    <v-card class="d-flex flex-wrap align-center ga-3 pa-4 bg-white rounded-xl border shadow-sm mb-6" elevation="0">
        <div class="text-sm font-black text-slate-800 uppercase tracking-tight mr-2 flex items-center gap-2">
          <v-icon icon="ri-filter-3-line" color="slate-400"></v-icon>
          Filter Data
        </div>
        <v-text-field
          v-model="searchQuery"
          prepend-inner-icon="ri-search-2-line"
          placeholder="Cari Nasabah / No Kontrak..."
          variant="outlined"
          density="compact"
          hide-details
          rounded="lg"
          bg-color="white"
          class="flex-shrink-0 font-bold text-slate-700"
          style="min-width: 250px; max-width: 300px;"
        ></v-text-field>
        <v-select
          v-model="selectedAo"
          :items="aoOptions"
          prepend-inner-icon="ri-user-star-line"
          variant="outlined"
          density="compact"
          hide-details
          rounded="lg"
          bg-color="white"
          class="flex-shrink-0 font-bold text-slate-700"
          style="min-width: 200px; max-width: 250px;"
        ></v-select>
        <v-select
          v-model="selectedCabang"
          :items="cabangOptions"
          prepend-inner-icon="ri-store-2-line"
          variant="outlined"
          density="compact"
          hide-details
          rounded="lg"
          bg-color="white"
          class="flex-shrink-0 font-bold text-slate-700"
          style="min-width: 200px; max-width: 250px;"
        ></v-select>
    </v-card>

    <!-- Data Table Section (Enterprise Grid) -->
    <div class="content-card">
      <div class="content-card__body pa-0">
        <div class="overflow-x-auto">
          <table class="fin-table fin-vtable text-sm">
            <thead>
              <tr>
                <th class="sticky left-0 z-10 whitespace-nowrap">Nasabah / Kontrak</th>
                <th class="whitespace-nowrap">Restruk Ke</th>
                <th class="whitespace-nowrap text-center">Total Restruk</th>
                <th class="whitespace-nowrap">Akad (Lama → Baru)</th>
                <th class="whitespace-nowrap text-center">Kolektibilitas</th>
                <th class="text-right whitespace-nowrap">O/S Pokok Baru</th>
              </tr>
            </thead>
          <tbody>
            <tr v-if="loading">
              <td colspan="6" class="pa-12 text-center bg-slate-50">
                <v-progress-circular indeterminate color="indigo" size="48" class="mb-4"></v-progress-circular>
                <div class="text-h6 text-slate-500 font-weight-bold">Memuat Data Restrukturisasi...</div>
              </td>
            </tr>
            <tr v-else-if="paginatedData.length === 0">
              <td colspan="6" class="pa-12 text-center bg-slate-50">
                <v-icon icon="ri-inbox-line" size="64" class="text-slate-300 mb-4"></v-icon>
                <div class="text-h6 text-slate-500 font-weight-bold">Data Tidak Ditemukan</div>
                <div class="text-caption text-slate-400 mt-1 font-weight-medium">Coba sesuaikan filter pencarian Anda</div>
              </td>
            </tr>
            <tr v-for="(item, index) in paginatedData" :key="index">
              <td class="sticky left-0 z-10 bg-white" style="border-bottom: 1px solid #f1f5f9;">
                <div class="font-black text-slate-800 text-sm mb-0.5 uppercase whitespace-nowrap">{{ item.nama }}</div>
                <div class="font-mono text-[10px] text-slate-500 font-bold tracking-tight mb-1">
                  {{ item.nokontrak }}
                </div>
                <div class="text-xs text-slate-500 font-medium flex items-center gap-1">
                  <v-icon icon="ri-user-star-line" size="12"></v-icon>
                  {{ item.nama_ao }}
                </div>
              </td>

              <td style="border-bottom: 1px solid #f1f5f9;">
                <div class="mb-1">
                  <v-chip size="small" variant="tonal" class="font-weight-bold px-3" :color="item.ke > 2 ? 'warning' : 'primary'">
                    Addendum Ke-{{ item.ke }}
                  </v-chip>
                </div>
                <div class="text-[10px] text-slate-500 font-bold uppercase">
                  Tgl: {{ item.tglakad_baru }}
                </div>
              </td>

              <td class="text-center" style="border-bottom: 1px solid #f1f5f9;">
                <v-chip
                  size="small"
                  variant="flat"
                  class="font-weight-black px-3"
                  :color="item.total_restrukturisasi >= 3 ? 'error' : (item.total_restrukturisasi >= 2 ? 'warning' : 'success')"
                >
                  {{ item.total_restrukturisasi }}x
                </v-chip>
              </td>

              <td style="border-bottom: 1px solid #f1f5f9;">
                <div class="flex flex-col gap-2">
                  <div class="flex items-center gap-2">
                    <span class="inline-block px-2 py-0.5 text-[9px] font-black bg-slate-100 text-slate-500 rounded uppercase">Lama</span>
                    <span class="text-xs text-slate-500 truncate max-w-[200px] font-weight-medium" :title="item.akad_lama">{{ item.akad_lama }}</span>
                  </div>
                  <div class="flex items-center gap-2">
                    <span class="inline-block px-2 py-0.5 text-[9px] font-black bg-emerald-100 text-emerald-700 rounded uppercase">Baru</span>
                    <span class="text-xs text-slate-800 font-bold truncate max-w-[200px]" :title="item.akad_baru">{{ item.akad_baru }}</span>
                  </div>
                </div>
              </td>

              <td style="border-bottom: 1px solid #f1f5f9;" class="text-center">
                <div class="flex items-center justify-center gap-2 mb-1">
                  <v-chip size="x-small" variant="flat" :color="item.col_sblm_rest > item.col_stlh_rest ? 'success' : (item.col_sblm_rest < item.col_stlh_rest ? 'error' : 'warning')" class="font-weight-bold px-2 rounded-lg">
                    {{ item.col_sblm_rest }}
                  </v-chip>
                  <v-icon icon="ri-arrow-right-line" color="slate-300" size="14"></v-icon>
                  <v-chip size="x-small" variant="flat" :color="item.col_stlh_rest < item.col_sblm_rest ? 'success' : (item.col_stlh_rest > item.col_sblm_rest ? 'error' : 'warning')" class="font-weight-bold px-2 rounded-lg">
                    {{ item.col_stlh_rest }}
                  </v-chip>
                  
                  <v-icon v-if="item.col_stlh_rest < item.col_sblm_rest" icon="ri-arrow-up-circle-fill" color="success" size="16" title="Membaik"></v-icon>
                  <v-icon v-else-if="item.col_stlh_rest > item.col_sblm_rest" icon="ri-arrow-down-circle-fill" color="error" size="16" title="Memburuk"></v-icon>
                  <v-icon v-else icon="ri-subtract-line" color="warning" size="16" title="Tetap"></v-icon>
                </div>
                <div class="text-[10px] text-slate-500 font-bold uppercase">
                  Saat ini: <span class="text-slate-800">Kol {{ item.col_berjalan }}</span>
                </div>
              </td>

              <td class="text-right" style="border-bottom: 1px solid #f1f5f9;">
                <div class="font-black text-slate-800 text-sm">{{ formatRp(item.osmdl_baru) }}</div>
                <div class="text-[10px] text-slate-500 font-bold mt-1">Plafon: {{ formatRp(item.mdlawal) }}</div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Pagination Footer -->
      <div class="bg-white border-t border-slate-100 px-6 py-4 flex items-center justify-between">
        <div class="text-xs font-bold text-slate-500 uppercase tracking-widest">
          Menampilkan <span class="text-slate-800 font-black">{{ filteredData.length > 0 ? ((currentPage - 1) * itemsPerPage) + 1 : 0 }} - {{ Math.min(currentPage * itemsPerPage, filteredData.length) }}</span>
          dari <span class="text-slate-800 font-black">{{ filteredData.length }}</span> Entri
        </div>
        <v-pagination
          v-if="totalPages > 1"
          v-model="currentPage"
          :length="totalPages"
          :total-visible="5"
          density="comfortable"
          active-color="primary"
          variant="flat"
          class="pagination-professional"
        ></v-pagination>
      </div>
    </div>
  </div>
  </div>
</template>

<style scoped>
.pagination-professional :deep(.v-pagination__item) {
  border-radius: 12px;
  font-weight: 800;
  font-size: 12px;
}
.pagination-professional :deep(.v-pagination__item--active) {
  box-shadow: 0 4px 12px rgba(var(--v-theme-indigo), 0.3);
}

:deep(.v-table) {
  background: transparent !important;
}
:deep(.v-table th) {
  border-bottom: 2px solid #f1f5f9 !important;
}
:deep(.v-table td) {
  height: 64px !important;
}
</style>
