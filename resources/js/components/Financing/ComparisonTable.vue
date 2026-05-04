<script setup>
/**
 * ComparisonTable Component
 * Tabel perbandingan Realtime vs Historical dengan direction indicators
 * Theme: Emerald Green (#059669) + Gold (#D97706)
 */

import { computed } from 'vue'

// Props
const props = defineProps({
  realtime: {
    type: Object,
    default: null
  },
  historical: {
    type: Object,
    default: null
  },
  comparison: {
    type: Object,
    default: null
  },
  loading: {
    type: Boolean,
    default: false
  }
})

// Theme Colors
const themeColors = {
  primary: '#059669',
  secondary: '#D97706',
  success: '#10B981',
  error: '#EF4444',
  warning: '#F59E0B'
}

// Comparison metrics
const metrics = computed(() => {
  if (!props.comparison) return []
  
  return [
    {
      key: 'noa',
      label: 'Jumlah Rekening',
      icon: 'ri-file-list-3-line',
      realtime: props.realtime?.total_noa ?? 0,
      historical: props.historical?.total_noa ?? 0,
      format: 'number',
      direction: props.comparison.noa?.direction ?? null,
      change: props.comparison.noa?.change ?? 0
    },
    {
      key: 'os',
      label: 'Total Outstanding',
      icon: 'ri-money-dollar-circle-line',
      realtime: props.realtime?.total_os ?? 0,
      historical: props.historical?.total_os ?? 0,
      format: 'currency',
      direction: props.comparison.os?.direction ?? null,
      change: props.comparison.os?.change ?? 0,
      isPositiveGood: true
    },
    {
      key: 'npf',
      label: 'NPF Ratio',
      icon: 'ri-alert-line',
      realtime: props.realtime?.npf_persen ?? 0,
      historical: props.historical?.npf_persen ?? 0,
      format: 'percent',
      direction: props.comparison.npf?.direction ?? null,
      change: props.comparison.npf?.change ?? 0,
      isPositiveGood: false // Lower is better
    },
    {
      key: 'avg_kolek',
      label: 'Avg Kolektibilitas',
      icon: 'ri-bar-chart-box-line',
      realtime: props.realtime?.avg_kolek ?? 0,
      historical: props.historical?.avg_kolek ?? 0,
      format: 'decimal',
      direction: props.comparison.avg_kolek?.direction ?? null,
      change: props.comparison.avg_kolek?.change ?? 0,
      isPositiveGood: false // Lower is better
    }
  ]
})

