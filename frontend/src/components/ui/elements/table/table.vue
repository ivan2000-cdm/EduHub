<template>
  <client-only>
    <div ref="ltable" v-resize class="ltable" @resize="getTableWidth">
      <div
        class="ltable__additional"
        :style="!needAdvancedSearch ? 'grid-template-columns: 1fr 25%' : ''"
      >
        <template v-if="loading">
          <div class="column">
            <l-skeleton />
          </div>
          <div class="column">
            <div :class="needAdvancedSearch ? 'column-3' : 'column-2'">
              <l-skeleton />
              <l-skeleton />
              <l-skeleton />
            </div>
          </div>
        </template>
        <template v-else>
          <div class="column">
            <slot name="functions" />
          </div>
          <div class="column">
            <div :class="needAdvancedSearch ? 'column-3' : 'column-2'">
              <l-table-dropdown :headers="headers" @changeHeader="headers = $event" />
              <l-button
                v-if="needAdvancedSearch"
                icon="fad fa-sliders-h"
                size="sm"
                :color="advancedSearch ? 'secondary' : 'default'"
                @click="toggleAdvancedSearch()"
              >
                Расширенный поиск
              </l-button>
              <l-input
                v-model="searchText"
                type="text"
                label="Поиск"
                :clear-button="true"
                @change="handleGlobalSearch"
              />
            </div>
          </div>
        </template>
      </div>
      <div :key="visibleColumns.length > 0" class="ltable__wrapper">
        <l-table-header
          ref="tableHeader"
          :headers="visibleColumns"
          :sort="sort"
          :advanced-search="advancedSearch"
          :is-loading="loading"
          :get-ajax-data="getAjaxData"
          :is-ajax="isAjax"
          :is-selected-row="isSelectedRow"
          :selected-rows="value"
          :disabled-checkboxes="disabledCheckboxes"
          @onSort="handleChangeSort"
          @selectAll="selectAll"
        />
        <perfect-scrollbar ref="scrollbar" :options="scrollBar" @ps-scroll-x="syncScrollX">
          <div
            class="ltable__table-scroll"
            :style="{
              width: overflowX === '100%' ? '100%' : `${overflowX}px`,
              height: overflowY,
              position: 'relative',
            }"
          >
            <l-table-body v-if="visibleColumns.length > 0">
              <template v-if="localOrAjaxData.length > 0">
                <l-table-row
                  v-for="(item, i) in localOrAjaxData"
                  :key="`row-${i}`"
                  v-model="value"
                  :item="item"
                  :headers="visibleColumns"
                  :is-loading="loading"
                  :is-selected="item.id === selectedRowId"
                  :is-checkbox="isSelectedRow"
                  :disabled-checkboxes="disabledCheckboxes"
                  :group="rowGroup"
                  :color-prop="colorProp"
                  @onRowClick="onRowClick($event, item.id)"
                  @onGroupRowClick="onGroupRowClick"
                  @selectCheckbox="$emit('selectCheckbox', $event)"
                >
                  <template v-for="slot in Object.keys($slots)" v-slot:[slot]="scope">
                    <slot :name="slot" v-bind="scope" />
                  </template>
                </l-table-row>
              </template>
              <div v-else class="ltable__error error">Элементы отсутствуют</div>
            </l-table-body>
          </div>
        </perfect-scrollbar>
      </div>
      <l-table-footer>
        <div class="ltable__footer-container">
          <template v-if="loading">
            <l-skeleton />
            <l-skeleton />
            <l-skeleton />
          </template>
          <template v-else>
            <div class="ltable__footer-range">
              <span>
                C {{ pageRange.start }} по {{ pageRange.end }} из {{ totalCount }} записей
              </span>
            </div>
            <div class="ltable__footer-select">
              <span>Показать по</span>
              <l-select
                v-model="perOnePage"
                :options="perPageOptions"
                :prop-data="{ value: 'value', text: 'name' }"
                @change="changePerPage"
              />
              <span>записей</span>
            </div>
            <div class="ltable__footer-pagination">
              <l-pagination
                ref="pagination"
                :total="totalCount"
                :per-page="perOnePage.value"
                :current-page="currentPage"
                @changePage="changePage"
              />
            </div>
          </template>
        </div>
      </l-table-footer>
    </div>
  </client-only>
