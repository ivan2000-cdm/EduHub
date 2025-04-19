<template>
  <div :class="[`icheck-${color}`, 'd-inline']">
    <input
      :name="name"
      type="radio"
      :value="value"
      :disabled="disabled"
      :checked="checked"
      @change="handleChange"
      @click="handleChange"
    >
    <label
      :for="name"
      @click="handleChange"
    >
      {{ label }}
    </label>
  </div>
</template>

<script>
export default {
  name: 'LRadio',
  props: {
    checked: {
      type: Boolean,
      default: false
    },
    name: {
      type: String,
      required: true,
      default: ''
    },
    label: {
      type: String,
      required: true,
      default: ''
    },
    value: {
      type: [String, Number],
      required: true
    },
    disabled: {
      type: Boolean,
      default: false
    },
    color: {
      type: String,
      default: 'primary',
      validator: value => ['primary', 'success', 'danger'].includes(value)
    }
  },
  emits: ['update:checked'],
  methods: {
    handleChange (e) {
      this.$emit('update:checked', e.target.checked)
    }
  }
}
</script>
