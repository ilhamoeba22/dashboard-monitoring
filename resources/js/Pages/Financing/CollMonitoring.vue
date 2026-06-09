<script setup>
import { ref, computed, onMounted, watch } from 'vue'
import { Head } from '@inertiajs/vue3'
import DefaultLayout from '@/layouts/default.vue'
import { useTunggakanStore } from '@/stores/tunggakanStore'
import { storeToRefs } from 'pinia'
import VueApexCharts from 'vue3-apexcharts'
import '@/assets/css/financing-shared.css'
import { formatExactRupiah, formatExactNumber, formatTruncatedPercentage, toFiniteNumber } from '@/utils/money'

defineOptions({ layout: DefaultLayout })

const store = useTunggakanStore()
const {
  collMonitoringData,
  collMonitoringMeta,
  loadingCollMonitoring,
  errorCollMonitoring,
  selectedCabang,
} = storeToRefs(store)

const cabangs = ref([])
const selectedAO = ref('Semua AO')
const selectedKolCurr = ref('Semua')
const selectedKolEom = ref('Semua')
const selectedTrend = ref('Semua')
const selectedRiskLevel = ref('Semua Risiko')
const searchQuery = ref('')
const isExporting = ref(false)
const currentPage = ref(1)
const itemsPerPage = ref(15)

const kolOptions = ['Semua', '1', '2', '3', '4', '5']
const trendOptions = ['Semua', 'Memburuk', 'Tetap', 'Membaik', 'Masuk NPF', 'Keluar NPF']
const riskOptions = ['Semua Risiko', 'Kritis', 'Tinggi', 'Menengah', 'Stabil']

const aoOptions = computed(() => {
  const aos = collMonitoringData.value.map(item => item.Nama_AO).filter(Boolean)
  return ['Semua AO', ...new Set(aos)].sort()
})

const activePeriodLabel = computed(() => {
  const raw = String(collMonitoringMeta.value?.active_period || '')
  if (raw.length !== 6) return 'Periode aktif CBS'
  const monthNames = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember']
  return `${monthNames[Number(raw.slice(4, 6)) - 1] || '-'} ${raw.slice(0, 4)}`
})

const sourceInfoLabel = computed(() => {
  const db = collMonitoringMeta.value?.source_database || '-'
  const table = collMonitoringMeta.value?.source_table || '-'
  return `${db}  -  ${table}`
})

const filteredData = computed(() => {
  const search = searchQuery.value.trim().toLowerCase()
  return collMonitoringData.value.filter(item => {
    const currentKol = String(item.colbaru_final_curr || '')
    const eomKol = String(item.colbaru_final_eom || '')
    const current = Number.parseInt(currentKol || 0)
    const eom = Number.parseInt(eomKol || 0)
    const risk = getRiskLevel(item)
    const haystack = [
      item.nama,
      item.nokontrak,
      item.nocif,
      item.Nama_AO,
      item.Nama_Kantor_Cabang,
      item.Nama_Produk,
      item.Keterangan_EOM_Detail,
    ].join(' ').toLowerCase()
    const matchAO = selectedAO.value === 'Semua AO' || item.Nama_AO === selectedAO.value
    const matchKolCurr = selectedKolCurr.value === 'Semua' || currentKol === selectedKolCurr.value
    const matchKolEom = selectedKolEom.value === 'Semua' || eomKol === selectedKolEom.value
    const matchRisk = selectedRiskLevel.value === 'Semua Risiko' || risk.label === selectedRiskLevel.value
    const matchSearch = !search || haystack.includes(search)
    let matchTrend = true

    if (selectedTrend.value === 'Memburuk') matchTrend = eom > current
    else if (selectedTrend.value === 'Membaik') matchTrend = eom < current
    else if (selectedTrend.value === 'Tetap') matchTrend = eom === current
    else if (selectedTrend.value === 'Masuk NPF') matchTrend = current < 3 && eom >= 3
    else if (selectedTrend.value === 'Keluar NPF') matchTrend = current >= 3 && eom < 3

    return matchAO && matchKolCurr && matchKolEom && matchRisk && matchSearch && matchTrend
  })
})

const totalPages = computed(() => Math.max(1, Math.ceil(filteredData.value.length / itemsPerPage.value)))
const paginatedCollMonitoring = computed(() => {
  const start = (currentPage.value - 1) * itemsPerPage.value
  return filteredData.value.slice(start, start + itemsPerPage.value)
})

watch([collMonitoringData, selectedAO, selectedKolCurr, selectedKolEom, selectedTrend, selectedRiskLevel, searchQuery], () => {
  currentPage.value = 1
})

const sumBy = (rows, selector) => rows.reduce((sum, item) => sum + toFiniteNumber(selector(item)), 0)
const getCurrentKol = item => Number.parseInt(item.colbaru_final_curr || 0)
const getEomKol = item => Number.parseInt(item.colbaru_final_eom || 0)
const getOs = item => toFiniteNumber(item.osmdlc)
const isWorsening = item => getEomKol(item) > getCurrentKol(item)
const isImproving = item => getEomKol(item) < getCurrentKol(item)
const isProjectedNpf = item => getEomKol(item) >= 3
const isNewNpf = item => getCurrentKol(item) < 3 && getEomKol(item) >= 3
const isOneObligor = item => String(item.Keterangan_EOM_Detail || '').includes('ONE OBLIGOR')
const isManual = item => String(item.Keterangan_EOM_Detail || '').startsWith('MANUAL:')

