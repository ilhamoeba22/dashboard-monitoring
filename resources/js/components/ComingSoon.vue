<script setup>
import DefaultLayout from '@/layouts/default.vue'
import { Head } from '@inertiajs/vue3'

defineOptions({ layout: DefaultLayout })

const props = defineProps({
  title: { type: String, required: true },
  subtitle: { type: String, default: '' },
  icon: { type: String, default: 'ri-tools-line' },
  color: { type: String, default: 'primary' },
  group: { type: String, default: '' },
})
</script>

<template>
  <div class="coming-soon-page">
    <Head :title="title" />

    <!-- Breadcrumb -->
    <div class="text-caption text-medium-emphasis mb-6 d-flex align-center gap-1" v-if="group">
      <v-icon icon="ri-home-3-line" size="12" />
      <span>Pembiayaan</span>
      <v-icon icon="ri-arrow-right-s-line" size="12" />
      <span>{{ group }}</span>
      <v-icon icon="ri-arrow-right-s-line" size="12" />
      <span class="font-weight-bold">{{ title }}</span>
    </div>

    <div class="coming-soon-container">
      <!-- Glow Background -->
      <div class="glow-bg" />

      <!-- Icon Badge -->
      <div class="icon-ring">
        <div class="icon-inner" :class="`bg-${color}-subtle`">
          <v-icon :icon="icon" size="52" :color="color" />
        </div>
      </div>

      <!-- Text -->
      <h1 class="coming-soon-title">{{ title }}</h1>
      <p class="coming-soon-subtitle" v-if="subtitle">{{ subtitle }}</p>
      <p class="coming-soon-subtitle" v-else>
        Halaman ini sedang dalam tahap pengembangan dan akan segera tersedia.
      </p>

      <!-- Badge -->
      <v-chip
        variant="tonal"
        :color="color"
        size="small"
        prepend-icon="ri-time-line"
        class="font-weight-bold text-none mt-2"
      >
        SEGERA HADIR
      </v-chip>

      <!-- Divider -->
      <div class="divider-line" />

      <!-- Info chips -->
      <div class="d-flex flex-wrap justify-center gap-2">
        <v-chip size="x-small" variant="outlined" color="grey" prepend-icon="ri-code-s-slash-line">
          Direncanakan
        </v-chip>
        <v-chip size="x-small" variant="outlined" color="grey" prepend-icon="ri-database-2-line">
          Integrasi SQL Server
        </v-chip>
        <v-chip size="x-small" variant="outlined" color="grey" prepend-icon="ri-bar-chart-2-line">
          Visualisasi Data
        </v-chip>
      </div>
    </div>
  </div>
</template>

<style scoped>
.coming-soon-page {
  max-width: 1200px;
  margin: 0 auto;
}

.coming-soon-container {
  position: relative;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  min-height: 520px;
  padding: 60px 24px;
  background: rgba(var(--v-theme-surface-variant), 0.35);
  border: 1px solid rgba(var(--v-border-color), var(--v-border-opacity));
  border-radius: 24px;
  overflow: hidden;
  text-align: center;
}

.glow-bg {
  position: absolute;
  width: 320px;
  height: 320px;
  border-radius: 50%;
  background: radial-gradient(circle, rgba(var(--v-theme-primary), 0.08) 0%, transparent 70%);
  pointer-events: none;
}

.icon-ring {
  width: 112px;
  height: 112px;
  border-radius: 50%;
  background: rgba(var(--v-theme-primary), 0.06);
  display: flex;
  align-items: center;
  justify-content: center;
  margin-bottom: 24px;
  border: 1.5px solid rgba(var(--v-theme-primary), 0.12);
}

.icon-inner {
  width: 84px;
  height: 84px;
  border-radius: 50%;
  background: rgba(var(--v-theme-primary), 0.1);
  display: flex;
  align-items: center;
  justify-content: center;
}

.coming-soon-title {
  font-family: 'Plus Jakarta Sans', sans-serif;
  font-size: 2rem;
  font-weight: 800;
  letter-spacing: -0.5px;
  margin: 0 0 10px;
  color: rgb(var(--v-theme-on-surface));
}

.coming-soon-subtitle {
  font-size: 0.9rem;
  color: rgb(var(--v-theme-on-surface));
  opacity: 0.55;
  max-width: 400px;
  line-height: 1.6;
  margin: 0 0 16px;
}

.divider-line {
  width: 48px;
  height: 2px;
  border-radius: 2px;
  background: rgba(var(--v-theme-primary), 0.25);
  margin: 20px 0 16px;
}
</style>
