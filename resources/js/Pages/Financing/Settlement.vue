<script setup>
import { ref, computed, onMounted, watch } from 'vue'
import { Head } from '@inertiajs/vue3'
import DefaultLayout from '@/layouts/default.vue'
import axios from 'axios'
import '@/assets/css/financing-shared.css'

defineOptions({ layout: DefaultLayout })

const loading = ref(true)
const rawData = ref([])
const summary = ref({
  total_realisasi_count: 0,
  total_realisasi_volume: 0,
  total_realisasi_margin: 0,
  total_pelunasan_count: 0,
  total_pelunasan_volume: 0,
  total_pelunasan_margin: 0,
  net_cash_flow: 0,
})

const activeTab = ref('realisasi')
const searchQuery = ref('')
const selectedCabang = ref('Semua Cabang')
const currentPage = ref(1)
const itemsPerPage = ref(15)

const cabangOptions = computed(() => {
  const cabangs = [...new Set(rawData.value.map(item => item.nama_cabang))]
  return ['Semua Cabang', ...cabangs.sort()]
})

const fetchData = async () => {
  loading.value = true
  try {
    const response = await axios.get('/api/v1/financing/penyelesaian/settlement')
    if (response.data.success) {
      rawData.value = response.data.data
      summary.value = response.data.summary
    }
  } catch (error) {
    console.error('Error fetching settlement data:', error)
  } finally {
    loading.value = false
  }
}

const isEarlySettlement = (item) => {
  if (!item.tglexp || !item.tgllunas || item.tglexp === '-' || item.tgllunas === '-') return false
  const expParts = item.tglexp.split('-')
  const lunasParts = item.tgllunas.split('-')
  if (expParts.length !== 3 || lunasParts.length !== 3) return false
  
  const expDate = new Date(`${expParts[2]}-${expParts[1]}-${expParts[0]}`)
  const lunasDate = new Date(`${lunasParts[2]}-${lunasParts[1]}-${lunasParts[0]}`)
  
  // Return true if settled more than 30 days before expiration
  return (expDate - lunasDate) / (1000 * 60 * 60 * 24) > 30
}

const filteredData = computed(() => {
  const statusFilter = activeTab.value === 'realisasi' ? 'A' : 'L'
  return rawData.value.filter(item => {
    const matchStatus = item.stsrec === statusFilter
    const matchCabang = selectedCabang.value === 'Semua Cabang' || item.nama_cabang === selectedCabang.value
    const matchSearch = item.nama.toLowerCase().includes(searchQuery.value.toLowerCase()) || 
                        item.nokontrak.includes(searchQuery.value)
    return matchStatus && matchCabang && matchSearch
  })
})

const totalPages = computed(() => Math.ceil(filteredData.value.length / itemsPerPage.value))
const paginatedData = computed(() => {
  const start = (currentPage.value - 1) * itemsPerPage.value
  return filteredData.value.slice(start, start + itemsPerPage.value)
})

const formatRp = (value) => {
  if (!value) return 'Rp 0'
  return new Intl.NumberFormat('id-ID', {
    style: 'currency',
    currency: 'IDR',
    minimumFractionDigits: 0,
    maximumFractionDigits: 0
  }).format(value)
}

const formatShortRp = (value) => {
  if (!value && value !== 0) return 'Rp 0'
  const num = Number(value)
  if (Math.abs(num) >= 1e12) return `Rp ${(num / 1e12).toFixed(2)} T`
  if (Math.abs(num) >= 1e9) return `Rp ${(num / 1e9).toFixed(2)} M`
  if (Math.abs(num) >= 1e6) return `Rp ${(num / 1e6).toFixed(2)} Jt`
  
  return new Intl.NumberFormat('id-ID', {
    style: 'currency',
    currency: 'IDR',
    minimumFractionDigits: 0,
    maximumFractionDigits: 0
  }).format(num)
}

const resetPage = () => { currentPage.value = 1 }

watch([activeTab, selectedCabang, searchQuery], resetPage)

onMounted(() => { fetchData() })
</script>

