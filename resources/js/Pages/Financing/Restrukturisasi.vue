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
  total_nasabah: 0,
  avg_ke: 0,
  kol_membaik: 0,
  kol_memburuk: 0,
  kol_tetap: 0,
  total_os_baru: 0,
  total_os_saat_ini: 0,
  frekuensi_tinggi: 0,
  os_frekuensi_tinggi: 0,
})
const periodMeta = ref({
  requested_period: null,
  active_period: null,
  is_historical: false,
  period_available: true,
  source_table: 'TOFLMBHP',
  source_database: null,
  message: null,
})

const selectedAo = ref('Semua AO')
const selectedCabang = ref('Semua Cabang')
const selectedTrend = ref('Semua')
const selectedRisk = ref('Semua')
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
const trendOptions = ['Semua', 'Membaik', 'Memburuk', 'Tetap']
const riskOptions = ['Semua', 'Frekuensi Tinggi', 'Kol Berjalan 3-5', 'Restruk Lancar']
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
const sourceInfoLabel = computed(() => `${periodMeta.value?.source_database || '-'} - ${periodMeta.value?.source_table || 'TOFLMBHP'}`)

const aoOptions = computed(() => {
  const aos = [...new Set(rawData.value.map(item => item.nama_ao).filter(Boolean))]
  return ['Semua AO', ...aos.sort()]
})

const cabangOptions = computed(() => {
  const cabangs = [...new Set(rawData.value.map(item => item.kantor_pelayanan).filter(Boolean))]
  return ['Semua Cabang', ...cabangs.sort()]
})

const getTrend = (item) => {
  const before = Number.parseInt(item.col_sblm_rest || 0)
  const after = Number.parseInt(item.col_stlh_rest || 0)
  if (after < before) return 'Membaik'
  if (after > before) return 'Memburuk'
  return 'Tetap'
}

const isHighFrequency = item => Number.parseInt(item.total_restrukturisasi || 0) >= 3
const isCurrentNpf = item => Number.parseInt(item.col_berjalan || 0) >= 3
const isRestructuredPerforming = item => Number.parseInt(item.col_berjalan || 0) === 1

const filteredData = computed(() => {
  const query = searchQuery.value.toLowerCase()
  return rawData.value.filter(item => {
    const matchAo = selectedAo.value === 'Semua AO' || item.nama_ao === selectedAo.value
    const matchCabang = selectedCabang.value === 'Semua Cabang' || item.kantor_pelayanan === selectedCabang.value
    const matchSearch = String(item.nama || '').toLowerCase().includes(query) || String(item.nokontrak || '').includes(searchQuery.value)
    const matchTrend = selectedTrend.value === 'Semua' || getTrend(item) === selectedTrend.value
    let matchRisk = true

    if (selectedRisk.value === 'Frekuensi Tinggi') matchRisk = isHighFrequency(item)
    else if (selectedRisk.value === 'Kol Berjalan 3-5') matchRisk = isCurrentNpf(item)
    else if (selectedRisk.value === 'Restruk Lancar') matchRisk = isRestructuredPerforming(item)

    return matchAo && matchCabang && matchSearch && matchTrend && matchRisk
  })
})

const totalPages = computed(() => Math.max(1, Math.ceil(filteredData.value.length / itemsPerPage.value)))
const paginatedData = computed(() => {
  const start = (currentPage.value - 1) * itemsPerPage.value
  return filteredData.value.slice(start, start + itemsPerPage.value)
})

const sumBy = (rows, selector) => rows.reduce((sum, item) => sum + toFiniteNumber(selector(item)), 0)
const totalOsSaatIniFiltered = computed(() => sumBy(filteredData.value, item => item.osmdlc_saat_ini))
const totalOsBaruFiltered = computed(() => sumBy(filteredData.value, item => item.osmdl_baru))
const highFrequencyRows = computed(() => filteredData.value.filter(isHighFrequency))
const currentNpfRows = computed(() => filteredData.value.filter(isCurrentNpf))
const performingRows = computed(() => filteredData.value.filter(isRestructuredPerforming))
const improvedRows = computed(() => filteredData.value.filter(item => getTrend(item) === 'Membaik'))
const deterioratedRows = computed(() => filteredData.value.filter(item => getTrend(item) === 'Memburuk'))
const highFrequencyOs = computed(() => sumBy(highFrequencyRows.value, item => item.osmdlc_saat_ini))
const currentNpfOs = computed(() => sumBy(currentNpfRows.value, item => item.osmdlc_saat_ini))
const highFrequencyRatio = computed(() => filteredData.value.length ? (highFrequencyRows.value.length / filteredData.value.length) * 100 : 0)
const avgLifetimeRestruk = computed(() => {
  const value = Number(summary.value.avg_ke || 0)
  if (!Number.isFinite(value)) return '0'
  const truncated = Math.trunc(value * 100) / 100
  return truncated.toLocaleString('id-ID', { maximumFractionDigits: 2 })
})

