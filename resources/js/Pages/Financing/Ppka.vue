<script setup>
import { ref, computed, onMounted, watch } from 'vue'
import { Head } from '@inertiajs/vue3'
import DefaultLayout from '@/layouts/default.vue'
import axios from 'axios'
import VueApexCharts from 'vue3-apexcharts'
import '@/assets/css/financing-shared.css'
import { formatExactRupiah } from '@/utils/money'

defineOptions({ layout: DefaultLayout })

const loading = ref(true)
const rawData = ref([])
const summary = ref({
  total_ppap: 0,
  kol1_ppap: 0,
  kol2_ppap: 0,
  kol3_ppap: 0,
  kol4_ppap: 0,
  kol5_ppap: 0,
  total_kontrak: 0,
})

const selectedAo = ref('Semua AO')
const searchQuery = ref('')
const currentPage = ref(1)
const itemsPerPage = ref(15)

// Dialog State
const showDialog = ref(false)
const savingAdjustment = ref(false)
const adjustmentForm = ref({
  nokontrak: '',
  nominal_ppap: '',
  alasan: ''
})

const aoOptions = computed(() => {
  const aos = [...new Set(rawData.value.map(item => item.nmao))]
  return ['Semua AO', ...aos.sort()]
})

const manualAdjustmentEnabled = ref(false)

const fetchSettings = async () => {
  try {
    const response = await axios.get('/api/v1/admin/settings')
    if (response.data.success && response.data.data) {
      const enabled = response.data.data.ppka_manual_enabled
      manualAdjustmentEnabled.value = enabled === 'true' || enabled === true
    }
  } catch (error) {
    console.error('Failed to fetch settings:', error)
  }
}

const fetchData = async () => {
  loading.value = true
  try {
    const response = await axios.get('/api/v1/financing/penyelesaian/ppka')
    if (response.data.success) {
      rawData.value = response.data.data
      summary.value = response.data.summary
    }
  } catch (error) {
    console.error('Error fetching PPKA data:', error)
  } finally {
    loading.value = false
  }
}

const submitAdjustment = async () => {
  if (!adjustmentForm.value.nokontrak || !adjustmentForm.value.nominal_ppap) return
  
  savingAdjustment.value = true
  try {
    const response = await axios.post('/api/v1/financing/penyelesaian/ppka-adjustment', {
      nokontrak: adjustmentForm.value.nokontrak,
      nominal_ppap: adjustmentForm.value.nominal_ppap,
      alasan: adjustmentForm.value.alasan
    })
    
    if (response.data.success) {
      showDialog.value = false
      adjustmentForm.value = { nokontrak: '', nominal_ppap: '', alasan: '' }
      fetchData() // Refresh data
    }
  } catch (error) {
    console.error('Error saving adjustment:', error)
  } finally {
    savingAdjustment.value = false
  }
}

const openAdjustmentDialog = (item = null) => {
  if (item) {
    adjustmentForm.value.nokontrak = item.nokontrak
    adjustmentForm.value.nominal_ppap = item.ppap_manual
  } else {
    adjustmentForm.value = { nokontrak: '', nominal_ppap: '', alasan: '' }
  }
  showDialog.value = true
}

const filteredData = computed(() => {
  return rawData.value.filter(item => {
    const matchAo = selectedAo.value === 'Semua AO' || item.nmao === selectedAo.value
    const matchSearch = item.nama.toLowerCase().includes(searchQuery.value.toLowerCase()) || 
                        item.nokontrak.includes(searchQuery.value)
    return matchAo && matchSearch
  })
})

const totalPages = computed(() => Math.ceil(filteredData.value.length / itemsPerPage.value))
const paginatedData = computed(() => {
  const start = (currentPage.value - 1) * itemsPerPage.value
  return filteredData.value.slice(start, start + itemsPerPage.value)
})

