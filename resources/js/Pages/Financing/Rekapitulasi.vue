<script setup>
import ComingSoon from '@/components/ComingSoon.vue'
import { Head } from '@inertiajs/vue3'
import DefaultLayout from '@/layouts/default.vue'
defineOptions({ layout: DefaultLayout })
</script>
<template>
  <div>
    <Head title="Rekapitulasi Pembiayaan" />
    <ComingSoon title="Rekapitulasi Pembiayaan" icon="ri-bar-chart-grouped-line" color="primary" group="Rekapitulasi" subtitle="Ringkasan rekapitulasi portofolio pembiayaan per cabang, produk, dan segmen." />
  </div>
</template>
