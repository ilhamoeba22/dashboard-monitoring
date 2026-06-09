<script setup>
import { ref, computed, onMounted, watch } from 'vue'
import { Head } from '@inertiajs/vue3'
import DefaultLayout from '@/layouts/default.vue'
import axios from 'axios'
import VueApexCharts from 'vue3-apexcharts'
import { formatExactRupiah, formatTruncatedPercentage } from '@/utils/money'
import * as XLSX from 'xlsx'
import jsPDF from 'jspdf'
import autoTable from 'jspdf-autotable'

defineOptions({ layout: DefaultLayout })

// --- State Management ----------------------------------------
const isLoading = ref(true)
const activeTab = ref(0)
const selectedCabang = ref(null)
const stressDialogOpen = ref(false)
const selectedStressScenario = ref('top5')
const actionWorkflows = ref({})
const actionWorkflowDialog = ref(false)
const selectedActionWorkflowItem = ref(null)
const isSavingActionWorkflow = ref(false)
const isExporting = ref(false)
const exportError = ref('')
const ppkaOperationalLoading = ref(false)
const ppkaOperationalRows = ref([])
const ppkaOperationalSummary = ref({
  total_ppap: 0,
  kol1_ppap: 0,
  kol2_ppap: 0,
  kol3_ppap: 0,
  kol4_ppap: 0,
  kol5_ppap: 0,
  total_kontrak: 0,
})
const ppkaOperationalSearch = ref('')
const ppkaOperationalAo = ref('Semua AO')
const ppkaOperationalPage = ref(1)
const manualAdjustmentEnabled = ref(false)
const ppkaAdjustmentDialog = ref(false)
const isSavingPpkaAdjustment = ref(false)
const ppkaAdjustmentForm = ref({
  nokontrak: '',
  nominal_ppap: '',
  alasan: ''
})
const actionWorkflowForm = ref({
  status: 'open',
  owner: '',
  due_date: '',
  note: '',
  reviewed_by: ''
})

const cabangs = ref([])
const segmens = ref(['Semua Segmen', 'Retail', 'Korporasi', 'Mikro'])
const qualityData = ref({
  kolektibilitas: [],
  akad_risk: [],
  aging: [],
  branch_compare: [],
  alerts: [],
  trend: [],
  trend_meta: { data_count: 0, last_bulan: null, filter_tahun: null, filter_bulan: null },
  top_obligor: [],
  ao_matrix: [],
  sector_data: [],
  product_data: [],
  ecl_staging: {
    ckpn_stage_1: 0,
    ckpn_stage_2: 0,
    ckpn_stage_3: 0
  },
  ckpn_model: {
    methodology: {},
    parameters: {
      pd_net_flow: 0,
      pd_migration: 0,
      selected_pd: 0,
      lgd_collateral_shortfall: 0,
      weighted_lgd: 0,
      transition_count: 0,
      observation_start: null,
      observation_end: null
    },
    summary: {
      eligible_noa: 0,
      excluded_noa: 0,
      individual_noa: 0,
      collective_noa: 0,
      eligible_os: 0,
      excluded_os: 0,
      eligible_ead: 0,
      individual_ead: 0,
      collective_ead: 0,
      individual_ckpn: 0,
      collective_ckpn: 0,
      model_ckpn: 0,
      system_ppap: 0,
      gap_vs_system: 0,
      coverage_model_to_ead: 0,
      system_coverage_to_ead: 0,
      eligible_collateral_net: 0,
      collateral_shortfall: 0
    },
    stage_breakdown: [],
    individual_debtors: [],
    product_scope: []
  },
  kap_metrics: {
    methodology: {},
    summary: {
      total_noa: 0,
      total_aktiva_produktif: 0,
      total_pembiayaan: 0,
      antar_bank_aktiva_total: 0,
      antar_bank_aktiva_lancar: 0,
      antar_bank_aktiva_macet: 0,
      antar_bank_aktiva_apyd: 0,
      antar_bank_aktiva_unmapped: 0,
      apyd: 0,
      apyd_all: 0,
      apyd_financing_tks: 0,
      apyd_ratio: 0,
      kap_ratio: 0,
      agunan_berbobot: 0,
      net_exposure_agunan: 0,
      net_exposure_npf: 0,
      net_exposure_ratio: 0,
      collateral_coverage_ratio: 0,
      npf_gross: 0,
      npf_gross_ratio: 0,
      npf_nett_ratio: 0,
      ppap_wajib_dibentuk: 0,
      ppap_wajib_dibentuk_financing: 0,
      ppap_system: 0,
      ppap_rekap_current: 0,
      ppap_rekap_previous: 0,
      ppap_rekap_gap: 0,
      ppap_system_vs_wd_gap: 0,
      ppap_gap: 0,
      ppap_coverage_to_wd: 0,
      ppap_wd_npf: 0,
      ppap_system_npf: 0
    },
    breakdown: [],
    worksheet_reconciliation: {
      rows: [],
      summary: {}
    },
    ppap_shortfall_accounts: [],
    ppap_over_reserved_accounts: [],
    ppap_gap_detail: {
      rows: [],
      summary: {}
    },
    apyd_contributors: [],
    aba_detail: {
      rows: [],
      reconciliation: {}
    },
    prudential_trend: [],
    prudential_trend_meta: {},
    anomaly_detector: {
      items: [],
      summary: {
        danger_count: 0,
        warning_count: 0,
        safe_count: 0,
        available_trend_months: 0,
        missing_trend_months: 0
      }
    },
    source_reconciliation: {
      rows: [],
      summary: {}
    },
    data_quality: {
      rows: [],
      summary: {}
    },
    recommendations: []
  },
  quality_risk_indicators: {
    miapb: {},
    ayda: {},
    pkr: {}
  },
  pkr_metrics: {
    summary: {},
    rows: [],
    detail_rows: [],
    trend: [],
    trend_meta: {},
    methodology: {}
  },
  restru_guard: {
    total_os_restru: 0,
    total_kontrak_restru: 0,
    restru_to_total_ratio: 0,
    gagal_kontrak: 0,
    vintage_failure_rate: 0
  },
  stress_test: {
    top5_os: 0,
    top10_os: 0,
    npf_gross_now: 0,
    npf_if_top5_fail: 0,
    npf_if_top10_fail: 0
  },
  summary: {
    total_os: 0,
    total_npf: 0,
    total_ppap: 0,
    npf_gross: 0,
    npf_net: 0,
    coverage_ratio: 0,
    far_ratio: 0,
    top_akad_risk: 'N/A',
    fdr: 0,
    fdr_v2: 0,
    fdr_components: {
      total_pembiayaan: 0,
      tabungan: 0,
      deposito: 0,
      dpk: 0,
      kewajiban_bank_lain: 0,
      ayda_pengurang: 0,
      modal_inti: 0
    },
    porsi_bagi_hasil: 0,
    composite_score: 1,
    risk_profile: {
      Kredit: 0, Likuiditas: 0, Operasional: 0, Kepatuhan: 0, Reputasi: 0
    }
  }
})

const filters = ref({
  tahun: new Date().getFullYear(),
  bulan: new Date().getMonth() + 1,
  segmen: 'Semua Segmen',
})

const years = computed(() => {
  const current = new Date().getFullYear()
  return [current, current - 1, current - 2, current - 3]
})

const months = [
  { title: 'Januari', value: 1 }, { title: 'Februari', value: 2 }, { title: 'Maret', value: 3 },
  { title: 'April', value: 4 }, { title: 'Mei', value: 5 }, { title: 'Juni', value: 6 },
  { title: 'Juli', value: 7 }, { title: 'Agustus', value: 8 }, { title: 'September', value: 9 },
  { title: 'Oktober', value: 10 }, { title: 'November', value: 11 }, { title: 'Desember', value: 12 }
]

// --- Computed Properties -------------------------------------
const actionWorkflowStatusItems = [
  { title: 'Open', value: 'open' },
  { title: 'In Progress', value: 'in_progress' },
  { title: 'Waiting', value: 'waiting' },
  { title: 'Done', value: 'done' },
  { title: 'Waived', value: 'waived' }
]

const actionWorkflowStatusLabel = (status) => ({
  open: 'Open',
  in_progress: 'In Progress',
  waiting: 'Waiting',
  done: 'Done',
  waived: 'Waived'
}[status] || 'Open')

const actionWorkflowStatusClass = (status) => ({
  open: 'workflow-status--open',
  in_progress: 'workflow-status--progress',
  waiting: 'workflow-status--waiting',
  done: 'workflow-status--done',
  waived: 'workflow-status--waived'
}[status] || 'workflow-status--open')

const isPastDueDate = (dateValue, status = 'open') => {
  if (!dateValue || ['done', 'waived'].includes(status)) return false
  const dueDate = new Date(`${dateValue}T23:59:59`)
  if (Number.isNaN(dueDate.getTime())) return false
  return dueDate < new Date()
}

const summary = computed(() => qualityData.value.summary || {})
const stressTest = computed(() => qualityData.value.stress_test || {})
const restruGuard = computed(() => qualityData.value.restru_guard || {})
const ckpnModel = computed(() => qualityData.value.ckpn_model || {})
const ckpnSummary = computed(() => ckpnModel.value.summary || {})
const ckpnParameters = computed(() => ckpnModel.value.parameters || {})
const ckpnMethodology = computed(() => ckpnModel.value.methodology || {})
const ckpnIndividualDebtors = computed(() => ckpnModel.value.individual_debtors || [])
const ckpnProductScope = computed(() => ckpnModel.value.product_scope || [])
const kapMetrics = computed(() => qualityData.value.kap_metrics || {})
const kapSummary = computed(() => kapMetrics.value.summary || {})
const kapMethodology = computed(() => kapMetrics.value.methodology || {})
const kapBreakdown = computed(() => kapMetrics.value.breakdown || [])
const kapWorksheet = computed(() => kapMetrics.value.worksheet_reconciliation || { rows: [], summary: {} })
const kapWorksheetRows = computed(() => kapWorksheet.value.rows || [])
const kapRecommendations = computed(() => kapMetrics.value.recommendations || [])
const qualityRiskIndicators = computed(() => qualityData.value.quality_risk_indicators || { miapb: {}, ayda: {}, pkr: {} })
const miapbIndicator = computed(() => qualityRiskIndicators.value.miapb || {})
const aydaIndicator = computed(() => qualityRiskIndicators.value.ayda || {})
const pkrMetrics = computed(() => qualityData.value.pkr_metrics || { summary: {}, rows: [], methodology: {} })
const pkrSummary = computed(() => pkrMetrics.value.summary || {})
const pkrRows = computed(() => pkrMetrics.value.rows || [])
const pkrDetailRows = computed(() => pkrMetrics.value.detail_rows || [])
const pkrTrendRows = computed(() => pkrMetrics.value.trend || [])
const pkrTrendMeta = computed(() => pkrMetrics.value.trend_meta || {})
const pkrMethodology = computed(() => pkrMetrics.value.methodology || {})
const kapPpapShortfallAccounts = computed(() => kapMetrics.value.ppap_shortfall_accounts || [])
const kapPpapOverReservedAccounts = computed(() => kapMetrics.value.ppap_over_reserved_accounts || [])
const kapPpapGapDetail = computed(() => kapMetrics.value.ppap_gap_detail || { rows: [], summary: {} })
const kapPpapGapRows = computed(() => kapPpapGapDetail.value.rows || [])
const kapPpapGapSummary = computed(() => kapPpapGapDetail.value.summary || {})
const kapApydContributors = computed(() => kapMetrics.value.apyd_contributors || [])
const kapAbaDetail = computed(() => kapMetrics.value.aba_detail || { rows: [], reconciliation: {} })
const kapAbaRows = computed(() => kapAbaDetail.value.rows || [])
const kapAbaReconciliation = computed(() => kapAbaDetail.value.reconciliation || {})
const kapPrudentialTrend = computed(() => kapMetrics.value.prudential_trend || [])
const kapTrendMeta = computed(() => kapMetrics.value.prudential_trend_meta || {})
const kapAnomalyDetector = computed(() => kapMetrics.value.anomaly_detector || { items: [], summary: {} })
const kapAnomalyItems = computed(() => kapAnomalyDetector.value.items || [])
const kapAnomalySummary = computed(() => kapAnomalyDetector.value.summary || {})
const kapSourceReconciliation = computed(() => kapMetrics.value.source_reconciliation || { rows: [], summary: {} })
const kapSourceReconciliationRows = computed(() => kapSourceReconciliation.value.rows || [])
const kapSourceReconciliationSummary = computed(() => kapSourceReconciliation.value.summary || {})
const kapDataQuality = computed(() => kapMetrics.value.data_quality || { rows: [], summary: {} })
const kapDataQualityRows = computed(() => kapDataQuality.value.rows || [])
const kapDataQualitySummary = computed(() => kapDataQuality.value.summary || {})
const ppkaOperationalAoOptions = computed(() => {
  const options = [...new Set(ppkaOperationalRows.value.map(item => item.nmao).filter(Boolean))]
  return ['Semua AO', ...options.sort()]
})
const filteredPpkaOperationalRows = computed(() => {
  const keyword = ppkaOperationalSearch.value.trim().toLowerCase()
  return ppkaOperationalRows.value.filter(item => {
    const matchAo = ppkaOperationalAo.value === 'Semua AO' || item.nmao === ppkaOperationalAo.value
    const matchSearch = !keyword ||
      String(item.nama || '').toLowerCase().includes(keyword) ||
      String(item.nokontrak || '').toLowerCase().includes(keyword) ||
      String(item.nocif || '').toLowerCase().includes(keyword)
    return matchAo && matchSearch
  })
})
const ppkaOperationalDistribution = computed(() => [
  { label: 'Kol 1 Lancar', value: Number(ppkaOperationalSummary.value.kol1_ppap) || 0, class: 'ppka-dist--safe' },
  { label: 'Kol 2 DPK', value: Number(ppkaOperationalSummary.value.kol2_ppap) || 0, class: 'ppka-dist--watch' },
  { label: 'Kol 3 KL', value: Number(ppkaOperationalSummary.value.kol3_ppap) || 0, class: 'ppka-dist--warning' },
  { label: 'Kol 4 Diragukan', value: Number(ppkaOperationalSummary.value.kol4_ppap) || 0, class: 'ppka-dist--danger' },
  { label: 'Kol 5 Macet', value: Number(ppkaOperationalSummary.value.kol5_ppap) || 0, class: 'ppka-dist--critical' }
])
const topObligors = computed(() => qualityData.value.top_obligor || [])
const actionPriorityQueue = computed(() => {
  const items = []
  const pushUnique = (item) => {
    const key = item.key || `${item.nokontrak || item.nama}-${item.signal}`
    const existing = items.find(row => row.key === key)
    if (existing) {
      existing.score += item.score
      existing.signals.push(item.signal)
      existing.action = `${existing.action} ${item.action}`
      existing.amount = Math.max(existing.amount || 0, item.amount || 0)
      existing.severity = existing.score >= 90 ? 'critical' : existing.score >= 65 ? 'high' : existing.score >= 40 ? 'medium' : 'watch'
      return
    }
    items.push({
      ...item,
      key,
      signals: [item.signal],
      severity: item.score >= 90 ? 'critical' : item.score >= 65 ? 'high' : item.score >= 40 ? 'medium' : 'watch'
    })
  }

  pkrDetailRows.value.slice(0, 80).forEach(item => {
    const col = String(item.colbaru || '')
    const score = col === '5' ? 95 : col === '4' ? 85 : col === '3' ? 75 : col === '2' ? 55 : 45
    pushUnique({
      key: `pkr-${item.nokontrak}`,
      signal: item.pkr_bucket || 'PKR',
      nokontrak: item.nokontrak,
      nama: item.nama,
      ao: item.nama_ao,
      cabang: item.cabang,
      kol: item.colbaru,
      amount: Number(item.os_pokok) || 0,
      score,
      action: col === '2'
        ? 'Tetapkan owner collection untuk cegah migrasi ke NPF.'
        : 'Susun action remedial, validasi agunan, dan eskalasi kolektibilitas.',
      source: 'PKR'
    })
  })

  kapPpapShortfallAccounts.value.slice(0, 50).forEach(item => {
    pushUnique({
      key: `shortfall-${item.nokontrak}`,
      signal: 'PPKA Shortfall',
      nokontrak: item.nokontrak,
      nama: item.nama,
      ao: item.nama_ao,
      cabang: item.cabang,
      kol: item.colbaru,
      amount: Math.abs(Number(item.ppap_gap) || 0),
      score: 88,
      action: 'Rekonsiliasi PPKA wajib dibentuk vs sistem dan validasi agunan.',
      source: 'PPKA'
    })
  })

  kapApydContributors.value.slice(0, 40).forEach(item => {
    pushUnique({
      key: `apyd-${item.nokontrak}`,
      signal: 'Top APYD Contributor',
      nokontrak: item.nokontrak,
      nama: item.nama,
      ao: item.nama_ao,
      cabang: item.cabang,
      kol: item.colbaru,
      amount: Number(item.apyd_tks) || 0,
      score: 72,
      action: 'Prioritaskan remedial karena kontribusi APYD besar.',
      source: 'APYD'
    })
  })

  topObligors.value.slice(0, 10).forEach(item => {
    pushUnique({
      key: `top-${item.nokontrak}`,
      signal: 'Top Obligor Stress',
      nokontrak: item.nokontrak,
      nama: item.nama,
      ao: item.nama_ao,
      cabang: item.cabang,
      kol: item.colbaru,
      amount: Number(item.os) || 0,
      score: 60,
      action: 'Review BMPK/stress exposure dan mitigasi jika terjadi penurunan kualitas.',
      source: 'Stress Test'
    })
  })

  ;(qualityData.value.alerts || []).slice(0, 30).forEach(item => {
    pushUnique({
      key: `ews-${item.nokontrak}`,
      signal: 'High-Risk Watchlist',
      nokontrak: item.nokontrak,
      nama: item.nama,
      ao: item.nama_ao || '-',
      cabang: item.cabang || '-',
      kol: item.colbaru,
      amount: Number(item.osmdlc) || 0,
      score: 68,
      action: 'Masukkan ke monitoring mingguan dan update status penagihan.',
      source: 'EWS'
    })
  })

  const rankedItems = items
    .sort((a, b) => (b.score - a.score) || ((b.amount || 0) - (a.amount || 0)))
    .slice(0, 50)

  return rankedItems.map(item => {
    const workflow = actionWorkflows.value[item.key] || {}
    const workflowStatus = workflow.status || 'open'

    return {
      ...item,
      workflow,
      workflow_status: workflowStatus,
      workflow_owner: workflow.owner || '',
      workflow_due_date: workflow.due_date || '',
      workflow_note: workflow.note || '',
      workflow_reviewed_by: workflow.reviewed_by || '',
      workflow_completed_at: workflow.completed_at || null,
      workflow_overdue: isPastDueDate(workflow.due_date, workflowStatus)
    }
  })
})
const actionQueueSummary = computed(() => ({
  total: actionPriorityQueue.value.length,
  critical: actionPriorityQueue.value.filter(item => item.severity === 'critical').length,
  high: actionPriorityQueue.value.filter(item => item.severity === 'high').length,
  medium: actionPriorityQueue.value.filter(item => item.severity === 'medium').length,
  inProgress: actionPriorityQueue.value.filter(item => item.workflow_status === 'in_progress').length,
  done: actionPriorityQueue.value.filter(item => item.workflow_status === 'done').length,
  overdue: actionPriorityQueue.value.filter(item => item.workflow_overdue).length,
  exposure: actionPriorityQueue.value.reduce((sum, item) => sum + (Number(item.amount) || 0), 0)
}))
const ewsWatchlistRows = computed(() => {
  return (qualityData.value.alerts || []).map(item => {
    const os = Number(item.osmdlc ?? item.os ?? 0) || 0
    const collateral = Number(item.htgagun ?? item.nilai_agunan ?? 0) || 0
    const ppka = Number(item.ppap ?? item.ppap_system ?? 0) || 0
    const daysPastDue = Number(item.haritgk) || 0
    const kol = String(item.colbaru || '')
    const totalCover = collateral + ppka
    const uncovered = Math.max(0, os - totalCover)
    const coverRatio = os > 0 ? (totalCover / os) * 100 : 0
    const severityScore =
      (kol === '5' ? 45 : kol === '4' ? 35 : kol === '3' ? 25 : 10) +
      (daysPastDue >= 180 ? 25 : daysPastDue >= 90 ? 18 : daysPastDue >= 30 ? 10 : 0) +
      (coverRatio < 50 ? 20 : coverRatio < 80 ? 12 : coverRatio < 100 ? 6 : 0) +
      (os >= 5000000000 ? 15 : os >= 1000000000 ? 10 : os >= 250000000 ? 5 : 0)
    const severity = severityScore >= 80 ? 'critical' : severityScore >= 60 ? 'high' : severityScore >= 40 ? 'medium' : 'watch'
    const recommendedAction = severity === 'critical'
      ? 'Eskalasi remedial harian, validasi agunan, dan siapkan opsi restrukturisasi/penyelesaian.'
      : severity === 'high'
        ? 'Tetapkan PIC monitoring mingguan dan pastikan rencana penagihan terdokumentasi.'
        : 'Pantau tren tunggakan dan update status kontak debitur.'
    const workflowKey = `ews-${item.nokontrak || item.nocif || item.nama || severityScore}`
    const workflow = actionWorkflows.value[workflowKey] || {}
    const workflowStatus = workflow.status || 'open'

    return {
      ...item,
      key: workflowKey,
      source: 'EWS Watchlist',
      signal: 'High-Risk Watchlist',
      signals: [
        'High-Risk Watchlist',
        `Kol ${kol || '-'}`,
        `${daysPastDue} hari tunggakan`,
        `Cover ${formatTruncatedPercentage(coverRatio)}`
      ],
      os,
      amount: os,
      collateral,
      ppka,
      total_cover: totalCover,
      uncovered,
      cover_ratio: coverRatio,
      severity_score: severityScore,
      score: severityScore,
      severity,
      days_past_due: daysPastDue,
      action: recommendedAction,
      recommended_action: recommendedAction,
      workflow_status: workflowStatus,
      workflow_owner: workflow.owner || '',
      workflow_due_date: workflow.due_date || '',
      workflow_note: workflow.note || '',
      workflow_reviewed_by: workflow.reviewed_by || '',
      workflow_completed_at: workflow.completed_at || null,
      workflow_overdue: isPastDueDate(workflow.due_date, workflowStatus)
    }
  }).sort((a, b) => (b.severity_score - a.severity_score) || (b.os - a.os))
})
const pagedEwsWatchlistRows = computed(() => paginateRows(ewsWatchlistRows.value, alertsPage.value))
const ewsWatchlistSummary = computed(() => ({
  total: ewsWatchlistRows.value.length,
  critical: ewsWatchlistRows.value.filter(item => item.severity === 'critical').length,
  high: ewsWatchlistRows.value.filter(item => item.severity === 'high').length,
  exposure: ewsWatchlistRows.value.reduce((sum, item) => sum + item.os, 0),
  uncovered: ewsWatchlistRows.value.reduce((sum, item) => sum + item.uncovered, 0),
  avg_cover_ratio: ewsWatchlistRows.value.length
    ? ewsWatchlistRows.value.reduce((sum, item) => sum + item.cover_ratio, 0) / ewsWatchlistRows.value.length
    : 0
}))
const topAoRisk = computed(() => {
  return [...(qualityData.value.ao_matrix || [])]
    .map(item => ({
      ...item,
      total_os: Number(item.total_os) || 0,
      npf_os: Number(item.npf_os ?? (Number(item.kol3_os || 0) + Number(item.kol4_os || 0) + Number(item.kol5_os || 0))) || 0,
      npf_ratio: Number(item.npf_ratio) || 0
    }))
    .sort((a, b) => (b.npf_ratio - a.npf_ratio) || (b.npf_os - a.npf_os))
    [0] || {}
})
const topProductRisk = computed(() => {
  return [...productRiskRows.value]
    .sort((a, b) => (b.npf_os - a.npf_os) || (b.npf_ratio - a.npf_ratio))
    [0] || {}
})
const topObligorRisk = computed(() => topObligors.value[0] || {})
const riskConcentrationSummary = computed(() => {
  const sectorName = sectorRiskSummary.value.top_sector || '-'
  const productName = topProductRisk.value.produk || summary.value.top_akad_risk || '-'
  const aoName = topAoRisk.value.nama_ao || '-'
  const obligorName = topObligorRisk.value.nama || '-'
  const warningLevel = ewsWatchlistSummary.value.critical > 0 || Number(sectorRiskSummary.value.npf_ratio) >= 5
    ? 'critical'
    : ewsWatchlistSummary.value.high > 0 || Number(pkrSummary.value.watch_kol2_ratio) >= 5
      ? 'high'
      : 'watch'

  return {
    warning_level: warningLevel,
    headline: warningLevel === 'critical'
      ? 'Tekanan risiko terkonsentrasi perlu eskalasi manajemen.'
      : warningLevel === 'high'
        ? 'Ada konsentrasi risiko yang perlu dipantau ketat.'
        : 'Konsentrasi risiko masih berada pada level monitoring.',
    interpretation: `Fokus utama saat ini berada pada sektor ${sectorName}, produk ${productName}, AO ${aoName}, dan obligor ${obligorName}. Critical EWS ${ewsWatchlistSummary.value.critical} debitur dengan net uncovered ${formatRp(ewsWatchlistSummary.value.uncovered)}.`,
    sector_name: sectorName,
    sector_npf_ratio: sectorRiskSummary.value.npf_ratio || 0,
    sector_npf: sectorRiskSummary.value.npf_os || 0,
    product_name: productName,
    product_npf: topProductRisk.value.npf_os || 0,
    ao_name: aoName,
    ao_npf_ratio: topAoRisk.value.npf_ratio || 0,
    obligor_name: obligorName,
    obligor_os: Number(topObligorRisk.value.os || 0),
    ews_critical: ewsWatchlistSummary.value.critical,
    ews_uncovered: ewsWatchlistSummary.value.uncovered
  }
})
const stressDialogLimit = computed(() => selectedStressScenario.value === 'top10' ? 10 : 5)
const stressDialogTitle = computed(() => selectedStressScenario.value === 'top10' ? 'Top 10 Debitur Macet' : 'Top 5 Debitur Macet')
const stressDialogItems = computed(() => topObligors.value.slice(0, stressDialogLimit.value))
const stressDialogExposure = computed(() => stressDialogItems.value.reduce((total, item) => total + (parseFloat(item.os) || 0), 0))
const tablePageSize = 10
const ckpnIndividualPage = ref(1)
const aoMatrixPage = ref(1)
const kapShortfallPage = ref(1)
const kapPpapGapPage = ref(1)
const kapApydPage = ref(1)
const kapAbaPage = ref(1)
const kapOverReservedPage = ref(1)
const alertsPage = ref(1)
const pkrPage = ref(1)
const pkrDetailPage = ref(1)
const actionQueuePage = ref(1)

const paginateRows = (rows, page) => {
  const safeRows = Array.isArray(rows) ? rows : []
  const safePage = Math.max(1, Number(page) || 1)
  const start = (safePage - 1) * tablePageSize
  return safeRows.slice(start, start + tablePageSize)
}

const totalPages = (rows) => Math.max(1, Math.ceil((Array.isArray(rows) ? rows.length : 0) / tablePageSize))
const ckpnIndividualPageCount = computed(() => totalPages(ckpnIndividualDebtors.value))
const aoMatrixPageCount = computed(() => totalPages(qualityData.value.ao_matrix || []))
const kapShortfallPageCount = computed(() => totalPages(kapPpapShortfallAccounts.value))
const kapPpapGapPageCount = computed(() => totalPages(kapPpapGapRows.value))
const kapApydPageCount = computed(() => totalPages(kapApydContributors.value))
const kapAbaPageCount = computed(() => totalPages(kapAbaRows.value))
const kapOverReservedPageCount = computed(() => totalPages(kapPpapOverReservedAccounts.value))
const alertsPageCount = computed(() => totalPages(ewsWatchlistRows.value))
const pkrPageCount = computed(() => totalPages(pkrRows.value))
const pkrDetailPageCount = computed(() => totalPages(pkrDetailRows.value))
const actionQueuePageCount = computed(() => totalPages(actionPriorityQueue.value))
const ppkaOperationalPageCount = computed(() => totalPages(filteredPpkaOperationalRows.value))

const pagedCkpnIndividualDebtors = computed(() => paginateRows(ckpnIndividualDebtors.value, ckpnIndividualPage.value))
const pagedAoMatrix = computed(() => paginateRows(qualityData.value.ao_matrix || [], aoMatrixPage.value))
const pagedKapPpapShortfallAccounts = computed(() => paginateRows(kapPpapShortfallAccounts.value, kapShortfallPage.value))
const pagedKapPpapGapRows = computed(() => paginateRows(kapPpapGapRows.value, kapPpapGapPage.value))
const pagedKapApydContributors = computed(() => paginateRows(kapApydContributors.value, kapApydPage.value))
const pagedKapAbaRows = computed(() => paginateRows(kapAbaRows.value, kapAbaPage.value))
const pagedKapPpapOverReservedAccounts = computed(() => paginateRows(kapPpapOverReservedAccounts.value, kapOverReservedPage.value))
const pagedAlerts = computed(() => paginateRows(ewsWatchlistRows.value, alertsPage.value))
const pagedPkrRows = computed(() => paginateRows(pkrRows.value, pkrPage.value))
const pagedPkrDetailRows = computed(() => paginateRows(pkrDetailRows.value, pkrDetailPage.value))
const pagedActionPriorityQueue = computed(() => paginateRows(actionPriorityQueue.value, actionQueuePage.value))
const pagedPpkaOperationalRows = computed(() => paginateRows(filteredPpkaOperationalRows.value, ppkaOperationalPage.value))
const ckpnComparison = computed(() => {
  const modelCkpn = Number(ckpnSummary.value.model_ckpn) || 0
  const systemPpap = Number(ckpnSummary.value.system_ppap) || 0
  const eligibleEad = Number(ckpnSummary.value.eligible_ead) || 0
  const gap = modelCkpn - systemPpap
  const absGap = Math.abs(gap)
  const gapToModel = modelCkpn > 0 ? (absGap / modelCkpn) * 100 : 0
  const gapToEad = eligibleEad > 0 ? (gap / eligibleEad) * 100 : 0
  const modelCoverage = Number(ckpnSummary.value.coverage_model_to_ead) || 0
  const systemCoverage = Number(ckpnSummary.value.system_coverage_to_ead) || 0
  const coverageDelta = modelCoverage - systemCoverage

  if (modelCkpn <= 0 && systemPpap <= 0) {
    return {
      status: 'neutral',
      icon: 'ri-information-line',
      label: 'Belum Ada Cadangan Terbaca',
      headline: 'CKPN model dan PPKA sistem belum menghasilkan nilai pada scope periode ini.',
      body: 'Interpretasi otomatis akan aktif ketika data EAD, PPKA sistem, dan parameter PD/LGD tersedia dari database.',
      action: 'Pastikan periode, cabang, segmen, dan scope produk CKPN sudah memiliki data pembiayaan aktif.',
      gap,
      gapToModel,
      gapToEad,
      coverageDelta
    }
  }

  if (gap > 0) {
    return {
      status: gapToModel >= 10 ? 'danger' : 'warning',
      icon: gapToModel >= 10 ? 'ri-alarm-warning-line' : 'ri-error-warning-line',
      label: gapToModel >= 10 ? 'Indikasi Under-Reserved' : 'Gap Perlu Review',
      headline: `CKPN model lebih tinggi dari PPKA sistem sebesar ${formatSignedRp(gap)}.`,
      body: `Artinya model berbasis PD, LGD, dan EAD membaca kebutuhan cadangan lebih besar daripada baseline CBS. Selisih ini setara ${formatTruncatedPercentage(gapToModel)} dari CKPN model dan ${formatTruncatedPercentage(gapToEad)} dari EAD eligible.`,
      action: 'Prioritaskan review parameter PD/LGD, validasi agunan, dan debitur individual sebelum dipakai sebagai rekomendasi pembentukan cadangan.',
      gap,
      gapToModel,
      gapToEad,
      coverageDelta
    }
  }

  if (gap < 0) {
    return {
      status: 'safe',
      icon: 'ri-shield-check-line',
      label: 'Cadangan Sistem Memadai',
      headline: `PPKA sistem lebih tinggi dari CKPN model sebesar ${formatRp(absGap)}.`,
      body: `Secara nominal baseline CBS masih menutup kebutuhan model CKPN pada scope eligible. Coverage sistem berada ${formatTruncatedPercentage(Math.abs(coverageDelta))} di atas coverage model terhadap EAD.`,
      action: 'Tetap lakukan review per stage dan per debitur besar agar tidak ada konsentrasi risiko yang tertutup oleh angka agregat.',
      gap,
      gapToModel,
      gapToEad,
      coverageDelta
    }
  }

  return {
    status: 'balanced',
    icon: 'ri-scales-3-line',
    label: 'Selaras',
    headline: 'CKPN model dan PPKA sistem berada pada nilai yang sama.',
    body: 'Tidak ada gap nominal pada scope eligible periode ini. Pembacaan lanjutan tetap perlu dilakukan melalui staging, individual debtor, dan AO matrix.',
    action: 'Pertahankan monitoring bulanan dan cek perubahan PD/LGD pada periode berikutnya.',
    gap,
    gapToModel,
    gapToEad,
    coverageDelta
  }
})
const agingStageTotals = computed(() => {
  const aging = qualityData.value.aging || []
  return aging.reduce((totals, row) => {
    totals.stage1 += parseFloat(row.aging_0) || 0
    totals.stage2 += (parseFloat(row.aging_1_30) || 0) + (parseFloat(row.aging_31_60) || 0) + (parseFloat(row.aging_61_90) || 0)
    totals.stage3 += parseFloat(row.aging_npf) || 0
    return totals
  }, { stage1: 0, stage2: 0, stage3: 0 })
})

const lastAvailableTrendMonth = computed(() => {
  if (qualityData.value.trend_meta?.last_bulan) {
    return qualityData.value.trend_meta.last_bulan
  }
  const trend = qualityData.value.trend || []
  if (trend.length === 0) return null
  return trend[trend.length - 1].bulan
})
const trendDataCount = computed(() => qualityData.value.trend_meta?.data_count ?? (qualityData.value.trend || []).length)
const selectedMonthLabel = computed(() => months.find(m => m.value === filters.value.bulan)?.title || '')
const hasTrendGap = computed(() => {
  const trend = qualityData.value.trend || []
  if (trend.length === 0) return false
  const monthNamesShort = ['Jan','Feb','Mar','Apr','Mei','Jun','Jul','Ags','Sep','Okt','Nov','Des']
  const selectedShort = monthNamesShort[filters.value.bulan - 1]
  return lastAvailableTrendMonth.value !== selectedShort
})

const formatRp = (v) => formatExactRupiah(v)
const formatRpSingkat = (v) => {
  return formatExactRupiah(v)
}
const formatRpUtuh = (value) => {
  return formatExactRupiah(value)
}
const fdrDenominatorTks = computed(() => {
  const components = summary.value.fdr_components || {}
  return (Number(components.dpk) || 0)
    + (Number(components.modal_inti) || 0)
    + (Number(components.kewajiban_bank_lain) || 0)
})
const formatWholeNumber = (value) => new Intl.NumberFormat('id-ID').format(Number(value) || 0)
const formatSignedRp = (value) => {
  const numeric = Number(value) || 0
  return `${numeric > 0 ? '+' : numeric < 0 ? '-' : ''}${formatExactRupiah(Math.abs(numeric))}`
}
const formatCbsDate = (value) => {
  if (!value) return '-'
  const raw = String(value).replace(/\D/g, '')
  if (raw.length !== 8) return String(value)

  const firstFour = parseInt(raw.slice(0, 4), 10)
  if (firstFour >= 1900 && firstFour <= 2100) {
    return `${raw.slice(6, 8)}/${raw.slice(4, 6)}/${raw.slice(0, 4)}`
  }

  return `${raw.slice(0, 2)}/${raw.slice(2, 4)}/${raw.slice(4, 8)}`
}
const kolektibilitasLabel = (kol) => ({
  '1': 'Kol 1 - Lancar',
  '2': 'Kol 2 - DPK',
  '3': 'Kol 3 - Kurang Lancar',
  '4': 'Kol 4 - Diragukan',
  '5': 'Kol 5 - Macet',
})[String(kol)] || `Kol ${kol || '-'}`
const anomalySeverityLabel = (severity) => ({
  danger: 'Kritis',
  warning: 'Perlu Perhatian',
  safe: 'Aman'
})[severity] || 'Info'
const anomalySeverityIcon = (severity) => ({
  danger: 'ri-alarm-warning-line',
  warning: 'ri-error-warning-line',
  safe: 'ri-shield-check-line'
})[severity] || 'ri-information-line'
const auditStatusLabel = (status) => ({
  matched: 'Sesuai',
  warning: 'Perlu Review',
  danger: 'Kritis'
})[status] || 'Info'
const auditStatusIcon = (status) => ({
  matched: 'ri-check-double-line',
  warning: 'ri-error-warning-line',
  danger: 'ri-alarm-warning-line'
})[status] || 'ri-information-line'
const openStressDialog = (scenario) => {
  selectedStressScenario.value = scenario
  stressDialogOpen.value = true
}

