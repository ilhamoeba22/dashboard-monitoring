<script setup>
import { ref, computed, onMounted, watch } from 'vue'
import { Head } from '@inertiajs/vue3'
import DefaultLayout from '@/layouts/default.vue'
import { storeToRefs } from 'pinia'
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
  total_volume: 0,
  count_topup: 0,
  count_ulangan: 0,
  count_retention: 0,
  count_naik: 0,
  count_turun: 0,
  count_tetap: 0,
})

// Filters
const selectedAo = ref('Semua AO Baru')
const selectedAnalisa = ref('Semua Tipe')
const searchQuery = ref('')

// Pagination
const currentPage = ref(1)
const itemsPerPage = ref(15)

// Options for dropdowns
const aoOptions = computed(() => {
  const aos = [...new Set(rawData.value.map(item => item.nama_ao_baru))]
  return ['Semua AO Baru', ...aos.sort()]
})

const analisaOptions = ['Semua Tipe', 'Top Up', 'Ulangan', 'Retention']

// Fetch Data
const fetchData = async () => {
  loading.value = true
  try {
    const response = await axios.get('/api/v1/financing/restrukturisasi/top-up')
    if (response.data.success) {
      rawData.value = response.data.data
      summary.value = response.data.summary
    }
  } catch (error) {
    console.error('Error fetching top-up data:', error)
  } finally {
    loading.value = false
  }
}

// Client-side Filtering
const filteredData = computed(() => {
  return rawData.value.filter(item => {
    const matchAo = selectedAo.value === 'Semua AO Baru' || item.nama_ao_baru === selectedAo.value
    const matchAnalisa = selectedAnalisa.value === 'Semua Tipe' || item.analisa_nasabah === selectedAnalisa.value
    const matchSearch = item.nama.toLowerCase().includes(searchQuery.value.toLowerCase()) || 
                        item.kontrak_baru.includes(searchQuery.value) ||
                        item.kontrak_lama.includes(searchQuery.value)
    
    return matchAo && matchAnalisa && matchSearch
  })
})

// Client-side Pagination
const totalPages = computed(() => Math.ceil(filteredData.value.length / itemsPerPage.value))
const paginatedData = computed(() => {
  const start = (currentPage.value - 1) * itemsPerPage.value
  return filteredData.value.slice(start, start + itemsPerPage.value)
})

