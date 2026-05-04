<script setup>
import { ref, onMounted } from 'vue'
import DefaultLayout from '@/layouts/default.vue'

defineOptions({ layout: DefaultLayout })

const activeReport = ref('neraca')
const loading = ref(false)
const reportData = ref(null)
const error = ref(null)

const reportTypes = [
  { value: 'neraca', title: 'Neraca', subtitle: 'Laporan posisi keuangan', icon: 'ri-file-list-3-line', color: 'primary' },
  { value: 'laba-rugi', title: 'Laba Rugi', subtitle: 'Income statement', icon: 'ri-line-chart-line', color: 'success' },
  { value: 'car', title: 'CAR', subtitle: 'Capital Adequacy Ratio', icon: 'ri-shield-star-line', color: 'info' },
  { value: 'bopo', title: 'BOPO', subtitle: 'Efisiensi operasional', icon: 'ri-pie-chart-2-line', color: 'warning' },
]

async function fetchReport(jenis) {
  loading.value = true
  reportData.value = null
  error.value = null
  try {
    const r = await fetch(`/api/v1/reporting/${jenis}`)
    const j = await r.json()
    reportData.value = j.data ?? j
  } catch (e) {
    error.value = e.message
  } finally {
    loading.value = false
  }
}

function selectReport(type) {
  activeReport.value = type
  fetchReport(type)
}

onMounted(() => fetchReport(activeReport.value))
</script>

<template>
  <div class="d-flex align-center justify-space-between mb-6">
    <div>
      <div class="d-flex align-center gap-2 mb-1">
        <VAvatar color="secondary" variant="tonal" size="32" rounded="lg">
          <VIcon icon="ri-file-chart-2-line" size="18" />
        </VAvatar>
        <h1 class="text-h5 font-weight-bold mb-0" style="font-family: 'Plus Jakarta Sans', sans-serif;">
          Laporan Keuangan
        </h1>
      </div>
      <p class="text-body-2 text-medium-emphasis mb-0 ms-10">
        Laporan posisi keuangan, laba rugi, dan rasio regulasi
      </p>
    </div>
    <VChip color="secondary" variant="tonal" size="small" prepend-icon="ri-file-chart-2-line">
      Reporting Module
    </VChip>
  </div>

  <VAlert v-if="error" type="warning" variant="tonal" closable class="mb-4" :text="`Koneksi API: ${error}`" />

  <VRow>
    <!-- Report Selector -->
    <VCol cols="12" md="3">
      <VCard elevation="0" border rounded="xl">
        <VCardItem class="pb-2">
          <VCardTitle class="text-subtitle-2 font-weight-bold">Jenis Laporan</VCardTitle>
        </VCardItem>
        <VList density="compact" nav class="pa-2">
          <VListItem
            v-for="rt in reportTypes"
            :key="rt.value"
            :active="activeReport === rt.value"
            :color="rt.color"
            rounded="xl"
            class="mb-1"
            @click="selectReport(rt.value)"
          >
            <template #prepend>
              <VAvatar :color="rt.color" variant="tonal" size="34" rounded="md" class="me-3">
                <VIcon :icon="rt.icon" size="16" />
              </VAvatar>
            </template>
            <VListItemTitle class="text-body-2 font-weight-semibold">{{ rt.title }}</VListItemTitle>
            <VListItemSubtitle class="text-caption" style="font-size: 10px;">{{ rt.subtitle }}</VListItemSubtitle>
          </VListItem>
        </VList>
      </VCard>
    </VCol>

    <!-- Report Content -->
    <VCol cols="12" md="9">
      <VCard elevation="0" border rounded="xl" min-height="400">
        <VCardItem class="pb-2">
          <template #prepend>
            <VAvatar :color="reportTypes.find(r => r.value === activeReport)?.color" variant="tonal" size="36" rounded="lg">
              <VIcon :icon="reportTypes.find(r => r.value === activeReport)?.icon" size="18" />
            </VAvatar>
          </template>
          <VCardTitle class="text-subtitle-2 font-weight-bold" style="font-family: 'Plus Jakarta Sans', sans-serif;">
            {{ reportTypes.find(r => r.value === activeReport)?.title }}
          </VCardTitle>
          <VCardSubtitle class="text-caption">
            {{ reportTypes.find(r => r.value === activeReport)?.subtitle }} — BPRS HIK MCI
          </VCardSubtitle>
          <template #append>
            <VBtn
              variant="tonal"
              color="primary"
              size="small"
              prepend-icon="ri-refresh-line"
              :loading="loading"
              @click="fetchReport(activeReport)"
            >
              Refresh
            </VBtn>
          </template>
        </VCardItem>

        <VDivider />

        <VCardText>
          <!-- Loading skeleton -->
          <div v-if="loading" class="pa-4">
            <VSkeleton v-for="i in 8" :key="i" type="text" class="mb-3" :width="`${70 + Math.random() * 30}%`" />
          </div>

          <!-- Data Result -->
          <div v-else-if="reportData">
            <!-- Jika data berupa array of objects -->
            <VTable v-if="Array.isArray(reportData)" density="comfortable" class="rounded-lg">
              <thead>
                <tr>
                  <th v-for="key in Object.keys(reportData[0] ?? {})" :key="key" class="text-uppercase font-weight-bold text-caption">
                    {{ key.replace(/_/g, ' ') }}
                  </th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="(row, i) in reportData" :key="i">
                  <td v-for="key in Object.keys(row)" :key="key" class="text-body-2">
                    {{ row[key] }}
                  </td>
                </tr>
              </tbody>
            </VTable>

            <!-- Jika data berupa object -->
            <VTable v-else density="comfortable" class="rounded-lg">
              <tbody>
                <tr
                  v-for="(value, key) in reportData"
                  :key="key"
                  class="hover:bg-surface-variant"
                >
                  <td class="text-body-2 font-weight-medium py-3" style="min-width: 200px; border-right: 1px solid rgba(var(--v-border-color), 0.1);">
                    {{ String(key).replace(/_/g, ' ').replace(/\b\w/g, l => l.toUpperCase()) }}
                  </td>
                  <td class="text-body-2 financial-number py-3 text-end">
                    {{ value }}
                  </td>
                </tr>
              </tbody>
            </VTable>
          </div>

          <!-- Empty State -->
          <div v-else class="d-flex flex-column align-center justify-center py-12 text-medium-emphasis">
            <VIcon icon="ri-file-chart-2-line" size="64" class="mb-4" style="opacity: 0.2;" />
            <p class="text-body-1 font-weight-medium mb-1">Data laporan tidak tersedia</p>
            <p class="text-body-2">Koneksi SQL Server diperlukan untuk menampilkan laporan keuangan</p>
          </div>
        </VCardText>
      </VCard>
    </VCol>
  </VRow>
</template>
