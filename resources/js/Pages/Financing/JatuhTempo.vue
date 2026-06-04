<script setup>
import { ref, computed, onMounted, watch } from 'vue'
import { Head } from '@inertiajs/vue3'
import DefaultLayout from '@/layouts/default.vue'
import { useTunggakanStore } from '@/stores/tunggakanStore'
import { storeToRefs } from 'pinia'
import VueApexCharts from 'vue3-apexcharts'
import '@/assets/css/financing-shared.css'

defineOptions({ layout: DefaultLayout })

const store = useTunggakanStore()
const {
  jatuhTempoData,
  loadingJatuhTempo,
  selectedCabang,
  selectedTahun,
  selectedBulan,
  periodMeta,
  totalJatuhTempo,
  totalTagihanPokok,
  saldoStatus
} = storeToRefs(store)

const cabangs = ref([])
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

// ─── Pagination Logic ─────────────────────────────────────────
const currentPage = ref(1)
const itemsPerPage = ref(15)

const totalPages = computed(() => Math.ceil(jatuhTempoData.value.length / itemsPerPage.value))

const paginatedJatuhTempo = computed(() => {
  const start = (currentPage.value - 1) * itemsPerPage.value
  return jatuhTempoData.value.slice(start, start + itemsPerPage.value)
})

watch(jatuhTempoData, () => {
  currentPage.value = 1
})

// ─── Critical Logic: Saldo Check ──────────────────────────────
const isSaldoSufficient = (item) => {
  const tagihanTotal = parseFloat(item.tgkmdl || 0) + parseFloat(item.tgkmgn || 0)
  const saldoEfektif = parseFloat(item.saving_balance || 0)
  return saldoEfektif >= tagihanTotal
}

// ─── Bulk Action Logic ───────────────────────────────────────
const selectedItems = ref([])
const selectAll = ref(false)

const toggleSelectAll = () => {
  if (selectAll.value) {
    selectedItems.value = paginatedJatuhTempo.value.map(i => i.nokontrak)
  } else {
    selectedItems.value = []
  }
}

watch(selectedItems, (val) => {
  if (val.length === 0) selectAll.value = false
  else if (val.length === paginatedJatuhTempo.value.length && val.length > 0) selectAll.value = true
})

const handleBulkWA = () => {
  if (selectedItems.value.length === 0) {
    alert('Pilih minimal 1 nasabah untuk mengirim WA Blast.')
    return
  }
  console.log('Sending WA Blast to:', selectedItems.value)
  alert(`Memproses pengiriman WA Blast ke ${selectedItems.value.length} nasabah terpilih. (Fungsi dummy, silakan cek console)`)
}

// ─── Analytics: Kesiapan Dana Chart ───────────────────────────
const ratioChartSeries = computed(() => [saldoStatus.value.cukup, saldoStatus.value.kurang])
const ratioChartOpts = computed(() => ({ 
  chart: { type: 'donut', sparkline: { enabled: true } }, 
  labels: ['Saldo Cukup', 'Saldo Kurang'], 
  colors: ['#10B981', '#EF4444'], 
  plotOptions: { pie: { donut: { size: '75%' } } }, 
  stroke: { width: 0 }, 
  tooltip: { enabled: true } 
}))

// ─── Helpers ─────────────────────────────────────────────────
const getUrgency = (dateStr) => {
  if (!dateStr || dateStr.length !== 8) return { label: 'Unknown', color: 'grey', icon: 'ri-question-line' }
  
  // Parse YYYYMMDD
  const year = parseInt(dateStr.substring(0, 4))
  const month = parseInt(dateStr.substring(4, 6)) - 1
  const day = parseInt(dateStr.substring(6, 8))
  
  const expDate = new Date(year, month, day)
  const today = new Date()
  today.setHours(0, 0, 0, 0)
  
  const diffTime = expDate - today
  const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24))

  if (diffDays < 0) return { label: 'OVERDUE', color: 'error', icon: 'ri-alarm-warning-fill' }
  if (diffDays <= 3) return { label: 'CRITICAL', color: 'deep-orange', icon: 'ri-fire-fill' }
  if (diffDays <= 7) return { label: 'WARNING', color: 'orange', icon: 'ri-error-warning-fill' }
  return { label: 'SAFE', color: 'success', icon: 'ri-checkbox-circle-fill' }
}

