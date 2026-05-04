<script setup>
import { ref, onMounted } from 'vue'
import DefaultLayout from '@/layouts/default.vue'

defineOptions({ layout: DefaultLayout })



const activeTab = ref('pembiayaan')
const loading = ref(true)
const error = ref(null)
const all = ref(null) // full API response: { financing, saving, deposito, periode }

async function load() {
  loading.value = true
  error.value = null
  try {
    const r = await fetch('/api/v1/dashboard/metrics')
    if (!r.ok) throw new Error(`HTTP ${r.status}`)
    const j = await r.json()
    all.value = j.data
  } catch (e) {
    error.value = e.message
  } finally {
    loading.value = false
  }
}

onMounted(load)

// ── Helpers ──────────────────────────────────────────────────────
function fmt(v) {
  if (v == null) return '—'
  const n = parseFloat(v)
  if (isNaN(n)) return '—'
  if (n >= 1e12) return `Rp ${(n/1e12).toFixed(3)} T`
  if (n >= 1e9)  return `Rp ${(n/1e9).toFixed(3)} M`
  if (n >= 1e6)  return `Rp ${(n/1e6).toFixed(2)} Jt`
  return `Rp ${n.toLocaleString('id-ID')}`
}
function num(v) { return v != null ? Number(v).toLocaleString('id-ID') : '—' }
function pct(v) { return v != null ? `${parseFloat(v).toFixed(2)}%` : '—' }
function growthColor(g) {
  if (!g) return ''
  if (g.raw > 0) return 'success'
  if (g.raw < 0) return 'error'
  return 'medium-emphasis'
}
function growthIcon(g) {
  if (!g) return 'ri-minus-line'
  if (g.raw > 0) return 'ri-arrow-up-s-fill'
  if (g.raw < 0) return 'ri-arrow-down-s-fill'
  return 'ri-minus-line'
}

// ── Periode ───────────────────────────────────────────────────────
const periodeLabel = computed(() => {
  if (!all.value?.periode) return 'Loading...'
  const p = all.value.periode
  const months = ['','Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agu','Sep','Okt','Nov','Des']
  return `${months[p.month]} ${p.year}`
})

// ── Computed shortcuts ────────────────────────────────────────────
const f = computed(() => all.value?.financing ?? {})
const s = computed(() => all.value?.saving    ?? {})
const d = computed(() => all.value?.deposito  ?? {})
</script>

<script>
import { computed } from 'vue'
</script>

