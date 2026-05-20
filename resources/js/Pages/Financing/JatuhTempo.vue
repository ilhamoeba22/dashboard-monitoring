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
              v-model="selectedCabang"
              :items="['Semua Cabang', ...cabangs.map(c => c.nama)]"
              label="Filter Cabang"
              variant="plain"
              density="compact"
              hide-details
              style="width: 200px"
              prepend-inner-icon="ri-store-2-line"
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
    <div class="kpi-cards-grid mb-6">
      <div class="kpi-card">
        <div class="kpi-card__accent" style="background: linear-gradient(90deg, #64748b, #94a3b8)"></div>
        <div class="kpi-card__inner">
          <div class="kpi-card__header">
            <span class="kpi-card__label">Total Antrian</span>
            <div class="kpi-card__icon bg-slate-100">
              <v-icon icon="ri-user-follow-line" size="18" color="grey-darken-1" />
            </div>
          </div>
          <div class="d-flex align-center gap-3 mt-2">
            <div class="kpi-card__value" style="font-size: 24px;">{{ totalJatuhTempo }}</div>
            <VueApexCharts type="donut" width="40" height="40" :options="ratioChartOpts" :series="ratioChartSeries" />
          </div>
          <div class="kpi-card__sub">Akun jatuh tempo bulan ini</div>
        </div>
      </div>

      <div class="kpi-card kpi-card--info">
        <div class="kpi-card__accent" style="background: linear-gradient(90deg, #3b82f6, #0ea5e9)"></div>
        <div class="kpi-card__inner">
          <div class="kpi-card__header">
            <span class="kpi-card__label text-blue-600">Proyeksi Tagihan Pokok</span>
            <div class="kpi-card__icon fin-icon-blue">
              <v-icon icon="ri-money-dollar-circle-line" size="18" />
            </div>
          </div>
          <div class="kpi-card__value mt-2">{{ store.formatShortRp(totalTagihanPokok) }}</div>
          <div class="kpi-card__sub">Estimasi total likuiditas masuk</div>
        </div>
      </div>

      <div class="kpi-card kpi-card--success">
        <div class="kpi-card__accent" style="background: linear-gradient(90deg, #10b981, #34d399)"></div>
        <div class="kpi-card__inner">
          <div class="kpi-card__header">
            <span class="kpi-card__label text-emerald-600">Saldo Cukup</span>
            <div class="kpi-card__icon fin-icon-green">
              <v-icon icon="ri-checkbox-circle-line" size="18" />
            </div>
          </div>
          <div class="kpi-card__value mt-2">{{ saldoStatus.cukup }}</div>
          <div class="kpi-card__sub text-emerald-600 font-weight-bold">Dana aman untuk autodebet</div>
        </div>
      </div>

      <div class="kpi-card kpi-card--danger">
        <div class="kpi-card__accent" style="background: linear-gradient(90deg, #e11d48, #fb7185)"></div>
        <div class="kpi-card__inner">
          <div class="kpi-card__header">
            <span class="kpi-card__label text-rose-600">Saldo Kurang / Risiko</span>
            <div class="kpi-card__icon fin-icon-red">
              <v-icon icon="ri-error-warning-line" size="18" />
            </div>
          </div>
          <div class="kpi-card__value mt-2">{{ saldoStatus.kurang }}</div>
          <div class="kpi-card__sub text-rose-600 font-weight-bold">Perlu tindak lanjut segera</div>
        </div>
      </div>
    </div>

    <!-- 3. Main Data Grid -->
    <div class="content-card">
      <div class="content-card__body pa-0">
        <div class="overflow-x-auto">
          <table class="fin-table fin-vtable">
            <thead>
              <tr>
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
