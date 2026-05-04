<script setup>
import { ref, onMounted, watch } from 'vue'
import DefaultLayout from '@/layouts/default.vue'

defineOptions({ layout: DefaultLayout })



const activeTab = ref('daftar')
const loading = ref(true)
const tableLoading = ref(false)
const error = ref(null)
const search = ref('')
const groupBy = ref('cabang')
const data = ref([])
const rekapData = ref([])
const cursor = ref(null)
const hasMore = ref(true)
const loadMoreLoading = ref(false)
const totalCount = ref(0)

const headers = [
  { title: 'No. CIF', key: 'nocif', width: '130px' },
  { title: 'Nama Nasabah', key: 'nm', sortable: true },
  { title: 'Jenis Kelamin', key: 'sex', width: '110px', align: 'center' },
  { title: 'No. HP', key: 'hp', width: '130px' },
  { title: 'Cabang', key: 'cabang', width: '110px' },
  { title: 'AO', key: 'ao', width: '110px' },
]

const rekapHeaders = [
  { title: 'Grup', key: 'label', sortable: true },
  { title: 'Jumlah Nasabah', key: 'total_nasabah', sortable: true, align: 'end' },
]

const groupOptions = [
  { value: 'cabang', title: 'Per Cabang' },
  { value: 'ao', title: 'Per AO' },
  { value: 'segmen', title: 'Per Segmen' },
  { value: 'agama', title: 'Per Agama' },
]

async function fetchList(reset = false) {
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
    const url = `/api/v1/cif?${params}`
    const r = await fetch(url)
    const json = await r.json()
    
    if (json.data?.data) {
      const newData = json.data.data.filter(item => !data.value.some(existing => existing.nocif === item.nocif))
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

// Load more button handler
async function loadMore() {
  if (loadMoreLoading.value || !hasMore.value) return
  loadMoreLoading.value = true
  try {
    await fetchList(false)
  } finally { loadMoreLoading.value = false }
}

async function fetchRekap() {
  tableLoading.value = true
  try {
    const r = await fetch(`/api/v1/cif/rekapitulasi?group_by=${groupBy.value}`)
    const json = await r.json()
    rekapData.value = json.data ?? []
  } catch (e) { error.value = e.message } finally { tableLoading.value = false }
}

onMounted(async () => {
  loading.value = true
  await fetchList()
  loading.value = false
})

watch(activeTab, (tab) => {
  if (tab === 'daftar') fetchList(true)
  else if (tab === 'rekapitulasi') fetchRekap()
})

watch(groupBy, () => { if (activeTab.value === 'rekapitulasi') fetchRekap() })

let st = null
watch(search, () => {
  clearTimeout(st)
  st = setTimeout(() => { if (activeTab.value === 'daftar') fetchList(true) }, 500)
})
</script>

<template>
  <div class="d-flex align-center justify-space-between mb-6">
    <div>
      <div class="d-flex align-center gap-2 mb-1">
        <VAvatar color="success" variant="tonal" size="32" rounded="lg">
          <VIcon icon="ri-user-star-line" size="18" />
        </VAvatar>
        <h1 class="text-h5 font-weight-bold mb-0" style="font-family: 'Plus Jakarta Sans', sans-serif;">
          Nasabah (CIF)
        </h1>
      </div>
      <p class="text-body-2 text-medium-emphasis mb-0 ms-10">
        Customer Information File — data nasabah terdaftar
      </p>
    </div>
    <VChip color="success" variant="tonal" size="small" prepend-icon="ri-user-star-line">
      CIF Module
    </VChip>
  </div>

  <VAlert v-if="error" type="warning" variant="tonal" closable class="mb-4" :text="`Koneksi API: ${error}`" />

  <VCard elevation="0" border rounded="xl">
    <VTabs v-model="activeTab" color="success" class="border-b">
      <VTab value="daftar" prepend-icon="ri-list-unordered">Daftar Nasabah</VTab>
      <VTab value="rekapitulasi" prepend-icon="ri-bar-chart-2-line">Rekapitulasi</VTab>
    </VTabs>

    <VTabsWindow v-model="activeTab">
      <!-- Daftar Nasabah -->
      <VTabsWindowItem value="daftar">
        <VCardText>
          <VTextField
            v-model="search"
            placeholder="Cari nama, no. CIF..."
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
              color="success"
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
            <template #item.sex="{ item }">
              <VChip :color="item.sex === 'P' || item.sex === 'W' ? 'pink' : 'info'" size="small" variant="tonal">
                {{ item.sex === 'P' || item.sex === 'W' ? 'Perempuan' : 'Laki-laki' }}
              </VChip>
            </template>
            <template #item.hp="{ item }">
              {{ item.hp || '—' }}
            </template>
            <template #item.cabang="{ item }">
              {{ item.cabang?.nama || '—' }}
            </template>
            <template #item.ao="{ item }">
              {{ item.ao?.nmao || '—' }}
            </template>
            <template #no-data>
              <div class="text-center py-8 text-medium-emphasis">
                <VIcon icon="ri-user-search-line" size="48" class="mb-2" style="opacity: 0.3;" />
                <p>Tidak ada data atau koneksi SQL Server belum aktif</p>
              </div>
            </template>
          </VDataTable>
        </VCardText>
      </VTabsWindowItem>

      <!-- Rekapitulasi -->
      <VTabsWindowItem value="rekapitulasi">
        <VCardText>
          <div class="d-flex align-center gap-3 mb-4">
            <span class="text-body-2">Kelompokkan:</span>
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
            <template #item.total_nasabah="{ item }">
              <span class="financial-number font-weight-semibold text-success">
                {{ item.total_nasabah?.toLocaleString('id-ID') || '0' }}
              </span>
            </template>
            <template #no-data>
              <div class="text-center py-6"><p class="text-body-2 text-medium-emphasis">Klik tab Rekapitulasi untuk memuat data</p></div>
            </template>
          </VDataTable>
        </VCardText>
      </VTabsWindowItem>
    </VTabsWindow>
  </VCard>
</template>
