<script setup>
import DefaultLayout from '@/layouts/default.vue'
import { useFinancingStore } from '@/stores/financingStore'
import SummaryCards from '@/components/Financing/SummaryCards.vue'
import { computed, onMounted } from 'vue'
import '@/assets/css/financing-shared.css'

defineOptions({ layout: DefaultLayout })

const store = useFinancingStore()

const isLoading = computed(() => store.loadingRealtime)
const hasData   = computed(() => store.realtimeData !== null)
const rtData    = computed(() => store.realtimeData || {})

// Quick Links Configuration
const quickLinks = [
  { title: 'Master Console',  icon: 'ri-bar-chart-grouped-line', color: '#4f46e5', bg: 'rgba(79,70,229,0.12)',  route: '/financing/rekapitulasi', desc: 'Analisis O/S & NOA multidimensi' },
  { title: 'Quality & Risk',  icon: 'ri-shield-keyhole-line',    color: '#e11d48', bg: 'rgba(225,29,72,0.12)', route: '/financing/quality',      desc: 'Mitigasi NPF, aging, & top exposure' },
  { title: 'Data Nominatif', icon: 'ri-list-check-3',           color: '#0d9488', bg: 'rgba(13,148,136,0.12)', route: '/financing/nominatif',    desc: 'Data rinci per rekening nasabah' },
  { title: 'Trend Portofolio',icon: 'ri-line-chart-fill',        color: '#f59e0b', bg: 'rgba(245,158,11,0.12)', route: '/financing/perkembangan', desc: 'Matriks pertumbuhan MoM & YoY' },
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

function getKolPillClass(kol) {
  const map = { '1': 'fin-pill--kol1', '2': 'fin-pill--kol2', '3': 'fin-pill--kol3', '4': 'fin-pill--kol4', '5': 'fin-pill--kol5' }
  return map[String(kol)] || 'fin-pill--neutral'
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
    chart: { type: 'donut', fontFamily: 'Inter, sans-serif' },
    labels,
    colors,
    stroke: { width: 0 },
    dataLabels: {
      enabled: true,
      formatter: val => val.toFixed(1) + '%'
    },
    plotOptions: {
      pie: {
        donut: {
          size: '72%',
          labels: {
            show: true,
            name: { show: true },
            value: { show: true, formatter: val => formatCurrency(val) },
            total: {
              show: true, showAlways: true, label: 'Total O/S',
              formatter: w => formatCurrency(w.globals.seriesTotals.reduce((a, b) => a + b, 0))
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
    const reversed = [...rtData.value.trend].reverse()
    reversed.forEach(item => {
      const year     = item.periode.toString().substring(2, 4)
      const monthNum = item.periode.toString().substring(4, 6)
      const names    = { '01': 'Jan', '02': 'Feb', '03': 'Mar', '04': 'Apr', '05': 'Mei', '06': 'Jun', '07': 'Jul', '08': 'Agt', '09': 'Sep', '10': 'Okt', '11': 'Nov', '12': 'Des' }
      categories.push(`${names[monthNum]} '${year}`)
    })
  }
  return {
    chart: { type: 'area', fontFamily: 'Inter, sans-serif', toolbar: { show: false }, zoom: { enabled: false } },
    colors: ['#059669', '#EF4444'],
    dataLabels: { enabled: false },
    stroke: { curve: 'smooth', width: 2 },
    fill: { type: 'gradient', gradient: { shadeIntensity: 1, opacityFrom: 0.35, opacityTo: 0.03, stops: [0, 90, 100] } },
    xaxis: { categories, tooltip: { enabled: false } },
    yaxis: [
      { labels: { formatter: v => formatCurrency(v) }, title: { text: 'Total O/S', style: { fontWeight: 600 } } },
      { opposite: true, labels: { formatter: v => formatCurrency(v) }, title: { text: 'Total NPF', style: { fontWeight: 600 } } }
    ],
    tooltip: { y: { formatter: val => 'Rp ' + val.toLocaleString('id-ID') } },
    legend: { position: 'top', horizontalAlign: 'right' }
  }
})

const areaChartSeries = computed(() => {
  const totalOs = [], totalNpf = []
  if (rtData.value.trend) {
    const reversed = [...rtData.value.trend].reverse()
    reversed.forEach(item => {
      totalOs.push(parseFloat(item.total_os) || 0)
      totalNpf.push(parseFloat(item.total_npf) || 0)
    })
  }
  return [
    { name: 'Total Outstanding', data: totalOs },
    { name: 'Total NPF (Macet)',  data: totalNpf }
  ]
})

onMounted(() => store.loadAll())
</script>

<template>
  <div class="fin-page px-4 pt-0">

    <!-- ── HERO HEADER ─────────────────────────────────────────── -->
    <div class="fin-hero mb-6">
      <div class="fin-hero__deco"></div>
      <div class="fin-hero__inner">
        <div class="fin-hero__top">
          <div class="fin-hero__icon">
            <v-icon icon="ri-vip-crown-line" size="26" color="white" />
          </div>
          <div class="fin-hero__meta">
            <h1 class="fin-hero__title">Executive Overview</h1>
            <p class="fin-hero__subtitle">High-level realtime intelligence — portofolio pembiayaan aktif bank syariah</p>
            <div class="fin-hero__badges">
              <span class="fin-badge fin-badge--teal">🏦 Islamic Banking</span>
              <span class="fin-badge fin-badge--glass">
                <v-icon size="10" color="white">ri-database-2-line</v-icon>
                {{ rtData?.database || 'Memuat...' }}
              </span>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- ── LOADING SKELETON ─────────────────────────────────────── -->
    <div v-if="isLoading">
      <v-row class="mb-4">
        <v-col v-for="i in 4" :key="i" cols="12" sm="6" lg="3">
          <v-skeleton-loader type="card" rounded="xl" />
        </v-col>
      </v-row>
      <v-row>
        <v-col cols="12" lg="4"><v-skeleton-loader type="image" height="350" rounded="xl" /></v-col>
        <v-col cols="12" lg="8"><v-skeleton-loader type="image" height="350" rounded="xl" /></v-col>
      </v-row>
    </div>

    <!-- ── DASHBOARD CONTENT ────────────────────────────────────── -->
    <template v-else-if="hasData">

      <!-- Quick Links Navigation -->
      <div class="mb-6">
        <div class="fin-section__label">
          <v-icon size="13" class="me-1">ri-navigation-line</v-icon>
          Navigasi Cepat
        </div>
        <v-row>
          <v-col v-for="(link, i) in quickLinks" :key="i" cols="12" sm="6" md="3">
            <a :href="link.route" style="text-decoration: none; display: block;">
              <div class="content-card quick-link-card pa-0">
                <div class="d-flex align-center gap-3 pa-4">
                  <div class="content-card__icon" :style="{ background: link.bg }">
                    <v-icon :icon="link.icon" size="22" :style="{ color: link.color }" />
                  </div>
                  <div>
                    <div class="font-weight-bold text-sm" style="font-size:14px; color:#1e293b;">{{ link.title }}</div>
                    <div class="text-xs mt-1" style="font-size:11.5px; color:#94a3b8; line-height:1.3;">{{ link.desc }}</div>
                  </div>
                </div>
              </div>
            </a>
          </v-col>
        </v-row>
      </div>

      <!-- Summary KPI Scorecards -->
      <SummaryCards :data="rtData" />

      <!-- Visual Analytics -->
      <v-row class="mt-4 mb-6">
        <!-- Donut Chart -->
        <v-col cols="12" lg="4">
          <div class="content-card h-100">
            <div class="content-card__accent-top" style="background: linear-gradient(90deg, #059669, #10b981);"></div>
            <div class="content-card__header">
              <div>
                <div class="content-card__title">Komposisi Kolektibilitas</div>
                <div class="content-card__subtitle">Distribusi kesehatan portofolio saat ini</div>
              </div>
              <div class="content-card__icon fin-icon-green">
                <v-icon icon="ri-pie-chart-2-fill" size="20" />
              </div>
            </div>
            <div class="content-card__body">
              <apexchart
                v-if="donutChartSeries.length"
                type="donut" height="300"
                :options="donutChartOptions"
                :series="donutChartSeries"
              />
              <div v-else class="fin-empty py-12">
                <v-icon icon="ri-bar-chart-2-line" size="40" class="fin-empty__icon" />
                <div class="fin-empty__desc">Data kolektibilitas tidak tersedia</div>
              </div>
            </div>
          </div>
        </v-col>

        <!-- Area Chart -->
        <v-col cols="12" lg="8">
          <div class="content-card h-100">
            <div class="content-card__accent-top" style="background: linear-gradient(90deg, #059669, #0284c7);"></div>
            <div class="content-card__header">
              <div>
                <div class="content-card__title">Pergerakan O/S & Risiko (12 Bulan)</div>
                <div class="content-card__subtitle">Tren nominal pokok vs total NPF makro</div>
              </div>
              <a href="/financing/perkembangan" style="text-decoration: none;">
                <div class="fin-badge fin-badge--glass" style="background: rgba(13,148,136,0.12); color: #0d9488; border-color: rgba(13,148,136,0.2);">
                  Detail MoM/YoY →
                </div>
              </a>
            </div>
            <div class="content-card__body">
              <apexchart
                v-if="areaChartSeries[0].data.length"
                type="area" height="300"
                :options="areaChartOptions"
                :series="areaChartSeries"
              />
              <div v-else class="fin-empty py-12">
                <div class="fin-empty__desc">Data tren tidak tersedia</div>
              </div>
            </div>
          </div>
        </v-col>
      </v-row>

      <!-- Top NPF Risk Alerts -->
      <div class="content-card content-card--dark-header">
        <div class="content-card__accent-top" style="background: linear-gradient(90deg, #e11d48, #f97316);"></div>
        <div class="content-card__header">
          <div class="d-flex align-center gap-3">
            <div class="fin-pulse-red">
              <v-icon icon="ri-alarm-warning-fill" size="26" color="#e11d48" />
            </div>
            <div>
              <div class="content-card__title">Top High-Risk Alerts (NPF)</div>
              <div class="content-card__subtitle">Prioritas penagihan hari ini — Top 5 Outstanding Macet</div>
            </div>
          </div>
          <a href="/financing/quality" style="text-decoration: none;">
            <div style="background:linear-gradient(135deg,#e11d48,#9f1239); color:white; padding:8px 18px; border-radius:100px; font-size:12px; font-weight:700; white-space:nowrap; box-shadow:0 4px 12px rgba(225,29,72,0.35);">
              Investigasi Lanjut
            </div>
          </a>
        </div>
        <div class="overflow-x-auto">
          <table class="fin-table fin-vtable" style="border-top: none;">
            <thead>
              <tr>
                <th>Nasabah Bermasalah</th>
                <th style="text-align:right;">O/S Pokok (Rp)</th>
                <th style="text-align:right;">Tunggakan Pokok (Rp)</th>
                <th style="text-align:center;">KOL</th>
              </tr>
            </thead>
            <tbody>
              <tr v-if="!rtData?.top_npf || rtData.top_npf.length === 0">
                <td colspan="4" style="text-align:center; padding:48px 16px;">
                  <v-icon icon="ri-shield-check-fill" color="success" size="40" class="mb-2 opacity-50 d-block mx-auto" />
                  <div style="color:#64748b; font-weight:600;">Tidak ada peringatan NPF tinggi</div>
                </td>
              </tr>
              <tr v-for="(item, idx) in (rtData?.top_npf || [])" :key="item.nokontrak">
                <td>
                  <div class="d-flex align-center gap-3">
                    <div class="rank-badge">{{ idx + 1 }}</div>
                    <div>
                      <div style="font-weight:700; font-size:13px; color:#1e293b;">{{ item.nama }}</div>
                      <div style="font-size:11px; color:#94a3b8; font-family:monospace;">{{ item.nokontrak }}</div>
                    </div>
                  </div>
                </td>
                <td style="text-align:right; font-weight:700; color:#1e293b;">Rp {{ formatCurrency(item.osmdlc) }}</td>
                <td style="text-align:right; font-weight:800; color:#e11d48;">Rp {{ formatCurrency(item.tgkmdl) }}</td>
                <td style="text-align:center;">
                  <span class="fin-pill" :class="getKolPillClass(item.colbaru)">{{ item.colbaru }}</span>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </template>

    <!-- Empty State -->
    <div v-if="!isLoading && !hasData" class="fin-empty mt-6">
      <v-icon icon="ri-database-warning-line" size="56" class="fin-empty__icon" />
      <div class="fin-empty__title">Data Tidak Tersedia</div>
      <div class="fin-empty__desc">Tidak ada data yang dapat dimuat dari database aktif. Periksa koneksi database SQL Server.</div>
    </div>

  </div>
</template>

<style scoped>
.fin-page { background: #f1f5f9; }

.quick-link-card {
  cursor: pointer;
  transition: transform 0.22s cubic-bezier(0.4,0,0.2,1), box-shadow 0.22s ease;
}
.quick-link-card:hover {
  transform: translateY(-4px);
  box-shadow: 0 8px 24px rgba(0,0,0,0.10) !important;
}

.rank-badge {
  width: 28px; height: 28px;
  background: #fee2e2; color: #e11d48;
  border-radius: 8px;
  display: flex; align-items: center; justify-content: center;
  font-weight: 900; font-size: 13px;
  flex-shrink: 0;
}
</style>
