<template>
  <div class="card">
    <div
      :class="{
        'card-header':true,
        'active-panel':isActive
      }"
      @click="selectPanel()"
    >
      <h4 class="card-title">
        <a
          data-toggle="collapse"
          data-parent="#accordion"
        >
          {{ name }}
        </a>
      </h4>
      <i
        :class="{
          'float-right': true,
          'fas': true,
          'fa-minus': isActive,
          'fa-plus': !isActive
        }"
      />
      <span
        v-show="showCountElement"
        class="float-right mr-4"
      >
        {{ existElementCount }}/{{ allElementCount }}
      </span>
    </div>
    <div
      :id="id"
      :class="{
        'panel-collapse': true,
        'collapse': true,
        'in': isActive,
        'show': isActive
      }"
    >
      <div
        v-show="isActive"
        class="card-body"
      >
        <fade-transition>
          <slot />
        </fade-transition>
      </div>
    </div>
  </div>
</template>

<script>
import { FadeTransition } from 'vue2-transitions'

export default {
  name: 'LAccordionPanel',
  components: {
    FadeTransition
  },
  props: {
    id: {
      type: String,
      default: ''
    },
    name: {
      type: String,
      default: ''
    },
    selected: {
      type: Boolean,
      default: false
    },
    showCountElement: {
      type: Boolean,
      default: false
    },
    existElementCount: {
      type: Number,
      default: 0
    },
    allElementCount: {
      type: Number,
      default: 0
    }
  },
  data: () => ({
    selectedPanel: false
  }),
  computed: {
    isActive () {
      return this.selectedPanel
    }
  },
  watch: {
    isActive () {
      this.$emit('onActive', this.name)
    }
  },
  mounted () {
    this.selectedPanel = this.selected
  },
  methods: {
    selectPanel () {
      this.selectedPanel = !this.selectedPanel
    }
  }
}
</script>

<style scoped>
  .card .card-header {
    cursor: pointer;
  }

  .active-panel {
    background-color: #007bff;
    color: #fff;
  }
</style>
