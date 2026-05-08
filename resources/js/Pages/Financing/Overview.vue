<script setup>
import DefaultLayout from '@/layouts/default.vue'
import { useFinancingStore } from '@/stores/financingStore'
import SummaryCards from '@/components/Financing/SummaryCards.vue'
import { computed, onMounted } from 'vue'

defineOptions({ layout: DefaultLayout })

const store = useFinancingStore()

const isLoading = computed(() => store.loadingRealtime)
const hasData = computed(() => store.realtimeData !== null)
const rtData = computed(() => store.realtimeData || {})

// Quick Links Configuration
const quickLinks = [
  { title: 'Master Console', icon: 'ri-bar-chart-grouped-line', color: 'primary', route: '/financing/rekapitulasi', desc: 'Analisis O/S & NOA multidimensi' },
  { title: 'Quality & Risk', icon: 'ri-shield-keyhole-line', color: 'error', route: '/financing/quality', desc: 'Mitigasi NPF, aging, & top exposure' },
  { title: 'Data Nominatif', icon: 'ri-list-check-3', color: 'success', route: '/financing/nominatif', desc: 'Data rinci per rekening nasabah' },
  { title: 'Trend Portofolio', icon: 'ri-line-chart-fill', color: 'warning', route: '/financing/perkembangan', desc: 'Matriks pertumbuhan MoM & YoY' },
]

// Formatting Helpers
function formatCurrency(value) {
  if (!value && value !== 0) return '—'
  const num = parseFloat(value)
  if (isNaN(num)) return '—'
  if (Math.abs(num) >= 1e9) return `${(num / 1e9).toFixed(2)} M`
  if (Math.abs(num) >= 1e6) return `${(num / 1e6).toFixed(1)} Jt`
  return num.toLocaleString('id-ID')
}

function getKolColor(kol) {
  const colors = { '1': '#10B981', '2': '#3B82F6', '3': '#F59E0B', '4': '#F97316', '5': '#EF4444' }
  return colors[String(kol)] || '#64748B'
}

function getKolLabel(kol) {
  const labels = { '1': 'Lancar', '2': 'DPK', '3': 'Kurang Lancar', '4': 'Diragukan', '5': 'Macet' }
  return labels[String(kol)] || 'Unknown'
}

// Chart Configurations
const donutChartOptions = computed(() => {
  const labels = []
  const colors = []
  
  if (rtData.value.kolektibilitas) {
    rtData.value.kolektibilitas.forEach(item => {
      labels.push(`Kol ${item.kol} - ${getKolLabel(item.kol)}`)
      colors.push(getKolColor(item.kol))
    })
  }

  return {
    chart: { type: 'donut', fontFamily: 'Plus Jakarta Sans, sans-serif' },
    labels: labels,
    colors: colors,
    stroke: { width: 0 },
    dataLabels: {
      enabled: true,
      formatter: function (val) {
        return val.toFixed(1) + "%"
      }
    },
    plotOptions: {
      pie: {
        donut: {
          size: '70%',
          labels: {
            show: true,
            name: { show: true },
            value: {
              show: true,
              formatter: function (val) {
                return formatCurrency(val)
              }
            },
            total: {
              show: true,
              showAlways: true,
              label: 'Total O/S',
              formatter: function (w) {
                const total = w.globals.seriesTotals.reduce((a, b) => a + b, 0)
                return formatCurrency(total)
              }
            }
          }
        }
      }
    },
    legend: { position: 'bottom', fontSize: '12px', fontWeight: 600 }
  }
})

const donutChartSeries = computed(() => {
  if (!rtData.value.kolektibilitas) return []
  return rtData.value.kolektibilitas.map(item => parseFloat(item.osmdlc) || 0)
})

