<script setup>
import { ref, computed, onMounted, watch } from 'vue'
import { Head } from '@inertiajs/vue3'
import DefaultLayout from '@/layouts/default.vue'
import axios from 'axios'
import VueApexCharts from 'vue3-apexcharts'
import '@/assets/css/financing-shared.css'
import { formatExactRupiah, formatExactNumber, formatTruncatedPercentage, toFiniteNumber } from '@/utils/money'

defineOptions({ layout: DefaultLayout })

const loading = ref(true)
const errorMessage = ref('')
const rawData = ref([])
const summary = ref({
  total_kontrak: 0,
  total_volume: 0,
  total_delta_plafon: 0,
  total_os_baru: 0,
  count_topup: 0,
  count_ulangan: 0,
  count_retention: 0,
  count_naik: 0,
  count_turun: 0,
  count_tetap: 0,
  count_pindah_ao: 0,
})
const periodMeta = ref({
  requested_period: null,
  active_period: null,
  is_historical: false,
  period_available: true,
  source_table: 'TOFLMB',
  source_database: null,
  message: null,
})

const selectedAo = ref('Semua AO Baru')
const selectedAnalisa = ref('Semua Tipe')
const selectedLimit = ref('Semua Limit')
const selectedAoStatus = ref('Semua Status AO')
const searchQuery = ref('')
const selectedTahun = ref(null)
const selectedBulan = ref(null)
const currentPage = ref(1)
const itemsPerPage = ref(15)
const isExporting = ref(false)

const monthOptions = [
  { title: 'Januari', value: 1 }, { title: 'Februari', value: 2 }, { title: 'Maret', value: 3 },
  { title: 'April', value: 4 }, { title: 'Mei', value: 5 }, { title: 'Juni', value: 6 },
  { title: 'Juli', value: 7 }, { title: 'Agustus', value: 8 }, { title: 'September', value: 9 },
  { title: 'Oktober', value: 10 }, { title: 'November', value: 11 }, { title: 'Desember', value: 12 },
]
const analisaOptions = ['Semua Tipe', 'Top Up', 'Ulangan', 'Retention']
const limitOptions = ['Semua Limit', 'Kenaikan', 'Penurunan', 'Tetap']
const aoStatusOptions = ['Semua Status AO', 'AO Tetap', 'Pindah AO']
const yearOptions = computed(() => {
  const current = new Date().getFullYear()
  return [current, current - 1, current - 2, current - 3, current - 4]
})

const activePeriodLabel = computed(() => {
  if (!selectedTahun.value || !selectedBulan.value) return 'Periode aktif CBS'
  const month = monthOptions.find(item => item.value === selectedBulan.value)?.title || '-'
  return `${month} ${selectedTahun.value}`
})
const periodUnavailable = computed(() => periodMeta.value?.period_available === false)
const sourceInfoLabel = computed(() => `${periodMeta.value?.source_database || '-'}  -  ${periodMeta.value?.source_table || 'TOFLMB'}`)

const aoOptions = computed(() => {
  const aos = [...new Set(rawData.value.map(item => item.nama_ao_baru).filter(Boolean))]
  return ['Semua AO Baru', ...aos.sort()]
})

const filteredData = computed(() => {
  const query = searchQuery.value.toLowerCase()
  return rawData.value.filter(item => {
    const matchAo = selectedAo.value === 'Semua AO Baru' || item.nama_ao_baru === selectedAo.value
    const matchAnalisa = selectedAnalisa.value === 'Semua Tipe' || item.analisa_nasabah === selectedAnalisa.value
    const matchLimit = selectedLimit.value === 'Semua Limit' || item.analisa_limit === selectedLimit.value
    const matchAoStatus = selectedAoStatus.value === 'Semua Status AO' || item.status_ao === selectedAoStatus.value
    const matchSearch = String(item.nama || '').toLowerCase().includes(query)
      || String(item.kontrak_baru || '').includes(searchQuery.value)
      || String(item.kontrak_lama || '').includes(searchQuery.value)
    return matchAo && matchAnalisa && matchLimit && matchAoStatus && matchSearch
  })
})

