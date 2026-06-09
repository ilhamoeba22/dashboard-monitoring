<script setup>
import { ref, computed, onMounted, watch } from 'vue'
import { Head } from '@inertiajs/vue3'
import DefaultLayout from '@/layouts/default.vue'
import { useTunggakanStore } from '@/stores/tunggakanStore'
import { storeToRefs } from 'pinia'
import VueApexCharts from 'vue3-apexcharts'
import '@/assets/css/financing-shared.css'
import { formatExactNumber, formatExactRupiah, formatTruncatedPercentage, toFiniteNumber } from '@/utils/money'

defineOptions({ layout: DefaultLayout })

const store = useTunggakanStore()
const {
  jatuhTempoData,
  loadingJatuhTempo,
  selectedCabang,
  selectedAo,
  selectedTahun,
  selectedBulan,
  periodMeta,
  totalJatuhTempo,
  totalTagihanPokok,
  saldoStatus
} = storeToRefs(store)

const cabangs = ref([])
const searchQuery = ref('')
const selectedStatus = ref('Semua Status')
const selectedEws = ref('Semua EWS')
const isExporting = ref(false)
const snackbar = ref(false)
const snackbarMessage = ref('')
const snackbarColor = ref('warning')
const statusOptions = ['Semua Status', 'SALDO CUKUP', 'KURANG']
const ewsOptions = ['Semua EWS', 'OVERDUE', 'CRITICAL', 'WARNING', 'SAFE']
const monthOptions = [
  { title: 'Januari', value: 1 }, { title: 'Februari', value: 2 }, { title: 'Maret', value: 3 },
  { title: 'April', value: 4 }, { title: 'Mei', value: 5 }, { title: 'Juni', value: 6 },
  { title: 'Juli', value: 7 }, { title: 'Agustus', value: 8 }, { title: 'September', value: 9 },
  { title: 'Oktober', value: 10 }, { title: 'November', value: 11 }, { title: 'Desember', value: 12 },
]
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
const sourceInfoLabel = computed(() => {
  const db = periodMeta.value?.source_database || '-'
  const table = periodMeta.value?.source_table || '-'
  return `${db}  -  ${table}`
})
const aoOptions = computed(() => {
  const aos = jatuhTempoData.value.map(item => item.nmao).filter(Boolean)
  return ['Semua AO', ...new Set(aos)].sort()
})
const filteredData = computed(() => {
  const search = searchQuery.value.trim().toLowerCase()
  return jatuhTempoData.value.filter(item => {
    const status = isSaldoSufficient(item) ? 'SALDO CUKUP' : 'KURANG'
    const ews = getUrgency(item.tglexp).label
    const haystack = [
      item.nama,
      item.nokontrak,
      item.noakad,
      item.nmao,
      item.cabang,
      item.wilayah,
      item.hp,
    ].join(' ').toLowerCase()
    return (!search || haystack.includes(search))
      && (selectedStatus.value === 'Semua Status' || status === selectedStatus.value)
      && (selectedEws.value === 'Semua EWS' || ews === selectedEws.value)
  })
})
const totalTagihanPokokFiltered = computed(() => filteredData.value.reduce((sum, item) => sum + toFiniteNumber(item.tgkmdl), 0))
const totalTagihanMargin = computed(() => filteredData.value.reduce((sum, item) => sum + toFiniteNumber(item.tgkmgn), 0))
const totalTagihan = computed(() => totalTagihanPokokFiltered.value + totalTagihanMargin.value)
const totalSaldoEfektif = computed(() => filteredData.value.reduce((sum, item) => sum + toFiniteNumber(item.saving_balance), 0))
const saldoCukupCount = computed(() => filteredData.value.filter(isSaldoSufficient).length)
const saldoKurangCount = computed(() => filteredData.value.length - saldoCukupCount.value)
const saldoCukupRatio = computed(() => filteredData.value.length > 0 ? (saldoCukupCount.value / filteredData.value.length) * 100 : 0)
const shortageAmount = computed(() => filteredData.value.reduce((sum, item) => sum + shortageFor(item), 0))
const urgentItems = computed(() => filteredData.value.filter(item => ['OVERDUE', 'CRITICAL'].includes(getUrgency(item.tglexp).label)))
const highestShortageItem = computed(() => [...filteredData.value].sort((a, b) => shortageFor(b) - shortageFor(a))[0] || null)
const jatuhTempoInsight = computed(() => {
  if (periodUnavailable.value) return 'Periode belum tersedia, sehingga tabel dikosongkan agar tidak menampilkan data yang salah.'
  if (!filteredData.value.length) return 'Tidak ada kontrak jatuh tempo pada filter ini.'
  if (saldoKurangCount.value > 0 && urgentItems.value.length > 0) return 'Ada kontrak prioritas tinggi dengan saldo kurang. Fokuskan follow-up sebelum proses autodebet berjalan.'
  if (saldoKurangCount.value > 0) return 'Sebagian nasabah belum memiliki saldo efektif cukup. Prioritaskan reminder dan validasi rekening autodebet.'
  return 'Seluruh kontrak pada filter ini memiliki saldo efektif cukup untuk estimasi autodebet.'
})

