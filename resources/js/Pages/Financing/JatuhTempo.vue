<script setup>
import { ref, computed, onMounted, watch } from 'vue'
import { Head } from '@inertiajs/vue3'
import DefaultLayout from '@/layouts/default.vue'
import { useTunggakanStore } from '@/stores/tunggakanStore'
import { storeToRefs } from 'pinia'
import VueApexCharts from 'vue3-apexcharts'

defineOptions({ layout: DefaultLayout })

const store = useTunggakanStore()
const {
  jatuhTempoData,
  loadingJatuhTempo,
  selectedCabang,
  totalJatuhTempo,
  totalTagihanPokok,
  saldoStatus
} = storeToRefs(store)

const cabangs = ref([])

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

watch(selectedCabang, () => store.fetchJatuhTempo())
</script>

<template>
  <v-container fluid class="pa-6">
    <Head title="Jatuh Tempo & Early Warning" />

    <!-- 1. Header & Filter Bar -->
    <div class="d-flex flex-column flex-sm-row justify-space-between align-start align-sm-center mb-6 gap-4">
      <div>
        <h1 class="text-h4 font-weight-bold mb-1">Jatuh Tempo & Early Warning</h1>
        <p class="text-subtitle-1 text-medium-emphasis">Monitoring harian antrian jatuh tempo dan kesiapan saldo nasabah.</p>
      </div>
      
      <div class="d-flex align-center gap-3 w-100 w-sm-auto">
        <v-select
          v-model="selectedCabang"
          :items="['Semua Cabang', ...cabangs.map(c => c.nama)]"
          label="Filter Cabang"
          variant="outlined"
          density="comfortable"
          hide-details
          rounded="lg"
          style="min-width: 240px"
          bg-color="white"
        ></v-select>
        
        <v-btn 
          color="primary" 
          variant="flat" 
          height="48"
          rounded="lg"
          @click="store.fetchJatuhTempo" 
          :loading="loadingJatuhTempo"
          prepend-icon="ri-refresh-line"
          class="px-6"
        >
          Sync Data
        </v-btn>
      </div>
    </div>

    <!-- 2. Executive Scorecards -->
    <v-row class="mb-6">
      <v-col cols="12" sm="6" lg="3">
        <v-card border flat rounded="xl" class="pa-4">
          <div class="text-overline text-medium-emphasis mb-1">Total Antrian</div>
          <div class="d-flex align-center justify-space-between">
            <div class="d-flex align-center gap-4">
              <div class="text-h4 font-weight-bold">{{ totalJatuhTempo }}</div>
              <VueApexCharts type="donut" width="60" height="60" :options="ratioChartOpts" :series="ratioChartSeries" />
            </div>
            <v-avatar color="primary-lighten-5" rounded="lg">
              <v-icon icon="ri-user-follow-line" color="primary"></v-icon>
            </v-avatar>
          </div>
          <div class="mt-2 text-caption text-medium-emphasis border-t pt-2">Akun jatuh tempo bulan ini</div>
        </v-card>
      </v-col>

      <v-col cols="12" sm="6" lg="3">
        <v-card border flat rounded="xl" class="pa-4">
          <div class="text-overline text-medium-emphasis mb-1">Proyeksi Tagihan Pokok</div>
          <div class="d-flex align-center justify-space-between">
            <div class="text-h4 font-weight-bold">{{ store.formatShortRp(totalTagihanPokok) }}</div>
            <v-avatar color="info-lighten-5" rounded="lg">
              <v-icon icon="ri-money-dollar-circle-line" color="info"></v-icon>
            </v-avatar>
          </div>
          <div class="mt-2 text-caption text-medium-emphasis border-t pt-2">Estimasi total likuiditas masuk</div>
        </v-card>
      </v-col>

      <v-col cols="12" sm="6" lg="3">
        <v-card border flat rounded="xl" class="pa-4" style="border-bottom: 4px solid #4CAF50 !important">
          <div class="text-overline text-medium-emphasis mb-1">Saldo Cukup</div>
          <div class="d-flex align-center justify-space-between">
            <div class="text-h4 font-weight-bold text-success">{{ saldoStatus.cukup }}</div>
            <v-avatar color="success-lighten-5" rounded="lg">
              <v-icon icon="ri-checkbox-circle-line" color="success"></v-icon>
            </v-avatar>
          </div>
          <div class="mt-2 text-caption text-success font-weight-bold border-t pt-2">Dana aman untuk autodebet</div>
        </v-card>
      </v-col>

      <v-col cols="12" sm="6" lg="3">
        <v-card border flat rounded="xl" class="pa-4" style="border-bottom: 4px solid #F44336 !important">
          <div class="text-overline text-medium-emphasis mb-1">Saldo Kurang / Risiko</div>
          <div class="d-flex align-center justify-space-between">
            <div class="text-h4 font-weight-bold text-error">{{ saldoStatus.kurang }}</div>
            <v-avatar color="error-lighten-5" rounded="lg">
              <v-icon icon="ri-error-warning-line" color="error"></v-icon>
            </v-avatar>
          </div>
          <div class="mt-2 text-caption text-error font-weight-bold border-t pt-2">Perlu tindak lanjut segera</div>
        </v-card>
      </v-col>
    </v-row>

    <!-- 3. Main Data Grid -->
    <v-card border flat rounded="xl">
      <v-table hover>
        <thead>
          <tr class="bg-grey-lighten-4">
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
          <tr v-for="item in paginatedJatuhTempo" :key="item.nokontrak">
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
      </v-table>

      <!-- Skeleton Loader -->
      <div v-if="loadingJatuhTempo" class="pa-4">
        <v-skeleton-loader type="table-row-divider@5"></v-skeleton-loader>
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
    </v-card>
  </v-container>
</template>

<style scoped>
/* Strict compliance with user request: No custom catchy CSS used */
</style>