const totalPages = computed(() => Math.max(1, Math.ceil(filteredData.value.length / itemsPerPage.value)))
const paginatedData = computed(() => {
  const start = (currentPage.value - 1) * itemsPerPage.value
  return filteredData.value.slice(start, start + itemsPerPage.value)
})

const sumBy = (rows, selector) => rows.reduce((sum, item) => sum + toFiniteNumber(selector(item)), 0)
const totalVolumeFiltered = computed(() => sumBy(filteredData.value, item => item.plafon_baru))
const totalDeltaFiltered = computed(() => sumBy(filteredData.value, item => item.selisih_plafon))
const totalOsBaruFiltered = computed(() => sumBy(filteredData.value, item => item.os_baru_saat_ini))
const topUpRows = computed(() => filteredData.value.filter(item => item.analisa_nasabah === 'Top Up'))
const ulanganRows = computed(() => filteredData.value.filter(item => item.analisa_nasabah === 'Ulangan'))
const retentionRows = computed(() => filteredData.value.filter(item => item.analisa_nasabah === 'Retention'))
const kenaikanRows = computed(() => filteredData.value.filter(item => item.analisa_limit === 'Kenaikan'))
const penurunanRows = computed(() => filteredData.value.filter(item => item.analisa_limit === 'Penurunan'))
const pindahAoRows = computed(() => filteredData.value.filter(item => item.status_ao === 'Pindah AO'))
const topUpRatio = computed(() => filteredData.value.length ? (topUpRows.value.length / filteredData.value.length) * 100 : 0)
const pindahAoRatio = computed(() => filteredData.value.length ? (pindahAoRows.value.length / filteredData.value.length) * 100 : 0)
const highestDeltaItem = computed(() => [...filteredData.value].sort((a, b) => toFiniteNumber(b.selisih_plafon) - toFiniteNumber(a.selisih_plafon))[0] || null)

const interpretation = computed(() => {
  if (errorMessage.value) return `Data belum dapat dimuat: ${errorMessage.value}`
  if (periodUnavailable.value) return 'Periode belum tersedia, sehingga data dikosongkan agar tidak salah membaca pipeline top-up.'
  if (!filteredData.value.length) return 'Tidak ada transaksi Top-Up/Retention pada filter ini.'
  if (penurunanRows.value.length > kenaikanRows.value.length) return `${penurunanRows.value.length} transaksi mengalami penurunan plafon. Pastikan penyebabnya sesuai strategi risiko dan bukan sekadar repeat order yang melemah.`
  if (pindahAoRows.value.length > 0 && kenaikanRows.value.length > 0) return `${kenaikanRows.value.length} transaksi naik plafon dan ${pindahAoRows.value.length} berpindah AO. Validasi handover AO dan kualitas analisa ulang sebelum pencairan lanjutan.`
  if (topUpRows.value.length > retentionRows.value.length) return 'Aktivitas didominasi Top Up cepat setelah pelunasan. Fokus pada validasi repayment historis, agunan, dan tujuan penggunaan fasilitas baru.'
  return 'Aktivitas didominasi ulangan/retention. Pantau efektivitas retensi nasabah dan kualitas kolektibilitas fasilitas baru.'
})

const aoPriorityRows = computed(() => {
  const groups = new Map()
  filteredData.value.forEach(item => {
    const key = item.nama_ao_baru || 'TANPA AO'
    const current = groups.get(key) || {
      ao: key,
      transaksi: 0,
      volume_baru: 0,
      delta_plafon: 0,
      pindah_ao: 0,
      kenaikan: 0,
      topup: 0,
    }
    current.transaksi += 1
    current.volume_baru += toFiniteNumber(item.plafon_baru)
    current.delta_plafon += toFiniteNumber(item.selisih_plafon)
    if (item.status_ao === 'Pindah AO') current.pindah_ao += 1
    if (item.analisa_limit === 'Kenaikan') current.kenaikan += 1
    if (item.analisa_nasabah === 'Top Up') current.topup += 1
    groups.set(key, current)
  })
  return [...groups.values()].sort((a, b) => b.volume_baru - a.volume_baru)
})

