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
  collMonitoringData,
  loadingCollMonitoring,
  selectedCabang,
  totalCollMonitoring,
  totalOsProyeksiBerisiko
} = storeToRefs(store)

const cabangs = ref([])

// ─── Filter State ─────────────────────────────────────────────
const selectedAO = ref('Semua AO')
const selectedKolCurr = ref('Semua')
const selectedKolEom = ref('Semua')
const selectedTrend = ref('Semua')

const aoOptions = computed(() => {
  const aos = collMonitoringData.value.map(item => item.Nama_AO).filter(Boolean)
  return ['Semua AO', ...new Set(aos)].sort()
})

// ─── Filter Engine (Intelligence Engine) ───────────────────────
const filteredData = computed(() => {
  return collMonitoringData.value.filter(item => {
    const matchAO = selectedAO.value === 'Semua AO' || item.Nama_AO === selectedAO.value
    const matchKolCurr = selectedKolCurr.value === 'Semua' || String(item.colbaru_final_curr) === selectedKolCurr.value
    const matchKolEom = selectedKolEom.value === 'Semua' || String(item.colbaru_final_eom) === selectedKolEom.value
    
    let matchTrend = true
    const curr = parseInt(item.colbaru_final_curr || 0)
    const eom = parseInt(item.colbaru_final_eom || 0)
    
    if (selectedTrend.value === 'Memburuk') matchTrend = eom > curr
    else if (selectedTrend.value === 'Membaik') matchTrend = eom < curr
    else if (selectedTrend.value === 'Tetap') matchTrend = eom === curr
    
    return matchAO && matchKolCurr && matchKolEom && matchTrend
  })
})

// ─── Pagination Logic ─────────────────────────────────────────
const currentPage = ref(1)
const itemsPerPage = ref(15)

const totalPages = computed(() => Math.ceil(filteredData.value.length / itemsPerPage.value))

const paginatedCollMonitoring = computed(() => {
  const start = (currentPage.value - 1) * itemsPerPage.value
  return filteredData.value.slice(start, start + itemsPerPage.value)
})

watch([collMonitoringData, selectedAO, selectedKolCurr, selectedKolEom, selectedTrend], () => {
  currentPage.value = 1
})

// ─── Analytics: Downgrade Stats ───────────────────────────────
const downgradeStats = computed(() => {
  const counts = { '1_2': 0, '2_3': 0, '3_4': 0, '4_5': 0, 'jump': 0 }
  
  filteredData.value.forEach(item => {
    const curr = parseInt(item.colbaru_final_curr || 0)
    const eom = parseInt(item.colbaru_final_eom || 0)
    
    if (eom > curr) {
      if (eom === curr + 1) {
        if (curr === 1) counts['1_2']++
        else if (curr === 2) counts['2_3']++
        else if (curr === 3) counts['3_4']++
        else if (curr === 4) counts['4_5']++
      } else {
        counts['jump']++
      }
    }
  })
  
  const series = [counts['1_2'], counts['2_3'], counts['3_4'], counts['4_5'], counts['jump']]
  const total = series.reduce((a, b) => a + b, 0)
  
  const chartOptions = {
    chart: { type: 'donut', sparkline: { enabled: false } },
    labels: ['1 ➔ 2', '2 ➔ 3', '3 ➔ 4', '4 ➔ 5', 'Lompat Kol'],
    colors: ['#FBBF24', '#F59E0B', '#EA580C', '#DC2626', '#991B1B'],
    dataLabels: { enabled: false },
    legend: { position: 'right', fontSize: '10px', markers: { radius: 2 } },
    plotOptions: { pie: { donut: { size: '70%' } } },
    stroke: { width: 0 },
    tooltip: { enabled: true }
  }
  
  return { series, chartOptions, total }
})

const fetchCabangs = async () => {
  try {
    const res = await fetch('/api/v1/financing/cabangs')
    const json = await res.json()
    if (json.success) cabangs.value = json.data
  } catch (e) { console.error(e) }
}

// ─── Helpers ─────────────────────────────────────────────────
const getColColor = (col) => {
  const num = parseInt(col)
  if (num === 1) return 'success'
  if (num === 2) return 'info'
  if (num === 3) return 'warning'
  if (num === 4) return 'deep-orange'
  return 'error'
}

