<template>
  <button
    v-if="!link"
    :class="bClass"
    :disabled="disabled"
    :type="type"
    @click="onClick"
  >
    <i v-if="icon" :class="['btn-icon', icon]" />
    <span v-if="$slots.default">
      <slot />
    </span>
  </button>
  <nuxt-link
    v-else
    :to="to"
    :class="bClass"
  >
    <i v-if="icon" :class="['btn-icon', icon]" />
    <span v-if="$slots.default">
      <slot />
    </span>
  </nuxt-link>
</template>

<script>
// Кнопка
export default {
  name: 'LButton',
  props: {
    // Цвет кнопки
    color: {
      type: String,
      required: true,
      default: 'default',
      validator: value => ['default', 'primary', 'secondary', 'success', 'info', 'danger', 'warning'].includes(value)
    },
    // Является ли кнопка ссылкой
    link: {
      type: Boolean,
      default: false
    },
    // Ссылка
    to: {
      type: String,
      default: '#'
    },
    // Размер кнопки
    size: {
      type: String,
      default: 'sm',
      validator: value => ['', 'lg', 'sm', 'xs'].includes(value)
    },
    // Выключить кнопку
    disabled: {
      type: Boolean,
      default: false
    },
    // Плоская кнопка
    flat: {
      type: Boolean,
      default: false
    },
    // Кнопка с обводкой
    outline: {
      type: Boolean,
      default: false
    },
    // Градиентный фон
    gradient: {
      type: Boolean,
      default: false
    },
    // Display block
    block: {
      type: Boolean,
      default: false
    },
    // Тип кнопки
    type: {
      type: String,
      default: 'button'
    },
    icon: {
      type: String,
      default: ''
    }
  },
  data () {
    return {}
  },
  computed: {
    bClass () {
      return [
        'btn',
        `btn-${this.size}`,
        `btn${this.outline ? '-outline-' : '-'}${this.color}`,
        this.gradient ? `bg-gradient-${this.color}` : `btn${this.outline ? '-outline-' : '-'}${this.color}`,
        {
          disabled: this.disabled,
          'btn-flat': this.flat,
          'btn-block': this.block
        }
      ]
    }
  },
  methods: {
    onClick (e) {
      this.$emit('click', e)
    }
  }
}
</script>

<style lang="scss" scoped>
.disable {
  pointer-events: none;
}

.btn {
  display: flex;
  align-items: center;
  justify-content: center;
  font-family: "Source Sans Pro", -apple-system,BlinkMacSystemFont,
                "Segoe UI",Roboto,"Helvetica Neue",Arial,sans-serif,
                "Apple Color Emoji","Segoe UI Emoji","Segoe UI Symbol";
  &-warning {
    color: #fff !important
  }
  &-icon + span {
    margin-left: 10px
  }
}
</style>
