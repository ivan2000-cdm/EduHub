<template>
  <div
    v-click-outside="documentListener"
    :title="!multiple && content && content[propData.text]"
    :class="{
      lselect: true,
      'is-valid': validation === 'valid',
      'is-warning': validation === 'warning',
      'is-invalid': validation === 'invalid'
    }"
    :style="maxAutoHeight ? { 'height': 'auto', 'max-height': 'max-content', 'padding-bottom': '5px' } : {}"
  >
    <div
      ref="select"
      :class="{
        lselect__wrapper: true,
        disabled: disabled,
        'overflow-hidden': true
      }"
      :style="maxAutoHeight ? { 'height': 'auto', 'max-height': 'max-content' } : {}"
      @click="clickFocus"
    >
      <l-loading
        v-show="loading"
        style="position: absolute; right: 35px; bottom: 15px;"
        :style-loading="{width: 0, height: 0}"
        :style-loading-body="{width: 0, height: 0}"
        :style-icon="{padding: 0, 'font-size': '1.5em'}"
      />
      <div
        v-show="showText && !isFocused"
        class="lselect__text"
      >
        {{ selectText }}
      </div>
      <input
        v-show="isFocused"
        ref="input"
        v-model="searchText"
        class="lselect__input"
        autocomplete="false"
        :placeholder="placeholder"
        :disabled="disabled"
        @mouseup="inputMouseup"
      >
      <label
        tabindex="-1"
        :title="label"
        :class="{
          'flex': true,
          lselect__placeholder: true,
          active: activeLabel,
          'overflow-hidden': true,
          'w-100': true
        }"
      >
        <span
          class="overflow-hidden text-nowrap flex-shrink-1"
          style="text-overflow: ellipsis; background: #fff;"
        >{{ label }}</span>
        <span
          v-if="required"
          class="required pl-1 mr-3 flex-shrink-0"
          style="flex-basis: 10px; background: #fff;"
        >*</span>
      </label>
    </div>
    <div class="lselect__icon" @click="clickFocus">
      <i
        :class="{
          'fas fa-caret-up': isOpen,
          'fas fa-caret-down': !isOpen
        }"
      />
    </div>
    <div
      v-show="isOpen"
      ref="options"
      class="lselect__options"
    >
      <perfect-scrollbar
        ref="scroll"
        :options="scrollBar"
        :style="{ height }"
      >
        <div class="lselect__options-scroll">
          <template v-if="ajaxLoading">
            <div class="lselect__options-item d-flex justify-content-center align-items-center">
              <l-loading
                :style-loading="{width: 0, height: 0}"
                :style-loading-body="{width: 0, height: 0}"
                :style-icon="{padding: 0, 'font-size': '1.5em'}"
              />
              <span class="ml-3">Загрузка...</span>
            </div>
          </template>
          <template v-else-if="Array.isArray(selectOptions) && selectOptions.length > 0">
            <div
              v-for="(item, i) in selectOptions"
              :key="i"
              :class="{
                'lselect__options-item': true,
                'lselect__options-item_selected': isSelected(item)
              }"
              @click.prevent.stop="selectedItem($event, item)"
            >
              {{ item[propData.text] }}
            </div>
          </template>
          <template v-else-if="searchUrl !== '' && searchText.length < (searchMinLength + 1)">
            <div class="lselect__options-item lselect__options-item_success">
              Пожалуйста, введите {{ searchText.length > 0 ? 'ещё' : '' }} хотя бы
              {{ (searchMinLength + 1) - searchText.length }}
              {{
                ((searchMinLength + 1) - searchText.length) === 1
                  ? ' символ'
                  : ' символа'
              }}
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
</template>

<script>
import LLoading from '../elements/lloading.vue'

