<template>
  <ol class="breadcrumb float-sm-right">
    <template
      v-for="(item, i) in data"
    >
      <li
        :key="i"
        :class="{
          'breadcrumb-item': true,
          'active': item.path === path
        }"
      >
        <nuxt-link
          v-if="item.path !== path"
          :to="item.path"
          :class="{
            'not-click': item.path === ''
          }"
          style="position: relative"
        >
          {{ item.name }}
        </nuxt-link>
        <template v-else>
          <span>{{ item.name }}</span>
        </template>
      </li>
    </template>
  </ol>
</template>

<script>
export default {
  name: 'LBreadcrumbs',
  props: {
    data: {
      type: Array,
      default: () => []
    }
  },
  computed: {
    path () {
      const { fullPath: path } = this.$route
      return path
    }
  }
}
</script>

<style lang="scss" scoped>
.breadcrumb {
  padding: 0;
  margin-bottom: 0;
  background: none;
  &-item {
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
  }
}
.not-click {
  cursor: not-allowed;
}
</style>