const activeFilterText = computed(() => {
  const m = months.find(x => x.value === filters.value.bulan)?.title || ''
  const c = cabangs.value.find(x => x.kdloc === selectedCabang.value)?.nama || 'Konsolidasi Seluruh Cabang'
  return `Menampilkan data: ${c} | Periode ${m} ${filters.value.tahun} | ${filters.value.segmen}`
})

// Chart TAB 1: Radar Chart (Profil Risiko)
const radarChartSeries = computed(() => {
  const profile = summary.value.risk_profile || {}
  return [{
    name: 'Tingkat Risiko',
    data: [
      parseFloat(profile.Kredit) || 0,
      parseFloat(profile.Likuiditas) || 0,
      parseFloat(profile.Operasional) || 0,
      parseFloat(profile.Kepatuhan) || 0,
      parseFloat(profile.Reputasi) || 0
    ]
  }]
})
const radarChartOpts = computed(() => ({
  chart: { type: 'radar', toolbar: { show: false }, fontFamily: "'Plus Jakarta Sans', sans-serif", background: 'transparent' },
  labels: ['Risiko Pembiayaan', 'Risiko Likuiditas', 'Risiko Operasional', 'Risiko Kepatuhan', 'Risiko Reputasi'],
  colors: ['#0d9488'],
  stroke: { width: 2.5, colors: ['#0d9488'] },
  fill: { opacity: 0.15, colors: ['#0d9488'] },
  markers: { size: 5, colors: ['#fff'], strokeColors: '#0d9488', strokeWidth: 2.5 },
  yaxis: { show: false, min: 0, max: 5, tickAmount: 5 },
  plotOptions: { radar: { polygons: { strokeColors: '#e2e8f0', connectorColors: '#e2e8f0', fill: { colors: ['#f8fafc', '#ffffff'] } } } }
}))

// NPF Trend
const trendChartSeries = computed(() => {
  const trendData = qualityData.value.trend || []
  if (trendData.length === 0) return []
  return [
    { name: 'NPF Gross (%)', data: trendData.map(r => r.total_os > 0 ? ((r.npf_os / r.total_os) * 100) : 0) },
    { name: 'NPF Net (%)', data: trendData.map(r => {
        const netVal = Math.max(0, parseFloat(r.npf_os) - parseFloat(r.total_ppap))
        return r.total_os > 0 ? ((netVal / r.total_os) * 100) : 0
      })
    }
  ]
})
const trendChartOpts = computed(() => ({
  chart: { type: 'area', toolbar: { show: false }, fontFamily: "'Plus Jakarta Sans', sans-serif", dropShadow: { enabled: true, top: 4, left: 0, blur: 8, opacity: 0.06 } },
  colors: ['#e11d48', '#059669'],
  stroke: { curve: 'smooth', width: 4 },
  fill: { type: 'gradient', gradient: { shadeIntensity: 1, opacityFrom: 0.35, opacityTo: 0.02, stops: [0, 90, 100] } },
  markers: { size: 4, strokeWidth: 2.5, hover: { size: 7 } },
  xaxis: { categories: (qualityData.value.trend || []).map(r => r.bulan), labels: { style: { colors: '#94a3b8', fontWeight: 500, fontSize: '12px' } }, axisBorder: { show: false }, axisTicks: { show: false } },
  yaxis: { labels: { formatter: (v) => formatTruncatedPercentage(v), style: { colors: '#94a3b8', fontWeight: 500 } } },
  grid: { borderColor: '#f1f5f9', strokeDashArray: 5, padding: { left: 10, right: 10 } },
  legend: { position: 'top', horizontalAlign: 'right', fontWeight: 600, fontSize: '13px' },
  dataLabels: { enabled: false },
  tooltip: { theme: 'light', y: { formatter: (v) => formatTruncatedPercentage(v) } }
}))

// Chart TAB 2: Kolektibilitas (Donut)
const kolChartSeries = computed(() => {
  const data = qualityData.value.kolektibilitas || []
  if (data.length === 0) return []
  const mapKol = { '1': 0, '2': 0, '3': 0, '4': 0, '5': 0 }
  data.forEach(r => {
    if (mapKol[r.kol] !== undefined) mapKol[r.kol] += (parseFloat(r.total_os) || 0)
  })
  return [mapKol['1'], mapKol['2'], mapKol['3'], mapKol['4'], mapKol['5']]
})
const kolChartOpts = computed(() => ({
  chart: { type: 'donut', fontFamily: "'Plus Jakarta Sans', sans-serif" },
  labels: ['Kol 1 (Lancar)', 'Kol 2 (DPK)', 'Kol 3 (Kurang Lancar)', 'Kol 4 (Diragukan)', 'Kol 5 (Macet)'],
  colors: ['#10b981', '#f59e0b', '#f97316', '#ef4444', '#991b1b'],
  plotOptions: { pie: { donut: { size: '72%', labels: { show: true, name: { show: true, fontSize: '13px' }, value: { formatter: (v) => formatRpSingkat(v), fontSize: '15px', fontWeight: '700' }, total: { show: true, showAlways: true, label: 'Total Portofolio', fontSize: '12px', fontWeight: '600', formatter: function (w) { return formatRpSingkat(w.globals.seriesTotals.reduce((a, b) => a + b, 0)) } } } } } },
  dataLabels: { enabled: false },
  legend: { position: 'bottom', horizontalAlign: 'center', fontSize: '12px', fontWeight: 500, markers: { radius: 4 } },
  stroke: { show: true, colors: ['#ffffff'], width: 3 },
  tooltip: { y: { formatter: (val) => formatRp(val) } }
}))

// Chart TAB 3: Sektor Ekonomi (Horizontal Bar)
const sectorChartSeries = computed(() => {
  const data = qualityData.value.sector_data || []
  if (data.length === 0) return []
  const top10 = sectorRiskRows.value
  return [
    { name: 'Total Outstanding', data: top10.map(r => Number(r.total_os) || 0) },
    { name: 'Pembiayaan Bermasalah (NPF)', data: top10.map(r => Number(r.npf_os) || 0) }
  ]
})
const sectorChartOpts = computed(() => ({
  chart: { type: 'bar', stacked: false, toolbar: { show: false }, fontFamily: "'Plus Jakarta Sans', sans-serif" },
  plotOptions: { bar: { horizontal: true, borderRadius: 6, barHeight: '62%', dataLabels: { position: 'top' } } },
  colors: ['#0f766e', '#e11d48'],
  xaxis: {
    labels: { formatter: (v) => formatRpSingkat(v), style: { fontSize: '11px', colors: '#64748b', fontWeight: 700 } },
    axisBorder: { show: false },
    axisTicks: { show: false }
  },
  yaxis: {
    categories: sectorRiskRows.value.map(r => r.sektor),
    labels: {
      maxWidth: 240,
      style: { fontSize: '11px', colors: '#334155', fontWeight: 800 }
    }
  },
  dataLabels: { enabled: false },
  grid: { xaxis: { lines: { show: true } }, yaxis: { lines: { show: false } }, borderColor: '#e2e8f0', strokeDashArray: 4 },
  tooltip: { shared: true, intersect: false, y: { formatter: (v) => formatRp(v) } },
  legend: { position: 'top', horizontalAlign: 'right', fontWeight: 700, markers: { radius: 8 } }
}))

const sectorRiskRows = computed(() => {
  return (qualityData.value.sector_data || [])
    .map(item => {
      const totalOs = Number(item.total_os) || 0
      const npfOs = Number(item.npf_os) || 0
      return {
        ...item,
        total_os: totalOs,
        npf_os: npfOs,
        noa: Number(item.noa) || 0,
        npf_ratio: totalOs > 0 ? (npfOs / totalOs) * 100 : 0
      }
    })
    .sort((a, b) => (b.npf_os - a.npf_os) || (b.total_os - a.total_os))
    .slice(0, 10)
})

const sectorRiskSummary = computed(() => {
  const rows = sectorRiskRows.value
  const totalOs = rows.reduce((sum, item) => sum + item.total_os, 0)
  const npfOs = rows.reduce((sum, item) => sum + item.npf_os, 0)
  const top = rows[0] || {}
  return {
    total_os: totalOs,
    npf_os: npfOs,
    npf_ratio: totalOs > 0 ? (npfOs / totalOs) * 100 : 0,
    top_sector: top.sektor || '-',
    top_npf: top.npf_os || 0
  }
})

// Chart TAB 3: Product Composition (Donut)
const productChartSeries = computed(() => {
  const data = qualityData.value.product_data || []
  if (data.length === 0) return []
  return data.map(r => parseFloat(r.total_os) || 0)
})
const productChartOpts = computed(() => ({
  chart: { type: 'donut', fontFamily: "'Plus Jakarta Sans', sans-serif" },
  labels: (qualityData.value.product_data || []).map(r => r.produk),
  colors: ['#4f46e5', '#8b5cf6', '#d946ef', '#0ea5e9', '#14b8a6', '#f59e0b', '#64748b'],
  plotOptions: { pie: { donut: { size: '65%' } } },
  dataLabels: { enabled: false },
  legend: {
    position: 'bottom',
    offsetY: 0,
    fontSize: '12px',
    markers: { width: 8, height: 8, radius: 4 },
    itemMargin: { horizontal: 5, vertical: 2 }
  },
  stroke: { show: true, colors: ['#ffffff'], width: 3 },
  tooltip: { y: { formatter: (val) => formatRp(val) } }
}))

const productRiskRows = computed(() => {
  return (qualityData.value.product_data || [])
    .map(item => {
      const totalOs = Number(item.total_os) || 0
      const npfOs = Number(item.npf_os) || 0
      return {
        ...item,
        total_os: totalOs,
        npf_os: npfOs,
        noa: Number(item.noa) || 0,
        npf_ratio: totalOs > 0 ? (npfOs / totalOs) * 100 : 0
      }
    })
    .sort((a, b) => b.total_os - a.total_os)
    .slice(0, 6)
})

const kapTrendAvailableRows = computed(() => kapPrudentialTrend.value.filter(item => item.available))
const kapTrendChartSeries = computed(() => {
  const rows = kapTrendAvailableRows.value
  return [
    { name: 'Rasio KAP (%)', type: 'line', data: rows.map(item => Number(item.kap_ratio) || 0) },
    { name: 'APYD', type: 'column', data: rows.map(item => Number(item.apyd) || 0) },
    { name: 'Total PPKA Bulanan', type: 'column', data: rows.map(item => Number(item.total_ppap_bulanan ?? item.ppap_rekap_current ?? item.ppap_system) || 0) },
    { name: 'Gap PPKA Bulanan', type: 'column', data: rows.map(item => Number(item.ppap_gap) || 0) },
    { name: 'Net Exposure Agunan', type: 'line', data: rows.map(item => Number(item.net_exposure_agunan) || 0) }
  ]
})
const formatChartPercentAxis = (value) => `${new Intl.NumberFormat('id-ID', {
  minimumFractionDigits: 0,
  maximumFractionDigits: 2
}).format(Number(value) || 0)}%`
const formatChartCurrencyAxis = (value) => `Rp ${new Intl.NumberFormat('id-ID', {
  minimumFractionDigits: 0,
  maximumFractionDigits: 0
}).format(Math.trunc(Number(value) || 0))}`
const kapTrendChartOpts = computed(() => ({
  chart: { type: 'line', stacked: false, toolbar: { show: false }, fontFamily: "'Plus Jakarta Sans', sans-serif", background: 'transparent' },
  labels: kapTrendAvailableRows.value.map(item => item.label),
  colors: ['#0d9488', '#f97316', '#8b5cf6', '#be123c', '#4f46e5'],
  stroke: { width: [4, 0, 0, 0, 3], curve: 'smooth' },
  plotOptions: { bar: { columnWidth: '58%', borderRadius: 5 } },
  dataLabels: { enabled: false },
  yaxis: [
    {
      seriesName: 'Rasio KAP (%)',
      decimalsInFloat: 2,
      tickAmount: 5,
      labels: { formatter: (value) => formatChartPercentAxis(value), style: { colors: '#0d9488', fontWeight: 800, fontSize: '11px' } },
      min: 0,
      max: 100
    },
    {
      opposite: true,
      seriesName: 'APYD',
      decimalsInFloat: 0,
      tickAmount: 5,
      labels: { formatter: (value) => formatChartCurrencyAxis(value), style: { colors: '#64748b', fontWeight: 800, fontSize: '10px' }, minWidth: 118, maxWidth: 150 },
      title: { text: 'Nominal', style: { color: '#64748b', fontSize: '10px', fontWeight: 800 } }
    },
    {
      opposite: true,
      seriesName: 'Total PPKA Bulanan',
      show: false,
      decimalsInFloat: 0,
      labels: { formatter: (value) => formatChartCurrencyAxis(value) }
    },
    {
      opposite: true,
      seriesName: 'Gap PPKA Bulanan',
      show: false,
      decimalsInFloat: 0,
      labels: { formatter: (value) => formatChartCurrencyAxis(value) }
    },
    {
      opposite: true,
      seriesName: 'Net Exposure Agunan',
      show: false,
      decimalsInFloat: 0,
      labels: { formatter: (value) => formatChartCurrencyAxis(value) }
    }
  ],
  grid: { borderColor: '#f1f5f9', strokeDashArray: 5 },
  legend: { position: 'top', horizontalAlign: 'right', fontWeight: 700, fontSize: '12px' },
  tooltip: {
    shared: true,
    intersect: false,
    y: {
      formatter: (value, context) => context.seriesIndex === 0 ? formatTruncatedPercentage(value) : formatRp(value)
    }
  }
}))

const pkrTrendAvailableRows = computed(() => pkrTrendRows.value.filter(item => item.available))
const pkrTrendChartSeries = computed(() => {
  const rows = pkrTrendAvailableRows.value
  return [
    { name: 'PKR Ratio (%)', type: 'line', data: rows.map(item => Number(item.pkr_ratio) || 0) },
    { name: 'OS PKR', type: 'column', data: rows.map(item => Number(item.pkr_os) || 0) },
    { name: 'Kol 1 Restrukturisasi/Valid', type: 'column', data: rows.map(item => Number(item.restructured_lancar_os) || 0) },
    { name: 'Kol 2 Watch', type: 'column', data: rows.map(item => Number(item.watch_kol2_os) || 0) },
    { name: 'Kol 2-5', type: 'column', data: rows.map(item => Number(item.pkr_non_lancar_os) || 0) }
  ]
})
const pkrTrendChartOpts = computed(() => ({
  chart: { type: 'line', stacked: false, toolbar: { show: false }, fontFamily: "'Plus Jakarta Sans', sans-serif", background: 'transparent' },
  labels: pkrTrendAvailableRows.value.map(item => item.label),
  colors: ['#e11d48', '#0d9488', '#8b5cf6', '#f59e0b', '#334155'],
  stroke: { width: [4, 0, 0, 0, 0], curve: 'smooth' },
  plotOptions: { bar: { columnWidth: '56%', borderRadius: 5 } },
  dataLabels: { enabled: false },
  yaxis: [
    {
      seriesName: 'PKR Ratio (%)',
      decimalsInFloat: 2,
      tickAmount: 5,
      labels: { formatter: (value) => formatChartPercentAxis(value), style: { colors: '#e11d48', fontWeight: 800, fontSize: '11px' } },
      min: 0,
      max: 100
    },
    {
      opposite: true,
      seriesName: 'OS PKR',
      decimalsInFloat: 0,
      tickAmount: 5,
      labels: { formatter: (value) => formatChartCurrencyAxis(value), style: { colors: '#64748b', fontWeight: 800, fontSize: '10px' }, minWidth: 118, maxWidth: 150 },
      title: { text: 'Nominal', style: { color: '#64748b', fontSize: '10px', fontWeight: 800 } }
    },
    { opposite: true, seriesName: 'Kol 1 Restrukturisasi/Valid', show: false, labels: { formatter: (value) => formatChartCurrencyAxis(value) } },
    { opposite: true, seriesName: 'Kol 2 Watch', show: false, labels: { formatter: (value) => formatChartCurrencyAxis(value) } },
    { opposite: true, seriesName: 'Kol 2-5', show: false, labels: { formatter: (value) => formatChartCurrencyAxis(value) } }
  ],
  grid: { borderColor: '#f1f5f9', strokeDashArray: 5 },
  legend: { position: 'top', horizontalAlign: 'right', fontWeight: 700, fontSize: '12px' },
  tooltip: {
    shared: true,
    intersect: false,
    y: {
      formatter: (value, context) => context.seriesIndex === 0 ? formatTruncatedPercentage(value) : formatRp(value)
    }
  }
}))

// PK Score formatter
const pkScoreData = computed(() => {
  const pk = summary.value.composite_score || 1
  if (pk <= 2) return { text: `PK-${pk} (Sehat)`, gradient: 'from-emerald-500 to-teal-600', icon: 'ri-shield-check-fill', badge: 'Sehat' }
  if (pk === 3) return { text: `PK-${pk} (Cukup Sehat)`, gradient: 'from-amber-400 to-orange-500', icon: 'ri-shield-star-fill', badge: 'Cukup Sehat' }
  return { text: `PK-${pk} (Kurang Sehat)`, gradient: 'from-rose-500 to-red-600', icon: 'ri-shield-cross-fill', badge: 'Kurang Sehat' }
})

const tabs = [
  { name: 'RGEC & Profil Risiko', icon: 'ri-dashboard-3-line' },
  { name: 'Kualitas & CKPN', icon: 'ri-safe-2-line' },
  { name: 'KAP & PPKA Prudential', icon: 'ri-shield-star-line' },
  { name: 'Risk Concentration & EWS', icon: 'ri-alarm-warning-line' }
]

// --- API Calls -----------------------------------------------
const exportPeriodLabel = computed(() => `${selectedMonthLabel.value} ${filters.value.tahun}`)
const exportReportTitle = computed(() => `Monitoring Dashboard - ${tabs[activeTab.value]?.name || 'Quality'}`)
const exportFileStem = computed(() => {
  const tab = tabs[activeTab.value]?.name || 'Quality'
  return `quality-${tab}-${exportPeriodLabel.value}`.toLowerCase().replace(/[^a-z0-9]+/g, '-').replace(/(^-|-$)/g, '')
})
const canExport = computed(() => !isLoading.value && !isExporting.value)
const currencyValue = (value) => formatExactRupiah(value)
const percentageValue = (value) => formatTruncatedPercentage(value)
const sheetName = (name) => String(name).replace(/[\\/?*[\]:]/g, ' ').slice(0, 31) || 'Sheet'
const exportGeneratedAt = () => new Date().toLocaleString('id-ID', {
  day: '2-digit',
  month: 'long',
  year: 'numeric',
  hour: '2-digit',
  minute: '2-digit'
})
const applyWorksheetWidths = (worksheet, rows) => {
  const columns = Object.keys(rows[0] || { Informasi: '' })
  worksheet['!cols'] = columns.map(column => {
    const maxContentLength = rows.reduce((max, row) => Math.max(max, String(row[column] ?? '').length), column.length)
    return { wch: Math.min(Math.max(maxContentLength + 2, 12), 48) }
  })
}
const exportMetadataRows = () => [
  { Field: 'Report', Value: exportReportTitle.value },
  { Field: 'Periode', Value: exportPeriodLabel.value },
  { Field: 'Filter', Value: activeFilterText.value },
  { Field: 'Tab Aktif', Value: tabs[activeTab.value]?.name || '-' },
  { Field: 'Tanggal Generate', Value: exportGeneratedAt() },
  { Field: 'Standar Angka', Value: 'Nominal ditampilkan penuh tanpa pembulatan; persentase maksimal 2 angka desimal.' },
  { Field: 'Sumber', Value: 'Database operasional/snapshot sesuai filter bulan dan tahun aktif.' }
]
const normalizedSheetRows = (sheet) => sheet.rows?.length ? sheet.rows : [{ Informasi: 'Tidak ada data pada filter ini' }]
const buildStandardWorksheet = (sheet, sheetIndex) => {
  const rows = normalizedSheetRows(sheet)
  const columns = Object.keys(rows[0] || { Informasi: '' })
  const headerRow = 7
  const worksheet = XLSX.utils.aoa_to_sheet([
    [exportReportTitle.value],
    ['Periode', exportPeriodLabel.value],
    ['Filter', activeFilterText.value],
    ['Generated At', exportGeneratedAt()],
    ['Sheet', `${String(sheetIndex + 1).padStart(2, '0')} - ${sheet.name}`],
    [],
    columns
  ])
  XLSX.utils.sheet_add_json(worksheet, rows, { origin: { r: headerRow, c: 0 }, skipHeader: true })
  const lastColumn = Math.max(columns.length - 1, 0)
  const lastRow = headerRow + rows.length
  worksheet['!merges'] = [{ s: { r: 0, c: 0 }, e: { r: 0, c: lastColumn } }]
  worksheet['!autofilter'] = {
    ref: XLSX.utils.encode_range({ s: { r: headerRow - 1, c: 0 }, e: { r: lastRow, c: lastColumn } })
  }
  worksheet['!freeze'] = { xSplit: 0, ySplit: headerRow, topLeftCell: 'A8', activePane: 'bottomLeft', state: 'frozen' }
  applyWorksheetWidths(worksheet, rows)
  worksheet['!rows'] = [
    { hpt: 24 },
    { hpt: 18 },
    { hpt: 18 },
    { hpt: 18 },
    { hpt: 18 },
    { hpt: 8 },
    { hpt: 22 }
  ]
  return worksheet
}
const buildMetadataWorksheet = () => {
  const rows = exportMetadataRows()
  const worksheet = XLSX.utils.json_to_sheet(rows)
  applyWorksheetWidths(worksheet, rows)
  worksheet['!autofilter'] = { ref: XLSX.utils.encode_range({ s: { r: 0, c: 0 }, e: { r: rows.length, c: 1 } }) }
  worksheet['!freeze'] = { xSplit: 0, ySplit: 1, topLeftCell: 'A2', activePane: 'bottomLeft', state: 'frozen' }
  return worksheet
}

const buildExportSheets = () => {
  const commonSummary = [
    { Metrik: 'Periode', Nilai: exportPeriodLabel.value },
    { Metrik: 'Filter', Nilai: activeFilterText.value },
    { Metrik: 'Tab Aktif', Nilai: tabs[activeTab.value]?.name || '-' },
    { Metrik: 'Waktu Generate', Nilai: exportGeneratedAt() },
    { Metrik: 'Total Pembiayaan', Nilai: currencyValue(summary.value.total_os) },
    { Metrik: 'NPF Gross', Nilai: percentageValue(summary.value.npf_gross) },
    { Metrik: 'NPF Net', Nilai: percentageValue(summary.value.npf_net) },
    { Metrik: 'Coverage Ratio', Nilai: percentageValue(summary.value.coverage_ratio) },
    { Metrik: 'FDR Pembiayaan / DPK + Modal Inti + KBL', Nilai: percentageValue(summary.value.fdr) },
    { Metrik: 'FDR Pembiayaan / DPK', Nilai: percentageValue(summary.value.fdr_v2) }
  ]

  if (activeTab.value === 0) {
    return [
      { name: 'Executive Summary', rows: commonSummary },
      { name: 'Profil Risiko', rows: Object.entries(summary.value.risk_profile || {}).map(([Risiko, Skor]) => ({ Risiko, Skor })) },
      { name: 'Trend NPF', rows: (qualityData.value.trend || []).map(item => ({
        Bulan: item.bulan,
        'Total OS': currencyValue(item.total_os),
        'NPF OS': currencyValue(item.npf_os),
        'Total PPKA': currencyValue(item.total_ppap),
        'NPF Gross': item.total_os > 0 ? percentageValue((Number(item.npf_os) / Number(item.total_os)) * 100) : percentageValue(0)
      })) },
      { name: 'Top Obligor', rows: topObligors.value.map(item => ({
        Kontrak: item.nokontrak,
        Nasabah: item.nama,
        AO: item.nama_ao,
        Kol: kolektibilitasLabel(item.colbaru),
        OS: currencyValue(item.os),
        PPKA: currencyValue(item.ppap)
      })) }
    ]
  }

  if (activeTab.value === 1) {
    return [
      { name: 'CKPN Summary', rows: Object.entries(ckpnSummary.value || {}).map(([Metrik, Nilai]) => ({ Metrik, Nilai: typeof Nilai === 'number' ? currencyValue(Nilai) : Nilai })) },
      { name: 'Stage Breakdown', rows: (ckpnModel.value.stage_breakdown || []).map(item => ({
        Stage: item.stage,
        NOA: item.noa,
        EAD: currencyValue(item.ead),
        CKPN: currencyValue(item.ckpn),
        Coverage: percentageValue(item.coverage_ratio)
      })) },
      { name: 'Debitur Individual CKPN', rows: ckpnIndividualDebtors.value.map(item => ({
        Kontrak: item.nokontrak,
        Nasabah: item.nama,
        Produk: item.produk,
        Kol: kolektibilitasLabel(item.colbaru),
        EAD: currencyValue(item.ead),
        Agunan: currencyValue(item.collateral_net),
        LGD: percentageValue(item.lgd),
        CKPN: currencyValue(item.ckpn)
      })) },
      { name: 'Scope Produk CKPN', rows: ckpnProductScope.value.map(item => ({
        Produk: item.produk,
        Scope: item.ckpn_scope,
        NOA: item.noa,
        EAD: currencyValue(item.ead),
        CKPN: currencyValue(item.ckpn)
      })) },
      { name: 'AO Matrix', rows: (qualityData.value.ao_matrix || []).map(item => ({
        AO: item.nama_ao,
        NOA: item.noa,
        OS: currencyValue(item.total_os),
        NPF: currencyValue(item.npf_os),
        'NPF Ratio': percentageValue(item.npf_ratio),
        PPKA: currencyValue(item.total_ppap)
      })) }
    ]
  }

  if (activeTab.value === 2) {
    return [
      { name: 'KAP Summary', rows: [
        { Metrik: 'Rasio KAP TKS', Nilai: percentageValue(kapSummary.value.kap_ratio) },
        { Metrik: 'Total Aktiva Produktif', Nilai: currencyValue(kapSummary.value.total_aktiva_produktif) },
        { Metrik: 'Total Pembiayaan', Nilai: currencyValue(kapSummary.value.total_pembiayaan) },
        { Metrik: 'MIAPB', Nilai: percentageValue(miapbIndicator.value.ratio) },
        { Metrik: 'Modal Inti MIAPB', Nilai: currencyValue(miapbIndicator.value.modal_inti) },
        { Metrik: 'Aset Bermasalah MIAPB', Nilai: currencyValue(miapbIndicator.value.aset_bermasalah) },
        { Metrik: 'PPKA Bermasalah MIAPB', Nilai: currencyValue(miapbIndicator.value.ppap_bermasalah) },
        { Metrik: 'AYDA Ratio', Nilai: percentageValue(aydaIndicator.value.ratio) },
        { Metrik: 'AYDA', Nilai: currencyValue(aydaIndicator.value.amount) },
        { Metrik: 'ABA Non-Macet', Nilai: currencyValue(kapSummary.value.antar_bank_aktiva_lancar) },
        { Metrik: 'APYD TKS', Nilai: currencyValue(kapSummary.value.apyd) },
        { Metrik: 'PPKA WD Total', Nilai: currencyValue(kapSummary.value.ppap_wajib_dibentuk) },
        { Metrik: 'PPKA Sistem', Nilai: currencyValue(kapSummary.value.ppap_system) },
        { Metrik: 'PPKA Template Bulan Berjalan', Nilai: currencyValue(kapSummary.value.ppap_rekap_current) },
        { Metrik: 'PPKA Pembanding Bulan Sebelumnya', Nilai: currencyValue(kapSummary.value.ppap_rekap_previous) },
        { Metrik: 'Gap PPKA Bulanan', Nilai: formatSignedRp(kapSummary.value.ppap_gap) },
        { Metrik: 'Gap PPKA Sistem - WD', Nilai: formatSignedRp(kapSummary.value.ppap_system_vs_wd_gap) }
      ] },
      { name: 'Worksheet KAP', rows: kapWorksheetRows.value.map(item => ({
        Section: item.section,
        Kol: item.label,
        NOA: item.noa ?? '-',
        OS: currencyValue(item.baki_debet),
        APYD: currencyValue(item.apyd),
        'Agunan Dikuasai': currencyValue(item.agunan_dikuasai),
        Jumlah: currencyValue(item.jumlah_setelah_agunan),
        'Tarif PPKA WD': item.tarif_ppap_wd,
        'PPKA WD': currencyValue(item.ppap_wajib_dibentuk),
        'PPKA Sistem': currencyValue(item.ppap_system)
      })) },
      { name: 'Rekomendasi', rows: kapRecommendations.value.map((item, index) => ({ No: index + 1, Rekomendasi: item })) },
      { name: 'Trend Prudential', rows: kapPrudentialTrend.value.map(item => ({
        Tahun: item.tahun,
        Bulan: item.label,
        Database: item.source_database || '-',
        Tersedia: item.available ? 'Ya' : 'Tidak',
        'Rasio KAP': item.available ? percentageValue(item.kap_ratio) : '-',
        APYD: item.available ? currencyValue(item.apyd) : '-',
        'APYD Ratio': item.available ? percentageValue(item.apyd_ratio) : '-',
        'PPKA WD': item.available ? currencyValue(item.ppap_wajib_dibentuk) : '-',
        'PPKA Sistem': item.available ? currencyValue(item.ppap_system) : '-',
        'Total PPKA Bulanan': item.available ? currencyValue(item.total_ppap_bulanan ?? item.ppap_rekap_current ?? item.ppap_system) : '-',
        'Gap PPKA Bulanan': item.available ? formatSignedRp(item.ppap_gap) : '-',
        'Net Exposure Agunan': item.available ? currencyValue(item.net_exposure_agunan) : '-',
        'KAP Delta': item.available ? percentageValue(item.kap_delta) : '-'
      })) },
      { name: 'Anomaly Detector', rows: kapAnomalyItems.value.map(item => ({
        Severity: anomalySeverityLabel(item.severity),
        Judul: item.title,
        Metrik: item.metric,
        Nilai: typeof item.value === 'number' ? item.value : item.value,
        Pesan: item.message,
        Aksi: item.action
      })) },
      { name: 'Rekonsiliasi Sumber', rows: kapSourceReconciliationRows.value.map(item => ({
        Komponen: item.component,
        Tabel: item.source_table,
        Field: item.source_field,
        Basis: item.basis,
        Nominal: currencyValue(item.amount),
        Status: auditStatusLabel(item.status),
        Catatan: item.note
      })) },
      { name: 'Data Quality Checks', rows: kapDataQualityRows.value.map(item => ({
        Severity: anomalySeverityLabel(item.severity),
        Issue: item.issue,
        Kontrak: item.nokontrak,
        CIF: item.nocif,
        Nasabah: item.nama,
        Produk: item.produk,
        Cabang: item.cabang,
        AO: item.nama_ao,
        Kol: item.colbaru,
        OS: currencyValue(item.os_pokok),
        'PPKA Sistem': currencyValue(item.ppap_system),
        'Agunan TOFJAMIN': currencyValue(item.collateral_from_tofjamin),
        'Agunan TOFLMB': currencyValue(item.collateral_from_toflmb)
      })) },
      { name: 'PPKA Operasional', rows: filteredPpkaOperationalRows.value.map(item => ({
        Kontrak: item.nokontrak,
        CIF: item.nocif || '-',
        Nasabah: item.nama,
        AO: item.nmao || '-',
        Kol: kolektibilitasLabel(item.colbaru),
        'Hari Tunggakan': item.haritgk ?? 0,
        Outstanding: currencyValue(item.osmdlc),
        'Agunan PPKA': currencyValue(item.total_agunan_ppka),
        'PPKA Sistem': currencyValue(item.ppap_system),
        'PPKA Dihitung': currencyValue(item.ppap_seharusnya),
        'PPKA Manual/Berlaku': currencyValue(item.ppap_manual),
        'Manual Adjusted': item.is_manual_adjusted ? 'Ya' : 'Tidak'
      })) },
      { name: 'Detail GAP PPKA', rows: kapPpapGapRows.value.map(item => ({
        Kontrak: item.nokontrak,
        CIF: item.nocif,
        Nasabah: item.nama,
        Produk: item.produk,
        AO: item.nama_ao,
        Cabang: item.cabang,
        'Kol Sebelumnya': item.col_previous || '-',
        'Kol Berjalan': item.col_current || '-',
        'OS Sebelumnya': currencyValue(item.os_previous),
        'OS Berjalan': currencyValue(item.os_current),
        'Nilai Likuidasi': currencyValue(item.nilai_likuidasi),
        'Jenis Agunan': item.jns_agunan || '-',
        'Jenis Ikatan': item.jns_ikatan || '-',
        'Gol Jamin': item.gol_jamin || '-',
        'PPKA Sebelumnya': currencyValue(item.ppap_previous),
        'PPKA Berjalan': currencyValue(item.ppap_current),
        'GAP PPKA': formatSignedRp(item.ppap_gap),
        Status: item.movement_status || '-'
      })) },
      { name: 'PPKA Shortfall', rows: kapPpapShortfallAccounts.value.map(item => ({
        Kontrak: item.nokontrak,
        Nasabah: item.nama,
        Produk: item.produk,
        Kol: kolektibilitasLabel(item.colbaru),
        AO: item.nama_ao,
        Cabang: item.cabang,
        OS: currencyValue(item.os_pokok),
        Agunan: currencyValue(item.collateral_weighted),
        Jumlah: currencyValue(item.net_exposure_agunan),
        'PPKA WD': currencyValue(item.ppap_wajib_dibentuk),
        'PPKA Sistem': currencyValue(item.ppap_system),
        Gap: formatSignedRp(item.ppap_gap)
      })) },
      { name: 'Top APYD', rows: kapApydContributors.value.map(item => ({
        Kontrak: item.nokontrak,
        Nasabah: item.nama,
        AO: item.nama_ao,
        Kol: kolektibilitasLabel(item.colbaru),
        OS: currencyValue(item.os_pokok),
        APYD: currencyValue(item.apyd_tks)
      })) },
      { name: 'Rincian ABA', rows: kapAbaRows.value.map(item => ({
        'No SBB': item.nosbb,
        'Nama Akun': item.nmsbb,
        Kol: item.coll || '-',
        Saldo: currencyValue(item.sahirrp),
        Status: item.prudential_status || '-'
      })) },
      { name: 'Over Reserved', rows: kapPpapOverReservedAccounts.value.map(item => ({
        Kontrak: item.nokontrak,
        Nasabah: item.nama,
        Produk: item.produk,
        AO: item.nama_ao,
        Kol: kolektibilitasLabel(item.colbaru),
        'PPKA WD': currencyValue(item.ppap_wajib_dibentuk),
        'PPKA Sistem': currencyValue(item.ppap_system),
        Surplus: formatSignedRp(item.ppap_gap)
      })) }
    ]
  }

  return [
    { name: 'Risk Summary', rows: [
      ...commonSummary,
      { Metrik: 'PKR', Nilai: percentageValue(pkrSummary.value.pkr_ratio) },
      { Metrik: 'OS PKR', Nilai: currencyValue(pkrSummary.value.pkr_os) },
      { Metrik: 'NOA PKR', Nilai: pkrSummary.value.pkr_noa || 0 },
      { Metrik: 'OS Kol 1 Restrukturisasi/Valid', Nilai: currencyValue(pkrSummary.value.restructured_lancar_os) },
      { Metrik: 'OS Kol 2-5', Nilai: currencyValue(pkrSummary.value.pkr_non_lancar_os) },
      { Metrik: 'Kol 2 Watch', Nilai: currencyValue(pkrSummary.value.watch_kol2_os) },
      { Metrik: 'O/S Restrukturisasi', Nilai: currencyValue(restruGuard.value.total_os_restru) },
      { Metrik: 'Vintage Failure Rate', Nilai: percentageValue(restruGuard.value.vintage_failure_rate) }
    ] },
    { name: 'PKR Detail', rows: pkrRows.value.map(item => ({
      Periode: item.periode,
      Cabang: item.kdloc,
      Segmen: item.segmen,
      Keterangan: item.ket || '-',
      Kol: kolektibilitasLabel(item.colbaru),
      'OS Semua Data': currencyValue(item.osmdlc_semua_data),
      'NOA Semua Data': item.rec_semua_data,
      'OS PKR': currencyValue(item.os_pkr),
      'NOA PKR': item.noa_pkr,
      'Selisih OS': currencyValue(item.selisih_osmdlc),
      'Selisih NOA': item.selisih_rec
    })) },
    { name: 'Trend PKR Bulanan', rows: pkrTrendRows.value.map(item => ({
      Tahun: item.tahun,
      Bulan: item.label,
      Database: item.source_database || '-',
      Tersedia: item.available ? 'Ya' : 'Tidak',
      'PKR Ratio': item.available ? percentageValue(item.pkr_ratio) : '-',
      'OS PKR': item.available ? currencyValue(item.pkr_os) : '-',
      'NOA PKR': item.available ? item.pkr_noa : '-',
      'Kol 1 Restrukturisasi/Valid': item.available ? currencyValue(item.restructured_lancar_os) : '-',
      'Kol 2 Watch': item.available ? currencyValue(item.watch_kol2_os) : '-',
      'Kol 2-5': item.available ? currencyValue(item.pkr_non_lancar_os) : '-',
      'PKR Delta': item.available ? percentageValue(item.pkr_delta) : '-'
    })) },
    { name: 'Detail Kontrak PKR', rows: pkrDetailRows.value.map(item => ({
      Periode: item.periode,
      Bucket: item.pkr_bucket,
      Kontrak: item.nokontrak,
      CIF: item.nocif,
      Nasabah: item.nama,
      Cabang: item.cabang,
      Segmen: item.segmen_nama,
      AO: item.nama_ao,
      Produk: item.produk,
      Kol: kolektibilitasLabel(item.colbaru),
      OS: currencyValue(item.os_pokok),
      'Tunggakan Pokok': currencyValue(item.tunggakan_pokok),
      'Tunggakan Margin': currencyValue(item.tunggakan_margin),
      PPKA: currencyValue(item.ppap_system)
    })) },
    { name: 'Action Priority Queue', rows: actionPriorityQueue.value.map((item, index) => ({
      Prioritas: index + 1,
      Severity: item.severity,
      Score: item.score,
      Sumber: item.source,
      Sinyal: (item.signals || [item.signal]).join(' | '),
      Kontrak: item.nokontrak || '-',
      Nasabah: item.nama || '-',
      AO: item.ao || '-',
      Cabang: item.cabang || '-',
      Kol: item.kol ? kolektibilitasLabel(item.kol) : '-',
      Exposure: currencyValue(item.amount),
      Status: actionWorkflowStatusLabel(item.workflow_status),
      Owner: item.workflow_owner || '-',
      'Due Date': item.workflow_due_date || '-',
      Overdue: item.workflow_overdue ? 'Ya' : 'Tidak',
      Catatan: item.workflow_note || '-',
      'Rekomendasi Aksi': item.action
    })) },
    { name: 'Watchlist Alerts', rows: ewsWatchlistRows.value.map(item => ({
      Kontrak: item.nokontrak,
      Nasabah: item.nama,
      AO: item.nama_ao,
      Kol: kolektibilitasLabel(item.colbaru),
      Severity: item.severity,
      Score: item.severity_score,
      OS: currencyValue(item.os),
      'Hari Tunggakan': item.days_past_due,
      Agunan: currencyValue(item.collateral),
      PPKA: currencyValue(item.ppka),
      'Cover Ratio': percentageValue(item.cover_ratio),
      'Net Uncovered': currencyValue(item.uncovered),
      Risiko: item.risk_level || item.alert_level || '-',
      'Workflow Status': actionWorkflowStatusLabel(item.workflow_status),
      Owner: item.workflow_owner || '-',
      'Due Date': item.workflow_due_date || '-',
      Overdue: item.workflow_overdue ? 'Ya' : 'Tidak',
      Catatan: item.workflow_note || '-',
      'Rekomendasi Aksi': item.recommended_action
    })) },
    { name: 'Sector Data', rows: (qualityData.value.sector_data || []).map(item => ({
      Sektor: item.sektor,
      OS: currencyValue(item.total_os),
      NPF: currencyValue(item.npf_os),
      NOA: item.noa
    })) },
    { name: 'Product Data', rows: (qualityData.value.product_data || []).map(item => ({
      Produk: item.produk,
      OS: currencyValue(item.total_os),
      NPF: currencyValue(item.npf_os),
      NOA: item.noa
    })) }
  ]
}