const getAlertConfig = (detail) => {
  const text = detail || ''
  if (text.includes('Sudah Membentuk PPKA') || text.includes('PPKA')) {
    return { icon: 'ri-alert-fill', color: 'error', label: 'PPKA Terbentuk' }
  }
  if (text.includes('Jangan Sampai Membentuk PPKA')) {
    return { icon: 'ri-alarm-warning-fill', color: 'warning', label: 'Peringatan PPKA' }
  }
  if (text.includes('Kembalikan ke Kolektibilitas 1') || text.includes('Maintain ke Coll 1')) {
    return { icon: 'ri-information-fill', color: 'info', label: 'Maintain Coll 1' }
  }
  if (text.includes('Posisi Aman') || text.includes('Kondisi Fasilitas Normal') || text.includes('Pertahankan')) {
    return { icon: 'ri-checkbox-circle-fill', color: 'success', label: 'Aman' }
  }
  return { icon: 'ri-eye-line', color: 'slate', label: 'Monitor' }
}

const getRollRateIcon = (curr, eom) => {
  const c = parseInt(curr)
  const e = parseInt(eom)
  if (e > c) return { icon: 'ri-arrow-right-down-line', color: 'error' }
  if (e < c) return { icon: 'ri-arrow-right-up-line', color: 'success' }
  return { icon: 'ri-arrow-right-line', color: 'medium-emphasis' }
}

onMounted(() => {
  fetchCabangs()
  store.fetchCollMonitoring()
})

watch(selectedCabang, () => store.fetchCollMonitoring())
</script>

