<script setup>
import { ref, onMounted, watch } from 'vue'
import DefaultLayout from '@/layouts/default.vue'

defineOptions({ layout: DefaultLayout })



const activeTab = ref('tabungan')
const tableLoading = ref(false)
const error = ref(null)
const savingData = ref([])
const savingRekap = ref([])
const depositData = ref([])
const depositRekap = ref([])
const depositJT = ref([])
const savingTab = ref('nominatif')
const depositTab = ref('nominatif')

function formatRp(v) {
  if (!v && v !== 0) return '—'
  const n = parseFloat(v)
  if (isNaN(n)) return '—'
  if (n >= 1e9) return `Rp ${(n / 1e9).toFixed(2)} M`
  if (n >= 1e6) return `Rp ${(n / 1e6).toFixed(1)} Jt`
  return `Rp ${n.toLocaleString('id-ID')}`
}

const savingHeaders = [
  { title: 'No. Rekening', key: 'norekening', width: '150px' },
  { title: 'Nama Nasabah', key: 'nama', sortable: true },
  { title: 'Jenis Tabungan', key: 'jenis', width: '150px' },
  { title: 'Saldo', key: 'saldo', sortable: true, align: 'end' },
  { title: 'Status', key: 'status', align: 'center', width: '100px' },
]

const depositHeaders = [
  { title: 'No. Bilyet', key: 'nobilyet', width: '150px' },
  { title: 'Nama Nasabah', key: 'nama', sortable: true },
  { title: 'Nominal', key: 'nominal', sortable: true, align: 'end' },
  { title: 'Nisbah', key: 'nisbah', align: 'end', width: '100px' },
  { title: 'Jatuh Tempo', key: 'jatuh_tempo', width: '130px' },
  { title: 'Status', key: 'status', align: 'center', width: '100px' },
]

async function fetchSaving(endpoint = 'nominative') {
  tableLoading.value = true
  try {
    const url = `/api/v1/saving/${endpoint}?per_page=100`
    const r = await fetch(url)
    const j = await r.json()
    if (endpoint === 'nominative') savingData.value = j.data?.data ?? j.data ?? []
    else savingRekap.value = j.data ?? []
  } catch (e) { error.value = e.message } finally { tableLoading.value = false }
}

async function fetchDeposit(endpoint = 'nominative') {
  tableLoading.value = true
  try {
    const url = `/api/v1/deposit/${endpoint}?per_page=100`
    // #region agent log
    debugLog('H1', 'resources/js/Pages/Funding/Index.vue:72', 'Funding deposit request dispatched', { endpoint, url })
    // #endregion
    const r = await fetch(url)
    const j = await r.json()
    // #region agent log
    debugLog('H2', 'resources/js/Pages/Funding/Index.vue:76', 'Funding deposit response received', {
      endpoint,
      rows: j?.data?.data?.length ?? j?.data?.length ?? null,
      meta: j?.data?.meta ?? null,
    })
    // #endregion
    if (endpoint === 'nominative') depositData.value = j.data?.data ?? j.data ?? []
    else if (endpoint === 'rekapitulasi') depositRekap.value = j.data ?? []
    else depositJT.value = j.data?.data ?? j.data ?? []
  } catch (e) { error.value = e.message } finally { tableLoading.value = false }
}

onMounted(() => { fetchSaving('nominative') })

watch(savingTab, (t) => fetchSaving(t === 'nominatif' ? 'nominative' : 'rekapitulasi'))
watch(depositTab, (t) => {
  if (t === 'nominatif') fetchDeposit('nominative')
  else if (t === 'rekapitulasi') fetchDeposit('rekapitulasi')
  else fetchDeposit('jatuh-tempo')
})
watch(activeTab, (t) => {
  if (t === 'deposito') fetchDeposit('nominative')
})
</script>