const getRiskLevel = (item) => {
  const eomKol = getEomKol(item)
  const currentKol = getCurrentKol(item)
  const hariTgk = toFiniteNumber(item.Hari_TGK_EOM_Real)
  if (eomKol >= 5 || (currentKol < 3 && eomKol >= 3)) return { label: 'Kritis', color: 'error', icon: 'ri-fire-line' }
  if (eomKol >= 3 || eomKol > currentKol) return { label: 'Tinggi', color: 'warning', icon: 'ri-alert-line' }
  if (eomKol === 2 || hariTgk > 0) return { label: 'Menengah', color: 'info', icon: 'ri-radar-line' }
  return { label: 'Stabil', color: 'success', icon: 'ri-checkbox-circle-line' }
}

const getRiskColor = (label) => ({
  Kritis: 'error',
  Tinggi: 'warning',
  Menengah: 'info',
  Stabil: 'success',
}[label] || 'secondary')

const ppkaWeight = (kol) => {
  if (kol === 3) return 0.1
  if (kol === 4) return 0.5
  if (kol >= 5) return 1
  return 0
}

const monitoredOs = computed(() => sumBy(filteredData.value, getOs))
const projectedNpfRows = computed(() => filteredData.value.filter(isProjectedNpf))
const worseningRows = computed(() => filteredData.value.filter(isWorsening))
const improvingRows = computed(() => filteredData.value.filter(isImproving))
const newNpfRows = computed(() => filteredData.value.filter(isNewNpf))
const projectedNpfOs = computed(() => sumBy(projectedNpfRows.value, getOs))
const worseningOs = computed(() => sumBy(worseningRows.value, getOs))
const newNpfOs = computed(() => sumBy(newNpfRows.value, getOs))
const weightedPpkaExposure = computed(() => sumBy(filteredData.value, item => getOs(item) * ppkaWeight(getEomKol(item))))
const projectedNpfRatio = computed(() => monitoredOs.value > 0 ? (projectedNpfOs.value / monitoredOs.value) * 100 : 0)
const worseningRatio = computed(() => filteredData.value.length > 0 ? (worseningRows.value.length / filteredData.value.length) * 100 : 0)
const oneObligorCount = computed(() => filteredData.value.filter(isOneObligor).length)
const manualCount = computed(() => filteredData.value.filter(isManual).length)

const highestRiskItem = computed(() => [...filteredData.value]
  .sort((a, b) => {
    const severityDiff = getEomKol(b) - getEomKol(a)
    if (severityDiff !== 0) return severityDiff
    return getOs(b) - getOs(a)
  })[0] || null)

const topEwsRows = computed(() => [...filteredData.value]
  .sort((a, b) => {
    const riskOrder = { Kritis: 4, Tinggi: 3, Menengah: 2, Stabil: 1 }
    const riskDiff = (riskOrder[getRiskLevel(b).label] || 0) - (riskOrder[getRiskLevel(a).label] || 0)
    if (riskDiff !== 0) return riskDiff
    if (isNewNpf(b) !== isNewNpf(a)) return Number(isNewNpf(b)) - Number(isNewNpf(a))
    return getOs(b) - getOs(a)
  })
  .slice(0, 10))

const interpretation = computed(() => {
  if (errorCollMonitoring.value) return `Data belum dapat dimuat: ${errorCollMonitoring.value}`
  if (!filteredData.value.length) return 'Tidak ada fasilitas sesuai filter saat ini.'
  if (newNpfRows.value.length > 0) return `${newNpfRows.value.length} fasilitas berpotensi masuk NPF. Prioritaskan penagihan, validasi pembayaran, dan review penyebab roll-rate sebelum akhir bulan.`
  if (worseningRows.value.length > 0) return `${worseningRows.value.length} fasilitas memburuk namun belum seluruhnya masuk NPF. Fokus pada akun Kol 1/2 yang mulai naik risiko.`
  if (projectedNpfRows.value.length > 0) return 'Tidak ada tambahan NPF baru pada filter ini, tetapi akun yang sudah NPF tetap membutuhkan rencana remedial dan pencadangan.'
  return 'Portofolio dalam filter ini relatif stabil: tidak ada proyeksi NPF dan tidak ada roll-rate memburuk.'
})

const transitionRows = computed(() => {
  const groups = new Map()
  filteredData.value.forEach(item => {
    const key = `${getCurrentKol(item)}->${getEomKol(item)}`
    const current = groups.get(key) || {
      transisi: key,
      rekening: 0,
      os: 0,
      ppka_berbobot: 0,
    }
    current.rekening += 1
    current.os += getOs(item)
    current.ppka_berbobot += getOs(item) * ppkaWeight(getEomKol(item))
    groups.set(key, current)
  })

  return [...groups.values()].sort((a, b) => b.os - a.os)
})

const riskBucketRows = computed(() => {
  const groups = new Map()
  filteredData.value.forEach(item => {
    const risk = getRiskLevel(item)
    const current = groups.get(risk.label) || {
      risiko: risk.label,
      rekening: 0,
      os: 0,
      os_npf: 0,
      masuk_npf: 0,
      memburuk: 0,
      ppka_berbobot: 0,
    }
    current.rekening += 1
    current.os += getOs(item)
    if (isProjectedNpf(item)) current.os_npf += getOs(item)
    if (isNewNpf(item)) current.masuk_npf += 1
    if (isWorsening(item)) current.memburuk += 1
    current.ppka_berbobot += getOs(item) * ppkaWeight(getEomKol(item))
    groups.set(risk.label, current)
  })

  const order = { Kritis: 1, Tinggi: 2, Menengah: 3, Stabil: 4 }
  return [...groups.values()].sort((a, b) => (order[a.risiko] || 99) - (order[b.risiko] || 99))
})

