<script setup>
import { computed, onMounted, ref } from 'vue'
import DefaultLayout from '@/layouts/default.vue'
import { useCifAuditLogic } from '@/composables/useCifAuditLogic'
import { formatExactNumber, formatTruncatedPercentage } from '@/utils/money'
import '@/assets/css/cif-shared.css'

defineOptions({ layout: DefaultLayout })

const { isNikAnomaly, isNamaAnomaly, isHpAnomaly, isIbuAnomaly } = useCifAuditLogic()

const isLoading = ref(true)
const errorMessage = ref('')
const qualityData = ref(null)

const summary = computed(() => qualityData.value?.summary || {})
const branches = computed(() => qualityData.value?.branch_quality || [])
const anomalies = computed(() => qualityData.value?.anomaly_samples || [])
const statusCounts = computed(() => {
  const distribution = qualityData.value?.status_distribusi || []
  return distribution.reduce((accumulator, item) => {
    accumulator[item.status] = Number(item.total || 0)
    return accumulator
  }, {})
})

const headers = [
  { title: 'NO CIF', key: 'nocif', align: 'center', width: '120px', fixed: true },
  { title: 'NAMA NASABAH', key: 'namanasabah', align: 'left', width: '220px', fixed: true },
  { title: 'CABANG', key: 'cabang', align: 'left', width: '180px' },
  { title: 'STATUS', key: 'status_cif', align: 'center', width: '150px' },
  { title: 'KETERANGAN ANOMALI', key: 'anomali', align: 'left', width: '420px' },
]

const kpiCards = computed(() => [
  {
    title: 'Data Lengkap',
    value: summary.value.persen_lengkap ?? 0,
    count: statusCounts.value.Lengkap ?? 0,
    icon: 'ri-checkbox-circle-fill',
    color: '#10b981',
    format: 'percent',
    subtitle: 'Rasio CIF valid dan siap pakai',
  },
  {
    title: 'Belum Lengkap',
    value: summary.value.persen_belum_lengkap ?? 0,
    count: statusCounts.value['Belum Lengkap'] ?? 0,
    icon: 'ri-error-warning-fill',
    color: '#e11d48',
    format: 'percent',
    subtitle: 'Butuh pelengkapan data inti',
  },
  {
    title: 'Perlu Cek Ulang',
    value: summary.value.persen_cek_ulang ?? 0,
    count: statusCounts.value['Cek Ulang'] ?? 0,
    icon: 'ri-search-eye-line',
    color: '#f59e0b',
    format: 'percent',
    subtitle: 'Butuh review kualitas field',
  },
])

function formatValue(value, format = 'number') {
  if (value === null || value === undefined || value === '') return '-'
  if (format === 'percent') return formatTruncatedPercentage(value)
  return formatExactNumber(value)
}

function getAnomalies(item) {
  const issues = []
  if (isNikAnomaly(item.noktp)) issues.push('KTP tidak valid')
  if (isNamaAnomaly(item.namanasabah)) issues.push('Nama perlu cek')
  if (isHpAnomaly(item.nohp)) issues.push('HP tidak valid')
  if (isIbuAnomaly(item.nama_ibu, item.namanasabah, item.kota)) issues.push('Nama ibu perlu cek')
  if (String(item.ket_stskawin || '').toUpperCase() === 'KAWIN' && isNikAnomaly(item.nik_pasangan)) {
    issues.push('NIK pasangan tidak valid')
  }
  return issues
}

async function fetchQualitySummary() {
  isLoading.value = true
  errorMessage.value = ''

  try {
    const response = await fetch('/api/v1/cif/audit/summary')
    const json = await response.json()
    if (!response.ok || !json.success) {
      throw new Error(json.message || 'Gagal memuat kualitas CIF.')
    }
    qualityData.value = json.data
  } catch (error) {
    qualityData.value = null
    errorMessage.value = error.message || 'Gagal memuat kualitas CIF.'
  } finally {
    isLoading.value = false
  }
}

onMounted(fetchQualitySummary)
</script>

