<template>
  <div
    :class="{
      ltable__row: true,
      selected: isSelected,
      group: group && children,
    }"
  >
    <div
      :class="{
        'ltable__row-wrapper': true,
        last: group && children === undefined,
      }"
      :style="columnsStyle"
    >
      <template v-if="group && label">
        <l-table-cell v-if="isCheckbox">
          <l-checkbox
            :indeterminate="indeterminate"
            :value="hasChildren ? allDescendantLeafsSelected : item[propValue]"
            :model-value="hasChildren ? allDescendantLeafsSelected : value"
            style="margin: 0px !important"
            label-styles="margin-left: -2px"
            :disabled="disabledCheckboxes"
            @change="selectValue"
          />
        </l-table-cell>
        <l-table-cell @click="onGroupRowClick">
          <i
            :class="['label-icon', opened ? 'fas fa-minus' : 'fas fa-plus']"
            @click="opened = !opened"
          />
          {{ label }}
        </l-table-cell>
      </template>
      <template v-else>
        <l-table-cell
          v-for="header in headers"
          :key="header.key"
          :class="`${item.color ? 'bg-' + item.color : ''}`"
          :title="`${item.hint ? item.hint : ''}`"
          @click="onRowClick"
        >
          <template v-if="isLoading">
            <l-skeleton />
          </template>
          <template v-else-if="header.key === 'checkbox'">
            <l-checkbox
              :value="hasChildren ? allDescendantLeafsSelected : item[propValue]"
              :model-value="hasChildren ? allDescendantLeafsSelected : value"
              style="margin: 0px !important"
              label-styles="margin-left: -2px"
              :disabled="disabledCheckboxes"
              @change="selectValue"
            />
          </template>
          <slot
            v-else
            :id="item.id"
            :name="`column.${header.key}`"
            :item="item[header.key]"
            :row="item"
            :column="header"
          >
            <template v-if="header.key === 'id'">
              <span class="flex jc-end" style="width: 100%">{{ item[header.key] }}</span>
            </template>
            <template v-else-if="header.type === 'color'">
              <div class="flex center" style="width: 100%">
                <span class="square" :style="`background-color: ${item[header.key]};`" />
              </div>
            </template>
            <template v-else>
              {{ item[header.key] }}
            </template>
          </slot>
        </l-table-cell>
      </template>
    </div>
    <div v-if="opened" class="ltable__row-children">
      <l-table-row
        v-for="(child, i) in children"
        :key="`child-${i}`"
        :headers="headers"
        :item="child"
        :is-loading="isLoading"
        :is-selected="isSelected"
        :is-checkbox="isCheckbox"
        :disabled-checkboxes="disabledCheckboxes"
        :group="group"
        :level="level + 1"
        :prop-value="propValue"
        :value="value"
        @input="childInput"
        @onRowClick="onChildRowClick($event)"
        @onGroupRowClick="$emit('onGroupRowClick', $event)"
        @selectCheckbox="$emit('selectCheckbox', $event)"
      >
        <template v-for="slot in Object.keys($slots)" v-slot:[slot]="scope">
          <slot :name="slot" v-bind="scope" />
        </template>
      </l-table-row>
    </div>
  </div>
</template>

<script>
// import LTableRow from '@/components/ui/elements/table/tableRow.vue'
import LTableCell from '@/components/ui/elements/table/tableCell.vue'
import LCheckbox from '@/components/ui/form/checkbox.vue'
import LSkeleton from '@/components/ui/elements/skeleton.vue'

