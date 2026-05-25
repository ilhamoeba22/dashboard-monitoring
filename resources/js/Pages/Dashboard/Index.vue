<script setup>
import { computed, onMounted, ref } from 'vue'
import { Head } from '@inertiajs/vue3'
import DefaultLayout from '@/layouts/default.vue'
import '@/assets/css/financing-shared.css'
import { formatExactNumber, formatExactRupiah } from '@/utils/money'

defineOptions({ layout: DefaultLayout })

const activeTab = ref('pembiayaan')
const loading = ref(true)
const error = ref(null)
const all = ref(null)

async function load() {
  loading.value = true
  error.value = null

  try {
    const response = await fetch('/api/v1/dashboard/metrics')
    if (!response.ok) throw new Error(`HTTP ${response.status}`)

    const payload = await response.json()
    all.value = payload.data
  } catch (exception) {
    error.value = exception.message
  } finally {
    loading.value = false
  }
}

onMounted(load)

const f = computed(() => all.value?.financing ?? {})
const s = computed(() => all.value?.saving ?? {})
const d = computed(() => all.value?.deposito ?? {})

const periodeLabel = computed(() => {
  if (!all.value?.periode) return 'Memuat periode...'
  const periode = all.value.periode
  const months = ['', 'Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des']
  return `${months[periode.month] ?? '-'} ${periode.year ?? ''}`
})

const fmt = (value) => formatExactRupiah(value, '—')
const num = (value) => formatExactNumber(value, '—')
const pct = (value) => value !== null && value !== undefined ? `${Number(value).toFixed(2)}%` : '—'

const growthColor = (growth) => {
  if (!growth) return 'medium-emphasis'
  if (growth.raw > 0) return 'success'
  if (growth.raw < 0) return 'error'
  return 'medium-emphasis'
}

const growthIcon = (growth) => {
  if (!growth) return 'ri-minus-line'
  if (growth.raw > 0) return 'ri-arrow-up-s-fill'
  if (growth.raw < 0) return 'ri-arrow-down-s-fill'
  return 'ri-minus-line'
}

const managementCards = computed(() => [
  { label: 'NPF Ratio', value: f.value.total_os > 0 ? pct((f.value.total_npf / f.value.total_os) * 100) : '—', icon: 'ri-error-warning-line', variant: 'danger', hint: 'Target ≤ 5%' },
  { label: 'Outstanding OS', value: fmt(f.value.total_os), icon: 'ri-bank-line', variant: 'info', hint: 'Total pembiayaan aktif' },
  { label: 'Outstanding NPF', value: fmt(f.value.total_npf), icon: 'ri-shield-check-line', variant: 'warning', hint: 'Pembiayaan bermasalah' },
  { label: 'Saldo Tabungan', value: fmt(s.value.total_saldo), icon: 'ri-piggy-bank-line', variant: 'success', hint: 'Dana tabungan' },
  { label: 'Saldo Deposito', value: fmt(d.value.total_saldo), icon: 'ri-safe-2-line', variant: 'purple', hint: 'Dana deposito' },
  { label: 'Bagi Hasil', value: fmt(d.value.total_baghas), icon: 'ri-money-dollar-circle-line', variant: 'info', hint: 'Bagi hasil deposito' },
])

const financingCards = computed(() => [
  { label: 'Outstanding Pembiayaan', value: fmt(f.value.total_os), growth: f.value.growth, icon: 'ri-bank-line', variant: 'info', hint: 'Total O/S aktif' },
  { label: 'Outstanding NPF', value: fmt(f.value.total_npf), growth: f.value.npf_growth, icon: 'ri-error-warning-line', variant: 'warning', hint: 'Nominal bermasalah' },
  { label: 'NPF Ratio', value: f.value.total_os > 0 ? pct((f.value.total_npf / f.value.total_os) * 100) : '—', icon: 'ri-percent-line', variant: 'danger', hint: 'Rasio kualitas aset' },
  { label: 'Rekening Aktif', value: num(f.value.total_noa), growth: f.value.noa_growth, icon: 'ri-file-list-3-line', variant: 'purple', hint: 'Total NOA' },
  { label: 'Marketing Aktif', value: num(f.value.total_ao), growth: f.value.ao_growth, icon: 'ri-user-star-line', variant: 'success', hint: 'AO aktif' },
])

const savingCards = computed(() => [
  { label: 'Total Saldo Tabungan', value: fmt(s.value.total_saldo), growth: s.value.growth, icon: 'ri-money-dollar-circle-line', variant: 'info', hint: 'Saldo tabungan' },
  { label: 'Total NOA Tabungan', value: num(s.value.total_noa), growth: s.value.noa_growth, icon: 'ri-file-list-3-line', variant: 'purple', hint: 'Rekening aktif' },
  { label: 'Total AO Aktif', value: num(s.value.total_ao), growth: s.value.ao_growth, icon: 'ri-user-star-line', variant: 'warning', hint: 'Marketing aktif' },
])

