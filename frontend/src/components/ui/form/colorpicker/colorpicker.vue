<template>
  <client-only>
    <div class="lcolorpicker">
      <div class="lcolorpicker_wrapper">
        <input
          :value="colors.hex"
          type="text"
          :disabled="disabled"
          @mousedown="toggle"
          @input="inputChange"
          @keydown="onKeyDown"
          @focus="focused = true"
          @blur="focused = false"
        >
        <label
          v-if="title != ''"
          :class="{
            'active': activeLabel
          }"
        >
          {{ title }}
        </label>
        <span
          class="lcolorpicker_icon"
          :style="{
            border: '1px solid #cecece',
            backgroundColor: colors.hex
          }"
          @click="toggle"
        />
      </div>

      <div
        v-show="openPicker"
        ref="panel"
        role="application"
        style="z-index: 99999"
        :class="['vc-chrome', disableAlpha && 'vc-chrome__disable-alpha', dropUp && 'vc-chrome__dropup']"
      >
        <div class="vc-chrome-saturation-wrap">
          <saturation
            v-model="colors"
            @change="childChange"
          />
        </div>
        <div class="vc-chrome-body">
          <div class="vc-chrome-controls">
            <div class="vc-chrome-color-wrap">
              <div
                class="vc-chrome-active-color"
                :style="{background: activeColor}"
              />
              <checkboard v-if="!disableAlpha" />
            </div>

            <div class="vc-chrome-sliders">
              <div class="vc-chrome-hue-wrap">
                <hue
                  v-model="colors"
                  @change="childChange"
                />
              </div>
              <div
                v-if="!disableAlpha"
                class="vc-chrome-alpha-wrap"
              >
                <alpha
                  v-model="colors"
                  @change="childChange"
                />
              </div>
            </div>
          </div>
          <div
            v-if="!disableFields"
            class="vc-chrome-fields-wrap"
          >
            <!-- <l-button
              size="sm"
              color="danger"
              @click="cancelColor"
            >
              Отмена
            </l-button>
            <l-button
              size="sm"
              color="success"
              @click="applyColor"
            >
              Применить
            </l-button> -->
            <!-- <div
              v-show="fieldsIndex === 0"
              class="vc-chrome-fields"
            >
              <div class="vc-chrome-field">
                <ed-in
                  v-if="!hasAlpha"
                  label="hex"
                  :value="colors.hex"
                  @change="inputChange"
                />
                <ed-in
                  v-if="hasAlpha"
                  label="hex"
                  :value="colors.hex8"
                  @change="inputChange"
                />
              </div>
            </div>
            <div
              v-show="fieldsIndex === 1"
              class="vc-chrome-fields"
            >
              <div class="vc-chrome-field">
                <ed-in
                  label="r"
                  :value="colors.rgba.r"
                  @change="inputChange"
                />
              </div>
              <div class="vc-chrome-field">
                <ed-in
                  label="g"
                  :value="colors.rgba.g"
                  @change="inputChange"
                />
              </div>
              <div class="vc-chrome-field">
                <ed-in
                  label="b"
                  :value="colors.rgba.b"
                  @change="inputChange"
                />
              </div>
              <div
                v-if="!disableAlpha"
                class="vc-chrome-field"
              >
                <ed-in
                  label="a"
                  :value="colors.a"
                  :arrow-offset="0.01"
                  :max="1"
                  @change="inputChange"
                />
              </div>
            </div>
            <div
              v-show="fieldsIndex === 2"
              class="vc-chrome-fields"
            >
              <div class="vc-chrome-field">
                <ed-in
                  label="h"
                  :value="hsl.h"
                  @change="inputChange"
                />
              </div>
              <div class="vc-chrome-field">
                <ed-in
                  label="s"
                  :value="hsl.s"
                  @change="inputChange"
                />
              </div>
              <div class="vc-chrome-field">
                <ed-in
                  label="l"
                  :value="hsl.l"
                  @change="inputChange"
                />
              </div>
              <div
                v-if="!disableAlpha"
                class="vc-chrome-field"
              >
                <ed-in
                  label="a"
                  :value="colors.a"
                  :arrow-offset="0.01"
                  :max="1"
                  @change="inputChange"
                />
              </div>
            </div> -->
            <!-- <div
              class="vc-chrome-toggle-btn"
              role="button"
              @click="toggleViews"
            >
              <div class="vc-chrome-toggle-icon">
                <svg
                  style="width:24px; height:24px"
                  viewBox="0 0 24 24"
                  @mouseover="showHighlight"
                  @mouseenter="showHighlight"
                  @mouseout="hideHighlight"
                >
                  <path
                    fill="#333"
                    d="M12,18.17L8.83,15L7.42,16.41L12,21L16.59,16.41L15.17,15M12,5.83L15.17,9L16.58,7.59L12,3L7.41,7.59L8.83,9L12,5.83Z"
                  />
                </svg>
              </div>
              <div
                v-show="highlight"
                class="vc-chrome-toggle-icon-highlight"
              />
            </div> -->
          </div>
        </div>
      </div>
    </div>
  </client-only>
</template>

