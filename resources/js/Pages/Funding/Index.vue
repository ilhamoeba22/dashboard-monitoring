<script setup>
import { computed, onMounted, reactive, ref, watch } from 'vue'
import DefaultLayout from '@/layouts/default.vue'
import '@/assets/css/financing-shared.css'
import {
  formatCompactRupiah,
  formatExactNumber,
  formatExactRupiah,
  formatTruncatedPercentage,
  toFiniteNumber,
} from '@/utils/money'

defineOptions({ layout: DefaultLayout })

const props = defineProps({
  initialDomain: {
    type: String,
    default: 'overview',
  },
  initialFeature: {
    type: String,
    default: '',
  },
})

const activeDomain = ref(['overview', 'tabungan', 'deposito', 'perkembangan', 'target', 'mutasi', 'risk', 'concentration', 'baghas'].includes(props.initialDomain) ? props.initialDomain : 'overview')
const savingFeature = ref(['nominatif', 'rekapitulasi', 'dormant'].includes(props.initialFeature) ? props.initialFeature : 'nominatif')
const depositFeature = ref(['nominatif', 'rekapitulasi', 'jatuh-tempo'].includes(props.initialFeature) ? props.initialFeature : 'nominatif')
const savingGroup = ref('produk')
const depositGroup = ref('produk')
const loading = ref(false)
const error = ref('')

const filters = reactive({
  savingSearch: '',
  depositSearch: '',
  savingCabang: '',
  savingAo: '',
  savingRekapSearch: '',
  savingRekapCabang: '',
  savingRekapAo: '',
  savingDormantSearch: '',
  savingDormantCabang: '',
  savingDormantAo: '',
  depositCabang: '',
  depositAo: '',
  depositRekapSearch: '',
  depositRekapCabang: '',
  depositRekapAo: '',
  depositMaturitySearch: '',
  depositMaturityCabang: '',
  depositMaturityAo: '',
})

const savingSummary = ref({})
const savingFilterOptions = ref({
  cabangs: [],
  account_officers: [],
})
const savingNominativeMeta = ref({
  per_page: 25,
  next_cursor: null,
  prev_cursor: null,
  next_page_url: null,
  prev_page_url: null,
})
const savingCursor = ref('')
const savingCursorStack = ref([])
const depositSummary = ref({})
const depositFilterOptions = ref({
  cabangs: [],
  account_officers: [],
})
const savingData = ref([])
const savingRekap = ref([])
const savingDormant = ref([])
const savingDormantMeta = ref({
  per_page: 25,
  next_cursor: null,
  prev_cursor: null,
  next_page_url: null,
  prev_page_url: null,
})
const savingDormantCursor = ref('')
const savingDormantCursorStack = ref([])
const depositData = ref([])
const depositNominativeMeta = ref({
  per_page: 25,
  next_cursor: null,
  prev_cursor: null,
  next_page_url: null,
  prev_page_url: null,
})
const depositCursor = ref('')
const depositCursorStack = ref([])
const depositRekap = ref([])
const depositJatuhTempo = ref([])
const depositMaturityMeta = ref({
  per_page: 25,
  next_cursor: null,
  prev_cursor: null,
  next_page_url: null,
  prev_page_url: null,
})
const depositMaturityCursor = ref('')
const depositMaturityCursorStack = ref([])
const riskOverview = ref({
  summary: {},
  top_depositors: [],
  maturity_buckets: [],
  dormant_buckets: [],
  product_mix: [],
  system_period: {},
})
const baghasOverview = ref({
  summary: {},
  saving_summary: {},
  deposit_summary: {},
  saving_by_product: [],
  deposit_by_product: [],
  deposit_by_nisbah: [],
  top_baghas_depositors: [],
  trace_note: '',
  system_period: {},
})
const fundingPerkembangan = ref({ saving: [], deposit: [], system_period: {} })
const fundingTarget = ref({ saving: [], deposit: [], system_period: {} })
const fundingMutasi = ref({ saving: [], deposit: [], system_period: {} })
const fundingConcentration = ref({ summary: {}, top_depositors: [], concentration_bands: [], system_period: {} })

const groupOptions = [
  { title: 'Produk', value: 'produk' },
  { title: 'Account Officer', value: 'ao' },
  { title: 'Cabang', value: 'cabang' },
  { title: 'Wilayah', value: 'wilayah' },
]

const pageMeta = computed(() => {
  const meta = {
    overview: {
      title: 'Funding Overview',
      subtitle: 'Executive monitoring Dana Pihak Ketiga berbasis legacy CBS: total DPK, mix tabungan/deposito, dan interpretasi likuiditas.',
      icon: 'ri-dashboard-3-line',
    },
    tabungan: {
      title: savingPageTitle.value,
      subtitle: savingPageSubtitle.value,
      icon: 'ri-wallet-3-line',
    },
    deposito: {
      title: depositPageTitle.value,
      subtitle: depositPageSubtitle.value,
      icon: 'ri-safe-2-line',
    },
    risk: {
      title: 'Risk Funding',
      subtitle: 'Concentration risk, deposan terbesar, maturity bucket deposito, dormant funding, dan product mix risk.',
      icon: 'ri-radar-line',
    },
    concentration: {
      title: 'Nasabah Terbesar Funding',
      subtitle: 'Detail concentration risk DPK per CIF: Top 100 deposan, band nominal, dan share terhadap total DPK.',
      icon: 'ri-user-star-line',
    },
    perkembangan: {
      title: 'Perkembangan Funding',
      subtitle: 'Trend bulanan DPK tabungan dan deposito memakai EOM history plus saldo live bulan berjalan.',
      icon: 'ri-line-chart-line',
    },
    target: {
      title: 'Target Funding',
      subtitle: 'Monitoring target vs realisasi Funding dari TARGETAO dibandingkan realisasi CBS berjalan.',
      icon: 'ri-crosshair-2-line',
    },
    mutasi: {
      title: 'Mutasi Funding',
      subtitle: 'Monitoring mutasi tabungan dan deposito: debet/kredit/netto serta deposito masuk/cair/netto.',
      icon: 'ri-arrow-left-right-line',
    },
    baghas: {
      title: 'Bagi Hasil Funding',
      subtitle: 'Monitoring bagi hasil hitung, bagi hasil bayar, pajak, nisbah, dan top deposan pembentuk biaya dana.',
      icon: 'ri-percent-line',
    },
  }

  return meta[activeDomain.value] || meta.overview
})

const savingPageTitle = computed(() => {
  if (savingFeature.value === 'rekapitulasi') return 'Rekapitulasi Tabungan'
  if (savingFeature.value === 'dormant') return 'Dormant Tabungan'
  return 'Nominatif Tabungan'
})

const savingPageSubtitle = computed(() => {
  if (savingFeature.value === 'rekapitulasi') return 'Rekap saldo, saldo rata-rata, bagi hasil, dan pajak tabungan berdasarkan AO, cabang, wilayah, atau produk.'
  if (savingFeature.value === 'dormant') return 'Daftar rekening tabungan pasif/dormant berdasarkan aktivitas transaksi terakhir dari CBS.'
  return 'Daftar rekening tabungan aktif dari TOFTABB/TOFTABC lengkap dengan saldo, nisbah, AO, produk, dan status.'
})

const depositPageTitle = computed(() => {
  if (depositFeature.value === 'rekapitulasi') return 'Rekapitulasi Deposito'
  if (depositFeature.value === 'jatuh-tempo') return 'Deposito Jatuh Tempo'
  return 'Nominatif Deposito'
})

const depositPageSubtitle = computed(() => {
  if (depositFeature.value === 'rekapitulasi') return 'Rekap nominal, saldo rata-rata, bagi hasil, pajak, nisbah, dan equivalent rate deposito berdasarkan dimensi operasional.'
  if (depositFeature.value === 'jatuh-tempo') return 'Daftar deposito aktif yang jatuh tempo pada bulan berjalan untuk monitoring rollover dan likuiditas.'
  return 'Daftar deposito Mudharabah aktif dari TOFDEP lengkap dengan nominal, nisbah, equivalent rate, ARO, dan jatuh tempo.'
})

const totalDpk = computed(() => toFiniteNumber(savingSummary.value.total_saldo) + toFiniteNumber(depositSummary.value.total_nominal))
const totalNoa = computed(() => toFiniteNumber(savingSummary.value.noa) + toFiniteNumber(depositSummary.value.noa))
const savingShare = computed(() => totalDpk.value > 0 ? (toFiniteNumber(savingSummary.value.total_saldo) / totalDpk.value) * 100 : 0)
const depositShare = computed(() => totalDpk.value > 0 ? (toFiniteNumber(depositSummary.value.total_nominal) / totalDpk.value) * 100 : 0)
const savingCabangOptions = computed(() => [
  { title: 'Semua Cabang', value: '' },
  ...(savingFilterOptions.value.cabangs || []).map(item => ({
    title: `${item.value} - ${item.title}`,
    value: item.value,
  })),
])
const savingAoOptions = computed(() => [
  { title: 'Semua AO', value: '' },
  ...(savingFilterOptions.value.account_officers || []).map(item => ({
    title: `${item.title} (${item.value})`,
    value: item.value,
  })),
])
const savingCurrentSaldo = computed(() => savingData.value.reduce((sum, item) => sum + toFiniteNumber(item.saldo), 0))
const savingCurrentAverage = computed(() => savingData.value.reduce((sum, item) => sum + toFiniteNumber(item.saldo_rata), 0))
const savingCurrentBaghas = computed(() => savingData.value.reduce((sum, item) => sum + toFiniteNumber(item.bhbayar), 0))
const savingHasNextPage = computed(() => Boolean(savingNominativeMeta.value.next_cursor || savingNominativeMeta.value.next_page_url))
const savingHasPrevPage = computed(() => savingCursorStack.value.length > 0)
const savingRekapTotals = computed(() => savingRekap.value.reduce((totals, item) => {
  totals.noa += toFiniteNumber(item.noa)
  totals.saldo += toFiniteNumber(item.total_saldo)
  totals.saldoRata += toFiniteNumber(item.saldo_rata)
  totals.baghas += toFiniteNumber(item.bagi_hasil_bayar)
  totals.pajak += toFiniteNumber(item.pajak_bayar)
  return totals
}, { noa: 0, saldo: 0, saldoRata: 0, baghas: 0, pajak: 0 }))
const topSavingRekapRow = computed(() => [...savingRekap.value].sort((left, right) => toFiniteNumber(right.total_saldo) - toFiniteNumber(left.total_saldo))[0] || null)
const savingRekapInterpretation = computed(() => {
  if (!savingRekap.value.length) return 'Tidak ada data rekapitulasi tabungan pada kombinasi filter saat ini.'

  const topRow = topSavingRekapRow.value
  const topShare = savingRekapTotals.value.saldo > 0 ? (toFiniteNumber(topRow?.total_saldo) / savingRekapTotals.value.saldo) * 100 : 0
  const taxRatio = savingRekapTotals.value.baghas > 0 ? (savingRekapTotals.value.pajak / savingRekapTotals.value.baghas) * 100 : 0

  if (topShare >= 50) {
    return `${topRow?.label || 'Satu kelompok'} mendominasi saldo tabungan sebesar ${formatTruncatedPercentage(topShare)} dari total hasil rekap. Pantau konsentrasi dana murah dan pastikan tidak bergantung pada satu kelompok operasional.`
  }

  if (taxRatio >= 15) {
    return `Pajak bagi hasil tabungan pada filter ini mencapai ${formatTruncatedPercentage(taxRatio)} dari bagi hasil bayar. Validasi status pajak nasabah prioritas dan konfigurasi produk tabungan terkait.`
  }

  return `Rekap tabungan menunjukkan distribusi relatif sehat: kelompok terbesar ${topRow?.label || '-'} berada pada ${formatTruncatedPercentage(topShare)} dari saldo hasil filter. Gunakan tabel ini untuk prioritas funding, AO follow-up, dan evaluasi produk dana murah.`
})
const savingDormantTotals = computed(() => savingDormant.value.reduce((totals, item) => {
  totals.noa += 1
  totals.saldo += toFiniteNumber(item.saldo)
  totals.saldoRata += toFiniteNumber(item.saldo_rata)
  totals.maxHariPasif = Math.max(totals.maxHariPasif, toFiniteNumber(item.hari_pasif))
  return totals
}, { noa: 0, saldo: 0, saldoRata: 0, maxHariPasif: 0 }))
const savingDormantBuckets = computed(() => {
  const buckets = [
    { label: '< 6 bulan', min: 0, max: 179, noa: 0, saldo: 0, color: '#0d9488' },
    { label: '6-12 bulan', min: 180, max: 364, noa: 0, saldo: 0, color: '#0284c7' },
    { label: '1-2 tahun', min: 365, max: 729, noa: 0, saldo: 0, color: '#f59e0b' },
    { label: '> 2 tahun', min: 730, max: Infinity, noa: 0, saldo: 0, color: '#e11d48' },
  ]

  savingDormant.value.forEach(item => {
    const hariPasif = toFiniteNumber(item.hari_pasif)
    const bucket = buckets.find(row => hariPasif >= row.min && hariPasif <= row.max) || buckets[0]
    bucket.noa += 1
    bucket.saldo += toFiniteNumber(item.saldo)
  })

  return buckets
})
const topDormantRow = computed(() => [...savingDormant.value].sort((left, right) => toFiniteNumber(right.saldo) - toFiniteNumber(left.saldo))[0] || null)
const savingDormantInterpretation = computed(() => {
  if (!savingDormant.value.length) return 'Tidak ada rekening dormant/pasif pada kombinasi filter saat ini.'

  const oldBucket = savingDormantBuckets.value.find(item => item.label === '> 2 tahun')
  const oldShare = savingDormantTotals.value.saldo > 0 ? (toFiniteNumber(oldBucket?.saldo) / savingDormantTotals.value.saldo) * 100 : 0
  const topShare = savingDormantTotals.value.saldo > 0 ? (toFiniteNumber(topDormantRow.value?.saldo) / savingDormantTotals.value.saldo) * 100 : 0

  if (oldShare >= 40) {
    return `Saldo pasif lebih dari 2 tahun mencapai ${formatTruncatedPercentage(oldShare)} dari saldo dormant yang tampil. Prioritaskan validasi rekening lama, kontak nasabah, dan program reaktivasi dana murah.`
  }

  if (topShare >= 25) {
    return `${topDormantRow.value?.nama || 'Satu rekening'} menjadi konsentrasi dormant terbesar dengan porsi ${formatTruncatedPercentage(topShare)}. Jadikan sebagai prioritas pertama follow-up AO.`
  }

  return `Dormant tabungan pada filter ini tersebar di ${formatExactNumber(savingDormantTotals.value.noa)} rekening. Prioritaskan bucket usia pasif tertua dan rekening dengan saldo terbesar untuk reaktivasi.`
})
const savingDormantHasNextPage = computed(() => Boolean(savingDormantMeta.value.next_cursor || savingDormantMeta.value.next_page_url))
const savingDormantHasPrevPage = computed(() => savingDormantCursorStack.value.length > 0)
const depositCabangOptions = computed(() => [
  { title: 'Semua Cabang', value: '' },
  ...(depositFilterOptions.value.cabangs || []).map(item => ({
    title: `${item.value} - ${item.title}`,
    value: item.value,
  })),
])
const depositAoOptions = computed(() => [
  { title: 'Semua AO', value: '' },
  ...(depositFilterOptions.value.account_officers || []).map(item => ({
    title: `${item.title} (${item.value})`,
    value: item.value,
  })),
])
const depositCurrentNominal = computed(() => depositData.value.reduce((sum, item) => sum + toFiniteNumber(item.nominal), 0))
const depositCurrentAverage = computed(() => depositData.value.reduce((sum, item) => sum + toFiniteNumber(item.saldo_rata), 0))
const depositCurrentBaghas = computed(() => depositData.value.reduce((sum, item) => sum + toFiniteNumber(item.bngbayar), 0))
const depositCurrentTax = computed(() => depositData.value.reduce((sum, item) => sum + toFiniteNumber(item.tax), 0))
const depositAroCount = computed(() => depositData.value.filter(item => String(item.aro || '').toUpperCase() === 'Y').length)
const depositHasNextPage = computed(() => Boolean(depositNominativeMeta.value.next_cursor || depositNominativeMeta.value.next_page_url))
const depositHasPrevPage = computed(() => depositCursorStack.value.length > 0)
const depositNominativeInterpretation = computed(() => {
  if (!depositData.value.length) return 'Tidak ada deposito aktif pada kombinasi filter saat ini.'

  const aroRatio = depositData.value.length > 0 ? (depositAroCount.value / depositData.value.length) * 100 : 0
  const taxRatio = depositCurrentBaghas.value > 0 ? (depositCurrentTax.value / depositCurrentBaghas.value) * 100 : 0
  const topRow = [...depositData.value].sort((left, right) => toFiniteNumber(right.nominal) - toFiniteNumber(left.nominal))[0]
  const topShare = depositCurrentNominal.value > 0 ? (toFiniteNumber(topRow?.nominal) / depositCurrentNominal.value) * 100 : 0

  if (topShare >= 35) {
    return `${topRow?.nama || 'Satu deposan'} mendominasi nominal deposito pada halaman ini sebesar ${formatTruncatedPercentage(topShare)}. Pantau pricing, nisbah, dan rencana rollover deposan besar tersebut.`
  }

  if (aroRatio < 50) {
    return `Proporsi ARO pada data tampil ${formatTruncatedPercentage(aroRatio)}. Prioritaskan komunikasi perpanjangan ke bilyet non-ARO untuk menjaga stabilitas DPK.`
  }

  if (taxRatio >= 15) {
    return `Pajak bagi hasil deposito mencapai ${formatTruncatedPercentage(taxRatio)} dari baghas bayar pada data tampil. Validasi status pajak dan pengecualian deposan prioritas.`
  }

  return `Portofolio deposito pada halaman ini relatif stabil: ARO ${formatTruncatedPercentage(aroRatio)} dan nominal tersebar pada ${formatExactNumber(depositData.value.length)} bilyet aktif.`
})
const depositRekapTotals = computed(() => depositRekap.value.reduce((totals, item) => {
  totals.noa += toFiniteNumber(item.noa)
  totals.nominal += toFiniteNumber(item.total_nominal)
  totals.saldoRata += toFiniteNumber(item.saldo_rata)
  totals.baghas += toFiniteNumber(item.bagi_hasil_bayar)
  totals.pajak += toFiniteNumber(item.pajak)
  totals.weightedNisbah += toFiniteNumber(item.avg_nisbah) * toFiniteNumber(item.total_nominal)
  totals.weightedRate += toFiniteNumber(item.avg_equivrate) * toFiniteNumber(item.total_nominal)
  return totals
}, { noa: 0, nominal: 0, saldoRata: 0, baghas: 0, pajak: 0, weightedNisbah: 0, weightedRate: 0 }))
const depositRekapAvgNisbah = computed(() => depositRekapTotals.value.nominal > 0 ? depositRekapTotals.value.weightedNisbah / depositRekapTotals.value.nominal : 0)
const depositRekapAvgRate = computed(() => depositRekapTotals.value.nominal > 0 ? depositRekapTotals.value.weightedRate / depositRekapTotals.value.nominal : 0)
const topDepositRekapRow = computed(() => [...depositRekap.value].sort((left, right) => toFiniteNumber(right.total_nominal) - toFiniteNumber(left.total_nominal))[0] || null)
const depositRekapInterpretation = computed(() => {
  if (!depositRekap.value.length) return 'Tidak ada data rekapitulasi deposito pada kombinasi filter saat ini.'

  const topRow = topDepositRekapRow.value
  const topShare = depositRekapTotals.value.nominal > 0 ? (toFiniteNumber(topRow?.total_nominal) / depositRekapTotals.value.nominal) * 100 : 0
  const taxRatio = depositRekapTotals.value.baghas > 0 ? (depositRekapTotals.value.pajak / depositRekapTotals.value.baghas) * 100 : 0

  if (topShare >= 50) {
    return `${topRow?.label || 'Satu kelompok'} mendominasi nominal deposito sebesar ${formatTruncatedPercentage(topShare)} dari total hasil filter. Pantau konsentrasi, nisbah khusus, dan risiko rollover pada kelompok tersebut.`
  }

  if (depositRekapAvgRate.value >= 8) {
    return `Equivalent rate tertimbang deposito berada di ${formatTruncatedPercentage(depositRekapAvgRate.value)}. Evaluasi pricing deposito besar agar biaya dana tetap terkendali.`
  }

  if (taxRatio >= 15) {
    return `Pajak bagi hasil deposito mencapai ${formatTruncatedPercentage(taxRatio)} dari baghas bayar. Validasi status pajak deposan prioritas dan konfigurasi PPh pada produk deposito.`
  }

  return `Rekap deposito relatif terkendali: kelompok terbesar ${topRow?.label || '-'} berada pada ${formatTruncatedPercentage(topShare)}, dengan equivalent rate tertimbang ${formatTruncatedPercentage(depositRekapAvgRate.value)}.`
})
const depositMaturityTotals = computed(() => depositJatuhTempo.value.reduce((totals, item) => {
  totals.bilyet += 1
  totals.nominal += toFiniteNumber(item.nominal)
  totals.aro += String(item.aro || '').toUpperCase() === 'Y' ? 1 : 0
  totals.nonAro += String(item.aro || '').toUpperCase() === 'Y' ? 0 : 1
  totals.baghas += toFiniteNumber(item.bngbayar)
  return totals
}, { bilyet: 0, nominal: 0, aro: 0, nonAro: 0, baghas: 0 }))
const depositMaturityAroRatio = computed(() => depositMaturityTotals.value.bilyet > 0 ? (depositMaturityTotals.value.aro / depositMaturityTotals.value.bilyet) * 100 : 0)
const depositMaturityBuckets = computed(() => {
  const buckets = [
    { label: 'Hari ini', min: -99999, max: 0, bilyet: 0, nominal: 0, color: '#e11d48' },
    { label: '1-7 hari', min: 1, max: 7, bilyet: 0, nominal: 0, color: '#f59e0b' },
    { label: '8-14 hari', min: 8, max: 14, bilyet: 0, nominal: 0, color: '#0284c7' },
    { label: '15+ hari', min: 15, max: Infinity, bilyet: 0, nominal: 0, color: '#0d9488' },
  ]

  depositJatuhTempo.value.forEach(item => {
    const hari = toFiniteNumber(item.hari_jatuh_tempo)
    const bucket = buckets.find(row => hari >= row.min && hari <= row.max) || buckets[3]
    bucket.bilyet += 1
    bucket.nominal += toFiniteNumber(item.nominal)
  })

  return buckets
})
const topMaturityRow = computed(() => [...depositJatuhTempo.value].sort((left, right) => toFiniteNumber(right.nominal) - toFiniteNumber(left.nominal))[0] || null)
const depositMaturityInterpretation = computed(() => {
  if (!depositJatuhTempo.value.length) return 'Tidak ada deposito jatuh tempo pada kombinasi filter saat ini.'

  const urgentBucket = depositMaturityBuckets.value.find(item => item.label === 'Hari ini')
  const weekBucket = depositMaturityBuckets.value.find(item => item.label === '1-7 hari')
  const urgentNominal = toFiniteNumber(urgentBucket?.nominal) + toFiniteNumber(weekBucket?.nominal)
  const urgentShare = depositMaturityTotals.value.nominal > 0 ? (urgentNominal / depositMaturityTotals.value.nominal) * 100 : 0
  const topShare = depositMaturityTotals.value.nominal > 0 ? (toFiniteNumber(topMaturityRow.value?.nominal) / depositMaturityTotals.value.nominal) * 100 : 0

  if (urgentShare >= 50) {
    return `Tekanan jatuh tempo sangat dekat: ${formatTruncatedPercentage(urgentShare)} nominal jatuh tempo berada di bucket hari ini sampai 7 hari. Prioritaskan call list rollover dan siapkan buffer likuiditas.`
  }

  if (depositMaturityAroRatio.value < 50) {
    return `Proporsi ARO hanya ${formatTruncatedPercentage(depositMaturityAroRatio.value)} dari bilyet tampil. Fokuskan follow-up ke Non ARO agar dana tidak keluar saat maturity.`
  }

  if (topShare >= 30) {
    return `${topMaturityRow.value?.nama || 'Satu deposan'} menyumbang ${formatTruncatedPercentage(topShare)} dari nominal jatuh tempo. Jadikan nasabah ini prioritas komunikasi dan negosiasi nisbah.`
  }

  return `Maturity bulan berjalan relatif tersebar: ${formatExactNumber(depositMaturityTotals.value.bilyet)} bilyet dengan ARO ${formatTruncatedPercentage(depositMaturityAroRatio.value)}. Tetap pantau bucket 1-7 hari setiap hari.`
})
const depositMaturityHasNextPage = computed(() => Boolean(depositMaturityMeta.value.next_cursor || depositMaturityMeta.value.next_page_url))
const depositMaturityHasPrevPage = computed(() => depositMaturityCursorStack.value.length > 0)