const chartOptions = computed(() => ({
  chart: { 
    type: 'donut', 
    fontFamily: "'Plus Jakarta Sans', sans-serif", 
    background: 'transparent',
    animations: { enabled: true, easing: 'easeinout', speed: 800 }
  },
  labels: ['Kol 1 (0.5%)', 'Kol 2 (3%)', 'Kol 3 (10%)', 'Kol 4 (50%)', 'Kol 5 (100%)'],
  colors: ['#10B981', '#3B82F6', '#F59E0B', '#EF4444', '#991B1B'],
  dataLabels: { enabled: false },
  legend: { position: 'right', fontSize: '12px', fontWeight: 600, markers: { radius: 12 } },
  plotOptions: {
    pie: {
      donut: {
        size: '80%',
        labels: {
          show: true,
          name: { show: true, fontSize: '12px', color: '#64748B', fontWeight: 600 },
          value: { 
            show: true, 
            fontSize: '28px', 
            fontWeight: 800, 
            color: '#1E293B',
            formatter: (val) => formatExactRupiah(val)
          },
          total: { 
            show: true, 
            showAlways: true, 
            label: 'Total PPAP', 
            fontSize: '12px', 
            color: '#64748B', 
            formatter: () => formatExactRupiah(summary.value.total_ppap)
          }
        }
      }
    }
  },
  stroke: { width: 0 },
  tooltip: { 
    theme: 'light', 
    y: { formatter: (val) => formatExactRupiah(val) } 
  }
}))

const chartSeries = computed(() => [
  summary.value.kol1_ppap,
  summary.value.kol2_ppap,
  summary.value.kol3_ppap,
  summary.value.kol4_ppap,
  summary.value.kol5_ppap
])

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

const resetPage = () => { currentPage.value = 1 }

watch([selectedAo, searchQuery], resetPage)

onMounted(() => { 
  fetchSettings()
  fetchData() 
})
</script>