const depositCards = computed(() => [
  { label: 'Total Saldo Deposito', value: fmt(d.value.total_saldo), growth: d.value.growth, icon: 'ri-safe-2-line', variant: 'info', hint: 'Saldo deposito' },
  { label: 'Total Bagi Hasil', value: fmt(d.value.total_baghas), growth: d.value.baghas_growth, icon: 'ri-money-dollar-circle-line', variant: 'warning', hint: 'Nominal bagi hasil' },
  { label: 'Total NOA', value: `${num(d.value.total_noa)} Rekening`, growth: d.value.noa_growth, icon: 'ri-file-list-3-line', variant: 'purple', hint: 'Rekening deposito' },
  { label: 'Total AO Aktif', value: `${num(d.value.total_ao)} Orang`, growth: d.value.ao_growth, icon: 'ri-user-star-line', variant: 'success', hint: 'Marketing aktif' },
])

const chartPlaceholders = {
  pembiayaan: [
    { title: 'Distribusi Pembiayaan per Produk', icon: 'ri-donut-chart-line', text: 'Chart produk pembiayaan' },
    { title: 'Total Outstanding & NPF Bulanan', icon: 'ri-bar-chart-grouped-line', text: 'Bar chart OS vs NPF' },
    { title: 'Top 5 Pembiayaan per AO', icon: 'ri-bar-chart-horizontal-line', text: 'Top AO chart' },
    { title: 'Performa Bulan Ini', icon: 'ri-bar-chart-2-line', text: 'Performa bulanan' },
    { title: 'Pertumbuhan OS & NPF', icon: 'ri-line-chart-line', text: 'Line chart pertumbuhan MoM' },
  ],
  tabungan: [
    { title: 'Distribusi Tabungan per Produk', icon: 'ri-donut-chart-line', text: 'Pie chart produk' },
    { title: 'Saldo Tabungan & Rekening Aktif', icon: 'ri-bar-chart-grouped-line', text: 'Bar chart saldo dan NOA' },
    { title: 'Top 5 Pencapaian Tabungan', icon: 'ri-bar-chart-horizontal-line', text: 'Top 5 AO' },
    { title: 'Pertumbuhan Saldo Tabungan', icon: 'ri-line-chart-line', text: 'Line chart pertumbuhan' },
  ],
  deposito: [
    { title: 'Distribusi Deposito per Produk', icon: 'ri-donut-chart-line', text: 'Pie chart produk' },
    { title: 'Saldo & Bagi Hasil Bulanan', icon: 'ri-bar-chart-grouped-line', text: 'Bar chart saldo dan bagi hasil' },
    { title: 'Top 5 Pencapaian Deposito', icon: 'ri-bar-chart-horizontal-line', text: 'Top 5 AO' },
    { title: 'Pertumbuhan Deposito', icon: 'ri-line-chart-line', text: 'Line chart pertumbuhan' },
  ],
}
</script>

