<script setup>
import { ref, computed, onMounted, watch } from 'vue'
import { Head } from '@inertiajs/vue3'
import DefaultLayout from '@/layouts/default.vue'
import axios from 'axios'
import VueApexCharts from 'vue3-apexcharts'

defineOptions({ layout: DefaultLayout })

// ─── State Management ────────────────────────────────────────
const isLoading = ref(true)
const activeTab = ref('health')
const selectedDimension = ref('cabang')
const selectedCabang = ref(null)

const cabangs = ref([])
const qualityData = ref({
  aging: [],
  sektor: [],
  alerts: [],
  summary: {
    total_os: 0,
    total_npf: 0,
    total_ppap: 0,
    npf_ratio: 0,
    coverage_ratio: 0
  }
})

const dimensionOptions = [
  { title: 'Per Cabang', value: 'cabang' },
  { title: 'Per Produk', value: 'produk' },
  { title: 'Per AO', value: 'ao' },
]

// ─── Computed Properties ─────────────────────────────────────
const summary = computed(() => qualityData.value.summary)

// Chart 1: Aging Stacked Bar
const agingChartSeries = computed(() => {
  const categories = ['Lancar (0)', 'DPK 1-30', 'DPK 31-60', 'DPK 61-90', 'NPF (>90)']
  return [
    { name: 'Lancar', data: qualityData.value.aging.map(r => Number((r.aging_0 / 1e6).toFixed(0))) },
    { name: 'DPK 1-30', data: qualityData.value.aging.map(r => Number((r.aging_1_30 / 1e6).toFixed(0))) },
    { name: 'DPK 31-60', data: qualityData.value.aging.map(r => Number((r.aging_31_60 / 1e6).toFixed(0))) },
    { name: 'DPK 61-90', data: qualityData.value.aging.map(r => Number((r.aging_61_90 / 1e6).toFixed(0))) },
    { name: 'NPF (>90)', data: qualityData.value.aging.map(r => Number((r.aging_npf / 1e6).toFixed(0))) },
  ]
})

const agingChartOpts = computed(() => ({
  chart: { type: 'bar', stacked: true, stackType: '100%', toolbar: { show: false }, fontFamily: 'Plus Jakarta Sans' },
  plotOptions: { bar: { horizontal: true } },
  xaxis: { categories: qualityData.value.aging.map(r => r.label) },
  colors: ['#10B981', '#FCD34D', '#FBBF24', '#F59E0B', '#EF4444'],
  legend: { position: 'top' },
  title: { text: 'Komposisi Kualitas Aset (Dalam %)', style: { fontWeight: 600 } }
}))

// Chart 2: Sektor Ekonomi TreeMap
const sektorTreeMapSeries = computed(() => [{
  data: qualityData.value.sektor
    .map(r => ({
    x: r.label || '(Tanpa Sektor)',
    y: Number((parseFloat(r.total_os || 0) / 1e9).toFixed(3)) // Miliar
  }))
}])

const sektorTreeMapOpts = computed(() => ({
  chart: { type: 'treemap', toolbar: { show: false }, fontFamily: 'Plus Jakarta Sans' },
  title: { text: 'Konsentrasi Eksposur Modal (Outstanding) per Sektor Ekonomi (Miliar)', style: { fontWeight: 600 } },
  colors: ['#3B82F6'], // Gunakan warna biru untuk eksposur modal umum (safe)
  plotOptions: {
    treemap: {
      enableShades: true,
      shadeIntensity: 0.4,
    }
  },
  tooltip: {
    y: { formatter: (val) => `Rp ${val.toFixed(3)} M (Total O/S)` }
  }
}))

// Helpers
const formatRp = (v) => new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', maximumFractionDigits: 0 }).format(v)
const formatNumber = (v) => new Intl.NumberFormat('id-ID').format(v)

// ─── API Calls ───────────────────────────────────────────────
const fetchCabangs = async () => {
  try {
    const res = await axios.get('/api/v1/financing/cabangs')
    if (res.data.success) cabangs.value = res.data.data
  } catch (e) { console.error(e) }
}

