<template>
  <div class="d-flex w-100 h-100 align-items-center">
    <l-textarea
      v-model="valueLocal"
      :autosize="autosize"
      :disabled="disabled"
      :label="label"
      :max-height="maxHeight"
      :min-height="minHeight"
      :placeholder="placeholder"
      :required="required"
      :rows="rows"
      :validate="validate"
      class="w-100"
      @input="input"
    />
    <div
      v-show="isEdit"
      class="ml-2"
    >
      <l-button
        class="w-100 mb-2"
        color="success"
        icon="fa fa-check"
        @click="commit"
      />
      <l-button
        class="w-100"
        color="danger"
        icon="fa fa-times"
        @click="cancel"
      />
    </div>
  </div>
</template>

<script>
export default {
  name: 'LTextareaCommitChanges',
  props: {
    modelValue: {
      type: String,
      default: ''
    },
    label: {
      type: String,
      default: ''
    },
    placeholder: {
      type: String,
      default: ''
    },
    // Оформление при валидации
    validate: {
      type: String,
      default: '',
      validator: value => ['', 'valid', 'warning', 'invalid'].includes(value)
    },
    disabled: {
      type: Boolean,
      default: false
    },
    required: {
      type: Boolean,
      default: false
    },
    minHeight: {
      type: Number,
      default: null
    },
    maxHeight: {
      type: Number,
      default: null
    },
    autosize: {
      type: Boolean,
      default: true
    },
    rows: {
      type: Number,
      default: 1
    }
  },
  data () {
    return {
      valueLocal: this.modelValue,
      valueDefault: this.modelValue
    }
  },
  computed: {
    isEdit () {
      return this.valueDefault !== this.valueLocal
    }
  },
  methods: {
    commit () {
      this.valueDefault = this.valueLocal
      this.$emit('update:modelValue', this.valueLocal) // Используем 'update:modelValue'
    },
    cancel () {
      this.valueLocal = this.valueDefault
      this.$emit('cancel', this.valueLocal)
    },
    input (value) {
      this.$emit('input', value)
    }
  }
}
</script>

<style scoped>

</style>
