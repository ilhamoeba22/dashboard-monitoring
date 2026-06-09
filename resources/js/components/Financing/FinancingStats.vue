<script setup>
import { computed } from 'vue'
import { formatExactRupiah, formatExactNumber, formatTruncatedPercentage } from '@/utils/money'

const props = defineProps({
  data: {
    type: Object,
    required: true
  }
})

// Metrics Configuration matching MDB Logic
// Total O/S, Total NPF (Kol 3,4,5), Rasio NPF (%), Total Pencadangan (PPKA)
const cards = computed(() => [
  {
    id: 'total_os',
    title: 'Total Outstanding',
    value: props.data?.summary?.total_os ?? 0,
    format: 'currency',
    subtitle: 'Portofolio modal berjalan',
    icon: 'ri-money-dollar-box-line',
    color: 'primary',
  },
  {
    id: 'total_npf',
    title: 'Total NPF (Kol 3-5)',
    value: props.data?.summary?.npf_os ?? 0,
    format: 'currency',
    subtitle: `${props.data?.summary?.npf_noa ?? 0} rekening bermasalah`,
    icon: 'ri-error-warning-line',
    color: 'error',
  },
  {
    id: 'npf_ratio',
    title: 'Rasio NPF (%)',
    value: props.data?.summary?.npf_persen ?? 0,
    format: 'percent',
    subtitle: 'Kualitas aset pembiayaan',
    icon: 'ri-pulse-line',
    color: 'warning',
  },
  {
    id: 'total_ppap',
    title: 'Total Pencadangan (PPKA)',
    value: props.data?.summary?.total_ppap ?? 0,
    format: 'currency',
    subtitle: 'Cadangan kerugian aset',
    icon: 'ri-shield-check-line',
    color: 'success',
  }
])

function formatValue(value, format) {
  if (value === null || value === undefined) return '0'
  
  switch (format) {
    case 'currency':
      return formatExactRupiah(value, '0')
    case 'percent':
      return formatTruncatedPercentage(value)
    default:
      return formatExactNumber(value, '0')
  }
}

function getCurrencyPrefix(format) {
  return ''
}
</script>

<template>
  <v-row class="mb-4">
    <v-col
      v-for="card in cards"
      :key="card.id"
      cols="12"
      sm="6"
      lg="3"
    >
      <v-card
        elevation="0"
        border
        rounded="xl"
        class="metric-card transition-swing"
      >
        <div
          class="card-accent"
          :style="{ background: `linear-gradient(90deg, rgb(var(--v-theme-${card.color})) 0%, transparent 100%)` }"
        />
        
        <v-card-text class="pa-5">
          <div class="d-flex align-start justify-space-between">
            <div class="flex-grow-1">
              <p class="text-caption text-uppercase font-weight-bold letter-spacing-1 text-slate-500 mb-1">
                {{ card.title }}
              </p>
              
              <h2 
                class="text-h4 font-weight-black mb-2"
                :class="`text-${card.color}`"
                style="font-family: 'Plus Jakarta Sans', sans-serif;"
              >
                {{ getCurrencyPrefix(card.format) }}{{ formatValue(card.value, card.format) }}
              </h2>
              
              <div class="d-flex align-center">
                <span class="text-caption text-medium-emphasis">{{ card.subtitle }}</span>
              </div>
            </div>
            
            <div
              class="icon-box"
              :style="{ backgroundColor: `rgba(var(--v-theme-${card.color}), 0.12)` }"
            >
              <v-icon :icon="card.icon" :color="card.color" size="24" />
            </div>
          </div>
        </v-card-text>
      </v-card>
    </v-col>
  </v-row>
</template>

<style scoped>
.metric-card {
  position: relative;
  overflow: hidden;
  background: white;
  transition: all 0.3s ease;
}

.metric-card:hover {
  transform: translateY(-4px);
  box-shadow: 0 12px 24px -8px rgba(0,0,0,0.1) !important;
}

.card-accent {
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  height: 4px;
}

.icon-box {
  width: 52px;
  height: 52px;
  border-radius: 14px;
  display: flex;
  align-items: center;
  justify-content: center;
}

.letter-spacing-1 {
  letter-spacing: 0.05em;
}
</style>
