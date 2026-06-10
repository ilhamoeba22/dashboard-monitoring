<script setup>
import { computed, onMounted, ref, watch } from 'vue'
import DefaultLayout from '@/layouts/default.vue'
import { formatExactNumber } from '@/utils/money'
import '@/assets/css/cif-shared.css'

defineOptions({ layout: DefaultLayout })

const groupBy = ref('cabang')
const isLoading = ref(false)
const errorMessage = ref('')
const rows = ref([])

const groupByOptions = [
  { title: 'Cabang', value: 'cabang' },
  { title: 'Wilayah', value: 'wilayah' },
  { title: 'Account Officer', value: 'ao' },
  { title: 'Segmen', value: 'segmen' },
  { title: 'Agama', value: 'agama' },
]

const groupLabel = computed(() => groupByOptions.find(item => item.value === groupBy.value)?.title || 'Parameter')

const tableData = computed(() => rows.value.map(item => ({
  label: item.label || 'Tidak Diketahui',
  individu: Number(item.individu || 0),
  badanHukum: Number(item.badan_hukum || 0),
  total: Number(item.total_nasabah || 0),
})))

const totalIndividu = computed(() => tableData.value.reduce((sum, item) => sum + item.individu, 0))
const totalBadanHukum = computed(() => tableData.value.reduce((sum, item) => sum + item.badanHukum, 0))
const totalKeseluruhan = computed(() => tableData.value.reduce((sum, item) => sum + item.total, 0))

const headers = computed(() => [
  { title: `NAMA ${groupLabel.value.toUpperCase()}`, key: 'label', align: 'left', width: '30%' },
  { title: 'NASABAH INDIVIDU', key: 'individu', align: 'end', width: '20%' },
  { title: 'NASABAH BADAN HUKUM', key: 'badanHukum', align: 'end', width: '25%' },
  { title: 'TOTAL KESELURUHAN', key: 'total', align: 'end', width: '25%' },
])

function formatNumber(value) {
  return formatExactNumber(value)
}

const chartOptions = computed(() => ({
  chart: { type: 'bar', fontFamily: 'Inter, sans-serif', toolbar: { show: false } },
  colors: ['#10b981', '#f59e0b'],
  plotOptions: {
    bar: { horizontal: false, columnWidth: '45%', borderRadius: 4, dataLabels: { position: 'top' } },
  },
  dataLabels: {
    enabled: true,
    formatter: value => formatNumber(value),
    offsetY: -20,
    style: { fontSize: '11px', colors: ['#64748b'] },
  },
  stroke: { show: true, width: 2, colors: ['transparent'] },
  xaxis: {
    categories: tableData.value.map(item => item.label),
    axisBorder: { show: false },
    axisTicks: { show: false },
    labels: { rotate: -20, trim: true },
  },
  yaxis: { labels: { formatter: value => formatNumber(value) } },
  legend: { position: 'top', horizontalAlign: 'right' },
  tooltip: { y: { formatter: value => `${formatNumber(value)} Nasabah` } },
}))

const chartSeries = computed(() => [
  { name: 'Individu', data: tableData.value.map(item => item.individu) },
  { name: 'Badan Hukum', data: tableData.value.map(item => item.badanHukum) },
])

async function fetchRekapitulasi() {
  isLoading.value = true
  errorMessage.value = ''

  try {
    const response = await fetch(`/api/v1/cif/rekapitulasi?group_by=${groupBy.value}`)
    const json = await response.json()
    if (!response.ok || !json.success) {
      throw new Error(json.message || 'Gagal memuat rekapitulasi CIF.')
    }
    rows.value = Array.isArray(json.data) ? json.data : []
  } catch (error) {
    rows.value = []
    errorMessage.value = error.message || 'Gagal memuat rekapitulasi CIF.'
  } finally {
    isLoading.value = false
  }
}

watch(groupBy, fetchRekapitulasi)
onMounted(fetchRekapitulasi)
</script>

<template>
  <div class="cif-page px-4 pt-0">
    <div class="cif-hero mb-6">
      <div class="cif-hero__deco"></div>
      <div class="cif-hero__inner">
        <div class="cif-hero__top">
          <div class="cif-hero__icon">
            <v-icon icon="ri-bar-chart-grouped-line" size="26" color="white" />
          </div>
          <div class="cif-hero__meta">
            <h1 class="cif-hero__title">Rekapitulasi Analitik CIF</h1>
            <p class="cif-hero__subtitle">Agregasi data nasabah berdasarkan cabang, wilayah, AO, segmen, atau agama.</p>
          </div>
        </div>
      </div>
    </div>

    <div class="cif-filter-card mb-6">
      <div style="width: 260px;">
        <div class="cif-filter-card__label mb-1">Group By Parameter</div>
        <v-select
          v-model="groupBy"
          :items="groupByOptions"
          item-title="title"
          item-value="value"
          variant="outlined"
          density="compact"
          hide-details
          bg-color="white"
          prepend-inner-icon="ri-filter-3-line"
        />
      </div>
    </div>

    <v-alert
      v-if="errorMessage"
      type="error"
      variant="tonal"
      class="mb-4"
      density="comfortable"
    >
      {{ errorMessage }}
    </v-alert>

    <div class="content-card mb-6">
      <div class="content-card__accent-top" style="background: linear-gradient(90deg, #059669, #10b981);"></div>
      <div class="content-card__header">
        <div>
          <div class="content-card__title">Distribusi CIF berdasarkan {{ groupLabel }}</div>
          <div class="content-card__subtitle">Komposisi Nasabah Individu vs Badan Hukum dari database berjalan</div>
        </div>
        <v-chip size="small" color="primary" variant="tonal">{{ formatNumber(totalKeseluruhan) }} CIF</v-chip>
      </div>
      <div class="content-card__body">
        <v-skeleton-loader v-if="isLoading" type="image" height="350" rounded="xl" />
        <apexchart
          v-else-if="tableData.length"
          type="bar"
          height="350"
          :options="chartOptions"
          :series="chartSeries"
        />
        <div v-else class="cif-empty py-12">
          <div class="cif-empty__desc">Data rekapitulasi tidak tersedia</div>
        </div>
      </div>
    </div>

    <div class="content-card">
      <div class="content-card__accent-top" style="background: linear-gradient(90deg, #1e293b, #334155);"></div>
      <div class="content-card__header">
        <div>
          <div class="content-card__title">Tabel Agregasi Data CIF</div>
          <div class="content-card__subtitle">Ringkasan angka absolut per {{ groupLabel.toLowerCase() }}</div>
        </div>
      </div>
      <div class="overflow-x-auto">
        <v-data-table
          :headers="headers"
          :items="tableData"
          :loading="isLoading"
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
.cif-page { background: #f1f5f9; min-height: 100vh; padding-bottom: 48px; }
</style>
