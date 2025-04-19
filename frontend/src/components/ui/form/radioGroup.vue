<template>
  <div :class="['flex', orientation === 'vertical' ? 'flex-column' : '']">
    <template v-for="(radio, i) in localRadios" :key="i">
      <div
        :class="['flex', orientation === 'vertical' ? 'flex-column' : '']"
      >
        <l-radio
          :label="radio.label"
          :name="name"
          :value="radio.name"
          :checked="radio.value === value"
          :disabled="Object.keys(radio).includes('disabled') ? radio.disabled : disabled"
          :color="getColor(radio)"
          @change="handleChange(radio)"
        />
        <slot :name="`radio.${radio.name}`" />
      </div>
    </template>
  </div>
</template>

<script>
import LRadio from '@/components/ui/form/radio'
export default {
  name: 'LRadioGroup',
  components: { LRadio },
  props: {
    // Название для input
    name: {
      type: String,
      required: true
    },
    // Список radio buttons
    radios: {
      type: Array,
      required: true
    },
    // Выбранный radio button
    value: {
      type: [Boolean, Object, String, Number],
      default: false
    },
    // Ориентация
    orientation: {
      type: String,
      default: 'vertical',
      validator: item => ['vertical', 'horizontal'].includes(item)
    },
    disabled: {
      type: Boolean,
      default: false
    }
  },
  data: () => ({
    // Локальные данные
    localRadios: []
  }),
  watch: {
    radios (val) {
      this.localRadios = val
    }
  },
  mounted () {
    this.localRadios = this.radios
  },
  methods: {
    /**
     * Окрашивание элемента в зависимости от валидности
     * @param radio
     * @returns {string}
     */
    getColor (radio) {
      switch (radio.valid) {
        case true:
          return 'success'
        case false:
          return 'danger'
        case null:
        default:
          return 'primary'
      }
    },
    /**
     * Выдача результата наружу и обнооаление локального значения
     * @param radio
     */
    handleChange (radio) {
      this.updateChecked(radio)
      this.$emit('input', radio.value)
    },

    /**
     * Обновление выбранного элемента
     * @param radio
     */
    updateChecked (radio) {
      this.localRadios = this.localRadios.map(item => ({ ...item, checked: radio.value === item.value }))
    }
  }
}
</script>

<style scoped>

</style>