const typeChart = computed(() => ({
  series: [topUpRows.value.length, ulanganRows.value.length, retentionRows.value.length],
  options: {
    chart: { type: 'donut', fontFamily: "'Plus Jakarta Sans', sans-serif", background: 'transparent' },
    labels: ['Top Up', 'Ulangan', 'Retention'],
    colors: ['#10B981', '#F59E0B', '#3B82F6'],
    dataLabels: { enabled: false },
    legend: { show: false },
    plotOptions: { pie: { donut: { size: '78%' } } },
    stroke: { width: 0 },
    tooltip: { y: { formatter: value => `${value} transaksi` } },
  },
}))

const volumeChart = computed(() => {
  const rows = aoPriorityRows.value.slice(0, 8)
  return {
    series: [{ name: 'Plafon Baru', data: rows.map(row => row.volume_baru) }],
    options: {
      chart: { type: 'bar', toolbar: { show: false }, fontFamily: 'Inter, sans-serif' },
      colors: ['#2563EB'],
      plotOptions: { bar: { borderRadius: 8, horizontal: true } },
      dataLabels: { enabled: false },
      xaxis: { categories: rows.map(row => row.ao), labels: { formatter: value => formatExactRupiah(value), style: { colors: '#64748b', fontSize: '10px' } } },
      yaxis: { labels: { style: { colors: '#0f172a', fontWeight: 800 } } },
      grid: { borderColor: '#e2e8f0', strokeDashArray: 4 },
      tooltip: { y: { formatter: value => formatExactRupiah(value) } },
    },
  }
})

const fetchData = async () => {
  loading.value = true
  errorMessage.value = ''
  try {
    const params = {}
    if (selectedTahun.value) params.tahun = selectedTahun.value
    if (selectedBulan.value) params.bulan = selectedBulan.value
    const response = await axios.get('/api/v1/financing/restrukturisasi/top-up', { params })
    if (response.data.success) {
      periodMeta.value = response.data.period_meta || periodMeta.value
      const requested = String(periodMeta.value?.requested_period || '')
      if (requested.length === 6) {
        selectedTahun.value = Number(requested.slice(0, 4))
        selectedBulan.value = Number(requested.slice(4, 6))
      }
      rawData.value = response.data.data || []
      summary.value = { ...summary.value, ...(response.data.summary || {}) }
    } else {
      throw new Error(response.data.error || 'Gagal memuat data Top-Up')
    }
  } catch (error) {
    errorMessage.value = error?.response?.data?.error || error.message || 'Gagal memuat data Top-Up'
    rawData.value = []
  } finally {
    loading.value = false
  }
}

const formatRp = value => formatExactRupiah(value)
const formatNumber = value => formatExactNumber(value)
const getAnalisaColor = type => ({ 'Top Up': 'success', Ulangan: 'warning', Retention: 'info' }[type] || 'grey')
const getLimitColor = limit => ({ Kenaikan: 'success', Penurunan: 'error', Tetap: 'grey' }[limit] || 'grey')
const getLimitIcon = limit => ({ Kenaikan: 'ri-arrow-right-up-line', Penurunan: 'ri-arrow-right-down-line', Tetap: 'ri-arrow-right-line' }[limit] || 'ri-checkbox-blank-circle-line')

const buildDetailRows = () => filteredData.value.map(item => ({
  'No CIF': item.nocif || '-',
  Nasabah: item.nama || '-',
  'Kontrak Lama': item.kontrak_lama || '-',
  'Kontrak Baru': item.kontrak_baru || '-',
  'AO Lama': item.nama_ao_lama || '-',
  'AO Baru': item.nama_ao_baru || '-',
  'Status AO': item.status_ao || '-',
  'Analisa Nasabah': item.analisa_nasabah || '-',
  'Analisa Limit': item.analisa_limit || '-',
  'Selisih Hari': toFiniteNumber(item.selisih_hari),
  'Tanggal Lunas': item.tgl_lunas || '-',
  'Tanggal Efektif Baru': item.tgl_efektif_baru || '-',
  'Plafon Lama': toFiniteNumber(item.plafon_lama),
  'Plafon Baru': toFiniteNumber(item.plafon_baru),
  'Selisih Plafon': toFiniteNumber(item.selisih_plafon),
  'OS Baru Saat Ini': toFiniteNumber(item.os_baru_saat_ini),
  'Kol Lama': item.coll_lama || '-',
  'Kol Baru': item.coll_baru || '-',
}))