<template>
  <div class="cif-page px-4 pt-0">
    <div class="cif-hero mb-6">
      <div class="cif-hero__deco"></div>
      <div class="cif-hero__inner">
        <div class="cif-hero__top">
          <div class="cif-hero__icon">
            <v-icon icon="ri-shield-keyhole-line" size="26" color="white" />
          </div>
          <div class="cif-hero__meta">
            <h1 class="cif-hero__title">Quality Audit Dashboard</h1>
            <p class="cif-hero__subtitle">
              Monitoring kelengkapan data CIF, anomali prioritas, dan kualitas cabang berdasarkan database operasional.
            </p>
            <div class="cif-hero__badges">
              <span class="cif-badge cif-badge--glass">
                <v-icon size="10" color="white">ri-database-2-line</v-icon>
                {{ qualityData?.database || 'Memuat database...' }}
              </span>
            </div>
          </div>
        </div>
      </div>
    </div>

    <v-alert
      v-if="errorMessage"
      type="error"
      variant="tonal"
      class="mb-4"
      density="comfortable"
    >
      {{ errorMessage }}
    </v-alert>

    <div v-if="isLoading">
      <v-row class="mb-4">
        <v-col v-for="i in 3" :key="i" cols="12" md="4">
          <v-skeleton-loader type="card" rounded="xl" />
        </v-col>
      </v-row>
      <v-skeleton-loader type="table" rounded="xl" />
    </div>

    <template v-else-if="qualityData">
      <v-row class="mb-4">
        <v-col v-for="card in kpiCards" :key="card.title" cols="12" md="4">
          <div class="content-card quality-kpi">
            <div class="quality-kpi__icon" :style="{ color: card.color, background: `${card.color}14` }">
              <v-icon :icon="card.icon" size="24" />
            </div>
            <div>
              <div class="quality-kpi__label">{{ card.title }}</div>
              <div class="quality-kpi__value" :style="{ color: card.color }">
                {{ formatValue(card.value, card.format) }}
              </div>
              <div class="quality-kpi__sub">
                {{ card.subtitle }} - {{ formatValue(card.count) }} CIF terkait
              </div>
            </div>
          </div>
        </v-col>
      </v-row>

      <div class="content-card mb-6">
        <div class="content-card__accent-top" style="background: linear-gradient(90deg, #059669, #0284c7);"></div>
        <div class="content-card__header">
          <div>
            <div class="content-card__title">Distribusi Kualitas per Cabang</div>
            <div class="content-card__subtitle">Cabang diurutkan berdasarkan rasio data yang perlu ditindaklanjuti.</div>
          </div>
        </div>
        <div class="content-card__body">
          <v-row>
            <v-col
              v-for="branch in branches"
              :key="branch.cabang"
              cols="12"
              :md="branches.length <= 1 ? 12 : 6"
              :lg="branches.length <= 1 ? 12 : 4"
            >
              <div class="branch-quality-card">
                <div class="d-flex justify-space-between align-start mb-3">
                  <div>
                    <div class="branch-quality-card__title">{{ branch.cabang }}</div>
                    <div class="branch-quality-card__meta">{{ formatValue(branch.total) }} total CIF</div>
                  </div>
                  <v-chip size="small" color="error" variant="tonal">
                    {{ formatValue(branch.rasio_anomali, 'percent') }}
                  </v-chip>
                </div>
                <div class="branch-quality-card__row">
                  <span>Lengkap</span>
                  <strong>{{ formatValue(branch.lengkap) }}</strong>
                </div>
                <v-progress-linear :model-value="branch.total ? (branch.lengkap / branch.total) * 100 : 0" color="#10b981" height="7" rounded />
                <div class="branch-quality-card__row mt-3">
                  <span>Belum Lengkap</span>
                  <strong>{{ formatValue(branch.belum_lengkap) }}</strong>
                </div>
                <v-progress-linear :model-value="branch.total ? (branch.belum_lengkap / branch.total) * 100 : 0" color="#e11d48" height="7" rounded />
                <div class="branch-quality-card__row mt-3">
                  <span>Cek Ulang</span>
                  <strong>{{ formatValue(branch.cek_ulang) }}</strong>
                </div>
                <v-progress-linear :model-value="branch.total ? (branch.cek_ulang / branch.total) * 100 : 0" color="#f59e0b" height="7" rounded />
              </div>
            </v-col>
          </v-row>
        </div>
      </div>

      <div class="content-card content-card--dark-header">
        <div class="content-card__accent-top" style="background: linear-gradient(90deg, #e11d48, #f97316);"></div>
        <div class="content-card__header">
          <div class="d-flex align-center gap-3">
            <div class="cif-pulse-red">
              <v-icon icon="ri-alarm-warning-fill" size="26" color="#e11d48" />
            </div>
            <div>
              <div class="content-card__title">Drill-Down Anomali CIF</div>
              <div class="content-card__subtitle">Sampel prioritas nasabah dengan status Belum Lengkap atau Perlu Cek Ulang.</div>
            </div>
          </div>
        </div>
        <div class="overflow-x-auto">
          <v-data-table
            :headers="headers"
            :items="anomalies"
            class="cif-vtable cif-table-frozen"
            hover
            density="comfortable"
            :items-per-page="15"
          >
            <template #item.nocif="{ item }">
              <span class="font-weight-medium font-monospace">{{ item.nocif }}</span>
            </template>
            <template #item.namanasabah="{ item }">
              <span class="font-weight-bold">{{ item.namanasabah }}</span>
            </template>
            <template #item.status_cif="{ item }">
              <v-chip
                :color="item.status_cif === 'Belum Lengkap' ? 'error' : 'warning'"
                size="small"
                variant="flat"
              >
                {{ item.status_cif }}
              </v-chip>
            </template>
            <template #item.anomali="{ item }">
              <div class="d-flex flex-wrap gap-1">
                <v-chip
                  v-for="issue in getAnomalies(item)"
                  :key="issue"
                  color="error"
                  variant="outlined"
                  size="x-small"
                  class="font-weight-bold"
                >
                  <v-icon start size="12">ri-error-warning-line</v-icon>
                  {{ issue }}
                </v-chip>
                <span v-if="!getAnomalies(item).length" class="text-muted text-caption">Perlu review manual status kelengkapan</span>
              </div>
            </template>
          </v-data-table>
        </div>
      </div>
    </template>
  </div>
