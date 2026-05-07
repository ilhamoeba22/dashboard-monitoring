<script setup>
import ComingSoon from '@/components/ComingSoon.vue'
import { Head } from '@inertiajs/vue3'
import DefaultLayout from '@/layouts/default.vue'
defineOptions({ layout: DefaultLayout })
</script>
<template>
  <div>
    <Head title="Repayment Rate" />
    <ComingSoon title="Repayment Rate" icon="ri-speed-line" color="primary" group="Performance" subtitle="Tingkat pengembalian angsuran pembiayaan secara keseluruhan per periode." />
  </div>
</template>