const areaChartOptions = computed(() => {
  const categories = []
  
  if (rtData.value.trend) {
    // API returns newest first (DESC), so we reverse to make it older to newer (Left to Right)
    const reversed = [...rtData.value.trend].reverse()
    reversed.forEach(item => {
      // Format 202312 -> Des 23
      const year = item.periode.toString().substring(2, 4)
      const monthNum = item.periode.toString().substring(4, 6)
      const monthNames = { '01': 'Jan', '02': 'Feb', '03': 'Mar', '04': 'Apr', '05': 'Mei', '06': 'Jun', '07': 'Jul', '08': 'Agt', '09': 'Sep', '10': 'Okt', '11': 'Nov', '12': 'Des' }
      categories.push(`${monthNames[monthNum]} '${year}`)
    })
  }

  return {
    chart: { type: 'area', fontFamily: 'Plus Jakarta Sans, sans-serif', toolbar: { show: false }, zoom: { enabled: false } },
    colors: ['#059669', '#EF4444'],
    dataLabels: { enabled: false },
    stroke: { curve: 'smooth', width: 2 },
    fill: {
      type: 'gradient',
      gradient: { shadeIntensity: 1, opacityFrom: 0.4, opacityTo: 0.05, stops: [0, 90, 100] }
    },
    xaxis: { categories: categories, tooltip: { enabled: false } },
    yaxis: [
      {
        labels: { formatter: (value) => formatCurrency(value) },
        title: { text: 'Total O/S', style: { fontWeight: 600 } }
      },
      {
        opposite: true,
        labels: { formatter: (value) => formatCurrency(value) },
        title: { text: 'Total NPF', style: { fontWeight: 600 } }
      }
    ],
    tooltip: {
      y: { formatter: function (val) { return 'Rp ' + val.toLocaleString('id-ID') } }
    },
    legend: { position: 'top', horizontalAlign: 'right' }
  }
})

const areaChartSeries = computed(() => {
  const totalOs = []
  const totalNpf = []
  
  if (rtData.value.trend) {
    const reversed = [...rtData.value.trend].reverse()
    reversed.forEach(item => {
      totalOs.push(parseFloat(item.total_os) || 0)
      totalNpf.push(parseFloat(item.total_npf) || 0)
    })
  }
  
  return [
    { name: 'Total Outstanding', data: totalOs },
    { name: 'Total NPF (Macet)', data: totalNpf }
  ]
})

onMounted(() => {
  store.loadAll()
})
</script>

