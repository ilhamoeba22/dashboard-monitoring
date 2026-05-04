<script setup>
/**
 * SummaryCards - Premium Dashboard Metric Cards
 * 
 * Desain: Materio Vuetify Style
 * - Emerald Green (#059669) untuk primary metrics
 * - Gold (#D97706) untuk accent/positive trends
 * - Clean typography dengan Plus Jakarta Sans headings
 * - Subtle elevation dan rounded corners
 */

import { computed } from 'vue'

const props = defineProps({
  data: {
    type: Object,
    required: true
  }
})

// Theme Colors (Emerald Green + Gold)
const themeColors = {
  primary: '#059669',     // Emerald Green
  secondary: '#D97706',   // Gold
  success: '#10B981',
  error: '#EF4444',
  warning: '#F59E0B'
}

// Cards configuration
const cards = computed(() => [
  {
    id: 'noa',
    title: 'Total Rekening',
    value: props.data?.summary?.total_noa ?? 0,
    format: 'number',
    subtitle: 'Akad pembiayaan aktif',
    icon: 'ri-file-list-3-line',
    color: 'primary',
    trend: null
  },
  {
    id: 'os',
    title: 'Total Outstanding',
    value: props.data?.summary?.total_os ?? 0,
    format: 'currency',
    subtitle: 'Portofolio pembiayaan',
    icon: 'ri-money-dollar-circle-line',
    color: 'success',
    trend: null
  },
  {
    id: 'npf',
    title: 'NPF Ratio',
    value: props.data?.summary?.npf_persen ?? 0,
    format: 'percent',
    subtitle: `${props.data?.summary?.npf_noa ?? 0} rekening bermasalah`,
    icon: 'ri-alert-line',
    color: 'error',
    isNegative: true // Lower is better
  },
  {
    id: 'kolek',
    title: 'Avg Kolektibilitas',
    value: props.data?.summary?.avg_kolek ?? 0,
    format: 'decimal',
    subtitle: 'Kolektibilitas rata-rata',
    icon: 'ri-bar-chart-box-line',
    color: 'warning',
    isNegative: true
  }
])

// Format value
function formatValue(value, format) {
  if (value === null || value === undefined) return '—'
  
  switch (format) {
    case 'number':
      return parseInt(value).toLocaleString('id-ID')
    case 'currency':
      const num = parseFloat(value)
      if (Math.abs(num) >= 1e9) return `${(num / 1e9).toFixed(2)} M`
      if (Math.abs(num) >= 1e6) return `${(num / 1e6).toFixed(1)} Jt`
      return num.toLocaleString('id-ID')
    case 'percent':
      return `${parseFloat(value).toFixed(2)}%`
    case 'decimal':
      return parseFloat(value).toFixed(2)
    default:
      return value
  }
}

// Get currency prefix
function getCurrencyPrefix(format) {
  return format === 'currency' ? 'Rp ' : ''
}

// Get color class based on card config
function getCardColor(card) {
  const colorMap = {
    primary: 'primary',
    success: 'success',
    error: 'error',
    warning: 'warning'
  }
  return colorMap[card.color] || 'primary'
}

// Get icon size
const iconSize = '22px'
</script>

<template>
  <v-row class="mb-2">
    <v-col
      v-for="card in cards"
      :key="card.id"
      cols="12"
      sm="6"
      lg="3"
    >
      <v-card
        elevation="0"
        :style="{
          border: '1px solid rgba(var(--v-border-color), 0.08)',
          borderRadius: '12px',
          overflow: 'hidden',
          position: 'relative'
        }"
        class="h-100 transition-swing"
      >
        <!-- Background accent stripe -->
        <div
          :style="{
            position: 'absolute',
            top: 0,
            left: 0,
            right: 0,
            height: '4px',
            background: `linear-gradient(90deg, rgb(var(--v-theme-${card.color})) 0%, transparent 100%)`
          }"
        />
        
        <v-card-text class="pa-5">
          <div class="d-flex align-start justify-space-between">
            <!-- Content -->
            <div class="flex-grow-1 pe-4">
              <!-- Label -->
              <p 
                class="text-body-2 text-medium-emphasis mb-2 font-weight-medium"
                style="font-size: 12px; letter-spacing: 0.5px; text-transform: uppercase;"
              >
                {{ card.title }}
              </p>
              
              <!-- Value -->
              <h2 
                class="text-h4 font-weight-bold mb-2"
                :style="{ 
                  color: `rgb(var(--v-theme-${card.color}))`,
                  fontFamily: 'Plus Jakarta Sans, sans-serif',
                  lineHeight: 1.2
                }"
              >
                {{ getCurrencyPrefix(card.format) }}{{ formatValue(card.value, card.format) }}
              </h2>
              
              <!-- Subtitle -->
              <p class="text-caption text-medium-emphasis mb-0">
                {{ card.subtitle }}
              </p>
            </div>
            
            <!-- Icon Container -->
            <div
              :style="{
                width: '52px',
                height: '52px',
                borderRadius: '12px',
                background: `rgba(var(--v-theme-${card.color}), 0.12)`,
                display: 'flex',
                alignItems: 'center',
                justifyContent: 'center'
              }"
            >
              <v-icon
                :icon="card.icon"
                :size="iconSize"
                :color="card.color"
              />
            </div>
          </div>
        </v-card-text>
      </v-card>
    </v-col>
  </v-row>
</template>

<style scoped>
.transition-swing {
  transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

.transition-swing:hover {
  transform: translateY(-2px);
  box-shadow: 0 8px 24px rgba(5, 150, 105, 0.12) !important;
}

:deep(.v-card-text) {
  padding: 20px;
}
</style>