<template>
  <div class="fin-page px-4 pt-0">
    <Head title="Settlement - Realisasi & Pelunasan" />

    <!-- ── HERO HEADER ─────────────────────────────────────────── -->
    <div class="fin-hero mb-6">
      <div class="fin-hero__deco"></div>
      <div class="fin-hero__inner">
        <div class="d-flex flex-column flex-md-row justify-space-between align-start align-md-center gap-4">
          <div class="d-flex align-center gap-4">
            <div class="fin-hero__icon fin-icon-blue">
              <v-icon icon="ri-exchange-line" size="26" color="white" />
            </div>
            <div class="fin-hero__meta">
              <h1 class="fin-hero__title">Settlement Pembiayaan</h1>
              <p class="fin-hero__subtitle">Monitoring pencairan pembiayaan baru dan pelunasan kontrak aktif secara real-time.</p>
              <div class="fin-hero__badges">
                <span class="fin-badge fin-badge--warning">🔄 Cash Flow</span>
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
      <v-col cols="12" sm="6" lg="3">
        <v-card class="rounded-xl border shadow-sm transition-swing h-100" elevation="0" style="position: relative; overflow: hidden;">
          <div style="position: absolute; top: -20px; right: -20px; width: 120px; height: 120px; opacity: 0.08;">
            <v-icon icon="ri-download-circle-fill" size="120" color="#10b981" />
          </div>
          <v-card-text class="pa-5" style="position: relative; z-index: 1;">
            <div class="d-flex justify-space-between align-start">
              <div>
                <p class="text-caption font-weight-bold text-uppercase tracking-widest mb-1" style="color: #64748B; font-family: 'Inter', sans-serif;">REALISASI BARU</p>
                <h2 class="text-h4 font-weight-bold mb-2" style="color: #059669; font-family: 'Plus Jakarta Sans', sans-serif; line-height: 1.2;">
                  <v-progress-circular v-if="loading" indeterminate size="24" width="3" color="emerald"></v-progress-circular>
                  <template v-else>{{ summary.total_realisasi_count }}</template>
                </h2>
                <p class="text-caption text-medium-emphasis mb-0" style="font-family: 'Inter', sans-serif; color: #059669; font-weight: 600;">Kontrak Pencairan</p>
              </div>
            </div>
          </v-card-text>
        </v-card>
      </v-col>

      <v-col cols="12" sm="6" lg="3">
        <v-card class="rounded-xl border shadow-sm transition-swing h-100" elevation="0" style="position: relative; overflow: hidden;">
          <div style="position: absolute; top: -20px; right: -20px; width: 120px; height: 120px; opacity: 0.08;">
            <v-icon icon="ri-money-dollar-circle-fill" size="120" color="#10b981" />
          </div>
          <v-card-text class="pa-5" style="position: relative; z-index: 1;">
            <div class="d-flex justify-space-between align-start">
              <div>
                <p class="text-caption font-weight-bold text-uppercase tracking-widest mb-1" style="color: #64748B; font-family: 'Inter', sans-serif;">VOLUME REALISASI</p>
                <h2 class="text-h4 font-weight-bold mb-2" style="color: #059669; font-family: 'Plus Jakarta Sans', sans-serif; line-height: 1.2;">
                  <v-progress-circular v-if="loading" indeterminate size="24" width="3" color="green"></v-progress-circular>
                  <template v-else>{{ formatShortRp(summary.total_realisasi_volume) }}</template>
                </h2>
                <p class="text-caption text-medium-emphasis mb-0" style="font-family: 'Inter', sans-serif; color: #059669; font-weight: 600;">Margin: {{ formatShortRp(summary.total_realisasi_margin) }}</p>
              </div>
            </div>
          </v-card-text>
        </v-card>
      </v-col>

      <v-col cols="12" sm="6" lg="3">
        <v-card class="rounded-xl border shadow-sm transition-swing h-100" elevation="0" style="position: relative; overflow: hidden;">
          <div style="position: absolute; top: -20px; right: -20px; width: 120px; height: 120px; opacity: 0.08;">
            <v-icon icon="ri-upload-circle-fill" size="120" color="#e11d48" />
          </div>
          <v-card-text class="pa-5" style="position: relative; z-index: 1;">
            <div class="d-flex justify-space-between align-start">
              <div>
                <p class="text-caption font-weight-bold text-uppercase tracking-widest mb-1" style="color: #64748B; font-family: 'Inter', sans-serif;">TOTAL PELUNASAN</p>
                <h2 class="text-h4 font-weight-bold mb-2" style="color: #e11d48; font-family: 'Plus Jakarta Sans', sans-serif; line-height: 1.2;">
                  <v-progress-circular v-if="loading" indeterminate size="24" width="3" color="rose"></v-progress-circular>
                  <template v-else>{{ summary.total_pelunasan_count }}</template>
                </h2>
                <p class="text-caption text-medium-emphasis mb-0" style="font-family: 'Inter', sans-serif; color: #e11d48; font-weight: 600;">Kontrak Berakhir</p>
              </div>
            </div>
          </v-card-text>
        </v-card>
      </v-col>

      <v-col cols="12" sm="6" lg="3">
        <v-card class="rounded-xl border shadow-sm transition-swing h-100" elevation="0" style="position: relative; overflow: hidden;">
          <div style="position: absolute; top: -20px; right: -20px; width: 120px; height: 120px; opacity: 0.08;">
            <v-icon :icon="summary.net_cash_flow >= 0 ? 'ri-line-chart-fill' : 'ri-funds-line'" size="120" :color="summary.net_cash_flow >= 0 ? '#3b82f6' : '#e11d48'" />
          </div>
          <v-card-text class="pa-5" style="position: relative; z-index: 1;">
            <div class="d-flex justify-space-between align-start">
              <div>
                <p class="text-caption font-weight-bold text-uppercase tracking-widest mb-1" style="color: #64748B; font-family: 'Inter', sans-serif;">NET CASH FLOW</p>
                <h2 class="text-h4 font-weight-bold mb-2" :style="{ color: summary.net_cash_flow >= 0 ? '#3b82f6' : '#e11d48', fontFamily: 'Plus Jakarta Sans, sans-serif', lineHeight: 1.2 }">
                  <v-progress-circular v-if="loading" indeterminate size="24" width="3" :color="summary.net_cash_flow >= 0 ? 'blue' : 'red'"></v-progress-circular>
                  <template v-else>{{ formatShortRp(summary.net_cash_flow) }}</template>
                </h2>
                <p class="text-caption text-medium-emphasis mb-0" style="font-family: 'Inter', sans-serif; font-weight: 600;" :style="{ color: summary.net_cash_flow >= 0 ? '#3b82f6' : '#e11d48' }">Realisasi - Pelunasan Pokok</p>
              </div>
            </div>
          </v-card-text>
        </v-card>
      </v-col>
    </v-row>

    <!-- Independent Filter Bar & Tab Navigation (Pro Max) -->
    <v-card class="rounded-xl border shadow-sm bg-white mb-6 overflow-hidden" elevation="0">
      <v-tabs v-model="activeTab" bg-color="slate-50" color="indigo-darken-1" class="border-b border-slate-100 font-bold" align-tabs="title" slider-color="indigo">
        <v-tab value="realisasi" class="text-sm font-black tracking-tight uppercase" :disabled="loading">
          <v-icon icon="ri-download-circle-line" class="mr-2"></v-icon> Realisasi Baru ({{ summary.total_realisasi_count }})
        </v-tab>
        <v-tab value="pelunasan" class="text-sm font-black tracking-tight uppercase" :disabled="loading">
          <v-icon icon="ri-upload-circle-line" class="mr-2"></v-icon> Pelunasan ({{ summary.total_pelunasan_count }})
        </v-tab>
      </v-tabs>

      <div class="pa-4 d-flex flex-wrap gap-3 align-center">
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
        
        <div v-if="activeTab === 'pelunasan'" class="flex gap-4 ml-auto items-center">
          <div class="flex items-center gap-1.5"><div class="w-2 h-2 rounded-full bg-emerald-500"></div><span class="text-[10px] font-black text-slate-500 uppercase tracking-widest">Normal</span></div>
          <div class="flex items-center gap-1.5"><div class="w-2 h-2 rounded-full bg-purple-500"></div><span class="text-[10px] font-black text-slate-500 uppercase tracking-widest">Early Settlement</span></div>
        </div>
      </div>
    </v-card>

    <!-- Data Table Section (Enterprise Grid) -->
    <div class="content-card">
      <div class="content-card__body pa-0">
        <div class="overflow-x-auto">
          <table class="fin-table fin-vtable text-sm">
            <thead>
              <tr>
                <th class="sticky left-0 z-10 whitespace-nowrap">Nasabah / Kontrak</th>
                <th class="whitespace-nowrap">Timeline</th>
                <th class="whitespace-nowrap">Status Settlement</th>
                <th class="text-right whitespace-nowrap">Outstanding Pokok</th>
                <th class="text-right whitespace-nowrap">Outstanding Margin</th>
              </tr>
            </thead>
            <tbody>
              <tr v-if="loading">
                <td colspan="5" class="pa-12 text-center bg-slate-50">
                  <v-progress-circular indeterminate color="indigo" size="48" class="mb-4"></v-progress-circular>
                  <div class="text-h6 text-slate-500 font-weight-bold">Memuat Data Transaksi...</div>
                </td>
              </tr>
              <tr v-else-if="paginatedData.length === 0">
                <td colspan="5" class="pa-12 text-center bg-slate-50">
                  <v-icon icon="ri-inbox-line" size="64" class="text-slate-300 mb-4"></v-icon>
                  <div class="text-h6 text-slate-500 font-weight-bold">Data Tidak Ditemukan</div>
                </td>
              </tr>
              <tr v-for="(item, index) in paginatedData" :key="index">
                <td class="sticky left-0 z-10 bg-white" style="border-bottom: 1px solid #f1f5f9;">
                  <div class="font-black text-slate-800 text-sm mb-0.5 uppercase whitespace-nowrap">{{ item.nama }}</div>
                  <div class="font-mono text-[10px] text-slate-500 font-bold tracking-tight mb-1">{{ item.nokontrak }}</div>
                  <div class="text-[10px] text-slate-400 font-bold uppercase truncate max-w-[200px]">{{ item.nama_produk }} | {{ item.nama_cabang }}</div>
                </td>

                <td style="border-bottom: 1px solid #f1f5f9;">
                  <div class="d-flex align-center gap-3">
                    <div class="flex items-center gap-2">
                      <span class="inline-block px-2 py-0.5 text-[9px] font-black bg-slate-100 text-slate-500 rounded uppercase">Book</span>
                      <span class="text-xs text-slate-600 font-bold">{{ item.tglbook }}</span>
                    </div>
                    <div class="flex items-center gap-2">
                      <span class="inline-block px-2 py-0.5 text-[9px] font-black bg-slate-100 text-slate-500 rounded uppercase">Exp</span>
                      <span class="text-xs text-slate-600 font-bold">{{ item.tglexp }}</span>
                    </div>
                  </div>
                </td>

                <td style="border-bottom: 1px solid #f1f5f9;">
                  <template v-if="activeTab === 'realisasi'">
                    <v-chip size="x-small" color="emerald" variant="flat" class="text-white font-black px-2 py-1 uppercase rounded-lg">Realisasi Aktif</v-chip>
                    <div class="text-[10px] text-emerald-600 font-bold mt-1 uppercase">Eff: {{ item.tgleff }}</div>
                  </template>
                  <template v-else>
                    <v-chip size="x-small" :color="isEarlySettlement(item) ? 'purple' : 'emerald'" variant="flat" class="text-white font-black px-2 py-1 uppercase rounded-lg">
                      {{ isEarlySettlement(item) ? 'Early Settlement' : 'Normal Settlement' }}
                    </v-chip>
                    <div class="text-[10px] font-bold mt-1 uppercase" :class="isEarlySettlement(item) ? 'text-purple-600' : 'text-emerald-600'">Lunas: {{ item.tgllunas }}</div>
                  </template>
                </td>

                <td class="text-right" style="border-bottom: 1px solid #f1f5f9;">
                  <div class="font-black text-slate-800 text-sm">{{ formatRp(activeTab === 'realisasi' ? item.mdlawal : item.mdleom) }}</div>
                  <div class="text-[10px] text-slate-400 font-bold uppercase mt-1">O/S Pokok (Cair)</div>
                </td>

                <td class="text-right" style="border-bottom: 1px solid #f1f5f9;">
                  <div class="font-black text-slate-600 text-sm">{{ formatRp(activeTab === 'realisasi' ? item.mgnawal : item.mgneom) }}</div>
                  <div class="text-[10px] text-slate-400 font-bold uppercase mt-1">O/S Margin (Lunas)</div>
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
        <v-pagination v-if="totalPages > 1" v-model="currentPage" :length="totalPages" :total-visible="5" density="comfortable" active-color="primary" variant="flat" class="pagination-professional"></v-pagination>
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