// Pagination Logic
const currentPage = ref(1)
const itemsPerPage = ref(15)

const totalPages = computed(() => Math.max(1, Math.ceil(filteredData.value.length / itemsPerPage.value)))

const paginatedJatuhTempo = computed(() => {
  const start = (currentPage.value - 1) * itemsPerPage.value
  return filteredData.value.slice(start, start + itemsPerPage.value)
})

watch([jatuhTempoData, searchQuery, selectedStatus, selectedEws], () => {
  currentPage.value = 1
  selectedItems.value = []
  selectAll.value = false
})

// Critical Logic: Saldo Check
const isSaldoSufficient = (item) => {
  const tagihanTotal = parseFloat(item.tgkmdl || 0) + parseFloat(item.tgkmgn || 0)
  const saldoEfektif = parseFloat(item.saving_balance || 0)
  return saldoEfektif >= tagihanTotal
}

// Bulk Action Logic
const selectedItems = ref([])
const selectAll = ref(false)

const toggleSelectAll = () => {
  if (selectAll.value) {
    selectedItems.value = paginatedJatuhTempo.value.map(i => i.nokontrak)
  } else {
    selectedItems.value = []
  }
}

watch(selectedItems, (val) => {
  if (val.length === 0) selectAll.value = false
  else if (val.length === paginatedJatuhTempo.value.length && val.length > 0) selectAll.value = true
})

const handleBulkWA = () => {
  if (selectedItems.value.length === 0) {
    notify('Pilih minimal 1 nasabah untuk mengirim WA Blast.')
    return
  }
  const selectedRows = filteredData.value.filter(item => selectedItems.value.includes(item.nokontrak))
  const validRows = selectedRows.filter(item => {
    const hp = item.hp?.replace(/[^0-9]/g, '')
    return hp && hp !== '-' && hp.length >= 10
  })
  if (!validRows.length) {
    notify('Tidak ada nomor HP valid pada data yang dipilih.')
    return
  }
  validRows.slice(0, 5).forEach(sendWA)
  if (validRows.length > 5) {
    notify('Dibuka 5 draft WhatsApp pertama agar browser tidak memblokir pop-up massal. Lanjutkan seleksi berikutnya secara bertahap.', 'info')
  }
}

// Analytics: Kesiapan Dana Chart
const ratioChartSeries = computed(() => [saldoCukupCount.value, saldoKurangCount.value])
const ratioChartOpts = computed(() => ({
  chart: { type: 'donut', sparkline: { enabled: true } },
  labels: ['Saldo Cukup', 'Saldo Kurang'],
  colors: ['#10B981', '#EF4444'],
  plotOptions: { pie: { donut: { size: '75%' } } },
  stroke: { width: 0 },
  tooltip: { enabled: true }
}))

// Helpers
const getUrgency = (dateStr) => {
  if (!dateStr || dateStr.length !== 8) return { label: 'Unknown', color: 'grey', icon: 'ri-question-line' }

  // Parse YYYYMMDD
  const year = parseInt(dateStr.substring(0, 4))
  const month = parseInt(dateStr.substring(4, 6)) - 1
  const day = parseInt(dateStr.substring(6, 8))

  const expDate = new Date(year, month, day)
  const today = getReferenceDate()

  const diffTime = expDate - today
  const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24))

  if (diffDays < 0) return { label: 'OVERDUE', color: 'error', icon: 'ri-alarm-warning-fill' }
  if (diffDays <= 3) return { label: 'CRITICAL', color: 'deep-orange', icon: 'ri-fire-fill' }
  if (diffDays <= 7) return { label: 'WARNING', color: 'orange', icon: 'ri-error-warning-fill' }
  return { label: 'SAFE', color: 'success', icon: 'ri-checkbox-circle-fill' }
}

const getRowClass = (item) => {
  const urgency = getUrgency(item.tglexp).label
  const kurangSaldo = !isSaldoSufficient(item)
  if ((urgency === 'CRITICAL' || urgency === 'OVERDUE') && kurangSaldo) {
    return 'row--danger'
  }
  return ''
}

const formatDate = (dateStr) => {
  if (!dateStr || dateStr.length !== 8) return '-'
  return `${dateStr.substring(6, 8)}/${dateStr.substring(4, 6)}/${dateStr.substring(0, 4)}`
}

const getReferenceDate = () => {
  const today = new Date()
  today.setHours(0, 0, 0, 0)
  const year = Number(selectedTahun.value)
  const month = Number(selectedBulan.value)
  if (!year || !month) return today
  if (today.getFullYear() === year && today.getMonth() === month - 1) return today
  return new Date(year, month, 0)
}