function latestRow(rows = []) {
  return rows?.length ? rows[rows.length - 1] : {}
}

function previousRow(rows = []) {
  return rows?.length > 1 ? rows[rows.length - 2] : {}
}

const perkembanganSummary = computed(() => {
  const savingLatest = latestRow(fundingPerkembangan.value.saving || [])
  const depositLatest = latestRow(fundingPerkembangan.value.deposit || [])
  const savingPrev = previousRow(fundingPerkembangan.value.saving || [])
  const depositPrev = previousRow(fundingPerkembangan.value.deposit || [])
  const totalLatest = toFiniteNumber(savingLatest.nominal) + toFiniteNumber(depositLatest.nominal)
  const totalPrev = toFiniteNumber(savingPrev.nominal) + toFiniteNumber(depositPrev.nominal)

  return {
    periode: savingLatest.periode || depositLatest.periode || '-',
    total_latest: totalLatest,
    total_prev: totalPrev,
    total_growth: totalPrev > 0 ? ((totalLatest - totalPrev) / totalPrev) * 100 : 0,
    saving_latest: toFiniteNumber(savingLatest.nominal),
    deposit_latest: toFiniteNumber(depositLatest.nominal),
    saving_growth: toFiniteNumber(savingLatest.mom_growth_percent),
    deposit_growth: toFiniteNumber(depositLatest.mom_growth_percent),
    total_noa: toFiniteNumber(savingLatest.noa) + toFiniteNumber(depositLatest.noa),
  }
})

const perkembanganInterpretation = computed(() => {
  const summary = perkembanganSummary.value
  if (summary.total_latest <= 0) return 'Belum ada data perkembangan funding untuk periode aktif.'

  const dominant = summary.deposit_latest >= summary.saving_latest ? 'Deposito' : 'Tabungan'
  const dominantValue = Math.max(summary.deposit_latest, summary.saving_latest)
  const dominantShare = summary.total_latest > 0 ? (dominantValue / summary.total_latest) * 100 : 0

  if (summary.total_growth < 0) {
    return `Total DPK turun ${formatTruncatedPercentage(Math.abs(summary.total_growth))} dibanding periode sebelumnya. Prioritaskan analisis sumber penurunan pada tabungan/deposito dan follow-up nasabah besar.`
  }

  if (dominantShare >= 75) {
    return `${dominant} mendominasi komposisi DPK sebesar ${formatTruncatedPercentage(dominantShare)}. Pantau dampaknya ke stabilitas likuiditas dan biaya dana.`
  }

  return `DPK periode ${summary.periode} tumbuh ${formatTruncatedPercentage(summary.total_growth)} dengan komposisi relatif seimbang. Lanjutkan monitoring tren bulanan tabungan dan deposito.`
})

const targetSummary = computed(() => {
  const savingLatest = latestRow(fundingTarget.value.saving || [])
  const depositLatest = latestRow(fundingTarget.value.deposit || [])
  const target = toFiniteNumber(savingLatest.target) + toFiniteNumber(depositLatest.target)
  const actual = toFiniteNumber(savingLatest.actual) + toFiniteNumber(depositLatest.actual)

  return {
    periode: savingLatest.periode || depositLatest.periode || '-',
    target,
    actual,
    gap: actual - target,
    achievement: target > 0 ? (actual / target) * 100 : 0,
    saving_achievement: toFiniteNumber(savingLatest.achievement_percent),
    deposit_achievement: toFiniteNumber(depositLatest.achievement_percent),
  }
})

const targetInterpretation = computed(() => {
  const summary = targetSummary.value
  if (summary.target <= 0) return 'Target funding belum tersedia pada periode aktif atau TARGETAO belum terisi.'

  if (summary.achievement >= 100) {
    return `Achievement funding periode ${summary.periode} sudah ${formatTruncatedPercentage(summary.achievement)}. Pertahankan kualitas dana dan pantau risiko pricing deposito.`
  }

  const weaker = summary.saving_achievement <= summary.deposit_achievement ? 'Tabungan' : 'Deposito'
  return `Achievement funding baru ${formatTruncatedPercentage(summary.achievement)} dengan gap ${formatExactRupiah(summary.gap)}. Fokus akselerasi ${weaker} karena pencapaiannya paling rendah dibanding komponen lain.`
})

const mutasiSummary = computed(() => {
  const savingRows = fundingMutasi.value.saving || []
  const depositRows = fundingMutasi.value.deposit || []
  const savingLatest = latestRow(savingRows)
  const depositLatest = latestRow(depositRows)
  const savingNetto = toFiniteNumber(savingLatest.netto)
  const depositNetto = toFiniteNumber(depositLatest.netto)

  return {
    periode: savingLatest.periode || depositLatest.periode || '-',
    saving_debet: toFiniteNumber(savingLatest.debet),
    saving_kredit: toFiniteNumber(savingLatest.kredit),
    saving_netto: savingNetto,
    deposit_masuk: toFiniteNumber(depositLatest.masuk),
    deposit_cair: toFiniteNumber(depositLatest.cair),
    deposit_netto: depositNetto,
    total_netto: savingNetto + depositNetto,
    transaksi: toFiniteNumber(savingLatest.transaksi) + toFiniteNumber(depositLatest.transaksi),
  }
})

const mutasiInterpretation = computed(() => {
  const summary = mutasiSummary.value
  if (summary.transaksi <= 0) return 'Belum ada mutasi funding pada periode aktif.'

  if (summary.total_netto < 0) {
    return `Mutasi bersih funding periode ${summary.periode} negatif sebesar ${formatExactRupiah(summary.total_netto)}. Prioritaskan penyebab deposito cair dan arus keluar tabungan.`
  }

  const driver = summary.deposit_netto >= summary.saving_netto ? 'Deposito' : 'Tabungan'
  return `Mutasi bersih funding positif sebesar ${formatExactRupiah(summary.total_netto)}. Kontributor terbesar berasal dari ${driver}; tetap pantau transaksi besar harian.`
})

const overviewCards = computed(() => [
  {
    label: 'Total DPK',
    value: formatExactRupiah(totalDpk.value),
    sub: `${formatExactNumber(totalNoa.value)} rekening tabungan dan deposito`,
    icon: 'ri-bank-card-line',
    color: '#0d9488',
    tone: 'kpi-card--success',
  },
  {
    label: 'Total Tabungan',
    value: formatExactRupiah(savingSummary.value.total_saldo),
    sub: `${formatExactNumber(savingSummary.value.noa)} rekening aktif`,
    icon: 'ri-wallet-3-line',
    color: '#0284c7',
    tone: 'kpi-card--info',
  },
  {
    label: 'Total Deposito',
    value: formatExactRupiah(depositSummary.value.total_nominal),
    sub: `${formatExactNumber(depositSummary.value.noa)} bilyet aktif`,
    icon: 'ri-safe-2-line',
    color: '#7c3aed',
    tone: 'kpi-card--purple',
  },
  {
    label: 'Deposito Jatuh Tempo',
    value: formatExactNumber(depositJatuhTempo.value.length),
    sub: 'Bilyet jatuh tempo bulan berjalan',
    icon: 'ri-calendar-event-line',
    color: '#f59e0b',
    tone: 'kpi-card--warning',
  },
])

const fundingInterpretation = computed(() => {
  const dominant = savingShare.value >= depositShare.value ? 'Tabungan' : 'Deposito'
  const share = Math.max(savingShare.value, depositShare.value)
  const jtNominal = depositJatuhTempo.value.reduce((sum, item) => sum + toFiniteNumber(item.nominal), 0)

  if (jtNominal > totalDpk.value * 0.1) {
    return `Konsentrasi jatuh tempo deposito bulan berjalan cukup material (${formatCompactRupiah(jtNominal)}). Prioritaskan komunikasi rollover/ARO dan siapkan alternatif likuiditas harian.`
  }

  return `${dominant} menjadi komponen dominan DPK dengan porsi ${formatTruncatedPercentage(share)}. Pantau perubahan mix dana karena komposisi tabungan vs deposito memengaruhi biaya dana dan stabilitas likuiditas.`
})

const riskSummary = computed(() => riskOverview.value?.summary || {})
const topDepositors = computed(() => riskOverview.value?.top_depositors || [])
const maturityBuckets = computed(() => riskOverview.value?.maturity_buckets || [])
const dormantBuckets = computed(() => riskOverview.value?.dormant_buckets || [])
const productMix = computed(() => riskOverview.value?.product_mix || [])
const baghasSummary = computed(() => baghasOverview.value?.summary || {})
const savingBaghasSummary = computed(() => baghasOverview.value?.saving_summary || {})
const depositBaghasSummary = computed(() => baghasOverview.value?.deposit_summary || {})
const savingBaghasByProduct = computed(() => baghasOverview.value?.saving_by_product || [])
const depositBaghasByProduct = computed(() => baghasOverview.value?.deposit_by_product || [])
const depositByNisbah = computed(() => baghasOverview.value?.deposit_by_nisbah || [])
const topBaghasDepositors = computed(() => baghasOverview.value?.top_baghas_depositors || [])

const riskCards = computed(() => [
  {
    label: 'Top 1 Depositor',
    value: formatExactRupiah(riskSummary.value.top1_nominal),
    sub: `${formatTruncatedPercentage(riskSummary.value.top1_ratio)} dari total DPK`,
    icon: 'ri-user-star-line',
    color: '#0d9488',
  },
  {
    label: 'Top 5 Concentration',
    value: formatExactRupiah(riskSummary.value.top5_nominal),
    sub: `${formatTruncatedPercentage(riskSummary.value.top5_ratio)} dari total DPK`,
    icon: 'ri-team-line',
    color: '#7c3aed',
  },
  {
    label: 'Top 10 Concentration',
    value: formatExactRupiah(riskSummary.value.top10_nominal),
    sub: `${formatTruncatedPercentage(riskSummary.value.top10_ratio)} dari total DPK`,
    icon: 'ri-bar-chart-grouped-line',
    color: '#0284c7',
  },
  {
    label: 'Top 25 Concentration',
    value: formatExactRupiah(riskSummary.value.top25_nominal),
    sub: `${formatTruncatedPercentage(riskSummary.value.top25_ratio)} dari total DPK`,
    icon: 'ri-radar-line',
    color: '#f59e0b',
  },
])

const riskInterpretation = computed(() => {
  const top5Ratio = toFiniteNumber(riskSummary.value.top5_ratio)
  const top10Ratio = toFiniteNumber(riskSummary.value.top10_ratio)
  const nearMaturity = maturityBuckets.value
    .filter(item => ['Lewat jatuh tempo', '0-7 hari', '8-14 hari', '15-30 hari'].includes(item.bucket))
    .reduce((sum, item) => sum + toFiniteNumber(item.nominal), 0)
  const dormantSaldo = dormantBuckets.value
    .filter(item => String(item.bucket || '').includes('Dormant'))
    .reduce((sum, item) => sum + toFiniteNumber(item.saldo), 0)

  if (top5Ratio >= 30) {
    return `Konsentrasi Top 5 deposan berada pada ${formatTruncatedPercentage(top5Ratio)} dari total DPK. Prioritaskan relationship plan, early renewal, dan monitoring pergerakan saldo harian untuk deposan utama.`
  }

  if (nearMaturity > 0) {
    return `Deposito yang jatuh tempo sampai 30 hari ke depan tercatat ${formatExactRupiah(nearMaturity)}. Pastikan daftar nasabah prioritas sudah masuk agenda rollover dan komunikasi ARO.`
  }

  if (dormantSaldo > 0) {
    return `Saldo dormant/pasif terdeteksi ${formatExactRupiah(dormantSaldo)}. Gunakan daftar ini untuk reaktivasi rekening dan evaluasi kualitas dana murah.`
  }

  return `Konsentrasi DPK relatif terkendali: Top 10 berada pada ${formatTruncatedPercentage(top10Ratio)}. Tetap pantau deposan besar, maturity deposito, dan perubahan mix produk setiap hari.`
})

const concentrationSummary = computed(() => fundingConcentration.value?.summary || {})
const concentrationBands = computed(() => fundingConcentration.value?.concentration_bands || [])
const concentrationTopDepositors = computed(() => fundingConcentration.value?.top_depositors || [])
const concentrationInterpretation = computed(() => {
  const top1Ratio = toFiniteNumber(concentrationSummary.value.top1_ratio)
  const top5Ratio = toFiniteNumber(concentrationSummary.value.top5_ratio)
  const top10Ratio = toFiniteNumber(concentrationSummary.value.top10_ratio)
  const largestBand = [...concentrationBands.value].sort((left, right) => toFiniteNumber(right.total_dpk) - toFiniteNumber(left.total_dpk))[0]

  if (top1Ratio >= 10) {
    return `Top 1 deposan menyumbang ${formatTruncatedPercentage(top1Ratio)} dari total DPK. Perlu relationship plan individual, monitoring saldo harian, dan mitigasi risiko penarikan mendadak.`
  }

  if (top5Ratio >= 30) {
    return `Top 5 deposan berada pada ${formatTruncatedPercentage(top5Ratio)} dari total DPK. Konsentrasi perlu dikendalikan lewat diversifikasi deposan dan strategi dana retail.`
  }

  if (top10Ratio >= 40) {
    return `Top 10 deposan mencapai ${formatTruncatedPercentage(top10Ratio)}. Pastikan maturity, pricing, dan komunikasi deposan besar masuk watchlist ALCO/treasury.`
  }

  return `Konsentrasi DPK relatif terkendali. Band terbesar ${largestBand?.band || '-'} mewakili ${formatTruncatedPercentage(largestBand?.share_percent)} dari total DPK.`
})