const aoPriorityRows = computed(() => {
  const groups = new Map()
  filteredData.value.forEach(item => {
    const key = item.Nama_AO || 'TANPA AO'
    const current = groups.get(key) || {
      ao: key,
      cabang: item.Nama_Kantor_Cabang || '-',
      rekening: 0,
      os: 0,
      memburuk: 0,
      masuk_npf: 0,
      os_npf: 0,
      ppka_berbobot: 0,
    }
    current.rekening += 1
    current.os += getOs(item)
    if (isWorsening(item)) current.memburuk += 1
    if (isNewNpf(item)) current.masuk_npf += 1
    if (isProjectedNpf(item)) current.os_npf += getOs(item)
    current.ppka_berbobot += getOs(item) * ppkaWeight(getEomKol(item))
    groups.set(key, current)
  })

  return [...groups.values()].sort((a, b) => {
    if (b.masuk_npf !== a.masuk_npf) return b.masuk_npf - a.masuk_npf
    if (b.os_npf !== a.os_npf) return b.os_npf - a.os_npf
    return b.os - a.os
  })
})

const downgradeStats = computed(() => {
  const counts = { '1->2': 0, '2->3': 0, '3->4': 0, '4->5': 0, 'Lompat Kol': 0 }
  filteredData.value.forEach(item => {
    const current = getCurrentKol(item)
    const eom = getEomKol(item)
    if (eom > current) {
      const key = eom === current + 1 ? `${current}->${eom}` : 'Lompat Kol'
      if (Object.prototype.hasOwnProperty.call(counts, key)) counts[key] += 1
      else counts['Lompat Kol'] += 1
    }
  })

  const labels = Object.keys(counts)
  const series = labels.map(label => counts[label])
  return {
    labels,
    series,
    total: series.reduce((a, b) => a + b, 0),
    chartOptions: {
      chart: { type: 'donut', sparkline: { enabled: true } },
      labels,
      colors: ['#FBBF24', '#F59E0B', '#EA580C', '#DC2626', '#7F1D1D'],
      dataLabels: { enabled: false },
      legend: { show: false },
      plotOptions: { pie: { donut: { size: '72%' } } },
      stroke: { width: 0 },
      tooltip: { y: { formatter: value => `${value} rekening` } },
    },
  }
})

const rollRateChart = computed(() => ({
  series: [{
    name: 'Outstanding',
    data: transitionRows.value.slice(0, 8).map(row => row.os),
  }],
  options: {
    chart: { type: 'bar', toolbar: { show: false }, fontFamily: 'Inter, sans-serif' },
    colors: ['#4F46E5'],
    plotOptions: { bar: { borderRadius: 8, horizontal: true } },
    dataLabels: { enabled: false },
    xaxis: {
      categories: transitionRows.value.slice(0, 8).map(row => row.transisi),
      labels: {
        formatter: value => formatExactRupiah(value),
        style: { colors: '#64748b', fontSize: '10px' },
      },
    },
    yaxis: { labels: { style: { colors: '#0f172a', fontWeight: 800 } } },
    grid: { borderColor: '#e2e8f0', strokeDashArray: 4 },
    tooltip: { y: { formatter: value => formatExactRupiah(value) } },
  },
}))

const fetchCabangs = async () => {
  try {
    const res = await fetch('/api/v1/financing/cabangs')
    const json = await res.json()
    if (json.success) cabangs.value = json.data
  } catch (e) {
    console.error(e)
  }
}

const formatRp = value => formatExactRupiah(value)
const formatNumber = value => formatExactNumber(value)
const formatDate = value => {
  const raw = String(value || '').slice(0, 10)
  if (!raw) return '-'
  if (/^\d{4}-\d{2}-\d{2}/.test(raw)) return `${raw.slice(8, 10)}/${raw.slice(5, 7)}/${raw.slice(0, 4)}`
  if (/^\d{8}$/.test(raw)) return `${raw.slice(6, 8)}/${raw.slice(4, 6)}/${raw.slice(0, 4)}`
  return raw
}

const getColColor = (kol) => {
  const num = Number.parseInt(kol)
  if (num === 1) return 'success'
  if (num === 2) return 'info'
  if (num === 3) return 'warning'
  if (num === 4) return 'deep-orange'
  return 'error'
}

const getAlertConfig = (detail) => {
  const text = String(detail || '')
  if (text.includes('ONE OBLIGOR')) return { icon: 'ri-group-line', color: 'purple', label: 'One Obligor' }
  if (text.startsWith('MANUAL:')) return { icon: 'ri-edit-box-line', color: 'primary', label: 'Manual Override' }
  if (text.includes('Sudah Membentuk PPKA') || text.includes('Segera Tangani')) return { icon: 'ri-alert-fill', color: 'error', label: 'Tindak Lanjut PPKA' }
  if (text.includes('Kembalikan') || text.includes('Maintain ke Coll 1')) return { icon: 'ri-alarm-warning-fill', color: 'warning', label: 'Early Warning' }
  if (text.includes('Pertahankan') || text.includes('Posisi Aman')) return { icon: 'ri-checkbox-circle-fill', color: 'success', label: 'Stabil' }
  return { icon: 'ri-eye-line', color: 'secondary', label: 'Monitor' }
}

const getRollRateIcon = (current, eom) => {
  const curr = Number.parseInt(current)
  const next = Number.parseInt(eom)
  if (next > curr) return { icon: 'ri-arrow-right-down-line', color: 'error', label: 'Memburuk' }
  if (next < curr) return { icon: 'ri-arrow-right-up-line', color: 'success', label: 'Membaik' }
  return { icon: 'ri-arrow-right-line', color: 'medium-emphasis', label: 'Tetap' }
}

