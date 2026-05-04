<script setup>
import { computed } from 'vue'
const props = defineProps({
  kolektibilitas: { type: Array, default: () => [] },
  totalNoa: { type: Number, default: 0 }
})
const kolConfig = {
  '1': { bg: '#10B981', bgLight: 'rgba(16, 185, 129, 0.12)', label: 'Lancar', desc: 'Kol 1' },
  '2': { bg: '#06B6D4', bgLight: 'rgba(6, 182, 212, 0.12)', label: 'DPK', desc: 'Kol 2' },
  '3': { bg: '#F59E0B', bgLight: 'rgba(245, 158, 11, 0.12)', label: 'Kurang Lancar', desc: 'Kol 3' },
  '4': { bg: '#F97316', bgLight: 'rgba(249, 115, 22, 0.12)', label: 'Diragukan', desc: 'Kol 4' },
  '5': { bg: '#EF4444', bgLight: 'rgba(239, 68, 68, 0.12)', label: 'Macet', desc: 'Kol 5' }
}
function calcPct(noa) { return props.totalNoa > 0 ? Math.round((noa / props.totalNoa) * 100) : 0 }
function formatRp(v) {
  if (!v && v !== 0) return '-'
  const n = parseFloat(v)
  if (isNaN(n)) return '-'
  if (n >= 1e9) return (n / 1e9).toFixed(2) + ' M'
  if (n >= 1e6) return (n / 1e6).toFixed(1) + ' Jt'
  return n.toLocaleString('id-ID')
}
function formatNumber(v) { if (!v && v !== 0) return '-'; return parseInt(v).toLocaleString('id-ID') }
function getKolStyle(kol) { const c = kolConfig[kol] || kolConfig['1']; return { bgColor: c.bg, bgLight: c.bgLight, label: c.label, desc: c.desc } }
function getPctTextColor(noa, kol) { const pct = calcPct(noa); return pct > 50 ? '#FFFFFF' : getKolStyle(kol).bgColor }
const totalOS = computed(() => { return props.kolektibilitas.reduce((sum, kol) => sum + parseFloat(kol.total_os || 0), 0) })
</script>

<template>
  <v-card elevation="0" style="border: 1px solid rgba(var(--v-border-color), 0.08); border-radius: 12px; overflow: hidden;">
    <div class="d-flex align-center justify-space-between pa-4 pb-0">
      <div class="d-flex align-center">
        <div style="width: 40px; height: 40px; border-radius: 10px; background: rgba(var(--v-theme-primary), 0.12); display: flex; align-items: center; justify-content: center; margin-right: 12px;">
          <v-icon icon="ri-pie-chart-line" color="primary" size="20" />
        </div>
        <div>
          <span class="text-h6 font-weight-bold">Distribusi Kolektibilitas</span>
          <p class="text-caption text-medium-emphasis mb-0">{{ formatRp(totalOS) }} Total Outstanding</p>
        </div>
      </div>
      <v-chip size="small" color="primary" variant="tonal">{{ kolektibilitas.length }} Kolektibilitas</v-chip>
    </div>
    <v-card-text class="pa-4">
      <v-table density="comfortable" class="rounded-lg overflow-hidden">
        <thead>
          <tr>
            <th class="text-left font-weight-bold" style="width: 140px; background: #F1F5F9; color: #374151; padding: 14px 16px; font-size: 11px; text-transform: uppercase;">Kolektibilitas</th>
            <th class="text-right font-weight-bold" style="background: #F1F5F9; color: #374151; padding: 14px 16px; font-size: 11px; text-transform: uppercase;">Jumlah</th>
            <th class="text-right font-weight-bold" style="background: #F1F5F9; color: #374151; padding: 14px 16px; font-size: 11px; text-transform: uppercase;">Outstanding</th>
            <th class="text-center font-weight-bold" style="width: 180px; background: #F1F5F9; color: #374151; padding: 14px 16px; font-size: 11px; text-transform: uppercase;">Distribusi</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="(kol, i) in kolektibilitas" :key="kol.kol">
            <td>
              <div class="d-flex align-center">
                <div :style="{ width: '8px', height: '8px', borderRadius: '50%', background: getKolStyle(kol.kol).bgColor, marginRight: '10px' }"></div>
                <v-chip size="small" :style="{ background: getKolStyle(kol.kol).bgLight, color: getKolStyle(kol.kol).bgColor, fontWeight: '600' }">{{ getKolStyle(kol.kol).desc }}</v-chip>
                <span class="text-body-2 text-medium-emphasis ml-2 d-none d-md-inline" style="font-size: 12px;">{{ getKolStyle(kol.kol).label }}</span>
              </div>
            </td>
            <td class="text-right">
              <span class="text-body-2 font-weight-medium">{{ formatNumber(kol.noa) }}</span>
              <span class="text-caption text-medium-emphasis ml-1">rekening</span>
            </td>
            <td class="text-right">
              <span class="text-body-2 font-weight-bold" :style="{ color: getKolStyle(kol.kol).bgColor }">{{ formatRp(kol.total_os) }}</span>
            </td>
            <td>
              <v-progress-linear :model-value="calcPct(kol.noa)" height="24" rounded :style="{ background: getKolStyle(kol.kol).bgLight }" :color="getKolStyle(kol.kol).bgColor">
                <template #default><span class="font-weight-bold" :style="{ color: getPctTextColor(kol.noa, kol.kol), fontSize: '12px' }">{{ calcPct(kol.noa) }}%</span></template>
              </v-progress-linear>
            </td>
          </tr>
        </tbody>
      </v-table>
    </v-card-text>
  </v-card>
</template>