</template>

<script>
import LTableHeader from './tableHeader.vue'
import LTableBody from './tableBody.vue'
import LTableRow from './tableRow.vue'
import LTableFooter from './tableFooter.vue'
import LTableDropdown from './tableDropdown.vue'

import AjaxMixin from './mixins/ajax.js'
import DataMixin from './mixins/data.js'
import HeaderMixin from './mixins/header.js'
import SelectedMixin from './mixins/selected.js'
import CacheMixin from './mixins/cache.js'
import LSelect from '@/components/ui/form/select.vue'
import LPagination from '@/components/ui/elements/pagination.vue'
import LSkeleton from '@/components/ui/elements/skeleton.vue'
import LInput from '@/components/ui/form/input.vue'
import LButton from '@/components/ui/elements/button.vue'

export default {
  name: 'LTable',
  components: {
    LButton,
    LInput,
    LSkeleton,
    LPagination,
    LSelect,
    LTableHeader,
    LTableBody,
    LTableRow,
    LTableFooter,
    LTableDropdown,
  },
  mixins: [AjaxMixin, DataMixin, HeaderMixin, SelectedMixin, CacheMixin],
  props: {
    columns: {
      type: Array,
      default: () => [],
      validator: (value) => {
        if (value.length === 0) {
          return true
        }
        const requiredKeys = ['label', 'key', 'sort', 'type', 'visible', 'selectFromOptions']
        const indexes = []
        value.forEach((item, idx) => {
          if (item) {
            const keys = Object.keys(item)
            const everyKeys = requiredKeys.filter((item) => !keys.includes(item))
            if (everyKeys.length !== 0) {
              indexes.push({ index: idx, key: requiredKeys.filter((item) => !keys.includes(item)) })
            }
          }
        })
        if (indexes.length > 0) {
          console.error(`Отсутствуют обязательные ключи: \n
            ${indexes.map(({ index, key }) => `Индекс: ${index}, ключ: ${key}`).join('\n')}
          `)
        }
        return indexes.length === 0
      },
    },
    page: {
      type: Number,
      default: 1,
    },
    perPage: {
      type: Number,
      default: 20,
    },
    perPageOptions: {
      type: Array,
      default: () => [
        { value: 5, name: '5' },
        { value: 10, name: '10' },
        { value: 15, name: '15' },
        { value: 20, name: '20' },
      ],
      validator: (value) => {
        const requiredKeys = ['value', 'name']
        const indexes = []
        value.forEach((item, idx) => {
          const keys = Object.keys(item)
          if (keys.length < 2) {
            indexes.push({ index: idx, key: requiredKeys.filter((item) => keys[0] !== item) })
          } else {
            const everyKeys = keys.every((item) => requiredKeys.includes(item))
            if (!everyKeys) {
              indexes.push({ index: idx, key: requiredKeys.filter((item) => !keys.includes(item)) })
            }
          }
        })
        if (indexes.length > 0) {
          console.error(`Отсутствуют ключи: \n
            ${indexes.map(({ index, key }) => `Индекс: ${index}, ключ: ${key}`).join('\n')}
          `)
        }
        return indexes.length === 0
      },
    },
    overflowY: {
      type: String,
      default: '100%',
    },
    selectedRowId: {
      type: Number,
      default: null,
    },
    rowGroup: {
      type: Boolean,
      default: false,
    },
    forceLoading: {
      type: Boolean,
      default: false,
    },
  },
  data() {
    const perOnePage = this.perPageOptions.find(({ value }) => value === this.perPage)
    const headers = new Array(5).fill('').map((item, i) => ({ key: `header-${i}`, visible: true }))
    const generatedData = {
      headers,
      localData: new Array(this.perPage).fill({}).map((item) => {
        headers.forEach((h) => {
          item[h.key] = ''
        })
        return item
      }),
    }
    return {
      error: null,
      tableWidth: '100%',
      localData: generatedData.localData,
      headers: generatedData.headers,
      currentPage: this.page || 1,
      perOnePage,
      generatedData,
      scrollBar: {
        suppressScrollY: false,
        suppressScrollX: false,
      },
    }
  },
  computed: {
    overflowX() {
      if (this.visibleColumns.length === 0) {
        return '100%'
      }
      const width = this.visibleColumns
        .map(({ width }) => (width !== undefined ? parseInt(width) : 185))
        .reduce((acc, width) => acc + width)
      return this.tableWidth > width ? '100%' : width
    },
    loading() {
      const everyHeadersGenerated = this.headers.every(
        (item) => Object.keys(item).length === 2 && item.key.includes('header'),
      )
      let pending = false
      if (this.$parent.$fetchState) {
        pending = this.$parent.$fetchState.pending
      }

      if (!this.isAjax) {
        return this.forceLoading || pending || (this.columns.length === 0 && everyHeadersGenerated)
      } else {
        return everyHeadersGenerated || this.ajaxMeta.loading
      }
    },
  },
  watch: {
    columns() {
      if (!this.isAjax && this.columns.length !== 0) {
        this.getHeaders()
      }
    },
    loading() {
      if (this.loading) {
        this.localData = this.generatedData.localData
      }
    },
    data() {
      this.localData = !this.isAjax && this.data.length !== 0 ? this.data : []
    },
    page() {
      this.currentPage = this.page
    },
  },
  mounted() {
    if (this.isAjax) {
      this.getAjaxData(true)
    } else {
      if (this.columns.length > 0) {
        this.getHeaders()
      }
      this.localData = this.data.length > 0 ? this.data : []
    }
  },
  methods: {
    onRowClick(row, id) {
      if (!this.loading) {
        this.$emit('onRowClick', { row, id, page: this.currentPage })
      }
    },
    onGroupRowClick(item) {
      this.$emit('onGroupRowClick', item)
    },
    getTableWidth() {
      this.tableWidth = parseInt(getComputedStyle(this.$refs.ltable).width) - 2
      this.$refs.scrollbar.$el.scrollLeft = 0
    },
    syncScrollX() {
      const scrollLeft = this.$refs.scrollbar.$el.scrollLeft
      const thead = this.$refs.tableHeader.$el
      if (thead.scrollLeft !== scrollLeft) {
        thead.scrollLeft = scrollLeft
      }
    },
  },
}
</script>

<style lang="scss" scoped>
.ltable {
  border: $borderTable;
  background-color: #fff;
  position: relative;
  &__wrapper {
    // overflow-x: scroll;
    // overflow-y: scroll;
  }
  &__table {
    &-scroll {
      // overflow-x: scroll;
      // overflow-y: scroll;
    }
  }
  &__additional {
    display: grid;
    grid-template-columns: 1fr 40%;
    column-gap: 1rem;
    padding: 10px;
    @media only screen and (max-width: 992px) {
      grid-template-columns: 1fr 60%;
    }
    @media only screen and (max-width: 576px) {
      grid-template-columns: 1fr;
      grid-template-rows: 1fr 1fr;
      row-gap: 20px;
    }
    .column-2 {
      display: grid;
      grid-template-columns: 100px 1fr;
      column-gap: 10px;
    }
    .column-3 {
      display: grid;
      grid-template-columns: 100px 0.8fr 1fr;
      column-gap: 10px;
    }
    .linput {
      margin-bottom: 0;
    }
  }
  &__error {
    padding: 20px;
  }
}
</style>
