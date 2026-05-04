<script setup>
/**
 * TrendChart Component
 * Line chart untuk menampilkan trend O/S, NOA dalam 12 bulan
 * Menggunakan ApexCharts dengan theme Emerald Green (#059669) + Gold (#D97706)
 */

import { computed, ref } from 'vue'

// Props
const props = defineProps({
  series: {
    type: Array,
    default: () => []
  },
  loading: {
    type: Boolean,
    default: false
  }
})

// Theme Colors
const themeColors = {
  primary: '#10b981',   // Emerald (Nominal Pencairan)
  secondary: '#1e293b', // Navy/Slate Premium (Rekening Cair)
}

// Period options untuk filter
const periodOptions = [
  { title: '3 bln', value: 3 },
  { title: '6 bln', value: 6 },
  { title: '12 bln', value: 12 }
]

const selectedPeriod = ref(12)

// Emit untuk filter change
const emit = defineEmits(['period-change'])

function onPeriodChange(value) {
  emit('period-change', value)
}

// Format Periode YYYYMM -> "Jan 26"
function formatPeriod(period) {
  if (!period || period.length !== 6) return period
  const year = period.substring(2, 4)
  const monthIdx = parseInt(period.substring(4, 6)) - 1
  const months = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agt', 'Sep', 'Okt', 'Nov', 'Des']
  return `${months[monthIdx]} ${year}`
}

// Computed chart data - dengan data type safety
const chartData = computed(() => {
  const safeSeries = Array.isArray(props.series) ? props.series : []
  
  return {
    categories: safeSeries.map(item => formatPeriod(item.periode)),
    series: [
      {
        name: 'Nominal Pencairan',
        type: 'column',
        data: safeSeries.map(item => {
          const val = parseFloat(item.total_os) || 0
          return val / 1e9
        })
      },
      {
        name: 'Rekening Cair (NOA)',
        type: 'line',
        data: safeSeries.map(item => parseInt(item.total_noa) || 0)
      }
    ]
  }
})

// ApexCharts config
const chartOptions = computed(() => ({
  chart: {
    type: 'line',
    height: 320,
    toolbar: { show: false },
    dropShadow: {
      enabled: true,
      enabledOnSeries: [1], // Hanya untuk NOA (Line)
      top: 5,
      left: 0,
      blur: 4,
      color: themeColors.secondary,
      opacity: 0.25
    },
    animations: {
      enabled: true,
      easing: 'easeinout',
      speed: 800
    },
    fontFamily: 'Plus Jakarta Sans, sans-serif',
    background: 'transparent'
  },
  colors: [themeColors.primary, themeColors.secondary],
  stroke: {
    curve: 'smooth',
    width: [0, 4] // Bar width 0, Line width 4
  },
  fill: {
    type: ['gradient', 'solid'],
    gradient: {
      shade: 'light',
      type: 'vertical',
      shadeIntensity: 0.5,
      gradientToColors: ['#34d399'], // Hijau Mint/terang di ujung atas
      inverseColors: true,
      opacityFrom: 1,
      opacityTo: 1,
      stops: [0, 100]
    }
  },
  plotOptions: {
    bar: {
      columnWidth: '40%',
      borderRadius: 8,
      borderRadiusApplication: 'around'
    }
  },
  dataLabels: {
    enabled: false
  },
  legend: {
    show: true,
    position: 'top',
    horizontalAlign: 'center',
    fontSize: '13px',
    fontWeight: 600,
    fontFamily: 'Plus Jakarta Sans, sans-serif',
    labels: { colors: '#64748b' },
    markers: { 
      width: 12, 
      height: 12, 
      radius: 12,
      offsetX: -4
    },
    itemMargin: {
      horizontal: 15,
      vertical: 10
    }
  },
  xaxis: {
    categories: chartData.value.categories,
    labels: {
      style: {
        colors: '#94a3b8',
        fontSize: '11px',
        fontFamily: 'Plus Jakarta Sans, sans-serif'
      }
    },
    axisBorder: { show: false },
    axisTicks: { show: false }
  },
  yaxis: [
    {
      seriesName: 'Nominal Pencairan',
      labels: {
        formatter: (val) => `Rp ${val.toFixed(0)} M`,
        style: {
          colors: '#94a3b8',
          fontSize: '10px',
          fontWeight: 500
        }
      },
      min: 0,
      tickAmount: 5
    },
    {
      seriesName: 'Rekening Cair (NOA)',
      opposite: true,
      labels: { 
        show: true,
        formatter: (val) => Math.round(val), // Fix decimal bug
        style: {
          colors: '#94a3b8',
          fontSize: '10px'
        }
      },
      min: 0,
      tickAmount: 5
    }
  ],
  tooltip: {
    enabled: true,
    theme: 'light',
    shared: true,
    intersect: false,
    y: {
      formatter: (val, { seriesIndex }) => {
        if (seriesIndex === 0) return `Rp ${val.toFixed(2)} Miliar`
        return `${Math.round(val).toLocaleString('id-ID')} Rekening`
      }
    }
  },
  grid: {
    borderColor: '#f1f5f9',
    strokeDashArray: 4,
    xaxis: { lines: { show: false } },
    yaxis: { lines: { show: true } }
  }
}))

