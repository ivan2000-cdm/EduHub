<template>
  <div v-show="selected">
    <slot />
  </div>
</template>

<script>
export default {
  name: 'Step',
  props: {
    name: {
      type: [String, null],
      required: true,
      default: null
    },
    label: {
      type: [String, null],
      required: true,
      default: null
    },
    value: {
      type: undefined,
      default: null
    },
    orderNum: {
      type: Number,
      default: null
    },
    validator: {
      type: Function,
      default: () => true
    },
    valid: {
      type: Boolean,
      default: true
    },
    selected: {
      type: Boolean,
      default: false
    },
    successes: {
      type: Boolean,
      default: false
    },
    disabled: {
      type: Boolean,
      default: false
    }
  },
  watch: {
    value (val) {
      if (this.validator(val)) {
        this.$emit('valid', this.name)
      } else {
        this.$emit('invalid', this.name)
      }
    }
  }
}
</script>

<style scoped>

</style>