const getRowClass = (item) => {
  const urgency = getUrgency(item.tglexp).label
  const kurangSaldo = !isSaldoSufficient(item)
  if ((urgency === 'CRITICAL' || urgency === 'OVERDUE') && kurangSaldo) {
    return 'row--danger'
  }
  return ''
}

const formatDate = (dateStr) => {
  if (!dateStr || dateStr.length !== 8) return '—'
  return `${dateStr.substring(6, 8)}/${dateStr.substring(4, 6)}/${dateStr.substring(0, 4)}`
}

const sendWA = (item) => {
  const hp = item.hp?.replace(/[^0-9]/g, '')
  if (!hp || hp === '-' || hp.length < 10) {
    alert('Nomor HP tidak valid atau tidak tersedia.')
    return
  }
  const formattedHp = hp.startsWith('0') ? '62' + hp.substring(1) : hp
  const tglJt = formatDate(item.tglexp)
  const tagihan = store.formatRp(parseFloat(item.tgkmdl || 0) + parseFloat(item.tgkmgn || 0))
  
  const message = `Assalamu'alaikum Bpk/Ibu ${item.nama}, kami dari BPRS HIK MCI menginformasikan bahwa angsuran pembiayaan No: ${item.nokontrak} akan jatuh tempo pada ${tglJt} sebesar ${tagihan}. Mohon pastikan saldo tabungan mencukupi untuk proses autodebet. Terima kasih.`
  
  window.open(`https://wa.me/${formattedHp}?text=${encodeURIComponent(message)}`, '_blank')
}

const fetchCabangs = async () => {
  try {
    const res = await fetch('/api/v1/financing/cabangs')
    const json = await res.json()
    if (json.success) cabangs.value = json.data
  } catch (e) { console.error(e) }
}

onMounted(() => {
  fetchCabangs()
  store.fetchJatuhTempo()
})

watch([selectedCabang, selectedTahun, selectedBulan], () => store.fetchJatuhTempo())
</script>

