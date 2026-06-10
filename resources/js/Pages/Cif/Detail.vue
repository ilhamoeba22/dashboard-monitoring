<script setup>
import { onMounted, ref } from 'vue'
import DefaultLayout from '@/layouts/default.vue'
import { formatExactRupiah } from '@/utils/money'
import '@/assets/css/cif-shared.css'

defineOptions({ layout: DefaultLayout })

const props = defineProps({
  nocif: { type: String, default: '1002938475' }
})

const isLoading = ref(true)
const errorMessage = ref('')
const cifData = ref({
  nocif: props.nocif,
  nama: '-',
  noktp: '-',
  alamat: '-',
  status_data: 'Cek Ulang',
  anomali: '-',
  jenis_kelamin: '-',
  tempat_tanggal_lahir: '-',
  usia: 0,
  nama_ibu_kandung: '-',
  no_hp: '-',
  email: '-',
  pekerjaan: '-',
  nama_instansi: '-',
  penghasilan: '-',
  status_pernikahan: '-',
  nama_pasangan: '-',
  nik_pasangan: '-',
  tgl_lahir_pasangan: '-',
  portofolio: { pembiayaan: [], tabungan: [], deposito: [] },
})

const activeTab = ref('profil')

function formatRp(val) {
  return formatExactRupiah(val || 0)
}

function normalizeCifPayload(payload) {
  const pembiayaan = Array.isArray(payload.portofolio?.pembiayaan)
    ? payload.portofolio.pembiayaan.map(item => ({
        norek: item.nokontrak || item.norek || '-',
        produk: item.kdprd || item.produk || 'Pembiayaan',
        plafond: Number(item.mdlawal || item.plafond || 0),
        os: Number(item.osmdlc || item.os || 0),
        status: item.stsrec || item.status || '-',
      }))
    : []

  const tabungan = Array.isArray(payload.portofolio?.tabungan)
    ? payload.portofolio.tabungan.map(item => ({
        norek: item.notab || item.norek || '-',
        produk: item.kdprd || item.produk || 'Tabungan',
        saldo: Number(item.sahirrp || item.saldo || 0),
        status: item.stsrec || item.status || '-',
      }))
    : []

  const deposito = Array.isArray(payload.portofolio?.deposito)
    ? payload.portofolio.deposito.map(item => ({
        norek: item.nodep || item.norek || '-',
        produk: item.kdprd || item.produk || 'Deposito',
        nominal: Number(item.nomrp || item.nominal || 0),
        status: item.stsrec || item.status || '-',
      }))
    : []

  return {
    nocif: payload.nocif || props.nocif,
    nama: payload.nama || '-',
    noktp: payload.ktp || '-',
    alamat: [payload.alamat, payload.kelurahan, payload.kecamatan, payload.kota].filter(Boolean).join(', ') || '-',
    status_data: payload.ktp && payload.ibu_kandung ? 'Lengkap' : 'Cek Ulang',
    anomali: payload.ktp && payload.ibu_kandung ? '-' : 'Data identitas atau keluarga perlu dilengkapi',
    jenis_kelamin: '-',
    tempat_tanggal_lahir: `${payload.tempat_lahir || '-'}, ${payload.tanggal_lahir || '-'}`,
    usia: payload.umur || 0,
    nama_ibu_kandung: payload.ibu_kandung || '-',
    no_hp: payload.telp || '-',
    email: '-',
    pekerjaan: '-',
    nama_instansi: '-',
    penghasilan: '-',
    status_pernikahan: '-',
    nama_pasangan: '-',
    nik_pasangan: '-',
    tgl_lahir_pasangan: '-',
    portofolio: { pembiayaan, tabungan, deposito },
  }
}

async function fetchDetail() {
  isLoading.value = true
  errorMessage.value = ''

  try {
    const response = await fetch(`/api/v1/cif/${encodeURIComponent(props.nocif)}`)
    const json = await response.json()
    if (!response.ok || !json.success) {
      throw new Error(json.message || 'Gagal memuat detail CIF.')
    }
    cifData.value = normalizeCifPayload(json.data)
  } catch (error) {
    errorMessage.value = error.message || 'Gagal memuat detail CIF.'
  } finally {
    isLoading.value = false
  }
}

