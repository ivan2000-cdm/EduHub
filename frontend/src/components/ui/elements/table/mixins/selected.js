export default {
  props: {
    isSelectedRow: {
      type: Boolean,
      default: false
    },
    value: {
      type: [Object, Array],
      default: () => ([])
    },
    disabledCheckboxes: {
      type: Boolean,
      default: false
    }
  },
  data: () => ({
    selectRowOptions: {
      selectAll: false
    }
  }),
  computed: {
    hasSelection () {
      return this.value && this.value.length > 0
    },
    allDescendantLeafs () {
      const leafs = []
      const searchTree = (items) => {
        if (items.children && items.children.length > 0) {
          items.children.forEach(child => searchTree(child))
        } else {
          leafs.push(items)
        }
      }
      this.data.forEach(item => searchTree(item))
      return leafs
    },
    someDescendantLeafsSelected () {
      return this.hasSelection &&
        this.allDescendantLeafs.some(leaf => this.value
          .some(sel => sel === leaf.id)
        )
    },
    allDescendantLeafsSelected () {
      return this.hasSelection &&
        this.allDescendantLeafs.every(leaf => this.value
          .some(sel => sel === leaf.id)
        )
    },
    indeterminate () {
      return this.hasSelection && this.rowGroup &&
        this.someDescendantLeafsSelected && !this.allDescendantLeafsSelected
    }
  },
  methods: {
    selectAll () {
      this.selectRowOptions.selectAll = !this.selectRowOptions.selectAll
      if (this.allDescendantLeafsSelected || !this.selectRowOptions.selectAll) {
        this.allDescendantLeafs.forEach((leaf) => {
          const index = this.value.indexOf(leaf.id)
          this.value.splice(index, 1)
        })
      } else {
        this.allDescendantLeafs.forEach((leaf) => {
          const index = this.value.indexOf(leaf.id)
          if (index === -1) {
            this.value.push(leaf.id)
          }
        })
      }
      this.$emit('input', this.value)
    }
  }
}
