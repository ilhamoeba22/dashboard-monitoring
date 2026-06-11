<script setup>
import { computed, onMounted, ref, watch } from 'vue'
import debounce from 'lodash/debounce'
import DefaultLayout from '@/layouts/default.vue'
import { useCifAuditStore } from '@/stores/cifAuditStore'
import { cifAuditHeaders, useCifAuditLogic } from '@/composables/useCifAuditLogic'
import '@/assets/css/cif-shared.css'

defineOptions({ layout: DefaultLayout })

const store = useCifAuditStore()
const { getCifStatus, isNikAnomaly, isNamaAnomaly, isHpAnomaly, isIbuAnomaly, exportToExcel, exportToPdf } = useCifAuditLogic()

const statusList = ['ALL', 'Lengkap', 'Belum Lengkap', 'Cek Ulang']
const exportMessage = ref('')
const exportMessageType = ref('info')
const headers = cifAuditHeaders

const currentHeaders = computed(() => {
  if (store.activeTab === 'individu') {
    return headers.filter(h => h.key !== 'npwp')
  }
  return headers
})

const doExport = async () => {
  const suffix = store.activeTab === 'individu' ? 'Individu' : 'Badan_Hukum'
  await exportToExcel(store.auditData, `Tabungan_${suffix}`)
}

const clearExportMessage = () => {
  exportMessage.value = ''
  exportMessageType.value = 'info'
}

const doExportPdf = async () => {
  try {
    const suffix = store.activeTab === 'individu' ? 'Individu' : 'Badan Hukum'
    await exportToPdf(store.auditData, currentHeaders.value, 'Pengecekan CIF Tabungan', suffix)
  } catch (e) {
    exportMessage.value = 'Gagal membuat PDF: ' + (e.message || e)
    exportMessageType.value = 'error'
    return
  }
}

watch(() => store.activeTab, () => {
  store.filters.page = 1
  store.fetchTabungan()
})

const debouncedFetch = debounce(() => {
  store.filters.page = 1
  store.fetchTabungan()
}, 500)

watch(() => store.filters.search, () => {
  debouncedFetch()
})

watch([() => store.filters.cabang, () => store.filters.status], () => {
  store.filters.page = 1
  store.fetchTabungan()
})

onMounted(() => {
  store.fetchTabungan()
})
</script>

