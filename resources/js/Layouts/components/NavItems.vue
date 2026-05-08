<script setup>
import VerticalNavSectionTitle from '@/@layouts/components/VerticalNavSectionTitle.vue'
import VerticalNavGroup from '@layouts/components/VerticalNavGroup.vue'
import VerticalNavLink from '@layouts/components/VerticalNavLink.vue'
</script>

<template>
  <!-- Dashboards -->
  <VerticalNavLink
    :item="{
      title: 'Dashboard',
      to: '/',
      icon: 'ri-home-smile-line',
    }"
  />

  <!-- Pembiayaan Module -->
  <VerticalNavGroup
    :item="{
      title: 'Pembiayaan',
      icon: 'ri-bank-line',
      badgeContent: 'MCI',
      badgeClass: 'bg-primary',
    }"
  >
    <VerticalNavLink
      :item="{
        title: 'Overview',
        to: '/financing',
        icon: 'ri-pie-chart-2-line',
      }"
    />
    
    <VerticalNavSectionTitle :item="{ heading: 'BISNIS & RISIKO' }" />
    <VerticalNavLink :item="{ title: 'Rekapitulasi', to: '/financing/rekapitulasi', icon: 'ri-bar-chart-grouped-line' }" />
    <VerticalNavLink :item="{ title: 'Quality & Risk', to: '/financing/quality', icon: 'ri-shield-keyhole-line' }" />
    <VerticalNavLink :item="{ title: 'Perkembangan', to: '/financing/perkembangan', icon: 'ri-line-chart-line' }" />
    <VerticalNavLink :item="{ title: 'Target RBB', to: '/financing/target', icon: 'ri-focus-2-line' }" />
  </VerticalNavGroup>

  <VerticalNavLink
    :item="{
      title: 'Nasabah (CIF)',
      icon: 'ri-user-search-line',
      to: '/cif',
    }"
  />

  <VerticalNavLink
    :item="{
      title: 'Funding',
      icon: 'ri-safe-2-line',
      to: '/funding',
    }"
  />

  <VerticalNavLink
    :item="{
      title: 'Laporan',
      icon: 'ri-file-chart-2-line',
      to: '/reporting',
    }"
  />

  <!-- Template Original Links (Hidden/Simplified for MCI) -->
  <VerticalNavSectionTitle :item="{ heading: 'SYSTEM' }" />
  <VerticalNavLink :item="{ title: 'Account Settings', icon: 'ri-user-settings-line', to: '/account-settings' }" />

</template>
