<script setup>
import { ref, onMounted, watch } from 'vue'
import DefaultLayout from '@/layouts/default.vue'

defineOptions({ layout: DefaultLayout })



// State
const loading = ref(true)
const tableLoading = ref(false)
const error = ref(null)
const search = ref('')
const groupBy = ref('kolektibilitas')
const data = ref([])
const rekapData = ref([])
const jatuhTempo = ref([])
const activeTab = ref('nominatif')
const cursor = ref(null)
const hasMore = ref(true)
const loadMoreLoading = ref(false)
const totalCount = ref(0)

const headers = [
  { title: 'No.', key: 'no', sortable: false, width: '50px', align: 'center' },
  { title: 'No. CIF', key: 'nocif', sortable: true, width: '100px' },
  { title: 'No. Kontrak', key: 'nokontrak', sortable: true, width: '130px' },
  { title: 'Nama Nasabah', key: 'nama', sortable: true },
  { title: 'Umur', key: 'umur', sortable: true, width: '60px', align: 'center' },
  { title: 'Kel. Umur', key: 'kelompok_umur', sortable: true, width: '80px', align: 'center' },
  { title: 'Segmen', key: 'segmen', sortable: true, width: '120px' },
  { title: 'No. Akad', key: 'noakad', sortable: true, width: '130px' },
  { title: 'Tgl Efektif', key: 'tgleff', sortable: true, width: '100px' },
  { title: 'JW (Bln)', key: 'jw', sortable: true, width: '70px', align: 'center' },
  { title: 'Sisa JW', key: 'sisajw', sortable: true, width: '70px', align: 'center' },
  { title: 'Tgl Expired', key: 'tglexp', sortable: true, width: '100px' },
  { title: 'Modal Awal', key: 'mdlawal', sortable: true, align: 'end' },
  { title: 'O/S Modal', key: 'osmdlc', sortable: true, align: 'end' },
  { title: 'Tunggak Modal', key: 'tgkmdl', sortable: true, align: 'end' },
  { title: 'Tunggak Margin', key: 'tgkmgn', sortable: true, align: 'end' },
  { title: 'Kolektibilitas', key: 'colbaru', sortable: true, align: 'center', width: '100px' },
  { title: 'Tgl Macet', key: 'tglmacet', sortable: true, width: '100px' },
  { title: 'Saldo Tabungan', key: 'saldo_netto', sortable: true, align: 'end' },
  { title: 'Ket. Debet', key: 'keterangan_debet', sortable: true, align: 'center', width: '90px' },
  { title: 'Tjgkn vs Tab.', key: 'tunggakan_vs_tabungan', sortable: true, align: 'end' },
  { title: 'Nilai Jaminan', key: 'htgagun', sortable: true, align: 'end' },
  { title: 'PPKA', key: 'ppap', sortable: true, align: 'end' },
  { title: 'AO', key: 'ao', sortable: true, width: '100px' },
  { title: 'Cabang', key: 'cabang', sortable: true, width: '100px' },
  { title: 'Wilayah', key: 'wilayah', sortable: true, width: '100px' },
  { title: 'Alamat', key: 'alamat', sortable: false },
]

const rekapHeaders = [
  { title: 'Grup', key: 'label', sortable: true },
  { title: 'Jumlah Rekening', key: 'noa', sortable: true, align: 'end' },
  { title: 'Total Plafond', key: 'total_mdlawal', sortable: true, align: 'end' },
  { title: 'Total Outstanding', key: 'total_osmdlc', sortable: true, align: 'end' },
  { title: 'Avg Rate', key: 'avg_rate', sortable: true, align: 'end' },
]

const jtHeaders = [
  { title: 'No. Kontrak', key: 'nokontrak', width: '150px' },
  { title: 'Nama', key: 'nama' },
  { title: 'Jatuh Tempo', key: 'tglexp', width: '130px' },
  { title: 'Sisa Pokok', key: 'osmdlc', align: 'end' },
  { title: 'Status', key: 'status', align: 'center' },
]

const groupOptions = [
  { value: 'kolektibilitas', title: 'Kolektibilitas' },
  { value: 'cabang', title: 'Per Cabang' },
  { value: 'ao', title: 'Per AO' },
  { value: 'produk', title: 'Per Produk' },
  { value: 'segmen', title: 'Per Segmen' },
]