<script>
// import editableInput from './editableInput'
import saturation from './saturation'
import hue from './hue'
import alpha from './alpha'
import checkboard from './checkboard'
import colorMixin from './mixins/color'

// Инпут с выбором цвета
export default {
  name: 'LColorpicker',
  components: {
    saturation,
    hue,
    alpha,
    // 'ed-in': editableInput,
    checkboard
  },
  mixins: [colorMixin],
  props: {
    disabled: {
      type: Boolean,
      default: false
    },
    // Выключение альфа канала
    disableAlpha: {
      type: Boolean,
      default: true
    },
    // Выключение полей для ввода
    disableFields: {
      type: Boolean,
      default: false
    },
    // Описание поля
    title: {
      type: String,
      default: '',
      required: true
    }
  },
  data () {
    return {
      fieldsIndex: 0,
      highlight: false,
      openPicker: false,
      focused: false,
      beforeValue: this.value,
      dropUp: false
    }
  },
  computed: {
    activeLabel () {
      return this.value !== '' || this.focused
    },
    hsl () {
      const { h, s, l } = this.colors.hsl
      return {
        h: h.toFixed(),
        s: `${(s * 100).toFixed()}%`,
        l: `${(l * 100).toFixed()}%`
      }
    },
    activeColor () {
      const rgba = this.colors.rgba
      return 'rgba(' + [rgba.r, rgba.g, rgba.b, rgba.a].join(',') + ')'
    },
    hasAlpha () {
      return this.colors.a < 1
    }
  },
  mounted () {
    this.$nextTick(() => {
      document.addEventListener('click', this.documentListener)
      window.addEventListener('resize', this.calculatePosition)
    })
  },
  unmounted () {
    this.$nextTick(() => {
      document.removeEventListener('click', this.documentListener)
      window.removeEventListener('resize', this.calculatePosition)
    })
  },
  methods: {
    calculatePosition () {
      const panel = this.$refs.panel
      // const input = this.$refs.select
      const panelOffset = {
        top: this.$el.offsetTop, // panel.offsetParent.offsetHeight - 2,
        left: panel.offsetLeft
      }
      const inputHeight = this.$el.clientHeight
      const panelHeight = panel.clientHeight
      const spaceUp = (panelOffset.top - inputHeight - panelHeight) - window.pageYOffset
      const spaceDown = (window.scrollTop || window.scrollY) + window.innerHeight - (panelOffset.top + panelHeight)
      // console.log('window.pageYOffset', window.pageYOffset)
      // console.log('window.scrollTop', window.scrollTop)
      // console.log('window.scrollY', window.scrollY)
      // console.log('window.innerHeight', window.innerHeight)
      // console.log('panelOffset.top', panelOffset.top)
      // console.log('panelHeight', panelHeight)
      // console.log(spaceUp, spaceDown)
      // console.log(spaceDown < 0)
      // console.log(spaceUp >= 0 || spaceUp > spaceDown)
      // console.log(this.$el.offsetTop)
      this.dropUp = spaceDown < spaceUp // spaceDown < 0 && (spaceUp >= 0 || spaceUp > spaceDown)
      // console.log(inputHeight, panelHeight, spaceUp, spaceDown)
    },
    // applyColor () {
    //   this.$emit('input', this.val.hex)
    //   this.beforeValue = this.val.hex
    //   this.openPicker = false
    // },
    // cancelColor () {
    //   this.$emit('input', this.beforeValue)
    //   this.openPicker = false
    // },
    documentListener (event) {
      if (this.openPicker && !this.$el.contains(event.target)) {
        this.openPicker = false
      }
    },
    toggle () {
      if (!this.disabled) {
        this.openPicker = !this.openPicker
        setTimeout(() => {
          this.calculatePosition()
        }, 3)
      }
    },
    onKeyDown (e) {
      const k = e.which || e.button
      if ((e.ctrlKey && k === 86) || k === 8) {
        return true
      } else {
        e.preventDefault()
        e.stopPropagation()
      }
    },
    childChange (data) {
      this.colorChange(data)
    },
    inputChange (data) {
      const value = data
      if (data instanceof Event) {
        value.hex = data.target.value
      }
      if (!value) {
        return
      }
      if (value.hex) {
        this.isValidHex(value.hex) && this.colorChange({
          hex: data.hex,
          source: 'hex'
        })
      } else if (value.r || value.g || value.b || value.a) {
        this.colorChange({
          r: value.r || this.colors.rgba.r,
          g: value.g || this.colors.rgba.g,
          b: value.b || this.colors.rgba.b,
          a: value.a || this.colors.rgba.a,
          source: 'rgba'
        })
      } else if (value.h || value.s || value.l) {
        const s = value.s ? (value.s.replace('%', '') / 100) : this.colors.hsl.s
        const l = value.l ? (value.l.replace('%', '') / 100) : this.colors.hsl.l
        this.colorChange({
          h: value.h || this.colors.hsl.h,
          s,
          l,
          source: 'hsl'
        })
      }
    },
    toggleViews () {
      if (this.fieldsIndex >= 2) {
        this.fieldsIndex = 0
        return
      }
      this.fieldsIndex++
    },
    showHighlight () {
      this.highlight = true
    },
    hideHighlight () {
      this.highlight = false
    }
  }
}
</script>

