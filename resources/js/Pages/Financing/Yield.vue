<script setup>
import ComingSoon from '@/components/ComingSoon.vue'
import { Head } from '@inertiajs/vue3'
import DefaultLayout from '@/layouts/default.vue'
defineOptions({ layout: DefaultLayout })
</script>
<template>
  <div>
    <Head title="Yield Pembiayaan" />
    <ComingSoon title="Yield" icon="ri-percent-line" color="amber" group="Penyelesaian" subtitle="Analisis imbal hasil (yield) portofolio pembiayaan per produk dan segmen." />
  </div>
</template>
