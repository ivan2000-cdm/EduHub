<template>
  <ul key="ul" :class="menuClass">
    <slot />
  </ul>
</template>

<script>
export default {
  name: 'LMenu',
  props: {
    classes: {
      type: String,
      default: 'nav'
    },
    treeview: {
      type: Boolean,
      default: false
    }
  },
  computed: {
    menuClass () {
      if (this.treeview) {
        return 'nav nav-treeview'
      }
      return this.classes
    }
  },
  mounted () {
    // console.log(this.$children)
  }
}
</script>

<style lang="scss" scoped>
.nav {
  &-treeview {
    display: block;
    list-style: none;
    padding: 0;
    transition: all .5s;
  }
  &-legacy {
    margin: -.25rem -.5rem 0 .1rem !important
  }
  &-flat {
    margin: 0 !important
  }
}
</style>