<style lang="scss" scoped>
  .lcolorpicker {
    margin-bottom: 1.5rem;
    &_wrapper {
      position: relative;
      input {
        display: block;
        width: 100%;
        height: calc(2.25rem + 2px);
        padding: .375rem .75rem;
        font-size: 1rem;
        font-weight: 400;
        line-height: 1.5;
        color: #495057;
        background-color: #fff;
        background-clip: padding-box;
        border: 1px solid #ced4da;
        // border-radius: .25rem;
        box-shadow: inset 0 0 0 transparent;
        transition: border-color .15s ease-in-out,box-shadow .15s ease-in-out;
        &:focus {
          border-color: var(--primary);
        }
        &:disabled {
          cursor: not-allowed;
        }
      }
      label {
        position: absolute;
        top: 8px;
        left: 10px;
        transition: all .2s linear;
        color: var(--secondary);
        font-size: 14px;
        margin-bottom: 0;
        &.active {
          top: -12px;
          left: 10px;
          font-weight: 500;
          background: #fff;
          font-size: 14px;
        }
      }
    }
    &_icon {
      position: absolute;
      display: inline-block;
      right: 10px;
      top: 12px;
      width: 15px;
      height: 15px;
    }
  }

  .vc-chrome {
    position: absolute;
    background: #fff;
    border-radius: 2px;
    box-shadow: 0 0 2px rgba(0,0,0,.3), 0 4px 8px rgba(0,0,0,.3);
    box-sizing: initial;
    width: 225px;
    font-family: Menlo;
    background-color: #fff;
    z-index: 999;
  }
  .vc-chrome__dropup {
  }
  .vc-chrome-controls {
    display: flex;
  }
  .vc-chrome-color-wrap {
    position: relative;
    width: 36px;
  }
  .vc-chrome-active-color {
    position: relative;
    width: 30px;
    height: 30px;
    border-radius: 15px;
    overflow: hidden;
    z-index: 1;
    border: 1px solid #dee2e6;
  }
  .vc-chrome-color-wrap .vc-checkerboard {
    width: 30px;
    height: 30px;
    border-radius: 15px;
    background-size: auto;
  }
  .vc-chrome-sliders {
    flex: 1;
  }
  .vc-chrome-fields-wrap {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding-top: 16px;
  }
  .vc-chrome-fields {
    display: flex;
    margin-left: -6px;
    flex: 1;
  }
  .vc-chrome-field {
    padding-left: 6px;
    width: 100%;
  }
  .vc-chrome-toggle-btn {
    width: 32px;
    text-align: right;
    position: relative;
  }
  .vc-chrome-toggle-icon {
    margin-right: -4px;
    margin-top: 12px;
    cursor: pointer;
    position: relative;
    z-index: 2;
  }
  .vc-chrome-toggle-icon-highlight {
    position: absolute;
    width: 24px;
    height: 28px;
    background: #eee;
    border-radius: 4px;
    top: 10px;
    left: 12px;
  }
  .vc-chrome-hue-wrap {
    position: relative;
    height: 10px;
    margin-bottom: 8px;
  }
  .vc-chrome-alpha-wrap {
    position: relative;
    height: 10px;
  }
  .vc-chrome-hue-wrap .vc-hue {
    border-radius: 2px;
  }
  .vc-chrome-alpha-wrap .vc-alpha-gradient {
    border-radius: 2px;
  }
  .vc-chrome-hue-wrap .vc-hue-picker, .vc-chrome-alpha-wrap .vc-alpha-picker {
    width: 12px;
    height: 12px;
    border-radius: 6px;
    transform: translate(-6px, -2px);
    background-color: rgb(248, 248, 248);
    box-shadow: 0 1px 4px 0 rgba(0, 0, 0, 0.37);
  }
  .vc-chrome-body {
    padding: 16px 16px 12px;
    background-color: #fff;
  }
  .vc-chrome-saturation-wrap {
    width: 100%;
    padding-bottom: 55%;
    position: relative;
    border-radius: 2px 2px 0 0;
    overflow: hidden;
  }
  .vc-chrome-saturation-wrap .vc-saturation-circle {
    width: 12px;
    height: 12px;
  }
  .vc-chrome-fields .vc-input__input {
    font-size: 11px;
    color: #333;
    width: 100%;
    border-radius: 2px;
    border: none;
    box-shadow: inset 0 0 0 1px #dadada;
    height: 21px;
    text-align: center;
  }
  .vc-chrome-fields .vc-input__label {
    text-transform: uppercase;
    font-size: 11px;
    line-height: 11px;
    color: #969696;
    text-align: center;
    display: block;
    margin-top: 12px;
  }
  .vc-chrome__disable-alpha .vc-chrome-active-color {
    width: 18px;
    height: 18px;
  }
  .vc-chrome__disable-alpha .vc-chrome-color-wrap {
    width: 30px;
  }
  .vc-chrome__disable-alpha .vc-chrome-hue-wrap {
    margin-top: 4px;
    margin-bottom: 4px;
  }
</style>
