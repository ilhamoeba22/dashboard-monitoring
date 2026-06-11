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
const exportMessage = ref('')
const exportMessageType = ref('info')

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

const isDenseChart = computed(() => tableData.value.length > 12)
const chartHeight = computed(() => isDenseChart.value ? Math.min(720, Math.max(360, tableData.value.length * 28)) : 340)

const chartOptions = computed(() => ({
  chart: { type: 'bar', fontFamily: 'Inter, sans-serif', toolbar: { show: false } },
  colors: ['#10b981', '#f59e0b'],
  plotOptions: {
    bar: {
      horizontal: isDenseChart.value,
      columnWidth: '42%',
      barHeight: '58%',
      borderRadius: 5,
      dataLabels: { position: isDenseChart.value ? 'right' : 'top' },
    },
  },
  dataLabels: {
    enabled: tableData.value.length <= 30,
    formatter: value => formatNumber(value),
    offsetY: isDenseChart.value ? 0 : -18,
    offsetX: isDenseChart.value ? 6 : 0,
    style: { fontSize: '10.5px', fontWeight: 800, colors: ['#475569'] },
  },
  stroke: { show: true, width: 2, colors: ['transparent'] },
  xaxis: {
    categories: tableData.value.map(item => item.label),
    axisBorder: { show: false },
    axisTicks: { show: false },
    labels: {
      rotate: isDenseChart.value ? 0 : -15,
      trim: true,
      maxHeight: 88,
      style: { fontSize: '11px', fontWeight: 650, colors: '#64748b' },
      formatter: value => isDenseChart.value ? formatNumber(value) : value,
    },
  },
  yaxis: {
    labels: {
      formatter: value => isDenseChart.value ? String(value).slice(0, 24) : formatNumber(value),
      style: { fontSize: '11px', fontWeight: 650, colors: '#64748b' },
    },
  },
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

const clearExportMessage = () => {
  exportMessage.value = ''
  exportMessageType.value = 'info'
}

const doExportExcel = async () => {
  if (!tableData.value.length) return
  const XLSX = await import('xlsx')
  const safeLabel = groupLabel.value.replace(/[^a-zA-Z0-9]+/g, '_')
  const worksheet = XLSX.utils.json_to_sheet(tableData.value)
  const workbook = XLSX.utils.book_new()
  XLSX.utils.book_append_sheet(workbook, worksheet, `Rekapitulasi_${safeLabel}`.substring(0, 31))
  XLSX.writeFile(workbook, `Export_Rekapitulasi_CIF_${safeLabel}_${new Date().toISOString().split('T')[0]}.xlsx`)
}

const doExportPdf = async () => {
  if (!tableData.value.length) return
  try {
    const { default: jsPDF } = await import('jspdf')
    const { default: autoTable } = await import('jspdf-autotable')
    const doc = new jsPDF({ orientation: 'landscape', unit: 'mm', format: 'a4' })
    doc.setFontSize(14)
    doc.text('Rekapitulasi Analitik CIF', 14, 15)
    doc.setFontSize(10)
    doc.text(`Group By: ${groupLabel.value}`, 14, 22)
    doc.text(`Jumlah Data: ${formatNumber(totalKeseluruhan.value)} CIF`, 14, 28)

    autoTable(doc, {
      startY: 34,
      head: [headers.value.map(h => h.title)],
      body: tableData.value.map(item => [
        item.label,
        formatNumber(item.individu),
        formatNumber(item.badanHukum),
        formatNumber(item.total),
      ]),
      foot: [['Total Keseluruhan', formatNumber(totalIndividu.value), formatNumber(totalBadanHukum.value), formatNumber(totalKeseluruhan.value)]],
      styles: { fontSize: 8, cellPadding: 2 },
      headStyles: { fillColor: [15, 23, 42], textColor: 255, halign: 'center' },
      footStyles: { fillColor: [241, 245, 249], textColor: 15, fontStyle: 'bold' },
      columnStyles: { 0: { halign: 'left' }, 1: { halign: 'right' }, 2: { halign: 'right' }, 3: { halign: 'right' } },
    })

    doc.save(`Export_Rekapitulasi_CIF_${groupLabel.value.replace(/[^a-zA-Z0-9]+/g, '_')}_${new Date().toISOString().split('T')[0]}.pdf`)
  } catch (e) {
    exportMessage.value = 'Gagal membuat PDF: ' + (e.message || e)
    exportMessageType.value = 'error'
    return
  }
}
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
      <div class="d-flex flex-wrap gap-2 ms-auto">
        <v-btn variant="outlined" color="#1e293b" prepend-icon="ri-file-excel-2-line" height="40" @click="doExportExcel">Excel</v-btn>
        <v-btn variant="outlined" color="#b91c1c" prepend-icon="ri-file-pdf-2-line" height="40" @click="doExportPdf">PDF</v-btn>
      </div>
    </div>

    <v-alert
      v-if="exportMessage"
      :type="exportMessageType === 'error' ? 'error' : 'info'"
      variant="tonal"
      class="mb-4"
      density="comfortable"
    >
      {{ exportMessage }}
      <template #append>
        <v-btn size="small" variant="text" @click="clearExportMessage">Tutup</v-btn>
      </template>
    </v-alert>

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
          :height="chartHeight"
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
