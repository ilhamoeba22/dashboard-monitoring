<script setup>
import { ref, computed, onMounted, mergeProps } from 'vue'
import { usePage } from '@inertiajs/vue3'
import { useTheme } from 'vuetify'
import logoMci from '@/assets/images/logos/logo_mci.png'
import headLogo from '@/assets/images/logos/head_logo.png'

const theme = useTheme()
const isDark = ref(theme.global.name.value === 'dark')
const rail = ref(false)
const page = usePage()

const currentPath = computed(() => {
  const url = page.url
  if (url === '/' || url === '') return '/'
  return url.split('?')[0]
})

// Navigation Items
const navItems = [
  { title: 'Dashboard', subtitle: 'Executive Overview', icon: 'ri-dashboard-2-line', href: '/' },

  // ========== PEMBIAYAAN (17 halaman, 8 grup) ==========
  { 
    title: 'Pembiayaan', 
    subtitle: 'Financing Module', 
    icon: 'ri-bank-line', 
    value: 'pembiayaan',
    children: [
      // G1: OVERVIEW
      { title: 'Overview', icon: 'ri-pie-chart-2-line', href: '/financing' },

      // G2: DATA ENTRY
      { type: 'subheader', title: 'DATA ENTRY' },
      { title: 'Nominatif', icon: 'ri-list-check-3', href: '/financing/nominatif' },
      { title: 'Sindikasi', icon: 'ri-group-line', href: '/financing/sindikasi' },
      { title: 'Karyawan', icon: 'ri-id-card-line', href: '/financing/karyawan' },

      // G3: PERKEMBANGAN
      { type: 'subheader', title: 'ANALITIK & TARGET' },
      { title: 'Perkembangan', icon: 'ri-line-chart-line', href: '/financing/perkembangan' },
      { title: 'Target RBB', icon: 'ri-focus-2-line', href: '/financing/target' },

      // G4: MASTER CONSOLE
      { type: 'subheader', title: 'MASTER CONSOLE' },
      { title: 'Rekapitulasi', icon: 'ri-bar-chart-grouped-line', href: '/financing/rekapitulasi' },
      { title: 'Quality & Risk', icon: 'ri-shield-keyhole-line', href: '/financing/quality' },

      // G5: TUNGGAKAN
      { type: 'subheader', title: 'TUNGGAKAN' },
      { title: 'Jatuh Tempo', icon: 'ri-alarm-warning-line', href: '/financing/jatuh-tempo' },
      { title: 'Coll Monitoring', icon: 'ri-eye-line', href: '/financing/coll-monitoring' },

      // G6: RESTRUKTURISASI
      { type: 'subheader', title: 'RESTRUKTURISASI' },
      { title: 'Restrukturisasi', icon: 'ri-refresh-line', href: '/financing/restrukturisasi' },
      { title: 'Top-Up', icon: 'ri-add-circle-line', href: '/financing/top-up' },

      // G7: PENYELESAIAN
      { type: 'subheader', title: 'PENYELESAIAN' },
      { title: 'Settlement', icon: 'ri-hand-coin-line', href: '/financing/settlement' },
      { title: 'Write-Off', icon: 'ri-delete-back-2-line', href: '/financing/write-off' },
      { title: 'Yield', icon: 'ri-percent-line', href: '/financing/yield' },

      // G8: PERFORMANCE
      { type: 'subheader', title: 'PERFORMANCE' },
      { title: 'Repayment Rate', icon: 'ri-speed-up-line', href: '/financing/repayment-rate' },
    ]
  },

  // ========== NASABAH (CIF) ==========
  { 
    title: 'Nasabah (CIF)', 
    subtitle: 'Customer Data', 
    icon: 'ri-user-search-line', 
    value: 'cif',
    children: [
      { title: 'Overview', icon: 'ri-dashboard-3-line', href: '/cif' },
      
      { type: 'subheader', title: 'PENGECEKAN DATA' },
      { title: 'CIF Pembiayaan', icon: 'ri-bank-line', href: '/cif/pembiayaan' },
      { title: 'CIF Tabungan', icon: 'ri-wallet-3-line', href: '/cif/tabungan' },
      { title: 'CIF Deposito', icon: 'ri-safe-2-line', href: '/cif/deposito' },
      
      { type: 'subheader', title: 'ANALITIK' },
      { title: 'Rekapitulasi', icon: 'ri-bar-chart-grouped-line', href: '/cif/rekapitulasi' },
      { title: 'Quality Audit', icon: 'ri-shield-check-line', href: '/cif/quality' },
    ]
  },

  // ========== FUNDING ==========
  { title: 'Funding', subtitle: 'Tabungan & Deposito', icon: 'ri-safe-2-line', href: '/funding' },

  // ========== LAPORAN ==========
  { title: 'Laporan', subtitle: 'Reporting Module', icon: 'ri-file-chart-2-line', href: '/reporting' },

  // ========== ADMINISTRATOR ==========
  { 
    title: 'Administrator', 
    subtitle: 'Control Center', 
    icon: 'ri-shield-user-line', 
    value: 'administrator',
    children: [
      { type: 'subheader', title: 'ADMINISTRATOR' },
      { title: 'Data Management', icon: 'ri-settings-5-line', href: '/admin/management' },
    ]
  },
]


