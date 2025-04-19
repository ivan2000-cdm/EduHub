<template>
  <li
    ref="item"
    v-click-outside="closeDropdown"
    :class="{
      'nav-item': true,
      'dropdown': true,
      'show': open
    }"
    @click="openDropdown"
  >
    <span class="nav-link" style="cursor: pointer">
      <i :class="icon" />
      <span
        v-if="Object.keys(badge).length > 0"
        :class="[
          'badge',
          `badge-${badge.color ? badge.color : 'primary'}`,
          'navbar-badge'
        ]"
      >
        {{ badge.count }}
      </span>
    </span>
    <fade-transition>
      <div
        v-show="open"
        :class="[
          'dropdown-menu',
          `dropdown-menu-${dropdown.size || 'lg'}`,
          `dropdown-menu-${dropdown.position || 'right'}`,
          open && 'show'
        ]"
      >
        <slot />
      </div>
    </fade-transition>
  </li>
</template>

<script>

import { FadeTransition } from 'vue2-transitions'

export default {
  name: 'LMenuItemDropdown',
  components: {
    FadeTransition
  },
  props: {
    icon: {
      type: String,
      default: ''
    },
    badge: {
      type: Object,
      default: () => ({})
    },
    dropdown: {
      type: Object,
      default: () => ({})
    }
  },
  data: () => ({
    open: false
  }),
  methods: {
    closeDropdown () {
      this.open = false
    },
    openDropdown () {
      this.open = true
    }
  }
}
</script>

<style lang="scss" scoped>

</style>
