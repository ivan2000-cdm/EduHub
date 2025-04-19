<template>
  <nuxt-link v-if="link !== '#'" :to="link" class="dropdown-item dropdown-footer">
    <slot />
  </nuxt-link>
  <span v-else class="dropdown-item dropdown-footer" @click="onClick($event)">
    <slot />
  </span>
</template>

<script>
export default {
  name: 'LDropdownFooter',
  props: {
    link: {
      type: String,
      default: '#'
    }
  },
  methods: {
    onClick (event) {
      this.$emit('click', event)
    }
  }
}
</script>