const buildDetailRows = () => filteredData.value.map(item => ({
  'No CIF': item.nocif || '-',
  'No Kontrak': item.nokontrak || '-',
  Nasabah: item.nama || '-',
  AO: item.Nama_AO || '-',
  Cabang: item.Nama_Kantor_Cabang || '-',
  Produk: item.Nama_Produk || '-',
  Segmen: item.Segmentasi_Pembiayaan || '-',
  'Outstanding Pokok': getOs(item),
  'Outstanding Margin': toFiniteNumber(item.osmgnc),
  'Kol Saat Ini': getCurrentKol(item),
  'Kol Proyeksi EOM': getEomKol(item),
  'Hari Tunggakan EOM': toFiniteNumber(item.Hari_TGK_EOM_Real),
  'Risk Level': getRiskLevel(item).label,
  'PPKA Berbobot Indikatif': getOs(item) * ppkaWeight(getEomKol(item)),
  'Restrukturisasi Ke': item.Restrukturisasi_Ke || '-',
  'Tanggal Hitung': formatDate(item.Tanggal_Hitung),
  'Tanggal Proyeksi EOM': formatDate(item.Tanggal_Proyeksi_EOM),
  'Keterangan EOM': item.Keterangan_EOM_Detail || '-',
}))

const buildSummaryRows = () => [
  { Metrik: 'Periode', Nilai: activePeriodLabel.value },
  { Metrik: 'Sumber Data', Nilai: sourceInfoLabel.value },
  { Metrik: 'Akun Dipantau', Nilai: filteredData.value.length },
  { Metrik: 'Outstanding Dipantau', Nilai: monitoredOs.value },
  { Metrik: 'Outstanding Proyeksi NPF', Nilai: projectedNpfOs.value },
  { Metrik: 'Rasio Proyeksi NPF', Nilai: projectedNpfRatio.value },
  { Metrik: 'Akun Memburuk', Nilai: worseningRows.value.length },
  { Metrik: 'Outstanding Memburuk', Nilai: worseningOs.value },
  { Metrik: 'Akun Masuk NPF Baru', Nilai: newNpfRows.value.length },
  { Metrik: 'Outstanding Masuk NPF Baru', Nilai: newNpfOs.value },
  { Metrik: 'Estimasi PPKA Berbobot Indikatif', Nilai: weightedPpkaExposure.value },
  { Metrik: 'One Obligor Flag', Nilai: oneObligorCount.value },
  { Metrik: 'Manual Override', Nilai: manualCount.value },
]

const exportExcel = async () => {
  if (isExporting.value) return
  isExporting.value = true
  try {
    const XLSX = await import('xlsx')
    const workbook = XLSX.utils.book_new()
    XLSX.utils.book_append_sheet(workbook, XLSX.utils.json_to_sheet(buildSummaryRows()), '00 Summary')
    XLSX.utils.book_append_sheet(workbook, XLSX.utils.json_to_sheet(buildDetailRows()), '01 Detail Akun')
    XLSX.utils.book_append_sheet(workbook, XLSX.utils.json_to_sheet(aoPriorityRows.value), '02 Prioritas AO')
    XLSX.utils.book_append_sheet(workbook, XLSX.utils.json_to_sheet(transitionRows.value), '03 Roll Rate')
    XLSX.utils.book_append_sheet(workbook, XLSX.utils.json_to_sheet(riskBucketRows.value), '04 Bucket Risiko')
    XLSX.utils.book_append_sheet(workbook, XLSX.utils.json_to_sheet(topEwsRows.value.map(item => ({
      Nasabah: item.nama || '-',
      Kontrak: item.nokontrak || '-',
      AO: item.Nama_AO || '-',
      Cabang: item.Nama_Kantor_Cabang || '-',
      Risiko: getRiskLevel(item).label,
      'Kol Saat Ini': getCurrentKol(item),
      'Kol EOM': getEomKol(item),
      'Outstanding Pokok': getOs(item),
      'Keterangan': item.Keterangan_EOM_Detail || '-',
    }))), '05 Top EWS')
    XLSX.writeFile(workbook, `coll-monitoring-eom-${activePeriodLabel.value.replace(/\s+/g, '-')}.xlsx`)
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
    doc.text('Coll Monitoring EOM - Proyeksi Kolektibilitas', 40, 38)
    doc.setFont('helvetica', 'normal')
    doc.setFontSize(9)
    doc.text(`${activePeriodLabel.value}  -  ${sourceInfoLabel.value}`, 40, 56)
    doc.text(`OS Proyeksi NPF ${formatRp(projectedNpfOs.value)}  -  Rasio ${formatTruncatedPercentage(projectedNpfRatio.value)}  -  Masuk NPF Baru ${newNpfRows.value.length} rekening`, 40, 70)
    doc.autoTable({
      startY: 92,
      head: [['Nasabah', 'Kontrak', 'AO', 'OS Pokok', 'Kol Kini', 'Kol EOM', 'Hari TGK EOM', 'Keterangan']],
      body: buildDetailRows().map(row => [
        row.Nasabah,
        row['No Kontrak'],
        row.AO,
        formatRp(row['Outstanding Pokok']),
        row['Kol Saat Ini'],
        row['Kol Proyeksi EOM'],
        row['Hari Tunggakan EOM'],
        row['Keterangan EOM'],
      ]),
      styles: { fontSize: 7, cellPadding: 4, overflow: 'linebreak' },
      headStyles: { fillColor: [79, 70, 229], textColor: 255, fontStyle: 'bold' },
      columnStyles: {
        3: { halign: 'right' },
        4: { halign: 'center' },
        5: { halign: 'center' },
        6: { halign: 'center' },
        7: { cellWidth: 210 },
      },
      didDrawPage: () => {
        doc.setFontSize(8)
        doc.text(`Generated: ${new Date().toLocaleString('id-ID')}`, 40, doc.internal.pageSize.height - 20)
      },
    })
    doc.save(`coll-monitoring-eom-${activePeriodLabel.value.replace(/\s+/g, '-')}.pdf`)
  } finally {
    isExporting.value = false
  }
}

