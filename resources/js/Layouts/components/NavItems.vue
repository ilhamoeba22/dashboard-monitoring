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

  <VerticalNavGroup
    :item="{
      title: 'Nasabah (CIF)',
      icon: 'ri-user-search-line',
      badgeContent: 'MCI',
      badgeClass: 'bg-success',
    }"
  >
    <VerticalNavLink :item="{ title: 'Overview', to: '/cif', icon: 'ri-dashboard-3-line' }" />
    
    <VerticalNavSectionTitle :item="{ heading: 'PENGECEKAN DATA' }" />
    <VerticalNavLink :item="{ title: 'CIF Pembiayaan', to: '/cif/pembiayaan', icon: 'ri-bank-line' }" />
    <VerticalNavLink :item="{ title: 'CIF Tabungan', to: '/cif/tabungan', icon: 'ri-wallet-3-line' }" />
    <VerticalNavLink :item="{ title: 'CIF Deposito', to: '/cif/deposito', icon: 'ri-safe-2-line' }" />
    
    <VerticalNavSectionTitle :item="{ heading: 'ANALITIK' }" />
    <VerticalNavLink :item="{ title: 'Rekapitulasi', to: '/cif/rekapitulasi', icon: 'ri-bar-chart-grouped-line' }" />
    <VerticalNavLink :item="{ title: 'Quality Audit', to: '/cif/quality', icon: 'ri-shield-check-line' }" />
  </VerticalNavGroup>

  <VerticalNavGroup
    :item="{
      title: 'Funding',
      icon: 'ri-safe-2-line',
      badgeContent: 'DPK',
      badgeClass: 'bg-info',
    }"
  >
    <VerticalNavLink :item="{ title: 'Overview', to: '/funding', icon: 'ri-dashboard-3-line' }" />

    <VerticalNavSectionTitle :item="{ heading: 'TABUNGAN' }" />
    <VerticalNavLink :item="{ title: 'Nominatif Tabungan', to: '/funding/tabungan/nominatif', icon: 'ri-list-check-3' }" />
    <VerticalNavLink :item="{ title: 'Rekapitulasi Tabungan', to: '/funding/tabungan/rekapitulasi', icon: 'ri-bar-chart-grouped-line' }" />
    <VerticalNavLink :item="{ title: 'Dormant Tabungan', to: '/funding/tabungan/dormant', icon: 'ri-time-line' }" />

    <VerticalNavSectionTitle :item="{ heading: 'DEPOSITO' }" />
    <VerticalNavLink :item="{ title: 'Nominatif Deposito', to: '/funding/deposito/nominatif', icon: 'ri-safe-2-line' }" />
    <VerticalNavLink :item="{ title: 'Rekapitulasi Deposito', to: '/funding/deposito/rekapitulasi', icon: 'ri-bar-chart-box-line' }" />
    <VerticalNavLink :item="{ title: 'Deposito Jatuh Tempo', to: '/funding/deposito/jatuh-tempo', icon: 'ri-calendar-event-line' }" />

    <VerticalNavSectionTitle :item="{ heading: 'ANALITIK & TARGET' }" />
    <VerticalNavLink :item="{ title: 'Perkembangan', to: '/funding/perkembangan', icon: 'ri-line-chart-line' }" />
    <VerticalNavLink :item="{ title: 'Target', to: '/funding/target', icon: 'ri-crosshair-2-line' }" />
    <VerticalNavLink :item="{ title: 'Mutasi', to: '/funding/mutasi', icon: 'ri-arrow-left-right-line' }" />

    <VerticalNavSectionTitle :item="{ heading: 'RISIKO & LIKUIDITAS' }" />
    <VerticalNavLink :item="{ title: 'Risk Funding', to: '/funding/risk', icon: 'ri-radar-line' }" />
    <VerticalNavLink :item="{ title: 'Nasabah Terbesar', to: '/funding/concentration', icon: 'ri-user-star-line' }" />

    <VerticalNavSectionTitle :item="{ heading: 'BAGI HASIL & PAJAK' }" />
    <VerticalNavLink :item="{ title: 'Bagi Hasil', to: '/funding/baghas', icon: 'ri-percent-line' }" />
  </VerticalNavGroup>

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
