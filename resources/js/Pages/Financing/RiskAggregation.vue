<script setup>
import { ref, onMounted, watch } from 'vue'
import { Head } from '@inertiajs/vue3'
import DefaultLayout from '@/layouts/default.vue'
import FinancingStats from '@/components/Financing/FinancingStats.vue'
import TrendChart from '@/components/Financing/TrendChart.vue'
import KolektibilitasProgress from '@/components/Financing/KolektibilitasProgress.vue'

defineOptions({ layout: DefaultLayout })

/**
 * Risk Aggregation - Enterprise Analytics Dashboard
 */

// State
const loading = ref(false)
const dashboardData = ref({
  summary: { total_noa: 0, total_os: 0, npf_noa: 0, npf_os: 0, npf_persen: 0, avg_kolek: 0, total_ppap: 0 },
  trend: [],
  kolektibilitas: [],
  top_npf: []
})

// Filters
const filterCabang = ref('Semua Cabang')
const cabangOptions = ref(['Semua Cabang'])

// Methods
async function fetchCabangs() {
  try {
    const response = await fetch('/api/v1/financing/cabangs')
    const json = await response.json()
    if (json.success) cabangOptions.value = ['Semua Cabang', ...json.data.map(c => c.nama.trim())]
  } catch (error) { console.error('Gagal memuat list cabang:', error) }
}

async function fetchDashboardData() {
  loading.value = true
  try {
    const params = new URLSearchParams()
    if (filterCabang.value !== 'Semua Cabang') params.append('cabang', filterCabang.value)

    const response = await fetch(`/api/v1/financing/overview?${params}`)
    const json = await response.json()

    if (json.success) {
      dashboardData.value = {
        summary: json.data.summary,
        trend: json.data.trend,
        kolektibilitas: json.data.kolektibilitas,
        top_npf: json.data.top_npf || [] 
      }
    }
  } catch (error) { console.error('Gagal memuat data risk aggregation:', error) }
  finally { loading.value = false }
}

function formatCurrency(v) {
  if (!v) return 'Rp 0'
  return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0, maximumFractionDigits: 0 }).format(v)
}

const getGrowthStyle = (val) => {
  if (val === null || val === undefined) return { backgroundColor: '#f1f5f9', color: '#94a3b8', borderColor: '#e2e8f0' }
  if (val > 0) return { backgroundColor: '#ffe4e6', color: '#be123c', borderColor: '#fecdd3' } // Emerald context in Risk usually red means NPF increase
  return { backgroundColor: '#dcfce7', color: '#15803d', borderColor: '#bbf7d0' }
}

// NOTE: Specific for Risk Aggregation, NPF increase is bad (Rose), decrease is good (Emerald)
const getNpfStatusStyle = (val) => {
    if (val > 5) return { backgroundColor: '#ffe4e6', color: '#be123c', borderColor: '#fecdd3' } // Macet style
    if (val > 2) return { backgroundColor: '#fff7ed', color: '#c2410c', borderColor: '#ffedd5' } // Warning style
    return { backgroundColor: '#dcfce7', color: '#15803d', borderColor: '#bbf7d0' } // Good style
}

watch(filterCabang, fetchDashboardData)
onMounted(() => { fetchCabangs(); fetchDashboardData(); })
</script>

<template>
  <div class="risk-aggregation-page">
    <Head title="Risk Aggregation" />

    <!-- Top Bar -->
    <div class="d-flex flex-column flex-md-row justify-space-between align-start align-md-center gap-4 mb-6">
      <div class="d-flex align-center gap-4">
        <div class="header-icon shadow-sm" style="width: 52px; height: 52px; border-radius: 14px; background: white; border: 1px solid #E2E8F0; display: flex; align-items: center; justify-content: center;">
          <v-icon icon="ri-shield-keyhole-line" color="primary" size="26" />
        </div>
        <div>
          <h1 class="text-h4 font-weight-black mb-0 text-slate-800" style="font-family: 'Plus Jakarta Sans', sans-serif;">Risk Aggregation</h1>
          <p class="text-body-2 text-medium-emphasis mb-0 font-weight-medium">Analytics Portofolio & Risk Management PT. BPRS HIK MCI</p>
        </div>
      </div>

      <div class="d-flex align-center gap-3 bg-white pa-2 rounded-xl border">
        <v-select v-model="filterCabang" :items="cabangOptions" variant="solo" density="compact" flat hide-details rounded="lg" style="min-width: 220px;" prepend-inner-icon="ri-bank-line" />
        <v-btn color="primary" variant="flat" rounded="lg" height="40" @click="fetchDashboardData" :loading="loading" prepend-icon="ri-refresh-line">Refresh</v-btn>
      </div>
    </div>

    <FinancingStats :data="dashboardData" />

    <v-row class="mt-2">
      <v-col cols="12" lg="8">
        <TrendChart :series="dashboardData.trend" :loading="loading" />
      </v-col>
      <v-col cols="12" lg="4">
        <KolektibilitasProgress :kolektibilitas="dashboardData.kolektibilitas" :totalNoa="dashboardData.summary.total_noa" class="h-100 shadow-none border" />
      </v-col>
    </v-row>

    <!-- Top NPF Grid -->
    <v-row class="mt-4">
      <v-col cols="12">
        <v-card elevation="0" border rounded="xl">
          <v-card-item title="Top 5 Alert: Risiko NPF Tertinggi">
            <template #append>
              <v-btn variant="tonal" color="primary" rounded="lg" size="small" to="/financing/nominatif">Detail Nominatif <v-icon icon="ri-arrow-right-line" end /></v-btn>
            </template>
          </v-card-item>
          <v-divider />
          <v-table density="comfortable" class="alert-grid">
            <thead>
              <tr class="bg-slate-50">
                <th class="text-uppercase text-[11px] font-weight-black py-4 ps-6">Nama Nasabah</th>
                <th class="text-uppercase text-[11px] font-weight-black py-4 text-center">No. Rekening</th>
                <th class="text-uppercase text-[11px] font-weight-black py-4 text-right">O/S Pokok</th>
                <th class="text-uppercase text-[11px] font-weight-black py-4 text-right">Tunggakan Pkk</th>
                <th class="text-uppercase text-[11px] font-weight-black py-4 text-center pe-6">Kol</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="item in dashboardData.top_npf" :key="item.nokontrak">
                <td class="ps-6 font-weight-bold text-slate-700">{{ item.nama }}</td>
                <td class="text-center font-weight-medium text-primary">{{ item.nokontrak }}</td>
                <td class="text-right font-weight-black text-slate-900">{{ formatCurrency(item.osmdlc) }}</td>
                <td class="text-right text-error font-weight-bold">{{ formatCurrency(item.tgkmdl) }}</td>
                <td class="text-center pe-6">
                  <span class="inline-block px-2 py-0.5 rounded text-[10px] font-bold border uppercase" :style="getNpfStatusStyle(parseInt(item.colbaru))">KOL {{ item.colbaru }}</span>
                </td>
              </tr>
            </tbody>
          </v-table>
        </v-card>
      </v-col>
    </v-row>
  </div>
</template>

<style scoped>
.risk-aggregation-page { max-width: 1600px; margin: 0 auto; background-color: #f8fafc; min-height: 100vh; padding-bottom: 2rem; }
.alert-grid :deep(th) { color: #64748b !important; letter-spacing: 1px; }
.alert-grid :deep(td) { font-size: 0.875rem; border-bottom: 1px solid #f1f5f9 !important; }
</style>