</template>

<style scoped>
.cif-page { background: #f1f5f9; min-height: 100vh; padding-bottom: 48px; }

.quality-kpi {
  display: flex;
  gap: 14px;
  align-items: flex-start;
  padding: 18px;
}

.quality-kpi__icon {
  width: 48px;
  height: 48px;
  border-radius: 14px;
  display: grid;
  place-items: center;
  flex: 0 0 auto;
}

.quality-kpi__label {
  font-size: 12px;
  font-weight: 800;
  letter-spacing: .06em;
  color: #64748b;
  text-transform: uppercase;
}

.quality-kpi__value {
  margin-top: 4px;
  font-size: clamp(24px, 3vw, 34px);
  line-height: 1;
  font-weight: 900;
  letter-spacing: -0.04em;
}

.quality-kpi__sub {
  margin-top: 8px;
  font-size: 12px;
  color: #64748b;
}

.branch-quality-card {
  border: 1px solid #dbeafe;
  border-radius: 18px;
  background: linear-gradient(180deg, #ffffff, #f8fafc);
  padding: 16px;
  box-shadow: 0 10px 24px rgba(15, 23, 42, 0.04);
}

.branch-quality-card__title {
  font-size: 14px;
  font-weight: 900;
  color: #0f172a;
}

.branch-quality-card__meta {
  margin-top: 2px;
  font-size: 12px;
  color: #64748b;
}

.branch-quality-card__row {
  display: flex;
  justify-content: space-between;
  align-items: center;
  gap: 12px;
  margin-bottom: 6px;
  font-size: 12px;
  color: #64748b;
}

.branch-quality-card__row strong {
  color: #0f172a;
  font-weight: 800;
}

.cif-table-frozen :deep(th:nth-child(1)),
.cif-table-frozen :deep(td:nth-child(1)) {
  position: sticky;
  left: 0;
  z-index: 2;
  background: white;
  border-right: 1px solid #e2e8f0;
}

.cif-table-frozen :deep(th:nth-child(1)),
.cif-table-frozen :deep(th:nth-child(1) *) { background: #0f172a !important; color: #e5edf7 !important; }

.cif-table-frozen :deep(th:nth-child(2)),
.cif-table-frozen :deep(td:nth-child(2)) {
  position: sticky;
  left: 120px;
  z-index: 2;
  background: white;
  box-shadow: inset -4px 0 8px -4px rgba(0, 0, 0, 0.08);
}

.cif-table-frozen :deep(th:nth-child(2)) {
  background: #0f172a !important;
  color: #e5edf7 !important;
  box-shadow: inset -4px 0 8px -4px rgba(0, 0, 0, 0.4);
}
.cif-table-frozen :deep(th:nth-child(2) *) { color: #e5edf7 !important; }

.text-muted { color: #64748b !important; }
.font-monospace { font-family: monospace; }
</style>
