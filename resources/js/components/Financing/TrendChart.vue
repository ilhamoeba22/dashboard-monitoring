<script setup>
/**
 * TrendChart - Analytical Growth & Risk (Enterprise Placeholder)
 * Resolves Vite dependency issues by using native Vuetify components.
 */

import { computed } from 'vue'

const props = defineProps({
  series: { type: Array, default: () => [] },
  loading: { type: Boolean, default: false }
})

// Simulating chart bars for placeholder
const bars = [40, 65, 55, 80, 75, 90, 70, 85, 95, 100, 88, 92]
</script>

<template>
  <v-card elevation="0" border rounded="xl" class="h-100 analytical-card">
    <v-card-item>
      <template #title>
        <span class="text-h6 font-weight-bold text-slate-800">Tren Pertumbuhan & Risiko</span>
      </template>
      <template #subtitle>
        Visualisasi pergerakan portfolio (MoM)
      </template>
    </v-card-item>
    
    <v-divider />
    
    <v-card-text class="pa-6">
      <div v-if="loading" class="d-flex justify-center align-center" style="height: 300px;">
        <v-progress-circular indeterminate color="primary" />
      </div>
      
      <div v-else class="chart-placeholder d-flex align-end justify-space-between" style="height: 300px; gap: 8px;">
        <!-- Simulated Bar Chart using Vuetify Sheets -->
        <div 
          v-for="(val, i) in bars" 
          :key="i"
          class="flex-grow-1 position-relative group"
          :style="{ height: val + '%', minWidth: '15px' }"
        >
          <v-sheet
            :color="i === bars.length - 1 ? 'primary' : 'primary-lighten-4'"
            class="rounded-t-lg transition-all w-100 h-100"
            style="opacity: 0.8;"
            :style="{ backgroundColor: i % 2 === 0 ? '#10b981' : '#34d399' }"
          />
          <!-- Simulated Line Point -->
          <div 
            class="position-absolute" 
            :style="{ bottom: (val * 0.4) + '%', left: '50%', transform: 'translateX(-50%)' }"
          >
            <div class="w-2 h-2 rounded-circle bg-rose-500 border-2 border-white"></div>
          </div>
        </div>
      </div>
      
      <!-- Chart Legend -->
      <div class="d-flex justify-center gap-6 mt-6">
        <div class="d-flex align-center gap-2">
          <v-sheet width="12" height="12" color="primary" rounded="xs" />
          <span class="text-caption font-weight-bold text-slate-600">Total Outstanding</span>
        </div>
        <div class="d-flex align-center gap-2">
          <v-sheet width="12" height="12" color="error" rounded="circle" />
          <span class="text-caption font-weight-bold text-slate-600">NPF Risk</span>
        </div>
      </div>
    </v-card-text>
  </v-card>
</template>

<style scoped>
.chart-placeholder {
  background-image: linear-gradient(#f8fafc 1px, transparent 1px);
  background-size: 100% 40px;
}
.group:hover v-sheet {
  opacity: 1 !important;
  transform: scaleX(1.1);
}
</style>