function formatRp(v) {
  if (!v && v !== 0) return '—'
  const n = parseFloat(v)
  if (isNaN(n)) return '—'
  if (n >= 1e9) return `Rp ${(n / 1e9).toFixed(2)} M`
  if (n >= 1e6) return `Rp ${(n / 1e6).toFixed(1)} Jt`
  return `Rp ${n.toLocaleString('id-ID')}`
}

function formatDate(v) {
  if (!v || v === '0000-00-00' || v === '1900-01-01') return '—'
  try {
    // Format: YYYY-MM-DD -> DD MMM YYYY
    if (/^\d{4}-\d{2}-\d{2}$/.test(v)) {
      const [y, m, d] = v.split('-')
      const months = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des']
      return `${parseInt(d)} ${months[parseInt(m) - 1]} ${y}`
    }
    // Format: YYYYMMDD (integer/string)
    if (/^\d{8}$/.test(String(v))) {
      const y = String(v).substring(0, 4)
      const m = String(v).substring(4, 6)
      const d = String(v).substring(6, 8)
      const months = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des']
      return `${parseInt(d)} ${months[parseInt(m) - 1]} ${y}`
    }
    return v
  } catch (e) {
    return '—'
  }
}

function kolBadge(kol) {
  const k = String(kol).toLowerCase()
  if (k.includes('lancar') && !k.includes('kurang')) return 'success'
  if (k.includes('kurang')) return 'info'
  if (k.includes('diragukan')) return 'warning'
  if (k.includes('macet') || k.includes('5') || kol === '4') return 'error'
  if (kol === '1') return 'success'
  if (kol === '2') return 'info'
  if (kol === '3') return 'warning'
  return 'secondary'
}

async function fetchNominatif(reset = false) {
  if (reset) {
    data.value = []
    cursor.value = null
    hasMore.value = true
  }
  
  tableLoading.value = true
  try {
    const params = new URLSearchParams({ per_page: 500 })
    if (search.value) params.append('search', search.value)
    if (cursor.value) params.append('cursor', cursor.value)
    const url = `/api/v1/financing/nominative?${params}`
    const r = await fetch(url)
    const json = await r.json()
    
    if (json.data?.data) {
      const newData = json.data.data.filter(item => !data.value.some(existing => existing.nokontrak === item.nokontrak))
      data.value = [...data.value, ...newData]
      cursor.value = json.data.meta?.next_cursor ?? null
      hasMore.value = json.data.meta?.has_more ?? false
      totalCount.value = json.data.meta?.total ?? data.value.length
    } else if (json.data) {
      data.value = json.data
      totalCount.value = json.data.length
      hasMore.value = false
    }
  } catch (e) { error.value = e.message } finally { tableLoading.value = false }
}

async function loadMore() {
  if (loadMoreLoading.value || !hasMore.value) return
  loadMoreLoading.value = true
  try {
    await fetchNominatif(false)
  } finally { loadMoreLoading.value = false }
}

async function fetchRekap() {
  tableLoading.value = true
  try {
    const r = await fetch(`/api/v1/financing/rekapitulasi?group_by=${groupBy.value}`)
    const json = await r.json()
    rekapData.value = json.data ?? []
  } catch (e) { error.value = e.message } finally { tableLoading.value = false }
}

async function fetchJatuhTempo() {
  tableLoading.value = true
  try {
    const r = await fetch(`/api/v1/financing/jatuh-tempo?per_page=50`)
    const json = await r.json()
    jatuhTempo.value = json.data?.data ?? json.data ?? []
  } catch (e) { error.value = e.message } finally { tableLoading.value = false }
}

onMounted(async () => {
  loading.value = true
  await fetchNominatif()
  loading.value = false
})

watch(activeTab, (tab) => {
  if (tab === 'nominatif') fetchNominatif(true)
  else if (tab === 'rekapitulasi') fetchRekap()
  else if (tab === 'jatuh-tempo') fetchJatuhTempo()
})

watch(groupBy, () => { if (activeTab.value === 'rekapitulasi') fetchRekap() })

let searchTimer = null
watch(search, () => {
  clearTimeout(searchTimer)
  searchTimer = setTimeout(() => { if (activeTab.value === 'nominatif') fetchNominatif(true) }, 500)
})
</script>

