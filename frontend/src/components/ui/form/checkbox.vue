<template>
  <div>
    <div
      :class="['checkbox', `icheck-${color}`, 'd-inline']"
    >
      <input
        ref="checkbox"
        type="checkbox"
        :value="value"
        :disabled="disabled"
        :checked="isChecked"
        @change="handleChange($event)"
      >
      <label
        class="checkbox"
        :style="labelStyles"
        @click="fireEvent"
      >
        <slot :label="label">
          {{ label }}
        </slot>
      </label>
    </div>
  </div>
</template>

<script>
export default {
  name: 'LCheckbox',
  props: {
    name: {
      type: String,
      default: ''
    },
    labelStyles: {
      type: [Object, String],
      default: ''
    },
    label: {
      type: String,
      default: ''
    },
    value: {
      type: [Number, Boolean, String, Array],
      default: false
    },
    disabled: {
      type: Boolean,
      default: false
    },
    color: {
      type: String,
      default: 'primary',
      validator: value => ['primary', 'success', 'danger'].includes(value)
    },
    modelValue: {
      type: [Number, Boolean, String, Array],
      default: false
    },
    indeterminate: {
      type: Boolean,
      default: false
    }
  },
  data() {
    return {
      trueValue: true,
      falseValue: false
    }
  },
  computed: {
    isChecked() {
      if (Array.isArray(this.modelValue)) {
        return this.modelValue.includes(this.value)
      }
      return this.modelValue === this.trueValue
    }
  },
  methods: {
    handleChange(event) {
      const targetChecked = event.target.checked
      if (Array.isArray(this.modelValue)) {
        const newValue = [...this.modelValue]
        targetChecked
          ? newValue.push(this.value)
          : newValue.splice(newValue.indexOf(this.value), 1)
        this.$emit('update:modelValue', newValue)
      } else if (this.modelValue !== null && typeof this.modelValue === 'object') {
        this.$emit('update:modelValue', this.modelValue)
      } else {
        this.$emit('update:modelValue', targetChecked ? this.trueValue : this.falseValue)
      }
    },
    fireEvent() {
      this.$refs.checkbox.checked = !this.$refs.checkbox.checked
      this.$refs.checkbox.dispatchEvent(new Event('change'))
    }
  },
  watch: {
    indeterminate(newValue) {
      if (this.$refs.checkbox) {
        this.$refs.checkbox.indeterminate = newValue
      }
    }
  },
  mounted() {
    if (this.$refs.checkbox) {
      this.$refs.checkbox.indeterminate = this.indeterminate
    }
  }
}
</script>

<style lang="scss" scoped>
input {
  &:indeterminate {
    + label {
      &:before {
        content: '\f068';
        font-family: "Font Awesome 5 Pro",serif;
        font-weight: 900;
        text-align: center;
        color: #fff;
        background-color: var(--secondary);
        border-color: transparent;
      }
    }
  }
}

.checkbox {
  margin-bottom: 20px;
}
</style>