const concentrationBandMax = computed(() => Math.max(...concentrationBands.value.map(item => toFiniteNumber(item.total_dpk)), 1))
const maturityPressure = computed(() => maturityBuckets.value.reduce((sum, item) => {
  const bucket = String(item.bucket || '')
  if (['Lewat jatuh tempo', '0-7 hari', '8-14 hari', '15-30 hari'].includes(bucket)) {
    return sum + toFiniteNumber(item.nominal)
  }
  return sum
}, 0))
const dormantPressure = computed(() => dormantBuckets.value.reduce((sum, item) => String(item.bucket || '').includes('Dormant') ? sum + toFiniteNumber(item.saldo) : sum, 0))

const baghasCards = computed(() => [
  {
    label: 'Bagi Hasil Dibayar',
    value: formatExactRupiah(baghasSummary.value.total_baghas_bayar),
    sub: `Net setelah pajak ${formatExactRupiah(baghasSummary.value.net_baghas_bayar)}`,
    icon: 'ri-hand-coin-line',
    color: '#0d9488',
  },
  {
    label: 'Bagi Hasil Dihitung',
    value: formatExactRupiah(baghasSummary.value.total_baghas_hitung),
    sub: 'Akumulasi field hitung CBS',
    icon: 'ri-calculator-line',
    color: '#0284c7',
  },
  {
    label: 'Pajak Bagi Hasil',
    value: formatExactRupiah(baghasSummary.value.total_pajak),
    sub: `${formatTruncatedPercentage(baghasSummary.value.tax_to_baghas_ratio)} dari baghas bayar`,
    icon: 'ri-file-list-3-line',
    color: '#f59e0b',
  },
  {
    label: 'Rasio ke Saldo Rata',
    value: formatTruncatedPercentage(baghasSummary.value.baghas_to_average_ratio),
    sub: 'Indikasi biaya dana berjalan',
    icon: 'ri-percent-line',
    color: '#7c3aed',
  },
])

const baghasInterpretation = computed(() => {
  const depositShare = toFiniteNumber(baghasSummary.value.deposit_share_baghas)
  const savingShare = toFiniteNumber(baghasSummary.value.saving_share_baghas)
  const taxRatio = toFiniteNumber(baghasSummary.value.tax_to_baghas_ratio)
  const dominant = depositShare >= savingShare ? 'Deposito' : 'Tabungan'
  const dominantShare = Math.max(depositShare, savingShare)

  if (depositShare >= 70) {
    return `Beban bagi hasil didominasi Deposito sebesar ${formatTruncatedPercentage(depositShare)}. Pantau pricing deposito besar, nisbah khusus, dan rollover deposan utama agar biaya dana tetap terkendali.`
  }

  if (taxRatio >= 15) {
    return `Pajak bagi hasil mencapai ${formatTruncatedPercentage(taxRatio)} dari bagi hasil dibayar. Pastikan status pajak dan pengecualian PPH pada deposan prioritas tervalidasi.`
  }

  return `${dominant} menjadi kontributor utama bagi hasil dengan porsi ${formatTruncatedPercentage(dominantShare)}. Monitoring harian tetap perlu diarahkan ke produk dan nasabah dengan kontribusi baghas tertinggi.`
})

const baghasCostMetrics = computed(() => {
  const totalBaghas = toFiniteNumber(baghasSummary.value.total_baghas_bayar)
  const totalSaldo = toFiniteNumber(baghasSummary.value.total_saldo)
  const totalSaldoRata = toFiniteNumber(baghasSummary.value.total_saldo_rata)
  const netBaghas = toFiniteNumber(baghasSummary.value.net_baghas_bayar)
  const topDepositorBaghas = toFiniteNumber(topBaghasDepositors.value?.[0]?.baghas_bayar)

  return {
    gross_cost_to_saldo: totalSaldo > 0 ? (totalBaghas / totalSaldo) * 100 : 0,
    gross_cost_to_average: totalSaldoRata > 0 ? (totalBaghas / totalSaldoRata) * 100 : 0,
    net_cost_to_average: totalSaldoRata > 0 ? (netBaghas / totalSaldoRata) * 100 : 0,
    top_depositor_share: totalBaghas > 0 ? (topDepositorBaghas / totalBaghas) * 100 : 0,
  }
})

const baghasCostInterpretation = computed(() => {
  const depositShare = toFiniteNumber(baghasSummary.value.deposit_share_baghas)
  const topShare = toFiniteNumber(baghasCostMetrics.value.top_depositor_share)
  const netCost = toFiniteNumber(baghasCostMetrics.value.net_cost_to_average)

  if (topShare >= 20) {
    return `Top deposan baghas menyerap ${formatTruncatedPercentage(topShare)} dari total bagi hasil dibayar. Pastikan nisbah dan pricing deposan tersebut masuk watchlist biaya dana.`
  }

  if (depositShare >= 75) {
    return `Biaya dana masih sangat dipengaruhi deposito dengan porsi ${formatTruncatedPercentage(depositShare)}. Prioritaskan penguatan dana murah dan evaluasi nisbah deposito besar.`
  }

  return `Biaya dana net terhadap saldo rata berada di ${formatTruncatedPercentage(netCost)}. Pantau perubahan nisbah, pajak, dan komposisi deposito/tabungan secara rutin.`
})

const nisbahBucketMax = computed(() => Math.max(...depositByNisbah.value.map(item => toFiniteNumber(item.baghas_bayar)), 1))
const topBaghasMax = computed(() => Math.max(...topBaghasDepositors.value.map(item => toFiniteNumber(item.baghas_bayar)), 1))
const topSavingBaghasProduct = computed(() => [...savingBaghasByProduct.value].sort((left, right) => toFiniteNumber(right.baghas_bayar) - toFiniteNumber(left.baghas_bayar))[0] || {})
const topDepositBaghasProduct = computed(() => [...depositBaghasByProduct.value].sort((left, right) => toFiniteNumber(right.baghas_bayar) - toFiniteNumber(left.baghas_bayar))[0] || {})
const topNisbahBaghasBucket = computed(() => [...depositByNisbah.value].sort((left, right) => toFiniteNumber(right.baghas_bayar) - toFiniteNumber(left.baghas_bayar))[0] || {})
const topBaghasDepositor = computed(() => topBaghasDepositors.value?.[0] || {})
const baghasDriverCards = computed(() => [
  {
    label: 'Produk Tabungan Terbesar',
    title: topSavingBaghasProduct.value.produk || '-',
    value: formatExactRupiah(topSavingBaghasProduct.value.baghas_bayar),
    sub: `${formatExactNumber(topSavingBaghasProduct.value.noa)} rekening • pajak ${formatExactRupiah(topSavingBaghasProduct.value.pajak)}`,
    color: '#0284c7',
  },
  {
    label: 'Produk Deposito Terbesar',
    title: topDepositBaghasProduct.value.produk || '-',
    value: formatExactRupiah(topDepositBaghasProduct.value.baghas_bayar),
    sub: `${formatExactNumber(topDepositBaghasProduct.value.noa)} bilyet • avg nisbah ${formatTruncatedPercentage(topDepositBaghasProduct.value.avg_nisbah)}`,
    color: '#7c3aed',
  },
  {
    label: 'Bucket Nisbah Dominan',
    title: topNisbahBaghasBucket.value.bucket || '-',
    value: formatExactRupiah(topNisbahBaghasBucket.value.baghas_bayar),
    sub: `${formatExactNumber(topNisbahBaghasBucket.value.noa)} bilyet • avg rate ${formatTruncatedPercentage(topNisbahBaghasBucket.value.avg_rate)}`,
    color: '#f59e0b',
  },
  {
    label: 'Top Nasabah Baghas',
    title: topBaghasDepositor.value.nama || '-',
    value: formatExactRupiah(topBaghasDepositor.value.baghas_bayar),
    sub: `${formatTruncatedPercentage(baghasCostMetrics.value.top_depositor_share)} dari total baghas bayar`,
    color: '#dc2626',
  },
])
const baghasFundingMix = computed(() => [
  {
    label: 'Tabungan',
    value: toFiniteNumber(baghasSummary.value.saving_share_baghas),
    amount: toFiniteNumber(savingBaghasSummary.value.baghas_bayar),
    color: '#0284c7',
  },
  {
    label: 'Deposito',
    value: toFiniteNumber(baghasSummary.value.deposit_share_baghas),
    amount: toFiniteNumber(depositBaghasSummary.value.baghas_bayar),
    color: '#7c3aed',
  },
])

const savingHeaders = [
  { title: 'No Rekening', key: 'norekening', width: 150 },
  { title: 'Nama Nasabah', key: 'nama', minWidth: 220 },
  { title: 'Produk', key: 'jenis', minWidth: 190 },
  { title: 'AO', key: 'ao', minWidth: 140 },
  { title: 'Saldo Akhir', key: 'saldo', align: 'end', width: 170 },
  { title: 'Saldo Rata', key: 'saldo_rata', align: 'end', width: 170 },
  { title: 'Nisbah', key: 'nisbah', align: 'end', width: 100 },
  { title: 'Status', key: 'stsrec', align: 'center', width: 110 },
]

const savingDormantHeaders = [
  { title: 'No Rekening', key: 'norekening', width: 150 },
  { title: 'No CIF', key: 'nocif', width: 130 },
  { title: 'Nama Nasabah', key: 'nama', minWidth: 220 },
  { title: 'AO', key: 'ao', minWidth: 140 },
  { title: 'Wilayah', key: 'wilayah', minWidth: 150 },
  { title: 'Tgl Transaksi', key: 'tgltrans', width: 140 },
  { title: 'Hari Pasif', key: 'hari_pasif', align: 'end', width: 120 },
  { title: 'Saldo', key: 'saldo', align: 'end', width: 170 },
  { title: 'Saldo Rata', key: 'saldo_rata', align: 'end', width: 170 },
]

const depositHeaders = [
  { title: 'No Deposito', key: 'norekening', width: 150 },
  { title: 'No Bilyet', key: 'nobilyet', width: 145 },
  { title: 'Nama Nasabah', key: 'nama', minWidth: 220 },
  { title: 'Produk', key: 'jenis', minWidth: 170 },
  { title: 'AO', key: 'ao', minWidth: 140 },
  { title: 'Nominal', key: 'nominal', align: 'end', width: 180 },
  { title: 'Nisbah', key: 'nisbah', align: 'end', width: 100 },
  { title: 'Jatuh Tempo', key: 'jatuh_tempo', width: 130 },
  { title: 'Hari JT', key: 'hari_jatuh_tempo', align: 'end', width: 105 },
  { title: 'ARO', key: 'aro', align: 'center', width: 90 },
]

const rekapHeaders = [
  { title: 'Kode', key: 'id', width: 100 },
  { title: 'Grup', key: 'label', minWidth: 220 },
  { title: 'NOA', key: 'noa', align: 'end', width: 110 },
  { title: 'Saldo/Nominal', key: 'principal', align: 'end', width: 190 },
  { title: 'Saldo Rata', key: 'saldo_rata', align: 'end', width: 190 },
  { title: 'Bagi Hasil Dibayar', key: 'bagi_hasil_bayar', align: 'end', width: 190 },
  { title: 'Pajak', key: 'tax', align: 'end', width: 170 },
]

const depositRekapHeaders = [
  { title: 'Kode', key: 'id', width: 100 },
  { title: 'Grup', key: 'label', minWidth: 220 },
  { title: 'Bilyet', key: 'noa', align: 'end', width: 110 },
  { title: 'Nominal', key: 'principal', align: 'end', width: 190 },
  { title: 'Saldo Rata', key: 'saldo_rata', align: 'end', width: 190 },
  { title: 'Bagi Hasil Dibayar', key: 'bagi_hasil_bayar', align: 'end', width: 190 },
  { title: 'Pajak', key: 'tax', align: 'end', width: 170 },
  { title: 'Avg Nisbah', key: 'avg_nisbah', align: 'end', width: 130 },
  { title: 'Avg Eq Rate', key: 'avg_equivrate', align: 'end', width: 130 },
]

const topDepositorHeaders = [
  { title: 'Rank', key: 'ranking', align: 'center', width: 80 },
  { title: 'No CIF', key: 'nocif', width: 130 },
  { title: 'Nama Nasabah', key: 'nama', minWidth: 260 },
  { title: 'Tabungan', key: 'tabungan', align: 'end', width: 170 },
  { title: 'Deposito', key: 'deposito', align: 'end', width: 170 },
  { title: 'Total DPK', key: 'total_dpk', align: 'end', width: 180 },
  { title: 'Share', key: 'share_percent', align: 'end', width: 110 },
  { title: 'NOA', key: 'noa', align: 'end', width: 100 },
]

const maturityHeaders = [
  { title: 'Bucket Jatuh Tempo', key: 'bucket', minWidth: 180 },
  { title: 'Bilyet', key: 'noa', align: 'end', width: 100 },
  { title: 'Nominal', key: 'nominal', align: 'end', width: 190 },
  { title: 'ARO', key: 'aro_count', align: 'end', width: 100 },
  { title: 'Non ARO', key: 'non_aro_count', align: 'end', width: 110 },
]

const dormantBucketHeaders = [
  { title: 'Bucket Aktivitas', key: 'bucket', minWidth: 180 },
  { title: 'Rekening', key: 'noa', align: 'end', width: 110 },
  { title: 'Saldo Akhir', key: 'saldo', align: 'end', width: 190 },
  { title: 'Saldo Rata-Rata', key: 'saldo_rata', align: 'end', width: 190 },
]

const productMixHeaders = [
  { title: 'Domain', key: 'domain', width: 120 },
  { title: 'Produk', key: 'produk', minWidth: 240 },
  { title: 'NOA', key: 'noa', align: 'end', width: 100 },
  { title: 'Nominal', key: 'nominal', align: 'end', width: 190 },
  { title: 'Share', key: 'share_percent', align: 'end', width: 110 },
]

const baghasProductHeaders = [
  { title: 'Domain', key: 'domain', width: 120 },
  { title: 'Produk', key: 'produk', minWidth: 250 },
  { title: 'NOA', key: 'noa', align: 'end', width: 100 },
  { title: 'Saldo', key: 'saldo', align: 'end', width: 180 },
  { title: 'Baghas Hitung', key: 'baghas_hitung', align: 'end', width: 180 },
  { title: 'Baghas Bayar', key: 'baghas_bayar', align: 'end', width: 180 },
  { title: 'Pajak', key: 'pajak', align: 'end', width: 160 },
  { title: 'Avg Nisbah', key: 'avg_nisbah', align: 'end', width: 120 },
  { title: 'Avg Rate', key: 'avg_rate', align: 'end', width: 120 },
]

const nisbahBucketHeaders = [
  { title: 'Bucket Nisbah', key: 'bucket', minWidth: 180 },
  { title: 'Bilyet', key: 'noa', align: 'end', width: 100 },
  { title: 'Saldo', key: 'saldo', align: 'end', width: 180 },
  { title: 'Baghas Bayar', key: 'baghas_bayar', align: 'end', width: 180 },
  { title: 'Pajak', key: 'pajak', align: 'end', width: 160 },
  { title: 'Avg Eq Rate', key: 'avg_rate', align: 'end', width: 130 },
]

const topBaghasHeaders = [
  { title: 'Rank', key: 'ranking', align: 'center', width: 80 },
  { title: 'No CIF', key: 'nocif', width: 130 },
  { title: 'Nama Nasabah', key: 'nama', minWidth: 260 },
  { title: 'Bilyet', key: 'noa_deposito', align: 'end', width: 100 },
  { title: 'Saldo Deposito', key: 'saldo_deposito', align: 'end', width: 180 },
  { title: 'Baghas Bayar', key: 'baghas_bayar', align: 'end', width: 180 },
  { title: 'Pajak', key: 'pajak', align: 'end', width: 160 },
  { title: 'Avg Nisbah', key: 'avg_nisbah', align: 'end', width: 120 },
]

const concentrationBandHeaders = [
  { title: 'Band Nominal', key: 'band', minWidth: 190 },
  { title: 'Nasabah', key: 'depositor_count', align: 'end', width: 110 },
  { title: 'NOA Tabungan', key: 'noa_tabungan', align: 'end', width: 130 },
  { title: 'NOA Deposito', key: 'noa_deposito', align: 'end', width: 130 },
  { title: 'Tabungan', key: 'tabungan', align: 'end', width: 180 },
  { title: 'Deposito', key: 'deposito', align: 'end', width: 180 },
  { title: 'Total DPK', key: 'total_dpk', align: 'end', width: 190 },
  { title: 'Share', key: 'share_percent', align: 'end', width: 110 },
]

const perkembanganHeaders = [
  { title: 'Periode', key: 'periode', width: 110 },
  { title: 'NOA', key: 'noa', align: 'end', width: 120 },
  { title: 'Nominal', key: 'nominal', align: 'end', width: 190 },
  { title: 'MoM Growth', key: 'mom_growth_percent', align: 'end', width: 130 },
]

const targetHeaders = [
  { title: 'Periode', key: 'periode', width: 110 },
  { title: 'Target', key: 'target', align: 'end', width: 180 },
  { title: 'Realisasi', key: 'actual', align: 'end', width: 180 },
  { title: 'Gap', key: 'gap', align: 'end', width: 180 },
  { title: 'Achievement', key: 'achievement_percent', align: 'end', width: 130 },
]

const savingMutationHeaders = [
  { title: 'Periode', key: 'periode', width: 110 },
  { title: 'Debet', key: 'debet', align: 'end', width: 180 },
  { title: 'Kredit', key: 'kredit', align: 'end', width: 180 },
  { title: 'Netto', key: 'netto', align: 'end', width: 180 },
  { title: 'Transaksi', key: 'transaksi', align: 'end', width: 120 },
]

const depositMutationHeaders = [
  { title: 'Periode', key: 'periode', width: 110 },
  { title: 'Masuk', key: 'masuk', align: 'end', width: 180 },
  { title: 'Cair', key: 'cair', align: 'end', width: 180 },
  { title: 'Netto', key: 'netto', align: 'end', width: 180 },
  { title: 'Transaksi', key: 'transaksi', align: 'end', width: 120 },
]

async function getJson(url) {
  const response = await fetch(url)
  const json = await response.json()
  if (!response.ok || json.success === false) {
    throw new Error(json.message || `Gagal memuat ${url}`)
  }
  return json.data
}

