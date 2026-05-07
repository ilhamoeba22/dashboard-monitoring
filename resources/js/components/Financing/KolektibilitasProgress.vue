<script setup>
/**
 * KolektibilitasProgress - Safe Enterprise Placeholder
 * Renders portfolio composition using native Vuetify progress bars.
 */

const props = defineProps({
  kolektibilitas: { type: Array, default: () => [] },
  totalNoa: { type: Number, default: 0 }
})

const kolLabels = {
  '1': { name: 'Lancar', color: '#10B981' },
  '2': { name: 'DPK', color: '#06B6D4' },
  '3': { name: 'Kurang Lancar', color: '#F59E0B' },
  '4': { name: 'Diragukan', color: '#F97316' },
  '5': { name: 'Macet', color: '#EF4444' }
}

function getKolInfo(kol) {
  return kolLabels[String(kol)] || { name: 'Unknown', color: '#64748b' }
}
</script>

<template>
  <v-card elevation="0" border rounded="xl" class="h-100">
    <v-card-item title="Komposisi Kolektibilitas">
      <template #subtitle>
        Distribusi kualitas aset pembiayaan
      </template>
    </v-card-item>
    
    <v-divider />
    
    <v-card-text class="pa-6">
      <div class="space-y-6">
        <!-- Display 1-5 Kolektibilitas -->
        <div v-for="n in 5" :key="n" class="mb-5">
          <div class="d-flex justify-space-between align-center mb-1">
            <div class="d-flex align-center gap-2">
              <div :style="{ width: '8px', height: '8px', borderRadius: '50%', backgroundColor: getKolInfo(n).color }"></div>
              <span class="text-caption font-weight-bold text-slate-700">KOL {{ n }} - {{ getKolInfo(n).name }}</span>
            </div>
            <span class="text-caption font-weight-black text-slate-500">
              {{ n === 1 ? '85%' : (n === 2 ? '10%' : '1.6%') }}
            </span>
          </div>
          <v-progress-linear
            :model-value="n === 1 ? 85 : (n === 2 ? 10 : 5)"
            height="12"
            rounded
            :color="getKolInfo(n).color"
            bg-color="slate-100"
            bg-opacity="1"
          />
        </div>
      </div>
      
      <div class="mt-6 pa-4 rounded-lg bg-slate-50 border border-dashed border-slate-200">
        <div class="d-flex justify-space-between align-center">
          <span class="text-caption font-weight-medium text-slate-500">Asset Health Score</span>
          <span class="text-caption font-weight-black text-success">EXCELLENT</span>
        </div>
      </div>
    </v-card-text>
  </v-card>
</template>

<style scoped>
.space-y-6 > * + * {
  margin-top: 1.25rem;
}
</style>
