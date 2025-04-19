<template>
  <nav class="main-header navbar navbar-expand navbar-white navbar-light">
    <ul class="navbar-nav align-items-center">
      <li class="nav-item">
        <span class="nav-link" role="button" @click="onCollapse($event)">
          <i class="fas fa-bars" />
        </span>
      </li>
      <slot name="leftmenu" />
    </ul>

    <ul class="navbar-nav ml-auto">
      <slot name="rightmenu" />
    </ul>
  </nav>
</template>

<script>
export default {
  name: 'LHeader',
  methods: {
    onCollapse (event) {
      this.$emit('onCollapse', event)
    }
  }
}
</script>

<style lang="scss" scoped>
</style>