function buildQuery(params = {}) {
  const query = new URLSearchParams()
  Object.entries(params).forEach(([key, value]) => {
    if (value !== null && value !== undefined && value !== '') query.set(key, value)
  })
  return query.toString()
}

async function loadOverview() {
  loading.value = true
  error.value = ''
  try {
    const [saving, deposit, jatuhTempo] = await Promise.all([
      getJson('/api/v1/saving/summary'),
      getJson('/api/v1/deposit/summary'),
      getJson('/api/v1/deposit/jatuh-tempo?per_page=100'),
    ])
    savingSummary.value = saving || {}
    depositSummary.value = deposit || {}
    depositJatuhTempo.value = jatuhTempo?.data ?? []
  } catch (err) {
    error.value = err.message
  } finally {
    loading.value = false
  }
}

async function loadSavingFilters() {
  try {
    savingFilterOptions.value = await getJson('/api/v1/saving/filters')
  } catch (err) {
    console.warn('Gagal memuat filter tabungan:', err)
  }
}

async function loadDepositFilters() {
  try {
    depositFilterOptions.value = await getJson('/api/v1/deposit/filters')
  } catch (err) {
    console.warn('Gagal memuat filter deposito:', err)
  }
}

async function loadSavingNominative({ cursor = savingCursor.value, reset = false } = {}) {
  loading.value = true
  error.value = ''
  try {
    if (reset) {
      cursor = ''
      savingCursor.value = ''
      savingCursorStack.value = []
    }

    const query = buildQuery({
      per_page: savingNominativeMeta.value.per_page,
      cursor,
      search: filters.savingSearch,
      cabang: filters.savingCabang,
      ao: filters.savingAo,
    })
    const data = await getJson(`/api/v1/saving/nominative?${query}`)
    savingData.value = data?.data ?? []
    savingNominativeMeta.value = {
      per_page: data?.per_page ?? savingNominativeMeta.value.per_page,
      next_cursor: data?.next_cursor ?? null,
      prev_cursor: data?.prev_cursor ?? null,
      next_page_url: data?.next_page_url ?? null,
      prev_page_url: data?.prev_page_url ?? null,
    }
  } catch (err) {
    error.value = err.message
  } finally {
    loading.value = false
  }
}

function loadNextSavingPage() {
  if (!savingHasNextPage.value) return
  const currentCursor = savingCursor.value || ''
  const nextCursor = savingNominativeMeta.value.next_cursor || ''
  savingCursorStack.value.push(currentCursor)
  savingCursor.value = nextCursor
  loadSavingNominative({ cursor: nextCursor })
}

function loadPrevSavingPage() {
  if (!savingHasPrevPage.value) return
  const prevCursor = savingCursorStack.value.pop() || ''
  savingCursor.value = prevCursor
  loadSavingNominative({ cursor: prevCursor })
}

async function loadSavingRekap() {
  loading.value = true
  error.value = ''
  try {
    const query = buildQuery({
      group_by: savingGroup.value,
      search: filters.savingRekapSearch,
      cabang: filters.savingRekapCabang,
      ao: filters.savingRekapAo,
    })
    savingRekap.value = await getJson(`/api/v1/saving/rekapitulasi?${query}`)
  } catch (err) {
    error.value = err.message
  } finally {
    loading.value = false
  }
}

async function loadSavingDormant({ cursor = savingDormantCursor.value, reset = false } = {}) {
  loading.value = true
  error.value = ''
  try {
    if (reset) {
      cursor = ''
      savingDormantCursor.value = ''
      savingDormantCursorStack.value = []
    }

    const query = buildQuery({
      per_page: savingDormantMeta.value.per_page,
      cursor,
      search: filters.savingDormantSearch,
      cabang: filters.savingDormantCabang,
      ao: filters.savingDormantAo,
    })
    const data = await getJson(`/api/v1/saving/doormant?${query}`)
    savingDormant.value = data?.data ?? []
    savingDormantMeta.value = {
      per_page: data?.per_page ?? savingDormantMeta.value.per_page,
      next_cursor: data?.next_cursor ?? null,
      prev_cursor: data?.prev_cursor ?? null,
      next_page_url: data?.next_page_url ?? null,
      prev_page_url: data?.prev_page_url ?? null,
    }
  } catch (err) {
    error.value = err.message
  } finally {
    loading.value = false
  }
}

function loadNextDormantPage() {
  if (!savingDormantHasNextPage.value) return
  const currentCursor = savingDormantCursor.value || ''
  const nextCursor = savingDormantMeta.value.next_cursor || ''
  savingDormantCursorStack.value.push(currentCursor)
  savingDormantCursor.value = nextCursor
  loadSavingDormant({ cursor: nextCursor })
}

function loadPrevDormantPage() {
  if (!savingDormantHasPrevPage.value) return
  const prevCursor = savingDormantCursorStack.value.pop() || ''
  savingDormantCursor.value = prevCursor
  loadSavingDormant({ cursor: prevCursor })
}

async function loadDepositNominative({ cursor = depositCursor.value, reset = false } = {}) {
  loading.value = true
  error.value = ''
  try {
    if (reset) {
      cursor = ''
      depositCursor.value = ''
      depositCursorStack.value = []
    }

    const query = buildQuery({
      per_page: depositNominativeMeta.value.per_page,
      cursor,
      search: filters.depositSearch,
      cabang: filters.depositCabang,
      ao: filters.depositAo,
    })
    const data = await getJson(`/api/v1/deposit/nominative?${query}`)
    depositData.value = data?.data ?? []
    depositNominativeMeta.value = {
      per_page: data?.per_page ?? depositNominativeMeta.value.per_page,
      next_cursor: data?.next_cursor ?? null,
      prev_cursor: data?.prev_cursor ?? null,
      next_page_url: data?.next_page_url ?? null,
      prev_page_url: data?.prev_page_url ?? null,
    }
  } catch (err) {
    error.value = err.message
  } finally {
    loading.value = false
  }
}

function loadNextDepositPage() {
  if (!depositHasNextPage.value) return
  const currentCursor = depositCursor.value || ''
  const nextCursor = depositNominativeMeta.value.next_cursor || ''
  depositCursorStack.value.push(currentCursor)
  depositCursor.value = nextCursor
  loadDepositNominative({ cursor: nextCursor })
}

function loadPrevDepositPage() {
  if (!depositHasPrevPage.value) return
  const prevCursor = depositCursorStack.value.pop() || ''
  depositCursor.value = prevCursor
  loadDepositNominative({ cursor: prevCursor })
}

async function loadDepositRekap() {
  loading.value = true
  error.value = ''
  try {
    const query = buildQuery({
      group_by: depositGroup.value,
      search: filters.depositRekapSearch,
      cabang: filters.depositRekapCabang,
      ao: filters.depositRekapAo,
    })
    depositRekap.value = await getJson(`/api/v1/deposit/rekapitulasi?${query}`)
  } catch (err) {
    error.value = err.message
  } finally {
    loading.value = false
  }
}

async function loadDepositJatuhTempo({ cursor = depositMaturityCursor.value, reset = false } = {}) {
  loading.value = true
  error.value = ''
  try {
    if (reset) {
      cursor = ''
      depositMaturityCursor.value = ''
      depositMaturityCursorStack.value = []
    }

    const query = buildQuery({
      per_page: depositMaturityMeta.value.per_page,
      cursor,
      search: filters.depositMaturitySearch,
      cabang: filters.depositMaturityCabang,
      ao: filters.depositMaturityAo,
    })
    const data = await getJson(`/api/v1/deposit/jatuh-tempo?${query}`)
    depositJatuhTempo.value = data?.data ?? []
    depositMaturityMeta.value = {
      per_page: data?.per_page ?? depositMaturityMeta.value.per_page,
      next_cursor: data?.next_cursor ?? null,
      prev_cursor: data?.prev_cursor ?? null,
      next_page_url: data?.next_page_url ?? null,
      prev_page_url: data?.prev_page_url ?? null,
    }
  } catch (err) {
    error.value = err.message
  } finally {
    loading.value = false
  }
}

function loadNextMaturityPage() {
  if (!depositMaturityHasNextPage.value) return
  const currentCursor = depositMaturityCursor.value || ''
  const nextCursor = depositMaturityMeta.value.next_cursor || ''
  depositMaturityCursorStack.value.push(currentCursor)
  depositMaturityCursor.value = nextCursor
  loadDepositJatuhTempo({ cursor: nextCursor })
}

function loadPrevMaturityPage() {
  if (!depositMaturityHasPrevPage.value) return
  const prevCursor = depositMaturityCursorStack.value.pop() || ''
  depositMaturityCursor.value = prevCursor
  loadDepositJatuhTempo({ cursor: prevCursor })
}

async function loadRiskOverview() {
  loading.value = true
  error.value = ''
  try {
    riskOverview.value = await getJson('/api/v1/funding/risk/overview')
  } catch (err) {
    error.value = err.message
  } finally {
    loading.value = false
  }
}

async function loadBaghasOverview() {
  loading.value = true
  error.value = ''
  try {
    baghasOverview.value = await getJson('/api/v1/funding/baghas/overview')
  } catch (err) {
    error.value = err.message
  } finally {
    loading.value = false
  }
}

async function loadFundingPerkembangan() {
  loading.value = true
  error.value = ''
  try {
    fundingPerkembangan.value = await getJson('/api/v1/funding/perkembangan')
  } catch (err) {
    error.value = err.message
  } finally {
    loading.value = false
  }
}

async function loadFundingTarget() {
  loading.value = true
  error.value = ''
  try {
    fundingTarget.value = await getJson('/api/v1/funding/target')
  } catch (err) {
    error.value = err.message
  } finally {
    loading.value = false
  }
}

async function loadFundingMutasi() {
  loading.value = true
  error.value = ''
  try {
    fundingMutasi.value = await getJson('/api/v1/funding/mutasi')
  } catch (err) {
    error.value = err.message
  } finally {
    loading.value = false
  }
}

async function loadFundingConcentration() {
  loading.value = true
  error.value = ''
  try {
    fundingConcentration.value = await getJson('/api/v1/funding/concentration')
  } catch (err) {
    error.value = err.message
  } finally {
    loading.value = false
  }
}

function reloadCurrentTab() {
  if (activeDomain.value === 'overview') return loadOverview()
  if (activeDomain.value === 'risk') return loadRiskOverview()
  if (activeDomain.value === 'baghas') return loadBaghasOverview()
  if (activeDomain.value === 'perkembangan') return loadFundingPerkembangan()
  if (activeDomain.value === 'target') return loadFundingTarget()
  if (activeDomain.value === 'mutasi') return loadFundingMutasi()
  if (activeDomain.value === 'concentration') return loadFundingConcentration()
  if (activeDomain.value === 'tabungan') {
    if (savingFeature.value === 'nominatif') return loadSavingNominative()
    if (savingFeature.value === 'rekapitulasi') return loadSavingRekap()
    if (savingFeature.value === 'dormant') return loadSavingDormant()
  }
  if (activeDomain.value === 'deposito') {
    if (depositFeature.value === 'nominatif') return loadDepositNominative()
    if (depositFeature.value === 'rekapitulasi') return loadDepositRekap()
    if (depositFeature.value === 'jatuh-tempo') return loadDepositJatuhTempo()
  }

  return Promise.resolve()
}

function formatDate(value) {
  if (!value) return '-'
  const text = String(value).trim()
  if (/^\d{8}$/.test(text)) return `${text.slice(6, 8)}-${text.slice(4, 6)}-${text.slice(0, 4)}`
  if (/^\d{4}-\d{2}-\d{2}/.test(text)) return text.slice(0, 10).split('-').reverse().join('-')
  return text
}

function statusLabel(value) {
  if (value === 'A') return 'Aktif'
  if (value === 'N') return 'New'
  return value || 'Aktif'
}

function formatPrincipal(item) {
  return formatExactRupiah(item.total_saldo ?? item.total_nominal)
}

function depositorNoa(item) {
  return formatExactNumber(toFiniteNumber(item.noa_tabungan) + toFiniteNumber(item.noa_deposito))
}

watch([savingGroup, () => filters.savingRekapCabang, () => filters.savingRekapAo], () => {
  if (activeDomain.value === 'tabungan' && savingFeature.value === 'rekapitulasi') {
    loadSavingRekap()
  }
})
watch([depositGroup, () => filters.depositRekapCabang, () => filters.depositRekapAo], () => {
  if (activeDomain.value === 'deposito' && depositFeature.value === 'rekapitulasi') {
    loadDepositRekap()
  }
})
watch([() => filters.depositMaturityCabang, () => filters.depositMaturityAo], () => {
  if (activeDomain.value === 'deposito' && depositFeature.value === 'jatuh-tempo') {
    loadDepositJatuhTempo({ reset: true })
  }
})
watch([() => filters.savingCabang, () => filters.savingAo], () => {
  if (activeDomain.value === 'tabungan' && savingFeature.value === 'nominatif') {
    loadSavingNominative({ reset: true })
  }
})
watch([() => filters.savingDormantCabang, () => filters.savingDormantAo], () => {
  if (activeDomain.value === 'tabungan' && savingFeature.value === 'dormant') {
    loadSavingDormant({ reset: true })
  }
})
watch([() => filters.depositCabang, () => filters.depositAo], () => {
  if (activeDomain.value === 'deposito' && depositFeature.value === 'nominatif') {
    loadDepositNominative({ reset: true })
  }
})

onMounted(async () => {
  await Promise.all([loadOverview(), loadSavingFilters(), loadDepositFilters()])
  if (activeDomain.value !== 'overview') {
    await reloadCurrentTab()
  }
})
</script>

