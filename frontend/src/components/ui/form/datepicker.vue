<template>
  <div class="ldatetimepicker" @click="focusTrigger">
    <DatePicker
      ref="datepicker"
      v-model="date"
      :type="type"
      :range="range"
      :format="format"
      :disabled="disabled"
      :disabled-date="disabledDate"
      :disabled-time="disabledTime"
      :show-time-panel="showTimePanel"
      :show-week-number="showWeekNumber"
      :value-type="valueType"
      :use12h="false"
      :append-to-body="appendToBody"
      range-separator=" — "
      title-format="DD.MM.YYYY"
      time-title-format="DD.MM.YYYY"
      clearable
      :class="{
        invalid: validation === 'invalid',
        valid: validation === 'valid'
      }"
      @input="onInput"
      @clear="clearDate"
      @blur="focused = false"
    >
      <template v-if="type !== 'date' && type !== 'time'" #footer>
        <button class="mx-btn mx-btn-text" @click="toggleTimePanel">
          {{ showTimePanel ? `Выбрать ${range ? 'даты' : 'дату'}` : 'Выбрать время' }}
        </button>
      </template>
    </DatePicker>
    <label
      v-if="label"
      :title="label"
      :class="labelClasses"
      tabindex="-1"
    >
      <span class="label-text">{{ label }}</span>
      <span v-if="required" class="required"> *</span>
    </label>
  </div>
</template>

<script>
import DatePicker from 'vue2-datepicker'
import 'vue2-datepicker/index.css'
import 'vue2-datepicker/locale/ru'

export default {
  name: 'LDateTimepicker',
  components: { DatePicker },
  props: {
    type: {
      type: String,
      default: 'date',
      validator: value => ['date', 'datetime', 'year', 'month', 'time', 'week'].includes(value)
    },
    range: { type: Boolean, default: false },
    format: { type: String, default: 'DD.MM.YYYY HH:mm:ss' },
    disabled: { type: Boolean, default: false },
    value: { type: [String, Object, Date, Array], default: '' },
    showWeekNumber: { type: Boolean, default: false },
    label: { type: String, default: '' },
    required: { type: Boolean, default: false },
    valueType: { type: String, default: 'date' },
    disabledDate: { type: Function, default: () => {} },
    disabledTime: { type: Function, default: () => {} },
    validate: {
      type: String,
      default: '',
      validator: value => ['', 'valid', 'warning', 'invalid'].includes(value)
    },
    appendToBody: { type: Boolean, default: false }
  },
  data () {
    return {
      date: this.value,
      showTimePanel: false,
      focused: false,
      validation: this.validate
    }
  },
  computed: {
    activeLabel () {
      return (this.date !== '' && this.date !== null) || this.focused
    },
    labelClasses () {
      return {
        flex: true,
        active: this.activeLabel,
        'overflow-hidden': true,
        'w-100': true
      }
    }
  },
  watch: {
    validate (newVal) {
      this.validation = newVal
    },
    validation (newVal) {
      if (newVal) {
        this.$emit(newVal, this.name)
      }
    },
    value (newVal) {
      this.date = newVal
    }
  },
  methods: {
    toggleTimePanel () {
      this.showTimePanel = !this.showTimePanel
    },
    onInput (value) {
      this.date = value
      if (this.required && !value) {
        this.validation = 'invalid'
      } else {
        this.validation = 'valid'
      }
      this.$emit('input', value)
    },
    focusTrigger (e) {
      if (e.target.tagName === 'INPUT') {
        this.focused = true
        this.$refs.datepicker.$refs.input.focus()
      }
    },
    clearDate () {
      this.date = null
      this.$emit('input', null)
    }
  }
}
</script>

<style lang="scss" scoped>
.required {
  color: var(--danger);
}

.ldatetimepicker {
  position: relative;
  margin-bottom: 1.5rem;

  label {
    position: absolute;
    top: 5px;
    left: 10px;
    transition: all 0.2s linear;
    color: var(--secondary);
    font-size: 14px;
    margin-bottom: 0;
    height: 18px;
    transform: translateY(-15px);
    padding-right: 5px;
    padding-left: 5px;
    margin: -2px;
  }

  .label-text {
    overflow: hidden;
    text-overflow: ellipsis;
    background: #fff;
    flex-shrink: 1;
  }

  .required {
    padding-left: 1px;
    margin-right: 3px;
    flex-shrink: 0;
    background: #fff;
    flex-basis: 10px;
  }
}

.mx-datepicker-main {
  z-index: 1036;
}
</style>
