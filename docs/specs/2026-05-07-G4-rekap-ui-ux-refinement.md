# Design Spec: Master Rekap UI/UX Refinement (Pro Max)

## Context
Refining the G4 Master Rekap Console UI to eliminate component overlapping and elevate the aesthetics to "Enterprise Pro Max" standards as per MCI Dashboard guidelines.

## Requirements
- Fix `v-btn-toggle` overlap (O/S vs NOA buttons).
- Implement a more premium "Segmented Pill" style for metric and view switchers.
- Improve visual hierarchy and breathing room in the filter header.
- Maintain consistency with Materio Vuetify and Blue/Amber design system.

## Proposed Design (Opsi 1: Segmented Pill)

### 1. Filter Bar Layout
- **Container:** `v-card` with `rounded-xl` and subtle 1px border.
- **Spacing:** `ga-6` (24px) for horizontal gap on desktop.
- **Alignment:** `align-center` to ensure all select inputs and toggles are vertically centered.

### 2. Component Refinements
- **Metric Switcher:**
  - `v-btn-toggle` with `rounded="pill"`.
  - `variant="tonal"` for base state.
  - Custom `selected-class="bg-primary elevation-2"` for the active pill.
  - Icons: `ri-money-dollar-circle-line` (O/S) and `ri-user-follow-line` (NOA).
- **View Mode Switcher:**
  - Same pill style as Metric Switcher.
  - Icons: `ri-table-alt-line` and `ri-bar-chart-2-line`.

### 3. Styles & Polish
- Remove `density="compact"` from toggles to avoid text clipping.
- Use `text-no-wrap` on buttons.
- Add subtle transition `transition: all 0.2s ease`.

## Implementation Plan
1. Update `Rekapitulasi.vue` template for the filter bar section.
2. Apply `rounded-pill` to `v-btn-toggle`.
3. Add icons to buttons.
4. Adjust spacing and grid layout in the header card.
5. Run `npm run build` and verify.