<template>
  <div class="fin-page px-4 pt-0">
    <Head title="PPKA - Cadangan Kerugian" />

    <!-- ── HERO HEADER ─────────────────────────────────────────── -->
    <div class="fin-hero mb-6">
      <div class="fin-hero__deco"></div>
      <div class="fin-hero__inner">
        <div class="d-flex flex-column flex-md-row justify-space-between align-start align-md-center gap-4">
          <div class="d-flex align-center gap-4">
            <div class="fin-hero__icon fin-icon-amber">
              <v-icon icon="ri-shield-check-fill" size="26" color="white" />
            </div>
            <div class="fin-hero__meta">
              <h1 class="fin-hero__title">PPKA - Cadangan Kerugian</h1>
              <p class="fin-hero__subtitle">Penyisihan Penghapusan Piutang Aktiva berdasarkan kolektibilitas OJK.</p>
              <div class="fin-hero__badges">
                <span class="fin-badge fin-badge--warning">🛡️ Risk</span>
              </div>
            </div>
          </div>
          
          <div class="fin-filter-bar">
            <v-btn v-if="manualAdjustmentEnabled" variant="tonal" class="mr-3 text-amber" color="amber-lighten-4" @click="openAdjustmentDialog()">
              <v-icon icon="ri-edit-box-line" class="mr-2"></v-icon> Penyesuaian Manual
            </v-btn>
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
      <v-col cols="12" sm="6" lg="3">
        <v-card class="rounded-xl border shadow-sm transition-swing h-100" elevation="0" style="position: relative; overflow: hidden;">
          <div style="position: absolute; top: -20px; right: -20px; width: 120px; height: 120px; opacity: 0.08;">
            <v-icon icon="ri-funds-line" size="120" color="#3b82f6" />
          </div>
          <v-card-text class="pa-5" style="position: relative; z-index: 1;">
            <div class="d-flex justify-space-between align-start">
              <div>
                <p class="text-caption font-weight-bold text-uppercase tracking-widest mb-1" style="color: #64748B; font-family: 'Inter', sans-serif;">TOTAL PPAP</p>
                <h2 class="text-h4 font-weight-bold mb-2" style="color: #3b82f6; font-family: 'Plus Jakarta Sans', sans-serif; line-height: 1.2;">
                  <v-progress-circular v-if="loading" indeterminate size="24" width="3" color="blue"></v-progress-circular>
                  <template v-else>{{ formatRp(summary.total_ppap) }}</template>
                </h2>
                <p class="text-caption text-medium-emphasis mb-0" style="font-family: 'Inter', sans-serif;">{{ summary.total_kontrak }} Kontrak Aktif</p>
              </div>
            </div>
          </v-card-text>
        </v-card>
      </v-col>

      <v-col cols="12" sm="6" lg="3">
        <v-card class="rounded-xl border shadow-sm transition-swing h-100" elevation="0" style="position: relative; overflow: hidden;">
          <div style="position: absolute; top: -20px; right: -20px; width: 120px; height: 120px; opacity: 0.08;">
            <v-icon icon="ri-shield-star-line" size="120" color="#059669" />
          </div>
          <v-card-text class="pa-5" style="position: relative; z-index: 1;">
            <div class="d-flex justify-space-between align-start">
              <div>
                <p class="text-caption font-weight-bold text-uppercase tracking-widest mb-1" style="color: #64748B; font-family: 'Inter', sans-serif;">KOL 1 & 2 (SAFE)</p>
                <h2 class="text-h4 font-weight-bold mb-2" style="color: #059669; font-family: 'Plus Jakarta Sans', sans-serif; line-height: 1.2;">
                  <v-progress-circular v-if="loading" indeterminate size="24" width="3" color="emerald"></v-progress-circular>
                  <template v-else>{{ formatRp(summary.kol1_ppap + summary.kol2_ppap) }}</template>
                </h2>
                <p class="text-caption text-medium-emphasis mb-0" style="font-family: 'Inter', sans-serif; color: #059669; font-weight: 600;">Lancar & DPK (0.5% - 3%)</p>
              </div>
            </div>
          </v-card-text>
        </v-card>
      </v-col>

      <v-col cols="12" sm="6" lg="3">
        <v-card class="rounded-xl border shadow-sm transition-swing h-100" elevation="0" style="position: relative; overflow: hidden;">
          <div style="position: absolute; top: -20px; right: -20px; width: 120px; height: 120px; opacity: 0.08;">
            <v-icon icon="ri-error-warning-line" size="120" color="#d97706" />
          </div>
          <v-card-text class="pa-5" style="position: relative; z-index: 1;">
            <div class="d-flex justify-space-between align-start">
              <div>
                <p class="text-caption font-weight-bold text-uppercase tracking-widest mb-1" style="color: #64748B; font-family: 'Inter', sans-serif;">KOL 3 (WARNING)</p>
                <h2 class="text-h4 font-weight-bold mb-2" style="color: #d97706; font-family: 'Plus Jakarta Sans', sans-serif; line-height: 1.2;">
                  <v-progress-circular v-if="loading" indeterminate size="24" width="3" color="amber"></v-progress-circular>
                  <template v-else>{{ formatRp(summary.kol3_ppap) }}</template>
                </h2>
                <p class="text-caption text-medium-emphasis mb-0" style="font-family: 'Inter', sans-serif; color: #d97706; font-weight: 600;">Kurang Lancar (10%)</p>
              </div>
            </div>
          </v-card-text>
        </v-card>
      </v-col>

      <v-col cols="12" sm="6" lg="3">
        <v-card class="rounded-xl border shadow-sm transition-swing h-100" elevation="0" style="position: relative; overflow: hidden;">
          <div style="position: absolute; top: -20px; right: -20px; width: 120px; height: 120px; opacity: 0.08;">
            <v-icon icon="ri-alarm-warning-fill" size="120" color="#e11d48" />
          </div>
          <v-card-text class="pa-5" style="position: relative; z-index: 1;">
            <div class="d-flex justify-space-between align-start">
              <div>
                <p class="text-caption font-weight-bold text-uppercase tracking-widest mb-1" style="color: #64748B; font-family: 'Inter', sans-serif;">KOL 4 & 5 (DANGER)</p>
                <h2 class="text-h4 font-weight-bold mb-2" style="color: #e11d48; font-family: 'Plus Jakarta Sans', sans-serif; line-height: 1.2;">
                  <v-progress-circular v-if="loading" indeterminate size="24" width="3" color="rose"></v-progress-circular>
                  <template v-else>{{ formatRp(summary.kol4_ppap + summary.kol5_ppap) }}</template>
                </h2>
                <p class="text-caption text-medium-emphasis mb-0" style="font-family: 'Inter', sans-serif; color: #e11d48; font-weight: 600;">Diragukan & Macet (50-100%)</p>
              </div>
            </div>
          </v-card-text>
        </v-card>
      </v-col>
    </v-row>

    <!-- Visual Analytics -->
    <v-card class="rounded-xl border shadow-sm bg-white mb-6" elevation="0">
      <div class="pa-6 border-b border-slate-100">
        <h2 class="text-lg font-black text-slate-800">Distribusi Pencadangan Per Kolektibilitas</h2>
      </div>
      <v-card-text class="pa-6">
        <VueApexCharts v-if="!loading" type="donut" height="320" :options="chartOptions" :series="chartSeries"></VueApexCharts>
        <div v-else class="flex justify-center align-center" style="height: 320px;">
          <v-progress-circular indeterminate color="amber" size="48"></v-progress-circular>
        </div>
      </v-card-text>
    </v-card>

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
    </v-card>

    <!-- Data Table Section (Enterprise Grid) -->
    <div class="content-card">
      <div class="content-card__body pa-0">
        <div class="overflow-x-auto">
          <table class="fin-table fin-vtable text-sm">
            <thead>
              <tr>
                <th class="sticky left-0 z-10 whitespace-nowrap">Nasabah / Kontrak</th>
                <th class="whitespace-nowrap">Jaminan (Agunan)</th>
                <th class="whitespace-nowrap text-center">Kol</th>
                <th class="text-right whitespace-nowrap">Outstanding</th>
                <th class="text-right whitespace-nowrap">PPAP Dihitung</th>
                <th class="text-center whitespace-nowrap">Aksi</th>
              </tr>
            </thead>
            <tbody>
              <tr v-if="loading">
                <td colspan="6" class="pa-12 text-center bg-slate-50">
                  <v-progress-circular indeterminate color="amber" size="48" class="mb-4"></v-progress-circular>
                  <div class="text-h6 text-slate-500 font-weight-bold">Memuat Data Agunan & PPKA...</div>
                </td>
              </tr>
              <tr v-else-if="paginatedData.length === 0">
                <td colspan="6" class="pa-12 text-center bg-slate-50">
                  <v-icon icon="ri-inbox-line" size="64" class="text-slate-300 mb-4"></v-icon>
                  <div class="text-h6 text-slate-500 font-weight-bold">Data Tidak Ditemukan</div>
                </td>
              </tr>
              <tr v-for="(item, index) in paginatedData" :key="index">
                <td class="sticky left-0 z-10 bg-white" style="border-bottom: 1px solid #f1f5f9;">
                  <div class="font-black text-slate-800 text-sm mb-0.5 uppercase whitespace-nowrap">{{ item.nama }}</div>
                  <div class="font-mono text-[10px] text-slate-500 font-bold tracking-tight mb-1">{{ item.nokontrak }}</div>
                  <div class="text-[10px] text-slate-400 font-bold uppercase truncate max-w-[200px]">Eff: {{ item.tgleff }} | Exp: {{ item.tglexp }}</div>
                </td>

                <td style="border-bottom: 1px solid #f1f5f9;">
                  <div class="font-bold text-slate-700 text-xs mb-1">Nilai: {{ formatRp(item.total_agunan_ppka) }}</div>
                  <div class="text-[10px] text-slate-500 font-medium">Pengurang PPAP OJK</div>
                </td>

                <td style="border-bottom: 1px solid #f1f5f9;" class="text-center">
                  <v-chip size="x-small" variant="flat" :style="`background-color: ${getKolStyle(item.colbaru).bg}; color: ${getKolStyle(item.colbaru).text};`" class="font-weight-bold px-2 rounded-lg uppercase">
                    Kol {{ item.colbaru }}
                  </v-chip>
                  <div class="text-[9px] text-slate-400 font-bold uppercase mt-1">Tgk: {{ item.haritgk }} Hr</div>
                </td>

                <td style="border-bottom: 1px solid #f1f5f9;" class="text-right">
                  <div class="font-black text-slate-800 text-sm">{{ formatRp(item.osmdlc) }}</div>
                </td>

                <td style="border-bottom: 1px solid #f1f5f9;" class="text-right">
                  <div class="font-black text-sm mb-1" :class="item.is_manual_adjusted ? 'text-indigo-600' : 'text-amber-600'">
                    {{ formatRp(item.ppap_manual) }}
                    <v-icon v-if="item.is_manual_adjusted" icon="ri-edit-2-fill" size="14" class="ml-1"></v-icon>
                  </div>
                  <div class="text-[10px] text-slate-400 font-bold line-through" v-if="item.is_manual_adjusted">{{ formatRp(item.ppap_seharusnya) }}</div>
                </td>
                
                <td style="border-bottom: 1px solid #f1f5f9;" class="text-center">
                  <v-btn icon="ri-edit-box-line" variant="text" color="amber-darken-3" size="small" @click="openAdjustmentDialog(item)"></v-btn>
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
        <v-pagination v-if="totalPages > 1" v-model="currentPage" :length="totalPages" :total-visible="5" density="comfortable" active-color="primary" variant="flat" class="pagination-professional"></v-pagination>
      </div>
    </div>

    <!-- Adjustment Dialog -->
    <v-dialog v-model="showDialog" max-width="500px">
      <v-card class="rounded-xl border-0 shadow-lg">
        <v-card-title class="pa-5 bg-slate-50 border-b border-slate-100 font-black text-slate-800 flex items-center gap-2">
          <v-icon icon="ri-edit-box-fill" color="amber-darken-3"></v-icon>
          Penyesuaian PPKA Manual
        </v-card-title>
        <v-card-text class="pa-5">
          <v-text-field v-model="adjustmentForm.nokontrak" label="Nomor Kontrak" variant="outlined" density="comfortable" class="mb-2 font-mono" readonly></v-text-field>
          <v-text-field v-model="adjustmentForm.nominal_ppap" label="Nominal PPKA Baru (Rp)" variant="outlined" density="comfortable" type="number" class="mb-2" prefix="Rp"></v-text-field>
          <v-textarea v-model="adjustmentForm.alasan" label="Alasan Penyesuaian" variant="outlined" density="comfortable" rows="3"></v-textarea>
        </v-card-text>
        <v-card-actions class="pa-5 border-t border-slate-100 bg-slate-50">
          <v-spacer></v-spacer>
          <v-btn color="slate-600" variant="text" class="font-bold rounded-lg" @click="showDialog = false">Batal</v-btn>
          <v-btn color="amber-darken-3" variant="elevated" class="font-bold rounded-lg px-6" :loading="savingAdjustment" @click="submitAdjustment">Simpan</v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>
  </div>
</template>

<style scoped>
.pagination-professional :deep(.v-pagination__item) {
  border-radius: 12px;
  font-weight: 800;
  font-size: 12px;
}
.pagination-professional :deep(.v-pagination__item--active) {
  box-shadow: 0 4px 12px rgba(var(--v-theme-warning), 0.3);
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
