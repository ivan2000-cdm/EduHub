<template>
  <div
    :class="{
      'linput': true,
      ...linputClass
    }"
  >
    <input
      :id="name"
      ref="input"
      :value="content"
      :name="name"
      :class="linputClass"
      :disabled="disabled"
      :title="content"
      autocomplete="off"
      @change="handleChange"
      @input="handleInput"
      @keyup="handleKeyup"
      @focus="focused = true"
      @blur="focused = false"
      @wheel="onScroll"
      @keypress="handleKeypress"
    >
    <l-loading
      v-show="loading"
      style="position: absolute;right: 35px;bottom: 15px;"
      :style-loading="{width: 0, height: 0}"
      :style-loading-body="{width: 0, height: 0}"
      :style-icon="{padding: 0, 'font-size': '1.5em'}"
    />
    <i
      v-if="clearButton && activeLabel && content !== ''"
      class="linput_icon clear fad fa-times"
      title="Очистить поле"
      @click="inputClear"
    />
    <i
      v-show="!clearButton && ['valid', 'warning', 'invalid'].includes(validation)"
      :class="{
        fa: true,
        'linput_icon': true,
        'fa-check': validation === 'valid',
        'fa-times': validation === 'invalid',
        'fa-question': validation === 'warning'
      }"
    />
    <label
      v-if="label !== ''"
      :for="name"
      :class="{'active': activeLabel}"
      :title="label"
      @click="labelClick"
      @blur="focused = false"
    >
      <span>{{ label }}</span>
      <span v-if="required && !disabled" class="required">*</span>
    </label>
  </div>
</template>

<script>
import LLoading from '../elements/lloading'

export default {
  name: 'LInputNumber',
  components: { LLoading },
  props: {
    name: {
      type: String,
      required: false,
      default: null
    },
    label: {
      type: String,
      default: ''
    },
    value: {
      type: [String, Number],
      default: ''
    },
    required: {
      type: Boolean,
      default: false
    },
    disabled: {
      type: Boolean,
      default: false
    },
    validate: {
      type: String,
      default: '',
      validator: value => ['', 'valid', 'warning', 'invalid'].includes(value)
    },
    clearButton: {
      type: Boolean,
      default: false
    },
    loading: {
      type: Boolean,
      default: false
    },
    type: {
      type: String,
      default: 'float',
      validator: value => ['int', 'float'].includes(value)
    },
    requiredDot: {
      type: Boolean,
      default: false
    },
    sign: {
      type: String,
      default: 'any',
      validator: value => ['any', 'positive', 'negative'].includes(value)
    },
    maxLength: {
      type: Number,
      default: null,
      validator: value => Number.isInteger(value)
    },
    accuracy: {
      type: Number,
      default: 2,
      validator: value => Number.isInteger(value)
    }
  },
  data () {
    return {
      content: this.value,
      prevContent: this.value,
      validation: this.validate,
      focused: false,
      regs: {
        number: /^[+-]?\\d+(\\.\\d+)?$/,
        int: /^[+-]?\\d+?$/,
        positiveInt: /^\\d+?$/,
        negativeInt: /^-\\d+?$/,
        float: /^[+-]?\\d+\\.\\d+$/,
        floatShort: /^[+-]?\\d+(\\.\\d+)?$/,
        positiveFloat: /^\\d+\\.\\d+$/,
        positiveFloatShort: /^\\d+(\\.\\d+)?$/,
        negativeFloat: /^-\\d+\\.\\d+$/,
        negativeFloatShort: /^-\\d+(\\.\\d+)?$/
      }
    }
  },
  computed: {
    linputClass () {
      return {
        'is-valid': this.validation === 'valid',
        'is-warning': this.validation === 'warning',
        'is-invalid': this.validation === 'invalid'
      }
    },
    activeLabel () {
      return this.content !== '' || this.focused
    },
    reg () {
      if (this.type === 'int') {
        if (this.sign === 'positive') {
          return this.regs.positiveInt
        } else if (this.sign === 'negative') {
          return this.regs.negativeInt
        } else if (this.sign === 'any') {
          return this.regs.int
        }
      } else if (this.sign === 'positive') {
        return this.requiredDot
          ? this.regs.positiveFloat
          : this.regs.positiveFloatShort
      } else if (this.sign === 'negative') {
        return this.requiredDot
          ? this.regs.negativeFloat
          : this.regs.negativeFloatShort
      } else if (this.sign === 'any') {
        return this.requiredDot
          ? this.regs.float
          : this.regs.floatShort
      }

      return this.regs.number
    }
  },
  watch: {
    value (newVal) {
      this.content = newVal
    },
    content (newVal, oldVal) {
      if (!Number.isNaN(+oldVal)) {
        this.prevContent = oldVal
      }
    },
    validate () {
      this.validation = this.validate
    },
    validation () {
      if (this.validation !== '') {
        this.$emit(this.validation, this.name)
      }
    }
  },
  mounted () {
    this.focused = !!this.content
  },
  methods: {
    labelClick () {
      this.focused = true
      this.$refs.input.focus()
    },
    checkValidation () {
      const required = this.required ? !!this.content : true
      const mask = this.validationMask ? this.validationMask.test(this.content) : true
      const length = this.maxLength ? this.content.length <= this.maxLength : true
      const number = this.reg.test(String(this.content))

      const result = required && mask && length && number
      this.validation = result ? 'valid' : 'invalid'
      return result
    },
    checkValue () {
      const result = this.checkValidation
        ? this.content
        : this.prevContent

      return this.accuracy && !Number.isNaN(+result) && result !== null
        ? result.toFixed(this.accuracy)
        : result
    },
    onScroll (e) {
      if (this.type.toLowerCase() === 'number') {
        e.preventDefault()
        e.stopPropagation()
      }
    },
    handleChange () {
      this.$emit('update:value', this.checkValue())  // Use update:value instead of change
    },
    handleKeypress () {
      this.$emit('keypress', this.checkValue())
    },
    handleInput () {
      this.$emit('input', this.checkValue())
    },
    handleKeyup (event) {
      this.$emit('keyup', event)
    },
    inputClear () {
      const content = this.type.toLowerCase() === 'number' ? 0 : ''
      this.content = content
      this.$emit('update:value', content)  // Use update:value instead of change
    }
  }
}
</script>