export default {
  name: 'LTableRow',
  components: {
    LSkeleton,
    LCheckbox,
    LTableCell,
  },
  props: {
    headers: {
      type: Array,
      default: () => [],
    },
    item: {
      type: [Object, Array],
      default: () => ({}),
    },
    isLoading: {
      type: Boolean,
      default: false,
    },
    isSelected: {
      type: Boolean,
      default: false,
    },
    isCheckbox: {
      type: Boolean,
      default: false,
    },
    disabledCheckboxes: {
      type: Boolean,
      default: false,
    },
    group: {
      type: Boolean,
      default: false,
    },
    level: {
      type: Number,
      default: 0,
    },
    propValue: {
      type: String,
      default: 'id',
    },
    value: {
      type: [Object, Array],
      default: () => [],
    },
    colorProp: {
      type: Object,
      default: () => ({
        background: null,
        text: null,
      }),
    },
  },
  data: () => ({
    opened: false,
  }),
  computed: {
    columnsStyle() {
      return {
        'grid-template-columns':
          this.group && this.label
            ? `${this.isCheckbox ? '40px' : ''} 1fr`
            : this.headers.map((item) => item.width ?? 'minmax(185px, 1fr)').join(' '),
        'background-color': this.colorProp.background ? this.item[this.colorProp.background] : null,
        color: this.colorProp.text ? this.item[this.colorProp.text] : null,
      }
    },
    isSelectedRow() {
      return !!this.headers.find(({ key }) => key === 'checkbox')
    },
    label() {
      return this.item.label
    },
    children() {
      return this.item.children
    },
    open() {
      return this.item.open
    },
    hasChildren() {
      return this.children && this.children.length > 0
    },
    hasSelection() {
      return this.value && this.value.length > 0
    },
    allDescendantLeafs() {
      const leafs = []
      const searchTree = (items) => {
        if (items.children && items.children.length > 0) {
          items.children.forEach((child) => searchTree(child))
        } else {
          leafs.push(items)
        }
      }
      searchTree(this.item)
      return leafs
    },
    someDescendantLeafsSelected() {
      return (
        this.hasSelection &&
        this.allDescendantLeafs.some((leaf) => this.value.includes(leaf[this.propValue]))
      )
    },
    allDescendantLeafsSelected() {
      return (
        this.hasChildren &&
        this.hasSelection &&
        this.allDescendantLeafs.every((leaf) => this.value.includes(leaf[this.propValue]))
      )
    },
    indeterminate() {
      return (
        this.hasSelection &&
        this.hasChildren &&
        this.someDescendantLeafsSelected &&
        !this.allDescendantLeafsSelected
      )
    },
  },
  watch: {
    open(val) {
      this.opened = !!val
    },
  },
  mounted() {
    this.opened = !!this.open
  },
  methods: {
    selectValue(selection) {
      this.$emit('selectCheckbox', { selection, item: this.item })
      if (this.hasChildren) {
        if (this.allDescendantLeafsSelected) {
          this.allDescendantLeafs.forEach((leaf) => {
            const index = this.value.indexOf(leaf[this.propValue])
            // eslint-disable-next-line vue/no-mutating-props
            this.value.splice(index, 1)
          })
        } else {
          this.allDescendantLeafs.forEach((leaf) => {
            const index = this.value.indexOf(leaf[this.propValue])
            if (index === -1) {
              // eslint-disable-next-line vue/no-mutating-props
              this.value.push(leaf[this.propValue])
            }
          })
        }
        this.$emit('input', this.value)
      } else if (Array.isArray(this.value)) {
        this.allDescendantLeafs.forEach((leaf) => {
          const index = this.value.indexOf(leaf[this.propValue])
          // eslint-disable-next-line vue/no-mutating-props
          index === -1 ? this.value.push(leaf[this.propValue]) : this.value.splice(index, 1)
        })
        this.$emit('input', this.value)
      } else {
        this.$emit('input', selection)
      }
    },
    childInput(selection) {
      this.$emit('input', selection)
    },
    onRowClick() {
      this.$emit('onRowClick', this.item)
    },
    onChildRowClick(item) {
      this.$emit('onRowClick', item)
    },
    onGroupRowClick() {
      this.$emit('onGroupRowClick', {
        item: this.item,
        level: this.level,
        opened: this.opened,
      })
    },
  },
}
</script>

<style lang="scss" scoped>
.square {
  display: block;
  width: 25px;
  height: 25px;
  border: 1px solid var(--secondary);
}

.label {
  font-weight: bold;
  &-icon {
    margin-right: 10px;
    color: $primary;
  }
}
.ltable__row {
  &-wrapper {
    display: grid;
    width: 100%;
    border-bottom: $borderTable;
    // &.last {
    //   .ltable__cell {
    //     padding-left: 3.7rem;
    //   }
    // }
  }
  &.group {
    background-color: #fff !important;
  }
  &.selected {
    background-color: rgba($primary, 0.1) !important;
  }
  &:nth-child(odd) {
    background-color: rgba($gray, 0.08);
  }
  &:hover {
    cursor: pointer;
    background-color: rgba($grayDark, 0.12);
  }
  &-children {
    background-color: #fff !important;
    .label {
      &-icon {
        margin: 0 10px;
      }
    }
    &:nth-child(odd) {
      background-color: rgba($gray, 0.08);
    }
  }
}

.cell {
  &__checkbox {
    width: 45px;
    border-right: $borderTable;
  }
  &__label {
    margin-left: 10px;
  }
}
</style>