// Open Groups State (Vuetify 3 uses array of values on VList)
const openedGroups = ref([])

function isActive(href, exact = false) {
  if (!href) return false
  if (exact || href === '/') return currentPath.value === href
  return currentPath.value.startsWith(href)
}

function isParentActive(item) {
  if (!item.children) return isActive(item.href)
  return item.children.some(child => child.href && isActive(child.href, true))
}

function toggleTheme() {
  isDark.value = !isDark.value
  theme.global.name.value = isDark.value ? 'dark' : 'light'
  localStorage.setItem('mci-theme', theme.global.name.value)
}

// Auto-expand logic
onMounted(() => {
  const saved = localStorage.getItem('mci-theme')
  if (saved) {
    theme.global.name.value = saved
    isDark.value = saved === 'dark'
  }

  // Find active group and expand it
  navItems.forEach(item => {
    if (item.children && isParentActive(item)) {
      if (!openedGroups.value.includes(item.value)) {
        openedGroups.value.push(item.value)
      }
    }
  })
})

function navigate(href) {
  window.location.href = href
}
</script>

<template>
  <VApp :theme="isDark ? 'dark' : 'light'">

    <!-- ==================== NAVIGATION DRAWER ==================== -->
    <VNavigationDrawer
      v-model:rail="rail"
      :rail-width="64"
      :width="256"
      permanent
      color="surface"
      border="e"
    >
      <!-- Logo -->
      <div
        class="d-flex align-center justify-center px-3 py-4"
        style="min-height: 68px; border-bottom: 1px solid rgba(var(--v-border-color), var(--v-border-opacity));"
      >
        <img
          :src="rail ? headLogo : logoMci"
          alt="MCI"
          :style="{
            height: rail ? '32px' : '42px',
            width: 'auto',
            objectFit: 'contain',
            transition: 'all 0.2s ease',
          }"
        />
      </div>

      <!-- Nav Items -->
      <VList v-model:opened="openedGroups" nav density="compact" class="px-2 pt-3">
        <template v-for="item in navItems" :key="item.title">
          
          <!-- Item dengan children -> VListGroup -->
          <VListGroup v-if="item.children" :value="item.value">
            <template #activator="{ props: groupProps }">
              <VTooltip :disabled="!rail" location="end">
                <template #activator="{ props: tooltipProps }">
                  <VListItem
                    v-bind="mergeProps(groupProps, tooltipProps)"
                    :active="isParentActive(item)"
                    color="primary"
                    rounded="xl"
                    class="mb-1"
                  >
                    <template #prepend>
                      <VIcon
                        :icon="item.icon"
                        size="20"
                        :color="isParentActive(item) ? 'primary' : undefined"
                      />
                    </template>
                    <VListItemTitle class="font-weight-semibold" style="font-size: 13.5px;">
                      {{ item.title }}
                    </VListItemTitle>
                    <VListItemSubtitle style="font-size: 10.5px; opacity: 0.6;">
                      {{ item.subtitle }}
                    </VListItemSubtitle>
                  </VListItem>
                </template>
                <span>{{ item.title }}</span>
              </VTooltip>
            </template>

            <!-- Children Items (Hidden in Rail mode to fix bleeding) -->
            <template v-if="!rail">
              <template v-for="child in item.children" :key="child.title">
                
                <!-- Subheader -->
                <VListSubheader 
                  v-if="child.type === 'subheader'" 
                  class="text-uppercase font-weight-bold text-slate-500 mt-2 mb-1"
                  style="font-size: 9px !important; height: 24px; padding-inline-start: 16px !important;"
                >
                  {{ child.title }}
                </VListSubheader>

                <!-- Regular Child Item -->
                <VListItem
                  v-else
                  :key="child.href"
                  :value="child.href"
                  :active="isActive(child.href, true)"
                  color="primary"
                  rounded="xl"
                  class="mb-1 ms-3"
                  @click="navigate(child.href)"
                >
                  <template #prepend>
                    <VIcon
                      :icon="child.icon"
                      size="18"
                      :color="isActive(child.href, true) ? 'primary' : undefined"
                    />
                  </template>
                  <VListItemTitle class="font-weight-medium" style="font-size: 12.5px;">
                    {{ child.title }}
                  </VListItemTitle>
                </VListItem>
              </template>
            </template>
          </VListGroup>

          <!-- Item tanpa children -> VListItem biasa -->
          <VTooltip v-else :disabled="!rail" :text="item.title" location="end">
            <template #activator="{ props: tp }">
              <VListItem
                v-bind="tp"
                :value="item.href"
                :active="isActive(item.href)"
                color="primary"
                rounded="xl"
                class="mb-1"
                @click="navigate(item.href)"
              >
                <template #prepend>
                  <VIcon
                    :icon="item.icon"
                    size="20"
                    :color="isActive(item.href) ? 'primary' : undefined"
                  />
                </template>
                <VListItemTitle class="font-weight-semibold" style="font-size: 13.5px;">
                  {{ item.title }}
                </VListItemTitle>
                <VListItemSubtitle style="font-size: 10.5px; opacity: 0.6;">
                  {{ item.subtitle }}
                </VListItemSubtitle>
              </VListItem>
            </template>
          </VTooltip>
        </template>
      </VList>

      <div class="flex-grow-1" />

      <!-- DB Status -->
      <transition name="fade">
        <div
          v-if="!rail"
          class="mx-3 mb-3 px-3 py-3 rounded-xl"
          style="background: rgba(var(--v-theme-primary), 0.1); border: 1px solid rgba(var(--v-theme-primary), 0.2);"
        >
          <div class="d-flex align-center gap-2 mb-1">
            <div style="width: 7px; height: 7px; border-radius: 50%; background: #10B981; animation: pulse 2s infinite; flex-shrink: 0;" />
            <span style="font-size: 9px; font-weight: 700; letter-spacing: 1px; text-transform: uppercase; color: rgb(var(--v-theme-primary));">
              Database Aktif
            </span>
          </div>
          <div style="font-size: 11px; font-weight: 700; color: rgb(var(--v-theme-secondary));" class="text-truncate">
            {{ page.props.mci?.active_db || 'N/A' }}
          </div>
          <div style="font-size: 10px; opacity: 0.55;" class="mt-1">
            Per {{ page.props.mci?.period || 'N/A' }}
          </div>
        </div>
      </transition>

      <VDivider />
      <VList nav density="compact" class="py-2 px-2">
        <VListItem
          :prepend-icon="rail ? 'ri-arrow-right-s-line' : 'ri-arrow-left-s-line'"
          :title="rail ? '' : 'Sembunyikan'"
          rounded="xl"
          style="font-size: 13px; opacity: 0.65;"
          @click="rail = !rail"
        />
      </VList>
    </VNavigationDrawer>

    <!-- ==================== APP BAR ==================== -->
    <VAppBar elevation="0" border="b" color="surface" height="64">
      <VAppBarTitle>
        <div class="d-flex align-center gap-2">
          <VIcon icon="ri-building-4-line" color="primary" size="18" />
          <span style="font-size: 14px; font-weight: 500; opacity: 0.8; font-family: 'Plus Jakarta Sans', sans-serif;">
            BPRS HIK MCI - Sistem Monitoring Keuangan
          </span>
        </div>
      </VAppBarTitle>

      <template #append>
        <div class="d-flex align-center gap-1 pe-4">
          <VBtn
            :icon="isDark ? 'ri-sun-line' : 'ri-moon-line'"
            variant="text"
            size="small"
            :color="isDark ? 'warning' : 'primary'"
            @click="toggleTheme"
          >
            <VTooltip activator="parent" location="bottom">
              {{ isDark ? 'Mode Terang' : 'Mode Gelap' }}
            </VTooltip>
          </VBtn>

          <VBtn icon variant="text" size="small">
            <VIcon icon="ri-notification-3-line" size="20" />
            <VBadge color="error" content="3" floating />
            <VTooltip activator="parent" location="bottom">Notifikasi</VTooltip>
          </VBtn>

          <VDivider vertical class="mx-2" style="height: 26px;" />

          <div class="d-flex align-center gap-2 cursor-pointer">
            <VAvatar size="34" color="primary" variant="tonal" style="font-weight: 700; font-size: 12px;">
              DIR
            </VAvatar>
            <div class="d-none d-sm-block">
              <div style="font-size: 13px; font-weight: 600; line-height: 1.3;">Directeur</div>
              <div style="font-size: 10px; opacity: 0.55;">Administrator</div>
            </div>
          </div>
        </div>
      </template>
    </VAppBar>

    <!-- ==================== MAIN CONTENT ==================== -->
    <VMain>
      <div style="padding: 20px 24px;">
        <slot />
      </div>
    </VMain>

  </VApp>
</template>

<style>
@keyframes pulse {
  0%, 100% { opacity: 1; transform: scale(1); }
  50% { opacity: 0.5; transform: scale(1.4); }
}
.fade-enter-active, .fade-leave-active { transition: opacity 0.2s ease; }
.fade-enter-from, .fade-leave-to { opacity: 0; }
.v-list-item-title { color: rgb(var(--v-theme-on-surface)) !important; opacity: 1 !important; }
.v-card-title { color: rgb(var(--v-theme-on-surface)) !important; opacity: 1 !important; }
.v-card-subtitle { opacity: 0.72 !important; }
.text-high-emphasis { color: rgb(var(--v-theme-on-surface)) !important; opacity: 0.95 !important; }
.text-medium-emphasis { color: rgb(var(--v-theme-on-surface)) !important; opacity: 0.70 !important; }
</style>