const buildSummaryRows = () => [
  { Metrik: 'Periode', Nilai: activePeriodLabel.value },
  { Metrik: 'Sumber Data', Nilai: sourceInfoLabel.value },
  { Metrik: 'Total Transaksi', Nilai: filteredData.value.length },
  { Metrik: 'Total Plafon Baru', Nilai: totalVolumeFiltered.value },
  { Metrik: 'Total Selisih Plafon', Nilai: totalDeltaFiltered.value },
  { Metrik: 'Total OS Baru Saat Ini', Nilai: totalOsBaruFiltered.value },
  { Metrik: 'Top Up', Nilai: topUpRows.value.length },
  { Metrik: 'Ulangan', Nilai: ulanganRows.value.length },
  { Metrik: 'Retention', Nilai: retentionRows.value.length },
  { Metrik: 'Kenaikan Plafon', Nilai: kenaikanRows.value.length },
  { Metrik: 'Penurunan Plafon', Nilai: penurunanRows.value.length },
  { Metrik: 'Pindah AO', Nilai: pindahAoRows.value.length },
]

const exportExcel = async () => {
  if (isExporting.value) return
  isExporting.value = true
  try {
    const XLSX = await import('xlsx')
    const workbook = XLSX.utils.book_new()
    XLSX.utils.book_append_sheet(workbook, XLSX.utils.json_to_sheet(buildSummaryRows()), '00 Summary')
    XLSX.utils.book_append_sheet(workbook, XLSX.utils.json_to_sheet(buildDetailRows()), '01 Detail Top Up')
    XLSX.utils.book_append_sheet(workbook, XLSX.utils.json_to_sheet(aoPriorityRows.value), '02 Prioritas AO')
    XLSX.writeFile(workbook, `top-up-retention-${activePeriodLabel.value.replace(/\s+/g, '-')}.xlsx`)
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
    doc.text('Top-Up & Retention Pembiayaan', 40, 38)
    doc.setFont('helvetica', 'normal')
    doc.setFontSize(9)
    doc.text(`${activePeriodLabel.value}  -  ${sourceInfoLabel.value}`, 40, 56)
    doc.text(`Plafon Baru ${formatRp(totalVolumeFiltered.value)}  -  Delta ${formatRp(totalDeltaFiltered.value)}  -  Pindah AO ${pindahAoRows.value.length}`, 40, 70)
    doc.autoTable({
      startY: 92,
      head: [['Nasabah', 'Kontrak Lama', 'Kontrak Baru', 'AO Baru', 'Tipe', 'Plafon Baru', 'Delta']],
      body: buildDetailRows().map(row => [row.Nasabah, row['Kontrak Lama'], row['Kontrak Baru'], row['AO Baru'], row['Analisa Nasabah'], formatRp(row['Plafon Baru']), formatRp(row['Selisih Plafon'])]),
      styles: { fontSize: 7, cellPadding: 4, overflow: 'linebreak' },
      headStyles: { fillColor: [16, 185, 129], textColor: 255, fontStyle: 'bold' },
      columnStyles: { 5: { halign: 'right' }, 6: { halign: 'right' } },
    })
    doc.save(`top-up-retention-${activePeriodLabel.value.replace(/\s+/g, '-')}.pdf`)
  } finally {
    isExporting.value = false
  }
}

const resetPage = () => { currentPage.value = 1 }
onMounted(fetchData)
watch([selectedAo, selectedAnalisa, selectedLimit, selectedAoStatus, searchQuery], resetPage)
watch([selectedTahun, selectedBulan], () => {
  resetPage()
  fetchData()
})
</script>

