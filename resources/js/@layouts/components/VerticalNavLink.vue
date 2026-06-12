<script setup>
const props = defineProps({
  item: {
    type: null,
    required: true,
  },
})
</script>

<template>
  <li
    class="nav-link"
    :class="{ disabled: item.disable }"
  >
    <Component
      :is="item.to ? 'RouterLink' : 'a'"
      :to="item.to"
      :href="item.href"
      :target="item.target"
    >
      <VIcon
        :icon="item.icon || 'ri-checkbox-blank-circle-line'"
        class="nav-item-icon"
      />
      <!-- 👉 Title -->
      <span class="nav-item-title">
        {{ item.title }}
      </span>
      <span
        class="nav-item-badge"
        :class="item.badgeClass"
      >
        {{ item.badgeContent }}
      </span>
    </Component>
  </li>
</template>

<style lang="scss">
.layout-vertical-nav {
  .nav-link a {
    display: flex;
    align-items: center;
    cursor: pointer;
    min-block-size: 38px;
    padding-inline: 0.65rem 0.5rem;
    gap: 0.55rem;
    border-radius: 0.875rem;
  }

  .nav-link .nav-item-icon {
    flex: 0 0 1.125rem;
    inline-size: 1.125rem;
    font-size: 1.125rem;
  }

  .nav-link .nav-item-title {
    overflow: hidden;
    font-size: 0.7625rem;
    line-height: 1.25;
    text-overflow: ellipsis;
    white-space: nowrap;
  }
}
</style>