<template>
  <div class="fin-page px-4 pt-0">
    <Head title="Jatuh Tempo & Early Warning" />

    <!-- ── HERO HEADER ─────────────────────────────────────────── -->
    <div class="fin-hero mb-6">
      <div class="fin-hero__deco"></div>
      <div class="fin-hero__inner">
        <div class="d-flex flex-column flex-md-row justify-space-between align-start align-md-center gap-4">
          <div class="d-flex align-center gap-4">
            <div class="fin-hero__icon fin-icon-amber">
              <v-icon icon="ri-alarm-warning-line" size="26" color="white" />
            </div>
            <div class="fin-hero__meta">
              <h1 class="fin-hero__title">Jatuh Tempo & Early Warning</h1>
              <p class="fin-hero__subtitle">Monitoring harian antrian jatuh tempo dan kesiapan saldo nasabah.</p>
              <div class="fin-hero__badges">
                <span class="fin-badge fin-badge--warning">⚡ EWS Module</span>
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
            ></v-select>
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
            ></v-select>
            <v-select
              v-model="selectedCabang"
              :items="['Semua Cabang', ...cabangs.map(c => c.nama)]"
              label="Filter Cabang"
              variant="solo"
              density="compact"
              flat
              hide-details
              rounded="lg"
              bg-color="white"
              prepend-inner-icon="ri-store-2-line"
              style="min-width: 180px; max-width: 240px;"
            ></v-select>
            
            <div style="width: 1px; height: 24px; background: rgba(255,255,255,0.2);" class="mx-1"></div>
            
            <v-btn 
              variant="text" 
              density="comfortable"
              @click="store.fetchJatuhTempo" 
              :loading="loadingJatuhTempo"
              icon="ri-refresh-line"
              color="white"
            ></v-btn>
          </div>
        </div>
      </div>
    </div>

    <!-- 2. Executive Scorecards -->
    <v-row class="mb-6">
      <v-col cols="12" md="4">
        <v-card class="rounded-xl border shadow-sm transition-swing h-100" elevation="0" style="position: relative; overflow: hidden;">
          <div style="position: absolute; top: -20px; right: -20px; width: 120px; height: 120px; opacity: 0.08;">
            <v-icon icon="ri-user-follow-line" size="120" color="#64748b" />
          </div>
          <v-card-text class="pa-5" style="position: relative; z-index: 1;">
            <div class="d-flex justify-space-between align-start">
              <div>
                <p class="text-caption font-weight-bold text-uppercase tracking-widest mb-1" style="color: #64748B; font-family: 'Inter', sans-serif;">TOTAL ANTRIAN</p>
                <div class="d-flex align-center gap-2 mb-2">
                  <h2 class="text-h4 font-weight-bold" style="color: #64748b; font-family: 'Plus Jakarta Sans', sans-serif; line-height: 1.2;">{{ totalJatuhTempo }}</h2>
                  <VueApexCharts type="donut" width="40" height="40" :options="ratioChartOpts" :series="ratioChartSeries" />
                </div>
                <p class="text-caption text-medium-emphasis mb-0" style="font-family: 'Inter', sans-serif;">Akun jatuh tempo bulan ini</p>
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
                <p class="text-caption font-weight-bold text-uppercase tracking-widest mb-1" style="color: #64748B; font-family: 'Inter', sans-serif;">PROYEKSI TAGIHAN</p>
                <h2 class="text-h4 font-weight-bold mb-2" style="color: #3b82f6; font-family: 'Plus Jakarta Sans', sans-serif; line-height: 1.2;">{{ store.formatShortRp(totalTagihanPokok) }}</h2>
                <p class="text-caption text-medium-emphasis mb-0" style="font-family: 'Inter', sans-serif;">Estimasi total likuiditas</p>
              </div>
            </div>
          </v-card-text>
        </v-card>
      </v-col>

      <v-col cols="12" md="4">
        <v-card class="rounded-xl border shadow-sm transition-swing h-100" elevation="0" style="position: relative; overflow: hidden;">
          <div style="position: absolute; top: -20px; right: -20px; width: 120px; height: 120px; opacity: 0.08;">
            <v-icon icon="ri-checkbox-circle-line" size="120" color="#059669" />
          </div>
          <v-card-text class="pa-5" style="position: relative; z-index: 1;">
            <div class="d-flex justify-space-between align-start">
              <div>
                <p class="text-caption font-weight-bold text-uppercase tracking-widest mb-1" style="color: #64748B; font-family: 'Inter', sans-serif;">SALDO CUKUP</p>
                <h2 class="text-h4 font-weight-bold mb-2" style="color: #059669; font-family: 'Plus Jakarta Sans', sans-serif; line-height: 1.2;">{{ saldoStatus.cukup }}</h2>
                <p class="text-caption text-medium-emphasis mb-0" style="color: #059669; font-weight: 600; font-family: 'Inter', sans-serif;">Dana aman untuk autodebet</p>
              </div>
            </div>
          </v-card-text>
        </v-card>
      </v-col>
    </v-row>

    <v-alert
      v-if="periodUnavailable && !loadingJatuhTempo"
      type="warning"
      variant="tonal"
      border="start"
      rounded="lg"
      class="mb-6"
    >
      <div class="font-weight-bold mb-1">Periode {{ activePeriodLabel }} belum tersedia</div>
      <div class="text-body-2">{{ periodMeta.message || 'Database snapshot periode ini belum tersedia.' }}</div>
    </v-alert>

    <!-- 3. Main Data Grid -->
    <div class="content-card">
      <div class="content-card__header d-flex justify-space-between align-center px-6 py-4 border-b">
        <div>
          <div class="content-card__title">Daftar Nasabah Jatuh Tempo</div>
          <div class="content-card__subtitle">Daftar nasabah yang mendekati atau melewati jatuh tempo</div>
        </div>
        <v-btn 
          v-if="selectedItems.length > 0"
          color="success" 
          variant="flat" 
          prepend-icon="ri-whatsapp-line" 
          @click="handleBulkWA"
          class="font-weight-bold"
        >
          WA Blast ({{ selectedItems.length }})
        </v-btn>
      </div>
      <div class="content-card__body pa-0">
        <div class="overflow-x-auto">
          <table class="fin-table fin-vtable">
            <thead>
              <tr>
                <th style="width: 50px" class="text-center px-0">
                  <v-checkbox-btn v-model="selectAll" @change="toggleSelectAll" color="primary" density="compact" inline hide-details />
                </th>
                <th class="text-center" style="width: 80px">EWS</th>
                <th class="text-left">NASABAH & KONTRAK</th>
                <th class="text-center">TGL JATUH TEMPO</th>
                <th class="text-right">TOTAL TAGIHAN</th>
                <th class="text-right" style="width: 200px">SALDO TABUNGAN</th>
                <th class="text-center">STATUS DEBET</th>
                <th class="text-center">AKSI</th>
              </tr>
            </thead>

            <tbody v-if="!loadingJatuhTempo">
              <tr v-for="item in paginatedJatuhTempo" :key="item.nokontrak" :class="getRowClass(item)">
                <td class="text-center px-0">
                  <v-checkbox-btn v-model="selectedItems" :value="item.nokontrak" color="primary" density="compact" inline hide-details />
                </td>
                <td class="text-center">
                  <v-tooltip :text="getUrgency(item.tglexp).label" location="top">
                    <template #activator="{ props }">
                      <v-icon 
                        v-bind="props"
                        :icon="getUrgency(item.tglexp).icon" 
                        :color="getUrgency(item.tglexp).color"
                        size="24"
                      ></v-icon>
                    </template>
                  </v-tooltip>
                </td>
                <td class="py-3">
                  <div class="font-weight-bold text-subtitle-2">{{ item.nama }}</div>
                  <div class="text-caption text-primary font-weight-medium">{{ item.nokontrak }}</div>
                </td>
                <td class="text-center">
                  <v-chip size="small" variant="tonal" color="primary" class="font-weight-bold">
                    {{ formatDate(item.tglexp) }}
                  </v-chip>
                </td>
                <td class="text-right font-weight-bold">
                  {{ store.formatRp(parseFloat(item.tgkmdl || 0) + parseFloat(item.tgkmgn || 0)) }}
                </td>
                <td class="text-right">
                  <div class="d-flex flex-column align-end">
                    <div class="font-weight-bold" :class="isSaldoSufficient(item) ? 'text-success' : 'text-error'">
                      {{ store.formatRp(item.saving_balance) }}
                    </div>
                    <v-progress-linear
                      :model-value="Math.min(100, (parseFloat(item.saving_balance || 0) / (parseFloat(item.tgkmdl || 0) + parseFloat(item.tgkmgn || 0) || 1)) * 100)"
                      :color="isSaldoSufficient(item) ? 'success' : 'error'"
                      height="4"
                      rounded
                      class="mt-1"
                      style="width: 100px"
                    ></v-progress-linear>
                  </div>
                </td>
                <td class="text-center">
                  <v-chip
                    :color="isSaldoSufficient(item) ? 'success' : 'error'"
                    size="small"
                    variant="flat"
                    class="font-weight-bold text-white"
                  >
                    {{ isSaldoSufficient(item) ? 'SALDO CUKUP' : 'KURANG' }}
                  </v-chip>
                </td>
                <td class="text-center">
                  <v-btn
                    icon="ri-whatsapp-line"
                    color="success"
                    variant="tonal"
                    size="x-small"
                    class="rounded-lg"
                    @click="sendWA(item)"
                  ></v-btn>
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        <!-- Skeleton Loader -->
        <div v-if="loadingJatuhTempo" class="fin-loading">
          <v-progress-circular indeterminate color="primary" size="48"></v-progress-circular>
        </div>

        <!-- Empty State -->
        <div v-else-if="jatuhTempoData.length === 0" class="text-center pa-12">
          <v-icon icon="ri-inbox-line" size="64" class="text-disabled mb-4"></v-icon>
          <div class="text-h6 text-disabled">Tidak ada data antrian jatuh tempo.</div>
          <p class="text-caption text-disabled">Gunakan filter cabang atau sinkronkan data terbaru.</p>
        </div>

        <!-- Pagination Footer -->
        <v-divider v-if="jatuhTempoData.length > 0"></v-divider>
        <div v-if="jatuhTempoData.length > 0" class="pa-4 d-flex align-center justify-space-between bg-slate-50">
          <div class="text-caption text-medium-emphasis font-weight-bold">
            Menampilkan {{ (currentPage - 1) * itemsPerPage + 1 }} - {{ Math.min(currentPage * itemsPerPage, jatuhTempoData.length) }} dari {{ jatuhTempoData.length }} data
          </div>
          <v-pagination
            v-model="currentPage"
            :length="totalPages"
            :total-visible="5"
            density="compact"
            variant="flat"
            active-color="primary"
          ></v-pagination>
        </div>
      </div>
    </div>
  </div>
</template>

<style scoped>
/* Component styles handled by financing-shared.css */
</style>