<template>
  <div class="fin-page px-4 pt-0">
    <Head title="Executive Dashboard" />

    <div class="fin-hero mb-6">
      <div class="fin-hero__deco"></div>
      <div class="fin-hero__inner">
        <div class="d-flex flex-column flex-md-row justify-space-between align-start align-md-center gap-4">
          <div class="d-flex align-center gap-4">
            <div class="fin-hero__icon fin-icon-blue">
              <v-icon icon="ri-dashboard-2-line" size="26" color="white" />
            </div>
            <div class="fin-hero__meta">
              <h1 class="fin-hero__title">Executive Dashboard</h1>
              <p class="fin-hero__subtitle">Ringkasan performa bank: pembiayaan, tabungan, deposito, dan rasio kesehatan utama.</p>
              <div class="fin-hero__badges">
                <span class="fin-badge fin-badge--primary">Periode {{ periodeLabel }}</span>
                <span class="fin-badge fin-badge--success">BPRS HIK MCI</span>
                <span class="fin-badge" :class="error ? 'fin-badge--danger' : 'fin-badge--info'">
                  {{ error ? 'Offline' : 'Data Aktif' }}
                </span>
              </div>
            </div>
          </div>

          <div class="fin-filter-bar">
            <v-btn
              class="fin-filter-apply"
              prepend-icon="ri-refresh-line"
              rounded="lg"
              :loading="loading"
              @click="load"
            >
              Refresh
            </v-btn>
          </div>
        </div>
      </div>
    </div>

    <v-alert
      v-if="error"
      type="error"
      variant="tonal"
      closable
      class="mb-6"
      :text="`Gagal terhubung ke API: ${error}`"
    />

    <div class="content-card">
      <div class="pa-4 border-b border-slate-100">
        <v-tabs v-model="activeTab" color="primary" density="comfortable">
          <v-tab value="manajemen" class="font-weight-bold text-xs">
            <v-icon icon="ri-bar-chart-grouped-line" size="16" class="mr-2" /> Manajemen
          </v-tab>
          <v-tab value="pembiayaan" class="font-weight-bold text-xs">
            <v-icon icon="ri-bank-line" size="16" class="mr-2" /> Pembiayaan
          </v-tab>
          <v-tab value="tabungan" class="font-weight-bold text-xs">
            <v-icon icon="ri-piggy-bank-line" size="16" class="mr-2" /> Tabungan
          </v-tab>
          <v-tab value="deposito" class="font-weight-bold text-xs">
            <v-icon icon="ri-safe-2-line" size="16" class="mr-2" /> Deposito
          </v-tab>
        </v-tabs>
      </div>

      <v-window v-model="activeTab">
        <v-window-item value="manajemen">
          <div class="content-card__body pa-5">
            <div class="content-card__title mb-1">Rasio Kesehatan Bank</div>
            <div class="content-card__subtitle mb-5">Konsolidasi metrik utama untuk pengambilan keputusan eksekutif.</div>
            <div class="kpi-cards-grid mb-0">
              <div v-for="card in managementCards" :key="card.label" :class="['kpi-card', `kpi-card--${card.variant}`]">
                <div class="kpi-card__accent" :style="`background: var(--fin-${card.variant === 'danger' ? 'danger' : card.variant === 'success' ? 'success' : card.variant === 'warning' ? 'warning' : card.variant === 'purple' ? 'purple' : 'info'});`"></div>
                <div class="kpi-card__inner">
                  <div class="kpi-card__header">
                    <div class="kpi-card__label">{{ card.label }}</div>
                    <div :class="['kpi-card__icon', `fin-icon-${card.variant === 'danger' ? 'red' : card.variant === 'success' ? 'green' : card.variant === 'warning' ? 'amber' : 'blue'}`]">
                      <v-icon :icon="card.icon" />
                    </div>
                  </div>
                  <div v-if="loading" class="bg-surface-variant rounded" style="height: 30px; width: 75%;"></div>
                  <div v-else class="kpi-card__value">{{ card.value }}</div>
                  <div class="kpi-card__sub">{{ card.hint }}</div>
                </div>
              </div>
            </div>
          </div>
        </v-window-item>

        <v-window-item value="pembiayaan">
          <div class="content-card__body pa-5">
            <div class="kpi-cards-grid">
              <div v-for="card in financingCards" :key="card.label" :class="['kpi-card', `kpi-card--${card.variant}`]">
                <div class="kpi-card__accent" :style="`background: var(--fin-${card.variant === 'danger' ? 'danger' : card.variant === 'success' ? 'success' : card.variant === 'warning' ? 'warning' : card.variant === 'purple' ? 'purple' : 'info'});`"></div>
                <div class="kpi-card__inner">
                  <div class="kpi-card__header">
                    <div class="kpi-card__label">{{ card.label }}</div>
                    <div :class="['kpi-card__icon', `fin-icon-${card.variant === 'danger' ? 'red' : card.variant === 'success' ? 'green' : card.variant === 'warning' ? 'amber' : 'blue'}`]">
                      <v-icon :icon="card.icon" />
                    </div>
                  </div>
                  <div v-if="loading" class="bg-surface-variant rounded" style="height: 30px; width: 75%;"></div>
                  <div v-else class="kpi-card__value">{{ card.value }}</div>
                  <div v-if="card.growth" class="kpi-card__status-pill" :class="`text-${growthColor(card.growth)}`">
                    <v-icon :icon="growthIcon(card.growth)" size="14" />
                    {{ card.growth.value }} MoM
                  </div>
                  <div class="kpi-card__sub">{{ card.hint }}</div>
                </div>
              </div>
            </div>

            <v-divider class="mb-6"></v-divider>

            <v-row>
              <v-col v-for="(chart, index) in chartPlaceholders.pembiayaan" :key="chart.title" cols="12" :md="index === 1 || index === 4 ? 6 : 3">
                <div class="content-card h-100">
                  <div class="pa-4 border-b border-slate-100">
                    <div class="content-card__title">{{ chart.title }}</div>
                  </div>
                  <div class="content-card__body d-flex align-center justify-center" style="height: 220px;">
                    <div class="text-center text-medium-emphasis">
                      <v-icon :icon="chart.icon" size="44" />
                      <div class="text-caption mt-2">{{ chart.text }}</div>
                    </div>
                  </div>
                </div>
              </v-col>
            </v-row>
          </div>
        </v-window-item>

        <v-window-item value="tabungan">
          <div class="content-card__body pa-5">
            <div class="kpi-cards-grid">
              <div v-for="card in savingCards" :key="card.label" :class="['kpi-card', `kpi-card--${card.variant}`]">
                <div class="kpi-card__accent" :style="`background: var(--fin-${card.variant === 'success' ? 'success' : card.variant === 'warning' ? 'warning' : card.variant === 'purple' ? 'purple' : 'info'});`"></div>
                <div class="kpi-card__inner">
                  <div class="kpi-card__header">
                    <div class="kpi-card__label">{{ card.label }}</div>
                    <div :class="['kpi-card__icon', `fin-icon-${card.variant === 'success' ? 'green' : card.variant === 'warning' ? 'amber' : 'blue'}`]">
                      <v-icon :icon="card.icon" />
                    </div>
                  </div>
                  <div v-if="loading" class="bg-surface-variant rounded" style="height: 30px; width: 75%;"></div>
                  <div v-else class="kpi-card__value">{{ card.value }}</div>
                  <div v-if="card.growth" class="kpi-card__status-pill" :class="`text-${growthColor(card.growth)}`">
                    <v-icon :icon="growthIcon(card.growth)" size="14" />
                    {{ card.growth.value }} MoM
                  </div>
                  <div class="kpi-card__sub">{{ card.hint }}</div>
                </div>
              </div>
            </div>

            <v-divider class="mb-6"></v-divider>

            <v-row>
              <v-col v-for="(chart, index) in chartPlaceholders.tabungan" :key="chart.title" cols="12" :md="index === 1 || index === 3 ? 9 : 3">
                <div class="content-card h-100">
                  <div class="pa-4 border-b border-slate-100">
                    <div class="content-card__title">{{ chart.title }}</div>
                  </div>
                  <div class="content-card__body d-flex align-center justify-center" style="height: 220px;">
                    <div class="text-center text-medium-emphasis">
                      <v-icon :icon="chart.icon" size="44" />
                      <div class="text-caption mt-2">{{ chart.text }}</div>
                    </div>
                  </div>
                </div>
              </v-col>
            </v-row>
          </div>
        </v-window-item>

        <v-window-item value="deposito">
          <div class="content-card__body pa-5">
            <div class="kpi-cards-grid">
              <div v-for="card in depositCards" :key="card.label" :class="['kpi-card', `kpi-card--${card.variant}`]">
                <div class="kpi-card__accent" :style="`background: var(--fin-${card.variant === 'success' ? 'success' : card.variant === 'warning' ? 'warning' : card.variant === 'purple' ? 'purple' : 'info'});`"></div>
                <div class="kpi-card__inner">
                  <div class="kpi-card__header">
                    <div class="kpi-card__label">{{ card.label }}</div>
                    <div :class="['kpi-card__icon', `fin-icon-${card.variant === 'success' ? 'green' : card.variant === 'warning' ? 'amber' : 'blue'}`]">
                      <v-icon :icon="card.icon" />
                    </div>
                  </div>
                  <div v-if="loading" class="bg-surface-variant rounded" style="height: 30px; width: 75%;"></div>
                  <div v-else class="kpi-card__value">{{ card.value }}</div>
                  <div v-if="card.growth" class="kpi-card__status-pill" :class="`text-${growthColor(card.growth)}`">
                    <v-icon :icon="growthIcon(card.growth)" size="14" />
                    {{ card.growth.value }} MoM
                  </div>
                  <div class="kpi-card__sub">{{ card.hint }}</div>
                </div>
              </div>
            </div>

            <v-divider class="mb-6"></v-divider>

            <v-row>
              <v-col v-for="(chart, index) in chartPlaceholders.deposito" :key="chart.title" cols="12" :md="index === 1 || index === 3 ? 9 : 3">
                <div class="content-card h-100">
                  <div class="pa-4 border-b border-slate-100">
                    <div class="content-card__title">{{ chart.title }}</div>
                  </div>
                  <div class="content-card__body d-flex align-center justify-center" style="height: 220px;">
                    <div class="text-center text-medium-emphasis">
                      <v-icon :icon="chart.icon" size="44" />
                      <div class="text-caption mt-2">{{ chart.text }}</div>
                    </div>
                  </div>
                </div>
              </v-col>
            </v-row>
          </div>
        </v-window-item>
      </v-window>
    </div>
  </div>
</template>
