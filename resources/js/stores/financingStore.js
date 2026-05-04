/**
 * Financing Store - Multi-Period Comparison Engine
 * 
 * State management untuk Financing Overview page.
 * Mendukung 3 mode: Realtime, Historical, Compare
 */

import { defineStore } from 'pinia'
import { ref, computed } from 'vue'

// API Base URL
const API = '/api/v1/financing'

export const useFinancingStore = defineStore('financing', () => {
  // === STATE ===
  const realtimeData = ref(null)
  const comparisonData = ref(null)
  const trendData = ref(null)
  const multiComparisonData = ref(null)
  
  const loading = ref(false)
  const loadingRealtime = ref(false)
  const loadingCompare = ref(false)
  const loadingTrend = ref(false)
  
  const error = ref(null)
  const errorRealtime = ref(null)
  const errorCompare = ref(null)
  const errorTrend = ref(null)
  
  // UI State
  const viewMode = ref('realtime') // 'realtime' | 'compare' | 'trend'
  const comparisonPeriod = ref('2026-02')
  const comparisonPeriods = ref([]) // List of available DBs from /periods
  const multiComparisonPeriods = ref([]) // Selected DB names (v-model)
  const trendMonths = ref(12)
  const compareMetric = ref('total_os') // 'total_os' | 'total_npf' | 'npf_persen'
  
  // === GETTERS ===
  
  // Summary metrics (computed dari realtime data)
  const summary = computed(() => realtimeData.value?.summary ?? null)
  
  const totalNOA = computed(() => summary.value?.total_noa ?? 0)
  const totalOS = computed(() => summary.value?.total_os ?? 0)
  const totalNPF = computed(() => summary.value?.npf_persen ?? 0)
  const avgKolek = computed(() => summary.value?.avg_kolek ?? 0)
  const totalPPAP = computed(() => summary.value?.total_ppap ?? 0)
  
  // Distribution data
  const kolektibilitas = computed(() => realtimeData.value?.kolektibilitas ?? [])
  const segmenData = computed(() => realtimeData.value?.segmen ?? [])
  const aoData = computed(() => realtimeData.value?.ao ?? [])
  const produkData = computed(() => realtimeData.value?.produk ?? [])
  const jatuhTempoData = computed(() => realtimeData.value?.jatuh_tempo ?? [])
  const realizeKemarinData = computed(() => realtimeData.value?.realisasi_kemarin ?? [])
  
  // Active info
  const activeDatabase = computed(() => realtimeData.value?.database ?? null)
  const trendSeries = computed(() => trendData.value?.series ?? [])
  
  // Comparison (Classic 1-to-1)
  const comparison = computed(() => comparisonData.value?.comparison ?? null)
  const realtimeMetrics = computed(() => comparisonData.value?.realtime ?? null)
  const historicalMetrics = computed(() => comparisonData.value?.historical ?? null)
  
  // Loading states
  const isLoading = computed(() => loadingRealtime.value || loadingCompare.value || loadingTrend.value)
  
  // === ACTIONS ===

  /**
   * Fetch Available Database Periods
   */
  async function fetchPeriods() {
    try {
      const response = await fetch(`${API}/overview/periods`)
      const json = await response.json()
      if (json.success) {
        comparisonPeriods.value = json.data
      }
    } catch (e) {
      console.error('[FinancingStore] fetchPeriods error:', e)
    }
  }
  
  /**
   * Fetch Realtime Data
   */
  async function fetchRealtime() {
    loadingRealtime.value = true
    errorRealtime.value = null
    
    try {
      const response = await fetch(`${API}/overview/realtime`)
      const json = await response.json()
      
      if (json.success) {
        realtimeData.value = json.data
      } else {
        throw new Error(json.error ?? 'Gagal memuat data realtime')
      }
    } catch (e) {
      errorRealtime.value = e.message
    } finally {
      loadingRealtime.value = false
    }
  }
  
  /**
   * Fetch Multi-Period Comparison Data
   */
  async function fetchMultiComparison(periods = multiComparisonPeriods.value) {
    if (!periods || periods.length === 0) {
        multiComparisonData.value = null
        return
    }
    
    loadingCompare.value = true
    errorCompare.value = null
    
    try {
      const params = new URLSearchParams()
      periods.forEach(p => params.append('periods[]', p))
      
      const response = await fetch(`${API}/overview/compare?${params.toString()}`)
      const json = await response.json()
      
      if (json.success) {
        multiComparisonData.value = json.data
      } else {
        throw new Error(json.error ?? 'Gagal memuat data comparison')
      }
    } catch (e) {
      errorCompare.value = e.message
    } finally {
      loadingCompare.value = false
    }
  }

  /**
   * Classic Comparison (Backward Compatibility)
   */
  async function fetchComparison(period = comparisonPeriod.value) {
    loadingCompare.value = true
    errorCompare.value = null
    
    try {
      const response = await fetch(`${API}/overview/compare?start=${period}`)
      const json = await response.json()
      
      if (json.success) {
        comparisonData.value = json.data
      } else {
        throw new Error(json.error ?? 'Gagal memuat data comparison')
      }
    } catch (e) {
      errorCompare.value = e.message
    } finally {
      loadingCompare.value = false
    }
  }
  
  /**
   * Fetch Trend Data
   */
  async function fetchTrend(months = trendMonths.value) {
    loadingTrend.value = true
    errorTrend.value = null
    trendMonths.value = months
    
    try {
      // Use 'period' parameter as requested by user instructions
      const response = await fetch(`${API}/overview/trend?period=${months}`)
      const json = await response.json()
      if (json.success) {
        trendData.value = json.data
      }
    } catch (e) {
      errorTrend.value = e.message
    } finally {
      loadingTrend.value = false
    }
  }
  
  /**
   * Load All Data
   */
  async function loadAll() {
    loading.value = true
    error.value = null
    
    try {
      await Promise.all([
        fetchRealtime(),
        fetchPeriods(),
        fetchTrend()
      ])
    } catch (e) {
      error.value = e.message
    } finally {
      loading.value = false
    }
  }
  
  /**
   * Set View Mode
   */
  async function setViewMode(mode) {
    viewMode.value = mode
    if (mode === 'realtime' && !realtimeData.value) {
      await fetchRealtime()
    } else if (mode === 'compare' && !comparisonPeriods.value.length) {
      await fetchPeriods()
    } else if (mode === 'trend' && !trendData.value) {
      await fetchTrend()
    }
  }
  
  /**
   * Helpers
   */
  function formatRp(value) {
    if (!value && value !== 0) return '-'
    const num = parseFloat(value)
    if (Math.abs(num) >= 1e9) return `Rp ${(num / 1e9).toFixed(2)} M`
    if (Math.abs(num) >= 1e6) return `Rp ${(num / 1e6).toFixed(1)} Jt`
    return `Rp ${num.toLocaleString('id-ID')}`
  }

  return {
    // State
    realtimeData, comparisonData, trendData, multiComparisonData,
    loading, loadingRealtime, loadingCompare, loadingTrend,
    error, errorRealtime, errorCompare, errorTrend,
    viewMode, comparisonPeriod, comparisonPeriods, multiComparisonPeriods, trendMonths, compareMetric,
    
    // Getters
    summary, totalNOA, totalOS, totalNPF, avgKolek, totalPPAP,
    kolektibilitas, segmenData, aoData, produkData, jatuhTempoData, realizeKemarinData,
    activeDatabase, trendSeries, comparison, realtimeMetrics, historicalMetrics,
    isLoading,
    
    // Actions
    fetchRealtime, fetchComparison, fetchMultiComparison, fetchPeriods, fetchTrend,
    loadAll, setViewMode, formatRp
  }
})
