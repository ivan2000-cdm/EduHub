<template>
  <transition
    name="fade"
    @after-enter="afterEnter"
    @before-leave="beforeLeave"
  >
    <div v-if="open" class="modal" @click.self="away">
      <div ref="modal-content" :class="mDialog">
        <div :class="mContent">
          <div class="modal-header">
            <slot name="header" />
            <button
              v-if="neededCloseButtonCorner"
              type="button"
              class="close"
              @click="close"
            >
              <span aria-hidden="true">×</span>
            </button>
          </div>
          <div class="modal-body" :style="maxHeight ? `overflow-y: auto !important; max-height: ${maxHeight}vh !important` : ''">
            <slot name="body" />
          </div>
          <div class="modal-footer justify-content-center">
            <slot name="footer">
              <button
                type="button"
                :class="btnClose"
                @click="close"
              >
                Закрыть
              </button>
            </slot>
          </div>
        </div>
      </div>
    </div>
  </transition>
</template>

<script>
export default {
  name: 'LModal',
  props: {
    // Открыто ли окно
    open: {
      type: Boolean,
      default: false
    },
    // Размер окна
    size: {
      type: String,
      default: ''
    },
    // Высота
    maxHeight: {
      type: Number,
      default: 0
    },
    // Цвет
    color: {
      type: String,
      default: ''
    },
    // Цвет кнопки "закрыть" в footer
    colorClose: {
      type: String,
      default: 'default'
    },
    neededCloseButtonCorner: {
      type: Boolean,
      default: true
    }
  },
  computed: {
    mClass () {
      return [
        'modal',
        'fade',
        {
          show: this.open
        }
      ]
    },
    btnClose () {
      return [
        'btn',
        `btn-${this.colorClose}`
      ]
    },
    mDialog () {
      return [
        'modal-dialog',
        `modal-${this.size}`
      ]
    },
    mContent () {
      return [
        'modal-content',
        `bg-${this.color}`
      ]
    }
  },
  watch: {
    open () {
      this.$store.commit('SET_BODY_FIXED', this.open)
    }
  },
  beforeDestroy () {
    const elem = document.getElementById('modal-background')
    if (elem) {
      document.body.removeChild(elem)
    }
  },
  methods: {
    hasSlot (name) {
      return this.$slots[name]
    },
    close () {
      this.$emit('close')
    },
    away (e) {
      this.close()
    },
    afterEnter (el) {
      document.body.insertAdjacentHTML('beforeend', '<div id="modal-background" class="modal-background"/>')
    },
    beforeLeave (el) {
      const elem = document.getElementById('modal-background')
      document.body.removeChild(elem)
    }
  }
}
</script>

<style lang="scss" scoped>

.fade-enter-active, .fade-leave-active {
  transition: all .05s;
}

.fade-enter, .fade-leave-to {
  opacity: 0;
}

.modal {
  width: 100%;
  height: 100vh;
  position: fixed;
  top: 0;
  left: 0;
  background: rgba(#000, .5);
  display: block;
  z-index: 9999;
  overflow-y: auto;

  &-dialog {
    width: 100% !important;
  }

  &-background {
    display: flex;
    justify-content: center;
    align-items: center;
  }

  // Если надо, можно закрепить footer с кнопкой "Закрыть" при прокрутки
  //.modal-footer {
  //  position: sticky;
  //  bottom: 0;
  //  background: #fff; // Задайте фон, если необходимо
  //  z-index: 1;
  //}
}

</style>
