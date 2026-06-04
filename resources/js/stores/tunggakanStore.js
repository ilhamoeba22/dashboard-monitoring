import { defineStore } from 'pinia'
import { ref, computed } from 'vue'
import { formatExactRupiah } from '@/utils/money'

const API = '/api/v1/financing/tunggakan'

export const useTunggakanStore = defineStore('tunggakan', () => {
  // State
  const jatuhTempoData = ref([])
  const collMonitoringData = ref([])
  
  const loadingJatuhTempo = ref(false)
  const loadingCollMonitoring = ref(false)
  
  const errorJatuhTempo = ref(null)
  const errorCollMonitoring = ref(null)
  const periodMeta = ref({
    requested_period: null,
    active_period: null,
    is_historical: false,
    period_available: true,
    source_table: 'TOFLMB',
    source_database: null,
    message: null,
  })
  
  // Filters
  const selectedCabang = ref('Semua Cabang')
  const selectedAo = ref('Semua AO')
  const selectedTahun = ref(null)
  const selectedBulan = ref(null)
  
  // Getters untuk UI Jatuh Tempo
  const totalJatuhTempo = computed(() => jatuhTempoData.value.length)
  const totalTagihanPokok = computed(() => jatuhTempoData.value.reduce((acc, curr) => acc + parseFloat(curr.tgkmdl || 0), 0))
  const totalTagihanMargin = computed(() => jatuhTempoData.value.reduce((acc, curr) => acc + parseFloat(curr.tgkmgn || 0), 0))
  
  // Menghitung berapa nasabah yang saldonya CUKUP vs KURANG
  const saldoStatus = computed(() => {
    let cukup = 0
    let kurang = 0
    
    jatuhTempoData.value.forEach(item => {
      const tagihanTotal = parseFloat(item.tgkmdl || 0) + parseFloat(item.tgkmgn || 0)
      const saldoEfektif = parseFloat(item.saving_balance || 0)
      
      if (saldoEfektif >= tagihanTotal) {
        cukup++
      } else {
        kurang++
      }
    })
    
    return { cukup, kurang }
  })

  // Getters untuk UI Coll Monitoring
  const totalCollMonitoring = computed(() => collMonitoringData.value.length)
  const totalOsProyeksiBerisiko = computed(() => {
    return collMonitoringData.value
      .filter(item => parseInt(item.colbaru_final_eom) > parseInt(item.colbaru_final_curr) && parseInt(item.colbaru_final_eom) >= 3)
      .reduce((acc, curr) => acc + parseFloat(curr.osmdlc || 0), 0)
  })
  
  // Actions
  async function fetchJatuhTempo() {
    loadingJatuhTempo.value = true
    errorJatuhTempo.value = null
    
    try {
      const params = new URLSearchParams()
      if (selectedCabang.value !== 'Semua Cabang') params.append('cabang', selectedCabang.value)
      if (selectedAo.value !== 'Semua AO') params.append('ao', selectedAo.value)
      if (selectedTahun.value) params.append('tahun', selectedTahun.value)
      if (selectedBulan.value) params.append('bulan', selectedBulan.value)
        
      const response = await fetch(`${API}/jatuh-tempo?${params.toString()}`)
      const json = await response.json()
      
      if (json.success) {
        periodMeta.value = json.period_meta || periodMeta.value
        const requested = String(periodMeta.value?.requested_period || '')
        if (requested.length === 6) {
          selectedTahun.value = Number(requested.slice(0, 4))
          selectedBulan.value = Number(requested.slice(4, 6))
        }
        jatuhTempoData.value = json.data
      } else {
        throw new Error(json.error || 'Gagal memuat data jatuh tempo')
      }
    } catch (e) {
      errorJatuhTempo.value = e.message
      console.error(e)
    } finally {
      loadingJatuhTempo.value = false
    }
  }

  async function fetchCollMonitoring() {
    loadingCollMonitoring.value = true
    errorCollMonitoring.value = null
    
    try {
      const params = new URLSearchParams()
      if (selectedCabang.value !== 'Semua Cabang') params.append('cabang', selectedCabang.value)
      if (selectedAo.value !== 'Semua AO') params.append('ao', selectedAo.value)
        
      const response = await fetch(`${API}/coll-monitoring?${params.toString()}`)
      const json = await response.json()
      
      if (json.success) {
        collMonitoringData.value = json.data
      } else {
        throw new Error(json.error || 'Gagal memuat data coll monitoring')
      }
    } catch (e) {
      errorCollMonitoring.value = e.message
      console.error(e)
    } finally {
      loadingCollMonitoring.value = false
    }
  }
  
  // Helper
  function formatRp(value) {
    return formatExactRupiah(value, '—')
  }
  
  function formatShortRp(value) {
    return formatExactRupiah(value, '—')
  }

  return {
    jatuhTempoData, collMonitoringData,
    periodMeta,
    loadingJatuhTempo, loadingCollMonitoring,
    errorJatuhTempo, errorCollMonitoring,
    selectedCabang, selectedAo, selectedTahun, selectedBulan,
    totalJatuhTempo, totalTagihanPokok, totalTagihanMargin, saldoStatus,
    totalCollMonitoring, totalOsProyeksiBerisiko,
    fetchJatuhTempo, fetchCollMonitoring,
    formatRp, formatShortRp
  }
})
