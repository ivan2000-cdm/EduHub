<template>
  <fade-transition :duration="500">
    <div
      v-show="isActive"
      :class="{
        'tab-pane': true,
        'fade': true,
        'active': isActive,
        'show': isActive
      }"
    >
      <slot />
    </div>
  </fade-transition>
</template>

<script>
import { FadeTransition } from 'vue2-transitions'

export default {
  name: 'LTab',
  components: {
    FadeTransition
  },
  props: {
    name: {
      type: String,
      default: ''
    },
    selected: {
      type: Boolean,
      default: false
    },
    disabled: {
      type: Boolean,
      default: false
    }
  },
  data () {
    return {
      isActive: this.selected || false
    }
  },
  watch: {
    isActive () {
      this.$emit('onActive', this.isActive, this.name)
    }
  }
}
</script>

<style lang="scss" scoped>

</style>
