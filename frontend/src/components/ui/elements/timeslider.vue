<template>
  <div class="ltimeline">
    <div v-if="autoplay" class="ltimeline__buttons">
      <div class="ltimeline__buttons-main">
        <i v-if="timeInterval !== 2000" title="Уменьшить скорость" class="fad fa-backward" @click="slower" />
        <i
          :title="state === 'play' ? 'Остановить' : 'Начать'"
          :class="['fad', `fa-${state === 'pause' ? 'play' : 'pause'}`]"
          @click="changeState"
        />
        <i v-if="timeInterval > 50" title="Увеличить скорость" class="fad fa-forward" @click="speedUp" />
      </div>
      <div class="ltimeline__buttons-speed">
        <span>Скорость: {{ speed }}</span>
        <i v-if="timeInterval !== 1000" title="Откатить изменения" class="fas fa-undo" @click="undo" />
      </div>
    </div>
    <div class="ltimeline__wrapper">
      <div class="ltimeline__wrapper-key left">
        {{ firstDate }}
      </div>
      <client-only>
        <vue-slider
          v-model="index"
          style="z-index: 9999999;"
          v-bind="sliderOptions"
          @change="changeIndex"
        />
      </client-only>
      <div class="ltimeline__wrapper-key right">
        {{ lastDate }}
      </div>
    </div>
  </div>
</template>

<script>
import formatDate from 'intl-dateformat'
const timeZone = {
  timezone: 'Europe/Moscow'
}

export default {
  name: 'LTimeSlider',
  model: {
    props: 'value',
    event: 'change'
  },
  props: {
    maxCount: {
      type: Number,
      required: true,
      default: 0
    },
    dateStart: {
      type: String,
      default: new Date().toLocaleDateString(),
      required: true
    },
    dateEnd: {
      type: String,
      default: new Date().toLocaleDateString(),
      required: true
    },
    keyFormat: {
      type: String,
      default: 'DD.MM.YYYY hh:mm:ss'
    },
    value: {
      type: Number,
      default: 0
    },
    autoplay: {
      type: Boolean,
      default: false
    },
    interval: {
      type: Number,
      default: 1
    },
    tooltipFormatter: {
      type: [String, Function],
      default: ''
    }
  },
  data () {
    return {
      state: 'pause',
      fInterval: null,
      index: this.value,
      timeInterval: 1000
    }
  },
  computed: {
    firstDate () {
      if (this.maxCount > 0) {
        return this.keyFormat ? formatDate(this.keyToDate(this.dateStart), this.keyFormat, timeZone) : this.dateStart
      }
      return ''
    },
    lastDate () {
      if (this.maxCount > 0) {
        return this.keyFormat ? formatDate(this.keyToDate(this.dateEnd), this.keyFormat, timeZone) : this.dateEnd
      }
      return ''
    },
    speed () {
      return (1 / this.timeInterval).toFixed(4)
    },
    sliderOptions () {
      return {
        dotSize: 14,
        width: 'auto',
        height: 4,
        contained: false,
        direction: 'ltr',
        data: null,
        min: 0,
        max: this.maxCount - 1,
        interval: this.interval,
        disabled: false,
        clickable: true,
        duration: 0.5,
        adsorb: false,
        lazy: true,
        tooltip: 'active',
        tooltipPlacement: 'top',
        tooltipFormatter: this.tooltipFormatter,
        useKeyboard: false,
        keydownHook: null,
        dragOnClick: false,
        enableCross: true,
        fixed: false,
        // minRange: void 0,
        // maxRange: void 0,
        order: true,
        marks: false,
        // dotOptions: void 0,
        process: true
        // dotStyle: void 0,
        // railStyle: void 0,
        // processStyle: void 0,
        // tooltipStyle: void 0,
        // stepStyle: void 0,
        // stepActiveStyle: void 0,
        // labelStyle: void 0,
        // labelActiveStyle: void 0
      }
    }
  },
  watch: {
    maxCount () {
      this.index = 0
    }
  },
  destroyed () {
    clearInterval(this.fInterval)
    this.fInterval = null
  },
  methods: {
    undo () {
      this.timeInterval = 1000
      if (this.state === 'play') {
        this.setAutoInterval()
      }
    },
    slower () {
      this.timeInterval += 50
      if (this.state === 'play') {
        this.setAutoInterval()
      }
    },
    speedUp () {
      if (this.timeInterval > 50) {
        this.timeInterval -= 50
      }
      if (this.state === 'play') {
        this.setAutoInterval()
      }
    },
    changeIndex (e) {
      this.index = e
      this.$emit('change', this.index)
    },
    changeState () {
      this.state = this.state === 'pause' ? 'play' : 'pause'
      if (this.state === 'pause') {
        return clearInterval(this.fInterval)
      }
      this.setAutoInterval()
    },
    setAutoInterval () {
      if (this.fInterval) {
        clearInterval(this.fInterval)
        this.fInterval = null
      }
      this.fInterval = setInterval(() => {
        if (this.index === this.maxCount - 1) {
          this.state = 'pause'
          return clearInterval(this.fInterval)
        }
        this.index++
        this.$emit('change', this.index)
      }, this.timeInterval)
    },
    keyToDate (key) {
      const englishRegexp = /([12]\d{3}-(0[1-9]|1[0-2])-(0[1-9]|[12]\d|3[01]))/
      const russianRegexp = /((0[1-9]|[12]\d|3[01]).(0[1-9]|1[0-2]).[12]\d{3})/
      const splitting = key.split(' ')
      let time = []
      if (splitting.length === 2) {
        time = splitting[1].split(':')
      }

      if (englishRegexp.test(key)) {
        const date = splitting[0].split('-')
        return splitting.length === 2
          ? new Date(date[0], date[1] - 1, date[2], time[0], time[1], time[2])
          : new Date(date[0], date[1] - 1, date[2])
      } else if (russianRegexp.test(key)) {
        const date = splitting[0].split('.')
        return splitting.length === 2
          ? new Date(date[2], date[1] - 1, date[0], time[0], time[1], time[2])
          : new Date(date[2], date[1] - 1, date[0])
      } else {
        return ''
      }
    }
  }
}
</script>

<style lang="scss" scoped>
.ltimeline {
  width: 100%;
  background-color: rgba(0, 0, 0, 0.7);
  padding: 10px 15px;
  &__buttons {
    position: relative;
    display: flex;
    align-items: center;
    justify-content: center;
    &-main {
      // display: flex;
      // flex-basis: 80%;
      // justify-content: center;
    }
    &-speed {
      position: absolute;
      right: 0;
      color: #fff;
      i, span {
        font-size: 1.1rem !important;
        margin: 0 10px !important;
      }
    }
    i {
      color: #fff;
      font-size: 1.3rem;
      padding: 10px;
      margin: 0 15px;
      &:hover {
        cursor: pointer;
        color: lighten(#007bff, 25%)
      }
      &.fa-pause {
        color: var(--warning)
      }
    }
  }
  &__wrapper {
    display: grid;
    grid-template-columns: 0.15fr 1fr 0.15fr;
    column-gap: 1rem;
    align-items: center;
    &-key {
      display: flex;
      align-items: center;
      color: white;
      font-weight: bold;
      &.left {
        justify-content: flex-start;
      }
      &.right {
        justify-content: flex-end;
      }
    }
  }
}
</style>
