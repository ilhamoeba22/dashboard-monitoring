<script setup>
import ComingSoon from '@/components/ComingSoon.vue'
import { Head } from '@inertiajs/vue3'
import DefaultLayout from '@/layouts/default.vue'
defineOptions({ layout: DefaultLayout })
</script>
<template>
  <div>
    <Head title="PPKA Pembiayaan" />
    <ComingSoon title="PPKA" icon="ri-file-damage-line" color="deep-purple" group="Penyelesaian" subtitle="Pencadangan Penghapusan Kerugian Aktiva (PPKA) — laporan penyisihan piutang." />
  </div>
</template>
