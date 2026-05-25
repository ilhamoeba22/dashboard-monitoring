<script setup>
import { ref, watch, onMounted, computed } from 'vue'
import { Head, usePage } from '@inertiajs/vue3'
import DefaultLayout from '@/layouts/default.vue'
import GrowthMixedChart from '@/components/Financing/GrowthMixedChart.vue'
import '@/assets/css/financing-shared.css'
import { formatExactRupiah } from '@/utils/money'

defineOptions({ layout: DefaultLayout })

// --- State ---
const activeTab = ref('ao')
const loading = ref(false)
const growthData = ref([])
const periods = ref([])
const activePeriodLabel = ref('...')

const tabOptions = [
  { title: 'Per AO', value: 'ao', icon: 'ri-user-follow-line' },
  { title: 'Per Produk', value: 'produk', icon: 'ri-briefcase-line' },
  { title: 'Per Cabang', value: 'cabang', icon: 'ri-bank-line' },
  { title: 'Per Segmen', value: 'segmen', icon: 'ri-pie-chart-line' },
]

// --- Computed ---
const chartSubtitle = computed(() => {
  const activeLabel = tabOptions.find(t => t.value === activeTab.value)?.title || ''
  return `Perbandingan MoM & YoY Growth (%) terhadap Total Outstanding (Agregasi ${activeLabel})`
})

// --- API ---
const fetchGrowthData = async () => {
  loading.value = true
  try {
    const response = await fetch(`/api/v1/financing/growth-trend?dimension=${activeTab.value}`)
    const json = await response.json()
    
    if (json.success) {
      growthData.value = json.data.matrix
      periods.value = json.data.periods
      activePeriodLabel.value = json.data.current_period_label
    }
  } catch (error) {
    console.error('API Error:', error)
  } finally {
    loading.value = false
  }
}

// --- Table Headers Configuration ---
const tableHeaders = computed(() => {
  const catTitle = tabOptions.find(t => t.value === activeTab.value)?.title || 'Kategori'
  
  const headers = [
    { title: catTitle, key: 'category', fixed: true, width: '220px' },
    { title: 'Jan Tahun Lalu (YoY Base)', key: 'yoy_base', align: 'end', width: '180px' },
  ]

  // Dynamic Month Column Pairs
  periods.value.forEach((p, index) => {
    const isCurrent = index === periods.value.length - 1;
    headers.push({ 
      title: `Nominal ${p.label}`, 
      key: `m${p.index}_nominal`, 
      align: 'end',
      width: '140px',
      cellProps: { class: isCurrent ? 'bg-blue-50' : '' }
    })
    headers.push({ 
      title: `Growth % (${p.label})`, 
      key: `m${p.index}_growth`, 
      align: 'center',
      width: '120px',
      cellProps: { class: isCurrent ? 'bg-blue-50' : '' }
    })
  })

  return headers
})

// --- Formatting Helpers ---
const formatCurrency = (v) => {
  return formatExactRupiah(v)
}

const getGrowthColor = (val) => {
  if (val > 0) return 'text-success'
  if (val < 0) return 'text-error'
  return 'text-slate-400'
}

const getGrowthIcon = (val) => {
  if (val > 0) return 'ri-arrow-right-up-line'
  if (val < 0) return 'ri-arrow-right-down-line'
  return 'ri-subtract-line'
}

// --- Lifecycle ---
watch(activeTab, fetchGrowthData)
onMounted(fetchGrowthData)
</script>