const downloadActiveTabExcel = () => {
  if (!canExport.value) return
  isExporting.value = true
  exportError.value = ''
  try {
    const workbook = XLSX.utils.book_new()
    workbook.Props = {
      Title: exportReportTitle.value,
      Subject: `${tabs[activeTab.value]?.name || 'Quality'} - ${exportPeriodLabel.value}`,
      Author: 'Monitoring Dashboard',
      Company: 'MCI',
      CreatedDate: new Date()
    }
    XLSX.utils.book_append_sheet(workbook, buildMetadataWorksheet(), '00 Metadata')
    buildExportSheets().forEach((sheet, index) => {
      XLSX.utils.book_append_sheet(workbook, buildStandardWorksheet(sheet, index), sheetName(`${String(index + 1).padStart(2, '0')} ${sheet.name}`))
    })
    XLSX.writeFile(workbook, `${exportFileStem.value}.xlsx`)
  } catch (error) {
    console.error(error)
    exportError.value = 'Gagal membuat file Excel. Coba ulangi setelah data selesai dimuat.'
  } finally {
    isExporting.value = false
  }
}

const downloadActiveTabPdf = () => {
  if (!canExport.value) return
  isExporting.value = true
  exportError.value = ''
  try {
    const doc = new jsPDF({ orientation: 'landscape', unit: 'mm', format: 'a4' })
    const pageWidth = doc.internal.pageSize.getWidth()
    const pageHeight = doc.internal.pageSize.getHeight()
    const sheets = buildExportSheets()
    const generatedAt = exportGeneratedAt()
    const drawHeader = () => {
      doc.setFillColor(15, 23, 42)
      doc.rect(0, 0, pageWidth, 30, 'F')
      doc.setFillColor(13, 148, 136)
      doc.rect(0, 29, pageWidth, 1.2, 'F')
      doc.setTextColor(255, 255, 255)
      doc.setFont('helvetica', 'bold')
      doc.setFontSize(15)
      doc.text(exportReportTitle.value, 14, 12)
      doc.setFont('helvetica', 'normal')
      doc.setFontSize(8.5)
      doc.text(doc.splitTextToSize(activeFilterText.value, 170), 14, 20)
      doc.setFont('helvetica', 'bold')
      doc.text(exportPeriodLabel.value, pageWidth - 14, 12, { align: 'right' })
      doc.setFont('helvetica', 'normal')
      doc.text(`Generate: ${generatedAt}`, pageWidth - 14, 20, { align: 'right' })
    }
    const drawFooter = (page, pageCount) => {
      doc.setDrawColor(226, 232, 240)
      doc.line(14, pageHeight - 10, pageWidth - 14, pageHeight - 10)
      doc.setFontSize(7.5)
      doc.setTextColor(100, 116, 139)
      doc.text('Nominal penuh tanpa pembulatan; persentase maksimal 2 desimal.', 14, pageHeight - 5)
      doc.text(`Halaman ${page}/${pageCount}`, pageWidth - 14, pageHeight - 5, { align: 'right' })
    }
    doc.setProperties({
      title: exportReportTitle.value,
      subject: `${tabs[activeTab.value]?.name || 'Quality'} - ${exportPeriodLabel.value}`,
      author: 'Monitoring Dashboard',
      creator: 'Monitoring Dashboard'
    })
    drawHeader()
    let y = 40

    sheets.forEach((sheet, index) => {
      const rows = normalizedSheetRows(sheet)
      const columns = Object.keys(rows[0] || { Informasi: '' })
      const body = rows.map(row => columns.map(column => row[column] ?? '-'))
      if (index > 0 && y > 172) {
        doc.addPage()
        drawHeader()
        y = 40
      }
      doc.setTextColor(15, 23, 42)
      doc.setFont('helvetica', 'bold')
      doc.setFontSize(11)
      doc.text(`${sheet.name} (${rows.length} baris)`, 14, y)
      autoTable(doc, {
        startY: y + 5,
        head: [columns],
        body,
        styles: { fontSize: 7, cellPadding: 2, overflow: 'linebreak', lineColor: [226, 232, 240], lineWidth: 0.1 },
        headStyles: { fillColor: [15, 118, 110], textColor: 255, fontStyle: 'bold' },
        alternateRowStyles: { fillColor: [248, 250, 252] },
        margin: { top: 38, left: 14, right: 14, bottom: 16 },
        theme: 'grid',
        showHead: 'everyPage',
        didDrawPage: () => drawHeader()
      })
      y = doc.lastAutoTable.finalY + 12
    })

    const pageCount = doc.internal.getNumberOfPages()
    for (let page = 1; page <= pageCount; page++) {
      doc.setPage(page)
      drawFooter(page, pageCount)
    }
    doc.save(`${exportFileStem.value}.pdf`)
  } catch (error) {
    console.error(error)
    exportError.value = 'Gagal membuat file PDF. Coba ulangi setelah data selesai dimuat.'
  } finally {
    isExporting.value = false
  }
}

const resetTablePages = () => {
  ckpnIndividualPage.value = 1
  aoMatrixPage.value = 1
  kapShortfallPage.value = 1
  kapApydPage.value = 1
  kapAbaPage.value = 1
  kapOverReservedPage.value = 1
  alertsPage.value = 1
  pkrPage.value = 1
  pkrDetailPage.value = 1
  actionQueuePage.value = 1
  ppkaOperationalPage.value = 1
}

const fetchCabangs = async () => {
  try {
    const res = await axios.get('/api/v1/financing/cabangs')
    if (res.data.success) cabangs.value = res.data.data
  } catch (e) { console.error(e) }
}

const fetchSegmens = async () => {
  try {
    const res = await axios.get('/api/v1/financing/segmens')
    if (res.data.success) {
      segmens.value = ['Semua Segmen', ...res.data.data.map(item => item.ket)]
    }
  } catch (e) { console.error(e) }
}

const fetchPpkaSettings = async () => {
  try {
    const res = await axios.get('/api/v1/admin/settings')
    const enabled = res.data?.data?.ppka_manual_enabled
    manualAdjustmentEnabled.value = enabled === true || enabled === 'true'
  } catch (e) {
    console.error(e)
    manualAdjustmentEnabled.value = false
  }
}

const fetchPpkaOperational = async () => {
  ppkaOperationalLoading.value = true
  try {
    const res = await axios.get('/api/v1/financing/penyelesaian/ppka')
    if (res.data.success) {
      ppkaOperationalRows.value = res.data.data || []
      ppkaOperationalSummary.value = {
        ...ppkaOperationalSummary.value,
        ...(res.data.summary || {})
      }
      ppkaOperationalPage.value = 1
    }
  } catch (e) {
    console.error(e)
  } finally {
    ppkaOperationalLoading.value = false
  }
}

const openPpkaAdjustmentDialog = (item) => {
  ppkaAdjustmentForm.value = {
    nokontrak: item?.nokontrak || '',
    nominal_ppap: item?.ppap_manual ?? item?.ppap_system ?? '',
    alasan: ''
  }
  ppkaAdjustmentDialog.value = true
}

const submitPpkaAdjustment = async () => {
  if (!ppkaAdjustmentForm.value.nokontrak || ppkaAdjustmentForm.value.nominal_ppap === '') return
  isSavingPpkaAdjustment.value = true
  try {
    const res = await axios.post('/api/v1/financing/penyelesaian/ppka-adjustment', {
      nokontrak: ppkaAdjustmentForm.value.nokontrak,
      nominal_ppap: ppkaAdjustmentForm.value.nominal_ppap,
      alasan: ppkaAdjustmentForm.value.alasan || 'Penyesuaian melalui Quality & Risk Console'
    })
    if (res.data.success) {
      ppkaAdjustmentDialog.value = false
      await fetchPpkaOperational()
      await fetchQualityData()
    }
  } catch (e) {
    console.error(e)
  } finally {
    isSavingPpkaAdjustment.value = false
  }
}

const actionWorkflowStorageKey = () => `quality-action-workflows:${filters.value.tahun}:${filters.value.bulan}`

const loadLocalActionWorkflows = () => {
  try {
    return JSON.parse(localStorage.getItem(actionWorkflowStorageKey()) || '{}')
  } catch (e) {
    console.error(e)
    return {}
  }
}

const saveLocalActionWorkflow = (workflow) => {
  const workflows = loadLocalActionWorkflows()
  const nextWorkflows = {
    ...workflows,
    [workflow.action_key]: workflow
  }
  localStorage.setItem(actionWorkflowStorageKey(), JSON.stringify(nextWorkflows))
  actionWorkflows.value = nextWorkflows
}

const fetchActionWorkflows = async () => {
  try {
    const res = await axios.get('/api/v1/financing/quality-action-workflows', {
      params: {
        tahun: filters.value.tahun,
        bulan: filters.value.bulan
      }
    })
    if (res.data.success) {
      const indexed = res.data.indexed || {}
      actionWorkflows.value = Object.keys(indexed).length ? indexed : loadLocalActionWorkflows()
    }
  } catch (e) {
    console.error(e)
    actionWorkflows.value = loadLocalActionWorkflows()
  }
}

const openActionWorkflowDialog = (item) => {
  selectedActionWorkflowItem.value = item
  actionWorkflowForm.value = {
    status: item.workflow_status || 'open',
    owner: item.workflow_owner || '',
    due_date: item.workflow_due_date || '',
    note: item.workflow_note || '',
    reviewed_by: item.workflow_reviewed_by || ''
  }
  actionWorkflowDialog.value = true
}

const saveActionWorkflow = async () => {
  if (!selectedActionWorkflowItem.value) return
  isSavingActionWorkflow.value = true
  const item = selectedActionWorkflowItem.value

  try {
    const res = await axios.post('/api/v1/financing/quality-action-workflows', {
      period_year: filters.value.tahun,
      period_month: filters.value.bulan,
      action_key: item.key,
      nokontrak: item.nokontrak || null,
      nama: item.nama || null,
      source: item.source || null,
      signals: item.signals || [item.signal],
      severity: item.severity,
      score: item.score || 0,
      exposure: item.amount || 0,
      status: actionWorkflowForm.value.status,
      owner: actionWorkflowForm.value.owner || null,
      due_date: actionWorkflowForm.value.due_date || null,
      note: actionWorkflowForm.value.note || null,
      reviewed_by: actionWorkflowForm.value.reviewed_by || null
    })

    if (res.data.success) {
      actionWorkflows.value = {
        ...actionWorkflows.value,
        [res.data.data.action_key]: res.data.data
      }
      actionWorkflowDialog.value = false
    }
  } catch (e) {
    console.error(e)
    saveLocalActionWorkflow({
      action_key: item.key,
      nokontrak: item.nokontrak || null,
      nama: item.nama || null,
      source: item.source || null,
      signals: item.signals || [item.signal],
      severity: item.severity,
      score: item.score || 0,
      exposure: item.amount || 0,
      status: actionWorkflowForm.value.status,
      owner: actionWorkflowForm.value.owner || '',
      due_date: actionWorkflowForm.value.due_date || '',
      note: actionWorkflowForm.value.note || '',
      reviewed_by: actionWorkflowForm.value.reviewed_by || '',
      completed_at: actionWorkflowForm.value.status === 'done' ? new Date().toISOString() : null,
      local_only: true
    })
    actionWorkflowDialog.value = false
  } finally {
    isSavingActionWorkflow.value = false
  }
}

const fetchQualityData = async () => {
  isLoading.value = true
  try {
    const res = await axios.get('/api/v1/financing/quality-analytics', {
      params: {
        cabang: selectedCabang.value || '',
        tahun: filters.value.tahun,
        bulan: filters.value.bulan,
        segmen: filters.value.segmen === 'Semua Segmen' ? '' : filters.value.segmen
      }
    })
    if (res.data.success) {
      qualityData.value = { ...qualityData.value, ...res.data.data }
      await fetchActionWorkflows()
      resetTablePages()
    }
  } catch (e) { console.error(e) }
  finally { isLoading.value = false }
}

onMounted(() => { fetchCabangs(); fetchSegmens(); fetchPpkaSettings(); fetchPpkaOperational(); fetchQualityData(); })
watch([selectedCabang, filters], fetchQualityData, { deep: true })
watch([ppkaOperationalSearch, ppkaOperationalAo], () => { ppkaOperationalPage.value = 1 })
</script>