<template>
  <div class="financing-overview">
    <!-- Page Header -->
    <div class="page-header mb-6">
      <v-row align="center" justify="space-between" no-gutters>
        <v-col cols="12" md="auto">
          <div class="d-flex align-center gap-4">
            <div class="header-icon">
              <v-icon icon="ri-vip-crown-line" size="28" color="primary" />
            </div>
            <div>
              <h1 class="text-h4 font-weight-bold mb-1">Executive Overview</h1>
              <p class="text-body-2 text-medium-emphasis mb-0">High-level realtime intelligence of financing portfolio.</p>
            </div>
          </div>
        </v-col>
        <v-col cols="12" md="auto" class="mt-4 mt-md-0">
          <div class="d-flex align-center bg-slate-100 rounded-pill px-4 py-2 border shadow-sm">
             <v-icon icon="ri-database-2-line" size="16" class="text-slate-500 me-2"></v-icon>
             <span class="text-caption font-weight-medium text-slate-700">Data Source: <strong class="text-primary">{{ rtData?.database || 'Loading...' }}</strong></span>
          </div>
        </v-col>
      </v-row>
    </div>

    <!-- Loading Skeleton -->
    <div v-if="isLoading" class="loading-skeleton">
      <v-row class="mb-4"><v-col v-for="i in 4" :key="i" cols="12" sm="6" lg="3"><v-skeleton-loader type="card" /></v-col></v-row>
      <v-row><v-col cols="12" lg="4"><v-skeleton-loader type="image" height="350" /></v-col><v-col cols="12" lg="8"><v-skeleton-loader type="image" height="350" /></v-col></v-row>
    </div>

    <!-- ======================== EXECUTIVE DASHBOARD ======================== -->
    <template v-else-if="hasData">
      
      <!-- 1. QUICK ACTIONS (Navigation Strip) -->
      <div class="mb-6">
        <v-row>
          <v-col cols="12" sm="6" md="3" v-for="(link, i) in quickLinks" :key="i">
            <a :href="link.route" style="text-decoration: none;">
              <v-card hover class="h-100 rounded-xl border border-slate-200 transition-swing shadow-sm ui-card">
                <v-card-text class="d-flex align-start gap-3 pa-4">
                  <v-avatar :color="link.color + '-lighten-5'" rounded="lg" size="48" class="border">
                    <v-icon :color="link.color" :icon="link.icon" size="24"></v-icon>
                  </v-avatar>
                  <div>
                    <div class="font-weight-bold text-slate-800 text-subtitle-1">{{ link.title }}</div>
                    <div class="text-caption text-slate-500 mt-1 lh-sm" style="line-height: 1.2;">{{ link.desc }}</div>
                  </div>
                </v-card-text>
              </v-card>
            </a>
          </v-col>
        </v-row>
      </div>

      <!-- 2. SCORECARDS (Macro Metrics) -->
      <SummaryCards :data="rtData" />
      
      <!-- 3. VISUAL ANALYTICS PILLARS -->
      <v-row class="mt-4 mb-6">
        
        <!-- Left Pillar: Donut Chart -->
        <v-col cols="12" lg="4">
          <v-card elevation="0" class="rounded-xl border border-slate-200 h-100 ui-card shadow-sm">
            <v-card-title class="pa-4 d-flex align-center bg-slate-50 border-b border-slate-100">
              <v-icon icon="ri-pie-chart-2-fill" color="primary" class="me-3" />
              <div>
                 <div class="text-subtitle-1 font-weight-bold text-slate-800">Komposisi Kolektibilitas</div>
                 <div class="text-caption text-slate-500 font-weight-regular">Distribusi kesehatan portofolio</div>
              </div>
            </v-card-title>
            <v-card-text class="pa-4 d-flex justify-center align-center" style="min-height: 320px;">
              <apexchart 
                v-if="donutChartSeries.length"
                type="donut" 
                height="320" 
                :options="donutChartOptions" 
                :series="donutChartSeries" 
              />
              <div v-else class="text-center text-slate-400 text-caption">Data tidak tersedia</div>
            </v-card-text>
          </v-card>
        </v-col>

        <!-- Right Pillar: Area Chart -->
        <v-col cols="12" lg="8">
          <v-card elevation="0" class="rounded-xl border border-slate-200 h-100 ui-card shadow-sm">
            <v-card-title class="pa-4 d-flex align-center justify-space-between bg-slate-50 border-b border-slate-100">
              <div class="d-flex align-center">
                 <v-icon icon="ri-pulse-fill" color="success" class="me-3" />
                 <div>
                    <div class="text-subtitle-1 font-weight-bold text-slate-800">Pergerakan O/S & Risiko (12 Bulan)</div>
                    <div class="text-caption text-slate-500 font-weight-regular">Tren Nominal Pokok vs NPF Makro</div>
                 </div>
              </div>
              <v-btn variant="text" color="primary" size="small" class="text-none font-weight-bold" append-icon="ri-arrow-right-s-line" href="/financing/perkembangan">
                 Detail MoM/YoY
              </v-btn>
            </v-card-title>
            <v-card-text class="pa-4">
              <apexchart 
                v-if="areaChartSeries[0].data.length"
                type="area" 
                height="320" 
                :options="areaChartOptions" 
                :series="areaChartSeries" 
              />
              <div v-else class="text-center text-slate-400 text-caption mt-10">Data tidak tersedia</div>
            </v-card-text>
          </v-card>
        </v-col>
      </v-row>

      <!-- 4. TOP RISKS ALERT (Exception Handling) -->
      <v-row>
        <v-col cols="12">
          <v-card class="rounded-xl border shadow-sm overflow-hidden border-error-lighten-3 ui-card" elevation="0">
            <v-card-title class="bg-error-lighten-5 py-4 px-5 d-flex align-center justify-space-between border-b border-error-lighten-4">
              <div class="d-flex align-center gap-3">
                 <div class="alert-icon-pulse">
                    <v-icon icon="ri-alarm-warning-fill" color="error" size="28"></v-icon>
                 </div>
                 <div>
                    <div class="text-h6 font-weight-bold text-error">Top High-Risk Alerts (NPF)</div>
                    <div class="text-caption text-error font-weight-medium">Prioritas penagihan hari ini (Top 5 Outstanding Macet)</div>
                 </div>
              </div>
              <v-btn variant="elevated" color="error" size="small" class="text-none font-weight-bold px-4" rounded="pill" href="/financing/quality">
                Investigasi Lanjut
              </v-btn>
            </v-card-title>
            
            <v-table density="comfortable" hover class="alert-table">
              <thead>
                <tr>
                  <th class="text-xs font-weight-black text-slate-500 bg-slate-50">NASABAH BERMASALAH</th>
                  <th class="text-right text-xs font-weight-black text-slate-500 bg-slate-50">O/S POKOK (Rp)</th>
                  <th class="text-right text-xs font-weight-black text-slate-500 bg-slate-50">TUNGGAKAN POKOK (Rp)</th>
                  <th class="text-center text-xs font-weight-black text-slate-500 bg-slate-50">KOL</th>
                </tr>
              </thead>
              <tbody>
                <tr v-if="!rtData?.top_npf || rtData.top_npf.length === 0">
                  <td colspan="4" class="text-center py-8">
                     <v-icon icon="ri-shield-check-fill" color="success" size="48" class="mb-2 opacity-50"></v-icon>
                     <div class="font-weight-bold text-slate-500">Tidak ada peringatan NPF tinggi</div>
                  </td>
                </tr>
                <tr v-for="(item, idx) in (rtData?.top_npf || [])" :key="item.nokontrak" v-else>
                  <td class="py-3 px-5">
                    <div class="d-flex align-center gap-3">
                       <div class="rank-badge">{{ idx + 1 }}</div>
                       <div>
                          <div class="font-weight-bold text-slate-800 text-body-2">{{ item.nama }}</div>
                          <div class="text-caption text-slate-500 font-mono">{{ item.nokontrak }}</div>
                       </div>
                    </div>
                  </td>
                  <td class="text-right font-weight-bold text-body-2 text-slate-700 px-5">Rp {{ formatCurrency(item.osmdlc) }}</td>
                  <td class="text-right text-error font-weight-black text-body-2 px-5">Rp {{ formatCurrency(item.tgkmdl) }}</td>
                  <td class="text-center px-5">
                    <v-chip size="small" :color="getKolColor(item.colbaru)" variant="elevated" class="font-weight-bold px-4">
                      {{ item.colbaru }}
                    </v-chip>
                  </td>
                </tr>
              </tbody>
            </v-table>
          </v-card>
        </v-col>
      </v-row>
    </template>

    <!-- Empty State -->
    <v-alert v-if="!isLoading && !hasData" type="warning" variant="tonal" class="mt-6" rounded="lg">
      <v-icon icon="ri-database-warning-line" size="48" class="me-4" />
      <strong>Tidak ada data tersedia dari database aktif.</strong>
    </v-alert>
  </div>
