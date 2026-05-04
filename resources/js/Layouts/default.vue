<script setup>
import { ref, computed, onMounted, mergeProps } from 'vue'
import { usePage } from '@inertiajs/vue3'
import { useTheme } from 'vuetify'
import logoMci from '@/assets/images/logos/logo_mci.png'

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
  { 
    title: 'Pembiayaan', 
    subtitle: 'Financing Module', 
    icon: 'ri-bank-line', 
    value: 'pembiayaan',
    children: [
      { title: 'Overview', icon: 'ri-pie-chart-2-line', href: '/financing' },
      { title: 'Nominatif', icon: 'ri-list-unordered', href: '/financing/nominatif' },
      { title: 'Rekapitulasi', icon: 'ri-bar-chart-2-line', href: '/financing/rekapitulasi' },
    ]
  },
  { title: 'Nasabah (CIF)', subtitle: 'Customer Data', icon: 'ri-user-star-line', href: '/cif' },
  { title: 'Funding', subtitle: 'Tabungan & Deposito', icon: 'ri-safe-2-line', href: '/funding' },
  { title: 'Laporan', subtitle: 'Reporting Module', icon: 'ri-file-chart-2-line', href: '/reporting' },
]

// Open Groups State (Vuetify 3 uses array of values on VList)
const openedGroups = ref([])

function isActive(href) {
  if (href === '/') return currentPath.value === '/'
  return currentPath.value.startsWith(href)
}

function isParentActive(item) {
  if (!item.children) return isActive(item.href)
  return item.children.some(child => isActive(child.href))
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
          :src="logoMci"
          alt="MCI"
          :style="{
            height: rail ? '36px' : '42px',
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

            <!-- Children Items -->
            <VListItem
              v-for="child in item.children"
              :key="child.href"
              :value="child.href"
              :active="isActive(child.href)"
              color="primary"
              rounded="xl"
              class="mb-1 ms-3"
              @click="navigate(child.href)"
            >
              <template #prepend>
                <VIcon
                  :icon="child.icon"
                  size="18"
                  :color="isActive(child.href) ? 'primary' : undefined"
                />
              </template>
              <VListItemTitle class="font-weight-medium" style="font-size: 12.5px;">
                {{ child.title }}
              </VListItemTitle>
            </VListItem>
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
            BPRS HIK MCI — Sistem Monitoring Keuangan
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
