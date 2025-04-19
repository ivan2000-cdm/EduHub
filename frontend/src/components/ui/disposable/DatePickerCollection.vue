<template>
  <div
    style="margin: -30px 8px 0; width: 100%; height: 100%"
  >
    <label
      :for="name"
      class="datepicker-collection"
    >
      {{ label }}
    </label>
    <div
      :id="name"
      class="row flex-column justify-content-between align-items-center w-100 h-100 border p-3"
    >
      <div class="row justify-content-around w-100">
        <l-datetimepicker
          v-model="dates"
          style="flex: 1"
          format="DD.MM.YYYY HH:mm:ss"
          value-type="YYYY-MM-DD HH:mm:ss"
          type="datetime"
          label="Диапазон дат"
          :range="true"
        />
        <l-button
          style="height: 38px; flex: 0 0 45px"
          color="primary"
          icon="fas fa-plus"
          @click="onAdd"
        />
      </div>
      <perfect-scrollbar
        ref="scrollbar"
        :options="scrollBar"
        style="width:100%"
      >
        <div style="max-height: 100px">
          <template v-for="(item, i) in content">
            <div :key="i" class="flex justify-content-between p-1">
              <span>{{ item.from | dateTime }} — {{ item.to | dateTime }}</span>
              <l-button
                style="height: 24px; flex: 0 0 45px"
                color="danger"
                icon="fas fa-times"
                @click="onDelete(i)"
              />
            </div>
          </template>
        </div>
      </perfect-scrollbar>
    </div>
  </div>
</template>

<script>
import formatDate from 'intl-dateformat'
import LButton from '../elements/button'

export default {
  name: 'DoubleDatePickerCollection',
  components: { LButton },
  filters: {
    dateTime (value) {
      return formatDate(new Date(value), 'DD.MM.YYYY hh:mm:ss')
    }
  },
  props: {
    value: {
      type: Array,
      default: null
    },
    label: {
      type: String,
      default: ''
    },
    name: {
      type: String,
      default: ''
    }
  },
  data () {
    return {
      content: null,
      dates: [],
      scrollBar: {
        suppressScrollY: false
      }
    }
  },
  watch: {
    value (val) {
      this.content = JSON.parse(JSON.stringify(val))
    }
  },
  mounted () {
    this.content = JSON.parse(JSON.stringify(this.value))
  },
  methods: {
    onAdd () {
      this.content.push({ from: this.dates[0], to: this.dates[1] })
      this.dates = []
      this.$emit('input', this.content)
    },
    onDelete (index) {
      this.content.splice(index, 1)
      this.$emit('input', this.content)
    }
  }
}
</script>

<style scoped>
.datepicker-collection {
  font-weight: 500;
  background: #fff;
  font-size: 14px;
  transition: all .2s linear;
  color: var(--secondary);
  position: relative;
  display: inline-block;
  top: 18px;
}
</style>
