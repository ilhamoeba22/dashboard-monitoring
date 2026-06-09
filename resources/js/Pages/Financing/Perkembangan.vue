<script setup>
import { ref, watch, onMounted, computed } from 'vue'
import { Head } from '@inertiajs/vue3'
import DefaultLayout from '@/layouts/default.vue'
import GrowthMixedChart from '@/components/Financing/GrowthMixedChart.vue'
import '@/assets/css/financing-shared.css'
import { formatExactRupiah, formatTruncatedPercentage } from '@/utils/money'

defineOptions({ layout: DefaultLayout })

// --- State ---
const activeTab = ref('ao')
const loading = ref(false)
const isExporting = ref(false)
const errorMessage = ref('')
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
const activeTabLabel = computed(() => tabOptions.find(t => t.value === activeTab.value)?.title || 'Dimensi')
const currentPeriod = computed(() => periods.value[periods.value.length - 1] || null)
const previousPeriod = computed(() => periods.value.length > 1 ? periods.value[periods.value.length - 2] : null)
const currentNominalKey = computed(() => currentPeriod.value ? `m${currentPeriod.value.index}_nominal` : '')
const currentGrowthKey = computed(() => currentPeriod.value ? `m${currentPeriod.value.index}_growth` : '')
const previousNominalKey = computed(() => previousPeriod.value ? `m${previousPeriod.value.index}_nominal` : '')
const totalCurrentOutstanding = computed(() => growthData.value.reduce((sum, item) => sum + Number(item[currentNominalKey.value] || 0), 0))
const totalPreviousOutstanding = computed(() => growthData.value.reduce((sum, item) => sum + Number(item[previousNominalKey.value] || 0), 0))
const aggregateGrowth = computed(() => {
  if (totalPreviousOutstanding.value <= 0) return totalCurrentOutstanding.value > 0 ? 100 : 0
  return ((totalCurrentOutstanding.value - totalPreviousOutstanding.value) / totalPreviousOutstanding.value) * 100
})
const topNominalContributor = computed(() => [...growthData.value].sort((a, b) => Number(b[currentNominalKey.value] || 0) - Number(a[currentNominalKey.value] || 0))[0] || null)
const topGrowthContributor = computed(() => [...growthData.value].filter(item => Number(item[currentNominalKey.value] || 0) > 0).sort((a, b) => Number(b[currentGrowthKey.value] || 0) - Number(a[currentGrowthKey.value] || 0))[0] || null)
const deepestContraction = computed(() => [...growthData.value].filter(item => Number(item[currentNominalKey.value] || 0) > 0).sort((a, b) => Number(a[currentGrowthKey.value] || 0) - Number(b[currentGrowthKey.value] || 0))[0] || null)
const growthInsight = computed(() => {
  if (errorMessage.value) return errorMessage.value
  if (!growthData.value.length) return 'Belum ada data pertumbuhan untuk dimensi ini.'
  if (aggregateGrowth.value >= 5) return 'Outstanding portofolio tumbuh kuat dibanding periode sebelumnya. Pastikan ekspansi tetap selaras dengan kualitas portofolio.'
  if (aggregateGrowth.value > 0) return 'Outstanding masih tumbuh positif, namun perlu pantau dimensi yang mulai melambat agar pipeline tetap sehat.'
  if (aggregateGrowth.value < 0) return 'Outstanding mengalami kontraksi. Prioritaskan analisis runoff, pelunasan, dan pipeline pencairan baru.'
  return 'Outstanding relatif stabil dibanding periode sebelumnya.'
})

