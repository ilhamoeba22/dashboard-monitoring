<script setup>
import { computed } from 'vue'

const props = defineProps({
  data: {
    type: Array,
    required: true,
    default: () => []
  },
  periods: {
    type: Array,
    required: true,
    default: () => []
  },
  loading: {
    type: Boolean,
    default: false
  }
})

// --- Data Processing for Grand Total Trend ---
const chartSeriesData = computed(() => {
  if (!props.data || props.data.length === 0 || !props.periods || props.periods.length === 0) {
    return { labels: [], nominals: [], growth: [] }
  }

  const labels = props.periods.map(p => p.label)
  const nominals = Array(props.periods.length).fill(0)
  
  // 1. Sum up nominals for each ACTIVE month
  props.data.forEach(item => {
    props.periods.forEach((p, idx) => {
      nominals[idx] += (item[`m${p.index}_nominal`] || 0)
    })
  })

  // 2. Calculate MoM/YoY Growth for the Total Line
  const growth = []
  props.data.forEach(item => {
    // Note: We use average growth across categories or recalculate based on total?
    // Enterprise Standard: Recalculate based on TOTAL sum to get the accurate portfolio growth %
  })

  const totalGrowth = []
  props.periods.forEach((p, idx) => {
    // To get accurate Total Growth %, we need the total yoy_base sum
    let totalYoyBase = 0
    props.data.forEach(item => { totalYoyBase += (item.yoy_base || 0) })

    if (idx === 0) { // Jan (YoY vs Base sum)
        const curr = nominals[0]
        const prev = totalYoyBase
        totalGrowth.push(prev === 0 ? (curr > 0 ? 100 : 0) : parseFloat((((curr - prev) / prev) * 100).toFixed(2)))
    } else { // MoM
        const curr = nominals[idx]
        const prev = nominals[idx - 1]
        totalGrowth.push(prev === 0 ? (curr > 0 ? 100 : 0) : parseFloat((((curr - prev) / prev) * 100).toFixed(2)))
    }
  })

  return {
    labels: labels,
    nominals: nominals.map(v => v / 1e9), // Convert to Miliar
    growth: totalGrowth
  }
})

// --- ApexCharts Configuration ---
const chartOptions = computed(() => ({
  chart: {
    height: 350,
    type: 'line',
    toolbar: { show: false },
    fontFamily: 'Plus Jakarta Sans, sans-serif',
    animations: { enabled: true, easing: 'easeinout', speed: 800 }
  },
  stroke: {
    width: [0, 3],
    curve: 'smooth'
  },
  colors: ['#10B981', '#F59E0B'], // Emerald Bar, Amber Line
  fill: {
    type: ['gradient', 'solid'],
    gradient: {
      shade: 'light',
      type: "vertical",
      shadeIntensity: 0.5,
      gradientToColors: ['#34D399'],
      inverseColors: true,
      opacityFrom: 0.85,
      opacityTo: 0.55,
      stops: [0, 100]
    }
  },
  plotOptions: {
    bar: {
      columnWidth: '45%',
      borderRadius: 6
    }
  },
  markers: {
    size: 5,
    colors: ['#F59E0B'],
    strokeColors: '#fff',
    strokeWidth: 2,
    hover: { size: 7 }
  },
  dataLabels: { enabled: false },
  legend: {
    show: true,
    position: 'top',
    horizontalAlign: 'center',
    labels: { colors: '#64748b' }
  },
  xaxis: {
    categories: chartSeriesData.value.labels,
    axisBorder: { show: false },
    axisTicks: { show: false },
    labels: {
      style: { colors: '#94a3b8', fontSize: '11px' }
    }
  },
  yaxis: [
    {
      title: {
        text: 'Total O/S (Miliar)',
        style: { color: '#10B981', fontWeight: 600 }
      },
      labels: {
        formatter: (val) => `Rp ${val.toFixed(0)}M`,
        style: { colors: '#94a3b8' }
      }
    },
    {
      opposite: true,
      title: {
        text: 'Growth (%)',
        style: { color: '#F59E0B', fontWeight: 600 }
      },
      labels: {
        formatter: (val) => `${val}%`,
        style: { colors: '#94a3b8' }
      }
    }
  ],
  tooltip: {
    shared: true,
    intersect: false,
    theme: 'light',
    y: {
      formatter: function (y, { seriesIndex }) {
        if (typeof y !== "undefined") {
          return seriesIndex === 0 ? `Rp ${y.toFixed(2)} Miliar` : `${y}% Growth`
        }
        return y
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

const series = computed(() => [
  {
    name: 'Total Portfolio O/S',
    type: 'column',
    data: chartSeriesData.value.nominals
  },
  {
    name: 'Portfolio Growth %',
    type: 'line',
    data: chartSeriesData.value.growth
  }
])
</script>

<template>
  <div class="growth-mixed-chart">
    <div v-if="loading" class="d-flex justify-center align-center" style="height: 350px;">
      <v-progress-circular indeterminate color="primary" size="48" />
    </div>
    
    <div v-else-if="!data || data.length === 0" class="d-flex flex-column align-center justify-center" style="height: 350px;">
      <v-icon icon="ri-bar-chart-grouped-line" size="48" color="slate-200" />
      <p class="text-slate-400 mt-2 italic">Data pertumbuhan belum tersedia</p>
    </div>
    
    <apexchart
      v-else
      type="line"
      height="350"
      :options="chartOptions"
      :series="series"
    />
  </div>
</template>

<style scoped>
.growth-mixed-chart {
  width: 100%;
}
</style>