onMounted(() => {
  fetchCabangs()
  store.fetchCollMonitoring()
})

watch(selectedCabang, () => store.fetchCollMonitoring())
</script>

<template>
  <div class="fin-page px-4 pt-0">
    <Head title="Coll Monitoring EOM" />

    <div class="fin-hero mb-6">
      <div class="fin-hero__deco"></div>
      <div class="fin-hero__inner">
        <div class="d-flex flex-column flex-xl-row justify-space-between align-start align-xl-center gap-4">
          <div class="d-flex align-center gap-4">
            <div class="fin-hero__icon fin-icon-purple">
              <v-icon icon="ri-radar-line" size="26" color="white" />
            </div>
            <div class="fin-hero__meta">
              <h1 class="fin-hero__title">Coll Monitoring <span class="text-white">EOM</span></h1>
              <p class="fin-hero__subtitle">Proyeksi kolektibilitas akhir bulan berbasis jadwal angsuran, jatuh tempo, override manual, dan one obligor.</p>
              <div class="fin-hero__badges">
                <span class="fin-badge fin-badge--warning">Proyeksi Akhir Bulan</span>
                <span class="fin-badge fin-badge--slate">{{ activePeriodLabel }}</span>
              </div>
            </div>
          </div>

          <div class="cm-toolbar">
            <div class="fin-filter-bar">
              <v-select
                v-model="selectedCabang"
                :items="['Semua Cabang', ...cabangs.map(c => c.nama)]"
                label="Operational Unit"
                variant="solo"
                density="compact"
                flat
                hide-details
                rounded="lg"
                bg-color="white"
                prepend-inner-icon="ri-store-2-line"
                style="min-width: 210px; max-width: 270px;"
              />
              <v-btn
                variant="text"
                density="comfortable"
                @click="store.fetchCollMonitoring"
                :loading="loadingCollMonitoring"
                icon="ri-refresh-line"
                color="white"
              />
            </div>
            <div class="cm-export-actions">
              <v-btn size="small" rounded="lg" color="success" variant="flat" :loading="isExporting" prepend-icon="ri-file-excel-2-line" @click="exportExcel">Excel</v-btn>
              <v-btn size="small" rounded="lg" color="error" variant="flat" :loading="isExporting" prepend-icon="ri-file-pdf-2-line" @click="exportPdf">PDF</v-btn>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="cm-insight-panel mb-6">
      <div class="cm-insight-card cm-insight-card--primary">
        <span>Interpretasi Operasional</span>
        <strong>{{ interpretation }}</strong>
        <small>{{ sourceInfoLabel }}</small>
      </div>
      <div class="cm-insight-card">
        <span>Outstanding Dipantau</span>
        <strong>{{ formatRp(monitoredOs) }}</strong>
        <small>{{ formatNumber(filteredData.length) }} rekening dalam filter</small>
      </div>
      <div class="cm-insight-card">
        <span>Proyeksi NPF EOM</span>
        <strong class="text-error">{{ formatRp(projectedNpfOs) }}</strong>
        <small>{{ formatTruncatedPercentage(projectedNpfRatio) }} dari outstanding dipantau</small>
      </div>
      <div class="cm-insight-card">
        <span>Estimasi PPKA Berbobot</span>
        <strong>{{ formatRp(weightedPpkaExposure) }}</strong>
        <small>Indikatif dari Kol EOM: 3=10%, 4=50%, 5=100%</small>
      </div>
    </div>

    <v-row class="mb-6">
      <v-col cols="12" sm="6" lg="3">
        <v-card class="cm-score-card" elevation="0">
          <v-icon icon="ri-arrow-right-down-line" size="34" color="#e11d48" />
          <div>
            <p>Akun Memburuk</p>
            <h2>{{ formatNumber(worseningRows.length) }}</h2>
            <small>{{ formatRp(worseningOs) }}</small>
          </div>
        </v-card>
      </v-col>
      <v-col cols="12" sm="6" lg="3">
        <v-card class="cm-score-card" elevation="0">
          <v-icon icon="ri-alarm-warning-line" size="34" color="#f97316" />
          <div>
            <p>Masuk NPF Baru</p>
            <h2>{{ formatNumber(newNpfRows.length) }}</h2>
            <small>{{ formatRp(newNpfOs) }}</small>
          </div>
        </v-card>
      </v-col>
      <v-col cols="12" sm="6" lg="3">
        <v-card class="cm-score-card" elevation="0">
          <v-icon icon="ri-group-line" size="34" color="#7c3aed" />
          <div>
            <p>One Obligor Flag</p>
            <h2>{{ formatNumber(oneObligorCount) }}</h2>
            <small>Terpengaruh kolektibilitas grup CIF</small>
          </div>
        </v-card>
      </v-col>
      <v-col cols="12" sm="6" lg="3">
        <v-card class="cm-score-card" elevation="0">
          <v-icon icon="ri-edit-box-line" size="34" color="#0284c7" />
          <div>
            <p>Manual Override</p>
            <h2>{{ formatNumber(manualCount) }}</h2>
            <small>Flag dari TOFMPCOL aktif sampai EOM</small>
          </div>
        </v-card>
      </v-col>
    </v-row>

    <v-card elevation="0" class="cm-filter-card mb-6">
      <v-text-field v-model="searchQuery" label="Cari nasabah, kontrak, CIF, AO" density="compact" variant="outlined" hide-details rounded="lg" prepend-inner-icon="ri-search-line" clearable />
      <v-select v-model="selectedAO" :items="aoOptions" label="Account Officer" density="compact" variant="outlined" hide-details rounded="lg" prepend-inner-icon="ri-user-settings-line" />
      <v-select v-model="selectedKolCurr" :items="kolOptions" label="Kol Hari Ini" density="compact" variant="outlined" hide-details rounded="lg" />
      <v-select v-model="selectedKolEom" :items="kolOptions" label="Proyeksi EOM" density="compact" variant="outlined" hide-details rounded="lg" />
      <v-select v-model="selectedTrend" :items="trendOptions" label="Trend Roll-Rate" density="compact" variant="outlined" hide-details rounded="lg" prepend-inner-icon="ri-line-chart-line" />
      <v-select v-model="selectedRiskLevel" :items="riskOptions" label="Level Risiko" density="compact" variant="outlined" hide-details rounded="lg" prepend-inner-icon="ri-shield-flash-line" />
    </v-card>

    <v-row class="mb-6 align-start">
      <v-col cols="12" lg="7">
        <div class="content-card cm-panel-card cm-panel-card--chart">
          <div class="content-card__header">
            <div>
              <div class="content-card__title">Roll-Rate Exposure</div>
              <div class="content-card__subtitle">Transisi kolektibilitas menurut outstanding pokok.</div>
            </div>
          </div>
          <div class="content-card__body cm-chart-body">
            <VueApexCharts type="bar" height="280" :options="rollRateChart.options" :series="rollRateChart.series" />
          </div>
        </div>
      </v-col>
      <v-col cols="12" lg="5">
        <div class="content-card cm-panel-card">
          <div class="content-card__header">
            <div>
              <div class="content-card__title">Prioritas Account Officer</div>
              <div class="content-card__subtitle">Urutan kerja berdasarkan akun masuk NPF dan OS berisiko.</div>
            </div>
          </div>
          <div class="content-card__body pa-0 cm-scroll-list">
            <div v-for="row in aoPriorityRows.slice(0, 8)" :key="row.ao" class="cm-ao-row">
              <div>
                <strong>{{ row.ao }}</strong>
                <small>{{ row.cabang }}  -  {{ row.rekening }} rekening  -  {{ row.masuk_npf }} masuk NPF</small>
              </div>
              <span>{{ formatRp(row.os_npf) }}</span>
            </div>
            <div v-if="!aoPriorityRows.length" class="pa-8 text-center text-disabled">Tidak ada prioritas AO pada filter ini.</div>
          </div>
        </div>
      </v-col>
    </v-row>

    <v-row class="mb-6 align-start">
      <v-col cols="12" lg="5">
        <div class="content-card cm-panel-card">
          <div class="content-card__header">
            <div>
              <div class="content-card__title">Bucket Risiko EOM</div>
              <div class="content-card__subtitle">Klasifikasi otomatis dari roll-rate, Kol EOM, hari tunggakan, dan migrasi NPF baru.</div>
            </div>
          </div>
          <div class="content-card__body pa-0">
            <div v-for="row in riskBucketRows" :key="row.risiko" class="cm-risk-row">
              <div>
                <v-chip size="small" :color="getRiskColor(row.risiko)" variant="tonal" class="font-weight-black">{{ row.risiko }}</v-chip>
                <small>{{ formatNumber(row.rekening) }} rekening - {{ formatNumber(row.masuk_npf) }} masuk NPF baru</small>
              </div>
              <div class="text-right">
                <strong>{{ formatRp(row.os) }}</strong>
                <span>PPKA indikatif {{ formatRp(row.ppka_berbobot) }}</span>
              </div>
            </div>
            <div v-if="!riskBucketRows.length" class="pa-8 text-center text-disabled">Tidak ada bucket risiko pada filter ini.</div>
          </div>
        </div>
      </v-col>
      <v-col cols="12" lg="7">
        <div class="content-card cm-panel-card">
          <div class="content-card__header">
            <div>
              <div class="content-card__title">Top 10 Early Warning Account</div>
              <div class="content-card__subtitle">Daftar kerja prioritas: risiko tertinggi, migrasi NPF baru, lalu outstanding terbesar.</div>
            </div>
          </div>
          <div class="content-card__body pa-0 cm-scroll-list cm-scroll-list--ews">
            <div v-for="item in topEwsRows" :key="`ews-${item.nokontrak}`" class="cm-ews-row">
              <div class="cm-ews-main">
                <v-avatar size="34" :color="getRiskLevel(item).color" variant="tonal">
                  <v-icon :icon="getRiskLevel(item).icon" size="18" />
                </v-avatar>
                <div>
                  <strong>{{ item.nama || '-' }}</strong>
                  <small>{{ item.nokontrak || '-' }} - {{ item.Nama_AO || '-' }} - {{ item.Nama_Kantor_Cabang || '-' }}</small>
                </div>
              </div>
              <div class="cm-ews-metric">
                <v-chip size="x-small" :color="getRiskLevel(item).color" variant="flat" class="font-weight-black text-white">{{ getRiskLevel(item).label }}</v-chip>
                <span>{{ formatRp(item.osmdlc) }}</span>
              </div>
            </div>
            <div v-if="!topEwsRows.length" class="pa-8 text-center text-disabled">Tidak ada account EWS pada filter ini.</div>
          </div>
        </div>
      </v-col>
    </v-row>

    <div class="content-card">
      <div class="content-card__header">
        <div>
          <div class="content-card__title">Detail Proyeksi Kolektibilitas EOM</div>
          <div class="content-card__subtitle">Rincian akun dengan OS pokok, roll-rate, hari tunggakan EOM, dan action intelligence.</div>
        </div>
        <div class="d-flex align-center gap-2">
          <v-chip size="x-small" color="primary" variant="flat" class="font-weight-black">{{ activePeriodLabel }}</v-chip>
          <v-chip size="x-small" color="secondary" variant="tonal" class="font-weight-black">{{ formatNumber(filteredData.length) }} data</v-chip>
        </div>
      </div>

      <div class="content-card__body pa-0">
        <div class="overflow-x-auto">
          <table class="fin-table fin-vtable enterprise-table">
            <thead>
              <tr>
                <th class="text-center" style="width: 60px">NO</th>
                <th class="text-left">NASABAH & KONTRAK</th>
                <th class="text-right">O/S POKOK</th>
                <th class="text-center">EST. HARI TGK</th>
                <th class="text-center">ROLL-RATE</th>
                <th class="text-center">RISIKO</th>
                <th class="text-left">ACTION INTELLIGENCE</th>
                <th class="text-left">OFFICER</th>
              </tr>
            </thead>
            <tbody v-if="!loadingCollMonitoring">
              <tr v-for="(item, index) in paginatedCollMonitoring" :key="item.nokontrak">
                <td class="text-center text-medium-emphasis font-weight-bold">{{ (currentPage - 1) * itemsPerPage + index + 1 }}</td>
                <td class="py-4">
                  <div class="font-weight-black text-uppercase text-subtitle-2 mb-0">{{ item.nama }}</div>
                  <div class="text-caption text-primary font-weight-black">{{ item.nokontrak }}</div>
                  <div class="text-caption text-medium-emphasis">{{ item.Nama_Produk || '-' }}  -  CIF {{ item.nocif || '-' }}</div>
                </td>
                <td class="text-right font-weight-black cm-money">{{ formatRp(item.osmdlc) }}</td>
                <td class="text-center">
                  <v-chip size="small" variant="tonal" class="font-weight-black">{{ formatNumber(item.Hari_TGK_EOM_Real) }}</v-chip>
                </td>
                <td class="text-center">
                  <div v-if="item.colbaru_final_curr === item.colbaru_final_eom" class="d-flex align-center justify-center">
                    <v-chip size="small" variant="tonal" class="font-weight-bold" :color="getColColor(item.colbaru_final_curr)">
                      Tetap Kol {{ item.colbaru_final_curr }}
                    </v-chip>
                  </div>
                  <div v-else class="d-flex align-center justify-center gap-3">
                    <v-avatar :color="getColColor(item.colbaru_final_curr)" size="32" variant="tonal" class="font-weight-black">{{ item.colbaru_final_curr }}</v-avatar>
                    <v-icon :icon="getRollRateIcon(item.colbaru_final_curr, item.colbaru_final_eom).icon" :color="getRollRateIcon(item.colbaru_final_curr, item.colbaru_final_eom).color" size="20" />
                    <v-avatar :color="getColColor(item.colbaru_final_eom)" size="36" variant="elevated" elevation="2" class="font-weight-black text-white">{{ item.colbaru_final_eom }}</v-avatar>
                  </div>
                </td>
                <td class="text-center">
                  <v-chip size="small" :color="getRiskLevel(item).color" variant="tonal" class="font-weight-black">
                    <v-icon :icon="getRiskLevel(item).icon" start size="16" />
                    {{ getRiskLevel(item).label }}
                  </v-chip>
                </td>
                <td class="py-3" style="min-width: 320px">
                  <div class="cm-action-box">
                    <v-icon :icon="getAlertConfig(item.Keterangan_EOM_Detail).icon" :color="getAlertConfig(item.Keterangan_EOM_Detail).color" size="20" />
                    <div>
                      <div class="text-caption font-weight-black text-uppercase mb-1">{{ getAlertConfig(item.Keterangan_EOM_Detail).label }}</div>
                      <div class="text-caption font-weight-medium">{{ item.Keterangan_EOM_Detail || '-' }}</div>
                    </div>
                  </div>
                </td>
                <td class="py-3">
                  <div class="text-caption font-weight-black text-uppercase">{{ item.Nama_AO || '-' }}</div>
                  <div class="text-caption text-medium-emphasis font-weight-bold">{{ item.Nama_Kantor_Cabang || '-' }}</div>
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        <div v-if="loadingCollMonitoring" class="fin-loading">
          <v-progress-circular indeterminate color="primary" size="48" />
        </div>

        <div v-else-if="filteredData.length === 0" class="text-center pa-16">
          <v-avatar color="grey-lighten-4" size="100" class="mb-4">
            <v-icon icon="ri-radar-line" size="48" color="grey" />
          </v-avatar>
          <div class="text-h6 font-weight-bold text-medium-emphasis">Tidak ada data sesuai kriteria filter.</div>
          <p class="text-subtitle-2 text-disabled">Sesuaikan filter atau refresh data CBS.</p>
        </div>

        <v-divider v-if="filteredData.length > 0" />
        <div v-if="filteredData.length > 0" class="pa-4 d-flex align-center justify-space-between bg-slate-50">
          <div class="text-caption text-medium-emphasis font-weight-bold">
            Menampilkan {{ (currentPage - 1) * itemsPerPage + 1 }} - {{ Math.min(currentPage * itemsPerPage, filteredData.length) }} dari {{ filteredData.length }} data
          </div>
          <v-pagination v-model="currentPage" :length="totalPages" :total-visible="5" density="compact" variant="flat" active-color="primary" />
        </div>
      </div>
    </div>
  </div>
