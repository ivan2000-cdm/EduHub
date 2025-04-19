<template>
  <div class="ltable__header ltable__header-fixed" :style="columnsStyle">
    <l-table-cell
      v-for="header in headers"
      :key="header.key"
      :swap="true"
      class="ltable__header-text"
    >
      <template v-if="!isLoading">
        <div v-if="header.key !== 'checkbox'" :class="['head']" @click="onSort(header.key)">
          <span class="head__text">{{ header.label }}</span>
          <span class="head__sort">
            <i
              v-if="sort.key === header.key && sort.order === 'asc'"
              :class="[
                'fad fa-sort-size-down-alt',
                sort.key === header.key && sort.order === 'asc' && 'active',
              ]"
            />
            <i
              v-else
              :class="[
                'fad fa-sort-size-down',
                sort.key === header.key && sort.order === 'desc' && 'active',
              ]"
            />
          </span>
        </div>
        <div v-else :class="['head', 'flex center']">
          <!-- :value="hasChildren ? allDescendantLeafsSelected : item[propValue]"  -->
          <l-checkbox
            :indeterminate="indeterminate"
            :model-value="hasChildren ? allDescendantLeafsSelected : value"
            style="margin: 0px !important"
            label-styles="margin-left: 5px"
            :disabled="disabledCheckboxes"
            @change="selectAll"
          />
        </div>
      </template>
      <l-skeleton v-else />
    </l-table-cell>
    <template v-if="advancedSearch">
      <l-table-cell v-for="header in headers" :key="`search-${header.key}`" :swap="true">
        <l-skeleton v-if="isLoading" />
        <template v-else-if="header.searchable">
          <div v-if="header.selectFromOptions" class="ltable__header-dropdown">
            <l-select
              style="margin-bottom: 0; width: 100%"
              @change="handleSelectChange($event, header)"
            />
          </div>
          <l-input
            v-else-if="isInput(header)"
            v-model="header.search"
            :type="jsType(header)"
            style="margin-bottom: 0; width: 100%"
            :display-validity="false"
            @keyup.enter="getData"
          />
          <l-datetimepicker
            v-else-if="header.type === 'date'"
            v-model="header.search"
            type="date"
            :range="true"
            style="margin-bottom: 0; width: 100%"
            @change="getData"
          />
        </template>
      </l-table-cell>
    </template>
  </div>
</template>

<script>
import LTableCell from './tableCell.vue'
import LSelect from '@/components/ui/form/select.vue'
import LInput from '@/components/ui/form/input.vue'
import LSkeleton from '@/components/ui/elements/skeleton.vue'
import LCheckbox from '@/components/ui/form/checkbox.vue'

export default {
  name: 'LTableHeader',
  components: {
    LCheckbox,
    LSkeleton,
    LInput,
    LSelect,
    LTableCell,
  },
  props: {
    headers: {
      type: Array,
      default: () => [],
    },
    sort: {
      type: Object,
      default: () => ({}),
    },
    advancedSearch: {
      type: Boolean,
      default: false,
    },
    isLoading: {
      type: Boolean,
      default: false,
    },
    isAjax: {
      type: Boolean,
      default: false,
    },
    getAjaxData: {
      type: Function,
      default: () => {},
    },
    isSelectedRow: {
      type: Boolean,
      default: false,
    },
    disabledCheckboxes: {
      type: Boolean,
      default: false,
    },
    selectedRows: {
      type: [Object, Array],
      default: () => [],
    },
  },
  data() {
    return {
      isRequest: false,
      cancelToken: this.$axios.CancelToken.source(),
      parent: this.$parent.$parent,
    }
  },
  computed: {
    columnsStyle() {
      const columns = this.headers.map((item) => item.width ?? 'minmax(185px, 1fr)').join(' ')
      return {
        'grid-template-columns': columns,
      }
    },
    allDescendantLeafs() {
      return this.parent.allDescendantLeafs
    },
    someDescendantLeafsSelected() {
      return this.parent.someDescendantLeafsSelected
    },
    allDescendantLeafsSelected() {
      return this.parent.allDescendantLeafsSelected
    },
    indeterminate() {
      if (this.hasChildren) {
        return this.parent.indeterminate
      }
      return this.someDescendantLeafsSelected && !this.allDescendantLeafsSelected
    },
    hasSelection() {
      return this.parent.hasSelection
    },
    hasChildren() {
      return this.parent.rowGroup
    },
    value() {
      return this.parent.value
    },
  },
  mounted() {},
  methods: {
    onSort(headerKey) {
      if (!this.isLoading && headerKey !== 'checkbox') {
        this.$emit('onSort', headerKey)
      }
    },
    async getData() {
      if (this.isAjax) {
        if (this.isRequest) {
          this.cancelToken.cancel()
          this.cancelToken = this.$axios.CancelToken.source()
        }
        try {
          this.isRequest = true
          await this.getAjaxData(false, this.cancelToken.token)
        } catch (e) {
          console.log(e)
        } finally {
          this.isRequest = false
        }
      }
    },
    isInput({ type }) {
      if (typeof type !== 'boolean') {
        return ['string', 'int'].includes(type.toLowerCase())
      }
      return true
    },
    jsType({ type }) {
      switch (type.toLowerCase()) {
        case 'string':
          return 'text'
        case 'int':
          return 'number'
        default:
          return 'text'
      }
    },
    handleSelectChange(selectedOption, header) {
      header.search = selectedOption ? selectedOption.id : null // Записываем id в header.search
    },
    selectAll() {
      const event = arguments[1]
      event.stopImmediatePropagation()
      event.stopPropagation()
      event.preventDefault()
      this.$emit('selectAll')
    },
  },
}
</script>

<style lang="scss" scoped>
.head {
  width: 100%;
  display: flex;
  align-items: center;
  justify-content: space-between;
  &__text {
    font-size: 14px;
    font-weight: bold;
  }
  &__sort {
    i {
      color: rgba($gray, 0.7);
      &.active {
        color: $primary;
      }
    }
  }
}

.ltable__header {
  display: grid;
  width: 100%;
  border-bottom: 3px solid $borderColor;
  border-top: $borderTable;
  background-color: #fff;
  z-index: 1000;
  overflow: hidden;
  &-text {
    border-bottom: $borderTable;
  }
  // &-fixed {
  //   z-index: 800;
  //   position: sticky;
  //   top: 0;
  //   left: 0;
  // }

  .ltable__header-dropdown {
    position: absolute;
    width: 13%;
  }
}
</style>
