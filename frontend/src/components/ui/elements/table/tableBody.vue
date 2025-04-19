<script>
export default {
  name: 'LTableBody',
  render (createElement) {
    return createElement('div', {
      class: 'ltable__body'
    }, this.$slots.default)
  }
}
</script>

<style lang="scss" scoped>
.ltable__body {}
</style>