<template>
  <div class="fin-page px-4 pt-0 funding-page">
    <section class="fin-hero mb-6">
      <div class="fin-hero__deco"></div>
      <div class="fin-hero__inner">
        <div class="fin-hero__top">
          <div class="fin-hero__icon">
            <VIcon :icon="pageMeta.icon" />
          </div>
          <div class="fin-hero__meta">
            <h1 class="fin-hero__title">{{ pageMeta.title }}</h1>
            <p class="fin-hero__subtitle">
              {{ pageMeta.subtitle }}
            </p>
            <div class="fin-hero__badges">
              <span class="fin-badge fin-badge--teal">Legacy Source: MDB Dashboard</span>
              <span class="fin-badge fin-badge--glass">No Rounding Nominal</span>
              <span class="fin-badge fin-badge--info">CBS Mirror</span>
            </div>
          </div>
          <VBtn class="fin-filter-apply" prepend-icon="ri-refresh-line" :loading="loading" @click="reloadCurrentTab">
            Refresh Data
          </VBtn>
        </div>
      </div>
    </section>

    <VAlert v-if="error" type="warning" variant="tonal" closable class="mb-4" @click:close="error = ''">
      {{ error }}
    </VAlert>

    <div class="kpi-cards-grid">
      <div
        v-for="card in overviewCards"
        :key="card.label"
        class="kpi-card"
        :class="card.tone"
      >
        <div class="kpi-card__accent" :style="{ background: card.color }"></div>
        <div class="kpi-card__inner">
          <div class="kpi-card__header">
            <div class="kpi-card__label">{{ card.label }}</div>
            <div class="kpi-card__icon" :style="{ background: `${card.color}1f`, color: card.color }">
              <VIcon :icon="card.icon" />
            </div>
          </div>
          <div class="kpi-card__value">
            <span class="fin-money-exact">{{ card.value }}</span>
          </div>
          <div class="kpi-card__sub">{{ card.sub }}</div>
        </div>
      </div>
    </div>

    <section class="funding-insight mb-6">
      <div>
        <div class="funding-insight__eyebrow">Interpretasi Operasional</div>
        <p>{{ fundingInterpretation }}</p>
      </div>
      <div class="funding-mix">
        <div class="funding-mix__row">
          <span>Tabungan</span>
          <strong>{{ formatTruncatedPercentage(savingShare) }}</strong>
        </div>
        <div class="funding-mix__bar">
          <span :style="{ width: `${Math.min(savingShare, 100)}%` }"></span>
        </div>
        <div class="funding-mix__row">
          <span>Deposito</span>
          <strong>{{ formatTruncatedPercentage(depositShare) }}</strong>
        </div>
        <div class="funding-mix__bar funding-mix__bar--purple">
          <span :style="{ width: `${Math.min(depositShare, 100)}%` }"></span>
        </div>
      </div>
    </section>

    <div class="funding-sections">
      <section v-if="activeDomain === 'overview'" class="funding-section">
        <div class="funding-grid funding-grid--two">
          <div class="content-card">
            <div class="content-card__accent-top" style="background:#0d9488"></div>
            <div class="content-card__header">
              <div>
                <div class="content-card__title">Ringkasan Tabungan</div>
                <div class="content-card__subtitle">Sumber: TOFTABB aktif, belum tutup.</div>
              </div>
            </div>
            <div class="content-card__body funding-stat-list">
              <div><span>Total saldo</span><strong>{{ formatExactRupiah(savingSummary.total_saldo) }}</strong></div>
              <div><span>Saldo rata-rata</span><strong>{{ formatExactRupiah(savingSummary.saldo_rata) }}</strong></div>
              <div><span>Bagi hasil dibayar</span><strong>{{ formatExactRupiah(savingSummary.bagi_hasil_bayar) }}</strong></div>
              <div><span>Pajak dibayar</span><strong>{{ formatExactRupiah(savingSummary.pajak_bayar) }}</strong></div>
            </div>
          </div>
          <div class="content-card">
            <div class="content-card__accent-top" style="background:#7c3aed"></div>
            <div class="content-card__header">
              <div>
                <div class="content-card__title">Ringkasan Deposito</div>
                <div class="content-card__subtitle">Sumber: TOFDEP aktif, belum tutup.</div>
              </div>
            </div>
            <div class="content-card__body funding-stat-list">
              <div><span>Total nominal</span><strong>{{ formatExactRupiah(depositSummary.total_nominal) }}</strong></div>
              <div><span>Saldo rata-rata</span><strong>{{ formatExactRupiah(depositSummary.saldo_rata) }}</strong></div>
              <div><span>Bagi hasil dibayar</span><strong>{{ formatExactRupiah(depositSummary.bagi_hasil_bayar) }}</strong></div>
              <div><span>Pajak</span><strong>{{ formatExactRupiah(depositSummary.pajak) }}</strong></div>
            </div>
          </div>
        </div>
      </section>

      <section v-if="activeDomain === 'tabungan'" class="funding-section">
        <div class="content-card">
          <div class="content-card__accent-top" style="background:#0284c7"></div>
          <div class="content-card__header funding-card-header">
            <div>
              <div class="content-card__title">{{ savingPageTitle }}</div>
              <div class="content-card__subtitle">{{ savingPageSubtitle }}</div>
            </div>
          </div>
          <div class="content-card__body">
            <div v-if="savingFeature === 'nominatif'" class="funding-toolbar">
              <VTextField
                v-model="filters.savingSearch"
                prepend-inner-icon="ri-search-line"
                label="Cari rekening / CIF / nama"
                density="compact"
                variant="outlined"
                hide-details
                clearable
                @keyup.enter="loadSavingNominative({ reset: true })"
                @click:clear="loadSavingNominative({ reset: true })"
              />
              <VSelect
                v-model="filters.savingCabang"
                :items="savingCabangOptions"
                prepend-inner-icon="ri-building-2-line"
                label="Cabang"
                density="compact"
                variant="outlined"
                hide-details
              />
              <VSelect
                v-model="filters.savingAo"
                :items="savingAoOptions"
                prepend-inner-icon="ri-user-star-line"
                label="Account Officer"
                density="compact"
                variant="outlined"
                hide-details
              />
              <VBtn color="primary" variant="flat" prepend-icon="ri-search-line" @click="loadSavingNominative({ reset: true })">Cari</VBtn>
              <VBtn
                variant="tonal"
                color="secondary"
                prepend-icon="ri-refresh-line"
                @click="filters.savingSearch = ''; filters.savingCabang = ''; filters.savingAo = ''; loadSavingNominative({ reset: true })"
              >
                Reset
              </VBtn>
            </div>

            <div v-if="savingFeature === 'nominatif'" class="funding-page-summary">
              <div>
                <span>Data tampil</span>
                <strong>{{ formatExactNumber(savingData.length) }} rekening</strong>
              </div>
              <div>
                <span>Saldo tampil</span>
                <strong>{{ formatExactRupiah(savingCurrentSaldo) }}</strong>
              </div>
              <div>
                <span>Saldo rata tampil</span>
                <strong>{{ formatExactRupiah(savingCurrentAverage) }}</strong>
              </div>
              <div>
                <span>Baghas bayar tampil</span>
                <strong>{{ formatExactRupiah(savingCurrentBaghas) }}</strong>
              </div>
            </div>

            <div v-if="savingFeature === 'rekapitulasi'" class="funding-toolbar">
              <VSelect
                v-model="savingGroup"
                :items="groupOptions"
                label="Kelompok rekap"
                density="compact"
                variant="outlined"
                hide-details
                style="max-width:260px"
              />
              <VTextField
                v-model="filters.savingRekapSearch"
                prepend-inner-icon="ri-search-line"
                label="Cari rekening / CIF / nama"
                density="compact"
                variant="outlined"
                hide-details
                clearable
                @keyup.enter="loadSavingRekap"
                @click:clear="loadSavingRekap"
              />
              <VSelect
                v-model="filters.savingRekapCabang"
                :items="savingCabangOptions"
                prepend-inner-icon="ri-building-2-line"
                label="Cabang"
                density="compact"
                variant="outlined"
                hide-details
              />
              <VSelect
                v-model="filters.savingRekapAo"
                :items="savingAoOptions"
                prepend-inner-icon="ri-user-star-line"
                label="Account Officer"
                density="compact"
                variant="outlined"
                hide-details
              />
              <VBtn color="primary" variant="flat" prepend-icon="ri-filter-3-line" @click="loadSavingRekap">Terapkan</VBtn>
              <VBtn
                variant="tonal"
                color="secondary"
                prepend-icon="ri-refresh-line"
                @click="filters.savingRekapSearch = ''; filters.savingRekapCabang = ''; filters.savingRekapAo = ''; loadSavingRekap()"
              >
                Reset
              </VBtn>
            </div>

            <div v-if="savingFeature === 'rekapitulasi'" class="funding-rekap-insight">
              <div>
                <span>Interpretasi Rekap</span>
                <p>{{ savingRekapInterpretation }}</p>
              </div>
              <div class="funding-rekap-insight__metrics">
                <div>
                  <span>Kelompok</span>
                  <strong>{{ formatExactNumber(savingRekap.length) }}</strong>
                </div>
                <div>
                  <span>NOA</span>
                  <strong>{{ formatExactNumber(savingRekapTotals.noa) }}</strong>
                </div>
                <div>
                  <span>Saldo</span>
                  <strong>{{ formatExactRupiah(savingRekapTotals.saldo) }}</strong>
                </div>
                <div>
                  <span>Baghas Bayar</span>
                  <strong>{{ formatExactRupiah(savingRekapTotals.baghas) }}</strong>
                </div>
              </div>
            </div>

            <div v-if="savingFeature === 'dormant'" class="funding-toolbar">
              <VTextField
                v-model="filters.savingDormantSearch"
                prepend-inner-icon="ri-search-line"
                label="Cari rekening / CIF / nama"
                density="compact"
                variant="outlined"
                hide-details
                clearable
                @keyup.enter="loadSavingDormant({ reset: true })"
                @click:clear="loadSavingDormant({ reset: true })"
              />
              <VSelect
                v-model="filters.savingDormantCabang"
                :items="savingCabangOptions"
                prepend-inner-icon="ri-building-2-line"
                label="Cabang"
                density="compact"
                variant="outlined"
                hide-details
              />
              <VSelect
                v-model="filters.savingDormantAo"
                :items="savingAoOptions"
                prepend-inner-icon="ri-user-star-line"
                label="Account Officer"
                density="compact"
                variant="outlined"
                hide-details
              />
              <VBtn color="primary" variant="flat" prepend-icon="ri-filter-3-line" @click="loadSavingDormant({ reset: true })">Terapkan</VBtn>
              <VBtn
                variant="tonal"
                color="secondary"
                prepend-icon="ri-refresh-line"
                @click="filters.savingDormantSearch = ''; filters.savingDormantCabang = ''; filters.savingDormantAo = ''; loadSavingDormant({ reset: true })"
              >
                Reset
              </VBtn>
            </div>

            <div v-if="savingFeature === 'dormant'" class="funding-rekap-insight funding-dormant-insight">
              <div>
                <span>Interpretasi Dormant</span>
                <p>{{ savingDormantInterpretation }}</p>
              </div>
              <div class="funding-rekap-insight__metrics">
                <div>
                  <span>Rekening Tampil</span>
                  <strong>{{ formatExactNumber(savingDormantTotals.noa) }}</strong>
                </div>
                <div>
                  <span>Saldo Dormant</span>
                  <strong>{{ formatExactRupiah(savingDormantTotals.saldo) }}</strong>
                </div>
                <div>
                  <span>Saldo Rata</span>
                  <strong>{{ formatExactRupiah(savingDormantTotals.saldoRata) }}</strong>
                </div>
                <div>
                  <span>Pasif Terlama</span>
                  <strong>{{ formatExactNumber(savingDormantTotals.maxHariPasif) }} hari</strong>
                </div>
              </div>
            </div>

            <div v-if="savingFeature === 'dormant'" class="funding-dormant-buckets">
              <div v-for="bucket in savingDormantBuckets" :key="bucket.label" class="funding-dormant-bucket">
                <div class="funding-dormant-bucket__dot" :style="{ background: bucket.color }"></div>
                <div>
                  <span>{{ bucket.label }}</span>
                  <strong>{{ formatExactRupiah(bucket.saldo) }}</strong>
                  <small>{{ formatExactNumber(bucket.noa) }} rekening</small>
                </div>
              </div>
            </div>

            <VDataTable
              v-if="savingFeature === 'nominatif'"
              :headers="savingHeaders"
              :items="savingData"
              :loading="loading"
              class="fin-vtable funding-table"
              density="comfortable"
              :items-per-page="-1"
              hide-default-footer
              hover
            >
              <template #item.saldo="{ item }"><span class="funding-money">{{ formatExactRupiah(item.saldo) }}</span></template>
              <template #item.saldo_rata="{ item }">{{ formatExactRupiah(item.saldo_rata) }}</template>
              <template #item.nisbah="{ item }">{{ formatExactNumber(item.nisbah) }}%</template>
              <template #item.stsrec="{ item }"><span class="fin-pill fin-pill--success">{{ statusLabel(item.stsrec) }}</span></template>
            </VDataTable>

            <div v-if="savingFeature === 'nominatif'" class="funding-cursor-footer">
              <div>
                Menampilkan data dengan server-side cursor dari CBS.
                <strong>{{ formatExactNumber(savingNominativeMeta.per_page) }}</strong> rekening per halaman.
              </div>
              <div class="funding-cursor-footer__actions">
                <VBtn size="small" variant="tonal" prepend-icon="ri-arrow-left-s-line" :disabled="!savingHasPrevPage || loading" @click="loadPrevSavingPage">
                  Sebelumnya
                </VBtn>
                <VBtn size="small" color="primary" variant="flat" append-icon="ri-arrow-right-s-line" :disabled="!savingHasNextPage || loading" @click="loadNextSavingPage">
                  Berikutnya
                </VBtn>
              </div>
            </div>

            <VDataTable
              v-if="savingFeature === 'rekapitulasi'"
              :headers="rekapHeaders"
              :items="savingRekap"
              :loading="loading"
              class="fin-vtable funding-table"
              density="comfortable"
              :items-per-page="15"
              hover
            >
              <template #item.principal="{ item }"><span class="funding-money">{{ formatPrincipal(item) }}</span></template>
              <template #item.noa="{ item }">{{ formatExactNumber(item.noa) }}</template>
              <template #item.saldo_rata="{ item }">{{ formatExactRupiah(item.saldo_rata) }}</template>
              <template #item.bagi_hasil_bayar="{ item }">{{ formatExactRupiah(item.bagi_hasil_bayar) }}</template>
              <template #item.tax="{ item }">{{ formatExactRupiah(item.pajak_bayar ?? item.pajak) }}</template>
              <template #body.append>
                <tr class="funding-total-row">
                  <td colspan="2">TOTAL REKAP TABUNGAN</td>
                  <td class="text-end">{{ formatExactNumber(savingRekapTotals.noa) }}</td>
                  <td class="text-end">{{ formatExactRupiah(savingRekapTotals.saldo) }}</td>
                  <td class="text-end">{{ formatExactRupiah(savingRekapTotals.saldoRata) }}</td>
                  <td class="text-end">{{ formatExactRupiah(savingRekapTotals.baghas) }}</td>
                  <td class="text-end">{{ formatExactRupiah(savingRekapTotals.pajak) }}</td>
                </tr>
              </template>
            </VDataTable>

            <VDataTable
              v-if="savingFeature === 'dormant'"
              :headers="savingDormantHeaders"
              :items="savingDormant"
              :loading="loading"
              class="fin-vtable funding-table"
              density="comfortable"
              :items-per-page="-1"
              hide-default-footer
              hover
            >
              <template #item.tgltrans="{ item }">{{ formatDate(item.tgltrans) }}</template>
              <template #item.hari_pasif="{ item }"><span class="funding-age">{{ formatExactNumber(item.hari_pasif) }} hari</span></template>
              <template #item.saldo="{ item }"><span class="funding-money">{{ formatExactRupiah(item.saldo) }}</span></template>
              <template #item.saldo_rata="{ item }">{{ formatExactRupiah(item.saldo_rata) }}</template>
            </VDataTable>

            <div v-if="savingFeature === 'dormant'" class="funding-cursor-footer">
              <div>
                Menampilkan rekening pasif dengan server-side cursor dari CBS.
                <strong>{{ formatExactNumber(savingDormantMeta.per_page) }}</strong> rekening per halaman.
              </div>
              <div class="funding-cursor-footer__actions">
                <VBtn size="small" variant="tonal" prepend-icon="ri-arrow-left-s-line" :disabled="!savingDormantHasPrevPage || loading" @click="loadPrevDormantPage">
                  Sebelumnya
                </VBtn>
                <VBtn size="small" color="primary" variant="flat" append-icon="ri-arrow-right-s-line" :disabled="!savingDormantHasNextPage || loading" @click="loadNextDormantPage">
                  Berikutnya
                </VBtn>
              </div>
            </div>
          </div>
        </div>
      </section>

      <section v-if="activeDomain === 'deposito'" class="funding-section">
        <div class="content-card">
          <div class="content-card__accent-top" style="background:#7c3aed"></div>
          <div class="content-card__header funding-card-header">
            <div>
              <div class="content-card__title">{{ depositPageTitle }}</div>
              <div class="content-card__subtitle">{{ depositPageSubtitle }}</div>
            </div>
          </div>
          <div class="content-card__body">
            <div v-if="depositFeature === 'nominatif'" class="funding-toolbar">
              <VTextField
                v-model="filters.depositSearch"
                prepend-inner-icon="ri-search-line"
                label="Cari deposito / bilyet / CIF / nama"
                density="compact"
                variant="outlined"
                hide-details
                clearable
                @keyup.enter="loadDepositNominative({ reset: true })"
                @click:clear="loadDepositNominative({ reset: true })"
              />
              <VSelect
                v-model="filters.depositCabang"
                :items="depositCabangOptions"
                prepend-inner-icon="ri-building-2-line"
                label="Cabang"
                density="compact"
                variant="outlined"
                hide-details
              />
              <VSelect
                v-model="filters.depositAo"
                :items="depositAoOptions"
                prepend-inner-icon="ri-user-star-line"
                label="Account Officer"
                density="compact"
                variant="outlined"
                hide-details
              />
              <VBtn color="primary" variant="flat" prepend-icon="ri-search-line" @click="loadDepositNominative({ reset: true })">Cari</VBtn>
              <VBtn
                variant="tonal"
                color="secondary"
                prepend-icon="ri-refresh-line"
                @click="filters.depositSearch = ''; filters.depositCabang = ''; filters.depositAo = ''; loadDepositNominative({ reset: true })"
              >
                Reset
              </VBtn>
            </div>

            <div v-if="depositFeature === 'nominatif'" class="funding-rekap-insight funding-deposit-insight">
              <div>
                <span>Interpretasi Nominatif Deposito</span>
                <p>{{ depositNominativeInterpretation }}</p>
              </div>
              <div class="funding-rekap-insight__metrics">
                <div>
                  <span>Bilyet Tampil</span>
                  <strong>{{ formatExactNumber(depositData.length) }}</strong>
                </div>
                <div>
                  <span>Nominal Tampil</span>
                  <strong>{{ formatExactRupiah(depositCurrentNominal) }}</strong>
                </div>
                <div>
                  <span>Baghas Bayar</span>
                  <strong>{{ formatExactRupiah(depositCurrentBaghas) }}</strong>
                </div>
                <div>
                  <span>ARO</span>
                  <strong>{{ formatExactNumber(depositAroCount) }} bilyet</strong>
                </div>
              </div>
            </div>

            <div v-if="depositFeature === 'rekapitulasi'" class="funding-toolbar">
              <VSelect
                v-model="depositGroup"
                :items="groupOptions"
                label="Kelompok rekap"
                density="compact"
                variant="outlined"
                hide-details
                style="max-width:260px"
              />
              <VTextField
                v-model="filters.depositRekapSearch"
                prepend-inner-icon="ri-search-line"
                label="Cari deposito / bilyet / CIF / nama"
                density="compact"
                variant="outlined"
                hide-details
                clearable
                @keyup.enter="loadDepositRekap"
                @click:clear="loadDepositRekap"
              />
              <VSelect
                v-model="filters.depositRekapCabang"
                :items="depositCabangOptions"
                prepend-inner-icon="ri-building-2-line"
                label="Cabang"
                density="compact"
                variant="outlined"
                hide-details
              />
              <VSelect
                v-model="filters.depositRekapAo"
                :items="depositAoOptions"
                prepend-inner-icon="ri-user-star-line"
                label="Account Officer"
                density="compact"
                variant="outlined"
                hide-details
              />
              <VBtn color="primary" variant="flat" prepend-icon="ri-filter-3-line" @click="loadDepositRekap">Terapkan</VBtn>
              <VBtn
                variant="tonal"
                color="secondary"
                prepend-icon="ri-refresh-line"
                @click="filters.depositRekapSearch = ''; filters.depositRekapCabang = ''; filters.depositRekapAo = ''; loadDepositRekap()"
              >
                Reset
              </VBtn>
            </div>

            <div v-if="depositFeature === 'rekapitulasi'" class="funding-rekap-insight funding-deposit-insight">
              <div>
                <span>Interpretasi Rekap Deposito</span>
                <p>{{ depositRekapInterpretation }}</p>
              </div>
              <div class="funding-rekap-insight__metrics">
                <div>
                  <span>Kelompok</span>
                  <strong>{{ formatExactNumber(depositRekap.length) }}</strong>
                </div>
                <div>
                  <span>Bilyet</span>
                  <strong>{{ formatExactNumber(depositRekapTotals.noa) }}</strong>
                </div>
                <div>
                  <span>Nominal</span>
                  <strong>{{ formatExactRupiah(depositRekapTotals.nominal) }}</strong>
                </div>
                <div>
                  <span>Eq Rate Tertimbang</span>
                  <strong>{{ formatTruncatedPercentage(depositRekapAvgRate) }}</strong>
                </div>
              </div>
            </div>

            <VDataTable
              v-if="depositFeature === 'nominatif'"
              :headers="depositHeaders"
              :items="depositData"
              :loading="loading"
              class="fin-vtable funding-table"
              density="comfortable"
              :items-per-page="-1"
              hide-default-footer
              hover
            >
              <template #item.nominal="{ item }"><span class="funding-money">{{ formatExactRupiah(item.nominal) }}</span></template>
              <template #item.nisbah="{ item }">{{ formatExactNumber(item.nisbah) }}%</template>
              <template #item.jatuh_tempo="{ item }">{{ formatDate(item.jatuh_tempo) }}</template>
              <template #item.hari_jatuh_tempo="{ item }">{{ item.hari_jatuh_tempo ?? '-' }}</template>
              <template #item.aro="{ item }"><span class="fin-pill" :class="item.aro === 'Y' ? 'fin-pill--success' : 'fin-pill--neutral'">{{ item.aro === 'Y' ? 'ARO' : 'Non ARO' }}</span></template>
            </VDataTable>

            <div v-if="depositFeature === 'nominatif'" class="funding-cursor-footer">
              <div>
                Menampilkan deposito aktif dengan server-side cursor dari CBS.
                <strong>{{ formatExactNumber(depositNominativeMeta.per_page) }}</strong> bilyet per halaman.
              </div>
              <div class="funding-cursor-footer__actions">
                <VBtn size="small" variant="tonal" prepend-icon="ri-arrow-left-s-line" :disabled="!depositHasPrevPage || loading" @click="loadPrevDepositPage">
                  Sebelumnya
                </VBtn>
                <VBtn size="small" color="primary" variant="flat" append-icon="ri-arrow-right-s-line" :disabled="!depositHasNextPage || loading" @click="loadNextDepositPage">
                  Berikutnya
                </VBtn>
              </div>
            </div>

            <VDataTable
              v-if="depositFeature === 'rekapitulasi'"
              :headers="depositRekapHeaders"
              :items="depositRekap"
              :loading="loading"
              class="fin-vtable funding-table"
              density="comfortable"
              :items-per-page="15"
              hover
            >
              <template #item.principal="{ item }"><span class="funding-money">{{ formatPrincipal(item) }}</span></template>
              <template #item.noa="{ item }">{{ formatExactNumber(item.noa) }}</template>
              <template #item.saldo_rata="{ item }">{{ formatExactRupiah(item.saldo_rata) }}</template>
              <template #item.bagi_hasil_bayar="{ item }">{{ formatExactRupiah(item.bagi_hasil_bayar) }}</template>
              <template #item.tax="{ item }">{{ formatExactRupiah(item.pajak_bayar ?? item.pajak) }}</template>
              <template #item.avg_nisbah="{ item }">{{ formatTruncatedPercentage(item.avg_nisbah) }}</template>
              <template #item.avg_equivrate="{ item }">{{ formatTruncatedPercentage(item.avg_equivrate) }}</template>
              <template #body.append>
                <tr class="funding-total-row">
                  <td colspan="2">TOTAL REKAP DEPOSITO</td>
                  <td class="text-end">{{ formatExactNumber(depositRekapTotals.noa) }}</td>
                  <td class="text-end">{{ formatExactRupiah(depositRekapTotals.nominal) }}</td>
                  <td class="text-end">{{ formatExactRupiah(depositRekapTotals.saldoRata) }}</td>
                  <td class="text-end">{{ formatExactRupiah(depositRekapTotals.baghas) }}</td>
                  <td class="text-end">{{ formatExactRupiah(depositRekapTotals.pajak) }}</td>
                  <td class="text-end">{{ formatTruncatedPercentage(depositRekapAvgNisbah) }}</td>
                  <td class="text-end">{{ formatTruncatedPercentage(depositRekapAvgRate) }}</td>
                </tr>
              </template>
            </VDataTable>

            <div v-if="depositFeature === 'jatuh-tempo'" class="funding-toolbar">
              <VTextField
                v-model="filters.depositMaturitySearch"
                prepend-inner-icon="ri-search-line"
                label="Cari deposito / bilyet / CIF / nama"
                density="compact"
                variant="outlined"
                hide-details
                clearable
                @keyup.enter="loadDepositJatuhTempo({ reset: true })"
                @click:clear="loadDepositJatuhTempo({ reset: true })"
              />
              <VSelect
                v-model="filters.depositMaturityCabang"
                :items="depositCabangOptions"
                prepend-inner-icon="ri-building-2-line"
                label="Cabang"
                density="compact"
                variant="outlined"
                hide-details
              />
              <VSelect
                v-model="filters.depositMaturityAo"
                :items="depositAoOptions"
                prepend-inner-icon="ri-user-star-line"
                label="Account Officer"
                density="compact"
                variant="outlined"
                hide-details
              />
              <VBtn color="primary" variant="flat" prepend-icon="ri-filter-3-line" @click="loadDepositJatuhTempo({ reset: true })">Terapkan</VBtn>
              <VBtn
                variant="tonal"
                color="secondary"
                prepend-icon="ri-refresh-line"
                @click="filters.depositMaturitySearch = ''; filters.depositMaturityCabang = ''; filters.depositMaturityAo = ''; loadDepositJatuhTempo({ reset: true })"
              >
                Reset
              </VBtn>
            </div>

            <div v-if="depositFeature === 'jatuh-tempo'" class="funding-rekap-insight funding-maturity-insight">
              <div>
                <span>Interpretasi Jatuh Tempo</span>
                <p>{{ depositMaturityInterpretation }}</p>
              </div>
              <div class="funding-rekap-insight__metrics">
                <div>
                  <span>Bilyet JT</span>
                  <strong>{{ formatExactNumber(depositMaturityTotals.bilyet) }}</strong>
                </div>
                <div>
                  <span>Nominal JT</span>
                  <strong>{{ formatExactRupiah(depositMaturityTotals.nominal) }}</strong>
                </div>
                <div>
                  <span>ARO</span>
                  <strong>{{ formatTruncatedPercentage(depositMaturityAroRatio) }}</strong>
                </div>
                <div>
                  <span>Non ARO</span>
                  <strong>{{ formatExactNumber(depositMaturityTotals.nonAro) }} bilyet</strong>
                </div>
              </div>
            </div>

            <div v-if="depositFeature === 'jatuh-tempo'" class="funding-dormant-buckets funding-maturity-buckets">
              <div v-for="bucket in depositMaturityBuckets" :key="bucket.label" class="funding-dormant-bucket">
                <div class="funding-dormant-bucket__dot" :style="{ background: bucket.color }"></div>
                <div>
                  <span>{{ bucket.label }}</span>
                  <strong>{{ formatExactRupiah(bucket.nominal) }}</strong>
                  <small>{{ formatExactNumber(bucket.bilyet) }} bilyet</small>
                </div>
              </div>
            </div>

            <VDataTable
              v-if="depositFeature === 'jatuh-tempo'"
              :headers="depositHeaders"
              :items="depositJatuhTempo"
              :loading="loading"
              class="fin-vtable funding-table"
              density="comfortable"
              :items-per-page="-1"
              hide-default-footer
              hover
            >
              <template #item.nominal="{ item }"><span class="funding-money funding-money--warning">{{ formatExactRupiah(item.nominal) }}</span></template>
              <template #item.nisbah="{ item }">{{ formatExactNumber(item.nisbah) }}%</template>
              <template #item.jatuh_tempo="{ item }">{{ formatDate(item.jatuh_tempo) }}</template>
              <template #item.hari_jatuh_tempo="{ item }"><span class="funding-age">{{ formatExactNumber(item.hari_jatuh_tempo) }} hari</span></template>
              <template #item.aro="{ item }"><span class="fin-pill" :class="item.aro === 'Y' ? 'fin-pill--success' : 'fin-pill--warning'">{{ item.aro === 'Y' ? 'ARO' : 'Non ARO' }}</span></template>
            </VDataTable>

            <div v-if="depositFeature === 'jatuh-tempo'" class="funding-cursor-footer">
              <div>
                Menampilkan deposito jatuh tempo bulan berjalan dengan server-side cursor CBS.
                <strong>{{ formatExactNumber(depositMaturityMeta.per_page) }}</strong> bilyet per halaman.
              </div>
              <div class="funding-cursor-footer__actions">
                <VBtn size="small" variant="tonal" prepend-icon="ri-arrow-left-s-line" :disabled="!depositMaturityHasPrevPage || loading" @click="loadPrevMaturityPage">
                  Sebelumnya
                </VBtn>
                <VBtn size="small" color="primary" variant="flat" append-icon="ri-arrow-right-s-line" :disabled="!depositMaturityHasNextPage || loading" @click="loadNextMaturityPage">
                  Berikutnya
                </VBtn>
              </div>
            </div>
          </div>
        </div>
      </section>

      <section v-if="activeDomain === 'perkembangan'" class="funding-section">
        <div class="funding-rekap-insight funding-analytics-insight">
          <div>
            <span>Interpretasi Perkembangan</span>
            <p>{{ perkembanganInterpretation }}</p>
          </div>
          <div class="funding-rekap-insight__metrics">
            <div>
              <span>Periode</span>
              <strong>{{ perkembanganSummary.periode }}</strong>
            </div>
            <div>
              <span>Total DPK</span>
              <strong>{{ formatExactRupiah(perkembanganSummary.total_latest) }}</strong>
            </div>
            <div>
              <span>Growth DPK</span>
              <strong>{{ formatTruncatedPercentage(perkembanganSummary.total_growth) }}</strong>
            </div>
            <div>
              <span>Total NOA</span>
              <strong>{{ formatExactNumber(perkembanganSummary.total_noa) }}</strong>
            </div>
          </div>
        </div>

        <div class="funding-grid funding-grid--two">
          <div class="content-card">
            <div class="content-card__accent-top" style="background:#0284c7"></div>
            <div class="content-card__header">
              <div>
                <div class="content-card__title">Perkembangan Tabungan</div>
                <div class="content-card__subtitle">Saldo akhir bulanan dari TOFTABEOM dan live TOFTABB untuk bulan berjalan.</div>
              </div>
            </div>
            <div class="content-card__body">
              <VDataTable :headers="perkembanganHeaders" :items="fundingPerkembangan.saving" :loading="loading" class="fin-vtable funding-table" density="comfortable" :items-per-page="12" hover>
                <template #item.nominal="{ item }"><span class="funding-money">{{ formatExactRupiah(item.nominal) }}</span></template>
                <template #item.noa="{ item }">{{ formatExactNumber(item.noa) }}</template>
                <template #item.mom_growth_percent="{ item }">{{ formatTruncatedPercentage(item.mom_growth_percent) }}</template>
              </VDataTable>
            </div>
          </div>
          <div class="content-card">
            <div class="content-card__accent-top" style="background:#7c3aed"></div>
            <div class="content-card__header">
              <div>
                <div class="content-card__title">Perkembangan Deposito</div>
                <div class="content-card__subtitle">Nominal deposito bulanan dari TOFDEPEOM dan live TOFDEP untuk bulan berjalan.</div>
              </div>
            </div>
            <div class="content-card__body">
              <VDataTable :headers="perkembanganHeaders" :items="fundingPerkembangan.deposit" :loading="loading" class="fin-vtable funding-table" density="comfortable" :items-per-page="12" hover>
                <template #item.nominal="{ item }"><span class="funding-money">{{ formatExactRupiah(item.nominal) }}</span></template>
                <template #item.noa="{ item }">{{ formatExactNumber(item.noa) }}</template>
                <template #item.mom_growth_percent="{ item }">{{ formatTruncatedPercentage(item.mom_growth_percent) }}</template>
              </VDataTable>
            </div>
          </div>
        </div>
      </section>

      <section v-if="activeDomain === 'target'" class="funding-section">
        <div class="funding-rekap-insight funding-target-insight">
          <div>
            <span>Interpretasi Target</span>
            <p>{{ targetInterpretation }}</p>
          </div>
          <div class="funding-rekap-insight__metrics">
            <div>
              <span>Periode</span>
              <strong>{{ targetSummary.periode }}</strong>
            </div>
            <div>
              <span>Target</span>
              <strong>{{ formatExactRupiah(targetSummary.target) }}</strong>
            </div>
            <div>
              <span>Realisasi</span>
              <strong>{{ formatExactRupiah(targetSummary.actual) }}</strong>
            </div>
            <div>
              <span>Achievement</span>
              <strong>{{ formatTruncatedPercentage(targetSummary.achievement) }}</strong>
            </div>
          </div>
        </div>

        <div class="funding-grid funding-grid--two">
          <div class="content-card">
            <div class="content-card__accent-top" style="background:#0284c7"></div>
            <div class="content-card__header">
              <div>
                <div class="content-card__title">Target Tabungan</div>
                <div class="content-card__subtitle">Target `TARGETAO.tab` dibandingkan realisasi tabungan bulanan.</div>
              </div>
            </div>
            <div class="content-card__body">
              <VDataTable :headers="targetHeaders" :items="fundingTarget.saving" :loading="loading" class="fin-vtable funding-table" density="comfortable" :items-per-page="12" hover>
                <template #item.target="{ item }">{{ formatExactRupiah(item.target) }}</template>
                <template #item.actual="{ item }"><span class="funding-money">{{ formatExactRupiah(item.actual) }}</span></template>
                <template #item.gap="{ item }"><span :class="toFiniteNumber(item.gap) >= 0 ? 'funding-money' : 'funding-money--warning'">{{ formatExactRupiah(item.gap) }}</span></template>
                <template #item.achievement_percent="{ item }">{{ formatTruncatedPercentage(item.achievement_percent) }}</template>
              </VDataTable>
            </div>
          </div>
          <div class="content-card">
            <div class="content-card__accent-top" style="background:#7c3aed"></div>
            <div class="content-card__header">
              <div>
                <div class="content-card__title">Target Deposito</div>
                <div class="content-card__subtitle">Target `TARGETAO.dep` dibandingkan realisasi deposito bulanan.</div>
              </div>
            </div>
            <div class="content-card__body">
              <VDataTable :headers="targetHeaders" :items="fundingTarget.deposit" :loading="loading" class="fin-vtable funding-table" density="comfortable" :items-per-page="12" hover>
                <template #item.target="{ item }">{{ formatExactRupiah(item.target) }}</template>
                <template #item.actual="{ item }"><span class="funding-money">{{ formatExactRupiah(item.actual) }}</span></template>
                <template #item.gap="{ item }"><span :class="toFiniteNumber(item.gap) >= 0 ? 'funding-money' : 'funding-money--warning'">{{ formatExactRupiah(item.gap) }}</span></template>
                <template #item.achievement_percent="{ item }">{{ formatTruncatedPercentage(item.achievement_percent) }}</template>
              </VDataTable>
            </div>
          </div>
        </div>
      </section>

      <section v-if="activeDomain === 'mutasi'" class="funding-section">
        <div class="funding-rekap-insight funding-mutasi-insight">
          <div>
            <span>Interpretasi Mutasi</span>
            <p>{{ mutasiInterpretation }}</p>
          </div>
          <div class="funding-rekap-insight__metrics">
            <div>
              <span>Periode</span>
              <strong>{{ mutasiSummary.periode }}</strong>
            </div>
            <div>
              <span>Netto Total</span>
              <strong>{{ formatExactRupiah(mutasiSummary.total_netto) }}</strong>
            </div>
            <div>
              <span>Netto Tabungan</span>
              <strong>{{ formatExactRupiah(mutasiSummary.saving_netto) }}</strong>
            </div>
            <div>
              <span>Netto Deposito</span>
              <strong>{{ formatExactRupiah(mutasiSummary.deposit_netto) }}</strong>
            </div>
          </div>
        </div>

        <div class="funding-grid funding-grid--two">
          <div class="content-card">
            <div class="content-card__accent-top" style="background:#0284c7"></div>
            <div class="content-card__header">
              <div>
                <div class="content-card__title">Mutasi Tabungan</div>
                <div class="content-card__subtitle">Debet, kredit, dan netto transaksi tabungan dari H_GLTRN.</div>
              </div>
            </div>
            <div class="content-card__body">
              <VDataTable :headers="savingMutationHeaders" :items="fundingMutasi.saving" :loading="loading" class="fin-vtable funding-table" density="comfortable" :items-per-page="12" hover>
                <template #item.debet="{ item }">{{ formatExactRupiah(item.debet) }}</template>
                <template #item.kredit="{ item }">{{ formatExactRupiah(item.kredit) }}</template>
                <template #item.netto="{ item }"><span :class="toFiniteNumber(item.netto) >= 0 ? 'funding-money' : 'funding-money--warning'">{{ formatExactRupiah(item.netto) }}</span></template>
                <template #item.transaksi="{ item }">{{ formatExactNumber(item.transaksi) }}</template>
              </VDataTable>
            </div>
          </div>
          <div class="content-card">
            <div class="content-card__accent-top" style="background:#7c3aed"></div>
            <div class="content-card__header">
              <div>
                <div class="content-card__title">Mutasi Deposito</div>
                <div class="content-card__subtitle">Deposito masuk, cair, dan netto dari TOFDEP/TOFDEPDEL.</div>
              </div>
            </div>
            <div class="content-card__body">
              <VDataTable :headers="depositMutationHeaders" :items="fundingMutasi.deposit" :loading="loading" class="fin-vtable funding-table" density="comfortable" :items-per-page="12" hover>
                <template #item.masuk="{ item }">{{ formatExactRupiah(item.masuk) }}</template>
                <template #item.cair="{ item }">{{ formatExactRupiah(item.cair) }}</template>
                <template #item.netto="{ item }"><span :class="toFiniteNumber(item.netto) >= 0 ? 'funding-money' : 'funding-money--warning'">{{ formatExactRupiah(item.netto) }}</span></template>
                <template #item.transaksi="{ item }">{{ formatExactNumber(item.transaksi) }}</template>
              </VDataTable>
            </div>
          </div>
        </div>
      </section>

      <section v-if="activeDomain === 'concentration'" class="funding-section">
        <section class="funding-risk-hero mb-4">
          <div>
            <div class="funding-risk-hero__eyebrow">Top Depositor Concentration Detail</div>
            <h2>Nasabah Terbesar Funding</h2>
            <p>{{ concentrationInterpretation }}</p>
          </div>
          <div class="funding-risk-hero__meta">
            <span>Total DPK</span>
            <strong>{{ formatExactRupiah(concentrationSummary.total_dpk) }}</strong>
            <small>Top 100 deposan + bucket nominal</small>
          </div>
        </section>

        <div class="funding-risk-cards mb-4">
          <div class="funding-risk-card">
            <div class="funding-risk-card__icon" style="background:#0d94881f;color:#0d9488"><VIcon icon="ri-user-star-line" /></div>
            <div><span>Top 1</span><strong>{{ formatExactRupiah(concentrationSummary.top1_nominal) }}</strong><small>{{ formatTruncatedPercentage(concentrationSummary.top1_ratio) }} dari DPK</small></div>
          </div>
          <div class="funding-risk-card">
            <div class="funding-risk-card__icon" style="background:#7c3aed1f;color:#7c3aed"><VIcon icon="ri-team-line" /></div>
            <div><span>Top 5</span><strong>{{ formatExactRupiah(concentrationSummary.top5_nominal) }}</strong><small>{{ formatTruncatedPercentage(concentrationSummary.top5_ratio) }} dari DPK</small></div>
          </div>
          <div class="funding-risk-card">
            <div class="funding-risk-card__icon" style="background:#0284c71f;color:#0284c7"><VIcon icon="ri-bar-chart-grouped-line" /></div>
            <div><span>Top 10</span><strong>{{ formatExactRupiah(concentrationSummary.top10_nominal) }}</strong><small>{{ formatTruncatedPercentage(concentrationSummary.top10_ratio) }} dari DPK</small></div>
          </div>
          <div class="funding-risk-card">
            <div class="funding-risk-card__icon" style="background:#f59e0b1f;color:#f59e0b"><VIcon icon="ri-radar-line" /></div>
            <div><span>Top 25</span><strong>{{ formatExactRupiah(concentrationSummary.top25_nominal) }}</strong><small>{{ formatTruncatedPercentage(concentrationSummary.top25_ratio) }} dari DPK</small></div>
          </div>
        </div>

        <div class="funding-band-strip mb-4">
          <div v-for="band in concentrationBands" :key="band.band" class="funding-band-strip__item">
            <div class="funding-band-strip__top">
              <span>{{ band.band }}</span>
              <strong>{{ formatTruncatedPercentage(band.share_percent) }}</strong>
            </div>
            <div class="funding-band-strip__bar">
              <i :style="{ width: `${Math.min((toFiniteNumber(band.total_dpk) / concentrationBandMax) * 100, 100)}%` }"></i>
            </div>
            <small>{{ formatExactNumber(band.depositor_count) }} deposan · {{ formatExactRupiah(band.total_dpk) }}</small>
          </div>
        </div>

        <div class="funding-grid">
          <div class="content-card">
            <div class="content-card__accent-top" style="background:#0d9488"></div>
            <div class="content-card__header">
              <div>
                <div class="content-card__title">Band Konsentrasi Nominal</div>
                <div class="content-card__subtitle">Distribusi deposan berdasarkan bucket nominal DPK untuk membaca kepadatan konsentrasi.</div>
              </div>
            </div>
            <div class="content-card__body">
              <VDataTable :headers="concentrationBandHeaders" :items="concentrationBands" :loading="loading" class="fin-vtable funding-table" density="comfortable" :items-per-page="10" hover>
                <template #item.depositor_count="{ item }">{{ formatExactNumber(item.depositor_count) }}</template>
                <template #item.noa_tabungan="{ item }">{{ formatExactNumber(item.noa_tabungan) }}</template>
                <template #item.noa_deposito="{ item }">{{ formatExactNumber(item.noa_deposito) }}</template>
                <template #item.tabungan="{ item }">{{ formatExactRupiah(item.tabungan) }}</template>
                <template #item.deposito="{ item }">{{ formatExactRupiah(item.deposito) }}</template>
                <template #item.total_dpk="{ item }"><span class="funding-money">{{ formatExactRupiah(item.total_dpk) }}</span></template>
                <template #item.share_percent="{ item }">{{ formatTruncatedPercentage(item.share_percent) }}</template>
              </VDataTable>
            </div>
          </div>

          <div class="content-card">
            <div class="content-card__accent-top" style="background:#7c3aed"></div>
            <div class="content-card__header">
              <div>
                <div class="content-card__title">Top 100 Nasabah DPK</div>
                <div class="content-card__subtitle">Gabungan saldo tabungan dan deposito aktif per CIF, diurutkan dari DPK terbesar.</div>
              </div>
            </div>
            <div class="content-card__body">
              <VDataTable :headers="topDepositorHeaders" :items="concentrationTopDepositors" :loading="loading" class="fin-vtable funding-table" density="comfortable" :items-per-page="15" hover>
                <template #item.ranking="{ item }"><span class="funding-rank">{{ item.ranking }}</span></template>
                <template #item.tabungan="{ item }">{{ formatExactRupiah(item.tabungan) }}</template>
                <template #item.deposito="{ item }">{{ formatExactRupiah(item.deposito) }}</template>
                <template #item.total_dpk="{ item }"><span class="funding-money">{{ formatExactRupiah(item.total_dpk) }}</span></template>
                <template #item.share_percent="{ item }">{{ formatTruncatedPercentage(item.share_percent) }}</template>
                <template #item.noa="{ item }">{{ depositorNoa(item) }}</template>
              </VDataTable>
            </div>
          </div>
        </div>
      </section>

      <section v-if="activeDomain === 'risk'" class="funding-section">
        <section class="funding-risk-hero mb-4">
          <div>
            <div class="funding-risk-hero__eyebrow">Funding Concentration & Liquidity Watch</div>
            <h2>Risk Funding</h2>
            <p>{{ riskInterpretation }}</p>
          </div>
          <div class="funding-risk-hero__meta">
            <span>Total DPK</span>
            <strong>{{ formatExactRupiah(riskSummary.total_dpk) }}</strong>
            <small>Realtime CBS mirror</small>
          </div>
        </section>

        <div class="funding-risk-cards mb-4">
          <div v-for="card in riskCards" :key="card.label" class="funding-risk-card">
            <div class="funding-risk-card__icon" :style="{ background: `${card.color}1f`, color: card.color }">
              <VIcon :icon="card.icon" />
            </div>
            <div>
              <span>{{ card.label }}</span>
              <strong>{{ card.value }}</strong>
              <small>{{ card.sub }}</small>
            </div>
          </div>
        </div>

        <div class="funding-risk-pressure mb-4">
          <div>
            <span>Tekanan Maturity ≤30 Hari</span>
            <strong>{{ formatExactRupiah(maturityPressure) }}</strong>
            <small>Gabungan deposito lewat jatuh tempo sampai 30 hari.</small>
          </div>
          <div>
            <span>Saldo Dormant/Pasif</span>
            <strong>{{ formatExactRupiah(dormantPressure) }}</strong>
            <small>Rekening tabungan dormant yang perlu reaktivasi.</small>
          </div>
          <div>
            <span>Produk Terbesar</span>
            <strong>{{ productMix[0]?.produk || '-' }}</strong>
            <small>{{ formatTruncatedPercentage(productMix[0]?.share_percent) }} dari DPK produk.</small>
          </div>
        </div>

        <div class="funding-grid">
          <div class="content-card">
            <div class="content-card__accent-top" style="background:#0d9488"></div>
            <div class="content-card__header">
              <div>
                <div class="content-card__title">Top Depositor / Konsentrasi DPK per CIF</div>
                <div class="content-card__subtitle">
                  Gabungan saldo tabungan aktif dan deposito aktif per CIF. Dipakai untuk membaca concentration risk dana pihak ketiga.
                </div>
              </div>
            </div>
            <div class="content-card__body">
              <VDataTable
                :headers="topDepositorHeaders"
                :items="topDepositors"
                :loading="loading"
                class="fin-vtable funding-table"
                density="comfortable"
                :items-per-page="10"
                hover
              >
                <template #item.ranking="{ item }">
                  <span class="funding-rank">{{ item.ranking }}</span>
                </template>
                <template #item.tabungan="{ item }">{{ formatExactRupiah(item.tabungan) }}</template>
                <template #item.deposito="{ item }">{{ formatExactRupiah(item.deposito) }}</template>
                <template #item.total_dpk="{ item }"><span class="funding-money">{{ formatExactRupiah(item.total_dpk) }}</span></template>
                <template #item.share_percent="{ item }">{{ formatTruncatedPercentage(item.share_percent) }}</template>
                <template #item.noa="{ item }">{{ depositorNoa(item) }}</template>
              </VDataTable>
            </div>
          </div>

          <div class="funding-grid funding-grid--two">
            <div class="content-card">
              <div class="content-card__accent-top" style="background:#f59e0b"></div>
              <div class="content-card__header">
                <div>
                  <div class="content-card__title">Maturity Bucket Deposito</div>
                  <div class="content-card__subtitle">Bucket jatuh tempo deposito aktif untuk membaca tekanan likuiditas dan kebutuhan rollover.</div>
                </div>
              </div>
              <div class="content-card__body">
                <VDataTable
                  :headers="maturityHeaders"
                  :items="maturityBuckets"
                  :loading="loading"
                  class="fin-vtable funding-table"
                  density="comfortable"
                  :items-per-page="10"
                  hover
                >
                  <template #item.noa="{ item }">{{ formatExactNumber(item.noa) }}</template>
                  <template #item.nominal="{ item }"><span class="funding-money funding-money--warning">{{ formatExactRupiah(item.nominal) }}</span></template>
                  <template #item.aro_count="{ item }">{{ formatExactNumber(item.aro_count) }}</template>
                  <template #item.non_aro_count="{ item }">{{ formatExactNumber(item.non_aro_count) }}</template>
                </VDataTable>
              </div>
            </div>

            <div class="content-card">
              <div class="content-card__accent-top" style="background:#0284c7"></div>
              <div class="content-card__header">
                <div>
                  <div class="content-card__title">Dormant / Pasif Tabungan</div>
                  <div class="content-card__subtitle">Pengelompokan rekening tabungan berdasarkan transaksi terakhir untuk agenda reaktivasi dana murah.</div>
                </div>
              </div>
              <div class="content-card__body">
                <VDataTable
                  :headers="dormantBucketHeaders"
                  :items="dormantBuckets"
                  :loading="loading"
                  class="fin-vtable funding-table"
                  density="comfortable"
                  :items-per-page="10"
                  hover
                >
                  <template #item.noa="{ item }">{{ formatExactNumber(item.noa) }}</template>
                  <template #item.saldo="{ item }"><span class="funding-money">{{ formatExactRupiah(item.saldo) }}</span></template>
                  <template #item.saldo_rata="{ item }">{{ formatExactRupiah(item.saldo_rata) }}</template>
                </VDataTable>
              </div>
            </div>
          </div>

          <div class="content-card">
            <div class="content-card__accent-top" style="background:#7c3aed"></div>
            <div class="content-card__header">
              <div>
                <div class="content-card__title">Product Mix Risk</div>
                <div class="content-card__subtitle">
                  Produk terbesar berdasarkan nominal DPK untuk membaca ketergantungan pada produk, pricing, dan stabilitas dana.
                </div>
              </div>
            </div>
            <div class="content-card__body">
              <VDataTable
                :headers="productMixHeaders"
                :items="productMix"
                :loading="loading"
                class="fin-vtable funding-table"
                density="comfortable"
                :items-per-page="10"
                hover
              >
                <template #item.domain="{ item }">
                  <span class="fin-pill" :class="item.domain === 'Deposito' ? 'fin-pill--info' : 'fin-pill--success'">{{ item.domain }}</span>
                </template>
                <template #item.noa="{ item }">{{ formatExactNumber(item.noa) }}</template>
                <template #item.nominal="{ item }"><span class="funding-money">{{ formatExactRupiah(item.nominal) }}</span></template>
                <template #item.share_percent="{ item }">{{ formatTruncatedPercentage(item.share_percent) }}</template>
              </VDataTable>
            </div>
          </div>
        </div>
      </section>

      <section v-if="activeDomain === 'baghas'" class="funding-section">
        <section class="funding-risk-hero funding-baghas-hero mb-4">
          <div>
            <div class="funding-risk-hero__eyebrow">Profit Sharing & Tax Watch</div>
            <h2>Bagi Hasil & Pajak Funding</h2>
            <p>{{ baghasInterpretation }}</p>
          </div>
          <div class="funding-risk-hero__meta">
            <span>Total Baghas Dibayar</span>
            <strong>{{ formatExactRupiah(baghasSummary.total_baghas_bayar) }}</strong>
            <small>{{ formatExactNumber(baghasSummary.total_noa) }} rekening/bilyet</small>
          </div>
        </section>

        <div class="funding-risk-cards mb-4">
          <div v-for="card in baghasCards" :key="card.label" class="funding-risk-card">
            <div class="funding-risk-card__icon" :style="{ background: `${card.color}1f`, color: card.color }">
              <VIcon :icon="card.icon" />
            </div>
            <div>
              <span>{{ card.label }}</span>
              <strong>{{ card.value }}</strong>
              <small>{{ card.sub }}</small>
            </div>
          </div>
        </div>

        <div class="funding-baghas-control mb-4">
          <div class="funding-baghas-control__main">
            <span>Interpretasi Operasional</span>
            <strong>{{ baghasCostInterpretation }}</strong>
            <small>
              Fokus monitoring pada biaya dana efektif, porsi deposito terhadap total bagi hasil, pajak, dan nasabah penyumbang baghas terbesar.
            </small>
          </div>
          <div class="funding-baghas-control__mix">
            <div v-for="item in baghasFundingMix" :key="item.label" class="funding-baghas-mix-row">
              <div>
                <span>{{ item.label }}</span>
                <strong>{{ formatTruncatedPercentage(item.value) }}</strong>
              </div>
              <div class="funding-baghas-mix-row__bar">
                <i :style="{ inlineSize: `${Math.min(Math.max(item.value, 0), 100)}%`, background: item.color }"></i>
              </div>
              <small>{{ formatExactRupiah(item.amount) }}</small>
            </div>
          </div>
        </div>

        <div class="funding-baghas-drivers mb-4">
          <div v-for="driver in baghasDriverCards" :key="driver.label" class="funding-baghas-driver">
            <div class="funding-baghas-driver__accent" :style="{ background: driver.color }"></div>
            <span>{{ driver.label }}</span>
            <strong>{{ driver.title }}</strong>
            <b>{{ driver.value }}</b>
            <small>{{ driver.sub }}</small>
          </div>
        </div>

        <div class="funding-grid funding-grid--two mb-4">
          <div class="content-card">
            <div class="content-card__accent-top" style="background:#0284c7"></div>
            <div class="content-card__header">
              <div>
                <div class="content-card__title">Ringkasan Baghas Tabungan</div>
                <div class="content-card__subtitle">Sumber: TOFTABB aktif, field bhhtg, bhbayar, taxbayar.</div>
              </div>
            </div>
            <div class="content-card__body funding-stat-list">
              <div><span>Saldo</span><strong>{{ formatExactRupiah(savingBaghasSummary.saldo) }}</strong></div>
              <div><span>Bagi hasil hitung</span><strong>{{ formatExactRupiah(savingBaghasSummary.baghas_hitung) }}</strong></div>
              <div><span>Bagi hasil bayar</span><strong>{{ formatExactRupiah(savingBaghasSummary.baghas_bayar) }}</strong></div>
              <div><span>Pajak bayar</span><strong>{{ formatExactRupiah(savingBaghasSummary.pajak) }}</strong></div>
              <div><span>Avg nisbah / rate</span><strong>{{ formatTruncatedPercentage(savingBaghasSummary.avg_nisbah) }} / {{ formatTruncatedPercentage(savingBaghasSummary.avg_rate) }}</strong></div>
            </div>
          </div>

          <div class="content-card">
            <div class="content-card__accent-top" style="background:#7c3aed"></div>
            <div class="content-card__header">
              <div>
                <div class="content-card__title">Ringkasan Baghas Deposito</div>
                <div class="content-card__subtitle">Sumber: TOFDEP aktif, field bnghtg, bngbayar, tax.</div>
              </div>
            </div>
            <div class="content-card__body funding-stat-list">
              <div><span>Saldo</span><strong>{{ formatExactRupiah(depositBaghasSummary.saldo) }}</strong></div>
              <div><span>Bagi hasil hitung</span><strong>{{ formatExactRupiah(depositBaghasSummary.baghas_hitung) }}</strong></div>
              <div><span>Bagi hasil bayar</span><strong>{{ formatExactRupiah(depositBaghasSummary.baghas_bayar) }}</strong></div>
              <div><span>Pajak</span><strong>{{ formatExactRupiah(depositBaghasSummary.pajak) }}</strong></div>
              <div><span>Avg nisbah / eq rate</span><strong>{{ formatTruncatedPercentage(depositBaghasSummary.avg_nisbah) }} / {{ formatTruncatedPercentage(depositBaghasSummary.avg_rate) }}</strong></div>
            </div>
          </div>
        </div>

        <div class="funding-grid">
          <div class="content-card">
            <div class="content-card__accent-top" style="background:#0d9488"></div>
            <div class="content-card__header">
              <div>
                <div class="content-card__title">Bagi Hasil per Produk Tabungan</div>
                <div class="content-card__subtitle">Membaca produk tabungan yang paling besar membentuk bagi hasil dan pajak.</div>
              </div>
            </div>
            <div class="content-card__body">
              <VDataTable
                :headers="baghasProductHeaders"
                :items="savingBaghasByProduct"
                :loading="loading"
                class="fin-vtable funding-table"
                density="comfortable"
                :items-per-page="10"
                hover
              >
                <template #item.domain="{ item }"><span class="fin-pill fin-pill--success">{{ item.domain }}</span></template>
                <template #item.noa="{ item }">{{ formatExactNumber(item.noa) }}</template>
                <template #item.saldo="{ item }">{{ formatExactRupiah(item.saldo) }}</template>
                <template #item.baghas_hitung="{ item }">{{ formatExactRupiah(item.baghas_hitung) }}</template>
                <template #item.baghas_bayar="{ item }"><span class="funding-money">{{ formatExactRupiah(item.baghas_bayar) }}</span></template>
                <template #item.pajak="{ item }">{{ formatExactRupiah(item.pajak) }}</template>
                <template #item.avg_nisbah="{ item }">{{ formatTruncatedPercentage(item.avg_nisbah) }}</template>
                <template #item.avg_rate="{ item }">{{ formatTruncatedPercentage(item.avg_rate) }}</template>
              </VDataTable>
            </div>
          </div>

          <div class="content-card">
            <div class="content-card__accent-top" style="background:#7c3aed"></div>
            <div class="content-card__header">
              <div>
                <div class="content-card__title">Bagi Hasil per Produk Deposito</div>
                <div class="content-card__subtitle">Membaca produk deposito yang paling besar membentuk biaya dana dan pajak.</div>
              </div>
            </div>
            <div class="content-card__body">
              <VDataTable
                :headers="baghasProductHeaders"
                :items="depositBaghasByProduct"
                :loading="loading"
                class="fin-vtable funding-table"
                density="comfortable"
                :items-per-page="10"
                hover
              >
                <template #item.domain="{ item }"><span class="fin-pill fin-pill--info">{{ item.domain }}</span></template>
                <template #item.noa="{ item }">{{ formatExactNumber(item.noa) }}</template>
                <template #item.saldo="{ item }">{{ formatExactRupiah(item.saldo) }}</template>
                <template #item.baghas_hitung="{ item }">{{ formatExactRupiah(item.baghas_hitung) }}</template>
                <template #item.baghas_bayar="{ item }"><span class="funding-money">{{ formatExactRupiah(item.baghas_bayar) }}</span></template>
                <template #item.pajak="{ item }">{{ formatExactRupiah(item.pajak) }}</template>
                <template #item.avg_nisbah="{ item }">{{ formatTruncatedPercentage(item.avg_nisbah) }}</template>
                <template #item.avg_rate="{ item }">{{ formatTruncatedPercentage(item.avg_rate) }}</template>
              </VDataTable>
            </div>
          </div>

          <div class="funding-grid funding-grid--two">
            <div class="content-card">
              <div class="content-card__accent-top" style="background:#f59e0b"></div>
              <div class="content-card__header">
                <div>
                  <div class="content-card__title">Bucket Nisbah Deposito</div>
                  <div class="content-card__subtitle">Distribusi saldo dan bagi hasil deposito berdasarkan rentang nisbah.</div>
                </div>
              </div>
              <div class="content-card__body">
                <VDataTable
                  :headers="nisbahBucketHeaders"
                  :items="depositByNisbah"
                  :loading="loading"
                  class="fin-vtable funding-table"
                  density="comfortable"
                  :items-per-page="10"
                  hover
                >
                  <template #item.noa="{ item }">{{ formatExactNumber(item.noa) }}</template>
                  <template #item.saldo="{ item }">{{ formatExactRupiah(item.saldo) }}</template>
                  <template #item.baghas_bayar="{ item }"><span class="funding-money">{{ formatExactRupiah(item.baghas_bayar) }}</span></template>
                  <template #item.pajak="{ item }">{{ formatExactRupiah(item.pajak) }}</template>
                  <template #item.avg_rate="{ item }">{{ formatTruncatedPercentage(item.avg_rate) }}</template>
                </VDataTable>
              </div>
            </div>

            <div class="content-card">
              <div class="content-card__accent-top" style="background:#dc2626"></div>
              <div class="content-card__header">
                <div>
                  <div class="content-card__title">Top Depositor Berdasarkan Baghas</div>
                  <div class="content-card__subtitle">Nasabah deposito dengan bagi hasil bayar terbesar untuk monitoring pricing dan pajak.</div>
                </div>
              </div>
              <div class="content-card__body">
                <VDataTable
                  :headers="topBaghasHeaders"
                  :items="topBaghasDepositors"
                  :loading="loading"
                  class="fin-vtable funding-table"
                  density="comfortable"
                  :items-per-page="10"
                  hover
                >
                  <template #item.ranking="{ item }"><span class="funding-rank">{{ item.ranking }}</span></template>
                  <template #item.noa_deposito="{ item }">{{ formatExactNumber(item.noa_deposito) }}</template>
                  <template #item.saldo_deposito="{ item }">{{ formatExactRupiah(item.saldo_deposito) }}</template>
                  <template #item.baghas_bayar="{ item }"><span class="funding-money">{{ formatExactRupiah(item.baghas_bayar) }}</span></template>
                  <template #item.pajak="{ item }">{{ formatExactRupiah(item.pajak) }}</template>
                  <template #item.avg_nisbah="{ item }">{{ formatTruncatedPercentage(item.avg_nisbah) }}</template>
                </VDataTable>
              </div>
            </div>
          </div>
        </div>
      </section>
    </div>
  </div>
