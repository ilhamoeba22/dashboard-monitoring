<script setup>
import ComingSoon from '@/components/ComingSoon.vue'
import { Head } from '@inertiajs/vue3'
import DefaultLayout from '@/layouts/default.vue'
defineOptions({ layout: DefaultLayout })
</script>
<template>
  <div>
    <Head title="Kolektibilitas Pembiayaan" />
    <ComingSoon title="Kolektibilitas" icon="ri-stack-line" color="warning" group="Rekapitulasi" subtitle="Distribusi kolektibilitas pembiayaan — Lancar, DPK, Kurang Lancar, Diragukan, Macet." />
  </div>
</template>
