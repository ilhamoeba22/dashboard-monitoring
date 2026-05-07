<script setup>
import ComingSoon from '@/components/ComingSoon.vue'
import { Head } from '@inertiajs/vue3'
import DefaultLayout from '@/layouts/default.vue'
defineOptions({ layout: DefaultLayout })
</script>
<template>
  <div>
    <Head title="Jatuh Tempo Pembiayaan" />
    <ComingSoon title="Jatuh Tempo" icon="ri-alarm-warning-line" color="error" group="Tunggakan" subtitle="Monitoring pembiayaan yang akan jatuh tempo dalam periode terdekat." />
  </div>
</template>