<template>
  <div class="quality-console">
    <Head title="RGEC Quality & Risk Console" />

    <!-- ==========================================
         HERO HEADER
    ========================================== -->
    <div class="hero-header">
      <div class="hero-bg-decoration"></div>
      <div class="hero-content max-w-7xl mx-auto px-6 py-8">

        <!-- Top Row: Identity + PK Badge -->
        <div class="d-flex flex-wrap justify-space-between align-start gap-6 mb-8">
          <div class="d-flex align-start gap-5">
            <div class="hero-icon-box">
              <v-icon icon="ri-scales-3-line" size="32" color="white"></v-icon>
            </div>
            <div>
              <div class="d-flex align-center gap-2 mb-2">
                <span class="badge-islamic">
                  <v-icon icon="ri-bank-line" size="11" class="mr-1"></v-icon>
                  ISLAMIC BANKING
                </span>
                <span class="badge-compliant">
                  <v-icon icon="ri-verified-badge-line" size="11" class="mr-1"></v-icon>
                  OJK / PSAK 71 Compliant
                </span>
              </div>
              <h1 class="hero-title">Kualitas Aktiva &amp; Mitigasi Risiko</h1>
              <p class="hero-subtitle">Konsol Konsentrasi Risiko, Profil RGEC, dan Pencadangan Kerugian BPRS.</p>
            </div>
          </div>

          <!-- PK Rating Badge -->
          <div class="pk-badge-wrapper">
            <div class="pk-label">Peringkat Komposit OJK</div>
            <div :class="['pk-badge', `pk-gradient-${summary.composite_score <= 2 ? 'sehat' : summary.composite_score === 3 ? 'cukup' : 'kurang'}`]">
              <v-icon :icon="pkScoreData.icon" size="22" color="white"></v-icon>
              <span class="pk-text">{{ pkScoreData.text }}</span>
            </div>
          </div>
        </div>

        <!-- Filter Bar -->
        <div class="filter-bar">
          <div class="filter-group">
            <div class="filter-icon-wrap">
              <v-icon icon="ri-calendar-2-line" size="16" color="#64748b"></v-icon>
            </div>
            <v-select
              v-model="filters.tahun"
              :items="years"
              label="Tahun"
              density="compact"
              variant="solo"
              flat
              hide-details
              class="filter-select"
              style="min-width: 110px"
            ></v-select>
          </div>

          <div class="filter-divider"></div>

          <div class="filter-group">
            <div class="filter-icon-wrap">
              <v-icon icon="ri-time-line" size="16" color="#64748b"></v-icon>
            </div>
            <v-select
              v-model="filters.bulan"
              :items="months"
              item-title="title"
              item-value="value"
              label="Bulan Tutup Buku"
              density="compact"
              variant="solo"
              flat
              hide-details
              class="filter-select"
              style="min-width: 160px"
            ></v-select>
          </div>

          <div class="filter-divider"></div>

          <div class="filter-group flex-grow-1">
            <div class="filter-icon-wrap">
              <v-icon icon="ri-building-4-line" size="16" color="#64748b"></v-icon>
            </div>
            <v-select
              v-model="selectedCabang"
              :items="cabangs"
              item-title="nama"
              item-value="kdloc"
              label="Konsolidasi / Cabang"
              density="compact"
              variant="solo"
              flat
              hide-details
              clearable
              class="filter-select"
              style="min-width: 240px"
            ></v-select>
          </div>

          <div class="filter-divider"></div>

          <div class="filter-group">
            <div class="filter-icon-wrap">
              <v-icon icon="ri-user-star-line" size="16" color="#64748b"></v-icon>
            </div>
            <v-select
              v-model="filters.segmen"
              :items="segmens"
              label="Segmen"
              density="compact"
              variant="solo"
              flat
              hide-details
              class="filter-select"
              style="min-width: 150px"
            ></v-select>
          </div>
        </div>

        <!-- Active Filter Info -->
        <div class="filter-info-bar mt-3">
          <v-icon icon="ri-information-line" size="13" color="#94a3b8" class="mr-1"></v-icon>
          <span>{{ activeFilterText }}</span>
        </div>

      </div>
    </div>

    <!-- ==========================================
         MAIN CONTENT
    ========================================== -->
    <div class="main-content max-w-7xl mx-auto px-6 pt-7 pb-16">

      <!-- Tab Navigation -->
      <div class="tab-toolbar mb-8">
        <div class="tab-nav">
          <button
            v-for="(tab, idx) in tabs"
            :key="idx"
            :class="['tab-btn', { 'tab-btn--active': activeTab === idx }]"
            @click="activeTab = idx"
          >
            <v-icon :icon="tab.icon" size="17" class="mr-2"></v-icon>
            {{ tab.name }}
          </button>
        </div>
        <div class="tab-export-actions">
          <button
            type="button"
            class="export-btn export-btn--excel"
            :disabled="!canExport"
            :aria-busy="isExporting"
            @click="downloadActiveTabExcel"
          >
            <v-icon :icon="isExporting ? 'ri-loader-4-line' : 'ri-file-excel-2-line'" size="16" :class="{ 'export-spin': isExporting }"></v-icon>
            {{ isExporting ? 'Menyiapkan...' : 'Download Excel' }}
          </button>
          <button
            type="button"
            class="export-btn export-btn--pdf"
            :disabled="!canExport"
            :aria-busy="isExporting"
            @click="downloadActiveTabPdf"
          >
            <v-icon :icon="isExporting ? 'ri-loader-4-line' : 'ri-file-pdf-2-line'" size="16" :class="{ 'export-spin': isExporting }"></v-icon>
            {{ isExporting ? 'Menyiapkan...' : 'Download PDF' }}
          </button>
          <span v-if="exportError" class="export-error">
            <v-icon icon="ri-error-warning-line" size="14"></v-icon>
            {{ exportError }}
          </span>
        </div>
      </div>

      <v-window v-model="activeTab" class="overflow-visible" :touch="false">

        <!-- ==================================
             TAB 1: RGEC & RISK PROFILE
        ================================== -->
        <v-window-item :value="0">

          <!-- KPI Cards -->
          <v-row class="mb-6">
            <v-col cols="12" sm="6" lg="3">
              <v-card class="rounded-xl border shadow-sm transition-swing" elevation="0" style="position: relative; overflow: hidden;">
                <div style="position: absolute; top: -20px; right: -20px; width: 120px; height: 120px; opacity: 0.08;">
                  <v-icon icon="ri-wallet-3-line" size="120" color="#059669" />
                </div>
                <v-card-text class="pa-5" style="position: relative; z-index: 1;">
                  <div class="d-flex justify-space-between align-start">
                    <div>
                      <p class="text-caption font-weight-bold text-uppercase tracking-widest mb-1" style="color: #64748B; font-family: 'Inter', sans-serif;">TOTAL PEMBIAYAAN</p>
                      <h2 class="font-weight-bold mb-2" style="color: #059669; font-family: 'Plus Jakarta Sans', sans-serif; line-height: 1.14; font-size: clamp(1.02rem, 1.15vw, 1.34rem); letter-spacing: -0.035em; white-space: nowrap;">
                        <span class="fin-money-exact">{{ formatRpUtuh(summary.total_os) }}</span>
                      </h2>
                      <p class="text-caption text-medium-emphasis mb-0" style="font-family: 'Inter', sans-serif;">Porsi Bagi Hasil: <strong>{{ formatTruncatedPercentage(summary.porsi_bagi_hasil) }}</strong></p>
                    </div>
                  </div>
                </v-card-text>
              </v-card>
            </v-col>

            <v-col cols="12" sm="6" lg="3">
              <v-card class="rounded-xl border shadow-sm transition-swing" elevation="0" style="position: relative; overflow: hidden;">
                <div style="position: absolute; top: -20px; right: -20px; width: 120px; height: 120px; opacity: 0.08;">
                  <v-icon icon="ri-error-warning-line" size="120" :color="(summary.npf_gross || 0) > 5 ? '#e11d48' : '#059669'" />
                </div>
                <v-card-text class="pa-5" style="position: relative; z-index: 1;">
                  <div class="d-flex justify-space-between align-start">
                    <div>
                      <p class="text-caption font-weight-bold text-uppercase tracking-widest mb-1" style="color: #64748B; font-family: 'Inter', sans-serif;">NPF GROSS</p>
                      <h2 class="text-h4 font-weight-bold mb-2" :style="{ color: (summary.npf_gross || 0) > 5 ? '#e11d48' : '#059669', fontFamily: 'Plus Jakarta Sans, sans-serif', lineHeight: 1.2 }">
                        {{ formatTruncatedPercentage(summary.npf_gross) }}                      </h2>
                      <p class="text-caption text-medium-emphasis mb-0" style="font-family: 'Inter', sans-serif;">NPF Net: <strong>{{ formatTruncatedPercentage(summary.npf_net) }}</strong></p>
                    </div>
                  </div>
                </v-card-text>
              </v-card>
            </v-col>

            <v-col cols="12" sm="6" lg="3">
              <v-card class="rounded-xl border shadow-sm transition-swing" elevation="0" style="position: relative; overflow: hidden;">
                <div style="position: absolute; top: -20px; right: -20px; width: 120px; height: 120px; opacity: 0.08;">
                  <v-icon icon="ri-bank-card-line" size="120" :color="(summary.fdr || 0) > 120 ? '#e11d48' : (summary.fdr || 0) > 100 ? '#d97706' : '#0284c7'" />
                </div>
                <v-card-text class="pa-5" style="position: relative; z-index: 1;">
                  <div class="d-flex justify-space-between align-start">
                    <div>
                      <p class="text-caption font-weight-bold text-uppercase tracking-widest mb-1" style="color: #64748B; font-family: 'Inter', sans-serif;">FDR TKS</p>
                      <h2 class="text-h4 font-weight-bold mb-2" :style="{ color: (summary.fdr || 0) > 120 ? '#e11d48' : (summary.fdr || 0) > 100 ? '#d97706' : '#0284c7', fontFamily: 'Plus Jakarta Sans, sans-serif', lineHeight: 1.2 }">
                        {{ formatTruncatedPercentage(summary.fdr) }}
                      </h2>
                      <p class="text-caption text-medium-emphasis mb-1" style="font-family: 'Inter', sans-serif;">Pembiayaan / DPK + Modal Inti + KBL</p>
                      <p class="mb-0" style="font-family: 'Inter', sans-serif; color: #64748b; font-size: 10px; line-height: 1.35;">
                        {{ formatRpUtuh(summary.fdr_components?.total_pembiayaan) }} /
                        {{ formatRpUtuh(fdrDenominatorTks) }}
                      </p>
                    </div>
                  </div>
                </v-card-text>
              </v-card>
            </v-col>

            <v-col cols="12" sm="6" lg="3">
              <v-card class="rounded-xl border shadow-sm transition-swing" elevation="0" style="position: relative; overflow: hidden;">
                <div style="position: absolute; top: -20px; right: -20px; width: 120px; height: 120px; opacity: 0.08;">
                  <v-icon icon="ri-water-flash-line" size="120" color="#4f46e5" />
                </div>
                <v-card-text class="pa-5" style="position: relative; z-index: 1;">
                  <div class="d-flex justify-space-between align-start">
                    <div>
                      <p class="text-caption font-weight-bold text-uppercase tracking-widest mb-1" style="color: #64748B; font-family: 'Inter', sans-serif;">FDR V2 (DPK)</p>
                      <h2 class="text-h4 font-weight-bold mb-2" style="color: #4f46e5; font-family: 'Plus Jakarta Sans', sans-serif; line-height: 1.2;">{{ formatTruncatedPercentage(summary.fdr_v2) }}</h2>
                      <p class="text-caption text-medium-emphasis mb-1" style="font-family: 'Inter', sans-serif;">Pembiayaan / DPK</p>
                      <p class="mb-0" style="font-family: 'Inter', sans-serif; color: #64748b; font-size: 10px; line-height: 1.35;">
                        {{ formatRpUtuh(summary.fdr_components?.total_pembiayaan) }} /
                        {{ formatRpUtuh(summary.fdr_components?.dpk) }}
                      </p>
                    </div>
                  </div>
                </v-card-text>
              </v-card>
            </v-col>
          </v-row>

          <!-- Charts Row -->
          <v-row>
            <!-- NPF Trend Chart -->
            <v-col cols="12" lg="8" class="d-flex flex-column">
              <div class="content-card flex-grow-1">
                <div class="content-card__header">
                  <div>
                    <div class="content-card__title">Tren Pembiayaan Bermasalah (NPF)</div>
                    <div class="content-card__subtitle" v-if="lastAvailableTrendMonth">
                      Pergerakan Gross vs Net - Jan s/d
                      <strong :style="hasTrendGap ? 'color: #d97706;' : 'color: #059669;'">
                        {{ lastAvailableTrendMonth }} {{ filters.tahun }}
                      </strong>
                      <span v-if="hasTrendGap" class="ml-2">
                        <em style="color: #d97706; font-size: 11px;">(data EOM s/d {{ lastAvailableTrendMonth }})</em>
                      </span>
                    </div>
                    <div class="content-card__subtitle" v-else>Memuat data tren...</div>
                  </div>
                  <div class="d-flex align-center gap-2">
                    <span v-if="hasTrendGap" class="status-chip status-chip--warning">
                      <v-icon icon="ri-error-warning-line" size="11" class="mr-1"></v-icon>
                      Data s/d {{ lastAvailableTrendMonth }}
                    </span>
                    <span class="status-chip status-chip--neutral">
                      <v-icon icon="ri-calendar-check-line" size="12" class="mr-1"></v-icon>
                      {{ filters.tahun }}
                    </span>
                  </div>
                </div>
                <div class="content-card__body pa-2">
                  <div v-if="isLoading" class="chart-loading"><v-progress-circular indeterminate color="#0d9488" size="36"></v-progress-circular></div>
                  <VueApexCharts v-else-if="qualityData.trend && qualityData.trend.length" type="area" height="320" :options="trendChartOpts" :series="trendChartSeries" />
                  <div v-else class="empty-state">
                    <v-icon icon="ri-bar-chart-grouped-line" size="48" color="#cbd5e1" class="mb-3"></v-icon>
                    <p>Data tren historis belum tersedia</p>
                  </div>
                </div>
              </div>
            </v-col>

            <!-- Risk Radar -->
            <v-col cols="12" lg="4" class="d-flex flex-column">
              <div class="content-card flex-grow-1">
                <div class="content-card__accent-top" style="background: linear-gradient(90deg, #6366f1, #8b5cf6);"></div>
                <div class="content-card__header">
                  <div>
                    <div class="content-card__title">Pemetaan Risiko Inheren</div>
                    <div class="content-card__subtitle">Analisis 5 Pilar Risiko Perbankan</div>
                  </div>
                  <div class="icon-badge" style="background: #eef2ff; color: #4f46e5;">
                    <v-icon icon="ri-radar-line" size="18"></v-icon>
                  </div>
                </div>
                <div class="content-card__body d-flex justify-center align-center" style="min-height: 320px;">
                  <div v-if="isLoading" class="chart-loading"><v-progress-circular indeterminate color="#6366f1" size="36"></v-progress-circular></div>
                  <div v-else class="w-100">
                    <VueApexCharts type="radar" height="320" width="100%" :options="radarChartOpts" :series="radarChartSeries" />
                  </div>
                </div>
              </div>
            </v-col>
          </v-row>

          <!-- Stress Test Panel -->
          <div class="stress-test-panel mt-6">
            <div class="stress-test-panel__bg-icon">
              <v-icon icon="ri-alarm-warning-fill" size="160" color="white"></v-icon>
            </div>
            <div class="stress-test-panel__content">
              <div class="d-flex align-center gap-4 mb-6">
                <div class="stress-test-avatar">
                  <v-icon icon="ri-flask-line" size="26" color="white"></v-icon>
                </div>
                <div>
                  <h3 class="stress-test-title">Simulasi Gagal Bayar Top Obligor (BMPK Stress Test)</h3>
                  <p class="stress-test-desc">Pengujian ketahanan kualitas aset jika debitur raksasa mengalami default seketika.</p>
                </div>
              </div>

              <v-row>
                <v-col cols="12" md="6">
                  <div class="stress-scenario-card stress-scenario-card--clickable" role="button" tabindex="0" @click="openStressDialog('top5')" @keydown.enter="openStressDialog('top5')">
                    <div class="stress-scenario-card__label">
                      <span class="scenario-badge">Skenario 1</span>
                      Top 5 Debitur Macet
                    </div>
                    <div class="d-flex justify-space-between align-end mt-4 mb-4">
                      <div>
                        <div class="stress-sub-label">Exposure (O/S)</div>
                        <div class="stress-value-primary">{{ formatRpSingkat(stressTest.top5_os) }}</div>
                      </div>
                      <div class="text-right">
                        <div class="stress-sub-label">Lonjakan NPF Gross</div>
                        <div class="d-flex align-center gap-2 mt-1">
                          <span class="npf-before">{{ formatTruncatedPercentage(stressTest.npf_gross_now) }}</span>
                          <v-icon icon="ri-arrow-right-line" size="14" color="#fca5a5"></v-icon>
                          <span class="npf-after npf-after--warn">{{ formatTruncatedPercentage(stressTest.npf_if_top5_fail) }}</span>
                        </div>
                      </div>
                    </div>
                    <div class="stress-progress-track">
                      <div class="stress-progress-fill" :style="`width: ${Math.min(stressTest.npf_if_top5_fail * 5, 100)}%; background: linear-gradient(90deg, #f87171, #ef4444);`"></div>
                    </div>
                    <button type="button" class="stress-detail-button mt-4" @click.stop="openStressDialog('top5')">
                      <v-icon icon="ri-user-search-line" size="15" class="mr-1"></v-icon>
                      Lihat detail Top 5 debitur
                    </button>
                  </div>
                </v-col>
                <v-col cols="12" md="6">
                  <div class="stress-scenario-card stress-scenario-card--critical stress-scenario-card--clickable" role="button" tabindex="0" @click="openStressDialog('top10')" @keydown.enter="openStressDialog('top10')">
                    <div class="stress-scenario-card__label">
                      <span class="scenario-badge scenario-badge--critical">Skenario 2</span>
                      Top 10 Debitur Macet
                    </div>
                    <div class="d-flex justify-space-between align-end mt-4 mb-4">
                      <div>
                        <div class="stress-sub-label">Exposure (O/S)</div>
                        <div class="stress-value-primary">{{ formatRpSingkat(stressTest.top10_os) }}</div>
                      </div>
                      <div class="text-right">
                        <div class="stress-sub-label">Lonjakan NPF Gross</div>
                        <div class="d-flex align-center gap-2 mt-1">
                          <span class="npf-before">{{ formatTruncatedPercentage(stressTest.npf_gross_now) }}</span>
                          <v-icon icon="ri-arrow-right-line" size="14" color="#fca5a5"></v-icon>
                          <span class="npf-after npf-after--critical">{{ formatTruncatedPercentage(stressTest.npf_if_top10_fail) }}</span>
                        </div>
                      </div>
                    </div>
                    <div class="stress-progress-track">
                      <div class="stress-progress-fill" :style="`width: ${Math.min(stressTest.npf_if_top10_fail * 5, 100)}%; background: linear-gradient(90deg, #dc2626, #991b1b);`"></div>
                    </div>
                    <button type="button" class="stress-detail-button stress-detail-button--critical mt-4" @click.stop="openStressDialog('top10')">
                      <v-icon icon="ri-user-search-line" size="15" class="mr-1"></v-icon>
                      Lihat detail Top 10 debitur
                    </button>
                  </div>
                </v-col>
              </v-row>
            </div>
          </div>

          <v-dialog v-model="stressDialogOpen" max-width="1180" scrollable>
            <v-card class="stress-detail-dialog" elevation="0">
              <div class="stress-detail-dialog__header">
                <div>
                  <div class="stress-detail-dialog__eyebrow">BMPK Stress Test Detail</div>
                  <h3 class="stress-detail-dialog__title">{{ stressDialogTitle }}</h3>
                  <p class="stress-detail-dialog__subtitle">
                    Daftar debitur terbesar yang diasumsikan default dalam simulasi, lengkap dengan exposure dan atribut monitoring risiko.
                  </p>
                </div>
                <button type="button" class="stress-detail-dialog__close" @click="stressDialogOpen = false" aria-label="Tutup detail debitur">
                  <v-icon icon="ri-close-line" size="20"></v-icon>
                </button>
              </div>

              <div class="stress-detail-summary">
                <div class="stress-detail-summary__item">
                  <span>Jumlah Debitur</span>
                  <strong>{{ stressDialogItems.length }}</strong>
                </div>
                <div class="stress-detail-summary__item">
                  <span>Total Exposure</span>
                  <strong>{{ formatRp(stressDialogExposure) }}</strong>
                </div>
                <div class="stress-detail-summary__item">
                  <span>NPF Gross Simulasi</span>
                  <strong>{{ selectedStressScenario === 'top10' ? formatTruncatedPercentage(stressTest.npf_if_top10_fail) : formatTruncatedPercentage(stressTest.npf_if_top5_fail) }}</strong>
                </div>
              </div>

              <v-card-text class="pa-0">
                <div v-if="!stressDialogItems.length" class="empty-state py-10">
                  <v-icon icon="ri-user-search-line" size="44" color="#cbd5e1" class="mb-3"></v-icon>
                  <p>Data top obligor belum tersedia untuk periode ini.</p>
                </div>

                <div v-else class="stress-detail-table-wrap">
                  <table class="stress-detail-table">
                    <thead>
                      <tr>
                        <th>Rank</th>
                        <th>Debitur</th>
                        <th>Akad / Cabang</th>
                        <th class="text-right">Outstanding</th>
                        <th class="text-right">Tunggakan</th>
                        <th class="text-right">PPKA/CKPN</th>
                        <th class="text-right">Agunan</th>
                        <th>Kualitas</th>
                        <th>Tanggal</th>
                      </tr>
                    </thead>
                    <tbody>
                      <tr v-for="(item, idx) in stressDialogItems" :key="item.nokontrak || idx">
                        <td>
                          <span class="stress-rank-badge">#{{ idx + 1 }}</span>
                        </td>
                        <td>
                          <div class="stress-debtor-name">{{ item.nama || '-' }}</div>
                          <div class="stress-debtor-meta">Kontrak: {{ item.nokontrak || '-' }}  -  CIF: {{ item.nocif || '-' }}</div>
                          <div class="stress-debtor-meta">No Akad: {{ item.noakad || '-' }}</div>
                        </td>
                        <td>
                          <div class="stress-cell-main">{{ item.jenis_akad || '-' }}</div>
                          <div class="stress-debtor-meta">{{ item.cabang || '-' }}  -  {{ item.nama_ao || '-' }}</div>
                          <div class="stress-debtor-meta">{{ item.segmen || '-' }}  -  Sektor {{ item.sekon || '-' }}</div>
                        </td>
                        <td class="text-right">
                          <div class="stress-cell-main">{{ formatRp(item.os) }}</div>
                          <div class="stress-debtor-meta">Plafon {{ formatRp(item.plafon) }}</div>
                          <div class="stress-debtor-meta">Margin O/S {{ formatRp(item.os_margin) }}</div>
                        </td>
                        <td class="text-right">
                          <div class="stress-cell-main">{{ formatRp((parseFloat(item.tunggakan_pokok) || 0) + (parseFloat(item.tunggakan_margin) || 0)) }}</div>
                          <div class="stress-debtor-meta">Pokok {{ formatRp(item.tunggakan_pokok) }}</div>
                          <div class="stress-debtor-meta">Margin {{ formatRp(item.tunggakan_margin) }}</div>
                        </td>
                        <td class="text-right">
                          <div class="stress-cell-main">{{ formatRp(item.ppap) }}</div>
                        </td>
                        <td class="text-right">
                          <div class="stress-cell-main">{{ formatRp(item.nilai_agunan) }}</div>
                        </td>
                        <td>
                          <span class="stress-kol-chip" :class="`stress-kol-chip--${item.colbaru || 'x'}`">{{ kolektibilitasLabel(item.colbaru) }}</span>
                          <div class="stress-debtor-meta mt-1">Hari tunggakan: {{ item.haritgk || 0 }}</div>
                        </td>
                        <td>
                          <div class="stress-debtor-meta">Akad: {{ formatCbsDate(item.tglakad) }}</div>
                          <div class="stress-debtor-meta">Efektif: {{ formatCbsDate(item.tgleff) }}</div>
                          <div class="stress-debtor-meta">Jatuh tempo: {{ formatCbsDate(item.tglexp) }}</div>
                        </td>
                      </tr>
                    </tbody>
                  </table>
                </div>
              </v-card-text>
            </v-card>
          </v-dialog>

          <v-dialog v-model="actionWorkflowDialog" max-width="760" persistent>
            <v-card class="action-workflow-dialog" elevation="0">
              <div class="action-workflow-dialog__header">
                <div>
                  <div class="stress-detail-dialog__eyebrow">Risk Action Workflow</div>
                  <h3 class="stress-detail-dialog__title">{{ selectedActionWorkflowItem?.nama || 'Update Tindakan' }}</h3>
                  <p class="stress-detail-dialog__subtitle">
                    Simpan owner, status, due date, dan catatan tindak lanjut untuk prioritas risiko periode {{ filters.bulan }}/{{ filters.tahun }}.
                  </p>
                </div>
                <button type="button" class="stress-detail-dialog__close" @click="actionWorkflowDialog = false" aria-label="Tutup workflow">
                  <v-icon icon="ri-close-line" size="20"></v-icon>
                </button>
              </div>

              <v-card-text class="pa-6">
                <div v-if="selectedActionWorkflowItem" class="action-workflow-context">
                  <div>
                    <span>Kontrak</span>
                    <strong>{{ selectedActionWorkflowItem.nokontrak || '-' }}</strong>
                  </div>
                  <div>
                    <span>Sumber Risiko</span>
                    <strong>{{ selectedActionWorkflowItem.source || '-' }}</strong>
                  </div>
                  <div>
                    <span>Exposure</span>
                    <strong>{{ formatRp(selectedActionWorkflowItem.amount) }}</strong>
                  </div>
                </div>

                <v-row class="mt-3">
                  <v-col cols="12" md="6">
                    <v-select
                      v-model="actionWorkflowForm.status"
                      :items="actionWorkflowStatusItems"
                      label="Status"
                      variant="outlined"
                      density="comfortable"
                    ></v-select>
                  </v-col>
                  <v-col cols="12" md="6">
                    <v-text-field
                      v-model="actionWorkflowForm.owner"
                      label="Owner / PIC"
                      variant="outlined"
                      density="comfortable"
                      placeholder="Contoh: Remedial - AO Cabang"
                    ></v-text-field>
                  </v-col>
                  <v-col cols="12" md="6">
                    <v-text-field
                      v-model="actionWorkflowForm.due_date"
                      label="Due Date"
                      type="date"
                      variant="outlined"
                      density="comfortable"
                    ></v-text-field>
                  </v-col>
                  <v-col cols="12" md="6">
                    <v-text-field
                      v-model="actionWorkflowForm.reviewed_by"
                      label="Reviewed By"
                      variant="outlined"
                      density="comfortable"
                      placeholder="Opsional"
                    ></v-text-field>
                  </v-col>
                  <v-col cols="12">
                    <v-textarea
                      v-model="actionWorkflowForm.note"
                      label="Catatan Tindak Lanjut"
                      variant="outlined"
                      rows="4"
                      auto-grow
                      placeholder="Tuliskan hasil kontak, rencana remedial, kebutuhan dokumen, atau alasan waived."
                    ></v-textarea>
                  </v-col>
                </v-row>
              </v-card-text>

              <v-card-actions class="pa-6 pt-0 justify-end">
                <v-btn variant="text" @click="actionWorkflowDialog = false">Batal</v-btn>
                <v-btn color="primary" :loading="isSavingActionWorkflow" @click="saveActionWorkflow">
                  Simpan Workflow
                </v-btn>
              </v-card-actions>
            </v-card>
          </v-dialog>



        </v-window-item>

        <!-- ==================================
             TAB 2: KUALITAS ASET & CKPN
        ================================== -->
        <v-window-item :value="1">
          <div class="quality-section-header mb-4">
            <div>
              <div class="quality-section-header__eyebrow">
                <v-icon icon="ri-pulse-line" size="13"></v-icon>
                Portfolio Quality Snapshot
              </div>
              <h2 class="quality-section-header__title">Kondisi Kualitas Portofolio Saat Ini</h2>
              <p class="quality-section-header__subtitle">
                Distribusi kolektibilitas dan aging tetap ditampilkan karena menjadi konteks utama sebelum membaca hasil CKPN model.
              </p>
            </div>
            <span class="quality-section-pill">OJK Collectibility</span>
          </div>

          <v-row class="mb-6">
            <!-- Kolektibilitas Donut -->
            <v-col cols="12" lg="4">
              <div class="content-card">
                <div class="content-card__accent-top" style="background: linear-gradient(90deg, #10b981, #059669);"></div>
                <div class="content-card__header">
                  <div>
                    <div class="content-card__title">Distribusi Kolektibilitas</div>
                    <div class="content-card__subtitle">Snapshot Kol 1-5 sebagai basis NPF dan staging risiko</div>
                  </div>
                  <div class="icon-badge" style="background: #ecfdf5; color: #059669;">
                    <v-icon icon="ri-donut-chart-line" size="18"></v-icon>
                  </div>
                </div>
                <div class="content-card__body d-flex justify-center align-center" style="min-height: 300px;">
                  <div v-if="isLoading" class="chart-loading"><v-progress-circular indeterminate color="#10b981" size="36"></v-progress-circular></div>
                  <VueApexCharts v-else-if="qualityData.kolektibilitas && qualityData.kolektibilitas.length" type="donut" height="320" :options="kolChartOpts" :series="kolChartSeries" class="w-100" />
                  <div v-else class="empty-state"><p>Data tidak tersedia</p></div>
                </div>
              </div>
            </v-col>

            <!-- Aging & ECL Table -->
            <v-col cols="12" lg="8">
              <div class="content-card">
                <div class="content-card__accent-top" style="background: linear-gradient(90deg, #0ea5e9, #0284c7);"></div>
                <div class="content-card__header">
                  <div>
                    <div class="content-card__title">Aging Bucket &amp; Pencadangan Sistem</div>
                    <div class="content-card__subtitle">Pembacaan operasional aging dan PPKA/CKPN sistem per stage kualitas</div>
                  </div>
                  <div class="icon-badge" style="background: #f0f9ff; color: #0284c7;">
                    <v-icon icon="ri-stack-line" size="18"></v-icon>
                  </div>
                </div>

                <div v-if="isLoading" class="pa-16 text-center"><v-progress-circular indeterminate color="#0ea5e9"></v-progress-circular></div>
                <div v-else-if="qualityData.aging && qualityData.aging.length" class="pa-0">
                  <!-- Stage 1 -->
                  <div class="aging-row aging-row--stage1">
                    <div class="aging-row__indicator" style="background: #10b981;"></div>
                    <div class="aging-row__body">
                      <div class="d-flex align-center gap-3">
                        <div class="aging-avatar" style="background: #ecfdf5; color: #10b981;">
                          <v-icon icon="ri-check-double-line" size="16"></v-icon>
                        </div>
                        <div>
                          <div class="aging-cat-name">Lancar</div>
                          <div class="aging-cat-sub">0 Hari - Belum Menunggak</div>
                        </div>
                      </div>
                      <div class="aging-row__financials">
                        <div class="aging-os">{{ formatRp(agingStageTotals.stage1) }}</div>
                        <div class="aging-ecl-wrap">
                          <div class="aging-ecl-label">Stage 1 - Sistem</div>
                          <div class="aging-ecl-chip aging-ecl-chip--stage1">{{ formatRpSingkat(qualityData.ecl_staging?.ckpn_stage_1) }}</div>
                        </div>
                      </div>
                    </div>
                  </div>

                  <!-- Stage 2 -->
                  <div class="aging-row aging-row--stage2">
                    <div class="aging-row__indicator" style="background: #f59e0b;"></div>
                    <div class="aging-row__body">
                      <div class="d-flex align-center gap-3">
                        <div class="aging-avatar" style="background: #fffbeb; color: #d97706;">
                          <v-icon icon="ri-error-warning-line" size="16"></v-icon>
                        </div>
                        <div>
                          <div class="aging-cat-name">Dalam Perhatian Khusus</div>
                          <div class="aging-cat-sub">1 - 90 Hari (SICR)</div>
                        </div>
                      </div>
                      <div class="aging-row__financials">
                        <div class="aging-os" style="color: #d97706;">{{ formatRp(agingStageTotals.stage2) }}</div>
                        <div class="aging-ecl-wrap">
                          <div class="aging-ecl-label">Stage 2 - Sistem</div>
                          <div class="aging-ecl-chip aging-ecl-chip--stage2">{{ formatRpSingkat(qualityData.ecl_staging?.ckpn_stage_2) }}</div>
                        </div>
                      </div>
                    </div>
                  </div>

                  <!-- Stage 3 -->
                  <div class="aging-row aging-row--stage3">
                    <div class="aging-row__indicator" style="background: #e11d48;"></div>
                    <div class="aging-row__body">
                      <div class="d-flex align-center gap-3">
                        <div class="aging-avatar" style="background: #fff1f2; color: #e11d48;">
                          <v-icon icon="ri-close-circle-line" size="16"></v-icon>
                        </div>
                        <div>
                          <div class="aging-cat-name" style="color: #be123c;">Non-Performing (NPF)</div>
                          <div class="aging-cat-sub" style="color: #f43f5e;">> 90 Hari - Credit Impaired</div>
                        </div>
                      </div>
                      <div class="aging-row__financials">
                        <div class="aging-os" style="color: #e11d48; font-weight: 800;">{{ formatRp(agingStageTotals.stage3) }}</div>
                        <div class="aging-ecl-wrap">
                          <div class="aging-ecl-label" style="color: #f43f5e;">Stage 3 - Sistem</div>
                          <div class="aging-ecl-chip aging-ecl-chip--stage3">{{ formatRpSingkat(qualityData.ecl_staging?.ckpn_stage_3) }}</div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </v-col>
          </v-row>

          <!-- CKPN PSAK 414 / Best Practice Model -->
          <div class="ckpn-model-panel mb-6">
            <div class="ckpn-model-panel__header">
              <div>
                <div class="ckpn-model-panel__eyebrow">
                  <v-icon icon="ri-verified-badge-line" size="14"></v-icon>
                  PSAK 414 / CKPN Best Practice
                </div>
                <h2 class="ckpn-model-panel__title">Model Kualitas &amp; CKPN Berbasis Database</h2>
                <p class="ckpn-model-panel__subtitle">
                  Menggunakan EAD, klasifikasi individual/kolektif, PD historis, LGD collateral shortfall, dan pembanding PPKA sistem.
                </p>
              </div>
              <div class="ckpn-model-panel__period">
                <span>Periode Observasi PD</span>
                <strong>{{ ckpnParameters.observation_start || '-' }} - {{ ckpnParameters.observation_end || '-' }}</strong>
              </div>
            </div>

            <div class="ckpn-kpi-grid">
              <div class="ckpn-kpi-card ckpn-kpi-card--primary">
                <span>Total CKPN Model</span>
                <strong>{{ formatRp(ckpnSummary.model_ckpn) }}</strong>
                <small>Individual + Kolektif berbasis PD x LGD x EAD</small>
              </div>
              <div class="ckpn-kpi-card">
                <span>PPKA Sistem Eligible</span>
                <strong>{{ formatRp(ckpnSummary.system_ppap) }}</strong>
                <small>Baseline CBS untuk produk yang masuk scope CKPN</small>
              </div>
              <div :class="['ckpn-kpi-card', Number(ckpnSummary.gap_vs_system) > 0 ? 'ckpn-kpi-card--danger' : 'ckpn-kpi-card--safe']">
                <span>Gap Model vs Sistem</span>
                <strong>{{ formatSignedRp(ckpnSummary.gap_vs_system) }}</strong>
                <small>{{ Number(ckpnSummary.gap_vs_system) > 0 ? 'Indikasi kekurangan cadangan' : 'Model di bawah/sama PPKA sistem' }}</small>
              </div>
              <div class="ckpn-kpi-card">
                <span>Coverage Model / EAD</span>
                <strong>{{ formatTruncatedPercentage(ckpnSummary.coverage_model_to_ead) }}</strong>
                <small>PPKA sistem: {{ formatTruncatedPercentage(ckpnSummary.system_coverage_to_ead) }}</small>
              </div>
            </div>

            <div :class="['ckpn-comparison-panel mt-4', `ckpn-comparison-panel--${ckpnComparison.status}`]">
              <div class="ckpn-comparison-panel__header">
                <div>
                  <div class="ckpn-section-title">
                    <v-icon icon="ri-scales-3-line" size="16"></v-icon>
                    Pendampingan CKPN Model vs PPKA Sistem
                  </div>
                  <p>
                    Membandingkan kebutuhan cadangan model ECL dengan baseline cadangan CBS pada produk eligible.
                  </p>
                </div>
                <span :class="['ckpn-interpretation-badge', `ckpn-interpretation-badge--${ckpnComparison.status}`]">
                  <v-icon :icon="ckpnComparison.icon" size="15"></v-icon>
                  {{ ckpnComparison.label }}
                </span>
              </div>

              <div class="ckpn-comparison-grid">
                <div class="ckpn-comparison-metric">
                  <span>CKPN Model</span>
                  <strong>{{ formatRp(ckpnSummary.model_ckpn) }}</strong>
                  <small>Hasil hitung PD x LGD x EAD + individual</small>
                </div>
                <div class="ckpn-comparison-metric">
                  <span>PPKA Sistem</span>
                  <strong>{{ formatRp(ckpnSummary.system_ppap) }}</strong>
                  <small>Cadangan yang terbaca dari CBS untuk scope eligible</small>
                </div>
                <div class="ckpn-comparison-metric">
                  <span>Coverage Delta</span>
                  <strong>{{ formatTruncatedPercentage(ckpnComparison.coverageDelta) }}</strong>
                  <small>Coverage model dikurangi coverage PPKA sistem</small>
                </div>
                <div class="ckpn-comparison-metric">
                  <span>Gap terhadap EAD</span>
                  <strong>{{ formatTruncatedPercentage(ckpnComparison.gapToEad) }}</strong>
                  <small>Selisih cadangan dibanding EAD eligible</small>
                </div>
              </div>

              <div class="ckpn-interpretation-box">
                <div class="ckpn-interpretation-box__icon">
                  <v-icon :icon="ckpnComparison.icon" size="20"></v-icon>
                </div>
                <div>
                  <h3>{{ ckpnComparison.headline }}</h3>
                  <p>{{ ckpnComparison.body }}</p>
                  <strong>{{ ckpnComparison.action }}</strong>
                </div>
              </div>
            </div>

            <v-row class="mt-4">
              <v-col cols="12" lg="4">
                <div class="ckpn-method-card">
                  <div class="ckpn-section-title">
                    <v-icon icon="ri-flow-chart" size="16"></v-icon>
                    Metodologi
                  </div>
                  <div class="ckpn-method-row">
                    <span>Standar</span>
                    <strong>{{ ckpnMethodology.standard || '-' }}</strong>
                  </div>
                  <div class="ckpn-method-row">
                    <span>EAD</span>
                    <strong>{{ ckpnMethodology.ead_formula || '-' }}</strong>
                  </div>
                  <div class="ckpn-method-row">
                    <span>Individual</span>
                    <strong>{{ ckpnMethodology.individual_rule || '-' }}</strong>
                  </div>
                  <div class="ckpn-method-note">
                    Produk masuk: {{ ckpnMethodology.eligible_products || '-' }}.
                    Produk keluar: {{ ckpnMethodology.excluded_products || '-' }}.
                  </div>
                </div>
              </v-col>

              <v-col cols="12" lg="4">
                <div class="ckpn-method-card">
                  <div class="ckpn-section-title">
                    <v-icon icon="ri-percent-line" size="16"></v-icon>
                    Parameter PD / LGD
                  </div>
                  <div class="ckpn-param-grid">
                    <div>
                      <span>PD Net Flow</span>
                      <strong>{{ formatTruncatedPercentage(ckpnParameters.pd_net_flow) }}</strong>
                    </div>
                    <div>
                      <span>PD Migration</span>
                      <strong>{{ formatTruncatedPercentage(ckpnParameters.pd_migration) }}</strong>
                    </div>
                    <div>
                      <span>PD Dipakai</span>
                      <strong>{{ formatTruncatedPercentage(ckpnParameters.selected_pd) }}</strong>
                    </div>
                    <div>
                      <span>LGD Shortfall</span>
                      <strong>{{ formatTruncatedPercentage(ckpnParameters.lgd_collateral_shortfall) }}</strong>
                    </div>
                  </div>
                  <div class="ckpn-method-note">
                    Basis transisi historis: {{ formatWholeNumber(ckpnParameters.transition_count) }} observasi kontrak bulanan.
                  </div>
                </div>
              </v-col>

              <v-col cols="12" lg="4">
                <div class="ckpn-method-card">
                  <div class="ckpn-section-title">
                    <v-icon icon="ri-bank-card-line" size="16"></v-icon>
                    Scope Portofolio
                  </div>
                  <div class="ckpn-scope-grid">
                    <div>
                      <span>Eligible</span>
                      <strong>{{ formatWholeNumber(ckpnSummary.eligible_noa) }}</strong>
                      <small>{{ formatRpSingkat(ckpnSummary.eligible_os) }}</small>
                    </div>
                    <div>
                      <span>Dikecualikan</span>
                      <strong>{{ formatWholeNumber(ckpnSummary.excluded_noa) }}</strong>
                      <small>{{ formatRpSingkat(ckpnSummary.excluded_os) }}</small>
                    </div>
                    <div>
                      <span>Individual</span>
                      <strong>{{ formatWholeNumber(ckpnSummary.individual_noa) }}</strong>
                      <small>{{ formatRpSingkat(ckpnSummary.individual_ckpn) }}</small>
                    </div>
                    <div>
                      <span>Kolektif</span>
                      <strong>{{ formatWholeNumber(ckpnSummary.collective_noa) }}</strong>
                      <small>{{ formatRpSingkat(ckpnSummary.collective_ckpn) }}</small>
                    </div>
                  </div>
                </div>
              </v-col>
            </v-row>

            <v-row class="mt-4">
              <v-col cols="12">
                <div class="ckpn-table-card">
                  <div class="ckpn-table-card__header">
                    <div>
                      <div class="ckpn-section-title mb-1">
                        <v-icon icon="ri-user-search-line" size="16"></v-icon>
                        Debitur Individual CKPN
                      </div>
                      <p>Rank 25 besar, Kol 2-5, dan produk masuk scope CKPN.</p>
                    </div>
                  </div>
                  <div class="overflow-x-auto">
                    <table class="ckpn-detail-table">
                      <thead>
                        <tr>
                          <th>Rank</th>
                          <th>Debitur</th>
                          <th>Produk</th>
                          <th class="text-right">EAD</th>
                          <th class="text-right">Agunan Neto</th>
                          <th class="text-right">CKPN Model</th>
                        </tr>
                      </thead>
                      <tbody>
                        <tr v-for="item in pagedCkpnIndividualDebtors" :key="item.nokontrak">
                          <td><span class="ckpn-rank-badge">#{{ item.exposure_rank }}</span></td>
                          <td>
                            <div class="stress-debtor-name">{{ item.nama }}</div>
                            <div class="stress-debtor-meta">{{ item.nokontrak }}  -  CIF {{ item.nocif || '-' }}</div>
                            <div class="stress-debtor-meta">{{ item.cabang }}  -  {{ item.nama_ao }}</div>
                          </td>
                          <td>
                            <span :class="['stress-kol-chip', `stress-kol-chip--${item.colbaru}`]">Kol {{ item.colbaru }}</span>
                            <div class="stress-debtor-meta mt-1">{{ item.produk }}</div>
                          </td>
                          <td class="text-right stress-cell-main">{{ formatRp(item.ead) }}</td>
                          <td class="text-right">{{ formatRp(item.collateral_net) }}</td>
                          <td class="text-right stress-cell-main">{{ formatRp(item.ckpn_model) }}</td>
                        </tr>
                        <tr v-if="!ckpnIndividualDebtors.length">
                          <td colspan="6" class="text-center text-slate-400 pa-4">Tidak ada debitur yang masuk kriteria individual pada periode ini.</td>
                        </tr>
                      </tbody>
                    </table>
                  </div>
                  <div v-if="ckpnIndividualDebtors.length > tablePageSize" class="table-pagination">
                    <span>Menampilkan {{ pagedCkpnIndividualDebtors.length }} dari {{ ckpnIndividualDebtors.length }} debitur</span>
                    <v-pagination v-model="ckpnIndividualPage" :length="ckpnIndividualPageCount" density="compact" total-visible="5"></v-pagination>
                  </div>
                </div>
              </v-col>

              <v-col cols="12">
                <div class="ckpn-table-card ckpn-product-card">
                  <div class="ckpn-table-card__header">
                    <div>
                      <div class="ckpn-section-title mb-1">
                        <v-icon icon="ri-pie-chart-line" size="16"></v-icon>
                        Scope Produk CKPN
                      </div>
                      <p>Validasi produk debt-type yang dihitung dan akad yang dikecualikan.</p>
                    </div>
                  </div>
                  <div class="ckpn-product-list">
                    <div v-for="item in ckpnProductScope" :key="`${item.produk}-${item.ckpn_scope}`" class="ckpn-product-row">
                      <div>
                        <div class="ckpn-product-name">{{ item.produk }}</div>
                        <div class="stress-debtor-meta">{{ formatWholeNumber(item.noa) }} rekening  -  PPKA {{ formatRpSingkat(item.ppap_system) }}</div>
                      </div>
                      <div class="ckpn-product-row__metric">
                        <span :class="['ckpn-scope-chip', item.ckpn_scope === 'eligible' ? 'ckpn-scope-chip--ok' : 'ckpn-scope-chip--muted']">
                          {{ item.ckpn_scope === 'eligible' ? 'Dihitung' : 'Dikecualikan' }}
                        </span>
                        <div class="stress-cell-main mt-1">{{ formatRpSingkat(item.os_pokok) }}</div>
                      </div>
                    </div>
                  </div>
                </div>
              </v-col>
            </v-row>
          </div>

          <div class="quality-section-header quality-section-header--compact mt-6 mb-4">
            <div>
              <div class="quality-section-header__eyebrow">
                <v-icon icon="ri-user-star-line" size="13"></v-icon>
                Accountability Layer
              </div>
              <h2 class="quality-section-header__title">Governance Kualitas per Account Officer</h2>
              <p class="quality-section-header__subtitle">
                Matriks AO tetap ditempatkan setelah model CKPN agar pembacaan risiko dapat langsung ditindaklanjuti ke pemilik portofolio.
              </p>
            </div>
            <span class="quality-section-pill quality-section-pill--teal">AO Matrix</span>
          </div>

          <!-- AO Matrix Table -->
          <div class="content-card">
            <div class="content-card__header">
              <div>
                <div class="content-card__title">Matriks Performa Kualitas Aset per Account Officer</div>
                <div class="content-card__subtitle">Monitoring tingkat NPF berdasarkan portfolio kelolaan masing-masing tenaga pemasar.</div>
              </div>
              <span class="quality-section-pill quality-section-pill--soft">Portfolio Owner View</span>
            </div>

            <div class="pa-0 overflow-x-auto">
              <table v-if="!isLoading && qualityData.ao_matrix && qualityData.ao_matrix.length" class="data-table">
                <thead>
                  <tr>
                    <th class="text-left">Nama Account Officer</th>
                    <th class="text-right">Kelolaan (O/S)</th>
                    <th class="text-right" style="color: #10b981;">Kol 1</th>
                    <th class="text-right" style="color: #d97706;">Kol 2</th>
                    <th class="text-right" style="color: #ea580c;">Kol 3</th>
                    <th class="text-right" style="color: #f43f5e;">Kol 4</th>
                    <th class="text-right" style="color: #be123c;">Kol 5</th>
                    <th class="text-center">Rasio NPF</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="item in pagedAoMatrix" :key="item.nama_ao" :class="{ 'row--danger': item.npf_ratio > 5 }">
                    <td>
                      <div class="d-flex align-center gap-3">
                        <div class="ao-avatar" :class="item.npf_ratio > 5 ? 'ao-avatar--danger' : ''">
                          {{ item.nama_ao.substring(0,2).toUpperCase() }}
                        </div>
                        <span class="font-weight-semibold" :style="item.npf_ratio > 5 ? 'color: #be123c;' : 'color: #334155;'">{{ item.nama_ao }}</span>
                      </div>
                    </td>
                    <td class="text-right font-weight-medium" style="color: #475569;">{{ formatRpSingkat(item.total_os) }}</td>
                    <td class="text-right" style="color: #059669; font-weight: 600;">{{ item.total_os > 0 ? formatTruncatedPercentage((item.kol1_os / item.total_os) * 100) : '0%' }}</td>
                    <td class="text-right" style="color: #d97706; font-weight: 600;">{{ item.total_os > 0 ? formatTruncatedPercentage((item.kol2_os / item.total_os) * 100) : '0%' }}</td>
                    <td class="text-right" style="color: #ea580c; font-weight: 600;">{{ item.total_os > 0 ? formatTruncatedPercentage((item.kol3_os / item.total_os) * 100) : '0%' }}</td>
                    <td class="text-right" style="color: #f43f5e; font-weight: 600;">{{ item.total_os > 0 ? formatTruncatedPercentage((item.kol4_os / item.total_os) * 100) : '0%' }}</td>
                    <td class="text-right" style="color: #be123c; font-weight: 700;">{{ item.total_os > 0 ? formatTruncatedPercentage((item.kol5_os / item.total_os) * 100) : '0%' }}</td>
                    <td class="text-center">
                      <span class="npf-pill" :class="item.npf_ratio > 5 ? 'npf-pill--danger' : 'npf-pill--safe'">
                        {{ formatTruncatedPercentage(item.npf_ratio) }}
                      </span>
                    </td>
                  </tr>
                </tbody>
              </table>
              <div v-if="qualityData.ao_matrix && qualityData.ao_matrix.length > tablePageSize" class="table-pagination">
                <span>Menampilkan {{ pagedAoMatrix.length }} dari {{ qualityData.ao_matrix.length }} AO</span>
                <v-pagination v-model="aoMatrixPage" :length="aoMatrixPageCount" density="compact" total-visible="5"></v-pagination>
              </div>
              <div v-if="isLoading" class="pa-16 text-center"><v-progress-circular indeterminate color="#0d9488"></v-progress-circular></div>
              <div v-if="!isLoading && (!qualityData.ao_matrix || !qualityData.ao_matrix.length)" class="empty-state pa-16">
                <v-icon icon="ri-group-line" size="48" color="#cbd5e1" class="mb-3"></v-icon>
                <p>Data Account Officer tidak tersedia.</p>
              </div>
            </div>
          </div>
        </v-window-item>

        <!-- ==================================
             TAB 3: KAP & PPKA PRUDENTIAL
        ================================== -->
        <v-window-item :value="2">
          <div class="quality-section-header mb-5">
            <div>
              <span class="quality-section-header__eyebrow">Prudential Asset Quality Layer</span>
              <h2 class="quality-section-header__title">KAP, APYD, PPKA WD, dan Net Exposure Agunan</h2>
              <p class="quality-section-header__subtitle">
                Tab ini dipisahkan dari CKPN agar pembacaan regulatory prudential tidak memotong alur model CKPN.
                Fokusnya adalah kualitas aktiva produktif, eksposur setelah agunan berbobot, dan kecukupan PPKA sistem.
              </p>
            </div>
            <span class="quality-section-pill quality-section-pill--danger">KAP & PPKA Control</span>
          </div>

          <div class="kap-risk-panel">
            <div class="kap-risk-panel__header">
              <div>
                <div class="ckpn-section-title">
                  <v-icon icon="ri-shield-star-line" size="16"></v-icon>
                  Prudential Summary
                </div>
              <p>
                  Menghubungkan Rasio KAP TKS, APYD, PPKA wajib dibentuk, PPKA sistem, dan jumlah setelah agunan
                  menjadi satu cockpit keputusan remedial dan pencadangan.
                </p>
              </div>
              <span class="quality-section-pill quality-section-pill--soft">Auto Recommendation</span>
            </div>

            <div class="kap-kpi-grid">
              <div class="kap-kpi-card kap-kpi-card--primary">
                <span>Rasio KAP TKS</span>
                <strong>{{ formatTruncatedPercentage(kapSummary.kap_ratio) }}</strong>
                <small>APYD TKS: {{ formatRp(kapSummary.apyd) }}</small>
              </div>
              <div class="kap-kpi-card">
                <span>Total Aktiva Produktif</span>
                <strong>{{ formatRp(kapSummary.total_aktiva_produktif) }}</strong>
                <small>Pembiayaan {{ formatRp(kapSummary.total_pembiayaan) }} + ABA lancar {{ formatRp(kapSummary.antar_bank_aktiva_lancar) }}</small>
              </div>
              <div class="kap-kpi-card">
                <span>Jumlah Setelah Agunan</span>
                <strong>{{ formatRp(kapSummary.net_exposure_agunan) }}</strong>
                <small>Baki debet - agunan dikuasai, tanpa floor</small>
              </div>
              <div :class="['kap-kpi-card', Number(kapSummary.ppap_gap) > 0 ? 'kap-kpi-card--danger' : 'kap-kpi-card--safe']">
                <span>Gap PPKA Bulanan</span>
                <strong>{{ formatSignedRp(kapSummary.ppap_gap) }}</strong>
                <small>Berjalan {{ formatRp(kapSummary.ppap_rekap_current) }}  -  Pembanding {{ formatRp(kapSummary.ppap_rekap_previous) }}</small>
              </div>
            </div>

            <div class="kap-component-grid mt-4">
              <div class="kap-component-card">
                <span>Pembiayaan / Baki Debet</span>
                <strong>{{ formatRp(kapSummary.total_pembiayaan) }}</strong>
                <small>Basis denominator NPF Gross/Net dan bagian utama Aktiva Produktif.</small>
              </div>
              <div class="kap-component-card">
                <span>ABA Total / Non-Macet / Macet</span>
                <strong>{{ formatRp(kapSummary.antar_bank_aktiva_total) }}</strong>
                <small>Non-macet {{ formatRp(kapSummary.antar_bank_aktiva_lancar) }}  -  Macet {{ formatRp(kapSummary.antar_bank_aktiva_macet) }}</small>
              </div>
              <div class="kap-component-card">
                <span>APYD Financing / ABA</span>
                <strong>{{ formatRp(kapSummary.apyd) }}</strong>
                <small>Financing {{ formatRp(kapSummary.apyd_financing_tks) }}  -  ABA {{ formatRp(kapSummary.antar_bank_aktiva_apyd) }}</small>
              </div>
              <div class="kap-component-card">
                <span>NPF Gross / Net</span>
                <strong>{{ formatTruncatedPercentage(kapSummary.npf_gross_ratio) }} / {{ formatTruncatedPercentage(kapSummary.npf_nett_ratio) }}</strong>
                <small>NPF nominal {{ formatRp(kapSummary.npf_gross) }}  -  PPKA NPF {{ formatRp(kapSummary.ppap_system_npf) }}</small>
              </div>
              <div class="kap-component-card">
                <span>PPKA WD / Sistem</span>
                <strong>{{ formatRp(kapSummary.ppap_wajib_dibentuk) }}</strong>
                <small>Gap Sistem-WD {{ formatSignedRp(kapSummary.ppap_system_vs_wd_gap) }}  -  Coverage {{ formatTruncatedPercentage(kapSummary.ppap_coverage_to_wd) }}</small>
              </div>
              <div class="kap-component-card">
                <span>Agunan / Coverage</span>
                <strong>{{ formatRp(kapSummary.agunan_berbobot) }}</strong>
                <small>Coverage agunan {{ formatTruncatedPercentage(kapSummary.collateral_coverage_ratio) }}  -  Unmapped ABA {{ formatRp(kapSummary.antar_bank_aktiva_unmapped) }}</small>
              </div>
            </div>

            <v-row class="mt-4">
              <v-col cols="12" xl="8">
                <div class="kap-trend-card">
                  <div class="kap-detail-header">
                    <div>
                      <div class="kap-card-title">Trend Prudential Bulanan</div>
                      <p>
                        Monitoring KAP, APYD, total PPKA bulanan, Gap PPKA Bulanan, dan Net Exposure Agunan berdasarkan database snapshot bulanan yang tersedia.
                      </p>
                    </div>
                    <span class="quality-section-pill quality-section-pill--soft">
                      {{ kapTrendMeta.available_months || 0 }} Bulan Tersedia
                    </span>
                  </div>
                  <div v-if="kapTrendAvailableRows.length" class="kap-trend-chart">
                    <VueApexCharts type="line" height="335" width="100%" :options="kapTrendChartOpts" :series="kapTrendChartSeries" />
                  </div>
                  <div v-else class="empty-state pa-10">
                    <v-icon icon="ri-line-chart-line" size="42" color="#cbd5e1" class="mb-2"></v-icon>
                    <p>Trend prudential belum tersedia karena belum ada database snapshot bulanan pada rentang filter ini.</p>
                  </div>
                  <div class="kap-trend-table overflow-x-auto mt-3">
                    <table class="data-table kap-table kap-detail-table">
                      <thead>
                        <tr>
                          <th class="text-left">Bulan</th>
                          <th class="text-left">Database</th>
                          <th class="text-right">KAP</th>
                          <th class="text-right">APYD</th>
                          <th class="text-right">Total PPKA Bulanan</th>
                          <th class="text-right">Gap PPKA Bulanan</th>
                          <th class="text-right">Net Exposure</th>
                        </tr>
                      </thead>
                      <tbody>
                        <tr v-for="item in kapPrudentialTrend" :key="`kap-trend-${item.tahun}-${item.bulan}`" :class="{ 'row--muted': !item.available }">
                          <td class="font-weight-bold">{{ item.label }}</td>
                          <td>{{ item.source_database || item.message || '-' }}</td>
                          <td class="text-right">{{ item.available ? formatTruncatedPercentage(item.kap_ratio) : '-' }}</td>
                          <td class="text-right">{{ item.available ? formatRp(item.apyd) : '-' }}</td>
                          <td class="text-right font-weight-bold">{{ item.available ? formatRp(item.total_ppap_bulanan ?? item.ppap_rekap_current ?? item.ppap_system) : '-' }}</td>
                          <td class="text-right" :class="Number(item.ppap_gap) > 0 ? 'text-error font-weight-bold' : 'text-success font-weight-bold'">
                            {{ item.available ? formatSignedRp(item.ppap_gap) : '-' }}
                          </td>
                          <td class="text-right">{{ item.available ? formatRp(item.net_exposure_agunan) : '-' }}</td>
                        </tr>
                      </tbody>
                    </table>
                  </div>
                </div>
              </v-col>

              <v-col cols="12" xl="4">
                <div class="kap-anomaly-card">
                  <div class="kap-card-title">Anomaly Detector Prudential</div>
                  <p class="kap-card-explanation">
                    Panel ini membaca tekanan data bulan aktif dan perubahan dari trend snapshot untuk menandai kondisi yang perlu tindakan manajemen.
                  </p>
                  <div class="kap-anomaly-summary">
                    <div class="kap-anomaly-summary__item kap-anomaly-summary__item--danger">
                      <span>Kritis</span>
                      <strong>{{ kapAnomalySummary.danger_count || 0 }}</strong>
                    </div>
                    <div class="kap-anomaly-summary__item kap-anomaly-summary__item--warning">
                      <span>Perhatian</span>
                      <strong>{{ kapAnomalySummary.warning_count || 0 }}</strong>
                    </div>
                    <div class="kap-anomaly-summary__item kap-anomaly-summary__item--safe">
                      <span>Aman</span>
                      <strong>{{ kapAnomalySummary.safe_count || 0 }}</strong>
                    </div>
                  </div>
                  <div class="kap-anomaly-list">
                    <div v-for="(item, index) in kapAnomalyItems" :key="`kap-anomaly-${index}`" :class="['kap-anomaly-item', `kap-anomaly-item--${item.severity}`]">
                      <div class="kap-anomaly-item__icon">
                        <v-icon :icon="anomalySeverityIcon(item.severity)" size="16"></v-icon>
                      </div>
                      <div>
                        <div class="kap-anomaly-item__title">
                          <span>{{ anomalySeverityLabel(item.severity) }}</span>
                          {{ item.title }}
                        </div>
                        <p>{{ item.message }}</p>
                        <small>{{ item.action }}</small>
                      </div>
                    </div>
                  </div>
                </div>
              </v-col>
            </v-row>

            <v-row class="mt-4">
              <v-col cols="12" xl="7">
                <div class="kap-audit-card">
                  <div class="kap-detail-header">
                    <div>
                      <div class="kap-card-title">Rekonsiliasi Sumber Data Prudential</div>
                      <p>
                        Menunjukkan tabel, field, basis filter, dan nominal pembentuk KAP/APYD/PPKA/ABA agar angka dashboard bisa diaudit sampai sumber CBS.
                      </p>
                    </div>
                    <span class="quality-section-pill quality-section-pill--soft">
                      {{ kapSourceReconciliationSummary.matched_count || 0 }}/{{ kapSourceReconciliationSummary.total_components || 0 }} Sesuai
                    </span>
                  </div>
                  <div class="kap-source-grid">
                    <div v-for="item in kapSourceReconciliationRows" :key="`source-${item.component}`" :class="['kap-source-item', `kap-source-item--${item.status}`]">
                      <div class="kap-source-item__top">
                        <div>
                          <span>{{ item.source_table }}</span>
                          <strong>{{ item.component }}</strong>
                        </div>
                        <span class="kap-source-status">
                          <v-icon :icon="auditStatusIcon(item.status)" size="13"></v-icon>
                          {{ auditStatusLabel(item.status) }}
                        </span>
                      </div>
                      <div class="kap-source-amount">{{ formatRp(item.amount) }}</div>
                      <p>{{ item.source_field }}</p>
                      <small>{{ item.note }}</small>
                    </div>
                  </div>
                </div>
              </v-col>

              <v-col cols="12" xl="5">
                <div class="kap-audit-card">
                  <div class="kap-detail-header">
                    <div>
                      <div class="kap-card-title">Data Quality Checks</div>
                      <p>
                        Pemeriksaan otomatis untuk kontrak aktif: kol invalid, OS/PPKA negatif, PPKA kosong untuk Kol 3-5, agunan tidak terbaca, dan agunan ekstrem.
                      </p>
                      <p v-if="kapDataQualitySummary.issue_count" class="kap-audit-note">
                        Menampilkan 10 prioritas teratas dari {{ kapDataQualitySummary.issue_count }} issue terdeteksi.
                      </p>
                    </div>
                    <span :class="['quality-section-pill', (kapDataQualitySummary.danger_count || 0) > 0 ? 'quality-section-pill--danger' : 'quality-section-pill--soft']">
                      {{ kapDataQualitySummary.issue_count || 0 }} Issue
                    </span>
                  </div>
                  <div class="kap-quality-summary">
                    <div class="kap-anomaly-summary__item kap-anomaly-summary__item--danger">
                      <span>Kritis</span>
                      <strong>{{ kapDataQualitySummary.danger_count || 0 }}</strong>
                    </div>
                    <div class="kap-anomaly-summary__item kap-anomaly-summary__item--warning">
                      <span>Review</span>
                      <strong>{{ kapDataQualitySummary.warning_count || 0 }}</strong>
                    </div>
                  </div>
                  <div v-if="kapDataQualityRows.length" class="kap-quality-list">
                    <div v-for="item in kapDataQualityRows.slice(0, 10)" :key="`dq-${item.issue}-${item.nokontrak}`" :class="['kap-quality-item', `kap-quality-item--${item.severity}`]">
                      <div>
                        <div class="kap-quality-item__title">{{ item.issue }}</div>
                        <p>{{ item.nama }}  -  {{ item.nokontrak }}</p>
                        <small>{{ item.produk }}  -  {{ item.nama_ao }}  -  Kol {{ item.colbaru || '-' }}</small>
                      </div>
                      <div class="kap-quality-item__amount">{{ formatRp(item.os_pokok) }}</div>
                    </div>
                  </div>
                  <div v-else class="empty-state pa-8">
                    <v-icon icon="ri-shield-check-line" size="36" color="#10b981" class="mb-2"></v-icon>
                    <p>Tidak ada issue kualitas data material pada filter ini.</p>
                  </div>
                </div>
              </v-col>
            </v-row>

            <v-row class="mt-4">
              <v-col cols="12">
                <div class="kap-breakdown-card">
                  <div class="kap-detail-header">
                    <div>
                      <div class="kap-card-title">Detail Drilldown GAP PPKA Bulanan</div>
                      <p>
                        Menjelaskan kontrak penyumbang kenaikan atau penurunan GAP PPKA.
                        PPKA berjalan dihitung memakai formula template, lalu dibandingkan dengan PPKA snapshot bulan sebelumnya.
                      </p>
                    </div>
                    <span :class="['quality-section-pill', Number(kapPpapGapSummary.net_gap) > 0 ? 'quality-section-pill--danger' : 'quality-section-pill--soft']">
                      Net {{ formatSignedRp(kapPpapGapSummary.net_gap) }}
                    </span>
                  </div>
                  <div class="aba-reconcile-strip">
                    <div>
                      <span>Pembentukan</span>
                      <strong>{{ formatRp(kapPpapGapSummary.positive_gap) }}</strong>
                    </div>
                    <div>
                      <span>Pengembalian</span>
                      <strong>{{ formatSignedRp(kapPpapGapSummary.negative_gap) }}</strong>
                    </div>
                    <div>
                      <span>Kontrak Bergerak</span>
                      <strong>{{ kapPpapGapSummary.row_count || 0 }}</strong>
                    </div>
                    <div>
                      <span>Baru / Keluar Scope</span>
                      <strong>{{ kapPpapGapSummary.new_or_in_scope_count || 0 }} / {{ kapPpapGapSummary.paid_off_or_out_scope_count || 0 }}</strong>
                    </div>
                  </div>
                  <div v-if="kapPpapGapRows.length" class="overflow-x-auto mt-3">
                    <table class="data-table kap-table kap-detail-table">
                      <thead>
                        <tr>
                          <th class="text-left">Kontrak</th>
                          <th class="text-left">Nasabah</th>
                          <th class="text-left">Kol</th>
                          <th class="text-left">Status</th>
                          <th class="text-right">OS Sebelumnya</th>
                          <th class="text-right">OS Berjalan</th>
                          <th class="text-right">Likuidasi Agunan</th>
                          <th class="text-right">PPKA Sebelumnya</th>
                          <th class="text-right">PPKA Berjalan</th>
                          <th class="text-right">GAP</th>
                        </tr>
                      </thead>
                      <tbody>
                        <tr v-for="item in pagedKapPpapGapRows" :key="`PPKA-gap-${item.nokontrak}`">
                          <td class="font-weight-bold">{{ item.nokontrak }}</td>
                          <td>
                            <div class="kap-account-name">{{ item.nama }}</div>
                            <small>{{ item.produk }}  -  {{ item.nama_ao }}</small>
                          </td>
                          <td>
                            <div class="kap-kol-flow">
                              <span class="kol-badge" :class="`kol-badge--${item.col_previous}`">{{ item.col_previous || '-' }}</span>
                              <v-icon icon="ri-arrow-right-line" size="14" color="#94a3b8"></v-icon>
                              <span class="kol-badge" :class="`kol-badge--${item.col_current}`">{{ item.col_current || '-' }}</span>
                            </div>
                          </td>
                          <td>
                            <span :class="['aba-status-chip', Number(item.ppap_gap) > 0 ? 'aba-status-chip--warning' : '']">
                              {{ item.movement_status }}
                            </span>
                          </td>
                          <td class="text-right">{{ formatRp(item.os_previous) }}</td>
                          <td class="text-right">{{ formatRp(item.os_current) }}</td>
                          <td class="text-right">{{ formatRp(item.nilai_likuidasi) }}</td>
                          <td class="text-right">{{ formatRp(item.ppap_previous) }}</td>
                          <td class="text-right font-weight-bold">{{ formatRp(item.ppap_current) }}</td>
                          <td class="text-right font-weight-bold" :class="Number(item.ppap_gap) > 0 ? 'text-error' : 'text-success'">{{ formatSignedRp(item.ppap_gap) }}</td>
                        </tr>
                      </tbody>
                    </table>
                  </div>
                  <div v-if="kapPpapGapRows.length > tablePageSize" class="table-pagination">
                    <span>Menampilkan {{ pagedKapPpapGapRows.length }} dari {{ kapPpapGapRows.length }} kontrak bergerak</span>
                    <v-pagination v-model="kapPpapGapPage" :length="kapPpapGapPageCount" density="compact" total-visible="5"></v-pagination>
                  </div>
                  <div v-if="!kapPpapGapRows.length" class="empty-state pa-8">
                    <v-icon icon="ri-shield-check-line" size="36" color="#10b981" class="mb-2"></v-icon>
                    <p>Tidak ada perubahan GAP PPKA material pada scope filter ini.</p>
                  </div>
                </div>
              </v-col>
            </v-row>

            <v-row class="mt-4">
              <v-col cols="12" md="6">
                <div class="kap-indicator-card kap-indicator-card--teal">
                  <div class="kap-indicator-card__top">
                    <div>
                      <span>MIAPB</span>
                      <h3>{{ formatTruncatedPercentage(miapbIndicator.ratio) }}</h3>
                    </div>
                    <v-icon icon="ri-bank-card-line" size="26"></v-icon>
                  </div>
                  <p>{{ miapbIndicator.interpretation || 'Interpretasi MIAPB akan muncul setelah data modal inti dan aset bermasalah terbaca.' }}</p>
                  <div class="kap-formula-mini">
                    <strong>{{ miapbIndicator.formula || 'Modal Inti / (Aset Bermasalah - PPKA Bermasalah)' }}</strong>
                    <span>{{ formatRp(miapbIndicator.modal_inti) }} / ({{ formatRp(miapbIndicator.aset_bermasalah) }} - {{ formatRp(miapbIndicator.ppap_bermasalah) }})</span>
                  </div>
                </div>
              </v-col>
              <v-col cols="12" md="6">
                <div class="kap-indicator-card kap-indicator-card--amber">
                  <div class="kap-indicator-card__top">
                    <div>
                      <span>AYDA Ratio</span>
                      <h3>{{ formatTruncatedPercentage(aydaIndicator.ratio) }}</h3>
                    </div>
                    <v-icon icon="ri-home-8-line" size="26"></v-icon>
                  </div>
                  <p>{{ aydaIndicator.interpretation || 'Interpretasi AYDA akan muncul setelah saldo AYDA terbaca dari GL.' }}</p>
                  <div class="kap-formula-mini">
                    <strong>{{ aydaIndicator.formula || 'AYDA / Total Pembiayaan' }}</strong>
                    <span>{{ formatRp(aydaIndicator.amount) }} / {{ formatRp(aydaIndicator.denominator) }}</span>
                  </div>
                </div>
              </v-col>
            </v-row>

            <v-row class="mt-4">
              <v-col cols="12">
                <div class="kap-breakdown-card">
                  <div class="kap-card-title">Rekonsiliasi Worksheet KAP Prudential</div>
                  <p class="kap-card-explanation">
                    Tabel ini menyusun ulang perhitungan KAP bergaya worksheet: pembiayaan per kolektibilitas,
                    subtotal pembiayaan, ABA non-macet, pengecualian ABA macet bila ada, sampai total Aktiva
                    Produktif. Tujuannya agar sumber pembentuk KAP, APYD, agunan, PPKA WD, dan PPKA sistem
                    bisa diaudit dalam satu alur tanpa membuka tab lain.
                  </p>
                  <div class="overflow-x-auto">
                    <table class="data-table kap-table">
                      <thead>
                        <tr>
                          <th class="text-left">Section</th>
                          <th class="text-left">Kol</th>
                          <th class="text-right">NOA</th>
                          <th class="text-right">OS</th>
                          <th class="text-right">APYD</th>
                          <th class="text-right">Agunan Dikuasai</th>
                          <th class="text-right">Jumlah</th>
                          <th class="text-center">Tarif</th>
                          <th class="text-right">PPKA WD</th>
                          <th class="text-right">PPKA Sistem</th>
                        </tr>
                      </thead>
                      <tbody>
                        <tr v-for="item in kapWorksheetRows" :key="`${item.section}-${item.kolektibilitas}`" :class="[`kap-worksheet-row--${item.row_type}`]">
                          <td class="font-weight-bold">{{ item.section }}</td>
                          <td>
                            <span class="kol-badge" :class="`kol-badge--${item.kolektibilitas}`">
                              {{ item.label }}
                            </span>
                          </td>
                          <td class="text-right">{{ item.noa ?? '-' }}</td>
                          <td class="text-right">{{ formatRp(item.baki_debet) }}</td>
                          <td class="text-right">{{ formatRp(item.apyd) }}</td>
                          <td class="text-right">{{ formatRp(item.agunan_dikuasai) }}</td>
                          <td class="text-right">{{ formatRp(item.jumlah_setelah_agunan) }}</td>
                          <td class="text-center">{{ item.tarif_ppap_wd }}</td>
                          <td class="text-right font-weight-bold">{{ formatRp(item.ppap_wajib_dibentuk) }}</td>
                          <td class="text-right">{{ formatRp(item.ppap_system) }}</td>
                        </tr>
                      </tbody>
                    </table>
                  </div>
                </div>
              </v-col>

              <v-col cols="12">
                <div class="kap-recommendation-card">
                  <div class="kap-card-title">Rekomendasi Otomatis Berikutnya</div>
                  <div class="kap-method-grid">
                    <div>
                      <span>Formula KAP</span>
                      <strong>{{ kapMethodology.kap_formula || '-' }}</strong>
                    </div>
                    <div>
                      <span>Formula APYD</span>
                      <strong>{{ kapMethodology.apyd_formula || '-' }}</strong>
                    </div>
                    <div>
                      <span>Formula PPKA WD</span>
                      <strong>{{ kapMethodology.ppap_wd_formula || '-' }}</strong>
                    </div>
                    <div>
                      <span>Sumber Agunan</span>
                      <strong>{{ kapMethodology.collateral_source || '-' }}</strong>
                    </div>
                  </div>
                  <div class="kap-recommendation-list">
                    <div v-for="(item, index) in kapRecommendations" :key="index" class="kap-recommendation-item">
                      <div class="kap-recommendation-item__icon">{{ index + 1 }}</div>
                      <p>{{ item }}</p>
                    </div>
                  </div>
                </div>
              </v-col>
            </v-row>

            <v-row class="mt-4">
              <v-col cols="12">
                <div class="kap-breakdown-card ppka-operational-panel">
                  <div class="kap-detail-header">
                    <div>
                      <div class="kap-card-title">Operasional PPKA Sistem & Adjustment</div>
                      <p>
                        Panel ini menggabungkan halaman PPKA lama ke Quality: membaca PPKA operasional per kontrak,
                        agunan pengurang PPKA, kolektibilitas, dan status penyesuaian manual bila fitur admin diaktifkan.
                        Bagian ini ditempatkan di KAP & PPKA karena menjadi tindak lanjut langsung dari Gap PPKA, PPKA WD,
                        dan rekonsiliasi prudential.
                      </p>
                    </div>
                    <div class="ppka-operational-actions">
                      <span :class="['quality-section-pill', manualAdjustmentEnabled ? 'quality-section-pill--warning' : 'quality-section-pill--soft']">
                        Manual {{ manualAdjustmentEnabled ? 'Aktif' : 'Nonaktif' }}
                      </span>
                      <v-btn size="small" variant="tonal" color="primary" :loading="ppkaOperationalLoading" prepend-icon="ri-refresh-line" @click="fetchPpkaOperational">
                        Refresh
                      </v-btn>
                    </div>
                  </div>

                  <div class="ppka-operational-summary">
                    <div>
                      <span>Total PPKA Berlaku</span>
                      <strong>{{ formatRp(ppkaOperationalSummary.total_ppap) }}</strong>
                      <small>{{ formatWholeNumber(ppkaOperationalSummary.total_kontrak) }} kontrak aktif</small>
                    </div>
                    <div>
                      <span>Kol 1-2</span>
                      <strong>{{ formatRp(Number(ppkaOperationalSummary.kol1_ppap || 0) + Number(ppkaOperationalSummary.kol2_ppap || 0)) }}</strong>
                      <small>Lancar dan Dalam Perhatian Khusus</small>
                    </div>
                    <div>
                      <span>Kol 3</span>
                      <strong>{{ formatRp(ppkaOperationalSummary.kol3_ppap) }}</strong>
                      <small>Kurang Lancar</small>
                    </div>
                    <div>
                      <span>Kol 4-5</span>
                      <strong>{{ formatRp(Number(ppkaOperationalSummary.kol4_ppap || 0) + Number(ppkaOperationalSummary.kol5_ppap || 0)) }}</strong>
                      <small>Diragukan dan Macet</small>
                    </div>
                  </div>

                  <div class="ppka-distribution-strip">
                    <div v-for="item in ppkaOperationalDistribution" :key="item.label" :class="['ppka-distribution-item', item.class]">
                      <span>{{ item.label }}</span>
                      <strong>{{ formatRp(item.value) }}</strong>
                    </div>
                  </div>

                  <div class="ppka-operational-filter">
                    <v-text-field
                      v-model="ppkaOperationalSearch"
                      prepend-inner-icon="ri-search-2-line"
                      label="Cari nasabah / kontrak / CIF"
                      variant="outlined"
                      density="compact"
                      hide-details
                    ></v-text-field>
                    <v-select
                      v-model="ppkaOperationalAo"
                      :items="ppkaOperationalAoOptions"
                      prepend-inner-icon="ri-user-star-line"
                      label="Account Officer"
                      variant="outlined"
                      density="compact"
                      hide-details
                    ></v-select>
                  </div>

                  <div v-if="ppkaOperationalLoading" class="pa-12 text-center">
                    <v-progress-circular indeterminate color="#0d9488"></v-progress-circular>
                    <p class="mt-3 text-medium-emphasis">Memuat detail PPKA operasional...</p>
                  </div>
                  <div v-else-if="filteredPpkaOperationalRows.length" class="overflow-x-auto">
                    <table class="data-table kap-table kap-detail-table">
                      <thead>
                        <tr>
                          <th class="text-left">Nasabah / Kontrak</th>
                          <th class="text-left">AO</th>
                          <th class="text-center">Kol</th>
                          <th class="text-right">Outstanding</th>
                          <th class="text-right">Agunan PPKA</th>
                          <th class="text-right">PPKA Sistem</th>
                          <th class="text-right">PPKA Berlaku</th>
                          <th class="text-center">Adjustment</th>
                          <th class="text-center">Aksi</th>
                        </tr>
                      </thead>
                      <tbody>
                        <tr v-for="item in pagedPpkaOperationalRows" :key="`ppka-operational-${item.nokontrak}`">
                          <td>
                            <div class="kap-account-name">{{ item.nama }}</div>
                            <small>{{ item.nokontrak }}  -  CIF {{ item.nocif || '-' }}</small>
                          </td>
                          <td>{{ item.nmao || '-' }}</td>
                          <td class="text-center">
                            <span class="kol-badge" :class="`kol-badge--${item.colbaru}`">
                              {{ kolektibilitasLabel(item.colbaru) }}
                            </span>
                            <small class="d-block mt-1">{{ item.haritgk || 0 }} hari</small>
                          </td>
                          <td class="text-right">{{ formatRp(item.osmdlc) }}</td>
                          <td class="text-right">{{ formatRp(item.total_agunan_ppka) }}</td>
                          <td class="text-right">{{ formatRp(item.ppap_system) }}</td>
                          <td class="text-right font-weight-bold">
                            <span :class="item.is_manual_adjusted ? 'text-primary' : 'text-amber-darken-3'">
                              {{ formatRp(item.ppap_manual) }}
                            </span>
                            <small v-if="item.is_manual_adjusted" class="d-block text-decoration-line-through text-medium-emphasis">
                              {{ formatRp(item.ppap_seharusnya) }}
                            </small>
                          </td>
                          <td class="text-center">
                            <span :class="['ppka-adjustment-chip', item.is_manual_adjusted ? 'ppka-adjustment-chip--manual' : 'ppka-adjustment-chip--system']">
                              {{ item.is_manual_adjusted ? 'Manual' : 'Sistem' }}
                            </span>
                          </td>
                          <td class="text-center">
                            <v-btn
                              v-if="manualAdjustmentEnabled"
                              size="small"
                              variant="tonal"
                              color="warning"
                              prepend-icon="ri-edit-box-line"
                              @click="openPpkaAdjustmentDialog(item)"
                            >
                              Adjust
                            </v-btn>
                            <span v-else class="text-medium-emphasis text-caption">Locked</span>
                          </td>
                        </tr>
                      </tbody>
                    </table>
                    <div v-if="filteredPpkaOperationalRows.length > tablePageSize" class="table-pagination">
                      <span>Menampilkan {{ pagedPpkaOperationalRows.length }} dari {{ filteredPpkaOperationalRows.length }} kontrak PPKA</span>
                      <v-pagination v-model="ppkaOperationalPage" :length="ppkaOperationalPageCount" density="compact" total-visible="5"></v-pagination>
                    </div>
                  </div>
                  <div v-else class="empty-state pa-8">
                    <v-icon icon="ri-inbox-line" size="40" color="#94a3b8" class="mb-2"></v-icon>
                    <p>Data PPKA operasional tidak ditemukan pada filter ini.</p>
                  </div>
                </div>
              </v-col>
            </v-row>

            <v-row class="mt-4">
              <v-col cols="12">
                <div class="kap-breakdown-card">
                  <div class="kap-detail-header">
                    <div>
                      <div class="kap-card-title">Rekonsiliasi PPKA Sistem vs PPKA WD per Kontrak</div>
                      <p>Daftar prioritas akun yang perlu dicek karena PPKA sistem belum menutup PPKA wajib dibentuk berdasarkan parameter prudential yang berlaku di dashboard.</p>
                    </div>
                    <span class="quality-section-pill quality-section-pill--danger">
                      {{ kapPpapShortfallAccounts.length }} Shortfall
                    </span>
                  </div>
                  <div v-if="kapPpapShortfallAccounts.length" class="overflow-x-auto">
                    <table class="data-table kap-table kap-detail-table">
                      <thead>
                        <tr>
                          <th class="text-left">Kontrak</th>
                          <th class="text-left">Nasabah</th>
                          <th class="text-left">Kol</th>
                          <th class="text-left">AO / Cabang</th>
                          <th class="text-right">OS</th>
                          <th class="text-right">Agunan</th>
                          <th class="text-right">Jumlah</th>
                          <th class="text-right">PPKA WD</th>
                          <th class="text-right">PPKA Sistem</th>
                          <th class="text-right">Gap</th>
                        </tr>
                      </thead>
                      <tbody>
                        <tr v-for="item in pagedKapPpapShortfallAccounts" :key="`shortfall-${item.nokontrak}`">
                          <td class="font-weight-bold">{{ item.nokontrak }}</td>
                          <td>
                            <div class="kap-account-name">{{ item.nama }}</div>
                            <small>{{ item.produk }}</small>
                          </td>
                          <td>
                            <span class="kol-badge" :class="`kol-badge--${item.colbaru}`">
                              {{ kolektibilitasLabel(item.colbaru) }}
                            </span>
                          </td>
                          <td>
                            <div>{{ item.nama_ao }}</div>
                            <small>{{ item.cabang }}</small>
                          </td>
                          <td class="text-right">{{ formatRp(item.os_pokok) }}</td>
                          <td class="text-right">{{ formatRp(item.collateral_weighted) }}</td>
                          <td class="text-right">{{ formatRp(item.net_exposure_agunan) }}</td>
                          <td class="text-right font-weight-bold">{{ formatRp(item.ppap_wajib_dibentuk) }}</td>
                          <td class="text-right">{{ formatRp(item.ppap_system) }}</td>
                          <td class="text-right text-error font-weight-bold">{{ formatSignedRp(item.ppap_gap) }}</td>
                        </tr>
                      </tbody>
                    </table>
                  </div>
                  <div v-if="kapPpapShortfallAccounts.length > tablePageSize" class="table-pagination">
                    <span>Menampilkan {{ pagedKapPpapShortfallAccounts.length }} dari {{ kapPpapShortfallAccounts.length }} kontrak</span>
                    <v-pagination v-model="kapShortfallPage" :length="kapShortfallPageCount" density="compact" total-visible="5"></v-pagination>
                  </div>
                  <div v-if="!kapPpapShortfallAccounts.length" class="empty-state pa-8">
                    <v-icon icon="ri-shield-check-line" size="36" color="#10b981" class="mb-2"></v-icon>
                    <p>Tidak ada kontrak dengan shortfall PPKA WD pada scope filter ini.</p>
                  </div>
                </div>
              </v-col>
            </v-row>

            <v-row class="mt-4">
              <v-col cols="12">
                <div class="kap-breakdown-card">
                  <div class="kap-detail-header">
                    <div>
                      <div class="kap-card-title">Top Contributor APYD TKS</div>
                      <p>Debitur Kol 3-5 yang paling besar membentuk APYD untuk prioritas remedial dan monitoring manajemen.</p>
                    </div>
                  </div>
                  <div class="overflow-x-auto">
                    <table class="data-table kap-table kap-detail-table">
                      <thead>
                        <tr>
                          <th class="text-left">Nasabah</th>
                          <th class="text-left">Kol</th>
                          <th class="text-right">OS</th>
                          <th class="text-right">APYD</th>
                        </tr>
                      </thead>
                      <tbody>
                        <tr v-for="item in pagedKapApydContributors" :key="`apyd-${item.nokontrak}`">
                          <td>
                            <div class="kap-account-name">{{ item.nama }}</div>
                            <small>{{ item.nokontrak }} - {{ item.nama_ao }}</small>
                          </td>
                          <td>
                            <span class="kol-badge" :class="`kol-badge--${item.colbaru}`">
                              {{ kolektibilitasLabel(item.colbaru) }}
                            </span>
                          </td>
                          <td class="text-right">{{ formatRp(item.os_pokok) }}</td>
                          <td class="text-right font-weight-bold">{{ formatRp(item.apyd_tks) }}</td>
                        </tr>
                      </tbody>
                    </table>
                  </div>
                  <div v-if="kapApydContributors.length > tablePageSize" class="table-pagination">
                    <span>Menampilkan {{ pagedKapApydContributors.length }} dari {{ kapApydContributors.length }} debitur</span>
                    <v-pagination v-model="kapApydPage" :length="kapApydPageCount" density="compact" total-visible="5"></v-pagination>
                  </div>
                </div>
              </v-col>

              <v-col cols="12">
                <div class="kap-breakdown-card">
                  <div class="kap-detail-header">
                    <div>
                      <div class="kap-card-title">Rincian Antar Bank Aktiva</div>
                      <p>Saldo ABA memakai MGL, sedangkan kolektibilitasnya diambil dari subledger TOFABA agar seluruh klasifikasi berasal dari database operasional.</p>
                    </div>
                  </div>
                  <div class="aba-reconcile-strip">
                    <div>
                      <span>Total ABA MGL</span>
                      <strong>{{ formatRp(kapAbaReconciliation.mgl_total) }}</strong>
                    </div>
                    <div>
                      <span>ABA Non-Macet</span>
                      <strong>{{ formatRp(kapAbaReconciliation.non_macet_total) }}</strong>
                    </div>
                    <div>
                      <span>ABA Macet</span>
                      <strong>{{ formatRp(kapAbaReconciliation.macet_total) }}</strong>
                    </div>
                  </div>
                  <div class="overflow-x-auto mt-3">
                    <table class="data-table kap-table kap-detail-table">
                      <thead>
                        <tr>
                          <th class="text-left">No SBB</th>
                          <th class="text-left">Nama Akun</th>
                          <th class="text-left">Kol</th>
                          <th class="text-right">Saldo</th>
                          <th class="text-left">Status</th>
                        </tr>
                      </thead>
                      <tbody>
                        <tr v-for="item in pagedKapAbaRows" :key="`aba-${item.nosbb}-${item.nmsbb}`">
                          <td class="font-weight-bold">{{ item.nosbb }}</td>
                          <td>{{ item.nmsbb }}</td>
                          <td>
                            <span class="kol-badge" :class="`kol-badge--${item.coll}`">
                              {{ item.coll || '-' }}
                            </span>
                          </td>
                          <td class="text-right">{{ formatRp(item.sahirrp) }}</td>
                          <td>
                            <span :class="['aba-status-chip', (item.prudential_status?.includes('Macet') || item.prudential_status?.includes('Belum')) ? 'aba-status-chip--warning' : '']">
                              {{ item.prudential_status }}
                            </span>
                          </td>
                        </tr>
                      </tbody>
                    </table>
                  </div>
                  <div v-if="kapAbaRows.length > tablePageSize" class="table-pagination">
                    <span>Menampilkan {{ pagedKapAbaRows.length }} dari {{ kapAbaRows.length }} akun ABA</span>
                    <v-pagination v-model="kapAbaPage" :length="kapAbaPageCount" density="compact" total-visible="5"></v-pagination>
                  </div>
                </div>
              </v-col>
            </v-row>

            <v-row class="mt-4">
              <v-col cols="12">
                <div class="kap-breakdown-card">
                  <div class="kap-detail-header">
                    <div>
                      <div class="kap-card-title">Akun Over-Reserved Terbesar</div>
                      <p>Akun dengan PPKA sistem di atas PPKA WD. Ini bukan masalah otomatis, tetapi wajib dibaca agar agregat tidak menutupi shortfall individual.</p>
                    </div>
                    <span class="quality-section-pill quality-section-pill--soft">
                      Review Agregat
                    </span>
                  </div>
                  <div class="overflow-x-auto">
                    <table class="data-table kap-table kap-detail-table">
                      <thead>
                        <tr>
                          <th class="text-left">Kontrak</th>
                          <th class="text-left">Nasabah</th>
                          <th class="text-left">Kol</th>
                          <th class="text-right">PPKA WD</th>
                          <th class="text-right">PPKA Sistem</th>
                          <th class="text-right">Surplus</th>
                        </tr>
                      </thead>
                      <tbody>
                        <tr v-for="item in pagedKapPpapOverReservedAccounts" :key="`over-${item.nokontrak}`">
                          <td class="font-weight-bold">{{ item.nokontrak }}</td>
                          <td>
                            <div class="kap-account-name">{{ item.nama }}</div>
                            <small>{{ item.produk }} - {{ item.nama_ao }}</small>
                          </td>
                          <td>
                            <span class="kol-badge" :class="`kol-badge--${item.colbaru}`">
                              {{ kolektibilitasLabel(item.colbaru) }}
                            </span>
                          </td>
                          <td class="text-right">{{ formatRp(item.ppap_wajib_dibentuk) }}</td>
                          <td class="text-right">{{ formatRp(item.ppap_system) }}</td>
                          <td class="text-right text-success font-weight-bold">{{ formatSignedRp(item.ppap_gap) }}</td>
                        </tr>
                      </tbody>
                    </table>
                  </div>
                  <div v-if="kapPpapOverReservedAccounts.length > tablePageSize" class="table-pagination">
                    <span>Menampilkan {{ pagedKapPpapOverReservedAccounts.length }} dari {{ kapPpapOverReservedAccounts.length }} kontrak</span>
                    <v-pagination v-model="kapOverReservedPage" :length="kapOverReservedPageCount" density="compact" total-visible="5"></v-pagination>
                  </div>
                </div>
              </v-col>
            </v-row>
          </div>
        </v-window-item>

        <!-- ==================================
             TAB 4: RISK CONCENTRATION & EARLY WARNING
        ================================== -->
        <v-window-item :value="3">
          <div class="quality-section-header mb-5">
            <div>
              <span class="quality-section-header__eyebrow">Quality & Risk Command Center</span>
              <h2 class="quality-section-header__title">Pusat Konsentrasi Risiko dan Sinyal Dini</h2>
              <p class="quality-section-header__subtitle">
                Fokus tab ini bukan rekap portofolio umum, tetapi area risiko yang perlu dipantau manajemen:
                sektor/akad berisiko, tekanan restrukturisasi, dan debitur watchlist.
              </p>
            </div>
            <span class="quality-section-pill quality-section-pill--danger">Risk Action View</span>
          </div>

          <div class="risk-concentration-summary mb-6">
            <div class="risk-concentration-summary__main">
              <div>
                <div class="risk-concentration-summary__eyebrow">Executive Risk Radar</div>
                <h3>{{ riskConcentrationSummary.headline }}</h3>
                <p>{{ riskConcentrationSummary.interpretation }}</p>
              </div>
              <span :class="['risk-level-chip', `risk-level-chip--${riskConcentrationSummary.warning_level}`]">
                {{ riskConcentrationSummary.warning_level }}
              </span>
            </div>
            <div class="risk-radar-grid">
              <div class="risk-radar-item">
                <span>Top Sector</span>
                <strong>{{ riskConcentrationSummary.sector_name }}</strong>
                <small>NPF {{ formatRp(riskConcentrationSummary.sector_npf) }}  -  {{ formatTruncatedPercentage(riskConcentrationSummary.sector_npf_ratio) }}</small>
              </div>
              <div class="risk-radar-item">
                <span>Top Akad / Produk</span>
                <strong>{{ riskConcentrationSummary.product_name }}</strong>
                <small>NPF {{ formatRp(riskConcentrationSummary.product_npf) }}</small>
              </div>
              <div class="risk-radar-item">
                <span>Top AO Risk</span>
                <strong>{{ riskConcentrationSummary.ao_name }}</strong>
                <small>NPF Ratio {{ formatTruncatedPercentage(riskConcentrationSummary.ao_npf_ratio) }}</small>
              </div>
              <div class="risk-radar-item">
                <span>Top Obligor</span>
                <strong>{{ riskConcentrationSummary.obligor_name }}</strong>
                <small>Exposure {{ formatRp(riskConcentrationSummary.obligor_os) }}</small>
              </div>
              <div class="risk-radar-item risk-radar-item--danger">
                <span>EWS Critical</span>
                <strong>{{ riskConcentrationSummary.ews_critical }} debitur</strong>
                <small>Net uncovered {{ formatRp(riskConcentrationSummary.ews_uncovered) }}</small>
              </div>
            </div>
          </div>

          <v-row class="mb-6">
            <v-col cols="12" md="3">
              <div class="rgec-card rgec-card--rose">
                <div class="rgec-card__icon">
                  <VIcon icon="ri-pulse-line" />
                </div>
                <div>
                  <span class="rgec-card__label">PKR Ratio</span>
                  <h3>{{ formatTruncatedPercentage(pkrSummary.pkr_ratio) }}</h3>
                  <p>{{ pkrSummary.pkr_noa || 0 }} rekening masuk OS_PKR: Kol 1 valid/restrukturisasi ditambah Kol 2-5.</p>
                </div>
              </div>
            </v-col>
            <v-col cols="12" md="3">
              <div class="rgec-card rgec-card--amber">
                <div class="rgec-card__icon">
                  <VIcon icon="ri-alert-line" />
                </div>
                <div>
                  <span class="rgec-card__label">Kol 2 Watch</span>
                  <h3>{{ formatRp(pkrSummary.watch_kol2_os) }}</h3>
                  <p>{{ formatTruncatedPercentage(pkrSummary.watch_kol2_ratio) }} dari scope pembiayaan; prioritas cegah migrasi ke NPF.</p>
                </div>
              </div>
            </v-col>
            <v-col cols="12" md="3">
              <div class="rgec-card rgec-card--teal">
                <div class="rgec-card__icon">
                  <VIcon icon="ri-funds-line" />
                </div>
                <div>
                  <span class="rgec-card__label">OS PKR</span>
                  <h3>{{ formatRp(pkrSummary.pkr_os) }}</h3>
                  <p>Kol 1 valid/restrukturisasi {{ formatRp(pkrSummary.restructured_lancar_os) }} + Kol 2-5 {{ formatRp(pkrSummary.pkr_non_lancar_os) }}.</p>
                </div>
              </div>
            </v-col>
            <v-col cols="12" md="3">
              <div class="rgec-card rgec-card--purple">
                <div class="rgec-card__icon">
                  <VIcon icon="ri-loop-left-line" />
                </div>
                <div>
                  <span class="rgec-card__label">Vintage Failure Rate</span>
                  <h3>{{ formatTruncatedPercentage(restruGuard.vintage_failure_rate) }}</h3>
                  <p>{{ restruGuard.gagal_kontrak || 0 }} kontrak restrukturisasi kembali masuk Kol 3-5.</p>
                </div>
              </div>
            </v-col>
          </v-row>

          <div class="watchlist-card action-queue-card mb-6">
            <div class="watchlist-card__header">
              <div class="watchlist-card__header-inner">
                <div class="watchlist-icon watchlist-icon--danger">
                  <v-icon icon="ri-sparkling-line" size="22" color="white"></v-icon>
                </div>
                <div>
                  <div class="watchlist-title">Action Priority Queue</div>
                  <div class="watchlist-subtitle">
                    Antrian tindak lanjut otomatis dari PKR, shortfall PPKA, APYD, stress obligor, dan EWS agar tim remedial punya urutan kerja yang jelas.
                  </div>
                </div>
              </div>
              <span class="quality-section-pill quality-section-pill--danger">{{ actionQueueSummary.total }} prioritas</span>
            </div>

            <div class="action-queue-summary">
              <div>
                <span>Critical</span>
                <strong>{{ actionQueueSummary.critical }}</strong>
              </div>
              <div>
                <span>High</span>
                <strong>{{ actionQueueSummary.high }}</strong>
              </div>
              <div>
                <span>Medium</span>
                <strong>{{ actionQueueSummary.medium }}</strong>
              </div>
              <div>
                <span>In Progress</span>
                <strong>{{ actionQueueSummary.inProgress }}</strong>
              </div>
              <div>
                <span>Done</span>
                <strong>{{ actionQueueSummary.done }}</strong>
              </div>
              <div>
                <span>Overdue</span>
                <strong>{{ actionQueueSummary.overdue }}</strong>
              </div>
              <div>
                <span>Exposure Terbaca</span>
                <strong>{{ formatRp(actionQueueSummary.exposure) }}</strong>
              </div>
            </div>

            <div v-if="actionPriorityQueue.length" class="overflow-x-auto mt-4">
              <table class="data-table kap-table action-queue-table">
                <thead>
                  <tr>
                    <th class="text-left">Prioritas</th>
                    <th class="text-left">Nasabah / Kontrak</th>
                    <th class="text-left">Sinyal Risiko</th>
                    <th class="text-left">AO / Cabang</th>
                    <th class="text-center">Kol</th>
                    <th class="text-right">Exposure</th>
                    <th class="text-left">Workflow</th>
                    <th class="text-left">Aksi Direkomendasikan</th>
                    <th class="text-center">Update</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="(item, index) in pagedActionPriorityQueue" :key="item.key">
                    <td>
                      <div class="action-priority-cell">
                        <span :class="['action-severity-chip', `action-severity-chip--${item.severity}`]">
                          {{ item.severity }}
                        </span>
                        <small>Score {{ item.score }}</small>
                        <strong>#{{ ((actionQueuePage - 1) * tablePageSize) + index + 1 }}</strong>
                      </div>
                    </td>
                    <td>
                      <div class="stress-debtor-name">{{ item.nama || '-' }}</div>
                      <div class="stress-debtor-meta">{{ item.nokontrak || '-' }}  -  {{ item.source }}</div>
                    </td>
                    <td>
                      <div class="action-signal-wrap">
                        <span v-for="signal in item.signals" :key="`${item.key}-${signal}`">{{ signal }}</span>
                      </div>
                    </td>
                    <td>
                      <div class="stress-debtor-name">{{ item.ao || '-' }}</div>
                      <div class="stress-debtor-meta">{{ item.cabang || '-' }}</div>
                    </td>
                    <td class="text-center">
                      <span v-if="item.kol" :class="['kol-badge', getKolClass(item.kol)]">{{ kolektibilitasLabel(item.kol) }}</span>
                      <span v-else>-</span>
                    </td>
                    <td class="text-right font-weight-bold">{{ formatRp(item.amount) }}</td>
                    <td>
                      <div class="workflow-cell">
                        <span :class="['workflow-status-chip', actionWorkflowStatusClass(item.workflow_status)]">
                          {{ actionWorkflowStatusLabel(item.workflow_status) }}
                        </span>
                        <small>{{ item.workflow_owner || 'Belum ada owner' }}</small>
                        <small :class="item.workflow_overdue ? 'text-error font-weight-bold' : ''">
                          Due: {{ item.workflow_due_date || '-' }}
                        </small>
                      </div>
                    </td>
                    <td class="action-text">{{ item.action }}</td>
                    <td class="text-center">
                      <v-btn size="small" variant="tonal" color="primary" @click="openActionWorkflowDialog(item)">
                        Update
                      </v-btn>
                    </td>
                  </tr>
                </tbody>
              </table>
              <div v-if="actionQueuePageCount > 1" class="d-flex justify-end mt-3">
                <v-pagination v-model="actionQueuePage" :length="actionQueuePageCount" density="compact" total-visible="5"></v-pagination>
              </div>
            </div>
            <div v-else class="empty-state compact">
              <v-icon icon="ri-shield-check-line"></v-icon>
              <p>Tidak ada prioritas tindakan material pada filter berjalan.</p>
            </div>
          </div>

          <div class="kap-trend-card mb-6">
            <div class="kap-detail-header">
              <div>
                <div class="kap-card-title">Trend PKR Bulanan</div>
                <p>
                  Monitoring PKR ratio, OS PKR, Kol 1 restrukturisasi/valid, Kol 2 Watch,
                  dan Kol 2-5 berdasarkan database snapshot bulanan yang tersedia.
                </p>
              </div>
              <span class="quality-section-pill quality-section-pill--soft">
                {{ pkrTrendMeta.available_months || pkrTrendAvailableRows.length }} bulan tersedia
              </span>
            </div>
            <div v-if="pkrTrendAvailableRows.length" class="trend-chart-shell">
              <VueApexCharts type="line" height="315" width="100%" :options="pkrTrendChartOpts" :series="pkrTrendChartSeries" />
            </div>
            <div class="overflow-x-auto mt-4">
              <table class="data-table kap-table">
                <thead>
                  <tr>
                    <th class="text-left">Bulan</th>
                    <th class="text-left">Database</th>
                    <th class="text-right">PKR</th>
                    <th class="text-right">OS PKR</th>
                    <th class="text-right">Kol 1 Restrukturisasi/Valid</th>
                    <th class="text-right">Kol 2 Watch</th>
                    <th class="text-right">Kol 2-5</th>
                    <th class="text-right">Delta</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="item in pkrTrendRows" :key="`pkr-trend-${item.tahun}-${item.bulan}`">
                    <td class="font-weight-bold">{{ item.label }}</td>
                    <td>{{ item.source_database || item.message || '-' }}</td>
                    <td class="text-right font-weight-bold">{{ item.available ? formatTruncatedPercentage(item.pkr_ratio) : '-' }}</td>
                    <td class="text-right">{{ item.available ? formatRp(item.pkr_os) : '-' }}</td>
                    <td class="text-right">{{ item.available ? formatRp(item.restructured_lancar_os) : '-' }}</td>
                    <td class="text-right">{{ item.available ? formatRp(item.watch_kol2_os) : '-' }}</td>
                    <td class="text-right">{{ item.available ? formatRp(item.pkr_non_lancar_os) : '-' }}</td>
                    <td class="text-right" :class="Number(item.pkr_delta) > 0 ? 'text-error font-weight-bold' : 'text-success font-weight-bold'">
                      {{ item.available ? formatTruncatedPercentage(item.pkr_delta) : '-' }}
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>

          <div class="watchlist-card mb-6">
            <div class="watchlist-card__header">
              <div class="watchlist-card__header-inner">
                <div class="watchlist-icon">
                  <v-icon icon="ri-file-chart-line" size="22" color="white"></v-icon>
                </div>
                <div>
                  <div class="watchlist-title">Pembiayaan Kualitas Rendah (PKR) by Segmen, Cabang, dan Kol</div>
                  <div class="watchlist-subtitle">
                    {{ pkrSummary.interpretation || 'PKR membaca Kol 1 valid/restrukturisasi dan Kol 2-5 sebagai sinyal kualitas rendah.' }}
                  </div>
                </div>
              </div>
            </div>
            <div class="kap-method-grid risk-method-grid">
              <div>
                <span>Formula PKR</span>
                <strong>{{ pkrMethodology.formula || 'PKR = Kol 2 + Kol 3 + Kol 4 + Kol 5' }}</strong>
              </div>
              <div>
                <span>Kebijakan Kol 1</span>
                <strong>{{ pkrMethodology.kol1_policy || 'Kol 1 hanya untuk rekonsiliasi kontrak valid.' }}</strong>
              </div>
              <div>
                <span>Basis Query</span>
                <strong>{{ pkrMethodology.basis || '-' }}</strong>
              </div>
            </div>
            <div class="pa-0 overflow-x-auto">
              <table v-if="!isLoading && pkrRows.length" class="data-table watchlist-table">
                <thead>
                  <tr>
                    <th class="text-left">Segmen / Cabang</th>
                    <th class="text-center">Kol</th>
                    <th class="text-right">OS Semua Data</th>
                    <th class="text-right">NOA Semua Data</th>
                    <th class="text-right">OS PKR</th>
                    <th class="text-right">NOA PKR</th>
                    <th class="text-right">Selisih Rekonsiliasi</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="item in pagedPkrRows" :key="`${item.periode}-${item.kdloc}-${item.segmen}-${item.colbaru}`">
                    <td>
                      <div class="font-weight-bold text-slate-800">{{ item.ket || '(Tanpa Segmen)' }}</div>
                      <small>Cabang {{ item.kdloc || '-' }}  -  Periode {{ item.periode || '-' }}</small>
                    </td>
                    <td class="text-center">
                      <span class="kol-badge" :class="`kol-badge--${item.colbaru}`">{{ kolektibilitasLabel(item.colbaru) }}</span>
                    </td>
                    <td class="text-right">{{ formatRp(item.osmdlc_semua_data) }}</td>
                    <td class="text-right">{{ item.rec_semua_data || 0 }}</td>
                    <td class="text-right font-weight-bold">{{ formatRp(item.os_pkr) }}</td>
                    <td class="text-right">{{ item.noa_pkr || 0 }}</td>
                    <td class="text-right">{{ formatRp(item.selisih_osmdlc) }} / {{ item.selisih_rec || 0 }} NOA</td>
                  </tr>
                </tbody>
              </table>
              <div v-if="pkrRows.length > tablePageSize" class="table-pagination">
                <span>Menampilkan {{ pagedPkrRows.length }} dari {{ pkrRows.length }} baris PKR</span>
                <v-pagination v-model="pkrPage" :length="pkrPageCount" density="compact" total-visible="5"></v-pagination>
              </div>
              <div v-if="isLoading" class="pa-16 text-center"><v-progress-circular indeterminate color="#0d9488"></v-progress-circular></div>
              <div v-if="!isLoading && !pkrRows.length" class="empty-state pa-12">
                <v-icon icon="ri-database-2-line" size="48" color="#cbd5e1" class="mb-3"></v-icon>
                <p>{{ pkrSummary.message || 'Data PKR tidak tersedia pada filter/database aktif.' }}</p>
              </div>
            </div>
          </div>

          <div class="watchlist-card mb-6">
            <div class="watchlist-card__header">
              <div class="watchlist-card__header-inner">
                <div class="watchlist-icon">
                  <v-icon icon="ri-user-search-line" size="22" color="white"></v-icon>
                </div>
                <div>
                  <div class="watchlist-title">Drilldown Kontrak PKR Prioritas</div>
                  <div class="watchlist-subtitle">
                    Detail fasilitas pembentuk PKR untuk follow-up: Kol 1 restrukturisasi/valid,
                    Kol 2 Watch, dan Kol 3-5 NPF.
                  </div>
                </div>
              </div>
            </div>
            <div class="pa-0 overflow-x-auto">
              <table v-if="!isLoading && pkrDetailRows.length" class="data-table watchlist-table">
                <thead>
                  <tr>
                    <th class="text-left">Bucket / Nasabah</th>
                    <th class="text-left">AO / Cabang</th>
                    <th class="text-left">Produk</th>
                    <th class="text-center">Kol</th>
                    <th class="text-right">OS</th>
                    <th class="text-right">Tunggakan</th>
                    <th class="text-right">PPKA Sistem</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="item in pagedPkrDetailRows" :key="`pkr-detail-${item.nokontrak}`">
                    <td>
                      <div class="font-weight-bold text-slate-800">{{ item.pkr_bucket }}</div>
                      <div class="kap-account-name">{{ item.nama }}</div>
                      <small>{{ item.nokontrak }}  -  {{ item.nocif }}</small>
                    </td>
                    <td>
                      <div>{{ item.nama_ao }}</div>
                      <small>{{ item.cabang }}  -  {{ item.segmen_nama }}</small>
                    </td>
                    <td>{{ item.produk }}</td>
                    <td class="text-center">
                      <span class="kol-badge" :class="`kol-badge--${item.colbaru}`">{{ kolektibilitasLabel(item.colbaru) }}</span>
                    </td>
                    <td class="text-right font-weight-bold">{{ formatRp(item.os_pokok) }}</td>
                    <td class="text-right">{{ formatRp((Number(item.tunggakan_pokok) || 0) + (Number(item.tunggakan_margin) || 0)) }}</td>
                    <td class="text-right">{{ formatRp(item.ppap_system) }}</td>
                  </tr>
                </tbody>
              </table>
              <div v-if="pkrDetailRows.length > tablePageSize" class="table-pagination">
                <span>Menampilkan {{ pagedPkrDetailRows.length }} dari {{ pkrDetailRows.length }} kontrak PKR</span>
                <v-pagination v-model="pkrDetailPage" :length="pkrDetailPageCount" density="compact" total-visible="5"></v-pagination>
              </div>
              <div v-if="isLoading" class="pa-16 text-center"><v-progress-circular indeterminate color="#0d9488"></v-progress-circular></div>
              <div v-if="!isLoading && !pkrDetailRows.length" class="empty-state pa-12">
                <v-icon icon="ri-shield-check-line" size="48" color="#10b981" class="mb-3"></v-icon>
                <p>Tidak ada kontrak PKR detail pada filter/database aktif.</p>
              </div>
            </div>
          </div>

          <v-row>
            <!-- Sector Chart -->
            <v-col cols="12" md="7" lg="8">
              <div class="sector-risk-panel">
                <div class="sector-risk-panel__header">
                  <div>
                    <div class="sector-risk-panel__eyebrow">Sector Concentration Control</div>
                    <div class="sector-risk-panel__title">Konsentrasi Risiko Sektor Ekonomi</div>
                    <div class="sector-risk-panel__subtitle">Top sektor diurutkan dari nominal NPF terbesar, lengkap dengan exposure dan rasio NPF sektoral.</div>
                  </div>
                  <span class="sector-risk-panel__pill">{{ sectorRiskRows.length }} sektor teratas</span>
                </div>

                <div class="sector-risk-kpis">
                  <div>
                    <span>Total Exposure Top Sektor</span>
                    <strong>{{ formatRp(sectorRiskSummary.total_os) }}</strong>
                  </div>
                  <div>
                    <span>NPF Top Sektor</span>
                    <strong>{{ formatRp(sectorRiskSummary.npf_os) }}</strong>
                  </div>
                  <div>
                    <span>Rasio NPF Top Sektor</span>
                    <strong>{{ formatTruncatedPercentage(sectorRiskSummary.npf_ratio) }}</strong>
                  </div>
                  <div>
                    <span>Sektor Paling Berisiko</span>
                    <strong>{{ sectorRiskSummary.top_sector }}</strong>
                  </div>
                </div>

                <div class="sector-risk-panel__body">
                  <div v-if="isLoading" class="chart-loading"><v-progress-circular indeterminate color="#0284c7" size="36"></v-progress-circular></div>
                  <VueApexCharts v-else-if="sectorRiskRows.length" type="bar" height="340" :options="sectorChartOpts" :series="sectorChartSeries" />
                  <div v-else class="empty-state py-16">
                    <v-icon icon="ri-building-2-line" size="48" color="#cbd5e1" class="mb-3"></v-icon>
                    <p>Data Sektor tidak tersedia</p>
                  </div>
                </div>

                <div v-if="sectorRiskRows.length" class="sector-risk-table-wrap">
                  <table class="sector-risk-table">
                    <thead>
                      <tr>
                        <th>Sektor</th>
                        <th class="text-right">Outstanding</th>
                        <th class="text-right">NPF</th>
                        <th class="text-right">Rasio NPF</th>
                        <th class="text-right">NOA</th>
                      </tr>
                    </thead>
                    <tbody>
                      <tr v-for="item in sectorRiskRows" :key="`sector-risk-${item.sektor}`">
                        <td>
                          <div class="sector-risk-name">{{ item.sektor || '-' }}</div>
                          <div class="sector-risk-bar">
                            <span :style="`width: ${sectorRiskSummary.npf_os > 0 ? Math.min((item.npf_os / sectorRiskSummary.npf_os) * 100, 100) : 0}%`"></span>
                          </div>
                        </td>
                        <td class="text-right font-weight-bold">{{ formatRp(item.total_os) }}</td>
                        <td class="text-right text-error font-weight-bold">{{ formatRp(item.npf_os) }}</td>
                        <td class="text-right">
                          <span :class="['sector-ratio-chip', Number(item.npf_ratio) >= 5 ? 'sector-ratio-chip--danger' : Number(item.npf_ratio) >= 2 ? 'sector-ratio-chip--warning' : 'sector-ratio-chip--safe']">
                            {{ formatTruncatedPercentage(item.npf_ratio) }}
                          </span>
                        </td>
                        <td class="text-right">{{ formatWholeNumber(item.noa) }}</td>
                      </tr>
                    </tbody>
                  </table>
                </div>
              </div>
            </v-col>

            <!-- Right Column -->
            <v-col cols="12" md="5" lg="4">
              <!-- Product Donut -->
              <div class="product-risk-panel mb-6">
                <div class="product-risk-panel__header">
                  <div>
                    <div class="content-card__title">Akad / Produk Risk Mix</div>
                    <div class="content-card__subtitle">Komposisi produk dan kontribusi NPF untuk konteks kualitas.</div>
                  </div>
                </div>
                <div class="product-risk-chart">
                  <div v-if="isLoading" class="chart-loading"><v-progress-circular indeterminate color="#6366f1" size="36"></v-progress-circular></div>
                  <VueApexCharts v-else-if="qualityData.product_data && qualityData.product_data.length" type="donut" height="300" :options="productChartOpts" :series="productChartSeries" class="w-100" />
                  <div v-else class="empty-state"><p>Data Produk tidak tersedia</p></div>
                </div>
                <div v-if="productRiskRows.length" class="product-risk-list">
                  <div v-for="item in productRiskRows" :key="`product-risk-${item.produk}`" class="product-risk-item">
                    <div>
                      <strong>{{ item.produk }}</strong>
                      <span>{{ formatWholeNumber(item.noa) }} rekening  -  NPF {{ formatTruncatedPercentage(item.npf_ratio) }}</span>
                    </div>
                    <div class="text-right">
                      <strong>{{ formatRp(item.total_os) }}</strong>
                      <span>{{ formatRp(item.npf_os) }}</span>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Top Akad Berisiko -->
              <div class="akad-risk-card">
                <div class="akad-risk-card__icon">
                  <v-icon icon="ri-pie-chart-box-line" size="22" color="white"></v-icon>
                </div>
                <div class="akad-risk-card__content">
                  <div class="akad-risk-card__label">Akad Paling Berisiko</div>
                  <div class="akad-risk-card__value">{{ summary.top_akad_risk || 'N/A' }}</div>
                  <div class="akad-risk-card__desc">Berdasarkan nominal NPF terbesar secara absolut pada portofolio saat ini.</div>
                </div>
              </div>
            </v-col>
          </v-row>

          <div class="ews-panel mt-6">
            <div class="ews-panel__header">
              <div class="watchlist-card__header-inner">
                <div class="watchlist-icon">
                  <v-icon icon="ri-spy-line" size="22" color="white"></v-icon>
                </div>
                <div>
                  <div class="watchlist-title">Early Warning Watchlist: Top High-Risk Obligors</div>
                  <div class="watchlist-subtitle">Prioritas remedial berbasis Kol 3-5, hari tunggakan, exposure, dan coverage agunan + PPKA.</div>
                </div>
              </div>
              <span class="quality-section-pill quality-section-pill--danger">{{ ewsWatchlistSummary.total }} obligor</span>
            </div>

            <div class="ews-summary-grid">
              <div>
                <span>Critical</span>
                <strong>{{ ewsWatchlistSummary.critical }}</strong>
              </div>
              <div>
                <span>High</span>
                <strong>{{ ewsWatchlistSummary.high }}</strong>
              </div>
              <div>
                <span>Total Exposure</span>
                <strong>{{ formatRp(ewsWatchlistSummary.exposure) }}</strong>
              </div>
              <div>
                <span>Net Uncovered</span>
                <strong>{{ formatRp(ewsWatchlistSummary.uncovered) }}</strong>
              </div>
              <div>
                <span>Average Cover</span>
                <strong>{{ formatTruncatedPercentage(ewsWatchlistSummary.avg_cover_ratio) }}</strong>
              </div>
            </div>

            <div class="pa-0 overflow-x-auto">
              <table v-if="!isLoading && ewsWatchlistRows.length" class="data-table watchlist-table ews-table">
                <thead>
                  <tr>
                    <th class="text-left">Severity</th>
                    <th class="text-left">Nasabah / Fasilitas</th>
                    <th class="text-left">Akad</th>
                    <th class="text-right">Baki Debet (O/S)</th>
                    <th class="text-center">Kolektibilitas</th>
                    <th class="text-center">Menunggak</th>
                    <th class="text-right">Cover Agunan + PPKA</th>
                    <th class="text-left">Aksi</th>
                    <th class="text-left">Workflow</th>
                    <th class="text-center">Update</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="item in pagedEwsWatchlistRows" :key="item.key" class="watchlist-row">
                    <td>
                      <div class="ews-severity-cell">
                        <span :class="['action-severity-chip', `action-severity-chip--${item.severity}`]">{{ item.severity }}</span>
                        <small>Score {{ item.severity_score }}</small>
                      </div>
                    </td>
                    <td>
                      <div class="font-weight-bold text-slate-800" style="font-size: 14px;">{{ item.nama }}</div>
                      <div class="d-flex align-center gap-1 mt-1" style="color: #94a3b8; font-size: 11px; font-family: monospace;">
                        <v-icon icon="ri-file-list-3-line" size="11"></v-icon>
                        {{ item.nokontrak }}
                      </div>
                    </td>
                    <td style="color: #475569; font-size: 13px; font-weight: 500;">{{ item.jenis_akad }}</td>
                    <td class="text-right font-weight-bold" style="color: #334155; font-size: 14px;">{{ formatRp(item.osmdlc) }}</td>
                    <td class="text-center">
                      <span class="kol-badge" :class="item.colbaru === '5' ? 'kol-badge--5' : item.colbaru === '4' ? 'kol-badge--4' : 'kol-badge--3'">
                        KOL {{ item.colbaru }}
                      </span>
                    </td>
                    <td class="text-center">
                      <span class="tunggak-badge">
                        <v-icon icon="ri-time-line" size="12" class="mr-1"></v-icon>
                        {{ item.days_past_due }} Hari
                      </span>
                    </td>
                    <td class="text-right">
                      <div class="ews-cover-cell">
                        <strong>{{ formatTruncatedPercentage(item.cover_ratio) }}</strong>
                        <span>Agunan {{ formatRp(item.collateral) }}</span>
                        <span>PPKA {{ formatRp(item.ppka) }}</span>
                        <small v-if="item.uncovered > 0">Uncovered {{ formatRp(item.uncovered) }}</small>
                      </div>
                    </td>
                    <td class="action-text">{{ item.recommended_action }}</td>
                    <td>
                      <div class="workflow-cell">
                        <span :class="['workflow-status-chip', actionWorkflowStatusClass(item.workflow_status)]">
                          {{ actionWorkflowStatusLabel(item.workflow_status) }}
                        </span>
                        <small>{{ item.workflow_owner || 'Belum ada owner' }}</small>
                        <small :class="item.workflow_overdue ? 'text-error font-weight-bold' : ''">
                          Due: {{ item.workflow_due_date || '-' }}
                        </small>
                      </div>
                    </td>
                    <td class="text-center">
                      <v-btn
                        size="small"
                        variant="tonal"
                        color="primary"
                        prepend-icon="ri-edit-2-line"
                        @click="openActionWorkflowDialog(item)"
                      >
                        Update
                      </v-btn>
                    </td>
                  </tr>
                </tbody>
              </table>
              <div v-if="ewsWatchlistRows.length > tablePageSize" class="table-pagination">
                <span>Menampilkan {{ pagedEwsWatchlistRows.length }} dari {{ ewsWatchlistRows.length }} debitur</span>
                <v-pagination v-model="alertsPage" :length="alertsPageCount" density="compact" total-visible="5"></v-pagination>
              </div>
              <div v-if="isLoading" class="pa-16 text-center"><v-progress-circular indeterminate color="#0d9488"></v-progress-circular></div>
              <div v-if="!isLoading && !ewsWatchlistRows.length" class="empty-state pa-16">
                <v-icon icon="ri-shield-check-line" size="56" color="#10b981" class="mb-3" style="opacity: 0.4;"></v-icon>
                <p style="color: #94a3b8; font-size: 15px; font-weight: 500;">Portofolio bersih. Tidak ada obligor berisiko tinggi saat ini.</p>
              </div>
            </div>
          </div>
        </v-window-item>



    <v-dialog v-model="ppkaAdjustmentDialog" max-width="560" persistent>
            <v-card class="action-workflow-dialog" elevation="0">
              <div class="action-workflow-dialog__header">
                <div>
                  <div class="stress-detail-dialog__eyebrow">PPKA Manual Adjustment</div>
                  <h3 class="stress-detail-dialog__title">{{ ppkaAdjustmentForm.nokontrak || 'Penyesuaian PPKA' }}</h3>
                  <p class="stress-detail-dialog__subtitle">
                    Gunakan hanya untuk penyesuaian yang sudah disetujui dan terdokumentasi. Nilai ini akan menjadi PPKA berlaku pada panel operasional.
                  </p>
                </div>
                <button type="button" class="stress-detail-dialog__close" @click="ppkaAdjustmentDialog = false" aria-label="Tutup adjustment PPKA">
                  <v-icon icon="ri-close-line" size="20"></v-icon>
                </button>
              </div>
              <v-card-text class="pa-6">
                <v-text-field
                  v-model="ppkaAdjustmentForm.nokontrak"
                  label="Nomor Kontrak"
                  variant="outlined"
                  density="comfortable"
                  readonly
                ></v-text-field>
                <v-text-field
                  v-model="ppkaAdjustmentForm.nominal_ppap"
                  label="Nominal PPKA Baru"
                  variant="outlined"
                  density="comfortable"
                  type="number"
                  prefix="Rp"
                ></v-text-field>
                <v-textarea
                  v-model="ppkaAdjustmentForm.alasan"
                  label="Alasan Penyesuaian"
                  variant="outlined"
                  rows="4"
                  auto-grow
                  placeholder="Tuliskan dasar memo, validasi agunan, koreksi data, atau persetujuan manajemen."
                ></v-textarea>
              </v-card-text>
              <v-card-actions class="pa-6 pt-0 justify-end">
                <v-btn variant="text" @click="ppkaAdjustmentDialog = false">Batal</v-btn>
                <v-btn color="warning" :loading="isSavingPpkaAdjustment" @click="submitPpkaAdjustment">
                  Simpan Adjustment
                </v-btn>
              </v-card-actions>
            </v-card>
          </v-dialog>

      </v-window>
    </div>
  </div>
