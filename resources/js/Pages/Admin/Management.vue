<script setup>
import { ref, onMounted } from 'vue'
import { Head } from '@inertiajs/vue3'
import DefaultLayout from '@/layouts/default.vue'
import axios from 'axios'

defineOptions({ layout: DefaultLayout })

// --- Tab State ---
const activeTab = ref('target-rbb')

// --- Upload Target RBB ---
const uploadLoading = ref(false)
const uploadFile = ref(null)
const uploadYear = ref(new Date().getFullYear())
const uploadHistory = ref([])

const snackbar = ref({ show: false, text: '', color: 'success' })

const showNotify = (text, color = 'success') => {
  snackbar.value = { show: true, text, color }
}

const downloadTemplate = () => {
  window.open('/api/v1/financing/targets/template', '_blank')
}

const handleUpload = async () => {
  if (!uploadFile.value) return showNotify('Pilih file terlebih dahulu', 'warning')

  uploadLoading.value = true
  const formData = new FormData()

  // Vuetify v-file-input v-model returns File[] (array), extract single File
  const file = Array.isArray(uploadFile.value) ? uploadFile.value[0] : uploadFile.value
  if (!file) {
    uploadLoading.value = false
    return showNotify('File tidak valid', 'warning')
  }

  formData.append('file', file)
  formData.append('year', uploadYear.value)

  try {
    const response = await axios.post('/api/v1/financing/targets/import', formData, {
      headers: {
        'Content-Type': 'multipart/form-data',
        'Accept': 'application/json',
        'X-Requested-With': 'XMLHttpRequest'
      }
    })
    if (response.data.success) {
      showNotify(response.data.message)
      uploadFile.value = null
      fetchHistory() // Refresh history after successful upload
    }
  } catch (error) {
    showNotify(error.response?.data?.error || 'Gagal mengupload target', 'error')
  } finally {
    uploadLoading.value = false
  }
}

const fetchHistory = async () => {
  try {
    const { data } = await axios.get('/api/v1/financing/targets/history')
    if (data.success) {
      uploadHistory.value = data.data.map(item => ({
        filename: item.document_name,
        year: item.year,
        size: '-',
        timestamp: item.date,
        status: item.status.toLowerCase()
      }))
    }
  } catch (error) {
    console.error('Failed to fetch upload history:', error)
  }
}

// --- Configuration State ---
const configLoading = ref(false)
const savingConfig = ref(false)
const appSettings = ref({
  ppka_manual_enabled: false,
  ppka_manual_roles: []
})

const rolesOptions = ['Direktur', 'Manajer Risiko', 'Admin', 'Supervisor']

const fetchSettings = async () => {
  configLoading.value = true
  try {
    const response = await axios.get('/api/v1/admin/settings')
    if (response.data.success && response.data.data) {
      if (response.data.data.ppka_manual_enabled !== undefined) {
        appSettings.value.ppka_manual_enabled = response.data.data.ppka_manual_enabled === 'true' || response.data.data.ppka_manual_enabled === true
      }
      if (response.data.data.ppka_manual_roles) {
        appSettings.value.ppka_manual_roles = typeof response.data.data.ppka_manual_roles === 'string' ? JSON.parse(response.data.data.ppka_manual_roles) : response.data.data.ppka_manual_roles
      }
    }
  } catch (error) {
    console.error('Failed to fetch settings:', error)
  } finally {
    configLoading.value = false
  }
}

const saveSettings = async () => {
  savingConfig.value = true
  try {
    const payload = {
      settings: {
        ppka_manual_enabled: appSettings.value.ppka_manual_enabled,
        ppka_manual_roles: JSON.stringify(appSettings.value.ppka_manual_roles)
      }
    }
    const response = await axios.post('/api/v1/admin/settings', payload)
    if (response.data.success) {
      showNotify('Pengaturan berhasil disimpan', 'success')
    }
  } catch (error) {
    showNotify('Gagal menyimpan pengaturan', 'error')
  } finally {
    savingConfig.value = false
  }
}

onMounted(() => {
  fetchHistory()
  fetchSettings()
})

// --- Tabs Configuration ---
const tabs = [
  { value: 'target-rbb', title: 'Target RBB', icon: 'ri-focus-2-line', color: 'primary' },
  { value: 'security-access', title: 'Keamanan & Hak Akses', icon: 'ri-shield-keyhole-line', color: 'error' },
  { value: 'placeholder-1', title: 'Master Data', icon: 'ri-database-2-line', color: 'info', disabled: true },
]
</script>

