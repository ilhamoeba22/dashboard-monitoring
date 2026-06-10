<script setup>
import { computed, onMounted, watch } from 'vue'
import debounce from 'lodash/debounce'
import DefaultLayout from '@/layouts/default.vue'
import { useCifAuditStore } from '@/stores/cifAuditStore'
import { useCifAuditLogic } from '@/composables/useCifAuditLogic'
import '@/assets/css/cif-shared.css'

defineOptions({ layout: DefaultLayout })

const store = useCifAuditStore()
const { getCifStatus, isNikAnomaly, isNamaAnomaly, isHpAnomaly, isIbuAnomaly, exportToExcel } = useCifAuditLogic()

const statusList = ['ALL', 'Lengkap', 'Belum Lengkap', 'Cek Ulang']

const headers = [
  { title: 'NO', key: 'id', align: 'center', minWidth: 60, width: 60, fixed: true },
  { title: 'NO CIF', key: 'nocif', align: 'center', minWidth: 120, width: 120, fixed: true },
  { title: 'NAMA NASABAH', key: 'namanasabah', align: 'left', minWidth: 250, width: 250, fixed: true },
  { title: 'NPWP', key: 'npwp', align: 'center', width: '180px' },
  { title: 'NOMOR KTP', key: 'noktp', align: 'center', width: '160px' },
  { title: 'CEK NIK', key: 'ceknik', align: 'center', width: '100px' },
  { title: 'J/K', key: 'jk', align: 'center', width: '60px' },
  { title: 'TEMPAT LAHIR', key: 'tempat_lahir', align: 'left', width: '150px' },
  { title: 'TGL LAHIR KTP', key: 'tgllhr_ktp', align: 'center', width: '130px' },
  { title: 'TGL LAHIR CIF', key: 'tgllhr', align: 'center', width: '130px' },
  { title: 'USIA', key: 'usia', align: 'center', width: '80px' },
  { title: 'NOMOR HP', key: 'nohp', align: 'center', width: '140px' },
  { title: 'SANDI DATI', key: 'sandi_dati', align: 'center', width: '100px' },
  { title: 'NAMA IBU KANDUNG', key: 'nama_ibu', align: 'left', width: '200px' },
  { title: 'STATUS KAWIN', key: 'ket_stskawin', align: 'center', width: '130px' },
  { title: 'HUBUNGAN PASANGAN', key: 'ket_kdhub', align: 'center', width: '160px' },
  { title: 'NAMA PASANGAN', key: 'nama_pasangan', align: 'left', width: '200px' },
  { title: 'NIK PASANGAN', key: 'nik_pasangan', align: 'center', width: '160px' },
  { title: 'HP PASANGAN', key: 'hp_pasangan', align: 'center', width: '140px' },
  { title: 'TGL LAHIR PASANGAN', key: 'tgllhr_pasangan', align: 'center', width: '160px' },
  { title: 'USIA PASANGAN', key: 'usia_pasangan', align: 'center', width: '130px' },
  { title: 'STATUS CIF', key: 'status_cif', align: 'center', width: '140px' },
  { title: 'ALAMAT LENGKAP', key: 'alamat', align: 'left', width: '300px' },
  { title: 'KELURAHAN', key: 'kelurahan', align: 'left', width: '150px' },
  { title: 'KECAMATAN', key: 'kecamatan', align: 'left', width: '150px' },
  { title: 'KOTA', key: 'kota', align: 'center', width: '150px' },
  { title: 'KODE POS', key: 'kodepos', align: 'center', width: '100px' },
  { title: 'NAMA MARKETING', key: 'nama_marketing', align: 'left', width: '180px' },
  { title: 'CABANG', key: 'cabang', align: 'left', width: '180px' }
]

const currentHeaders = computed(() => {
  if (store.activeTab === 'individu') {
    return headers.filter(h => h.key !== 'npwp')
  }
  return headers
})

const doExport = () => {
  const suffix = store.activeTab === 'individu' ? 'Individu' : 'Badan_Hukum'
  exportToExcel(store.auditData, `Tabungan_${suffix}`)
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
        <v-btn variant="outlined" color="#1e293b" height="40" prepend-icon="ri-file-excel-2-line" @click="doExport">Export</v-btn>
      </div>
    </div>

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
.cif-table-frozen :deep(th:nth-child(1)) { background: #0f172a !important; }
.cif-table-frozen :deep(th:nth-child(2)), .cif-table-frozen :deep(td:nth-child(2)) { position: sticky; left: 60px; z-index: 2; background: white; border-right: 1px solid #e2e8f0; }
.cif-table-frozen :deep(th:nth-child(2)) { background: #0f172a !important; }
.cif-table-frozen :deep(th:nth-child(3)), .cif-table-frozen :deep(td:nth-child(3)) { position: sticky; left: 180px; z-index: 2; background: white; box-shadow: inset -4px 0 8px -4px rgba(0, 0, 0, 0.08); }
.cif-table-frozen :deep(th:nth-child(3)) { background: #0f172a !important; box-shadow: inset -4px 0 8px -4px rgba(0, 0, 0, 0.4); }
.text-error { color: #e11d48 !important; }
</style>
