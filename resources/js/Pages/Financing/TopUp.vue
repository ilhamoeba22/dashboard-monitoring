<script setup>
import ComingSoon from '@/components/ComingSoon.vue'
import { Head } from '@inertiajs/vue3'
import DefaultLayout from '@/layouts/default.vue'
defineOptions({ layout: DefaultLayout })
</script>
<template>
  <div>
    <Head title="Top-Up Pembiayaan" />
    <ComingSoon title="Top-Up" icon="ri-add-circle-line" color="success" group="Restrukturisasi" subtitle="Data pembiayaan yang mendapatkan fasilitas penambahan plafon (top-up)." />
  </div>
</template>