<template>
  <!-- Header -->
  <div class="d-flex align-start align-md-center justify-space-between flex-column flex-md-row gap-3 mb-6">
    <div>
      <div class="d-flex align-center gap-2 mb-1">
        <VAvatar color="primary" variant="tonal" size="32" rounded="lg">
          <VIcon icon="ri-bank-line" size="18" />
        </VAvatar>
        <h1 class="text-h5 font-weight-bold mb-0" style="font-family: 'Plus Jakarta Sans', sans-serif;">
          Pembiayaan
        </h1>
      </div>
      <p class="text-body-2 text-medium-emphasis mb-0 ms-10">
        Monitoring pembiayaan aktif — nominatif, rekapitulasi, dan jatuh tempo
      </p>
    </div>
    <VChip color="primary" variant="tonal" size="small" prepend-icon="ri-database-2-line">
      Financing Module
    </VChip>
  </div>

  <VAlert v-if="error" type="warning" variant="tonal" closable class="mb-4" :text="`Koneksi API: ${error}`" />

  <!-- Tabs -->
  <VCard elevation="0" border rounded="xl">
    <VTabs v-model="activeTab" color="primary" class="border-b">
      <VTab value="nominatif" prepend-icon="ri-list-unordered">Nominatif</VTab>
      <VTab value="rekapitulasi" prepend-icon="ri-bar-chart-2-line">Rekapitulasi</VTab>
      <VTab value="jatuh-tempo" prepend-icon="ri-calendar-event-line">Jatuh Tempo</VTab>
    </VTabs>

    <!-- Nominatif -->
    <VTabsWindow v-model="activeTab">
      <VTabsWindowItem value="nominatif">
        <VCardText>
          <VTextField
            v-model="search"
            placeholder="Cari nama nasabah, no. kontrak..."
            prepend-inner-icon="ri-search-line"
            variant="outlined"
            density="compact"
            clearable
            hide-details
            class="mb-4"
            style="max-width: 400px;"
          />
          <div class="d-flex align-center justify-space-between mt-4">
            <span class="text-body-2 text-medium-emphasis">
              Total: {{ totalCount.toLocaleString('id-ID') }} data
            </span>
            <VBtn
              v-if="hasMore"
              variant="tonal"
              color="primary"
              size="small"
              :loading="loadMoreLoading"
              @click="loadMore"
            >
              <VIcon icon="ri-add-line" start />
              Muat Lebih Banyak ({{ data.length }})
            </VBtn>
          </div>
          <VDataTable
            :headers="headers"
            :items="data"
            :loading="tableLoading || loading"
            density="comfortable"
            :items-per-page="-1"
            class="rounded-lg border"
            hover
          >
            <template #item.no="{ item, index }">
              {{ index + 1 }}
            </template>
            <template #item.nocif="{ item }">
              {{ item.nocif || '—' }}
            </template>
            <template #item.segmen="{ item }">
              {{ item.segmen_pasar?.ket || '—' }}
            </template>
            <template #item.tgleff="{ item }">
              {{ formatDate(item.tgleff) }}
            </template>
            <template #item.sisajw="{ item }">
              {{ (item.jw || 0) - (item.total_bayar || 0) }}
            </template>
            <template #item.tglexp="{ item }">
              {{ formatDate(item.tglexp) }}
            </template>
            <template #item.mdlawal="{ item }">
              <span class="financial-number">{{ formatRp(item.mdlawal) }}</span>
            </template>
            <template #item.osmdlc="{ item }">
              <span class="financial-number font-weight-semibold">{{ formatRp(item.osmdlc) }}</span>
            </template>
            <template #item.tgkmdl="{ item }">
              <span class="financial-number">{{ formatRp(item.tgkmdl) }}</span>
            </template>
            <template #item.tgkmgn="{ item }">
              <span class="financial-number">{{ formatRp(item.tgkmgn) }}</span>
            </template>
            <template #item.colbaru="{ item }">
              <VChip :color="kolBadge(item.colbaru)" size="small" variant="tonal">
                {{ `Kol. ${item.colbaru}` }}
              </VChip>
            </template>
            <template #item.tglmacet="{ item }">
              {{ formatDate(item.tglmacet) }}
            </template>
            <template #item.saldo_netto="{ item }">
              <span class="financial-number">{{ formatRp(item.saldo_netto) }}</span>
            </template>
            <template #item.keterangan_debet="{ item }">
              <VChip
                :color="item.keterangan_debet === 'Cukup' ? 'success' : 'secondary'"
                size="small"
                variant="tonal"
              >
                {{ item.keterangan_debet || '—' }}
              </VChip>
            </template>
            <template #item.tunggakan_vs_tabungan="{ item }">
              <span class="financial-number" :class="item.tunggakan_vs_tabungan < 0 ? 'text-error' : ''">
                {{ formatRp(item.tunggakan_vs_tabungan) }}
              </span>
            </template>
            <template #item.htgagun="{ item }">
              <span class="financial-number">{{ formatRp(item.htgagun) }}</span>
            </template>
            <template #item.ppap="{ item }">
              <span class="financial-number">{{ formatRp(item.ppap) }}</span>
            </template>
            <template #item.ao="{ item }">
              {{ item.ao?.nmao || '—' }}
            </template>
            <template #item.cabang="{ item }">
              {{ item.cabang?.nama || '—' }}
            </template>
            <template #item.wilayah="{ item }">
              {{ item.wilayah?.ket || '—' }}
            </template>
            <template #item.alamat="{ item }">
              {{ item.cif?.alamat || '—' }}
            </template>
            <template #no-data>
              <div class="text-center py-8 text-medium-emphasis">
                <VIcon icon="ri-file-search-line" size="48" class="mb-2" style="opacity: 0.3;" />
                <p class="text-body-2">Tidak ada data atau koneksi SQL Server belum aktif</p>
              </div>
            </template>
          </VDataTable>
        </VCardText>
      </VTabsWindowItem>

      <!-- Rekapitulasi -->
      <VTabsWindowItem value="rekapitulasi">
        <VCardText>
          <div class="d-flex align-center gap-3 mb-4">
            <span class="text-body-2 font-weight-medium">Kelompokkan berdasarkan:</span>
            <VSelect
              v-model="groupBy"
              :items="groupOptions"
              item-title="title"
              item-value="value"
              density="compact"
              variant="outlined"
              hide-details
              style="max-width: 200px;"
            />
          </div>
          <VDataTable
            :headers="rekapHeaders"
            :items="rekapData"
            :loading="tableLoading"
            density="comfortable"
            class="rounded-lg border"
            hover
          >
            <template #item.total_mdlawal="{ item }">
              <span class="financial-number">{{ formatRp(item.total_mdlawal) }}</span>
            </template>
            <template #item.total_osmdlc="{ item }">
              <span class="financial-number font-weight-semibold text-primary">
                {{ formatRp(item.total_osmdlc) }}
              </span>
            </template>
            <template #item.avg_rate="{ item }">
              <span class="financial-number">{{ parseFloat(item.avg_rate).toFixed(2) }}%</span>
            </template>
            <template #no-data>
              <div class="text-center py-8 text-medium-emphasis">
                <p class="text-body-2">Klik tab Rekapitulasi untuk memuat data</p>
              </div>
            </template>
          </VDataTable>
        </VCardText>
      </VTabsWindowItem>

      <!-- Jatuh Tempo -->
      <VTabsWindowItem value="jatuh-tempo">
        <VCardText>
          <VDataTable
            :headers="jtHeaders"
            :items="jatuhTempo"
            :loading="tableLoading"
            density="comfortable"
            class="rounded-lg border"
            hover
          >
            <template #item.tglexp="{ item }">
              {{ item.tglexp ? `${item.tglexp.substring(6,8)}/${item.tglexp.substring(4,6)}/${item.tglexp.substring(0,4)}` : '—' }}
            </template>
            <template #item.osmdlc="{ item }">
              <span class="financial-number font-weight-semibold text-error">
                {{ formatRp(item.osmdlc) }}
              </span>
            </template>
            <template #item.status="{ item }">
              <VChip :color="kolBadge(item.colbaru)" size="small" variant="tonal">
                {{ item.colbaru ? `Kol. ${item.colbaru}` : 'Aktif' }}
              </VChip>
            </template>
            <template #no-data>
              <div class="text-center py-8 text-medium-emphasis">
                <p class="text-body-2">Klik tab Jatuh Tempo untuk memuat data</p>
              </div>
            </template>
          </VDataTable>
        </VCardText>
      </VTabsWindowItem>
    </VTabsWindow>
  </VCard>
</template>
