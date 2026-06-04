<script setup>
/**
 * SummaryCards Component - STRICT BANKING VERSION
 * Displays main metrics without any rounding or abbreviations in the final value.
 * Layout uses responsive typography to handle long unrounded strings.
 */

import { computed } from 'vue'
import { formatExactRupiah, formatExactNumber, formatTruncatedPercentage } from '@/utils/money'

const props = defineProps({
  data: {
    type: Object,
    required: true,
    default: () => ({ summary: {} })
  }
})

// Configuration for cards
const cards = computed(() => [
  {
    id: 'os',
    title: 'Total Outstanding',
    value: props.data?.summary?.total_os ?? 0,
    format: 'currency',
    subtitle: 'Portofolio pembiayaan',
    icon: 'ri-money-dollar-circle-line',
    color: 'success'
  },
  {
    id: 'noa',
    title: 'Jumlah Nasabah (NOA)',
    value: props.data?.summary?.total_noa ?? 0,
    format: 'number',
    subtitle: 'Rekening aktif berjalan',
    icon: 'ri-group-line',
    color: 'primary'
  },
  {
    id: 'npf',
    title: 'Outstanding NPF',
    value: props.data?.summary?.total_npf ?? 0,
    format: 'currency',
    subtitle: `${props.data?.summary?.npf_noa ?? 0} rekening macet`,
    icon: 'ri-error-warning-line',
    color: 'error'
  },
  {
    id: 'npf_ratio',
    title: 'Rasio NPF',
    value: props.data?.summary?.npf_persen ?? 0,
    format: 'percent',
    subtitle: 'Kualitas aset makro',
    icon: 'ri-percent-line',
    color: 'warning'
  }
])

// Format value
function formatValue(value, format) {
  if (value === null || value === undefined) return '—'
  
  switch (format) {
    case 'number':
      return formatExactNumber(value, '—')
    case 'currency':
      return formatExactRupiah(value, '—')
    case 'percent':
      return formatTruncatedPercentage(value)
    case 'decimal':
      return `${value}`
    default:
      return value
  }
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
</script>

<template>
  <v-row class="summary-cards">
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
        class="content-card h-100"
        :class="`content-card--${getCardColor(card)}`"
      >
        <div class="content-card__accent-top" :class="`bg-${card.color}`"></div>
        
        <div class="content-card__header pb-2">
          <div>
            <div class="content-card__title">{{ card.title }}</div>
            <div class="content-card__subtitle">{{ card.subtitle }}</div>
          </div>
          <div class="content-card__icon" :class="`fin-icon-${card.color === 'success' ? 'green' : card.color === 'error' ? 'red' : card.color === 'warning' ? 'amber' : 'blue'}`">
            <v-icon :icon="card.icon" size="24" />
          </div>
        </div>

        <div class="content-card__body pt-0">
          <div class="d-flex align-end">
            <!-- font-size dynamic based on string length to avoid overflow -->
            <div 
              class="font-weight-black text-slate-800 tracking-tight"
              :class="formatValue(card.value, card.format).length > 15 ? 'text-h5' : 'text-h4'"
            >
              {{ formatValue(card.value, card.format) }}
            </div>
          </div>
        </div>
      </v-card>
    </v-col>
  </v-row>
</template>

<style scoped>
.summary-cards {
  margin-top: -12px;
}

.content-card {
    transition: all 0.3s ease;
}
.content-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 12px 24px rgba(0,0,0,0.08) !important;
}

.bg-primary { background: #3b82f6 !important; }
.bg-success { background: #10b981 !important; }
.bg-error   { background: #ef4444 !important; }
.bg-warning { background: #f59e0b !important; }

.tracking-tight {
  letter-spacing: -0.025em !important;
  word-break: break-all;
}
</style>
