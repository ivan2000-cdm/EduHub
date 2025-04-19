<template>
  <div v-click-outside="documentListener">
    <div :class="['linput', validityClass]">
      <input
        :id="name"
        ref="input"
        v-model="content"
        :class="validityClass"
        :disabled="disabled"
        :title="content"
        autocomplete="off"
        @change="handleChange"
        @input="handleInput"
      >
      <l-lloading
        v-show="loading"
        class="loading"
        :style-icon="{padding: 0, 'font-size': '1.5em'}"
        :style-loading="{width: 0, height: 0}"
        :style-loading-body="{width: 0, height: 0}"
      />
      <i
        v-show="isShowClearButton"
        class="linput_icon clear fad fa-times"
        title="Очистить поле"
        @click="inputClear"
      />
      <label
        v-if="label"
        :title="label"
        class="active flex overflow-hidden w-100 "
        :for="name"
        @click="labelClick"
      >
        <span class="overflow-hidden text-nowrap flex-shrink-1 overflow text-overflow">
          {{ label }}
        </span>
        <span
          v-if="required"
          class="required pl-1 mr-3 flex-shrink-0 flex-basic-10"
        >*</span>
      </label>
    </div>
    <div class="lselect">
      <div
        v-show="isShowOptions"
        ref="options"
        class="lselect__options"
      >
        <perfect-scrollbar :options="{suppressScrollY: false}">
          <div class="lselect__options-scroll">
            <template v-if="options.length > 0 && filteredOptions.length === 0">
              <div
                v-for="(option, i) in options"
                :key="i"
                class="lselect__options-item"
                @click.prevent.stop="selectOption(option)"
              >
                {{ option }}
              </div>
            </template>
            <template v-else-if="filteredOptions.length > 0">
              <div
                v-for="(option, i) in filteredOptions"
                :key="i"
                class="lselect__options-item"
                @click.prevent.stop="selectOption(option)"
              >
                {{ option }}
              </div>
            </template>
            <template v-else>
              <div class="lselect__options-item lselect__options-item_error">
                Элементы отсутствуют
              </div>
            </template>
          </div>
        </perfect-scrollbar>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'LInputAutocomplete',
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
      default: true
    },
    options: {
      type: Array,
      default: () => ([])
    },
    url: {
      type: String,
      default: null
    },
    minCharCount: {
      type: Number,
      default: 3,
      validator: value => Number.isInteger(value)
    },
    timeout: {
      type: Number,
      default: 1000,
      validator: value => Number.isInteger(value)
    }
  },
  data () {
    return {
      content: this.value,
      filteredOptions: [],
      loading: false,
      isShowOptions: false,
      timer: null
    }
  },
  computed: {
    isShowClearButton () {
      return this.clearButton && this.content
    },
    validityClass () {
      switch (this.validate) {
        case 'valid':
          return 'is-valid'
        case 'warning':
          return 'is-warning'
        case 'invalid':
          return 'is-invalid'
        default:
          return null
      }
    }
  },
  watch: {
    value () {
      this.content = this.value
    }
  },
  methods: {
    selectOption (value) {
      this.content = value
      this.isShowOptions = false
      this.$emit('update:value', this.content) // emit updated value with the new v-model syntax
    },
    labelClick () {
      this.$refs.input.focus()
    },
    handleChange () {
      this.$emit('update:value', this.content)
    },
    handleInput () {
      this.$emit('input', this.content)
      this.search(this.content)
    },
    inputClear () {
      this.content = ''
      this.isShowOptions = false
      this.$emit('update:value', this.content) // emit updated value with the new v-model syntax
    },
    documentListener () {
      this.isShowOptions = false
    },
    search (value) {
      if (this.url) {
        this.searchServerSide(value)
      } else {
        this.searchClientSide(value)
      }
    },
    searchServerSide (value) {
      if (value.length < this.minCharCount) {
        return
      }

      if (this.timer) {
        clearTimeout(this.timer)
        this.timer = null
      }

      this.timer = setTimeout(async () => {
        this.loading = true
        const { data } = await this.$axios.get(this.url, { params: { text: value } })
        this.filteredOptions = data
        this.loading = false
        this.isShowOptions = true
      }, this.timeout)
    },
    searchClientSide (value) {
      this.loading = true
      this.filteredOptions = this.options.filter(option => option.toLowerCase().includes(value.toLowerCase()))
      this.loading = false
      this.isShowOptions = true
    }
  }
}
</script>


<style lang="scss" scoped>
.flex-basic-10 {
  flex-basis: 10px;
}

.loading {
  position: absolute;
  right: 35px;
  bottom: 15px;
}

.text-overflow {
  text-overflow: ellipsis
}

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

.lselect {
  position: relative;
  margin-top: -25px;
  margin-bottom: 1.5rem;
  display: flex;

  &__options {
    position: absolute;
    left: 0;
    border: 1px solid #dee2e6;
    width: 100%;
    height: max-content;
    max-height: 250px;
    overflow: hidden;
    z-index: 1036;

    &-scroll {
      height: 100%;
      max-height: 250px;
    }

    &-item {
      padding: .5rem 1rem;

      &:not(:last-child) {
        border-bottom: 1px solid #dee2e6;
      }

      &:hover {
        background-color: rgba(#e9e9e9, 1);
      }

      &:nth-child(odd) {
        background-color: rgba(#f6f7f8, 1);

        &:hover {
          background-color: rgba(#e9e9e9, 1);
        }
      }

      &:nth-child(even) {
        background-color: rgba(#ffffff, 1);

        &:hover {
          background-color: rgba(#e9e9e9, 1);
        }
      }

      &_selected {
        background-color: rgba(#007bff, 1) !important;
        color: #fff;

        &:hover {
          background-color: rgba(#007bff, 1);
        }
      }

      &_error {
        color: var(--danger);
      }
    }
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