<template>
  <div class="cif-page px-4 pt-0">
    <div class="cif-hero mb-6">
      <div class="cif-hero__deco"></div>
      <div class="cif-hero__inner">
        <div class="cif-hero__top">
          <div class="cif-hero__icon" style="background: linear-gradient(135deg, #0ea5e9, #0369a1); box-shadow: 0 8px 20px rgba(14, 165, 233, 0.4);">
            <v-icon icon="ri-wallet-3-line" size="26" color="white" />
          </div>
          <div class="cif-hero__meta">
            <h1 class="cif-hero__title">Pengecekan CIF Tabungan</h1>
            <p class="cif-hero__subtitle">Audit kelengkapan dan anomali data nasabah tabungan.</p>
            
            <v-tabs
              v-model="store.activeTab"
              density="compact"
              hide-slider
              class="cif-hero-tabs mt-4"
              style="gap: 8px;"
            >
              <v-tab value="individu" rounded="pill" class="mr-2">Individu</v-tab>
              <v-tab value="badan_hukum" rounded="pill">Badan Hukum</v-tab>
            </v-tabs>
          </div>
        </div>
      </div>
    </div>

    <div class="fin-filter-card d-flex justify-space-between align-center mb-6">
      <div class="d-flex flex-wrap gap-3" style="flex: 1;">
        <div style="width: 200px;">
          <div class="fin-filter-card__label mb-1">Cabang</div>
          <v-select v-model="store.filters.cabang" :items="store.cabangOptions" variant="outlined" density="compact" hide-details bg-color="white" />
        </div>
        <div style="width: 200px;">
          <div class="fin-filter-card__label mb-1">Status CIF</div>
          <v-select v-model="store.filters.status" :items="statusList" variant="outlined" density="compact" hide-details bg-color="white" />
        </div>
        <div style="width: 250px;">
          <div class="fin-filter-card__label mb-1">Pencarian</div>
          <v-text-field v-model="store.filters.search" placeholder="Cari NIK / Nama..." variant="outlined" density="compact" hide-details bg-color="white" prepend-inner-icon="ri-search-line" />
        </div>
      </div>
      <div class="d-flex align-end">
        <div class="d-flex gap-2">
          <v-btn variant="outlined" color="#64748b" height="40" prepend-icon="ri-refresh-line" @click="store.resetFilters(); store.fetchTabungan()">Reset</v-btn>
          <v-btn variant="outlined" color="#1e293b" height="40" prepend-icon="ri-file-excel-2-line" @click="doExport">Excel</v-btn>
          <v-btn variant="outlined" color="#b91c1c" height="40" prepend-icon="ri-file-pdf-2-line" @click="doExportPdf">PDF</v-btn>
        </div>
      </div>
    </div>

    <v-alert
      v-if="exportMessage"
      :type="exportMessageType === 'error' ? 'error' : 'info'"
      variant="tonal"
      class="mb-4"
      density="comfortable"
    >
      {{ exportMessage }}
      <template #append>
        <v-btn size="small" variant="text" @click="clearExportMessage">Tutup</v-btn>
      </template>
    </v-alert>

    <v-alert
      v-if="!store.isLoading && !store.errorMessage && !store.auditData.length"
      type="info"
      variant="tonal"
      class="mb-4"
      density="comfortable"
    >
      Tidak ada data CIF tabungan untuk filter ini.
    </v-alert>

    <v-alert
      v-if="store.errorMessage"
      type="error"
      variant="tonal"
      class="mb-4"
      density="comfortable"
    >
      {{ store.errorMessage }}
    </v-alert>

    <div class="content-card">
      <div class="overflow-x-auto">
        <v-data-table
          :headers="currentHeaders"
          :items="store.auditData"
          :loading="store.isLoading"
          class="fin-vtable cif-table-frozen"
          hover
          density="comfortable"
          :items-per-page="50"
        >
          <template #item.namanasabah="{ item }">
            <div :class="{'text-error font-weight-bold': isNamaAnomaly(item.namanasabah)}">
              {{ item.namanasabah }}
              <v-icon v-if="isNamaAnomaly(item.namanasabah)" color="error" size="14" class="ml-1">ri-error-warning-line</v-icon>
            </div>
          </template>

          <template #item.ceknik="{ item }">
            <v-chip v-if="isNikAnomaly(item.noktp)" color="error" size="small" variant="flat">Invalid ({{ String(item.noktp).replace(/\D/g, '').length }})</v-chip>
            <v-chip v-else color="success" size="small" variant="flat">Valid (16)</v-chip>
          </template>

          <template #item.nohp="{ item }">
            <div :class="{'text-error font-weight-bold': isHpAnomaly(item.nohp)}">{{ item.nohp }}</div>
          </template>

          <template #item.nama_ibu="{ item }">
            <div :class="{'text-error font-weight-bold': isIbuAnomaly(item.nama_ibu, item.namanasabah, item.kota)}">{{ item.nama_ibu }}</div>
          </template>

          <template #item.nik_pasangan="{ item }">
             <div :class="{'text-error font-weight-bold': item.ket_stskawin === 'KAWIN' && isNikAnomaly(item.nik_pasangan)}">{{ item.nik_pasangan }}</div>
          </template>

          <template #item.status_cif="{ item }">
            <v-chip :color="getCifStatus(item) === 'Lengkap' ? 'success' : getCifStatus(item) === 'Belum Lengkap' ? 'error' : 'warning'" size="small" variant="flat">{{ getCifStatus(item) }}</v-chip>
          </template>
        </v-data-table>
      </div>
    </div>
  </div>
</template>

<style scoped>
.cif-table-frozen :deep(th:nth-child(1)), .cif-table-frozen :deep(td:nth-child(1)) { position: sticky; left: 0; z-index: 2; background: white; border-right: 1px solid #e2e8f0; }
.cif-table-frozen :deep(th:nth-child(1)), .cif-table-frozen :deep(th:nth-child(1) *) { background: #0f172a !important; color: #e5edf7 !important; }
.cif-table-frozen :deep(th:nth-child(2)), .cif-table-frozen :deep(td:nth-child(2)) { position: sticky; left: 60px; z-index: 2; background: white; border-right: 1px solid #e2e8f0; }
.cif-table-frozen :deep(th:nth-child(2)), .cif-table-frozen :deep(th:nth-child(2) *) { background: #0f172a !important; color: #e5edf7 !important; }
.cif-table-frozen :deep(th:nth-child(3)), .cif-table-frozen :deep(td:nth-child(3)) { position: sticky; left: 180px; z-index: 2; background: white; box-shadow: inset -4px 0 8px -4px rgba(0, 0, 0, 0.08); }
.cif-table-frozen :deep(th:nth-child(3)), .cif-table-frozen :deep(th:nth-child(3) *) { background: #0f172a !important; color: #e5edf7 !important; box-shadow: inset -4px 0 8px -4px rgba(0, 0, 0, 0.4); }
.text-error { color: #e11d48 !important; }
</style>