<template>
  <div class="d-flex align-center justify-space-between mb-6">
    <div>
      <div class="d-flex align-center gap-2 mb-1">
        <VAvatar color="info" variant="tonal" size="32" rounded="lg">
          <VIcon icon="ri-safe-2-line" size="18" />
        </VAvatar>
        <h1 class="text-h5 font-weight-bold mb-0" style="font-family: 'Plus Jakarta Sans', sans-serif;">
          Funding
        </h1>
      </div>
      <p class="text-body-2 text-medium-emphasis mb-0 ms-10">
        Dana Pihak Ketiga — Tabungan & Deposito Mudharabah
      </p>
    </div>
    <VChip color="info" variant="tonal" size="small" prepend-icon="ri-safe-2-line">Funding Module</VChip>
  </div>

  <VAlert v-if="error" type="warning" variant="tonal" closable class="mb-4" :text="`Koneksi API: ${error}`" />

  <!-- Main Tabs: Tabungan vs Deposito -->
  <VTabs v-model="activeTab" color="info" class="mb-4">
    <VTab value="tabungan" prepend-icon="ri-piggy-bank-line">Tabungan</VTab>
    <VTab value="deposito" prepend-icon="ri-safe-2-line">Deposito</VTab>
  </VTabs>

  <VTabsWindow v-model="activeTab">

    <!-- TABUNGAN -->
    <VTabsWindowItem value="tabungan">
      <VCard elevation="0" border rounded="xl">
        <VTabs v-model="savingTab" color="success" class="border-b" density="compact">
          <VTab value="nominatif" prepend-icon="ri-list-unordered">Nominatif</VTab>
          <VTab value="rekapitulasi" prepend-icon="ri-bar-chart-2-line">Rekapitulasi</VTab>
        </VTabs>
        <VTabsWindow v-model="savingTab">
          <VTabsWindowItem value="nominatif">
            <VCardText>
              <VDataTable
                :headers="savingHeaders"
                :items="savingData"
                :loading="tableLoading"
                density="comfortable"
                :items-per-page="25"
                class="rounded-lg border"
                hover
              >
                <template #item.saldo="{ item }">
                  <span class="financial-number font-weight-semibold">{{ formatRp(item.saldo) }}</span>
                </template>
                <template #item.status="{ item }">
                  <VChip :color="item.status === 'Aktif' || item.status === 'A' ? 'success' : 'warning'" size="x-small" variant="tonal">
                    {{ item.status === 'A' ? 'Aktif' : item.status ?? 'Aktif' }}
                  </VChip>
                </template>
                <template #no-data>
                  <div class="text-center py-8 text-medium-emphasis">
                    <VIcon icon="ri-piggy-bank-line" size="48" class="mb-2" style="opacity: 0.3;" />
                    <p>Koneksi SQL Server diperlukan untuk menampilkan data</p>
                  </div>
                </template>
              </VDataTable>
            </VCardText>
          </VTabsWindowItem>
          <VTabsWindowItem value="rekapitulasi">
            <VCardText>
              <VDataTable
                :headers="[{ title: 'Grup', key: 'group' }, { title: 'Rekening', key: 'count', align: 'end' }, { title: 'Total Saldo', key: 'total_saldo', align: 'end' }]"
                :items="savingRekap"
                :loading="tableLoading"
                density="comfortable"
                class="rounded-lg border"
                hover
              >
                <template #item.total_saldo="{ item }">
                  <span class="financial-number font-weight-semibold text-success">{{ formatRp(item.total_saldo ?? item.total_balance) }}</span>
                </template>
              </VDataTable>
            </VCardText>
          </VTabsWindowItem>
        </VTabsWindow>
      </VCard>
    </VTabsWindowItem>

    <!-- DEPOSITO -->
    <VTabsWindowItem value="deposito">
      <VCard elevation="0" border rounded="xl">
        <VTabs v-model="depositTab" color="info" class="border-b" density="compact">
          <VTab value="nominatif" prepend-icon="ri-list-unordered">Nominatif</VTab>
          <VTab value="rekapitulasi" prepend-icon="ri-bar-chart-2-line">Rekapitulasi</VTab>
          <VTab value="jatuh-tempo" prepend-icon="ri-calendar-event-line">Jatuh Tempo</VTab>
        </VTabs>
        <VTabsWindow v-model="depositTab">
          <VTabsWindowItem value="nominatif">
            <VCardText>
              <VDataTable
                :headers="depositHeaders"
                :items="depositData"
                :loading="tableLoading"
                density="comfortable"
                :items-per-page="25"
                class="rounded-lg border"
                hover
              >
                <template #item.nominal="{ item }">
                  <span class="financial-number font-weight-semibold text-info">{{ formatRp(item.nominal) }}</span>
                </template>
                <template #item.nisbah="{ item }">
                  <span class="financial-number">{{ item.nisbah ? `${item.nisbah}%` : '—' }}</span>
                </template>
                <template #item.status="{ item }">
                  <VChip :color="item.status === 'Aktif' ? 'success' : 'warning'" size="x-small" variant="tonal">
                    {{ item.status ?? 'Aktif' }}
                  </VChip>
                </template>
                <template #no-data>
                  <div class="text-center py-8 text-medium-emphasis">
                    <VIcon icon="ri-safe-2-line" size="48" class="mb-2" style="opacity: 0.3;" />
                    <p>Koneksi SQL Server diperlukan</p>
                  </div>
                </template>
              </VDataTable>
            </VCardText>
          </VTabsWindowItem>
          <VTabsWindowItem value="rekapitulasi">
            <VCardText>
              <VDataTable
                :headers="[{ title: 'Grup', key: 'group' }, { title: 'Rekening', key: 'count', align: 'end' }, { title: 'Total Nominal', key: 'total_nominal', align: 'end' }]"
                :items="depositRekap"
                :loading="tableLoading"
                density="comfortable"
                class="rounded-lg border"
              >
                <template #item.total_nominal="{ item }">
                  <span class="financial-number font-weight-semibold text-info">{{ formatRp(item.total_nominal ?? item.total_outstanding) }}</span>
                </template>
              </VDataTable>
            </VCardText>
          </VTabsWindowItem>
          <VTabsWindowItem value="jatuh-tempo">
            <VCardText>
              <VDataTable
                :headers="[
                  { title: 'No. Bilyet', key: 'nobilyet', width: '140px' },
                  { title: 'Nama', key: 'nama', sortable: true },
                  { title: 'Nominal', key: 'nominal', align: 'end' },
                  { title: 'Jatuh Tempo', key: 'jatuh_tempo', width: '130px' },
                  { title: 'ARO', key: 'aro', align: 'center', width: '80px' },
                ]"
                :items="depositJT"
                :loading="tableLoading"
                density="comfortable"
                class="rounded-lg border"
              >
                <template #item.nominal="{ item }">
                  <span class="financial-number font-weight-semibold text-error">{{ formatRp(item.nominal) }}</span>
                </template>
                <template #item.aro="{ item }">
                  <VChip :color="item.aro === 'Y' || item.aro === true ? 'success' : 'warning'" size="x-small" variant="tonal">
                    {{ item.aro === 'Y' || item.aro === true ? 'ARO' : 'Non-ARO' }}
                  </VChip>
                </template>
              </VDataTable>
            </VCardText>
          </VTabsWindowItem>
        </VTabsWindow>
      </VCard>
    </VTabsWindowItem>
  </VTabsWindow>
</template>