<template>
  <div class="fin-page px-4 pt-0">
    <Head title="Coll Monitoring EOM" />

    <!-- ── HERO HEADER ─────────────────────────────────────────── -->
    <div class="fin-hero mb-6">
      <div class="fin-hero__deco"></div>
      <div class="fin-hero__inner">
        <div class="d-flex flex-column flex-md-row justify-space-between align-start align-md-center gap-4">
          <div class="d-flex align-center gap-4">
            <div class="fin-hero__icon fin-icon-purple">
              <v-icon icon="ri-radar-line" size="26" color="white" />
            </div>
            <div class="fin-hero__meta">
              <h1 class="fin-hero__title">Coll Monitoring <span class="text-white">EOM</span></h1>
              <p class="fin-hero__subtitle">Intelligence Control Center: Proyeksi pergerakan kolektibilitas akhir bulan.</p>
              <div class="fin-hero__badges">
                <span class="fin-badge fin-badge--warning">🎯 Proyeksi Akhir Bulan</span>
              </div>
            </div>
          </div>
          
          <div class="fin-filter-bar">
            <v-select
              v-model="selectedCabang"
              :items="['Semua Cabang', ...cabangs.map(c => c.nama)]"
              label="Operational Unit"
              variant="plain"
              density="compact"
              hide-details
              style="width: 220px"
              prepend-inner-icon="ri-bank-line"
            ></v-select>
            
            <div style="width: 1px; height: 24px; background: rgba(255,255,255,0.2);" class="mx-1"></div>
            
            <v-btn 
              variant="text" 
              density="comfortable"
              @click="store.fetchCollMonitoring" 
              :loading="loadingCollMonitoring"
              icon="ri-refresh-line"
              color="white"
            ></v-btn>
          </div>
        </div>
      </div>
    </div>

    <!-- 2. Intelligence Scorecards -->
    <div class="kpi-cards-grid mb-6">
      <div class="kpi-card">
        <div class="kpi-card__accent" style="background: linear-gradient(90deg, #1e40af, #3b82f6)"></div>
        <div class="kpi-card__inner">
          <div class="kpi-card__header">
            <span class="kpi-card__label">Akun Dipantau</span>
            <div class="kpi-card__icon fin-icon-blue">
              <v-icon icon="ri-radar-line" size="18" />
            </div>
          </div>
          <div class="kpi-card__value mt-2">{{ filteredData.length }}</div>
          <div class="kpi-card__sub">Fasilitas aktif dalam kriteria filter saat ini</div>
        </div>
      </div>

      <div class="kpi-card kpi-card--danger">
        <div class="kpi-card__accent" style="background: linear-gradient(90deg, #e11d48, #fb7185)"></div>
        <div class="kpi-card__inner">
          <div class="kpi-card__header">
            <span class="kpi-card__label text-rose-600">O/S Proyeksi NPF (Kol ≥ 3)</span>
            <div class="kpi-card__icon fin-icon-red">
              <v-icon icon="ri-alarm-warning-line" size="18" />
            </div>
          </div>
          <div class="kpi-card__value mt-2 text-rose-600">
            {{ store.formatShortRp(filteredData.filter(i => parseInt(i.colbaru_final_eom) >= 3).reduce((a, b) => a + parseFloat(b.osmdlc || 0), 0)) }}
          </div>
          <div class="kpi-card__sub text-rose-600 font-weight-bold">Estimasi potensi PPKA dari data terfilter</div>
        </div>
      </div>

      <div class="kpi-card">
        <div class="kpi-card__accent" style="background: linear-gradient(90deg, #d97706, #fbbf24)"></div>
        <div class="kpi-card__inner">
          <div class="kpi-card__header">
            <span class="kpi-card__label">Distribusi Downgrade</span>
          </div>
          <div v-if="downgradeStats.total === 0" class="d-flex flex-column align-center justify-center py-2">
            <v-icon icon="ri-checkbox-circle-fill" color="success" size="24" class="mb-1"></v-icon>
            <div class="text-caption text-success font-weight-bold text-uppercase">Data Sangat Aman</div>
          </div>
          <div v-else class="d-flex align-center mt-2">
            <VueApexCharts type="donut" width="100%" height="80" :options="downgradeStats.chartOptions" :series="downgradeStats.series" />
          </div>
        </div>
      </div>
    </div>

    <!-- 2.5 Advanced Filter Bar -->
    <v-card elevation="0" class="mb-6 rounded-xl border bg-white overflow-hidden shadow-sm">
      <div class="d-flex flex-wrap gap-3 pa-4">
        <v-select
          v-model="selectedAO"
          :items="aoOptions"
          label="Account Officer"
          density="compact"
          variant="outlined"
          hide-details
          rounded="lg"
          class="flex-grow-1 flex-shrink-0"
          style="min-width: 200px"
          prepend-inner-icon="ri-user-settings-line"
        ></v-select>

        <v-select
          v-model="selectedKolCurr"
          :items="['Semua', '1', '2', '3', '4', '5']"
          label="Kol Hari Ini"
          density="compact"
          variant="outlined"
          hide-details
          rounded="lg"
          class="flex-shrink-0"
          style="min-width: 150px"
        ></v-select>

        <v-select
          v-model="selectedKolEom"
          :items="['Semua', '1', '2', '3', '4', '5']"
          label="Proyeksi EOM"
          density="compact"
          variant="outlined"
          hide-details
          rounded="lg"
          class="flex-shrink-0"
          style="min-width: 150px"
        ></v-select>

        <v-select
          v-model="selectedTrend"
          :items="['Semua', 'Memburuk', 'Tetap', 'Membaik']"
          label="Trend Roll-Rate"
          density="compact"
          variant="outlined"
          hide-details
          rounded="lg"
          class="flex-shrink-0"
          style="min-width: 180px"
          prepend-inner-icon="ri-line-chart-line"
        ></v-select>
      </div>
    </v-card>

    <!-- 3. Main Data Grid -->
    <div class="content-card">
      <div class="content-card__header">
        <div>
          <div class="content-card__title">Proyeksi Kolektibilitas End of Month</div>
          <div class="content-card__subtitle">Rincian pergerakan fasilitas per akun</div>
        </div>
        <div class="d-flex align-center gap-2">
          <v-chip size="x-small" color="primary" variant="flat" class="font-weight-black">REALTIME SNAPSHOT</v-chip>
        </div>
      </div>

      <div class="content-card__body pa-0">
        <div class="overflow-x-auto">
          <table class="fin-table fin-vtable enterprise-table">
            <thead>
              <tr>
                <th class="text-center font-weight-black text-caption" style="width: 60px">NO</th>
                <th class="text-left font-weight-black text-caption">NASABAH & KONTRAK</th>
                <th class="text-right font-weight-black text-caption">O/S POKOK</th>
                <th class="text-center font-weight-black text-caption">EST. HARI TGK</th>
                <th class="text-center font-weight-black text-caption">ROLL-RATE (TODAY ➔ EOM)</th>
                <th class="text-left font-weight-black text-caption">ACTION INTELLIGENCE</th>
                <th class="text-left font-weight-black text-caption">OFFICER</th>
              </tr>
            </thead>

        <tbody v-if="!loadingCollMonitoring">
          <tr v-for="(item, index) in paginatedCollMonitoring" :key="item.nokontrak">
            <td class="text-center text-medium-emphasis font-weight-bold">
              {{ (currentPage - 1) * itemsPerPage + index + 1 }}
            </td>
            <td class="py-4">
              <div class="font-weight-black text-uppercase text-subtitle-2 mb-0" style="letter-spacing: 0.5px">{{ item.nama }}</div>
              <div class="text-caption text-primary font-weight-black">{{ item.nokontrak }}</div>
            </td>
            <td class="text-right font-weight-black">
              {{ store.formatShortRp(item.osmdlc) }}
            </td>
            <td class="text-center">
              <v-chip size="small" variant="tonal" class="font-weight-black">{{ item.Hari_TGK_EOM_Real }}</v-chip>
            </td>
            <td class="text-center">
              <div class="d-flex align-center justify-center gap-3">
                <v-avatar :color="getColColor(item.colbaru_final_curr)" size="32" variant="tonal" class="font-weight-black">
                  {{ item.colbaru_final_curr }}
                </v-avatar>
                
                <v-icon 
                  :icon="getRollRateIcon(item.colbaru_final_curr, item.colbaru_final_eom).icon"
                  :color="getRollRateIcon(item.colbaru_final_curr, item.colbaru_final_eom).color"
                  size="20"
                ></v-icon>

                <v-avatar :color="getColColor(item.colbaru_final_eom)" size="36" variant="elevated" elevation="2" class="font-weight-black text-white">
                  {{ item.colbaru_final_eom }}
                </v-avatar>
              </div>
            </td>
            <td class="py-3" style="min-width: 280px">
              <div 
                :class="`bg-${getAlertConfig(item.Keterangan_EOM_Detail).color}-lighten-5 text-${getAlertConfig(item.Keterangan_EOM_Detail).color}`"
                class="pa-3 rounded-lg d-flex align-start gap-2 border"
                style="border-style: dashed !important"
              >
                <v-icon :icon="getAlertConfig(item.Keterangan_EOM_Detail).icon" size="20"></v-icon>
                <div>
                  <div class="text-caption font-weight-black text-uppercase leading-tight mb-1">
                    {{ getAlertConfig(item.Keterangan_EOM_Detail).label }}
                  </div>
                  <div class="text-caption font-weight-medium opacity-90 line-clamp-2" style="line-height: 1.2">
                    {{ item.Keterangan_EOM_Detail?.split('|')[0] }}
                  </div>
                </div>
              </div>
            </td>
            <td class="py-3">
              <div class="text-caption font-weight-black text-uppercase">{{ item.Nama_AO }}</div>
              <div class="text-caption text-disabled font-weight-bold">{{ item.Nama_Kantor_Cabang }}</div>
            </td>
          </tr>
        </tbody>
      </table>
      </div>

      <!-- Loading State -->
      <div v-if="loadingCollMonitoring" class="fin-loading">
        <v-progress-circular indeterminate color="primary" size="48"></v-progress-circular>
      </div>

      <div v-else-if="filteredData.length === 0" class="text-center pa-16">
        <v-avatar color="slate-lighten-5" size="100" class="mb-4">
          <v-icon icon="ri-radar-line" size="48" color="slate-lighten-2"></v-icon>
        </v-avatar>
        <div class="text-h6 font-weight-bold text-slate-lighten-1">Tidak ada data sesuai kriteria filter.</div>
        <p class="text-subtitle-2 text-disabled">Sesuaikan filter atau reset pencarian.</p>
      </div>

      <!-- Pagination Footer -->
      <v-divider v-if="filteredData.length > 0"></v-divider>
      <div v-if="filteredData.length > 0" class="pa-4 d-flex align-center justify-space-between bg-slate-50">
        <div class="text-caption text-medium-emphasis font-weight-bold">
          Menampilkan {{ (currentPage - 1) * itemsPerPage + 1 }} - {{ Math.min(currentPage * itemsPerPage, filteredData.length) }} dari {{ filteredData.length }} data
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
.enterprise-table :deep(th) {
  height: 50px !important;
  letter-spacing: 0.5px !important;
}
.enterprise-table :deep(td) {
  height: 70px !important;
}
.line-clamp-2 {
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}
</style>