</template>

<style scoped>
.funding-page {
  --funding-green: #0d9488;
  --funding-purple: #7c3aed;
}

.funding-sections,
.funding-section {
  min-width: 0;
}

.funding-insight {
  display: grid;
  grid-template-columns: minmax(0, 1.4fr) minmax(280px, 0.6fr);
  gap: 16px;
  background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%);
  border: 1px solid #dbeafe;
  border-radius: 18px;
  box-shadow: var(--fin-shadow-card);
  padding: 18px 20px;
}

.funding-insight__eyebrow {
  color: #64748b;
  font-size: 11px;
  font-weight: 900;
  letter-spacing: .08em;
  text-transform: uppercase;
  margin-bottom: 6px;
}

.funding-insight p {
  margin: 0;
  color: #1e293b;
  font-size: 13px;
  font-weight: 650;
  line-height: 1.65;
}

.funding-mix {
  display: grid;
  gap: 8px;
}

.funding-mix__row {
  display: flex;
  justify-content: space-between;
  color: #64748b;
  font-size: 12px;
  font-weight: 800;
}

.funding-mix__row strong {
  color: #0f172a;
}

.funding-mix__bar {
  height: 9px;
  border-radius: 999px;
  background: #e2e8f0;
  overflow: hidden;
}

.funding-mix__bar span {
  display: block;
  height: 100%;
  border-radius: inherit;
  background: linear-gradient(90deg, #0d9488, #2dd4bf);
}

.funding-mix__bar--purple span {
  background: linear-gradient(90deg, #7c3aed, #a78bfa);
}

.funding-grid {
  display: grid;
  gap: 18px;
}

.funding-grid--two {
  grid-template-columns: repeat(2, minmax(0, 1fr));
}

.funding-stat-list {
  display: grid;
  gap: 10px;
}

.funding-stat-list > div {
  display: flex;
  justify-content: space-between;
  gap: 18px;
  padding: 12px 14px;
  border: 1px solid #e2e8f0;
  border-radius: 12px;
  background: #f8fafc;
}

.funding-stat-list span {
  color: #64748b;
  font-size: 12px;
  font-weight: 750;
}

.funding-stat-list strong {
  color: #0f172a;
  font-size: 13px;
  font-weight: 900;
  text-align: right;
  overflow-wrap: anywhere;
}

.funding-card-header {
  align-items: center;
  gap: 16px;
  flex-wrap: wrap;
}

.funding-toolbar {
  display: flex;
  align-items: center;
  gap: 10px;
  margin-bottom: 14px;
  flex-wrap: wrap;
}

.funding-toolbar .v-text-field {
  max-width: 420px;
}

.funding-toolbar .v-select {
  max-width: 260px;
}

.funding-page-summary {
  display: grid;
  grid-template-columns: repeat(4, minmax(0, 1fr));
  gap: 10px;
  margin-bottom: 14px;
}

.funding-page-summary > div {
  min-width: 0;
  border: 1px solid #dbe7f3;
  border-radius: 14px;
  background: linear-gradient(145deg, #fff 0%, #f8fafc 100%);
  padding: 12px 14px;
}

.funding-page-summary span {
  display: block;
  color: #64748b;
  font-size: 10px;
  font-weight: 900;
  letter-spacing: .06em;
  text-transform: uppercase;
  margin-bottom: 6px;
}

.funding-page-summary strong {
  display: block;
  color: #0f172a;
  font-family: 'Plus Jakarta Sans', sans-serif;
  font-size: clamp(12px, 1vw, 15px);
  font-weight: 950;
  line-height: 1.2;
  overflow-wrap: anywhere;
}

.funding-rekap-insight {
  display: grid;
  grid-template-columns: minmax(0, 1.15fr) minmax(320px, .85fr);
  gap: 14px;
  align-items: stretch;
  margin-bottom: 14px;
  border: 1px solid #dbeafe;
  border-radius: 16px;
  background:
    radial-gradient(circle at top left, rgba(2, 132, 199, .1), transparent 34%),
    linear-gradient(145deg, #fff 0%, #f8fafc 100%);
  padding: 14px;
}

.funding-rekap-insight span {
  display: block;
  color: #64748b;
  font-size: 10px;
  font-weight: 950;
  letter-spacing: .07em;
  text-transform: uppercase;
}

.funding-rekap-insight p {
  color: #1e293b;
  font-size: 12.5px;
  font-weight: 700;
  line-height: 1.65;
  margin: 7px 0 0;
}

.funding-rekap-insight__metrics {
  display: grid;
  grid-template-columns: repeat(2, minmax(0, 1fr));
  gap: 10px;
}

.funding-rekap-insight__metrics > div {
  min-width: 0;
  border: 1px solid #e2e8f0;
  border-radius: 13px;
  background: rgba(255, 255, 255, .82);
  padding: 11px 12px;
}

.funding-rekap-insight__metrics strong {
  display: block;
  color: #0f172a;
  font-family: 'Plus Jakarta Sans', sans-serif;
  font-size: 13px;
  font-weight: 950;
  line-height: 1.2;
  margin-top: 5px;
  overflow-wrap: anywhere;
}

.funding-table {
  border: 1px solid #e2e8f0;
  border-radius: 14px;
  overflow: hidden;
}

.funding-total-row td {
  background: #f8fafc !important;
  border-top: 2px solid #cbd5e1 !important;
  color: #0f172a !important;
  font-size: 12px !important;
  font-weight: 950 !important;
}

.funding-dormant-insight {
  background:
    radial-gradient(circle at top left, rgba(245, 158, 11, .13), transparent 34%),
    linear-gradient(145deg, #fff 0%, #fffbeb 100%);
  border-color: #fde68a;
}

.funding-deposit-insight {
  background:
    radial-gradient(circle at top left, rgba(124, 58, 237, .13), transparent 34%),
    linear-gradient(145deg, #fff 0%, #faf5ff 100%);
  border-color: #ddd6fe;
}

.funding-maturity-insight {
  background:
    radial-gradient(circle at top left, rgba(225, 29, 72, .12), transparent 34%),
    linear-gradient(145deg, #fff 0%, #fff7ed 100%);
  border-color: #fed7aa;
}

.funding-analytics-insight {
  background:
    radial-gradient(circle at top left, rgba(2, 132, 199, .12), transparent 34%),
    linear-gradient(145deg, #fff 0%, #eff6ff 100%);
  border-color: #bfdbfe;
}

.funding-target-insight {
  background:
    radial-gradient(circle at top left, rgba(13, 148, 136, .12), transparent 34%),
    linear-gradient(145deg, #fff 0%, #ecfdf5 100%);
  border-color: #a7f3d0;
}

.funding-mutasi-insight {
  background:
    radial-gradient(circle at top left, rgba(79, 70, 229, .12), transparent 34%),
    linear-gradient(145deg, #fff 0%, #eef2ff 100%);
  border-color: #c7d2fe;
}

.funding-maturity-buckets .funding-dormant-bucket {
  background: linear-gradient(145deg, #fff 0%, #fffaf0 100%);
}

.funding-dormant-buckets {
  display: grid;
  grid-template-columns: repeat(4, minmax(0, 1fr));
  gap: 10px;
  margin-bottom: 14px;
}

.funding-dormant-bucket {
  display: flex;
  align-items: flex-start;
  gap: 11px;
  min-width: 0;
  border: 1px solid #e2e8f0;
  border-radius: 15px;
  background: #fff;
  box-shadow: 0 8px 22px rgba(15, 23, 42, .05);
  padding: 13px;
}

.funding-dormant-bucket__dot {
  flex: 0 0 10px;
  inline-size: 10px;
  block-size: 10px;
  border-radius: 999px;
  margin-top: 4px;
}

.funding-dormant-bucket span,
.funding-dormant-bucket small {
  display: block;
  color: #64748b;
  font-size: 10px;
  font-weight: 900;
  letter-spacing: .05em;
  text-transform: uppercase;
}

.funding-dormant-bucket strong {
  display: block;
  color: #0f172a;
  font-family: 'Plus Jakarta Sans', sans-serif;
  font-size: 13px;
  font-weight: 950;
  line-height: 1.25;
  margin: 5px 0 3px;
  overflow-wrap: anywhere;
}

.funding-age {
  display: inline-flex;
  align-items: center;
  justify-content: flex-end;
  min-inline-size: 74px;
  border-radius: 999px;
  background: #fff7ed;
  color: #9a3412;
  font-size: 11px;
  font-weight: 900;
  padding: 4px 8px;
}

.funding-cursor-footer {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 12px;
  border: 1px solid #e2e8f0;
  border-top: 0;
  border-radius: 0 0 14px 14px;
  background: #f8fafc;
  padding: 12px 14px;
  color: #64748b;
  font-size: 12px;
  font-weight: 750;
}

.funding-cursor-footer strong {
  color: #0f172a;
  font-weight: 950;
}

.funding-cursor-footer__actions {
  display: flex;
  gap: 8px;
  flex-wrap: wrap;
}

.funding-money {
  font-weight: 900;
  color: #064e3b;
  font-variant-numeric: tabular-nums;
  overflow-wrap: anywhere;
}

.funding-money--warning {
  color: #92400e;
}

.funding-risk-hero {
  display: grid;
  grid-template-columns: minmax(0, 1.3fr) minmax(260px, .7fr);
  gap: 18px;
  align-items: stretch;
  border: 1px solid #dbeafe;
  border-radius: 20px;
  padding: 20px;
  background:
    radial-gradient(circle at top left, rgba(13, 148, 136, .12), transparent 34%),
    linear-gradient(135deg, #ffffff 0%, #f8fafc 100%);
  box-shadow: var(--fin-shadow-card);
}

.funding-risk-hero__eyebrow {
  color: #0d9488;
  font-size: 11px;
  font-weight: 950;
  letter-spacing: .1em;
  text-transform: uppercase;
  margin-bottom: 8px;
}

.funding-risk-hero h2 {
  color: #0f172a;
  font-size: 24px;
  font-weight: 950;
  letter-spacing: -0.04em;
  margin: 0 0 8px;
}

.funding-risk-hero p {
  color: #475569;
  font-size: 13px;
  font-weight: 650;
  line-height: 1.7;
  margin: 0;
}

.funding-baghas-hero {
  background:
    radial-gradient(circle at top left, rgba(124, 58, 237, .12), transparent 34%),
    linear-gradient(135deg, #ffffff 0%, #f8fafc 100%);
}

.funding-risk-hero__meta {
  display: grid;
  align-content: center;
  gap: 5px;
  border: 1px solid #e2e8f0;
  border-radius: 18px;
  padding: 18px;
  background: #fff;
}

.funding-risk-hero__meta span,
.funding-risk-hero__meta small {
  color: #64748b;
  font-size: 11px;
  font-weight: 850;
  text-transform: uppercase;
  letter-spacing: .06em;
}

.funding-risk-hero__meta strong {
  color: #0f172a;
  font-size: clamp(18px, 2vw, 26px);
  font-weight: 950;
  line-height: 1.15;
  overflow-wrap: anywhere;
}

.funding-risk-cards {
  display: grid;
  grid-template-columns: repeat(4, minmax(0, 1fr));
  gap: 14px;
}

.funding-band-strip,
.funding-risk-pressure {
  display: grid;
  grid-template-columns: repeat(3, minmax(0, 1fr));
  gap: 12px;
}

.funding-band-strip__item,
.funding-risk-pressure > div {
  min-width: 0;
  border: 1px solid #e2e8f0;
  border-radius: 17px;
  background: linear-gradient(145deg, #fff 0%, #f8fafc 100%);
  box-shadow: 0 10px 26px rgba(15, 23, 42, .055);
  padding: 14px;
}

.funding-band-strip__top {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 10px;
  margin-bottom: 9px;
}

.funding-band-strip__top span,
.funding-risk-pressure span {
  color: #64748b;
  font-size: 10.5px;
  font-weight: 950;
  letter-spacing: .06em;
  text-transform: uppercase;
}

.funding-band-strip__top strong,
.funding-risk-pressure strong {
  display: block;
  color: #0f172a;
  font-family: 'Plus Jakarta Sans', sans-serif;
  font-size: 13px;
  font-weight: 950;
  line-height: 1.25;
  overflow-wrap: anywhere;
}

.funding-risk-pressure strong {
  margin: 6px 0 4px;
  font-size: clamp(13px, 1vw, 16px);
}

.funding-band-strip__bar {
  block-size: 8px;
  border-radius: 999px;
  background: #e2e8f0;
  overflow: hidden;
  margin-bottom: 8px;
}

.funding-band-strip__bar i {
  display: block;
  block-size: 100%;
  border-radius: inherit;
  background: linear-gradient(90deg, #0d9488, #7c3aed);
}

.funding-band-strip small,
.funding-risk-pressure small {
  display: block;
  color: #64748b;
  font-size: 11px;
  font-weight: 750;
  line-height: 1.45;
}

.funding-risk-card {
  display: flex;
  gap: 14px;
  align-items: flex-start;
  border: 1px solid #e2e8f0;
  border-radius: 18px;
  padding: 16px;
  background: #fff;
  box-shadow: var(--fin-shadow-card);
  min-width: 0;
}

.funding-risk-card__icon {
  display: grid;
  place-items: center;
  flex: 0 0 42px;
  inline-size: 42px;
  block-size: 42px;
  border-radius: 13px;
  font-size: 20px;
}

.funding-risk-card span {
  display: block;
  color: #64748b;
  font-size: 11px;
  font-weight: 900;
  letter-spacing: .06em;
  text-transform: uppercase;
  margin-bottom: 7px;
}

.funding-risk-card strong {
  display: block;
  color: #0f172a;
  font-size: clamp(15px, 1.5vw, 20px);
  font-weight: 950;
  line-height: 1.2;
  overflow-wrap: anywhere;
}

.funding-risk-card small {
  display: block;
  color: #64748b;
  font-size: 11px;
  font-weight: 750;
  margin-top: 5px;
}

.funding-baghas-control {
  display: grid;
  grid-template-columns: minmax(0, 1.25fr) minmax(280px, .75fr);
  gap: 14px;
  border: 1px solid #dbeafe;
  border-radius: 20px;
  background:
    radial-gradient(circle at top right, rgba(124, 58, 237, .08), transparent 34%),
    linear-gradient(135deg, #ffffff 0%, #f8fafc 100%);
  box-shadow: var(--fin-shadow-card);
  padding: 16px;
}

.funding-baghas-control__main,
.funding-baghas-control__mix {
  min-width: 0;
}

.funding-baghas-control__main span,
.funding-baghas-driver span {
  display: block;
  color: #64748b;
  font-size: 10.5px;
  font-weight: 950;
  letter-spacing: .07em;
  text-transform: uppercase;
  margin-bottom: 7px;
}

.funding-baghas-control__main strong {
  display: block;
  color: #0f172a;
  font-size: clamp(13px, 1.2vw, 16px);
  font-weight: 900;
  line-height: 1.65;
}

.funding-baghas-control__main small,
.funding-baghas-driver small {
  display: block;
  color: #64748b;
  font-size: 11px;
  font-weight: 750;
  line-height: 1.5;
  margin-top: 8px;
}

.funding-baghas-control__mix {
  display: grid;
  gap: 11px;
  border: 1px solid #e2e8f0;
  border-radius: 17px;
  background: rgba(255, 255, 255, .78);
  padding: 13px;
}

.funding-baghas-mix-row {
  display: grid;
  gap: 7px;
}

.funding-baghas-mix-row > div:first-child,
.funding-baghas-mix-row small {
  display: flex;
  justify-content: space-between;
  gap: 10px;
}

.funding-baghas-mix-row span,
.funding-baghas-mix-row small {
  color: #64748b;
  font-size: 11px;
  font-weight: 850;
}

.funding-baghas-mix-row strong {
  color: #0f172a;
  font-size: 12px;
  font-weight: 950;
}

.funding-baghas-mix-row__bar {
  block-size: 9px;
  border-radius: 999px;
  background: #e2e8f0;
  overflow: hidden;
}

.funding-baghas-mix-row__bar i {
  display: block;
  block-size: 100%;
  border-radius: inherit;
}

.funding-baghas-drivers {
  display: grid;
  grid-template-columns: repeat(4, minmax(0, 1fr));
  gap: 14px;
}

.funding-baghas-driver {
  position: relative;
  min-width: 0;
  overflow: hidden;
  border: 1px solid #e2e8f0;
  border-radius: 18px;
  background: #fff;
  box-shadow: var(--fin-shadow-card);
  padding: 16px;
}

.funding-baghas-driver__accent {
  position: absolute;
  inset-block-start: 0;
  inset-inline: 0;
  block-size: 4px;
}

.funding-baghas-driver strong,
.funding-baghas-driver b {
  display: block;
  color: #0f172a;
  font-family: 'Plus Jakarta Sans', sans-serif;
  font-weight: 950;
  line-height: 1.25;
  overflow-wrap: anywhere;
}

.funding-baghas-driver strong {
  font-size: 13px;
  min-block-size: 34px;
}

.funding-baghas-driver b {
  font-size: clamp(14px, 1.25vw, 18px);
  margin-top: 10px;
}

.funding-rank {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  inline-size: 30px;
  block-size: 30px;
  border-radius: 999px;
  background: #ecfdf5;
  color: #047857;
  font-size: 12px;
  font-weight: 950;
}

@media (max-width: 1080px) {
  .funding-insight,
  .funding-risk-hero,
  .funding-baghas-control,
  .funding-grid--two,
  .funding-page-summary,
  .funding-rekap-insight,
  .funding-dormant-buckets {
    grid-template-columns: 1fr;
  }

  .funding-risk-cards {
    grid-template-columns: repeat(2, minmax(0, 1fr));
  }

  .funding-baghas-drivers {
    grid-template-columns: repeat(2, minmax(0, 1fr));
  }

  .funding-band-strip,
  .funding-risk-pressure {
    grid-template-columns: 1fr;
  }
}

@media (max-width: 720px) {
  .funding-toolbar .v-text-field,
  .funding-toolbar .v-select {
    max-width: unset;
    flex: 1 1 100%;
  }

  .funding-cursor-footer {
    align-items: flex-start;
    flex-direction: column;
  }

  .funding-risk-cards {
    grid-template-columns: 1fr;
  }
}
</style>
