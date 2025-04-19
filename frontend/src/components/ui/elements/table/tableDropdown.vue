<template>
  <div
    ref="button"
    v-click-outside="documentListener"
    class="ltable-dropdown"
  >
    <l-button
      :color="open ? 'secondary' : 'default'"
      icon="fas fa-caret-down"
      size="sm"
      @click="toggleOpen"
    >
      Столбцы
    </l-button>
    <div
      v-show="open"
      class="ltable-dropdown__elements"
    >
      <div class="btn-group btn-group-sm btn-block">
        <l-button
          size="sm"
          color="default"
          style="width:50%"
          @click="all(true)"
        >
          Отобразить всё
        </l-button>
        <l-button
          size="sm"
          color="default"
          style="width:50%"
          @click="all(false)"
        >
          Скрыть всё
        </l-button>
      </div>
      <perfect-scrollbar
        ref="scroll"
        :options="scrollBar"
      >
        <draggable v-model="columns" class="ltable-dropdown__elements-scroll">
          <l-button
            v-for="column in columns"
            :key="column.key"
            size="sm"
            :color="column.visible ? 'primary' : 'secondary'"
            :style="{
              marginBottom: '10px',
              width: '100%'
            }"
            @click.stop.prevent="column.visible = !column.visible"
          >
            {{ column.label }}
          </l-button>
        </draggable>
      </perfect-scrollbar>
    </div>
  </div>
</template>

<script>
export default {
  name: 'LTableDropdown',
  props: {
    headers: {
      type: Array,
      default: () => ([])
    }
  },
  data: () => ({
    open: false,
    scrollBar: {
      suppressScrollX: true
    }
  }),
  computed: {
    columns: {
      get () {
        return this.headers
      },
      set (value) {
        this.$emit('changeHeader', value)
      }
    },
    maxHeight () {
      const btnHeight = 31
      const marginBottom = 10
      return `${(btnHeight + marginBottom) * this.columns.length}px`
    }
  },
  methods: {
    documentListener () {
      if (this.open) {
        this.open = false
      }
      // event.stopPropagation()
      // event.preventDefault()
    },
    toggleOpen () {
      this.open = !this.open
    },
    all (visible) {
      this.columns.forEach((item) => {
        item.visible = visible
      })
    }
  }
}
</script>

<style lang="scss" scoped>
.ltable-dropdown {
  position: relative;
  width: 111px;
  &__elements {
    max-width: 280px;
    max-height: 300px;
    width: 280px;
    height: max-content;
    background-color: #fff;
    position: absolute;
    right: 0;
    margin-top: .5rem;
    z-index: 9999;
    border: 1px solid #dee2e6;
    padding: .5rem;
    overflow: hidden;
    &-scroll {
      max-height: 230px;
      height: 100%;
      // height: 100%;
      // max-height: 270px;
    }
    > .btn-group {
      margin-bottom: .5rem;
    }
  }
}
</style>