const highestRiskItem = computed(() => [...filteredData.value].sort((a, b) => {
  const frequencyDiff = Number.parseInt(b.total_restrukturisasi || 0) - Number.parseInt(a.total_restrukturisasi || 0)
  if (frequencyDiff !== 0) return frequencyDiff
  const kolDiff = Number.parseInt(b.col_berjalan || 0) - Number.parseInt(a.col_berjalan || 0)
  if (kolDiff !== 0) return kolDiff
  return toFiniteNumber(b.osmdlc_saat_ini) - toFiniteNumber(a.osmdlc_saat_ini)
})[0] || null)

const interpretation = computed(() => {
  if (errorMessage.value) return `Data belum dapat dimuat: ${errorMessage.value}`
  if (periodUnavailable.value) return 'Periode belum tersedia, sehingga data dikosongkan agar tidak salah membaca riwayat restrukturisasi.'
  if (!filteredData.value.length) return 'Tidak ada data restrukturisasi pada filter ini.'
  if (currentNpfRows.value.length > 0 && highFrequencyRows.value.length > 0) return `${currentNpfRows.value.length} kontrak restrukturisasi berada pada Kol 3-5 dan ${highFrequencyRows.value.length} kontrak sudah restruk minimal 3 kali. Prioritaskan review komite, remedial, dan validasi kelayakan restruk lanjutan.`
  if (highFrequencyRows.value.length > 0) return `${highFrequencyRows.value.length} kontrak memiliki frekuensi restrukturisasi tinggi. Pantau repayment pasca-addendum dan pastikan tidak menjadi evergreen restructuring.`
  if (currentNpfRows.value.length > 0) return `${currentNpfRows.value.length} kontrak restrukturisasi masih berada pada Kol 3-5. Fokus pada pemulihan kolektibilitas dan pencadangan.`
  if (improvedRows.value.length > deterioratedRows.value.length) return 'Mayoritas perubahan kolektibilitas pada filter ini membaik atau stabil setelah restrukturisasi.'
  return 'Portofolio restrukturisasi pada filter ini relatif stabil, namun tetap perlu pemantauan pembayaran pasca-addendum.'
})

const aoPriorityRows = computed(() => {
  const groups = new Map()
  filteredData.value.forEach(item => {
    const key = item.nama_ao || 'TANPA AO'
    const current = groups.get(key) || {
      ao: key,
      cabang: item.kantor_pelayanan || '-',
      kontrak: 0,
      nasabah: new Set(),
      os_saat_ini: 0,
      frekuensi_tinggi: 0,
      kol_berjalan_npf: 0,
      memburuk: 0,
    }
    current.kontrak += 1
    current.nasabah.add(item.nocif || item.nokontrak)
    current.os_saat_ini += toFiniteNumber(item.osmdlc_saat_ini)
    if (isHighFrequency(item)) current.frekuensi_tinggi += 1
    if (isCurrentNpf(item)) current.kol_berjalan_npf += 1
    if (getTrend(item) === 'Memburuk') current.memburuk += 1
    groups.set(key, current)
  })

  return [...groups.values()]
    .map(row => ({ ...row, nasabah: row.nasabah.size }))
    .sort((a, b) => {
      if (b.kol_berjalan_npf !== a.kol_berjalan_npf) return b.kol_berjalan_npf - a.kol_berjalan_npf
      if (b.frekuensi_tinggi !== a.frekuensi_tinggi) return b.frekuensi_tinggi - a.frekuensi_tinggi
      return b.os_saat_ini - a.os_saat_ini
    })
})

const getRiskLevel = (item) => {
  if (isCurrentNpf(item) && isHighFrequency(item)) return { label: 'Kritis', color: 'error', icon: 'ri-fire-line' }
  if (isCurrentNpf(item) || getTrend(item) === 'Memburuk') return { label: 'Tinggi', color: 'warning', icon: 'ri-alert-line' }
  if (isHighFrequency(item)) return { label: 'Monitoring', color: 'info', icon: 'ri-radar-line' }
  return { label: 'Stabil', color: 'success', icon: 'ri-checkbox-circle-line' }
}

const riskBucketRows = computed(() => {
  const groups = new Map()
  filteredData.value.forEach(item => {
    const risk = getRiskLevel(item)
    const current = groups.get(risk.label) || {
      risiko: risk.label,
      color: risk.color,
      kontrak: 0,
      nasabah: new Set(),
      os_saat_ini: 0,
      frekuensi_tinggi: 0,
      kol_berjalan_npf: 0,
      memburuk: 0,
    }
    current.kontrak += 1
    current.nasabah.add(item.nocif || item.nokontrak)
    current.os_saat_ini += toFiniteNumber(item.osmdlc_saat_ini)
    if (isHighFrequency(item)) current.frekuensi_tinggi += 1
    if (isCurrentNpf(item)) current.kol_berjalan_npf += 1
    if (getTrend(item) === 'Memburuk') current.memburuk += 1
    groups.set(risk.label, current)
  })
  const order = { Kritis: 1, Tinggi: 2, Monitoring: 3, Stabil: 4 }
  return [...groups.values()]
    .map(row => ({ ...row, nasabah: row.nasabah.size }))
    .sort((a, b) => (order[a.risiko] || 99) - (order[b.risiko] || 99))
})

