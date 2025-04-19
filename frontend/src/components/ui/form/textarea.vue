<template>
  <div :class="{ltextarea: true, ...linputClass}">
    <textarea
      ref="input"
      v-model="content"
      :autosize="autosize"
      :class="linputClass"
      :disabled="disabled"
      :placeholder="placeholder"
      :rows="rows"
      :style="textAreaStyle"
      @blur="focused = false"
      @change="handleChange"
      @focus="focused = true"
      @input="handleInput"
    />
    <label
      v-if="label !== ''"
      :class="{
        'active': activeLabel
      }"
      :title="label"
      @blur="focused = false"
      @click="labelClick"
    >
      <span>{{ label }}</span>
      <span v-if="required && !disabled" class="required">*</span>
    </label>
  </div>
</template>

<script>
export default {
  name: 'LTextarea',
  props: {
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
    value: {
      type: String,
      default: ''
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
      content: this.value,
      validation: this.validate,
      focused: false,
      height: 'auto',
      maxHeightScroll: false
    }
  },
  computed: {
    activeLabel () {
      return this.content !== '' || this.placeholder !== '' || this.focused
    },
    linputClass () {
      return {
        'is-valid': this.validation === 'valid',
        'is-warning': this.validation === 'warning',
        'is-invalid': this.validation === 'invalid',
        'h-100': !this.maxHeight
      }
    },
    textAreaStyle () {
      if (!this.autosize) { return {} }
      return {
        resize: !this.isResizeImportant ? 'none' : 'none !important',
        height: this.height
      }
    },
    isResizeImportant () {
      const imp = this.important
      return imp === true || (Array.isArray(imp) && imp.includes('resize'))
    },
    isHeightImportant () {
      const imp = this.important
      return imp === true || (Array.isArray(imp) && imp.includes('height'))
    }
  },
  watch: {
    validate () {
      this.validation = this.validate
    },
    value () {
      this.validation = ''
      this.content = this.value
      this.resize()
    },
    minHeight () {
      this.resize()
    },
    maxHeight () {
      this.resize()
    },
    autosize (val) {
      if (val) {
        this.resize()
      }
    }

  },
  mounted () {
    this.resize()
  },
  methods: {
    labelClick () {
      this.focused = true
      this.$refs.input.focus()
    },
    handleInput () {
      this.$emit('input', this.content)
    },
    handleChange () {
      this.$emit('change', this.content)
    },
    resize () {
      const important = this.isHeightImportant ? 'important' : ''
      this.height = `auto${important ? ' !important' : ''}`
      this.$nextTick(() => {
        let contentHeight = this.$refs.input.scrollHeight + 1
        if (this.minHeight) {
          contentHeight = contentHeight < this.minHeight ? this.minHeight : contentHeight
        }
        if (this.maxHeight) {
          if (contentHeight > this.maxHeight) {
            contentHeight = this.maxHeight
            this.maxHeightScroll = true
          } else {
            this.maxHeightScroll = false
          }
        }
        const heightVal = contentHeight + 'px'
        this.height = `${heightVal}${important ? ' !important' : ''}`
      })
      return this
    }
  }
}
</script>

<style lang="scss" scoped>
@mixin validation($color) {
  textarea {
    border-color: $color;

    &:focus {
      border-color: $color;
    }
  }
}

.ltextarea {
  position: relative;

  &.is-valid {
    @include validation(var(--success));
  }

  &.is-warning {
    @include validation(var(--warning));
  }

  &.is-invalid {
    @include validation(var(--danger));
  }

  textarea {
    display: block;
    width: 100%;
    padding: .375rem .75rem;
    font-size: 1rem;
    font-weight: 400;
    line-height: 1.5;
    color: #495057;
    background-color: #fff;
    background-clip: padding-box;
    border: 1px solid #ced4da;
    border-radius: .25rem;
    box-shadow: inset 0 0 0 transparent;
    transition: border-color .15s ease-in-out, box-shadow .15s ease-in-out;
    margin-bottom: 1rem;
    filter: none;

    &:focus {
      border-color: var(--primary) !important;
    }

    &:disabled {
      cursor: not-allowed;
    }
  }

  label {
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
    width: max-content;
    white-space: nowrap;
    background-color: #fff;

    &.active {
      top: -12px;
      left: 10px;
      font-size: 14px;

      & span {
        background: #fff;
      }
    }
  }
}

</style>