const sendWA = (item) => {
  const hp = item.hp?.replace(/[^0-9]/g, '')
  if (!hp || hp === '-' || hp.length < 10) {
    notify('Nomor HP tidak valid atau tidak tersedia.')
    return
  }
  const formattedHp = hp.startsWith('0') ? '62' + hp.substring(1) : hp
  const tglJt = formatDate(item.tglexp)
  const tagihan = formatRp(parseFloat(item.tgkmdl || 0) + parseFloat(item.tgkmgn || 0))

  const message = `Assalamu'alaikum Bpk/Ibu ${item.nama}, kami dari BPRS HIK MCI menginformasikan bahwa angsuran pembiayaan No: ${item.nokontrak} akan jatuh tempo pada ${tglJt} sebesar ${tagihan}. Mohon pastikan saldo tabungan mencukupi untuk proses autodebet. Terima kasih.`

  window.open(`https://wa.me/${formattedHp}?text=${encodeURIComponent(message)}`, '_blank')
}

function notify(message, color = 'warning') {
  snackbarMessage.value = message
  snackbarColor.value = color
  snackbar.value = true
}

const formatRp = (value) => formatExactRupiah(value)
const formatNumber = (value) => formatExactNumber(value)
const tagihanTotal = (item) => toFiniteNumber(item.tgkmdl) + toFiniteNumber(item.tgkmgn)
const shortageFor = (item) => Math.max(tagihanTotal(item) - toFiniteNumber(item.saving_balance), 0)

const topPriorityRows = computed(() => [...filteredData.value]
  .sort((a, b) => {
    const shortageDiff = shortageFor(b) - shortageFor(a)
    if (shortageDiff !== 0) return shortageDiff
    return tagihanTotal(b) - tagihanTotal(a)
  })
  .slice(0, 10))

const aoRecapRows = computed(() => {
  const groups = new Map()
  filteredData.value.forEach(item => {
    const key = item.nmao || 'TANPA AO'
    const current = groups.get(key) || {
      ao: key,
      cabang: item.cabang || '-',
      rekening: 0,
      tagihan: 0,
      saldo: 0,
      shortage: 0,
      kurang: 0,
      critical: 0,
    }
    current.rekening += 1
    current.tagihan += tagihanTotal(item)
    current.saldo += toFiniteNumber(item.saving_balance)
    current.shortage += shortageFor(item)
    if (!isSaldoSufficient(item)) current.kurang += 1
    if (['OVERDUE', 'CRITICAL'].includes(getUrgency(item.tglexp).label)) current.critical += 1
    groups.set(key, current)
  })
  return [...groups.values()].sort((a, b) => {
    if (b.shortage !== a.shortage) return b.shortage - a.shortage
    return b.tagihan - a.tagihan
  })
})

const buildExportRows = () => filteredData.value.map(item => ({
  'No Kontrak': item.nokontrak,
  Nasabah: item.nama,
  AO: item.nmao || '-',
  Cabang: item.cabang || '-',
  Wilayah: item.wilayah || '-',
  'Tanggal Jatuh Tempo': formatDate(item.tglexp),
  'Tunggakan Pokok': Number(item.tgkmdl || 0),
  'Tunggakan Margin': Number(item.tgkmgn || 0),
  'Total Tagihan': tagihanTotal(item),
  'Saldo Efektif': Number(item.saving_balance || 0),
  'Kekurangan Saldo': shortageFor(item),
  Kolektibilitas: item.colbaru || '-',
  EWS: getUrgency(item.tglexp).label,
  'Status Debet': isSaldoSufficient(item) ? 'SALDO CUKUP' : 'KURANG',
  HP: item.hp || '-',
}))

const buildSummaryRows = () => [
  { Metrik: 'Periode', Nilai: activePeriodLabel.value },
  { Metrik: 'Sumber Data', Nilai: sourceInfoLabel.value },
  { Metrik: 'Total Antrian Filter', Nilai: filteredData.value.length },
  { Metrik: 'Total Antrian Periode', Nilai: totalJatuhTempo.value },
  { Metrik: 'Total Tagihan', Nilai: totalTagihan.value },
  { Metrik: 'Total Saldo Efektif', Nilai: totalSaldoEfektif.value },
  { Metrik: 'Saldo Cukup', Nilai: saldoCukupCount.value },
  { Metrik: 'Saldo Kurang', Nilai: saldoKurangCount.value },
  { Metrik: 'Coverage Saldo %', Nilai: saldoCukupRatio.value },
  { Metrik: 'Kekurangan Saldo', Nilai: shortageAmount.value },
]