const topRiskRows = computed(() => [...filteredData.value]
  .sort((a, b) => {
    const rank = { Kritis: 4, Tinggi: 3, Monitoring: 2, Stabil: 1 }
    const riskDiff = (rank[getRiskLevel(b).label] || 0) - (rank[getRiskLevel(a).label] || 0)
    if (riskDiff !== 0) return riskDiff
    const frequencyDiff = Number.parseInt(b.total_restrukturisasi || 0) - Number.parseInt(a.total_restrukturisasi || 0)
    if (frequencyDiff !== 0) return frequencyDiff
    return toFiniteNumber(b.osmdlc_saat_ini) - toFiniteNumber(a.osmdlc_saat_ini)
  })
  .slice(0, 10))

const trendChartOptions = computed(() => ({
  chart: { type: 'donut', fontFamily: "'Plus Jakarta Sans', sans-serif", background: 'transparent' },
  labels: ['Membaik', 'Memburuk', 'Tetap'],
  colors: ['#10B981', '#EF4444', '#F59E0B'],
  dataLabels: { enabled: false },
  legend: { show: false },
  plotOptions: { pie: { donut: { size: '78%' } } },
  stroke: { width: 0 },
  tooltip: { y: { formatter: value => `${value} kontrak` } },
}))
const trendChartSeries = computed(() => [improvedRows.value.length, deterioratedRows.value.length, Math.max(filteredData.value.length - improvedRows.value.length - deterioratedRows.value.length, 0)])