<template>
  <div class="fin-page px-4 pt-0">
    <Head title="Perkembangan & Pertumbuhan" />

    <!-- â”€â”€ HERO HEADER â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€ -->
    <div class="fin-hero mb-6">
      <div class="fin-hero__deco"></div>
      <div class="fin-hero__inner">
        <div class="d-flex flex-column flex-md-row justify-space-between align-start align-md-center gap-4">
          <div class="d-flex align-center gap-4">
            <div class="fin-hero__icon fin-icon-blue">
              <v-icon icon="ri-line-chart-fill" size="26" color="white" />
            </div>
            <div class="fin-hero__meta">
              <h1 class="fin-hero__title">Perkembangan Pembiayaan</h1>
              <p class="fin-hero__subtitle">Analisis Pertumbuhan Portofolio | Periode Aktif: <span class="font-weight-bold text-white">{{ activePeriodLabel }}</span></p>
            </div>
          </div>

          <!-- Segmented Navigation (Enterprise Pills) -->
          <div class="pa-1 rounded-pill d-inline-flex" style="background: rgba(255,255,255,0.1); backdrop-filter: blur(10px); min-width: 600px; border: 1px solid rgba(255,255,255,0.1);">
            <v-tabs
              v-model="activeTab"
              density="compact"
              hide-slider
              grow
              :show-arrows="false"
              class="fin-hero-tabs w-100"
            >
              <v-tab
                v-for="opt in tabOptions"
                :key="opt.value"
                :value="opt.value"
                class="text-none rounded-pill transition-all px-4"
              >
                <v-icon :icon="opt.icon" start size="16" />
                {{ opt.title }}
              </v-tab>
            </v-tabs>
          </div>
        </div>
      </div>
    </div>

    <!-- Chart Section -->
    <v-row>
      <v-col cols="12">
        <v-card elevation="0" border rounded="xl" class="analytical-card overflow-hidden">
          <v-card-item title="Visualisasi Pertumbuhan Portofolio">
            <template #subtitle>{{ chartSubtitle }}</template>
            <template #append>
              <v-btn icon="ri-refresh-line" variant="text" density="compact" @click="fetchGrowthData" :loading="loading" />
            </template>
          </v-card-item>
          <v-divider />
          <v-card-text class="pa-6 pa-lg-8">
            <GrowthMixedChart :data="growthData" :periods="periods" :loading="loading" />
          </v-card-text>
        </v-card>
      </v-col>
    </v-row>

    <!-- Matrix Table Section -->
    <v-row class="mt-4">
      <v-col cols="12">
        <v-card elevation="0" border rounded="xl">
          <v-card-item title="Growth Matrix Grid">
            <template #subtitle>Matriks perbandingan nominal dan persentase pertumbuhan bulanan (Januari - {{ activePeriodLabel.split(' ')[0] }})</template>
          </v-card-item>
          <v-divider />
          <v-data-table
            :headers="tableHeaders"
            :items="growthData"
            :loading="loading"
            density="compact"
            class="custom-table fin-vtable growth-table border rounded-lg"
            hover
          >
            <!-- Category Cell -->
            <template #[`item.category`]="{ item }">
              <div class="d-flex align-center gap-2 ps-4">
                <v-avatar size="24" color="primary-lighten-5" class="text-primary font-weight-bold text-[10px]">
                  {{ String(item.id).substring(0, 3) }}
                </v-avatar>
                <span class="font-weight-black text-slate-700 text-xs truncate" style="max-width: 160px;">{{ item.category }}</span>
              </div>
            </template>

            <!-- YoY Base Cell -->
            <template #[`item.yoy_base`]="{ item }">
              <span class="text-xs font-weight-bold text-slate-600">{{ formatCurrency(item.yoy_base) }}</span>
            </template>

            <!-- Dynamic Month Cells -->
            <template v-for="p in periods" :key="p.key" #[`item.m${p.index}_nominal`]="{ item }">
               <span class="text-xs font-medium text-slate-800">{{ formatCurrency(item[`m${p.index}_nominal`]) }}</span>
            </template>

            <template v-for="p in periods" :key="p.key + '_growth'" #[`item.m${p.index}_growth`]="{ item }">
              <div 
                v-if="item[`m${p.index}_growth`] !== null && item[`m${p.index}_growth`] !== 0"
                class="d-flex align-center justify-center gap-1 font-weight-bold text-xs"
                :class="getGrowthColor(item[`m${p.index}_growth`])"
              >
                <v-icon :icon="getGrowthIcon(item[`m${p.index}_growth`])" size="14" />
                <span>{{ Math.abs(item[`m${p.index}_growth`]).toFixed(2) }}%</span>
              </div>
              <span v-else class="text-slate-300">â€”</span>
            </template>

            <template #loading>
              <v-skeleton-loader type="table-row@10" />
            </template>
          </v-data-table>
        </v-card>
      </v-col>
    </v-row>
  </div>
</template>

<style scoped>
.perkembangan-page {
  max-width: 1600px;
  margin: 0 auto;
}

.growth-table :deep(th) {
  background-color: #f8fafc !important;
  color: #64748b !important;
  font-weight: 800 !important;
  text-transform: uppercase !important;
  font-size: 10px !important;
  letter-spacing: 0.5px;
  height: 48px !important;
  border-right: 1px solid #f1f5f9;
}

.growth-table :deep(td) {
  height: 44px !important;
  border-bottom: 1px solid #f1f5f9 !important;
  border-right: 1px solid #f8fafc;
}

.growth-table :deep(td:first-child), 
.growth-table :deep(th:first-child) {
  position: sticky !important;
  left: 0;
  z-index: 10;
  background-color: white !important;
  box-shadow: 2px 0 5px rgba(0,0,0,0.02);
  border-right: 2px solid #f1f5f9 !important;
  padding-left: 16px !important;
}

.growth-table :deep(th:first-child) {
  background-color: #f8fafc !important;
}
</style>
