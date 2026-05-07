<script setup>
import ComingSoon from '@/components/ComingSoon.vue'
import { Head } from '@inertiajs/vue3'
import DefaultLayout from '@/layouts/default.vue'
defineOptions({ layout: DefaultLayout })
</script>
<template>
  <div>
    <Head title="Repayment Rate (Nasabah Baru)" />
    <ComingSoon title="Repayment Rate (Baru)" icon="ri-speed-up-line" color="green" group="Performance" subtitle="Repayment rate khusus untuk nasabah baru — segmentasi akuisisi vs. retention." />
  </div>
</template>
