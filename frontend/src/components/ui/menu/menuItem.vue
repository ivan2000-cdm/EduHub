<template>
  <li
    style="cursor: pointer"
    :class="{
      'nav-item': true,
      'has-treeview': hasChildren,
      'menu-open': opened
    }"
  >
    <a
      :href="link ? link : undefined"
      :class="{
        'nav-link': true,
        'active': hasChildren ? someDescendantLeafsSelected : activeLink
      }"
      @click.prevent.stop="onClickLink"
    >
      <i
        :class="{
          'nav-icon': true,
          [icon]: true,
        }"
      />
      <p>
        {{ label }}
        <i
          v-if="hasChildren"
          class="right fas fa-angle-left"
        />
      </p>
    </a>
    <template v-if="hasChildren">
      <collapse-transition>
        <ul v-show="opened" class="nav nav-treeview">
          <l-menu-item
            v-for="(child, i) in children"
            :key="`child-${i}`"
            :items="child"
            :value="value"
            :label-field="labelField"
            :children-field="childrenField"
            :link-field="linkField"
            :icon-field="iconField"
            @input="childInput"
          />
        </ul>
      </collapse-transition>
    </template>
  </li>
</template>

<script>
import { CollapseTransition } from 'vue2-transitions'

export default {
  name: 'LMenuItem',
  components: {
    CollapseTransition
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
      default: 'items'
    },
    linkField: {
      type: String,
      default: 'link'
    },
    iconField: {
      type: String,
      default: 'icon'
    },
    value: {
      type: [Object, Array],
      default: () => ([])
    }
  },
  data () {
    return {
      opened: false
    }
  },
  computed: {
    label () {
      return this.items[this.labelField]
    },
    icon () {
      return this.items[this.iconField] || 'fad fa-circle'
    },
    link () {
      return this.items[this.linkField]
    },
    selectedItem () {
      return this.value.findIndex(({ label }) => label === this.label) !== -1
    },
    activeLink () {
      const { path } = this.$route
      return path !== '/' ? path.includes(this.link) : false
    },
    children () {
      return this.items[this.childrenField].filter(({ disabled }) => !disabled)
    },
    hasChildren () {
      return !!this.items[this.childrenField] && this.items[this.childrenField].length > 0
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
      return leafs
    },
    hasSelection () {
      return !!this.value && Object.keys(this.value).length > 0
    },
    someDescendantLeafsSelected () {
      return this.allDescendantLeafs.some((leaf) => {
        if (
          (Array.isArray(this.value) && this.value.length > 0) ||
          Object.keys(this.value).length > 0
        ) {
          return this.value === leaf
        }
        const { path } = this.$route
        return path !== '/' ? path.includes(leaf.link) : false
      })
    }
  },
  methods: {
    onClickLink () {
      if (this.hasChildren) {
        this.opened = !this.opened
      } else if (this.link) {
        this.$router.push(this.link)
        this.$emit('input', this.items)
      } else {
        this.$emit('click')
      }
    },
    childInput (selection) {
      this.$emit('input', selection)
    }
  }
}
</script>

<style lang="scss" scoped>
.nav {
  &-item {
  }

  &-link {
    display: flex;
    align-items: center;
    // // width: 72%;
    > i {
      margin-right: 5px !important;
    }

    > p {
      white-space: normal;
    }
  }
}
</style>
