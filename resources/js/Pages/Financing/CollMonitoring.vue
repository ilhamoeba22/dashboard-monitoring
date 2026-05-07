<script setup>
import ComingSoon from '@/components/ComingSoon.vue'
import { Head } from '@inertiajs/vue3'
import DefaultLayout from '@/layouts/default.vue'
defineOptions({ layout: DefaultLayout })
</script>
<template>
  <div>
    <Head title="Coll Monitoring" />
    <ComingSoon title="Coll Monitoring" icon="ri-eye-line" color="orange" group="Tunggakan" subtitle="Pemantauan kolektibilitas secara real-time untuk deteksi dini potensi NPF." />
  </div>
</template>
