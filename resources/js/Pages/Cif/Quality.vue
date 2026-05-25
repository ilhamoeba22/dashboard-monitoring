<script setup>
import { ref, computed } from 'vue'
import DefaultLayout from '@/layouts/default.vue'
import { useCifAuditLogic } from '@/composables/useCifAuditLogic'
import '@/assets/css/cif-shared.css'

defineOptions({ layout: DefaultLayout })

const { getCifStatus, isNikAnomaly, isNamaAnomaly, isHpAnomaly, isIbuAnomaly } = useCifAuditLogic()

// --- Mock KPI Data ---
const kpiData = {
  lengkap: { value: 85.4, count: 12540 },
  cekUlang: { value: 9.2, count: 1350 },
  belumLengkap: { value: 5.4, count: 793 }
}

// --- Mock Branch Data ---
const branchData = ref([
  { name: 'Cabang Utama', lengkap: 92, cekUlang: 5, belumLengkap: 3, total: 4500 },
  { name: 'Cabang Sudirman', lengkap: 88, cekUlang: 8, belumLengkap: 4, total: 3200 },
  { name: 'Cabang Thamrin', lengkap: 81, cekUlang: 12, belumLengkap: 7, total: 2800 },
  { name: 'Cabang Kebon Jeruk', lengkap: 75, cekUlang: 15, belumLengkap: 10, total: 1950 },
  { name: 'Cabang BSD', lengkap: 95, cekUlang: 3, belumLengkap: 2, total: 2233 }
])

// --- Mock Anomalies Data ---
const anomaliesData = ref([
  { nocif: '1002938475', namanasabah: 'Budi Santoso', noktp: '317401230495', nohp: '0812', nama_ibu: 'Budi Santoso', kota: 'JAKARTA', ket_stskawin: 'KAWIN', nik_pasangan: '00000000', status: 'Belum Lengkap' },
  { nocif: '1002938476', namanasabah: 'Siti Aminah', noktp: '3174012304950001', nohp: '08123456789', nama_ibu: 'AMINAH BINTI HASAN', kota: 'BANDUNG', ket_stskawin: 'BELUM KAWIN', nik_pasangan: '-', status: 'Cek Ulang' },
  { nocif: '1002938477', namanasabah: 'Andi Pratama\'', noktp: '31740123', nohp: '-', nama_ibu: 'IBU ANDI', kota: 'SURABAYA', ket_stskawin: 'KAWIN', nik_pasangan: '3174012304950002', status: 'Belum Lengkap' },
  { nocif: '1002938478', namanasabah: 'Dewi Lestari', noktp: '3174012304950003', nohp: '08129876543', nama_ibu: 'JAKARTA', kota: 'JAKARTA', ket_stskawin: 'KAWIN', nik_pasangan: '3174012304950004', status: 'Cek Ulang' },
])

const headers = [
  { title: 'NO CIF', key: 'nocif', align: 'center', width: '120px', fixed: true },
  { title: 'NAMA NASABAH', key: 'namanasabah', align: 'left', width: '200px', fixed: true },
  { title: 'STATUS', key: 'status', align: 'center', width: '140px' },
  { title: 'KETERANGAN ANOMALI', key: 'anomali', align: 'left', width: '350px' },
]

function getAnomalies(item) {
  const issues = []
  if (isNikAnomaly(item.noktp)) issues.push('KTP Invalid')
  if (isNamaAnomaly(item.namanasabah)) issues.push('Nama Invalid')
  if (isHpAnomaly(item.nohp)) issues.push('HP Invalid')
  if (isIbuAnomaly(item.nama_ibu, item.namanasabah, item.kota)) issues.push('Nama Ibu Invalid')
  if (item.ket_stskawin === 'KAWIN' && isNikAnomaly(item.nik_pasangan)) issues.push('NIK Pasangan Invalid')
  return issues
}
</script>