const frequencyChart = computed(() => {
  const buckets = [
    { label: '1x', min: 1, max: 1 },
    { label: '2x', min: 2, max: 2 },
    { label: '3x', min: 3, max: 3 },
    { label: '4x+', min: 4, max: 999 },
  ]
  return {
    series: [{
      name: 'Outstanding Saat Ini',
      data: buckets.map(bucket => sumBy(filteredData.value.filter(item => {
        const total = Number.parseInt(item.total_restrukturisasi || 0)
        return total >= bucket.min && total <= bucket.max
      }), item => item.osmdlc_saat_ini)),
    }],
    options: {
      chart: { type: 'bar', toolbar: { show: false }, fontFamily: 'Inter, sans-serif' },
      colors: ['#2563EB'],
      plotOptions: { bar: { borderRadius: 8, columnWidth: '46%' } },
      dataLabels: { enabled: false },
      xaxis: { categories: buckets.map(bucket => bucket.label), labels: { style: { colors: '#64748b', fontWeight: 800 } } },
      yaxis: { labels: { formatter: value => formatExactRupiah(value), style: { colors: '#64748b', fontSize: '10px' } } },
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
    const response = await axios.get('/api/v1/financing/restrukturisasi', { params })
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
      throw new Error(response.data.error || 'Gagal memuat data restrukturisasi')
    }
  } catch (error) {
    errorMessage.value = error?.response?.data?.error || error.message || 'Gagal memuat data restrukturisasi'
    rawData.value = []
  } finally {
    loading.value = false
  }
}

const formatRp = value => formatExactRupiah(value)
const formatNumber = value => formatExactNumber(value)
const getKolColor = (kol) => {
  const num = Number.parseInt(kol)
  if (num === 1) return 'success'
  if (num === 2) return 'info'
  if (num === 3) return 'warning'
  if (num === 4) return 'deep-orange'
  return 'error'
}

const buildDetailRows = () => filteredData.value.map(item => ({
  'No CIF': item.nocif || '-',
  'No Kontrak': item.nokontrak || '-',
  Nasabah: item.nama || '-',
  AO: item.nama_ao || '-',
  Cabang: item.kantor_pelayanan || '-',
  'Addendum Ke': Number.parseInt(item.ke || 0),
  'Total Restruk Lifetime': Number.parseInt(item.total_restrukturisasi || 0),
  'Jumlah Riwayat TOFLMBHP': Number.parseInt(item.jumlah_riwayat_restrukturisasi || 0),
  'Kol Sebelum Restruk': item.col_sblm_rest || '-',
  'Kol Setelah Restruk': item.col_stlh_rest || '-',
  'Kol Berjalan': item.col_berjalan || '-',
  Trend: getTrend(item),
  'Akad Lama': item.akad_lama || '-',
  'Akad Baru': item.akad_baru || '-',
  'Tgl Akad Baru': item.tglakad_baru || '-',
  'OS Pokok Lama': toFiniteNumber(item.osmdl_lama),
  'OS Pokok Baru': toFiniteNumber(item.osmdl_baru),
  'OS Pokok Saat Ini': toFiniteNumber(item.osmdlc_saat_ini),
  'OS Margin Saat Ini': toFiniteNumber(item.osmgnc_saat_ini),
  'Plafon Awal': toFiniteNumber(item.mdlawal),
  Keterangan: item.ket_rest || '-',
}))

const buildSummaryRows = () => [
  { Metrik: 'Periode', Nilai: activePeriodLabel.value },
  { Metrik: 'Sumber Data', Nilai: sourceInfoLabel.value },
  { Metrik: 'Total Kontrak', Nilai: filteredData.value.length },
  { Metrik: 'Total Nasabah Unik', Nilai: new Set(filteredData.value.map(item => item.nocif)).size },
  { Metrik: 'Total OS Pokok Saat Ini', Nilai: totalOsSaatIniFiltered.value },
  { Metrik: 'Total OS Pokok Baru Saat Restruk', Nilai: totalOsBaruFiltered.value },
  { Metrik: 'Kontrak Frekuensi Tinggi', Nilai: highFrequencyRows.value.length },
  { Metrik: 'OS Frekuensi Tinggi', Nilai: highFrequencyOs.value },
  { Metrik: 'Kontrak Kol Berjalan 3-5', Nilai: currentNpfRows.value.length },
  { Metrik: 'OS Kol Berjalan 3-5', Nilai: currentNpfOs.value },
  { Metrik: 'Kol Membaik', Nilai: improvedRows.value.length },
  { Metrik: 'Kol Memburuk', Nilai: deterioratedRows.value.length },
]

const exportExcel = async () => {
  if (isExporting.value) return
  isExporting.value = true
  try {
    const XLSX = await import('xlsx')
    const workbook = XLSX.utils.book_new()
    XLSX.utils.book_append_sheet(workbook, XLSX.utils.json_to_sheet(buildSummaryRows()), '00 Summary')
    XLSX.utils.book_append_sheet(workbook, XLSX.utils.json_to_sheet(buildDetailRows()), '01 Detail Restruk')
    XLSX.utils.book_append_sheet(workbook, XLSX.utils.json_to_sheet(aoPriorityRows.value), '02 Prioritas AO')
    XLSX.utils.book_append_sheet(workbook, XLSX.utils.json_to_sheet(riskBucketRows.value.map(row => ({
      Risiko: row.risiko,
      Kontrak: row.kontrak,
      Nasabah: row.nasabah,
      'OS Saat Ini': row.os_saat_ini,
      'Frekuensi Tinggi': row.frekuensi_tinggi,
      'Kol 3-5': row.kol_berjalan_npf,
      Memburuk: row.memburuk,
    }))), '03 Bucket Risiko')
    XLSX.utils.book_append_sheet(workbook, XLSX.utils.json_to_sheet(topRiskRows.value.map(item => ({
      Nasabah: item.nama || '-',
      Kontrak: item.nokontrak || '-',
      AO: item.nama_ao || '-',
      Risiko: getRiskLevel(item).label,
      'Total Restruk Lifetime': Number.parseInt(item.total_restrukturisasi || 0),
      'Kol Berjalan': item.col_berjalan || '-',
      'OS Saat Ini': toFiniteNumber(item.osmdlc_saat_ini),
      Keterangan: item.ket_rest || '-',
    }))), '04 Top Risiko')
    XLSX.writeFile(workbook, `restrukturisasi-${activePeriodLabel.value.replace(/\s+/g, '-')}.xlsx`)
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
    doc.text('Monitoring Restrukturisasi Pembiayaan', 40, 38)
    doc.setFont('helvetica', 'normal')
    doc.setFontSize(9)
    doc.text(`${activePeriodLabel.value} - ${sourceInfoLabel.value}`, 40, 56)
    doc.text(`OS Saat Ini ${formatRp(totalOsSaatIniFiltered.value)} - Frekuensi Tinggi ${highFrequencyRows.value.length} - Kol 3-5 ${currentNpfRows.value.length}`, 40, 70)
    doc.autoTable({
      startY: 92,
      head: [['Nasabah', 'Kontrak', 'AO', 'Total Restruk', 'Kol', 'OS Saat Ini', 'Keterangan']],
      body: buildDetailRows().map(row => [
        row.Nasabah,
        row['No Kontrak'],
        row.AO,
        `${row['Total Restruk Lifetime']}x`,
        `${row['Kol Sebelum Restruk']} -> ${row['Kol Setelah Restruk']} / Kini ${row['Kol Berjalan']}`,
        formatRp(row['OS Pokok Saat Ini']),
        row.Keterangan,
      ]),
      styles: { fontSize: 7, cellPadding: 4, overflow: 'linebreak' },
      headStyles: { fillColor: [37, 99, 235], textColor: 255, fontStyle: 'bold' },
      columnStyles: { 3: { halign: 'center' }, 5: { halign: 'right' }, 6: { cellWidth: 220 } },
    })
    doc.save(`restrukturisasi-${activePeriodLabel.value.replace(/\s+/g, '-')}.pdf`)
  } finally {
    isExporting.value = false
  }
}

const resetPage = () => { currentPage.value = 1 }

onMounted(fetchData)
watch([selectedAo, selectedCabang, selectedTrend, selectedRisk, searchQuery], resetPage)
watch([selectedTahun, selectedBulan], () => {
  resetPage()
  fetchData()
})
</script>

<template>
  <div class="fin-page px-4 pt-0">
    <Head title="Restrukturisasi Pembiayaan" />

    <div class="fin-hero mb-6">
      <div class="fin-hero__deco"></div>
      <div class="fin-hero__inner">
        <div class="d-flex flex-column flex-xl-row justify-space-between align-start align-xl-center gap-4">
          <div class="d-flex align-center gap-4">
            <div class="fin-hero__icon fin-icon-blue">
              <v-icon icon="ri-refresh-line" size="26" color="white" />
            </div>
            <div class="fin-hero__meta">
              <h1 class="fin-hero__title">Restrukturisasi Pembiayaan</h1>
              <p class="fin-hero__subtitle">Monitoring addendum, frekuensi restruk lifetime per kontrak, perubahan kolektibilitas, dan kualitas pasca-restrukturisasi.</p>
              <div class="fin-hero__badges">
                <span class="fin-badge fin-badge--warning">Addendum TOFLMBHP</span>
                <span class="fin-badge fin-badge--slate">{{ activePeriodLabel }}</span>
              </div>
            </div>
          </div>

          <div class="rs-toolbar">
            <div class="fin-filter-bar">
              <v-select v-model="selectedTahun" :items="yearOptions" label="Tahun" variant="solo" density="compact" flat hide-details rounded="lg" bg-color="white" prepend-inner-icon="ri-calendar-line" style="min-width: 120px; max-width: 140px;" />
              <v-select v-model="selectedBulan" :items="monthOptions" item-title="title" item-value="value" label="Bulan" variant="solo" density="compact" flat hide-details rounded="lg" bg-color="white" prepend-inner-icon="ri-calendar-event-line" style="min-width: 150px; max-width: 180px;" />
              <v-btn variant="text" density="comfortable" @click="fetchData" :loading="loading" icon="ri-refresh-line" color="white" />
            </div>
            <div class="rs-export-actions">
              <v-btn size="small" rounded="lg" color="success" variant="flat" :loading="isExporting" prepend-icon="ri-file-excel-2-line" @click="exportExcel">Excel</v-btn>
              <v-btn size="small" rounded="lg" color="error" variant="flat" :loading="isExporting" prepend-icon="ri-file-pdf-2-line" @click="exportPdf">PDF</v-btn>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="rs-insight-panel mb-6">
      <div class="rs-insight-card rs-insight-card--primary">
        <span>Interpretasi Operasional</span>
        <strong>{{ interpretation }}</strong>
        <small>{{ sourceInfoLabel }}</small>
      </div>
      <div class="rs-insight-card">
        <span>OS Pokok Saat Ini</span>
        <strong>{{ formatRp(totalOsSaatIniFiltered) }}</strong>
        <small>{{ formatNumber(filteredData.length) }} kontrak restrukturisasi dalam filter</small>
      </div>
      <div class="rs-insight-card">
        <span>Frekuensi Tinggi</span>
        <strong>{{ formatNumber(highFrequencyRows.length) }} kontrak</strong>
        <small>{{ formatTruncatedPercentage(highFrequencyRatio) }} - OS {{ formatRp(highFrequencyOs) }}</small>
      </div>
      <div class="rs-insight-card">
        <span>Kol Berjalan 3-5</span>
        <strong class="text-error">{{ formatNumber(currentNpfRows.length) }} kontrak</strong>
        <small>{{ formatRp(currentNpfOs) }}</small>
      </div>
    </div>

    <v-alert v-if="periodUnavailable && !loading" type="warning" variant="tonal" border="start" rounded="lg" class="mb-6">
      <div class="font-weight-bold mb-1">Periode {{ activePeriodLabel }} belum tersedia</div>
      <div class="text-body-2">{{ periodMeta.message || 'Database snapshot periode ini belum tersedia.' }}</div>
    </v-alert>

    <v-row class="mb-6">
      <v-col cols="12" sm="6" lg="3">
        <v-card class="rs-score-card" elevation="0">
          <v-icon icon="ri-file-list-3-line" size="34" color="#2563eb" />
          <div><p>Total Restruk</p><h2>{{ formatNumber(filteredData.length) }}</h2><small>{{ formatNumber(new Set(filteredData.map(item => item.nocif)).size) }} nasabah unik</small></div>
        </v-card>
      </v-col>
      <v-col cols="12" sm="6" lg="3">
        <v-card class="rs-score-card" elevation="0">
          <v-icon icon="ri-repeat-2-line" size="34" color="#d97706" />
          <div><p>Avg Lifetime Restruk</p><h2>{{ avgLifetimeRestruk }}x</h2><small>Total restruk sejak awal kontrak</small></div>
        </v-card>
      </v-col>
      <v-col cols="12" sm="6" lg="3">
        <v-card class="rs-score-card" elevation="0">
          <v-icon icon="ri-arrow-up-down-line" size="34" color="#059669" />
          <div><p>Kol Membaik</p><h2>{{ formatNumber(improvedRows.length) }}</h2><small>Memburuk {{ formatNumber(deterioratedRows.length) }} - Tetap {{ formatNumber(Math.max(filteredData.length - improvedRows.length - deterioratedRows.length, 0)) }}</small></div>
        </v-card>
      </v-col>
      <v-col cols="12" sm="6" lg="3">
        <v-card class="rs-score-card" elevation="0">
          <v-icon icon="ri-alarm-warning-line" size="34" color="#e11d48" />
          <div><p>Kontrak Prioritas</p><h2>{{ highestRiskItem?.nama || '-' }}</h2><small>{{ highestRiskItem ? `${highestRiskItem.total_restrukturisasi}x - Kol ${highestRiskItem.col_berjalan}` : 'Tidak ada data' }}</small></div>
        </v-card>
      </v-col>
    </v-row>

    <v-card class="rs-filter-card mb-6" elevation="0">
      <v-text-field v-model="searchQuery" prepend-inner-icon="ri-search-2-line" placeholder="Cari nasabah / no kontrak..." variant="outlined" density="compact" hide-details rounded="lg" />
      <v-select v-model="selectedAo" :items="aoOptions" label="Account Officer" prepend-inner-icon="ri-user-star-line" variant="outlined" density="compact" hide-details rounded="lg" />
      <v-select v-model="selectedCabang" :items="cabangOptions" label="Cabang/Wilayah" prepend-inner-icon="ri-store-2-line" variant="outlined" density="compact" hide-details rounded="lg" />
      <v-select v-model="selectedTrend" :items="trendOptions" label="Trend Kol" prepend-inner-icon="ri-arrow-up-down-line" variant="outlined" density="compact" hide-details rounded="lg" />
      <v-select v-model="selectedRisk" :items="riskOptions" label="Risk Flag" prepend-inner-icon="ri-filter-3-line" variant="outlined" density="compact" hide-details rounded="lg" />
    </v-card>

    <v-row class="mb-6 align-start">
      <v-col cols="12" lg="5">
        <div class="content-card rs-panel-card">
          <div class="content-card__header"><div><div class="content-card__title">Perubahan Kolektibilitas</div><div class="content-card__subtitle">Sebelum vs sesudah restrukturisasi pada data terfilter.</div></div></div>
          <div class="content-card__body d-flex align-center justify-center gap-6">
            <VueApexCharts v-if="!loading" type="donut" width="190" height="190" :options="trendChartOptions" :series="trendChartSeries" />
            <div class="rs-chart-legend">
              <span><i style="background:#10B981"></i>Membaik {{ formatNumber(improvedRows.length) }}</span>
              <span><i style="background:#EF4444"></i>Memburuk {{ formatNumber(deterioratedRows.length) }}</span>
              <span><i style="background:#F59E0B"></i>Tetap {{ formatNumber(Math.max(filteredData.length - improvedRows.length - deterioratedRows.length, 0)) }}</span>
            </div>
          </div>
        </div>
      </v-col>
      <v-col cols="12" lg="7">
        <div class="content-card rs-panel-card">
          <div class="content-card__header"><div><div class="content-card__title">OS Menurut Frekuensi Restruk</div><div class="content-card__subtitle">Outstanding saat ini berdasarkan total restruk lifetime per kontrak.</div></div></div>
          <div class="content-card__body">
            <VueApexCharts type="bar" height="260" :options="frequencyChart.options" :series="frequencyChart.series" />
          </div>
        </div>
      </v-col>
    </v-row>

    <v-row class="mb-6 align-start">
      <v-col cols="12" lg="5">
        <div class="content-card rs-panel-card">
          <div class="content-card__header"><div><div class="content-card__title">Bucket Risiko Restrukturisasi</div><div class="content-card__subtitle">Klasifikasi otomatis dari frekuensi restruk, Kol berjalan, dan arah perubahan kolektibilitas.</div></div></div>
          <div class="content-card__body pa-0">
            <div v-for="row in riskBucketRows" :key="row.risiko" class="rs-risk-row">
              <div>
                <v-chip size="small" :color="row.color" variant="tonal" class="font-weight-black">{{ row.risiko }}</v-chip>
                <small>{{ formatNumber(row.kontrak) }} kontrak - {{ formatNumber(row.kol_berjalan_npf) }} Kol 3-5 - {{ formatNumber(row.frekuensi_tinggi) }} frekuensi tinggi</small>
              </div>
              <span>{{ formatRp(row.os_saat_ini) }}</span>
            </div>
            <div v-if="!riskBucketRows.length" class="pa-8 text-center text-disabled">Tidak ada bucket risiko pada filter ini.</div>
          </div>
        </div>
      </v-col>
      <v-col cols="12" lg="7">
        <div class="content-card rs-panel-card">
          <div class="content-card__header"><div><div class="content-card__title">Top 10 Kontrak Pasca-Restruk</div><div class="content-card__subtitle">Prioritas review: risiko tertinggi, frekuensi restruk terbesar, lalu outstanding terbesar.</div></div></div>
          <div class="content-card__body pa-0 rs-scroll-list">
            <div v-for="item in topRiskRows" :key="`risk-${item.nokontrak}-${item.ke}`" class="rs-priority-row">
              <div class="rs-priority-main">
                <v-avatar size="34" :color="getRiskLevel(item).color" variant="tonal"><v-icon :icon="getRiskLevel(item).icon" size="18" /></v-avatar>
                <div>
                  <strong>{{ item.nama || '-' }}</strong>
                  <small>{{ item.nokontrak || '-' }} - {{ item.nama_ao || '-' }} - Restruk {{ item.total_restrukturisasi }}x - Kol {{ item.col_berjalan }}</small>
                </div>
              </div>
              <div class="rs-priority-metric">
                <v-chip size="x-small" :color="getRiskLevel(item).color" variant="flat" class="font-weight-black text-white">{{ getRiskLevel(item).label }}</v-chip>
                <span>{{ formatRp(item.osmdlc_saat_ini) }}</span>
              </div>
            </div>
            <div v-if="!topRiskRows.length" class="pa-8 text-center text-disabled">Tidak ada kontrak prioritas pada filter ini.</div>
          </div>
        </div>
      </v-col>
    </v-row>

    <div class="content-card mb-6 rs-panel-card">
      <div class="content-card__header"><div><div class="content-card__title">Prioritas Account Officer</div><div class="content-card__subtitle">Urutan kerja berdasarkan Kol 3-5, frekuensi tinggi, dan outstanding.</div></div></div>
      <div class="content-card__body pa-0 rs-scroll-list rs-scroll-list--ao">
        <div v-for="row in aoPriorityRows.slice(0, 8)" :key="row.ao" class="rs-ao-row">
          <div><strong>{{ row.ao }}</strong><small>{{ row.cabang }} - {{ row.kontrak }} kontrak - {{ row.frekuensi_tinggi }} frekuensi tinggi - {{ row.kol_berjalan_npf }} Kol 3-5</small></div>
          <span>{{ formatRp(row.os_saat_ini) }}</span>
        </div>
        <div v-if="!aoPriorityRows.length" class="pa-8 text-center text-disabled">Tidak ada prioritas AO pada filter ini.</div>
      </div>
    </div>

    <div class="content-card">
      <div class="content-card__header">
        <div><div class="content-card__title">Detail Restrukturisasi</div><div class="content-card__subtitle">Total restruk dihitung lifetime sejak awal nomor kontrak ada pada TOFLMBHP.</div></div>
        <v-chip size="x-small" color="primary" variant="flat" class="font-weight-black">{{ formatNumber(filteredData.length) }} data</v-chip>
      </div>
      <div class="content-card__body pa-0">
        <div class="overflow-x-auto">
          <table class="fin-table fin-vtable rs-table">
            <thead>
              <tr>
                <th>Nasabah / Kontrak</th>
                <th class="text-center">Addendum</th>
                <th class="text-center">Total Restruk Lifetime</th>
                <th>Akad Lama -> Baru</th>
                <th class="text-center">Kolektibilitas</th>
                <th class="text-right">OS Pokok Saat Ini</th>
                <th>Keterangan</th>
              </tr>
            </thead>
            <tbody>
              <tr v-if="loading">
                <td colspan="7" class="pa-12 text-center"><v-progress-circular indeterminate color="primary" size="46" /><div class="text-h6 text-medium-emphasis font-weight-bold mt-4">Memuat Data Restrukturisasi...</div></td>
              </tr>
              <tr v-else-if="paginatedData.length === 0">
                <td colspan="7" class="pa-12 text-center"><v-icon icon="ri-inbox-line" size="64" class="text-disabled mb-4" /><div class="text-h6 text-medium-emphasis font-weight-bold">Data Tidak Ditemukan</div><div class="text-caption text-disabled mt-1">Coba sesuaikan filter pencarian.</div></td>
              </tr>
              <tr v-for="item in paginatedData" :key="`${item.nokontrak}-${item.ke}`">
                <td>
                  <div class="font-weight-black text-uppercase">{{ item.nama }}</div>
                  <div class="text-caption text-primary font-weight-black">{{ item.nokontrak }}</div>
                  <div class="text-caption text-medium-emphasis">{{ item.nama_ao }} - {{ item.kantor_pelayanan }}</div>
                </td>
                <td class="text-center"><v-chip size="small" variant="tonal" color="primary" class="font-weight-bold">Ke-{{ item.ke }}</v-chip><div class="text-caption text-medium-emphasis mt-1">{{ item.tglakad_baru }}</div></td>
                <td class="text-center"><v-chip size="small" variant="flat" class="font-weight-black" :color="isHighFrequency(item) ? 'error' : (Number(item.total_restrukturisasi) >= 2 ? 'warning' : 'success')">{{ item.total_restrukturisasi }}x</v-chip><div class="text-caption text-medium-emphasis mt-1">{{ item.jumlah_riwayat_restrukturisasi }} riwayat</div></td>
                <td><div class="rs-akad"><span>Lama</span><strong>{{ item.akad_lama }}</strong></div><div class="rs-akad rs-akad--new"><span>Baru</span><strong>{{ item.akad_baru }}</strong></div></td>
                <td class="text-center">
                  <div class="d-flex align-center justify-center gap-2">
                    <v-chip size="x-small" variant="flat" :color="getKolColor(item.col_sblm_rest)" class="font-weight-bold">{{ item.col_sblm_rest }}</v-chip>
                    <v-icon icon="ri-arrow-right-line" color="grey" size="15" />
                    <v-chip size="x-small" variant="flat" :color="getKolColor(item.col_stlh_rest)" class="font-weight-bold">{{ item.col_stlh_rest }}</v-chip>
                  </div>
                  <div class="text-caption text-medium-emphasis mt-1">Kini Kol {{ item.col_berjalan }} - {{ getTrend(item) }}</div>
                </td>
                <td class="text-right"><div class="rs-money">{{ formatRp(item.osmdlc_saat_ini) }}</div><div class="text-caption text-medium-emphasis">OS baru {{ formatRp(item.osmdl_baru) }}</div></td>
                <td><div class="rs-note">{{ item.ket_rest || '-' }}</div></td>
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
.rs-toolbar,.rs-export-actions{display:flex;align-items:center;gap:12px;flex-wrap:wrap;justify-content:flex-end}.rs-export-actions{gap:8px}.fin-badge--slate{background:rgba(255,255,255,.12);color:#e2e8f0;border:1px solid rgba(255,255,255,.18)}
.rs-insight-panel{display:grid;grid-template-columns:minmax(0,1.4fr) repeat(3,minmax(210px,.75fr));gap:16px}.rs-insight-card,.rs-score-card,.rs-filter-card{background:linear-gradient(145deg,#fff 0%,#f8fafc 100%);border:1px solid #dbe7f3;border-radius:20px;box-shadow:0 10px 28px rgba(15,23,42,.06)}.rs-insight-card{min-height:124px;padding:18px 20px;display:flex;flex-direction:column;justify-content:center;gap:7px}.rs-insight-card span,.rs-score-card p{color:#64748b;font-size:11px;font-weight:900;text-transform:uppercase;letter-spacing:.06em;margin:0}.rs-insight-card strong{color:#0f172a;font-family:'Plus Jakarta Sans',sans-serif;font-size:clamp(.98rem,1vw,1.16rem);line-height:1.45;letter-spacing:-.02em}.rs-insight-card small,.rs-score-card small{color:#64748b;font-size:12px;font-weight:700}.rs-insight-card--primary{background:radial-gradient(circle at top right,rgba(37,99,235,.16),transparent 34%),linear-gradient(145deg,#eff6ff 0%,#fff 74%);border-color:#bfdbfe}
.rs-score-card{min-height:132px;padding:20px;display:flex;align-items:center;gap:16px;height:100%}.rs-score-card h2{color:#0f172a;font-family:'Plus Jakarta Sans',sans-serif;font-size:clamp(1rem,1.55vw,1.75rem);font-weight:900;line-height:1.1;margin:4px 0;max-width:260px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis}.rs-filter-card{padding:16px;display:grid;grid-template-columns:minmax(240px,1.2fr) repeat(4,minmax(150px,.7fr));gap:12px}.rs-chart-legend{display:flex;flex-direction:column;gap:10px}.rs-chart-legend span{font-size:12px;font-weight:800;color:#334155}.rs-chart-legend i{display:inline-block;width:10px;height:10px;border-radius:999px;margin-right:8px}.rs-ao-row{display:flex;justify-content:space-between;gap:14px;padding:14px 18px;border-bottom:1px solid #e2e8f0}.rs-ao-row strong{color:#0f172a;font-size:13px;font-weight:900}.rs-ao-row small{display:block;color:#64748b;font-size:11px;font-weight:700;margin-top:3px}.rs-ao-row span,.rs-money{color:#0f172a;font-family:'Plus Jakarta Sans',sans-serif;font-size:12px;font-weight:900;white-space:nowrap}.rs-akad{display:flex;gap:8px;align-items:center;margin:2px 0}.rs-akad span{font-size:10px;font-weight:900;text-transform:uppercase;background:#f1f5f9;color:#64748b;border-radius:6px;padding:3px 6px}.rs-akad--new span{background:#dcfce7;color:#15803d}.rs-akad strong{font-size:12px;color:#0f172a;max-width:240px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap}.rs-note{font-size:12px;color:#475569;max-width:260px;line-height:1.35}.rs-table :deep(th){height:52px!important;letter-spacing:.5px!important}.rs-table :deep(td){height:76px!important;vertical-align:middle}
.rs-panel-card{height:auto;overflow:hidden}.rs-scroll-list{max-height:420px;overflow:auto;overscroll-behavior:contain;scrollbar-width:thin;scrollbar-color:#cbd5e1 transparent}.rs-scroll-list--ao{max-height:390px}.rs-scroll-list::-webkit-scrollbar{width:8px}.rs-scroll-list::-webkit-scrollbar-track{background:transparent}.rs-scroll-list::-webkit-scrollbar-thumb{background:#cbd5e1;border-radius:999px;border:2px solid #fff}.rs-risk-row,.rs-priority-row{display:flex;justify-content:space-between;gap:16px;padding:15px 18px;border-bottom:1px solid #e2e8f0}.rs-risk-row:last-child,.rs-priority-row:last-child{border-bottom:0}.rs-risk-row small,.rs-priority-row small{display:block;color:#64748b;font-size:11px;font-weight:800;margin-top:5px}.rs-risk-row span,.rs-priority-metric span{display:block;color:#0f172a;font-family:'Plus Jakarta Sans',sans-serif;font-size:12px;font-weight:950;white-space:nowrap}.rs-priority-main{min-width:0;display:flex;align-items:center;gap:12px}.rs-priority-main>div{min-width:0}.rs-priority-main strong{display:block;color:#0f172a;font-size:13px;font-weight:950;text-transform:uppercase;overflow:hidden;text-overflow:ellipsis;white-space:nowrap}.rs-priority-metric{display:flex;min-width:170px;align-items:flex-end;flex-direction:column;gap:7px}
@media(max-width:1180px){.rs-insight-panel{grid-template-columns:1fr 1fr}.rs-filter-card{grid-template-columns:1fr 1fr}}@media(max-width:720px){.rs-toolbar,.rs-export-actions{width:100%}.rs-export-actions .v-btn{flex:1}.rs-insight-panel,.rs-filter-card{grid-template-columns:1fr}.rs-priority-row,.rs-risk-row{flex-direction:column}.rs-priority-metric{align-items:flex-start;min-width:0}}
</style>