<template>
  <!-- Page Header -->
  <div class="d-flex align-center justify-space-between mb-5">
    <div>
      <div class="d-flex align-center gap-2 mb-1">
        <VIcon icon="ri-dashboard-2-line" color="primary" size="22" />
        <h1 class="text-h5 font-weight-bold mb-0" style="font-family:'Plus Jakarta Sans',sans-serif;">
          Executive Dashboard
        </h1>
      </div>
      <p class="text-body-2 mb-0" style="opacity:.72;">
        BPRS HIK MCI — Periode: <strong>{{ periodeLabel }}</strong> &nbsp;|&nbsp; Database: MCI_MAR26_01042026
      </p>
    </div>
    <div class="d-flex gap-2">
      <VChip v-if="!error" color="success" variant="tonal" size="small" prepend-icon="ri-circle-fill">
        Data Aktif
      </VChip>
      <VChip v-else color="error" variant="tonal" size="small" prepend-icon="ri-error-warning-line">
        Offline
      </VChip>
      <VBtn variant="tonal" color="primary" size="small" prepend-icon="ri-refresh-line" :loading="loading" @click="load">
        Refresh
      </VBtn>
    </div>
  </div>

  <VAlert v-if="error" type="error" variant="tonal" closable class="mb-4"
    :text="`Gagal terhubung ke API: ${error}`" />

  <!-- ============ TABS ============ -->
  <VCard elevation="0" border rounded="xl">
    <VTabs v-model="activeTab" color="primary" class="border-b" height="48">
      <VTab value="manajemen"  style="font-weight:600;font-size:13px;">
        <VIcon icon="ri-bar-chart-grouped-line" size="16" class="me-2"/>Manajemen
      </VTab>
      <VTab value="pembiayaan" style="font-weight:600;font-size:13px;">
        <VIcon icon="ri-bank-line" size="16" class="me-2"/>Pembiayaan
      </VTab>
      <VTab value="tabungan"   style="font-weight:600;font-size:13px;">
        <VIcon icon="ri-piggy-bank-line" size="16" class="me-2"/>Tabungan
      </VTab>
      <VTab value="deposito"   style="font-weight:600;font-size:13px;">
        <VIcon icon="ri-safe-2-line" size="16" class="me-2"/>Deposito
      </VTab>
    </VTabs>

    <VTabsWindow v-model="activeTab">

      <!-- ===== MANAJEMEN ===== -->
      <VTabsWindowItem value="manajemen">
        <VCardText class="pa-5">
          <p class="text-body-2 font-weight-semibold mb-4">Rasio Kesehatan Bank — Regulasi OJK/BI</p>
          <VRow>
            <VCol v-for="item in [
              {label:'NPF Ratio', val: f.total_os > 0 ? pct((f.total_npf/f.total_os)*100) : '—', icon:'ri-error-warning-line', color:'error',   hint:'Target ≤ 5%'},
              {label:'Outstanding OS', val: fmt(f.total_os), icon:'ri-bank-line', color:'primary', hint:'Total pembiayaan aktif'},
              {label:'Outstanding NPF', val: fmt(f.total_npf), icon:'ri-shield-check-line', color:'warning', hint:'Pembiayaan bermasalah'},
              {label:'Total Saldo Tabungan', val: fmt(s.total_saldo), icon:'ri-piggy-bank-line', color:'success', hint:'Dana tabungan'},
              {label:'Total Saldo Deposito', val: fmt(d.total_saldo), icon:'ri-safe-2-line', color:'info', hint:'Dana deposito'},
              {label:'Total Bagi Hasil', val: fmt(d.total_baghas), icon:'ri-money-dollar-circle-line', color:'secondary', hint:'Bagi hasil deposito'},
            ]" :key="item.label" cols="6" sm="4" md="2">
              <VCard elevation="0" border rounded="xl" class="pa-4 text-center"
                :style="`border-top: 3px solid rgb(var(--v-theme-${item.color})) !important;`">
                <VAvatar :color="item.color" variant="tonal" size="38" rounded="lg" class="mb-2">
                  <VIcon :icon="item.icon" size="18" />
                </VAvatar>
                <div v-if="loading"><div class="bg-surface-variant rounded mb-1" style="height:20px;width:80%;margin:auto"/></div>
                <template v-else>
                  <div class="font-weight-bold mb-1" style="font-size:1.1rem;line-height:1.2;font-family:'Plus Jakarta Sans',sans-serif;">
                    {{ item.val }}
                  </div>
                </template>
                <div class="text-caption font-weight-semibold" style="opacity:.7;font-size:10px;">{{ item.label }}</div>
                <div class="text-caption" style="opacity:.45;font-size:9px;">{{ item.hint }}</div>
              </VCard>
            </VCol>
          </VRow>
        </VCardText>
      </VTabsWindowItem>

      <!-- ===== PEMBIAYAAN ===== -->
      <VTabsWindowItem value="pembiayaan">
        <VCardText class="pa-5">
          <!-- KPI Strip -->
          <VRow class="mb-5">
            <VCol v-for="kpi in [
              {label:'Outstanding Pembiayaan', val:()=>fmt(f.total_os),     growth:()=>f.growth,     icon:'ri-bank-line',         color:'primary'},
              {label:'Outstanding NPF',         val:()=>fmt(f.total_npf),    growth:()=>f.npf_growth,  icon:'ri-error-warning-line', color:'warning'},
              {label:'NPF Ratio',               val:()=>f.total_os>0?pct((f.total_npf/f.total_os)*100):'—', growth:null, icon:'ri-percent-line', color:'error'},
              {label:'Jumlah Rekening Aktif',   val:()=>num(f.total_noa),    growth:()=>f.noa_growth,  icon:'ri-file-list-3-line',   color:'info'},
              {label:'Jumlah Marketing Aktif',  val:()=>num(f.total_ao),     growth:()=>f.ao_growth,   icon:'ri-user-star-line',     color:'success'},
            ]" :key="kpi.label" cols="12" sm="6" md="">
              <div :style="`border-left:3px solid rgb(var(--v-theme-${kpi.color}));padding-left:12px;`">
                <div class="d-flex align-center gap-1 mb-1">
                  <VAvatar :color="kpi.color" variant="tonal" size="20" rounded style="flex-shrink:0">
                    <VIcon :icon="kpi.icon" size="11"/>
                  </VAvatar>
                  <span class="text-caption font-weight-semibold" style="opacity:.75;line-height:1.2;">{{ kpi.label }}</span>
                </div>
                <div v-if="loading" class="bg-surface-variant rounded" style="height:22px;width:70%;"/>
                <div v-else>
                  <div class="font-weight-bold" style="font-size:1.05rem;font-family:'Plus Jakarta Sans',sans-serif;">
                    {{ kpi.val() }}
                  </div>
                  <div v-if="kpi.growth && kpi.growth()" class="d-flex align-center gap-1 mt-1">
                    <VIcon :icon="growthIcon(kpi.growth())" :color="growthColor(kpi.growth())" size="14"/>
                    <span class="text-caption font-weight-semibold" :class="`text-${growthColor(kpi.growth())}`">
                      {{ kpi.growth().value }}
                    </span>
                    <span class="text-caption" style="opacity:.5;">MoM</span>
                  </div>
                </div>
              </div>
            </VCol>
          </VRow>

          <VDivider class="mb-5"/>

          <!-- Chart Placeholders (Row 1) -->
          <VRow class="mb-4">
            <VCol cols="12" md="3">
              <p class="text-body-2 font-weight-semibold mb-2">Distribusi Pembiayaan per Produk</p>
              <VCard elevation="0" border rounded="xl" class="d-flex align-center justify-center" style="height:200px;">
                <div class="text-center" style="opacity:.35;">
                  <VIcon icon="ri-donut-chart-line" size="44"/><br/>
                  <span class="text-caption">Chart tersedia setelah API chart aktif</span>
                </div>
              </VCard>
            </VCol>
            <VCol cols="12" md="6">
              <p class="text-body-2 font-weight-semibold mb-2">Total Outstanding & Nominal NPF Bulanan</p>
              <VCard elevation="0" border rounded="xl" class="d-flex align-center justify-center" style="height:200px;">
                <div class="text-center" style="opacity:.35;">
                  <VIcon icon="ri-bar-chart-grouped-line" size="44"/><br/>
                  <span class="text-caption">Bar Chart OS vs NPF</span>
                </div>
              </VCard>
            </VCol>
            <VCol cols="12" md="3">
              <p class="text-body-2 font-weight-semibold mb-2">Top 5 Pembiayaan per AO</p>
              <VCard elevation="0" border rounded="xl" class="d-flex align-center justify-center" style="height:200px;">
                <div class="text-center" style="opacity:.35;">
                  <VIcon icon="ri-bar-chart-horizontal-line" size="44"/><br/>
                  <span class="text-caption">Top AO Chart</span>
                </div>
              </VCard>
            </VCol>
          </VRow>

          <!-- Chart Placeholders (Row 2) -->
          <VRow>
            <VCol cols="12" md="3">
              <p class="text-body-2 font-weight-semibold mb-2">Performa Bulan Ini</p>
              <VCard elevation="0" border rounded="xl" class="d-flex align-center justify-center" style="height:200px;">
                <div class="text-center" style="opacity:.35;">
                  <VIcon icon="ri-bar-chart-2-line" size="44"/><br/>
                  <span class="text-caption">Performa Bulanan</span>
                </div>
              </VCard>
            </VCol>
            <VCol cols="12" md="6">
              <p class="text-body-2 font-weight-semibold mb-2">Pertumbuhan OS & Perubahan NPF (MoM)</p>
              <VCard elevation="0" border rounded="xl" class="d-flex align-center justify-center" style="height:200px;">
                <div class="text-center" style="opacity:.35;">
                  <VIcon icon="ri-line-chart-line" size="44"/><br/>
                  <span class="text-caption">Line Chart Pertumbuhan</span>
                </div>
              </VCard>
            </VCol>
            <VCol cols="12" md="3">
              <p class="text-body-2 font-weight-semibold mb-2">Top 5 Kontribusi NPF</p>
              <VCard elevation="0" border rounded="xl" class="d-flex align-center justify-center" style="height:200px;">
                <div class="text-center" style="opacity:.35;">
                  <VIcon icon="ri-bar-chart-horizontal-line" size="44"/><br/>
                  <span class="text-caption">Top NPF Chart</span>
                </div>
              </VCard>
            </VCol>
          </VRow>
        </VCardText>
      </VTabsWindowItem>

      <!-- ===== TABUNGAN ===== -->
      <VTabsWindowItem value="tabungan">
        <VCardText class="pa-5">
          <VRow class="mb-5">
            <VCol v-for="kpi in [
              {label:'Total Saldo Tabungan', val:()=>fmt(s.total_saldo), growth:()=>s.growth,     icon:'ri-money-dollar-circle-line', color:'primary'},
              {label:'Total NOA Tabungan',   val:()=>num(s.total_noa),   growth:()=>s.noa_growth,  icon:'ri-file-list-3-line',         color:'info'},
              {label:'Total AO Aktif',       val:()=>num(s.total_ao),    growth:()=>s.ao_growth,   icon:'ri-user-star-line',            color:'warning'},
            ]" :key="kpi.label" cols="12" sm="4">
              <div :style="`border-left:3px solid rgb(var(--v-theme-${kpi.color}));padding-left:12px;`">
                <div class="d-flex align-center gap-1 mb-1">
                  <VAvatar :color="kpi.color" variant="tonal" size="20" rounded>
                    <VIcon :icon="kpi.icon" size="11"/>
                  </VAvatar>
                  <span class="text-caption font-weight-semibold" style="opacity:.75;">{{ kpi.label }}</span>
                </div>
                <div v-if="loading" class="bg-surface-variant rounded" style="height:22px;width:70%;"/>
                <template v-else>
                  <div class="font-weight-bold" style="font-size:1.1rem;font-family:'Plus Jakarta Sans',sans-serif;">
                    {{ kpi.val() }}
                  </div>
                  <div v-if="kpi.growth && kpi.growth()" class="d-flex align-center gap-1 mt-1">
                    <VIcon :icon="growthIcon(kpi.growth())" :color="growthColor(kpi.growth())" size="14"/>
                    <span class="text-caption font-weight-semibold" :class="`text-${growthColor(kpi.growth())}`">
                      {{ kpi.growth().value }}
                    </span>
                    <span class="text-caption" style="opacity:.5;">MoM</span>
                  </div>
                </template>
              </div>
            </VCol>
          </VRow>
          <VDivider class="mb-5"/>
          <VRow class="mb-4">
            <VCol cols="12" md="3">
              <p class="text-body-2 font-weight-semibold mb-2">Distribusi Tabungan per Produk</p>
              <VCard elevation="0" border rounded="xl" class="d-flex align-center justify-center" style="height:200px;">
                <div class="text-center" style="opacity:.35;"><VIcon icon="ri-donut-chart-line" size="44"/><br/><span class="text-caption">Pie Chart Produk</span></div>
              </VCard>
            </VCol>
            <VCol cols="12" md="9">
              <p class="text-body-2 font-weight-semibold mb-2">Total Saldo Tabungan & Rekening Aktif Bulanan</p>
              <VCard elevation="0" border rounded="xl" class="d-flex align-center justify-center" style="height:200px;">
                <div class="text-center" style="opacity:.35;"><VIcon icon="ri-bar-chart-grouped-line" size="44"/><br/><span class="text-caption">Bar Chart Saldo & NOA</span></div>
              </VCard>
            </VCol>
          </VRow>
          <VRow>
            <VCol cols="12" md="3">
              <p class="text-body-2 font-weight-semibold mb-2">Top 5 Pencapaian Tabungan</p>
              <VCard elevation="0" border rounded="xl" class="d-flex align-center justify-center" style="height:200px;">
                <div class="text-center" style="opacity:.35;"><VIcon icon="ri-bar-chart-horizontal-line" size="44"/><br/><span class="text-caption">Top 5 AO</span></div>
              </VCard>
            </VCol>
            <VCol cols="12" md="9">
              <p class="text-body-2 font-weight-semibold mb-2">Pertumbuhan Saldo & Rekening Aktif (MoM)</p>
              <VCard elevation="0" border rounded="xl" class="d-flex align-center justify-center" style="height:200px;">
                <div class="text-center" style="opacity:.35;"><VIcon icon="ri-line-chart-line" size="44"/><br/><span class="text-caption">Line Chart Pertumbuhan</span></div>
              </VCard>
            </VCol>
          </VRow>
        </VCardText>
      </VTabsWindowItem>

      <!-- ===== DEPOSITO ===== -->
      <VTabsWindowItem value="deposito">
        <VCardText class="pa-5">
          <VRow class="mb-5">
            <VCol v-for="kpi in [
              {label:'Total Saldo Deposito', val:()=>fmt(d.total_saldo),  growth:()=>d.growth,        icon:'ri-safe-2-line',              color:'primary'},
              {label:'Total Bagi Hasil',     val:()=>fmt(d.total_baghas), growth:()=>d.baghas_growth,  icon:'ri-money-dollar-circle-line', color:'warning'},
              {label:'Total NOA',            val:()=>num(d.total_noa)+' Rekening', growth:()=>d.noa_growth, icon:'ri-file-list-3-line', color:'info'},
              {label:'Total AO Aktif',       val:()=>num(d.total_ao)+' Orang', growth:()=>d.ao_growth, icon:'ri-user-star-line',          color:'success'},
            ]" :key="kpi.label" cols="6" sm="3">
              <div :style="`border-left:3px solid rgb(var(--v-theme-${kpi.color}));padding-left:12px;`">
                <div class="d-flex align-center gap-1 mb-1">
                  <VAvatar :color="kpi.color" variant="tonal" size="20" rounded>
                    <VIcon :icon="kpi.icon" size="11"/>
                  </VAvatar>
                  <span class="text-caption font-weight-semibold" style="opacity:.75;">{{ kpi.label }}</span>
                </div>
                <div v-if="loading" class="bg-surface-variant rounded" style="height:22px;width:70%;"/>
                <template v-else>
                  <div class="font-weight-bold" style="font-size:1.05rem;font-family:'Plus Jakarta Sans',sans-serif;">
                    {{ kpi.val() }}
                  </div>
                  <div v-if="kpi.growth && kpi.growth()" class="d-flex align-center gap-1 mt-1">
                    <VIcon :icon="growthIcon(kpi.growth())" :color="growthColor(kpi.growth())" size="14"/>
                    <span class="text-caption font-weight-semibold" :class="`text-${growthColor(kpi.growth())}`">
                      {{ kpi.growth().value }}
                    </span>
                    <span class="text-caption" style="opacity:.5;">MoM</span>
                  </div>
                </template>
              </div>
            </VCol>
          </VRow>
          <VDivider class="mb-5"/>
          <VRow class="mb-4">
            <VCol cols="12" md="3">
              <p class="text-body-2 font-weight-semibold mb-2">Distribusi Deposito per Produk</p>
              <VCard elevation="0" border rounded="xl" class="d-flex align-center justify-center" style="height:200px;">
                <div class="text-center" style="opacity:.35;"><VIcon icon="ri-donut-chart-line" size="44"/><br/><span class="text-caption">Pie Chart Produk</span></div>
              </VCard>
            </VCol>
            <VCol cols="12" md="9">
              <p class="text-body-2 font-weight-semibold mb-2">Total Saldo & Total Bagi Hasil Bulanan</p>
              <VCard elevation="0" border rounded="xl" class="d-flex align-center justify-center" style="height:200px;">
                <div class="text-center" style="opacity:.35;"><VIcon icon="ri-bar-chart-grouped-line" size="44"/><br/><span class="text-caption">Bar Chart Saldo & Bagi Hasil</span></div>
              </VCard>
            </VCol>
          </VRow>
          <VRow>
            <VCol cols="12" md="3">
              <p class="text-body-2 font-weight-semibold mb-2">Top 5 Pencapaian Deposito</p>
              <VCard elevation="0" border rounded="xl" class="d-flex align-center justify-center" style="height:200px;">
                <div class="text-center" style="opacity:.35;"><VIcon icon="ri-bar-chart-horizontal-line" size="44"/><br/><span class="text-caption">Top 5 AO</span></div>
              </VCard>
            </VCol>
            <VCol cols="12" md="9">
              <p class="text-body-2 font-weight-semibold mb-2">Pertumbuhan Saldo Deposito & Bagi Hasil (MoM)</p>
              <VCard elevation="0" border rounded="xl" class="d-flex align-center justify-center" style="height:200px;">
                <div class="text-center" style="opacity:.35;"><VIcon icon="ri-line-chart-line" size="44"/><br/><span class="text-caption">Line Chart Pertumbuhan</span></div>
              </VCard>
            </VCol>
          </VRow>
        </VCardText>
      </VTabsWindowItem>

    </VTabsWindow>
  </VCard>
</template>