// --- API ---
const fetchGrowthData = async () => {
  loading.value = true
  errorMessage.value = ''
  try {
    const response = await fetch(`/api/v1/financing/growth-trend?dimension=${activeTab.value}`)
    const json = await response.json()
    
    if (json.success) {
      growthData.value = json.data.matrix
      periods.value = json.data.periods
      activePeriodLabel.value = json.data.current_period_label
    } else {
      throw new Error(json.error || 'Gagal memuat data perkembangan.')
    }
  } catch (error) {
    console.error('API Error:', error)
    growthData.value = []
    periods.value = []
    errorMessage.value = error.message || 'Gagal memuat data perkembangan pembiayaan.'
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

const buildExportRows = () => growthData.value.map(item => {
  const row = {
    Dimensi: activeTabLabel.value,
    Kode: item.id,
    Nama: item.category,
    'YoY Base': Number(item.yoy_base || 0),
  }
  periods.value.forEach(period => {
    row[`Nominal ${period.label}`] = Number(item[`m${period.index}_nominal`] || 0)
    row[`Growth % ${period.label}`] = Number(item[`m${period.index}_growth`] || 0)
  })
  return row
})

const buildSummaryRows = () => [
  { Metrik: 'Periode Aktif', Nilai: activePeriodLabel.value },
  { Metrik: 'Dimensi', Nilai: activeTabLabel.value },
  { Metrik: 'Total Outstanding Aktif', Nilai: totalCurrentOutstanding.value },
  { Metrik: 'Total Outstanding Sebelumnya', Nilai: totalPreviousOutstanding.value },
  { Metrik: 'Aggregate Growth %', Nilai: aggregateGrowth.value },
  { Metrik: 'Kontributor Nominal Terbesar', Nilai: topNominalContributor.value?.category || '-' },
  { Metrik: 'Kontributor Growth Tertinggi', Nilai: topGrowthContributor.value?.category || '-' },
  { Metrik: 'Kontraksi Terdalam', Nilai: deepestContraction.value?.category || '-' },
]

const exportExcel = async () => {
  if (isExporting.value) return
  isExporting.value = true
  try {
    const XLSX = await import('xlsx')
    const workbook = XLSX.utils.book_new()
    XLSX.utils.book_append_sheet(workbook, XLSX.utils.json_to_sheet(buildSummaryRows()), '00 Summary')
    XLSX.utils.book_append_sheet(workbook, XLSX.utils.json_to_sheet(buildExportRows()), '01 Growth Matrix')
    XLSX.writeFile(workbook, `perkembangan-pembiayaan-${activeTab.value}-${activePeriodLabel.value.replace(/\s+/g, '-')}.xlsx`)
  } finally {
    isExporting.value = false
  }
}

const exportPdf = async () => {
  if (isExporting.value) return
  isExporting.value = true
  try {
    const { default: jsPDF } = await import('jspdf')
    await import('jspdf-autotable')
    const doc = new jsPDF({ orientation: 'landscape', unit: 'pt', format: 'a4' })
    doc.setFont('helvetica', 'bold')
    doc.setFontSize(15)
    doc.text('Perkembangan Pembiayaan', 40, 38)
    doc.setFont('helvetica', 'normal')
    doc.setFontSize(9)
    doc.text(`${activePeriodLabel.value} · ${activeTabLabel.value} · Aggregate Growth ${formatTruncatedPercentage(aggregateGrowth.value)}`, 40, 56)
    doc.autoTable({
      startY: 76,
      head: [['Kode', 'Nama', 'YoY Base', 'Outstanding Aktif', 'Growth Aktif']],
      body: growthData.value.map(item => [
        item.id,
        item.category,
        formatCurrency(item.yoy_base),
        formatCurrency(item[currentNominalKey.value]),
        formatTruncatedPercentage(item[currentGrowthKey.value]),
      ]),
      styles: { fontSize: 7, cellPadding: 4, overflow: 'linebreak' },
      headStyles: { fillColor: [15, 23, 42], textColor: 255, fontStyle: 'bold' },
      alternateRowStyles: { fillColor: [248, 250, 252] },
      margin: { left: 32, right: 32 },
    })
    const pageCount = doc.internal.getNumberOfPages()
    for (let page = 1; page <= pageCount; page += 1) {
      doc.setPage(page)
      doc.setFontSize(8)
      doc.setTextColor(100)
      doc.text(`Generated: ${new Date().toLocaleString('id-ID')}`, 32, doc.internal.pageSize.height - 18)
      doc.text(`Halaman ${page}/${pageCount}`, doc.internal.pageSize.width - 90, doc.internal.pageSize.height - 18)
    }
    doc.save(`perkembangan-pembiayaan-${activeTab.value}-${activePeriodLabel.value.replace(/\s+/g, '-')}.pdf`)
  } finally {
    isExporting.value = false
  }
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

    <!-- ── HERO HEADER ─────────────────────────────────────────── -->
    <div class="fin-hero mb-6">
      <div class="fin-hero__deco"></div>
      <div class="fin-hero__inner">
        <div class="d-flex flex-column flex-xl-row justify-space-between align-start align-xl-center gap-4">
          <div class="d-flex align-center gap-4">
            <div class="fin-hero__icon fin-icon-blue">
              <v-icon icon="ri-line-chart-fill" size="26" color="white" />
            </div>
            <div class="fin-hero__meta">
              <h1 class="fin-hero__title">Perkembangan Pembiayaan</h1>
              <p class="fin-hero__subtitle">Analisis pertumbuhan outstanding portofolio per dimensi. Periode aktif: <span class="font-weight-bold text-white">{{ activePeriodLabel }}</span></p>
            </div>
          </div>

          <div class="growth-toolbar">
            <!-- Segmented Navigation (Enterprise Pills) -->
            <div class="pa-1 rounded-pill d-inline-flex growth-tabs-wrap">
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
            <div class="growth-export-actions">
              <v-btn size="small" rounded="lg" color="success" variant="flat" :loading="isExporting" prepend-icon="ri-file-excel-2-line" @click="exportExcel">Excel</v-btn>
              <v-btn size="small" rounded="lg" color="error" variant="flat" :loading="isExporting" prepend-icon="ri-file-pdf-2-line" @click="exportPdf">PDF</v-btn>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="growth-insight-panel mb-6">
      <div class="growth-insight-card growth-insight-card--primary">
        <span>Interpretasi Growth</span>
        <strong>{{ growthInsight }}</strong>
      </div>
      <div class="growth-insight-card">
        <span>Total Outstanding Aktif</span>
        <strong>{{ formatCurrency(totalCurrentOutstanding) }}</strong>
        <small>{{ formatTruncatedPercentage(aggregateGrowth) }} vs periode sebelumnya</small>
      </div>
      <div class="growth-insight-card">
        <span>Kontributor Terbesar</span>
        <strong>{{ topNominalContributor?.category || '-' }}</strong>
        <small>{{ topNominalContributor ? formatCurrency(topNominalContributor[currentNominalKey]) : 'Tidak ada data' }}</small>
      </div>
      <div class="growth-insight-card">
        <span>Growth Tertinggi</span>
        <strong>{{ topGrowthContributor?.category || '-' }}</strong>
        <small>{{ topGrowthContributor ? formatTruncatedPercentage(topGrowthContributor[currentGrowthKey]) : 'Tidak ada data' }}</small>
      </div>
    </div>

    <v-alert
      v-if="errorMessage && !loading"
      type="error"
      variant="tonal"
      border="start"
      rounded="lg"
      class="mb-6"
    >
      {{ errorMessage }}
    </v-alert>

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
                <span>{{ formatTruncatedPercentage(item[`m${p.index}_growth`]) }}</span>
              </div>
              <span v-else class="text-slate-300">—</span>
            </template>

            <template #loading>
              <v-skeleton-loader type="table-row@10" />
            </template>

            <template #no-data>
              <div class="py-10 text-center text-slate-500">
                Tidak ada data perkembangan untuk dimensi {{ activeTabLabel }}.
              </div>
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

.growth-toolbar {
  display: flex;
  align-items: center;
  gap: 12px;
  flex-wrap: wrap;
  justify-content: flex-end;
}

.growth-tabs-wrap {
  background: rgba(255,255,255,0.1);
  backdrop-filter: blur(10px);
  min-width: 600px;
  border: 1px solid rgba(255,255,255,0.1);
}

.growth-export-actions {
  display: flex;
  align-items: center;
  gap: 8px;
}

.growth-insight-panel {
  display: grid;
  grid-template-columns: minmax(0, 1.35fr) repeat(3, minmax(190px, 0.75fr));
  gap: 16px;
}

.growth-insight-card {
  min-height: 116px;
  background: linear-gradient(145deg, #ffffff 0%, #f8fafc 100%);
  border: 1px solid #dbe7f3;
  border-radius: 20px;
  padding: 18px 20px;
  box-shadow: 0 10px 28px rgba(15, 23, 42, 0.06);
  display: flex;
  flex-direction: column;
  justify-content: center;
  gap: 7px;
}

.growth-insight-card span {
  color: #64748b;
  font-size: 11px;
  font-weight: 900;
  text-transform: uppercase;
  letter-spacing: 0.06em;
}

.growth-insight-card strong {
  color: #0f172a;
  font-family: 'Plus Jakarta Sans', sans-serif;
  font-size: 14px;
  line-height: 1.45;
}

.growth-insight-card small {
  color: #64748b;
  font-size: 12px;
  font-weight: 700;
}

.growth-insight-card--primary {
  background:
    radial-gradient(circle at top right, rgba(14, 165, 233, 0.18), transparent 34%),
    linear-gradient(145deg, #f0f9ff 0%, #ffffff 74%);
  border-color: #bae6fd;
}

.growth-insight-card--primary strong {
  color: #0369a1;
  font-size: 15px;
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

@media (max-width: 1180px) {
  .growth-insight-panel {
    grid-template-columns: 1fr 1fr;
  }

  .growth-tabs-wrap {
    min-width: 100%;
  }
}

@media (max-width: 720px) {
  .growth-toolbar,
  .growth-export-actions {
    width: 100%;
  }

  .growth-export-actions .v-btn {
    flex: 1;
  }

  .growth-insight-panel {
    grid-template-columns: 1fr;
  }
}
</style>