</template>

<style scoped>
.cm-toolbar {
  display: flex;
  align-items: center;
  gap: 12px;
  flex-wrap: wrap;
  justify-content: flex-end;
}

.cm-export-actions {
  display: flex;
  align-items: center;
  gap: 8px;
}

.fin-badge--slate {
  background: rgba(255, 255, 255, 0.12);
  color: #e2e8f0;
  border: 1px solid rgba(255, 255, 255, 0.18);
}

.cm-insight-panel {
  display: grid;
  grid-template-columns: minmax(0, 1.4fr) repeat(3, minmax(210px, 0.75fr));
  gap: 16px;
}

.cm-insight-card,
.cm-score-card,
.cm-filter-card {
  background: linear-gradient(145deg, #ffffff 0%, #f8fafc 100%);
  border: 1px solid #dbe7f3;
  border-radius: 20px;
  box-shadow: 0 10px 28px rgba(15, 23, 42, 0.06);
}

.cm-insight-card {
  min-height: 124px;
  padding: 18px 20px;
  display: flex;
  flex-direction: column;
  justify-content: center;
  gap: 7px;
}

.cm-insight-card span,
.cm-score-card p {
  color: #64748b;
  font-size: 11px;
  font-weight: 900;
  text-transform: uppercase;
  letter-spacing: 0.06em;
  margin: 0;
}

.cm-insight-card strong {
  color: #0f172a;
  font-family: 'Plus Jakarta Sans', sans-serif;
  font-size: clamp(0.98rem, 1vw, 1.16rem);
  line-height: 1.45;
  letter-spacing: -0.02em;
}

.cm-insight-card small,
.cm-score-card small {
  color: #64748b;
  font-size: 12px;
  font-weight: 700;
}

.cm-insight-card--primary {
  background:
    radial-gradient(circle at top right, rgba(79, 70, 229, 0.16), transparent 34%),
    linear-gradient(145deg, #eef2ff 0%, #ffffff 74%);
  border-color: #c7d2fe;
}

.cm-score-card {
  min-height: 132px;
  padding: 20px;
  display: flex;
  align-items: center;
  gap: 16px;
  height: 100%;
}

.cm-score-card h2 {
  color: #0f172a;
  font-family: 'Plus Jakarta Sans', sans-serif;
  font-size: 1.75rem;
  font-weight: 900;
  line-height: 1.1;
  margin: 4px 0;
}

.cm-filter-card {
  padding: 16px;
  display: grid;
  grid-template-columns: minmax(260px, 1.4fr) repeat(5, minmax(150px, 0.72fr));
  gap: 12px;
}

.cm-panel-card {
  height: auto;
  overflow: hidden;
}

.cm-panel-card--chart {
  max-width: 100%;
}

.cm-chart-body {
  padding: 18px 22px 14px;
}

.cm-scroll-list {
  max-height: 376px;
  overflow: auto;
  overscroll-behavior: contain;
  scrollbar-width: thin;
  scrollbar-color: #cbd5e1 transparent;
}

.cm-scroll-list--ews {
  max-height: 430px;
}

.cm-scroll-list::-webkit-scrollbar {
  width: 8px;
}

.cm-scroll-list::-webkit-scrollbar-track {
  background: transparent;
}

.cm-scroll-list::-webkit-scrollbar-thumb {
  background: #cbd5e1;
  border-radius: 999px;
  border: 2px solid #ffffff;
}

.cm-ao-row {
  display: flex;
  justify-content: space-between;
  gap: 14px;
  padding: 14px 18px;
  border-bottom: 1px solid #e2e8f0;
}

.cm-ao-row strong {
  color: #0f172a;
  font-size: 13px;
  font-weight: 900;
}

.cm-ao-row small {
  display: block;
  color: #64748b;
  font-size: 11px;
  font-weight: 700;
  margin-top: 3px;
}

.cm-ao-row span,
.cm-money {
  color: #0f172a;
  font-family: 'Plus Jakarta Sans', sans-serif;
  font-size: 12px;
  font-weight: 900;
  white-space: nowrap;
}

.cm-risk-row,
.cm-ews-row {
  display: flex;
  justify-content: space-between;
  gap: 16px;
  padding: 15px 18px;
  border-bottom: 1px solid #e2e8f0;
}

.cm-risk-row:last-child,
.cm-ews-row:last-child,
.cm-ao-row:last-child {
  border-bottom: 0;
}

.cm-risk-row small,
.cm-risk-row span,
.cm-ews-row small {
  display: block;
  color: #64748b;
  font-size: 11px;
  font-weight: 800;
  margin-top: 5px;
}

.cm-risk-row strong,
.cm-ews-row strong {
  color: #0f172a;
  font-size: 13px;
  font-weight: 950;
  letter-spacing: -0.01em;
}

.cm-ews-main {
  min-width: 0;
  display: flex;
  align-items: center;
  gap: 12px;
}

.cm-ews-main > div {
  min-width: 0;
}

.cm-ews-main strong {
  display: block;
  overflow: hidden;
  text-overflow: ellipsis;
  text-transform: uppercase;
  white-space: nowrap;
}

.cm-ews-metric {
  display: flex;
  min-width: 170px;
  align-items: flex-end;
  flex-direction: column;
  gap: 7px;
}

.cm-ews-metric span {
  color: #0f172a;
  font-family: 'Plus Jakarta Sans', sans-serif;
  font-size: 12px;
  font-weight: 950;
  white-space: nowrap;
}

.cm-action-box {
  border: 1px dashed #cbd5e1;
  background: #f8fafc;
  border-radius: 14px;
  padding: 12px;
  display: flex;
  gap: 10px;
  align-items: flex-start;
  color: #334155;
}

.enterprise-table :deep(th) {
  height: 52px !important;
  letter-spacing: 0.5px !important;
}

.enterprise-table :deep(td) {
  height: 76px !important;
  vertical-align: middle;
}

@media (max-width: 1180px) {
  .cm-insight-panel {
    grid-template-columns: 1fr 1fr;
  }

  .cm-filter-card {
    grid-template-columns: 1fr 1fr;
  }
}

@media (max-width: 720px) {
  .cm-toolbar,
  .cm-export-actions {
    width: 100%;
  }

  .cm-export-actions .v-btn {
    flex: 1;
  }

  .cm-insight-panel,
  .cm-filter-card {
    grid-template-columns: 1fr;
  }
}
</style>