// Format value helper
function formatValue(value, format) {
  if (value === null || value === undefined) return '—'
  
  switch (format) {
    case 'number':
      return parseInt(value).toLocaleString('id-ID')
    case 'currency':
      const num = parseFloat(value)
      if (Math.abs(num) >= 1e9) return `Rp ${(num / 1e9).toFixed(2)} M`
      if (Math.abs(num) >= 1e6) return `Rp ${(num / 1e6).toFixed(1)} Jt`
      return `Rp ${num.toLocaleString('id-ID')}`
    case 'percent':
      return `${parseFloat(value).toFixed(2)}%`
    case 'decimal':
      return parseFloat(value).toFixed(2)
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
  
  if (format === 'percent') {
    return `${sign}${change.toFixed(2)}%`
  }
  if (format === 'currency') {
    const num = Math.abs(change)
    if (num >= 1e9) return `${sign}${change < 0 ? '-' : ''}Rp ${(num / 1e9).toFixed(2)} M`
    if (num >= 1e6) return `${sign}${change < 0 ? '-' : ''}Rp ${(num / 1e6).toFixed(1)} Jt`
    return `${sign}${change.toLocaleString('id-ID')}`
  }
  return `${sign}${parseInt(Math.abs(change)).toLocaleString('id-ID')}`
}

// Check if data is available
const hasData = computed(() => {
  return props.realtime && props.historical && props.comparison
})
</script>

<template>
  <v-card elevation="2" class="h-full">
    <v-card-title class="d-flex align-center justify-space-between pa-4 pb-2">
      <div class="d-flex align-center">
        <v-avatar color="secondary" size="36" variant="tonal" class="mr-3">
          <v-icon icon="ri-git-compare-line" size="20" />
        </v-avatar>
        <div>
          <h3 class="text-h6 font-weight-bold mb-0" style="font-family: 'Plus Jakarta Sans', sans-serif;">
            Perbandingan Periode
          </h3>
          <p class="text-caption text-medium-emphasis mb-0">
            Realtime vs Historical
          </p>
        </div>
      </div>
    </v-card-title>
    
    <v-card-text class="pa-4 pt-2">
      <!-- Loading Skeleton -->
      <div v-if="loading" class="d-flex justify-center align-center py-8">
        <v-progress-circular indeterminate color="primary" size="48" />
      </div>
      
      <!-- No Data Message -->
      <v-alert
        v-else-if="!hasData"
        type="info"
        variant="tonal"
        density="compact"
      >
        <div class="text-body-2">
          Tidak ada data comparison tersedia.
        </div>
        <template #append>
          <code class="text-caption">php artisan financing:aggregate-snapshot</code>
        </template>
      </v-alert>
      
      <!-- Comparison Cards Grid -->
      <v-row v-else dense>
        <v-col
          v-for="metric in metrics"
          :key="metric.key"
          cols="12"
          sm="6"
          md="3"
        >
          <v-card
            elevation="0"
            border
            class="pa-4 h-100"
            :style="{
              borderColor: 'var(--border-color, #E5E7EB)',
              borderRadius: '12px'
            }"
          >
            <!-- Header -->
            <div class="d-flex align-center mb-3">
              <v-avatar
                :color="metric.key === 'noa' ? 'primary' : metric.key === 'os' ? 'success' : metric.key === 'npf' ? 'error' : 'warning'"
                size="32"
                variant="tonal"
              >
                <v-icon :icon="metric.icon" size="16" />
              </v-avatar>
              <span class="text-body-2 text-medium-emphasis ml-2">
                {{ metric.label }}
              </span>
            </div>
            
            <!-- Values -->
            <div class="d-flex align-center justify-space-between mb-2">
              <!-- Realtime -->
              <div class="text-center flex-grow-1">
                <div class="text-caption text-disabled mb-1">Realtime</div>
                <div
                  class="text-h6 font-weight-bold"
                  :style="{
                    color: metric.key === 'noa' ? themeColors.primary :
                           metric.key === 'os' ? themeColors.success :
                           metric.key === 'npf' ? themeColors.error : themeColors.warning
                  }"
                >
                  {{ formatValue(metric.realtime, metric.format) }}
                </div>
              </div>
              
              <!-- Direction Arrow -->
              <div class="d-flex flex-column align-center mx-2">
                <v-icon
                  :icon="getDirectionIcon(metric.direction)"
                  :color="getDirectionColor(metric.direction, metric.isPositiveGood)"
                  size="20"
                />
                <span
                  class="text-caption mt-1"
                  :style="{ color: getDirectionColor(metric.direction, metric.isPositiveGood) }"
                >
                  {{ metric.direction || 'N/A' }}
                </span>
              </div>
              
              <!-- Historical -->
              <div class="text-center flex-grow-1">
                <div class="text-caption text-disabled mb-1">Historical</div>
                <div class="text-h6 text-medium-emphasis">
                  {{ formatValue(metric.historical, metric.format) }}
                </div>
              </div>
            </div>
            
            <!-- Change Indicator -->
            <v-chip
              :color="getDirectionColor(metric.direction, metric.isPositiveGood)"
              variant="tonal"
              size="small"
              class="mt-2 w-100 justify-center"
            >
              <v-icon :icon="getDirectionIcon(metric.direction)" start size="14" />
              {{ getChangeText(metric.change, metric.format) }}
            </v-chip>
          </v-card>
        </v-col>
      </v-row>
      
      <!-- Legend -->
      <div v-if="hasData" class="d-flex justify-center gap-4 mt-4">
        <div class="d-flex align-center gap-2">
          <v-icon icon="ri-arrow-up-line" color="success" size="16" />
          <span class="text-caption text-medium-emphasis">Increase</span>
        </div>
        <div class="d-flex align-center gap-2">
          <v-icon icon="ri-arrow-down-line" color="error" size="16" />
          <span class="text-caption text-medium-emphasis">Decrease</span>
        </div>
        <div class="d-flex align-center gap-2">
          <v-icon icon="ri-subtract-line" color="secondary" size="16" />
          <span class="text-caption text-medium-emphasis">Stable</span>
        </div>
      </div>
    </v-card-text>
  </v-card>
</template>