<template>
  <div class="admin-management-page">
    <Head title="Control Center — Data Management" />

    <!-- Page Header -->
    <div class="d-flex flex-column flex-md-row justify-space-between align-start align-md-center gap-4 mb-6">
      <div class="d-flex align-center gap-4">
        <div class="header-icon-wrapper">
          <v-icon icon="ri-shield-user-line" size="26" style="color: #f59e0b;" />
        </div>
        <div>
          <h1 class="page-title">Control Center</h1>
          <p class="page-subtitle">Pusat unggahan data mentah untuk sinkronisasi sistem</p>
        </div>
      </div>
      <v-chip variant="flat" color="warning" size="small" prepend-icon="ri-admin-line" class="font-weight-bold text-none">
        ADMINISTRATOR
      </v-chip>
    </div>

    <!-- Tab Navigation -->
    <v-card elevation="0" border rounded="xl" class="mb-0">
      <v-tabs
        v-model="activeTab"
        color="primary"
        slider-color="primary"
        class="tab-navigation"
        show-arrows
      >
        <v-tab
          v-for="tab in tabs"
          :key="tab.value"
          :value="tab.value"
          :disabled="tab.disabled"
          class="text-none font-weight-bold"
          style="font-family: 'Plus Jakarta Sans', sans-serif; letter-spacing: 0;"
        >
          <v-icon :icon="tab.icon" start size="18" />
          {{ tab.title }}
          <v-chip
            v-if="tab.disabled"
            size="x-small"
            variant="tonal"
            color="grey"
            class="ms-2 font-weight-bold"
          >
            SEGERA
          </v-chip>
        </v-tab>
      </v-tabs>
      <v-divider />

      <!-- Tab Content -->
      <v-tabs-window v-model="activeTab">

        <!-- ========== TAB 1: TARGET RBB ========== -->
        <v-tabs-window-item value="target-rbb">
          <div class="pa-6">
            <v-row>
              <!-- Left: Upload Form -->
              <v-col cols="12" md="7">
                <v-card variant="outlined" rounded="xl" class="upload-card">
                  <v-card-text class="pa-6">
                    <!-- Section Title -->
                    <div class="d-flex align-center gap-3 mb-5">
                      <div class="section-icon">
                        <v-icon icon="ri-upload-cloud-2-line" size="20" color="primary" />
                      </div>
                      <div>
                        <div class="text-body-1 font-weight-bold" style="font-family: 'Plus Jakarta Sans', sans-serif;">
                          Unggah Target RBB
                        </div>
                        <div class="text-caption text-medium-emphasis">
                          Import data target tahunan dari file Excel
                        </div>
                      </div>
                    </div>

                    <!-- Step 1: Download Template -->
                    <v-alert
                      type="info"
                      variant="tonal"
                      rounded="lg"
                      class="mb-5"
                      density="compact"
                      icon="ri-information-line"
                    >
                      <div class="text-caption">
                        <strong>Langkah 1:</strong> Unduh template terlebih dahulu, isi data, lalu unggah kembali.
                      </div>
                    </v-alert>

                    <v-btn
                      variant="tonal"
                      color="primary"
                      rounded="lg"
                      prepend-icon="ri-download-cloud-line"
                      class="text-none font-weight-bold mb-6"
                      block
                      @click="downloadTemplate"
                    >
                      Download Template Excel
                    </v-btn>

                    <v-divider class="mb-5" />

                    <!-- Step 2: File Input -->
                    <div class="text-caption font-weight-bold text-uppercase text-medium-emphasis mb-3" style="letter-spacing: 0.5px;">
                      Langkah 2: Pilih File & Tahun
                    </div>

                    <v-file-input
                      v-model="uploadFile"
                      label="Pilih File Excel (.xlsx, .xls)"
                      variant="outlined"
                      density="compact"
                      accept=".xlsx,.xls"
                      prepend-icon=""
                      prepend-inner-icon="ri-file-excel-2-line"
                      rounded="lg"
                      hide-details
                      class="mb-4"
                      :disabled="uploadLoading"
                    />

                    <v-text-field
                      v-model="uploadYear"
                      label="Tahun Target"
                      type="number"
                      variant="outlined"
                      density="compact"
                      rounded="lg"
                      hide-details
                      class="mb-5"
                      :disabled="uploadLoading"
                    />

                    <!-- Upload Button -->
                    <v-btn
                      color="primary"
                      variant="flat"
                      rounded="lg"
                      prepend-icon="ri-upload-cloud-2-line"
                      class="text-none font-weight-bold"
                      block
                      size="large"
                      :loading="uploadLoading"
                      :disabled="!uploadFile"
                      @click="handleUpload"
                    >
                      Unggah Sekarang
                    </v-btn>
                  </v-card-text>
                </v-card>
              </v-col>

              <!-- Right: Activity Log -->
              <v-col cols="12" md="5">
                <v-card variant="outlined" rounded="xl" class="h-100">
                  <v-card-text class="pa-6">
                    <div class="d-flex align-center gap-3 mb-5">
                      <div class="section-icon" style="background: rgba(var(--v-theme-info), 0.12);">
                        <v-icon icon="ri-history-line" size="20" color="info" />
                      </div>
                      <div>
                        <div class="text-body-1 font-weight-bold" style="font-family: 'Plus Jakarta Sans', sans-serif;">
                          Riwayat Unggahan
                        </div>
                        <div class="text-caption text-medium-emphasis">
                          Aktivitas sesi ini
                        </div>
                      </div>
                    </div>

                    <!-- Empty State -->
                    <div v-if="uploadHistory.length === 0" class="text-center py-8">
                      <v-icon icon="ri-inbox-2-line" size="48" color="grey-lighten-1" class="mb-3" />
                      <div class="text-body-2 text-medium-emphasis">Belum ada unggahan</div>
                      <div class="text-caption text-disabled">Riwayat akan muncul setelah upload pertama</div>
                    </div>

                    <!-- History Items -->
                    <v-list v-else class="pa-0" density="compact">
                      <v-list-item
                        v-for="(entry, idx) in uploadHistory"
                        :key="idx"
                        rounded="lg"
                        class="mb-2 px-3"
                        :style="{ background: entry.status === 'success' ? 'rgba(16,185,129,0.06)' : 'rgba(239,68,68,0.06)' }"
                      >
                        <template #prepend>
                          <v-icon
                            :icon="entry.status === 'success' ? 'ri-checkbox-circle-fill' : 'ri-close-circle-fill'"
                            :color="entry.status === 'success' ? 'success' : 'error'"
                            size="20"
                          />
                        </template>
                        <v-list-item-title class="font-weight-bold" style="font-size: 12px;">
                          {{ entry.filename }}
                        </v-list-item-title>
                        <v-list-item-subtitle style="font-size: 10.5px;">
                          Tahun {{ entry.year }} · {{ entry.size }} · {{ entry.timestamp }}
                        </v-list-item-subtitle>
                        <v-list-item-subtitle v-if="entry.error" style="font-size: 10px; color: #ef4444;">
                          {{ entry.error }}
                        </v-list-item-subtitle>
                      </v-list-item>
                    </v-list>
                  </v-card-text>
                </v-card>
              </v-col>
            </v-row>
          </div>
        </v-tabs-window-item>

        <!-- ========== TAB 2: SECURITY & ACCESS ========== -->
        <v-tabs-window-item value="security-access">
          <div class="pa-6">
            <v-row>
              <v-col cols="12" md="8">
                <v-card variant="outlined" rounded="xl" class="border-slate-200">
                  <v-card-text class="pa-6">
                    <div class="d-flex align-center gap-3 mb-6">
                      <div class="section-icon" style="background: rgba(var(--v-theme-error), 0.1);">
                        <v-icon icon="ri-shield-keyhole-line" size="24" color="error" />
                      </div>
                      <div>
                        <div class="text-h6 font-weight-black text-slate-800" style="font-family: 'Plus Jakarta Sans', sans-serif;">
                          Konfigurasi Hak Akses Sistem
                        </div>
                        <div class="text-caption text-slate-500 font-weight-medium">
                          Kelola izin dan keamanan fitur krusial untuk mencegah penyalahgunaan.
                        </div>
                      </div>
                    </div>

                    <v-divider class="mb-6"></v-divider>

                    <div class="d-flex align-start gap-4 mb-6">
                      <v-switch
                        v-model="appSettings.ppka_manual_enabled"
                        color="success"
                        hide-details
                        inset
                      ></v-switch>
                      <div>
                        <div class="text-body-1 font-weight-bold text-slate-800">Izinkan Penyesuaian PPKA Manual</div>
                        <div class="text-caption text-slate-500 mb-3">
                          Jika diaktifkan, pengguna dengan role yang ditentukan dapat mengubah nilai pencadangan (PPKA/CKPN) secara manual untuk menimpa kalkulasi otomatis dari Core Banking.
                        </div>
                        <v-select
                          v-if="appSettings.ppka_manual_enabled"
                          v-model="appSettings.ppka_manual_roles"
                          :items="rolesOptions"
                          label="Pilih Role yang Diizinkan"
                          variant="outlined"
                          density="comfortable"
                          multiple
                          chips
                          closable-chips
                          rounded="lg"
                          bg-color="slate-50"
                          class="font-weight-bold"
                          hide-details
                        ></v-select>
                      </div>
                    </div>
                  </v-card-text>
                  <v-card-actions class="pa-6 bg-slate-50 border-t border-slate-100">
                    <v-spacer></v-spacer>
                    <v-btn
                      color="primary"
                      variant="flat"
                      rounded="lg"
                      class="px-6 font-weight-bold"
                      :loading="savingConfig"
                      @click="saveSettings"
                    >
                      Simpan Pengaturan
                    </v-btn>
                  </v-card-actions>
                </v-card>
              </v-col>
            </v-row>
          </div>
        </v-tabs-window-item>

        <!-- ========== TAB 3: MASTER DATA (Placeholder) ========== -->
        <v-tabs-window-item value="placeholder-1">
          <div class="pa-6 text-center py-16">
            <v-icon icon="ri-database-2-line" size="64" color="grey-lighten-1" class="mb-4" />
            <h3 class="text-h6 font-weight-bold mb-2" style="font-family: 'Plus Jakarta Sans', sans-serif;">Master Data</h3>
            <p class="text-body-2 text-medium-emphasis">Fitur pengelolaan master data akan segera hadir.</p>
          </div>
        </v-tabs-window-item>

        <!-- ========== TAB 3: KONFIGURASI (Placeholder) ========== -->
        <v-tabs-window-item value="placeholder-2">
          <div class="pa-6 text-center py-16">
            <v-icon icon="ri-settings-4-line" size="64" color="grey-lighten-1" class="mb-4" />
            <h3 class="text-h6 font-weight-bold mb-2" style="font-family: 'Plus Jakarta Sans', sans-serif;">Konfigurasi Sistem</h3>
            <p class="text-body-2 text-medium-emphasis">Fitur konfigurasi sistem akan segera hadir.</p>
          </div>
        </v-tabs-window-item>

      </v-tabs-window>
    </v-card>

    <!-- Notifications -->
    <v-snackbar v-model="snackbar.show" :color="snackbar.color" rounded="pill" elevation="12" :timeout="4000">
      <div class="d-flex align-center gap-2">
        <v-icon :icon="snackbar.color === 'success' ? 'ri-checkbox-circle-line' : 'ri-error-warning-line'" />
        <span class="font-weight-bold">{{ snackbar.text }}</span>
      </div>
    </v-snackbar>
  </div>
</template>

<style scoped>
.admin-management-page {
  max-width: 1200px;
  margin: 0 auto;
}

.page-title {
  font-family: 'Plus Jakarta Sans', sans-serif;
  font-size: 1.65rem;
  font-weight: 800;
  letter-spacing: -0.3px;
  margin: 0;
}

.page-subtitle {
  font-size: 0.8rem;
  opacity: 0.6;
  margin: 2px 0 0;
}

.header-icon-wrapper {
  width: 52px;
  height: 52px;
  border-radius: 14px;
  background: linear-gradient(135deg, rgba(245, 158, 11, 0.12), rgba(245, 158, 11, 0.04));
  border: 1px solid rgba(245, 158, 11, 0.2);
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
}

.section-icon {
  width: 40px;
  height: 40px;
  border-radius: 10px;
  background: rgba(var(--v-theme-primary), 0.1);
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
}

.tab-navigation :deep(.v-tab) {
  min-width: 140px;
  font-size: 13px;
}

.upload-card {
  border-style: dashed !important;
  border-width: 1.5px !important;
  transition: border-color 0.2s ease;
}

.upload-card:hover {
  border-color: rgb(var(--v-theme-primary)) !important;
}
</style>