const exportExcel = async () => {
  if (isExporting.value) return
  isExporting.value = true
  try {
    const XLSX = await import('xlsx')
    const workbook = XLSX.utils.book_new()
    XLSX.utils.book_append_sheet(workbook, XLSX.utils.json_to_sheet(buildSummaryRows()), '00 Summary')
    XLSX.utils.book_append_sheet(workbook, XLSX.utils.json_to_sheet(buildExportRows()), '01 Jatuh Tempo')
    XLSX.utils.book_append_sheet(workbook, XLSX.utils.json_to_sheet(aoRecapRows.value), '02 Rekap AO')
    XLSX.utils.book_append_sheet(workbook, XLSX.utils.json_to_sheet(topPriorityRows.value.map(item => ({
      Nasabah: item.nama || '-',
      Kontrak: item.nokontrak || '-',
      AO: item.nmao || '-',
      Cabang: item.cabang || '-',
      'Tanggal Jatuh Tempo': formatDate(item.tglexp),
      'Total Tagihan': tagihanTotal(item),
      'Saldo Efektif': toFiniteNumber(item.saving_balance),
      'Kekurangan Saldo': shortageFor(item),
      EWS: getUrgency(item.tglexp).label,
    }))), '03 Prioritas Follow Up')
    XLSX.writeFile(workbook, `jatuh-tempo-${activePeriodLabel.value.replace(/\s+/g, '-')}.xlsx`)
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
    doc.text('Jatuh Tempo & Early Warning Pembiayaan', 40, 38)
    doc.setFont('helvetica', 'normal')
    doc.setFontSize(9)
    doc.text(`${activePeriodLabel.value} - Total Tagihan ${formatRp(totalTagihan.value)} - Saldo Cukup ${formatTruncatedPercentage(saldoCukupRatio.value)}`, 40, 56)
    doc.autoTable({
      startY: 76,
      head: [['Nasabah', 'Kontrak', 'Jatuh Tempo', 'Total Tagihan', 'Saldo Efektif', 'Kekurangan', 'EWS']],
      body: buildExportRows().map(row => [
        row.Nasabah,
        row['No Kontrak'],
        row['Tanggal Jatuh Tempo'],
        formatRp(row['Total Tagihan']),
        formatRp(row['Saldo Efektif']),
        formatRp(row['Kekurangan Saldo']),
        row.EWS,
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
      doc.text(`Sumber: ${sourceInfoLabel.value}`, 32, doc.internal.pageSize.height - 18)
      doc.text(`Halaman ${page}/${pageCount}`, doc.internal.pageSize.width - 90, doc.internal.pageSize.height - 18)
    }
    doc.save(`jatuh-tempo-${activePeriodLabel.value.replace(/\s+/g, '-')}.pdf`)
  } finally {
    isExporting.value = false
  }
}

const fetchCabangs = async () => {
  try {
    const res = await fetch('/api/v1/financing/cabangs')
    const json = await res.json()
    if (json.success) cabangs.value = json.data
  } catch (e) { console.error(e) }
}

onMounted(() => {
  fetchCabangs()
  store.fetchJatuhTempo()
})

watch([selectedCabang, selectedAo, selectedTahun, selectedBulan], () => store.fetchJatuhTempo())
</script>

<template>
  <div class="fin-page px-4 pt-0">
    <Head title="Jatuh Tempo & Early Warning" />

    <!-- HERO HEADER -->
    <div class="fin-hero mb-6">
      <div class="fin-hero__deco"></div>
      <div class="fin-hero__inner">
        <div class="d-flex flex-column flex-xl-row justify-space-between align-start align-xl-center gap-4">
          <div class="d-flex align-center gap-4">
            <div class="fin-hero__icon fin-icon-amber">
              <v-icon icon="ri-alarm-warning-line" size="26" color="white" />
            </div>
            <div class="fin-hero__meta">
              <h1 class="fin-hero__title">Jatuh Tempo & Early Warning</h1>
              <p class="fin-hero__subtitle">Monitoring kontrak jatuh tempo, kesiapan saldo efektif, dan prioritas follow-up autodebet.</p>
              <div class="fin-hero__badges">
                <span class="fin-badge fin-badge--warning">EWS Module</span>
                <span class="fin-badge fin-badge--slate">{{ activePeriodLabel }}</span>
              </div>
            </div>
          </div>

          <div class="jt-toolbar">
            <div class="fin-filter-bar">
              <v-select
                v-model="selectedTahun"
                :items="yearOptions"
                label="Tahun"
                variant="solo"
                density="compact"
                flat
                hide-details
                rounded="lg"
                bg-color="white"
                prepend-inner-icon="ri-calendar-line"
                style="min-width: 120px; max-width: 140px;"
              ></v-select>
              <v-select
                v-model="selectedBulan"
                :items="monthOptions"
                item-title="title"
                item-value="value"
                label="Bulan"
                variant="solo"
                density="compact"
                flat
                hide-details
                rounded="lg"
                bg-color="white"
                prepend-inner-icon="ri-calendar-event-line"
                style="min-width: 150px; max-width: 180px;"
              ></v-select>
              <v-select
                v-model="selectedCabang"
                :items="['Semua Cabang', ...cabangs.map(c => c.nama)]"
                label="Filter Cabang"
                variant="solo"
                density="compact"
                flat
                hide-details
                rounded="lg"
                bg-color="white"
                prepend-inner-icon="ri-store-2-line"
                style="min-width: 180px; max-width: 240px;"
              ></v-select>
              <v-select
                v-model="selectedAo"
                :items="aoOptions"
                label="Filter AO"
                variant="solo"
                density="compact"
                flat
                hide-details
                rounded="lg"
                bg-color="white"
                prepend-inner-icon="ri-user-settings-line"
                style="min-width: 180px; max-width: 240px;"
              ></v-select>
              <v-btn
                variant="text"
                density="comfortable"
                @click="store.fetchJatuhTempo"
                :loading="loadingJatuhTempo"
                icon="ri-refresh-line"
                color="white"
              ></v-btn>
            </div>
            <div class="jt-export-actions">
              <v-btn size="small" rounded="lg" color="success" variant="flat" :loading="isExporting" prepend-icon="ri-file-excel-2-line" @click="exportExcel">Excel</v-btn>
              <v-btn size="small" rounded="lg" color="error" variant="flat" :loading="isExporting" prepend-icon="ri-file-pdf-2-line" @click="exportPdf">PDF</v-btn>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="jt-insight-panel mb-6">
      <div class="jt-insight-card jt-insight-card--primary">
        <span>Interpretasi Operasional</span>
        <strong>{{ jatuhTempoInsight }}</strong>
      </div>
      <div class="jt-insight-card">
        <span>Total Tagihan</span>
        <strong>{{ formatRp(totalTagihan) }}</strong>
        <small>Pokok {{ formatRp(totalTagihanPokokFiltered) }} + Margin {{ formatRp(totalTagihanMargin) }}</small>
      </div>
      <div class="jt-insight-card">
        <span>Saldo Cukup</span>
        <strong>{{ formatTruncatedPercentage(saldoCukupRatio) }}</strong>
        <small>{{ formatNumber(saldoCukupCount) }} cukup - {{ formatNumber(saldoKurangCount) }} kurang</small>
      </div>
      <div class="jt-insight-card">
        <span>Kekurangan Saldo</span>
        <strong>{{ formatRp(shortageAmount) }}</strong>
        <small>{{ highestShortageItem?.nama || 'Tidak ada shortage' }}</small>
      </div>
    </div>

    <!-- 2. Executive Scorecards -->
    <v-row class="mb-6">
      <v-col cols="12" md="4">
        <v-card class="rounded-xl border shadow-sm transition-swing" elevation="0" style="position: relative; overflow: hidden;">
          <div style="position: absolute; top: -20px; right: -20px; width: 120px; height: 120px; opacity: 0.08;">
            <v-icon icon="ri-user-follow-line" size="120" color="#64748b" />
          </div>
          <v-card-text class="pa-5" style="position: relative; z-index: 1;">
            <div class="d-flex justify-space-between align-start">
              <div>
                <p class="text-caption font-weight-bold text-uppercase tracking-widest mb-1" style="color: #64748B; font-family: 'Inter', sans-serif;">TOTAL ANTRIAN</p>
                <div class="d-flex align-center gap-2 mb-2">
                  <h2 class="text-h4 font-weight-bold" style="color: #64748b; font-family: 'Plus Jakarta Sans', sans-serif; line-height: 1.2;">{{ formatNumber(filteredData.length) }}</h2>
                  <VueApexCharts type="donut" width="40" height="40" :options="ratioChartOpts" :series="ratioChartSeries" />
                </div>
                <p class="text-caption text-medium-emphasis mb-0" style="font-family: 'Inter', sans-serif;">Akun jatuh tempo bulan ini</p>
              </div>
            </div>
          </v-card-text>
        </v-card>
      </v-col>

      <v-col cols="12" md="4">
        <v-card class="rounded-xl border shadow-sm transition-swing" elevation="0" style="position: relative; overflow: hidden;">
          <div style="position: absolute; top: -20px; right: -20px; width: 120px; height: 120px; opacity: 0.08;">
            <v-icon icon="ri-money-dollar-circle-line" size="120" color="#3b82f6" />
          </div>
          <v-card-text class="pa-5" style="position: relative; z-index: 1;">
            <div class="d-flex justify-space-between align-start">
              <div>
                <p class="text-caption font-weight-bold text-uppercase tracking-widest mb-1" style="color: #64748B; font-family: 'Inter', sans-serif;">PROYEKSI TAGIHAN</p>
                <h2 class="jt-money-exact mb-2" style="color: #3b82f6;">{{ formatRp(totalTagihan) }}</h2>
                <p class="text-caption text-medium-emphasis mb-0" style="font-family: 'Inter', sans-serif;">Pokok + margin jatuh tempo</p>
              </div>
            </div>
          </v-card-text>
        </v-card>
      </v-col>

      <v-col cols="12" md="4">
        <v-card class="rounded-xl border shadow-sm transition-swing" elevation="0" style="position: relative; overflow: hidden;">
          <div style="position: absolute; top: -20px; right: -20px; width: 120px; height: 120px; opacity: 0.08;">
            <v-icon icon="ri-checkbox-circle-line" size="120" color="#059669" />
          </div>
          <v-card-text class="pa-5" style="position: relative; z-index: 1;">
            <div class="d-flex justify-space-between align-start">
              <div>
                <p class="text-caption font-weight-bold text-uppercase tracking-widest mb-1" style="color: #64748B; font-family: 'Inter', sans-serif;">SALDO CUKUP</p>
                <h2 class="text-h4 font-weight-bold mb-2" style="color: #059669; font-family: 'Plus Jakarta Sans', sans-serif; line-height: 1.2;">{{ formatNumber(saldoCukupCount) }}</h2>
                <p class="text-caption text-medium-emphasis mb-0" style="color: #059669; font-weight: 600; font-family: 'Inter', sans-serif;">Dana aman untuk autodebet</p>
              </div>
            </div>
          </v-card-text>
        </v-card>
      </v-col>
    </v-row>

    <v-card elevation="0" class="jt-filter-card mb-6">
      <v-text-field v-model="searchQuery" label="Cari nasabah, kontrak, akad, AO" density="compact" variant="outlined" hide-details rounded="lg" prepend-inner-icon="ri-search-line" clearable />
      <v-select v-model="selectedStatus" :items="statusOptions" label="Status Debet" density="compact" variant="outlined" hide-details rounded="lg" prepend-inner-icon="ri-bank-card-line" />
      <v-select v-model="selectedEws" :items="ewsOptions" label="EWS" density="compact" variant="outlined" hide-details rounded="lg" prepend-inner-icon="ri-alarm-warning-line" />
    </v-card>

    <v-row class="mb-6">
      <v-col cols="12" lg="5">
        <div class="content-card">
          <div class="content-card__header">
            <div>
              <div class="content-card__title">Prioritas Account Officer</div>
              <div class="content-card__subtitle">Urutan berdasarkan kekurangan saldo dan total tagihan.</div>
            </div>
          </div>
          <div class="content-card__body pa-0">
            <div v-for="row in aoRecapRows.slice(0, 8)" :key="row.ao" class="jt-ao-row">
              <div>
                <strong>{{ row.ao }}</strong>
                <small>{{ row.cabang }} - {{ formatNumber(row.rekening) }} rekening - {{ formatNumber(row.kurang) }} kurang saldo</small>
              </div>
              <span>{{ formatRp(row.shortage) }}</span>
            </div>
            <div v-if="!aoRecapRows.length" class="pa-8 text-center text-disabled">Tidak ada prioritas AO pada filter ini.</div>
          </div>
        </div>
      </v-col>
      <v-col cols="12" lg="7">
        <div class="content-card">
          <div class="content-card__header">
            <div>
              <div class="content-card__title">Top 10 Follow-Up Autodebet</div>
              <div class="content-card__subtitle">Akun dengan kekurangan saldo terbesar untuk reminder dan validasi dana.</div>
            </div>
          </div>
          <div class="content-card__body pa-0">
            <div v-for="item in topPriorityRows" :key="`priority-${item.nokontrak}`" class="jt-priority-row">
              <div>
                <strong>{{ item.nama || '-' }}</strong>
                <small>{{ item.nokontrak || '-' }} - {{ item.nmao || '-' }} - {{ formatDate(item.tglexp) }}</small>
              </div>
              <div class="text-right">
                <v-chip size="x-small" :color="getUrgency(item.tglexp).color" variant="tonal" class="font-weight-black">{{ getUrgency(item.tglexp).label }}</v-chip>
                <span>{{ formatRp(shortageFor(item)) }}</span>
              </div>
            </div>
            <div v-if="!topPriorityRows.length" class="pa-8 text-center text-disabled">Tidak ada prioritas follow-up pada filter ini.</div>
          </div>
        </div>
      </v-col>
    </v-row>

    <v-alert
      v-if="periodUnavailable && !loadingJatuhTempo"
      type="warning"
      variant="tonal"
      border="start"
      rounded="lg"
      class="mb-6"
    >
      <div class="font-weight-bold mb-1">Periode {{ activePeriodLabel }} belum tersedia</div>
      <div class="text-body-2">{{ periodMeta.message || 'Database snapshot periode ini belum tersedia.' }}</div>
    </v-alert>

    <!-- 3. Main Data Grid -->
    <div class="content-card">
      <div class="content-card__header d-flex justify-space-between align-center px-6 py-4 border-b">
        <div>
          <div class="content-card__title">Daftar Nasabah Jatuh Tempo</div>
          <div class="content-card__subtitle">Daftar nasabah yang mendekati atau melewati jatuh tempo</div>
        </div>
        <v-btn
          v-if="selectedItems.length > 0"
          color="success"
          variant="flat"
          prepend-icon="ri-whatsapp-line"
          @click="handleBulkWA"
          class="font-weight-bold"
        >
          WA Blast ({{ selectedItems.length }})
        </v-btn>
      </div>
      <div class="content-card__body pa-0">
        <div class="overflow-x-auto">
          <table class="fin-table fin-vtable">
            <thead>
              <tr>
                <th style="width: 50px" class="text-center px-0">
                  <v-checkbox-btn v-model="selectAll" @change="toggleSelectAll" color="primary" density="compact" inline hide-details />
                </th>
                <th class="text-center" style="width: 80px">EWS</th>
                <th class="text-left">NASABAH & KONTRAK</th>
                <th class="text-left">AO / CABANG</th>
                <th class="text-center">TGL JATUH TEMPO</th>
                <th class="text-right">TOTAL TAGIHAN</th>
                <th class="text-right" style="width: 200px">SALDO TABUNGAN</th>
                <th class="text-right">KEKURANGAN</th>
                <th class="text-center">STATUS DEBET</th>
                <th class="text-center">AKSI</th>
              </tr>
            </thead>

            <tbody v-if="!loadingJatuhTempo">
              <tr v-for="item in paginatedJatuhTempo" :key="item.nokontrak" :class="getRowClass(item)">
                <td class="text-center px-0">
                  <v-checkbox-btn v-model="selectedItems" :value="item.nokontrak" color="primary" density="compact" inline hide-details />
                </td>
                <td class="text-center">
                  <v-tooltip :text="getUrgency(item.tglexp).label" location="top">
                    <template #activator="{ props }">
                      <v-icon
                        v-bind="props"
                        :icon="getUrgency(item.tglexp).icon"
                        :color="getUrgency(item.tglexp).color"
                        size="24"
                      ></v-icon>
                    </template>
                  </v-tooltip>
                </td>
                <td class="py-3">
                  <div class="font-weight-bold text-subtitle-2">{{ item.nama }}</div>
                  <div class="text-caption text-primary font-weight-medium">{{ item.nokontrak }}</div>
                </td>
                <td class="py-3">
                  <div class="font-weight-bold text-subtitle-2">{{ item.nmao || '-' }}</div>
                  <div class="text-caption text-medium-emphasis">{{ item.cabang || '-' }}</div>
                </td>
                <td class="text-center">
                  <v-chip size="small" variant="tonal" color="primary" class="font-weight-bold">
                    {{ formatDate(item.tglexp) }}
                  </v-chip>
                </td>
                <td class="text-right font-weight-bold">
                  {{ formatRp(tagihanTotal(item)) }}
                </td>
                <td class="text-right">
                  <div class="d-flex flex-column align-end">
                    <div class="font-weight-bold" :class="isSaldoSufficient(item) ? 'text-success' : 'text-error'">
                      {{ formatRp(item.saving_balance) }}
                    </div>
                    <v-progress-linear
                      :model-value="Math.min(100, (parseFloat(item.saving_balance || 0) / (parseFloat(item.tgkmdl || 0) + parseFloat(item.tgkmgn || 0) || 1)) * 100)"
                      :color="isSaldoSufficient(item) ? 'success' : 'error'"
                      height="4"
                      rounded
                      class="mt-1"
                      style="width: 100px"
                    ></v-progress-linear>
                  </div>
                </td>
                <td class="text-right font-weight-bold" :class="shortageFor(item) > 0 ? 'text-error' : 'text-success'">
                  {{ formatRp(shortageFor(item)) }}
                </td>
                <td class="text-center">
                  <v-chip
                    :color="isSaldoSufficient(item) ? 'success' : 'error'"
                    size="small"
                    variant="flat"
                    class="font-weight-bold text-white"
                  >
                    {{ isSaldoSufficient(item) ? 'SALDO CUKUP' : 'KURANG' }}
                  </v-chip>
                </td>
                <td class="text-center">
                  <v-btn
                    icon="ri-whatsapp-line"
                    color="success"
                    variant="tonal"
                    size="x-small"
                    class="rounded-lg"
                    @click="sendWA(item)"
                  ></v-btn>
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        <!-- Skeleton Loader -->
        <div v-if="loadingJatuhTempo" class="fin-loading">
          <v-progress-circular indeterminate color="primary" size="48"></v-progress-circular>
        </div>

        <!-- Empty State -->
        <div v-else-if="filteredData.length === 0" class="text-center pa-12">
          <v-icon icon="ri-inbox-line" size="64" class="text-disabled mb-4"></v-icon>
          <div class="text-h6 text-disabled">Tidak ada data antrian jatuh tempo.</div>
          <p class="text-caption text-disabled">Gunakan filter cabang atau sinkronkan data terbaru.</p>
        </div>

        <!-- Pagination Footer -->
        <v-divider v-if="filteredData.length > 0"></v-divider>
        <div v-if="filteredData.length > 0" class="pa-4 d-flex align-center justify-space-between bg-slate-50">
          <div class="text-caption text-medium-emphasis font-weight-bold">
            Menampilkan {{ (currentPage - 1) * itemsPerPage + 1 }} - {{ Math.min(currentPage * itemsPerPage, filteredData.length) }} dari {{ filteredData.length }} data
          </div>
          <v-pagination
            v-model="currentPage"
            :length="totalPages"
            :total-visible="5"
            density="compact"
            variant="flat"
            active-color="primary"
          ></v-pagination>
        </div>
      </div>
    </div>

    <v-snackbar
      v-model="snackbar"
      :color="snackbarColor"
      location="top right"
      timeout="3600"
      rounded="lg"
      elevation="8"
    >
      {{ snackbarMessage }}
    </v-snackbar>
  </div>
</template>

<style scoped>
.jt-toolbar {
  display: flex;
  align-items: center;
  gap: 12px;
  flex-wrap: wrap;
  justify-content: flex-end;
}

.jt-export-actions {
  display: flex;
  align-items: center;
  gap: 8px;
}

.fin-badge--slate {
  background: rgba(255, 255, 255, 0.12);
  color: #e2e8f0;
  border: 1px solid rgba(255, 255, 255, 0.18);
}

.jt-insight-panel {
  display: grid;
  grid-template-columns: minmax(0, 1.35fr) repeat(3, minmax(190px, 0.75fr));
  gap: 16px;
}

.jt-insight-card {
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

.jt-insight-card span {
  color: #64748b;
  font-size: 11px;
  font-weight: 900;
  text-transform: uppercase;
  letter-spacing: 0.06em;
}

.jt-insight-card strong {
  color: #0f172a;
  font-family: 'Plus Jakarta Sans', sans-serif;
  font-size: 14px;
  line-height: 1.45;
}

.jt-insight-card small {
  color: #64748b;
  font-size: 12px;
  font-weight: 700;
}

.jt-insight-card--primary {
  background:
    radial-gradient(circle at top right, rgba(245, 158, 11, 0.18), transparent 34%),
    linear-gradient(145deg, #fffbeb 0%, #ffffff 74%);
  border-color: #fde68a;
}

.jt-insight-card--primary strong {
  color: #b45309;
  font-size: 15px;
}

.jt-money-exact {
  font-family: 'Plus Jakarta Sans', sans-serif;
  font-size: clamp(0.98rem, 1.08vw, 1.28rem);
  font-weight: 900;
  line-height: 1.16;
  letter-spacing: -0.035em;
  white-space: nowrap;
}

.jt-filter-card {
  padding: 16px;
  display: grid;
  grid-template-columns: minmax(280px, 1.4fr) repeat(2, minmax(170px, 0.6fr));
  gap: 12px;
  background: linear-gradient(145deg, #ffffff 0%, #f8fafc 100%);
  border: 1px solid #dbe7f3;
  border-radius: 20px;
  box-shadow: 0 10px 28px rgba(15, 23, 42, 0.06);
}

.jt-ao-row,
.jt-priority-row {
  display: flex;
  justify-content: space-between;
  gap: 16px;
  padding: 15px 18px;
  border-bottom: 1px solid #e2e8f0;
}

.jt-ao-row:last-child,
.jt-priority-row:last-child {
  border-bottom: 0;
}

.jt-ao-row strong,
.jt-priority-row strong {
  display: block;
  color: #0f172a;
  font-size: 13px;
  font-weight: 950;
  text-transform: uppercase;
}

.jt-ao-row small,
.jt-priority-row small {
  display: block;
  color: #64748b;
  font-size: 11px;
  font-weight: 800;
  margin-top: 4px;
}

.jt-ao-row span,
.jt-priority-row span {
  display: block;
  color: #be123c;
  font-family: 'Plus Jakarta Sans', sans-serif;
  font-size: 12px;
  font-weight: 950;
  margin-top: 6px;
  white-space: nowrap;
}

@media (max-width: 1180px) {
  .jt-insight-panel {
    grid-template-columns: 1fr 1fr;
  }

  .jt-filter-card {
    grid-template-columns: 1fr 1fr;
  }
}

@media (max-width: 720px) {
  .jt-toolbar,
  .jt-export-actions {
    width: 100%;
  }

  .jt-export-actions .v-btn {
    flex: 1;
  }

  .jt-insight-panel {
    grid-template-columns: 1fr;
  }

  .jt-filter-card {
    grid-template-columns: 1fr;
  }
}
</style>
