<template>
  <span class="dropdown-item" @click="onClick($event)">
    <div v-if="isMedia" class="media">
      <slot name="media" />
      <div class="media-body">
        <slot name="media-body" />
      </div>
    </div>
    <slot />
  </span>
</template>

<script>
export default {
  name: 'LDropdownItem',
  props: {
    isMedia: {
      type: Boolean,
      default: false
    }
  },
  methods: {
    onClick (event) {
      this.$emit('click', event)
    }
  }
}
</script>