const fetchQualityData = async () => {
  isLoading.value = true
  try {
    const res = await axios.get('/api/v1/financing/quality-analytics', {
      params: { group_by: selectedDimension.value, cabang: selectedCabang.value || '' }
    })
    if (res.data.success) qualityData.value = res.data.data
  } catch (e) { console.error(e) }
  finally { isLoading.value = false }
}

onMounted(() => { fetchCabangs(); fetchQualityData(); })
watch([selectedDimension, selectedCabang], fetchQualityData)
</script>

<template>
  <div class="quality-risk-console">
    <Head title="Quality & Risk Console" />

    <!-- TOP HEADER -->
    <div class="d-flex flex-column flex-md-row justify-space-between align-center mb-6 gap-4">
      <div>
        <div class="d-flex align-center gap-3">
          <v-icon icon="ri-shield-keyhole-line" color="primary" size="32"></v-icon>
          <h1 class="text-h4 font-weight-black text-slate-800">Quality & Risk Console</h1>
        </div>
        <p class="text-subtitle-2 text-medium-emphasis mb-0 mt-1">Kualitas aset, aging tunggakan & konsentrasi risiko &mdash; Data volume di <a href="/financing/rekapitulasi" class="text-primary font-weight-medium">Master Rekap Console</a></p>
      </div>

      <div class="d-flex gap-3 align-center bg-white pa-2 rounded-xl border shadow-sm">
        <v-select
          v-model="selectedCabang"
          :items="cabangs"
          item-title="nama"
          item-value="kdloc"
          label="Filter Cabang"
          prepend-inner-icon="ri-store-2-line"
          variant="solo"
          density="compact"
          flat hide-details
          clearable
          rounded="lg"
          style="min-width: 240px"
        ></v-select>
        <v-btn color="primary" variant="flat" rounded="lg" @click="fetchQualityData" :loading="isLoading" icon="ri-refresh-line"></v-btn>
      </div>
    </div>

    <!-- SUMMARY SCORECARDS -->
    <v-row class="mb-6">
      <v-col cols="12" md="4">
        <v-card class="rounded-xl border shadow-sm" :style="{ borderLeft: '6px solid ' + (summary.npf_ratio > 5 ? '#EF4444' : '#10B981') }">
          <v-card-text>
            <div class="text-overline text-medium-emphasis">NPF RATIO (GROSS)</div>
            <div class="text-h3 font-weight-black mb-1">{{ summary.npf_ratio }}%</div>
            <div class="d-flex align-center gap-2">
              <v-chip size="x-small" :color="summary.npf_ratio > 5 ? 'error' : 'success'" variant="tonal" class="font-weight-bold">
                {{ summary.npf_ratio > 5 ? 'OVER LIMIT' : 'HEALTHY' }}
              </v-chip>
              <span class="text-caption text-medium-emphasis">Limit OJK: 5.00%</span>
            </div>
          </v-card-text>
        </v-card>
      </v-col>

      <v-col cols="12" md="4">
        <v-card class="rounded-xl border shadow-sm" style="borderLeft: 6px solid #3B82F6">
          <v-card-text>
            <div class="text-overline text-medium-emphasis">COVERAGE RATIO</div>
            <div class="text-h3 font-weight-black mb-1">{{ summary.coverage_ratio }}%</div>
            <div class="text-caption text-medium-emphasis">PPAP: {{ formatRp(summary.total_ppap) }}</div>
          </v-card-text>
        </v-card>
      </v-col>

      <v-col cols="12" md="4">
        <v-card class="rounded-xl border shadow-sm" style="borderLeft: 6px solid #F59E0B">
          <v-card-text>
            <div class="text-overline text-medium-emphasis">TOTAL NPF EXPOSURE</div>
            <div class="text-h4 font-weight-black mb-1">{{ formatRp(summary.total_npf) }}</div>
            <div class="text-caption text-medium-emphasis">Kolektibilitas 3, 4, dan 5</div>
          </v-card-text>
        </v-card>
      </v-col>
    </v-row>

    <!-- MAIN CONSOLE WITH TABS -->
    <v-card class="rounded-xl border shadow-sm overflow-hidden">
      <v-tabs v-model="activeTab" bg-color="grey-lighten-4" color="primary" grow>
        <v-tab value="health" prepend-icon="ri-heart-pulse-line">Portfolio Health</v-tab>
        <v-tab value="exposure" prepend-icon="ri-radar-line">Risk Exposure</v-tab>
        <v-tab value="alerts" prepend-icon="ri-error-warning-line">Risk Alerts</v-tab>
      </v-tabs>

      <v-divider></v-divider>

      <v-window v-model="activeTab" class="pa-6">
        <!-- TAB 1: PORTFOLIO HEALTH (AGING) -->
        <v-window-item value="health">
          <div class="d-flex justify-space-between align-center mb-6">
            <h3 class="text-h6 font-weight-bold">Analisis Penuaan Portofolio (Aging)</h3>
            <v-btn-toggle v-model="selectedDimension" mandatory rounded="lg" density="compact" class="border">
              <v-btn v-for="opt in dimensionOptions" :key="opt.value" :value="opt.value" size="small">{{ opt.title }}</v-btn>
            </v-btn-toggle>
          </div>
          
          <v-row>
            <v-col cols="12" lg="8">
              <div v-if="!isLoading">
                <VueApexCharts height="450" :options="agingChartOpts" :series="agingChartSeries" />
              </div>
            </v-col>
            <v-col cols="12" lg="4">
              <v-table density="comfortable" class="border rounded-lg">
                <thead>
                  <tr class="bg-grey-lighten-4">
                    <th class="text-caption font-weight-black">AGING BUCKET</th>
                    <th class="text-right text-caption font-weight-black">NOMINAL O/S</th>
                  </tr>
                </thead>
                <tbody>
                  <tr><td>Lancar (0 Hari)</td><td class="text-right font-weight-bold">{{ formatRp(qualityData.aging.reduce((a,b) => a+parseFloat(b.aging_0 || 0), 0)) }}</td></tr>
                  <tr><td>DPK (1-30 Hari)</td><td class="text-right font-weight-bold text-warning">{{ formatRp(qualityData.aging.reduce((a,b) => a+parseFloat(b.aging_1_30 || 0), 0)) }}</td></tr>
                  <tr><td>DPK (31-60 Hari)</td><td class="text-right font-weight-bold text-orange-darken-2">{{ formatRp(qualityData.aging.reduce((a,b) => a+parseFloat(b.aging_31_60 || 0), 0)) }}</td></tr>
                  <tr><td>DPK (61-90 Hari)</td><td class="text-right font-weight-bold text-deep-orange-accent-4">{{ formatRp(qualityData.aging.reduce((a,b) => a+parseFloat(b.aging_61_90 || 0), 0)) }}</td></tr>
                  <tr class="bg-red-lighten-5"><td>NPF (>90 Hari)</td><td class="text-right font-weight-bold text-error">{{ formatRp(qualityData.aging.reduce((a,b) => a+parseFloat(b.aging_npf || 0), 0)) }}</td></tr>
                </tbody>
              </v-table>
            </v-col>
          </v-row>
        </v-window-item>

        <!-- TAB 2: RISK EXPOSURE (TREEMAP) -->
        <v-window-item value="exposure">
          <v-row>
            <v-col cols="12" lg="8">
              <div v-if="!isLoading">
                <VueApexCharts type="treemap" height="500" :options="sektorTreeMapOpts" :series="sektorTreeMapSeries" />
              </div>
            </v-col>

            <v-col cols="12" lg="4">
              <div class="pa-5 bg-slate-50 rounded-xl border border-dashed mb-6">
                <div class="d-flex align-center gap-2 mb-3">
                  <v-icon icon="ri-information-line" color="primary"></v-icon>
                  <span class="font-weight-bold text-primary">Interpretasi Exposure</span>
                </div>
                <p class="text-caption text-medium-emphasis leading-relaxed mb-0">
                  Visualisasi di samping menunjukkan konsentrasi pembiayaan berdasarkan <strong>Sektor Ekonomi</strong>. 
                  Semakin besar kotak, semakin tinggi eksposur modal bank pada sektor tersebut. Kotak yang dominan menandakan ketergantungan portofolio pada sektor spesifik.
                </p>
              </div>

              <div class="text-overline font-weight-black text-medium-emphasis mb-2 px-1">Ringkasan Sektor (Top Exposure)</div>
              <v-list lines="two" class="border rounded-xl bg-transparent pa-0 overflow-hidden">
                <template v-for="(s, idx) in qualityData.sektor.slice(0, 8)" :key="idx">
                  <v-list-item>
                    <template #prepend>
                      <v-avatar color="primary-lighten-5" rounded="lg" size="32" class="mr-1">
                        <span class="text-[10px] font-weight-black text-primary">{{ idx + 1 }}</span>
                      </v-avatar>
                    </template>
                    <v-list-item-title class="text-caption font-weight-bold">{{ s.label || 'Lainnya' }}</v-list-item-title>
                    <v-list-item-subtitle class="text-[10px] d-flex justify-space-between mt-1">
                      <span>OS: {{ formatRp(s.total_os) }}</span>
                      <span :class="parseFloat(s.npf_os) > 0 ? 'text-error font-weight-bold' : ''">
                        NPF: {{ ((parseFloat(s.npf_os)/parseFloat(s.total_os))*100).toFixed(2) }}%
                      </span>
                    </v-list-item-subtitle>
                  </v-list-item>
                  <v-divider v-if="idx < 7 && idx < qualityData.sektor.length - 1"></v-divider>
                </template>
              </v-list>
            </v-col>
          </v-row>
        </v-window-item>

        <!-- TAB 3: RISK ALERTS (TOP 10) -->
        <v-window-item value="alerts">
          <div class="d-flex align-center gap-2 mb-6">
            <v-avatar color="error-lighten-4" size="32">
              <v-icon color="error" icon="ri-alarm-warning-line" size="small"></v-icon>
            </v-avatar>
            <h3 class="text-h6 font-weight-bold">Top 10 High-Risk Potential</h3>
          </div>

          <v-table hover density="comfortable" class="border rounded-lg overflow-hidden">
            <thead>
              <tr class="bg-grey-lighten-4">
                <th class="text-uppercase text-caption font-weight-black">Nama Nasabah</th>
                <th class="text-center text-uppercase text-caption font-weight-black">No. Kontrak</th>
                <th class="text-right text-uppercase text-caption font-weight-black">O/S Pokok</th>
                <th class="text-right text-uppercase text-caption font-weight-black">Tunggakan Pkk</th>
                <th class="text-center text-uppercase text-caption font-weight-black">Hari Tgk</th>
                <th class="text-center text-uppercase text-caption font-weight-black">Kol</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="item in qualityData.alerts" :key="item.nokontrak">
                <td class="font-weight-bold">{{ item.nama }}</td>
                <td class="text-center font-mono">{{ item.nokontrak }}</td>
                <td class="text-right">{{ formatRp(item.osmdlc) }}</td>
                <td class="text-right text-error font-weight-bold">{{ formatRp(item.tgkmdl) }}</td>
                <td class="text-center"><v-chip size="x-small" color="secondary" variant="flat">{{ item.haritgk }} Hari</v-chip></td>
                <td class="text-center">
                  <v-avatar size="24" :color="Number(item.coll) > 2 ? 'error' : 'warning'" class="text-[10px] font-weight-black text-white">
                    {{ item.coll }}
                  </v-avatar>
                </td>
              </tr>
            </tbody>
          </v-table>
        </v-window-item>
      </v-window>
    </v-card>
  </div>
</template>

<style scoped>
.gap-2 { gap: 8px; }
.gap-3 { gap: 12px; }
.gap-4 { gap: 16px; }
.v-tab { text-transform: none !important; font-weight: 600 !important; letter-spacing: 0 !important; }
</style>
