<template>
  <div class="ltreeview">
    <div
      :class="{
        'ltreeview_row': true,
        'last': children === undefined
      }"
    >
      <i
        v-if="children"
        :class="{
          'fas fa-caret-right': true,
          'opened': opened
        }"
        @click="opened = !opened"
      />
      <l-checkbox
        v-if="value"
        :name="label"
        :label="label"
        :model-value="hasChildren ? allDescendantLeafsSelected : value"
        :value="hasChildren ? allDescendantLeafsSelected : items[propValue]"
        :indeterminate="indeterminate"
        :disabled="disabled"
        style="margin: 0 !important"
        @change="selectValue"
      />
      <div class="ltreeview_row__slot">
        <slot
          :vehicle="items"
          :hasChildren="children !== undefined"
        />
      </div>
    </div>
    <div
      v-if="opened"
      :class="{
        'ltreeview_children': true
      }"
    >
      <l-treeview
        v-for="(child, i) in children"
        :key="`child-${i}`"
        :items="child"
        :value="value"
        :label-field="labelField"
        :children-field="childrenField"
        :prop-value="propValue"
        @input="childInput"
      >
        <template v-for="slot in Object.keys($scopedSlots)" v-slot:[slot]="scope">
          <slot
            :name="slot"
            v-bind="scope"
          />
        </template>
      </l-treeview>
    </div>
  </div>
</template>

<script>
import LTreeview from './treeview'

export default {
  name: 'LTreeview',
  components: {
    LTreeview
  },
  props: {
    items: {
      type: [Object, Array],
      default: () => ([])
    },
    labelField: {
      type: String,
      default: 'name'
    },
    childrenField: {
      type: String,
      default: 'children'
    },
    value: {
      type: [Object, Array],
      default: () => ([])
    },
    propValue: {
      type: String,
      default: 'id'
    }
  },
  data () {
    return {
      open: false
    }
  },
  computed: {
    label () {
      return this.items[this.labelField]
    },
    opened: {
      get () {
        return this.items.open || this.open
      },
      set (value) {
        this.open = value
        if (this.items.open) {
          this.items.open = false
        }
      }
    },
    children () {
      return this.items[this.childrenField]
    },
    hasChildren () {
      return !!this.items[this.childrenField] && this.items[this.childrenField].length > 0
    },
    hasSelection () {
      return !!this.value && this.value.length > 0
    },
    disabled () {
      return this.items.disabled
    },
    allDescendantLeafs () {
      const leafs = []
      const searchTree = (items) => {
        if (!!items[this.childrenField] && items[this.childrenField].length > 0) {
          items[this.childrenField].forEach(child => searchTree(child))
        } else {
          leafs.push(items)
        }
      }
      searchTree(this.items)
      return leafs.filter(({ disabled }) => !disabled)
    },
    someDescendantLeafsSelected () {
      return this.hasSelection &&
        this.allDescendantLeafs.some(leaf => this.value
          .some(sel => sel === leaf[this.propValue])
        )
    },
    allDescendantLeafsSelected () {
      const everyLeafSelected = this.allDescendantLeafs.length > 0
        ? this.allDescendantLeafs.every(leaf => this.value.some(sel => sel === leaf[this.propValue]))
        : false

      return this.hasChildren && this.hasSelection && everyLeafSelected
    },
    indeterminate () {
      return this.hasSelection && this.hasChildren &&
        this.someDescendantLeafsSelected && !this.allDescendantLeafsSelected
    }
  },
  methods: {
    selectValue (selection) {
      if (this.hasChildren) {
        if (this.allDescendantLeafsSelected) {
          this.allDescendantLeafs
            .filter(({ disabled }) => !disabled)
            .forEach((leaf) => {
              const index = this.value.indexOf(leaf[this.propValue])
              this.value.splice(index, 1)
            })
        } else {
          this.allDescendantLeafs
            .filter(({ disabled }) => !disabled)
            .forEach((leaf) => {
              const index = this.value.indexOf(leaf[this.propValue])
              if (index === -1) {
                this.value.push(leaf[this.propValue])
              }
            })
        }
        this.$emit('input', this.value)
      } else if (Array.isArray(this.value)) {
        this.allDescendantLeafs.forEach((leaf) => {
          const index = this.value.indexOf(leaf[this.propValue])
          index === -1 ? this.value.push(leaf[this.propValue]) : this.value.splice(index, 1)
        })
        this.$emit('input', this.value)
      } else {
        this.$emit('input', selection)
      }
    },
    childInput (selection) {
      this.$emit('input', selection)
    }
  }
}
</script>

<style lang="scss" scoped>
.ltreeview {
  &_row {
    display: flex;
    align-items: center;
    &.last {
      margin-top: .2rem;
      margin-left: .5rem;
    }
    &__slot {
      margin-left: 10px;
    }
    i {
      margin-right: .5rem;
      transition: .5s all;
      color: #a3a3a8;
      &.opened {
        transform: rotate(90deg);
      }
    }
  }
  &_children {
    margin-top: .2rem;
    margin-left: 1rem;
  }
}
</style>