<template>
  <div class="fin-page px-4 pt-0">
    <Head title="Top-Up & Retention Pembiayaan" />

    <div class="fin-hero mb-6">
      <div class="fin-hero__deco"></div>
      <div class="fin-hero__inner">
        <div class="d-flex flex-column flex-xl-row justify-space-between align-start align-xl-center gap-4">
          <div class="d-flex align-center gap-4">
            <div class="fin-hero__icon fin-icon-blue">
              <v-icon icon="ri-add-circle-fill" size="26" color="white" />
            </div>
            <div class="fin-hero__meta">
              <h1 class="fin-hero__title">Top-Up & Retention</h1>
              <p class="fin-hero__subtitle">Monitoring repeat order, retensi nasabah lunas, penambahan plafon, perpindahan AO, dan kualitas fasilitas baru.</p>
              <div class="fin-hero__badges">
                <span class="fin-badge fin-badge--warning">Repeat Order</span>
                <span class="fin-badge fin-badge--slate">{{ activePeriodLabel }}</span>
              </div>
            </div>
          </div>

          <div class="tu-toolbar">
            <div class="fin-filter-bar">
              <v-select v-model="selectedTahun" :items="yearOptions" label="Tahun" variant="solo" density="compact" flat hide-details rounded="lg" bg-color="white" prepend-inner-icon="ri-calendar-line" style="min-width: 120px; max-width: 140px;" />
              <v-select v-model="selectedBulan" :items="monthOptions" item-title="title" item-value="value" label="Bulan" variant="solo" density="compact" flat hide-details rounded="lg" bg-color="white" prepend-inner-icon="ri-calendar-event-line" style="min-width: 150px; max-width: 180px;" />
              <v-btn variant="text" density="comfortable" @click="fetchData" :loading="loading" icon="ri-refresh-line" color="white" />
            </div>
            <div class="tu-export-actions">
              <v-btn size="small" rounded="lg" color="success" variant="flat" :loading="isExporting" prepend-icon="ri-file-excel-2-line" @click="exportExcel">Excel</v-btn>
              <v-btn size="small" rounded="lg" color="error" variant="flat" :loading="isExporting" prepend-icon="ri-file-pdf-2-line" @click="exportPdf">PDF</v-btn>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="tu-insight-panel mb-6">
      <div class="tu-insight-card tu-insight-card--primary">
        <span>Interpretasi Operasional</span>
        <strong>{{ interpretation }}</strong>
        <small>{{ sourceInfoLabel }}</small>
      </div>
      <div class="tu-insight-card">
        <span>Total Plafon Baru</span>
        <strong>{{ formatRp(totalVolumeFiltered) }}</strong>
        <small>{{ formatNumber(filteredData.length) }} transaksi dalam filter</small>
      </div>
      <div class="tu-insight-card">
        <span>Delta Plafon</span>
        <strong :class="totalDeltaFiltered < 0 ? 'text-error' : 'text-success'">{{ formatRp(totalDeltaFiltered) }}</strong>
        <small>{{ highestDeltaItem?.nama || 'Tidak ada transaksi' }}</small>
      </div>
      <div class="tu-insight-card">
        <span>Pindah AO</span>
        <strong>{{ formatNumber(pindahAoRows.length) }} transaksi</strong>
        <small>{{ formatTruncatedPercentage(pindahAoRatio) }} dari total filter</small>
      </div>
    </div>

    <v-alert v-if="periodUnavailable && !loading" type="warning" variant="tonal" border="start" rounded="lg" class="mb-6">
      <div class="font-weight-bold mb-1">Periode {{ activePeriodLabel }} belum tersedia</div>
      <div class="text-body-2">{{ periodMeta.message || 'Database snapshot periode ini belum tersedia.' }}</div>
    </v-alert>

    <v-row class="mb-6">
      <v-col cols="12" sm="6" lg="3"><v-card class="tu-score-card" elevation="0"><v-icon icon="ri-file-add-line" size="34" color="#10b981" /><div><p>Total Transaksi</p><h2>{{ formatNumber(filteredData.length) }}</h2><small>Top Up {{ formatNumber(topUpRows.length) }}  -  {{ formatTruncatedPercentage(topUpRatio) }}</small></div></v-card></v-col>
      <v-col cols="12" sm="6" lg="3"><v-card class="tu-score-card" elevation="0"><v-icon icon="ri-arrow-right-up-line" size="34" color="#059669" /><div><p>Kenaikan Plafon</p><h2>{{ formatNumber(kenaikanRows.length) }}</h2><small>Penurunan {{ formatNumber(penurunanRows.length) }}</small></div></v-card></v-col>
      <v-col cols="12" sm="6" lg="3"><v-card class="tu-score-card" elevation="0"><v-icon icon="ri-wallet-3-line" size="34" color="#2563eb" /><div><p>OS Baru Saat Ini</p><h2>{{ formatRp(totalOsBaruFiltered) }}</h2><small>Outstanding fasilitas baru</small></div></v-card></v-col>
      <v-col cols="12" sm="6" lg="3"><v-card class="tu-score-card" elevation="0"><v-icon icon="ri-user-shared-line" size="34" color="#d97706" /><div><p>Handover AO</p><h2>{{ formatNumber(pindahAoRows.length) }}</h2><small>Perlu validasi transfer relationship</small></div></v-card></v-col>
    </v-row>

    <v-card class="tu-filter-card mb-6" elevation="0">
      <v-text-field v-model="searchQuery" prepend-inner-icon="ri-search-2-line" placeholder="Cari nasabah / kontrak..." variant="outlined" density="compact" hide-details rounded="lg" />
      <v-select v-model="selectedAo" :items="aoOptions" label="AO Baru" prepend-inner-icon="ri-user-star-line" variant="outlined" density="compact" hide-details rounded="lg" />
      <v-select v-model="selectedAnalisa" :items="analisaOptions" label="Tipe Nasabah" prepend-inner-icon="ri-pie-chart-2-line" variant="outlined" density="compact" hide-details rounded="lg" />
      <v-select v-model="selectedLimit" :items="limitOptions" label="Analisa Limit" prepend-inner-icon="ri-arrow-up-down-line" variant="outlined" density="compact" hide-details rounded="lg" />
      <v-select v-model="selectedAoStatus" :items="aoStatusOptions" label="Status AO" prepend-inner-icon="ri-user-shared-line" variant="outlined" density="compact" hide-details rounded="lg" />
    </v-card>

    <v-row class="mb-6">
      <v-col cols="12" lg="5">
        <div class="content-card">
          <div class="content-card__header"><div><div class="content-card__title">Komposisi Tipe Nasabah</div><div class="content-card__subtitle">Top Up cepat, ulangan, dan retention.</div></div></div>
          <div class="content-card__body d-flex align-center justify-center gap-6">
            <VueApexCharts v-if="!loading" type="donut" width="190" height="190" :options="typeChart.options" :series="typeChart.series" />
            <div class="tu-chart-legend">
              <span><i style="background:#10B981"></i>Top Up {{ formatNumber(topUpRows.length) }}</span>
              <span><i style="background:#F59E0B"></i>Ulangan {{ formatNumber(ulanganRows.length) }}</span>
              <span><i style="background:#3B82F6"></i>Retention {{ formatNumber(retentionRows.length) }}</span>
            </div>
          </div>
        </div>
      </v-col>
      <v-col cols="12" lg="7">
        <div class="content-card">
          <div class="content-card__header"><div><div class="content-card__title">Top AO by Plafon Baru</div><div class="content-card__subtitle">Volume fasilitas baru berdasarkan AO baru.</div></div></div>
          <div class="content-card__body"><VueApexCharts type="bar" height="260" :options="volumeChart.options" :series="volumeChart.series" /></div>
        </div>
      </v-col>
    </v-row>

    <div class="content-card mb-6">
      <div class="content-card__header"><div><div class="content-card__title">Prioritas Account Officer</div><div class="content-card__subtitle">Urutan kerja berdasarkan volume baru, delta plafon, dan handover AO.</div></div></div>
      <div class="content-card__body pa-0">
        <div v-for="row in aoPriorityRows.slice(0, 8)" :key="row.ao" class="tu-ao-row">
          <div><strong>{{ row.ao }}</strong><small>{{ row.transaksi }} transaksi  -  {{ row.kenaikan }} kenaikan  -  {{ row.pindah_ao }} pindah AO  -  {{ row.topup }} Top Up</small></div>
          <span>{{ formatRp(row.volume_baru) }}</span>
        </div>
        <div v-if="!aoPriorityRows.length" class="pa-8 text-center text-disabled">Tidak ada prioritas AO pada filter ini.</div>
      </div>
    </div>

    <div class="content-card">
      <div class="content-card__header">
        <div><div class="content-card__title">Detail Top-Up & Retention</div><div class="content-card__subtitle">Transisi fasilitas lama ke baru, selisih hari, plafon, AO, dan kolektibilitas.</div></div>
        <v-chip size="x-small" color="primary" variant="flat" class="font-weight-black">{{ formatNumber(filteredData.length) }} data</v-chip>
      </div>
      <div class="content-card__body pa-0">
        <div class="overflow-x-auto">
          <table class="fin-table fin-vtable tu-table">
            <thead><tr><th>Transisi Fasilitas</th><th>Tipe</th><th class="text-right">Plafon Lama -> Baru</th><th class="text-right">Delta</th><th class="text-center">Kol</th><th>Status AO</th></tr></thead>
            <tbody>
              <tr v-if="loading"><td colspan="6" class="pa-12 text-center"><v-progress-circular indeterminate color="primary" size="46" /><div class="text-h6 text-medium-emphasis font-weight-bold mt-4">Memuat Data Top-Up...</div></td></tr>
              <tr v-else-if="paginatedData.length === 0"><td colspan="6" class="pa-12 text-center"><v-icon icon="ri-inbox-line" size="64" class="text-disabled mb-4" /><div class="text-h6 text-medium-emphasis font-weight-bold">Data Tidak Ditemukan</div><div class="text-caption text-disabled mt-1">Coba sesuaikan filter pencarian.</div></td></tr>
              <tr v-for="item in paginatedData" :key="`${item.kontrak_lama}-${item.kontrak_baru}`">
                <td><div class="font-weight-black text-uppercase">{{ item.nama }}</div><div class="tu-contract-flow"><span>{{ item.kontrak_lama }}</span><v-icon icon="ri-arrow-right-double-line" size="16" /><span>{{ item.kontrak_baru }}</span></div><div class="text-caption text-medium-emphasis">{{ item.tgl_lunas }} -> {{ item.tgl_efektif_baru }}  -  {{ item.selisih_hari }} hari</div></td>
                <td><v-chip size="small" :color="getAnalisaColor(item.analisa_nasabah)" variant="tonal" class="font-weight-bold">{{ item.analisa_nasabah }}</v-chip></td>
                <td class="text-right"><div class="tu-money">{{ formatRp(item.plafon_baru) }}</div><div class="text-caption text-medium-emphasis">Lama {{ formatRp(item.plafon_lama) }}</div></td>
                <td class="text-right"><v-chip size="small" :color="getLimitColor(item.analisa_limit)" variant="flat" class="font-weight-bold"><v-icon :icon="getLimitIcon(item.analisa_limit)" size="small" class="mr-1" />{{ formatRp(item.selisih_plafon) }}</v-chip><div class="text-caption text-medium-emphasis mt-1">{{ item.analisa_limit }}</div></td>
                <td class="text-center"><v-chip size="x-small" color="secondary" variant="tonal" class="font-weight-bold">{{ item.coll_lama }}</v-chip><v-icon icon="ri-arrow-right-line" size="14" class="mx-1" /><v-chip size="x-small" color="primary" variant="flat" class="font-weight-bold">{{ item.coll_baru }}</v-chip></td>
                <td><div class="font-weight-black text-caption text-uppercase">{{ item.nama_ao_baru }}</div><div class="text-caption text-medium-emphasis">Lama {{ item.nama_ao_lama }}</div><v-chip size="x-small" :color="item.status_ao === 'Pindah AO' ? 'warning' : 'success'" variant="tonal" class="font-weight-bold mt-1">{{ item.status_ao }}</v-chip></td>
              </tr>
            </tbody>
          </table>
        </div>
        <v-divider v-if="filteredData.length > 0" />
        <div v-if="filteredData.length > 0" class="pa-4 d-flex align-center justify-space-between bg-slate-50">
          <div class="text-caption text-medium-emphasis font-weight-bold">Menampilkan {{ (currentPage - 1) * itemsPerPage + 1 }} - {{ Math.min(currentPage * itemsPerPage, filteredData.length) }} dari {{ filteredData.length }} data</div>
          <v-pagination v-model="currentPage" :length="totalPages" :total-visible="5" density="compact" variant="flat" active-color="primary" />
        </div>
      </div>
    </div>
  </div>