</template>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap');

/* --- Base ----------------------------------------- */
* { box-sizing: border-box; }
.quality-console {
  font-family: 'Inter', sans-serif;
  background: #f1f5f9;
  min-height: 100vh;
}

/* --- Hero Header ----------------------------------- */
.hero-header {
  background: linear-gradient(135deg, #0f172a 0%, #1e293b 50%, #0f3460 100%);
  position: relative;
  overflow: hidden;
  border-bottom: 1px solid rgba(255,255,255,0.06);
}
.hero-bg-decoration {
  position: absolute;
  inset: 0;
  background: radial-gradient(ellipse at 70% -10%, rgba(99, 102, 241, 0.18) 0%, transparent 60%),
              radial-gradient(ellipse at 10% 110%, rgba(13, 148, 136, 0.15) 0%, transparent 50%);
  pointer-events: none;
}
.hero-content { position: relative; z-index: 1; }

.hero-icon-box {
  width: 60px;
  height: 60px;
  border-radius: 16px;
  background: linear-gradient(135deg, #0d9488, #0f766e);
  display: flex;
  align-items: center;
  justify-content: center;
  box-shadow: 0 8px 24px rgba(13, 148, 136, 0.35);
  flex-shrink: 0;
}

.hero-title {
  font-size: 26px;
  font-weight: 800;
  color: #ffffff;
  letter-spacing: -0.03em;
  line-height: 1.2;
  margin: 0 0 4px;
}
.hero-subtitle {
  font-size: 14px;
  color: #94a3b8;
  margin: 0;
  font-weight: 400;
}

/* Badges */
.badge-islamic {
  display: inline-flex;
  align-items: center;
  background: linear-gradient(135deg, #0d9488, #0f766e);
  color: white;
  font-size: 10px;
  font-weight: 700;
  padding: 3px 10px;
  border-radius: 99px;
  letter-spacing: 0.05em;
  text-transform: uppercase;
}
.badge-compliant {
  display: inline-flex;
  align-items: center;
  background: rgba(255,255,255,0.08);
  border: 1px solid rgba(255,255,255,0.15);
  color: #cbd5e1;
  font-size: 10px;
  font-weight: 600;
  padding: 3px 10px;
  border-radius: 99px;
  letter-spacing: 0.02em;
}

/* PK Badge */
.pk-badge-wrapper { text-align: right; }
.pk-label {
  font-size: 11px;
  color: #64748b;
  font-weight: 600;
  letter-spacing: 0.05em;
  text-transform: uppercase;
  margin-bottom: 8px;
}
.pk-badge {
  display: inline-flex;
  align-items: center;
  gap: 8px;
  padding: 10px 20px;
  border-radius: 14px;
  color: white;
  font-size: 16px;
  font-weight: 800;
  box-shadow: 0 8px 24px rgba(0,0,0,0.25);
  transition: transform 0.2s ease;
}
.pk-badge:hover { transform: translateY(-2px); }
.pk-gradient-sehat { background: linear-gradient(135deg, #10b981, #059669); }
.pk-gradient-cukup { background: linear-gradient(135deg, #f59e0b, #d97706); }
.pk-gradient-kurang { background: linear-gradient(135deg, #f43f5e, #e11d48); }

/* --- Filter Bar ------------------------------------ */
.filter-bar {
  display: flex;
  align-items: center;
  flex-wrap: wrap;
  gap: 0;
  background: rgba(255, 255, 255, 0.06);
  border: 1px solid rgba(255, 255, 255, 0.1);
  border-radius: 16px;
  padding: 6px 6px;
  backdrop-filter: blur(12px);
}
.filter-group {
  display: flex;
  align-items: center;
  gap: 6px;
  padding: 4px 12px;
}
.filter-icon-wrap {
  width: 28px;
  height: 28px;
  display: flex;
  align-items: center;
  justify-content: center;
  border-radius: 8px;
  background: rgba(255,255,255,0.06);
  flex-shrink: 0;
}
.filter-divider {
  width: 1px;
  height: 32px;
  background: rgba(255,255,255,0.1);
  margin: 0 2px;
}
.filter-select :deep(.v-field) {
  background: transparent !important;
  box-shadow: none !important;
}
.filter-select :deep(.v-field__input) {
  color: #e2e8f0 !important;
  font-size: 13px !important;
  font-weight: 600 !important;
  padding-top: 8px !important;
}
.filter-select :deep(.v-label) {
  color: rgba(255,255,255,0.5) !important;
  font-size: 12px !important;
}
.filter-select :deep(.v-icon) { color: rgba(255,255,255,0.5) !important; }
.filter-icon-wrap .v-icon { color: rgba(255,255,255,0.6) !important; }
.filter-apply-btn {
  display: flex;
  align-items: center;
  background: linear-gradient(135deg, #0d9488, #0f766e);
  color: white;
  font-size: 13px;
  font-weight: 700;
  padding: 10px 20px;
  border-radius: 12px;
  cursor: pointer;
  transition: all 0.2s ease;
  margin-left: auto;
  white-space: nowrap;
  user-select: none;
  box-shadow: 0 4px 12px rgba(13, 148, 136, 0.4);
}
.filter-apply-btn:hover { transform: translateY(-1px); box-shadow: 0 6px 16px rgba(13, 148, 136, 0.5); }
.filter-apply-btn:active { transform: translateY(0); }

.filter-info-bar {
  display: flex;
  align-items: center;
  font-size: 12px;
  color: #475569;
  font-weight: 500;
}

/* --- Tab Navigation -------------------------------- */
.tab-toolbar {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 14px;
  flex-wrap: wrap;
}
.tab-nav {
  display: flex;
  flex-wrap: wrap;
  gap: 6px;
  background: rgba(255,255,255,0.7);
  border: 1px solid #e2e8f0;
  padding: 6px;
  border-radius: 14px;
  backdrop-filter: blur(8px);
  width: fit-content;
  box-shadow: 0 1px 3px rgba(0,0,0,0.06);
}
.tab-export-actions {
  display: flex;
  align-items: center;
  gap: 8px;
  flex-wrap: wrap;
}
.export-btn {
  display: inline-flex;
  align-items: center;
  gap: 7px;
  border: 1px solid #e2e8f0;
  border-radius: 12px;
  padding: 10px 14px;
  background: rgba(255,255,255,0.82);
  color: #334155;
  font-size: 12px;
  font-weight: 900;
  letter-spacing: 0.01em;
  cursor: pointer;
  box-shadow: 0 1px 3px rgba(15, 23, 42, 0.06);
  transition: transform 0.18s ease, box-shadow 0.18s ease, border-color 0.18s ease;
}
.export-btn:hover {
  transform: translateY(-1px);
  box-shadow: 0 10px 24px rgba(15, 23, 42, 0.10);
}
.export-btn:disabled {
  cursor: not-allowed;
  opacity: 0.58;
  transform: none;
  box-shadow: none;
}
.export-btn--excel {
  color: #047857;
  border-color: #bbf7d0;
  background: linear-gradient(180deg, #ffffff 0%, #f0fdf4 100%);
}
.export-btn--pdf {
  color: #be123c;
  border-color: #fecdd3;
  background: linear-gradient(180deg, #ffffff 0%, #fff1f2 100%);
}
.export-error {
  display: inline-flex;
  align-items: center;
  gap: 6px;
  color: #be123c;
  background: #fff1f2;
  border: 1px solid #fecdd3;
  border-radius: 999px;
  padding: 8px 11px;
  font-size: 11px;
  font-weight: 800;
}
.export-spin {
  animation: export-spin 0.8s linear infinite;
}
@keyframes export-spin {
  to { transform: rotate(360deg); }
}
.tab-btn {
  display: inline-flex;
  align-items: center;
  background: transparent;
  border: none;
  color: #64748b;
  font-size: 13px;
  font-weight: 600;
  font-family: 'Inter', sans-serif;
  padding: 9px 18px;
  border-radius: 10px;
  cursor: pointer;
  transition: all 0.2s ease;
  white-space: nowrap;
}
.tab-btn:hover { background: rgba(0,0,0,0.04); color: #334155; }
.tab-btn--active {
  background: #0f172a !important;
  color: #ffffff !important;
  box-shadow: 0 4px 12px rgba(15, 23, 42, 0.2);
}

/* --- Content Card ---------------------------------- */
.content-card {
  background: #ffffff;
  border: 1px solid #e2e8f0;
  border-radius: 18px;
  overflow: hidden;
  box-shadow: 0 1px 3px rgba(0,0,0,0.05), 0 4px 12px rgba(0,0,0,0.04);
  transition: box-shadow 0.25s ease, transform 0.25s ease;
}
.content-card:hover {
  box-shadow: 0 4px 16px rgba(0,0,0,0.09), 0 12px 24px rgba(0,0,0,0.05);
  transform: translateY(-2px);
}
.content-card__accent-top {
  height: 4px;
  width: 100%;
}
.content-card__header {
  display: flex;
  align-items: flex-start;
  justify-content: space-between;
  padding: 20px 24px 16px;
  border-bottom: 1px solid #f1f5f9;
}
.content-card__title {
  font-size: 15px;
  font-weight: 700;
  color: #1e293b;
  letter-spacing: -0.01em;
  line-height: 1.3;
}
.content-card__subtitle {
  font-size: 12px;
  color: #94a3b8;
  margin-top: 3px;
  font-weight: 500;
}
.content-card__body { padding: 8px; }

.icon-badge {
  width: 38px;
  height: 38px;
  border-radius: 10px;
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
}

/* --- KPI Cards ------------------------------------- */
.kpi-card {
  background: #ffffff;
  border: 1px solid #e2e8f0;
  border-radius: 18px;
  overflow: hidden;
  box-shadow: 0 1px 3px rgba(0,0,0,0.05), 0 2px 8px rgba(0,0,0,0.04);
  transition: all 0.25s ease;
  position: relative;
}
.kpi-card:hover {
  transform: translateY(-4px);
  box-shadow: 0 8px 24px rgba(0,0,0,0.10);
}
.kpi-card--danger {
  border-color: #fecdd3;
  background: linear-gradient(160deg, #fff1f2 0%, #ffffff 60%);
}
.kpi-card__accent {
  height: 4px;
  width: 100%;
}
.kpi-card__inner { padding: 20px 20px 16px; }
.kpi-card__header {
  display: flex;
  align-items: flex-start;
  justify-content: space-between;
  margin-bottom: 10px;
}
.kpi-card__label {
  font-size: 11px;
  font-weight: 700;
  color: #94a3b8;
  text-transform: uppercase;
  letter-spacing: 0.06em;
}
.kpi-card__icon {
  width: 36px;
  height: 36px;
  border-radius: 10px;
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
}
.kpi-card__value {
  font-size: 26px;
  font-weight: 900;
  color: #1e293b;
  letter-spacing: -0.03em;
  line-height: 1.1;
  margin-bottom: 8px;
}
.kpi-card__sub {
  font-size: 12px;
  color: #94a3b8;
  font-weight: 500;
}
.kpi-card__badge {
  display: inline-flex;
  align-items: center;
  font-size: 11px;
  font-weight: 700;
  padding: 4px 10px;
  border-radius: 99px;
  margin-top: 2px;
}
.kpi-badge--success { background: #ecfdf5; color: #059669; }
.kpi-badge--danger { background: #fff1f2; color: #e11d48; }

/* --- Status Chips ---------------------------------- */
.status-chip {
  display: inline-flex;
  align-items: center;
  font-size: 11px;
  font-weight: 700;
  padding: 4px 10px;
  border-radius: 99px;
}
.status-chip--warning { background: #fffbeb; color: #d97706; border: 1px solid #fde68a; }
.status-chip--neutral { background: #ecfdf5; color: #059669; border: 1px solid #a7f3d0; }

/* --- Quality Tab Sectioning ----------------------- */
.quality-section-header {
  display: flex;
  align-items: flex-end;
  justify-content: space-between;
  gap: 20px;
  padding: 18px 20px;
  border: 1px solid #e2e8f0;
  border-radius: 20px;
  background:
    radial-gradient(circle at top left, rgba(14, 165, 233, 0.10), transparent 34%),
    linear-gradient(135deg, #ffffff 0%, #f8fafc 100%);
  box-shadow: 0 4px 18px rgba(15, 23, 42, 0.04);
}
.quality-section-header--compact {
  background:
    radial-gradient(circle at top left, rgba(20, 184, 166, 0.10), transparent 34%),
    linear-gradient(135deg, #ffffff 0%, #f8fafc 100%);
}
.quality-section-header__eyebrow {
  display: inline-flex;
  align-items: center;
  gap: 7px;
  color: #0369a1;
  font-size: 11px;
  font-weight: 900;
  letter-spacing: 0.10em;
  text-transform: uppercase;
  margin-bottom: 7px;
}
.quality-section-header__title {
  color: #0f172a;
  font-size: 20px;
  font-weight: 900;
  letter-spacing: -0.04em;
  line-height: 1.2;
  margin: 0;
}
.quality-section-header__subtitle {
  color: #64748b;
  font-size: 13px;
  line-height: 1.55;
  max-width: 820px;
  margin: 7px 0 0;
}
.quality-section-pill {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  min-height: 32px;
  padding: 7px 12px;
  border-radius: 999px;
  background: #eff6ff;
  color: #1d4ed8;
  border: 1px solid #bfdbfe;
  font-size: 11px;
  font-weight: 900;
  text-transform: uppercase;
  letter-spacing: 0.06em;
  white-space: nowrap;
}
.quality-section-pill--teal {
  background: #f0fdfa;
  color: #0f766e;
  border-color: #99f6e4;
}
.quality-section-pill--soft {
  background: #f8fafc;
  color: #475569;
  border-color: #e2e8f0;
}
.quality-section-pill--danger {
  background: #fff1f2;
  color: #be123c;
  border-color: #fecdd3;
}

@media (max-width: 768px) {
  .quality-section-header {
    align-items: flex-start;
    flex-direction: column;
  }
}

/* --- CKPN Model Panel ----------------------------- */
.ckpn-model-panel {
  background: linear-gradient(180deg, #ffffff 0%, #f8fafc 100%);
  border: 1px solid #e2e8f0;
  border-radius: 24px;
  padding: 24px;
  box-shadow: 0 10px 30px rgba(15, 23, 42, 0.06);
}
.ckpn-model-panel__header {
  display: flex;
  justify-content: space-between;
  gap: 20px;
  align-items: flex-start;
  margin-bottom: 18px;
}
.ckpn-model-panel__eyebrow {
  display: inline-flex;
  align-items: center;
  gap: 7px;
  color: #0369a1;
  background: #f0f9ff;
  border: 1px solid #bae6fd;
  border-radius: 999px;
  padding: 6px 10px;
  font-size: 11px;
  font-weight: 900;
  text-transform: uppercase;
  letter-spacing: 0.08em;
  margin-bottom: 10px;
}
.ckpn-model-panel__title {
  color: #0f172a;
  font-size: 22px;
  font-weight: 900;
  letter-spacing: -0.04em;
  margin: 0;
}
.ckpn-model-panel__subtitle {
  color: #64748b;
  font-size: 13px;
  line-height: 1.55;
  max-width: 780px;
  margin: 8px 0 0;
}
.ckpn-model-panel__period {
  min-width: 210px;
  background: #0f172a;
  color: #cbd5e1;
  border-radius: 16px;
  padding: 14px 16px;
  box-shadow: 0 8px 20px rgba(15, 23, 42, 0.18);
}
.ckpn-model-panel__period span {
  display: block;
  font-size: 10px;
  font-weight: 900;
  text-transform: uppercase;
  letter-spacing: 0.08em;
  color: #94a3b8;
  margin-bottom: 4px;
}
.ckpn-model-panel__period strong {
  color: #ffffff;
  font-size: 15px;
  font-weight: 900;
}
.ckpn-kpi-grid {
  display: grid;
  grid-template-columns: repeat(4, minmax(0, 1fr));
  gap: 14px;
}
.ckpn-kpi-card {
  background: #ffffff;
  border: 1px solid #e2e8f0;
  border-radius: 18px;
  padding: 16px;
  min-height: 118px;
}
.ckpn-kpi-card span {
  display: block;
  color: #64748b;
  font-size: 11px;
  font-weight: 900;
  text-transform: uppercase;
  letter-spacing: 0.06em;
  margin-bottom: 8px;
}
.ckpn-kpi-card strong {
  display: block;
  color: #0f172a;
  font-size: 20px;
  font-weight: 900;
  letter-spacing: -0.04em;
  line-height: 1.2;
  word-break: break-word;
}
.ckpn-kpi-card small {
  display: block;
  color: #94a3b8;
  font-size: 11px;
  line-height: 1.45;
  margin-top: 8px;
}
.ckpn-kpi-card--primary {
  background: linear-gradient(135deg, #0f766e 0%, #0f172a 100%);
  border-color: transparent;
}
.ckpn-kpi-card--primary span,
.ckpn-kpi-card--primary small { color: #ccfbf1; }
.ckpn-kpi-card--primary strong { color: #ffffff; }
.ckpn-kpi-card--safe {
  background: #f0fdf4;
  border-color: #bbf7d0;
}
.ckpn-kpi-card--safe strong { color: #047857; }
.ckpn-kpi-card--danger {
  background: #fff1f2;
  border-color: #fecdd3;
}
.ckpn-kpi-card--danger strong { color: #be123c; }
.ckpn-comparison-panel {
  border: 1px solid #e2e8f0;
  border-radius: 20px;
  padding: 18px;
  background:
    radial-gradient(circle at top right, rgba(14, 165, 233, 0.10), transparent 30%),
    linear-gradient(135deg, #ffffff 0%, #f8fafc 100%);
}
.ckpn-comparison-panel--danger {
  border-color: #fecdd3;
  background:
    radial-gradient(circle at top right, rgba(225, 29, 72, 0.12), transparent 30%),
    linear-gradient(135deg, #ffffff 0%, #fff1f2 100%);
}
.ckpn-comparison-panel--warning {
  border-color: #fde68a;
  background:
    radial-gradient(circle at top right, rgba(217, 119, 6, 0.12), transparent 30%),
    linear-gradient(135deg, #ffffff 0%, #fffbeb 100%);
}
.ckpn-comparison-panel--safe,
.ckpn-comparison-panel--balanced {
  border-color: #bbf7d0;
  background:
    radial-gradient(circle at top right, rgba(5, 150, 105, 0.12), transparent 30%),
    linear-gradient(135deg, #ffffff 0%, #f0fdf4 100%);
}
.ckpn-comparison-panel__header {
  display: flex;
  align-items: flex-start;
  justify-content: space-between;
  gap: 18px;
  margin-bottom: 16px;
}
.ckpn-comparison-panel__header p {
  color: #64748b;
  font-size: 12px;
  line-height: 1.55;
  margin: 6px 0 0;
}
.ckpn-interpretation-badge {
  display: inline-flex;
  align-items: center;
  gap: 7px;
  flex-shrink: 0;
  min-height: 34px;
  padding: 8px 12px;
  border-radius: 999px;
  font-size: 11px;
  font-weight: 900;
  text-transform: uppercase;
  letter-spacing: 0.06em;
}
.ckpn-interpretation-badge--danger {
  color: #be123c;
  background: #ffe4e6;
  border: 1px solid #fecdd3;
}
.ckpn-interpretation-badge--warning {
  color: #b45309;
  background: #fef3c7;
  border: 1px solid #fde68a;
}
.ckpn-interpretation-badge--safe,
.ckpn-interpretation-badge--balanced {
  color: #047857;
  background: #dcfce7;
  border: 1px solid #bbf7d0;
}
.ckpn-interpretation-badge--neutral {
  color: #475569;
  background: #f8fafc;
  border: 1px solid #e2e8f0;
}
.ckpn-comparison-grid {
  display: grid;
  grid-template-columns: repeat(4, minmax(0, 1fr));
  gap: 12px;
}
.ckpn-comparison-metric {
  background: rgba(255, 255, 255, 0.82);
  border: 1px solid rgba(226, 232, 240, 0.88);
  border-radius: 16px;
  padding: 14px;
}
.ckpn-comparison-metric span {
  display: block;
  color: #64748b;
  font-size: 10px;
  font-weight: 900;
  letter-spacing: 0.07em;
  text-transform: uppercase;
  margin-bottom: 7px;
}
.ckpn-comparison-metric strong {
  display: block;
  color: #0f172a;
  font-size: 18px;
  font-weight: 900;
  letter-spacing: -0.04em;
  line-height: 1.2;
  word-break: break-word;
}
.ckpn-comparison-metric small {
  display: block;
  color: #94a3b8;
  font-size: 11px;
  line-height: 1.45;
  margin-top: 7px;
}
.ckpn-interpretation-box {
  display: flex;
  gap: 13px;
  margin-top: 14px;
  padding: 15px;
  border-radius: 18px;
  background: rgba(255, 255, 255, 0.86);
  border: 1px solid rgba(226, 232, 240, 0.88);
}
.ckpn-interpretation-box__icon {
  width: 38px;
  height: 38px;
  flex: 0 0 38px;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  border-radius: 14px;
  color: #0369a1;
  background: #e0f2fe;
}
.ckpn-interpretation-box h3 {
  color: #0f172a;
  font-size: 15px;
  font-weight: 900;
  letter-spacing: -0.03em;
  line-height: 1.35;
  margin: 0 0 6px;
}
.ckpn-interpretation-box p {
  color: #475569;
  font-size: 12px;
  line-height: 1.6;
  margin: 0 0 8px;
}
.ckpn-interpretation-box strong {
  display: block;
  color: #0f172a;
  font-size: 12px;
  line-height: 1.55;
}
.kap-risk-panel {
  border: 1px solid #fecdd3;
  border-radius: 22px;
  padding: 18px;
  background:
    radial-gradient(circle at top right, rgba(225, 29, 72, 0.10), transparent 32%),
    linear-gradient(135deg, #ffffff 0%, #fff7ed 100%);
}
.kap-risk-panel__header {
  display: flex;
  align-items: flex-start;
  justify-content: space-between;
  gap: 18px;
  margin-bottom: 16px;
}
.kap-risk-panel__header p {
  color: #64748b;
  font-size: 12px;
  line-height: 1.6;
  margin: 6px 0 0;
  max-width: 820px;
}
.kap-kpi-grid {
  display: grid;
  grid-template-columns: repeat(4, minmax(0, 1fr));
  gap: 12px;
}
.kap-kpi-card {
  background: rgba(255, 255, 255, 0.90);
  border: 1px solid rgba(226, 232, 240, 0.92);
  border-radius: 18px;
  padding: 15px;
  min-height: 116px;
}
.kap-kpi-card span {
  display: block;
  color: #64748b;
  font-size: 10px;
  font-weight: 900;
  letter-spacing: 0.07em;
  text-transform: uppercase;
  margin-bottom: 7px;
}
.kap-kpi-card strong {
  display: block;
  color: #0f172a;
  font-size: 18px;
  font-weight: 900;
  letter-spacing: -0.04em;
  line-height: 1.2;
  word-break: break-word;
}
.kap-kpi-card small {
  display: block;
  color: #94a3b8;
  font-size: 11px;
  line-height: 1.45;
  margin-top: 7px;
}
.kap-kpi-card--primary {
  background: linear-gradient(135deg, #be123c 0%, #7f1d1d 100%);
  border-color: transparent;
}
.kap-kpi-card--primary span,
.kap-kpi-card--primary small { color: #ffe4e6; }
.kap-kpi-card--primary strong { color: #ffffff; }
.kap-kpi-card--safe {
  background: #f0fdf4;
  border-color: #bbf7d0;
}
.kap-kpi-card--safe strong { color: #047857; }
.kap-kpi-card--danger {
  background: #fff1f2;
  border-color: #fecdd3;
}
.kap-kpi-card--danger strong { color: #be123c; }
.kap-component-grid {
  display: grid;
  grid-template-columns: repeat(3, minmax(0, 1fr));
  gap: 12px;
}
.kap-component-card {
  background: #ffffff;
  border: 1px solid #e2e8f0;
  border-radius: 16px;
  padding: 14px;
  min-width: 0;
}
.kap-component-card span {
  display: block;
  color: #64748b;
  font-size: 10px;
  font-weight: 900;
  letter-spacing: 0.07em;
  text-transform: uppercase;
  margin-bottom: 7px;
}
.kap-component-card strong {
  display: block;
  color: #0f172a;
  font-size: clamp(13px, 1.1vw, 17px);
  font-weight: 900;
  letter-spacing: -0.04em;
  line-height: 1.25;
  overflow-wrap: anywhere;
}
.kap-component-card small {
  display: block;
  color: #94a3b8;
  font-size: 11px;
  line-height: 1.45;
  margin-top: 7px;
  overflow-wrap: anywhere;
}
.kap-breakdown-card,
.kap-recommendation-card,
.kap-indicator-card,
.kap-trend-card,
.kap-anomaly-card,
.kap-audit-card {
  background: rgba(255, 255, 255, 0.90);
  border: 1px solid rgba(226, 232, 240, 0.92);
  border-radius: 18px;
  padding: 16px;
}
.kap-indicator-card {
  min-height: 100%;
  padding: 18px;
  overflow: hidden;
  position: relative;
}
.kap-indicator-card--teal {
  background:
    radial-gradient(circle at top right, rgba(13, 148, 136, 0.16), transparent 36%),
    rgba(255, 255, 255, 0.94);
}
.kap-indicator-card--amber {
  background:
    radial-gradient(circle at top right, rgba(245, 158, 11, 0.16), transparent 36%),
    rgba(255, 255, 255, 0.94);
}
.kap-indicator-card__top {
  display: flex;
  justify-content: space-between;
  gap: 14px;
  align-items: flex-start;
  color: #0f766e;
}
.kap-indicator-card__top span {
  display: block;
  color: #64748b;
  font-size: 10px;
  font-weight: 900;
  text-transform: uppercase;
  letter-spacing: 0.08em;
}
.kap-indicator-card__top h3 {
  color: #0f172a;
  font-size: clamp(22px, 2.1vw, 30px);
  font-weight: 950;
  letter-spacing: -0.05em;
  line-height: 1.05;
  margin: 6px 0 0;
}
.kap-indicator-card p {
  color: #475569;
  font-size: 12px;
  line-height: 1.55;
  margin: 12px 0;
}
.kap-formula-mini {
  border: 1px solid #e2e8f0;
  border-radius: 14px;
  padding: 11px;
  background: #f8fafc;
}
.kap-formula-mini strong,
.kap-formula-mini span {
  display: block;
  overflow-wrap: anywhere;
}
.kap-formula-mini strong {
  color: #0f172a;
  font-size: 11px;
  font-weight: 900;
}
.kap-formula-mini span {
  color: #64748b;
  font-size: 11px;
  line-height: 1.45;
  margin-top: 5px;
}
.kap-trend-card {
  background:
    radial-gradient(circle at top left, rgba(13, 148, 136, 0.08), transparent 34%),
    rgba(255, 255, 255, 0.94);
}
.kap-anomaly-card {
  background:
    radial-gradient(circle at top right, rgba(244, 63, 94, 0.08), transparent 34%),
    rgba(255, 255, 255, 0.94);
}
.kap-audit-card {
  background:
    radial-gradient(circle at top right, rgba(79, 70, 229, 0.07), transparent 34%),
    rgba(255, 255, 255, 0.94);
}
.kap-source-grid {
  display: grid;
  grid-template-columns: repeat(2, minmax(0, 1fr));
  gap: 10px;
}
.kap-source-item {
  border: 1px solid #e2e8f0;
  border-radius: 16px;
  padding: 13px;
  background: #ffffff;
  min-width: 0;
}
.kap-source-item--warning {
  border-color: #fed7aa;
  background: #fff7ed;
}
.kap-source-item--danger {
  border-color: #fecdd3;
  background: #fff1f2;
}
.kap-source-item__top {
  display: flex;
  align-items: flex-start;
  justify-content: space-between;
  gap: 10px;
}
.kap-source-item__top span {
  display: block;
  color: #64748b;
  font-size: 9px;
  font-weight: 900;
  letter-spacing: 0.07em;
  text-transform: uppercase;
}
.kap-source-item__top strong {
  display: block;
  color: #0f172a;
  font-size: 12px;
  font-weight: 900;
  line-height: 1.35;
  margin-top: 3px;
}
.kap-source-status {
  display: inline-flex !important;
  align-items: center;
  gap: 4px;
  border-radius: 999px;
  padding: 4px 8px;
  background: #f1f5f9;
  color: #475569 !important;
  white-space: nowrap;
}
.kap-source-amount {
  color: #0f172a;
  font-size: clamp(12px, 1vw, 15px);
  font-weight: 900;
  letter-spacing: -0.03em;
  line-height: 1.2;
  margin-top: 10px;
  overflow-wrap: anywhere;
}
.kap-source-item p,
.kap-source-item small {
  display: block;
  color: #64748b;
  font-size: 10px;
  line-height: 1.45;
  margin: 7px 0 0;
}
.kap-audit-note {
  color: #475569 !important;
  font-size: 11px !important;
  font-weight: 800;
  margin-top: 6px !important;
}
.kap-quality-summary {
  display: grid;
  grid-template-columns: repeat(2, minmax(0, 1fr));
  gap: 8px;
  margin-bottom: 12px;
}
.kap-quality-list {
  display: flex;
  flex-direction: column;
  gap: 9px;
  max-height: 430px;
  overflow: auto;
  padding-right: 2px;
}
.kap-quality-item {
  display: flex;
  justify-content: space-between;
  gap: 12px;
  border: 1px solid #e2e8f0;
  border-radius: 14px;
  padding: 11px;
  background: #ffffff;
}
.kap-quality-item--danger {
  border-color: #fecdd3;
  background: #fff1f2;
}
.kap-quality-item--warning {
  border-color: #fed7aa;
  background: #fff7ed;
}
.kap-quality-item__title {
  color: #0f172a;
  font-size: 12px;
  font-weight: 900;
}
.kap-quality-item p {
  color: #475569;
  font-size: 11px;
  line-height: 1.45;
  margin: 4px 0 0;
}
.kap-quality-item small {
  display: block;
  color: #64748b;
  font-size: 10px;
  line-height: 1.35;
  margin-top: 3px;
}
.kap-quality-item__amount {
  flex: 0 0 120px;
  color: #0f172a;
  font-size: 11px;
  font-weight: 900;
  text-align: right;
  overflow-wrap: anywhere;
}
.kap-trend-chart {
  border: 1px solid #e2e8f0;
  border-radius: 16px;
  background: #ffffff;
  padding: 8px 8px 0;
}
.kap-trend-table {
  max-height: 260px;
}
.data-table tbody tr.row--muted {
  background: #f8fafc;
  color: #94a3b8;
}
.kap-anomaly-summary {
  display: grid;
  grid-template-columns: repeat(3, minmax(0, 1fr));
  gap: 8px;
  margin-bottom: 12px;
}
.kap-anomaly-summary__item {
  border: 1px solid #e2e8f0;
  border-radius: 14px;
  padding: 11px;
  background: #ffffff;
}
.kap-anomaly-summary__item span {
  display: block;
  color: #64748b;
  font-size: 9px;
  font-weight: 900;
  letter-spacing: 0.07em;
  text-transform: uppercase;
}
.kap-anomaly-summary__item strong {
  display: block;
  color: #0f172a;
  font-size: 21px;
  font-weight: 900;
  line-height: 1.1;
  margin-top: 4px;
}
.kap-anomaly-summary__item--danger {
  border-color: #fecdd3;
  background: #fff1f2;
}
.kap-anomaly-summary__item--danger strong { color: #be123c; }
.kap-anomaly-summary__item--warning {
  border-color: #fed7aa;
  background: #fff7ed;
}
.kap-anomaly-summary__item--warning strong { color: #c2410c; }
.kap-anomaly-summary__item--safe {
  border-color: #bbf7d0;
  background: #f0fdf4;
}
.kap-anomaly-summary__item--safe strong { color: #047857; }
.kap-anomaly-list {
  display: flex;
  flex-direction: column;
  gap: 10px;
  max-height: 520px;
  overflow: auto;
  padding-right: 2px;
}
.kap-anomaly-item {
  display: flex;
  gap: 10px;
  border: 1px solid #e2e8f0;
  border-radius: 15px;
  padding: 12px;
  background: #ffffff;
}
.kap-anomaly-item--danger {
  border-color: #fecdd3;
  background: #fff1f2;
}
.kap-anomaly-item--warning {
  border-color: #fed7aa;
  background: #fff7ed;
}
.kap-anomaly-item--safe {
  border-color: #bbf7d0;
  background: #f0fdf4;
}
.kap-anomaly-item__icon {
  width: 30px;
  height: 30px;
  flex: 0 0 30px;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  border-radius: 999px;
  background: #0f172a;
  color: #ffffff;
}
.kap-anomaly-item--danger .kap-anomaly-item__icon { background: #be123c; }
.kap-anomaly-item--warning .kap-anomaly-item__icon { background: #c2410c; }
.kap-anomaly-item--safe .kap-anomaly-item__icon { background: #047857; }
.kap-anomaly-item__title {
  color: #0f172a;
  font-size: 12px;
  font-weight: 900;
  line-height: 1.35;
}
.kap-anomaly-item__title span {
  display: inline-flex;
  margin-right: 6px;
  color: #64748b;
  font-size: 9px;
  font-weight: 900;
  letter-spacing: 0.06em;
  text-transform: uppercase;
}
.kap-anomaly-item p {
  color: #475569;
  font-size: 11px;
  line-height: 1.5;
  margin: 5px 0 0;
}
.kap-anomaly-item small {
  display: block;
  color: #64748b;
  font-size: 10px;
  line-height: 1.45;
  margin-top: 6px;
  font-weight: 700;
}
.kap-card-title {
  color: #0f172a;
  font-size: 14px;
  font-weight: 900;
  letter-spacing: -0.02em;
  margin-bottom: 13px;
}
.kap-card-explanation {
  color: #64748b;
  font-size: 12px;
  line-height: 1.6;
  margin: -4px 0 14px;
  max-width: 920px;
}
.kap-table th,
.kap-table td {
  white-space: nowrap;
}
.kap-detail-header {
  display: flex;
  align-items: flex-start;
  justify-content: space-between;
  gap: 16px;
  margin-bottom: 14px;
}
.kap-detail-header p {
  color: #64748b;
  font-size: 12px;
  line-height: 1.55;
  margin: 6px 0 0;
}
.kap-detail-table td {
  vertical-align: top;
}
.kap-account-name {
  color: #0f172a;
  font-weight: 900;
  max-width: 260px;
  overflow: hidden;
  text-overflow: ellipsis;
}
.kap-kol-flow {
  display: inline-flex;
  align-items: center;
  gap: 6px;
}
.kap-detail-table small {
  display: block;
  color: #94a3b8;
  font-size: 10px;
  line-height: 1.35;
  margin-top: 3px;
}
.table-pagination {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 12px;
  flex-wrap: wrap;
  border-top: 1px solid #e2e8f0;
  padding-top: 12px;
  margin-top: 12px;
}
.table-pagination span {
  color: #64748b;
  font-size: 11px;
  font-weight: 800;
}
.aba-reconcile-strip {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(170px, 1fr));
  gap: 10px;
  margin-top: 12px;
}
.aba-reconcile-strip > div {
  background: #f8fafc;
  border: 1px solid #e2e8f0;
  border-radius: 14px;
  padding: 12px;
}
.aba-reconcile-strip span {
  display: block;
  color: #64748b;
  font-size: 10px;
  font-weight: 900;
  letter-spacing: 0.06em;
  text-transform: uppercase;
  margin-bottom: 6px;
}
.aba-reconcile-strip strong {
  display: block;
  color: #0f172a;
  font-size: 13px;
  font-weight: 900;
  letter-spacing: -0.03em;
  word-break: break-word;
}
.ppka-operational-panel {
  border-color: #fed7aa;
  background:
    radial-gradient(circle at top right, rgba(245, 158, 11, 0.12), transparent 34%),
    linear-gradient(180deg, #ffffff 0%, #fffaf3 100%);
}
.ppka-operational-actions {
  display: flex;
  align-items: center;
  justify-content: flex-end;
  gap: 10px;
  flex-wrap: wrap;
}
.ppka-operational-summary {
  display: grid;
  grid-template-columns: repeat(4, minmax(0, 1fr));
  gap: 12px;
  margin-top: 18px;
}
.ppka-operational-summary > div,
.ppka-distribution-item {
  border: 1px solid #e2e8f0;
  border-radius: 16px;
  background: rgba(255, 255, 255, 0.86);
  padding: 14px 16px;
  min-width: 0;
}
.ppka-operational-summary span,
.ppka-distribution-item span {
  display: block;
  color: #64748b;
  font-size: 10px;
  font-weight: 900;
  text-transform: uppercase;
  letter-spacing: 0.06em;
}
.ppka-operational-summary strong,
.ppka-distribution-item strong {
  display: block;
  margin-top: 7px;
  color: #0f172a;
  font-size: 14px;
  font-weight: 900;
  overflow-wrap: anywhere;
}
.ppka-operational-summary small {
  display: block;
  margin-top: 5px;
  color: #64748b;
  font-size: 11px;
  font-weight: 700;
}
.ppka-distribution-strip {
  display: grid;
  grid-template-columns: repeat(5, minmax(0, 1fr));
  gap: 10px;
  margin-top: 12px;
}
.ppka-dist--safe { border-color: #bbf7d0; background: #f0fdf4; }
.ppka-dist--watch { border-color: #bfdbfe; background: #eff6ff; }
.ppka-dist--warning { border-color: #fde68a; background: #fffbeb; }
.ppka-dist--danger { border-color: #fed7aa; background: #fff7ed; }
.ppka-dist--critical { border-color: #fecdd3; background: #fff1f2; }
.ppka-operational-filter {
  display: grid;
  grid-template-columns: minmax(260px, 1fr) minmax(220px, 320px);
  gap: 12px;
  margin: 16px 0;
}
.ppka-adjustment-chip {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  border-radius: 999px;
  padding: 5px 10px;
  font-size: 10px;
  font-weight: 900;
  text-transform: uppercase;
  letter-spacing: 0.05em;
}
.ppka-adjustment-chip--manual {
  background: #eef2ff;
  color: #4338ca;
  border: 1px solid #c7d2fe;
}
.ppka-adjustment-chip--system {
  background: #ecfdf5;
  color: #047857;
  border: 1px solid #a7f3d0;
}
.aba-status-chip {
  display: inline-flex;
  align-items: center;
  border-radius: 999px;
  padding: 5px 9px;
  background: #f1f5f9;
  color: #475569;
  font-size: 10px;
  font-weight: 900;
  letter-spacing: 0.04em;
  text-transform: uppercase;
}
.aba-status-chip--warning {
  background: #fef3c7;
  color: #92400e;
}
.kap-method-grid {
  display: grid;
  grid-template-columns: repeat(4, minmax(0, 1fr));
  gap: 10px;
  margin-bottom: 14px;
}
.kap-method-grid > div {
  background: #f8fafc;
  border: 1px solid #e2e8f0;
  border-radius: 14px;
  padding: 11px 12px;
}
.kap-method-grid span {
  display: block;
  color: #64748b;
  font-size: 10px;
  font-weight: 900;
  letter-spacing: 0.07em;
  text-transform: uppercase;
  margin-bottom: 5px;
}
.kap-method-grid strong {
  color: #0f172a;
  font-size: 12px;
  font-weight: 800;
  line-height: 1.45;
}
.risk-method-grid {
  grid-template-columns: repeat(3, minmax(0, 1fr));
  margin: 14px 16px;
}
.kap-worksheet-row--subtotal td,
.kap-worksheet-row--total td {
  background: #f8fafc;
  color: #0f172a;
  font-weight: 900;
}
.kap-worksheet-row--adjustment td {
  background: #fff7ed;
}
.kap-recommendation-list {
  display: flex;
  flex-direction: column;
  gap: 10px;
}
.kap-recommendation-item {
  display: flex;
  gap: 10px;
  align-items: flex-start;
  padding: 11px;
  border: 1px solid #fed7aa;
  border-radius: 14px;
  background: #fff7ed;
}
.kap-recommendation-item__icon {
  width: 24px;
  height: 24px;
  flex: 0 0 24px;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  border-radius: 999px;
  background: #be123c;
  color: #ffffff;
  font-size: 11px;
  font-weight: 900;
}
.kap-recommendation-item p {
  color: #475569;
  font-size: 12px;
  line-height: 1.55;
  margin: 0;
}
.ckpn-method-card,
.ckpn-table-card {
  background: #ffffff;
  border: 1px solid #e2e8f0;
  border-radius: 18px;
  padding: 18px;
}
.ckpn-section-title {
  display: flex;
  align-items: center;
  gap: 8px;
  color: #0f172a;
  font-size: 14px;
  font-weight: 900;
  letter-spacing: -0.02em;
}
.ckpn-method-row {
  display: flex;
  justify-content: space-between;
  gap: 14px;
  padding: 12px 0;
  border-bottom: 1px solid #f1f5f9;
}
.ckpn-method-row span,
.ckpn-param-grid span,
.ckpn-scope-grid span {
  color: #64748b;
  font-size: 11px;
  font-weight: 800;
  text-transform: uppercase;
  letter-spacing: 0.05em;
}
.ckpn-method-row strong {
  max-width: 62%;
  color: #1e293b;
  font-size: 12px;
  font-weight: 800;
  text-align: right;
  line-height: 1.45;
}
.ckpn-method-note {
  margin-top: 14px;
  color: #64748b;
  background: #f8fafc;
  border: 1px dashed #cbd5e1;
  border-radius: 14px;
  padding: 12px;
  font-size: 12px;
  line-height: 1.55;
}
.ckpn-param-grid,
.ckpn-scope-grid {
  display: grid;
  grid-template-columns: repeat(2, minmax(0, 1fr));
  gap: 10px;
  margin-top: 14px;
}
.ckpn-param-grid div,
.ckpn-scope-grid div {
  background: #f8fafc;
  border: 1px solid #e2e8f0;
  border-radius: 14px;
  padding: 13px;
}
.ckpn-param-grid strong,
.ckpn-scope-grid strong {
  display: block;
  color: #0f172a;
  font-size: 18px;
  font-weight: 900;
  margin-top: 5px;
}
.ckpn-scope-grid small {
  color: #64748b;
  font-size: 11px;
  font-weight: 700;
}
.ckpn-table-card__header {
  display: flex;
  justify-content: space-between;
  gap: 12px;
  margin-bottom: 12px;
}
.ckpn-table-card__header p {
  color: #64748b;
  font-size: 12px;
  margin: 2px 0 0;
}
.ckpn-detail-table {
  width: 100%;
  min-width: 780px;
  border-collapse: separate;
  border-spacing: 0;
}
.ckpn-detail-table th {
  background: #f8fafc;
  color: #64748b;
  font-size: 10px;
  font-weight: 900;
  text-transform: uppercase;
  letter-spacing: 0.06em;
  padding: 12px;
  border-bottom: 1px solid #e2e8f0;
}
.ckpn-detail-table td {
  padding: 13px 12px;
  border-bottom: 1px solid #f1f5f9;
  vertical-align: top;
  font-size: 12px;
}
.ckpn-rank-badge {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  min-width: 34px;
  height: 28px;
  border-radius: 10px;
  background: #e0f2fe;
  color: #0369a1;
  font-size: 12px;
  font-weight: 900;
}
.ckpn-product-list {
  display: grid;
  grid-template-columns: repeat(3, minmax(0, 1fr));
  gap: 12px;
}
.ckpn-product-row {
  display: flex;
  align-items: flex-start;
  justify-content: space-between;
  gap: 14px;
  min-height: 118px;
  padding: 15px;
  border: 1px solid #e2e8f0;
  border-radius: 18px;
  background:
    radial-gradient(circle at top right, rgba(13, 148, 136, 0.08), transparent 36%),
    #ffffff;
  box-shadow: 0 1px 3px rgba(15, 23, 42, 0.04);
  transition: transform 0.18s ease, box-shadow 0.18s ease, border-color 0.18s ease;
}
.ckpn-product-row:hover {
  transform: translateY(-2px);
  border-color: #99f6e4;
  box-shadow: 0 12px 24px rgba(15, 23, 42, 0.08);
}
.ckpn-product-name {
  color: #0f172a;
  font-size: 14px;
  font-weight: 900;
  line-height: 1.35;
  max-width: 260px;
}
.ckpn-product-row__metric {
  display: flex;
  flex-direction: column;
  align-items: flex-end;
  gap: 10px;
  min-width: 168px;
  text-align: right;
}
.ckpn-product-row__metric .stress-cell-main {
  font-size: clamp(13px, 1vw, 16px);
  line-height: 1.2;
  overflow-wrap: anywhere;
}
.ckpn-scope-chip {
  display: inline-flex;
  padding: 4px 9px;
  border-radius: 999px;
  font-size: 10px;
  font-weight: 900;
  text-transform: uppercase;
  letter-spacing: 0.04em;
}
.ckpn-scope-chip--ok {
  background: #ecfdf5;
  color: #047857;
}
.ckpn-scope-chip--muted {
  background: #f1f5f9;
  color: #64748b;
}

@media (max-width: 1100px) {
  .ckpn-kpi-grid,
  .ckpn-comparison-grid,
  .kap-kpi-grid,
  .kap-component-grid,
  .ckpn-product-list,
  .kap-method-grid {
    grid-template-columns: repeat(2, minmax(0, 1fr));
  }
}
@media (max-width: 768px) {
  .ckpn-model-panel__header,
  .ckpn-comparison-panel__header,
  .kap-risk-panel__header {
    flex-direction: column;
  }
  .ckpn-model-panel__period {
    width: 100%;
  }
  .ckpn-kpi-grid,
  .ckpn-comparison-grid,
  .kap-kpi-grid,
  .kap-component-grid,
  .kap-method-grid,
  .ckpn-product-list,
  .kap-anomaly-summary,
  .kap-source-grid,
  .aba-reconcile-strip,
  .ppka-operational-summary,
  .ppka-distribution-strip,
  .ppka-operational-filter,
  .ckpn-param-grid,
  .ckpn-scope-grid {
    grid-template-columns: 1fr;
  }
  .ckpn-interpretation-box {
    flex-direction: column;
  }
}

/* --- Stress Test Panel ----------------------------- */
.stress-test-panel {
  border-radius: 20px;
  background: linear-gradient(135deg, #1a0a14 0%, #2d0a1e 40%, #3b0726 100%);
  position: relative;
  overflow: hidden;
  border: 1px solid rgba(244, 63, 94, 0.2);
  box-shadow: 0 8px 32px rgba(225, 29, 72, 0.15);
}
.stress-test-panel__bg-icon {
  position: absolute;
  right: -20px;
  top: -20px;
  opacity: 0.04;
  transform: rotate(-10deg);
}
.stress-test-panel__content { padding: 28px; position: relative; z-index: 1; }
.stress-test-avatar {
  width: 52px;
  height: 52px;
  border-radius: 14px;
  background: linear-gradient(135deg, #e11d48, #f43f5e);
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
  box-shadow: 0 6px 18px rgba(225, 29, 72, 0.4);
}
.stress-test-title {
  font-size: 18px;
  font-weight: 800;
  color: #ffffff;
  letter-spacing: -0.02em;
  margin: 0 0 4px;
}
.stress-test-desc {
  font-size: 13px;
  color: #fca5a5;
  margin: 0;
  font-weight: 400;
}

.stress-scenario-card {
  background: rgba(255,255,255,0.06);
  border: 1px solid rgba(255,255,255,0.1);
  border-radius: 14px;
  padding: 20px;
  transition: background 0.2s;
}
.stress-scenario-card--clickable {
  cursor: pointer;
  outline: none;
}
.stress-scenario-card--clickable:focus-visible {
  box-shadow: 0 0 0 3px rgba(251, 113, 133, 0.35);
}
.stress-scenario-card:hover { background: rgba(255,255,255,0.09); }
.stress-scenario-card--critical {
  border-color: rgba(220, 38, 38, 0.3);
  background: rgba(153, 27, 27, 0.15);
}
.stress-scenario-card__label {
  font-size: 12px;
  font-weight: 700;
  color: #fca5a5;
  text-transform: uppercase;
  letter-spacing: 0.06em;
  display: flex;
  align-items: center;
  gap: 8px;
}
.scenario-badge {
  background: rgba(244, 63, 94, 0.25);
  color: #fda4af;
  font-size: 10px;
  padding: 2px 8px;
  border-radius: 99px;
  font-weight: 800;
  letter-spacing: 0.05em;
}
.scenario-badge--critical {
  background: rgba(153, 27, 27, 0.4);
  color: #fca5a5;
}
.stress-sub-label { font-size: 11px; color: #94a3b8; margin-bottom: 4px; }
.stress-value-primary { font-size: 18px; font-weight: 800; color: #ffffff; letter-spacing: -0.02em; }
.npf-before {
  font-size: 15px;
  font-weight: 600;
  color: #64748b;
  text-decoration: line-through;
}
.npf-after {
  font-size: 24px;
  font-weight: 900;
  letter-spacing: -0.03em;
}
.npf-after--warn { color: #fbbf24; }
.npf-after--critical { color: #f87171; }
.stress-progress-track {
  height: 8px;
  background: rgba(255,255,255,0.08);
  border-radius: 99px;
  overflow: hidden;
}
.stress-progress-fill {
  height: 100%;
  border-radius: 99px;
  transition: width 0.8s cubic-bezier(0.4, 0, 0.2, 1);
}

/* --- Aging Rows ------------------------------------ */
.stress-detail-button {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  border: 1px solid rgba(253, 164, 175, 0.34);
  background: rgba(255, 255, 255, 0.08);
  color: #ffe4e6;
  border-radius: 999px;
  padding: 8px 12px;
  font-size: 12px;
  font-weight: 800;
  letter-spacing: -0.01em;
  transition: all 0.2s ease;
}
.stress-detail-button:hover {
  background: rgba(255, 255, 255, 0.14);
  border-color: rgba(253, 164, 175, 0.55);
  transform: translateY(-1px);
}
.stress-detail-button--critical {
  border-color: rgba(248, 113, 113, 0.38);
}
.stress-detail-dialog {
  border-radius: 22px !important;
  overflow: hidden;
  border: 1px solid rgba(148, 163, 184, 0.22);
}
.stress-detail-dialog__header {
  display: flex;
  justify-content: space-between;
  gap: 20px;
  padding: 24px 26px;
  background: linear-gradient(135deg, #fff1f2 0%, #ffffff 55%, #eef2ff 100%);
  border-bottom: 1px solid #e2e8f0;
}
.stress-detail-dialog__eyebrow {
  color: #be123c;
  font-size: 11px;
  font-weight: 900;
  letter-spacing: 0.12em;
  text-transform: uppercase;
  margin-bottom: 6px;
}
.stress-detail-dialog__title {
  margin: 0;
  color: #0f172a;
  font-size: 22px;
  font-weight: 900;
  letter-spacing: -0.04em;
}
.stress-detail-dialog__subtitle {
  margin: 7px 0 0;
  color: #64748b;
  font-size: 13px;
  line-height: 1.55;
  max-width: 760px;
}
.stress-detail-dialog__close {
  width: 38px;
  height: 38px;
  border: 1px solid #e2e8f0;
  border-radius: 12px;
  background: rgba(255,255,255,0.8);
  color: #475569;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  transition: all 0.2s ease;
}
.stress-detail-dialog__close:hover {
  background: #fff;
  color: #e11d48;
}
.stress-detail-summary {
  display: grid;
  grid-template-columns: repeat(3, minmax(0, 1fr));
  gap: 12px;
  padding: 16px 26px;
  background: #f8fafc;
  border-bottom: 1px solid #e2e8f0;
}
.stress-detail-summary__item {
  border: 1px solid #e2e8f0;
  background: #ffffff;
  border-radius: 14px;
  padding: 14px 16px;
}
.stress-detail-summary__item span {
  display: block;
  color: #64748b;
  font-size: 11px;
  font-weight: 800;
  text-transform: uppercase;
  letter-spacing: 0.06em;
  margin-bottom: 5px;
}
.stress-detail-summary__item strong {
  color: #0f172a;
  font-size: 18px;
  font-weight: 900;
  letter-spacing: -0.03em;
}
.stress-detail-table-wrap {
  overflow: auto;
  max-height: 62vh;
}
.stress-detail-table {
  width: 100%;
  border-collapse: separate;
  border-spacing: 0;
  min-width: 1120px;
}
.stress-detail-table th {
  position: sticky;
  top: 0;
  z-index: 1;
  background: #ffffff;
  color: #64748b;
  text-align: left;
  font-size: 11px;
  font-weight: 900;
  text-transform: uppercase;
  letter-spacing: 0.06em;
  padding: 13px 14px;
  border-bottom: 1px solid #e2e8f0;
}
.stress-detail-table td {
  padding: 15px 14px;
  border-bottom: 1px solid #f1f5f9;
  vertical-align: top;
  color: #334155;
  font-size: 13px;
}
.stress-detail-table tbody tr:hover {
  background: #fff7f8;
}
.stress-rank-badge {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  min-width: 34px;
  height: 28px;
  border-radius: 10px;
  background: #ffe4e6;
  color: #be123c;
  font-size: 12px;
  font-weight: 900;
}
.stress-debtor-name {
  color: #0f172a;
  font-size: 14px;
  font-weight: 900;
  letter-spacing: -0.02em;
}
.stress-debtor-meta {
  color: #64748b;
  font-size: 11px;
  line-height: 1.45;
  margin-top: 3px;
}
.stress-cell-main {
  color: #0f172a;
  font-weight: 800;
  letter-spacing: -0.01em;
}
.stress-kol-chip {
  display: inline-flex;
  border-radius: 999px;
  padding: 5px 9px;
  font-size: 11px;
  font-weight: 900;
  white-space: nowrap;
  background: #e2e8f0;
  color: #475569;
}
.stress-kol-chip--1 { background: #dcfce7; color: #047857; }
.stress-kol-chip--2 { background: #fef3c7; color: #b45309; }
.stress-kol-chip--3 { background: #ffedd5; color: #c2410c; }
.stress-kol-chip--4 { background: #ffe4e6; color: #be123c; }
.stress-kol-chip--5 { background: #fecdd3; color: #9f1239; }

@media (max-width: 768px) {
  .stress-detail-summary {
    grid-template-columns: 1fr;
    padding: 14px;
  }
  .stress-detail-dialog__header {
    padding: 20px 16px;
  }
}

.aging-row {
  display: flex;
  position: relative;
  border-bottom: 1px solid #f1f5f9;
  transition: background 0.15s;
}
.aging-row:hover { background: #f8fafc; }
.aging-row--stage3 { background: rgba(255, 241, 242, 0.4); }
.aging-row--stage3:hover { background: rgba(255, 241, 242, 0.7); }
.aging-row__indicator {
  width: 4px;
  flex-shrink: 0;
  align-self: stretch;
}
.aging-row__body {
  flex: 1;
  display: flex;
  align-items: center;
  justify-content: space-between;
  flex-wrap: wrap;
  gap: 12px;
  padding: 18px 24px;
}
.aging-avatar {
  width: 36px;
  height: 36px;
  border-radius: 10px;
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
}
.aging-cat-name { font-size: 14px; font-weight: 700; color: #1e293b; }
.aging-cat-sub { font-size: 11px; color: #94a3b8; font-weight: 500; margin-top: 1px; }
.aging-row__financials { display: flex; align-items: center; gap: 24px; text-align: right; }
.aging-os { font-size: 15px; font-weight: 700; color: #334155; }
.aging-ecl-wrap { text-align: right; }
.aging-ecl-label { font-size: 10px; color: #94a3b8; font-weight: 600; margin-bottom: 4px; text-transform: uppercase; letter-spacing: 0.04em; }
.aging-ecl-chip {
  display: inline-flex;
  align-items: center;
  font-size: 12px;
  font-weight: 800;
  padding: 4px 12px;
  border-radius: 99px;
}
.aging-ecl-chip--stage1 { background: #ecfdf5; color: #059669; }
.aging-ecl-chip--stage2 { background: #fffbeb; color: #d97706; }
.aging-ecl-chip--stage3 { background: #fff1f2; color: #e11d48; }

/* --- Data Table ------------------------------------ */
.data-table {
  width: 100%;
  border-collapse: separate;
  border-spacing: 0;
}
.data-table thead tr {
  background: #f8fafc;
  border-bottom: 1px solid #e2e8f0;
}
.data-table th {
  font-size: 11px;
  font-weight: 700;
  color: #94a3b8;
  text-transform: uppercase;
  letter-spacing: 0.06em;
  padding: 12px 20px;
  border-bottom: 1px solid #e2e8f0;
  white-space: nowrap;
}
.data-table td {
  padding: 14px 20px;
  border-bottom: 1px solid #f1f5f9;
  font-size: 13px;
  vertical-align: middle;
  white-space: nowrap;
}
.data-table tbody tr { transition: background 0.15s; }
.data-table tbody tr:hover { background: #f8fafc; }
.data-table tbody tr.row--danger { background: rgba(255, 241, 242, 0.5); }
.data-table tbody tr.row--danger:hover { background: rgba(255, 241, 242, 0.8); }

.ao-avatar {
  width: 32px;
  height: 32px;
  border-radius: 8px;
  background: #f1f5f9;
  color: #475569;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 11px;
  font-weight: 800;
  flex-shrink: 0;
}
.ao-avatar--danger { background: #fff1f2; color: #e11d48; }

.npf-pill {
  display: inline-flex;
  align-items: center;
  font-size: 12px;
  font-weight: 800;
  padding: 4px 12px;
  border-radius: 99px;
}
.npf-pill--safe { background: #ecfdf5; color: #059669; }
.npf-pill--danger { background: #e11d48; color: white; }

.export-btn {
  display: inline-flex;
  align-items: center;
  background: #f8fafc;
  border: 1px solid #e2e8f0;
  color: #475569;
  font-size: 12px;
  font-weight: 700;
  font-family: 'Inter', sans-serif;
  padding: 7px 14px;
  border-radius: 10px;
  cursor: pointer;
  transition: all 0.15s;
  white-space: nowrap;
}
.export-btn:hover { background: #f1f5f9; border-color: #cbd5e1; }
.export-btn:disabled,
.export-btn:disabled:hover {
  cursor: not-allowed;
  opacity: 0.58;
  background: #f8fafc;
  border-color: #e2e8f0;
  transform: none;
  box-shadow: none;
}

/* --- Akad Risk Card -------------------------------- */
.akad-risk-card {
  background: linear-gradient(135deg, #312e81 0%, #4c1d95 100%);
  border-radius: 18px;
  padding: 20px;
  display: flex;
  align-items: flex-start;
  gap: 16px;
  box-shadow: 0 8px 24px rgba(79, 70, 229, 0.25);
}
.akad-risk-card__icon {
  width: 44px;
  height: 44px;
  border-radius: 12px;
  background: rgba(255,255,255,0.15);
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
}
.akad-risk-card__label {
  font-size: 11px;
  font-weight: 700;
  color: #a5b4fc;
  text-transform: uppercase;
  letter-spacing: 0.06em;
  margin-bottom: 6px;
}
.akad-risk-card__value {
  font-size: 17px;
  font-weight: 900;
  color: #ffffff;
  letter-spacing: -0.01em;
  margin-bottom: 6px;
  line-height: 1.2;
}
.akad-risk-card__desc {
  font-size: 12px;
  color: #c4b5fd;
  font-weight: 400;
  line-height: 1.5;
}

/* --- Watchlist Card -------------------------------- */
.watchlist-card {
  background: #ffffff;
  border: 1px solid #e2e8f0;
  border-radius: 18px;
  overflow: hidden;
  box-shadow: 0 2px 8px rgba(0,0,0,0.06);
}
.watchlist-card__header {
  background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
  padding: 20px 24px;
  border-bottom: 1px solid rgba(255,255,255,0.05);
}
.watchlist-card__header-inner {
  display: flex;
  align-items: center;
  gap: 16px;
}
.watchlist-icon {
  width: 46px;
  height: 46px;
  border-radius: 12px;
  background: linear-gradient(135deg, #e11d48, #9f1239);
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
  box-shadow: 0 4px 12px rgba(225, 29, 72, 0.35);
}
.watchlist-title {
  font-size: 15px;
  font-weight: 700;
  color: #ffffff;
  letter-spacing: -0.01em;
}
.watchlist-subtitle {
  font-size: 12px;
  color: #94a3b8;
  margin-top: 2px;
}
.watchlist-table { width: 100%; }
.watchlist-row { transition: background 0.15s; }
.watchlist-row:hover { background: #f8fafc !important; }

/* Kolektibilitas Badges */
.kol-badge {
  display: inline-flex;
  align-items: center;
  font-size: 11px;
  font-weight: 800;
  padding: 4px 10px;
  border-radius: 8px;
  text-transform: uppercase;
  letter-spacing: 0.04em;
}
.kol-badge--1 { background: #ecfdf5; color: #047857; border: 1px solid #a7f3d0; }
.kol-badge--2 { background: #fef3c7; color: #b45309; border: 1px solid #fde68a; }
.kol-badge--3 { background: #fff7ed; color: #ea580c; border: 1px solid #fed7aa; }
.kol-badge--4 { background: #fff1f2; color: #f43f5e; border: 1px solid #fecdd3; }
.kol-badge--5 { background: #e11d48; color: white; }
.kol-badge--subtotal,
.kol-badge--total,
.kol-badge--aba,
.kol-badge--aba-macet {
  background: #eef2ff;
  color: #4338ca;
  border: 1px solid #c7d2fe;
}

.tunggak-badge {
  display: inline-flex;
  align-items: center;
  background: #fff1f2;
  color: #e11d48;
  font-size: 11px;
  font-weight: 700;
  padding: 4px 10px;
  border-radius: 8px;
}

/* --- Empty State ----------------------------------- */
.empty-state {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  color: #94a3b8;
  text-align: center;
  padding: 32px;
}
.empty-state p { font-size: 14px; font-weight: 500; margin: 0; }

/* --- Chart Loading --------------------------------- */
.chart-loading {
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 48px;
  width: 100%;
}

/* --- Max Width ------------------------------------- */
.max-w-7xl { max-width: 1280px; }
.mx-auto { margin-left: auto; margin-right: auto; }
.px-6 { padding-left: 24px; padding-right: 24px; }
.py-8 { padding-top: 32px; padding-bottom: 32px; }
.pt-7 { padding-top: 28px; }
.pb-16 { padding-bottom: 64px; }
.mb-6 { margin-bottom: 24px; }
.mb-8 { margin-bottom: 32px; }
.mt-6 { margin-top: 24px; }
.mt-3 { margin-top: 12px; }
.w-100 { width: 100%; }
.gap-2 { gap: 8px; }
.gap-3 { gap: 12px; }
.gap-4 { gap: 16px; }
.gap-5 { gap: 20px; }
.gap-6 { gap: 24px; }
.pa-4 { padding: 16px; }
.pa-16 { padding: 64px; }
.mr-1 { margin-right: 4px; }
.mr-2 { margin-right: 8px; }
.ml-2 { margin-left: 8px; }
.font-weight-bold { font-weight: 700; }
.font-weight-semibold { font-weight: 600; }
.font-weight-medium { font-weight: 500; }
.text-center { text-align: center; }
.text-right { text-align: right; }
.text-left { text-align: left; }
.d-flex { display: flex; }
.align-center { align-items: center; }
.align-start { align-items: flex-start; }
.justify-center { justify-content: center; }
.justify-space-between { justify-content: space-between; }
.flex-grow-1 { flex-grow: 1; }
.flex-shrink-0 { flex-shrink: 0; }
.flex-column { flex-direction: column; }
.overflow-x-auto {
  overflow-x: auto;
  scrollbar-width: thin;
  scrollbar-color: #94a3b8 #f1f5f9;
}
.overflow-x-auto::-webkit-scrollbar {
  height: 8px;
}
.overflow-x-auto::-webkit-scrollbar-track {
  background: #f1f5f9;
  border-radius: 999px;
}
.overflow-x-auto::-webkit-scrollbar-thumb {
  background: #94a3b8;
  border-radius: 999px;
}
.overflow-hidden { overflow: hidden; }
.flex-wrap { flex-wrap: wrap; }

.action-queue-card .watchlist-card__header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 16px;
}

.watchlist-icon--danger {
  background: linear-gradient(135deg, #dc2626, #7f1d1d);
}

.action-queue-summary {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(130px, 1fr));
  gap: 12px;
  padding: 18px 24px 0;
}

.action-queue-summary > div {
  border: 1px solid #e2e8f0;
  border-radius: 14px;
  background: linear-gradient(180deg, #ffffff, #f8fafc);
  padding: 14px 16px;
}

.action-queue-summary span,
.action-priority-cell small {
  display: block;
  font-size: 10px;
  font-weight: 800;
  color: #64748b;
  text-transform: uppercase;
  letter-spacing: 0.06em;
}

.action-queue-summary strong {
  display: block;
  margin-top: 6px;
  color: #0f172a;
  font-size: 15px;
  font-weight: 900;
}

.action-queue-table {
  margin: 0 24px 20px;
  width: calc(100% - 48px);
}

.action-priority-cell {
  display: flex;
  flex-direction: column;
  gap: 5px;
}

.action-priority-cell strong {
  color: #0f172a;
  font-size: 12px;
}

.action-severity-chip {
  display: inline-flex;
  width: fit-content;
  align-items: center;
  border-radius: 999px;
  padding: 4px 9px;
  font-size: 10px;
  font-weight: 900;
  text-transform: uppercase;
  letter-spacing: 0.06em;
}

.action-severity-chip--critical {
  background: #be123c;
  color: #ffffff;
}

.action-severity-chip--high {
  background: #fff1f2;
  color: #be123c;
  border: 1px solid #fecdd3;
}

.action-severity-chip--medium {
  background: #fffbeb;
  color: #b45309;
  border: 1px solid #fde68a;
}

.action-severity-chip--watch {
  background: #eef2ff;
  color: #4338ca;
  border: 1px solid #c7d2fe;
}

.action-signal-wrap {
  display: flex;
  flex-wrap: wrap;
  gap: 6px;
  max-width: 280px;
}

.action-signal-wrap span {
  display: inline-flex;
  border-radius: 999px;
  background: #f1f5f9;
  color: #334155;
  padding: 5px 9px;
  font-size: 11px;
  font-weight: 800;
}

.action-text {
  color: #334155;
  font-size: 12px;
  line-height: 1.45;
  min-width: 260px;
}

.workflow-cell {
  display: flex;
  flex-direction: column;
  gap: 5px;
  min-width: 150px;
}

.workflow-cell small {
  color: #64748b;
  font-size: 11px;
  font-weight: 700;
}

.workflow-status-chip {
  display: inline-flex;
  width: fit-content;
  align-items: center;
  border-radius: 999px;
  padding: 5px 10px;
  font-size: 10px;
  font-weight: 900;
  text-transform: uppercase;
  letter-spacing: 0.06em;
}

.workflow-status--open {
  background: #f1f5f9;
  color: #475569;
  border: 1px solid #cbd5e1;
}

.workflow-status--progress {
  background: #eff6ff;
  color: #1d4ed8;
  border: 1px solid #bfdbfe;
}

.workflow-status--waiting {
  background: #fffbeb;
  color: #b45309;
  border: 1px solid #fde68a;
}

.workflow-status--done {
  background: #ecfdf5;
  color: #047857;
  border: 1px solid #a7f3d0;
}

.workflow-status--waived {
  background: #f5f3ff;
  color: #6d28d9;
  border: 1px solid #ddd6fe;
}

.action-workflow-dialog {
  border-radius: 22px;
  overflow: hidden;
  border: 1px solid #e2e8f0;
}

.action-workflow-dialog__header {
  display: flex;
  justify-content: space-between;
  gap: 18px;
  padding: 24px;
  background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
  border-bottom: 1px solid rgba(255, 255, 255, 0.08);
}

.action-workflow-context {
  display: grid;
  grid-template-columns: repeat(3, minmax(0, 1fr));
  gap: 12px;
}

.action-workflow-context > div {
  border: 1px solid #e2e8f0;
  border-radius: 14px;
  background: #f8fafc;
  padding: 12px 14px;
}

.action-workflow-context span {
  display: block;
  color: #64748b;
  font-size: 10px;
  font-weight: 900;
  text-transform: uppercase;
  letter-spacing: 0.06em;
}

.action-workflow-context strong {
  display: block;
  margin-top: 5px;
  color: #0f172a;
  font-size: 13px;
  font-weight: 900;
}

.sector-risk-panel,
.product-risk-panel {
  position: relative;
  overflow: hidden;
  border: 1px solid #dbeafe;
  border-radius: 22px;
  background:
    radial-gradient(circle at top right, rgba(14, 165, 233, 0.12), transparent 34%),
    linear-gradient(180deg, #ffffff 0%, #f8fbff 100%);
  box-shadow: 0 18px 48px rgba(15, 23, 42, 0.08);
}

.sector-risk-panel::before,
.product-risk-panel::before {
  content: "";
  position: absolute;
  inset: 0 0 auto 0;
  height: 5px;
  background: linear-gradient(90deg, #0f766e, #0284c7, #e11d48);
}

.sector-risk-panel__header,
.product-risk-panel__header {
  display: flex;
  align-items: flex-start;
  justify-content: space-between;
  gap: 16px;
  padding: 24px 26px 14px;
  border-bottom: 1px solid #e2e8f0;
}

.sector-risk-panel__eyebrow {
  color: #0f766e;
  font-size: 10px;
  font-weight: 900;
  text-transform: uppercase;
  letter-spacing: 0.12em;
  margin-bottom: 6px;
}

.sector-risk-panel__title {
  color: #0f172a;
  font-size: 18px;
  font-weight: 900;
  letter-spacing: -0.03em;
}

.sector-risk-panel__subtitle {
  color: #64748b;
  font-size: 12px;
  font-weight: 600;
  line-height: 1.5;
  margin-top: 4px;
  max-width: 680px;
}

.sector-risk-panel__pill {
  display: inline-flex;
  align-items: center;
  white-space: nowrap;
  border: 1px solid #bae6fd;
  border-radius: 999px;
  background: #f0f9ff;
  color: #0369a1;
  padding: 8px 12px;
  font-size: 11px;
  font-weight: 900;
  text-transform: uppercase;
  letter-spacing: 0.05em;
}

.sector-risk-kpis {
  display: grid;
  grid-template-columns: repeat(4, minmax(0, 1fr));
  gap: 12px;
  padding: 16px 26px 4px;
}

.sector-risk-kpis > div {
  border: 1px solid #e2e8f0;
  border-radius: 16px;
  background: rgba(255, 255, 255, 0.82);
  padding: 13px 14px;
  min-width: 0;
}

.sector-risk-kpis span {
  display: block;
  color: #64748b;
  font-size: 10px;
  font-weight: 900;
  text-transform: uppercase;
  letter-spacing: 0.06em;
}

.sector-risk-kpis strong {
  display: block;
  margin-top: 6px;
  color: #0f172a;
  font-size: 13px;
  font-weight: 900;
  overflow-wrap: anywhere;
}

.sector-risk-panel__body {
  padding: 8px 18px 0;
}

.sector-risk-table-wrap {
  padding: 6px 26px 24px;
}

.sector-risk-table {
  width: 100%;
  border-collapse: separate;
  border-spacing: 0;
  overflow: hidden;
  border: 1px solid #e2e8f0;
  border-radius: 16px;
  background: #ffffff;
}

.sector-risk-table th {
  background: #f8fafc;
  color: #64748b;
  font-size: 10px;
  font-weight: 900;
  padding: 12px 14px;
  text-transform: uppercase;
  letter-spacing: 0.06em;
}

.sector-risk-table td {
  border-top: 1px solid #edf2f7;
  color: #0f172a;
  font-size: 12px;
  padding: 12px 14px;
  vertical-align: middle;
}

.sector-risk-name {
  color: #0f172a;
  font-weight: 900;
}

.sector-risk-bar {
  width: 100%;
  height: 5px;
  border-radius: 999px;
  background: #f1f5f9;
  margin-top: 7px;
  overflow: hidden;
}

.sector-risk-bar span {
  display: block;
  height: 100%;
  border-radius: inherit;
  background: linear-gradient(90deg, #fb7185, #be123c);
}

.sector-ratio-chip {
  display: inline-flex;
  border-radius: 999px;
  padding: 5px 9px;
  font-size: 11px;
  font-weight: 900;
}

.sector-ratio-chip--danger {
  background: #fff1f2;
  color: #be123c;
  border: 1px solid #fecdd3;
}

.sector-ratio-chip--warning {
  background: #fffbeb;
  color: #b45309;
  border: 1px solid #fde68a;
}

.sector-ratio-chip--safe {
  background: #ecfdf5;
  color: #047857;
  border: 1px solid #a7f3d0;
}

.product-risk-chart {
  display: flex;
  align-items: center;
  justify-content: center;
  min-height: 260px;
  padding: 10px 12px 0;
}

.product-risk-list {
  display: flex;
  flex-direction: column;
  gap: 8px;
  padding: 0 18px 18px;
}

.product-risk-item {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 12px;
  border: 1px solid #e2e8f0;
  border-radius: 14px;
  background: #ffffff;
  padding: 10px 12px;
}

.product-risk-item strong {
  display: block;
  color: #0f172a;
  font-size: 12px;
  font-weight: 900;
}

.product-risk-item span {
  display: block;
  color: #64748b;
  font-size: 11px;
  font-weight: 700;
  margin-top: 2px;
}

.ews-panel {
  overflow: hidden;
  border: 1px solid #fecdd3;
  border-radius: 22px;
  background:
    radial-gradient(circle at top right, rgba(225, 29, 72, 0.10), transparent 32%),
    linear-gradient(180deg, #ffffff 0%, #fff8fa 100%);
  box-shadow: 0 18px 48px rgba(136, 19, 55, 0.10);
}

.ews-panel__header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 18px;
  background: linear-gradient(135deg, #111827 0%, #3f0b1f 100%);
  padding: 22px 26px;
}

.ews-summary-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
  gap: 12px;
  padding: 18px 24px 6px;
}

.ews-summary-grid > div {
  border: 1px solid #ffe4e6;
  border-radius: 16px;
  background: rgba(255, 255, 255, 0.86);
  padding: 14px 16px;
}

.ews-summary-grid span {
  display: block;
  color: #9f1239;
  font-size: 10px;
  font-weight: 900;
  letter-spacing: 0.07em;
  text-transform: uppercase;
}

.ews-summary-grid strong {
  display: block;
  margin-top: 6px;
  color: #0f172a;
  font-size: 14px;
  font-weight: 900;
  overflow-wrap: anywhere;
}

.ews-table {
  margin: 14px 24px 0;
  width: calc(100% - 48px);
  border: 1px solid #f1f5f9;
  border-radius: 16px;
  overflow: hidden;
}

.ews-table th {
  background: #fff1f2;
  color: #9f1239;
}

.ews-severity-cell {
  display: flex;
  flex-direction: column;
  gap: 5px;
}

.ews-severity-cell small {
  color: #64748b;
  font-size: 11px;
  font-weight: 800;
}

.ews-cover-cell {
  display: flex;
  flex-direction: column;
  align-items: flex-end;
  gap: 3px;
  min-width: 190px;
}

.ews-cover-cell strong {
  color: #0f172a;
  font-size: 13px;
  font-weight: 900;
}

.ews-cover-cell span {
  color: #64748b;
  font-size: 11px;
  font-weight: 700;
}

.ews-cover-cell small {
  display: inline-flex;
  width: fit-content;
  border-radius: 999px;
  background: #fff1f2;
  color: #be123c;
  border: 1px solid #fecdd3;
  padding: 3px 8px;
  font-size: 10px;
  font-weight: 900;
}

.risk-concentration-summary {
  position: relative;
  overflow: hidden;
  border: 1px solid #c7d2fe;
  border-radius: 24px;
  background:
    radial-gradient(circle at top left, rgba(79, 70, 229, 0.18), transparent 28%),
    radial-gradient(circle at bottom right, rgba(225, 29, 72, 0.12), transparent 32%),
    linear-gradient(135deg, #0f172a 0%, #1e1b4b 52%, #4c0519 100%);
  box-shadow: 0 24px 64px rgba(15, 23, 42, 0.22);
  color: #ffffff;
}

.risk-concentration-summary__main {
  display: flex;
  align-items: flex-start;
  justify-content: space-between;
  gap: 20px;
  padding: 26px 28px 18px;
}

.risk-concentration-summary__eyebrow {
  color: #a5b4fc;
  font-size: 10px;
  font-weight: 900;
  letter-spacing: 0.14em;
  text-transform: uppercase;
  margin-bottom: 8px;
}

.risk-concentration-summary h3 {
  color: #ffffff;
  font-size: 22px;
  font-weight: 950;
  line-height: 1.15;
  letter-spacing: -0.04em;
  margin: 0;
}

.risk-concentration-summary p {
  color: #cbd5e1;
  font-size: 13px;
  font-weight: 600;
  line-height: 1.6;
  margin: 10px 0 0;
  max-width: 980px;
}

.risk-level-chip {
  display: inline-flex;
  align-items: center;
  border-radius: 999px;
  padding: 9px 13px;
  font-size: 11px;
  font-weight: 950;
  text-transform: uppercase;
  letter-spacing: 0.08em;
  border: 1px solid rgba(255, 255, 255, 0.20);
  white-space: nowrap;
}

.risk-level-chip--critical {
  background: rgba(225, 29, 72, 0.22);
  color: #fecdd3;
}

.risk-level-chip--high {
  background: rgba(245, 158, 11, 0.22);
  color: #fde68a;
}

.risk-level-chip--watch {
  background: rgba(20, 184, 166, 0.20);
  color: #99f6e4;
}

.risk-radar-grid {
  display: grid;
  grid-template-columns: repeat(5, minmax(0, 1fr));
  gap: 12px;
  padding: 0 28px 26px;
}

.risk-radar-item {
  min-width: 0;
  border: 1px solid rgba(255, 255, 255, 0.12);
  border-radius: 18px;
  background: rgba(255, 255, 255, 0.08);
  backdrop-filter: blur(10px);
  padding: 15px;
}

.risk-radar-item--danger {
  border-color: rgba(251, 113, 133, 0.45);
  background: rgba(190, 18, 60, 0.18);
}

.risk-radar-item span {
  display: block;
  color: #a5b4fc;
  font-size: 10px;
  font-weight: 900;
  letter-spacing: 0.08em;
  text-transform: uppercase;
}

.risk-radar-item strong {
  display: block;
  color: #ffffff;
  font-size: 13px;
  font-weight: 950;
  line-height: 1.25;
  margin-top: 8px;
  overflow-wrap: anywhere;
}

.risk-radar-item small {
  display: block;
  color: #cbd5e1;
  font-size: 11px;
  font-weight: 700;
  line-height: 1.45;
  margin-top: 6px;
}

@media (max-width: 960px) {
  .action-queue-card .watchlist-card__header,
  .action-queue-summary {
    grid-template-columns: 1fr;
  }

  .action-queue-card .watchlist-card__header {
    align-items: flex-start;
    flex-direction: column;
  }

  .action-workflow-context {
    grid-template-columns: 1fr;
  }

  .ppka-operational-summary,
  .ppka-distribution-strip,
  .ppka-operational-filter {
    grid-template-columns: 1fr;
  }

  .sector-risk-panel__header,
  .product-risk-panel__header {
    flex-direction: column;
  }

  .sector-risk-kpis {
    grid-template-columns: 1fr;
  }

  .ews-panel__header {
    align-items: flex-start;
    flex-direction: column;
  }

  .risk-concentration-summary__main {
    flex-direction: column;
  }

  .risk-radar-grid {
    grid-template-columns: 1fr;
  }
}
</style>
