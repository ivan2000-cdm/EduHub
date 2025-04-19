<template>
  <div
    class="card card-primary card-outline card-outline-tabs"
    :class="{
      vertical: vertical
    }"
  >
    <div class="card-header p-0 border-bottom-0">
      <ul
        class="nav nav-tabs"
        :class="{
          vertical: vertical
        }"
      >
        <li
          v-for="(tab, i) in tabs"
          :key="i"
          :class="{
            'nav-item': true,
            'vertical': vertical
          }"
          @click="selectTab(tab)"
        >
          <span
            :class="{
              'nav-link': true,
              'active': tab.isActive,
              'vertical': vertical,
              'translucentText': tab.disabled
            }"
          >
            {{ tab.name }}
          </span>
        </li>
      </ul>
    </div>
    <div :class="['card-body', withPadding ? '' : 'p-0']">
      <div class="tab-content">
        <slot />
      </div>
    </div>
  </div>
</template>

<script>

export default {
  name: 'LTabs',
  props: {
    vertical: {
      type: Boolean,
      default: false
    },
    withPadding: {
      type: Boolean,
      default: true
    }
  },
  data: () => ({
    tabs: []
  }),
  mounted () {
    this.tabs = this.$children
  },
  methods: {
    selectTab (selectedTab) {
      if (!selectedTab.disabled) {
        this.tabs.forEach((tab) => {
          tab.isActive = (tab.name === selectedTab.name)
        })
      }
    }
  }
}
</script>

<style lang="scss" scoped>
.card {
  &.vertical {
    display: grid;
    grid-template-columns: .5fr 1fr;
  }
}
.nav {
  &.vertical {
    display: flex;
    flex-direction: column;
  }
  &-tabs {}
  &-item {
    &.vertical:not(:last-child) {
      border-bottom: 1px solid rgb(222, 226, 230) !important;
    }
  }
  &-link {
    border-top-left-radius: 0;
    border-top-right-radius: 0;
    &.active {
      border-top: 3px solid #007bff !important;
      &.vertical {
        border-top: 1px solid rgb(222, 226, 230) !important;
        border-right: 3px solid #007bff !important;
      }
    }
    &.translucentText {
      opacity: 0.5;
      cursor: not-allowed !important;
    }
    &:hover {
      cursor: pointer;
    }
  }
}
</style>
