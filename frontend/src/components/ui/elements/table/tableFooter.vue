<script>
export default {
  name: 'LTableFooter',
  functional: true,
  render (createElement, ctx) {
    return createElement('div', {
      class: 'ltable__footer'
    }, ctx.children)
  }
}
</script>

<style lang="scss">
.ltable__footer {
  padding: 10px;
  border-top: 2px solid $borderColor;
  &-container {
    display: grid;
    grid-template-columns: 0.2fr 0.25fr 1fr;
    column-gap: 1rem;
    > div {
      display: flex;
      align-items: center;
    }
    @media only screen and (max-width: 992px) {
      grid-template-columns: 0.4fr 0.25fr 1fr;
    }
    @media only screen and (max-width: 768px) {
      grid-template-columns: 1fr 1fr;
      .ltable__footer-range {
        display: none
      }
    }
  }
  &-select {
    > .lselect {
      width: 30%;
      margin: 0 10px;
    }
  }
  &-pagination {
    justify-self: flex-end;
  }
}
</style>
