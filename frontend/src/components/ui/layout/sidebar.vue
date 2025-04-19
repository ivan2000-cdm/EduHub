<template>
  <aside class="main-sidebar sidebar-dark-primary elevation-4" style="z-index: 9999;">
    <slot name="brand" />
    <div class="sidebar">
      <div
        v-if="$slots.user"
        class="user-panel mt-3 pb-3 mb-3 d-flex"
      >
        <slot name="user" />
      </div>
      <nav class="mt-2">
        <slot name="menu" />
      </nav>
    </div>
  </aside>
</template>

<script>
export default {
  name: 'LSidebar',
  props: {
    openedItemMenu: {
      type: [Object, Array],
      default: () => []
    }
  },
  data: () => ({
  })
}
</script>

<style lang="scss" scoped>
.brand-link {
  display: flex;
  align-items: center;
}
.sidebar {
  overflow-y: hidden;
  padding: 0 !important
}
</style>