onMounted(fetchDetail)

// Ensure fixed header widths for portofolio tables
const pembiayaanHeaders = [
  { title: 'NO. REKENING', key: 'norek', width: '150px' },
  { title: 'PRODUK', key: 'produk', width: '250px' },
  { title: 'PLAFOND', key: 'plafond', align: 'end', width: '200px' },
  { title: 'OUTSTANDING', key: 'os', align: 'end', width: '200px' },
  { title: 'STATUS', key: 'status', align: 'center', width: '150px' }
]

const tabunganHeaders = [
  { title: 'NO. REKENING', key: 'norek', width: '150px' },
  { title: 'PRODUK', key: 'produk', width: '250px' },
  { title: 'SALDO', key: 'saldo', align: 'end', width: '200px' },
  { title: 'STATUS', key: 'status', align: 'center', width: '150px' }
]

const depositoHeaders = [
  { title: 'NO. DEPOSITO', key: 'norek', width: '150px' },
  { title: 'PRODUK', key: 'produk', width: '250px' },
  { title: 'NOMINAL', key: 'nominal', align: 'end', width: '200px' },
  { title: 'STATUS', key: 'status', align: 'center', width: '150px' }
]
</script>

<template>
  <div class="cif-page px-4 pt-0">
    <!-- BREADCRUMB / BACK NAVIGATION -->
    <div class="mb-4">
      <v-btn variant="text" color="primary" class="text-none px-0" prepend-icon="ri-arrow-left-line">
        Kembali ke Daftar CIF
      </v-btn>
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

    <div v-if="isLoading" class="mb-6">
      <v-skeleton-loader type="card" rounded="xl" class="mb-4" />
      <v-skeleton-loader type="table" rounded="xl" />
    </div>

    <!-- PROFILE HEADER CARD -->
    <div v-else class="content-card mb-6 overflow-hidden relative">
      <div class="content-card__accent-top" style="background: linear-gradient(90deg, #059669, #10b981);"></div>
      <div class="pa-6 d-flex flex-column flex-md-row gap-6 align-md-center">
        <!-- Avatar -->
        <v-avatar color="grey-lighten-3" size="90" class="border">
          <span class="text-h4 font-weight-bold text-primary">{{ cifData.nama.charAt(0) }}</span>
        </v-avatar>
        
        <!-- Info -->
        <div class="flex-grow-1">
          <div class="d-flex flex-wrap align-center gap-3 mb-1">
            <h1 class="text-h5 font-weight-bold mb-0 text-grey-darken-4">{{ cifData.nama }}</h1>
            <v-chip
              :color="cifData.status_data === 'Lengkap' ? 'success' : 'error'"
              size="small"
              class="font-weight-bold text-uppercase"
              variant="flat"
            >
              {{ cifData.status_data }}
            </v-chip>
          </div>
          <div class="text-body-2 text-muted mb-2 font-monospace">
            CIF: <strong>{{ cifData.nocif }}</strong> <span class="mx-2">|</span> KTP: <strong>{{ cifData.noktp }}</strong>
          </div>
          <div class="text-body-2 text-muted d-flex align-center">
            <v-icon size="16" class="me-1">ri-map-pin-line</v-icon> {{ cifData.alamat }}
          </div>
        </div>
      </div>
    </div>

    <!-- TABS NAVIGATION & CONTENT -->
    <div v-if="!isLoading" class="content-card">
      <div class="border-b">
        <v-tabs v-model="activeTab" color="primary" align-tabs="start" class="px-2">
          <v-tab value="profil" class="text-none font-weight-medium">
            <v-icon start>ri-user-settings-line</v-icon> Profil Lengkap
          </v-tab>
          <v-tab value="pembiayaan" class="text-none font-weight-medium">
            <v-icon start>ri-bank-line</v-icon> Pembiayaan ({{ cifData.portofolio.pembiayaan.length }})
          </v-tab>
          <v-tab value="tabungan" class="text-none font-weight-medium">
            <v-icon start>ri-wallet-3-line</v-icon> Tabungan ({{ cifData.portofolio.tabungan.length }})
          </v-tab>
          <v-tab value="deposito" class="text-none font-weight-medium">
            <v-icon start>ri-safe-2-line</v-icon> Deposito ({{ cifData.portofolio.deposito.length }})
          </v-tab>
        </v-tabs>
      </div>

      <div class="pa-6">
        <v-window v-model="activeTab">
          <!-- TAB 1: Profil Lengkap -->
          <v-window-item value="profil">
            <v-row>
              <!-- Demografi -->
              <v-col cols="12" md="6">
                <h3 class="text-subtitle-1 font-weight-bold mb-4 d-flex align-center text-primary">
                  <v-icon size="20" class="me-2">ri-shield-user-line</v-icon> Demografi Utama
                </h3>
                <v-table density="comfortable" class="border rounded-lg mb-6 cif-info-table">
                  <tbody>
                    <tr><td class="font-weight-medium bg-grey-lighten-4" width="40%">Jenis Kelamin</td><td>{{ cifData.jenis_kelamin }}</td></tr>
                    <tr><td class="font-weight-medium bg-grey-lighten-4">Tempat, Tgl Lahir</td><td>{{ cifData.tempat_tanggal_lahir }} ({{ cifData.usia }} Tahun)</td></tr>
                    <tr><td class="font-weight-medium bg-grey-lighten-4">Nama Ibu Kandung</td><td><span :class="{'text-error font-weight-bold': cifData.nama_ibu_kandung === '-'}">{{ cifData.nama_ibu_kandung }}</span></td></tr>
                    <tr><td class="font-weight-medium bg-grey-lighten-4">No. Handphone</td><td>{{ cifData.no_hp }}</td></tr>
                    <tr><td class="font-weight-medium bg-grey-lighten-4">Email</td><td>{{ cifData.email }}</td></tr>
                  </tbody>
                </v-table>

                <h3 class="text-subtitle-1 font-weight-bold mb-4 d-flex align-center text-primary">
                  <v-icon size="20" class="me-2">ri-briefcase-line</v-icon> Pekerjaan & Finansial
                </h3>
                <v-table density="comfortable" class="border rounded-lg cif-info-table">
                  <tbody>
                    <tr><td class="font-weight-medium bg-grey-lighten-4" width="40%">Pekerjaan</td><td>{{ cifData.pekerjaan }}</td></tr>
                    <tr><td class="font-weight-medium bg-grey-lighten-4">Nama Instansi</td><td>{{ cifData.nama_instansi }}</td></tr>
                    <tr><td class="font-weight-medium bg-grey-lighten-4">Estimasi Penghasilan</td><td>{{ cifData.penghasilan }}</td></tr>
                  </tbody>
                </v-table>
              </v-col>

              <!-- Relasional -->
              <v-col cols="12" md="6">
                <h3 class="text-subtitle-1 font-weight-bold mb-4 d-flex align-center text-primary">
                  <v-icon size="20" class="me-2">ri-group-line</v-icon> Relasional / Keluarga
                </h3>
                <v-table density="comfortable" class="border rounded-lg cif-info-table">
                  <tbody>
                    <tr><td class="font-weight-medium bg-grey-lighten-4" width="40%">Status Pernikahan</td><td><v-chip size="small" color="primary" variant="tonal">{{ cifData.status_pernikahan }}</v-chip></td></tr>
                    <tr v-if="cifData.status_pernikahan === 'KAWIN'"><td class="font-weight-medium bg-grey-lighten-4">Nama Pasangan</td><td>{{ cifData.nama_pasangan }}</td></tr>
                    <tr v-if="cifData.status_pernikahan === 'KAWIN'">
                      <td class="font-weight-medium bg-grey-lighten-4">NIK Pasangan</td>
                      <td :class="{'text-error font-weight-bold': cifData.nik_pasangan === '00000000'}">
                        {{ cifData.nik_pasangan }}
                        <v-icon v-if="cifData.nik_pasangan === '00000000'" color="error" size="14" class="ms-1">ri-alert-line</v-icon>
                      </td>
                    </tr>
                    <tr v-if="cifData.status_pernikahan === 'KAWIN'"><td class="font-weight-medium bg-grey-lighten-4">Tgl Lahir Pasangan</td><td>{{ cifData.tgl_lahir_pasangan }}</td></tr>
                  </tbody>
                </v-table>
                
                <v-alert
                  v-if="cifData.status_data !== 'Lengkap'"
                  type="error"
                  variant="tonal"
                  icon="ri-error-warning-fill"
                  class="mt-6 font-weight-medium border"
                >
                  Terdapat anomali data pada Profil ini: <strong>{{ cifData.anomali }}</strong>. <br/>
                  <span class="text-caption">Silakan lakukan pemutakhiran data di Core Banking System.</span>
                </v-alert>
              </v-col>
            </v-row>
          </v-window-item>

          <!-- TAB 2: Pembiayaan -->
          <v-window-item value="pembiayaan">
            <v-data-table
              v-if="cifData.portofolio.pembiayaan.length"
              :headers="pembiayaanHeaders"
              :items="cifData.portofolio.pembiayaan"
              class="cif-vtable border rounded-lg"
              hover
              density="comfortable"
              hide-default-footer
            >
              <template #item.norek="{ item }">
                <span class="font-weight-bold text-primary font-monospace">{{ item.norek }}</span>
              </template>
              <template #item.plafond="{ item }">
                <span>{{ formatRp(item.plafond) }}</span>
              </template>
              <template #item.os="{ item }">
                <span class="font-weight-medium">{{ formatRp(item.os) }}</span>
              </template>
              <template #item.status="{ item }">
                <v-chip size="small" color="success" variant="flat">{{ item.status }}</v-chip>
              </template>
            </v-data-table>
            <v-empty-state
              v-else
              icon="ri-bank-line"
              title="Tidak Ada Pembiayaan"
              text="Nasabah ini tidak memiliki fasilitas pembiayaan yang aktif."
              class="border rounded-lg bg-grey-lighten-5 py-10"
            ></v-empty-state>
          </v-window-item>

          <!-- TAB 3: Tabungan -->
          <v-window-item value="tabungan">
            <v-data-table
              v-if="cifData.portofolio.tabungan.length"
              :headers="tabunganHeaders"
              :items="cifData.portofolio.tabungan"
              class="cif-vtable border rounded-lg"
              hover
              density="comfortable"
              hide-default-footer
            >
              <template #item.norek="{ item }">
                <span class="font-weight-bold text-primary font-monospace">{{ item.norek }}</span>
              </template>
              <template #item.saldo="{ item }">
                <span class="font-weight-medium">{{ formatRp(item.saldo) }}</span>
              </template>
              <template #item.status="{ item }">
                <v-chip size="small" color="success" variant="flat">{{ item.status }}</v-chip>
              </template>
            </v-data-table>
            <v-empty-state
              v-else
              icon="ri-wallet-3-line"
              title="Tidak Ada Tabungan"
              text="Nasabah ini tidak memiliki rekening tabungan yang aktif."
              class="border rounded-lg bg-grey-lighten-5 py-10"
            ></v-empty-state>
          </v-window-item>

          <!-- TAB 4: Deposito -->
          <v-window-item value="deposito">
            <v-data-table
              v-if="cifData.portofolio.deposito.length"
              :headers="depositoHeaders"
              :items="cifData.portofolio.deposito"
              class="cif-vtable border rounded-lg"
              hover
              density="comfortable"
              hide-default-footer
            >
              <template #item.norek="{ item }">
                <span class="font-weight-bold text-primary font-monospace">{{ item.norek }}</span>
              </template>
              <template #item.nominal="{ item }">
                <span class="font-weight-medium">{{ formatRp(item.nominal) }}</span>
              </template>
              <template #item.status="{ item }">
                <v-chip size="small" color="success" variant="flat">{{ item.status }}</v-chip>
              </template>
            </v-data-table>
            <v-empty-state
              v-else
              icon="ri-safe-2-line"
              title="Tidak Ada Deposito"
              text="Nasabah ini tidak memiliki penempatan deposito yang aktif."
              class="border rounded-lg bg-grey-lighten-5 py-10"
            ></v-empty-state>
          </v-window-item>
        </v-window>
      </div>
    </div>
  </div>
</template>

<style scoped>
.cif-page { background: #f1f5f9; }
.cif-info-table td { font-size: 0.875rem; padding: 12px 16px !important; }
.text-error { color: #e11d48 !important; }
.text-muted { color: #64748b !important; }
.font-monospace { font-family: monospace; }
</style>
