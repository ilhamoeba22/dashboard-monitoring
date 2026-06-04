<script setup>
/**
 * ComparisonTable Component - STRICT BANKING VERSION
 * Displays raw accurate data for both periods.
 */

import { computed } from 'vue'
import { formatExactRupiah, formatExactNumber } from '@/utils/money'

// Props
const props = defineProps({
  realtime: {
    type: Object,
    default: () => ({})
  },
  historical: {
    type: Object,
    default: () => ({})
  },
  comparison: {
    type: Object,
    default: () => ({})
  }
})

// Metrics Configuration
const metrics = computed(() => {
  return [
    {
      label: 'Total Outstanding',
      icon: 'ri-money-dollar-circle-line',
      realtime: props.realtime?.total_os ?? 0,
      historical: props.historical?.total_os ?? 0,
      format: 'currency',
      direction: props.comparison.total_os?.direction ?? null,
      change: props.comparison.total_os?.change ?? 0,
      isPositiveGood: true
    },
    {
      label: 'Jumlah Nasabah (NOA)',
      icon: 'ri-group-line',
      realtime: props.realtime?.total_noa ?? 0,
      historical: props.historical?.total_noa ?? 0,
      format: 'number',
      direction: props.comparison.total_noa?.direction ?? null,
      change: props.comparison.total_noa?.change ?? 0,
      isPositiveGood: true
    },
    {
      label: 'Nominal NPF',
      icon: 'ri-error-warning-line',
      realtime: props.realtime?.total_npf ?? 0,
      historical: props.historical?.total_npf ?? 0,
      format: 'currency',
      direction: props.comparison.total_npf?.direction ?? null,
      change: props.comparison.total_npf?.change ?? 0,
      isPositiveGood: false 
    },
    {
      label: 'Rasio NPF (%)',
      icon: 'ri-percent-line',
      realtime: props.realtime?.npf_persen ?? 0,
      historical: props.historical?.npf_persen ?? 0,
      format: 'percent',
      direction: props.comparison.npf_ratio?.direction ?? null,
      change: props.comparison.npf_ratio?.change ?? 0,
      isPositiveGood: false 
    }
  ]
})

// Format value helper
function formatValue(value, format) {
  if (value === null || value === undefined) return '—'
  
  switch (format) {
    case 'number':
      return formatExactNumber(value, '—')
    case 'currency':
      return formatExactRupiah(value, '—')
    case 'percent':
      return `${value}%`
    case 'decimal':
      return `${value}`
    default:
      return value
  }
}

// Get direction icon
function getDirectionIcon(direction) {
  switch (direction) {
    case 'up': return 'ri-arrow-up-line'
    case 'down': return 'ri-arrow-down-line'
    case 'stable': return 'ri-subtract-line'
    default: return 'ri-dash-line'
  }
}

// Get direction color
function getDirectionColor(direction, isPositiveGood = true) {
  if (direction === 'stable' || !direction) return 'secondary'
  
  const isGood = direction === (isPositiveGood ? 'up' : 'down')
  return isGood ? 'success' : 'error'
}

// Get change percentage
function getChangeText(change, format) {
  if (!change && change !== 0) return '—'
  const sign = change > 0 ? '+' : ''
  
  if (format === 'percent' || format === 'decimal') {
    return `${sign}${change}%`
  }
  return `${sign}${formatValue(change, format)}`
}

// Check if data is available
const hasData = computed(() => {
  return props.realtime && props.historical && props.comparison
})
</script>

<template>
  <div class="comparison-table">
    <div v-if="!hasData" class="d-flex flex-column align-center justify-center py-12 bg-slate-50 rounded-xl border border-dashed">
      <v-icon icon="ri-file-search-line" size="48" color="slate-200" />
      <p class="text-slate-400 mt-2 font-weight-medium">Data pembanding belum dimuat</p>
    </div>

    <v-table v-else class="fin-table border rounded-xl overflow-hidden shadow-sm" hover>
      <thead>
        <tr class="bg-slate-50">
          <th class="text-left font-weight-black text-slate-700 py-4" style="width: 30%">MATRIK UTAMA</th>
          <th class="text-right font-weight-black text-slate-700">REALTIME</th>
          <th class="text-right font-weight-black text-slate-500">HISTORICAL</th>
          <th class="text-center font-weight-black text-slate-700" style="width: 20%">PERUBAHAN</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="m in metrics" :key="m.label" class="transition-swing">
          <td class="py-4">
            <div class="d-flex align-center gap-3">
              <div class="metrics-icon-box">
                <v-icon :icon="m.icon" size="20" color="primary" />
              </div>
              <span class="text-subtitle-2 font-weight-bold text-slate-700">{{ m.label }}</span>
            </div>
          </td>
          <td class="text-right">
            <div class="text-body-2 font-weight-black text-slate-900">
              {{ formatValue(m.realtime, m.format) }}
            </div>
          </td>
          <td class="text-right">
            <div class="text-body-2 font-weight-medium text-slate-500">
              {{ formatValue(m.historical, m.format) }}
            </div>
          </td>
          <td class="text-center">
            <v-chip
              size="small"
              :color="getDirectionColor(m.direction, m.isPositiveGood)"
              variant="tonal"
              class="font-weight-black"
              rounded="lg"
            >
              <template #prepend>
                <v-icon :icon="getDirectionIcon(m.direction)" size="14" class="me-1" />
              </template>
              {{ getChangeText(m.change, m.format) }}
            </v-chip>
          </td>
        </tr>
      </tbody>
    </v-table>
  </div>
</template>

<style scoped>
.fin-table :deep(table) {
  border-collapse: separate !important;
  border-spacing: 0 !important;
}

.fin-table :deep(thead th) {
  border-bottom: 2px solid #e2e8f0 !important;
  font-size: 11px !important;
  letter-spacing: 0.05em !important;
  text-transform: uppercase !important;
}

.fin-table :deep(tbody td) {
  border-bottom: 1px solid #f1f5f9 !important;
  transition: all 0.2s ease;
}

.metrics-icon-box {
  width: 36px;
  height: 36px;
  background: #eff6ff;
  border-radius: 10px;
  display: flex;
  align-items: center;
  justify-content: center;
}

.transition-swing:hover td {
  background-color: #f8fafc !important;
}
</style>
