<template>
  <div
    :class="{
      'ltable__cell': true,
      'swap': swap
    }"
    @click="cellClick"
  >
    <slot />
  </div>
</template>

<script>
export default {
  name: 'LTableCell',
  props: {
    swap: {
      type: Boolean,
      default: false
    }
  },
  methods: {
    cellClick () {
      this.$emit('click')
    }
  }
}
</script>

<style lang="scss" scoped>
.ltable__cell {
  display: flex;
  align-items: center;
  border-right: $borderTable;
  padding: 6px 10px;
  @media only screen and (max-width: 576px) {
    padding: 6px 5px;
  }
}

</style>