export default {
  name: 'LSelect',
  components: { LLoading },
  props: {
    label: {
      type: String,
      default: ''
    },
    disabled: {
      type: Boolean,
      default: false
    },
    placeholder: {
      type: String,
      default: 'Поиск...'
    },
    name: {
      type: String,
      required: false,
      default: null
    },
    height: {
      type: String,
      default: 'auto'
    },
    validate: {
      type: String,
      default: '',
      validator: value => [
        '',
        'valid',
        'warning',
        'invalid'
      ].includes(value)
    },
    value: {
      type: [Array, Object, String],
      default: () => ({})
    },
    required: {
      type: Boolean,
      default: false
    },
    options: {
      type: Array,
      default: () => []
    },
    propData: {
      type: Object,
      default: () => ({
        value: 'id',
        text: 'name'
      })
    },
    multiple: {
      type: Boolean,
      default: false
    },
    loading: {
      type: Boolean,
      default: false
    },
    searchUrl: {
      type: String,
      default: ''
    },
    /**
     * array = [ { name: 'name', value: 'value' },{...} ]
     */
    searchParams: {
      type: Object,
      default: null,
      validator: (val) => {
        return val !== null
          ? Object.entries(val).every(([key, value]) =>
            (typeof key === 'string' || (Number.isInteger(key) && key > 0)) &&
            (typeof value === 'string' || typeof value === 'number' || typeof value === 'boolean' || value === null)
          )
          : true
      }
    },
    searchMinLength: {
      type: Number,
      default: 3
    },
    searchAwait: {
      type: Number,
      default: 2000
    },
    maxAutoHeight: {
      type: Boolean,
      default: false
    }
  },
  data () {
    return {
      content: this.value,
      isOpen: false,
      isFocused: false,
      searchText: '',
      searched: [],
      scrollBar: {
        suppressScrollY: false
      },
      validation: this.validate,
      data: this.options,
      ajaxLoading: false,
      timeout: null
    }
  },
  computed: {
    inputStyles () {
      if (!this.activeLabel) {
        return {
          width: 0,
          position: 'absolute',
          padding: 0
        }
      }
      return {}
    },
    activeLabel () {
      if (this.content === null || this.content === undefined) {
        return this.isFocused
      }
      return (this.multiple
        ? this.content
        : Object.keys(this.content)
      ).length > 0 || this.isFocused
    },
    selectOptions () {
      return this.searchUrl ? this.data : (this.searchText !== '' ? this.searched : this.data)
    },
    selectedCount () {
      if (this.content === null || this.content === undefined) {
        return 0
      }
      const values = this.content.map(item => item[this.propData.value])
      return this.data.filter(item => values.includes(item[this.propData.value])).length
    },
    selectText () {
      if (this.multiple) {
        let end = ''
        if (this.selectedCount === 1) {
          end = 'элемент выбран'
        } else if (this.selectedCount > 2 && this.selectedCount < 5) {
          end = 'элемента выбрано'
        } else {
          end = 'элементов выбрано'
        }
        return `${this.selectedCount} ${end}`
      }
      return this.content ? this.content[this.propData.text] : {}
    },
    showText () {
      if (this.content === null || this.content === undefined) {
        return false
      } else {
        if (this.multiple) {
          return this.content.length > 0
        }
        return Object.keys(this.content).length > 0
      }
    }
  },
  watch: {
    options () {
      this.data = this.options
    },
    value () {
      this.content = this.value
    },
    searchText () {
      if (this.searchUrl) {
        this.initSearch(this.searchText)
      } else if (this.searchText !== '') {
        this.searched = this.options
          .filter(item => item[this.propData.text].toLowerCase().includes(this.searchText.toLowerCase()))
      } else {
        this.searched = []
      }
    },
    validate () {
      this.validation = this.validate
    },
    validation () {
      if (this.validation !== '') {
        this.$emit(this.validation, this.name)
      }
    },
    isFocused (focused) {
      if (focused) {
        // без таймаута не успевает появиться элемент и фокус не срабатывает
        setTimeout(() => {
          this.$refs.input.focus()
        }, 100)
      }
    }
  },
  methods: {
    initSearch (text) {
      if (text.length > this.searchMinLength) {
        if (this.timeout) {
          clearTimeout(this.timeout)
          this.ajaxLoading = false
        }
        this.ajaxLoading = true

        this.timeout = setTimeout(() => {
          const params = this.searchParams
            ? { ...this.searchParams, text }
            : { text }

          this.$axios.get(this.searchUrl, { params })
            .then(({ data: opts }) => {
              this.data = opts
            })
            .catch(() => {
              this.data = []
              this.content = null
            })
            .finally(() => {
              this.ajaxLoading = false
            })
        }, this.searchAwait)
      } else {
        clearTimeout(this.timeout)
        this.ajaxLoading = false
        this.data = []
        this.$emit('input', this.multiple ? [] : {})
        if (this.validation === 'valid') {
          this.validation = 'invalid'
        }
      }
    },
    documentListener () {
      this.isOpen = false
      this.isFocused = false
      this.searched = []
    },
    clickFocus () {
      if (!this.disabled) {
        this.isFocused = true
        this.isOpen = true
      }
    },
    inputMouseup () {
      this.clickFocus()
    },
    isSelected (item) {
      if (this.content === null || this.content === undefined) {
        return false
      }
      if (this.multiple) {
        const values = this.content.map(val => val[this.propData.value])
        return values.includes(item[this.propData.value])
      }
      return this.content[this.propData.value] === item[this.propData.value]
    },
    checkValidation () {
      // TODO c required и value === null || undefined будет всегда invalid
      if (this.required) {
        if (!this.content || (Array.isArray(this.content) && this.content.length === 0)) {
          this.validation = 'invalid'
          return false
        } else {
          this.validation = 'valid'
          return true
        }
      } else {
        this.validation = 'valid'
        return true
      }
    },
    selectedItem (e, item) {
      if (this.multiple) {
        const newValue = !this.content ? [] : [...this.content]
        const index = newValue.findIndex(val => item[this.propData.value] === val[this.propData.value])
        index === -1
          ? newValue.push(item)
          : newValue.splice(index, 1)
        if (this.data.length < 2) {
          this.isOpen = false
          this.isFocused = false
        }
        this.content = newValue
        this.$emit('change', newValue)
        this.$emit('input', newValue)
      } else {
        this.isOpen = false
        this.isFocused = false
        this.content = item
        this.$emit('change', item)
        this.$emit('input', item)
      }
      this.checkValidation()
    }
  }
}
</script>