<template>
  <div class="cif-page px-4 pt-0">
    <!-- HERO HEADER -->
    <div class="cif-hero mb-6">
      <div class="cif-hero__deco"></div>
      <div class="cif-hero__inner">
        <div class="cif-hero__top">
          <div class="cif-hero__icon">
            <v-icon icon="ri-shield-keyhole-line" size="26" color="white" />
          </div>
          <div class="cif-hero__meta">
            <h1 class="cif-hero__title">Quality Audit Dashboard</h1>
            <p class="cif-hero__subtitle">Analisis tingkat kelengkapan dan validitas data berkas CIF.</p>
          </div>
        </div>
      </div>
    </div>

    <!-- TOP METRICS ROW -->
    <div class="kpi-cards-grid">
      <!-- 1. Data Lengkap -->
      <div class="kpi-card kpi-card--success">
        <div class="kpi-card__accent" style="background: var(--cif-success);"></div>
        <div class="kpi-card__inner">
          <div class="kpi-card__header">
            <div class="kpi-card__label">% DATA LENGKAP</div>
            <div class="kpi-card__icon cif-icon-green"><v-icon>ri-checkbox-circle-fill</v-icon></div>
          </div>
          <div class="kpi-card__value">{{ kpiData.lengkap.value }}%</div>
          <div class="kpi-card__sub">{{ kpiData.lengkap.count.toLocaleString('id-ID') }} Nasabah Valid</div>
        </div>
      </div>
      <!-- 2. Cek Ulang -->
      <div class="kpi-card kpi-card--warning">
        <div class="kpi-card__accent" style="background: var(--cif-warning);"></div>
        <div class="kpi-card__inner">
          <div class="kpi-card__header">
            <div class="kpi-card__label">% PERLU CEK ULANG</div>
            <div class="kpi-card__icon cif-icon-amber"><v-icon>ri-information-fill</v-icon></div>
          </div>
          <div class="kpi-card__value">{{ kpiData.cekUlang.value }}%</div>
          <div class="kpi-card__sub">{{ kpiData.cekUlang.count.toLocaleString('id-ID') }} Nasabah Perlu Direview</div>
        </div>
      </div>
      <!-- 3. Belum Lengkap -->
      <div class="kpi-card kpi-card--danger">
        <div class="kpi-card__accent" style="background: var(--cif-danger);"></div>
        <div class="kpi-card__inner">
          <div class="kpi-card__header">
            <div class="kpi-card__label">% BELUM LENGKAP</div>
            <div class="kpi-card__icon cif-icon-red"><v-icon>ri-alert-fill</v-icon></div>
          </div>
          <div class="kpi-card__value">{{ kpiData.belumLengkap.value }}%</div>
          <div class="kpi-card__sub">{{ kpiData.belumLengkap.count.toLocaleString('id-ID') }} Nasabah Tidak Lengkap</div>
        </div>
      </div>
    </div>

    <!-- BRANCH BREAKDOWN SECTION -->
    <div class="content-card mb-6">
      <div class="content-card__accent-top" style="background: linear-gradient(90deg, #059669, #0284c7);"></div>
      <div class="content-card__header">
        <div>
          <div class="content-card__title">Distribusi Kualitas per Cabang</div>
          <div class="content-card__subtitle">Persentase kelengkapan data berbanding total nasabah cabang</div>
        </div>
      </div>
      <div class="content-card__body">
        <v-row>
          <v-col v-for="branch in branchData" :key="branch.name" cols="12" md="6" lg="4">
            <div class="pa-4 border rounded-lg bg-white h-100">
              <div class="d-flex justify-space-between align-center mb-2">
                <span class="font-weight-bold text-primary">{{ branch.name }}</span>
                <span class="text-caption text-muted">{{ branch.total.toLocaleString('id-ID') }} CIF</span>
              </div>
              <div class="d-flex align-center gap-2 mb-1">
                <v-progress-linear :model-value="branch.lengkap" color="#10b981" height="8" rounded />
                <span class="text-caption font-weight-bold" style="width: 35px;">{{ branch.lengkap }}%</span>
              </div>
              <div class="d-flex align-center gap-2 mb-1">
                <v-progress-linear :model-value="branch.cekUlang" color="#f59e0b" height="8" rounded />
                <span class="text-caption font-weight-bold text-warning" style="width: 35px;">{{ branch.cekUlang }}%</span>
              </div>
              <div class="d-flex align-center gap-2">
                <v-progress-linear :model-value="branch.belumLengkap" color="#e11d48" height="8" rounded />
                <span class="text-caption font-weight-bold text-error" style="width: 35px;">{{ branch.belumLengkap }}%</span>
              </div>
            </div>
          </v-col>
        </v-row>
      </div>
    </div>

    <!-- DRILL-DOWN ANOMALIES TABLE -->
    <div class="content-card content-card--dark-header">
      <div class="content-card__accent-top" style="background: linear-gradient(90deg, #e11d48, #f97316);"></div>
      <div class="content-card__header">
        <div class="d-flex align-center gap-3">
          <div class="cif-pulse-red">
            <v-icon icon="ri-alarm-warning-fill" size="26" color="#e11d48" />
          </div>
          <div>
            <div class="content-card__title">Drill-Down Anomali CIF</div>
            <div class="content-card__subtitle">Daftar nasabah dengan status Belum Lengkap atau Perlu Cek Ulang</div>
          </div>
        </div>
      </div>
      <div class="overflow-x-auto">
        <v-data-table
          :headers="headers"
          :items="anomaliesData"
          class="cif-vtable cif-table-frozen"
          hover
          density="comfortable"
          :items-per-page="50"
        >
          <template #item.nocif="{ item }">
            <span class="font-weight-medium font-monospace">{{ item.nocif }}</span>
          </template>
          <template #item.namanasabah="{ item }">
            <span class="font-weight-bold">{{ item.namanasabah }}</span>
          </template>
          <template #item.status="{ item }">
            <v-chip
              :color="item.status === 'Belum Lengkap' ? 'error' : 'warning'"
              size="small"
              variant="flat"
            >
              {{ item.status }}
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
              <span v-if="!getAnomalies(item).length" class="text-muted text-caption">Tidak ada deteksi spesifik</span>
            </div>
          </template>
        </v-data-table>
      </div>
    </div>
  </div>
</template>

<style scoped>
.cif-page { background: #f1f5f9; }

/* Frozen Column CSS Implementation */
.cif-table-frozen :deep(th:nth-child(1)),
.cif-table-frozen :deep(td:nth-child(1)) {
  position: sticky;
  left: 0;
  z-index: 2;
  background: white;
  border-right: 1px solid #e2e8f0;
}
.cif-table-frozen :deep(th:nth-child(1)) { background: #0f172a !important; }

.cif-table-frozen :deep(th:nth-child(2)),
.cif-table-frozen :deep(td:nth-child(2)) {
  position: sticky;
  left: 120px; /* Width of NO CIF col */
  z-index: 2;
  background: white;
  box-shadow: inset -4px 0 8px -4px rgba(0, 0, 0, 0.08);
}
.cif-table-frozen :deep(th:nth-child(2)) { background: #0f172a !important; box-shadow: inset -4px 0 8px -4px rgba(0, 0, 0, 0.4); }

.text-error { color: #e11d48 !important; }
.text-warning { color: #f59e0b !important; }
.text-muted { color: #64748b !important; }
.font-monospace { font-family: monospace; }
</style>
