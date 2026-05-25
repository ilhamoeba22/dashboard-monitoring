<script setup>
import { ref, computed } from 'vue'
import DefaultLayout from '@/layouts/default.vue'
import '@/assets/css/cif-shared.css'

defineOptions({ layout: DefaultLayout })

const groupBy = ref('Cabang')
const groupByOptions = ['Cabang', 'Wilayah', 'AO', 'Segmen']

// Mock Data generation based on selected Group By
const mockDataMap = {
  'Cabang': [
    { label: 'Cabang Utama', individu: 1250, badanHukum: 45 },
    { label: 'Cabang Sudirman', individu: 850, badanHukum: 120 },
    { label: 'Cabang Thamrin', individu: 920, badanHukum: 80 },
    { label: 'Cabang Kebon Jeruk', individu: 640, badanHukum: 15 },
    { label: 'Cabang BSD', individu: 1100, badanHukum: 30 },
  ],
  'Wilayah': [
    { label: 'Jabodetabek', individu: 4760, badanHukum: 290 },
    { label: 'Jawa Barat', individu: 3200, badanHukum: 110 },
    { label: 'Jawa Tengah', individu: 2100, badanHukum: 50 },
    { label: 'Jawa Timur', individu: 2800, badanHukum: 85 },
  ],
  'AO': [
    { label: 'Budi Santoso', individu: 450, badanHukum: 12 },
    { label: 'Andi Pratama', individu: 380, badanHukum: 8 },
    { label: 'Siti Aminah', individu: 510, badanHukum: 25 },
    { label: 'Rina Wijaya', individu: 410, badanHukum: 15 },
    { label: 'Dewi Lestari', individu: 620, badanHukum: 40 },
  ],
  'Segmen': [
    { label: 'Mikro', individu: 3500, badanHukum: 0 },
    { label: 'Kecil', individu: 1800, badanHukum: 150 },
    { label: 'Menengah', individu: 450, badanHukum: 300 },
    { label: 'Korporasi', individu: 50, badanHukum: 120 },
  ]
}

const tableData = computed(() => {
  const data = mockDataMap[groupBy.value] || []
  return data.map(item => ({
    ...item,
    total: item.individu + item.badanHukum
  }))
})

// Calculate totals for footer
const totalIndividu = computed(() => tableData.value.reduce((sum, item) => sum + item.individu, 0))
const totalBadanHukum = computed(() => tableData.value.reduce((sum, item) => sum + item.badanHukum, 0))
const totalKeseluruhan = computed(() => tableData.value.reduce((sum, item) => sum + item.total, 0))

const headers = computed(() => [
  { title: `NAMA ${groupBy.value.toUpperCase()}`, key: 'label', align: 'left', width: '30%' },
  { title: 'NASABAH INDIVIDU', key: 'individu', align: 'end', width: '20%' },
  { title: 'NASABAH BADAN HUKUM', key: 'badanHukum', align: 'end', width: '25%' },
  { title: 'TOTAL KESELURUHAN', key: 'total', align: 'end', width: '25%' }
])

function formatNumber(val) {
  return val.toLocaleString('id-ID')
}

// Chart Configuration
const chartOptions = computed(() => {
  const categories = tableData.value.map(item => item.label)
  return {
    chart: { type: 'bar', fontFamily: 'Inter, sans-serif', toolbar: { show: false } },
    colors: ['#10b981', '#f59e0b'],
    plotOptions: {
      bar: { horizontal: false, columnWidth: '45%', borderRadius: 4, dataLabels: { position: 'top' } }
    },
    dataLabels: {
      enabled: true,
      formatter: (val) => formatNumber(val),
      offsetY: -20,
      style: { fontSize: '11px', colors: ['#64748b'] }
    },
    stroke: { show: true, width: 2, colors: ['transparent'] },
    xaxis: { categories, axisBorder: { show: false }, axisTicks: { show: false } },
    yaxis: { labels: { formatter: (val) => formatNumber(val) } },
    fill: { opacity: 1 },
    legend: { position: 'top', horizontalAlign: 'right' },
    tooltip: {
      y: { formatter: (val) => formatNumber(val) + " Nasabah" }
    }
  }
})