// Chart series (reactive)
const chartSeries = computed(() => chartData.value.series)

// Loading skeleton
const isEmpty = computed(() => {
  const safeSeries = Array.isArray(props.series) ? props.series : []
  return safeSeries.length === 0
})
</script>

<template>
  <v-card
    elevation="0"
    :style="{
      border: '1px solid rgba(var(--v-border-color), 0.08)',
      borderRadius: '12px'
    }"
  >
    <!-- Card Header -->
    <div class="d-flex align-center justify-space-between pa-4 pb-2">
      <div class="d-flex align-center">
        <div
          :style="{
            width: '40px',
            height: '40px',
            borderRadius: '10px',
            background: 'rgba(5, 150, 105, 0.12)',
            display: 'flex',
            alignItems: 'center',
            justifyContent: 'center',
            marginRight: '12px'
          }"
        >
          <v-icon icon="ri-line-chart-line" color="primary" size="20" />
        </div>
        <div>
          <h2 class="text-h6 font-weight-bold mb-0" style="font-family: 'Plus Jakarta Sans', sans-serif;">
            Trend Pembiayaan
          </h2>
          <p class="text-caption text-medium-emphasis mb-0">
            Outstanding &amp; Jumlah Rekening
          </p>
        </div>
      </div>
      
      <!-- Period Filter -->
      <div class="period-filter">
        <v-btn-toggle
          v-model="selectedPeriod"
          mandatory
          density="compact"
          variant="outlined"
          color="primary"
          divided
          rounded="lg"
          class="period-toggle"
          @update:model-value="onPeriodChange"
        >
          <v-btn
            v-for="opt in periodOptions"
            :key="opt.value"
            :value="opt.value"
            size="x-small"
            class="px-3"
          >
            {{ opt.title }}
          </v-btn>
        </v-btn-toggle>
      </div>
    </div>
    
    <v-card-text class="pa-4 pt-2">
      <!-- Loading -->
      <div v-if="loading" class="d-flex justify-center align-center" style="height: 280px;">
        <v-progress-circular indeterminate color="primary" size="48" />
      </div>
      
      <!-- Empty State -->
      <div v-else-if="isEmpty" class="text-center py-12">
        <v-icon icon="ri-line-chart-line" size="64" color="primary" style="opacity: 0.3;" />
        <p class="text-body-2 text-medium-emphasis mt-4">
          Tidak ada data trend tersedia
        </p>
      </div>
      
      <!-- Chart -->
      <div v-else>
        <apexchart
          type="line"
          height="300"
          :options="chartOptions"
          :series="chartSeries"
        />
      </div>
    </v-card-text>
  </v-card>
</template>

<style scoped>
.period-filter {
  display: flex;
  align-items: center;
}

.period-toggle {
  border: 1px solid rgba(5, 150, 105, 0.3) !important;
  border-radius: 8px !important;
  background: rgb(var(--v-theme-surface)) !important;
}

.period-toggle :deep(.v-btn) {
  border: none !important;
  text-transform: none;
  font-weight: 500;
  font-size: 12px;
}

.period-toggle :deep(.v-btn--active) {
  background: linear-gradient(135deg, #059669 0%, #10B981 100%) !important;
  color: white !important;
}
</style>