// Chart Configuration (Analisa Nasabah)
const chartOptions = computed(() => ({
  chart: {
    type: 'donut',
    fontFamily: "'Plus Jakarta Sans', sans-serif",
    background: 'transparent',
    animations: { enabled: true, easing: 'easeinout', speed: 800 }
  },
  labels: ['Top Up (< 3 hr)', 'Ulangan (4-30 hr)', 'Retention (> 30 hr)'],
  colors: ['#10B981', '#F59E0B', '#3B82F6'], // Green, Amber, Blue
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
  summary.value.count_topup,
  summary.value.count_ulangan,
  summary.value.count_retention
])

// Utility Formatters
const formatRp = (value) => {
  return formatExactRupiah(value)
}

const getAnalisaColor = (type) => {
  const colors = {
    'Top Up': 'success',
    'Ulangan': 'warning',
    'Retention': 'info'
  }
  return colors[type] || 'grey'
}

const getLimitColor = (limit) => {
  const colors = {
    'Kenaikan': 'success',
    'Penurunan': 'error',
    'Tetap': 'grey'
  }
  return colors[limit] || 'grey'
}

const getLimitIcon = (limit) => {
  const icons = {
    'Kenaikan': 'ri-arrow-right-up-line',
    'Penurunan': 'ri-arrow-right-down-line',
    'Tetap': 'ri-arrow-right-line'
  }
  return icons[limit] || 'ri-checkbox-blank-circle-line'
}

onMounted(() => {
  fetchData()
})

const resetPage = () => { currentPage.value = 1 }

watch([selectedAo, selectedAnalisa, searchQuery], resetPage)
</script>

<template>
  <div class="fin-page px-4 pt-0">
    <Head title="Top-Up & Retention Pembiayaan" />

    <!-- â”€â”€ HERO HEADER â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€ -->
    <div class="fin-hero mb-6">
      <div class="fin-hero__deco"></div>
      <div class="fin-hero__inner">
        <div class="d-flex flex-column flex-md-row justify-space-between align-start align-md-center gap-4">
          <div class="d-flex align-center gap-4">
            <div class="fin-hero__icon fin-icon-blue">
              <v-icon icon="ri-add-circle-fill" size="26" color="white" />
            </div>
            <div class="fin-hero__meta">
              <h1 class="fin-hero__title">Top-Up & Retention</h1>
              <p class="fin-hero__subtitle">Pemantauan fasilitas penambahan plafon, retensi nasabah lunas, dan pembiayaan berulang.</p>
              <div class="fin-hero__badges">
                <span class="fin-badge fin-badge--warning">ðŸ”„ Repeat Order</span>
              </div>
            </div>
          </div>
          
          <div class="fin-filter-bar">
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
            <v-icon icon="ri-file-add-line" size="120" color="#10b981" />
          </div>
          <v-card-text class="pa-5" style="position: relative; z-index: 1;">
            <div class="d-flex justify-space-between align-start">
              <div>
                <p class="text-caption font-weight-bold text-uppercase tracking-widest mb-1" style="color: #64748B; font-family: 'Inter', sans-serif;">TOTAL TRANSAKSI</p>
                <h2 class="text-h4 font-weight-bold mb-2" style="color: #10b981; font-family: 'Plus Jakarta Sans', sans-serif; line-height: 1.2;">
                  <v-progress-circular v-if="loading" indeterminate size="24" width="3" color="emerald"></v-progress-circular>
                  <template v-else>{{ summary.total_kontrak }}</template>
                </h2>
                <p class="text-caption text-medium-emphasis mb-0" style="font-family: 'Inter', sans-serif; color: #10b981; font-weight: 600;">Bulan Berjalan CBS</p>
              </div>
            </div>
          </v-card-text>
        </v-card>
      </v-col>

      <v-col cols="12" md="4">
        <v-card class="rounded-xl border shadow-sm transition-swing h-100" elevation="0" style="position: relative; overflow: hidden;">
          <div style="position: absolute; top: -20px; right: -20px; width: 120px; height: 120px; opacity: 0.08;">
            <v-icon icon="ri-money-dollar-circle-line" size="120" color="#3b82f6" />
          </div>
          <v-card-text class="pa-5" style="position: relative; z-index: 1;">
            <div class="d-flex justify-space-between align-start">
              <div>
                <p class="text-caption font-weight-bold text-uppercase tracking-widest mb-1" style="color: #64748B; font-family: 'Inter', sans-serif;">TOTAL VOLUME PLAFON BARU</p>
                <h2 class="text-h4 font-weight-bold mb-2" style="color: #3b82f6; font-family: 'Plus Jakarta Sans', sans-serif; line-height: 1.2;">
                  <v-progress-circular v-if="loading" indeterminate size="24" width="3" color="blue"></v-progress-circular>
                  <template v-else>{{ formatRp(summary.total_volume) }}</template>
                </h2>
                <div class="d-flex align-center gap-2 mt-1">
                  <span class="text-xs font-bold text-emerald-600"><v-icon icon="ri-arrow-up-line" size="small"></v-icon> Naik: {{ summary.count_naik }}</span>
                  <span class="text-xs font-bold text-rose-500"><v-icon icon="ri-arrow-down-line" size="small"></v-icon> Turun: {{ summary.count_turun }}</span>
                </div>
              </div>
            </div>
          </v-card-text>
        </v-card>
      </v-col>

      <v-col cols="12" md="4">
        <v-card class="rounded-xl border shadow-sm transition-swing h-100" elevation="0" style="position: relative; overflow: hidden;">
          <div style="position: absolute; top: -20px; right: -20px; width: 120px; height: 120px; opacity: 0.08;">
            <v-icon icon="ri-pie-chart-2-line" size="120" color="#d97706" />
          </div>
          <v-card-text class="pa-5" style="position: relative; z-index: 1;">
            <div class="d-flex justify-space-between align-start">
              <div>
                <p class="text-caption font-weight-bold text-uppercase tracking-widest mb-1" style="color: #64748B; font-family: 'Inter', sans-serif;">TIPE NASABAH</p>
                <div class="d-flex flex-column gap-1 mt-2">
                  <div class="d-flex align-center gap-2">
                    <div class="w-3 h-3 rounded-full bg-emerald-500"></div>
                    <span class="text-xs font-bold text-slate-700">Top Up ({{ summary.count_topup }})</span>
                  </div>
                  <div class="d-flex align-center gap-2">
                    <div class="w-3 h-3 rounded-full bg-amber-500"></div>
                    <span class="text-xs font-bold text-slate-700">Ulangan ({{ summary.count_ulangan }})</span>
                  </div>
                  <div class="d-flex align-center gap-2">
                    <div class="w-3 h-3 rounded-full bg-blue-500"></div>
                    <span class="text-xs font-bold text-slate-700">Retention ({{ summary.count_retention }})</span>
                  </div>
                </div>
              </div>
              <div style="width: 70px; height: 70px;" class="relative">
                <VueApexCharts v-if="!loading" type="donut" width="100%" height="100%" :options="chartOptions" :series="chartSeries"></VueApexCharts>
                <div v-else class="absolute inset-0 flex items-center justify-center">
                  <v-progress-circular indeterminate color="indigo" size="20"></v-progress-circular>
                </div>
              </div>
            </div>
          </v-card-text>
        </v-card>
      </v-col>
    </v-row>

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
          v-model="selectedAnalisa"
          :items="analisaOptions"
          prepend-inner-icon="ri-pie-chart-2-line"
          variant="outlined"
          density="compact"
          hide-details
          rounded="lg"
          bg-color="white"
          class="flex-shrink-0 font-bold text-slate-700"
          style="min-width: 150px; max-width: 200px;"
        ></v-select>
    </v-card>

    <!-- Data Table Section (Enterprise Grid) -->
    <div class="content-card">
      <div class="content-card__body pa-0">
        <div class="overflow-x-auto">
          <table class="fin-table fin-vtable text-sm">
            <thead>
              <tr>
                <th class="sticky left-0 z-10 whitespace-nowrap">Transisi Fasilitas</th>
                <th class="whitespace-nowrap">Analisa Nasabah</th>
                <th class="text-right whitespace-nowrap">Plafon Lama â†’ Baru</th>
              </tr>
            </thead>
            <tbody>
              <tr v-if="loading">
                <td colspan="3" class="pa-12 text-center bg-slate-50">
                  <v-progress-circular indeterminate color="indigo" size="48" class="mb-4"></v-progress-circular>
                  <div class="text-h6 text-slate-500 font-weight-bold">Memuat Data Top-Up...</div>
                </td>
              </tr>
              <tr v-else-if="paginatedData.length === 0">
                <td colspan="3" class="pa-12 text-center bg-slate-50">
                  <v-icon icon="ri-inbox-line" size="64" class="text-slate-300 mb-4"></v-icon>
                  <div class="text-h6 text-slate-500 font-weight-bold">Data Tidak Ditemukan</div>
                  <div class="text-caption text-slate-400 mt-1 font-weight-medium">Coba sesuaikan filter pencarian Anda</div>
                </td>
              </tr>
              <tr v-for="(item, index) in paginatedData" :key="index">
                <td class="sticky left-0 z-10 bg-white min-w-[300px]" style="border-bottom: 1px solid #f1f5f9;">
                  <div class="font-black text-slate-800 text-sm mb-2 uppercase">{{ item.nama }}</div>
                  
                  <div class="flex items-center justify-between bg-slate-50 p-2 rounded-lg border border-slate-100">
                    <div class="flex-1">
                      <div class="text-[9px] font-black text-slate-400 uppercase mb-0.5">Fasilitas Lama</div>
                      <div class="font-mono text-[10px] text-slate-500 font-bold mb-0.5">{{ item.kontrak_lama }}</div>
                      <div class="text-[10px] text-slate-400 font-medium">{{ item.nama_ao_lama }}</div>
                    </div>
                    
                    <div class="px-2 text-slate-300">
                      <v-icon icon="ri-arrow-right-double-line" size="18"></v-icon>
                    </div>
                    
                    <div class="flex-1 text-right">
                      <div class="text-[9px] font-black text-indigo-400 uppercase mb-0.5">Fasilitas Baru</div>
                      <div class="font-mono text-[10px] text-indigo-600 font-bold mb-0.5">{{ item.kontrak_baru }}</div>
                      <div class="text-[10px] text-indigo-500 font-bold">
                        {{ item.nama_ao_baru }}
                        <span v-if="item.status_ao === 'Pindah AO'" class="text-amber-600 font-bold ml-1" title="Pindah AO">(Pindah)</span>
                      </div>
                    </div>
                  </div>
                </td>

                <td class="align-top" style="border-bottom: 1px solid #f1f5f9;">
                  <v-chip size="small" :color="getAnalisaColor(item.analisa_nasabah)" variant="tonal" class="font-weight-bold px-3 mb-2">
                    {{ item.analisa_nasabah }}
                  </v-chip>
                  <div class="text-xs text-slate-500 mt-1 font-weight-medium">
                    Selisih: <span class="font-black text-slate-700">{{ item.selisih_hari }} Hari</span>
                  </div>
                  <div class="text-[10px] text-slate-400 mt-0.5 font-weight-bold uppercase">
                    Lunas: {{ item.tgl_lunas }}<br>
                    Eff: {{ item.tgl_efektif_baru }}
                  </div>
                </td>

                <td class="align-top text-right" style="border-bottom: 1px solid #f1f5f9;">
                  <div class="font-black text-slate-800 text-lg mb-1">{{ formatRp(item.plafon_baru) }}</div>
                  <div class="text-xs text-slate-400 line-through mb-2 font-weight-medium">{{ formatRp(item.plafon_lama) }}</div>
                  
                  <v-chip size="x-small" :color="getLimitColor(item.analisa_limit)" variant="flat" class="px-2 font-weight-bold uppercase tracking-wider text-[9px]">
                    <v-icon :icon="getLimitIcon(item.analisa_limit)" size="small" class="mr-1"></v-icon>
                    {{ item.analisa_limit }}
                  </v-chip>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
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
