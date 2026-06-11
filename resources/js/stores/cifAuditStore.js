import { defineStore } from 'pinia'
import { ref, computed } from 'vue'

export const useCifAuditStore = defineStore('cifAudit', () => {
  const isLoading = ref(false)
  const auditData = ref([])
  const totalItems = ref(0)
  const errorMessage = ref('')
  const activeTab = ref('individu')
  
  // Filters
  const filters = ref({
    cabang: 'ALL',
    status: 'ALL', // 'ALL', 'Lengkap', 'Belum Lengkap', 'Cek Ulang'
    search: '',
    page: 1,
    perPage: 50
  })

  const getAuditData = computed(() => auditData.value)
  const golcust = computed(() => activeTab.value === 'badan_hukum' ? 'B' : 'I')
  const cabangOptions = computed(() => {
    const branches = auditData.value
      .map(item => item.cabang)
      .filter(Boolean)
      .filter((value, index, source) => source.indexOf(value) === index)
      .sort((left, right) => String(left).localeCompare(String(right), 'id-ID'))

    return ['ALL', ...branches]
  })

  // Actions
  const setTab = (tab) => {
    activeTab.value = tab
    filters.value.page = 1
  }

  const setFilters = (newFilters) => {
    filters.value = { ...filters.value, ...newFilters, page: 1 }
  }

  const setPage = (page) => {
    filters.value.page = page
  }

  const resetFilters = () => {
    filters.value = {
      cabang: 'ALL',
      status: 'ALL',
      search: '',
      page: 1,
      perPage: 50
    }
  }

  const buildQuery = () => {
    const params = new URLSearchParams({
      perPage: String(filters.value.perPage || 50),
      golcust: golcust.value,
    })

    if (filters.value.cabang && filters.value.cabang !== 'ALL') params.append('cabang', filters.value.cabang)
    if (filters.value.status && filters.value.status !== 'ALL') params.append('status', filters.value.status)
    if (filters.value.search) params.append('search', filters.value.search)

    return params.toString()
  }

  const fetchAudit = async (segment) => {
    isLoading.value = true
    errorMessage.value = ''
    try {
      const response = await fetch(`/api/v1/cif/audit/${segment}?${buildQuery()}`)
      const json = await response.json()
      if (!response.ok || !json.success) {
        throw new Error(json.message || `Gagal memuat audit CIF ${segment}.`)
      }
      auditData.value = Array.isArray(json.data)
        ? json.data.map((item, index) => ({
            ...item,
            id: index + 1,
            status_cif: item.status_cif || item.status || '',
          }))
        : []
      totalItems.value = Number(json.meta?.total ?? json.data?.length ?? 0)
    } catch (error) {
      auditData.value = []
      totalItems.value = 0
      errorMessage.value = error.message || 'Gagal memuat data audit CIF.'
    } finally {
      isLoading.value = false
    }
  }

  const fetchPembiayaan = () => fetchAudit('pembiayaan')
  const fetchTabungan = () => fetchAudit('tabungan')
  const fetchDeposito = () => fetchAudit('deposito')

  return {
    activeTab,
    isLoading,
    auditData,
    totalItems,
    errorMessage,
    filters,
    getAuditData,
    cabangOptions,
    setTab,
    setFilters,
    setPage,
    resetFilters,
    fetchPembiayaan,
    fetchTabungan,
    fetchDeposito
  }
})
