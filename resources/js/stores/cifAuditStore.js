import { defineStore } from 'pinia'
import { ref, computed } from 'vue'

export const useCifAuditStore = defineStore('cifAudit', () => {
  // State
  const activeTab = ref('individu') // 'individu' or 'badan_hukum'
  const isLoading = ref(false)
  const auditData = ref([])
  const totalItems = ref(0)
  
  // Filters
  const filters = ref({
    cabang: 'ALL',
    status: 'ALL', // 'ALL', 'Lengkap', 'Belum Lengkap', 'Cek Ulang'
    search: '',
    page: 1,
    perPage: 50
  })

  // Getters
  const getAuditData = computed(() => auditData.value)

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

  // Generate Mock Data for tables
  const generateMockData = (type, count) => {
    const data = []
    for (let i = 0; i < count; i++) {
      const isIndividu = activeTab.value === 'individu'
      const stskawin = Math.random() > 0.3 ? 'KAWIN' : 'LAJANG'
      
      const row = {
        id: i + 1,
        nocif: `CIF${String(i + 1).padStart(7, '0')}`,
        namanasabah: `Nasabah ${type} ${i + 1}${Math.random() > 0.9 ? "'" : ''}`,
        noktp: `320101${String(Math.floor(Math.random() * 10000000000)).padStart(10, '0')}`,
        jk: Math.random() > 0.5 ? 'L' : 'P',
        tempat_lahir: 'JAKARTA',
        tgllhr_ktp: '1990-01-01',
        tgllhr: '1990-01-01',
        usia: 34,
        nohp: Math.random() > 0.1 ? `0812${String(Math.floor(Math.random() * 100000000)).padStart(8, '0')}` : '-',
        sandi_dati: '0123',
        nama_ibu: Math.random() > 0.1 ? `Ibu Nasabah ${i + 1}` : 'BINTI',
        ket_stskawin: isIndividu ? stskawin : '-',
        ket_kdhub: (isIndividu && stskawin === 'KAWIN') ? 'SUAMI' : '-',
        nama_pasangan: (isIndividu && stskawin === 'KAWIN') ? `Pasangan ${i + 1}` : '-',
        nik_pasangan: (isIndividu && stskawin === 'KAWIN') ? `320101${String(Math.floor(Math.random() * 10000000000)).padStart(10, '0')}` : '-',
        hp_pasangan: (isIndividu && stskawin === 'KAWIN') ? '08123456789' : '-',
        tgllhr_pasangan: (isIndividu && stskawin === 'KAWIN') ? '1992-02-02' : '-',
        usia_pasangan: (isIndividu && stskawin === 'KAWIN') ? 32 : '-',
        alamat: `Jl. Contoh Alamat No ${i + 1}`,
        kelurahan: 'KELURAHAN A',
        kecamatan: 'KECAMATAN B',
        kota: 'KOTA C',
        kodepos: '12345',
        nama_marketing: `AO ${Math.floor(Math.random() * 10) + 1}`,
        cabang: `Cabang ${Math.floor(Math.random() * 5) + 1}`,
      }

      if (!isIndividu) {
        row.npwp = `01.${String(Math.floor(Math.random() * 1000)).padStart(3, '0')}.${String(Math.floor(Math.random() * 1000)).padStart(3, '0')}.1-000.000`
      }

      data.push(row)
    }
    return data
  }

  // Mock API Fetchers
  const fetchPembiayaan = async () => {
    isLoading.value = true
    try {
      await new Promise(r => setTimeout(r, 600)) // delay
      auditData.value = generateMockData('Pembiayaan', 50)
      totalItems.value = 245
    } catch (e) {
      console.error(e)
    } finally {
      isLoading.value = false
    }
  }

  const fetchTabungan = async () => {
    isLoading.value = true
    try {
      await new Promise(r => setTimeout(r, 600)) // delay
      auditData.value = generateMockData('Tabungan', 50)
      totalItems.value = 180
    } catch (e) {
      console.error(e)
    } finally {
      isLoading.value = false
    }
  }

  const fetchDeposito = async () => {
    isLoading.value = true
    try {
      await new Promise(r => setTimeout(r, 600)) // delay
      auditData.value = generateMockData('Deposito', 50)
      totalItems.value = 85
    } catch (e) {
      console.error(e)
    } finally {
      isLoading.value = false
    }
  }

  return {
    activeTab,
    isLoading,
    auditData,
    totalItems,
    filters,
    getAuditData,
    setTab,
    setFilters,
    setPage,
    fetchPembiayaan,
    fetchTabungan,
    fetchDeposito
  }
})
