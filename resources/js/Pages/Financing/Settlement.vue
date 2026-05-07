<script setup>
import ComingSoon from '@/components/ComingSoon.vue'
import { Head } from '@inertiajs/vue3'
import DefaultLayout from '@/layouts/default.vue'
defineOptions({ layout: DefaultLayout })
</script>
<template>
  <div>
    <Head title="Settlement Pembiayaan" />
    <ComingSoon title="Settlement" icon="ri-hand-coin-line" color="teal" group="Penyelesaian" subtitle="Data penyelesaian pembiayaan bermasalah melalui skema settlement." />
  </div>
</template>