const chartSeries = computed(() => {
  return [
    { name: 'Individu', data: tableData.value.map(item => item.individu) },
    { name: 'Badan Hukum', data: tableData.value.map(item => item.badanHukum) }
  ]
})
</script>

<template>
  <div class="cif-page px-4 pt-0">
    <!-- HERO HEADER -->
    <div class="cif-hero mb-6">
      <div class="cif-hero__deco"></div>
      <div class="cif-hero__inner">
        <div class="cif-hero__top">
          <div class="cif-hero__icon">
            <v-icon icon="ri-bar-chart-grouped-line" size="26" color="white" />
          </div>
          <div class="cif-hero__meta">
            <h1 class="cif-hero__title">Rekapitulasi Analitik CIF</h1>
            <p class="cif-hero__subtitle">Agregasi dan distribusi data nasabah berdasarkan parameter operasional.</p>
          </div>
        </div>
      </div>
    </div>

    <!-- FILTER BAR -->
    <div class="cif-filter-card mb-6">
      <div style="width: 250px;">
        <div class="cif-filter-card__label mb-1">Group By Parameter</div>
        <v-select
          v-model="groupBy"
          :items="groupByOptions"
          variant="outlined"
          density="compact"
          hide-details
          bg-color="white"
          prepend-inner-icon="ri-filter-3-line"
        />
      </div>
    </div>

    <!-- CHART SECTION -->
    <div class="content-card mb-6">
      <div class="content-card__accent-top" style="background: linear-gradient(90deg, #059669, #10b981);"></div>
      <div class="content-card__header">
        <div>
          <div class="content-card__title">Distribusi CIF berdasarkan {{ groupBy }}</div>
          <div class="content-card__subtitle">Komposisi Nasabah Individu vs Badan Hukum</div>
        </div>
      </div>
      <div class="content-card__body">
        <apexchart
          type="bar"
          height="350"
          :options="chartOptions"
          :series="chartSeries"
        />
      </div>
    </div>

    <!-- DATA SUMMARY TABLE -->
    <div class="content-card">
      <div class="content-card__accent-top" style="background: linear-gradient(90deg, #1e293b, #334155);"></div>
      <div class="content-card__header">
        <div>
          <div class="content-card__title">Tabel Agregasi Data CIF</div>
          <div class="content-card__subtitle">Ringkasan angka absolut per {{ groupBy.toLowerCase() }}</div>
        </div>
      </div>
      <div class="overflow-x-auto">
        <v-data-table
          :headers="headers"
          :items="tableData"
          class="cif-vtable"
          hover
          density="comfortable"
          :items-per-page="-1"
          hide-default-footer
        >
          <template #item.individu="{ item }">
            <span class="font-weight-medium">{{ formatNumber(item.individu) }}</span>
          </template>
          <template #item.badanHukum="{ item }">
            <span class="font-weight-medium">{{ formatNumber(item.badanHukum) }}</span>
          </template>
          <template #item.total="{ item }">
            <span class="font-weight-bold text-primary">{{ formatNumber(item.total) }}</span>
          </template>
          <template #bottom>
            <!-- Custom Footer -->
            <table class="cif-table" style="width: 100%; border-top: 2px solid #e2e8f0; margin-top: -1px;">
              <tfoot style="position: static; box-shadow: none;">
                <tr>
                  <td style="width: 30%; text-transform: uppercase;">Total Keseluruhan</td>
                  <td style="width: 20%; text-align: right;">{{ formatNumber(totalIndividu) }}</td>
                  <td style="width: 25%; text-align: right;">{{ formatNumber(totalBadanHukum) }}</td>
                  <td style="width: 25%; text-align: right; color: #0f172a; font-size: 14px;">{{ formatNumber(totalKeseluruhan) }}</td>
                </tr>
              </tfoot>
            </table>
          </template>
        </v-data-table>
      </div>
    </div>
  </div>
</template>

<style scoped>
.cif-page { background: #f1f5f9; }
</style>
