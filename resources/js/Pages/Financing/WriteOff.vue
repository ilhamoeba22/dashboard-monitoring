<script setup>
import ComingSoon from '@/components/ComingSoon.vue'
import { Head } from '@inertiajs/vue3'
import DefaultLayout from '@/layouts/default.vue'
defineOptions({ layout: DefaultLayout })
</script>
<template>
  <div>
    <Head title="Write-Off Pembiayaan" />
    <ComingSoon title="Write-Off" icon="ri-delete-back-2-line" color="red" group="Penyelesaian" subtitle="Rekap pembiayaan yang telah dihapusbukukan beserta status penagihannya." />
  </div>
</template>