<style lang="scss" scoped>

@mixin validation($color) {
  input {
    border-color: $color;

    &:focus {
      border-color: $color;
    }
  }
  i {
    color: $color
  }
}

.linput {
  position: relative;
  margin-bottom: 1.5rem;

  &.is-valid {
    @include validation(var(--success));
  }

  &.is-warning {
    @include validation(var(--warning));
  }

  &.is-invalid {
    @include validation(var(--danger));
  }

  &_icon {
    position: absolute;
    top: 10px;
    right: 10px;
    transition: all .5s;

    &.clear {
      font-size: 16px;
      color: #000 !important;
      top: 1px !important;
      right: 5px !important;
      padding: 5px;
    }
  }

  input {
    text-overflow: ellipsis;
    white-space: nowrap;
    display: block;
    width: 100%;
    height: 30px;
    padding: .375rem .75rem;
    font-size: 1rem;
    font-weight: 400;
    line-height: 1.5;
    color: #495057;
    background-color: #fff !important;
    background-clip: padding-box;
    border: 1px solid #ced4da;
    // border-radius: .25rem;
    box-shadow: inset 0 0 0 transparent;
    transition: border-color .15s ease-in-out, box-shadow .15s ease-in-out;
    filter: none;

    &[type=number] {
      -moz-appearance: textfield;

      &::-webkit-outer-spin-button, &::-webkit-inner-spin-button {
        -webkit-appearance: none;
        margin: 0;
      }
    }

    &:focus {
      border-color: var(--primary) !important;
    }

    &:disabled {
      cursor: not-allowed;
    }

    &:-webkit-autofill {
      box-shadow: 0 0 0 50px white inset;

      ~ label {
        transform: translateY(-15px);
        font-weight: 500;
        background: #fff;
        font-size: 14px;
      }
    }
  }

  label {
    // transform: translateX(10px) translateY(-26px);
    position: absolute;
    top: 5px;
    left: 10px;
    transition: all .2s linear;
    color: var(--secondary);
    font-size: 14px;
    margin-bottom: 0;
    overflow-x: hidden;
    overflow-y: hidden;
    text-overflow: ellipsis;
    height: 20px;
    width: 90%;
    white-space: nowrap;

    &.active {
      transform: translateY(-15px);
      font-weight: 500;
      font-size: 14px;

      & span {
        background: #fff;
      }
    }
  }
}

@keyframes rotate {
  from {
    transform: rotate(0deg);
  }
  to {
    transform: rotate(360deg);
  }
}
</style>