</template>

<style scoped>
.tu-toolbar,.tu-export-actions{display:flex;align-items:center;gap:12px;flex-wrap:wrap;justify-content:flex-end}.tu-export-actions{gap:8px}.fin-badge--slate{background:rgba(255,255,255,.12);color:#e2e8f0;border:1px solid rgba(255,255,255,.18)}
.tu-insight-panel{display:grid;grid-template-columns:minmax(0,1.4fr) repeat(3,minmax(210px,.75fr));gap:16px}.tu-insight-card,.tu-score-card,.tu-filter-card{background:linear-gradient(145deg,#fff 0%,#f8fafc 100%);border:1px solid #dbe7f3;border-radius:20px;box-shadow:0 10px 28px rgba(15,23,42,.06)}.tu-insight-card{min-height:124px;padding:18px 20px;display:flex;flex-direction:column;justify-content:center;gap:7px}.tu-insight-card span,.tu-score-card p{color:#64748b;font-size:11px;font-weight:900;text-transform:uppercase;letter-spacing:.06em;margin:0}.tu-insight-card strong{color:#0f172a;font-family:'Plus Jakarta Sans',sans-serif;font-size:clamp(.98rem,1vw,1.16rem);line-height:1.45;letter-spacing:-.02em}.tu-insight-card small,.tu-score-card small{color:#64748b;font-size:12px;font-weight:700}.tu-insight-card--primary{background:radial-gradient(circle at top right,rgba(16,185,129,.16),transparent 34%),linear-gradient(145deg,#ecfdf5 0%,#fff 74%);border-color:#bbf7d0}
.tu-score-card{min-height:132px;padding:20px;display:flex;align-items:center;gap:16px;height:100%}.tu-score-card h2{color:#0f172a;font-family:'Plus Jakarta Sans',sans-serif;font-size:clamp(1rem,1.55vw,1.75rem);font-weight:900;line-height:1.1;margin:4px 0;max-width:280px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis}.tu-filter-card{padding:16px;display:grid;grid-template-columns:minmax(240px,1.2fr) repeat(4,minmax(150px,.7fr));gap:12px}.tu-chart-legend{display:flex;flex-direction:column;gap:10px}.tu-chart-legend span{font-size:12px;font-weight:800;color:#334155}.tu-chart-legend i{display:inline-block;width:10px;height:10px;border-radius:999px;margin-right:8px}.tu-ao-row{display:flex;justify-content:space-between;gap:14px;padding:14px 18px;border-bottom:1px solid #e2e8f0}.tu-ao-row strong{color:#0f172a;font-size:13px;font-weight:900}.tu-ao-row small{display:block;color:#64748b;font-size:11px;font-weight:700;margin-top:3px}.tu-ao-row span,.tu-money{color:#0f172a;font-family:'Plus Jakarta Sans',sans-serif;font-size:12px;font-weight:900;white-space:nowrap}.tu-contract-flow{display:flex;align-items:center;gap:8px;color:#64748b;font-family:monospace;font-size:11px;font-weight:800;margin:4px 0}.tu-table :deep(th){height:52px!important;letter-spacing:.5px!important}.tu-table :deep(td){height:76px!important;vertical-align:middle}
@media(max-width:1180px){.tu-insight-panel{grid-template-columns:1fr 1fr}.tu-filter-card{grid-template-columns:1fr 1fr}}@media(max-width:720px){.tu-toolbar,.tu-export-actions{width:100%}.tu-export-actions .v-btn{flex:1}.tu-insight-panel,.tu-filter-card{grid-template-columns:1fr}}
</style>
