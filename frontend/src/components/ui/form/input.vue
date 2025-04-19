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
      v-model="content"
      v-mask="inputmask"
      :class="linputClass"
      :disabled="disabled"
      :maxlength="maxLength"
      :title="content"
      :type="type.toLowerCase()"
      autocomplete="off"
      @blur="focused = false"
      @change="handleChange"
      @focus="focused = true"
      @input="handleInput"
      @keypress="handleKeypress"
      @keyup="handleKeyup"
      @wheel="onScroll"
    >
    <!-- @input="handleInput" -->
    <l-loading
      v-show="loading"
      :style-icon="{padding: 0, 'font-size': '1.5em'}"
      :style-loading="{width: 0, height: 0}"
      :style-loading-body="{width: 0, height: 0}"
      style="position: absolute;right: 35px;bottom: 15px;"
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
        'fa-check': displayValidity && validation === 'valid',
        'fa-times': displayValidity && validation === 'invalid',
        'fa-question': displayValidity && validation === 'warning'
      }"
    />
    <label
      v-if="label"
      :title="label"
      :class="{
        'active': activeLabel,
        'flex': true,
        'overflow-hidden': true,
        'w-100': true
      }"
      :for="name"
      @blur="focused = false"
      @click="labelClick"
    >
      <span
        class="overflow-hidden text-nowrap flex-shrink-1"
        style="text-overflow: ellipsis"
      >{{ label }}</span>
      <span
        v-if="required"
        class="required pl-1 mr-3 flex-shrink-0"
        style="flex-basis: 10px;"
      >*</span>
    </label>
  </div>
</template>

<script>
import LLoading from '../elements/lloading.vue'
// import LTable from '@/components/ui/elements/table/table.vue'

export default {
  name: 'LInput',
  components: { LLoading },
  props: {
    // id элемента
    name: {
      type: String,
      required: false,
      default: null
    },
    // Описание
    label: {
      type: String,
      default: ''
    },
    // Тип формы
    type: {
      type: String,
      required: false,
      default: 'text'
    },
    // Текущее значение
    modelValue: {
      type: [String, Number],
      default: ''
    },
    required: {
      type: Boolean,
      default: false
    },
    // Выключение
    disabled: {
      type: Boolean,
      default: false
    },
    // Оформление при валидации
    validate: {
      type: String,
      default: '',
      validator: value => ['', 'valid', 'warning', 'invalid'].includes(value)
    },
    validationMask: {
      type: RegExp,
      default: () => {
        return new RegExp()
      }
    },
    displayValidity: {
      type: Boolean,
      default: true
    },
    clearButton: {
      type: Boolean,
      default: false
    },
    loading: {
      type: Boolean,
      default: false
    },
    maxLength: {
      type: [Number, undefined],
      default: undefined
    },
    mask: {
      type: Object,
      default: () => ({})
    }
  },
  data () {
    return {
      content: this.modelValue,
      validation: this.validate,
      inputmask: this.mask,
      focused: false,
      reg: /^[+-]?\\d+(\\.\\d+)?$/
    }
  },
  computed: {
    linputClass () {
      return {
        'is-valid': this.displayValidity && this.validation === 'valid',
        'is-warning': this.displayValidity && this.validation === 'warning',
        'is-invalid': this.displayValidity && this.validation === 'invalid'
      }
    },
    activeLabel () {
      return this.content !== '' || this.focused
    }
  },
  watch: {
    modelValue(newVal) {
      this.content = newVal
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
      const number = this.type.toLowerCase() === 'number' ? this.reg.test(String(this.content)) : true

      const result = required && mask && number
      this.validation = result ? 'valid' : 'invalid'
      return result
    },
    checkValue () {
      const maxNumber = 9007200000000000
      switch (this.type.toLowerCase()) {
        case 'text':
          return this.content
        case 'number':
          if (this.reg.test(String(this.content))) {
            const floatContent = parseFloat(this.content)
            if (floatContent > maxNumber) {
              this.content = maxNumber
              return maxNumber
            }

            return floatContent
          }
          return this.content
        default:
          return this.content
      }
    },
    onScroll (e) {
      if (this.type.toLowerCase() === 'number') {
        e.preventDefault()
        e.stopPropagation()
      }
    },
    handleChange () {
      this.checkValidation()
      this.$emit('update:modelValue', this.checkValue()) // Используем update:modelValue вместо change
    },
    handleKeypress () {
      this.$emit('keypress', this.checkValue())
    },
    handleInput () {
      this.checkValidation()
      this.$emit('input', this.checkValue())
    },
    handleKeyup (event) {
      this.$emit('keyup', event)
    },
    inputClear () {
      const content = this.type.toLowerCase() === 'number' ? 0 : ''
      this.content = content
      this.$emit('update:modelValue', content) // Используем update:modelValue
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
