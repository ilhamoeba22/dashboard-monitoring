<script setup>
import ComingSoon from '@/components/ComingSoon.vue'
import { Head } from '@inertiajs/vue3'
import DefaultLayout from '@/layouts/default.vue'
defineOptions({ layout: DefaultLayout })
</script>
<template>
  <div>
    <Head title="Restrukturisasi Pembiayaan" />
    <ComingSoon title="Restrukturisasi" icon="ri-refresh-line" color="info" group="Restrukturisasi" subtitle="Data pembiayaan yang telah direstrukturisasi beserta riwayat dan statusnya." />
  </div>
</template>