<style lang="scss" scoped>

.lselect {
  position: relative;
  width: 100%;
  height: 30px;
  max-height: 30px;
  margin-bottom: 1.5rem;
  border: 1px solid #ced4da;
  display: flex;
  // align-items: center;
  &.is-valid {
    border-color: var(--success);
  }

  &.is-warning {
    border-color: var(--warning);
  }

  &.is-invalid {
    border-color: var(--danger);
  }

  &__icon {
    width: 30px;
    height: 30px;
    display: flex;
    align-items: center;
    justify-content: center;

    i {
      color: #999;
      transition: all .5s;
    }
  }

  &__wrapper {
    width: 100%;
    height: 30px;
    max-height: 30px;

    &.disabled {
      cursor: not-allowed
    }
  }

  &__text {
    z-index: 1;
    position: relative;
    top: 5px;
    left: 10px;
    color: var(--secondary);
    font-size: 14px;
    margin-bottom: 0;
  }

  &__input {
    position: relative;
    display: inline-block;
    width: 100%;
    height: 30px;
    max-height: 30px;
    line-height: 30px;
    padding: 0 10px;
    border: none;
    background: #fff;
    margin-bottom: 8px;
    vertical-align: top;
  }

  &__placeholder {
    font-weight: bold;
    position: absolute;
    top: 5px;
    left: 10px;
    // transform: translateX(10px) translateY(-26px);
    transition: all .2s linear;
    color: var(--secondary);
    font-size: 14px;
    margin-bottom: 0;
    height: 18px;

    //&.active {
    // top: -12px;
    // left: 10px;
    transform: translateY(-15px);
    padding-left: 5px;
    padding-right: 5px;
    border-radius: 5px;
    //}
  }

  &__options {
    position: absolute;
    top: 100%;
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
</style>