</template>

<style scoped>
.financing-overview { max-width: 100%; padding-bottom: 40px; }
.page-header { padding-bottom: 16px; border-bottom: 1px solid rgba(var(--v-border-color), 0.06); }
.header-icon {
  width: 56px; height: 56px; border-radius: 16px;
  background: linear-gradient(135deg, rgba(5,150,105,0.15) 0%, rgba(5,150,105,0.05) 100%);
  display: flex; align-items: center; justify-content: center;
  border: 1px solid rgba(5,150,105,0.2);
}

.ui-card {
   transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
   background: white;
}
.ui-card:hover {
   transform: translateY(-3px);
   box-shadow: 0 12px 24px rgba(0,0,0,0.06) !important;
}

.font-mono { font-family: 'JetBrains Mono', 'Fira Code', monospace; letter-spacing: -0.5px; }

.alert-table :deep(th) {
   height: 40px !important;
   border-bottom: 2px solid #E2E8F0 !important;
}
.alert-table :deep(td) {
   border-bottom: 1px solid #F1F5F9 !important;
}
.alert-table :deep(tr:hover td) {
   background-color: #FEF2F2 !important; /* Error lighten-5 */
}

.alert-icon-pulse {
   animation: pulse-red 2s infinite;
   border-radius: 50%;
}

@keyframes pulse-red {
   0% { box-shadow: 0 0 0 0 rgba(239, 68, 68, 0.4); }
   70% { box-shadow: 0 0 0 10px rgba(239, 68, 68, 0); }
   100% { box-shadow: 0 0 0 0 rgba(239, 68, 68, 0); }
}

.rank-badge {
   width: 28px;
   height: 28px;
   background: #FEE2E2;
   color: #EF4444;
   border-radius: 8px;
   display: flex;
   align-items: center;
   justify-content: center;
   font-weight: 900;
   font-size: 14px;
}
</style>
