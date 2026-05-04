<script setup>
/**
 * MultiPeriodCompareChart Component - FINAL TREND VERSION
 * Exclusive Line Visualization:
 * - Smooth curves for all metrics
 * - Floating Data Labels (Rupiah/Percent)
 * - Dynamic Contrast Zoom (Auto-scaling Y-axis)
 */

import { computed } from 'vue'

const props = defineProps({
  data: {
    type: Object,
    default: () => ({
      series: [],
      labels: [],
      raw_data: []
    })
  },
  metric: {
    type: String,
    default: 'total_os' // 'total_os' | 'total_npf' | 'npf_persen'
  },
  loading: {
    type: Boolean,
    default: false
  }
})

// Metric Configurations
const metricConfigs = {
  total_os: {
    name: 'Total Outstanding',
    color: '#10B981', // Emerald
    unit: 'Rp'
  },
  total_npf: {
    name: 'Nominal NPF',
    color: '#F59E0B', // Amber
    unit: 'Rp'
  },
  npf_persen: {
    name: 'Rasio NPF (%)',
    color: '#EF4444', // Red
    unit: '%'
  }
}

/**
 * Helper: Parse Database Name to Date Object
 */
function parseDbDate(name) {
  if (!name) return new Date(0)
  const months = { 'JAN': 0, 'FEB': 1, 'MAR': 2, 'APR': 3, 'MEI': 4, 'JUN': 5, 'JUL': 6, 'AGU': 7, 'SEP': 8, 'OKT': 9, 'NOV': 10, 'DES': 11 }
  const matchNew = name.match(/MCI_([A-Z]{3})(\d{2})_/)
  if (matchNew) return new Date(2000 + parseInt(matchNew[2]), months[matchNew[1]], 1)
  const matchOld = name.match(/MCI_([A-Z]{3})_/)
  if (matchOld) return new Date(parseInt(name.slice(-4)), months[matchOld[1]], 1)
  return new Date(0)
}

/**
 * Helper: Format label for X-Axis
 */
function formatShortLabel(name) {
  const date = parseDbDate(name)
  if (date.getTime() === 0) return name
  const monthNames = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des']
  return `${monthNames[date.getMonth()]} ${date.getFullYear().toString().slice(-2)}`
}

/**
 * Helper: Dynamic Currency Formatter for Data Labels
 */
function formatCompactRp(val) {
  if (val >= 1e9) return `Rp ${(val / 1e9).toFixed(2)} M`
  if (val >= 1e6) return `Rp ${(val / 1e6).toFixed(1)} Jt`
  return val.toLocaleString('id-ID')
}

/**
 * Prepared Data for dynamic metric
 */
const chartData = computed(() => {
  if (!props.data?.raw_data || props.data.raw_data.length === 0) return null

  const sorted = [...props.data.raw_data].sort((a, b) => parseDbDate(a.period).getTime() - parseDbDate(b.period).getTime())
  const config = metricConfigs[props.metric]

  return {
    labels: sorted.map(item => formatShortLabel(item.period)),
    series: [{
      name: config.name,
      data: sorted.map(item => item[props.metric])
    }]
  }
})

const chartOptions = computed(() => {
  const isRatio = props.metric === 'npf_persen'
  const config = metricConfigs[props.metric]

  return {
    chart: {
      height: 380,
      type: 'line', // Forced Line Chart as per final requirement
      toolbar: { show: true },
      fontFamily: 'Plus Jakarta Sans, sans-serif',
      zoom: { enabled: false }
    },
    stroke: {
      width: 4,
      curve: 'smooth'
    },
    colors: [config.color],
    dataLabels: {
      enabled: true,
      offsetY: -12,
      style: {
        fontSize: '12px',
        fontWeight: 700,
        colors: [config.color]
      },
      background: {
        enabled: true,
        foreColor: '#fff',
        padding: 4,
        borderRadius: 4,
        borderWidth: 0,
        opacity: 0.9,
        dropShadow: { enabled: false }
      },
      formatter: (val) => isRatio ? `${val.toFixed(2)}%` : formatCompactRp(val)
    },
    markers: {
      size: 6,
      strokeWidth: 3,
      strokeColors: '#fff',
      hover: { size: 8 }
    },
    labels: chartData.value?.labels || [],
    xaxis: {
      axisBorder: { show: false },
      labels: { style: { fontWeight: 700, colors: '#64748B' } }
    },
    yaxis: {
      show: false, // Hide labels because DataLabels are prominent at each point
      title: { 
        text: config.name, 
        style: { color: config.color, fontWeight: 700 } 
      },
      // Dynamic Zoom Logic for Maximum Contrast
      min: (min) => {
          if (isRatio) return Math.max(0, min - 0.2);
          return min - (min * 0.02); // 2% padding below the lowest point
      },
      max: (max) => {
          if (isRatio) return max + 0.2;
          return max + (max * 0.01); // 1% padding above the highest point
      }
    },
    tooltip: {
      theme: 'light',
      y: {
        formatter: (y) => {
          if (isRatio) return `${y.toFixed(2)}%`
          return `Rp ${y.toLocaleString('id-ID')}`
        }
      }
    },
    grid: { 
      borderColor: '#F1F5F9', 
      strokeDashArray: 4,
      padding: { top: 20, bottom: 10, left: 10, right: 10 }
    }
  }
})

const hasData = computed(() => chartData.value !== null)
</script>

<template>
  <v-card elevation="0" border class="pa-4 rounded-xl shadow-sm">
    <div class="d-flex align-center mb-6">
      <v-avatar :color="metricConfigs[metric].color" variant="tonal" size="48" class="me-4">
        <v-icon icon="ri-line-chart-line" size="28" />
      </v-avatar>
      <div>
        <h3 class="text-h6 font-weight-bold mb-0" :style="{ color: metricConfigs[metric].color }">
            {{ metricConfigs[metric].name }}
        </h3>
        <p class="text-caption text-medium-emphasis mb-0">Tren pergerakan komparatif lintas periode</p>
      </div>
    </div>

    <div v-if="loading" class="d-flex justify-center align-center" style="height: 380px;">
      <v-progress-circular indeterminate color="primary" size="64" />
    </div>

    <div v-else-if="!hasData" class="d-flex flex-column justify-center align-center" style="height: 380px;">
      <v-icon icon="ri-pulse-line" size="64" class="text-disabled mb-4" />
      <p class="text-body-1 font-weight-medium text-medium-emphasis">Silakan pilih periode komparasi</p>
    </div>

    <div v-else>
      <apexchart
        height="380"
        :options="chartOptions"
        :series="chartData.series"
      />
    </div>
  </v-card>
</template>
